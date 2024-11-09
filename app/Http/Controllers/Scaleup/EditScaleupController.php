<?php

namespace App\Http\Controllers\Scaleup;

use App\Http\Controllers\Controller;
use App\Models\ScaleUpDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use stdClass;

class EditScaleupController extends Controller
{
	private $sessionCategory;
	private $sessionItem;
	private $sessionId;
	public function __construct()
	{
		$this->sessionCategory = 'scaleupCategory';
		$this->sessionItem = 'scaleupItem';
		$this->sessionId = 'ScaleupDoc';
	}

	public function edit($id)
	{
		try {
			$doc_number =  Crypt::decryptString($id);
		} catch (\Throwable $th) {
			// dd($th);
			return abort('404');
		}


		$idSession = session($this->sessionId, null);

		if ($idSession !== $doc_number) {
			session([$this->sessionId => $doc_number]);
			session()->forget([$this->sessionCategory, $this->sessionItem]);
		}

		$header =  DB::table('scaleup_header as sh')
			->leftJoin('users as u', 'u.id', '=', 'sh.user_id')
			->leftJoin('sub_category as sc', 'sc.id', 'sh.doctype_id')
			->select('sh.*', 'u.name', 'sc.description as category_description')
			->where('sh.doc_number', $doc_number)
			->where('sh.status', 'P')
			->where('sh.user_id', Auth::user()->id)
			->first();

		$totalApprover = DB::table('approval_document')->where('scaleup_header_id', $header->id)->count('id');
		$totalPending = DB::table('approval_document')->where('scaleup_header_id', $header->id)->where('status', 'P')->count('id');

		if ($totalPending !== $totalApprover) {
			return redirect()->route('scaleup.index')->with(['message' => [
				"type" => "error",
				"text" => "Scaleup sudah ada yang approve / reject, tidak dapat diedit"
			]]);
		}

		if (!$header) {
			session()->forget([$this->sessionCategory, $this->sessionItem, $this->sessionId]);
			return redirect()->route('scaleup.index')->with(['message' => [
				"type" => "error",
				"text" => "Anda tidak mempunyai authorisasi untuk mengedit Scale Up Tersebut"
			]]);
		}

		$detail = ScaleUpDetail::where('scaleup_header_id', $header->id)->get();
		$category_ref = $detail->pluck('category_reference')->unique();
		$item_category = DB::table('item_category')->whereIn('uniqid', $category_ref)->get();

		$newDetail = [];
		foreach ($detail as $item) {
			$newDetail[] = [
				"uniqid" => $item->id,
				"material_id" => $item->material_id,
				"item_code" => $item->material_code,
				"item_description" => $item->material_description,
				"qty" => 0,
				"percent" => round($item->percent, 3),
				"uom" => $item->uom,
				"item_remark" => $item->remark,
				"item_category" => $item->category_reference,
			];
		}

		$newCategory = [];
		foreach ($item_category as $item) {
			$newCategory[] = [
				"id" => $item->uniqid,
				"description" => $item->description,
			];
		}


		// Session handling
		$itemCart = session($this->sessionItem, []);
		$itemCategory = session($this->sessionCategory, []);

		if (empty($itemCart)) {
			session([$this->sessionItem => $newDetail]);
			$itemCart = $newDetail;
		}

		if (empty($itemCategory)) {
			session([$this->sessionCategory => $newCategory]);
			$itemCategory = $newCategory;
		}


		$header->doc_number = $id;
		return view('scaleup.edit', [
			"header" => $header,
			"itemCart" => $itemCart,
			"itemCategory" => $itemCategory,
		]);
	}




	public function update(Request $request, $id)
	{
		try {
			$doc_number =  Crypt::decryptString($id);
		} catch (\Throwable $th) {
			dd($th);
			return abort('404');
		};

		$header =  DB::table('scaleup_header as sh')
			->leftJoin('users as u', 'u.id', '=', 'sh.user_id')
			->leftJoin('sub_category as sc', 'sc.id', 'sh.doctype_id')
			->select('sh.*', 'u.name', 'sc.description as category_description')
			->where('sh.doc_number', $doc_number)
			->where('sh.status', 'P')
			->where('sh.user_id', Auth::user()->id)
			->first();

		if (!$header) {
			session()->forget([$this->sessionCategory, $this->sessionItem]);
			return redirect()->route('scaleup.index')->with(['message' => [
				"type" => "error",
				"text" => "Anda tidak mempunyai authorisasi untuk mengedit Scale Up Tersebut"
			]]);
		}


		$request->validate([
			'IssueDate' => 'required',
			'DocDate' => 'required',
			'doctype' => 'required|exists:sub_category,id',
			// 'sap_code' => 'required|exists:material_sap,sap_code',
			// 'product_select' => 'required|exists:product_new,id',
			'product_code' => 'required|exists:product_new,product_code',
			'material_description' => 'required',
			'revisi' => 'max:200',
			'base_uom' => 'required',
			'per_pack' => 'required',
			'total' => 'required|numeric',
			'remark' => 'max:255',
		]);

		if (session($this->sessionItem) == null) {
			return Redirect::back()
				->withInput()
				->withErrors(['message' => "Item List tidak boleh kosong"]);
		}
		if (session($this->sessionCategory) == null) {
			return Redirect::back()
				->withInput()
				->withErrors(['message' => "Item harus memiliki kategori"]);
		}

		$itemCart = session($this->sessionItem);
		$itemCategory = session($this->sessionCategory);

		// Kondisi Jika akan dijadikan scale Up
		if ($request->action == 'save') {

			$totalPercent = 0;

			foreach ($itemCart as $item) {
				$totalPercent += $item['percent'];
			}

			if ($totalPercent != 100) {
				return Redirect::back()
					->withInput()
					->withErrors(['message' => "Total persentase harus 100%"]);
			}
			DB::beginTransaction();
			try {
				$header_data = [
					"doc_number" => $doc_number,
					"issue_date" => Carbon::createFromFormat('d-m-Y', $request->IssueDate)->toDateString(),
					"doc_date" => Carbon::createFromFormat('d-m-Y', $request->DocDate)->toDateString(),
					"doctype_id" => $request->doctype,
					"material_id" => $request->product_select,
					"product_code" => $request->product_code,
					"material_code" => $request->sap_code,
					"material_description" => $request->material_description,
					"remark" => $request->remark,
					"revision" => $request->revisi,
					"base_uom" => $request->base_uom,
					"base_qty" => $request->base_qty,
					"per_pack" => $request->per_pack,
					"total" => $request->total,
					"user_id" => Auth::user()->id,
					"status" => "P",
					"is_active" => true,
					"updated_at" => Carbon::now(),
				];

				DB::table('scaleup_header')->where('id', $header->id)->update($header_data);

				$header =  DB::table('scaleup_header')->where('doc_number', $doc_number)->first();
				$scaleup_detail = [];
				foreach ($itemCart as $item) {
					$item_detail = [
						"scaleup_header_id" => $header->id,
						"material_id" => $item["material_id"],
						"material_code" => $item["item_code"],
						"material_description" => $item["item_description"],
						"percent" => $item["percent"],
						"uom" => $item["uom"],
						"remark" => $item["item_remark"],
						"category_reference" => $item["item_category"],
						"user_id" => Auth::user()->id,
						"created_at" => Carbon::now()
					];
					$scaleup_detail[] = $item_detail;
				}

				DB::table('scaleup_detail')->where('scaleup_header_id', $header->id)->delete();
				DB::table('scaleup_detail')->insert($scaleup_detail);

				foreach ($itemCategory as $item) {
					DB::table('item_category')->updateOrInsert(
						['uniqid' => $item['id']],
						[
							'uniqid' => $item['id'],
							'description' => $item['description'],
							'created_at' => Carbon::now(),
							'user_id' => Auth::user()->id,
						],
					);
				}

				// $approval_doc = [];
				// foreach ($approver as $app) {
				// 	$app_item = [
				// 		"scaleup_header_id" => $header->id,
				// 		"user_id" => $app->user_id,
				// 		"level" => $app->level,
				// 		"email" => $app->email,
				// 		"status" => "P",
				// 		"create_user" => Auth::user()->id,
				// 		"created_at" => Carbon::now()
				// 	];

				// 	$approval_doc[] = $app_item;
				// }
				// DB::table('approval_document')->insert($approval_doc);

				// $firstApprover = ApprovalDocument::where('scaleup_header_id', $header->id)->get();
				// $min = $firstApprover->min('level');
				// $filtered = $firstApprover->filter(function ($firstApprover) use ($min) {
				// 	return $firstApprover->level == $min;
				// });

				// $data = $header;
				// $data->type = 'request';
				// $data->intro = "Mohon Untuk di Approve permohonan dokumen Scale Up berikut ini :";

				// foreach ($filtered as $item) {
				// 	Mail::to($item->email)
				// 		->send(new AprrovalNotification($data));
				// 	$item->update(['sent_at' => Carbon::now()]);
				// 	$item->save();
				// }

				$totalApprover = DB::table('approval_document')->where('scaleup_header_id', $header->id)->count('id');
				$totalPending = DB::table('approval_document')->where('scaleup_header_id', $header->id)->where('status', 'P')->count('id');

				if ($totalPending !== $totalApprover) {
					return redirect()->route('scaleup.index')->with(['message' => [
						"type" => "error",
						"text" => "Scaleup sudah ada yang approve / reject, tidak dapat diedit"
					]]);
				}
				DB::commit();
				session()->forget([$this->sessionCategory, $this->sessionItem, $this->sessionId]);
				return redirect()->route('scaleup.index')->with(['message' => [
					"type" => "success",
					"text" => "Scale Up berhadil diupdate"
				]]);
			} catch (\Throwable $th) {
				DB::rollBack();
				dd($th);
				return redirect()->route('scaleup.index')->with(['message' => [
					"type" => "error",
					"text" => "Scale Up Gagal diupdate"
				]]);
			}
		}
	}

	public function sessionGetSubCategory()
	{

		if (session($this->sessionCategory) == null) {
			$subCategories = [];
		} else {
			$subCategories = session($this->sessionCategory);
		}
		return response()->json($subCategories, 200);
	}

	public function saveitemCategory(Request $request)
	{
		if (session($this->sessionCategory) != NULL) {
			$itemCategory = session($this->sessionCategory);
			$itemCategory = array_merge($itemCategory, [
				[
					"id" => uniqid(),
					"description" => $request->item_category_desc,
				]
			]);
			session([$this->sessionCategory => $itemCategory]);
		} else {
			session([
				$this->sessionCategory => [
					[
						"id" => uniqid(),
						"description" => $request->item_category_desc,
					]
				]
			]);
		}

		return response()->json(session($this->sessionCategory));
	}

	public function updateItemcategory(Request $request)
	{
		if (session($this->sessionCategory) != null) {
			$itemCategory = session($this->sessionCategory);
			$key = array_search($request->id, array_column($itemCategory, 'id'));

			$newVal = [
				'id' => $request->id,
				'description' => $request->description,
			];
			$itemCategory[$key] = $newVal;

			session([$this->sessionCategory => $itemCategory]);
			return response()->json($itemCategory);
		}
	}


	public function getItemById(Request $request)
	{
		$itemCart = session($this->sessionItem);
		$targetUniqid = $request->uniqid;

		$result = array_filter($itemCart, function ($item) use ($targetUniqid) {
			return $item['uniqid'] == $targetUniqid;
		});

		$result = reset($result);


		return response()->json($result);
	}

	public function saveItem(Request $request)
	{

		if ($request->action == 'edit') {
			$targetUniqid = $request->uniqid;
			$itemCart = session($this->sessionItem);
			$key = array_search($targetUniqid, array_column($itemCart, 'uniqid'));

			$newVal = [
				"uniqid" => $targetUniqid,
				"material_id" => $request->material_id,
				"item_code" => $request->item_code,
				"item_description" => $request->item_description,
				"qty" => round($request->qty, 3),
				"percent" => round($request->percent, 3),
				"uom" => $request->uom,
				"item_remark" => $request->item_remark,
				"item_category" => $request->item_category,
				"item_category_text" => $request->item_category_text,
			];


			$itemCart[$key] = $newVal;
			session([$this->sessionItem => $itemCart]);
		} else {
			if (session($this->sessionItem) != NULL) {
				$itemCart = session($this->sessionItem);
				$itemCart = array_merge($itemCart, [
					[
						"uniqid" => uniqid(),
						"material_id" => $request->material_id,
						"item_code" => $request->item_code,
						"item_description" => $request->item_description,
						"qty" => round($request->qty, 3),
						"percent" => round($request->percent, 3),
						"uom" => $request->uom,
						"item_remark" => $request->item_remark,
						"item_category" => $request->item_category,
						"item_category_text" => $request->item_category_text,
					]
				]);
				session([$this->sessionItem => $itemCart]);
			} else {
				session([
					$this->sessionItem => [
						[

							"uniqid" => uniqid(),
							"material_id" => $request->material_id,
							"item_code" => $request->item_code,
							"item_description" => $request->item_description,
							"qty" => round($request->qty, 3),
							"percent" => round($request->percent, 3),
							"uom" => $request->uom,
							"item_remark" => $request->item_remark,
							"item_category" => $request->item_category,
							"item_category_text" => $request->item_category_text,
						]
					]
				]);
			}
		}

		return response()->json(session($this->sessionItem));
	}

	public function deleteItem(Request $request)
	{
		if (session($this->sessionItem) != null) {
			$itemCart = session($this->sessionItem);
			$key = array_search($request->uniqid, array_column($itemCart, 'uniqid'));
			unset($itemCart[$key]);
			$itemCart = array_values($itemCart);
			session([$this->sessionItem => $itemCart]);

			return response()->json(session($this->sessionItem));
		}
	}

	public function deleteItemcategory(Request $request)
	{
		$id = $request->id;
		if (session($this->sessionCategory) != null) {
			$itemCategory = session($this->sessionCategory);
			$key = array_search($id, array_column($itemCategory, 'id'));
			unset($itemCategory[$key]);
			$itemCategory = array_values($itemCategory);
			session([$this->sessionCategory => $itemCategory]);

			$itemCart = [];
			if (session($this->sessionItem) != null) {
				$itemCart = session($this->sessionItem);
				$itemCart = array_filter($itemCart, function ($item) use ($id) {
					return $item['item_category'] !== $id;
				});

				$itemCart = array_values($itemCart);
				session([$this->sessionItem => $itemCart]);
			}


			$result = new stdClass();
			$result->itemCart = $itemCart;
			$result->itemCategory = $itemCategory;

			return response()->json($result);
		}
	}
}

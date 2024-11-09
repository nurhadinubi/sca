<?php

namespace App\Http\Controllers\Scaleup;

use App\Http\Controllers\Controller;
use App\Mail\AprrovalNotification;
use App\Models\ApprovalDocument;
use App\Models\KeyCode;
use App\Models\ScaleUpDetail;
use App\Models\ScaleUpHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class DuplicateScaleupController extends Controller
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


	function duplicate($id)
	{

		try {
			$key_code = Crypt::decryptString($id);
		} catch (\Throwable $th) {
			session()->forget([$this->sessionCategory, $this->sessionItem, $this->sessionId]);
			// abort('404');
			throw $th;
		}

		$keycode = KeyCode
			::where([
				'key_code' => $key_code,
				'is_used' => false,
				'status' => 'A',
				'user_id' => Auth::user()->id,
			])->first();

		// dd($keycode);

		if (!$keycode) {
			session()->forget([$this->sessionCategory, $this->sessionItem, $this->sessionId]);
			return redirect()->route('keycode.process')->with(['message' => [
				"type" => "error",
				"text" => "Keycode yang anda gunakan tidak valid. keycode sudah digunakan atau user anda tidak mempunyai authorisasi untuk keycode ini"
			]]);
		}

		$header =  DB::table('scaleup_header as sh')
			->leftJoin('users as u', 'u.id', '=', 'sh.user_id')
			->leftJoin('sub_category as sc', 'sc.id', 'sh.doctype_id')
			->select('sh.*', 'u.name', 'sc.description as category_description')
			->where('sh.id', $keycode->scaleup_header_id)
			// ->where('sh.status', 'P')
			->where('sh.user_id', Auth::user()->id)
			->first();

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
		return view('scaleup.duplicate', [
			"header" => $header,
			"itemCart" => $itemCart,
			"itemCategory" => $itemCategory,
		]);
	}

	function store(Request $request, $id)
	{
		$key_code = '';
		try {
			$key_code = Crypt::decryptString($id);
		} catch (\Throwable $th) {
			// dd($th);
			abort('404');
		}

		$keycode = KeyCode::where([
			'keycode.key_code' => $key_code,
			'keycode.is_used' => false,
			'keycode.status' => 'A',
			'keycode.user_id' => Auth::user()->id,
		])->first();

		if (!$keycode) {
			return redirect()->back()->with(['message' => [
				"type" => "error",
				"text" => "Keycode yang anda gunakan tidak valid. keycode sudah digunakan atau user anda tidak mempunyai authorisasi untuk keycode ini"
			]]);
		}

		$request->validate([
			'IssueDate' => 'required',
			'DocDate' => 'required',
			'doctype' => 'required|exists:sub_category,id',
			// 'sap_code' => 'nullable|exists:material_sap,sap_code',
			'product_select' => 'required|exists:product_new,id',
			'product_code' => 'required|exists:product_new,product_code',
			'material_description' => 'required',
			'revisi' => 'max:200',
			'base_uom' => 'required',
			'per_pack' => 'required',
			'total' => 'required|numeric',
			'remark' => 'max:255',
		]);

		if (session($this->sessionItem) == null) {
			return redirect()->back()
				->with(['message' => [
					"type" => "error",
					"text" => "Item tidak boleh kosong"
				]]);
		}
		if (session($this->sessionCategory) == null) {
			return redirect()->back()
				->with(['message' => [
					"type" => "error",
					"text" => "item kategori tidak boleh kosong"
				]]);
		}

		$itemCart = session($this->sessionItem);
		$itemCategory = session($this->sessionCategory);

		// jika memilih draft
		if ($request->action == 'draft') {
			DB::beginTransaction();
			try {
				// ScaleUP Header
				$header_data = [
					"issue_date" => Carbon::createFromFormat('d-m-Y', $request->IssueDate)->toDateString(),
					"doc_date" => Carbon::createFromFormat('d-m-Y', $request->DocDate)->toDateString(),
					"doctype_id" => $request->doctype,
					"material_id" => $request->product_select,
					"product_code" => $request->product_code,
					"material_code" => $request->sap_code,
					"material_description" => $request->material_description,
					"remark" => $request->remark,
					"revision" => $request->revisi,
					"rev0" => $request->rev0,
					"base_uom" => $request->base_uom,
					"base_qty" => $request->base_qty,
					"per_pack" => $request->per_pack,
					"total" => $request->total,
					"user_id" => Auth::user()->id,
					"status" => "D",
					"is_active" => true,
					"created_at" => Carbon::now(),
				];

				$header = new ScaleUpHeader();
				$doc_id = $header->create($header_data);
				$scaleup_detail = [];
				foreach ($itemCart as $item) {
					$item_detail = [
						"scaleup_header_id" => $doc_id->id,
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


				ScaleUpDetail::insert($scaleup_detail);

				// insert item category
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

				$keycode->is_used = true;
				$keycode->scaleup_header_id = $doc_id->id;
				$keycode->save();
				DB::commit();

				session()->forget([$this->sessionCategory, $this->sessionItem, $this->sessionId]);
				session()->forget('headerData');
				return redirect()->route('draft.scaleup.index')->with(['message' => [
					"type" => "success",
					"text" => "Scale Up Disimpan di draft "
				]]);
			} catch (\Throwable $th) {
				DB::rollBack();
				// dd($th);
				abort(500);
			}
		}

		// jika pilih save
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

				// ScaleUP Header
				// get doctype
				$subType = DB::table('sub_category')->where('id', $request->doctype)->first();
				$doc = DB::table('doctype')->where('id', $subType->doctype_id)->first();

				// Get Table approver
				$approver = DB::table('approval')
					->where('doctype_id', '=', $doc->id)
					->where('transaction_type', 'scaleup-create')
					->leftJoin('users', 'approval.user_id', 'users.id')
					->select('approval.*', 'users.email as email')
					->orderBy('level', 'ASC')->get()->toArray();
				if (count($approver) < 1) {
					return redirect()->back()
						->withInput()
						->with(['message' => [
							"type" => "error",
							"text" => "Approver belum disetting, Hubungi admin"
						]]);
				}
				// create doc number,
				$year = explode('-', $request->DocDate)[2];
				$month = explode('-', $request->DocDate)[1];
				$month = number2roman(intVal($month));

				$first = "SCA/{$doc->doctype}/{$year}/{$month}/";

				// Gel last Doc No
				$last = DB::table('scaleup_header')->where('doc_number', 'LIKE', "%{$first}%")->latest()->first();
				if (!$last) {
					$last = 0;
				} else {
					$last = str_replace($first, "", $last->doc_number);
				}

				$last = intval($last);
				$no = str_pad(($last + 1), 6, 0, STR_PAD_LEFT);
				$doc_number = "{$first}{$no}";

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
					"created_at" => Carbon::now(),
				];

				$header = new ScaleUpHeader();
				$doc_id = $header->create($header_data);

				$scaleup_detail = [];
				foreach ($itemCart as $item) {
					$item_detail = [
						"scaleup_header_id" => $doc_id->id,
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
				ScaleUpDetail::insert($scaleup_detail);

				// insert item category

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

				$approval_doc = [];
				foreach ($approver as $app) {
					$app_item = [
						"scaleup_header_id" => $doc_id->id,
						"user_id" => $app->user_id,
						"level" => $app->level,
						"email" => $app->email,
						"status" => "P",
						"create_user" => Auth::user()->id,
						"created_at" => Carbon::now()
					];
					$approval_doc[] = $app_item;
				}

				DB::table('approval_document')->insert($approval_doc);

				$firstApprover = ApprovalDocument::where('scaleup_header_id', $doc_id->id)->get();
				$min = $firstApprover->min('level');
				$filtered = $firstApprover->filter(function ($firstApprover) use ($min) {
					return $firstApprover->level == $min;
				});

				$data = $doc_id;
				$data->type = 'request';
				$data->intro = "Mohon Untuk di Approve permohonan dokumen Scale Up berikut ini :";

				foreach ($filtered as $item) {
					$data->link = Crypt::encryptString($doc_id->doc_number . "|" . $item->id . "|" . $item->created_at);
					Mail::to($item->email)
						->send(new AprrovalNotification($data));
					$item->update(['sent_at' => Carbon::now()]);
					$item->save();
				}

				$keycode->is_used = true;
				$keycode->scaleup_header_id = $doc_id->id;
				$keycode->save();

				// $revision = $header->revision;
				// $revision = $revision ? 0 : $revision + 1;
				// $header->revison = $revision;

				DB::commit();
				session()->forget([$this->sessionCategory, $this->sessionItem, $this->sessionId]);
				session()->forget('headerData');

				session()->forget('headerData');
				return redirect()->route('scaleup.index')->with(['message' => [
					"type" => "success",
					"text" => "Scale Up {$header->doc_number} Berhasil disimpan "
				]]);
			} catch (\Throwable $th) {
				DB::rollBack();
				abort('500');
				// dd($th);
			}
		}
	}
}

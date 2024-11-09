<?php

namespace App\Http\Controllers\Scaleup;

use App\Http\Controllers\Controller;
use App\Models\MasterMaterial;
use App\Models\ScaleUpDetail;
use App\Models\ScaleUpHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

use App\Mail\AprrovalNotification;
use App\Models\ApprovalDocument;
use Barryvdh\DomPDF\Facade\Pdf;

// use PDF as Pdf;
use stdClass;

use function Laravel\Prompts\select;

class ScaleUpController extends Controller
{

	/**
	 * - Saat inu rano direset perbulan
	 * - Scale up tidak perlu interface langsung ke BOM
	 * - Di email ada Approve atau reject
	 * - Approve tidak perlu login, Pas klik email bisa langsung approve tanpa perlu ke web
	 * - Ada kode produk CI, untuk antisipasi jika ada produk yang belum didaftarin di SAP
	 * - Kode material tidak mandatory ada kodenya, bisa free text 
	 * - Kode yang freetext ini akan ada yang mapping ke SAP jika sudah dibuatkan
	 * - tambahan Base Qty untuk dikirim ke SAP
	 * - Berat Perkemasn free text (Bukan base qty)
	 * - Revision auto increment
	 * - Kode Produk != kode material SAP, Dia ada penomoran sendiri 1 Kode Produk = 1 kode material, Bisa banyak Scale Up
	 * - Semua UOM SCA KG (nanti dari DAI hanya input persentase) jadi user harus input dulu total berat semau material
	 * - Scale up yang berhasil yang akan di kirim ke DFI
	 * Item
	 * Item material ada add material yang belumadsa di SAP. (hanya di pake di web)
	 * Di item bisa di group menjadi beberapa kategori, misal powder, mix, saus dll
	 * Contoh
	 * A. Item Poder
	 * 	- Material 1 
	 * 	- Material 2
	 * 	- Material 3
	 * B. Item Saus
	 *  - Material 1
	 * 	- Material 2
	 * C dst.
	 * Item ini biasa ada kategorinya ada yang tidak (langsun)
	 */
	public function __construct(Type $var = null)
	{
		// $this->middleware(function ($request, $next) {
		// 	$user = Auth::user();
		// 	dd($user->hasRole('need-otp'));

		// 	return $next($request);
		// });
	}
	public function index()
	{
		$scaleup =  DB::table('scaleup_header as sh')
			->select('sh.*', 'mm.material_code as material_code', 'mm.material_description as material_desc', 'pc.product_code')
			->leftJoin('master_material as mm', 'mm.id', '=', "sh.material_id")
			->leftJoin('product_code as pc', 'pc.material_id', 'sh.material_id')
			->orderBy('pc.product_code', 'ASC')
			->paginate(15);
		return view('scaleup.index', ["scaleup" => $scaleup]);
	}

	public function getItemcategory(Request $request)
	{
		$itemCategories = DB::table('item_category')
			->whereRaw('LOWER(description) LIKE ?', ['%' . $request->q . '%'])
			->get();

		return response()->json($itemCategories, 200);
	}

	public function getMaterial(Request $request)
	{
		$q = strtolower($request->q);

		$material = DB::table('master_material as mm')
			->leftJoin('map_sap_ci as map', 'map.ci_id', '=', 'mm.id')
			->leftJoin('material_sap as sap', 'sap.sap_code', '=', 'map.sap_code')
			->where(function ($query) use ($q) {
				$query->whereRaw('LOWER(mm.material_description) LIKE ?', ['%' . $q . '%'])
					->orWhereRaw('LOWER(mm.material_code) LIKE ?', ['%' . $q . '%']);
			})
			->select('mm.id', 'sap.sap_code as material_code', 'mm.material_description', 'mm.material_uom')
			->where('mm.is_active', true)
			->where('mm.is_deleted', false)
			->get();
		return response()->json($material, 200);
	}
	public function listScaleUp(Request $request)
	{
		$q = strtolower($request->q);

		// dd($q);
		$material = DB::table('scaleup_header')
			// ->select('sh.*', 'mm.material_code as material_code', 'mm.material_description as material_desc', 'pc.product_code')
			// ->leftJoin('master_material as mm', 'mm.id', '=', "sh.material_id")
			// ->leftJoin('product_code as pc', 'pc.material_id', 'sh.material_id')
			->where(function ($query) use ($q) {
				$query->whereRaw('LOWER(scaleup_header.doc_number) LIKE ?', ['%' . $q . '%'])
					->orWhereRaw('LOWER(scaleup_header.product_code) LIKE ?', ['%' . $q . '%'])
					->orWhereRaw('LOWER(scaleup_header.product_code) LIKE ?', ['%' . $q . '%']);
			})
			->get();
		dd($material);
		return response()->json($material, 200);
	}
	// public function listScaleUp(Request $request)
	// {
	// 	$q = strtolower($request->q);

	// 	$material = DB::table('scaleup_header as sh')
	// 		->select('sh.*', 'mm.material_code as material_code', 'mm.material_description as material_desc', 'pc.product_code')
	// 		->leftJoin('master_material as mm', 'mm.id', '=', "sh.material_id")
	// 		->leftJoin('product_code as pc', 'pc.material_id', 'sh.material_id')
	// 		->whereRaw('LOWER(sh.doc_number) LIKE ?', ['%' . $q . '%'])
	// 		->get();
	// 	return response()->json($material, 200);
	// }

	function getMaterialById(Request $request)
	{
		$material = DB::table('master_material as mm')
			->leftJoin('map_sap_ci as map', 'map.ci_id', '=', 'mm.id')
			->leftJoin('material_sap as sap', 'sap.sap_code', '=', 'map.sap_code')
			->select('mm.id', 'sap.sap_code as material_code', 'mm.material_description', 'mm.material_uom')
			->where('mm.id', $request->id)
			->first();
		return response()->json($material);
	}

	public function create()
	{

		if (session("itemCart") == null) {
			$itemCart = [];
		} else {
			$itemCart = session("itemCart");
		}

		if (session("headerData") == null) {
			$headerData = [];
		} else {
			$headerData = session("headerData");
		}

		if (session("itemCategory") == null) {
			$itemCategory = [];
		} else {
			$itemCategory = session("itemCategory");
		}


		$doctype = DB::table('public.mapping_subcategory_user as msu')
			->join('sub_category as sc', 'sc.id', '=', 'msu.sub_category_id')
			->where('msu.user_id', Auth::user()->id)
			->get();

		if (count($doctype) < 1) {
			$doctype = DB::table('public.mapping_subcategory_user as msu')
				->join('sub_category as sc', 'sc.id', '=', 'msu.sub_category_id')
				->get();
		}

		// dd($doctype);
		// $doctype = DB::table('doctype')->get();
		return view('scaleup.create', compact('doctype', 'itemCart', 'itemCategory'));
	}

	public function show($id)
	{
		$doc_number = base64_decode($id);
		$header =  DB::table('scaleup_header as sh')
			->select('sh.*', 'map.sap_code as material_code', 'mm.material_description', 'u.name as name', 'pc.product_code')
			->leftJoin('master_material as mm', 'mm.id', '=', "sh.material_id")
			->leftJoin('users as u', 'u.id', '=', 'sh.user_id')
			->leftJoin('product_code as pc', 'pc.id', '=', 'sh.material_id')
			->leftJoin('map_sap_ci as map', 'map.ci_id', '=', 'sh.material_id')
			->where('sh.doc_number', $doc_number)
			->first();

		// dd($header);
		if (!$header) {
			return abort('404');
		}

		$detail = ScaleUpDetail::where('scaleup_header_id', $header->id)->get();
		// dd($detail);
		$category_ref = $detail->pluck('category_reference')->unique();
		$item_category = DB::table('item_category')->whereIn('uniqid', $category_ref)->get();
		$approval = DB::table("approval_document as ad")
			->select('ad.*', 'users.name as name')
			->join('users', 'users.id', '=', 'ad.user_id')
			->orderBy('ad.level', 'ASC')
			->where('scaleup_header_id', $header->id)
			->get();
		return view('scaleup.show', [
			"header" => $header,
			"detail" => $detail,
			"approval" => $approval,
			"itemCategory" => $item_category,
		]);
	}

	public function saveItem(Request $request)
	{

		if ($request->action == 'edit') {
			$targetUniqid = $request->uniqid;
			$itemCart = session('itemCart');
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
			session(['itemCart' => $itemCart]);
		} else {
			if (session('itemCart') != NULL) {
				$itemCart = session('itemCart');
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
				session(['itemCart' => $itemCart]);
			} else {
				session([
					'itemCart' => [
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

		return response()->json(session('itemCart'));
	}

	public function saveTotal(Request $request)
	{
		session(["headerData" => [
			"total" => round($request->total, 3)
		]]);

		$newtotal = (float)$request->total;
		if (session('itemCart') != null) {

			$data = session('itemCart');
			$data = array_map(function ($item) use ($newtotal) {
				$item['qty'] = round(($newtotal * $item['percent']) / 100, 3);
				return $item;
			}, $data);
			session(['itemCart' => $data]);
		}




		return response()->json(session('headerData'));
	}

	public function saveitemCategory(Request $request)
	{
		// session()->forget(itemCategory);
		if (session('itemCategory') != NULL) {
			$itemCategory = session('itemCategory');
			$itemCategory = array_merge($itemCategory, [
				[
					"id" => uniqid(),
					"description" => $request->item_category_desc,
				]
			]);
			session(['itemCategory' => $itemCategory]);
		} else {
			session([
				'itemCategory' => [
					[
						"id" => uniqid(),
						"description" => $request->item_category_desc,
					]
				]
			]);
		}

		return response()->json(session('itemCategory'));
	}

	public function sessionGetSubCategory()
	{
		if (session('itemCategory') == null) {
			$subCategories = [];
		} else {
			$subCategories = session('itemCategory');
		}
		return response()->json($subCategories);
	}

	public function updateItemcategory(Request $request)
	{
		if (session('itemCategory') != null) {
			$itemCategory = session('itemCategory');
			$key = array_search($request->id, array_column($itemCategory, 'id'));

			$newVal = [
				'id' => $request->id,
				'description' => $request->description,
			];
			$itemCategory[$key] = $newVal;

			session(['itemCategory' => $itemCategory]);
			return response()->json($itemCategory);
		}
	}

	public function deleteItemcategory(Request $request)
	{
		$id = $request->id;

		if (session('itemCategory') != null) {
			$itemCategory = session('itemCategory');
			$key = array_search($id, array_column($itemCategory, 'id'));
			unset($itemCategory[$key]);
			$itemCategory = array_values($itemCategory);
			session(['itemCategory' => $itemCategory]);

			if (session('itemCart') != null) {
				$itemCart = session('itemCart');
				$itemCart = array_filter($itemCart, function ($item) use ($id) {
					return $item['item_category'] !== $id;
				});

				$itemCart = array_values($itemCart);
				session(['itemCart' => $itemCart]);
			}

			$result = new stdClass();
			$result->itemCart = $itemCart;
			$result->itemCategory = $itemCategory;

			return response()->json($result);
		}
	}

	public function getItemById(Request $request)
	{
		$itemCart = session('itemCart');
		$targetUniqid = $request->uniqid;

		$result = array_filter($itemCart, function ($item) use ($targetUniqid) {
			return $item['uniqid'] === $targetUniqid;
		});


		$result = reset($result);

		return response()->json($result);
	}
	public function editItem(Request $request) {}

	public function deleteItem(Request $request)
	{
		if (session('itemCart') != null) {
			$itemCart = session('itemCart');
			$key = array_search($request->uniqid, array_column($itemCart, 'uniqid'));
			unset($itemCart[$key]);
			$itemCart = array_values($itemCart);
			session(['itemCart' => $itemCart]);

			return response()->json($itemCart);
		}
	}

	public function store(Request $request)
	{
		$request->validate([
			'IssueDate' => 'required',
			'DocDate' => 'required',
			'doctype' => 'required',
			'material_code' => 'required|exists:master_material,id',
			'product_code' => 'required|exists:product_code,id',
			'remark' => 'max:200',
			'revisi' => 'max:200',
			'rev0' => 'max:200',
			'page' => 'max:200',
			'base_uom' => 'required',
			'total' => 'required|numeric'
		]);


		if (session('itemCart') == null) {
			return Redirect::back()
				->withInput()
				->withErrors(['message' => "Item List tidak boleh kosong"]);
		}

		$itemCart = session('itemCart');
		$itemCategory = session('itemCategory');

		// dd($itemCategory);

		DB::beginTransaction();

		try {
			// ScaleUP Header

			// get doctype
			$subType = DB::table('sub_category')->where('id', $request->doctype)->first();
			$doc = DB::table('doctype')->where('id', $subType->doctype_id)->first();
			// dd($doc);

			// Get Table approver
			$approver = DB::table('approval')->where('doctype_id', '=', $doc->id)
				->leftJoin('users', 'approval.user_id', 'users.id')
				->select('approval.*', 'users.email as email')
				->orderBy('level', 'ASC')->get()->toArray();


			if (count($approver) < 1) {
				return Redirect::back()
					->withInput()
					->withErrors(['message' => "Approver dokumen ini belum disetting, Silahkan hubungi admin"]);
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

			// dd($doc_number);
			$header_data = [
				"doc_number" => $doc_number,
				"issue_date" => Carbon::createFromFormat('d-m-Y', $request->IssueDate)->toDateString(),
				"doc_date" => Carbon::createFromFormat('d-m-Y', $request->DocDate)->toDateString(),
				"doctype_id" => $request->doctype,
				"material_id" => $request->material_code,
				"remark" => $request->remark,
				"revision" => $request->revisi,
				"halaman" => $request->page,
				"base_qty" => $request->base_qty,
				"base_uom" => $request->base_uom,
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

			$item_category_data = [];
			foreach ($itemCategory as $item) {
				$item_category_item = [
					'uniqid' => $item['id'],
					'description' => $item['description'],
					'created_at' => Carbon::now(),
					'user_id' => Auth::user()->id,
				];
				$item_category_data[] = $item_category_item;
			}
			DB::table('item_category')->insert($item_category_data);

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

			$email = $filtered->pluck('email')->toArray();
			$material = DB::table('master_material')->where('id', $doc_id->material_id)->first();


			$data = $doc_id;
			$data->type = 'request';
			$data->intro = "Mohon Untuk di Approve permohonan dokumen Scale Up berikut ini :";
			$data->material = $material;
			// Mail::to($email)
			// 	->cc(env('MAIL_FROM_ADDRESS'))
			// 	->send(new AprrovalNotification($data));

			DB::commit();

			session()->forget('itemCart');
			session()->forget('itemCategory');
			session()->forget('headerData');
			return redirect()->route('scaleup.index')->with(['message' => [
				"type" => "success",
				"text" => "Scale Up {$header->doc_number} Berhasil disimpan "
			]]);
		} catch (\Throwable $th) {
			DB::rollBack();
			dd($th);
		}
	}


	public function print($id)
	{
		$doc_number = base64_decode($id);
		$header =  DB::table('scaleup_header as sh')
			->select('sh.*', 'mm.material_code as material_code', 'mm.material_description as material_desc', 'mm.material_uom as uom', 'u.name as name')
			->leftJoin('master_material as mm', 'mm.id', '=', "sh.material_id")
			->leftJoin('users as u', 'u.id', '=', 'sh.user_id')
			->where('sh.doc_number', $doc_number)
			->first();

		if (!$header) {
			return abort('404');
		}

		if ($header->status != 'A') {
			return redirect('/scaleup')
				->withErrors(['message' => "Hanya Dokumen Approve yang bisa di Print"]);
		}

		$detail = ScaleUpDetail::where('scaleup_header_id', $header->id)->get();
		$approval = DB::table("approval_document as ad")
			->select('ad.*', 'users.name as name')
			->join('users', 'users.id', '=', 'ad.user_id')
			->orderBy('ad.level', 'ASC')
			->where('scaleup_header_id', $header->id)
			->get();

		$pdf = Pdf::loadView('pdf.scaleup', [
			"header" => $header,
			"detail" => $detail,
			"approval" => $approval,
		]);

		return $pdf->stream("{$header->doc_number}.pdf");
	}

	public function approve($id)
	{
		$doc_number = base64_decode($id);
		$header =  DB::table('scaleup_header as sh')
			->select('sh.*', 'mm.material_code as material_code', 'mm.material_description as material_decription', 'mm.material_uom as uom', 'u.name as name')
			->leftJoin('master_material as mm', 'mm.id', '=', "sh.material_id")
			->leftJoin('users as u', 'u.id', '=', 'sh.user_id')
			->where('sh.doc_number', $doc_number)
			->first();
		if (!$header) {
			return abort('404');
		}

		$approval = DB::table("approval_document as ad")
			->select('ad.*', 'users.name as name')
			->leftJoin('users', 'users.id', '=', 'ad.user_id')
			->orderBy('ad.level', 'ASC')
			->where('scaleup_header_id', $header->id)
			->get();

		$me = Auth::user();
		if (!$approval->contains('user_id', $me->id)) {
			return abort('401');
		}
		$valid = true;
		$approver = DB::table('approval_document')->where([['user_id', $me->id], ['scaleup_header_id', $header->id]])->orderBy('level', 'ASC')->first();


		if ($approver->status == 'A' || $approver->status == 'R') {
			$valid = false;
		}

		foreach ($approval as $item) {
			if ($item->level < $approver->level && $item->status == 'P') {
				$valid = false;
				break;
			}
		}


		$detail = ScaleUpDetail::where('scaleup_header_id', $header->id)->get();
		return view('scaleup.approve', [
			"header" => $header,
			"detail" => $detail,
			"approver" => $approver,
			'valid' => $valid,
		]);
	}

	public function approvestore(Request $request, $id)
	{
		$action = $request->action;

		DB::beginTransaction();
		try {
			$update = ApprovalDocument::find($id);

			$header = ScaleUpHeader::find($update->scaleup_header_id);

			$material = DB::table('master_material')->where('id', $header->material_id)->first();

			$update->note = $request->note;
			$update->updated_at = Carbon::now();
			$update->status = $action;
			$update->update_user = Auth::user()->id;
			$update->save();

			if ($action == 'R') {
				$header->status = 'R';
				$header->updated_at = Carbon::now();
				$header->save();

				$email = DB::table('users')->where('id', $header->user_id)->pluck('email')->toArray();
				$data = $header;
				$data->type = 'notif';
				$data->intro = "Dokumen Sacaleup dibawah ini sudah di-Reject :";
				$data->material = $material;
				Mail::to($email)
					->cc(env('MAIL_FROM_ADDRESS'))
					->send(new AprrovalNotification($data));
			} elseif ($action == 'A') {
				$next = ApprovalDocument::where('scaleup_header_id', $update->scaleup_header_id)
					->where('status', 'P')
					->orderBy('level', 'ASC')
					->get();

				if (count($next) > 0) {
					// Kirim email ke next approval
					$min = $next->min('level');
					$filtered = $next->filter(function ($firstApprover) use ($min) {
						return $firstApprover->level == $min;
					});
					$email = $filtered->pluck('email')->toArray();
					// return response()->json($filtered);
					$data = $header;
					$data->type = 'request';
					$data->intro = "Mohon Untuk di Approve permohonan dokumen Scale Up berikut ini :";
					$data->material = $material;
					Mail::to($email)
						->cc(env('MAIL_FROM_ADDRESS'))
						->send(new AprrovalNotification($data));
				} else {
					$header->status = 'A';
					$header->updated_at = Carbon::now();
					$header->save();

					// kirim emil ke requester sudah approve
					$email = DB::table('users')->where('id', $header->user_id)->pluck('email')->toArray();
					$data = $header;
					$data->type = 'notif';
					$data->intro = "Dokumen Sacaleup dibawah ini sudah di-Approve :";
					$data->material = $material;
					Mail::to($email)
						->cc(env('MAIL_FROM_ADDRESS'))
						->send(new AprrovalNotification($data));
				}
			}
			DB::commit();
			return redirect('/scaleup')->with('message', 'success|Data Berhasil disimpan');
		} catch (\Throwable $th) {
			// dd($th);
			abort(500);
		}
	}

	public function compare()
	{
		return view('scaleup.compare');
	}

	public function getByID(Request $request)
	{
		$data = new stdClass();
		$id = $request->id;
		$header =  DB::table('scaleup_header as sh')
			->select('sh.*', 'map.sap_code as material_code', 'mm.material_description', 'u.name as name', 'pc.product_code')
			->leftJoin('master_material as mm', 'mm.id', '=', "sh.material_id")
			->leftJoin('users as u', 'u.id', '=', 'sh.user_id')
			->leftJoin('product_code as pc', 'pc.id', '=', 'sh.material_id')
			->leftJoin('map_sap_ci as map', 'map.ci_id', '=', 'sh.material_id')
			->where('sh.id', $id)
			->first();

		$detail = ScaleUpDetail::where('scaleup_header_id', $id)->get();
		$data->header = $header;
		$data->detail = $detail;

		return response()->json($data);
	}

	private function test()
	{
		$categoryId = 2;


		$content = [
			[
				'id' => 1,
				"title" => "judul 1",
				"category_id" => 1,
			],
			[
				'id' => 2,
				"title" => "judul 2",
				"category_id" => 1,
			],
			[
				'id' => 3,
				"title" => "judul 3",
				"category_id" => 2,
			],
			[
				'id' => 4,
				"title" => "judul 4",
				"category_id" => 1,
			],
			[
				'id' => 5,
				"title" => "judul 5",
				"category_id" => 1,
			],
			[
				'id' => 6,
				"title" => "judul 6",
				"category_id" => 2,
			],
		];
	}
}

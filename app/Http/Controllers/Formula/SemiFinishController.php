<?php

namespace App\Http\Controllers\Formula;

use App\Http\Controllers\Controller;
use App\Models\FormulaDetail;
use App\Models\FormulaHeader;
use App\Models\ScaleUpHeader;
use App\Models\BomApproval;
use App\Models\KeyCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use App\Mail\AprrovalNotification;

class SemiFinishController extends Controller
{

    public function index()
    {

        $formula = DB::table('formula_header')->paginate(15);
        return view('formula.index', compact('formula'));
    }
    public function create()
    {
        return view('formula.create-sf');
    }

    public function show($id)
    {
        $doc_number = base64_decode($id);
        $header = DB::table('formula_header as fh')
            ->join('users', 'users.id', '=', 'fh.user_id')
            ->select('fh.*', 'users.name as name')
            ->where('doc_number', $doc_number)->first();

        if (!$header) {
            return abort('404');
        }

        $detail = DB::table('formula_detail')
            ->where('formula_header_id', $header->id)->get();

        $approval = DB::table("bom_approvals as ad")
            ->select('ad.*', 'users.name as name')
            ->join('users', 'users.id', '=', 'ad.user_id')
            ->where('formula_header_id', $header->id)
            ->orderBy('ad.level', 'ASC')
            ->get();
            // dd($approval);

        return view('formula.show', [
            "header" => $header,
            "detail" => $detail,
            "approval" => $approval,
        ]);
    }

    
    
    // ADD untuk menampilkan formulir dengan keycode (SP)
    public function createWithKeycode($id)
	{
        // dd('abc');
		$key_code = '';
		try {
			$key_code = Crypt::decryptString($id);
		} catch (\Throwable $th) {
			abort('404');
			//throw $th;
		}

		$keycode = KeyCode::leftJoin('product_new as pn', 'pn.id', '=', 'keycode.product_id')
			->leftJoin('sub_category as sc', 'sc.id', '=', 'pn.product_type')
			->select('keycode.*', 'pn.sap_code', 'sc.id as cotegry_id', 'sc.description as category_description')
			->where([
				'keycode.key_code' => $key_code,
				'keycode.is_used' => false,
				'keycode.status' => 'A',
				'keycode.user_id' => Auth::user()->id,
			])->first();

		if (!$keycode) {
			return redirect()->route('keycode.process')->with(['message' => [
				"type" => "error",
				"text" => "Keycode yang anda gunakan tidak valid. keycode sudah digunakan atau user anda tidak mempunyai authorisasi untuk keycode ini"
			]]);
		}


		$keycode->key_code = Crypt::encryptString($keycode->key_code);

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

		return view('formula.create-sf', compact('itemCart', 'itemCategory', 'keycode'));
	}

    public function storeWithKeycode(Request $request, $id)
	{
		$key_code = '';
		try {
			$key_code = Crypt::decryptString($id);
		} catch (\Throwable $th) {
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
            'scaleup' => 'required|string',
            'plant' => 'required',
            'bom_usage' => 'required',
            'valid_from' => 'required',
            'doc_date' => 'required',
            'material' => 'required|exists:material_sap,sap_code',
            'material_description' => 'required',
            'detail.*.qty_sap' => 'required|numeric',
            'detail.*.sloc' => 'required',
            'detail.*.material_code' => 'required',
            'detail.*.material_description' => 'required',
        ]);

        $status = '';
		if ($request->action == 'draft') {         // jika memilih draft
            $status = 'D'; // Draft
		} else if ($request->action == 'save') {    // jika memilih save
            $status = 'P'; // Pending
        }

        

        DB::beginTransaction();
        try {
            $scaleup =   DB::table('scaleup_header')->where('id', $request->scaleup)->first();
            $doc = DB::table('sub_category as sc')
                        ->select('sc.*', 'doctype.doctype')
                        ->join('doctype', 'doctype.id', '=', 'sc.doctype_id')
                        ->where('sc.id', $scaleup->doctype_id)->first();

            $approver = DB::table('approval')
                        ->select('approval.*', 'users.email as email')
                        ->leftJoin('users', 'approval.user_id', 'users.id')
                        ->where('doctype_id', '=', $doc->doctype_id)
                        ->where('transaction_type', '=', 'formula-create')
                        ->orderBy('level', 'ASC')->get()->toArray();


            if (count($approver) < 1) {
                return Redirect::back()
                    ->withInput()
                    ->with(['message' => [
                        "type" => "error",
                        "text" => "Approver Belum disetting, Hubungi admin"
                    ]]);
            }

            // create doc number,
            $year = explode('-', $request->doc_date)[2];
            $month = explode('-', $request->doc_date)[1];
            $month = number2roman(intVal($month));

            $first = "FRM/{$doc->doctype}/{$year}/{$month}/";

            // Gel last Doc No
            $last = DB::table('formula_header')->where('doc_number', 'LIKE', "%{$first}%")->latest()->first();
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
                "doc_date" => Carbon::createFromFormat('d-m-Y', $request->doc_date)->toDateString(),
                "valid_date" => Carbon::createFromFormat('d-m-Y', $request->valid_from)->toDateString(),
                "scaleup_header_id" => $request->scaleup,
                "material_code" => $request->material,
                "material_description" => $request->material_description,
                "plant" => $request->plant,
                "bom_usage" => $request->bom_usage,
                "base_qty" => $request->base_qty,
                "base_uom" => $request->uom,
                "remark" => $request->header_remark,
                "user_id" => Auth::user()->id,
                "status" => $status,
                "is_active" => true,
                "created_at" => Carbon::now(),
            ];

            $header = new FormulaHeader();
            $formulaHeader = $header->create($header_data);
            $detail_data = [];
            foreach ($request->detail as $item) {
                $item_detail = [
                    "formula_header_id" => $formulaHeader->id,
                    "material_code" => $item["material_code"],
                    "material_description" => $item["material_description"],
                    "qty" => $item["qty_sap"],
                    "sloc" => $item["sloc"],
                    "item_category" => "L",
                    "valid_from" => Carbon::createFromFormat('d-m-Y', $request->valid_from)->toDateString(),
                    "created_at" => Carbon::now()
                ];
                $detail_data[] = $item_detail;
            }

            FormulaDetail::insert($detail_data);

            $approval_doc = [];
            foreach ($approver as $app) {
                $app_item = [
                    "formula_header_id" => $formulaHeader->id,
                    "user_id" => $app->user_id,
                    "level" => $app->level,
                    "email" => $app->email,
                    "status" => "P",
                    "create_user" => Auth::user()->id,
                    // "created_at" => Carbon::now()
                ];

                $approval_doc[] = $app_item;
            }

            DB::table('bom_approvals')->insert($approval_doc);
            $keycode->is_used = true;
            $keycode->formula_header_id = $formulaHeader->id;
            $keycode->save();

            DB::commit();
            return redirect()->route('sf.index')->with(['message' => [
                "type" => "success",
                "text" => "Formula {$header->doc_number} Berhasil disimpan "
            ]]);
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
        }
        
    }

    public function listSemiFinish(Request $request)
	{
		$q = strtolower($request->q);

		$material =DB::table('formula_header as fh')
                        ->select('fh.*',  'sh.product_code')
                        ->join('scaleup_header as sh', 'sh.id', '=', 'fh.scaleup_header_id')
                        ->where('fh.user_id', Auth::user()->id)
                        ->whereIn('fh.status', ['P', 'A', 'R'])
                        ->where(function ($query) use ($q) {
                            $query->whereRaw('LOWER(fh.doc_number) LIKE ?', ['%' . $q . '%'])
                                ->orWhereRaw('LOWER(fh.material_description) LIKE ?', ['%' . $q . '%'])
                                ->orWhereRaw('LOWER(sh.product_code) LIKE ?', ['%' . $q . '%']);
                        })
                        ->orderBy('sh.product_code')
                        ->get();
    
        
        // dd($material);

		return response()->json($material, 200);
	}

    public function getHeader(Request $request)
	{
		$id = $request->id;
		$header =  DB::table('formula_header as fh')
			->where('fh.id', $id)
			->first();
		return response()->json($header);
	}

    public function listScaleUpPush(Request $request)
	{
		
		$q = strtolower($request->q);
		$material = DB::table('scaleup_header as sh')
            ->where('sh.is_push', true)
			->where('sh.status', 'A')
			->where(function ($query) use ($q) {
				$query->whereRaw('LOWER(sh.doc_number) LIKE ?', ['%' . $q . '%'])
					->orWhereRaw('LOWER(sh.product_code) LIKE ?', ['%' . $q . '%'])
					->orWhereRaw('LOWER(sh.material_description) LIKE ?', ['%' . $q . '%']);
			})
			->orderBy('sh.product_code')
			->get();
		// dd($material);
		return response()->json($material, 200);
	}

    public function listScaleUp($id)
	{
        $key_code = '';
		try {
			$key_code = Crypt::decryptString($id);
		} catch (\Throwable $th) {
			abort('404');
		}

		$material = DB::table('scaleup_header as sh')
            ->select('sh.*')
            ->join('keycode', 'keycode.scaleup_header_id', '=', 'sh.id')
			->where('sh.status', 'A')
            ->where('sh.is_push', true)
            ->where('keycode.key_code', $key_code)
			->orderBy('sh.product_code')
			->get();

		return response()->json($material, 200);
	}

    // Get data Dokumen formula semi finish yang akan di approve
    public function approve($id)
	{
		$doc_number = '';
		try {
			$doc_number = Crypt::decryptString($id);
		} catch (\Throwable $th) {
			abort('404');
		}

		$header =  DB::table('formula_header as fh')
			->select('fh.*', 'u.name as name', 'sh.product_code as product_code')
			->leftJoin('users as u', 'u.id', '=', 'fh.user_id')
            ->leftJoin('scaleup_header as sh', 'sh.id', '=', 'fh.scaleup_header_id')
			->where('fh.doc_number', $doc_number)
			->first();

		if (!$header) {
			return abort('404');
		}

		$approval = DB::table("bom_approvals as ba")
			->select('ba.*', 'users.name as name')
			->leftJoin('users', 'users.id', '=', 'ba.user_id')
			->where('ba.formula_header_id', $header->id)
            ->orderBy('ba.level', 'ASC')
			->get();
     
		$me = Auth::user();
		if (!$approval->contains('user_id', $me->id)) {
			return abort('401');
		}
		$valid = true;
		$approver = DB::table('bom_approvals')->where([['user_id', $me->id], ['formula_header_id', $header->id], ['status', 'P']])->orderBy('level', 'ASC')->first();
		if (!$approver) {
			$approver = DB::table('bom_approvals')->where([['user_id', $me->id], ['formula_header_id', $header->id]])->orderBy('level', 'ASC')->first();
		}


		if ($approver->status == 'A' || $approver->status == 'R') {
			$valid = false;
		}

		foreach ($approval as $item) {
			if ($item->level < $approver->level && $item->status == 'P') {
				$valid = false;
				break;
			}
		}


		$detail = formulaDetail::where('formula_header_id', $header->id)->get();
        
		$category_ref = $detail->pluck('category_reference')->unique();
		$item_category = DB::table('item_category')->whereIn('uniqid', $category_ref)->get();

		return view('formula.sf-approve', [
			"header" => $header,
			"detail" => $detail,
			"approver" => $approver,
			'valid' => $valid,
			"itemCategory" => $item_category,
		]);
	}
	

    // Save data dokumen formula semi finish dengan status approve
	public function approvestore(Request $request, $key)
	{

		$id = '';
		try {
			$id = Crypt::decryptString($key);
		} catch (\Throwable $th) {
			abort('404');
		}

		$action = $request->action;

		DB::beginTransaction();
		try {
			$update = BomApproval::find($id);

			$header = FormulaHeader::find($update->formula_header_id);
			$requester = DB::table('users')->where('id', $header->user_id)->first();

			$update->note = $request->note;
			$update->updated_at = Carbon::now();
			$update->status = $action;
			$update->update_user = Auth::user()->id;
			$update->save();

			if ($action == 'R') {
				$header->status = 'R';
				$header->updated_at = Carbon::now();
				$header->save();

				$data = $header;
				$data->type = 'notif';
				$data->intro = "Dokumen Formula Semi Finish dibawah ini sudah di-Reject :";
				Mail::to($requester->email)
					->send(new AprrovalNotification($data));

				DB::commit();
				return redirect('/approve/formula/semifinish')->with(['message' => [
					"type" => "error",
					"text" => "Anda sudah Menolak Formula Semi Finish"
				]]);
			} elseif ($action == 'A') {
				$next = BomApproval::where('formula_header_id', $update->formula_header_id)
					->where('status', 'P')
					->orderBy('level', 'ASC')
					->get();
				if (count($next) > 0) {
					// Kirim email ke next approval
					$min = $next->min('level');
					$filtered = $next->filter(function ($firstApprover) use ($min) {
						return $firstApprover->level == $min;
					});

					$data = $header;
					$data->type = 'request';
					$data->intro = "Mohon Untuk di Approve permohonan dokumen Formula Semi Finish berikut ini :";

					foreach ($filtered as $item) {
						if ($item->sent_at == null) {
							$data->link = Crypt::encryptString($header->doc_number . "|" . $item->id . "|" . $item->created_at);
							Mail::to($item->email)
								->send(new AprrovalNotification($data));
							$item->update(['sent_at' => Carbon::now()]);
							$item->save();
						}
					}
				} else {
					$header->status = 'A';
					$header->updated_at = Carbon::now();
					// Generate revisian pada scaleup header +1 jika approver
					// $product_code = $header->product_code;
					$lastRow = DB::table('formula_header')
						->where([
							// 'product_code' => $product_code,
                            'id' => $header->id,
							'status' => 'A'
						])
						->orderBy('revision', 'DESC')
						->first();

					$rev = 0;

					if (!$lastRow) {
						$rev = 0;
					} else {
						$rev = (int)$lastRow->revision + 1;
					}
					$header->revision = $rev;
					$header->save();

					// kirim emil ke requester sudah approve
					$data = $header;
					$data->type = 'notif';
					$data->intro = "Dokumen Formula Semi Finish dibawah ini sudah di-Approve :";

					Mail::to($requester->email)
						->send(new AprrovalNotification($data));
				}
			}
			DB::commit();
			return redirect('/approve/formula/semifinish')->with(['message' => [
				"type" => "success",
				"text" => "Anda sudah menyetujui request Formula Semi Finish"
			]]);
		} catch (\Throwable $th) {
			dd($th);
			// abort(500);
		}
	}



}

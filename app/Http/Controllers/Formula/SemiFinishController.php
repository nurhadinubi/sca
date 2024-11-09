<?php

namespace App\Http\Controllers\Formula;

use App\Http\Controllers\Controller;
use App\Models\FormulaDetail;
use App\Models\FormulaHeader;
use App\Models\ScaleUpHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

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
            ->orderBy('ad.level', 'ASC')
            ->where('formula_header_id', $header->id)
            ->get();
        return view('formula.show', [
            "header" => $header,
            "detail" => $detail,
            "approval" => $approval,
        ]);
    }

    public function store(Request $request)
    {
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

        // $detail_data = [];
        // foreach ($request->detail as $item) {
        //     $item_detail = [
        //         "formula_header_id" => $request->scaleup,
        //         "material_code" => $item["material_code"],
        //         "material_description" => $item["material_description"],
        //         "qty" => $item["qty_sap"],
        //         "sloc" => $item["sloc"],
        //         "item_category" => "L",
        //         "valid_from" => Carbon::createFromFormat('d-m-Y', $request->valid_from)->toDateString(),
        //         "created_at" => Carbon::now()
        //     ];
        //     $detail_data[] = $item_detail;
        // }

        // dd($detail_data);

        DB::beginTransaction();
        try {
            $scaleup =   DB::table('scaleup_header')->where('id', $request->scaleup)->first();
            // $subType = DB::table('sub_category')->where('id', $request->doctype)->first();
            $doc = DB::table('sub_category as sc')
                ->join('doctype', 'doctype.id', '=', 'sc.doctype_id')
                ->select('sc.*', 'doctype.doctype')
                ->where('sc.id', $scaleup->doctype_id)->first();

            $approver = DB::table('approval')->where('doctype_id', '=', $doc->doctype_id)
                ->leftJoin('users', 'approval.user_id', 'users.id')
                ->select('approval.*', 'users.email as email')
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
                "status" => "P",
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
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Doctype;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubDivisionController extends Controller
{
    public function index()
    {
        $subs = DB::table('sub_category as sub')
            ->join('doctype as doc', 'doc.id', '=', 'sub.doctype_id')
            ->select('sub.*', 'doc.doctype')
            ->orderBy('doc.doctype', 'asc')
            ->paginate(15);

        return view('subdivision.index', compact('subs'));
    }


    public function create()
    {
        $divisions = Doctype::all();
        return view('subdivision.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'division' => 'required|exists:doctype,id',
            'code' => 'required|unique:sub_category,code',
            'description' => 'required'
        ]);


        $insert = DB::table('sub_category')->insert(['doctype_id' => $request->division, 'code' => $request->code, 'description' => $request->description, 'created_at' => Carbon::now()]);

        if (!$insert) {
            abort(500);
        }

        return redirect()->route('sub-div.index')->with(['message' => [
            "type" => "success",
            "text" => "Divisi sudah ditambahkan"
        ]]);
    }

    public function edit($id)
    {
        $sub = DB::table('sub_category')->where('id', $id)->first();
        $divisions = Doctype::all();

        if (!$sub) {
            abort(404);
        }
        return view('subdivision.edit', compact('sub', 'divisions'));
    }
    public function update(Request $request, $id)
    {
        $sub = DB::table('sub_category')->where('id', $id)->first();

        if (!$sub) {
            abort(404);
        }
        $codeVal = $sub->code == $request->code ? 'required' : 'required|unique:doctype,doctype';
        $request->validate([
            'code' => $codeVal,
            'description' => 'required',
            'division' => 'required|exists:doctype,id',
        ]);

        $data = DB::table('sub_category')->upsert(['doctype_id' => $request->division, 'code' => $request->code, 'description' => $request->description, 'id' => $id, 'updated_at' => Carbon::now()], 'id');

        if (!$data) {
            abort(500);
        }

        return redirect()->route('sub-div.index')->with(['message' => [
            "type" => "success",
            "text" => "Divisi sudah diupdate"
        ]]);
    }
}

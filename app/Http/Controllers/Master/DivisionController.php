<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Doctype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Doctype::paginate(15);
        return view('division.index', compact('divisions'));
    }

    public function create()
    {
        return view('division.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:doctype,doctype',
            'description' => 'required'
        ]);

        $insert = DB::table('doctype')->insert(['doctype' => $request->code, 'description' => $request->description]);

        if (!$insert) {
            abort(500);
        }

        return redirect()->route('division.index')->with(['message' => [
            "type" => "success",
            "text" => "Divisi sudah ditambahkan"
        ]]);
    }

    public function edit($id)
    {
        $division = DB::table('doctype')->where('id', $id)->first();

        if (!$division) {
            abort(404);
        }
        return view('division.edit', compact('division'));
    }
    public function update(Request $request, $id)
    {
        $division = DB::table('doctype')->where('id', $id)->first();

        if (!$division) {
            abort(404);
        }
        $codeVal = $division->doctype == $request->code ? 'required' : 'required|unique:doctype,doctype';
        $request->validate([
            'code' => $codeVal,
            'description' => 'required'
        ]);

        $data = DB::table('doctype')->where('id', $id)->update(['doctype' => $request->code, 'description' => $request->description]);

        if (!$data) {
            abort(500);
        }

        return redirect()->route('division.index')->with(['message' => [
            "type" => "success",
            "text" => "Divisi sudah diupdate"
        ]]);
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialSAPController extends Controller
{
	public function list(Request $request, $type = 'ROH1')
	{
		$q = strtolower($request->q);
		$data = DB::table('material_sap')
			->where(function ($query) use ($q) {
				$query->whereRaw("LOWER(sap_code) LIKE ?", ['%' . $q . '%'])
					->orWhereRaw('LOWER(sap_description) LIKE ?', ['%' . $q . '%']);
			})
			->where('material_type', $type)
			->where('is_deleted', false)
			->get();
		return response()->json($data, 200);
	}


	public function byId(Request $request)
	{
		$data = DB::table('material_sap')
			->where('id', $request->id)
			->first();
		return response()->json($data, 200);
	}


	public function createRM()
	{
		return view('materialci.create-rm');
	}

	public function store(Request $request)
	{
		$request->validate([
			'sap_code' => 'nullable|unique:material_sap,sap_code',
			'desc' => 'required',
			'sap_type' => 'required',
			'unit' => 'required',
		]);


		$data = [
			'sap_code' => $request->sap_code,
			'sap_description' => $request->desc,
			'sap_description' => $request->desc,
			'material_uom' => $request->unit,
			'material_type' => $request->sap_type,
			'created_at' => Carbon::now(),
		];

		$id = DB::table('material_sap')->insertGetId($data);
		if (!$id) {
			return abort('404');
		}
		// $data = DB::table('material_sap')->where('id', $id)->first();
		return redirect()->route('ci.list')->with(['message' => [
			"type" => "success",
			"text" => "material berhasil ditambahkan"
		]]);
	}
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ProductNew;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductCIController extends Controller
{
	public function getProducts(Request $request)
	{
		$q = strtolower($request->q);
		$products = DB::table('product_code')
			->where(function ($query) use ($q) {
				$query->whereRaw("LOWER(product_code) LIKE ?", ['%' . $q . '%'])
					->orWhereRaw('LOWER(description) LIKE ?', ['%' . $q . '%']);
			})
			->get();

		return response()->json($products, 200);
	}

	public function productList(Request $request, $type)
	{
		$q = strtolower($request->q);
		$products = DB::table('product_new')
			->where(function ($query) use ($q) {
				$query->whereRaw("LOWER(sap_code) LIKE ?", ['%' . $q . '%'])
					->orWhereRaw('LOWER(description) LIKE ?', ['%' . $q . '%']);
			})
			->where('sap_type', '=', $type)
			->get();
		return response()->json($products);
	}

	public function productExist(Request $request)
	{
		$q = strtolower($request->q);
		$products = DB::table('product_new')
			->where(function ($query) use ($q) {
				$query->whereRaw("LOWER(sap_code) LIKE ?", ['%' . $q . '%'])
					->orWhereRaw('LOWER(product_code) LIKE ?', ['%' . $q . '%'])
					->orWhereRaw('LOWER(description) LIKE ?', ['%' . $q . '%']);
			})
			->where('is_active', true)
			->whereNot('product_code', '=', null)
			->get();
		return response()->json($products);
	}
	public function productById(Request $request)
	{

		$product = DB::table('product_new as pn')
			->join('sub_category as cat', 'cat.id', '=', 'pn.product_type')
			->select('pn.*', 'cat.description as category_description')
			->where('pn.id', $request->id)
			->first();
		return response()->json($product);
	}

	public function create()
	{

		$categories = DB::table('sub_category')->where('is_deleted', '!=', true)->get();
		return view('materialci.create', compact('categories'));
	}

	public function store(Request $request)
	{

		$request->validate([
			'desc' => 'required',
			'unit' => 'required',
			'product_type' => 'required|exists:sub_category,id',
			'product_code' => 'unique:product_new,product_code',
			'sap_type' => 'required',
			'sap_code' => 'nullable|unique:product_new,sap_code|exists:material_sap,sap_code'
		]);

		$created_at = Carbon::now();
		$data = [
			"description" => $request->desc,
			"product_type" => $request->product_type,
			"product_code" => $request->product_code,
			"sap_type" => $request->sap_type,
			"sap_code" => $request->sap_code,
			"uom" => $request->unit,
			"created_at" => $created_at,
			"user_id" => Auth::user()->id
		];

		$id = DB::table('product_new')->insertGetId($data);
		if (!$id) {
			return redirect()->back()->withInput()->with(['message' => [
				"type" => "error",
				"text" => "Data gagal disimpan"
			]]);
		}

		return redirect()->back()->with(['message' => [
			"type" => "success",
			"text" => "Data berhasil disimpan"
		]]);
	}

	public function edit($id)
	{

		$categories = DB::table('sub_category')->where('is_deleted', '!=', true)->get();
		$product = DB::table('product_new as pn')
			->leftJoin('sub_category as sc', 'sc.id', 'pn.product_type')
			->select('pn.*', 'sc.description as category_description')
			->where('pn.id', $id)
			->first();

		if (!$product) {
			abort(404);
		}
		return view('product.edit', compact('categories', 'product'));
	}


	public function update(Request $request, $id)
	{
		$product = ProductNew::where('id', $id)->first();
		if (!$product) {
			abort(404);
		}

		if ($request->action == 'active') {
			$product->is_active = !$product->is_active;
			$active = $product->save();
			if (!$active) {
				return redirect()->back()->withInput()->with(['message' => [
					"type" => "error",
					"text" => "Data gagal diupdate"
				]]);
			}

			return redirect()->route('ci.list')->with(['message' => [
				"type" => "success",
				"text" => "Data berhasil disimpan"
			]]);
		}

		if ($request->action == 'update') {
			$sapVal = $product->sap_code != $request->sap_code ? 'unique:product_new,sap_code|' : '';
			$pcVal = $product->product_code != $request->product_code ? 'unique:product_new,product_code|' : '';
			$request->validate([
				'desc' => 'required',
				'unit' => 'required',
				'product_type' => 'required|exists:sub_category,id',
				'product_code' => $pcVal . 'nullable',
				'sap_type' => 'required',
				'sap_code' => $sapVal . 'nullable|exists:material_sap,sap_code'
			]);

			$data = [
				"description" => $request->desc,
				"product_type" => $request->product_type,
				"product_code" => $request->product_code,
				"sap_type" => $request->sap_type,
				"sap_code" => $request->sap_code,
				"uom" => $request->unit,
				"updated_at" => Carbon::now(),
			];
			$update = $product->update($data);

			if (!$update) {
				return redirect()->back()->withInput()->with(['message' => [
					"type" => "error",
					"text" => "Data gagal diupdate"
				]]);
			}

			return redirect()->route('ci.list')->with(['message' => [
				"type" => "success",
				"text" => "Data berhasil disimpan"
			]]);
		}
	}
	public function list()
	{
		$categories = DB::table('sub_category')->get();
		// $products = DB::table('product_new as pn')
		// 	->leftJoin('sub_category as sc', 'sc.id', 'pn.product_type')
		// 	->select('pn.*', 'sc.description as category_description')
		// 	->where('pn.sap_type', 'HALB')
		// 	->orderBy('pn.description', 'asc')
		// 	->paginate(15);

		return view('product.index', compact('categories'));
	}
	public function rm()
	{

		$products = DB::table('material_sap as ms')
			->where('material_type', "LIKE", 'ROH%')
			->get();
		return view('product.rm', compact('products'));
	}




	public function storeOld(Request $request)
	{
		$request->validate([
			'desc' => 'required',
			'unit' => 'required',
			'product_type' => 'required|exists:product_new,product_type',
			'sap_type' => 'required',
			'product_code' => 'unique:product_new,product_code',
			'sap_code' => 'unique:product_new,sap_code|exists:material_sap,sap_code'
		]);

		if ($request->sap_code) {
			$combine = DB::table('map_sap_ci')
				->where('sap_code', $request->sap_code)
				->first();
			if ($combine) {
				return redirect()->back()->withInput()->with(['message' => [
					"type" => "error",
					"text" => "Kode SAP tersebut sudah digunakan"
				]]);
			}
		}

		$created_at = Carbon::now();
		$data = [
			"material_description" => $request->desc,
			"material_uom" => $request->unit,
			"created_at" => $created_at
		];
		$id = DB::table('master_material')->insertGetId($data);
		if (!$id) {
			return redirect()->back()->withInput()->with(['message' => [
				"type" => "error",
				"text" => "Data gagal disimpan"
			]]);
		}

		if ($request->sap_code) {
			DB::table('map_sap_ci')->insert([
				'ci_id' => $id,
				'sap_code' => $request->sap_code,
				"created_at" => $created_at
			]);
		}

		return redirect()->back()->with(['message' => [
			"type" => "success",
			"text" => "Data berhasil disimpan"
		]]);
	}
}

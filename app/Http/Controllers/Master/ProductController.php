<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ProductNew;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{

	public function index(Request $request)
	{
		$categories = DB::table('sub_category')->get();
		if ($request->ajax()) {
			$query = ProductNew::query()
				->leftJoin('sub_category', 'product_new.product_type', '=', 'sub_category.id')
				->select(
					'product_new.*',
					'sub_category.description as product_type_description'
				)
				->where('sap_type', 'HALB')
				->orderBy('sub_category.description');

			// Apply filter for product_type if present
			if ($request->has('product_type') && !empty($request->product_type)) {
				$query->where('product_new.product_type', $request->product_type);
			}

			return DataTables::of($query)
				->addColumn('product_type_description', function ($row) {
					return $row->product_type_description; // Kolom ditambahkan manual jika DataTables tidak membaca alias
				})
				->addColumn('action', function ($row) {
					return '<a href="' . route('ci.edit', ['id' => $row->id]) . '" class="btn btn-outline-primary btn-sm">Edit</a>';
				})
				->rawColumns(['product_type_description', 'action']) // Jika diperlukan untuk membaca HTML atau data raw
				->make(true);
		}

		return view('product.index', compact('categories'));
	}


	public function create()
	{

		$categories = DB::table('sub_category')->where('is_deleted', '!=', true)->get();
		return view('product.create', compact('categories'));
	}

	public function store(Request $request)
	{
		$request->validate([
			'material' => 'required|exists:product_new,id',
			'sub_category' => 'required|exists:sub_category,id',
			'code' => 'unique:product_new,product_code',
		]);


		$material = DB::table('product_new')
			->where('id', $request->material)
			->first();

		if ($material->product_code) {
			return redirect()->back()->withInput()->with(['message' => [
				"type" => "error",
				"text" => "Material tersebut sudah mempunyai kode produk: " . $material->product_code
			]]);
		}

		$id = DB::table('product_new')
			->where('id', $request->material)
			->update([
				'product_code' => $request->code,
				'product_type' => $request->sub_category,
			]);


		if (!$id) {
			return redirect()->back()->withInput()->with(['message' => [
				"type" => "error",
				"text" => "Data gagal disimpan "
			]]);
		}

		$data = DB::table('product_new')->where('id', $id)->first();
		return redirect()->back()->with(['message' => [
			"type" => "success",
			"text" => "Data {$data->product_code} Berhasil disimpan "
		]]);
	}



	public function storeOld(Request $request)
	{
		$request->validate([
			"sub_category" => 'required|exists:sub_category,id',
			"code" => 'required|unique:product_code,product_code',
			"material" => 'required|exists:master_material,id',
			'description' => "required|min:5|max:200",
		]);

		$material = DB::table('product_code')->where('material_id', $request->material)->first();

		if ($material) {
			return redirect()->back()->withInput()->with(['message' => [
				"type" => "error",
				"text" => "Material tersebut sudah mempunyai kode produk: " . $material->product_code
			]]);
		}

		$id = DB::table('product_code')->insertGetId([
			'product_code' => $request->code,
			'sub_category_id' => $request->sub_category,
			'material_id' => $request->material,
			'description' => $request->description,
			'created_by' => Auth::user()->id,
			'created_at' => Carbon::now(),
		]);

		if (!$id) {
			return redirect()->back()->withInput()->with(['message' => [
				"type" => "error",
				"text" => "Data gagal disimpan "
			]]);
		}

		$data = DB::table('product_code')->where('id', $id)->first();
		return redirect()->back()->with(['message' => [
			"type" => "success",
			"text" => "Data {$data->product_code} Berhasil disimpan "
		]]);
	}
}

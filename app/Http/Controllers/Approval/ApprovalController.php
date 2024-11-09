<?php

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ApprovalController extends Controller
{

	/**
	 * Approval bisa auto skip, reject atu approve jika melewati waktu tertentui
	 * 
	 */
	public function index()
	{
		$approvals = DB::table('approval as ap')
			->leftJoin('users as us', 'ap.user_id', '=', 'us.id')
			->leftJoin('doctype as dc', 'ap.doctype_id', 'dc.id')
			->select('ap.*', 'us.name', 'us.email', 'dc.doctype', 'dc.description')
			->orderBy('ap.transaction_type')
			->orderBy('ap.doctype_id')
			->orderBy('ap.level')
			->paginate(15);

		return view('approval.index', compact('approvals'));
	}

	public function create()
	{
		$doctype = DB::table('doctype')->get();
		$users = DB::table('users')
			->select('users.id', 'users.name', 'users.email')
			->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
			->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
			->where([
				['is_active', true],
				['is_deleted', false],
				['roles.name', 'approver'],
			])
			->get();
		// $users = DB::table('users')->select('id', 'name', 'email')->where([['is_active', true], ['is_deleted', false]])->get();

		return view('approval.create', compact('doctype', 'users'));
	}

	public function store(Request $request)
	{

		$request->validate(
			[
				'doctype' => 'required|exists:doctype,id',
				'user' => 'required|exists:users,id',
				'transaction_type' => 'required',
				'level' => 'required|numeric|between:1,9',
			]
		);

		$combine = DB::table('approval')
			->where('user_id', $request->user)
			->where('level', $request->level)
			->where('transaction_type', $request->transaction_type)
			->where('doctype_id', $request->doctype)
			->first();

		if ($combine) {
			return redirect()->back()->withInput()->with(['message' => [
				"type" => "error",
				"text" => "kombinasi user, dokumen dan level sudah ada"
			]]);
		}


		$data = [
			'user_id' => $request->user,
			'doctype_id' => $request->doctype,
			'level' => $request->level,
			'transaction_type' => $request->transaction_type,
			'is_active' => true,
			'is_deleted' => false,
			'created_at' => Carbon::now()
		];

		$approval = Approval::create($data);


		if (!$approval) {
			return back()->withErrors(['message' => "Data gagal disimpan"]);
		}

		return redirect()->route('master.approval.index')->with(['message' => [
			"type" => "success",
			"text" => "Approval berhasil ditambahkan"
		]]);
	}

	public function edit($id)
	{
		$approval =  DB::table('approval as ap')
			->leftJoin('users as us', 'ap.user_id', '=', 'us.id')
			->leftJoin('doctype as dc', 'ap.doctype_id', 'dc.id')
			->select('ap.*', 'us.name', 'us.email', 'dc.doctype', 'dc.description')
			->orderBy('ap.doctype_id')
			->orderBy('ap.level')
			->where('ap.id', $id)
			->first();

		if (!$approval) {
			return redirect()->back()->withInput()->with(['message' => [
				"type" => "error",
				"text" => "data tidak ditemukan"
			]]);
		}

		// dd($approval);
		$doctype = DB::table('doctype')->get();
		$users = DB::table('users')->select('id', 'name', 'email')->where([['is_active', true], ['is_deleted', false]])->get();

		return view('approval.edit', compact('approval', 'doctype', 'users'));
	}

	function put(Request $request, $id)
	{
		$action = $request->action;

		$approval =  DB::table('approval as ap')
			->leftJoin('users as us', 'ap.user_id', '=', 'us.id')
			->leftJoin('doctype as dc', 'ap.doctype_id', 'dc.id')
			->select('ap.*', 'us.name', 'us.email', 'dc.doctype', 'dc.description')
			->orderBy('ap.doctype_id')
			->orderBy('ap.level')
			->where('ap.id', $id)
			->first();

		if (!$approval) {
			return redirect()->back()->withInput()->with(['message' => [
				"type" => "error",
				"text" => "data tidak ditemukan"
			]]);
		}


		if ($action == "D") {
			$app = DB::table('approval')->where('id', $id)->update([
				'is_deleted' => !$approval->is_deleted,
				'deleted_at' => $approval->is_deleted ? null : Carbon::now()
			]);
			if (!$app > 0) {
				return back()->withInput()->with(['message' => [
					"type" => "error",
					"text" => "Data gagal diupdate"
				]]);
			}

			return redirect()->route('master.approval.index')->with(['message' => [
				"type" => "success",
				"text" => "Approval berhasil dihapus"
			]]);
		} elseif ($action == "U") {
			$checkboxValue = $request->has('is_active') ? true : false;
			$request->merge(['is_active' => $checkboxValue]);
			$request->validate(
				[
					'doctype' => 'required',
					'user' => 'required',
					'level' => 'required|numeric|between:1,9',
				]
			);


			if ($approval->transaction_type == $request->transaction_type && $approval->level == $request->level && $approval->user_id == $request->user && $approval->doctype_id == $request->doctype && $request->is_active == $approval->is_active) {
				return redirect('/master/approval')->withInput()->with(['message' => [
					"type" => "warning",
					"text" => "Anda tidak merubah data apapun"
				]]);
			}

			$combine = DB::table('approval')
				->where('user_id', $request->user)
				->where('level', $request->level)
				->where('transaction_type', $request->transaction_type)
				->where('doctype_id', $request->doctype)
				->where('is_active', $request->is_active)
				->first();

			if ($combine) {
				return redirect()->back()->withInput()->with(['message' => [
					"type" => "error",
					"text" => "kombinasi user, dokumen, dan level sudah ada #update"
				]]);
			}


			$data = [
				'user_id' => $request->user,
				'doctype_id' => $request->doctype,
				'level' => $request->level,
				'transaction_type' => $request->transaction_type,
				'is_active' => $request->is_active,
				'is_deleted' => false,
				'updated_at' => Carbon::now()
			];


			$approval = DB::table('approval')->where('id', $id)->update($data);

			if (!$approval > 0) {
				return back()->withInput()->with(['message' => [
					"type" => "error",
					"text" => "Data gagal diupdate"
				]]);
			}

			return redirect()->route('master.approval.index')->with(['message' => [
				"type" => "success",
				"text" => "Approval berhasil diupdate"
			]]);
		}
	}

	public function needApprove()
	{
		$idDoc = DB::table('approval_document')->where([
			'status' => 'P',
			'user_id' => Auth::user()->id,
		])->pluck('scaleup_header_id')->toArray();

		$scaleup = DB::table('scaleup_header as sh')
			->join('users as u', 'u.id', '=', 'sh.user_id')
			->select('u.name as requester', 'sh.*')
			->where('sh.status', 'P')
			->whereIn('sh.id', $idDoc)->get();
		return view('approval.list-scaleup', compact('scaleup'));
	}

	public function getApproval(Request $request)
	{
		$doc_number = "";
		try {
			$doc_number = Crypt::decryptString($request->id);
		} catch (\Throwable $th) {
			//throw $th;
		}

		$header = DB::table('scaleup_header')->where([
			'doc_number' => $doc_number,
		])->first();

		$approver = DB::table('approval_document')->where('scaleup_header_id', $header->id)->orderBy('level')->get();
		return view('keycode.partial.step', compact('approver'));
	}
}

<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use stdClass;

class PermissionController extends Controller
{
	/**
	 * 
	 * 
	 */
	public function index()
	{
		$permissions = Permission::paginate(15);
		return view('permission.index', compact('permissions'));
	}

	public function addPermissionToUser()
	{
		return view('permission.user-permission');
	}

	public function addRoleToUser()
	{
		$roles = Role::all();
		return view('permission.user-role', compact('roles'));
	}


	function userPermissionList($user_id)
	{
		$user = User::findOrFail($user_id);
		$permissions = $user->getAllPermissions();
		$data = new StdClass();
		$nonactive = Permission::whereNotIn('id', array_values($permissions->pluck('id')->toArray()))->get();

		$data->active = $permissions;
		$data->nonactive = $nonactive;
		return response()->json($data);
	}

	function userRoleList($user_id)
	{
		$user = User::findOrFail($user_id);
		$role = $user->roles()->select('id', 'name')->first();
		return response()->json($role);
	}

	function updateUserPermission(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'user-select' => 'required|exists:users,id',
			'actives' => 'nullable|array',
			'actives.*' => 'exists:permissions,id',
			'opens' => 'array',
			'opens.*' => 'exists:permissions,id',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$user = User::findOrFail($request->input("user-select"));
		$oldPermission = $user->getAllPermissions();
		$newPermission = [];

		if ($request->actives != null && $request->opens != null) {
			$newPermission = array_merge($request->actives, $request->opens);
		}

		if ($request->actives == null && $request->opens != null) {
			$newPermission = $request->opens;
		}

		if ($request->actives != null && $request->opens == null) {
			$newPermission = $request->actives;
		}
		if ($request->actives == null && $request->opens == null) {
			$newPermission = null;
		}


		$permissions = $newPermission ? Permission::whereIn('id', $newPermission)->pluck('name')->toArray() : [];


		if (count($oldPermission) >= 1) {
			$user->permissions()->detach();
		}

		$user->syncPermissions($permissions);

		return redirect()->back()->withInput();
	}

	function updateUserRole(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'user-select' => 'required|exists:users,id',
			'role' => 'nullable|exists:roles,name',
		]);

		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		}

		$user = User::findOrFail($request->input("user-select"));

		if ($request->role == 'admin' && $user->hasRole('admin')) {
			return back()->withInput()->with(['message' => [
				"type" => "warning",
				"text" => "Anda tidak mengubah apapun"
			]]);
		}

		if ($request->role != 'admin' && $user->hasRole('admin')) {
			$userByRole = User::role('admin')->get();
			if (count($userByRole) <= 1) {
				return back()->withInput()->with(['message' => [
					"type" => "error",
					"text" => "Role Admin minimal satu , Anda tidak bisa mengubah user ini menjadi non admin,"
				]]);
			}
		}

		$user->syncRoles($request->role);
		return back()->withInput()->with(['message' => [
			"type" => "success",
			"text" => "Role berhasil diterapkan"
		]]);
	}
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
	public function index()

	{
		$user = User::select('id', 'username', 'name', 'email', 'is_active', 'is_deleted')->paginate(10);
		return view('user.index', compact('user'));
	}

	public function search(Request $request)
	{
		$search = $request->input('term');

		$users = DB::table('users')->orWhere('name', 'LIKE', "%{$search}%")->orWhere('email', 'LIKE', "%{$search}%")->select('id', 'name', 'email')->get();

		return response()->json($users);
	}

	function create()
	{
		$roles = Role::all();
		$divisions = DB::table('sub_category')->get();
		return view('user.create', compact('roles', 'divisions'));
	}
	function edit($id)
	{
		$user = User::findOrfail($id);
		$assignedDivision = DB::table('mapping_subcategory_user as map')
			->join('sub_category as sub', 'sub.id', '=', 'map.sub_category_id')
			->where('map.user_id', $id)
			->get();

		$roles = Role::all();
		$divisions = DB::table('sub_category')->get();
		// dd($divisions);

		return view('user.edit', compact('roles', 'divisions', 'user', 'assignedDivision'));
	}

	public function store(Request $request)
	{
		$division = $request->role == 'need-otp' ? 'required' : 'nullable';
		$request->validate([
			'name' => 'required',
			'email' => 'required|unique:users,email',
			'password' => 'required|min:8',
			'role' => 'required:exists:roles,name',
			'division' => $division,
		]);

		$approval = $request->division == 'nullable' ? null : $request->division;

		$data = [
			'name' => $request->name,
			'email' => $request->email,
			'password' => Hash::make($request->password),
			'is_active' => true,
			'is_deleted' => false,
			'created_at' => Carbon::now(),
		];


		DB::beginTransaction();
		try {
			$user = User::create($data);

			if ($approval) {
				$div = [
					'user_id' => $user->id,
					'sub_category_id' => $approval,
					'created_at' => Carbon::now(), // Menambahkan timestamp created_at
				];
				DB::table("mapping_subcategory_user")->insert($div);
			}
			$user->syncRoles([$request->role]);
			DB::commit();
			return redirect()->route('user.index')->with(['message' => [
				"type" => "success",
				"text" => "Data Berhasil ditambahkan "
			]]);
		} catch (\Throwable $th) {
			DB::rollBack();
			// abort(404);
			dd($th);
		}
	}

	public function update(Request $request, $id)
	{

		$user = User::findOrfail($id);
		if ($request->action == 'delete') {
			$user->is_active = $user->is_active ? false : true;
			$user->save();

			return redirect()->route('user.index')->with(['message' => [
				"type" => "success",
				"text" => "Data Berhasil diupdate "
			]]);
		} else {
			$emailVal = $user->email == $request->email ? 'required' : 'required|unique:users,email';
			$divisionVal = $request->role == 'need-otp' ? 'required' : 'nullable';

			$request->validate([
				'name' => 'required',
				'email' => $emailVal,
				'role' => 'required:exists:roles,name',
				'divisions' => $divisionVal . '|exists:sub_category,id',
			]);


			$divisions = $request->divisions ?? [];


			$data = array_map(function ($division_id) use ($id) {
				return [
					'user_id' => $id,
					'sub_category_id' => $division_id,
					'created_at' => Carbon::now(), // Menambahkan timestamp created_at
				];
			}, $divisions);


			DB::beginTransaction();
			try {
				DB::table('users')->where('id', $id)->update(['name' => $request->name, 'email' => $request->email]);
				$user->syncRoles([$request->role]);
				DB::table("mapping_subcategory_user")->where('user_id', $id)->delete();
				DB::table("mapping_subcategory_user")->insert($data);

				DB::commit();
				return redirect()->route('user.index')->with(['message' => [
					"type" => "success",
					"text" => "Data Berhasil diupdate "
				]]);
			} catch (\Throwable $th) {
				DB::rollBack();
				abort(500);
				// dd($th);
			}
		}
	}

	public function showChangePasswordForm()
	{
		return view('user.password-change');
	}

	public function changePassword(Request $request)
	{

		$request->validate([
			'old_password' => ['required', function ($attribute, $value, $fail) use ($request) {

				if (! $request->has('old_password') || !Hash::check($value, Auth::user()->password)) {
					$fail(__('The old password is incorrect.'));
				}
			}],
			'new_password' => ['required', 'min:8', 'confirmed', function ($attribute, $value, $fail) use ($request) {

				if ($value === $request->input('old_password')) {
					$fail(__('The new password cannot be the same as the old password.'));
				}
			}],
			'new_password_confirmation' => ['required'],
		]);

		$user = Auth::user();
		$user->password = Hash::make($request->new_password);
		$user->save();

		return redirect()->route('password.change')->with(['message' => [
			"type" => "success",
			"text" => "Password user berhasil direset "
		]]);
	}

	public function showResetPasswordForm(User $user)
	{

		if (!Auth::user()->hasRole('admin')) {
			abort(403);
		}
		return view('user.reset-password', ['user' => $user]);
	}

	public function resetPassword(Request $request, User $user)
	{
		if (!Auth::user()->hasRole('admin')) {
			abort(403);
		}
		$request->validate([
			'new_password' => ['required', 'min:8', 'confirmed'],
			'new_password_confirmation' => ['required'],
		]);

		// Update password
		$user->password = Hash::make($request->new_password);
		$user->save();

		return redirect()->route('user.index', $user->id)->with(['message' => [
			"type" => "success",
			"text" => "Password user berhasil direset "
		]]);
	}
}

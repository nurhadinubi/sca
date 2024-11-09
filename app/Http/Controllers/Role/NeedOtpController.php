<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Mail\OtpEmail;
use App\Models\ChooseMenu;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use stdClass;

class NeedOtpController extends Controller
{
	public function index()
	{
		// dd(Auth::user()->getAllPermissions());

		$category = DB::table('sub_category as sc')
			->join('doctype', 'doctype.id', '=', 'sc.doctype_id')
			->join('public.mapping_subcategory_user as mp', 'mp.sub_category_id', '=', 'sc.id')
			->select('sc.*', 'doctype.doctype')
			->where('mp.user_id', 3)
			->get();

		// if (count($category) <= 0) {
		//     return redirect('/choose')->with(['message' => [
		//         "type" => "error",
		//         "text" => "Anda Belum di assign ke Sub depatemen, silahkan hubungi admin untu assign user anda"
		//     ]]);
		// }
		return view('home', compact('category'));
		// return view('auth.choose-transaction', compact('category'));
	}

	public function choose()
	{

		$cekmenu = DB::table('choose_menu')
			->where('user_id', Auth::user()->id)
			->where('status', 'P')
			->first();

		return view('auth.choose-transaction', compact('cekmenu'));
	}

	public function requestMenu(Request $request)
	{
		$request->validate([
			'menu' => 'required|exists:permissions,name',
		]);

		$otp = Str::random(6);
		$cekmenu = DB::table('choose_menu')
			->where('user_id', Auth::user()->id)
			->where('status', 'P')
			->first();

		if (!$cekmenu) {
			$insert = DB::table('choose_menu')->insertGetId(
				[
					'user_id' => Auth::user()->id,
					'permission_name' => $request->menu,
					'status' => 'P',
					'created_at' => Carbon::now(),
					'otp' => $otp
				]
			);

			$data =  DB::table('choose_menu')->where('id', $insert)->first();
			$data->name = Auth::user()->name;


			Mail::to(Auth::user()->email)
				// ->cc(env('MAIL_FROM_ADDRESS'))
				->send(new OtpEmail($data));

			$cekmenu = $data;
		}


		return view('auth.choose-transaction', compact('cekmenu'));
	}

	public function verifikasiOtp(Request $request)
	{

		$request->validate([
			'otp' => 'required'
		]);


		DB::beginTransaction();
		try {
			// update tabel otp
			$update = ChooseMenu::where('user_id', Auth::user()->id)
				->where('otp', $request->otp)
				->where('status', 'P')
				->first();


			if (!$update) {
				// dd($update);

				return redirect()->route('choose')->with(['message' => [
					"type" => "error",
					"text" => "OTP tidak Valid"
				]]);
			}
			$user = Auth::user();
			$user->syncPermissions([$update->permission_name]);
			$update->valid_to = Carbon::now()->addHours(2);
			$update->status = 'U';
			$update->updated_at = Carbon::now();
			$update->save();

			DB::commit();
			return redirect()->route('index')->with(['message' => [
				"type" => "success",
				"text" => "Suksess menggunakan OTP"
			]]);
		} catch (\Throwable $th) {
			dd($th);
			//throw $th;
		}
	}
}

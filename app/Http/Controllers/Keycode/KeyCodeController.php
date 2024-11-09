<?php

namespace App\Http\Controllers\Keycode;

use App\Http\Controllers\Controller;
use App\Mail\AprrovalNotification;
use App\Models\KeyCode;
use App\Models\KeycodeApprovalHeader;
use App\Models\ScaleUpHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use stdClass;

class KeyCodeController extends Controller
{
	public function menu()
	{
		return view('keycode.choose');
	}

	public function request(Request $request)
	{
		$menu = $request->menu;
		$scaleup_validation = 'required|exists:scaleup_header,id';
		$approval_type = "";
		switch ($menu) {
			case "scaleup-create":
				$approval_type = "scaleup-create";
				$scaleup_validation = 'nullable';
				break;
			case "scaleup-edit":
				$approval_type = "scaleup-edit";
				break;
			case "scaleup-view":
				$approval_type = "scaleup-view";
				break;
			case "scaleup-print":
				$approval_type = "scaleup-print";
				break;
			default:
				$approval_type = null;
		}


		$request->validate([
			'menu' => 'required',
			'scaleup' => $scaleup_validation,
			'product_code' => 'required|exists:product_new,id',
			'remark' => 'required',
		]);

		$product = DB::table('product_new')->where('id', $request->product_code)->first();

		$subType = DB::table('sub_category')->where('id', $product->product_type)->first();
		$doc = DB::table('doctype')->where('id', $subType->doctype_id)->first();

		// Get Table approver
		$approver = DB::table('approval')->where('doctype_id', '=', $doc->id)
			->leftJoin('users', 'approval.user_id', 'users.id')
			->select('approval.*', 'users.email as email')
			->where('transaction_type', $approval_type)
			->where('approval.is_active', true)
			->where('approval.is_deleted', false)
			->orderBy('level', 'ASC')->get()->toArray();

		if (count($approver) < 1) {
			return redirect()->back()
				->with(['message' => [
					"type" => "error",
					"text" => "Approver {$approval_type} Belum disetting, hubungi admin "
				]]);
		}

		$data = [
			'scaleup_header_id' => $request->scaleup,
			'product_id' => $request->product_code,
			'formula_header_id' => $request->formula,
			'remark' => $request->remark,
			'product_code' => $product->product_code,
			'product_code' => $product->product_code,
			'product_description' => $product->description,
			'status' => "P",
			"transaction" => $approval_type,
			'user_id' => Auth::user()->id,
			'created_at' => Carbon::now(),
		];

		DB::beginTransaction();

		try {
			$insert = DB::table('keycode')->insertGetId($data);
			$keycode = DB::table('keycode')->where('id', $insert)->first();

			$approval_doc = [];
			foreach ($approver as $app) {
				$app_item = [
					"keycode_id" => $insert,
					"user_id" => $app->user_id,
					"level" => $app->level,
					"email" => $app->email,
					"status" => "P",
					"create_user" => Auth::user()->id,
					"created_at" => Carbon::now()
				];

				$approval_doc[] = $app_item;
			}


			DB::table('keycode_approval')->insert($approval_doc);

			$firstApprover = KeycodeApprovalHeader::where('keycode_id', $insert)->get();
			$min = $firstApprover->min('level');
			$filtered = $firstApprover->filter(function ($firstApprover) use ($min) {
				return $firstApprover->level == $min;
			});

			$data = $keycode;
			$data->type = 'keycode';
			$data->intro = "Mohon Untuk di Approve permohonan Key Code Untuk melakukan transaksi sbb:";
			// $data->material = $material;
			// Mail::to($email)
			// 	->cc(env('MAIL_FROM_ADDRESS'))
			// 	->send(new AprrovalNotification($data));

			foreach ($filtered as $item) {
				$data->link = Crypt::encryptString($keycode->id . "|" . $item->id . "|" . $item->created_at);
				Mail::to($item->email)
					->send(new AprrovalNotification($data));
				$item->update(['sent_at' => Carbon::now()]);
				$item->save();
			}

			DB::commit();
			return redirect()->route('keycode.pending')->with(['message' => [
				"type" => "success",
				"text" => "Request keycode berhasil dibuat"
			]]);
		} catch (\Throwable $th) {
			DB::rollBack();
			dd($th);
		}
	}

	public function approve($id)
	{
		$keycode_id = null;
		try {
			// Crypt::encryptString()
			$keycode_id = Crypt::decryptString($id);
		} catch (\Throwable $th) {
			return abort('404');
			// dd($th);
		}

		$keycode = DB::table('keycode')
			->join('users', 'users.id', '=', 'keycode.user_id')
			->select('keycode.*', 'users.name as requester')
			->where('keycode.id', $keycode_id)->first();
		$approval = DB::table("keycode_approval as ka")
			->select('ka.*', 'users.name as name')
			->leftJoin('users', 'users.id', '=', 'ka.user_id')
			->orderBy('ka.level', 'ASC')
			->where('keycode_id', $keycode->id)
			->get();

		$me = Auth::user();
		if (!$approval->contains('user_id', $me->id)) {
			return abort('401');
		}
		$valid = true;
		$approver = DB::table('keycode_approval')->where([['user_id', $me->id], ['keycode_id', $keycode->id]])->orderBy('level', 'ASC')->first();
		if ($approver->status == 'A' || $approver->status == 'R') {
			$valid = false;
		}

		foreach ($approval as $item) {
			if ($item->level < $approver->level && $item->status == 'P') {
				$valid = false;
				break;
			}
		}

		return view('keycode.approve', [
			"keycode" => $keycode,
			"approver" => $approver,
			'valid' => $valid,
		]);
	}

	public function approveStore(Request $request, $id)
	{

		$approver_id = null;
		try {
			// Crypt::encryptString()
			$approver_id = Crypt::decryptString($id);
		} catch (\Throwable $th) {
			return abort('404');
			// dd($th);
		}
		$action = $request->action;
		$data = [
			'status' => $request->action,
			'note' => $request->note,
			'update_user' => Auth::user()->id,
			'updated_at' => Carbon::now()
		];

		$update = KeycodeApprovalHeader::findOrfail($approver_id);
		$header = KeyCode::findOrfail($update->keycode_id);


		DB::beginTransaction();
		try {
			$update->note = $request->note;
			$update->updated_at = Carbon::now();
			$update->status = $action;
			$update->update_user = Auth::user()->id;
			$update->save();
			$requester = DB::table('users')->where('id', $header->user_id)->first();
			if ($action == 'R') {
				$header->status = 'R';
				$header->updated_at = Carbon::now();
				$header->save();


				$data = $header;
				$data->type = 'keycode-notif';
				$data->intro = "Permintaan Keycode Anda sudah direject";
				Mail::to($requester->email)
					->send(new AprrovalNotification($data));

				$header->key_code = null;
				DB::commit();
				return redirect()->route('index')->with(['message' => [
					"type" => "error",
					"text" => "Anda sudah mereject request keycode"
				]]);
			} elseif ($action == 'A') {
				$next = KeycodeApprovalHeader::where('keycode_id', $update->keycode_id)
					->where('status', 'P')
					->orderBy('level', 'ASC')
					->get();

				if (count($next) > 0) {
					$min = $next->min('level');
					$filtered = $next->filter(function ($firstApprover) use ($min) {
						return $firstApprover->level == $min;
					});

					$data = $header;
					$data->type = 'keycode';
					$data->intro = "Mohon Untuk di Approve permohonan Key Code Untuk melakukan transaksi sbb:";

					foreach ($filtered as $item) {
						if ($item->sent_at == null) {
							$data->link = Crypt::encryptString($header->id . "|" . $item->id . "|" . $item->created_at);
							Mail::to($item->email)
								->send(new AprrovalNotification($data));
							$item->update(['sent_at' => Carbon::now()]);
							$item->save();
						}
					}
				} else {
					$random = Str::random(8);
					$header->status = 'A';
					$header->updated_at = Carbon::now();
					$header->key_code = $random;
					$header->save();
					// email Notif
					$data = $header;
					$data->type = 'keycode-notif';
					$data->intro = "Permintaan Keycode Anda sudah disetujui";
					Mail::to($requester->email)
						->send(new AprrovalNotification($data));
				}

				DB::commit();
				return redirect()->route('index')->with(['message' => [
					"type" => "success",
					"text" => "Anda sudah menyetujui request keycode"
				]]);
			}
		} catch (\Throwable $th) {
			DB::rollBack();
			dd($th);
		}
	}

	public function pending(Request $request)
	{
		$start_date = $request->query('start_date') ?
			Carbon::createFromFormat('d-m-Y', $request->query('start_date'))->format('Y-m-d') :
			Carbon::now()->addDays(-30)->format('Y-m-d');
		$end_date = $request->query('end_date') ?
			Carbon::createFromFormat('d-m-Y', $request->query('end_date'))->addDays(1)->format('Y-m-d') :
			Carbon::now()->addDays(1)->format('Y-m-d');

		$keycode = DB::table('keycode')
			->where('status', 'P')
			->whereBetween('keycode.created_at', [$start_date, $end_date])
			->where('user_id', Auth::user()->id)
			->get();
		// ->paginate(15)
		// ->appends($request->query());


		return view('keycode.pending', ["keycode" => $keycode, 'title' => "List Keycode Pending"]);
	}
	public function complete(Request $request)
	{
		$start_date = $request->query('start_date') ?
			Carbon::createFromFormat('d-m-Y', $request->query('start_date'))->format('Y-m-d') :
			Carbon::now()->addDays(-30)->format('Y-m-d');
		$end_date = $request->query('end_date') ?
			Carbon::createFromFormat('d-m-Y', $request->query('end_date'))->addDays(1)->format('Y-m-d') :
			Carbon::now()->addDays(1)->format('Y-m-d');

		$keycode = DB::table('keycode')
			->where('status', '!=', 'P')
			->whereBetween('keycode.created_at', [$start_date, $end_date])
			->where('user_id', Auth::user()->id)
			->get();
		// ->paginate(15)
		// ->appends($request->query());


		return view('keycode.complete', ["keycode" => $keycode, 'title' => "List Keycode Completed"]);
	}

	public function input()
	{
		return view('keycode.input');
	}
	public function process(Request $request)
	{
		$keycode = KeyCode::where([
			'key_code' => $request->keycode,
			'is_used' => false,
			'status' => 'A',
			'user_id' => Auth::user()->id,
		])->first();

		if (!$keycode) {
			return redirect()->back()->with(['message' => [
				"type" => "error",
				"text" => "Keycode yang anda gunakan tidak valid. keycode sudah digunakan atau user anda tidak mempunyai authorisasi untuk keycode ini"
			]]);
		}

		if ($keycode->transaction == 'scaleup-create') {
			return redirect()->route('scaleup.createWithKeycode', ['id' => Crypt::encryptString($keycode->key_code)]);
		}

		if ($keycode->transaction == 'scaleup-print') {
			$scaleup = ScaleUpHeader::where('id', $keycode->scaleup_header_id)
				->where('user_id', Auth::user()->id)->first();

			if (!$scaleup) {
				return redirect()->back()->with(['message' => [
					"type" => "error",
					"text" => "Anda tidak mempunyai authorisasi untuk print scaleup ini"
				]]);
			}
			$data = new stdClass();
			$data->keycode = $keycode->key_code;
			$data->scaleup = $scaleup->doc_number;
			$print = $scaleup->print_count ? $scaleup->print_count : 0;
			$scaleup->print_count = ($print + 1);
			$scaleup->save();
			return redirect()->route('scaleup.print', ['id' => Crypt::encrypt($data, true)]);
		}

		if ($keycode->transaction == 'scaleup-edit') {
			$scaleup = ScaleUpHeader::where('id', $keycode->scaleup_header_id)
				->where('user_id', Auth::user()->id)
				->first();

			if (!$scaleup) {
				return redirect()->back()->with(['message' => [
					"type" => "error",
					"text" => "Anda tidak mempunyai authorisasi untuk Edit scaleup ini"
				]]);
			}

			return redirect()->route('scaleup.duplicate', ['id' => Crypt::encryptString($keycode->key_code)]);
		}

		if ($keycode->transaction == 'scaleup-view') {
			$scaleup = ScaleUpHeader::where('id', $keycode->scaleup_header_id)
				->where('user_id', Auth::user()->id)->first();

			if (!$scaleup) {
				return redirect()->back()->with(['message' => [
					"type" => "error",
					"text" => "Anda tidak mempunyai authorisasi untuk print scaleup ini"
				]]);
			}

			return redirect()->route('scaleup.show', ['id' => Crypt::encryptString($keycode->key_code)]);
		}


		return view('keycode.input');
	}

	public function getApproval(Request $request)
	{
		$id = $request->id;
		$approver = DB::table('keycode_approval')->where('keycode_id', $id)->orderBy('level', 'ASC')->get();
		return view('keycode.partial.step', compact('approver'));
	}

	public function needApprove()
	{
		$idDoc = DB::table('keycode_approval')->where([
			'status' => 'P',
			'user_id' => Auth::user()->id,
		])->pluck('keycode_id')->toArray();

		$keycode = DB::table('keycode as kc')
			->join('users as u', 'u.id', '=', 'kc.user_id')
			->select('u.name as requester', 'kc.*')
			->where('kc.status', 'P')
			->whereIn('kc.id', $idDoc)->get();
		// dd($keycode);
		return view('approval.list-keycode', compact('keycode'));
	}
}

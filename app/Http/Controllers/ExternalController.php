<?php

namespace App\Http\Controllers;

use App\Mail\AprrovalNotification;
use App\Models\ApprovalDocument;
use App\Models\KeyCode;
use App\Models\KeycodeApprovalHeader;
use App\Models\ScaleUpHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ExternalController extends Controller
{
	protected function scaleup($param)
	{

		try {
			// dd(Crypt::encryptString('SCA/DRY/2024/X/000001|90'));
			$doc = Crypt::decryptString($param);
			$approval = explode('|', $doc)[1];
		} catch (\Throwable $th) {
			dd($th);
		}
		$approval_doc = ApprovalDocument::findOrFail($approval);
		return view('scaleup.ext-approve', compact('approval_doc'));
	}

	protected function scaleupStore(Request $request, $param)
	{
		try {

			$doc = Crypt::decryptString($param);
			$approval = explode('|', $doc)[1];
			// dd($doc);
		} catch (\Throwable $th) {
			dd($th);
		}
		DB::beginTransaction();
		try {
			$approval_doc = ApprovalDocument::findOrFail($approval);
			$header = ScaleUpHeader::find($approval_doc->scaleup_header_id);
			$requester = DB::table('users')->where('id', $header->user_id)->first();

			$approval_doc->status = $request->action;
			$approval_doc->updated_at = Carbon::now();
			$approval_doc->save();
			if ($request->action == 'R') {

				$header->status = 'R';
				$header->updated_at = Carbon::now();
				$header->save();
				$data = $header;
				$data->type = 'notif';
				$data->intro = "Dokumen Sacaleup dibawah ini sudah di-Reject :";
				Mail::to($requester->email)
					->send(new AprrovalNotification($data));
				DB::commit();
				$message = "Dokumen sudah anda reject";
				return view('scaleup.feedback-ext', compact('message'));
			}

			if ($request->action == 'A') {

				$next = ApprovalDocument::where('scaleup_header_id', $approval_doc->scaleup_header_id)
					->where('status', 'P')
					->orderBy('level', 'ASC')
					->get();
				if (count($next) > 0) {
					// Kirim email ke next approval
					$min = $next->min('level');
					$filtered = $next->filter(function ($firstApprover) use ($min) {
						return $firstApprover->level == $min;
					});

					$data = $header;
					$data->type = 'request';
					$data->intro = "Mohon Untuk di Approve permohonan dokumen Scale Up berikut ini :";

					foreach ($filtered as $item) {
						if ($item->sent_at == null) {
							$data->link = Crypt::encryptString($header->doc_number . "|" . $item->id . "|" . $item->created_at);
							Mail::to($item->email)
								->send(new AprrovalNotification($data));
							$item->update(['sent_at' => Carbon::now()]);
							$item->save();
						}
					}
				} else {
					$header->status = 'A';
					$header->updated_at = Carbon::now();
					// Generate revisian pada scaleup header +1 jika approver
					$product_code = $header->product_code;
					$lastRow = DB::table('scaleup_header')
						->where([
							'product_code' => $product_code,
							"status" => 'A'
						])
						->orderBy('revision', 'DESC')
						->first();

					$rev = 0;

					if (!$lastRow) {
						$rev = 0;
					} else {
						$rev = (int)$lastRow->revision + 1;
					}
					$header->revision = $rev;
					$header->save();

					// kirim emil ke requester sudah approve

					$data = $header;
					$data->type = 'notif';
					$data->intro = "Dokumen Sacaleup dibawah ini sudah di-Approve :";

					Mail::to($requester->email)
						->send(new AprrovalNotification($data));
				}

				DB::commit();
				$message = "Dokumen sudah anda Approve";
				return view('scaleup.ext-feedback', compact('message'));
			}
		} catch (\Throwable $th) {
			//throw $th;
			DB::rollBack();
			dd($th);
		}
	}

	protected function keycode($param)
	{

		try {
			// dd(Crypt::encryptString('SCA/DRY/2024/X/000001|90'));
			$doc = Crypt::decryptString($param);
			$approval = explode('|', $doc)[1];
		} catch (\Throwable $th) {
			dd($th);
		}
		$approval_doc = KeycodeApprovalHeader::findOrFail($approval);
		return view('scaleup.ext-approve', compact('approval_doc'));
	}


	protected function keyCodeStore(Request $request, $param)
	{
		try {
			$doc = Crypt::decryptString($param);
			$approval = explode('|', $doc)[1];
			// dd($doc);
		} catch (\Throwable $th) {
			dd($th);
		}
		DB::beginTransaction();
		try {
			$approval_doc = KeycodeApprovalHeader::findOrFail($approval);
			$header = KeyCode::find($approval_doc->keycode_id);
			$requester = DB::table('users')->where('id', $header->user_id)->first();

			$approval_doc->status = $request->action;
			$approval_doc->updated_at = Carbon::now();
			$approval_doc->save();
			if ($request->action == 'R') {
				$header->status = 'R';
				$header->updated_at = Carbon::now();
				$header->save();
				$data = $header;
				$data->type = 'notif';
				$data->intro = "Permintaan keycode sudah di-Reject :";
				Mail::to($requester->email)
					->send(new AprrovalNotification($data));
				DB::commit();
				$message = "Dokumen sudah anda reject";
				return view('scaleup.feedback-ext', compact('message'));
			}

			if ($request->action == 'A') {

				$next = KeycodeApprovalHeader::where('keycode_id', $approval_doc->keycode_id)
					->where('status', 'P')
					->orderBy('level', 'ASC')
					->get();
				if (count($next) > 0) {
					// Kirim email ke next approval
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
				$message = "Keycode sudah anda Approve";
				return view('scaleup.ext-feedback', compact('message'));
			}
		} catch (\Throwable $th) {
			//throw $th;
			DB::rollBack();
			dd($th);
		}
	}
}

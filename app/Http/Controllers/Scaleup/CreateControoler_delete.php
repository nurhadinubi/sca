<?php

namespace App\Http\Controllers\ScaleUp;

use App\Http\Controllers\Controller;
use App\Models\MasterMaterial;
use App\Models\ScaleUpDetail;
use App\Models\ScaleUpHeader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

use App\Mail\AprrovalNotification;
use App\Models\ApprovalDocument;
use Barryvdh\DomPDF\Facade\Pdf;

class CreateControoler extends Controller
{

    public function __construct(Type $var = null)
    {
        $this->middleware([]);
    }

    public function create()
    {
        if (session("itemCart") == null) {
            $itemCart = [];
        } else {
            $itemCart = session("itemCart");
        }

        $doctype = DB::table('doctype')->get();
        return view('scaleup.create', compact('doctype', 'itemCart'));
    }
}

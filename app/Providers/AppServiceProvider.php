<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Paginator::useBootstrapFive();

        View::composer('components.dashboard.sidebar', function ($view) {
            $scaleupPending = DB::table('approval_document as ad')
                ->join('scaleup_header as sh', 'sh.id', '=', 'ad.scaleup_header_id')
                ->where([
                    "ad.user_id" => Auth::user()->id,
                    "ad.status" => "P",
                    "sh.status" => "P"
                ])
                ->distinct('sh.id')
                ->count('sh.id');
            $keycodePending = DB::table('keycode_approval as ka')
                ->join('keycode as kk', 'kk.id', 'ka.keycode_id')
                ->where([
                    "ka.user_id" => Auth::user()->id,
                    "ka.status" => "P",
                    "kk.status" => "P",
                ])
                ->distinct('kk.id')
                ->count('kk.id');

            $view->with(['scaleupPending' => $scaleupPending, 'keycodePending' => $keycodePending]);
        });
    }
}

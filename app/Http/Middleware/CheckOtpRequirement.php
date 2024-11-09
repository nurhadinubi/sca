<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOtpRequirement
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        // Cek apakah pengguna memerlukan OTP
        // if ($user && $user->hasRole('need-otp')) {
        //     $permissions = $user->getAllPermissions();
        //     if (count($permissions) < 1) {
        //         return redirect('/choose');
        //     }
        // }

        // Lanjutkan ke request berikutnya jika semua cek lulus
        return $next($request);
    }
}

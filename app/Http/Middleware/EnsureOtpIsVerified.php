<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->session()->get('otp_verified') !== true) {
            return redirect()->route('otp.show');
        }

        return $next($request);
    }
}

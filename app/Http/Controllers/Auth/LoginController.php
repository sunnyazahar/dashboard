<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/otp';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        app(OtpController::class)->issueOtp($request);

        return redirect()->route('otp.show');
    }

    protected function loggedOut(Request $request)
    {
        $request->session()->forget([
            'otp_verified',
            'login_otp_hash',
            'login_otp_expires_at',
            'login_otp_last_sent_at',
        ]);

        return redirect()->route('login');
    }
}

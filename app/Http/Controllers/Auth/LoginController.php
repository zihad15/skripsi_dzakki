<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Log;
use App\Models\IpFailedLoginAttempt;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $ip = $request->ip();
        (int) $failedAttempt = 0;
        $lastLoginAttemptFromThisIp = IpFailedLoginAttempt::whereIp($ip)->orderBy('created_at', 'desc')->first();

        if($lastLoginAttemptFromThisIp){
            $failedAttempt = (int) $lastLoginAttemptFromThisIp->failed_attempt;
        }

        $latestLoginAttempt = IpFailedLoginAttempt::create([
            'input_username' => $request->email,
            'ip' => $ip,
            'failed_attempt' => $failedAttempt + 1,
        ]);

        if((int)$latestLoginAttempt->failed_attempt >= 5){
            return abort(403, 'Access Denied! Your IP has been BLOCKED due to a detected BRUTE FORCE ATTEMPT. To regain access, please CONTACT the administrator.');
        }

        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => trans('auth.failed'),
            ]);
    }

    protected function authenticated(Request $request, $user)
    {
        $ip = $request->ip();

        // Log the login event
        IpFailedLoginAttempt::create([
            'input_username' => $request->email,
            'ip' => $ip,
            'failed_attempt' => 0,
        ]);

        // Check the user role and redirect accordingly
        if ($user->role === 'admin') {
            return redirect('/dashboard');
        } elseif ($user->role === 'user') {
            return redirect('/home');
        }

        return redirect($this->redirectTo);
    }

    public function showLoginForm(Request $request)
    {
        $ip = $request->ip();
        $lastLoginAttemptFromThisIp = IpFailedLoginAttempt::whereIp($ip)->orderBy('created_at', 'desc')->first();

        if($lastLoginAttemptFromThisIp) {
            if((int)$lastLoginAttemptFromThisIp->failed_attempt >= 5){
                return abort(403, 'Access Denied! Your IP has been BLOCKED due to a detected BRUTE FORCE ATTEMPT. To regain access, please CONTACT the administrator.');
            }
        }

        $data = [
            'failedCounter' => (int)$lastLoginAttemptFromThisIp->failed_attempt
        ];

        return view('auth.login', $data);
    }
}

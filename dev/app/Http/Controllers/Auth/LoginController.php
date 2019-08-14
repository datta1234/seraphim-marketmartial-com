<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Rules\LoginWindow;
use App\Rules\CheckActiveUser;
use App\Models\UserManagement\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/trade';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        // Recaptcha will be include after the 3nd login attempt, attempts start at 0
        if($this->limiter()->attempts($this->throttleKey($request)) >= 2) {
            $request->session()->put('include_recaptcha', true);
        }

        // Will validate the recaptcha from the 4de login attempt, attempts start at 0
        if($request->session()->has('include_recaptcha') && $request->session()->get('include_recaptcha')) {
            $this->validate($request, ['g-recaptcha-response' => 'recaptcha']);
        }

        $this->validate($request, [
            $this->username() => [
                'required',
                'string',
                // new LoginWindow, // removed as we cannot verify if user is admin prior to login...
                new CheckActiveUser
            ],
            'password' => 'required|string',
        ]);
    }

    protected function redirectTo()
    {
        if(\Auth::user()->role->title === 'Admin') {
            if (\Auth::user()->google2fa_secret === NULL)
                return '/admin/user';
            else
                return '/admin/mfa';        
        }
        return $this->redirectTo;
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        
        $request->session()->regenerate();

        // Remove recaptcha from session after login
        $request->session()->put('include_recaptcha', false);
        
        $this->clearLoginAttempts($request);

        // add check to see if user has multiple logins and remove all that is not current login
        $sessions = \DB::table('sessions')->where('user_id', \Auth::user()->id)
            ->where('id','!=',session()->getId())
            ->get();
        

        $filtered_sessions = array_filter($sessions->toArray(), function($session) {
            return strpos(base64_decode($session->payload), 'impersonated_by') === false;
        });

        \DB::table('sessions')->whereIn('id',array_map(function($session) {
            return $session->id;
        }, $filtered_sessions))->delete();         

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }
}

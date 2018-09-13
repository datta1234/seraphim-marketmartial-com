<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckActive
{
    protected $redirectPage = 'home';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_state = $request->user()->verifiedActiveUser();
        if ( $user_state !== null && !$user_state ) {
            Auth::logout();
            return redirect()->route($this->redirectPage)->with('error', 'Your account has been Deactivated.');
        }
        return $next($request);
    }
}

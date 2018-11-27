<?php

namespace App\Http\Middleware;

use Closure;

class CheckVerified
{
    protected $redirectPage = 'user.edit';

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
        if ( $user_state === null ) {
            return redirect()->route($this->redirectPage)->with('error', 'Your account has not been verified.');
        }
        return $next($request);
    }
}

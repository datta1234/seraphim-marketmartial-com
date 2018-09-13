<?php

namespace App\Http\Middleware;

use Closure;

class Role
{
    /**
     * Handle an incoming request.
     *
     * Avalable Roles:
     *
     * -> Admin
     * -> Trader
     * -> Viewer
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param Array $roles
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (\Auth::user() && in_array(\Auth::user()->role->title, $roles)) {
            return $next($request);
        }
        else {
            return redirect()->route('login');
        }
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Misc\TimeRestrictions;

class WindowPreventAction
{


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        if(!$this->inWindow()) 
        {
            return Auth::logout();
        }

        return $next($request);
    }

    private function inWindow()
    {   
        return TimeRestrictions::canLogin(Carbon::now());
    }
}

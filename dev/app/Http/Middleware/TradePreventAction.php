<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Misc\TimeRestrictions;

class TradePreventAction
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
            return redirect()->route('previous_day')->with('error', "Trading is currently closed");
        }

        return $next($request);
    }

    private function inWindow()
    {   
        return TimeRestrictions::canTrade(Carbon::now());
    }
}

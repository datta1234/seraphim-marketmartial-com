<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Misc\TimeRestrictions;

class TradeRestrictAction
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
        if($request->method() !== "GET" && !$this->inWindow()) 
        {
            return response()->json(['data' => false, 'message' => "Trading is currently closed."],403);
        }

        return $next($request);
    }

    private function inWindow()
    {   
        return TimeRestrictions::canViewTrade(Carbon::now());
    }
}

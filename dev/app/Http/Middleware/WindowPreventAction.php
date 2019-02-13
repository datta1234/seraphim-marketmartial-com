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
            // only non admins are locked out
            if(!Auth::user()->isAdmin()) {
                Auth::logout();
                $startTime = Carbon::createFromTimeString(config('marketmartial.window.operation_start_time'))->format('H:i');
                $endTime = Carbon::createFromTimeString(config('marketmartial.window.operation_end_time'))->format('H:i');
                return redirect('/')
                    ->with('warning', "Login is only during $startTime and $endTime On Weekdays");
            }
        }

        return $next($request);
    }

    private function inWindow()
    {   
        return TimeRestrictions::canLogin(Carbon::now());
    }
}

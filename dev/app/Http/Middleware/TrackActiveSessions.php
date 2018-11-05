<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

class TrackActiveSessions
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
        
        $authPre = \Auth::user();
        $response = $next($request);
        $authPost = \Auth::user();

        $active_market_makers = \Cache::get('active_market_makers', []);
        $active_market_makers = array_filter($active_market_makers, function($item){
            return now()->lt($item); // now before expirty time
        });
        // Logged out now - remove
        if($authPre && !$authPost) {
            unset($active_market_makers[$authPre->id]);
        }
        // Just Logged In
        elseif(!$authPre && $authPost) {
            $active_market_makers[$authPost->id] = now()->addMinutes(config('session.lifetime'));
        }
        // Still Logged In Extend Session
        elseif($authPre && $authPost) {
            $active_market_makers[$authPre->id] = now()->addMinutes(config('session.lifetime'));
        }
        \Cache::put('active_market_makers', $active_market_makers, now()->endOfDay());


        $response->header('active-market-makers', count($active_market_makers));
        return $response;
    }
}

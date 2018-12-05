<?php

namespace App\Http\Middleware;

use Closure;

class LockModelAction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $identifier, $action)
    {
        $item = $request->route($identifier);
        // fail misconfigured
        if(!$item) {
            \Log::error("[LockModelAction] Misconfigred ... looking for '".$identifier."' on request... non existent");
            abort(500);
        }
        $item = $item->id;
        $key = 'lock_model_'.$identifier.'_'.$item.'_'.$action;
        if(isset($item)) {
            if(\Cache::has($key)) {
                abort(401);
            }
            \Cache::put($key, 60);
            $returnval = $next($request);
            \Cache::forget($key);
            return $returnval;
        }
        \Log::error("[LockModelAction] Misconfigred ... looking for '".$identifier."' on request... no value");
        abort(500); // failed
    }
}

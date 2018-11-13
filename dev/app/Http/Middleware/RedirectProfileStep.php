<?php

namespace App\Http\Middleware;

use Closure;

class RedirectProfileStep
{
    /**
     * The URIs that should be excluded from First time login.
     *
     * @var array
     */
    protected $except = [
        'user.update',
        'user.change_password',
        'tsandcs.update',
        'email.store',
        'email.update',
        'trade_settings.update',
        'interest.update'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        $redirect_route = \Auth::user()->setRequiredProfileStep();
        if (!in_array($request->route()->getName(),$this->except) && !$request->user()->completeProfile()) {
            if($redirect_route !== null && $request->route()->getName() != $redirect_route) {
                return redirect()->route($redirect_route)->with('warning', 'Please complete your profile');
            } 
        }
        return $next($request);
    }
}

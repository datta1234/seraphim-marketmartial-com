<?php

namespace App\Http\Middleware;

use Closure;

class RedirectOnFirstLogin
{

    /**
     * The URIs that should be excluded from First time login.
     *
     * @var array
     */
    protected $except = [
        'user.edit',
        'user.update',
        'user.edit_password',
        'user.change_password',
        'tsandcs.edit',
        'tsandcs.update',
        'email.edit',
        'email.store',
        'email.update',
        'trade_settings.edit',
        'trade_settings.update',
        'interest.edit',
        'interest.update'
    ];

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
        if (!in_array($request->route()->getName(),$this->except) && !$request->user()->completeProfile()) {
            return redirect()->route($this->redirectPage)->with('warning', 'Please complete your profile');
        }
        return $next($request);
    }
}

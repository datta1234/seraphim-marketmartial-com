<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Using class based composers...
        View::composer(
           [
                'users.edit',
                'users.change_password',
                'users.terms_and_conditions',
                'interest.edit',
                'emails.edit',
                'trading_account.edit'
           ], 'App\Http\ViewComposers\ProfileIsComplete'
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

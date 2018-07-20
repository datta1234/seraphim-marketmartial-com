<?php

namespace App\Providers;

use App\Observers\OrganisationObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\UserManagement\Organisation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Organisation::observe(OrganisationObserver::class);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

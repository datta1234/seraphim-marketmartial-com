<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\UserManagement\Organisation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use App\Observers\OrganisationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Organisation::observe(OrganisationObserver::class);

        Blade::directive('datetime', function ($expression) {
            return "<?php echo ($expression)->format('d F'); ?>";
        });
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

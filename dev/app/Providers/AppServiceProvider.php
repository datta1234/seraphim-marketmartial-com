<?php

namespace App\Providers;

use App\Observers\OrganisationObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\UserManagement\Organisation;
use Illuminate\Support\Facades\Blade;

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

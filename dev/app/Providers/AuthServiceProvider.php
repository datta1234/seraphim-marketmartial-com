<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        'App\Models\Market\UserMarket' => 'App\Policies\UserMarketPolicy',
        'App\Models\Market\MarketNegotiation' => 'App\Policies\MarketNegotiationPolicy',
        'App\Models\MarketRequest\UserMarketRequest' => 'App\Policies\UserMarketRequestPolicy',
        'App\Models\StructureItems\Market' => 'App\Policies\MarketPolicy',
        'App\Models\Trade\TradeNegotiation' => 'App\Policies\TradeNegotiationPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }

}

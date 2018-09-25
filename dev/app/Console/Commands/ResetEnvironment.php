<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models;

class ResetEnvironment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mm:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the MarketMartial Environment (Development Only)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // fail if 
        if(env('APP_ENV') !== 'local') {
            echo "Can only run this command in 'local' Environments";
            return false;
        }
        //disable foreign key check for this connection before running seeders
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear Trading
        // @TODO

        // Clear Market Negoting
        Models\Market\MarketNegotiation::truncate();
        Models\Market\UserMarket::truncate();
        Models\Market\UserMarketSubscription::truncate();

        // Clear Market Requesting
        Models\MarketRequest\UserMarketRequest::truncate();
        Models\MarketRequest\UserMarketRequestGroup::truncate();
        Models\MarketRequest\UserMarketRequestItem::truncate();
        
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        \Artisan::call('cache:clear');
        
    }
}

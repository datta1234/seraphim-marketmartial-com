<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('gen:fok', function () {
    $id = 1;
    
    $n = \App\Models\Market\MarketNegotiation::find($id);
    $job = new \App\Jobs\MarketNegotiationTimeout($n);
    dispatch($job);

})->describe('Debug FOK');

Artisan::command('mm:populate', function() {
    Artisan::call('db:seed', [
        '--class' => 'OrganisationTableSeeder'
    ]);
});

Artisan::command('mm:sample', function() {
    Artisan::call('db:seed', [
        '--class' => 'SampleSeeder'
    ]);
});

Artisan::command('mm:reset', function() {
    // fail if 
    \Artisan::call('config:clear'); // force clear
    echo "ENV: ".env('APP_ENV').PHP_EOL;
    if(env('APP_ENV') !== 'local') {
        echo "Can only run this command in 'local' Environments".PHP_EOL;
        return false;
    }
    //disable foreign key check for this connection before running seeders
    \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // Clear Trading
    \App\Models\Trade\Rebate::truncate();
    \App\Models\TradeConfirmations\Distpute::truncate();
    \App\Models\TradeConfirmations\BookedTrade::truncate();
    \App\Models\TradeConfirmations\TradeConfirmation::truncate();
    \App\Models\TradeConfirmations\TradeConfirmationGroup::truncate();
    \App\Models\TradeConfirmations\TradeConfirmationItem::truncate();


    \App\Models\Trade\TradeNegotiation::truncate();

    // Clear Market Negoting
    \App\Models\Market\MarketNegotiation::truncate();
    \App\Models\Market\UserMarketVolatility::truncate();
    \App\Models\Market\UserMarket::truncate();
    \App\Models\Market\UserMarketSubscription::truncate();

    // Clear Market Requesting
    \App\Models\MarketRequest\UserMarketRequestTradable::truncate();
    \App\Models\MarketRequest\UserMarketRequest::truncate();
    \App\Models\MarketRequest\UserMarketRequestGroup::truncate();
    \App\Models\MarketRequest\UserMarketRequestItem::truncate();
    
    // empty the jobs
    \DB::table('jobs')->truncate();

    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    \Artisan::call('cache:clear');
});
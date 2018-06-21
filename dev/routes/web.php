<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();


Route::get('/test', function(){
return App\Models\StructureItems\TradeStructure::where('title','Outright')->with('tradeStructureGroups.items')->first()->toArray();

	dd(App\Models\StructureItems\TradeStructure::where('title','Outright')->with('tradeStructureGroups.items')->first()->toArray(),
		App\Models\StructureItems\TradeStructure::where('title','Outright')->with('tradeStructureGroups.items')->first());
	exit;
});

/*
*
*   Public Page Routes
*
*/
Route::get('/', 'PageController@index')->name('home');
Route::get('/contact', 'PageController@contact')->name('contact');
Route::get('/about', 'PageController@about')->name('about');
Route::post('/contact', 'PageController@contactMessage')->name('contact');

Route::group(['middleware' => ['auth','redirectOnFirstLogin']], function () {
	Route::get('/trade', 'TradeScreenController@index')->name('trade');

	Route::get('/my-profile', 'UserController@edit')->name('user.edit');
	Route::post('/my-profile','UserController@update')->name('user.update');

	Route::get('/change-password', 'UserController@editPassword')->name('user.edit_password');
	Route::put('/change-password','UserController@storePassword')->name('user.change_password');

	Route::get('/terms-and-conditions', 'UserController@termsOfConditions')->name('tsandcs.edit');
	Route::put('/terms-and-conditions','UserController@storeTermsAndConditions')->name('tsandcs.update');

	Route::get('/email-settings', 'EmailController@edit')->name('email.edit');
	Route::post('/email-settings','EmailController@store')->name('email.store');
	Route::put('/email-settings','EmailController@update')->name('email.update');

	Route::get('/trade-settings', 'TradingAccountController@edit')->name('trade_settings.edit');
	Route::put('/trade-settings','TradingAccountController@update')->name('trade_settings.update');

	Route::get('/interest-settings', 'InterestController@edit')->name('interest.edit');
	Route::put('/interest-settings','InterestController@update')->name('interest.update');
});



Route::group(['prefix' => 'trade', 'middleware' => ['auth']], function() {

	Route::resource('market.market-request', 'TradeScreen\MarketUserMarketReqeustController');
    Route::resource('market-type', 'TradeScreen\MarketTypeController');
    Route::resource('market-type.market', 'TradeScreen\MarketTypeMarketController');


    Route::get('market-type/{marketType}/trade-structure', 'TradeScreen\MarketType\TradeStructureController@index');
    Route::get('safex-expiration-date', 'TradeScreen\SafexExpirationDateController@index');
    Route::get('stock', 'TradeScreen\StockController@index');

});
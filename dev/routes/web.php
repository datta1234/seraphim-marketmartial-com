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

Route::get('/test',function(){
	$lol = App\Models\MarketRequest\UserMarketRequest::first();
	$lol->notifyRequested();
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

Route::group(['middleware' => ['auth','redirectOnFirstLogin','timeWindowPreventAction']], function () {
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

	Route::resource('user-pref', 'UserPrefController');
});



Route::group(['prefix' => 'trade', 'middleware' => ['auth','timeWindowPreventAction']], function() {

	Route::resource('market.market-request', 'TradeScreen\MarketUserMarketReqeustController');
    Route::resource('market-type', 'TradeScreen\MarketTypeController');
    Route::resource('market-type.market', 'TradeScreen\MarketTypeMarketController');

    Route::get('market-type/{marketType}/trade-structure', 'TradeScreen\MarketType\TradeStructureController@index');
    Route::get('safex-expiration-date', 'TradeScreen\SafexExpirationDateController@index');
    Route::get('stock', 'TradeScreen\StockController@index');

    Route::resource('market.market-request', 'TradeScreen\MarketUserMarketReqeustController');
    Route::resource('market-condition', 'TradeScreen\MarketConditionsController');
    Route::resource('user-market-request.user-market', 'TradeScreen\MarketRequest\UserMarketController');
    Route::resource('user-market.market-negotiation', 'TradeScreen\UserMarket\MarketNegotiationController');

 //    Route::resource('organisation-chat', 'TradeScreen\ChatController', [
	// 	'only' => ['store','index']
	// ]);

    Route::post('/user-market-request/{user_market_request}/action-taken','TradeScreen\MarketUserMarketReqeustController@actionTaken');
});
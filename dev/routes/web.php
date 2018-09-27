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
	$marketRequest = App\Models\MarketRequest\UserMarketRequest::find(331);
	dd($marketRequest->preFormatted());
	echo "test";
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

Route::group(['middleware' => ['auth','active','redirectOnFirstLogin','timeWindowPreventAction']], function () {
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

	Route::group(['prefix' => 'stats'], function() {
		Route::get('/my-activity', 'Stats\ActivityControlller@show')->name('activity.show');
		Route::get('/my-activity/year', 'Stats\ActivityControlller@yearActivity')
			->name('my_activity.year');
		Route::get('/my-activity/markets', 'Stats\MarketController@index')
			->name('my_activity.markets');
		
		Route::get('/market-activity', 'Stats\ActivityControlller@index')->name('activity.index');
		
		Route::get('/open-interest', 'Stats\OpenInterestControlller@show')->name('open_interest.show');
	});
});



Route::group(['prefix' => 'trade', 'middleware' => ['auth','active','timeWindowPreventAction']], function() {

	Route::resource('market.market-request', 'TradeScreen\MarketUserMarketReqeustController');
    Route::resource('market-type', 'TradeScreen\MarketTypeController');
    Route::resource('market-type.market', 'TradeScreen\MarketTypeMarketController');

    Route::get('market-type/{marketType}/trade-structure', 'TradeScreen\MarketType\TradeStructureController@index');
    Route::get('safex-expiration-date', 'TradeScreen\SafexExpirationDateController@index');
    Route::get('stock', 'TradeScreen\StockController@index');

    Route::resource('market.market-request', 'TradeScreen\MarketUserMarketReqeustController');
    Route::resource('user-market-request.user-market', 'TradeScreen\MarketRequest\UserMarketController');
    Route::resource('user-market.market-negotiation', 'TradeScreen\UserMarket\MarketNegotiationController');
    
    Route::resource('market-negotiation.trade-negotiation', 'TradeScreen\MarketNegotiation\TradeNegotiationController');

    Route::resource('organisation-chat', 'TradeScreen\ChatController', [
		'only' => ['store','index']
	]);

	Route::resource('organisation-chat', 'TradeScreen\ChatController', [
		'only' => ['store','index']
	]);

    Route::post('stream','TradeScreen\StreamController@index');
    Route::post('/user-market-request/{user_market_request}/action-taken','TradeScreen\MarketUserMarketReqeustController@actionTaken');
});

/**
 * Admin routes
 */
Route::group(['prefix' => 'admin', 'middleware' => ['role:Admin','active',]], function() {
	Route::resource('user', 'Admin\UserController', [
		'as' => 'admin'
	]);

	Route::post('/user/profile/{user}','Admin\UserController@updateProfile')
		->name('admin.user.profile.update');

	Route::get('/user/email-settings/{user}/edit','Admin\EmailController@edit')
		->name('admin.user.email.edit');
	Route::post('/user/email-settings/{user}','Admin\EmailController@store')
		->name('admin.user.email.store');
	Route::put('/user/email-settings/{user}','Admin\EmailController@update')
		->name('admin.user.email.update');

	Route::get('/user/trade-settings/{user}/edit', 'Admin\TradingAccountController@edit')
		->name('admin.user.trade_settings.edit');
	Route::put('/user/trade-settings/{user}','Admin\TradingAccountController@update')
		->name('admin.user.trade_settings.update');
	
	Route::get('/user/interest-settings/{user}/edit', 'Admin\InterestController@edit')
		->name('admin.user.interest.edit');
	Route::put('/user/interest-settings/{user}','Admin\InterestController@update')
		->name('admin.user.interest.update');

	Route::group(['prefix' => 'stats'], function() {
		Route::post('/market-activity','Stats\ActivityControlller@store')
			->name('activity.upload_safex_data');
	});
});
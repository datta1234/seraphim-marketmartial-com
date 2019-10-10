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

/*
*
*   Public Page Routes
*
*/
Route::get('/', 'PageController@index')->name('home');
Route::get('/contact', 'PageController@contact')->name('contact');
Route::post('/contact', 'PageController@contactMessage')->name('contact_send');
Route::get('/about', 'PageController@about')->name('about');
Route::get('/fee-structures', 'PageController@feeStructures')->name('fee_structures');
Route::get('/fsp-disclosures', 'PageController@fspDisclosures')->name('fsp_disclosures');

// Keepalive For Trade Screen
Route::get('/ping', function(\Illuminate\Http\Request $request) {
    return response("pong");
    // $authed = \Auth::guard('web')->check();
    // $ft = (int) $request->get('t');
    // $bt = (int) round(microtime(true) * 1000);
    // // valid session still alive, continue
    // return response()->json([
    //     "ft" => $ft,
    //     "bt" => $bt,
    //     "bfd" => ($bt - $ft),
    //     "authed" => $authed
    // ]);
});

Route::group(['middleware' => ['auth','active','redirectOnFirstLogin','RedirectProfileStep','timeWindowPreventAction']], function () {
    Route::group(['middleware' => ['verified']], function () {
        Route::get('/previous-day', 'PreviousDayController@index')->name('previous_day');
        Route::get('/previous-day/markets', 'PreviousDayController@showMarkets')->name('previous_day.markets');
        Route::get('/previous-day/market-requests', 'PreviousDayController@showMarketRequests')->name('previous_day.market_requests');
    });
});

Route::group(['middleware' => ['auth','active','redirectOnFirstLogin','RedirectProfileStep','timeWindowPreventAction']], function () {

	Route::group(['middleware' => ['verified']], function () {

		Route::resource('user-pref', 'UserPrefController');

		Route::group(['prefix' => 'stats'], function() {
			Route::get('/my-activity', 'Stats\ActivityControlller@show')->name('activity.show');
			Route::get('/my-activity/year', 'Stats\ActivityControlller@yearActivity')
				->name('my_activity.year');
			Route::get('/my-activity/markets', 'Stats\MarketController@index')
				->name('my_activity.markets');
			
			Route::get('/market-activity', 'Stats\ActivityControlller@index')->name('activity.index');
			Route::get('/market-activity/safex', 'Stats\ActivityControlller@safexRollingData')->name('activity.safex');
			
			Route::get('/open-interest', 'Stats\OpenInterestControlller@show')->name('open_interest.show');
			//Route::get('/open-interest/table', 'Stats\OpenInterestControlller@openInterestTableData')->name('open_interest.table');
		});

		Route::get('/rebates-summary', 'RebatesSummaryController@index')->name('rebate_summary.index');
		Route::get('/rebates-summary/year', 'RebatesSummaryController@show')->name('rebate_summary.show');
	});

	Route::get('/my-profile', 'UserController@edit')->name('user.edit');
	Route::post('/my-profile','UserController@update')->name('user.update');

	Route::get('/change-password', 'UserController@editPassword')->name('user.edit_password');
	Route::put('/change-password','UserController@storePassword')->name('user.change_password');

	Route::get('/terms-and-conditions', 'UserController@termsOfConditions')->name('tsandcs.edit');
	Route::put('/terms-and-conditions','UserController@storeTermsAndConditions')->name('tsandcs.update');

	Route::get('/email-settings', 'EmailController@edit')->name('email.edit');
	Route::post('/email-settings','EmailController@store')->name('email.store');
	Route::put('/email-settings','EmailController@update')->name('email.update');

	Route::get('/trade-accounts', 'TradingAccountController@index')->name('trade_settings.index');

	Route::get('/trade-settings', 'TradingAccountController@edit')->name('trade_settings.edit');
	Route::put('/trade-settings','TradingAccountController@update')->name('trade_settings.update');

	Route::get('/interest-settings', 'InterestController@edit')->name('interest.edit');
	Route::put('/interest-settings','InterestController@update')->name('interest.update');

	Route::impersonate();

});



Route::group(['prefix' => 'trade', 'middleware' => ['auth','active','verified','timeWindowPreventAction','timeWindowPreventTrade']], function() {

	Route::get('/organisation-chat', 'TradeScreen\ChatController@index');
	Route::post('/organisation-chat', 'TradeScreen\ChatController@sendChat');

	// Removed as per email also commented on Task [MM-987]
	/*Route::middleware(['timeWindowRestrictTrade'])->group(function () {*/

	    Route::get('/', 'TradeScreenController@index')->name('trade');

	    Route::get('/previous-quotes', 'PreviousDayController@getOldQuotes')->name('previous-quotes');
	    Route::post('/previous-quotes', 'PreviousDayController@refreshOldQuotes')->name('previous-quotes.refresh');

	    Route::get('/previous-negotiations', 'PreviousDayController@getOldNegotiations')->name('previous-negotiations');
	    Route::post('/previous-negotiations', 'PreviousDayController@refreshOldNegotiations')->name('previous-negotiations.refresh');

		Route::resource('market.market-request', 'TradeScreen\MarketUserMarketReqeustController')->only(['index', 'store', 'destroy']);
	    Route::resource('market-type', 'TradeScreen\MarketTypeController')->only(['index', 'show']);
	    Route::resource('market-type.market', 'TradeScreen\MarketTypeMarketController')->only(['index']);

	    Route::get('market-type/{marketType}/trade-structure', 'TradeScreen\MarketType\TradeStructureController@index');
		Route::resource('market-type/{market_type}/trade-confirmations', 'TradeScreen\MarketType\TradeConfirmationController', [
			'only' => ['index']
		]);

	    Route::get('safex-expiration-date', 'TradeScreen\SafexExpirationDateController@index');
	    Route::get('stock', 'TradeScreen\StockController@index');
	    
	    Route::post('trade-negotiation/{trade_negotiation}/no-further-cares',
			'TradeScreen\MarketNegotiation\TradeNegotiationController@noFurtherCares');

	    Route::post(
	    	'user-market-request/{user_market_request}/user-market/{user_market}/work-the-balance', 
	    	'TradeScreen\MarketRequest\UserMarketController@workTheBalance'
	    );

	    Route::resource('user-market-request.user-market', 'TradeScreen\MarketRequest\UserMarketController')->only(['store', 'update', 'destroy']);
	    Route::resource('user-market.market-negotiation', 'TradeScreen\UserMarket\MarketNegotiationController')->only(['store', 'update', 'destroy']);
	    
	    Route::post('user-market/{user_market}/market-negotiation/{market_negotiation}/repeat', 'TradeScreen\UserMarket\MarketNegotiationController@repeatProposal');
	    Route::post('user-market/{user_market}/market-negotiation/{market_negotiation}/counter', 'TradeScreen\UserMarket\MarketNegotiationController@counterProposal');
	    Route::post('user-market/{user_market}/market-negotiation/{market_negotiation}/improve', 'TradeScreen\UserMarket\MarketNegotiationController@improveBest');
	    
	    Route::resource('market-negotiation.trade-negotiation', 'TradeScreen\MarketNegotiation\TradeNegotiationController')->only(['store']);

		Route::post('trade-confirmation/{trade_confirmation}/dispute','TradeScreen\TradeConfirmationController@dispute');
	 	Route::post('trade-confirmation/{trade_confirmation}/confirm','TradeScreen\TradeConfirmationController@confirm');
	    Route::post('trade-confirmation/{trade_confirmation}/phase-two','TradeScreen\TradeConfirmationController@phaseTwo');

	    Route::put('trade-confirmation/{trade_confirmation}','TradeScreen\TradeConfirmationController@update');

	    Route::post('stream','TradeScreen\StreamController@index');
	    Route::post('/user-market-request/{user_market_request}/action-taken','TradeScreen\MarketUserMarketReqeustController@actionTaken');

	    Route::post('market-request-subscribe/{user_market_request}','TradeScreen\UserMarketRequestSubscritionController@ToggleAlertCleared');

	    /*
	    *	Activity Routes
	    */
	    Route::delete('/user-market/{user_market}/activity/{activity}', 'ActivityController@userMarket');
	    
	    /*
	    * Admin Trade Screen Functionality
	    */
	    Route::group(['prefix' => 'admin', 'middleware' => ['role:Admin','active',]], function() {
	        Route::resource('market-request', 'TradeScreen\Admin\MarketRequestController', [
	            'as' => 'market-request',
	            'only' => ['show']
	        ]);
	        
	        Route::post('user-market/{user_market}/market-negotiation/{market_negotiation}/reset-timer', 'TradeScreen\UserMarket\MarketNegotiationController@alterTradeAtBestTimer');
	    });
	/*});	*/

});

/**
 * Admin routes
 */
Route::group(['prefix' => 'admin', 'middleware' => ['role:Admin','active',]], function() {

	Route::resource('user', 'Admin\UserController', [
		'as' => 'admin'
	]);

	Route::get('organisation-man', 'Admin\OrganisationManagementController@index')
		->name('admin.organisation-man.index');
	Route::put('organisation-man/{organisation}', 'Admin\OrganisationManagementController@update')
		->name('admin.organisation-man.update');

	Route::get('logging/download', 'Admin\ActivityLogController@download')->name('admin.logging.download');
    Route::get('logging', 'Admin\ActivityLogController@index')->name('admin.logging.index');

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
		Route::post('/open-interest','Stats\OpenInterestControlller@store')
			->name('open-interest.upload_data');
		Route::get('/bank-activity', 'Stats\ActivityControlller@adminShow')->name('admin.activity.show');
    });

    Route::resource('booked-trades', 'Admin\BookedTradesController', [
		'as' => 'admin'
	]);
	Route::get('booked-trades-csv','Admin\BookedTradesController@downloadCsv');

	Route::resource('rebates', 'Admin\RebatesController', [
		'as' => 'admin'
	]);
	Route::get('rebates-csv','Admin\RebatesController@downloadCsv');
	Route::get('/rebates-summary', 'Admin\RebatesController@summaryIndex')->name('admin.rebate_summary.index');

	Route::get('organisation', 'Admin\OrganisationController@index');
	Route::get('organisation/{organisation}/users', 'Admin\OrganisationController@users');

	Route::get('markets', 'Admin\MarketController@index')->name('admin.markets.index');
	Route::put('markets','Admin\MarketController@update')->name('admin.markets.update');

	Route::get('/mfa', 'Admin\MfaController@index')->name('admin.mfa.index');
	Route::get('/mfa-setup', 'Admin\MfaController@setup')->name('admin.mfa.setup');
	Route::get('/mfa-finish-setup', 'Admin\MfaController@finishSetup')->name('admin.mfa.finish_setup');
	Route::post('2fa', function () {
		return redirect('/admin/user');
	})->name('2fa')->middleware('2fa');
	
	Route::resource('brokerage-fees', 'Admin\BrokerageFeesController', [
		'as' => 'admin',
		'only' => ['index','edit','update']
	]);

	Route::post('brokerage-fees/trade-structures','Admin\BrokerageFeesController@updateTradeStructureFees');
});

Route::group(['middleware' => ['auth']], function() {
		Route::get('assemble/oldstyle', function () {
        return view('assemble.oldstyle')->with(['is_admin_update'=>false]);
    }); 
});


Route::group(['prefix' => 'document'], function() {
    Route::get('terms-and-conditions', 'PDFController@termsAndConditions');
    Route::get('privacy-policy', 'PDFController@privacyPolicy');
    Route::get('conflicts-of-interest-policy', 'PDFController@conflictsOfInterestPolicy');

    Route::get('trading-spreads-and-fees', 'PDFController@tradingSpreads', [ 'middleware' => 'auth' ]);
});
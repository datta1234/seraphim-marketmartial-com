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
Route::get('/about', 'PageController@about')->name('about');
Route::post('/contact', 'PageController@contactMessage')->name('contact');

Route::group(['middleware' => ['auth']], function () {
	Route::get('/trade', 'TradeScreenController@index')->name('trade');

	Route::get('/my-profile', 'UserController@edit')->name('user.edit');
	Route::post('/my-profile','UserController@update')->name('user.update');

	Route::get('/change-password', 'UserController@editPassword')->name('user.edit_password');
	Route::post('/change-password','UserController@storePassword')->name('user.change_password');

	Route::get('/email-settings', 'EmailController@edit')->name('email.edit');
	Route::post('/email-settings','EmailController@update')->name('email.update');

	Route::get('/account-settings', 'TradingAccountController@edit')->name('account.edit');
	Route::post('/account-settings','TradingAccountController@update')->name('account.update');

});

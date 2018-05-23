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
	Route::get('/trade', 'PageController@trade')->name('trade');
});

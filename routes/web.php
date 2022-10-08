<?php

use Illuminate\Support\Facades\Route;

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

Route::fallback('PageController@fallback')->name('fallback');

Route::get('/{path?}', 'PageController@index')
	->where('path', '^((?!api).)*$')
	->where('path', '^((?!admin).)*$')
	->name('home');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function() {
});
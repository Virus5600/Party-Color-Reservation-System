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

// Route::fallback('PageController@fallback')->name('fallback');
Route::get('/login', 'UserController@redirectLogin')->name('redirectLogin');

Route::group(['prefix' => 'admin'], function() {
	// AUTHENTICATION RELATED
	Route::get('/login', 'UserController@login')->name('login');
	Route::get('/logout', 'UserController@logout')->name('logout');
	Route::post('/authenticate', 'UserController@authenticate')->name('authenticate');

	// NEEDED AUTH
	Route::group(['middleware' => ['auth']], function() {
		// DASHBAORD
		Route::get('/', 'PageController@redirectToDashboard')->name('admin.redirectToDashboard');
		Route::get('/dashboard', 'PageController@dashboard')->name('admin.dashboard');

		// USERS
		Route::group(['prefix' => 'users', 'middleware' => ['permissions:users_tab_access']], function() {
			Route::get('/', 'UserController@index')->name('admin.users.index');

			// Create
			Route::group(['middleware' => ['permissions:users_tab_create']], function() {
				Route::get('/create', 'UserController@create')->name('admin.users.create');
				Route::post('/create/store', 'UserController@store')->name('admin.users.store');
			});
		});
		
		// PERMISSIONS
		Route::group(['prefix' => 'permissions', 'middleware' => ['permissions:permissions_tab_access']], function() {
			Route::get('/', 'PermissionController@index')->name('admin.permissions.index');
		});

		// SETTINGS
		Route::group(['prefix' => 'settings', 'middleware' => ['permissions:settings_tab_access']], function() {
			Route::get('/', 'SettingsController@index')->name('admin.settings.index');
		});
	});
});

Route::get('/{path?}', 'PageController@index')
	->where('path', '^((?!api).)*$')
	->name('home');
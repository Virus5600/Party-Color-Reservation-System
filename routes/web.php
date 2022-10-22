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

		// ANNOUNCEMENTS
		Route::group(['prefix' => 'announcements', 'middleware' => ['permissions:announcements_tab_access']], function() {
			Route::get('/', 'AnnouncementController@index')->name('admin.announcements.index');

			// Create
			Route::group(['middleware' => ['permissions:announcements_tab_create']], function() {
				Route::get('/create', 'AnnouncementController@create')->name('admin.announcements.create');
				Route::post('/create/store', 'AnnouncementController@store')->name('admin.announcements.store');
			});
		});

		// USERS
		Route::group(['prefix' => 'users', 'middleware' => ['permissions:users_tab_access']], function() {
			Route::get('/', 'UserController@index')->name('admin.users.index');

			// Create
			Route::group(['middleware' => ['permissions:users_tab_create']], function() {
				Route::get('/create', 'UserController@create')->name('admin.users.create');
				Route::post('/create/store', 'UserController@store')->name('admin.users.store');
			});

			// Edit
			Route::group(['middleware' => ['permissions:users_tab_edit']], function() {
				Route::get('/{id}/edit', 'UserController@edit')->name('admin.users.edit');
				Route::post('/{id}/update', 'UserController@update')->name('admin.users.update');
				Route::post('/{id}/change-password', 'UserController@changePassword')->name('admin.users.change-password');
			});

			// Permission Management
			Route::group(['middleware' => ['permissions:users_tab_permissions']], function() {
				Route::get('/{id}/manage-permissions', 'UserController@managePermissions')->name('admin.users.manage-permissions');
				Route::get('/{id}/revert-permissions', 'UserController@revertPermissions')->name('admin.users.revert-permissions');
				Route::post('{id}/update-permissions', 'UserController@updatePermissions')->name('admin.users.update-permissions');
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

// React (User) Routing. This is handled by react router instead of the web.php
Route::get('/{path?}', 'PageController@index')
	->where('path', '^((?!api|admin))*$')
	->name('home');
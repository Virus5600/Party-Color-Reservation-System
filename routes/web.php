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

		// INVENTORY
		Route::group(['prefix' => 'inventory', 'middleware' => ['permissions:inventory_tab_access']], function() {
			// Create
			Route::group(['prefix' => 'create', 'middleware' => ['permissions:inventory_tab_create']], function() {
				Route::get('/', 'InventoryController@create')->name('admin.inventory.create');
				Route::post('/store', 'InventoryController@store')->name('admin.inventory.store');
			});

			// Edit
			Route::group(['middleware' => ['permissions:inventory_tab_edit']], function() {
				Route::get('/{id}/edit', 'InventoryController@edit')->name('admin.inventory.edit');
				// Route::post('/{id}/update', 'InventoryController@update')->name('admin.inventory.update');
			});

			// Delete
			Route::group(['middleware' => ['permissions:inventory_tab_delete']], function() {
				Route::get('/{id}/delete', 'InventoryController@delete')->name('admin.inventory.delete');
				Route::get('/{id}/restore', 'InventoryController@restore')->name('admin.inventory.restore');
			});
			
			// Permanent Delete
			Route::get('/{id}/perma-delete', 'InventoryController@permaDelete')->name('admin.inventory.permaDelete')->middleware('permissions:inventory_tab_perma_delete');

			// Publishing and Unpublishing
			// Route::get('/{id}/publish', 'InventoryController@publish')->name('admin.inventory.publish')->middleware('permissions:inventory_tab_publish');
			// Route::get('/{id}/unpublish', 'InventoryController@unpublish')->name('admin.inventory.unpublish')->middleware('permissions:inventory_tab_unpublish');

			// Index
			Route::get('/', 'InventoryController@index')->name('admin.inventory.index');
		});


		// ANNOUNCEMENTS
		Route::group(['prefix' => 'announcements', 'middleware' => ['permissions:announcements_tab_access']], function() {
			// Create
			Route::group(['prefix' => 'create', 'middleware' => ['permissions:announcements_tab_create']], function() {
				Route::get('/', 'AnnouncementController@create')->name('admin.announcements.create');
				Route::post('/store', 'AnnouncementController@store')->name('admin.announcements.store');
			});

			// Edit
			Route::group(['middleware' => ['permissions:announcements_tab_edit']], function() {
				Route::get('/{id}/edit', 'AnnouncementController@edit')->name('admin.announcements.edit');
				Route::post('/{id}/update', 'AnnouncementController@update')->name('admin.announcements.update');
			});

			// Delete
			Route::group(['middleware' => ['permissions:users_tab_delete']], function() {
				Route::get('/{id}/delete', 'AnnouncementController@delete')->name('admin.announcements.delete');
				Route::get('/{id}/restore', 'AnnouncementController@restore')->name('admin.announcements.restore');
			});
			
			// Permanent Delete
			Route::get('/{id}/perma-delete', 'AnnouncementController@permaDelete')->name('admin.announcements.permaDelete')->middleware('permissions:users_tab_perma_delete');

			// Publishing and Unpublishing
			Route::get('/{id}/publish', 'AnnouncementController@publish')->name('admin.announcements.publish')->middleware('permissions:announcements_tab_publish');
			Route::get('/{id}/unpublish', 'AnnouncementController@unpublish')->name('admin.announcements.unpublish')->middleware('permissions:announcements_tab_unpublish');

			// Index
			Route::get('/', 'AnnouncementController@index')->name('admin.announcements.index');

			// Show
			Route::get('/{id}', 'AnnouncementController@show')->name('admin.announcements.show');
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
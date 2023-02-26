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
Route::get('login', 'UserController@redirectLogin')->name('redirectLogin');

Route::group(['prefix' => 'admin'], function() {
	// FORGOT PASSWORD
	Route::group(['prefix' => 'forgot-password'], function() {
		// Index
		Route::get('/', 'PasswordResetController@index')->name('forgot-password.index');
		// Submit
		Route::post('/submit', 'PasswordResetController@submit')->name('forgot-password.submit');
		
	});

	// CHANGE PASSWORD
	Route::group(['prefix' => 'change-password'], function() {
		// Edit
		Route::get('{token}', 'PasswordResetController@edit')->name('change-password.edit');

		// Update
		Route::post('{token}/update', 'PasswordResetController@update')->name('change-password.update');
	});
	
	// AUTHENTICATION RELATED
	Route::get('login', 'UserController@login')->name('login');
	Route::get('logout', 'UserController@logout')->name('logout');
	Route::post('authenticate', 'UserController@authenticate')->name('authenticate');

	// NEEDED AUTH
	Route::group(['middleware' => ['auth', 'auth:sanctum']], function() {
		// API PASSWORD CONFIRM
		Route::get('confirm-password', 'ApiController@confirmPassword')->name('password.confirm');
		Route::post('check-password', 'ApiController@checkPassword')->middleware(['throttle:6,1'])->name('password.confirm.check');

		// DASHBAORD
		Route::get('/', 'PageController@redirectToDashboard')->name('admin.redirectToDashboard')->middleware(['permissions:sanctum']);
		Route::get('dashboard', 'PageController@dashboard')->name('admin.dashboard')->middleware(['permissions:sanctum']);

		// BOOKINGS
		Route::group(['prefix' => 'bookings', 'middleware' => ['permissions:bookings_tab_access']], function() {
			// Create
			Route::group(['middleware' => ['permissions:bookings_tab_create']], function() {
				Route::get('create', 'BookingController@create')->name('admin.bookings.create');
				Route::post('store', 'BookingController@store')->name('admin.bookings.store');
			});

			Route::group(['prefix' => '{id}'], function() {
				// Show
				Route::get('/', 'BookingController@show')->name('admin.bookings.show');

				// Edit
				Route::group(['middleware' => ['permissions:bookings_tab_edit']], function() {
					Route::get('edit', 'BookingController@edit')->name('admin.bookings.edit');
					Route::post('update', 'BookingController@update')->name('admin.bookings.update');
				});

				// Status
				Route::group(['prefix' => 'status', 'middleware' => 'permissions:bookings_tab_respond'], function() {
					// Accept Cancellation
					Route::post('accept-cancellation', 'BookingController@acceptCancel')->name('admin.bookings.status.accept-cancel');
					Route::post('reject-cancellation', 'BookingController@rejectCancel')->name('admin.bookings.status.reject-cancel');
					
					// Reject
					Route::post('reject', 'BookingController@reject')->name('admin.bookings.status.reject');

					// Accept
					Route::get('accept', 'BookingController@accept')->name('admin.bookings.status.accept');

					// Pending
					Route::get('pending', 'BookingController@pending')->name('admin.bookings.status.pending');

				});

				// Delete/Archive
				Route::group(['middleware' => ['permissions:bookings_tab_delete']], function() {
					Route::get('archive', 'BookingController@archive')->name('admin.bookings.archive');
					Route::get('restore', 'BookingController@restore')->name('admin.bookings.restore');

					Route::get('delete', 'BookingController@delete')->name('admin.bookings.delete');
				});

				// ADDITIONAL ORDERS
				Route::group(['prefix' => 'additional-orders'], function() {
					// Index
					Route::get('/', 'AdditionalOrderController@index')->name('admin.bookings.additional-orders.index');

					// Create
					Route::get('create', 'AdditionalOrderController@create')->name('admin.bookings.additional-orders.create');
					Route::post('store', 'AdditionalOrderController@store')->name('admin.bookings.additional-orders.store');

					Route::group(['prefix' => '{order_id}'], function() {
						// Show
						Route::get('/', 'AdditionalOrderController@show')->name('admin.bookings.additional-orders.show');

						// Edit
						Route::get('edit', 'AdditionalOrderController@edit')->name('admin.bookings.additional-orders.edit');
						Route::post('update', 'AdditionalOrderController@update')->name('admin.bookings.additional-orders.update');

						// Delete
						Route::get('void', 'AdditionalOrderController@delete')->name('admin.bookings.additional-orders.void')->middleware(['password.confirm']);
					});
				});
			});

			// Index
			Route::get('/', 'BookingController@index')->name('admin.bookings.index');
		});

		// INVENTORY
		Route::group(['prefix' => 'inventory', 'middleware' => ['permissions:inventory_tab_access']], function() {
			// Create
			Route::group(['prefix' => 'create', 'middleware' => ['permissions:inventory_tab_create']], function() {
				Route::get('/', 'InventoryController@create')->name('admin.inventory.create');
				Route::post('store', 'InventoryController@store')->name('admin.inventory.store');
			});

			Route::group(['prefix' => '{id}'], function() {
				// Edit
				Route::group(['middleware' => ['permissions:inventory_tab_edit']], function() {
					Route::get('/edit', 'InventoryController@edit')->name('admin.inventory.edit');
					Route::post('/update', 'InventoryController@update')->name('admin.inventory.update');
				});

				// Delete
				Route::group(['middleware' => ['permissions:inventory_tab_delete']], function() {
					Route::get('delete', 'InventoryController@delete')->name('admin.inventory.delete');
					Route::get('restore', 'InventoryController@restore')->name('admin.inventory.restore');
				});

				// Show
				Route::get('/', 'InventoryController@show')->name('admin.inventory.show');
			});

			// Index
			Route::get('/', 'InventoryController@index')->name('admin.inventory.index');
		});

		// MENU
		Route::group(['prefix' => 'menu', 'middleware' => ['permissions:menu_tab_access']], function() {
			// Create
			Route::group(['prefix' => 'create', 'middleware' => ['permissions:menu_tab_create']], function() {
				Route::post('store', 'MenuController@store')->name('admin.menu.store');
			});

			Route::group(['prefix' => '{id}'], function() {
				// MENU VARIATION (Menu's show)
				Route::group(['prefix' => 'variations', 'middleware' => ['permissions:menu_var_tab_access']], function() {
					// Index
					Route::get('/', 'MenuVariationController@index')->name('admin.menu.variation.index');

					// Create
					Route::group(['prefix' => 'create', 'middleware' => ['permissions:menu_var_tab_create']], function() {
						Route::get('/', 'MenuVariationController@create')->name('admin.menu.variation.create');
						Route::post('store', 'MenuVariationController@store')->name('admin.menu.variation.store');
					});

					Route::group(['prefix' => '{vid}'], function() {
						// Show
						Route::get('/', 'MenuVariationController@show')->name('admin.menu.variation.show');

						// Edit
						Route::group(['middleware' => ['permissions:menu_var_tab_edit']], function() {
							Route::get('edit', 'MenuVariationController@edit')->name('admin.menu.variation.edit');
							Route::post('update', 'MenuVariationController@update')->name('admin.menu.variation.update');
						});

						// Delete
						Route::group(['middleware' => ['permissions:menu_var_tab_delete']], function() {
							Route::get('delete', 'MenuVariationController@delete')->name('admin.menu.variation.delete');
							Route::get('restore', 'MenuVariationController@restore')->name('admin.menu.variation.restore');
						});
					});
				});

				// Edit
				Route::group(['middleware' => ['permissions:menu_tab_edit']], function() {
					Route::post('update', 'MenuController@update')->name('admin.menu.update');
				});

				// Delete
				Route::group(['middleware' => ['permissions:menu_tab_delete']], function() {
					Route::get('delete', 'MenuController@delete')->name('admin.menu.delete');
					Route::get('restore', 'MenuController@restore')->name('admin.menu.restore');
				});
			});

			// Index
			Route::get('/', 'MenuController@index')->name('admin.menu.index');
		});

		// ANNOUNCEMENTS
		Route::group(['prefix' => 'announcements', 'middleware' => ['permissions:announcements_tab_access']], function() {
			// Create
			Route::group(['prefix' => 'create', 'middleware' => ['permissions:announcements_tab_create']], function() {
				Route::get('/', 'AnnouncementController@create')->name('admin.announcements.create');
				Route::post('store', 'AnnouncementController@store')->name('admin.announcements.store');
			});

			Route::group(['prefix' => '{id}'], function() {
				// Edit
				Route::group(['middleware' => ['permissions:announcements_tab_edit']], function() {
					Route::get('edit', 'AnnouncementController@edit')->name('admin.announcements.edit');
					Route::post('update', 'AnnouncementController@update')->name('admin.announcements.update');
				});
				
				// Delete
				Route::group(['middleware' => ['permissions:announcements_tab_delete']], function() {
					Route::get('delete', 'AnnouncementController@delete')->name('admin.announcements.delete');
					Route::get('restore', 'AnnouncementController@restore')->name('admin.announcements.restore');
				});

				// Permanent Delete
				Route::get('perma-delete', 'AnnouncementController@permaDelete')->name('admin.announcements.permaDelete')->middleware('permissions:announcements_tab_perma_delete');

				// Publishing and Unpublishing
				Route::get('publish', 'AnnouncementController@publish')->name('admin.announcements.publish')->middleware('permissions:announcements_tab_publish');
				Route::get('unpublish', 'AnnouncementController@unpublish')->name('admin.announcements.unpublish')->middleware('permissions:announcements_tab_unpublish');
				
				// Show
				Route::get('/', 'AnnouncementController@show')->name('admin.announcements.show');
			});
			
			// Index
			Route::get('/', 'AnnouncementController@index')->name('admin.announcements.index');
		});

		// USERS
		Route::group(['prefix' => 'users', 'middleware' => ['permissions:users_tab_access']], function() {
			// Create
			Route::group(['prefix' => 'create', 'middleware' => ['permissions:users_tab_create']], function() {
				Route::get('/', 'UserController@create')->name('admin.users.create');
				Route::post('store', 'UserController@store')->name('admin.users.store');
			});

			Route::group(['prefix' => '{id}', 'middleware' => ['permissions:users_tab_create']], function() {
				// Edit
				Route::group(['middleware' => ['permissions:users_tab_edit']], function() {
					Route::get('edit', 'UserController@edit')->name('admin.users.edit');
					Route::post('update', 'UserController@update')->name('admin.users.update');
					Route::post('change-password', 'UserController@changePassword')->name('admin.users.change-password');
				});

				// Permission Management
				Route::group(['middleware' => ['permissions:users_tab_permissions']], function() {
					Route::get('manage-permissions', 'UserController@managePermissions')->name('admin.users.manage-permissions');
					Route::get('revert-permissions', 'UserController@revertPermissions')->name('admin.users.revert-permissions');
					Route::post('update-permissions', 'UserController@updatePermissions')->name('admin.users.update-permissions');
				});

				// Delete
				Route::group(['middleware' => ['permissions:users_tab_delete']], function() {
					Route::get('delete', 'UserController@delete')->name('admin.users.delete');
					Route::get('restore', 'UserController@restore')->name('admin.users.restore');
				});

				// Permanent Delete
				Route::get('perma-delete', 'UserController@permaDelete')->name('admin.users.permaDelete')->middleware('permissions:users_tab_perma_delete');

				// Show
				Route::get('/', 'UserController@show')->name('admin.users.show');
			});

			// Index
			Route::get('/', 'UserController@index')->name('admin.users.index');
		});
		
		// TYPES
		Route::group(['prefix' => 'types', 'middleware' => ['permissions:types_tab_access']], function() {
			// Create
			Route::group(['prefix' => 'create', 'middleware' => ['permissions:types_tab_create']], function() {
				Route::get('/', 'TypesController@create')->name('admin.types.create');
				Route::post('/', 'TypesController@store')->name('admin.types.store');
			});

			Route::group(['prefix' => '{id}'], function() {
				// Edit
				Route::post('update', 'TypesController@update')->name('admin.types.update')->middleware(['permissions:types_tab_edit']);

				// Permission Management
				Route::group(['middleware' => ['permissions:users_tab_permissions']], function() {
					Route::get('manage-permissions', 'TypesController@managePermissions')->name('admin.types.manage-permissions');
					Route::post('update-permissions', 'TypesController@updatePermissions')->name('admin.types.update-permissions');
				});

				// Delete
				Route::group(['middleware' => ['permissions:types_tab_delete']], function() {
					Route::get('delete', 'TypesController@delete')->name('admin.types.delete');
					Route::get('restore', 'TypesController@restore')->name('admin.types.restore');
				});

				// Permanent Delete
				Route::get('perma-delete', 'TypesController@permaDelete')->name('admin.types.permaDelete')->middleware('permissions:types_tab_perma_delete');

				// Show
				Route::get('/', 'TypesController@show')->name('admin.types.show');
			});

			// Index
			Route::get('/', 'TypesController@index')->name('admin.types.index');
		});

		// PERMISSIONS
		Route::group(['prefix' => 'permissions', 'middleware' => ['permissions:permissions_tab_access']], function() {
			// Show
			Route::get('{slug}', 'PermissionController@show')->name('admin.permissions.show');

			// Index
			Route::get('/', 'PermissionController@index')->name('admin.permissions.index');
		});

		// ACTIVITY LOG
		Route::group(['prefix' => 'activity-log', 'middleware' => ['permissions:activity_logs_tab_access']], function() {
			// Index
			Route::get('/', 'ActivityLogController@index')->name('admin.activity-log.index');

			Route::group(['prefix' => '{id}', 'middleware' => ['permissions:activity_logs_tab_manage']], function() {
				// Show
				Route::get('/', 'ActivityLogController@show')->name('admin.activity-log.show');

				// Update
				Route::post('update', 'ActivityLogController@update')->name('admin.activity-log.update');

				// Mark
				Route::post('mark', 'ActivityLogController@mark')->name('admin.activity-log.mark');
				
				// Unmark
				Route::post('unmark', 'ActivityLogController@unmark')->name('admin.activity-log.unmark');
			});
		});

		// SETTINGS
		Route::group(['prefix' => 'settings', 'middleware' => ['permissions:settings_tab_access']], function() {
			// Update
			Route::post('update', 'SettingsController@update')->name('admin.settings.update')->middleware('permissions:settings_tab_edit');

			// Index
			Route::get('/', 'SettingsController@index')->name('admin.settings.index');
		});
	});
});

// React (User) Routing. This is handled by react router instead of the web.php
Route::get('/{path?}', 'PageController@index')
	// ->where('path', '^(?!api).*|^(?!admin).*')
	->where('path', '.*')
	->name('home');
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// LARAVEL API ENDPOINTS
// (Admin) Search box request
Route::post('/admin-search/{id}', 'ApiController@adminSearch')->name('adminSearch');

// Removing Image
Route::post('/remove-image', 'ApiController@removeImage')->name('removeImage');

// Fetch Booking Event
Route::get('/booking/{id}', 'ApiController@fetchBookingEvent')->name('bookings.fetch-event');
Route::get('/booking/fetch/{monthYear?}', 'ApiController@fetchBookingFromRange')->name('api.admin.bookings.fetch');

// REACT API ENDPOINTS //
Route::group(['prefix' => 'react'], function() {
	// Announcements
	Route::group(['prefix' => 'announcements'], function() {
		Route::get('/fetch', 'ReactApiController@fetchAnnouncements')->name('api.react.announcements.fetch');
	});

	// Booking
	Route::group(['prefix' => 'bookings'], function() {
		Route::post('/create', 'ReactApiController@bookingsCreate')->name('api.react.bookings.create');
	});
});
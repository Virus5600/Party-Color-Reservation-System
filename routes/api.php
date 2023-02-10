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

// Fetch Reservation Event
Route::get('/reservation/{id}', 'ApiController@fetchReservationEvent')->name('reservations.fetch-event');
Route::get('/reservation/fetch/{monthYear?}', 'ApiController@fetchReservationFromRange')->name('api.admin.reservations.fetch');

// REACT API ENDPOINTS //
Route::group(['prefix' => 'react'], function() {
	// Announcements
	Route::group(['prefix' => 'announcements'], function() {
		Route::get('/fetch', 'ReactApiController@fetchAnnouncements')->name('api.react.announcements.fetch');
	});

	// Reservation
	Route::group(['prefix' => 'reservations'], function() {
		Route::post('/create', 'ReactApiController@reservationsCreate')->name('api.react.reservations.create');
	});
});
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

// (Admin) Search box request
Route::post('/admin-search/{id}', 'ApiController@adminSearch')->name('adminSearch');

// Removing Image
Route::post('/remove-image', 'ApiController@removeImage')->name('removeImage');

// Fetching Announcements
Route::get('/user/fetch-announcements', 'ApiController@fetchAnnouncements')->name('user.fetch-announcements');

// Fetch Reservation Event
Route::get('/reservation/{id}', 'ApiController@fetchReservationEvent')->name('reservations.fetch-event');
Route::get('/reservation/fetch/{monthYear?}', 'ApiController@fetchReservationFromRange')->name('api.admin.reservations.fetch');
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
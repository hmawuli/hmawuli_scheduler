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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/organisations', 'Api\ApiController@addOrganisation');
Route::post('/organisations/login', 'Api\ApiController@loginOrganisation');
Route::post('/timeslot', 'Api\ApiController@addTimeSlot');
Route::post('/clients', 'Api\ApiController@addClient');
Route::post('/clients/login', 'Api\ApiController@loginClient');
Route::post('/appointments', 'Api\ApiController@bookAppointment');

//test 

Route::get('/test', 'Api\ApiController@test');
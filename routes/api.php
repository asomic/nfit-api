<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

/** 
 * Plans resources
 */
Route::Apiresource('users.planusers', 'Users\PlanUserController');


/**
 *  Users resources
 */
Route::Apiresource('users', 'Users\UserController');
Route::Apiresource('users.emergencies', 'Users\UserEmergencyController');

/**
 *  Token for api
 */
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
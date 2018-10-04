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



/**
 *  Users resources
 */
Route::Apiresource('users', 'Users\UserController');
	Route::get('profile', 'Users\UserController@profile');
	Route::get('logout', 'Users\UserController@logout');
Route::Apiresource('users.emergencies', 'Users\UserEmergencyController');
Route::Apiresource('users.planusers', 'Users\PlanUserController');

/**
 *  Token for api
 */
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
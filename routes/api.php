<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
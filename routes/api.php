<?php

use Illuminate\Http\Request;

/**
 * Classes resources
 */
Route::Apiresource('clases', 'Clases\ClaseController');
	Route::post('clases/{clase}/reserve', 'Clases\ClaseController@reserve');
	Route::post('clases/{clase}/remove', 'Clases\ClaseController@remove');
/**
 *  Users resources
 */
Route::Apiresource('users', 'Users\UserController');
	Route::get('profile', 'Users\UserController@profile');
	Route::get('logout', 'Users\UserController@logout');
Route::Apiresource('users.emergencies', 'Users\UserEmergencyController');
Route::Apiresource('users.planusers', 'Users\PlanUserController');
	Route::get('users/{user}/planusers-active', 'Users\PlanUserController@active')->name('users.planusers.active');

	/**
	 *  Wods
	 */
	Route::get('todaywods', 'Wods\WodController@today')->name('wods.today');

/**
 *  Token for api
 */
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

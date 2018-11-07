<?php

use Illuminate\Http\Request;

/**
 * Classes resources
 */
Route::Apiresource('clases', 'Clases\ClaseController');
	Route::get('clases/{clase}/users', 'Clases\ClaseController@users')->name('clases.users');
	Route::get('clases-historic/', 'Clases\ClaseController@historic')->name('clases.historic');
	Route::get('clases-coming/', 'Clases\ClaseController@coming')->name('clases.coming');
	Route::post('clases/{clase}/reserve', 'Clases\ClaseController@reserve');
	Route::post('clases/{clase}/remove', 'Clases\ClaseController@remove');

/**
 * Reservation resources
 */
Route::Apiresource('reservations', 'Clases\ReservationController');
//Route::get('reservations/{reservation}/users', 'Clases\ReservationController@users')->name('reservations.users');
	Route::get('reservations-historic/', 'Clases\ReservationController@historic')->name('reservations.historic');
	Route::get('reservations-coming/', 'Clases\ReservationController@coming')->name('reservations.reserved');
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
Route::Apiresource('wods', 'Wods\WodController');
	Route::get('todaywods', 'Wods\WodController@today')->name('wods.today');

/**
 *  Token for api
 */
Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

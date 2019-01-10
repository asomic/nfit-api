<?php

use Illuminate\Http\Request;

/**
 * Bills resources
 */
Route::Apiresource('bills', 'Bills\BillController');

/**
 * Classes resources
 */
Route::Apiresource('clases', 'Clases\ClaseController');
	Route::get('clases/{clase}/users', 'Clases\ClaseController@users')->name('clases.users');
	Route::get('clases/{clase}/reservations', 'Clases\ClaseController@reservations')->name('clases.reservations');
	Route::get('clases-historic/', 'Clases\ClaseController@historic')->name('clases.historic');
	Route::get('clases-coming/', 'Clases\ClaseController@coming')->name('clases.coming');
	Route::post('clases/{clase}/reserve', 'Clases\ClaseController@reserve');
	Route::post('clases/{clase}/remove', 'Clases\ClaseController@remove');
	Route::post('clases/{clase}/confirm', 'Clases\ClaseController@confirm');


//week
	Route::get('week', 'Clases\ClaseController@week');
/**
 * Reservation resources
 */
Route::Apiresource('reservations', 'Clases\ReservationController');
//Route::get('reservations/{reservation}/users', 'Clases\ReservationController@users')->name('reservations.users');
	Route::get('reservations-historic/', 'Clases\ReservationController@historic')->name('reservations.historic');
	Route::get('reservations-coming/', 'Clases\ReservationController@coming')->name('reservations.reserved');
	Route::post('reservations/{reservation}/details', 'Clases\ReservationController@details')->name('reservations.details');
/**
 *  Users resources
 */
Route::Apiresource('users', 'Users\UserController');
	Route::get('profile', 'Users\UserController@profile');
	Route::post('profile/image', 'Users\UserController@image');
	Route::get('assistance', 'Users\UserController@assistance');
	Route::get('logout', 'Users\UserController@logout');
	Route::get('today', 'Users\UserController@today')->name('users.today');
	Route::get('plans', 'Users\UserController@plans')->name('users.plans');




Route::Apiresource('users.emergencies', 'Users\UserEmergencyController');
Route::Apiresource('users.planusers', 'Users\PlanUserController');
	Route::get('users/{user}/planusers-active', 'Users\PlanUserController@active')->name('users.planusers.active');
	Route::get('users-alerts', 'Users\UserController@alerts')->name('users.alerts');

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

<?php

Route::middleware(['auth:api'])->group(function() {
    /**
     * Bills resources
     */
    Route::Apiresource('bills', 'Bills\BillController');

    /**
     * Classes resources
     */
    Route::get('clases/historic', 'Clases\ClaseController@historic')->name('clases.historic');
    Route::get('clases/coming', 'Clases\ClaseController@coming')->name('clases.coming');
    Route::get('clases/types', 'Clases\ClaseController@types')->name('clases.types');
    Route::Apiresource('clases', 'Clases\ClaseController');
        Route::get('clases/{clase}/users', 'Clases\ClaseController@users')->name('clases.users');
        Route::get('clases/{clase}/reservations', 'Clases\ClaseController@reservations')->name('clases.reservations');

        Route::post('clases/{clase}/reserve', 'Clases\ClaseController@reserve');
        Route::post('clases/{clase}/remove', 'Clases\ClaseController@remove');
        Route::post('clases/{clase}/confirm', 'Clases\ClaseController@confirm');
        


    //week
        Route::get('week/{clase_type?}', 'Clases\ClaseController@week');
    /**
     * Reservation resources
     */
    Route::get('reservations/next', 'Clases\ReservationController@next')->name('reservations.next');
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
        Route::get('profile/tutorial', 'Users\UserController@tutorial');
        Route::post('profile/image', 'Users\UserController@image');
        Route::get('assistance', 'Users\UserController@assistance');
        Route::get('logout', 'Users\UserController@logout');
        Route::get('today', 'Users\UserController@today')->name('users.today');
        Route::get('profile/plans', 'Users\UserController@plans')->name('users.plans');
        Route::get('profile/actualplan', 'Users\UserController@actualPlan')->name('users.actualplan');

        Route::Apiresource('users.emergencies', 'Users\UserEmergencyController');
        Route::Apiresource('users.planusers', 'Users\PlanUserController');
        Route::get('users/{user}/planusers-active', 'Users\PlanUserController@active')->name('users.planusers.active');
        Route::get('users-alerts', 'Users\UserController@alerts')->name('users.alerts');
        

    /**
     *  Plans
     */

    Route::resource('plans', 'Plans\PlanController')->only([
        'index', 'show'
    ]);

        Route::get('plans/{plan}/dates', 'Plans\PlanController@dates')->name('plans.user.date');
        Route::get('plans/{plan}/contract', 'Plans\PlanController@contract')->name('plans.user.contract');

    /**
     *  FLOW
     */
        Route::get('flow/{planuserflow}', 'Flow\FlowController@payFlow')->name('flow.pay');


        
    /**
     *  Wods
     */
    Route::Apiresource('wods', 'Wods\WodController');
    Route::get('wods/{wod}/stages', 'Wods\WodController@stages')->name('wods.stages');
    Route::get('todaywods', 'Wods\WodController@today')->name('wods.today');

    /**
     *  Token for api
     */
    Route::post('oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
    Route::post('fcm/token', 'Users\UserController@fcmToken');
    Route::get('fcm/token/{token}', 'Users\UserController@fcmTokenGet');

    // Route::middleware('auth:api')->get('/user', function (Request $request) {
    //     return $request->user();
    // });

    /**
     *  PASSWORD RESET REQUEST
     */
    Route::group(['middleware' => 'api', 'prefix' => 'password'], function () {
        Route::post('reset', 'Users\PasswordResetController@reset')->name('password.reset');
    });

});

Route::post(
    'oauth/token',
    '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken'
)->middleware('tenancy.enforce');

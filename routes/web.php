<?php

use Hyn\Tenancy\Database\Connection;

// Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['web'])->domain('admin.' . env('TENANCY_DEFAULT_HOSTNAME'))
                           ->group(function() {
                            Route::get('/clients-json', 'System\ClientController@clientsJson')->name('clients-json');
                            // return view('welcome');

});

//flow
Route::post('/flow/return','Flow\FlowController@returnFlow')->name('flow.return');
Route::post('/flow/confirm','Flow\FlowController@confirmFlow')->name('flow.confirm');
Route::get('/flow-return',function () {
    return view('flow.return');
});
Route::get('/flow-error',function () {
    return view('flow.error');
});



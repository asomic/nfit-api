<?php

Route::middleware(['auth:api'])->group(function() {
    Route::get('hola', function() {
        return 'holass';
    })->middleware('tenancy.enforce');
    Route::get('cn', 'HomeController@cn');
    Route::get('profile', 'Users\UserController@profile');
});

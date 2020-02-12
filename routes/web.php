<?php

use Hyn\Tenancy\Database\Connection;

// Auth::routes();

Route::get('holas', function(){
    dd(app(\Hyn\Tenancy\Environment::class)->tenant());
    return 'hola';
});



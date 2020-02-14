<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\System\Client;


class ClientController extends Controller
{

    /**
     * Through ajax,
     * get all users who meet certain requirements,
     * indicated in the table of all users
     *
     * @return json
     */
    public function clientsJson()
    {
        dd('peo');
        $clients = Client::all();

        return response()->json(['data' => $clients]);
    }
}

<?php

namespace App\Http\Controllers\System\Clients;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\System\Clients\Client;


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
        $clients = Client::all();

        return response()->json(['data' => $clients]);
    }
}

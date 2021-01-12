<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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
        $clients = Client::all();

        return response()->json(['data' => $clients]);
    }

    public function getDomain($email)
    {
        $boxUser = DB::table('box_users')->whereNull('deleted_at')->where('email',$email)->latest()->first();
        return response()->json(['domain' => $boxUser->domain ]);
    }
}

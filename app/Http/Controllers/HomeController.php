<?php

namespace App\Http\Controllers;

use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Models\Hostname;
use App\Http\Controllers\Controller;
use Hyn\Tenancy\Database\Connection;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function cn()
    {
        dd(app(\Hyn\Tenancy\Environment::class)->tenant());
        // $cn = app(Connection::class)->get()->getConfig();
// dd($cn);
        $uuid = $cn->getConfig()['uuid'];
        $website = Website::where('uuid', $uuid)->first();
        $hostname = Hostname::find($website->id);
        $fqdn = $hostname->fqdn;
        $hostnameParts = explode(".", $fqdn);
        // return $hostnameParts;
        return $hostnameParts[0];

    }
}

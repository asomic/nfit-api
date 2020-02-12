<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;

/** [ApiController description] */
class ApiController extends Controller
{
    use ApiResponser;

    /** [__construct description] */
    public function __construct()
    {
        /** Use auth "api", appointed on config/auth */
        $this->middleware('auth:api');
    }
}

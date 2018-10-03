<?php

namespace App\Http\Controllers\Users;

use App\Models\Users\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserEmergencyController extends ApiController
{
    /**
     * [index description]
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public function index(User $user)
    {
        $emergency = $user->emergency;
        return $this->showOne($emergency);
    }

    
    public function update(Request $request, User $user)
    {
        //
    }
}

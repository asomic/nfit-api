<?php

namespace App\Traits;

use App\Models\Users\RoleUser;

trait AdminActions
{
	public function before($user, $ability)
    {
    	$admin = RoleUser::where('role_id', 1)->where('user_id', $user->id)->get();
        if (count($admin) != 0) {
            return true;
        }
    }
}
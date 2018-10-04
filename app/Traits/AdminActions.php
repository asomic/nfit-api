<?php

namespace App\Traits;

trait AdminActions
{
	public function before($user, $ability)
    {
        if ($user->role_user('role_id', 1)) {
            return true;
        }
    }
}
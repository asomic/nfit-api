<?php

namespace App\Policies\Users;

use App\Models\Users\User;
use App\Traits\AdminActions;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization, AdminActions;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\Users\User  $user
     * @param  \App\Models\Users\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        return $user->id === $model->id;
    }
}

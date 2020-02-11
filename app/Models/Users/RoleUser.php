<?php

namespace App\Models\Users;

use App\Models\Users\Role;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    /**
     * [$table description]
     *
     * @var [type]
     */
	protected $table = 'role_user';

    /**
     * [$fillable description]
     *
     * @var [type]
     */
    protected $fillable = ['role_id', 'user_id'];

    /**
     * [role description]
     *
     * @return  [type]  [return description]
     */
	public function role()
	{
		return $this->belongsTo(Role::class);
	}

    /**
     * [user description]
     *
     * @return  [type]  [return description]
     */
    public function user()
	{
		return $this->belongsTo(User::class);
	}
}

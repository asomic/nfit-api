<?php

namespace App\Models\Users;

use App\Models\Users\Role;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    protected $table = 'role_user';
    
	protected $fillable = ['role_id', 'user_id'];

	public function role()
	{
		return $this->belongsTo(Role::class);
	}

    public function user()
	{
		return $this->belongsTo(User::class);
	}
}

<?php

namespace App\Models\Users;

use App\Models\Users\User;
use App\Models\Users\RoleUser;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
   	// Innecesario ya que se agregara desde DB
   	protected $fillable = ['role'];

	/**
    * [users description]
	* @method users
	* @return [model] [description]
	*/
	public function users()
	{
	    return $this->belongsToMany(User::class)->using(RoleUser::class);
	}	
}

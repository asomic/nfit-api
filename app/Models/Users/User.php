<?php

namespace App\Models\Users;

use Laravel\Passport\HasApiTokens;
use App\Notifications\MyResetPassword;
use Illuminate\Notifications\Notifiable;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, UsesTenantConnection, SoftDeletes;

    /**
     * [$dates description]
     * @var [type]
     */
    protected $dates = ['birthdate', 'since', 'deleted_at'];

    /**
     * Massive Assignment for this Model
     *
     * @var array
     */
    protected $fillable = [
        'rut', 'first_name', 'last_name',
        'email', 'password', 'avatar', 'phone',
        'birthdate', 'gender', 'address',
        'since', 'emergency_id', 'status_user'
    ];

    /**
     * [$hidden description]
     * @var [type]
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * [$appends description]
     *
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * [getFullNameAttribute description]
     *
     * @return [type] [description]
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

}

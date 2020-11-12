<?php

namespace App\Models\System\Users;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoxUser extends Model
{
    use UsesSystemConnection, SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     *  Massive Assignment for this Model
     *
     *  @var array
     */
    protected $fillable = [
        'email', 'domain', 'box_user_id'
    ];
}

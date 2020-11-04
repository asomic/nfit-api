<?php

namespace App\Models\Plans;

use App\Models\Users\User;
use App\Models\Plans\PlanUser;
use App\Models\Plans\PlanPeriod;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\PlanTransformer;

/**
 * [Plan description]
 */
class Plan extends Model
{
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    public $transformer = PlanTransformer::class;
    
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $fillable = [
        'plan', 'plan_period_id', 'class_numbers', 'amount'
    ];

    /**
     * Undocumented function
     *
     * @return void
     */
    public function plan_period()
    {
        return $this->belongsTo(PlanPeriod::class);
    }

    /**
     *  Undocumented function
     *
     *  @return  void
     */
    public function plan_users()
    {
        return $this->hasMany(PlanUser::class);
    }

    /**
     *  Get all the users related to this Model
     *
     *  @return  App\Models\Tenant\Users\User
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->using(PlanUser::class);
    }

    /**
     *  Get blocks related to this model
     *
     * @return App\Models\Clases\Block
     */
    public function blocks()
    {
        return $this->belongsToMany('App\Models\Clases\Block', 'block_plan');
    }
}

<?php

namespace App\Models\Plans;

use Illuminate\Database\Eloquent\Model;

class PlanStatus extends Model
{
    /**
     * [ description]
     *
     * @return  [type]  [return description]
     */
    const ACTIVE = 1;

     /**
     * [ description]
     *
     * @return  [type]  [return description]
     */
    const INACTIVE = 2;

     /**
     * [ description]
     *
     * @return  [type]  [return description]
     */
    const PRUEBA = 3;

    /**
     * [$table description]
     *
     * @var  string
     */
    protected $table = 'plan_status';

    /**
     * [plans description]
     *
     * @return  [type]  [return description]
     */
    public function plans()
    {
        return $this->hasMany(Plan::class);
    }
}

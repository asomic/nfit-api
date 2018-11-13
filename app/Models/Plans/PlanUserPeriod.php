<?php

namespace App\Models\Plans;

use App\Models\Plans\PlanUser;
use Illuminate\Database\Eloquent\Model;

class PlanUserPeriod extends Model
{
    protected $fillable = ['start_date', 'finish_date',
    'counter', 'plan_user_id'];

    /**
     * [planuser description]
     * @return [model] [description]
     */
    public function planuser()
    {
    	return $this->belongsTo(PlanUser::class);
    }
}

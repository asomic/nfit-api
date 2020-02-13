<?php

namespace App\Models\Plans;

use App\Models\Bills\Bill;
use App\Models\Plans\Plan;
use App\Models\Plans\PlanStatus;
use App\Models\Users\User;
use App\Models\Plans\PlanUserFlow;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\PlanUserFlowTransformer;


class PlanUserFlow extends Model
{
    public $transformer = PlanUserFlowTransformer::class;

    protected $table = 'plan_user_flows';
    protected $dates = ['deleted_at','start_date', 'finish_date'];
    protected $fillable = ['start_date', 'finish_date', 'amount',
    'counter', 'plan_status_id', 'discount_id', 'plan_id', 'user_id'];


    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

    public function planStatus()
    {
        return $this->belongsTo(PlanStatus::class,'plan_status_id');
    }
}

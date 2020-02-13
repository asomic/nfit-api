<?php

namespace App\Models\Plans;

use App\Models\Plans\PlanUser;
use Illuminate\Database\Eloquent\Model;

class PlanIncomeSummary extends Model
{
    protected $fillable = ['plan_id', 'amount', 'month', 'year'];
    protected $table = 'plan_income_summaries';

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}

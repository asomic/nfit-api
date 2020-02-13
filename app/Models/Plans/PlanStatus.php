<?php

namespace App\Models\Plans;

use App\Models\Plans\Plan;
use Illuminate\Database\Eloquent\Model;

class PlanStatus extends Model
{
  protected $table = 'plan_status';

  public function plans()
  {
      return $this->hasMany(Plan::class);
  }
}

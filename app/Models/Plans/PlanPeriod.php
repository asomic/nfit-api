<?php

namespace App\Models\Plans;

use App\Models\Plans\Plan;
use Illuminate\Database\Eloquent\Model;

/**
 * [PlanPeriod description]
 */
class PlanPeriod extends Model
{
  protected $fillable = ['period', 'period_number'];

  /**
   * [plans description]
   * @return [type] [description]
   */
  public function plans()
  {
      return $this->hasMany(Plan::class);
  }
}

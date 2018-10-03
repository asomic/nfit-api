<?php

namespace App\Models\Exercises;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clases\ReservationStatisticStage;

/**
 * [Statistic description]
 */
class Statistic extends Model
{
    protected $fillable = ['statistic'];
  /**
   * [reservation_statistic_stages description]
   * @method reservation_statistic_stages
   * @return [model]                       [description]
   */
  public function reservation_statistic_stages()
  {
    return $this->hasMany(ReservationStatisticStage::class);
  }

}

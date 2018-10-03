<?php

namespace App\Models\Clases;

use App\Models\Exercises\Statistic;
use App\Models\Exercises\ExerciseStage;
use Illuminate\Database\Eloquent\Model;
use App\Models\Clases\Reservation;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * [ReservationStatisticStage description]
 */
class ReservationStatisticStage extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];
  protected $fillable = [
    'statistic_id',
    'reservation_id',
    'stage_exercise_id',
    'weight',
    'time',
    'round',
    'repetitions',
  ];

  /**
   * [stage description]
   * @method stage
   * @return [type] [description]
   */
  public function excercise_stage()
  {
    return $this->belongsTo(ExerciseStage::class);
  }

  /**
   * [reservation description]
   * @method reservation
   * @return [type]      [description]
   */
  public function reservation()
  {
    return $this->belongsTo(Reservation::class);
  }

  /**
  * [statistic description]
  * @method statistic
  * @return [type]    [description]
  */
  public function statistic()
  {
    return $this->belongsTo(Statistic::class);
  }

}

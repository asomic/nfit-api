<?php

namespace App\Models\Exercises;

use App\Models\Exercises\Stage;
use App\Models\Exercises\Exercise;
use Illuminate\Database\Eloquent\Model;
use App\Models\Clases\ReservationStatisticStage;

/**
 * [ExerciseStage description]
 */
class ExerciseStage extends Model
{
  /**
   * [reservation_statistic_stages description]
   * @method reservation_statistic_stages
   * @return [type]  [description]
   */
  public function reservation_statistic_stages()
  {
    return $this->hasMany(ReservationStatisticStage::class);
  }

  /**
	 * [stage relation to this model]
	 * @return [model] [return stage model]
	 */
	public function stage()
	{
		return $this->belongsTo(Stage::class);
	}

	/**
	 * [exercise relation to this model]
	 * @return [model] [return exercise model]
	 */
	public function exercise()
	{
		return $this->belongsTo(Exercise::class);
	}
}

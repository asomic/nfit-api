<?php

namespace App\Models\Clases;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Clases\ReservationStatisticStage;
use App\Models\Users\User;

/**
 * [Reservation description]
 */
class Reservation extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];
  protected $fillable = ['clase_id', 'reservation_status_id', 'user_id'];

  /**
   * [reservation_statistic_stages description]
   * @method reservation_statistic_stages
   * @return [type]                       [description]
   */
  public function reservation_statistic_stages()
  {
    return $this->hasMany(ReservationStatisticStage::class);
  }

  /**
   * [user description]
   * @return [type] [description]
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }

}

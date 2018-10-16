<?php

namespace App\Models\Clases;

use App\Models\Users\User;
use App\Models\Clases\Clase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Clases\ReservationStatisticStage;

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
     * @return [model]                       [description]
     */
    public function reservation_statistic_stages()
    {
      return $this->hasMany(ReservationStatisticStage::class);
    }

    /**
     * [user description]
     * @return [model] [description]
     */
    public function user()
    {
      return $this->belongsTo(User::class);
    }

    /**
     * [clase description]
     * @return [model] [description]
     */
    public function clase()
    {
      return $this->belongsTo(Clase::class);
    }
}

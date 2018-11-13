<?php

namespace App\Models\Clases;

use App\Models\Users\User;
use App\Models\Clases\Clase;
use Illuminate\Database\Eloquent\Model;
use App\Transformers\ReservationTransformer;

use App\Models\Clases\ReservationStatisticStage;
use App\Models\Clases\ReservationStatus;

/**
 * [Reservation description]
 */
class Reservation extends Model
{


  protected $dates = ['deleted_at'];
  protected $fillable = ['clase_id', 'reservation_status_id', 'user_id', 'details'];

  public $transformer = ReservationTransformer::class;

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

    public function status()
    {
      return $this->belongsTo(ReservationStatus::class,'reservation_status_id');
    }
}

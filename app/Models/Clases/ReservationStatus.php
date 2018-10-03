<?php

namespace App\Models\Clases;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clases\Reservation;

/**
 * [ReservationStatus description]
 */
class ReservationStatus extends Model
{
    protected $fillable = ['reservation_status'];

    /**
     * [reservations description]
     * @method reservations
     * @return [type]       [description]
     */
    public function reservations()
    {
      return $this->hasMany(Reservation::class);
    }
}

<?php

namespace App\Models\Clases;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clases\Reservation;

/**
 * [ReservationStatus description]
 */
class ReservationStatus extends Model
{
    const PENDIENTE = 1;
    const CONFIRMADA = 2;
    const CONSUMIDA = 3;
    const PERDIDA = 4;

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

<?php

namespace App\Http\Controllers\Clases;

use Auth;
use Carbon\Carbon;
use App\Models\Clases\Clase;
use Illuminate\Http\Request;
use App\Models\Plans\PlanUser;
use App\Models\Clases\Reservation;
use App\Http\Controllers\ApiController;

class ReservationController extends ApiController
{
  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Clases\Reservation  $clase
   * @return \Illuminate\Http\Response
   */
    public function show(Reservation $reservation)
    {
        return $this->showOne($reservation, 200);
    }

    public function historic()
    {
        $reservations = Auth::user()->historic_reservations;

        return $this->showAll($reservations);
    }

    public function coming()
    {
        $reservations = Auth::user()->coming_reservations;

        return $this->showAll($reservations);
    }

    public function details(Reservation $reservation, Request $request)
    {
        $reservation->details = $request->details;

        if ($reservation->save()) {
          return response()->json('nota guardada reserva :'.$reservation->detail, 200);
        } else {
          return response()->json('error al guardar nota', 401);
        }
    }
}

<?php

namespace App\Http\Controllers\Clases;

use Auth;
use App\Models\Clases\Clase;
use Illuminate\Http\Request;
use App\Models\Plans\PlanUser;
use App\Models\Clases\Reservation;
use App\Http\Controllers\ApiController;

class ClaseController extends ApiController
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Clases\Clase  $clase
     * @return \Illuminate\Http\Response
     */
    public function show(Clase $clase)
    {
        return $this->showOne($clase, 200);
    }

    /**
     * [reserve description]
     * @param  Request $request [description]
     * @return [instance]           [description]
     */
    public function reserve(Request $request, Clase $clase)
    {
        $response = $this->hasReserve($clase);
        if ($response != null) {
            return $this->errorResponse($response, 400);
        }

        $planuser = PlanUser::where('plan_status_id', 1)->where('user_id', Auth::id())->first();
        $responseTwo = $this->hasTwelvePlan($planuser);
        if ($responseTwo != null) {
            return $this->errorResponse($responseTwo, 400);
        }else{
            $campos['user_id'] = Auth::id();
            $campos['clase_id'] = $clase->id;
            $campos['reservation_status_id'] = 1;
            $planuser->counter = $planuser->counter + 1;
            $planuser->save();
            $reservation = Reservation::create($campos);

            return $this->showOne($reservation->clase, 200);
        }
    }

    private function hasReserve($clase)
    {
        $response = '';
        $clases = Clase::where('date', $clase->date)->get();
        foreach ($clases as $clase) {
            $reservations = Reservation::where('user_id', Auth::id())->where('clase_id', $clase->id)->get();
            if (count($reservations) != 0) {
                $response = 'ya tiene clase tomada este día';
            }
        }
        return $response;
    }

    private function hasTwelvePlan($planuser)
    {
        $responseTwo = null;
        if ($planuser->plan_id == 5 && $planuser->counter >= 12) {
            $responseTwo = 'El plan de 12 clases mensual no le permite tomar mas clases';
        }
        elseif ($planuser->plan_id == 6 && $planuser->counter >= 12) {
            $responseTwo = 'El plan de 12 clases trimestral no le permite tomar mas clases';
        }
        elseif ($planuser->plan_id == 7 && $planuser->counter >= 12) {
            $responseTwo = 'El plan de 12 clases semestral no le permite tomar mas clases';
        }
        elseif ($planuser->plan_id == 8 && $planuser->counter >= 12) {
            $responseTwo = 'El plan de 12 clases anual no le permite tomar mas clases';
        }

        return $responseTwo;
    }

    public function update(Request $request, Clase $clase)
    {
        //
    }

    public function destroy(Clase $clase)
    {
        //
    }

}

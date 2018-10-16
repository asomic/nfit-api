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
        $planuser = PlanUser::where('plan_status_id', 1)->where('user_id', Auth::id())->get();
        if (!$planuser->plan_id == 5 && !$planuser->counter < 12) {
            return $this->errorResponse('El plan de 12 clases mensual no le permite tomar mas clases', 400);
        }elseif (!$planuser->plan_id == 6 && !$planuser->counter < 12) {
            return $this->errorResponse('El plan de 12 clases trimestral no le permite tomar mas clases', 400);
        }elseif (!$planuser->plan_id == 7 && !$planuser->counter < 12) {
            return $this->errorResponse('El plan de 12 clases semestral no le permite tomar mas clases', 400);
        }elseif (!$planuser->plan_id == 8 && !$planuser->counter < 12) {
            return $this->errorResponse('El plan de 12 clases anual no le permite tomar mas clases', 400);
        }
        if ($planuser->plan_id == 5 && $planuser->counter < 12) {
            $campos['user_id'] = Auth::id();
            $campos['clase_id'] = $clase->id;
            $campos['reservation_status_id'] = 1;
            $planuser->counter = $planuser->counter + 1;
            $reservation = Reservation::create($campos);
            return $this->showOne($reservation->clase, 200);
        }else {
            
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Clases\Clase  $clase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Clase $clase)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Clases\Clase  $clase
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clase $clase)
    {
        //
    }


}

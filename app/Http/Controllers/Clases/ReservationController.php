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
        if($reservation->save()){
          return response()->json('nota guardada', 200);
        } else {
          return response()->json('error al guardar nota', 401);
        }

    }

    // public function week()
    // {
    //     $clases = Clase::Where('date','>=',today())->get();
    //     return $this->showAll($clases);
    // }
    //
    // public function historic()
    // {
    //     $clases = Auth::user()->clases->where('date','<=',today());
    //
    //     return $this->showAll($clases);
    // }
    //
    // public function reserved()
    // {
    //     $clases = Auth::user()->clases->where('date','>=',today());
    //
    //     return $this->showAll($clases);
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Clases\Clase  $clase
     * @return \Illuminate\Http\Response
     */
    // public function show(Clase $clase)
    // {
    //     return $this->showOne($clase, 200);
    // }

    /**
     * [reserve description]
     * @param  Request $request [description]
     * @return [instance]           [description]
     */
    // public function reserve(Request $request, Clase $clase)
    // {
    //     $planuser = PlanUser::where('plan_status_id', 1)->where('user_id', Auth::id())->first();
    //     if ($planuser == null) {
    //         return $this->errorResponse('No puede reservar, no tiene ningun plan activo', 400);
    //     }
    //
    //     $response = $this->hasReserve($clase);
    //     if ($response != null) {
    //         return $this->errorResponse($response, 400);
    //     }
    //
    //     $responseTwo = $this->hasTwelvePlan($planuser);
    //     if ($responseTwo != null) {
    //         return $this->errorResponse($responseTwo, 400);
    //     }
    //
    //     if ($clase->date < toDay()->format('Y-m-d')) {
    //         return $this->errorResponse('No puede tomar una clase de un dia anterior a hoy', 400);
    //     }
    //     elseif ($clase->date > toDay()->format('Y-m-d')) {
    //         $campos['user_id'] = Auth::id();
    //         $campos['clase_id'] = $clase->id;
    //         $campos['reservation_status_id'] = 1;
    //         $planuser->update(['counter' => $planuser->counter + 1]);
    //         $reservation = Reservation::create($campos);
    //
    //         return $this->showOne($reservation->clase, 200);
    //     }
    //     else {
    //         $class_hour = Carbon::parse($clase->start_at);
    //         $diff_mns = $class_hour->diffInMinutes(now()->format('H:i'));
    //         // ??? SERA NECESARIO PONER LAS RESPUESTAS PERSONALIZADAS ???
    //         if ((now()->format('H:i') > $class_hour) || (diff_mns < 40)) {
    //             return $this->errorResponse('Ya no se puede tomar esta clase', 400);
    //         }else{
    //             $campos['user_id'] = Auth::id();
    //             $campos['clase_id'] = $clase->id;
    //             $campos['reservation_status_id'] = 1;
    //             $planuser->update(['counter' => $planuser->counter + 1]);
    //             $reservation = Reservation::create($campos);
    //
    //             return $this->showOne($reservation->clase, 200);
    //         }
    //     }
    // }
    //
    // private function hasReserve($clase)
    // {
    //     $response = '';
    //     $clases = Clase::where('date', $clase->date)->get();
    //     foreach ($clases as $clase) {
    //         $reservations = Reservation::where('user_id', Auth::id())->where('clase_id', $clase->id)->get();
    //         if (count($reservations) != 0) {
    //             $response = 'Ya tiene clase tomada este dia';
    //         }
    //     }
    //     return $response;
    // }
    //
    // private function hasTwelvePlan($planuser)
    // {
    //     $responseTwo = null;
    //     if ($planuser->plan_id == 5 && $planuser->counter >= 12) {
    //         $responseTwo = 'No puede reservar, ya ha ocupado o reservado sus 12 clases del plan 12 clases mensual';
    //     }
    //     elseif ($planuser->plan_id == 6 && $planuser->counter >= 12) {
    //         $responseTwo = 'El plan de 12 clases trimestral no le permite tomar mas clases';
    //     }
    //     elseif ($planuser->plan_id == 7 && $planuser->counter >= 12) {
    //         $responseTwo = 'El plan de 12 clases semestral no le permite tomar mas clases';
    //     }
    //     elseif ($planuser->plan_id == 8 && $planuser->counter >= 12) {
    //         $responseTwo = 'El plan de 12 clases anual no le permite tomar mas clases';
    //     }
    //
    //     return $responseTwo;
    // }
    //
    //
    // public function remove(Request $request, Clase $clase)
    // {
    //     $reservation = Reservation::where('clase_id', $clase->id)->where('user_id', Auth::id())->first();
    //     if ($reservation == null) {
    //         return $this->errorResponse('No puede votar una clase en la que no esta', 403);
    //     }
    //     $planuser = PlanUser::where('plan_status_id', 1)->where('user_id', Auth::id())->first();
    //
    //     if ($clase->date < toDay()->format('Y-m-d')) {
    //         return $this->errorResponse('No puede votar una clase de un día anterior a hoy', 403);
    //     }
    //     elseif ($clase->date > toDay()->format('Y-m-d')) {
    //         if ($reservation->delete()) {
    //                 $planuser->counter = $planuser->counter - 1;
    //                 $planuser->save();
    //                 return $this->showOne($clase, 200);
    //             }
    //     }
    //     else {
    //         $class_hour = Carbon::parse($clase->start_at);
    //         if ($class_hour->diffInMinutes(now()->format('H:i')) < 40) {
    //         return $this->errorResponse('Ya no puede votar la clase', 400);
    //         }else{
    //             if ($reservation->delete()) {
    //                 $planuser->counter = $planuser->counter - 1;
    //                 $planuser->save();
    //                 return $this->showOne($clase, 200);
    //             }
    //         }
    //     }
    // }



}

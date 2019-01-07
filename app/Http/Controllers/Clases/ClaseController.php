<?php

namespace App\Http\Controllers\Clases;

use Auth;
use Carbon\Carbon;
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

    public function index()
    {
        $clases = Clase::all();
        return $this->showAll($clases);
    }

    public function week()
    {
        $week = [];
        $today = carbon::today();
        $date = $today;
        $day = [];
        for ($i=0; $i < 7; $i++) {
            $dow = $date->dayOfWeek;
            if($i==0){
                $isToday = (bool)true;
            }else{
                $isToday = (bool)false;
            }

            $reservation_today = $this->hasClase($date);
            $can_reserve = $this->canReserve($date);
            $day = ["date" => (string)$date->toDateString(),
                    "day"=> (string)$date->format('d'),
                    "dayName"=> (string)ucfirst($date->formatLocalized('%A' )),
                    "today"=> $isToday,
                    "hasClases"=> $reservation_today,
                    "canReserve"=> $can_reserve,
                   ];

            $week = array_add($week, $dow, $day);

            $date = $date->addDay();
      }


      array_forget($week, '0');


      return response()->json(['data' => $week], 200);

    }

    public function historic()
    {
        $clases = Auth::user()->clases->where('date','<=',today())
                                      ->where(now()->format('H:i'), '<', 'finish_at');

        return $this->showAll($clases);
    }

    public function coming()
    {
        $clases = Auth::user()->clases->where('date','>=',today());
                                      // ->where(now()->format('H:i'), '>=', 'finish_at');
        return $this->showAll($clases);
    }

    public function users(Clase $clase)
    {
        $users = $clase->users;
        //dd($users);
        return $this->showAll($users, 200);
    }

    public function reservations(Clase $clase)
    {
        $reservations = $clase->reservations;
        //dd($users);
        return $this->showAll($reservations, 200);
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
      $planuser = PlanUser::where('plan_status_id', 1)->where('user_id', Auth::id())->first();
      $reservation = new Reservation;
      $reservation->user_id = Auth::user()->id;
      $reservation->clase_id = $clase->id;
      $reservation->by_god = false;
      $reservation->reservation_status_id = 1 ;
      if($reservation->save())
      {
        $planuser->counter = $planuser->counter - 1;
        $planuser->save();
        return $this->showOne($reservation->clase, 200);
      } else {
        return $this->errorResponse('No se puede tomar la clase', 400);
      }
        // $planusers = PlanUser::whereIn('plan_status_id', [1,3])->where('user_id', Auth::id())->get();
        //
        // foreach ($planusers as $planuser) {
        //     foreach ($planuser->plan_user_periods as $pup) {
        //         if ($date_class->between(Carbon::parse($pup->start_date), Carbon::parse($pup->finish_date))) {
        //             $period_plan = $pup;
        //         }
        //     }
        // }
        //
        // if ($clase->date > toDay()->format('Y-m-d')) {
        //     $campos['user_id'] = Auth::id();
        //     $campos['clase_id'] = $clase->id;
        //     $campos['reservation_status_id'] = 1;
        //     $period_plan->update(['counter' => $period_plan->counter - 1]);
        //     $reservation = Reservation::create($campos);
        //
        //     return $this->showOne($reservation->clase, 200);
        // }
        // else {
        //     $class_hour = Carbon::parse($clase->start_at);
        //     $diff_mns = $class_hour->diffInMinutes(now()->format('H:i'));
        //     // ??? SERA NECESARIO PONER LAS RESPUESTAS PERSONALIZADAS ???
        //     if ((now()->format('H:i') > $class_hour) || (diff_mns < 40)) {
        //         return $this->errorResponse('Ya no se puede tomar esta clase', 400);
        //     }else{
        //         $campos['user_id'] = Auth::id();
        //         $campos['clase_id'] = $clase->id;
        //         $campos['reservation_status_id'] = 1;
        //         $period_plan->update(['counter' => $period_plan->counter - 1]);
        //         $reservation = Reservation::create($campos);
        //
        //         return $this->showOne($reservation->clase, 200);
        //     }
        // }


    }

    public function confirm(Request $request, Clase $clase)
    {
      $reservation = Reservation::where('clase_id', $clase->id)->where('user_id', Auth::id())->first();
      if ($reservation == null) {
          return $this->errorResponse('No puede confirmar una clase en la que no esta', 403);
      }
      $reservation->reservation_status_id = 2;
      $reservation->save();
      return $this->showOne($reservation->clase, 200);
    }

    private function hasReserve($clase)
    {
        $response = '';
        $clases = Clase::where('date', $clase->date)->get();
        foreach ($clases as $clase) {
            $reservations = Reservation::where('user_id', Auth::id())->where('clase_id', $clase->id)->get();
            if (count($reservations) != 0) {
                $response = 'Ya tiene clase tomada este dia';
            }
        }
        return $response;
    }

    private function hasClase($date)
    {
        $response = false;
        $clases = Clase::where('date', $date)->get();
        foreach ($clases as $clase) {
            $reservations = Reservation::where('user_id', Auth::id())->where('clase_id', $clase->id)->get();
            if (count($reservations) != 0) {
                $response = true;
            }
        }
        return $response;
    }

    public function canReserve($date)
    {
        $response = false;
        $planusers = PlanUser::whereIn('plan_status_id', [1,3])->where('user_id', Auth::id())->get();
        foreach ($planusers as $planuser) {

          if ($date->between(Carbon::parse($planuser->start_date), Carbon::parse($planuser->finish_date))) {
              if ($planuser->counter > 0) {
                  $response = true;
              }
          }

        }
        return $response;
    }

    public function remove(Request $request, Clase $clase)
    {
        $reservation = Reservation::where('clase_id', $clase->id)->where('user_id', Auth::id())->first();
        if ($reservation == null) {
            return $this->errorResponse('No puede votar una clase en la que no esta', 403);
        }
        $planuser = PlanUser::where('plan_status_id', 1)->where('user_id', Auth::id())->first();

        if ($clase->date < toDay()->format('Y-m-d')) {
            return $this->errorResponse('No puede votar una clase de un día anterior a hoy', 403);
        }
        elseif ($clase->date > toDay()->format('Y-m-d')) {
            if ($reservation->delete()) {
                    $planuser->counter = $planuser->counter + 1;
                    $planuser->save();
                    return $this->showOne($clase, 200);
                }
        }
        else {
            $class_hour = Carbon::parse($clase->start_at);
            if ($class_hour->diffInMinutes(now()->format('H:i')) < 40) {
            return $this->errorResponse('Ya no puede votar la clase', 400);
            }else{
                if ($reservation->delete()) {
                    $planuser->counter = $planuser->counter - 1;
                    $planuser->save();
                    return $this->showOne($clase, 200);
                }
            }
        }
    }


}
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

    //     return $responseTwo;
    // }

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
     * Get all clases
     * 
     * @return Clase
     */
    public function index()
    {
        $clases = Clase::all();

        return $this->showAll($clases);
    }

    /**
     * Get all days of the week
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function week()
    {
        $week = [];
        $today = today();
        $date = $today;
        $day = [];
        for ($i=0; $i < 7; $i++) {
            $dow = $date->dayOfWeek;
            $isToday = $i == 0 ? true : false;

            $reservation_today = $this->hasClase($date);
            $can_reserve = $this->canReserve($date);
            $day = [
                "date" => (string)$date->toDateString(),
                "day" => (string)$date->format('d'),
                "dayName" => (string)ucfirst($date->formatLocalized('%A' )),
                "today" => $isToday,
                "hasClases" => $reservation_today,
                "canReserve" => $can_reserve,
            ];

            $week = array_add($week, $dow, $day);

            $date = $date->addDay();
        }

        array_forget($week, '0');

        return response()->json(['data' => $week], 200);
    }

    /**
     * [historic description]
     * 
     * @return [type] [description]
     */
    public function historic()
    {
        $clases = Auth::user()->clases->where('date','<',today())
                                      ->where(now()->format('H:i'), '<', 'finish_at');

        return $this->showAll($clases);
    }

    /**
     * [coming description]
     * 
     * @return [type] [description]
     */
    public function coming()
    {
        $clases = Auth::user()->clases->where('date', '>=', today());
                                      // ->where(now()->format('H:i'), '>=', 'finish_at');
        return $this->showAll($clases);
    }

    /**
     * [users description]
     * 
     * @param  Clase  $clase [description]
     * @return [type]        [description]
     */
    public function users(Clase $clase)
    {
        $users = $clase->users;

        return $this->showAll($users, 200);
    }

    /**
     * [reservations description]
     * 
     * @param  Clase  $clase [description]
     * @return [type]        [description]
     */
    public function reservations(Clase $clase)
    {
        $reservations = $clase->reservations;

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
        $planuser = PlanUser::where('plan_status_id', 1)
                            ->where('user_id', Auth::id())
                            ->first();
      $reservation = new Reservation;
      $reservation->user_id = Auth::user()->id;
      $reservation->clase_id = $clase->id;
      $reservation->by_god = false;
      $reservation->reservation_status_id = 1 ;
      if($planuser){
        $reservation->plan_user_id = $planuser->id;
        if (!in_array($planuser->plan->id, $clase->block->plans->pluck('id')->toArray())) {
          return $this->errorResponse('Tu plan no te deja tomar esta clase', 400);
        }
      }
      if($reservation->save())
      {
        $planuser->counter = $planuser->counter - 1;
        $planuser->save();
        return $this->showOne($reservation->clase, 200);
      } else {
        return $this->errorResponse('No se puede tomar la clase', 400);
      }

    }

    /**
     * [confirm description]
     * 
     * @param  Request $request [description]
     * @param  Clase   $clase   [description]
     * @return [type]           [description]
     */
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

    /**
     * [hasReserve description]
     * 
     * @param  [type]  $clase [description]
     * @return boolean        [description]
     */
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

    /**
     * [hasClase description]
     * 
     * @param  DateTimeZone|string|null  $date 
     * @return boolean       [description]
     */
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

    /**
     * Check for the user not exceed the limit of day classes
     * 
     * @param  \DateTimeZone|string|null $date [description]
     * @return boolean
     */
    public function canReserve($date)
    {
        $response = false;
        $planusers = PlanUser::whereIn('plan_status_id', [1,3])
                             ->where('user_id', Auth::id())
                             ->get(['id', 'start_date', 'finish_date', 'counter']);

        foreach ($planusers as $planuser) {
            if ($date->between(Carbon::parse($planuser->start_date), Carbon::parse($planuser->finish_date))) {
                if ($planuser->counter > 0) {
                    $response = true;
                }
            }
        }

        return $response;
    }

    /**
     * [remove description]
     * 
     * @param  Request $request [description]
     * @param  Clase   $clase   [description]
     * @return [type]           [description]
     */
    public function remove(Request $request, Clase $clase)
    {
        $reservation = Reservation::where('clase_id', $clase->id)->where('user_id', Auth::id())->first();
        
        if ($reservation == null) {
            return $this->errorResponse('No puede votar una clase en la que no esta', 403);
        }
        $planuser = PlanUser::where('plan_status_id', 1)->where('user_id', Auth::id())->first();

        if ($clase->date < toDay()->format('Y-m-d')) {
            return $this->errorResponse('No puede votar una clase de un día anterior a hoy', 403);
        } elseif ($clase->date > toDay()->format('Y-m-d')) {
            if ($reservation->delete()) {
                    $planuser->counter = $planuser->counter + 1;
                    $planuser->save();
                    return $this->showOne($clase, 200);
                }
        } else {
            $class_hour = Carbon::parse($clase->start_at);
            if ($class_hour->diffInMinutes(now()->format('H:i')) < 40) {
                return $this->errorResponse('Ya no puede votar la clase', 400);
            } else {
                if ($reservation->delete()) {
                    $planuser->counter = $planuser->counter - 1;
                    $planuser->save();
                    return $this->showOne($clase, 200);
                }
            }
        }
    }
}

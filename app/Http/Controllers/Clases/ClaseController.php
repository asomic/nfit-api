<?php

namespace App\Http\Controllers\Clases;

use Auth;
use Carbon\Carbon;
use App\Models\Clases\Clase;
use Illuminate\Http\Request;
use App\Models\Plans\PlanUser;
use App\Models\Clases\ClaseType;
use App\Models\Clases\Reservation;
use App\Http\Controllers\ApiController;
use App\Models\Clases\ReservationStatus;

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
     *  Undocumented function
     *
     *  @param   Request  $request
     *
     *  @return  void
     */
    public function index(Request $request)
    {
        $request->request->add(['sort_by_asc' => 'start','per_page' => 15]); //por ahora despues este request debe estar en la app cliente

        $clases = Clase::all();

        return $this->showAll($clases);
    }

    public function week(ClaseType $clase_type)
    {
        $week = [];
        $today = carbon::today();
        $date = $today;
        $day = [];
        for ($i = 0; $i <= 7; $i++) {
            $dow = $date->dayOfWeek;
            if ($i == 0) {
                $isToday = (bool) true;
            } else {
                $isToday = (bool) false;
            }

            $day_has_clases = $this->dayHasClases($date, $clase_type);
            //$can_reserve = $this->canReserve($date);
            $day = ["date" => (string) $date->toDateString(),
                "day" => (string) $date->format('d'),
                // "dayName" => (string) ucfirst($date->formatLocalized('%A')),
                'dayName' => (string) ucfirst(strftime('%A', $date->timestamp)),
                "today" => $isToday,
                "dayHasClases" => $day_has_clases,
                "canReserve" => (bool) true,
                "hasReserve" => (bool) false,
                // "canReserve" => $can_reserve,
            ];

            $week = array_add($week, $dow, $day);

            $date = $date->addDay();
        }
        $week[7] = $week[0];
        array_forget($week, '0');

        return response()->json(['data' => $week], 200);

    }

    public function historic()
    {
        $clases = Auth::user()->clases()->where('date', '<=', today())->get();
        return $this->showAll($clases);
    }

    public function types()
    {
        $types = Clasetype::all();

        return  $this->showAll($types);
    }

    public function coming()
    {
        $clases = Auth::user()->clases->where('date', '>=', today());

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
        $planuser = PlanUser::where('start_date', '<=', Carbon::parse($clase->date))
            ->where('finish_date', '>=', Carbon::parse($clase->date))
            ->where('user_id', Auth::id())
            ->whereIn('plan_status_id', [1, 3])
            ->first();

        if (!$planuser) {
            return $this->errorResponse('No tienes un plan que te permita tomar esta clase', 403);
        }

        if (count($clase->users) >= $clase->quota) {
            return $this->errorResponse('No puedes reservar, la clase esta llena.', 403);
        }

        $hasReserve = $this->hasReserve($clase);

        if ($hasReserve) {
            return $this->errorResponse($hasReserve, 403);
        }

        $reservation = new Reservation;
        $reservation->user_id = Auth::user()->id;
        $reservation->clase_id = $clase->id;
        $reservation->by_user = Auth::user()->id;
        $reservation->reservation_status_id = 1;
        $reservation->plan_user_id = $planuser->id;

        if (!in_array($planuser->plan->id, $clase->block->plans->pluck('id')->toArray())) {
            return $this->errorResponse('Tu plan no te deja tomar esta clase', 403);
        }

        if ($reservation->save()) {
            $planuser->counter = $planuser->counter - 1;
            $planuser->save();
            return $this->showOne($reservation->clase, 201);
        } else {
            return $this->errorResponse('No se pudo tomar la clase', 400);
        }

    }

    public function confirm(Request $request, Clase $clase)
    {
        $reservation = Reservation::where('clase_id', $clase->id)->where('user_id', Auth::id())->first();

        if (!$reservation) {
            return $this->errorResponse('No puede confirmar una clase en la que no esta', 403);
        }

        $start = $clase->start_at;
        $dateTimeStringStart = $clase->date->format('Y-m-d') . " " . $start;
        $timezone = auth()->user()->timezone ?? 'America/Santiago';
        $dateTimeStart = Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeStringStart, $timezone);

        if (now($timezone) > $dateTimeStart) {
            return $this->errorResponse('No puedes reservar, la clase ya comenzó.', 403);
        }

        // if (count($clase->users) >= $clase->quota) {
        //     return $this->errorResponse('No puedes reservar, la clase esta llena.', 403);
        // }

        $reservation->reservation_status_id = ReservationStatus::CONFIRMADA;
        $reservation->save();
        return $this->showOne($reservation->clase, 201);
    }

    public function directConfirm(Request $request, Clase $clase)
    {
        $planuser = PlanUser::where('start_date', '<=', Carbon::parse($clase->date))
            ->where('finish_date', '>=', Carbon::parse($clase->date))
            ->where('user_id', Auth::id())
            ->whereIn('plan_status_id', [1, 3])
            ->first();
        if (!$planuser) {
            return $this->errorResponse('No tienes un plan que te permita tomar esta clase', 403);
        }

        if (count($clase->users) >= $clase->quota) {
            return $this->errorResponse('No puedes reservar, la clase esta llena.', 403);
        }

        $timezone = auth()->user()->timezone ?? 'America/Santiago';
        $start = $clase->start_at;
        $dateTimeStringStart = $clase->date->format('Y-m-d')." ".$start;
        $dateTimeStart = Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeStringStart, $timezone);

        if (now($timezone)->copy() > $dateTimeStart) {
            return $this->errorResponse('No puedes reservar, la clase ya comenzó.', 403);
        }

        $hasReserve = $this->hasReserve($clase);
        if ($hasReserve) {
            return $this->errorResponse($hasReserve, 403);
        }
        $reservation = new Reservation;
        $reservation->user_id = Auth::user()->id;
        $reservation->clase_id = $clase->id;
        $reservation->by_user = Auth::user()->id;
        $reservation->reservation_status_id = 2;
        $reservation->plan_user_id = $planuser->id;

        if (!in_array($planuser->plan->id, $clase->block->plans->pluck('id')->toArray())) {
            return $this->errorResponse('Tu plan no te deja tomar esta clase', 403);
        }

        if ($reservation->save()) {
            $planuser->counter = $planuser->counter - 1;
            $planuser->save();
            return $this->showOne($reservation->clase, 201);
        } else {
            return $this->errorResponse('No se pudo tomar la clase', 400);
        }

    }

    private function hasReserve($clase)
    {
        $response = '';
        $clases = Clase::where('date', $clase->date)->where('clase_type_id', $clase->clase_type_id)->get();
        foreach ($clases as $clase) {
            $reservations = Reservation::where('user_id', Auth::id())->where('clase_id', $clase->id)->get();
            if (count($reservations) != 0) {
                $response = 'Ya tiene una clase tomada este dia';
            }
        }
        return $response;
    }


    private function hasClase($date)
    {
        $response = false;
        $clases = Clases::where('date', $date)->get();
        foreach ($clases as $clase) {
            $reservations = Reservation::where('user_id', Auth::id())->where('clase_id', $clase->id)->get();
            if (count($reservations) != 0) {
                $response = true;
            }
        }
        return $response;
    }

    private function dayHasClases($date, ClaseType $clase_type )
    {
        $response = false;
        if(!$clase_type->exists){
            $clase_type = ClaseType::first();
        }


        $clases = $clase_type->clases()->whereDate('date', $date->format('Y-m-d'))->first();
        if($clases){
            $response = true;
        }

        // if(!$response){
        //     dd($date,$clase_type,$response, $clases);
        // }


        return $response;
    }

    public function canReserve($date)
    {
        $response = false;
        $planusers = PlanUser::whereIn('plan_status_id', [1, 3])->where('user_id', Auth::id())->get();
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
        $planUser = Auth::user()->plan_users()->where('start_date', '<=', $clase->date)->where('finish_date', '>=', $clase->date) ->whereIn('plan_status_id', [1, 3])->first();

        if(!$planUser){
            return $this->errorResponse('no existe el plan', 403);
        }

        if ($clase->date < toDay()->format('Y-m-d')) {
            return $this->errorResponse('No puede votar una clase de un día anterior a hoy', 403);
        } elseif ($clase->date > toDay()->format('Y-m-d')) {
            if ($reservation->delete()) {
                $planUser->counter = $planUser->counter + 1;
                $planUser->save();
                return $this->showOne($clase, 200);
            }
        } else {
            $class_hour = Carbon::parse($clase->start_at);
            if ($class_hour->diffInMinutes(now()->format('H:i')) < 40) {
                return $this->errorResponse('Ya no puede votar la clase', 400);
            } else {
                if ($reservation->delete()) {
                    $planUser->counter = $planUser->counter - 1;
                    $planUser->save();
                    return $this->showOne($clase, 200);
                }
            }
        }
    }

    // por mientras
    public function getZoom(Clase $clase)
    {
        $timezone = auth()->user()->timezone ?? 'America/Santiago';

        $can_zoom = false;
        $zoom_link = null;
        $now = Carbon::now($timezone)->copy();

        $stringStart = $clase->date->format('Y-m-d') . " " . $clase->start_at;
        $start = Carbon::createFromFormat('Y-m-d H:i:s', $stringStart, $timezone)->subMinutes(10);

        $stringEnd = $clase->date->format('Y-m-d') . " " . $clase->finish_at;
        $end = Carbon::createFromFormat('Y-m-d H:i:s', $stringEnd, $timezone);

        if (($clase->zoom_link !== null) &&
            $start->lte(Carbon::now($timezone)->copy())  &&
            $end->gte(Carbon::now($timezone)->copy())  &&
            $clase->authReservedThis()
        ) {
            $can_zoom = true;
            $zoom_link = $clase->zoom_link;
        }

        //test
        return response()->json([
            'now' => $now,
            'start' => $start,
            'end' => $end,
            'has' => $clase->authReservedThis(),
            'can_zoom' => $can_zoom,
            'zoom_link' => $zoom_link,
        ]);
    }
}


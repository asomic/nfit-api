<?php

namespace App\Http\Controllers\Clases;

use Carbon\Carbon;
use App\Models\Clases\Clase;
use Illuminate\Http\Request;
use App\Models\Plans\PlanUser;
use App\Models\Clases\ClaseType;
use App\Models\Plans\PlanStatus;
use App\Models\Clases\Reservation;
use App\Models\Settings\Parameter;
use App\Models\Plans\PlanUserStatus;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use App\Models\Clases\ReservationStatus;

class ClaseController extends ApiController
{
    /**
     *  Undocumented function
     *
     *  @param   Request  $request
     *
     *  @return  void
     */
    public function index(Request $request)
    {
        $request->request->add(['sort_by_asc' => 'start', 'per_page' => 15]); //por ahora despues este request debe estar en la app cliente

        $clases = Clase::all();

        return $this->showAll($clases);
    }

    /**
     *  Get the days of the week
     *
     *  @param   ClaseType  $clase_type
     *
     *  @return  json
     */
    public function week(ClaseType $clase_type)
    {
        $timezone = Auth::user()->timezone ?? 'America/Santiago';
        $week = [];
        $today = today($timezone);
        $date = $today;
        $day = [];

        for ($i = 0; $i <= 7; $i++) {
            $isToday = $i === 0 ? true : false;

            $day = [
                "date"         => (string) $date->toDateString(),
                "day"          => (string) $date->format('d'),
                'dayName'      => (string) ucfirst(strftime('%A', $date->timestamp)),
                "today"        => (bool) $isToday,
                "dayHasClases" => $this->dayHasClases($date, $clase_type),
                "canReserve"   => (bool) true,
                "hasReserve"   => (bool) false,
                // "dayName" => (string) ucfirst($date->formatLocalized('%A')),
                // "canReserve" => $can_reserve,
            ];

            $dow = $date->dayOfWeek;
            $week = array_add($week, $dow, $day);
            $date = $date->addDay();
        }

        $week[7] = $week[0];
        array_forget($week, '0');

        return response()->json(['data' => $week], 200);
    }

    /**
     *  [historic description]
     *
     *  @return  [type]  [return description]
     */
    public function historic()
    {
        $clases = Auth::user()->clases()->where('date', '<=', today())->get();

        return $this->showAll($clases);
    }

    /**
     *  [types description]
     *
     *  @return  [type]  [return description]
     */
    public function types()
    {
        $types = Clasetype::all();

        return $this->showAll($types);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function coming()
    {
        $timezone = Auth::user()->timezone ?? 'America/Santiago';

        $clases = Auth::user()->clases->where('date', '>=', today($timezone)->format('Y-m-d'));

        return $this->showAll($clases);
    }

    /**
     * [users description]
     *
     * @param   Clase  $clase  [$clase description]
     *
     * @return  [type]         [return description]
     */
    public function users(Clase $clase)
    {
        $users = $clase->users;

        return $this->showAll($users, 200);
    }

    /**
     * [reservations description]
     *
     * @param   Clase  $clase  [$clase description]
     *
     * @return  [type]         [return description]
     */
    public function reservations(Clase $clase)
    {
        $reservations = $clase->reservations;

        return $this->showAll($reservations, 200);
    }

    /**
     *  Display the specified resource.
     *
     *  @param  \App\Models\Clases\Clase   $clase
     *  
     *  @return \Illuminate\Http\Response
     */
    public function show(Clase $clase)
    {
        return $this->showOne($clase, 200);
    }

    /**
     *  [reserve description]
     * 
     *  @param   Request     $request  [description]
     *  @return  [instance]            [description]
     */
    public function reserve(Request $request, Clase $clase)
    {
        $planuser = PlanUser::where('start_date', '<=', Carbon::parse($clase->date))
                                ->where('finish_date', '>=', Carbon::parse($clase->date))
                                ->where('user_id', Auth::id())
                                ->whereIn('plan_status_id', [PlanUserStatus::ACTIVO, PlanUserStatus::PRECOMPRA])
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

        $timezone = auth()->user()->timezone ?? 'UTC';
        $dateTimeStart = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            "{$clase->date->format('Y-m-d')} {$clase->start_at}",
            $timezone
        );

        if (now($timezone) > $dateTimeStart) {
            return $this->errorResponse('No puedes reservar, la clase ya comenzó.', 403);
        }

        if (!in_array($planuser->plan->id, $clase->block->plans->pluck('id')->toArray())) {
            return $this->errorResponse('Tu plan no te deja tomar esta clase', 403);
        }

        $reservation = new Reservation;
        $reservation->user_id = Auth::id();
        $reservation->clase_id = $clase->id;
        $reservation->by_user = Auth::id();
        $reservation->reservation_status_id = ReservationStatus::PENDIENTE;
        $reservation->plan_user_id = $planuser->id;


        if ($reservation->save()) {
            $planuser->counter = $planuser->counter - 1;
            $planuser->save();
            return $this->showOne($reservation->clase, 201);
        } else {
            return $this->errorResponse('No se pudo tomar la clase', 400);
        }
    }

    /**
     *  [confirm description]
     *
     *  @param   Request  $request  [$request description]
     *  @param   Clase    $clase    [$clase description]
     *
     *  @return  [type]             [return description]
     */
    public function confirm(Request $request, Clase $clase)
    {
        $reservation = Reservation::where('clase_id', $clase->id)
                                    ->where('user_id', Auth::id())
                                    ->first();

        if (!$reservation) {
            return $this->errorResponse('No puede confirmar una clase en la que no esta', 403);
        }

        $reservation->reservation_status_id = ReservationStatus::CONFIRMADA;
        $reservation->save();
        return $this->showOne($reservation->clase, 201);
    }

    /**
     * [directConfirm description]
     *
     * @param   Request  $request  [$request description]
     * @param   Clase    $clase    [$clase description]
     *
     * @return  [type]             [return description]
     */
    public function directConfirm(Request $request, Clase $clase)
    {
        $planuser = PlanUser::where('start_date', '<=', Carbon::parse($clase->date))
                            ->where('finish_date', '>=', Carbon::parse($clase->date))
                            ->where('user_id', Auth::id())
                            ->whereIn('plan_status_id', [PlanStatus::ACTIVO, PlanStatus::PRECOMPRA])
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

    /**
     * [hasReserve description]
     *
     * @param   [type]  $clase  [$clase description]
     *
     * @return  [type]          [return description]
     */
    private function hasReserve($clase)
    {
        $response = '';
        $clases = Clase::where('date', $clase->date)
                            ->where('clase_type_id', $clase->clase_type_id)
                            ->get();

        foreach ($clases as $clase) {
            $reservations = Reservation::where('user_id', Auth::id())->where('clase_id', $clase->id)->get();
            if (count($reservations) != 0) {
                $response = 'Ya tiene una clase tomada este dia';
            }
        }

        return $response;
    }

    /**
     *  Check if in a specific day there are classes available
     *
     *  @param   [type]     $date        [$date description]
     *  @param   ClaseType  $clase_type  [$clase_type description]
     *
     *  @return  bool
     */
    private function dayHasClases($date, ClaseType $clase_type)
    {
        if (!$clase_type->exists) {
            $clase_type = ClaseType::first();
        }

        return $clase_type->clases()->whereDate('date', $date->format('Y-m-d'))->exists('id');
    }

    /**
     * [canReserve description]
     *
     * @param   [type]  $date  [$date description]
     *
     * @return  [type]         [return description]
     */
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

    /**
     * [remove description]
     *
     * @param   Request  $request  [$request description]
     * @param   Clase    $clase    [$clase description]
     *
     * @return  [type]             [return description]
     */
    public function remove(Request $request, Clase $clase)
    {
        $timezone = Auth::user()->timezone ?? 'UTC';
        $reservation = Reservation::where('clase_id', $clase->id)
                                    ->where('user_id', Auth::id())
                                    ->first();

        if (is_null($reservation)) {
            return $this->errorResponse('No puede votar una clase en la que no esta', 403);
        }

        $planUser = Auth::user()->plan_users()->where('start_date', '<=', $clase->date)
                                ->where('finish_date', '>=', $clase->date)
                                ->whereIn('plan_status_id', [PlanStatus::ACTIVO, PlanStatus::PRECOMPRA])
                                ->first();

        if (!$planUser) {
            return $this->errorResponse('no existe el plan', 403);
        }

        if ($clase->date < toDay($timezone)->format('Y-m-d')) {
            return $this->errorResponse('No puede votar una clase de un día anterior a hoy', 403);
        } elseif ($clase->date > toDay($timezone)->format('Y-m-d')) {
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
        $authTimezone = auth()->user()->timezone ?? 'America/Santiago';

        $can_zoom = false;
        $zoom_link = null;

        $start = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            "{$clase->date->format('Y-m-d')} {$clase->start_at}",
            $authTimezone
        )->subMinutes(10);
            
        $end = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            "{$clase->date->format('Y-m-d')} {$clase->finish_at}",
            $authTimezone
        );

        if (($clase->zoom_link !== null) &&
            $start->lte(now($authTimezone)->copy())  &&
            $end->gte(now($authTimezone)->copy())  &&
            $clase->authReservedThis()
        ) {
            $can_zoom = true;
            $zoom_link = $clase->zoom_link;
        }

        return response()->json([
            'now' => now($authTimezone)->copy(),
            'start' => $start,
            'end' => $end,
            'has' => $clase->authReservedThis(),
            'can_zoom' => $can_zoom,
            'zoom_link' => $zoom_link,
        ]);
    }
}


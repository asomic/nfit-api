<?php

namespace App\Observers\Plans;

use Session;
use Carbon\Carbon;
use App\Models\Users\User;
use App\Models\Plans\Plan;
use App\Models\Plans\PlanUser;
use App\Models\Clases\Reservation;
use App\Models\Plans\PlanIncomeSummary;

class PlanUserObserver
{
    /**
     * [creating description]
     * @param  PlanUser $planUser [description]
     * @return [type]             [description]
     */
    public function creating(PlanUser $planUser)
    {
        $user = User::findOrFail($planUser->user_id);
        $fecha_inicio = Carbon::parse($planUser->start_date);
        $fecha_termino = Carbon::parse($planUser->finish_date);
        $plan_users = PlanUser::whereIn('plan_status_id', [1, 3])->where('user_id', $user->id)->get();
        foreach ($plan_users as $plan_user) {
            if (($fecha_inicio->between(Carbon::parse($plan_user->start_date), Carbon::parse($plan_user->finish_date))) || ($fecha_termino->between(Carbon::parse($plan_user->start_date), Carbon::parse($plan_user->finish_date)))) {

                Session::flash('error', 'El usuario tiene un plan que choca con la fecha de inicio y período seleccionados');
                return false;
            } elseif (($fecha_inicio->lt(Carbon::parse($plan_user->start_date))) && ($fecha_termino->gt(Carbon::parse($plan_user->finish_date)))) {

                Session::flash('error', 'El usuario tiene un plan activo que choca con la fecha de inicio y período seleccionados');
                return false;
            } elseif (($fecha_inicio->gt(Carbon::parse($plan_user->start_date))) && ($fecha_termino->lt(Carbon::parse($plan_user->finish_date)))) {
                Session::flash('error', 'El usuario tiene un plan activo que choca con la fecha de inicio y período seleccionados');
                return false;
            }
        }
        $planUser->plan_status_id = $this->checkActualPlan($planUser);
    }

    /**
     * Handle the plan user "created" event.
     *
     * @param  \App\Models\Plans\PlanUser  $planUser
     * @return void
     */
    public function created(PlanUser $planUser)
    {
        $this->fixReservations($planUser);

        $this->updateStatusUser($planUser);
    }

    /**
     * [updating description]
     * 
     * @param  PlanUser $planUser [description]
     * @return [type]             [description]
     */
    public function updating(PlanUser $planUser)
    {
        $user = User::findOrFail($planUser->user_id);
        
        $fecha_inicio = Carbon::parse($planUser->start_date);
        
        $fecha_termino = Carbon::parse($planUser->finish_date);
        
        $plan_users = PlanUser::whereIn('plan_status_id', [1, 3])
                              ->where('user_id', $user->id)
                              ->where('id', '!=', $planUser->id)
                              ->get();
        
        foreach ($plan_users as $plan_user) {
            $start_date = Carbon::parse($plan_user->start_date);
            
            $finish_date = Carbon::parse($plan_user->finish_date);

            if ( $fecha_inicio->between($start_date, $finish_date) || $fecha_termino->between($start_date, $finish_date)) {

                Session::flash(
                    'error-tap',
                    'No se pudo actualizar las fechas, debido a que el plan ' . $plan_user->plan->plan . ' que va desde el ' . $start_date->format('d-m-Y') . ' al ' . $finish_date->format('d-m-Y') . ' choca con una fecha del plan que intentas modificar');

                return false;
            }

            if ( $fecha_inicio->lt($start_date) && $fecha_termino->gt($finish_date) ) {

                Session::flash(
                    'error-tap',
                    'No se pudo actualizar las fechas, debido a que el plan ' . $plan_user->plan->plan . ' que va desde el ' . $start_date->format('d-m-Y') . ' al ' . $finish_date->format('d-m-Y') . ', choca fecha del plan que intentas modificar'
                );
                
                return false;
            } 

            if ( $fecha_inicio->gt($start_date) && $fecha_termino->lt($finish_date) ) {

                Session::flash(
                    'error-tap',
                    'No se pudo actualizar las fechas, debido a que el plan ' . $plan_user->plan->plan . ' que va desde el ' . $start_date->format('d-m-Y') . ' al ' . $finish_date->format('d-m-Y') . ', choca con una fecha del plan que intentas modificar');
                
                return false;
            }
        }

        if ($planUser->plan_status_id != 5) {

            $planUser->plan_status_id = $this->checkActualPlan($planUser);
        
        }
    }

    //UPDATE PARA CANCELAR EL PLAN
    public function updated(PlanUser $planUser)
    {
        if ($planUser->plan_status_id == 5) {
            foreach ($planUser->reservations as $key => $reserv) {
                if ($reserv->reservation_status_id == 1 || $reserv->reservation_status_id == 2) {
                    $reserv->delete();
                } elseif ($reserv->reservation_status_id == 3 || $reserv->reservation_status_id == 4) {
                    $reserv->update(['plan_user_id' => null]);
                }
            }

            $this->updateStatusUser($planUser);
        } else {
            $this->fixReservations($planUser);

            $this->updateStatusUser($planUser);
        }
    }

    /**
     * Handle the plan user "restored" event.
     *
     * @param  \App\Models\Plans\PlanUser  $planUser
     * @return void
     */
    public function deleted(PlanUser $planUser)
    {
        if ($planUser->bill) {
            $plan_income_sum = PlanIncomeSummary::where('month', Carbon::parse($planUser->bill->date)->month)
                ->where('year', Carbon::parse($planUser->bill->date)->year)
                ->where('plan_id', $planUser->plan_id)->first();
            if ($plan_income_sum) {
                $plan_income_sum->amount -= $planUser->bill->amount;
                $plan_income_sum->quantity -= 1;
                $plan_income_sum->save();
            }
            $planUser->bill->delete();
        }
    }

    public function checkActualPlan(PlanUser $planUser)
    {
        if ($planUser->start_date > today()) {
            $planUser->plan_status_id = 3;
        } elseif ($planUser->finish_date < today()) {
            $planUser->plan_status_id = 4;
        }
        if (!$planUser->user->actual_plan && $planUser->start_date <= today() && $planUser->finish_date >= today()) {
            $planUser->plan_status_id = 1;
        }
        return $planUser->plan_status_id;
    }

    public function fixReservations(PlanUser $planUser)
    {
        $reservations = Reservation::join('clases', 'reservations.clase_id', '=', 'clases.id')
                                   ->where('reservations.user_id', $planUser->user_id)
                                   ->whereBetween('date', [Carbon::parse($planUser->start_date)->format('Y-m-d'), Carbon::parse($planUser->finish_date)->format('Y-m-d')])
                                   ->get('reservations.id');

        $reservations_out = Reservation::join('clases', 'reservations.clase_id', '=', 'clases.id')
                                       ->where('reservations.user_id', $planUser->user_id)
                                       ->whereNotBetween('date', [Carbon::parse($planUser->start_date)->format('Y-m-d'), Carbon::parse($planUser->finish_date)->format('Y-m-d')])
                                       ->get('reservations.id');

        foreach ($reservations as $reserv) {
            $reservation = Reservation::find($reserv->id, ['id', 'plan_user_id']);
            
            if ($reservation->plan_user_id !== $planUser->id) {
                $reservation->update(['plan_user_id' => $planUser->id]);
                $planUser->counter -= 1;
                $planUser->save();
            }
        }

        foreach ($reservations_out as $reserv) {
            $reservation = Reservation::find($reserv->id, ['id', 'plan_user_id']);

            if ($reservation->plan_user_id === $planUser->id) {
                $reservation->update(['plan_user_id' => null]);
                $planUser->counter += 1;
                $planUser->save();
            }
        }

        return $planUser;
    }

    public function updateStatusUser(PlanUser $planUser)
    {
        $user = $planUser->user;

        if (today()->between(Carbon::parse($planUser->start_date), Carbon::parse($planUser->finish_date)) &&
            $planUser->plan_status_id === 1) {
            $user->status_user = ($planUser->plan->id === 1) ? 3 : 1;
        } elseif ($user->actual_plan && $user->actual_plan->id != $planUser->id) {
            $user->status_user = $user->actual_plan->plan->id === 1 ? 3 : 1;
        } else {
            $user->status_user = 2;
        }

        $user->save();
    }
}

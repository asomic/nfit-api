<?php

namespace App\Http\Controllers\Plans;

use App\Models\Plans\Plan;
use App\Models\Plans\PlanUserFlow;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Auth;
use Carbon\Carbon;

class PlanController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::whereNotNull('plan_period_id')->where('custom',0)->get();
        return $this->showAll($plans);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Users\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        return $this->showOne($plan);
    }

    public function dates(Plan $plan)
    {
        $user_plan = Auth::user()->plan_users()->where('plan_status_id', '!=', 3)->orderBy('finish_date', 'desc')->first();

        $start = Carbon::now();
        $end = Carbon::now();

        if($user_plan) {
            if($user_plan->finish_date->addDay() > Carbon::now() ){
                $start = $user_plan->finish_date->addDay();
                $end = $user_plan->finish_date->addDay();
            }
        }

        switch ($plan->plan_period_id) {
            case 1:
                $end->addMonths(1)->subDay();
                break;
            case 3:
                $end->addMonths(3)->subDay();
                break;
            case 5:
                $end->addMonths(6)->subDay();
                break;
            case 6:
                $end->addYear()->subDay();
                break;
        }

        return response()->json([
            'start' => (string) ucfirst(strftime('%A %d de %B, %Y', $start->timestamp)),
            'end' => (string) ucfirst(strftime('%A %d de %B, %Y', $end->timestamp)),
            // 'start' => (string) ucfirst($start->formatLocalized('%A %d')).' de '.ucfirst($start->formatLocalized('%B, %Y')) ,
            // 'end'  => (string) ucfirst($end->subDay()->formatLocalized('%A %d')).' de '.ucfirst($end->subDay()->formatLocalized('%B, %Y')),
          ], 200);
    }

    public function contract(Plan $plan)
    {

        $user_plan = Auth::user()->plan_users()->where('plan_status_id','!=',3)->orderBy('finish_date', 'desc')->first();

        $start = Carbon::now();
        $end = Carbon::now();

        if($user_plan) {
            if($user_plan->finish_date->addDay() > Carbon::now() ){
                $start = $user_plan->finish_date->addDay();
                $end = $user_plan->finish_date->addDay();
            }

        }

        switch ($plan->plan_period_id) {
            case 1:
                $end->addMonths(1);
                $months = 1 ;
                break;
            case 3:
                $end->addMonths(3);
                $months = 3;
                break;
            case 5:
                $end->addMonths(6);
                $months = 6;
                break;
            case 6:
                $end->addYear();
                $months = 12;
                break;
        }


        $user = Auth::user();
        $planUserFlow = new PlanUserFlow;
        $planUserFlow->start_date = $start;
        $planUserFlow->finish_date = $end;
        $planUserFlow->counter = $plan->class_numbers*$months*$plan->daily_clases;
        $planUserFlow->plan_status_id = 1;
        $planUserFlow->plan_id = $plan->id;
        $planUserFlow->user_id = $user->id;

        $planUserFlow->save();


        return $this->showOne($planUserFlow);

    }

}

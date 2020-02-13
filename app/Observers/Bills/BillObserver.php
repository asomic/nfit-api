<?php

namespace App\Observers\Bills;

use App\Models\Bills\Bill;
use App\Models\Plans\PlanIncomeSummary;

/**
 * [PlanUserObserver description]
 */
class BillObserver
{

    /**
     * Handle the plan user "created" event.
     *
     * @param  \App\Models\Plans\PlanUser  $planUser
     * @return void
     */
    public function created(Bill $bill)
    {
        $month = $bill->date->month;
        $year = $bill->date->year;
        $plan_id = $bill->plan_user->plan->id;
        $amount = $bill->amount;

        $plan_income_sum = PlanIncomeSummary::where('month', $month)->where('year', $year)->where('plan_id', $plan_id)->first();

        if ($plan_income_sum) {
            $plan_income_sum->amount = $plan_income_sum->amount + $amount;
            $plan_income_sum->quantity = $plan_income_sum->quantity + 1;
        } else {
            $plan_income_sum = new PlanIncomeSummary;
            $plan_income_sum->amount = $amount;
            $plan_income_sum->plan_id = $plan_id;
            $plan_income_sum->month = $month;
            $plan_income_sum->year = $year;
            $plan_income_sum->quantity = 1;
        }

        $plan_income_sum->save();

    }

}

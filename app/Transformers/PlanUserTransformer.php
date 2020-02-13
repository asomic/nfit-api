<?php

namespace App\Transformers;

use App\Models\Plans\PlanUser;
use League\Fractal\TransformerAbstract;

class PlanUserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(PlanUser $planuser)
    {
         //dd($planuser->planStatus->plan_status);
        $hasBill = false;
        $bill_id = '--';
        $bill_date = '--';
        $bill_method = '--';
        $bill_detail = '--';
        $bill_amount = '--';
        if($planuser->bill)
        {
          $hasBill = true;
          $bill_id = $planuser->bill->id;
          $bill_date = $planuser->bill->date->format('d-m-Y');
          $bill_method = $planuser->bill->payment_type->payment_type;
          if($planuser->bill->total_paid == null){
            $bill_amount = '$'.number_format($planuser->bill->amount, 0, ',', '.');
          } else {
            $bill_amount = '$'.number_format($planuser->bill->total_paid, 0, ',', '.');
          }
          
        }

        $period = '';
        $periodId = 0;
        $hasPeriod = false;
        if($planuser->plan->plan_period)
        {
            $period = $planuser->plan->plan_period->period;
            $hasPeriod = true;
            $periodId =$planuser->plan->plan_period_id;
        }
        $canContract = true;
        if(!$planuser->plan->plan_period || $planuser->plan->custom == 1)
        {
            $canContract = false;
        }



        return [
            'id' => (int)$planuser->id,
            'start' => (string)$planuser->start_date,
            'end' => (string)$planuser->finish_date,
            'vencimiento' => (string)ucfirst($planuser->finish_date->formatLocalized('%A %d')).' de '.ucfirst($planuser->finish_date->formatLocalized('%B, %Y')) ,
            'inicio' => (string)ucfirst($planuser->start_date->formatLocalized('%A %d')).' de '.ucfirst($planuser->start_date->formatLocalized('%B, %Y')) ,
            'counter' => (string)$planuser->counter,
            'canContract' => (boolean)$canContract,
            'status' => [
                'id' => (string)$planuser->plan_status_id,
                'name' => (string)$planuser->planStatus->plan_status,
                'class' => (string)$planuser->planStatus->type,
            ],

            'rels' => [
                'user' => [
                  'user_id' => $planuser->user->id,
                ],
                'plan' => [
                    'id' => (int)$planuser->plan_id,
                    'name' => (string)$planuser->plan->plan,
                    'hasPeriod' => (boolean)$hasPeriod,
                    'period' => (string)$period,
                    'period_id' => (int)$periodId,
                    'amount' => (string)'$'.number_format($planuser->plan->amount, 0, ',', '.'),
                  ],
                'bill' => [
                  'has' => (bool)$hasBill,
                  'id' => (string)$bill_id,
                  'method' => (string)$bill_method,
                  'date' => (string)$bill_date,
                  'detail' => (string)$bill_detail,
                  'amount' => (string)  $bill_amount,

                ],
            ],
            //
        ];
    }

    /**
     * [originalAttribute changes the faced version to the original]
     *
     * @return [array]        [description]
     */
    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'start' => 'start_date',
            'end' => 'finish_date',
            'counter' => 'counter',
            'status' => 'plan_status_id',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /**
     * [transformedAttribute changes the original attributes to the faced]
     *
     * @return [type]        [description]
     */
    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'start_date' => 'start',
            'finish_date' => 'end',
            'counter' => 'counter',
            'plan_status_id' => 'status',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

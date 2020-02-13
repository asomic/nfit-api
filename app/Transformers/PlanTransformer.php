<?php

namespace App\Transformers;

use App\Models\Plans\Plan;
use League\Fractal\TransformerAbstract;

class PlanTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Plan $plan)
    {


        return [
            'id' => (int)$plan->id,
            'name' => (string)$plan->plan,
            'description' => (string)$plan->description,
            'periodId' => (int)$plan->plan_period_id,
            'period' => (string)$plan->plan_period->period,
            'amount' => (string)number_format($plan->amount, 0, ',', '.'),
            'commission' => (string)number_format($plan->amount*0.0396, 0, ',', '.'),
            'total' => (string)number_format($plan->amount + $plan->amount*0.0396, 0, ',', '.'),
            'contractable' => (boolean)$plan->contractable,
            'convenio' => (boolean)$plan->convenio,
            'rels' => [
                // 'auth' => [
                //     'can' => (boolean)true,
                //     'start' => (string)'start',
                //     'end' => (string)'end',
                // ],
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
            'name' => 'plan',
            'periodId' => 'plan_period_id',
            'amount' => 'amount',
            'contractable' => 'contractable',
            'convenio' => 'convenio',
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
            'plan' => 'name',
            'plan_period_id' => 'periodId',
            'amount' => 'amount',
            'contractable' => 'contractable',
            'convenio' => 'convenio',

        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

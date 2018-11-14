<?php

namespace App\Transformers;

use App\Models\Bills\Bill;
use League\Fractal\TransformerAbstract;

class BillTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Bill $bill)
    {
        return [
            'idTipoPago' => (int)$bill->payment_type_id,
            'idPlanUsuario' => (int)$bill->plan_user_id,
            'fecha' => (string)$bill->date,
            'fechaInicio' => (string)$bill->start_date,
            'fenchaTermino' => (string)$bill->finish_date,
            'detalles' => (string)$bill->detail,
            'monto' => (int)$bill->amount,

            'rels' => [
                'self' => [
                    'href' => route('bills.show', $bill->id),
                ],
            ],
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'idTipoPago' => 'payment_type_id',
            'idPlanUsuario' => 'plan_user_id',
            'fecha' => 'date',
            'fechaInicio' => 'start_date',
            'fenchaTermino' => 'finish_date',
            'detalles' => 'detail',
            'monto' => 'amount',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'payment_type_id' => 'idTipoPago',
            'plan_user_id' => 'idPlanUsuario',
            'date' => 'fecha',
            'start_date' => 'fechaInicio',
            'finish_date' => 'fenchaTermino',
            'detail' => 'detalles',
            'amount' => 'monto',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

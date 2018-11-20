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
        $hasBill = false;
        $bill_id = '--';
        $bill_date = '--';
        $bill_method = '--';
        $bill_detail = '--';
        if($planuser->bill)
        {
          $hasBill = true;
          $bill_id = $planuser->bill->id;
          $bill_date = $planuser->bill->date;
          $bill_method = $planuser->bill->payment_type->payment_type;
        }


        return [
            'plan_user_id' => (int)$planuser->id,
            'start' => (string)$planuser->start_date,
            'end' => (string)$planuser->finish_date,
            'vencimiento' => (string)$planuser->finish_date->formatLocalized('%A %d de %B, %Y'),
            'amount' => (string)$planuser->amount,
            'counter' => (string)$planuser->counter,
            'plan_status' => (string)$planuser->plan_status_id,
            'plan_id' => (string)$planuser->plan_id,
            'plan_name' => (string)$planuser->plan->plan,

            'rels' => [
                'user' => [
                  'user_id' => $planuser->user->id,
                ],
                'bill' => [
                  'has' => (bool)$hasBill,
                  'id' => (string)$bill_id,
                  'method' => (string)$bill_method,
                  'date' => (string)$bill_date,
                  'detail' => (string)$bill_detail,


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
            'identificador' => 'id',
            'fechaInicio' => 'start_date',
            'fechatermino' => 'finish_date',
            'monto' => 'amount',
            'contador' => 'counter',
            'estadoPlan' => 'gender',
            'idDescuento' => 'phone',
            'idPlan' => 'address',
            'idUsuario' => 'emergency_id',
            'fechaCreacion' => 'created_at',
            'fechaActualizacion' => 'updated_at',
            'fechaEliminacion' => 'deleted_at',
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
            'id' => 'identificador',
            'start_date' => 'start_date',
            'finish_date' => 'finish_date',
            'amount' => 'correo',
            'counter' => 'fechaNacimiento',
            'gender' => 'genero',
            'phone' => 'telefono',
            'address' => 'direccion',
            'emergency_id' => 'contactoEmergencia',
            'status_planuser_id' => 'estadoUsuario',
            'created_at' => 'fechaCreacion',
            'updated_at' => 'fechaActualizacion',
            'deleted_at' => 'fechaEliminacion',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

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
        return [
            'identificador' => (int)$planuser->id,
            'fechaInicio' => (string)$planuser->start_date,
            'fechatermino' => (string)$planuser->finish_date,
            'vencimiento' => (string)$planuser->finish_date->format('jS \o\f F, Y'),
            'monto' => (string)$planuser->amount,
            'contador' => (string)$planuser->counter,
            'estadoPlan' => (string)$planuser->plan_status_id,
            'idDescuento' => (int)$planuser->discount_id,
            'idPlan' => (string)$planuser->plan_id,
            'nombrePlan' => (string)$planuser->plan->plan,
            'idUsuario' => (int)$planuser->user_id,
            'fechaCreacion' => (string)$planuser->created_at,
            'fechaActualizacion' => (string)$planuser->updated_at,
            'fechaEliminacion' => isset($planuser->deleted_at) ? (string) $planuser->deleted_at : null,

            // 'rels' => [
            //     'self' => [
            //         'href' => route('users.plans.show', [
            //             'user' => $planuser->user_id,
            //             'plan' => $planuser->plan_id]),
            //     ],
            // ],
            // //
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

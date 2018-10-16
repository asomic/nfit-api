<?php

namespace App\Transformers;

use App\Models\Clases\Clase;
use League\Fractal\TransformerAbstract;

class ClaseTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Clase $clase)
    {
        return [
            'identificador' => (int)$clase->id,
            'fecha' => (string)$clase->date,
            'comienzo' => (string)$clase->start_at,
            'termino' => (string)$clase->finish_at,
            'idBloque' => (int)$clase->block_id,
            'idProfesor' => (int)$clase->profesor_id,
            'idWod' => (int)$clase->wod_id,
            'cupos' => (int)$clase->quota,
            'idtipoClase' => (int)$clase->clase_type_id,
            'fechaCreacion' => (string)$clase->created_at,
            'fechaActualizacion' => (string)$clase->updated_at,
            'fechaEliminacion' => isset($clase->deleted_at) ? (string) $clase->deleted_at : null,

            // 'rels' => [
            //     'self' => [
            //         'href' => route('users.plans.show', [
            //             'user' => $clase->user_id,
            //             'plan' => $clase->plan_id]),
            //     ],
            // ],
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
            'fecha' => 'date',
            'comienzo' => 'start_at',
            'termino' => 'finish_at',
            'idBloque' => 'block_id',
            'idProfesor' => 'profesor_id',
            'idWod' => 'wod_id',
            'cupos' => 'quota',
            'idtipoClase' => 'clase_type_id',
            'fechaCreacion' => 'created_at',
            'fechaActualizacion' => 'updated_at',
            'fechaEliminacion' => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /**
     * [transformedAttribute changes the original attributes to the faced]
     * @return [type]        [description]
     */
    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'identificador',
            'date' => 'fecha',
            'start_at' => 'comienzo',
            'finish_at' => 'termino',
            'block_id' => 'idBloque',
            'profesor_id' => 'idProfesor',
            'wod_id' => 'idWod',
            'quota' => 'cupos',
            'clase_type_id' => 'idtipoClase',
            'created_at' => 'fechaCreacion',
            'updated_at' => 'fechaActualizacion',
            'deleted_at' => 'fechaEliminacion',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

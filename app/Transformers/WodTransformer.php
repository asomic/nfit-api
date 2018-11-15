<?php

namespace App\Transformers;

use App\Models\Wods\Wod;
use League\Fractal\TransformerAbstract;

class WodTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Wod $wod)
    {
        return [
            'identificador' => (int)$wod->id,
            'fecha' => (string)$wod->date,
            'fechaHuman' => (string)$wod->date->formatLocalized('%A %d de %B'),
            'year' => (string)$wod->date->formatLocalized('%Y'),
            'fechaCreacion' => (string)$wod->created_at,
            'fechaActualizacion' => (string)$wod->updated_at,
            'fechaEliminacion' => isset($wod->deleted_at) ? (string) $wod->deleted_at : null,
            'rels' => [
                'self' => [
                    'href' => '',
                ],
                'stages' => [
                    'warmup' => (string)$wod->stage(1)->description,
                    'skill' => (string)$wod->stage(2)->description,
                    'wod' => (string)$wod->stage(3)->description,
                ],
            ],


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

            'created_at' => 'fechaCreacion',
            'updated_at' => 'fechaActualizacion',
            'deleted_at' => 'fechaEliminacion',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

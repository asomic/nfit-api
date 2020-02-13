<?php

namespace App\Transformers;

use App\Models\Wods\Stage;
use League\Fractal\TransformerAbstract;
use Auth;

class StageTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Stage $stage)
    {


        return [
            'id' => (int)$stage->id,
            'stageTypeId' => (int)$stage->stage_type_id,
            'stageType' => (string)$stage->stage_type->stage_type,
            'description' => (string)$stage->description,
            'rels' => [
                'wod' => [
                    'href' => route('wods.show', ['wod' => (int)$stage->wod_id]),
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
            'id' => 'id',
            'stageTypeId' => 'stage_type_id',
            'description' => 'description',
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
            'id' => 'id',
            'stage_type_id' => 'stageTypeId',
            'description' => 'description',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

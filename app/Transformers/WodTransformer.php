<?php

namespace App\Transformers;

use App\Models\Wods\Wod;
use League\Fractal\TransformerAbstract;
use Auth;

class WodTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Wod $wod)
    {

        if(count($wod->stages)>0)
        {
            $hasStages = true ;
            $stages = $wod->stages;
            $featuredStage = $stages[0]->description;
            foreach($stages as $stage )
            {
                if($stage->stage_type->featured == 1 )
                {
                    $featuredStage = $stage->description;
                }
            }

        } else {
            $hasStages = false;
            $featuredStage = '';
        }


        return [
            'id' => (int)$wod->id,
            'day' => (string)$wod->date->format('d'),
            'month' => (string)strtoupper ( $wod->date->formatLocalized('%b')) ,
            'year' => (string)$wod->date->formatLocalized('%Y'),
            'dateHuman' =>  (string)ucfirst($wod->date->formatLocalized('%A %d')).' de '.ucfirst($wod->date->formatLocalized('%B')) ,
            'rels' => [
                'claseType' => [
                    'id' => (int)$wod->clase_type_id,
                    'name' => (string)$wod->claseType->clase_type,
                    'icon' => (string) $wod->claseType->icon,
                    'iconWhite' => (string) $wod->claseType->icon_white,
                ],
                'stages' => [
                    'has' => (boolean)$hasStages,
                    'href' => route('wods.stages',['wod'=>$wod->id]),
                    'featured' => (string)$featuredStage,
                    'all' => (Array)$wod->stages->toArray(),
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
            'fecha' => 'date',
            'claseTypeId' => 'clase_type_id',

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
            'date' => 'fecha',
            'clase_type_id' => 'claseTypeId',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

<?php

namespace App\Transformers;

use App\Models\Plans\PlanUserFlow;
use League\Fractal\TransformerAbstract;

class PlanUserFlowTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(PlanUserFlow $planuserflow)
    {
        return [
            'id' => (int)$planuserflow->id,
            'start' => (string)$planuserflow->start_date,
            'end' => (string)$planuserflow->finish_date,
            'vencimiento' => (string) ucfirst(strftime('%A %d de %B', $planuserflow->finish_date->timestamp)),
            // 'vencimiento' => (string)ucfirst($planuserflow->finish_date->formatLocalized('%A %d')).' de '.ucfirst($planuserflow->finish_date->formatLocalized('%B, %Y')) ,
            'inicio' => (string) ucfirst(strftime('%A %d de %B', $planuserflow->start_date->timestamp)),
            // 'inicio' => (string)ucfirst($planuserflow->start_date->formatLocalized('%A %d')).' de '.ucfirst($planuserflow->start_date->formatLocalized('%B, %Y')) ,
            'user_id' => (int)$planuserflow->user->id,
            'rels' => [

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
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

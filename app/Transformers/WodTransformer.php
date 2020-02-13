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
        $reservationHas = false;
        $reservation = Auth::User()->clases()->where('date',today())->where('clase_type_id',$wod->clase_type_id)->first();
        $todayReservation = [];

    
        

        if(count($wod->stages)>0)
        {
            $hasStages = true ;
            $stages = $wod->stages;
            $featuredStage = '';
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



        if($reservation){
          $reservationHas = true;
          $todayReservation = [
            'id' => (int)$reservation->id,
            'status' => (int)$reservation->pivot->reservation_status_id,
            'start' => (string)date('H:i', strtotime($reservation->start_at)),
            'end' => (string)date('H:i', strtotime($reservation->finish_at)),
            'href' => (string)route('clases.show', ['clase' => $reservation->id]),
          ];
        }

        return [
            'id' => (int)$wod->id,
            'claseTypeId' => (int)$wod->clase_type_id,
            'claseType' => (string)$wod->claseType->clase_type,
            'fecha' => (string)$wod->date,
            'day' => (string)$wod->date->format('d'),
            'month' => (string)strtoupper ( $wod->date->formatLocalized('%b')) ,
            'dateHuman' =>  (string)ucfirst($wod->date->formatLocalized('%A %d')).' de '.ucfirst($wod->date->formatLocalized('%B')) ,
            'year' => (string)$wod->date->formatLocalized('%Y'),
            'rels' => [
                'self' => [
                    'href' => '',
                ],
                'claseType' => [
                    'id' => (int)$wod->clase_type_id,
                    'name' => (string)$wod->claseType->clase_type,
                    'icon' => (string) url('/').'/icon/clases/'.$wod->claseType->icon,
                    'iconWhite' => (string) url('/').'/icon/clases/'.$wod->claseType->icon_white,
                ],
                'auth' => [
                    'reservationHas' => (boolean)$reservationHas,
                    'todayReservation' => $todayReservation,
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

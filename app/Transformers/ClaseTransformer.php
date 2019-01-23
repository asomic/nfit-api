<?php

namespace App\Transformers;


use App\Models\Clases\Clase;
use App\Models\Clases\Reservation;
use League\Fractal\TransformerAbstract;
use Carbon\Carbon;
use Auth;

class ClaseTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Clase $clase)
    {
        $start = $clase->start_at;
        $end = $clase->finish_at;

        $dateTimeString = $clase->date->format('Y-m-d')." ".$start;
        $dateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeString);
        $pruebaCount = 0;
        foreach ($clase->users as $user) {
          if($user->status_user_id == 3){
            $pruebaCount++;
          }
        }

        if($dateTime > Carbon::now())
        {
          $active = true;
        } else {
          $active = false;
        }

        if($clase->auth_has_reservation())
        {
          $reservation = Reservation::where('user_id',Auth::user()->id)->where('clase_id',$clase->id)->first();
          $reservation_id = $reservation->id;
          $reservation_status = $reservation->status->reservation_status;
          $reservation_details = $reservation->details;
        } else {
          $reservation_id = '';
          $reservation_status = '';
          $reservation_details = '';
        }

        return [
            'clase_id' => (int)$clase->id,
            'date' => (string)$clase->date->toDateString(),
            'dateHuman' => (string)ucfirst($clase->date->formatLocalized('%A %d')).' de '.ucfirst($clase->date->formatLocalized('%B')) ,
            'day' => (string)ucfirst($clase->date->formatLocalized('%A %d' )),
            'month' =>(string)ucfirst($clase->date->formatLocalized('%B' )),
            'year' => (string)$clase->date->formatLocalized('%Y'),
            'start' => (string)date('H:i', strtotime($start)),
            'end' => (string)date('H:i', strtotime($end)),
            'quota' => (int)$clase->quota,
            'active' => (bool)$active,

            'rels' => [
                'wod' => [
                  'id' => (int)$clase->wod_id,
                  'href' => route('wods.show', ['wod' => (int)$clase->wod_id])
                ],
                'reservations' => [
                  'count' => (int)count($clase->users),
                  'prueba_count' => $pruebaCount,
                  'href' => route('clases.reservations', ['clase' => (int)$clase->id])
                ],
                'auth_reservation' => [
                  'has' => (bool)$clase->auth_has_reservation(),
                  'can' => (bool)$clase->auth_can_reserve(),
                  'reservation_id' => (int)$reservation_id,
                  'status' => (string)$reservation_status,
                  'details' => (string)$reservation_details,
                ]

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
            'clase_id' => 'id',
            'date' => 'date',
            'start' => 'start_at',
            'end' => 'finish_at',
            'quota' => 'quota'

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
            'id' => 'clase_id',
            'date' => 'date',
            'start_at' => 'start',
            'finish_at' => 'end',
            'quota' => 'quota'

        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

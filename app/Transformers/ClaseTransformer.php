<?php

namespace App\Transformers;


use App\Models\Clases\Clase;
use App\Models\Clases\Reservation;
use League\Fractal\TransformerAbstract;
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
        if($clase->auth_has_reservation())
        {
          $reservation = Reservation::where('user_id',Auth::user()->id)->where('clase_id',$clase->id)->first();
          $reservation_id = $reservation->id;
          $reservation_status = $reservation->status->reservation_status;
        } else {
          $reservation_id = '';
          $reservation_status = '';
        }

        return [
            'clase_id' => (int)$clase->id,
            'date' => (string)$clase->date,
            'dateHuman' => (string)$clase->date->format('jS \o\f F'),
            'start' => (string)$clase->start_at,
            'end' => (string)$clase->finish_at,
            'quota' => (int)$clase->quota,

            'rels' => [
                'wod' => [
                  'id' => (int)$clase->wod_id,
                  'href' => route('wods.show', ['wod' => (int)$clase->wod_id])
                ],
                'users' => [
                  'count' => (int)count($clase->users),
                  'href' => route('clases.users', ['clase' => (int)$clase->id])
                ],
                'auth_reservation' => [
                  'has' => (bool)$clase->auth_has_reservation(),
                  'reservation_id' => (int)$reservation_id,
                  'status' => (string)$reservation_status,
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

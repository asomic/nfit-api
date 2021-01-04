<?php

namespace App\Transformers;

use App\Models\Clases\Reservation;
use League\Fractal\TransformerAbstract;

class ReservationTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Reservation $reservation)
    {
        return [
            'reservation_id' => (int)$reservation->id,
            'status' => (string)$reservation->status->reservation_status,
            'details' => (string)$reservation->details,
            'rels' => [
                'clase' => [
                    'id' => (int)$reservation->clase_id,
                    'start' => (string)$reservation->clase->start_at,
                    'end' => (string)$reservation->clase->finish_at,
                    'date' => (string)$reservation->clase->date,
                    'dateHuman' => (string) c,
                //   'dateHuman' => (string) ucfirst($reservation->clase->date->formatLocalized('%A %d')).' de '.ucfirst($reservation->clase->date->formatLocalized('%B, %Y')) ,
                    'day' => (string) ucfirst(strftime('%A %d', $reservation->clase->date->timestamp)),
                //   'day' => (string)ucfirst($reservation->clase->date->formatLocalized('%A %d')),
                    'month' =>(string) ucfirst($reservation->clase->date->formatLocalized('%B')),
                    'year' =>(string) $reservation->clase->date->formatLocalized('%Y'),
                    'href' => (string)route('clases.show', ['clase' => (int)$reservation->clase_id]),
                    'users' => [
                        'count' => (int)count($reservation->clase->users),
                        // 'prueba_count' => (int)count($reservation->clase->pruebaUsersCount()),
                        'href' => (string)route('clases.users', ['clase' => (int)$reservation->clase_id]),
                        ],
                    ],
                    'wod' => [
                        'id' => (int)$reservation->clase->wod_id,
                        'href' => route('wods.show', ['wod' => (int)$reservation->clase->wod_id])
                    ],
                    'user' => [
                        'id' => (int)$reservation->user->id,
                        'first_name' => (string)$reservation->user->first_name,
                        'last_name' => (string)$reservation->user->last_name,
                        'avatar' => (string)$reservation->user->avatar,
                        'href' => route('users.show', ['user' =>  (int)$reservation->user->id])
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
            'reservation_id' => 'id',

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
            'id' => 'reservation_id',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

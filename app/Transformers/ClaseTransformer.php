<?php

namespace App\Transformers;

use Auth;
use Carbon\Carbon;
use App\Models\Clases\Clase;
use App\Models\Users\StatusUser;
use App\Models\Clases\Reservation;
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
        $start = $clase->getOriginal('start_at');
        $end = $clase->getOriginal('finish_at');

        $dateTimeStringStart = $clase->date->format('Y-m-d')." ".$start;
        $dateTimeStringEnd = $clase->date->format('Y-m-d')." ".$end;
        $dateTimeStart = Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeStringStart);
        $dateTimeEnd = Carbon::createFromFormat('Y-m-d H:i:s', $dateTimeStringEnd);

        // $pruebaCount = 0;
        // foreach ($clase->users as $user) {
        //     if($user->status_user == StatusUser::PRUEBA) {
        //         $pruebaCount++;
        //     }


        // }

        if ($clase->authReservedThis()) {
            $reservation = Reservation::where('user_id', Auth::user()->id)
                                        ->where('clase_id',$clase->id)
                                        ->first();
            $reservation_id = $reservation->id;
            $reservation_status = $reservation->status->toArray();
            $reservation_details = $reservation->details;
        } else {
            $reservation_id = '';
            $reservation_status = [];
            $reservation_details = '';
        }

        return [
            'id' => (int) $clase->id,
            'type' => (int) $clase->clase_type_id,
            'typeName' => (string) $clase->claseType->clase_type,
            'date' => (string) $clase->date->toDateString(),
            'date_human' => (string) strftime('%A %d de %B', $clase->date->timestamp),
            // 'dateHuman' => (string) ucfirst($clase->date->formatLocalized('%A %d de %B')),
            'day' => (string) $clase->date->format('d'),
            'month' =>(string) $clase->date->formatLocalized('%b'),
            'year' => (string) $clase->date->formatLocalized('%Y'),
            'start' => (string) date('H:i', strtotime($clase->start_at)),
            'end' => (string) date('H:i', strtotime($clase->finish_at)),
            'quota' => (int)$clase->quota,
            'active' => (bool) $clase->stillActive(),
            'finished' => (bool)$clase->hasFinished(),
            'coach' => (string) isset($clase->profesor) ? $clase->profesor->full_name : null,

            'rels' => [
                'wod' => [
                    'id' => (int)$clase->wod_id,
                    'href' => route('wods.show', ['wod' => (int)$clase->wod_id]),
                    'stages' => route('wods.stages', ['wod' => (int)$clase->wod_id])
                ],
                'reservations' => [
                    'count' => (int)count($clase->users),
                    'prueba_count' => $pruebaCount ?? 0,
                    'href' => route('clases.reservations', ['clase' => (int)$clase->id])
                ],
                'auth_reservation' => [
                    'has' => (bool)$clase->authReservedThis(),
                    'can' => (bool)$clase->auth_can_reserve(),
                    'reservation_id' => (int)$reservation_id,
                    'status' => (Array)$reservation_status,
                    'details' => (string)$reservation_details,
                ],
                'claseType' => [
                    'id' => (string)$clase->claseType->id,
                    'name' => (string)$clase->claseType->clase_type,
                    'icon' => (string) $clase->claseType->icon,
                    'iconWhite' => (string) $clase->claseType->icon_white,
                ]
            ],
        ];
    }

    /**
     *  Undocumented function
     *
     *  @param   [type] $index
     *
     *  @return  array|null
     */
    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'type' => 'clase_type_id',
            'date' => 'date',
            'start' => 'start_at',
            'end' => 'finish_at',
            'quota' => 'quota'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /**
     *  Undocumented function
     *
     *  @param  [type] $index
     *
     *  @return  array|null
     */
    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',
            'clase_type_id' => 'type',
            'date' => 'date',
            'start_at' => 'start',
            'finish_at' => 'end',
            'quota' => 'quota'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

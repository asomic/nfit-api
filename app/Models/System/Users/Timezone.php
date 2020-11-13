<?php

namespace App\Models\System\Users;

use Carbon\Carbon;
// use App\Models\Tenant\Settings\Parameter;

class Timezone
{
    /**
     *  methodDescription
     *
     *  @return  returnType
     */
    public function timezones()
    {
        return [
            ['name' => 'America/Argentina/Buenos_Aires', 'human_name' => 'Argentina/Buenos Aires'],
            // ['name' => 'America/Argentina/Catamarca', 'human_name' => 'Argentina/Catamarca'],
            ['name' => 'America/Argentina/Cordoba', 'human_name' => 'Argentina/Cordoba'],
            // ['name' => 'America/Argentina/La_Rioja', 'human_name' => 'Argentina/La Rioja'],
            ['name' => 'America/Argentina/Mendoza', 'human_name' => 'Argentina/Mendoza'],
            // ['name' => 'America/Argentina/Rio_Gallegos', 'human_name' => 'Argentina/Rio Gallegos'],
            // ['name' => 'America/Argentina/Salta', 'human_name' => 'Argentina/Salta'],
            // ['name' => 'America/Argentina/San_Juan', 'human_name' => 'Argentina/San Juan'],
            // ['name' => 'America/Argentina/San_Luis', 'human_name' => 'Argentina/San Luis'],
            // ['name' => 'America/Argentina/Tucuman', 'human_name' => 'Argentina/Tucuman'],
            // ['name' => 'America/Argentina/Ushuaia', 'human_name' => 'Argentina/Ushuaia'],
            ['name' => 'America/Belize', 'human_name' => 'Belice/Belize'],
            // ['name' => 'America/Bahia', 'human_name' => 'Brasil/Bahia'],
            ['name' => 'America/Belem', 'human_name' => 'Brasil/Belem'],
            // ['name' => 'America/Boa_Vista', 'human_name' => 'Brasil/Boa Vista'],
            // ['name' => 'America/Boise', 'human_name' => 'Brasil/Boise'],
            // ['name' => 'America/Campo_Grande', 'human_name' => 'Brasil/Campo Grande'],
            // ['name' => 'America/Cuiaba', 'human_name' => 'Brasil/Cuiaba'],
            // ['name' => 'America/Curacao', 'human_name' => 'Brasil/Curacao'],
            ['name' => 'America/Fortaleza', 'human_name' => 'Brasil/Fortaleza'],
            // ['name' => 'America/Maceio', 'human_name' => 'Brasil/Maceio'],
            // ['name' => 'America/Managua', 'human_name' => 'Brasil/Managua'],
            // ['name' => 'America/Manaus', 'human_name' => 'Brasil/Manaus'],
            ['name' => 'America/Sao_Paulo', 'human_name' => 'Brasil/Sao Paulo'],
            ['name' => 'America/Punta_Arenas', 'human_name' => 'Chile/Punta Arenas'],
            ['name' => 'America/Santiago', 'human_name' => 'Chile/Santiago'],
            ['name' => 'America/Bogota', 'human_name' => 'Colombia/Bogota'],
            ['name' => 'America/Santo_Domingo', 'human_name' => 'Costa Rica/Santo Domingo'],
            // ['name' => 'America/Bahia_Banderas', 'human_name' => 'Mexico/Bahia Banderas'],
            ['name' => 'America/Cancun', 'human_name' => 'Mexico/Cancun'],
            // ['name' => 'America/Chihuahua', 'human_name' => 'Mexico/Chihuahua'],
            // ['name' => 'America/Guatemala', 'human_name' => 'Guatemala/Guatemala'],
            // ['name' => 'America/Guayaquil', 'human_name' => 'Mexico/Guayaquil'],
            // ['name' => 'America/Hermosillo', 'human_name' => 'Mexico/Hermosillo'],
            // ['name' => 'America/Matamoros', 'human_name' => 'Mexico/Matamoros'],
            // ['name' => 'America/Mazatlan', 'human_name' => 'Mexico/Mazatlan'],
            // ['name' => 'America/Merida', 'human_name' => 'Mexico/Merida'],
            ['name' => 'America/Mexico_City', 'human_name' => 'Mexico/Ciudad de Mexico'],
            // ['name' => 'America/Monterrey', 'human_name' => 'Mexico/Monterrey'],
            // ['name' => 'America/Montserrat', 'human_name' => 'Mexico/Montserrat'],
            // ['name' => 'America/Tijuana', 'human_name' => 'Mexico/Tijuana'],
            ['name' => 'America/Asuncion', 'human_name' => 'Paraguay/Asuncion'],
            ['name' => 'America/La_Paz', 'human_name' => 'Peru/La Paz'],
            ['name' => 'America/Lima', 'human_name' => 'Peru/Lima'],
            ['name' => 'America/Panama', 'human_name' => 'Panama/Panama'],
            ['name' => 'America/Montevideo', 'human_name' => 'Uruguay/Montevideo'],
            ['name' => 'America/Caracas', 'human_name' => 'Venezuela/Caracas'],
        ];
    }

    public static function calculateTimezoneDifference($timezoneName = 'UTC') {
        $utc = today();

        $boxTime = Carbon::createMidnightDate($utc->year, $utc->month, $utc->day, $timezoneName);

        return $boxTime->diffInHours($utc, false);
    }

    /**
     *  Pass an hour and calculate difference hour and return a new hour
     *
     *  @return  returnType
     */
    public function convertTimeZone($hour, $timezoneName)
    {
        $differenceHours = $this->calculateTimezoneDifference($timezoneName);

        if ($differenceHours < 0) {
            return $hour->subHours($differenceHours);
        }

        return $hour->addHours($differenceHours);
    }

    /**
     *  methodDescription
     *
     *  @return  returnType
     */
    public static function getUTCHour($hour)
    {
        $hour = Carbon::parse($hour);

        $timezoneName = Parameter::value('timezone');
        $differenceHours = self::calculateTimezoneDifference($timezoneName);

        if ($differenceHours < 0) {
            return $hour->subHours($differenceHours);
        }

        return $hour->addHours($differenceHours);
    }
}
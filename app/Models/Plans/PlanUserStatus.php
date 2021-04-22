<?php

namespace App\Models\Plans;

use App\Models\Tenant\Misc\Statuses;

class PlanUserStatus
{
    /** ID status Plan */
	const ACTIVO = 1;
    /** ID status Plan */
	const CONGELADO = 2;
    /** ID status Plan */
	const PRECOMPRA = 3;
    /** ID status Plan */
	const COMPLETADO = 4;
    /** ID status Plan */
	const CANCELADO = 5;

    /**
     *  Undocumented function
     *
     *  @return  array
     */
    public static function listAll(): array
    {
        return [
            self::ACTIVO     => 'ACTIVO',
            self::CONGELADO  => 'CONGELADO',
            self::PRECOMPRA  => 'PRECOMPRA',
            self::COMPLETADO => 'COMPLETADO',
            self::CANCELADO  => 'CANCELADO',
        ];
    }

    /**
     *  Return a Plan User Status by an specific Id
     *
     *  @param   integer   Id for a status
     *
     *  @return  string    A Plan User Status
     */
    public static function get($planUserStatusId)
    {
        $plan_user_statuses = self::listAll();

        return $plan_user_statuses[$planUserStatusId] ?? 'SIN ESTADO';
    }

    /**
     *  Check if the passed plan_status_id is active or not
     *
     *  @param   int|string
     *
     *  @return  boolean
     */
    public static function isActive($planUserStatusId): bool
    {
        return self::ACTIVO === (int) $planUserStatusId;
    }
}

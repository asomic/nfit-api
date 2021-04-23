<?php

namespace App\Models\Plans;

use App\Models\Plans\Plan;
use Illuminate\Database\Eloquent\Model;

class PlanStatus extends Model
{
	/** ID status Plan */
	const ACTIVO = 1;
	const CONGELADO = 2;
	const PRECOMPRA = 3;
	const COMPLETADO = 4;
	const CANCELADO = 5;

    protected $table = 'plan_status';

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }
}

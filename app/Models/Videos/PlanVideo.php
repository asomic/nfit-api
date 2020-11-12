<?php

namespace App\Models\Tenant\Videos;

use App\Models\Tenant\Plans\Plan;
use App\Models\Tenant\Videos\Video;
use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class PlanVideo extends Model
{
    use UsesTenantConnection;

    protected $table = 'plan_video';

	/**
	 * [block description]
	 * @return [model] [return block model]
	 */
    public function video()
	{
		return $this->belongsTo(Video::class);
	}

	/**
	 * [plan description]
	 * @return [model] [return plan model]
	 */
	public function plan()
	{
		return $this->belongsTo(Plan::class);
	}
}

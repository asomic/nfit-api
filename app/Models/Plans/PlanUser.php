<?php

namespace App\Models\Plans;

use Carbon\Carbon;
use App\Models\Plans\Plan;
use App\Models\Bills\Bill;
use App\Models\Users\User;
use App\Models\Plans\Discount;
use App\Models\Plans\PlanUserPeriod;
use Illuminate\Database\Eloquent\Model;
use App\Observers\Plans\PlanUserObserver;
use App\Transformers\PlanUserTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanUser extends Model
{
    use SoftDeletes;

    /**
     * [$table description]
     *
     * @var [type]
     */
    protected $table = 'plan_user';

    /**
     * [$dates description]
     *
     * @var [type]
     */
    protected $dates = ['deleted_at','start_date', 'finish_date'];

    /**
     * [$fillable description]
     *
     * @var [type]
     */
    protected $fillable = [
        'start_date', 'finish_date', 'amount', 'counter',
        'plan_status_id', 'discount_id', 'plan_id', 'user_id'
    ];

    /**
     * [$transformer description]
     *
     * @var [type]
     */
    public $transformer = PlanUserTransformer::class;

    /**
     * [boot description]
     *
     * @return [model] [description]
     */
    public static function boot()
    {
        parent::boot();
    }

    /**
     * [getStartDateAttribute description]
     *
     * @param  [type] $value [description]
     *
     * @return [type]        [description]
     */
    public function getStartDateAttribute($value)
    {
        return Carbon::parse($value);
    }

    /**
     * [getFinishDateAttribute description]
     *
     * @param  [type] $value [description]
     *
     * @return [type]        [description]
     */
    public function getFinishDateAttribute($value)
    {
        return Carbon::parse($value);
    }

    /**
     * [discount description]
     *
     * @return  [type]  [return description]
     */
    public function discount()
    {
        return $this->hasOne(Discount::class);
    }

    /**
     * [installments description]
     *
     * @return  [type]  [return description]
     */
    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    /**
     * [plan description]
     *
     * @return [model] [description]
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * [user description]
     *
     * @return [model] [description]
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * [plan_user_periods description]
     *
     * @return [model] [description]
     */
    public function plan_user_periods()
    {
        return $this->hasMany(PlanUserPeriod::class);
    }

    /**
     * [bill description]
     *
     * @return  [type]  [return description]
     */
    public function bill()
    {
        return $this->hasOne(Bill::class);
    }
}

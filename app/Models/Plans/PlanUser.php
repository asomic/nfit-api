<?php

namespace App\Models\Plans;

use Carbon\Carbon;
use App\Models\Bills\Bill;
use App\Models\Plans\Plan;
use App\Models\Users\User;
use App\Models\Plans\Discount;
use App\Models\Plans\PlanStatus;
use App\Models\System\NfitTimeZone;
use App\Models\Plans\PlanUserPeriod;
use Illuminate\Database\Eloquent\Model;
use App\Observers\Plans\PlanUserObserver;
use App\Transformers\PlanUserTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;

/** [PlanUser description] */
class PlanUser extends Model
{
    use SoftDeletes;

    /**
     *  Table name in Database
     *
     *  @var  string
     */
    protected $table = 'plan_user';

    /**
     *  Data to be treat like date
     *
     *  @var  array
     */
    protected $dates = ['deleted_at','start_date', 'finish_date'];
    
    /**
     * Massive assignment data
     *
     * @var  array
     */
    protected $fillable = [
        'start_date', 'finish_date', 'amount', 'counter',
        'plan_status_id', 'discount_id', 'plan_id', 'user_id'
    ];

    /**
     *  Undocumented variable
     *
     *  @var  Class
     */
    public $transformer = PlanUserTransformer::class;

    /**
     * [boot description]
     * @return [model] [description]
     */
    public static function boot()
    {
        parent::boot();
        PlanUser::observe(PlanUserObserver::class);
    }

    /**
     *  Convert from UTC to user timezone
     *
     *  @param   String  $value
     * 
     *  @return  Carbon\Carbon
     */
    public function getStartDateAttribute($value)
    {
        if ($value) {
            return NfitTimeZone::adjustToTimeZoneDate($value);
        }
    }

    /**
     *  Convert from UTC to user timezone
     *
     *  @param   String  $value
     * 
     *  @return  Carbon\Carbon
     */
    public function getFinishDateAttribute($value)
    {
        return Carbon::parse($value);
    }

    /**
     * [discount description]
    * @method discount
    * @return [model]   [description]
    */
    public function discount()
    {
        return $this->hasOne(Discount::class);
    }

    /**
     * [installments description]
     * @method installments
     * @return [model]       [description]
     */
    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    /**
     * [plan description]
     * @method plan
     * @return [model] [description]
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * [user description]
     * @method user
     * @return [model] [description]
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * [plan_user_periods description]
     * @return [model] [description]
     */
    public function plan_user_periods()
    {
        return $this->hasMany(PlanUserPeriod::class);
    }

    /**
     * Bill relationship
     *
     *  @return  App\Models\Bill
     */
    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

    /**
     *  Plan Status relationship
     *
     *  @return  App\Models\Plans\PlanStatus
     */
    public function planStatus()
    {
        return $this->belongsTo(PlanStatus::class,'plan_status_id');
    }
}

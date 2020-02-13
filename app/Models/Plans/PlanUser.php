<?php

namespace App\Models\Plans;

use App\Models\Bills\Bill;
use App\Models\Plans\Discount;
use App\Models\Plans\Plan;
use App\Models\Plans\PlanStatus;
use App\Models\Plans\PlanUserPeriod;
use App\Models\Users\User;
use App\Observers\Plans\PlanUserObserver;
use App\Transformers\PlanUserTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/** [PlanUser description] */
class PlanUser extends Model
{
  use SoftDeletes;

  protected $table = 'plan_user';
  protected $dates = ['deleted_at','start_date', 'finish_date'];
  protected $fillable = ['start_date', 'finish_date', 'amount',
  'counter', 'plan_status_id', 'discount_id', 'plan_id', 'user_id'];
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
   * [getStartDateAttribute description]
   * @param  [type] $value [description]
   * @return [type]        [description]
   */
  public function getStartDateAttribute($value)
  {
      return Carbon::parse($value);
  }

  /**
   * [getFinishDateAttribute description]
   * @param  [type] $value [description]
   * @return [type]        [description]
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

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }

    public function planStatus()
    {
        return $this->belongsTo(PlanStatus::class,'plan_status_id');
    }
}

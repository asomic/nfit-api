<?php

namespace App\Models\Bills;

use Carbon\Carbon;
use App\Models\Users\User;
use App\Models\Plans\PlanUser;
use App\Models\Bills\Installment;
use App\Models\Bills\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * [Bill description]
 */
class Bill extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at','date'];
    protected $fillable = ['payment_type_id', 'plan_user_id', 'date', 'start_date', 'finish_date', 'detail', 'amount'];
    protected $appends = ['date_formated'];

    public function getDateFormatedAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
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
     * [payment_type description]
     * @method payment_type
     * @return [model]       [description]
     */
    public function payment_type()
    {
      return $this->belongsTo(PaymentType::class);
    }

    /**
     * [user description]
     * @method user
     * @return [type] [description]
     */
    // public function user()
    // {
    //     return $this->hasManyThrough('App\Models\Users\User',
    //                                  'App\Models\Plans\PlanUser', 'user_','user_id');
    //     // return $this->belongsToMany(User::class);
    // }

    public function plan_user()
    {
        return $this->belongsTo('App\Models\Plans\PlanUser');
    }
}

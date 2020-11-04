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

    /**
     *  Date formats for columns
     *
     *  @var  array
     */
    protected $dates = ['deleted_at', 'date'];
    
    /**
     *  Massive assignment for this model
     *
     *  @var  array
     */
    protected $fillable = [
        'payment_type_id', 'plan_user_id', 'date',
        'start_date', 'finish_date', 'detail', 'amount'
    ];

    /**
     *  Variable to be append to querys
     *
     *  @var  array
     */
    protected $appends = ['date_formated'];

    /**
     *  To display into the view with a "human date format" 
     *
     *  @param   String  $value
     * 
     *  @return  Carbon\Carbon
     */
    public function getDateFormatedAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    // /**
    //  *  Convert from UTC to user timezone
    //  *
    //  *  @param   string|null  $value
    //  * 
    //  *  @return  Carbon\Carbon
    //  */
    // public function getDateAttribute($value)
    // {
    //     if ($value) {
    //         return NfitTimeZone::adjustToTimeZoneDate($value)->format('Y-m-d');
    //     }
    // }

    // /**
    //  *  Calculate the user timezone and parse to UTC time to storage in the database 
    //  *
    //  *  @param   string|Carbon
    //  * 
    //  *  @return  void
    //  */
    // public function setDateAttribute($value)
    // {
    //     $this->attributes['date'] = NfitTimeZone::adjustDateToUTC($value);
    // }

    /**
     *  Convert from UTC to user timezone and get it with a "human date format" 
     *
     *  @param   String|null  $value
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
     *  Calculate the user timezone and parse to UTC time to storage in the database 
     *
     *  @param   string|Carbon
     * 
     *  @return  void
     */
    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = NfitTimeZone::adjustDateToUTC($value);
    }

    /**
     *  Convert from UTC to user timezone and get it with a "human date format" 
     *
     *  @param   String|null  $value
     * 
     *  @return  Carbon\Carbon
     */
    public function getFinishDateAttribute($value)
    {
        if ($value) {
            return NfitTimeZone::adjustToTimeZoneDate($value);
        }
    }

    /**
     *  Calculate the user timezone and parse to UTC time to storage in the database 
     *
     *  @param   string|Carbon
     * 
     *  @return  void
     */
    public function setFinishDateAttribute($value)
    {
        $this->attributes['finish_date'] = NfitTimeZone::adjustDateToUTC($value);
    }

    /**
     *  [installments description]
     * 
     *  @method  installments
     *  
     *  @return [model]       [description]
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

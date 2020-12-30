<?php

namespace App\Models\Users;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\System\NfitTimeZone;

class Alert extends Model
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $dates = ['from','to'];
    
    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $fillable = ['message', 'from', 'to'];

    /**
     *  Convert from UTC to user timezone and get it with a "human date format" 
     *
     *  @param   String  $value
     * 
     *  @return  Carbon\Carbon
     */
    public function getFromAttribute($value)
    {
        return NfitTimeZone::adjustToTimeZoneDate($value)->format('d-m-Y');
    }

    /**
     *  Convert from UTC to user timezone and get it with a "human date format" 
     *
     *  @param   String  $value
     * 
     *  @return  Carbon\Carbon
     */
    public function getToAttribute($value)
    {
        return NfitTimeZone::adjustToTimeZoneDate($value)->format('d-m-Y');
    }
}

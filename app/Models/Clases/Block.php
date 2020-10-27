<?php

namespace App\Models\Clases;

use App\Models\Users\User;
use App\Models\Clases\Clase;
use App\Models\System\NfitTimeZone;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $table = 'blocks';
    
    protected $fillable = [
        'start', 'end', 'dow', 'title',
        'date', 'profesor_id', 'block_type_id'
    ];
    
    /**
     *  Array of appendable data to block querys
     *
     *  @var  array
     */
    protected $appends = ['plans_id'];
    // protected $with = array('plans');

    
    /**
     *  Adjust from UTC time to user local timezone
     *
     *  @return  Carbon\Carbon
     */
    public function getDateAttribute($value)
    {
        if ($value) {
            return NfitTimeZone::adjustToTimeZoneDate($value)->format('Y-m-d');
        }
    }

    /**
     *  Calculate the user timezone and parse to UTC time to storage in the database 
     *
     *  @param   string|Carbon
     * 
     *  @return  void
     */
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = NfitTimeZone::adjustDateToUTC($value);
    }
    
    /**
     *  Transformamos el valor de dow a un arreglo para fullcalendar
     *
     *  @param   string $value
     * 
     *  @return  array
     */
    public function getDowAttribute($value)
    {
        $array = [];
        array_push($array,$value);
        return $array;
    }

    /**
     *  Adjust from UTC time to user local timezone
     * 
     *  @param   string
     *
     *  @return  Carbon\Carbon
     */
    public function getStartAttribute($value)
    {
        if (isset($this->attributes['date']) && $this->attributes['date'] !== null) {
            return Carbon::parse($this->attributes['date'])->format('Y-m-d') . ' ' . NfitTimeZone::adjustToTimeZoneDate($value)->format('H:i');
        }

        return NfitTimeZone::adjustToTimeZoneDate($value)->format('H:i');
    }

    /**
     *  Calculate the user timezone and parse to UTC time to storage in the database 
     *
     *  @param   string|Carbon
     * 
     *  @return  void
     */
    public function setStartAttribute($value)
    {
        $this->attributes['start'] = NfitTimeZone::adjustDateToUTC($value);
    }

    /**
     *  Adjust from UTC time to user local timezone
     * 
     *  @param   string
     *
     *  @return  Carbon\Carbon
     */
    public function getEndAttribute($value)
    {
        if (isset($this->attributes['date']) && $this->attributes['date'] !== null) {
            return Carbon::parse($this->attributes['date'])->format('Y-m-d') . ' ' . NfitTimeZone::adjustToTimeZoneDate($value)->format('H:i');
        }

        return NfitTimeZone::adjustToTimeZoneDate($value)->format('H:i');
    }

    /**
     *  Calculate the user timezone and parse to UTC time to storage in the database 
     *
     *  @param   string|Carbon
     * 
     *  @return  void
     */
    public function setEndAttribute($value)
    {
        $this->attributes['end'] = NfitTimeZone::adjustDateToUTC($value);
    }

    /**
     *  Relationship to this Model
     *
     *  @return  App\Models\Plans\Plan
     */
    public function plans()
    {
        return $this->belongsToMany('App\Models\Plans\Plan', 'block_plan');
    }

    /**
     *  Relationship to this Model
     *
     *  @return  App\Models\Users\User
     */
    public function user()
    {
        return $this->belongsTo(User::class,'profesor_id');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getPlansIdAttribute()
    {
        return $this->plans->pluck('id');
    }

    /**
     *  Relationship to this Model
     *
     *  @return  App\Models\Clases\Clase
     */
    public function clases()
    {
        return $this->hasMany(Clase::class);
    }
}

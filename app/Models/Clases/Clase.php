<?php

namespace App\Models\Clases;

use Auth;
use Carbon\Carbon;
use App\Models\Users\User;
use App\Models\Clases\Clase;
use App\Models\Exercises\Stage;
use App\Models\Clases\Reservation;
use App\Models\System\NfitTimeZone;
use App\Transformers\ClaseTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clase extends Model
{
    use SoftDeletes;

    /**
     *  Table name on Database
     *
     *  @var  string
     */
    protected $table = 'clases';

    /**
     *  Massive Assignment for this Model
     *
     *  @var  array
     */
    protected $fillable = [
        'date', 'start_at', 'finish_at',
        'room', 'profesor_id', 'quota',
        'block_id', 'clase_type_id', 'zoom_link'
    ];

    /**
     *  [$dates description]
     *
     *  @var  array
     */
    protected $dates = ['date', 'deleted_at'];

    /**
     *  values to append on querys
     *
     *  @var  array
     */
    protected $appends = ['start','end','url','reservation_count'];

    /**
     *  Undocumented variable
     *
     *  @var  [type]
     */
    public $transformer = ClaseTransformer::class;

    /**
     *  Undocumented function
     *
     *  @return  void
     */
    protected static function boot() {
        parent::boot();
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
    //     return NfitTimeZone::adjustToTimeZoneDate($value);
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
     *  Convert from UTC to user timezone and display to hour and minute format
     *
     *  @param   string|null  $value
     *
     *  @return  Carbon\Carbon
     */
    public function getStartAtAttribute($value)
    {
        return NfitTimeZone::adjustToTimeZoneDate($value)->format('H:i:s');
    }

    /**
     *  Calculate the user timezone and parse to UTC time to storage in the database
     *
     *  @param   string|Carbon
     *
     *  @return  void
     */
    public function setStartAtAttribute($value)
    {
        $this->attributes['start_at'] = NfitTimeZone::adjustDateToUTC($value);
    }

    /**
     *  Convert from UTC to user timezone and display to hour and minute format
     *
     *  @param   string|null  $value
     *
     *  @return  Carbon\Carbon
     */
    public function getFinishAtAttribute($value)
    {
        return NfitTimeZone::adjustToTimeZoneDate($value)->format('H:i:s');
    }

    /**
     *  Calculate the user timezone and parse to UTC time to storage in the database
     *
     *  @param   string|Carbon
     *
     *  @return  void
     */
    public function setFinishAtAttribute($value)
    {
        $this->attributes['finish_at'] = NfitTimeZone::adjustDateToUTC($value);
    }

    /**
     *  Reservation relation to this model
     *
     *  @return  App\Models\Clases\Reservation
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     *  Check if the authenticated user took this class
     *
     *  @return  boolean
     */
    public function authReservedThis()
    {
        return Reservation::where('user_id', Auth::user()->id)
                            ->where('clase_id', $this->id)
                            ->exists('id');
    }

    /**
     *  Check plans and reservations for the authenticated user if he can reserve this Clase
     *
     *  @return  boolean
     */
    public function auth_can_reserve()
    {
        $user = Auth::User();
        $clase_type = $this->claseType;

        $clases = Clase::where('date', $this->date)->pluck('id');
        $auth_reservations = $user->reservations()->whereIn('clase_id', $clases)->get();
        $auth_plan = Auth::user()->active_planuser();

        foreach ($auth_reservations as $res) {
            if($clase_type->id == $res->clase->clase_type_id ) {
                return false;
            }
        }
      // foreach ($clases as $clase) {
      //     $reservations = Reservation::where('user_id', Auth::id())->where('clase_id', $clase->id)->get();
      //     $reservations_clase_type = $reservations->pluck('clase_types');

      //     $clase_count = $clase_count + count($reservations);
      //     if($clase_count >= $auth_plan->daily_clases)
      //     {
      //       return false;
      //     }
      //     if(in_array($clase_type->id,$reservations_clase_type ))
      //     {
      //       return false;
      //     }
      // }

      //maximo de 3 usuarios de prueba
    //   if(Auth::user()->status_user == 3){

    //     $pruebaCount = 0;
    //     foreach ($this->users as $user) {
    //       if($user->status_user == 3){
    //         $pruebaCount++;
    //       }
    //     }
    //     if($pruebaCount >= 3)
    //     {
    //       return false;
    //     }
    //   }



        $planUser = Auth::user()->plan_users()->where('start_date', '<=', $this->date)
                                                ->where('finish_date', '>=', $this->date)
                                                ->whereIn('plan_status_id', [1, 3])
                                                ->first();

        if ($planUser) {
            if(count($auth_reservations) >= $planUser->plan->daily_clases) {
                return false;
            }

            $ids = $this->block->getPlansIdAttribute()->toArray();
            if((in_array($planUser->plan_id,$ids)) && ($planUser->counter > 0 ) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     *  Staege relation to this model
     *
     *  @return  App\Models\Excercises\Stage
     */
    public function stages()
    {
        return $this->belongsTo(Stage::class);
    }

    /**
     *  ClaseType relation to this model
     *
     *  @return  App\Models\Clases\ClaseType
     */
    public function claseType()
    {
        return $this->belongsTo('App\Models\Clases\ClaseType');
    }

    /**
     *  User relation to this model
     *
     *  @return  App\Models\Users\User
     */
    public function users()
    {
        return $this->belongsToMany(User::Class, 'reservations', 'clase_id');
    }

    /**
     *  Undocumented function
     *
     *  @return  void
     */
    public function profesor()
    {
        return $this->belongsTo(User::Class, 'profesor_id');
    }

    /**
     *  Undocumented function
     *
     *  @return  void
     */
    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    /**
     *  Undocumented function
     *
     *  @return  void
     */
    public function pruebaUsersCount()
    {
        $users = $this->belongsToMany(User::Class)->using(Reservation::class)->get();
        $count = 0;
        foreach ($users as  $user) {
            $count++;
        }

        return $count;
    }

    /**
     *  Undocumented function
     *
     *  @return  void
     */
    public function getReservationCountAttribute()
    {
        return $this->hasMany(Reservation::class)->count();
    }

    /**
     *  Undocumented function
     *
     *  @return void
     */
    public function getStartAttribute()
    {
        return "{$this->date} {$this->start_at}";
    }

    /**
     *  Undocumented function
     *
     *  @return  string
     */
    public function getEndAttribute()
    {
        return "{$this->date} {$this->finish_at}";
    }

    /**
     *  Undocumented function
     *
     *  @return  string
     */
    public function getUrlAttribute()
    {
        return url("clases/{$this->id}");
    }

    /**
     *  If database start at class is after than right now
     *  and the total students numbes allowed in the class,
     *  is greater than the total of user reserved in the class yet,
     *  the class can still recieve students
     *
     *  @return  boolean
     */
    public function stillActive()
    {
        $dateTimeStartClase = $this->dateTimeThisHour($this->getOriginal('start_at'));

        return $dateTimeStartClase > now()->copy()->format('Y-m-d H:i:s');
    }

    public function dateTimeThisHour($hour)
    {
        return Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $this->date->format('Y-m-d') . ' ' . $hour
        );
    }

    /**
     *  Check if right now is after than the end class hour
     *
     *  @return  boolean
     */
    public function hasFinished()
    {
        $dateTimeFinishClase = $this->dateTimeThisHour($this->getOriginal('finish_at'));

        return $dateTimeFinishClase < now()->copy()->format('Y-m-d H:i:s');
    }
}

<?php

namespace App\Models\Clases;

use Carbon\Carbon;
use App\Models\Users\User;
use App\Models\Exercises\Stage;
use App\Models\Clases\Reservation;
use App\Models\System\NfitTimeZone;
use Illuminate\Support\Facades\Auth;
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

    /**
     *  Convert from UTC to user timezone and display to hour and minute format
     *
     *  @param   string|null  $value
     *
     *  @return  string       '19:00'
     */
    public function getStartAtAttribute($value)
    {
        return NfitTimeZone::changeTimeAccordingAuthTimezone($value);
    }

    /**
     *  Convert string hour to timezone user and display to hour and minute format
     *
     *  @param   string|null  $value
     *
     *  @return  string       '20:00'
     */
    public function getFinishAtAttribute($value)
    {
        return NfitTimeZone::changeTimeAccordingAuthTimezone($value);
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
        $user = Auth::user();
        $clase_type = $this->claseType;

        $clases = Clase::where('date', $this->date)->pluck('id');
        $auth_reservations = $user->reservations()->whereIn('clase_id', $clases)->get();
        $auth_plan = Auth::user()->active_planuser();

        foreach ($auth_reservations as $res) {
            if($clase_type->id == $res->clase->clase_type_id ) {
                return false;
            }
        }

        $planUser = Auth::user()->plan_users()->where('start_date', '<=', $this->date)
                                                ->where('finish_date', '>=', $this->date)
                                                ->whereIn('plan_status_id', [1, 3])
                                                ->first();
        if ($planUser) {
            if(count($auth_reservations) >= $planUser->plan->daily_clases) {
                return false;
            }
            if (!$this->block) {
                return false;
            }
            $ids = $this->block->getPlansIdAttribute()->toArray();

            // dd($planUser->counter);
            if((in_array($planUser->plan_id, $ids)) && ($planUser->counter > 0 ) ) {
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
        return $this->belongsToMany(User::class, 'reservations', 'clase_id');
    }

    /**
     *  Undocumented function
     *
     *  @return  void
     */
    public function profesor()
    {
        return $this->belongsTo(User::class, 'profesor_id');
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
        $users = $this->belongsToMany(User::class)->using(Reservation::class)->get();
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
        $timezone = auth()->user()->timezone ?? 'America/Santiago';

        $dateTimeStartClase = $this->dateTimeThisHour($this->start_at, $timezone);

        return $dateTimeStartClase > now($timezone)->copy()->format('Y-m-d H:i:s');
    }

    /**
     *
     */
    public function dateTimeThisHour($hour, $timezone)
    {
        return Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $this->date->format('Y-m-d') . ' ' . $hour,
            $timezone
        );
    }

    /**
     *  Check if right now is after than the end class hour
     *
     *  @return  boolean
     */
    public function hasFinished()
    {
        $timezone = auth()->user()->timezone ?? 'America/Santiago';

        $dateTimeFinishClase = $this->dateTimeThisHour($this->finish_at, $timezone);

        return now($timezone)->copy() > $dateTimeFinishClase;
    }
}

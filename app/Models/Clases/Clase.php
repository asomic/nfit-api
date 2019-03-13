<?php

namespace App\Models\Clases;

use App\Models\Users\User;
use App\Models\Exercises\Stage;
use App\Models\Clases\Reservation;
use App\Models\Clases\Clase;
use App\Transformers\ClaseTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

/**
 * [Clase description]
 */
class Clase extends Model
{
    use SoftDeletes;

    protected $table = 'clases';
    protected $dates = ['date','deleted_at'];
    protected $fillable = ['date', 'start_at', 'finish_at', 'room', 'profesor_id', 'quota' ,'block_id'];
    protected $appends = ['start','end','url','reservation_count'];

    public $transformer = ClaseTransformer::class;

    protected static function boot() {
      parent::boot();
    }

    /**
     * [reservations description]
     * @return [type] [description]
     */
    public function reservations()
    {
      return $this->hasMany(Reservation::class);
    }

    public function auth_has_reservation()
    {
      $exist = Reservation::where('user_id',Auth::user()->id)->where('clase_id',$this->id)->first();
      if($exist)
      {
        return true;
      } else {
        return false;
      }
    }



    public function auth_can_reserve()
    {
      //maximo de 3 usuarios de prueba
      $clases = Clase::where('date', $this->$date)->get();
      foreach ($clases as $clase) {
          $reservations = Reservation::where('user_id', Auth::id())->where('clase_id', $clase->id)->get();
          if (count($reservations) != 0) {
              return false;
          }
      }
    
      if(Auth::user()->status_user_id == 3){

        $pruebaCount = 0;
        foreach ($this->users as $user) {
          if($user->status_user_id == 3){
            $pruebaCount++;
          }
        }
        if($pruebaCount >= 3)
        {
          return false;
        }
      }

      $planUser = Auth::user()->plan_users()->where('start_date', '<=', $this->date)->where('finish_date', '>=', $this->date)->first();

      if($planUser)
      {
          $ids = $this->block->getPlansIdAttribute()->toArray();
          if((in_array($planUser->plan_id,$ids)) && ($planUser->counter > 0 ) )
          {

            return true;

          } else {
            return false;
          }
      } else {
        return false;
      }

    }


    // public function auth_get_reservation()
    // {
    //   return  Reservation::where('user_id',Auth::user()->id)->where('clase_id',$this->id)->first();
    // }

    /**
     * [stages description]
     * @return [type] [description]
     */
    public function stages()
    {
      return $this->belongsToMany(Stage::class);
    }

    /**
     * [users description]
     * @return [type] [description]
     */
    public function users()
    {
      return $this->belongsToMany(User::Class, 'reservations','clase_id');
    }



    public function profresor()
    {
        return $this->morphMany('App\Models\Users\User', 'userable');
    }

    public function profesor()
    {
    return $this->belongsToMany(User::Class)->using(Reservation::class);
    }

    public function block()
    {
      return $this->belongsTo(Block::class);
    }

    public function pruebaUsersCount()
    {
      $users = $this->belongsToMany(User::Class)->using(Reservation::class)->get();
      $count = 0;
      foreach ($users as  $user) {
        $count++;
      }
      return $count;
    }

    //set y get


    public function getReservationCountAttribute()
    {
      return $this->hasMany(Reservation::class)->count();
    }

    public function getStartAttribute()
    {
      return $this->date.' '.$this->block->start;
    }

    public function getEndAttribute()
    {
      return $this->date.' '.$this->block->end;
    }

    public function getUrlAttribute()
    {
      return url('clases/'.$this->id);
    }
}

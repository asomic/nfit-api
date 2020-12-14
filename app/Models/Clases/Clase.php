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


      $user = Auth::User();

      $clase_type = $this->claseType;
      $clases = Clase::where('date', $this->date)->pluck('id');
      $auth_reservations = $user->reservations()->whereIn('clase_id',$clases)->get();
      $auth_plan = Auth::user()->active_planuser();

      foreach ($auth_reservations as $res) {
        if($clase_type->id == $res->clase->clase_type_id )
        {
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

      

      $planUser = Auth::user()->plan_users()->where('start_date', '<=', $this->date)->where('finish_date', '>=', $this->date) ->whereIn('plan_status_id', [1, 3])->first();
      


      if($planUser)
      {
          if(count($auth_reservations) >= $planUser->plan->daily_clases)
          {
            return false;
          }

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
      return $this->belongsTo(Stage::class);
    }


    /**
     * [stages description]
     * @return [type] [description]
     */

    public function claseType()
    {
      return $this->belongsTo('App\Models\Clases\ClaseType');
    }
 

    /**
     * [users description]
     * @return [type] [description]
     */
    public function users()
    {
      return $this->belongsToMany(User::Class, 'reservations','clase_id');
    }



    // public function profresor()
    // {
    //     return $this->morphMany('App\Models\Users\User', 'userable');
    // }

    public function profesor()
    {
      return $this->belongsTo(User::Class, 'profesor_id');
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

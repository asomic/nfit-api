<?php

namespace App\Models\Users;

use App\Models\Bills\Bill;
use App\Models\Bills\Installment;
use App\Models\Clases\Block;
use App\Models\Clases\Clase;
use App\Models\Clases\Reservation;
use App\Models\Plans\Plan;
use App\Models\Plans\PlanStatus;
use App\Models\Plans\PlanUser;
use App\Models\Users\Emergency;
use App\Models\Users\Millestone;
use App\Models\Users\Role;
use App\Notifications\MyResetPassword;
use App\Transformers\UserTransformer;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * [User description]
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'rut', 'first_name', 'last_name', 'birthdate',
        'gender', 'email', 'address', 'password', 'phone',
        'emergency_id', 'status_user', 'tutorial'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $dates = ['deleted_at'];

    protected $appends = ['full_name'];

    public $transformer = UserTransformer::class;

    /**
    * Send the password reset notification.
    *
    * @param  string  $token
    * @return void
    */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MyResetPassword($token));
    }

    /**
     * [getFullNameAttribute description]
     * @return [type] [description]
     */
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     *  Check if user has the specific Role
     *
     *  @param   int      $role
     *
     *  @return  boolean
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * [regular_users description]
     * @return [collection] [description]
     */
    public function regular_users()
    {
        return User::all()->where('admin', 'false')->orderBy('name');
    }

    /**
     * [active_users description]
     * @return [type] [description]
     */
    public function active_users()
    {
        return $this->where('status_user', 1);
    }

    /**
     * [blocks description]
     * @return [type] [description]
     */
    public function blocks()
    {
        return $this->hasMany(Block::class);
    }

    /**
     *  Get all the clases of this User through reservations table
     *
     *  @return  belongsToMany
     */
    public function clases()
    {
        return $this->belongsToMany(Clase::class, 'reservations', 'user_id')
                    ->withPivot('reservation_status_id');
    }

    /**
     * [emergency description]
     * @method emergency
     * @return [Model]    [description]
     */
    public function emergency()
    {
      return $this->belongsTo(Emergency::class)->withDefault();
    }

    /**
    * [status_user description]
    * @method status_user
    * @return [Model]      [description]
    */

    /**
     * [installments description]
     * @return [type] [description]
     */
    public function installments()
    {
        return $this->hasManyThrough(
            Installment::class,
            PlanUser::class,
            'user_id',
            'plan_user_id'
        );
    }

    public function bills()
    {
        return $this->hasManyThrough(
            Bill::class,
            PlanUser::class,
            'user_id',
            'plan_user_id'
        );
    }

    /**
    * [millestones description]
    * @method millestones
    * @return [Model]      [description]
    */
    public function millestones()
    {
      return $this->belongsToMany(Millestone::class);
    }

    /**
    * [plans description]
    * @return [type] [description]
    */
    public function plans()
    {
      return $this->belongsToMany(Plan::class)->using(PlanUser::class);
    }

    /**
    *  metodo  para obtener el plan activo del usuario
    *
    *  @return [type] [description]
    */
    public function active_planuser()
    {
        return PlanUser::where('user_id', $this->id)
                        ->where('plan_status_id', PlanStatus::ACTIVO)
                        ->first();
    }

    // public function actual_planuser()
    // {
    //   $active = PlanUser::where('user_id', $this->id)
    //                       ->where('plan_status_id','!=', 1)
    //                       ->first();
    //   return $active;
    // }


    /**
    * [status_user description]
    * @return [model] [description]
    */
    public function plan_users()
    {
      return $this->hasMany(PlanUser::class)->orderBy('plan_status_id', 'asc');
    }

    /**
    * [reservations description]
    * @method reservations
    * @return [Model]       [description]
    */

    public function reservations($status = null )
    {
      if($status!=null){
        return $this->hasMany(Reservation::class)->where('reservation_status_id', $status);
      } else {
        return $this->hasMany(Reservation::class);
      }
    }

    public function assistence()
    {
      return $this->belongsToMany(
          Clase::class,
          'reservations',
          'user_id',
          'clase_id'
      )->wherePivot('reservation_status_id', 3)->where('date','>=',date("Y").'-01-01');
    }

    /**
     * [roles description]
     * @return [type] [description]
     */
    public function roles()
    {
      return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * [status_user description]
     * @return [type] [description]
     */
    // public function status_user()
    // {
    //   return $this->belongsTo(StatusUser::class);
    // }

    // public function dateReservations($date)
    // {

    //     return $this->hasMany(Reservation::class)->where('reservation_status_id', $status);

    // }

}

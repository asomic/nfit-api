<?php

namespace App\Models\Users;

use App\Models\Bills\Bill;
use App\Models\Plans\Plan;
use App\Models\Users\Role;
use App\Models\Clases\Block;
use App\Models\Clases\Clase;
use App\Models\Plans\PlanUser;
use App\Models\Plans\PlanStatus;
use App\Models\Users\StatusUser;
use App\Models\Clases\Reservation;
use Laravel\Passport\HasApiTokens;
use App\Transformers\UserTransformer;
use App\Notifications\MyResetPassword;
use Illuminate\Notifications\Notifiable;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, UsesTenantConnection, SoftDeletes;

    /**
     * [$transformer description]
     *
     * @var [type]
     */
    public $transformer = UserTransformer::class;

    /**
     * [$dates description]
     *
     * @var [type]
     */
    protected $dates = ['birthdate', 'since', 'deleted_at'];

    /**
     * Massive Assignment for this Model
     *
     * @var array
     */
    protected $fillable = [
        'rut', 'first_name', 'last_name',
        'email', 'password', 'avatar', 'phone',
        'birthdate', 'gender', 'address',
        'since', 'emergency_id', 'status_user'
    ];

    /**
     * [$hidden description]
     *
     * @var [type]
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * [$appends description]
     *
     * @var array
     */
    protected $appends = ['full_name'];

    /**
     * [getFullNameAttribute description]
     *
     * @return [type] [description]
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * [hasRole description]
     * @param  [type]  $role [description]
     * @return boolean       [description]
     */
    public function hasRole($role)
    {
        $role = RoleUser::where('role_id', $role)->where('user_id', $this->id)->get();

        if (count($role) > 0) {
            return true;
        }
        return false;
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
      return $this->where('status_user_id', 1);
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
    * [clases description]
    * @return [type] [description]
    */
    public function clases()
    {
      //return $this->belongsToMany(Clase::Class)->using(Reservation::class);
      return $this->belongsToMany(Clase::Class, 'reservations', 'user_id')->withPivot('reservation_status_id');
      //return $this->hasManyThrough(Clase::Class, Reservation::class);
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
    * metodo  para obtener el plan activo del usuario
    * @return [type] [description]
    */
    public function active_planuser()
    {
        $active = PlanUser::where('user_id', $this->id)
                          ->where('plan_status_id', PlanStatus::ACTIVE)
                          ->first();

        return $active;
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
        if ($status != null) {
            return $this->hasMany(Reservation::class)->where('reservation_status_id', $status);
        }

        return $this->hasMany(Reservation::class);
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
    public function status_user()
    {
      return $this->belongsTo(StatusUser::class);
    }
}

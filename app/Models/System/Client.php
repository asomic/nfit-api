<?php

namespace App\Models\System\Clients;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
// use Freshwork\ChileanBundle\Rut;
use App\Models\System\Clients\ClientStatus;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes, UsesSystemConnection;

    /**
     * [$dates description]
     *
     * @var [type]
     */
    protected $dates = ['birthdate', 'deleted_at'];

    /**
     * Massive Assignment for this Model
     *
     * @var array
     */
    protected $fillable = [
        'rut', 'first_name', 'last_name', 'box_name',
        'database_uuid', 'email', 'phone', 'birthdate',
        'address', 'lat', 'lng', 'status_client', 'hostname_id'
    ];

    /**
     * [$appends description]
     *
     * @var array
     */
    protected $appends = [
        'rut_formated',
        'full_name',
        'type_user',
        'status_client_name'
    ];

    /**
     * Set birthdate value to American type
     *
     * @param [type] $value [description]
     */
    public function setBirthdateAttribute($value)
    {
        $this->attributes['birthdate'] = Carbon::parse($value)->format('Y-m-d');
    }

    /**
     * [setRutAttribute description]
     *
     * @param [type] $value [description]
     */
    // public function setRutAttribute($value)
    // {
    //     $this->attributes['rut'] = Rut::parse($value)->number();
    // }

    /**
     * getRutAttribute
     *
     * @return [type] [description]
     */
    // public function getRutFormatedAttribute()
    // {
    //     return Rut::set($this->rut)->fix()->format();
    // }

    /**
     * Being called, it's return the full client name
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
    public function getTypeUserAttribute()
    {
        return app(ClientStatus::class)->getType($this->attributes['status_client']);
    }

    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
    public function getStatusClientNameAttribute()
    {
        return app(ClientStatus::class)->getTypeName($this->attributes['status_client']);
    }

    /**
     * Get the client status
     *
     * @return  Illuminate\Database\Eloquent\Model
     */
    public function statusClient()
    {
        return $this->hasOne(ClientStatus::class);
    }

    /**
     * [scopeCountStatusUsers description]
     *
     * @param  query $users
     *
     * @return Collection
     */
    public function scopeCountStatusClients($users)
    {
        return $users->groupBy('status_client')
                     ->selectRaw('count(*) as total, status_client');
    }
}

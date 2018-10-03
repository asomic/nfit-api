<?php

namespace App\Models\Clases;

use App\Models\Users\User;
use App\Models\Clases\Clase;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
  protected $table = 'blocks';
  protected $fillable = ['start', 'end', 'dow', 'title'];
  protected $appends = ['plans_id'];
  // protected $with = array('plans');

  //transformamos el valor de dow a un arraglo para fullcalendar
  public function getDowAttribute($value)
  {
    $array = [];
    array_push($array,$value);
    return $array;
  }

  public function plans()
  {
    return $this->belongsToMany('App\Models\Plans\Plan', 'block_plan');
  }

  public function user()
  {
    return $this->belongsTo(User::class,'profesor_id');
  }

  public function getPlansIdAttribute()
  {
    return $this->plans->pluck('id');
  }

  public function clases()
  {
    return $this->hasMany(Clase::class);
  }

}

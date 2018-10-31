<?php

namespace App\Models\Wods;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clases\Clase;
use App\Transformers\WodTransformer;

class Wod extends Model
{

  public $transformer = WodTransformer::class;

  protected $fillable = ['date','clase_type_id'];
  protected $appends = ['start','allDay','title','url'];


  public function clases()
  {
    return $this->hasMany(Clase::class);
  }

  public function stages()
  {
    return $this->hasMany(Stage::class);
  }

  //etapa por id
  public function stage($id)
  {
    return $this->hasMany(Stage::class)->where('stage_type_id',$id)->first() ?? null;
  }


  public function getAllDayAttribute()
  {
    return true;
  }

  public function getStartAttribute()
  {
    return $this->date;
  }
  public function getTitleAttribute()
  {
    return 'WorkOut';
  }
  public function getUrlAttribute()
  {
    return url('/wods/'.$this->id.'/edit');
  }


}

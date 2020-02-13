<?php

namespace App\Models\Wods;


use Illuminate\Database\Eloquent\Model;
use App\Models\Clases\Clase;
use App\Models\Clases\ClaseStage;
use App\Models\Wods\StageType;
use App\Models\Wods\Wod;
use App\Transformers\StageTransformer;

/**
 * [Stage description]
 */
class Stage extends Model
{
  public $transformer = StageTransformer::class;
  protected $fillable = ['wod_id','stage', 'stage_type_id', 'name', 'description', 'star'];

  public function wod()
  {
    return $this->belongsTo(Wod::class);
  }

  public function stage_type()
  {
    return $this->belongsTo(StageType::class);
  }
}

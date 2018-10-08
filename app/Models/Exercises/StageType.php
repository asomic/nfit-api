<?php

namespace App\Models\Exercises;

use App\Models\Exercises\Stage;
use Illuminate\Database\Eloquent\Model;

class StageType extends Model
{
  protected $fillable = ['stage_type'];

  /**
   * [plans description]
   * @return [type] [description]
   */
  public function stages()
  {
      return $this->hasMany(Stage::class);
  }
}

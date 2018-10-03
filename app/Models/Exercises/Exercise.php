<?php

namespace App\Models\Exercises;

use Illuminate\Database\Eloquent\Model;

/**
 * [Exercise description]
 */
class Exercise extends Model
{
  protected $fillable = ['exercise'];

  /**
   * [stages description]
   * @method stages
   * @return [type] [description]
   */
  public function stages()
  {
    return $this->belongsToMany(Stage::class);
  }
}

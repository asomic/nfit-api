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
     * [stages relation]
     * @return [model] [description]
     */
    public function stages()
    {
       return $this->belongsToMany(Stage::class)->using(ExerciseStage::class);
    }
}

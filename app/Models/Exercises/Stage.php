<?php

namespace App\Models\Exercises;

Use App\Models\Exercises\Exercise;
use App\Models\Exercises\ExerciseStage;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    /**
     *  Massive fillable data
     *
     *  @var  array
     */
    protected $fillable = ['stage'];

    /**
     *  Excercise relationship to this model
     *
     *  @return  Excercise
     */
    public function exercises()
    {
        return $this->belongsToMany(Exercise::class)->using(ExerciseStage::class);
    }
}

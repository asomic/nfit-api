<?php

namespace App\Models;
use Rut;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
  // Mutadores
  public function getRutAttribute($value) //mutador del rut
  {
      return Rut::set($value)->fix()->format();
  }
  // End mutadores
}

<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

/** [Millestone description] */
class Millestone extends Model
{
  protected $fillable = ['millestone'];

  /**
   * [users description]
   * @method users
   * @return [model] [description]
   */
  public function users()
  {
    return $this->belongsToMany(User::class);
  }

}

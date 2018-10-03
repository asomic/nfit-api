<?php

namespace App\Models\Users;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

/** [Emergency description] */
class Emergency extends Model
{
  protected $fillable = ['contact_name', 'contact_phone'];

  /**
   * [user description]
   * @method user
   * @return [model] [description]
   */
  public function user()
  {
    return $this->hasOne(User::class);
  }
}

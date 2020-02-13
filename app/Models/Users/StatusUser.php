<?php

namespace App\Models\Users;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

/**
 * [StatusUser description]
 */
class StatusUser extends Model
{
  protected $fillable = ['status_user'];

  /**
   * [users description]
   * @return [type] [description]
   */
  public function users()
  {
    return $this->hasMany(User::class);
  }
}

<?php

namespace App\Models\Bills;

use App\Models\Bills\Bill;
use App\Models\Plans\PlanUser;
use App\Models\Bills\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/** [Installment description] */
class Installment extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];
  protected $fillable = [
    'bill_id',
    'payment_status_id',
    'commitment_date',
    'expiration_date',
    'payment_date'.
    'amount',
  ];
  /**
   * [bill description]
   * @return [type] [description]
   * @method bill
   */
  public function bill()
  {
    return $this->belongsTo(Bill::class);
  }

  /**
   * [payment_status description]
   * @method payment_status
   * @return [type]         [description]
   */
  public function payment_status()
  {
    return $this->belongsTo(PaymentStatus::class);
  }

  /**
   * [plan_cliente description]
   * @method plan_cliente
   * @return [type]       [description]
   */
  public function plan_user()
  {
    return $this->belongsTo(PlanUser::class);
  }

}

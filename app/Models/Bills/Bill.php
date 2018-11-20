<?php

namespace App\Models\Bills;

use App\Models\Users\User;
use App\Models\Bills\Installment;
use App\Models\Bills\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Transformers\BillTransformer;
// use App\Models\Bills\Payment_status;

/**
 * [Bill description]
 */
class Bill extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = ['payment_type_id', 'user_id',
    'date', 'detail', 'amount', 'sub_total', 'total'];

    public $transformer = BillTransformer::class;

    /**
     * [installments description]
     * @method installments
     * @return [model]       [description]
     */
    public function installments()
    {
      return $this->hasMany(Installment::class);
    }

    /**
     * [payment_type description]
     * @method payment_type
     * @return [model]       [description]
     */
    public function payment_type()
    {
      return $this->belongsTo(PaymentType::class);
    }

    /**
     * [user description]
     * @method user
     * @return [type] [description]
     */
    public function user()
    {
      return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Transformers;

use App\Models\Bills\Bill;
use League\Fractal\TransformerAbstract;

class BillTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Bill $bill)
    {
        return [
            'bill_id' => (int)$bill->id,
            'paymenty_type' => (string)$bill->payment_type->payment_type,
            'plan' => $bill->plan->plan,

        ];
    }

    /**
     * [originalAttribute changes the faced version to the original]
     *
     * @return [array]        [description]
     */
    public static function originalAttribute($index)
    {
        $attributes = [
            'bill_id' => 'id',


        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /**
     * [transformedAttribute changes the original attributes to the faced]
     * @return [type]        [description]
     */
    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'bill_id',

        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

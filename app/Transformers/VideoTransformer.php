<?php

namespace App\Transformers;


use App\Models\Videos\Video;
use League\Fractal\TransformerAbstract;
use Carbon\Carbon;
use Auth;

class ClaseTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Video $video)
    {

        return [
            'id' => (int)$video->id,

        ];
    }

    /**
     *  Undocumented function
     *
     *  @param   [type] $index
     * 
     *  @return  array|null
     */
    public static function originalAttribute($index)
    {
        $attributes = [
            'id' => 'id',

        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    /**
     *  Undocumented function
     *
     *  @param  [type] $index
     *  
     *  @return  array|null
     */
    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'id',

        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}

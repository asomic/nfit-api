<?php

namespace App\Transformers;


use App\Models\Videos\Video;
use League\Fractal\TransformerAbstract;
use Carbon\Carbon;
use Auth;

class VideoTransformer extends TransformerAbstract
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
            'title' => (string)$video->title,
            'description' => (string)$video->description,
            'duration' => (int)$video->duration, 
            // 'duration_minutes' => (int) ($video->duration)/60,
            'thumbnail_path' => (string) $video->thumbnail_path,
            'url' => (string) 'https://player.vimeo.com/video/'.$video->id,
            'release_raw' => (string) $video->release_at,
            'release_human' => (string)ucfirst($video->release_at->formatLocalized('%A %d')).' de '.ucfirst($video->release_at->formatLocalized('%B')),
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

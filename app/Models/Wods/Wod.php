<?php

namespace App\Models\Wods;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clases\Clase;
use App\Models\Clases\ClaseType;
use App\Transformers\WodTransformer;

class Wod extends Model
{
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    public $transformer = WodTransformer::class;

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $fillable = ['date','clase_type_id'];

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $dates = ['date'];

    /**
     * Undocumented variable
     *
     * @var array
     */
    protected $appends = ['start', 'allDay', 'title', 'url'];

    /**
     * Undocumented function
     *
     * @return void
     */
    public function clases()
    {
        return $this->hasMany(Clase::class);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function stages()
    {
        return $this->hasMany(Stage::class);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function claseType()
    {
        return $this->belongsTo(ClaseType::class);
    }

    /**
     *  Undocumented function
     *
     *  @param   int   $id
     * 
     *  @return  void
     */
    public function stage($id)
    {
        return $this->hasMany(Stage::class)->where('stage_type_id',$id)->first() ?? null;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getAllDayAttribute()
    {
        return true;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getStartAttribute()
    {
        return $this->date;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getTitleAttribute()
    {
        return 'WorkOut';
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getUrlAttribute()
    {
        return url('/wods/'.$this->id.'/edit');
    }
}

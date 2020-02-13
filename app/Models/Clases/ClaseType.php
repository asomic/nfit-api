<?php

namespace App\Models\Clases;

use Illuminate\Database\Eloquent\Model;
use App\Transformers\ClaseTypeTransformer;

class ClaseType extends Model
{
	public $transformer = ClaseTypeTransformer::class;

	public function blocks()
	{
		return $this->hasMany('App\Models\Clases\Block');
    }
    public function clases()
	{
		return $this->hasMany('App\Models\Clases\Clase');
	}
}

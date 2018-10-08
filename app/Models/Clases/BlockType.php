<?php

namespace App\Models\Clases;

use App\Models\Clases\Block;
use Illuminate\Database\Eloquent\Model;

class BlockType extends Model
{
	/**
	 * [blocks description]
	 * @return [model] [return block model]
	 */
	public function blocks()
	{
		return $this->hasMany(Block::class);
	}
}

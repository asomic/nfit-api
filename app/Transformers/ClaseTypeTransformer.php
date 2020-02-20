<?php

namespace App\Transformers;

use App\Models\CLases\ClaseType;
use League\Fractal\TransformerAbstract;

class ClaseTypeTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(ClaseType $clase_type)
    {
        if($clase_type->active == 1){
            $active = true;
        } else {
            $active = false;
        }
        return [
            'id' => (int) $clase_type->id,
            'name' => (string) $clase_type->clase_type,
            'icon' => (string) $clase_type->icon,
            'iconWhite' => (string) $clase_type->icon_white,
            'active' => (boolean) $active,
        ];
    }


}

<?php

namespace App\Http\Controllers\Wods;

use Auth;
use Session;
use App\Models\Wods\Wod;
use App\Models\Wods\Stage;
use Illuminate\Http\Request;
use App\Models\Wods\StageType;
use App\Models\Users\StatusUser;
use App\Http\Controllers\ApiController;

class WodController extends ApiController
{
    /**
     *  Get today wods only if the auth user is Active or Prueba into the system
     *
     *  @return  json
     */
    public function today()
    {
        if ((int) Auth::user()->status_user !== StatusUser::INACTIVO) {
            $wods = Wod::where('date', today())->get();

            return $this->showAll($wods);
        }
        
        return $this->showAll([]);
    }

  public function show(Wod $wod)
  {
    return $this->showOne($wod, 200);
  }

  public function stages(Wod $wod)
  {
    $stages = $wod->stages;
    return $this->showAll($stages, 200);
  }
    //
}

<?php

namespace App\Http\Controllers\Wods;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\Wods\Wod;
use App\Models\Wods\Stage;
use App\Models\Wods\StageType;
use Session;
use Auth;

class WodController extends ApiController
{
  //wods de hoy
  public function today()
  {
    if (Auth::user()->active_planuser) {
      $wods = Wod::where('date',today())->get();
      return $this->showAll($wods);
    } else {
      return response()->json(['data'=>[]]);
    }
    
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

<?php

namespace App\Http\Controllers\Wods;

use App\Models\Wods\Wod;
use App\Models\Users\StatusUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;

class WodController extends ApiController
{
    /**
     *  Get today wods only if the auth user is activeor test in the system
     *
     *  @return  json
     */
    public function today()
    {
        $todayTimezoned = Auth::user()->timezone ?? 'America/Santiago';

        if ((int) Auth::user()->status_user !== StatusUser::INACTIVO) {
            $wods = Wod::where('date', today($todayTimezoned))->get();

            return $this->showAll($wods);
        }

        return $this->showAll(collect());
    }

    /**
     *  Undocumented function
     *
     *  @param  Wod  $wod
     *
     *  @return  void
     */
    public function show(Wod $wod)
    {
        return $this->showOne($wod, 200);
    }

    /**
     *  Undocumented function
     *
     *  @param  Wod  $wod
     *
     *  @return  json
     */
    public function stages(Wod $wod)
    {
        return $this->showAll($wod->stages, 200);
    }
}

<?php

namespace App\Http\Controllers\Videos;

use App\Http\Controllers\ApiController;
use App\Models\Videos\Video;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VideoController extends ApiController
{

    public function index()
    {
        $videos = Video::all();
        return $this->showAll($videos);
    }

    public function lastest()
    {
        $video = Video::where('release_at', '<', now())->first();
        return $this->showOne($video);
    }

}


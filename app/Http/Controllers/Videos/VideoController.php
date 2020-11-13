<?php

namespace App\Http\Controllers\Videos;

use App\Http\Controllers\ApiController;
use App\Models\Videos\Video;
// use App\Models\comments\Comment;

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

    public function show(Video $video)
    {
        return $this->showOne($video);
    }

    public function commments(Video $video)
    {
        $comments = $video->comments->map( function ($comment) {
            $user = $comment->user;
            return [
                'id' => (int) $comment->id,
                'user_avatar' => (string) $user->avatar,
                'body' => (string) $comment->body,
                'created_at' => (string) $comment->created_at,
                'since' => (string) $comment->created_at->diffForHumans(),
            ];
        });
        return response()->json(['status' => true, 'comments' => $comments],200);   
     }

}


<?php

namespace App\Models\Videos;

use App\Models\Plans\Plan;
use App\Models\Comments\Comment;
use Illuminate\Database\Eloquent\Model;
use App\Models\System\Users\NfitTimeZone;
use Vimeo\Exceptions\VimeoUploadException;
use App\Transformers\VideoTransformer;


class Video extends Model
{

    public $transformer = VideoTransformer::class;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     *  Value in Database to be convert to a Carbon instance
     *
     *  @var  array
     */
    protected $dates = ['release_at'];

    /**
     *  Mass assignment variables
     *
     *  @var  array
     */
    protected $fillable = [
        'id',
        'clase_type_id',
        'description',
        'title',
        'user_id',
        'duration',
        'size',
        'uploaded',
        'video_path',
        'thumbnail_path',
        'release_at'
    ];

    /**
     *  Convert to Carbon date the date who come from Database
     *
     *  @param   string  $value
     *
     *  @return  Carbon\Carbon
     */
    public function getReleaseAtAttribute($value)
    {
        if ($value) {
            return NfitTimeZone::adjustToTimeZoneDate($value);
        }
    }

    /**
     *  Undocumented function
     *
     *  @param   [type] $value
     *  @return  void
     */
    public function setReleaseAtAttribute($value)
    {
        $this->attributes['release_at'] = NfitTimeZone::adjustDateToUTC($value);
    }

    /**
     *  [plans description]
     *
     *  @return  [type]  [return description]
     */
    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_video');
    }

    /**
     *  Get formated Duration of the video
     *
     *  @return  string
     */
    public function getDurationAttribute($duration)
    {
        if (! $duration) {
            return null;
        }

        return $duration < 60 ? "{$duration} seg." : round($duration/60) . ' min.';
    }

    /**
     *  methodDescription
     *
     *  @return  returnType
     */
    public function allVideosByActualPlan($request)
    {
        if (auth()->user()->isAdmin() || auth()->user()->isCoach()) {
            return $this->orderByDesc('release_at')
                ->when($request->clase_type_id, function($video, $clase_type_id) {
                    $video->where('clase_type_id', $clase_type_id);
                })
                ->select([
                    'videos.id', 'clase_type_id', 'release_at',
                    'title', 'thumbnail_path', 'duration'
                ])
                ->paginate($request->per_page ?? 6);
        }

        return $this->leftJoin('plan_video', 'plan_video.video_id', '=', 'videos.id')
            ->join('plans', 'plans.id', '=', 'plan_video.plan_id')
            ->join('plan_user', 'plan_user.plan_id', '=', 'plans.id')
            ->where('plan_user.user_id', auth()->id())
            ->where('plan_user.plan_status_id', 1)
            ->where('plan_user.start_date', '<=', today())
            ->where('plan_user.finish_date', '>=', today())
            // get the actual timezone of the box
            ->where('videos.release_at', '<=', now())
            ->when($request->clase_type_id, function($video, $clase_type_id) {
                $video->where('clase_type_id', $clase_type_id);
            })
            ->orderByDesc('release_at')
            ->select([
                'videos.id', 'clase_type_id', 'plan_user.user_id', 'release_at',
                'title', 'thumbnail_path', 'duration', 'plan_user.plan_id'
            ])
            ->paginate(6);
    }

    /**
     *  methodDescription
     *
     *  @return  returnType
     */
    public static function getThumbnailLink($upload_link)
    {
        $imageId = explode('/', $upload_link)[4];

        if ($imageId) {
            return "https://i.vimeocdn.com/video/{$imageId}_1280x720.jpg";
        }
    }

    /**
     *  Bring just comments without replies
     *
     *  @return  void
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id')->orderBy('created_at', 'desc');
    }

    /**
     *  methodDescription
     *
     *  @return  returnType
     */
    public function getComments()
    {
        return $this->comments()->with([
                'user:id,first_name,last_name,avatar',
                'replies' => function($reply) {
                    $reply->with(['user:id,first_name,last_name,avatar']);
                }
            ])
            ->orderByDesc('created_at')
            ->get(['id', 'body', 'user_id', 'created_at']);
    }
}

<?php

namespace App\Models\Comments;

use Carbon\Carbon;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    // use UsesTenantConnection;

    /**
     * Massive assignment for this model
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'parent_id',
        'body',
        'commentable_id',
        'commentable_type'
    ];

    protected $appends = ['created_date'];

    /**
     *  methodDescription
     *
     *  @return  returnType
     */
    public function getCreatedDateAttribute()
    {
        // return Carbon::parse($this->attributes['created_at'])->format('d-m-Y');
        return Carbon::parse($this->attributes['created_at'])->diffForHumans();
    }

    /**
     *  Undocumented function
     *
     *  @return  void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}

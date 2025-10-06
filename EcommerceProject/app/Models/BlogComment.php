<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'blog_id',
        'user_id',
        'content',
        'parent_id',
        'reply_to',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(BlogComment::class, 'parent_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(BlogComment::class, 'reply_to');
    }

    public function replies()
    {
        return $this->hasMany(BlogComment::class, 'reply_to');
    }
}

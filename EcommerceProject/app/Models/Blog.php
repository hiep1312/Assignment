<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'author_id',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }

    public function images()
    {
        return $this->morphToMany(Image::class, 'imageable');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id');
    }
}

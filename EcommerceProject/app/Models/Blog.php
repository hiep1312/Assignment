<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

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

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id');
    }

    public function imageable()
    {
        return $this->morphOne(Imageable::class, 'imageable')
            ->where('is_main', true);
    }

    public function thumbnail()
    {
        return $this->morphOne(Imageable::class, 'imageable')
            ->join('images', 'images.id', '=', 'imageables.image_id')
            ->select([
                'imageables.image_id', 'imageables.imageable_id', 'imageables.imageable_type',
                'images.*',
            ])->where('imageables.is_main', true);
    }
}

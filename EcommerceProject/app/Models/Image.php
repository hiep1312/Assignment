<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'image_url',
        'is_main',
        'position',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'position' => 'integer',
    ];

    public function products()
    {
        return $this->morphedByMany(Product::class, 'imageable');
    }

    public function banners()
    {
        return $this->morphedByMany(Banner::class, 'imageable');
    }

    public function blogs()
    {
        return $this->morphedByMany(Blog::class, 'imageable');
    }
}

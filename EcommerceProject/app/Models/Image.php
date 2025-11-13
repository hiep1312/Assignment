<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'image_url',
    ];

    public function products()
    {
        return $this->morphedByMany(Product::class, 'imageable')
            ->withPivot('is_main', 'position');
    }

    public function banners()
    {
        return $this->morphedByMany(Banner::class, 'imageable')
            ->withPivot('position');
    }

    public function blogs()
    {
        return $this->morphedByMany(Blog::class, 'imageable')
            ->withPivot('is_main');
    }
}

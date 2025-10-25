<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }

    public function images()
    {
        return $this->morphToMany(Image::class, 'imageable')
            ->withPivot('is_main', 'position')
            ->withTimestamps();
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function mainImages()
    {
        return $this->morphToMany(Image::class, 'imageable')
                ->wherePivot('is_main', true)
                ->orderBy('position');
    }

    public function getMainImageAttribute()
    {
        return $this->mainImages->first();
    }
}

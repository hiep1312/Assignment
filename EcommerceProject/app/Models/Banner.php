<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'link_url',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function imageable()
    {
        return $this->morphOne(Imageable::class, 'imageable');
    }

    public function position(): Attribute
    {
        return Attribute::make(
            fn() => $this->loadMissing('imageable')->imageable->position
        );
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(
            fn() => $this->loadMissing('imageable.image')->imageable->image?->image_url
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imageable extends Model
{
    protected $fillable = [
        'image_id',
        'imageable_id',
        'imageable_type',
    ];

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function imageable()
    {
        return $this->morphTo();
    }
}

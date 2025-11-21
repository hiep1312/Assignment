<?php

namespace App\Models;

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

    public function image()
    {
        return $this->morphOne(Imageable::class, 'imageable')
            ->join('images', 'images.id', '=', 'imageables.image_id')
            ->select([
                'imageables.image_id', 'imageables.imageable_id', 'imageables.imageable_type', 'imageables.position',
                'images.*',
            ]);
    }
}

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
        'position',
        'status',
    ];

    protected $casts = [
        'position' => 'integer',
        'status' => 'integer',
    ];

    public function image()
    {
        return $this->morphOne(Imageable::class, 'imageable');
    }
}

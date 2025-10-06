<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoryable extends Model
{
    protected $fillable = [
        'category_id',
        'categoryable_id',
        'categoryable_type',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categoryable()
    {
        return $this->morphTo();
    }
}

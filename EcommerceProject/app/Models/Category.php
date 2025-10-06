<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products()
    {
        return $this->morphedByMany(Product::class, 'categoryable');
    }

    public function blogs()
    {
        return $this->morphedByMany(Blog::class, 'categoryable');
    }
}

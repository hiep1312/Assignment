<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_id',
        'stock',
        'reserved',
        'sold_number',
    ];

    protected $casts = [
        'stock' => 'integer',
        'reserved' => 'integer',
        'sold_number' => 'integer',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}

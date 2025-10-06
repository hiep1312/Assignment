<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'discount',
        'status',
    ];

    protected $casts = [
        'price' => 'integer',
        'discount' => 'integer',
        'status' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function inventory()
    {
        return $this->hasOne(ProductVariantInventory::class, 'variant_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_variant_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_variant_id');
    }
}

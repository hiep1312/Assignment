<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use stdClass;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'status',
    ];

    protected $appends = [
        'inventory_summary'
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

    public function mainImage()
    {
        return $this->morphOne(Imageable::class, 'imageable')
            ->join('images', 'images.id', '=', 'imageables.image_id')
            ->select([
                'imageables.image_id', 'imageables.imageable_id', 'imageables.imageable_type',
                'images.*',
            ])->where('imageables.is_main', true);
    }

    public function primaryVariant()
    {
        return $this->hasOne(ProductVariant::class, 'product_id')
            ->where('status', 1)
            ->orderByRaw('COALESCE(discount, price) ASC')
            ->limit(1);
    }

    public function inventorySummary(): Attribute
    {
        return Attribute::get(function(): ?stdClass {
            return DB::table('product_variants', 'pv')
                ->join('product_variant_inventories as pvi', 'pvi.variant_id', '=', 'pv.id')
                ->selectRaw('pv.product_id, SUM(pvi.stock) AS total_stock, SUM(pvi.reserved) AS total_reserved, SUM(pvi.sold_number) AS total_sold')
                ->where('pv.product_id', $this->id)
                ->where('pv.status', 1)
                ->where('pvi.stock', '>', 0)
                ->groupBy('pv.product_id')
                ->first();
        });
    }
}

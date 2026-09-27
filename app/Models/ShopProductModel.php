<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopProductModel extends Model
{
    use HasFactory;

    protected $table      = 'tbl_shop_products';
    protected $primaryKey = 'product_id';
    public $incrementing  = true;
    public $timestamps    = true;

    protected $fillable = [
        'product_code', 'product_name', 'product_slug', 'brand_id',
        'category_id', 'shop_unit_id', 'vendor_id', 'short_description',
        'long_description', 'base_price', 'selling_price', 'discount_percent',
        'cost_price', 'hsn_code', 'gst_percent', 'stock_qty',
        'min_order_qty', 'max_order_qty', 'low_stock_threshold',
        'weight', 'dimensions', 'sku', 'has_variants',
        'is_featured', 'is_new_arrival', 'is_bestseller', 'is_active',
        'meta_title', 'meta_description', 'meta_keywords', 'view_count',
        'status', 'created_by', 'updated_by',
    ];

    public function scopeActive($query)   { return $query->where('status', 0)->where('is_active', 1); }
    public function scopeFeatured($query) { return $query->where('is_featured', 1); }
    public function scopeInStock($query)  { return $query->where('stock_qty', '>', 0); }

    public function brand()    { return $this->belongsTo(ShopBrandModel::class, 'brand_id', 'brand_id'); }
    public function category() { return $this->belongsTo(ShopCategoryModel::class, 'category_id', 'category_id'); }
    public function unit()     { return $this->belongsTo(ShopUnitModel::class, 'shop_unit_id', 'shop_unit_id'); }
    public function vendor()   { return $this->belongsTo(ShopVendorModel::class, 'vendor_id', 'vendor_id'); }

    public function images()
    {
        return $this->hasMany(ShopProductImageModel::class, 'product_id')->orderBy('sort_order');
    }
    public function primaryImage()
    {
        return $this->hasOne(ShopProductImageModel::class, 'product_id')->where('is_primary', 1);
    }
    public function variants()
    {
        return $this->hasMany(ShopProductVariantModel::class, 'product_id');
    }
    public function inventory()
    {
        return $this->hasMany(ShopInventoryModel::class, 'product_id');
    }
    public function reviews()
    {
        return $this->hasMany(ShopReviewModel::class, 'product_id');
    }
    public function wishlistedBy()
    {
        return $this->hasMany(ShopWishlistModel::class, 'product_id');
    }

    public function getDiscountPercentAttribute($value): float
    {
        if ($this->base_price > 0 && $this->selling_price < $this->base_price) {
            return round((($this->base_price - $this->selling_price) / $this->base_price) * 100, 0);
        }
        return (float) $value;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_qty <= $this->low_stock_threshold && $this->stock_qty > 0;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopReviewModel extends Model
{
    use HasFactory;

    protected $table      = 'tbl_shop_reviews';
    protected $primaryKey = 'review_id';
    public $incrementing  = true;
    public $timestamps    = true;

    protected $casts = [
        'review_images' => 'array',
        'rating'        => 'integer',
    ];

    protected $fillable = [
        'product_id', 'customer_id', 'order_id', 'order_item_id',
        'rating', 'review_title', 'review_body', 'review_images',
        'is_verified_purchase', 'is_approved', 'helpful_count',
        'admin_reply', 'replied_at', 'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 0)->where('is_approved', 1);
    }

    public function product()
    {
        return $this->belongsTo(ShopProductModel::class, 'product_id', 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(ShopCustomerModel::class, 'customer_id', 'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(ShopOrderModel::class, 'order_id', 'order_id');
    }
}

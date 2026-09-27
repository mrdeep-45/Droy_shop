<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOrderModel extends Model
{
    protected $table      = 'tbl_shop_orders';
    protected $primaryKey = 'order_id';
    public $incrementing  = true;
    public $timestamps    = true;

    protected $casts = [
        'shipping_address' => 'array',
    ];

    protected $fillable = [
        'order_number', 'customer_id', 'address_id', 'shipping_address',
        'coupon_id', 'coupon_code', 'subtotal', 'discount_amount',
        'coupon_discount', 'shipping_charge', 'tax_amount', 'total_amount',
        'payment_method', 'payment_status', 'payment_transaction_id',
        'order_status', 'customer_notes', 'admin_notes',
        'expected_delivery_date', 'delivered_at', 'cancelled_at',
        'cancellation_reason', 'invoice_generated', 'invoice_number',
        'status', 'created_by', 'updated_by',
    ];

    public function scopeActive($query)   { return $query->where('status', 0); }
    public function scopePending($query)  { return $query->where('order_status', 'pending'); }
    public function scopeDelivered($query){ return $query->where('order_status', 'delivered'); }

    public function customer()   { return $this->belongsTo(ShopCustomerModel::class, 'customer_id', 'customer_id'); }
    public function address()    { return $this->belongsTo(ShopAddressModel::class, 'address_id', 'address_id'); }
    public function coupon()     { return $this->belongsTo(ShopCouponModel::class, 'coupon_id', 'coupon_id'); }
    public function items()      { return $this->hasMany(ShopOrderItemModel::class, 'order_id'); }
    public function tracking()   { return $this->hasMany(ShopOrderTrackingModel::class, 'order_id')->orderBy('created_at', 'desc'); }
    public function delivery()   { return $this->hasOne(ShopDeliveryAssignmentModel::class, 'order_id'); }
    public function returns()    { return $this->hasMany(ShopReturnModel::class, 'order_id'); }
}

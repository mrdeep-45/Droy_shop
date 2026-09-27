<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopCustomerModel extends Model
{
    use HasFactory;

    protected $table      = 'shop_customers';
    protected $primaryKey = 'customer_id';
    public $incrementing  = true;
    public $timestamps    = true;

    protected $fillable = [
        'customer_code', 'name', 'email', 'phone', 'password',
        'profile_image', 'address', 'city', 'state', 'country',
        'pincode', 'gender', 'dob', 'email_verified_at',
        'otp', 'otp_expires_at', 'referral_code', 'referred_by',
        'is_email_verified', 'status',
    ];

    protected $hidden = ['password', 'otp'];

    public function scopeActive($query) { return $query->where('status', 0); }

    public function addresses()
    {
        return $this->hasMany(ShopAddressModel::class, 'customer_id');
    }

    public function orders()
    {
        return $this->hasMany(ShopOrderModel::class, 'customer_id');
    }

    public function wishlist()
    {
        return $this->hasMany(ShopWishlistModel::class, 'customer_id');
    }

    public function cart()
    {
        return $this->hasMany(ShopCartModel::class, 'customer_id');
    }

    public function reviews()
    {
        return $this->hasMany(ShopReviewModel::class, 'customer_id');
    }
}

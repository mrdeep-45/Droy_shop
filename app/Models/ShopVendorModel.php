<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopVendorModel extends Model
{
    use HasFactory;

    protected $table      = 'shop_vendors';
    protected $primaryKey = 'vendor_id';
    public $incrementing  = true;
    public $timestamps    = true;

    protected $fillable = [
        'vendor_code', 'business_name', 'owner_name', 'email', 'phone',
        'password', 'logo', 'description', 'gst_number', 'pan_number',
        'bank_name', 'bank_account', 'ifsc_code', 'address', 'city',
        'state', 'country', 'pincode', 'commission_percent',
        'is_approved', 'rejection_reason', 'approved_at',
        'status', 'created_by', 'updated_by',
    ];

    protected $hidden = ['password'];

    public function scopeActive($query)   { return $query->where('status', 0); }
    public function scopeApproved($query) { return $query->where('is_approved', 1); }

    public function products()
    {
        return $this->hasMany(ShopProductModel::class, 'vendor_id');
    }

    public function earnings()
    {
        return $this->hasMany(ShopVendorEarningModel::class, 'vendor_id');
    }

    public function getApprovalStatusLabelAttribute(): string
    {
        return match ((int)$this->is_approved) {
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Rejected',
            default => 'Unknown',
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopDeliveryStaffModel extends Model
{
    use HasFactory;

    protected $table      = 'shop_delivery_staff';
    protected $primaryKey = 'delivery_staff_id';
    public $incrementing  = true;
    public $timestamps    = true;

    protected $fillable = [
        'staff_code', 'name', 'email', 'phone', 'password',
        'vehicle_type', 'vehicle_number', 'profile_image',
        'latitude', 'longitude', 'is_available', 'status',
        'created_by', 'updated_by',
    ];

    protected $hidden = ['password'];

    public function scopeActive($query)    { return $query->where('status', 0); }
    public function scopeAvailable($query) { return $query->where('is_available', 1); }

    public function assignments()
    {
        return $this->hasMany(ShopDeliveryAssignmentModel::class, 'delivery_staff_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopDeliveryAssignmentModel extends Model
{
    use HasFactory;

    protected $table      = 'tbl_shop_delivery_assignments';
    protected $primaryKey = 'assignment_id';
    public $incrementing  = true;
    public $timestamps    = true;

    protected $fillable = [
        'order_id', 'delivery_staff_id', 'assigned_by',
        'assigned_at', 'delivery_otp', 'otp_verified_at',
        'delivery_status', 'failure_reason', 'delivery_notes',
        'delivery_latitude', 'delivery_longitude', 'delivered_at',
    ];

    public function order()
    {
        return $this->belongsTo(ShopOrderModel::class, 'order_id', 'order_id');
    }

    public function staff()
    {
        return $this->belongsTo(ShopDeliveryStaffModel::class, 'delivery_staff_id', 'delivery_staff_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopAdminModel extends Model
{
    use HasFactory;

    protected $table      = 'shop_admin';
    protected $primaryKey = 'shop_admin_id';
    public $incrementing  = true;
    public $timestamps    = true;

    protected $fillable = [
        'name', 'email', 'password', 'admin_type',
        'profile_image', 'phone', 'status',
        'created_by', 'updated_by',
    ];

    protected $hidden = ['password'];

    public function scopeActive($query) { return $query->where('status', 0); }
    public function isSuperAdmin(): bool { return $this->admin_type === 'super_admin'; }
}

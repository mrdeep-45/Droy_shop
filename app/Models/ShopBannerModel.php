<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopBannerModel extends Model
{
    use HasFactory;

    protected $table      = 'tbl_shop_banners';
    protected $primaryKey = 'banner_id';
    public $incrementing  = true;
    public $timestamps    = true;

    protected $fillable = [
        'banner_title', 'banner_subtitle', 'banner_image', 'banner_mobile_image',
        'banner_link', 'banner_cta_text', 'banner_position', 'target_type',
        'target_id', 'sort_order', 'is_active', 'valid_from', 'valid_until',
        'status', 'created_by', 'updated_by',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 0)->where('is_active', 1);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopCmsPageModel extends Model
{
    use HasFactory;

    protected $table      = 'tbl_shop_cms_pages';
    protected $primaryKey = 'page_id';
    public $incrementing  = true;
    public $timestamps    = true;

    protected $fillable = [
        'page_title', 'page_slug', 'page_content', 'meta_title',
        'meta_description', 'meta_keywords', 'is_active', 'show_in_footer',
        'sort_order', 'status', 'created_by', 'updated_by',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 0)->where('is_active', 1);
    }
}

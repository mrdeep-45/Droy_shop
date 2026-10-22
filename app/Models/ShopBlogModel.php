<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShopBlogModel extends Model
{
    use HasFactory;

    protected $table      = 'tbl_shop_blogs';
    protected $primaryKey = 'blog_id';
    public $incrementing  = true;
    public $timestamps    = true;

    protected $fillable = [
        'blog_title', 'blog_slug', 'blog_content', 'blog_image',
        'blog_excerpt', 'blog_category', 'tags', 'author_id',
        'author_type', 'is_published', 'published_at', 'view_count',
        'meta_title', 'meta_description', 'meta_keywords',
        'status', 'created_by', 'updated_by',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 0)->where('is_published', 1);
    }
}

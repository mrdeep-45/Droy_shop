<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategoryModel extends Model
{
    //
    use HasFactory;
    protected $table = 'tbl_product_category';
    protected $primaryKey = 'category_id';
    protected $fillable = ['category_id', 'name', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];
    public function scopeActive($query)
    {
        return $query->where('status', 0)->orderBy('name', 'asc');
    }
}

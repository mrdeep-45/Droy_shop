<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    //
    use HasFactory;

    protected $table = 'tbl_product';
    protected $primaryKey = 'product_id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'unit_id',
        'prod_code',
        'prod_name',
        'prod_desc',
        'prod_amount',
        'gst',   //currently we store gst id here 
        'status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }

     public function category()
    {
        return $this->belongsTo(ProductCategoryModel::class, 'category_id');
    }
     public function unit()
    {
        return $this->belongsTo(UnitModel::class, 'unit_id', 'unit_id');
    }
     public function gstDetails()
    {
        return $this->belongsTo(GstModel::class, 'gst', 'gst_id');
    }
     public function unitDetails()
    {
        return $this->belongsTo(UnitModel::class, 'unit_id', 'unit_id');
    }

}

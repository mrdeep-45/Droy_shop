<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawmaterialModel extends Model
{
    //
     use HasFactory;

    protected $table = 'tbl_raw_material';
    protected $primaryKey = 'rm_id';
    protected $fillable = [
        'rm_id',
        'name',
        'description',
        'notes',
        'stock_group_id',
        'stock_category_id',
        'unit_id',
        'alt_unit_id',
        'convert_factor',
        'reorder_level',
        'hsn_code',
        'type_of_supply',
        'gst_id',
        'incl_of_taxes',
        'opening_stock',
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

    public function stockGroup()
    {
        return $this->belongsTo(StockGroupModel::class, 'stock_group_id');
    }

    public function stockCategory()
    {
        return $this->belongsTo(StockCategoryModel::class, 'stock_category_id');
    }

    public function unit()
    {
        return $this->belongsTo(UnitModel::class, 'unit_id');
    }

    public function alternateUnit()
    {
        return $this->belongsTo(AlternateUnitModel::class, 'alt_unit_id');
    }

    public function gst()
    {
        return $this->belongsTo(GstModel::class, 'gst_id');
    }
}

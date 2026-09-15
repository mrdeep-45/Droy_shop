<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $table = 'tbl_quotation_item';

    protected $primaryKey = 'item_id';

    public $timestamps = false;

    protected $fillable = [
        'quotation_id',
        'product_id',
        'qty',
        'description',
        'unit_price',
        'gst_percent',
        'gst_amount',
        'discount_percentage',
        'discount_price',
        'total',
        'unit_price_converted',
        'total_converted'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'unit_price_converted' => 'decimal:2',
        'total_converted' => 'decimal:2'
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'quotation_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'product_id');
    }
}

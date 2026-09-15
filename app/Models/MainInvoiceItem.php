<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainInvoiceItem extends Model
{
    protected $table = 'tbl_main_invoice_item';

    protected $primaryKey = 'mii_id';

    public $timestamps = true;

    protected $fillable = [
        'main_invoice_id',
        'product_id',
        'description',
        'qty',
        'unit_price',
        'unit_price_converted',
        'discount_percentage',
        'discount_price',
        'gst_percent',
        'gst_amount',
        'total',
        'total_converted',
    ];

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'product_id');
    }
}

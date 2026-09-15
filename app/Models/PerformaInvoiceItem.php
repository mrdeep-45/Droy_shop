<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformaInvoiceItem extends Model
{
    protected $table = 'tbl_performa_invoice_item';

    protected $primaryKey = 'pii_id';

    public $timestamps = false;

    protected $fillable = [
        'invoice_id',
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
        'total_converted'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'unit_price_converted' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'total_converted' => 'decimal:2'
    ];

    public function invoice()
    {
        return $this->belongsTo(PerformaInvoice::class, 'invoice_id', 'invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'product_id');
    }
}

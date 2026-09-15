<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformaInvoice extends Model
{
    protected $table = 'tbl_performa_invoice';

    protected $primaryKey = 'invoice_id';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const EXPORT = 'export';
    const DOMESTIC = 'domestic';

    const ORIGINAL = 0;
    const REVISED = 1;

    const ACTIVE = 0;
    const INACTIVE = 1;

    const GST_EXEMPTED = 0;
    const GST_NOT_EXEMPTED = 1;

    protected $fillable = [
        'invoice_no',
        'invoice_type',
        'customer_id',
        'quotation_id',
        'parent_invoice_id',
        'buyer_order_no',
        'buyer_order_date',
        'assigned_to',
        'invoice_date',
        'advance_payment_percentage',
        'advance_payment',
        'balance_payment_percentage',
        'balance_payment',
        'terms_payment',
        'transportation',
        'destination',
        'dispatch_time',
        'delivery_note',
        'po_reference',
        'gst_exemption',
        'include_gst',
        'email',
        'phone',
        'country_id',
        'state_id',
        'city_id',
        'currency',
        'exchange_rate',
        'subtotal',
        'discount_total',
        'gst_total',
        'grand_total',
        'grand_total_converted',
        'status',
        'is_revised',
        'revised_count',
        'converted_to_main',
        'created_by',
        'updated_by',
        'attachments'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'buyer_order_date' => 'date',
        'advance_payment_percentage' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'balance_payment_percentage' => 'decimal:2',
        'balance_payment' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'gst_total' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'grand_total_converted' => 'decimal:2'
    ];

    public function items()
    {
        return $this->hasMany(PerformaInvoiceItem::class, 'invoice_id', 'invoice_id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'quotation_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public static function getInvoiceNo(): string
    {
        $latest = self::orderByDesc('invoice_id')->first();

        $number = 1;

        if ($latest && $latest->invoice_no) {
            $parts = explode('-', $latest->invoice_no);

            if (isset($parts[1])) {
                $number = (int) $parts[1] + 1;
            }
        }

        $year = date('Y');
       $nextYearShort = date('y', strtotime('+1 year'));

        return 'PI-' .
            str_pad($number, 3, '0', STR_PAD_LEFT) .
            '-' . $year .
            '-' . $nextYearShort;
    }
}

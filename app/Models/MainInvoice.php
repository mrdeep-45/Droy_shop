<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainInvoice extends Model
{
    protected $table = 'tbl_main_invoice';

    protected $primaryKey = 'main_invoice_id';

    public $timestamps = true;

    const ACTIVE   = 0;
    const INACTIVE = 1;

    const GST_EXEMPTED     = 0;
    const GST_NOT_EXEMPTED = 1;

    protected $fillable = [
        'invoice_no',
        'invoice_type',
        'customer_id',
        'quotation_id',
        'performa_invoice_id',
        'assigned_to',
        'invoice_date',
        'advance_payment',
        'balance_payment',
        'terms_payment',
        'transportation',
        'dispatch_time',
        'commercial_terms',
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
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'invoice_date'           => 'date',
        'advance_payment'        => 'decimal:2',
        'balance_payment'        => 'decimal:2',
        'exchange_rate'          => 'decimal:6',
        'subtotal'               => 'decimal:2',
        'discount_total'         => 'decimal:2',
        'gst_total'              => 'decimal:2',
        'grand_total'            => 'decimal:2',
        'grand_total_converted'  => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(MainInvoiceItem::class, 'main_invoice_id', 'main_invoice_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function performaInvoice()
    {
        return $this->belongsTo(PerformaInvoice::class, 'performa_invoice_id', 'invoice_id');
    }

    public static function getInvoiceNo(): string
    {
        $latest = self::orderByDesc('main_invoice_id')->first();
        $number = 1;
        if ($latest && $latest->invoice_no) {
            $parts  = explode('-', $latest->invoice_no);
            if (isset($parts[1])) {
                $number = (int) $parts[1] + 1;
            }
        }
        $year      = date('Y');
        $yearShort = date('y');
        return 'MI-' . str_pad($number, 3, '0', STR_PAD_LEFT) . '-' . $year . '-' . $yearShort;
    }
}

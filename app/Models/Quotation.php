<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $table = 'tbl_quotation';

    protected $primaryKey = 'quotation_id';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const ORIGINAL = 1;
    const REVISED = 0;

    const ACTIVE = 0;
    const INACTIVE = 1;


    const DIRECT = 0;
    const FROM_DEAL = 1;

    const GST_EXEMPTED = 0;
    const GST_NOT_EXEMPTED = 1;

    protected $fillable = [
        'quotation_no',
        'quotation_type',
        'deal_id',
        'lead_id',
        'customer_id',
        'parent_quotation_id',
        'assigned_to',
        'customer_name',
        'contact_person_name',
        'gst_no',
        'gst_exemption',
        'email',
        'phone',
        'country_id',
        'state_id',
        'city_id',
        'quotation_date',
        'is_revised',
        'revised_count',
        'terms',
        'subject',
        'message',
        'status',
        'currency',
        'exchange_rate',
        'attachments',
        'grand_total',
        'discount_total',
        'gst_total',
        'grand_total_converted',
        'is_direct',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'exchange_rate' => 'decimal:6',
        'grand_total' => 'decimal:2',
        'grand_total_converted' => 'decimal:2'
    ];

    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id', 'quotation_id');
    }

    public static function getQuotationNo(): string
    {
        $latest = self::where('is_revised', self::ORIGINAL)
            ->orderByDesc('quotation_id')
            ->first();

        $number = 1;

        if ($latest && $latest->quotation_no) {
            $parts = explode('-', $latest->quotation_no);

            if (isset($parts[1])) {
                $number = (int) $parts[1] + 1;
            }
        }

        $year = date('Y');
        $nextYearShort = date('y', strtotime('+1 year'));

        return 'HCX-' .
            str_pad($number, 3, '0', STR_PAD_LEFT) .
            '-' . $year .
            '-' . $nextYearShort;
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id', 'deal_id');
    }

    public function lead()
    {
        return $this->belongsTo(EnquiryManageModel::class, 'lead_id', 'enquiry_id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
    public function assignedStaff()
    {
        return $this->belongsTo(Staffmodel::class, 'assigned_to', 'staff_id');
    }

    public function createdByStaff()
    {
        return $this->belongsTo(Staffmodel::class, 'created_by', 'staff_id');
    }

    public function revisions()
    {
        return $this->hasMany(Quotation::class, 'parent_quotation_id', 'quotation_id');
    }
}

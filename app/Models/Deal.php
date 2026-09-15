<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $table = 'tbl_deal';

    protected $primaryKey = 'deal_id';

    protected $fillable = [
        'lead_id',
        'customer_id',
        'deal_name',
        'iwo_no',
        'dispatch_date',
        'type',
        'deal_type',
        'gst_no',
        'amount',
        'assigned_to',
        'closing_date',
        'deal_stage',
        'is_direct',
        'status',
        'deal_status',
        'description',
        'complain_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'closing_date' => 'date',
        'dispatch_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($deal) {
            if ($deal->isDirty('deal_stage')) {
                $stage = DealStage::find($deal->deal_stage);
                if ($stage) {
                    if ($stage->is_won == 1) {
                        $deal->deal_status = 1;
                    } elseif ($stage->is_lost == 1) {
                        $deal->deal_status = 2;
                    } else {
                        $deal->deal_status = 0;
                    }
                }
            }
        });
    }

    // Record Status
    const STATUS_ACTIVE = 0;
    const STATUS_DELETED = 1;

    // Deal Status
    const DEAL_PENDING = 0;
    const DEAL_WON = 1;
    const DEAL_LOST = 2;

    // Deal Type
    const TYPE_NEW = 'new';
    const TYPE_EXISTING = 'existing';

    public function dealStage()
    {
        return $this->belongsTo(DealStage::class, 'deal_stage', 'id');
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
}



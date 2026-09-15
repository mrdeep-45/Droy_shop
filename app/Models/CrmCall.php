<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmCall extends Model
{
    use HasFactory;

    protected $table = 'tbl_crm_calls';
    protected $primaryKey = 'id';
    public $timestamps = true;

    /**
     * reference_type: 'deal' | 'lead'
     * reference_id: deal_id or enquiry_id
     */
    protected $fillable = [
        'reference_type',
        'reference_id',
        'lead_name',
        'call_status',
        'assigned_to',
        'call_date',
        'call_time',
        'subject',
        'call_agenda',
        'call_purpose',
        'call_result',
        'status',
        'created_by',
        'updated_by',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'reference_id', 'deal_id');
    }

    public function lead()
    {
        return $this->belongsTo(EnquiryManageModel::class, 'reference_id', 'enquiry_id');
    }

    public function assignedStaff()
    {
        return $this->belongsTo(Staffmodel::class, 'assigned_to', 'staff_id');
    }
}

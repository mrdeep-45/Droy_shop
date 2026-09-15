<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmNote extends Model
{
    use HasFactory;

    protected $table = 'tbl_crm_notes';
    protected $primaryKey = 'id';
    public $timestamps = true;

    /**
     * reference_type: 'deal' | 'lead'
     * reference_id: deal_id or enquiry_id
     */
    protected $fillable = [
        'reference_type',
        'reference_id',
        'title',
        'assigned_to',
        'note',
        'status',
        'created_by',
        'updated_by',
        'follow_up_date',
        'is_completed',
        'result',
        'attachment',
        'created_at',
    ];

    /**
     * Resolve the parent reference dynamically.
     */
    public function reference()
    {
        if ($this->reference_type === 'deal') {
            return $this->belongsTo(Deal::class, 'reference_id', 'deal_id');
        }
        return $this->belongsTo(EnquiryManageModel::class, 'reference_id', 'enquiry_id');
    }

    public function assignedStaff()
    {
        return $this->belongsTo(Staffmodel::class, 'assigned_to', 'staff_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Staffmodel::class, 'created_by', 'staff_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(Staffmodel::class, 'updated_by', 'staff_id');
    }
}

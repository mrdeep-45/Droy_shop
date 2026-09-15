<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmAttachment extends Model
{
    use HasFactory;

    protected $table = 'tbl_crm_attachments';
    protected $primaryKey = 'id';
    public $timestamps = true;

    /**
     * reference_type: 'deal' | 'lead'
     * reference_id: deal_id or enquiry_id
     */
    protected $fillable = [
        'reference_type',
        'reference_id',
        'file_name',
        'file_path',
        'status',
        'created_by',
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
}

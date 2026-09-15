<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    protected $table = 'mst_lead_source';
    protected $primaryKey = 'source_id';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'source_type',
        'company_id',
        'status',
        'created_at',
        'created_by',
        'updated_by',
        'updated_at',
        'created_type'
    ];

    public function enquirys()
    {
        return $this->hasMany(EnquiryManageModel::class, 'source', 'source_id');
    }

     
}


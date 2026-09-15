<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    use HasFactory;

    protected $table = 'lead_status';
    protected $primaryKey = 'status_id';
    protected $fillable = [
        'status_id',
        'title',
        'sort_order',
        'company_id',
        'created_type',
        'status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }
}

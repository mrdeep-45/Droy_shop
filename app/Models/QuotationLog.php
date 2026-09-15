<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationLog extends Model
{
    protected $table = 'tbl_quotation_log';

    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'quotation_id',
        'action',
        'description',
        'payload',
        'performed_by',
        'performed_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'performed_at' => 'datetime',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'quotation_id');
    }
}

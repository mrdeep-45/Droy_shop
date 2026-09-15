<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformaInvoiceLog extends Model
{
    protected $table = 'tbl_performa_invoice_log';

    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'invoice_id',
        'action',
        'description',
        'payload',
        'performed_by',
        'performed_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'performed_at' => 'datetime'
    ];

    public function invoice()
    {
        return $this->belongsTo(PerformaInvoice::class, 'invoice_id', 'invoice_id');
    }
}

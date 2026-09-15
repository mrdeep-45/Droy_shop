<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealLog extends Model
{
    protected $table = 'tbl_deal_log';

    protected $primaryKey = 'log_id';

    public $timestamps = false;

    protected $fillable = [
        'deal_id',
        'action',
        'description',
        'payload',
        'performed_by',
        'performed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'performed_at' => 'datetime',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id', 'deal_id');
    }
}

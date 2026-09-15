<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignLogModel extends Model
{
    //
    protected $table = 'tbl_campaign_activity_logs';
    protected $primaryKey = 'log_id';
    public $timestamps = false;

    protected $fillable = [
        'campaign_id',
        'action',
        'description',
        'created_by',
        'created_at'
    ];
}


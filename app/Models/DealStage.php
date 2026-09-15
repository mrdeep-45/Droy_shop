<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealStage extends Model
{
    protected $table = 'mst_deal_stage';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'stage_name',
        'company_id',
        'color',
        'order_no',
        'status',
        'is_won',
        'is_lost',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    public function deals()
    {
        return $this->hasMany(Deal::class, 'deal_stage', 'id');
    }
}

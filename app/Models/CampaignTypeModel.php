<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignTypeModel extends Model
{
    //
    use HasFactory;

    protected $table = 'mst_campaign_type';
    protected $primaryKey = 'campaign_type_id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'campaign_type_id', 
        'campaign_type_name', 
        'status', 
        'created_by', 
        'created_at', 
        'updated_by', 
        'updated_at'
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 0)->orderBy('campaign_type_name', 'ASC');
    }
}

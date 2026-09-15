<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CampaignModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_campaign';
    protected $primaryKey = 'campaign_id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'assigned_to',
        'campaign_name',
        'campaign_type_id',
        'start_date',
        'end_date',
        'expected_budget',
        'actual_cost',
        'description',
        'num_visitors',
        'machines_displayed',
        'design_details',
        'stall_number',
        'venue_location',
        'status',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];
protected $casts = [
        'design_details' => 'array'
    ];
    // Get the Campaign Type detail
    public function campaignType()
    {
        return $this->belongsTo(CampaignTypeModel::class, 'campaign_type_id', 'campaign_type_id');
    }

    // Get the assigned owner details
    public function assignedStaff()
    {
        return $this->belongsTo(Staffmodel::class, 'assigned_to', 'staff_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 0);
    }
    /**
 * Get the dynamic timeline status of the campaign.
 */
public function getTimelineStatusAttribute()
{
    $today = Carbon::today()->format('Y-m-d');

    if (!$this->start_date || !$this->end_date) {
        return '<span class="badge bg-secondary">No Date Set</span>';
    }

    if ($this->start_date > $today) {
        // Upcoming: You can calculate remaining days if needed
        $daysLeft = Carbon::parse($today)->diffInDays(Carbon::parse($this->start_date));
        return '<span class="badge bg-warning text-dark">Upcoming (' . $daysLeft . ' Days to go)</span>';
    } elseif ($this->start_date <= $today && $this->end_date >= $today) {
        return '<span class="badge bg-success">Ongoing</span>';
    } else {
        return '<span class="badge bg-danger">Completed</span>';
    }
}
}

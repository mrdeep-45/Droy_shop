<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryManageModel extends Model
{
    //
    use HasFactory;
    protected $table = 'enquiry_manage';

    protected $primaryKey = 'enquiry_id';

    protected $fillable = [
        'enquiry_no',
        'enquiry_name',
        'company_name',
        'is_exist',
        'customer_id',
        'contact_person_name',
        'email',
        'alternate_phone_1',
        'unique_query_id',
        'phone',
        'source',
        'status_id',
        'deal_source',
        'deal_status_id',
        'unique_query_id',
        'enquiry_date',
        'next_follow_up_date',
        'notes',
        'rollback_count',
        'close_report_remark',
        'product_id',
        'status',
        'lead_status',
        'countries_id',
        'state_id',
        'city_id',
        'created_by',
        'updated_by',
        'assigned_to', //  Add this line
        'lead_type',
        'industry_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'next_follow_up_date',
    ];


    public static function get_enquiry_no()
    {
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        if ($currentMonth >= 4) {
            $startYear = substr($currentYear, -2);
            $endYear = substr($currentYear + 1, -2);
        } else {
            $startYear = substr($currentYear - 1, -2);
            $endYear = substr($currentYear, -2);
        }

        $fyStr = $startYear . '-' . $endYear;
        $prefix = 'lead-' . $fyStr . '-';

        $latestenquiry = EnquiryManageModel::where('enquiry_no', 'like', $prefix . '%')
            ->orderBy('enquiry_id', 'desc')
            ->first();

        $number = 1;

        if ($latestenquiry && !empty($latestenquiry->enquiry_no)) {
            $parts = explode('-', $latestenquiry->enquiry_no);
            if (isset($parts[3])) {
                $number = (int) $parts[3] + 1;
            }
        }

        return $prefix . $number;
    }
    // Add relationship to easily get staff details if needed later
    public function assignedStaff()
    {
        return $this->belongsTo(Staffmodel::class, 'assigned_to', 'staff_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Staffmodel::class, 'created_by', 'staff_id');
    }

    public function products()
    {
        return $this->hasMany(EnquiryItemModel::class, 'enquiry_id', 'enquiry_id');
    }

    public function leadStatus()
    {
        return $this->belongsTo(LeadStatus::class, 'status_id', 'status_id');
    }

    public function deal()
    {
        return $this->hasOne(Deal::class, 'lead_id', 'enquiry_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function sourceInfo()
    {
        return $this->belongsTo(LeadSource::class, 'source', 'source_id');
    }

    public function city()
    {
        return $this->belongsTo(CityModel::class, 'city_id', 'city_id');
    }

    public function state()
    {
        return $this->belongsTo(StateModel::class, 'state_id', 'state_id');
    }

    public function country()
    {
        return $this->belongsTo(CountryModel::class, 'countries_id', 'c_id');
    }

    public function industryType()
    {
        return $this->belongsTo(IndustryTypeModel::class, 'industry_id', 'industry_type_id');
    }
}

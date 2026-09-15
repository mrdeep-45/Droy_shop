<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Staffmodel extends Model
{
    use HasFactory;

    protected $table = 'mst_staff';

    protected $primaryKey = 'staff_id';

    protected $fillable = [
        'salute',
        'role_id',
        'staff_name',
        'staff_email',
        'password',
        'is_hrms_admin',
        'face_data',
        'face_image',
        'phone',
        'address',
        'position',
        'department',
        'hire_date',
        'dob',
        'dor',
        'salary',
        'employment_category',
        'probation_period_months',
        'shift_type',
        'shift_timing',
        'week_off_day',
        'status',
        'shift_id',
        'staff_code',
        'family_contact_no',
        'family_member_name',
        'aadhar_no',
        'pan_no',
        'pf_no',
        'uan_no',
        'esi_no',
        'is_pf_applicable',
        'staff_type',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['hire_date', 'dob', 'dor'];

    public function getIsProbationCompletedAttribute()
    {
        if (!$this->hire_date || !$this->probation_period_months) {
            return false;
        }
        // $probationPeriodMonths = 3; // 
        $hireDate = Carbon::parse($this->hire_date);
        //return Carbon::today()->greaterThanOrEqualTo($hireDate->addMonths($probationPeriodMonths));
        return $hireDate->diffInMonths(now()) >= $this->probation_period_months;
    }
    public function role()
    {
        return $this->belongsTo(RoleModel::class, 'role_id');
    }
    public function shift()
    {
        return $this->belongsTo(ShiftModel::class, 'shift_id');
    }
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class, 'staff_id');
    }

    public function salaryRecords()
    {

        return $this->hasMany(Salarymodel::class, 'staff_id', 'staff_id');
    }
}

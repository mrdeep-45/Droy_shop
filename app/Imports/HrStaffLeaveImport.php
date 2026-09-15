<?php

namespace App\Imports;

use App\Models\Staffmodel;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\Attendancemodel;

class HrStaffLeaveImport implements ToCollection, WithHeadingRow
{
    private $errors = [];

    public function collection(Collection $rows)
    {
        // 1. Database Transaction for Atomicity
        DB::beginTransaction();
        
        $currentRow = 1; 
        
        try {
            foreach ($rows as $row) {
                $currentRow++; // Start tracking actual data rows (after heading row)

                $leave_date_value = $row['leave_date'];
                // if (is_numeric($leave_date_value) && Date::isExcelDate($leave_date_value)) {
                //     $row['leave_date'] = Date::excelToDateTimeObject($leave_date_value)->format('Y-m-d');
                // }

                if (is_numeric($leave_date_value) && $leave_date_value > 1) {
                    try {
                        // Convert the numeric timestamp to a Carbon object, then format
                        $row['leave_date'] = Date::excelToDateTimeObject($leave_date_value)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $this->errors[] = ['row' => $currentRow, 'errors' => ['Date conversion failed. Ensure cell is formatted as Date or YYYY-MM-DD string.']];
                        continue;
                    }
                }

                // 2. Data Validation
                $validator = Validator::make($row->toArray(), [
                    'staff_code' => 'required|string|exists:mst_staff,staff_code',
                    'leave_type' => 'required|in:CL,PL,SL',
                    'leave_date' => 'required|date',
                    'total_days' => 'required|numeric|in:0.5,1',
            
                    'reason' => 'required|string|max:255',
                    'contact_info' => 'nullable|max:11'
                ]);

                if ($validator->fails()) {
                    $this->errors[] = ['row' => $currentRow, 'errors' => $validator->errors()->all()];
                    continue; // Skip invalid row
                }
                
                // 3. Find Staff and Prepare Data
                $staff = Staffmodel::where('staff_code', $row['staff_code'])->first();
                if (!$staff) {
                    $this->errors[] = ['row' => $currentRow, 'errors' => ['Staff code not found.']];
                    continue;
                }
                
                $staffId = $staff->staff_id;
                $leaveType = strtoupper($row['leave_type']);
                $totalDays = $row['total_days'];
                $singleDate = Carbon::parse($row['leave_date']);
                $startDate = $singleDate; 
                $endDate = $singleDate;
                $year = $singleDate->year;
                $leaveKey = strtolower($leaveType);
                
                // Determine leave_day (simplistic: full if 1 day, half if 0.5 day)
                $leaveDay = ($totalDays == 0.5) ? 'half' : 'full'; 

                // 4. Duplicate Check (Crucial step)
                $exists = LeaveRequest::where('staff_id', $staffId)
                    ->where('status', '!=', 'rejected')
                    ->where(function ($query) use ($singleDate) {
                        $query->whereBetween('start_date', [$singleDate->toDateString(), $singleDate->toDateString()])
                            ->orWhereBetween('end_date', [$singleDate->toDateString(), $singleDate->toDateString()]);
                    })
                    ->exists();

                if ($exists) {
                    $this->errors[] = ['row' => $currentRow, 'errors' => ["Leave request already exists for the date: " . $singleDate->format('Y-m-d')]];
                    continue;
                }

                // 5. Create Leave Request (Approved by HR)
                LeaveRequest::create([
                    'staff_id' => $staffId,
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'leave_type' => $leaveType,
                    'reason' => $row['reason'],
                    'leave_day' => $leaveDay,
                    'total_days' => $totalDays,
                    'contact_info' => $row['contact_info'] ?? null, // Optional contact
                    'status' => 'approved', // HR approval
                    'created_by' => getCreatedBy(),
                    'created_at' => now(),
                ]);

               Attendancemodel::updateOrCreate(
                [
                    'staff_id' => $staffId,
                    'date' => $startDate->toDateString()
                ],
                [
                    'date' => $startDate->toDateString(),
                    'status' => 'leave',
                    'leave_type' => $leaveType,
                    'time_in' => $startDate->copy()->setTime(10, 30, 0),
                    'time_out' => null
                ]
            );
            

                // 6. Update Leave Balance (Allow Negative Balance)
               $leaveBalance = LeaveBalance::firstOrCreate(
    ['staff_id' => $staffId, 'year' => $year],
    [
        // Required by schema:
        'leave_type' => 'CL,PL,SL', // <-- ADD THIS LINE
        'CL' => 0, 
        'PL' => 0, 
        'SL' => 0, 
        
        // Fields that track allocation/usage:
        'cl_allocated' => 0,
        'pl_allocated' => 0,
        'sl_allocated' => 0,
        
        // If your schema requires 'earned', 'used', 'carried_forward', and the 'used' fields:
        'earned' => 0,
        'used' => 0,
        'carried_forward' => 0,
        'cl_used' => 0,
        'pl_used' => 0,
        'sl_used' => 0,
        
    ]
);

                $updateField = $leaveKey . '_used';
                // Increment the USED column by the total_days
                if (in_array($updateField, ['cl_used', 'pl_used', 'sl_used'])) {
                    $leaveBalance->increment($updateField, $totalDays);
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            // Store a general error if the whole process fails
            $this->errors[] = ['row' => 'General', 'errors' => ['Import failed due to an unexpected error: ' . $e->getMessage()]];
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
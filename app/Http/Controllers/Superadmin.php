<?php

namespace App\Http\Controllers;

use App\Models\Attendancemodel;
use App\Models\LeaveRequest;
use App\Models\Staffmodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
class Superadmin extends Controller
{
    public function dashboard()
    {
        $page_title = 'Dashboard';
        $page_name = 'Dashboard';
        return view('superadmin.dashboard', compact('page_title', 'page_name'));
    }
    public function sample_page()
    {
        $page_title = 'Sample Page';
        $page_name = 'Sample Page';
        return view('superadmin.form', compact('page_title', 'page_name'));
    }

    // public function attendanceview()
    // {
    //     $page_title = 'Attendance View';
    //     $page_name = 'Attendance View';
    //     $staffList = Staffmodel::where('status', 0)->get();
    //     $canManualEntry = canAddManualAttendance();
    //     $loginType = Session::get('login_type');
    //     return view('company.attendance.attendance_view', compact('page_title', 'page_name', 'staffList', 'canManualEntry', 'loginType'));
    // }
    public function holidayList(Request $request)
    {
        $query = DB::table('tbl_holidays')
            ->where('status', 0)
            ->orderByDesc('holiday_id');

        return DataTables::of($query)
            ->addIndexColumn()

            ->editColumn('date', function ($row) {
                return Carbon::parse($row->date)->format('d-m-Y');
            })

            ->addColumn('staff_count', function ($row) {
                return count(array_filter(explode(',', $row->staff_ids)));
            })

            ->addColumn('action', function ($row) {
                return '
                    <button
                        class="btn btn-danger btn-sm deleteHoliday"
                        data-id="'.$row->holiday_id.'">
                        Delete
                    </button>
                ';
            })

            ->rawColumns(['action'])
            ->make(true);
    }
     public function data(Request $request)
    {

        $HALF_DAY_THRESHOLD_MINUTES = 300; // 4 hours, as per your request (or 5 hours if you use 300)
    $UNDER_TIME_HIGHLIGHT_MINUTES = 240; // Staff working less than 4 hours is highlighted (RED)
    $OVER_TIME_HIGHLIGHT_MINUTES = 60; // Staff working 60 min more than scheduled is highlighted (YELLOW)
    $loginType = Session::get('login_type');
$sessionStaffId = Session::get('staff_id');
        $staffId = $request->input('staff_id');
         if ($loginType === 'Staff') {
            $staffId = $sessionStaffId;
        }
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $canManualEntry = canAddManualAttendance();

        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
$endOfMonth = Carbon::createFromDate($year, $month, $daysInMonth)->endOfDay();
        $dateRange = collect(range(1, $daysInMonth))
            ->map(fn($d) => Carbon::createFromDate($year, $month, $d)->format('Y-m-d'));

        $staffList = Staffmodel::with('role')
            ->where('status', 0)
            ->when($staffId, fn($q) => $q->where('staff_id', $staffId))
            ->orderBy('staff_name', 'asc')
            ->get();
        $attendance = Attendancemodel::whereYear('time_in', $year)
            ->whereMonth('time_in', $month)
            ->get()
            ->groupBy(['staff_id', fn($rec) => Carbon::parse($rec->time_in)->format('Y-m-d')]);

        $holidays = Attendancemodel::where('status', 'Holiday')
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->groupBy([
                    'staff_id',
                    fn($rec) => Carbon::parse($rec->date)->format('Y-m-d')
                ]);

        foreach ($holidays as $hStaffId => $dates) {
                    foreach ($dates as $hDate => $records) {
                        if (!isset($attendance[$hStaffId][$hDate])) {
                            $attendance[$hStaffId][$hDate] = $records;
                        }
                    }
                }

        $leaves = LeaveRequest::where('status', 'approved')
            ->whereYear('start_date', '<=', $year)
            ->whereYear('end_date', '>=', $year)
            ->whereMonth('start_date', '<=', $month)
            ->whereMonth('end_date', '>=', $month)
            ->get()
            ->groupBy('staff_id');



        $data = [];

        foreach ($staffList as $staff) {
            $roleName = $staff->role->role_name ?? 'N/A';
    $row = [
        'staff_name' => $staff->staff_name . ' (' . ucfirst($roleName) . ')'
    ];



            foreach ($dateRange as $date) {
                    $currentDate = Carbon::parse($date)->startOfDay();
               $dayOfWeek = $currentDate->dayName; // e.g., 'Sunday'
                $att = $attendance[$staff->staff_id][$date][0] ?? null;

            //     $leave = ($leaves[$staff->staff_id] ?? collect())->firstWhere(fn($lv) =>
            //     $currentDate->betweenIncluded($lv->start_date, $lv->end_date)
            // );


            $cellContent = '';
            $buttonClass = '';
            $isHalfDay = false;

           $isWeekOff = strtolower($dayOfWeek) === strtolower($staff->week_off_day);

          $isInsideLeaveRange = false;
$staffLeaves = $leaves[$staff->staff_id] ?? collect();

foreach ($staffLeaves as $lv) {
    $start = Carbon::parse($lv->start_date)->startOfDay();
    $end   = Carbon::parse($lv->end_date)->endOfDay();

    if ($currentDate->between($start, $end)) {
        $isInsideLeaveRange = true;
        break;
    }
}

// If NOT inside any range, check 1 day before and after
if (!$isInsideLeaveRange) {
    $prev = $currentDate->copy()->subDay();
    $next = $currentDate->copy()->addDay();

    $hasPrevLeave = $staffLeaves->contains(fn($lv) =>
        $prev->between($lv->start_date, $lv->end_date)
    );

    $hasNextLeave = $staffLeaves->contains(fn($lv) =>
        $next->between($lv->start_date, $lv->end_date)
    );

    if ($hasPrevLeave && $hasNextLeave) {
        $isInsideLeaveRange = true;
    }
}




// Access leaves grouped by staff_id, then search the collection for a matching date
$leave = ($leaves[$staff->staff_id] ?? collect())->firstWhere(fn($lv) =>
    // Use Carbon methods for safe comparison
    $currentDate->betweenIncluded($lv->start_date, $lv->end_date)
);

                if ($att && strtolower($att->status) === 'holiday') {
                    $buttonClass = 'btn-dark';
                    $cellContent = 'H';
                }
                else if ($att && $att->time_in && $att->time_out) {
                    $clockInTime = Carbon::parse($att->time_in);
                    $clockOutTime = Carbon::parse($att->time_out);
                    $actualWorkMinutes = $clockInTime->diffInMinutes($clockOutTime);
                    $scheduledWorkMinutes = $this->calculateScheduledWorkMinutes($staff->shift_timing, $date);

                    $buttonClass = 'btn-success';
                    $cellContent = 'P';

                    if ($actualWorkMinutes > 0 && $actualWorkMinutes < $HALF_DAY_THRESHOLD_MINUTES) {
                        // Assuming you want to display the Half Day status directly in the cell
                        $cellContent = 'H.D.';
                        $buttonClass = 'btn-info'; // Use a distinct color for Half Day
                        $isHalfDay = true;
                    }

                    if (!$isHalfDay) { // Check the new flag
                        $isOverTime = ($scheduledWorkMinutes > 0 && ($actualWorkMinutes - $scheduledWorkMinutes) >= $OVER_TIME_HIGHLIGHT_MINUTES);
                        $isUnderTime = ($actualWorkMinutes < $UNDER_TIME_HIGHLIGHT_MINUTES);

                        if ($isUnderTime) {
                            $buttonClass = 'btn-danger highlight-under-time';
                            $cellContent = 'P';
                        } elseif ($isOverTime) {
                            $buttonClass = 'btn-warning highlight-over-time';
                        }
                    }



                }
                elseif ($isWeekOff && $isInsideLeaveRange) {

                    $buttonClass = 'btn-secondary';
                    $cellContent = 'DEL';

                }
                elseif ($isWeekOff) {
                    // Normal Week Off
                    $buttonClass = 'btn-primary';
                    $cellContent = 'WO';
                }
                 elseif ($leave) {
                         // 4. Approved Leave
                        $buttonClass = 'btn-warning manual-entry-trigger';
                        $cellContent = 'L (' . strtoupper($leave->leave_type) . ')';
                } else {
                    // 5. Absent/Missing Attendance
                $buttonClass = 'btn-danger manual-entry-trigger';
                $cellContent = 'A';

                }

                // Build the final button/cell content
                if ($cellContent === 'DEL') {
                    // Week off is final text, no button/modal
                    $row[$date] = '<span class="text-secondary">' . $cellContent . '</span>';
                } else {
                    $dataTarget = (($att && $att->time_out) || $isHalfDay) ? '#attendanceDetailModal' : '#manualAttendanceModal';
                    $dataClass = (($att && $att->time_out) || $isHalfDay) ? 'attendance-detail-btn' : 'manual-entry-trigger';
                     $isManualTarget = ($dataTarget === '#manualAttendanceModal');
                        if ($isManualTarget && !$canManualEntry) {
                            // No permission -> show status only, not clickable
                            $row[$date] = '<span class="badge ' . str_replace('btn-', 'bg-', $buttonClass) . '" ' .
                                'style="padding:4px 8px;font-size:10px;" ' .
                                'title="You do not have permission to add manual attendance">' . $cellContent . '</span>';
                        } else {


                            // For attendance/leave/absent, use a clickable button
                            $row[$date] = '<button type="button" class="btn btn-sm ' . $buttonClass . ' ' . $dataClass . '" ' .
                                'data-staff-id="' . $staff->staff_id . '" ' .
                                'data-staff-name="' . e($staff->staff_name . ' (' . ucfirst($roleName) . ')') . '" ' .
                                'data-date="' . $date . '" ' .
                                'data-time-in="' . ($att ? Carbon::parse($att->time_in)->format('d-m-Y g:i A') : '') . '" ' .
                                'data-time-out="' . ($att && $att->time_out ? Carbon::parse($att->time_out)->format('d-m-Y g:i A') : '') . '" ' .
                                'data-is-half-day="' . ($isHalfDay ? 'true' : 'false') . '" ' .
                                'data-bs-toggle="modal" data-bs-target="' . $dataTarget . '" ' .
                                'style="padding: 2px 5px; font-size: 10px;">' . $cellContent . '</button>';
                         }
                }
            }

            $data[] = $row;
        }

        // Add 'staff_name' to the list of raw columns
        return DataTables::of($data)->rawColumns(array_merge(['staff_name'], $dateRange->toArray()))->make(true);
    }
    private function calculateScheduledWorkMinutes($shiftTimingString, $date)
    {
        $today = Carbon::parse($date)->toDateString();
        $shiftStartTimeStr = $this->extractShiftStartTime($shiftTimingString);
        $shiftEndTimeStr = $this->extractShiftEndTime($shiftTimingString);

        if (!$shiftStartTimeStr || !$shiftEndTimeStr) return 0;

        try {
            $scheduledShiftStart = Carbon::parse($today . ' ' . $shiftStartTimeStr);
            $scheduledShiftEnd = Carbon::parse($today . ' ' . $shiftEndTimeStr);

            // Handle overnight shifts
            if ($scheduledShiftStart->greaterThan($scheduledShiftEnd)) {
                $scheduledShiftEnd->addDay();
            }

            return $scheduledShiftStart->diffInMinutes($scheduledShiftEnd);
        } catch (\Exception $e) {
            return 0;
        }
    }
    private function extractShiftStartTime($shiftTimingString)
    {
        // The format is like 'Label: HH:MM AM/PM - HH:MM AM/PM'
        $parts = explode(':', $shiftTimingString, 2);
        if (count($parts) < 2) return null;
        $timePart = trim($parts[1]);
        $times = explode(' - ', $timePart, 2);
        return trim($times[0]);
    }

    private function extractShiftEndTime($shiftTimingString)
    {
        // The format is like 'Label: HH:MM AM/PM - HH:MM AM/PM'
        $parts = explode(' - ', $shiftTimingString);
        if (count($parts) < 2) return null;
        $endTime = trim(end($parts));
        return $endTime;
    }

    public function exportPdf(Request $request)
    {
        $exportData = $this->getAttendanceDataForExport($request);
        $monthName = $exportData['monthName'];
        $year = $exportData['year'];
        $data = $exportData['data'];
        $header = $exportData['header'];
         $logoPath = $exportData['logoPath'];
        $filename = "Attendance_Report_{$monthName}_{$year}.pdf";

        //  STEP 1: Modify the data array for PDF only
    $modifiedData = [];
    foreach ($data as $row) {
        $newRow = [];
        foreach ($row as $key => $value) {
            if ($value === '') {
                $newRow[$key] = 'P'; // Change Present symbol
            } elseif ($value === '') {
                $newRow[$key] = 'A'; // Change Absent symbol
            } else {
                $newRow[$key] = $value; // Keep Staff Name and Leave data as is
            }
        }
        $modifiedData[] = $newRow;
    }
    // END STEP 1

         $pdf = Pdf::loadView('superadmin.attendance_report', compact('modifiedData', 'header', 'monthName', 'year','logoPath'))
                // Set paper to A4 Landscape for the wide attendance table
                ->setPaper('a4', 'landscape');

        // $pdf = Pdf::loadView('superadmin.attendance_report', compact('data', 'header', 'monthName', 'year','logoPath'))
        //         // Set paper to A4 Landscape for the wide attendance table
        //         ->setPaper('a4', 'landscape');

        //return $pdf->download($filename);
        return $pdf->stream($filename);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CompanyModel;
use App\Models\Staffmodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\Attendancemodel;
use App\Models\DailyWagesStaffModel;
use App\Models\DailyStaffAttendancemodel;
use App\Models\ForgotOutRequestmodel;
class Company extends Controller
{
    protected $actual_path;
    public function __construct()
    {
        $this->actual_path = config('app.actual_url') . '/uploads/';
    }
    public function dashboard()
    {
        $page_title = 'Company Dashboard';
        $page_name = 'Company Dashboard';

        // dd($page_title);
        if (session('login_type') == 'Company') {
            return view('company.staff_dashboard', compact(
                'page_title',
                'page_name'
            ));
        }

        if (session('login_type') == 'Staff') {
            return view('company.staff_dashboard', compact(
                'page_title',
                'page_name'
            ));
        }

        return view('superadmin.dashboard', compact(
            'page_title',
            'page_name'
        ));
    }

    public function company_register_view()
    {
        $page_title = 'Companies Register';
        $page_name = 'Companies Register';
        $menus = DB::table('company_menus')
            ->where('status', 0)
            ->orderBy('menu_order', 'asc')
            ->get()
            ->map(function ($menu) {
                if ($menu->is_submenu) {
                    $menu->submenus = DB::table('company_submenus')
                        ->where('c_menu_id', $menu->c_menu_id)
                        ->where('status', 0)
                        ->orderBy('submenu_order', 'asc')
                        ->get();
                } else {
                    $menu->submenus = collect([]);
                }
                return $menu;
            });
        return view('superadmin.company_register', compact('page_title', 'page_name', 'menus'));
    }
    // public function dashboard()
    // {
    //     $page_title = 'Company Dashboard';
    //     $page_name = 'Company Dashboard';
    //     return view('company/dashboard', compact('page_title', 'page_name'));
    // }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'contact_no' => 'nullable|string|max:20',
            'project_name' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico',
            'registration_number' => 'nullable|string|max:50',
            'company_type' => 'nullable|string|max:50',
            'industry' => 'nullable|string|max:50',
            'founded_date' => 'nullable|date',
            'address' => 'nullable|string',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:100',
            'description' => 'nullable|string',
            'gst_no' => ['nullable', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}$/'],
            'menus' => 'nullable|array',
            'menus.*' => 'integer|exists:company_menus,c_menu_id',
            'submenus' => 'nullable|array',
            'submenus.*' => 'integer|exists:company_submenus,c_sub_menu_id',
            'account_holder_name' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
            'branch_name' => 'nullable|string',
            'account_type' => 'nullable|string',
            'upi_id' => 'nullable|string',
            'upi_qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ];

        if (!$request->has('company_id')) {
            $rules['email'] .= '|unique:companies,email';
            $rules['registration_number'] .= '|unique:companies,registration_number';
        } else {
            $rules['email'] .= '|unique:companies,email,' . $request->company_id . ',company_id';
            $rules['registration_number'] .= '|unique:companies,registration_number,' . $request->company_id . ',company_id';
        }

        if ($request->has('website') && !empty($request->website)) {
            if (!preg_match("~^(?:f|ht)tps?://~i", $request->website)) {
                $request->merge(['website' => 'https://' . $request->website]);
            }
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('logo')) {
            $uploadPath = public_path('assets/uploads/company');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if ($request->has('company_id') && $request->company_id != null) {
                $company = CompanyModel::find($request->company_id);
                if ($company && $company->logo) {
                    $oldLogoPath = $uploadPath . '/' . $company->logo;
                    if (file_exists($oldLogoPath)) {
                        unlink($oldLogoPath);
                    }
                }
            }

            $extension = $request->file('logo')->getClientOriginalExtension();
            $fileName = time() . '_logo.' . $extension;
            $request->file('logo')->move($uploadPath, $fileName);
            $validated['logo'] = $fileName;
        }

        if ($request->hasFile('upi_qr_code')) {
            $uploadPath = public_path('assets/uploads/company');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if ($request->has('company_id') && $request->company_id != null) {
                $company = CompanyModel::find($request->company_id);
                if ($company && $company->upi_qr_code) {
                    $oldQRPath = $uploadPath . '/' . $company->upi_qr_code;
                    if (file_exists($oldQRPath)) {
                        unlink($oldQRPath);
                    }
                }
            }

            $extension = $request->file('upi_qr_code')->getClientOriginalExtension();
            $fileName = time() . '_upi_qr_code.' . $extension;
            $request->file('upi_qr_code')->move($uploadPath, $fileName);
            $validated['upi_qr_code'] = $fileName;
        }

        if ($request->hasFile('signature')) {
            $uploadPath = public_path('assets/uploads/company');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if ($request->has('company_id') && $request->company_id != null) {
                $company = CompanyModel::find($request->company_id);
                if ($company && $company->signature) {
                    $oldSignaturePath = $uploadPath . '/' . $company->signature;
                    if (file_exists($oldSignaturePath)) {
                        unlink($oldSignaturePath);
                    }
                }
            }

            $extension = $request->file('signature')->getClientOriginalExtension();
            $fileName = time() . '_signature.' . $extension;
            $request->file('signature')->move($uploadPath, $fileName);
            $validated['signature'] = $fileName;
        }
        if ($request->hasFile('favicon')) {
            $uploadPath = public_path('assets/uploads/company');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            if ($request->has('company_id') && $request->company_id != null) {
                $company = CompanyModel::find($request->company_id);
                if ($company && $company->favicon) {
                    $oldFaviconPath = $uploadPath . '/' . $company->favicon;
                    if (file_exists($oldFaviconPath)) {
                        unlink($oldFaviconPath);
                    }
                }
            }

            $extension = $request->file('favicon')->getClientOriginalExtension();
            $fileName = time() . '_favicon.' . $extension;
            $request->file('favicon')->move($uploadPath, $fileName);
            $validated['favicon'] = $fileName;
        }

        if ($request->has('company_id') && $request->company_id != null) {
            $company = CompanyModel::find($request->company_id);
            if (!$company) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found.',
                ], 404);
            }

            $company->update($validated);
        } else {
            $validated['created_by'] = 0;
            $validated['password'] = md5(123456);
            $validated['status'] = 0;

            $company = CompanyModel::create($validated);
        }

        if ($company) {
            if ($request->has('menus') || $request->has('submenus')) {
                // dd($request->all());
                DB::table('company_permission')->where('company_id', $company->company_id)->delete();
                $menuPermissions = [];
                $now = now();

                if ($request->has('menus')) {
                    foreach ($request->menus as $menuId) {
                        $menuPermissions[] = [
                            'company_id' => $company->company_id,
                            'c_menu_id' => $menuId,
                            'c_sub_menu_id' => null,
                            'status' => 0,
                            'created_by' => 0,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if ($request->has('submenus')) {
                    foreach ($request->submenus as $submenuId) {
                        $submenu = DB::table('company_submenus')->where('c_sub_menu_id', $submenuId)->first();
                        if ($submenu) {
                            $menuPermissions[] = [
                                'company_id' => $company->company_id,
                                'c_menu_id' => $submenu->c_menu_id,
                                'c_sub_menu_id' => $submenuId,
                                'status' => 0,
                                'created_by' => 0,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        }
                    }
                }

                if (!empty($menuPermissions)) {
                    DB::table('company_permission')->insert($menuPermissions);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $request->has('company_id') && $request->company_id != null ? 'Company updated successfully' : 'Company created successfully',
            ]);
        } else {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create or update company.',
            ], 500);
        }
    }

    public function list()
    {
        $companies = CompanyModel::select(['company_id', 'logo', 'name', 'email', 'contact_no', 'company_type', 'status']);
        return DataTables::of($companies)
            ->addIndexColumn()
            ->addColumn('logo', function ($company) {
                $logo = $company->logo ? $this->actual_path . '/company/' . $company->logo : $this->actual_path . '/company.png';
                return '<img src="' . $logo . '" alt="Logo" style="max-height:50px; max-width:100px;">';
            })
            ->addColumn('action', function ($company) {
                return '
            <div class="btn-group">
                <button class="btn btn-sm btn-primary edit-btn" data-id="' . $company->company_id . '">Edit</button>
                <button class="btn btn-sm btn-danger delete-btn" data-id="' . $company->company_id . '">Delete</button>
            </div>
            ';
            })
            ->editColumn('status', function ($company) {
                return $company->status
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-secondary">Inactive</span>';
            })
            ->rawColumns(['logo', 'action', 'status'])
            ->make(true);
    }
    public function edit($id)
    {

        // dd($id);
        $company = CompanyModel::with([
            'permissions' => function ($query) {
                $query->select('company_id', 'c_menu_id', 'c_sub_menu_id');
            },
        ])->findOrFail($id);
        $menus = [];
        $submenus = [];
        foreach ($company->permissions as $permission) {
            if ($permission->c_sub_menu_id) {
                $submenus[] = $permission->c_sub_menu_id;
            } else {
                $menus[] = $permission->c_menu_id;
            }
        }
        $company->menus = $menus;
        $company->submenus = $submenus;
        return response()->json($company);
    }

    public function getCountries(Request $request)
    {
        $search = $request->search;
        $page = $request->page ?? 1;
        $fetchOne = $request->fetch_one ?? false;
        $perPage = 10;

        $query = DB::table('mst_country')
            ->select('c_id as id', 'country_name as text');

        if ($fetchOne && $search) {

            return $query->where('c_id', $search)->first();
        }

        if ($search) {
            $query->where('country_name', 'like', '%' . $search . '%');
        }

        $total = $query->count();
        $results = $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'results' => $results,
            'total_count' => $total,
        ]);
    }

    public function getStates(Request $request)
    {
        $search = $request->search;
        $page = $request->page ?? 1;
        $countryId = $request->country_id;
        $fetchOne = $request->fetch_one ?? false;
        $perPage = 10;

        $query = DB::table('mst_state')
            ->select('state_id as id', 'state_name as text')
            ->where('c_id', $countryId);

        if ($fetchOne && $search) {

            return $query->where('state_id', $search)->first();
        }

        if ($search) {
            $query->where('state_name', 'like', '%' . $search . '%');
        }

        $total = $query->count();
        $results = $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'results' => $results,
            'total_count' => $total,
        ]);
    }

    public function getCities(Request $request)
    {
        $search = $request->search;
        $page = $request->page ?? 1;
        $stateId = $request->state_id;
        $fetchOne = $request->fetch_one ?? false;
        $perPage = 10;

        $query = DB::table('mst_city')
            ->select('city_id as id', 'city_name as text');

        if ($fetchOne && $search) {

            return $query->where('city_id', $search)->first();
        }

        if ($search) {
            $query->where('city_name', 'like', '%' . $search . '%');
        }

        $total = $query->count();
        $results = $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'results' => $results,
            'total_count' => $total,
        ]);
    }

    public function deleteUpiQr(Request $request)
    {
        $companyId = Session::get('company_id');
        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or session expired.'], 401);
        }

        $company = CompanyModel::find($companyId);
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company not found.'], 404);
        }

        if ($company->upi_qr_code) {
            $filePath = public_path('assets/uploads/company/' . $company->upi_qr_code);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $company->update(['upi_qr_code' => null]);
            return response()->json(['success' => true, 'message' => 'UPI QR Code deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'No UPI QR Code found to delete.']);
    }

    public function deleteSignature(Request $request)
    {
        $companyId = Session::get('company_id');
        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or session expired.'], 401);
        }

        $company = CompanyModel::find($companyId);
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company not found.'], 404);
        }

        if ($company->signature) {
            $filePath = public_path('assets/uploads/company/' . $company->signature);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $company->update(['signature' => null]);
            return response()->json(['success' => true, 'message' => 'Signature deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'No Signature found to delete.']);
    }

    public function getPresentStaffList(Request $request)
    {
        // Get the current date in the format used in your database (e.g., 'Y-m-d')
        $today = Carbon::today()->toDateString();

        $highlightThresholdMinutes = 60;
        $overTimeThresholdMinutes = 60; // Flag if worked 60 minutes (1 hour) more than scheduled
        $underTimeThresholdMinutes = 240; // Flag if total work is less than 4 hours (4 * 60 minutes)

        // 1. Fetch all *available* staff (assuming all staff in Staffmodel are available)
        $availableStaff = Staffmodel::where('status', 0)->get(); // Adjust model and condition as necessary

        // 2. Fetch today's attendance records and map them by staff_id for quick lookup
        $todayAttendance = Attendancemodel::whereDate('time_in', $today)
            ->whereNotNull('time_in')
            //->whereNotNull('time_out')
            ->where(function ($query) {
                $query->where('status', '!=', 'leave')->orWhereNull('status');
            })
            ->get()
            ->keyBy('staff_id'); // Key the collection by staff_id

        $data = $availableStaff->map(function ($staff) use ($todayAttendance, $today, $overTimeThresholdMinutes, $underTimeThresholdMinutes) {
            $attendance = $todayAttendance->get($staff->staff_id); // Get attendance for this staff member

            $isPresent = (bool) $attendance;
            $isEarlyCheckIn = false;
            $isOverTime = false;
            $isUnderTime = false;
            $totalWorkMinutes = 0;

            if ($isPresent && $staff->shift_timing && $attendance->time_out) {

                $clockInTime = Carbon::parse($attendance->time_in);
                $clockOutTime = Carbon::parse($attendance->time_out);
                $totalWorkMinutes = $clockInTime->diffInMinutes($clockOutTime);
                $shiftStartTimeStr = $this->extractShiftStartTime($staff->shift_timing);
                $shiftEndTimeStr = $this->extractShiftEndTime($staff->shift_timing);

                if ($shiftStartTimeStr && $shiftEndTimeStr) {
                    // B. Combine today's date with the shift time to create a full Carbon object
                    try {
                        // This handles AM/PM parsing, e.g., '09:00 AM'
                        $scheduledShiftStart = Carbon::parse($today . ' ' . $shiftStartTimeStr);
                        $scheduledShiftEnd = Carbon::parse($today . ' ' . $shiftEndTimeStr);
                        //$clockInTime = Carbon::parse($attendance->time_in);

                        // Handle overnight shifts (e.g., 7 PM to 5 AM)
                        if ($scheduledShiftStart->greaterThan($scheduledShiftEnd)) {
                            $scheduledShiftEnd->addDay();
                        }

                        $scheduledWorkMinutes = $scheduledShiftStart->diffInMinutes($scheduledShiftEnd);

                        // Check for Under Time Alert (if total worked minutes is too low)
                        // The requirement is: total work hour are less than 4 hour (240 minutes)
                        if ($totalWorkMinutes < $underTimeThresholdMinutes) {
                            $isUnderTime = true;
                        }
                        // Check for Over Time Alert (if worked significantly more than scheduled)
                        $difference = $totalWorkMinutes - $scheduledWorkMinutes;
                        if ($difference >= $overTimeThresholdMinutes) {
                            $isOverTime = true;
                        }

                        //    $minutesEarly = $clockInTime->diffInMinutes($scheduledShiftStart, false);

                        //     // D. Check for EARLY check-in: $minutesEarly must be positive AND >= 60 minutes.
                        //     if ($minutesEarly >= $highlightThresholdMinutes) {
                        //         $isEarlyCheckIn = true;
                        //     }
                    } catch (\Exception $e) {
                        // Handle parsing errors if the time format is unexpected
                        // console.log("Error parsing shift time for staff ID: " . $staff->staff_id); 
                    }
                }
            }


            $resultArray = [
                'staff_id' => $staff->staff_id,
                'staff_name' => $staff->staff_name ?? 'N/A',
                'is_present' => $isPresent,
                'is_over_time' => $isOverTime,
                'is_under_time' => $isUnderTime,
                //'is_early_check_in' => $isEarlyCheckIn,
                'time_in' => $isPresent ? Carbon::parse($attendance->time_in)->format('h:i A') : '-',
                'time_out' => $isPresent && $attendance->time_out ? Carbon::parse($attendance->time_out)->format('h:i A') : '-',
                'face_image_in_url' => $isPresent ? ($attendance->face_image_in_url ?? '') : '',
                'face_image_out_url' => $isPresent && $attendance->face_image_out_url ? $attendance->face_image_out_url : '',

            ];

            // Log the resulting array for THIS staff member
            //Log::info('Staff Shift Check Result:', $resultArray); 

            return $resultArray;
        })->sortByDesc('is_present')->values(); // Sort to show present staff first

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function getDailyWagesPresentStaffList(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $overTimeThresholdMinutes = 60; // 1 hour extra
        $underTimeThresholdMinutes = 240; // 4 hours minimum

        // 1️⃣ Get Active Daily Wages Staff
        $availableStaff = DailyWagesStaffModel::where('status', 0)->get();

        // 2️⃣ Get Today Attendance
        $todayAttendance = DailyStaffAttendancemodel::whereDate('date', $today)
            ->whereNotNull('time_in')
            ->where(function ($query) {
                $query->where('status', '!=', 'leave')
                    ->orWhereNull('status');
            })
            ->get()
            ->keyBy('staff_id');

        // 3 Prepare Data
        $data = $availableStaff->map(function ($staff) use ($todayAttendance, $today, $overTimeThresholdMinutes, $underTimeThresholdMinutes) {

            $attendance = $todayAttendance->get($staff->dw_staff_id);

            $isPresent = (bool) $attendance;
            $isOverTime = false;
            $isUnderTime = false;
            $totalWorkMinutes = 0;

            if ($isPresent && $attendance->time_out) {

                $clockIn = Carbon::parse($attendance->time_in);
                $clockOut = Carbon::parse($attendance->time_out);

                $totalWorkMinutes = $clockIn->diffInMinutes($clockOut);

                // Under Time
                if ($totalWorkMinutes < $underTimeThresholdMinutes) {
                    $isUnderTime = true;
                }

                // Shift Based OT Check
                if ($staff->shift_timing) {
                    $shiftHours = $this->extractShiftHours($staff->shift_timing);
                    if ($shiftHours > 0) {

                        $expectedMinutes = $shiftHours * 60;

                        $extraMinutes = $totalWorkMinutes - $expectedMinutes;

                        if ($extraMinutes >= $overTimeThresholdMinutes) {
                            $isOverTime = true;
                        }
                    }
                }
            }

            return [
                'staff_id' => $staff->dw_staff_id,
                'staff_name' => $staff->staff_name,
                'is_present' => $isPresent,
                'is_over_time' => $isOverTime,
                'is_under_time' => $isUnderTime,
                'time_in' => $isPresent ? Carbon::parse($attendance->time_in)->format('h:i A') : '-',
                'time_out' => ($isPresent && $attendance->time_out)
                    ? Carbon::parse($attendance->time_out)->format('h:i A')
                    : '-',
                'face_image_in_url' => $isPresent ? ($attendance->face_image_in_url ?? '') : '',
                'face_image_out_url' => $isPresent && $attendance->face_image_out_url ? $attendance->face_image_out_url : '',
            ];
        })
            ->sortByDesc('is_present')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getStaffAttendanceList(Request $request)
    {
        $forWhom = $request->input('for_whom');   // 'staff' | 'dailystaff'
        $what = $request->input('what');       // 'all' | 'present' | 'absent' | 'on_leave'
        $date = $request->input('date', Carbon::today()->toDateString());

        if (!in_array($forWhom, ['staff', 'dailystaff'])) {
            return response()->json(['error' => 'Invalid for_whom. Use: staff or dailystaff'], 422);
        }
        if (!in_array($what, ['all', 'present', 'absent', 'on_leave'])) {
            return response()->json(['error' => 'Invalid what. Use: all, present, absent, on_leave'], 422);
        }


        if ($forWhom === 'dailystaff') {

            $allStaff = DailyWagesStaffModel::where('status', 0)->get();

            $presentIds = DailyStaffAttendancemodel::whereDate('time_in', $date)
                ->pluck('staff_id')
                ->toArray();

            if ($what === 'present') {
                $staffQuery = DailyWagesStaffModel::where('status', 0)
                    ->whereIn('dw_staff_id', $presentIds);
            } elseif ($what === 'absent') {
                $staffQuery = DailyWagesStaffModel::where('status', 0)
                    ->whereNotIn('dw_staff_id', $presentIds);
            } elseif ($what === 'on_leave') {

                return DataTables::of(collect([]))
                    ->addColumn('note', fn() => 'Leave tracking not applicable for daily wages staff.')
                    ->make(true);
            } else {

                $staffQuery = DailyWagesStaffModel::where('status', 0);
            }

            $staffList = $staffQuery
                ->orderBy('staff_name', 'asc')
                ->get();

            $attendanceMap = DailyStaffAttendancemodel::whereDate('time_in', $date)
                ->whereIn('staff_id', $staffList->pluck('dw_staff_id')->toArray())
                ->get()
                ->keyBy('staff_id');

            $rows = $staffList->map(function ($staff) use ($attendanceMap, $date, $presentIds) {
                $att = $attendanceMap->get($staff->dw_staff_id);

                $isPresent = in_array($staff->dw_staff_id, $presentIds);

                $timeIn = $att?->time_in ? Carbon::parse($att->time_in)->format('h:i A') : '-';
                $timeOut = $att?->time_out ? Carbon::parse($att->time_out)->format('h:i A') : '-';

                $workingHours = '-';
                if ($att?->time_in && $att?->time_out) {
                    $diff = Carbon::parse($att->time_in)->diff(Carbon::parse($att->time_out));
                    $workingHours = $diff->format('%H:%I hrs');
                }

                $statusBadge = $isPresent
                    ? '<span class="badge bg-success">Present</span>'
                    : '<span class="badge bg-danger">Absent</span>';

                return [
                    'staff_id' => $staff->dw_staff_id,
                    'staff_code' => $staff->staff_code ?? '-',
                    'staff_name' => $staff->staff_name ?? '-',
                    'per_hour_rate' => '₹ ' . number_format($staff->per_hour_rate ?? 0, 2),
                    'status' => $statusBadge,
                    'time_in' => $timeIn,
                    'time_out' => $timeOut,
                    'working_hours' => $workingHours,
                    'date' => Carbon::parse($date)->format('d-m-Y'),
                ];
            });

            return DataTables::of($rows)
                ->rawColumns(['status'])
                ->make(true);
        }


        $allStaff = Staffmodel::where('status', 0)->get();
        $allStaffIds = $allStaff->pluck('staff_id')->toArray();

        $presentIds = Attendancemodel::whereDate('time_in', $date)
            ->whereIn('status', ['present', 'Manual Update'])
            ->pluck('staff_id')
            ->unique()
            ->toArray();

        $onLeaveIds = DB::table('leave_request')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->pluck('staff_id')
            ->unique()
            ->toArray();

        $absentIds = array_values(
            array_diff($allStaffIds, $presentIds, $onLeaveIds)
        );

        switch ($what) {
            case 'present':
                $filteredIds = $presentIds;
                break;
            case 'absent':
                $filteredIds = $absentIds;
                break;
            case 'on_leave':
                $filteredIds = $onLeaveIds;
                break;
            default:
                $filteredIds = $allStaffIds;
        }

        $staffList = Staffmodel::with('role')
            ->where('status', 0)
            ->whereIn('staff_id', $filteredIds)
            ->orderBy('staff_name', 'asc')
            ->get();

        $attendanceMap = Attendancemodel::whereDate('time_in', $date)
            ->whereIn('staff_id', $filteredIds)
            ->get()
            ->keyBy('staff_id');

        $leaveMap = DB::table('leave_request')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->whereIn('staff_id', $filteredIds)
            ->get()
            ->keyBy('staff_id');

        $rows = $staffList->map(function ($staff) use ($attendanceMap, $leaveMap, $presentIds, $onLeaveIds, $absentIds, $date) {
            $att = $attendanceMap->get($staff->staff_id);
            $leave = $leaveMap->get($staff->staff_id);

            if (in_array($staff->staff_id, $presentIds)) {
                $attStatus = 'present';
            } elseif (in_array($staff->staff_id, $onLeaveIds)) {
                $attStatus = 'on_leave';
            } else {
                $attStatus = 'absent';
            }

            $statusBadge = match ($attStatus) {
                'present' => '<span class="badge bg-success">Present</span>',
                'on_leave' => '<span class="badge bg-warning text-dark">On Leave</span>',
                default => '<span class="badge bg-danger">Absent</span>',
            };

            $timeIn = $att?->time_in ? Carbon::parse($att->time_in)->format('h:i A') : '-';
            $timeOut = $att?->time_out ? Carbon::parse($att->time_out)->format('h:i A') : '-';

            $workingHours = '-';
            if ($att?->time_in && $att?->time_out) {
                $diff = Carbon::parse($att->time_in)->diff(Carbon::parse($att->time_out));
                $workingHours = $diff->format('%H:%I hrs');
            }

            $lateStatusBadge = '-';
            if ($att?->late_status) {
                $lateStatusBadge = match ($att->late_status) {
                    'On Time' => '<span class="badge bg-success">On Time</span>',
                    'Late' => '<span class="badge bg-warning text-dark">Late</span>',
                    'Half Day' => '<span class="badge bg-info text-dark">Half Day</span>',
                    default => '<span class="badge bg-secondary">' . $att->late_status . '</span>',
                };
            }


            $leaveType = $leave ? strtoupper($leave->leave_type) : '-';

            return [
                'staff_id' => $staff->staff_id,
                'staff_code' => $staff->staff_code ?? '-',
                'badge_no' => $staff->badge_no ?? '-',
                'staff_name' => $staff->staff_name ?? '-',
                'role' => $staff->role->role_name ?? '-',
                'shift_timing' => $staff->shift_timing ?? '-',
                'status' => $statusBadge,
                'time_in' => $timeIn,
                'time_out' => $timeOut,
                'working_hours' => $workingHours,
                'late_status' => $lateStatusBadge,
                'leave_type' => $leaveType,
                'date' => Carbon::parse($date)->format('d-m-Y'),
            ];
        });

        return DataTables::of($rows)
            ->rawColumns(['status', 'late_status'])
            ->make(true);
    }

    public function getDashboardData(Request $request)
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $isFiltered = ($startDate && $endDate);

            if ($isFiltered) {
                $start = Carbon::createFromFormat('d/m/Y', $startDate)->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay();
            } else {
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfDay();
            }

            $userId = getCreatedBy();
            $role = Session::get('login_type');

            $isRestricted = in_array($role, ['Staff', 'Manager']);

            // 1. MASTER DATA COUNTS
            $leadCountQuery = \App\Models\EnquiryManageModel::where('status', 0);
            $dealCountQuery = \App\Models\Deal::query();
            $quotationCountQuery = \App\Models\Quotation::query();
            $productCountQuery = \App\Models\ProductModel::query();
            $customerCountQuery = \App\Models\Customer::query();
            $staffCountQuery = \App\Models\Staffmodel::where('status', 0);

            if ($isFiltered) {
                $leadCountQuery->whereBetween('enquiry_date', [$start->toDateString(), $end->toDateString()]);
                $dealCountQuery->whereBetween('created_at', [$start, $end]);
                $quotationCountQuery->whereBetween('created_at', [$start, $end]);
                $customerCountQuery->whereBetween('created_at', [$start, $end]);
            }

            if ($isRestricted) {
                $staffId = Session::get('staff_id') ?? $userId;

                $leadCountQuery->where(function ($q) use ($staffId) {
                    // 1. Directly assigned to or created by the staff
                    $q->where('assigned_to', $staffId)
                        ->orWhere('created_by', $staffId)
                        // 2. Has a note assigned to or created by this staff
                        ->orWhereIn('enquiry_id', function ($sub) use ($staffId) {
                            $sub->select('reference_id')
                                ->from('tbl_crm_notes')
                                ->where('reference_type', 'lead')
                                ->where(function ($w) use ($staffId) {
                                    $w->where('assigned_to', $staffId)
                                      ->orWhere('created_by', $staffId);
                                })
                                ->where('status', 0);
                        })
                        // 3. Has a scheduled call assigned to or created by this staff
                        ->orWhereIn('enquiry_id', function ($sub) use ($staffId) {
                            $sub->select('reference_id')
                                ->from('tbl_crm_calls')
                                ->where('reference_type', 'lead')
                                ->where(function ($w) use ($staffId) {
                                    $w->where('assigned_to', $staffId)
                                      ->orWhere('created_by', $staffId);
                                })
                                ->where('status', 0);
                        });
                });

                $dealCountQuery->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
                });
                $quotationCountQuery->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
                });
            }

            $totalLeads = $leadCountQuery->count();
            $totalDeals = $dealCountQuery->count();
            $totalQuotations = $quotationCountQuery->count();
            $totalProducts = $productCountQuery->count();
            $totalCustomers = $customerCountQuery->count();
            $totalStaff = $staffCountQuery->count();

            // 2. PERFORMANCE ANALYTICS
            $leadsInPeriodQuery = \App\Models\EnquiryManageModel::where('status', 0);
            if ($isFiltered) {
                $leadsInPeriodQuery->whereBetween('enquiry_date', [$start->toDateString(), $end->toDateString()]);
            }
            if ($isRestricted) {
                $staffId = Session::get('staff_id') ?? $userId;
                $leadsInPeriodQuery->where(function ($q) use ($staffId) {
                    $q->where('assigned_to', $staffId)
                        ->orWhere('created_by', $staffId)
                        ->orWhereIn('enquiry_id', function ($sub) use ($staffId) {
                            $sub->select('reference_id')->from('tbl_crm_notes')
                                ->where('reference_type', 'lead')
                                ->where(function ($w) use ($staffId) {
                                    $w->where('assigned_to', $staffId)
                                      ->orWhere('created_by', $staffId);
                                })
                                ->where('status', 0);
                        })
                        ->orWhereIn('enquiry_id', function ($sub) use ($staffId) {
                            $sub->select('reference_id')->from('tbl_crm_calls')
                                ->where('reference_type', 'lead')
                                ->where(function ($w) use ($staffId) {
                                    $w->where('assigned_to', $staffId)
                                      ->orWhere('created_by', $staffId);
                                })
                                ->where('status', 0);
                        });
                });
            }
            $leadsInPeriodCount = $leadsInPeriodQuery->count();

            $dealsInPeriodQuery = \App\Models\Deal::query();
            if ($isFiltered) {
                $dealsInPeriodQuery->whereBetween('created_at', [$start, $end]);
            }
            if ($isRestricted) {
                $dealsInPeriodQuery->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
                });
            }
            $dealsInPeriodCount = $dealsInPeriodQuery->count();

            $salesInPeriodQuery = \App\Models\Deal::where('deal_stage', 5);
            if ($isFiltered) {
                $salesInPeriodQuery->whereBetween('closing_date', [$start->toDateString(), $end->toDateString()]);
            }
            if ($isRestricted) {
                $salesInPeriodQuery->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
                });
            }
            $salesInPeriodAmount = $salesInPeriodQuery->sum('amount');
            $salesInPeriodCount = $salesInPeriodQuery->count();

            $conversionRate = $dealsInPeriodCount > 0
                ? round(($salesInPeriodCount / $dealsInPeriodCount) * 100, 2)
                : 0.00;

            // 3. SCHEDULED CALLS (Today, Upcoming, Overdue)
            $callsQuery = \App\Models\CrmCall::with(['assignedStaff', 'deal.lead', 'lead'])
                ->where('status', 0);

            if ($isRestricted) {
                $callsQuery->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
                });
            }

            $allCalls = $callsQuery->get()->map(function ($call) {
                $call->call_owner_name = get_createdby_name($call->created_by) ?: 'System';
                $dateObj = Carbon::parse($call->call_date . ' ' . $call->call_time);
                $call->formatted_date_time = $dateObj->format('d/m/Y (h:i A)');
                $call->datetime_sort = $dateObj->timestamp;

                if ($call->reference_type === 'deal') {
                    $deal = $call->deal;
                    if ($deal && $deal->lead) {
                        $name = trim($deal->lead->company_name ?? $deal->lead->contact_person_name ?? '');
                        $call->reference_module = "Deal: " . ($deal->lead->enquiry_no ?? '');
                        if ($name !== '') {
                            $call->reference_module .= " ($name)";
                        }
                    } else {
                        $call->reference_module = 'N/A';
                    }
                } elseif ($call->reference_type === 'lead') {
                    $lead = $call->lead;
                    if ($lead) {
                        $name = trim($lead->company_name ?? $lead->contact_person_name ?? '');
                        $call->reference_module = "Lead: " . ($lead->enquiry_no ?? '');
                        if ($name !== '') {
                            $call->reference_module .= " ($name)";
                        }
                    } else {
                        $call->reference_module = 'N/A';
                    }
                } else {
                    $call->reference_module = 'N/A';
                }
                return $call;
            });

            $todayDateStr = Carbon::today()->toDateString();

            $callsToday = $allCalls->filter(function ($call) use ($todayDateStr) {
                return $call->call_date === $todayDateStr && $call->call_status == 0;
            })->sortByDesc('created_at')->values();

            $callsUpcoming = $allCalls->filter(function ($call) use ($todayDateStr) {
                return $call->call_date > $todayDateStr && $call->call_status == 0;
            })->sortByDesc('created_at')->values();

            $callsOverdue = $allCalls->filter(function ($call) use ($todayDateStr) {
                return $call->call_date < $todayDateStr && $call->call_status == 0;
            })->sortByDesc('created_at')->values();

            $followUpsQuery = \App\Models\CrmNote::with(['assignedStaff', 'createdBy'])
                ->where('status', 0)
                ->where('is_completed', 0)
                ->whereNotNull('follow_up_date');

            if ($isRestricted) {
                $followUpsQuery->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
                });
            }

            $upcomingFollowUps = $followUpsQuery->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($note) {
                    $lead = null;
                    $leadEncryptedId = null;
                    if ($note->reference_type === 'lead') {
                        $lead = \App\Models\EnquiryManageModel::find($note->reference_id);
                        if ($lead)
                            $leadEncryptedId = encrypt($lead->enquiry_id);
                    } elseif ($note->reference_type === 'deal') {
                        $deal = \App\Models\Deal::with('lead')->find($note->reference_id);
                        if ($deal) {
                            $lead = $deal->lead;
                            if ($lead)
                                $leadEncryptedId = encrypt($lead->enquiry_id);
                        }
                    }

                    if (!$lead) {
                        return null;
                    }

                    $daysRemaining = Carbon::today()->diffInDays(Carbon::parse($note->follow_up_date), false);

                    $followUpStatus = 'upcoming';
                    $followUpHint = 'In ' . $daysRemaining . ' days';
                    if ($daysRemaining < 0) {
                        $followUpStatus = 'overdue';
                        $followUpHint = abs($daysRemaining) . ' days overdue';
                    } elseif ($daysRemaining == 0) {
                        $followUpStatus = 'today';
                        $followUpHint = 'Today';
                    }

                    return [
                        'id' => $note->id,
                        'follow_up_date' => $note->follow_up_date,
                        'remarks' => $note->note,
                        'follow_up_status' => $followUpStatus,
                        'follow_up_hint' => $followUpHint,
                        'reference_type' => $note->reference_type,
                        'lead' => [
                            'company_name' => $lead->company_name,
                            'contact_person_name' => $lead->contact_person_name,
                            'enquiry_no' => $lead->enquiry_no,
                            'encrypted_id' => $leadEncryptedId,
                        ],
                        'assigned_staff' => $note->assignedStaff ? [
                            'name' => $note->assignedStaff->staff_name,
                        ] : null,
                        'created_by_name' => $note->created_by ? get_createdby_name($note->created_by) : null,
                    ];
                })
                ->filter()
                ->take(10)
                ->values();

            // 5. RECENT LEADS ASSIGNED
            $recentLeadsQuery = \App\Models\EnquiryManageModel::with(['assignedStaff', 'createdBy']);
            if ($isRestricted) {
                $recentLeadsQuery->where('assigned_to', $userId);
            }
            $recentLeads = $recentLeadsQuery->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($lead) {
                    return [
                        'company_name' => $lead->company_name,
                        'contact_person_name' => $lead->contact_person_name,
                        'enquiry_no' => $lead->enquiry_no,
                        'formatted_created_at' => Carbon::parse($lead->created_at)->format('d/m/Y h:i A'),
                        'encrypted_id' => encrypt($lead->enquiry_id),
                        'status' => $lead->status, // 0 = Lead (open), 1 = Converted to Deal
                        'assigned_staff' => $lead->assignedStaff ? [
                            'name' => $lead->assignedStaff->staff_name,
                        ] : null,
                        'created_by_name' => $lead->created_by ? get_createdby_name($lead->created_by) : null,
                    ];
                });

            // 6. RECENT QUOTATIONS
            $recentQuotationsQuery = \App\Models\Quotation::with(['assignedStaff', 'createdByStaff']);
            if ($isRestricted) {
                $recentQuotationsQuery->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)->orWhere('created_by', $userId);
                });
            }
            $recentQuotations = $recentQuotationsQuery->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->unique('quotation_no')
                ->take(5)
                ->values()
                ->map(function ($quot) {
                    $quot->formatted_created_at = Carbon::parse($quot->created_at)->format('d/m/Y');
                    $quot->formatted_grand_total = number_format($quot->grand_total, 2);
                    $quot->encrypted_id = encrypt($quot->quotation_id);
                    $quot->assigned_staff_name = $quot->assignedStaff ? $quot->assignedStaff->staff_name : null;
                    $quot->created_by_name = $quot->created_by ? get_createdby_name($quot->created_by) : null;
                    return $quot;
                });

            // 7. RECENT ACTIVITIES (Lead, Deal, Quotation, Performa Invoice)
            $recentDealLogsQuery = \App\Models\DealLog::with('deal');
            if ($isRestricted) {
                $recentDealLogsQuery->where('performed_by', $userId);
            }
            $dealLogs = $recentDealLogsQuery->orderBy('performed_at', 'desc')->limit(10)->get();

            $recentQuotLogsQuery = \App\Models\QuotationLog::with('quotation');
            if ($isRestricted) {
                $recentQuotLogsQuery->where('performed_by', $userId);
            }
            $quotLogs = $recentQuotLogsQuery->orderBy('performed_at', 'desc')->limit(10)->get();

            $recentInvoiceLogsQuery = \App\Models\PerformaInvoiceLog::with('invoice');
            if ($isRestricted) {
                $recentInvoiceLogsQuery->where('performed_by', $userId);
            }
            $invoiceLogs = $recentInvoiceLogsQuery->orderBy('performed_at', 'desc')->limit(10)->get();

            $recentLeadLogsQuery = \Illuminate\Support\Facades\DB::table('enquiry_history')
                ->join('enquiry_manage', 'enquiry_history.enquiry_id', '=', 'enquiry_manage.enquiry_id')
                ->select('enquiry_history.*', 'enquiry_manage.company_name', 'enquiry_manage.contact_person_name');
            if ($isRestricted) {
                $recentLeadLogsQuery->where('enquiry_history.changed_by', $userId);
            }
            $leadLogs = $recentLeadLogsQuery->orderBy('enquiry_history.changed_at', 'desc')->limit(10)->get();

            $activities = collect();
            foreach ($dealLogs as $log) {
                $activities->push([
                    'type' => 'deal',
                    'title' => $log->deal->deal_name ?? 'Deal Action',
                    'action' => $log->action,
                    'description' => $log->description,
                    'time' => $log->performed_at,
                    'formatted_time' => Carbon::parse($log->performed_at)->diffForHumans(),
                    'performed_by' => get_createdby_name($log->performed_by) ?: 'User',
                ]);
            }
            foreach ($quotLogs as $log) {
                $activities->push([
                    'type' => 'quotation',
                    'title' => $log->quotation->quotation_no ?? 'Quotation Action',
                    'action' => $log->action,
                    'description' => $log->description,
                    'time' => $log->performed_at,
                    'formatted_time' => Carbon::parse($log->performed_at)->diffForHumans(),
                    'performed_by' => get_createdby_name($log->performed_by) ?: 'User',
                ]);
            }
            foreach ($invoiceLogs as $log) {
                $activities->push([
                    'type' => 'performa-invoice',
                    'title' => $log->invoice->invoice_no ?? 'Invoice Action',
                    'action' => $log->action,
                    'description' => $log->description,
                    'time' => $log->performed_at,
                    'formatted_time' => Carbon::parse($log->performed_at)->diffForHumans(),
                    'performed_by' => get_createdby_name($log->performed_by) ?: 'User',
                ]);
            }
            foreach ($leadLogs as $log) {
                $activities->push([
                    'type' => 'lead',
                    'title' => trim($log->company_name ?? $log->contact_person_name ?? 'Lead Action'),
                    'action' => $log->change_type,
                    'description' => $log->reason,
                    'time' => $log->changed_at,
                    'formatted_time' => Carbon::parse($log->changed_at)->diffForHumans(),
                    'performed_by' => get_createdby_name($log->changed_by) ?: 'User',
                ]);
            }

            $sortedActivities = $activities->sortByDesc('time')->take(8)->values();

            // 8. GRAPHICAL CHARTS DATA
            // A. Donut Chart: Deal Stage Distribution
            $dealStages = \Illuminate\Support\Facades\DB::table('mst_deal_stage')->where('status', 0)->orderBy('order_no', 'asc')->get();
            $dealStageDistribution = [];
            foreach ($dealStages as $stage) {
                $stageQuery = \App\Models\Deal::where('deal_stage', $stage->id);
                if ($isFiltered) {
                    $stageQuery->whereBetween('created_at', [$start, $end]);
                }
                if ($isRestricted) {
                    $stageQuery->where(function ($sub) use ($userId) {
                        $sub->where('assigned_to', $userId)->orWhere('created_by', $userId);
                    });
                }
                $dealStageDistribution[] = [
                    'stage_name' => $stage->stage_name,
                    'count' => $stageQuery->count(),
                ];
            }

            // B. Line Chart: Daily trends for Leads and Deals
            $trendData = [];
            $currentDate = $start->copy();
            $daysDiff = $start->diffInDays($end);

            if ($daysDiff <= 31) {
                while ($currentDate->lte($end)) {
                    $dateStr = $currentDate->toDateString();

                    $leadQ = \App\Models\EnquiryManageModel::whereDate('enquiry_date', $dateStr);
                    if ($isRestricted) {
                        $leadQ->where(function ($sub) use ($userId) {
                            $sub->where('assigned_to', $userId)->orWhere('created_by', $userId);
                        });
                    }

                    $dealQ = \App\Models\Deal::whereDate('created_at', $dateStr);
                    if ($isRestricted) {
                        $dealQ->where(function ($sub) use ($userId) {
                            $sub->where('assigned_to', $userId)->orWhere('created_by', $userId);
                        });
                    }

                    $trendData[] = [
                        'label' => $currentDate->format('d M'),
                        'leads' => $leadQ->count(),
                        'deals' => $dealQ->count(),
                    ];

                    $currentDate->addDay();
                }
            } else {
                while ($currentDate->lte($end)) {
                    $monthStart = $currentDate->copy()->startOfMonth();
                    $monthEnd = $currentDate->copy()->endOfMonth();
                    if ($monthStart->lt($start))
                        $monthStart = $start->copy();
                    if ($monthEnd->gt($end))
                        $monthEnd = $end->copy();

                    $leadQ = \App\Models\EnquiryManageModel::whereBetween('enquiry_date', [$monthStart->toDateString(), $monthEnd->toDateString()]);
                    if ($isRestricted) {
                        $leadQ->where(function ($sub) use ($userId) {
                            $sub->where('assigned_to', $userId)->orWhere('created_by', $userId);
                        });
                    }

                    $dealQ = \App\Models\Deal::whereBetween('created_at', [$monthStart, $monthEnd]);
                    if ($isRestricted) {
                        $dealQ->where(function ($sub) use ($userId) {
                            $sub->where('assigned_to', $userId)->orWhere('created_by', $userId);
                        });
                    }

                    $trendData[] = [
                        'label' => $currentDate->format('M Y'),
                        'leads' => $leadQ->count(),
                        'deals' => $dealQ->count(),
                    ];

                    $currentDate->addMonth();
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'charts' => [
                        'stage_distribution' => $dealStageDistribution,
                        'trends' => $trendData,
                    ],
                    'counts' => [
                        'total_leads' => $totalLeads,
                        'total_deals' => $totalDeals,
                        'total_quotations' => $totalQuotations,
                        'total_products' => $totalProducts,
                        'total_customers' => $totalCustomers,
                        'total_staff' => $totalStaff,
                    ],
                    'performance' => [
                        'leads_count' => $leadsInPeriodCount,
                        'sales_amount' => $salesInPeriodAmount,
                        'conversion_rate' => $conversionRate,
                    ],
                    'calls' => [
                        'today' => $callsToday,
                        'upcoming' => $callsUpcoming,
                        'overdue' => $callsOverdue,
                    ],
                    'follow_ups' => $upcomingFollowUps,
                    'recent_leads' => $recentLeads,
                    'recent_quotations' => $recentQuotations,
                    'activities' => $sortedActivities,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function extractShiftStartTime($shiftTimingString)
    {
        // The format is like 'Label: HH:MM AM/PM - HH:MM AM/PM'
        $parts = explode(':', $shiftTimingString, 2);
        if (count($parts) < 2)
            return null;
        $timePart = trim($parts[1]);
        $times = explode(' - ', $timePart, 2);
        return trim($times[0]);
    }

    private function extractShiftEndTime($shiftTimingString)
    {
        // The format is like 'Label: HH:MM AM/PM - HH:MM AM/PM'
        $parts = explode(' - ', $shiftTimingString);
        if (count($parts) < 2)
            return null;
        $endTime = trim(end($parts));
        return $endTime;
    }

    private function extractShiftHours($shiftTimingString)
    {
        try {
            $startTimeStr = $this->extractShiftStartTime($shiftTimingString);
            $endTimeStr = $this->extractShiftEndTime($shiftTimingString);
            if (!$startTimeStr || !$endTimeStr)
                return 0;

            $start = Carbon::parse($startTimeStr);
            $end = Carbon::parse($endTimeStr);
            if ($end->lt($start)) {
                $end->addDay(); // Handles overnight shifts
            }

            return $start->diffInHours($end);
        } catch (\Exception $e) {
            return 0;
        }
    }
}




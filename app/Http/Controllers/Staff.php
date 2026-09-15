<?php

namespace App\Http\Controllers;

use App\Exports\StaffExport;
use App\Models\LeaveBalance;
use App\Models\RoleModel;
use App\Models\Staffmodel;
use App\Models\StaffLog;
use App\Services\RekognitionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class Staff extends Controller
{
    protected RekognitionService $rekognitionService;

    public function __construct(RekognitionService $rekognitionService)
    {
        $this->rekognitionService = $rekognitionService;
    }

    public function index()
    {
        $page_title = 'Staff';
        $page_name  = 'Staff';
        $role       = RoleModel::where('status', 0)->orderBy('role_name', 'asc')->get();
        $shifts     = DB::table('mst_shift')->where('status', 0) // Assuming 0 is active
            ->select('shift_id', 'shift_name', 'shift_type', 'time_in', 'time_out')
            ->get();
        // Format the name for the dropdown
        $shifts = $shifts->map(function ($shift) {
            $shift->time_in      = date('g:i A', strtotime($shift->time_in));
            $shift->time_out     = date('g:i A', strtotime($shift->time_out));
            $shift->display_name = "{$shift->shift_type}: {$shift->shift_name} ({$shift->time_in} - {$shift->time_out})";
            return $shift;
        });
        return view('company.user_managment.staff.index', compact('page_title', 'page_name', 'role', 'shifts'));
    }

    private function generateStaffCode()
    {
        $prefix = 'HC-';

        // Find the latest staff code
        // REMOVE: ->withTrashed()
        $latestStaff = Staffmodel::where('staff_code', 'like', $prefix . '%')
            ->orderBy('staff_id', 'desc')
            ->first();

        if (! $latestStaff) {
            return $prefix . '001';
        }

        // Extract the number part, increment it, and format
        $lastNumber = (int) substr($latestStaff->staff_code, strlen($prefix));
        $nextNumber = $lastNumber + 1;

        // Use str_pad to ensure it has at least 3 digits (e.g., 1 -> 001)
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'salute'                  => 'required|in:Mr,Mrs,Miss,Ms',
                'staff_name'              => 'required|max:100',
                'staff_email'             => 'required|email',
                'phone'                   => 'required|digits_between:10,15|regex:/^[0-9]+$/',
                'address'                 => 'required|max:255',
                'position'                => 'nullable|max:100',
                'department'              => 'nullable|max:100',
                'role_id'                 => 'required',
                'staff_type'              => 'required|in:Domestic,Export',
            ], [
                'required'          => 'The :attribute field is required.',
                'email'             => 'Please enter a valid email address.',
                'regex' => 'Please enter a valid 10-digit mobile number.',
                'digits_between'    => 'The :attribute must be between :min and :max digits.',
                'max'               => 'The :attribute may not be greater than :max characters.',
                'min'               => 'The :attribute must be at least :min.',
                'in'                => 'The selected :attribute is invalid.',
            ], [
                'salute'                  => 'Salutation',
                'staff_name'              => 'Staff Name',
                'staff_email'             => 'Email Address',
                'phone'                   => 'Phone Number',
                'address'                 => 'Address',
                'position'                => 'Position',
                'department'              => 'Department',
                'role_id'                 => 'Role',
                'staff_type'              => 'Staff Type',
            ]);

            // face_image

            if ($validator->fails()) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Logic to make sure uniqueness

            $existingStaff = Staffmodel::where('staff_email', $request->staff_email)->first();

            $staffCode = $existingStaff?->staff_code ?? $this->generateStaffCode();
            
            $staffData = [
                'salute'                  => $request->salute,
                'role_id'                 => $request->role_id,
                'staff_name'              => $request->staff_name,
                'staff_email'             => $request->staff_email,
                'password'                => md5(123456),
                'phone'                   => $request->phone,
                'address'                 => $request->address,
                'position'                => $request->position,
                'department'              => $request->department,
                'staff_type'              => $request->staff_type,
                'staff_code'              => $this->generateStaffCode(),
                'created_by'              => getCreatedBy(),
                'status' => 0
            ];
            
            // Create the staff record
            $staff = Staffmodel::updateOrCreate([
                'staff_email' => $request->staff_email
            ], $staffData);

            if (! $staff) {
                throw new \Exception('Failed to create staff record');
            }
            StaffLog::create([
            'staff_id'     => $staff->staff_id,
            'action'       => 'Created',
            'description'  => "Staff record created for {$staff->salute}. {$staff->staff_name}.",
            'performed_by' => getCreatedBy(), // Maps to your active session identifier
            'created_at' => now()
        ]);
           
            DB::commit();
            return response()->json([
                'status'  => true,
                'message' => 'Staff created successfully',
                'data'    => $staff,
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Database error occurred',
                'error'   => config('app.debug') ? $e->getMessage() : 'Please try again later',
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred while processing your request',
                'error'   => config('app.debug') ? $e->getMessage() : 'Please try again later',
            ], 500);
        }
    }
    public function list()
    {
        $staff = Staffmodel::select([
            'staff_id',
            'staff_code',
            'salute',
            'staff_name',
            'staff_email',
            'phone',
            'position',
            'department',
            'role_id',
            'staff_type',
            'address',
            'status',
        ])->where('status', 0)->orderByDesc('created_at');

        $permissions = checkPermissions(get_index_route($this));
        $canUpdate   = $permissions['canUpdate'] ?? false;
        $canDelete   = $permissions['canDelete'] ?? false;

        return DataTables::of($staff)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($canUpdate, $canDelete) {
                $btn = '<div class="d-flex align-items-center gap-2">';
                if ($canUpdate) {
                    $btn .= '<a href="javascript:void(0)" class="btn btn-sm btn-primary edit-staff" data-id="' . $row->staff_id . '"> <i class="fas fa-edit"></i>Edit</a>';
                }
                if ($canDelete) {
                    $btn .= '<a href="javascript:void(0)" class="btn btn-sm btn-danger delete-staff" data-id="' . $row->staff_id . '"><i class="fas fa-trash-alt"></i>Delete</a>';
                    
                }
                $btn .= '<a href="javascript:void(0)"
class="btn btn-sm btn-warning reset-password d-inline-flex align-items-center gap-1"
title="Reset Password"
data-id="' . $row->staff_id . '"
data-name="' . $row->staff_name . '">
<i class="bx bx-key"></i> Reset
</a>';
$btn .= '<a href="javascript:void(0)" class="btn btn-sm btn-info view-logs d-inline-flex align-items-center gap-1" title="View Timeline History" data-id="' . $row->staff_id . '"><i class="fas fa-history"></i> History</a>';
                $btn .= '</div>';
                return $btn;
            })
            ->editColumn('hire_date', function ($row) {
                return date('d-m-Y', strtotime($row->hire_date));
            })
            ->editColumn('position', function ($row) {
                return $row->position ?? 'N/A';
            })
            ->editColumn('department', function ($row) {
                return $row->department ?? 'N/A';
            })
            ->editColumn('status', function ($row) {
                switch ($row->status) {
                    case 0:
                        return 'Active';
                    case 1:
                        return 'Deleted';
                    case 2:
                        return 'Exited';
                    default:
                        return 'Unknown';
                }
            })

            ->filterColumn('name', function ($query, $keyword) {
                $query->whereRaw("CONCAT(salute, ' ', staff_name, ' ', staff_code) LIKE ?", ["%{$keyword}%"]);
            })
            ->addColumn('name', function ($row) {
                return $row->salute . '. ' . $row->staff_name . '  (<b>' . get_role_name($row->role_id) . '</b>)<br>' . $row->staff_code;
            })
            ->rawColumns(['action', 'name', 'status'])
            ->make(true);
    }
    // public function update(Request $request)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $id = $request->staff_id;

    //         $validator = Validator::make($request->all(), [
    //             'salute'              => 'required|in:Mr,Mrs,Miss,Ms',
    //             'staff_name'          => 'required|max:100',
    //             'staff_email'         => 'required|email|unique:mst_staff,staff_email,' . $id . ',staff_id',
    //             'phone'               => 'required|digits_between:10,15',
    //             'address'             => 'required|max:255',
    //             'position'            => 'nullable|max:100',
    //             'department'          => 'nullable|max:100',
    //             'role_id'              => 'required',
    //             'staff_type'              => 'required|in:Domestic,Export',
    //         ], [
    //             'required'          => 'The :attribute field is required.',
    //             'email'             => 'Please enter a valid email address.',
    //             'unique'            => 'This :attribute is already registered with another staff member.',
    //             'digits_between'    => 'The :attribute must be between :min and :max digits.',
    //             'max'               => 'The :attribute may not be greater than :max characters.',
    //             'min'               => 'The :attribute must be at least :min.',
    //             'in'                => 'The selected :attribute is invalid.',
    //         ], [
    //             'salute'              => 'Salutation',
    //             'staff_name'          => 'Staff Name',
    //             'staff_email'         => 'Email Address',
    //             'phone'               => 'Phone Number',
    //             'address'             => 'Address',
    //             'position'            => 'Position',
    //             'department'          => 'Department',
    //             'role_id'             => 'Role',
    //             'staff_type'              => 'Staff Type',
    //         ]);

    //         if ($validator->fails()) {
    //             DB::rollBack();
    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'Validation error',
    //                 'errors'  => $validator->errors(),
    //             ], 422);
    //         }

    //         $staff = Staffmodel::findOrFail($id);

    //         $staff->update([
    //             'salute'              => $request->salute,
    //             'role_id'             => $request->role_id,
    //             'staff_name'          => $request->staff_name,
    //             'staff_email'         => $request->staff_email,
    //             'phone'               => $request->phone,
    //             'address'             => $request->address,
    //             'position'            => $request->position,
    //             'department'          => $request->department,
    //             'staff_type'          => $request->staff_type,
    //             'updated_by'          => getCreatedBy(),
    //         ]);
    //         StaffLog::create([
    //             'staff_id'     => $staff->staff_id,
    //             'action'       => 'Updated',
    //             'description'  => "Staff record profile structural info updated.",
    //             'performed_by' => getCreatedBy(),
    //             'created_at' => now()
    //         ]);
    //         DB::commit();
    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Staff updated successfully',
    //             'data'    => $staff,
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status'  => false,
    //             'message' => 'An error occurred while updating',
    //             'error'   => config('app.debug') ? $e->getMessage() : 'Please try again later',
    //         ], 500);
    //     }
    // }

    public function update(Request $request)
{
    DB::beginTransaction();
    try {
        $id = $request->staff_id; //[cite: 6]
        $validator = Validator::make($request->all(), [
            'salute'              => 'required|in:Mr,Mrs,Miss,Ms', //[cite: 6]
            'staff_name'          => 'required|max:100', //[cite: 6]
            'staff_email'         => 'required|email|unique:mst_staff,staff_email,' . $id . ',staff_id', //[cite: 6]
            'phone'               => 'required|digits_between:10,15', //[cite: 6]
            'address'             => 'required|max:255', //[cite: 6]
            'position'            => 'nullable|max:100', //[cite: 6]
            'department'          => 'nullable|max:100', //[cite: 6]
            'role_id'             => 'required', //[cite: 6]
            'staff_type'          => 'required|in:Domestic,Export', //[cite: 6]
        ], [
            'required'          => 'The :attribute field is required.', //[cite: 6]
            'email'             => 'Please enter a valid email address.', //[cite: 6]
            'unique'            => 'This :attribute is already registered with another staff member.', //[cite: 6]
            'digits_between'    => 'The :attribute must be between :min and :max digits.', //[cite: 6]
            'max'               => 'The :attribute may not be greater than :max characters.', //[cite: 6]
            'min'               => 'The :attribute must be at least :min.', //[cite: 6]
            'in'                => 'The selected :attribute is invalid.', //[cite: 6]
        ], [
            'salute'              => 'Salutation', //[cite: 6]
            'staff_name'          => 'Staff Name', //[cite: 6]
            'staff_email'         => 'Email Address', //[cite: 6]
            'phone'               => 'Phone Number', //[cite: 6]
            'address'             => 'Address', //[cite: 6]
            'position'            => 'Position', //[cite: 6]
            'department'          => 'Department', //[cite: 6]
            'role_id'             => 'Role', //[cite: 6]
            'staff_type'          => 'Staff Type', //[cite: 6]
        ]);

        if ($validator->fails()) { //[cite: 6]
            DB::rollBack(); //[cite: 6]
            return response()->json([ //[cite: 6]
                'status'  => false, //[cite: 6]
                'message' => 'Validation error', //[cite: 6]
                'errors'  => $validator->errors(), //[cite: 6]
            ], 422); //[cite: 6]
        }

        $staff = Staffmodel::findOrFail($id); //[cite: 6]

        // 1. Assign the new data without saving yet so we can track dirty modifications
        $staff->fill([
            'salute'              => $request->salute, //[cite: 6]
            'role_id'             => $request->role_id, //[cite: 6]
            'staff_name'          => $request->staff_name, //[cite: 6]
            'staff_email'         => $request->staff_email, //[cite: 6]
            'phone'               => $request->phone, //[cite: 6]
            'address'             => $request->address, //[cite: 6]
            'position'            => $request->position, //[cite: 6]
            'department'          => $request->department, //[cite: 6]
            'staff_type'          => $request->staff_type, //[cite: 6]
            'updated_by'          => getCreatedBy(), //[cite: 6]
        ]);

        // 2. Compute readable logs if modifications are found
        $changeLog = [];
        if ($staff->isDirty()) {
            foreach ($staff->getDirty() as $field => $newValue) {
                // Ignore tracking updated_by metadata column fields
                if (in_array($field, ['updated_by', 'updated_at'])) continue;

                $oldValue = $staff->getOriginal($field) ?? 'N/A';
                $displayField = ucwords(str_replace('_', ' ', $field));
                
                $changeLog[] = "<strong>{$displayField}</strong> changed from \"{$oldValue}\" to \"{$newValue}\"";
            }
        }

        // 3. Complete database modification persistence
        $staff->save();

        // 4. Log the audit trail changes
        if (!empty($changeLog)) {
            StaffLog::create([
                'staff_id'     => $staff->staff_id,
                'action'       => 'Updated',
                'description'  => implode('<br>', $changeLog), // Connect items dynamically
                'performed_by' => getCreatedBy()
            ]);
        }

        DB::commit(); //[cite: 6]
        return response()->json([ //[cite: 6]
            'status'  => true, //[cite: 6]
            'message' => 'Staff updated successfully', //[cite: 6]
            'data'    => $staff, //[cite: 6]
        ]);
    } catch (\Exception $e) { //[cite: 6]
        DB::rollBack(); //[cite: 6]
        return response()->json([ //[cite: 6]
            'status'  => false, //[cite: 6]
            'message' => 'An error occurred while updating', //[cite: 6]
            'error'   => config('app.debug') ? $e->getMessage() : 'Please try again later', //[cite: 6]
        ], 500); //[cite: 6]
    }
}

    public function get_staff(Request $request)
    {
        $id = $request->input('staff_id');

        $staff = Staffmodel::select([
            'staff_id',
            'salute',
            'role_id',
            'staff_name',
            'staff_email',
            'phone',
            'address',
            'position',
            'department',
            'staff_type',
        ])
            ->where('staff_id', $id)
            ->first();

        if ($staff) {
            return response()->json(['status' => true, 'data' => $staff]);
        } else {
            return response()->json(['status' => false, 'message' => 'Staff not found']);
        }
    }

    public function markAttendance(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'face_data' => 'required|json',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Get all staff with face data
            $staffMembers    = Staffmodel::whereNotNull('face_data')->get();
            $inputDescriptor = json_decode($request->face_data, true);

            $bestMatch    = null;
            $bestDistance = 0.6; // Threshold for face recognition (lower is more strict)

            foreach ($staffMembers as $staff) {
                $storedDescriptor = json_decode($staff->face_data, true);

                // Calculate Euclidean distance between descriptors
                $distance = $this->getEuclideanDistance($inputDescriptor, $storedDescriptor);

                if ($distance < $bestDistance) {
                    $bestDistance = $distance;
                    $bestMatch    = $staff;
                }
            }

            if (! $bestMatch) {
                return response()->json([
                    'status'  => false,
                    'message' => 'No matching staff member found',
                ], 404);
            }

            // Check if staff already has attendance for today
            $existingAttendance = Attendance::where('staff_id', $bestMatch->id)
                ->whereDate('time_in', today())
                ->first();

            if ($existingAttendance) {
                // Update time_out if exists (for checkout)
                if (! $existingAttendance->time_out) {
                    $existingAttendance->update(['time_out' => now()]);
                    $message = 'Check-out recorded successfully';
                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Attendance already completed for today',
                    ], 400);
                }
            } else {
                // Create new attendance record (check-in)
                Attendance::create([
                    'staff_id' => $bestMatch->id,
                    'time_in'  => now(),
                    'status'   => 'present',
                ]);
                $message = 'Check-in recorded successfully';
            }

            return response()->json([
                'status'  => true,
                'message' => $message,
                'staff'   => $bestMatch,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    private function getEuclideanDistance(array $descriptor1, array $descriptor2)
    {
        $sum = 0;
        for ($i = 0; $i < count($descriptor1); $i++) {
            $sum += pow($descriptor1[$i] - $descriptor2[$i], 2);
        }
        return sqrt($sum);
    }
    private function decodeBase64Image(string $base64String): string
    {
        $imageParts = explode(";base64,", $base64String);
        if (count($imageParts) !== 2) {
            throw new \Exception('Invalid image format. Expected base64 string.');
        }
        return base64_decode($imageParts[1]);
    }
    // public function export_excel()
    // {

    //     $fileName = 'Staff_List_' . now()->format('Ymd_His') . '.xlsx';

    //    // $response = Excel::download(new StaffExport, $fileName);

    //     $tempPath = storage_path('app/public/temp_staff_images');

    //     if (File::exists($tempPath)) {
    //         File::deleteDirectory($tempPath);
    //     }

    //     return $response;
    // }

    public function destroy(Request $request)
    {
        $id = $request->id;

        try {
            DB::beginTransaction();

            $staff = Staffmodel::find($id);

            if (!$staff) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Staff not found.'
                ]);
            }
            if (!empty($staff->face_data)) {
                $this->rekognitionService->deleteFace($staff->face_data);
            }
            // Soft delete (status = 1)
            $staff->update([
                'status' => 1,
                'face_data'  => null,
                'face_image'  => null,
                'updated_at' => now(),
                'updated_by' => getUpdatedBy()
            ]);
            StaffLog::create([
                'staff_id'     => $staff->staff_id,
                'action'       => 'Deleted',
                'description'  => "Staff profiling metrics marked status deleted, cloud biometrics cleared.",
                'performed_by' => getUpdatedBy(),
                'created_at' => now()
            ]);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Staff deleted successfully.'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete staff: ' . $e->getMessage()
            ]);
        }
    }

   

    public function resetPassword(Request $request)
    {
        try {

            $staff = Staffmodel::find($request->id);

            if (!$staff) {
                return response()->json([
                    'status' => false,
                    'message' => 'Staff not found.'
                ]);
            }

            $staff->update([
                'password' => md5(123456),
                'updated_at' => now(),
                'updated_by' => getUpdatedBy()
            ]);

            StaffLog::create([
                'staff_id'     => $staff->staff_id,
                'action'       => 'Password Reset',
                'description'  => "Account access validation secret credentials forced to defaults.",
                'performed_by' => getUpdatedBy(),
                'created_at' => now()
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Password reset successfully.'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ]);
        }
    }

 public function getLogs(Request $request)
{
   $logs = StaffLog::where('staff_id', $request->staff_id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($log) {
            // Transform the performed_by ID into the actual user/admin name using your helper
            $log->updater_name = get_createdby_name($log->performed_by) ?? 'Unknown User';
            return $log;
        })
        ->groupBy(function($log) {
            return Carbon::parse($log->created_at)->format('d M Y');
        });

    return response()->json(['status' => true, 'data' => $logs]);
}

   
}

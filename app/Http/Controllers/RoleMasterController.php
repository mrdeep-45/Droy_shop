<?php

namespace App\Http\Controllers;

use App\Models\RoleModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use App\Models\Staffmodel;
use App\Models\Manager;

class RoleMasterController extends Controller
{
    public function index()
    {
        $page_title = 'Role Master';
        $page_name = 'Roles';
        return view('company.master.role', compact('page_title', 'page_name'));
    }

    public function list(Request $request)
    {
        // Query only active roles (status 0) and filter by type if provided
        $query = RoleModel::where('status', 0)
            ->where('company_id', get_company_id());

        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        $query->orderBy('role_id', 'desc');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('type_badge', function ($row) {
                $color = $row->type == 'manager' ? 'primary' : 'info';
                return '<span class="badge bg-' . $color . '">' . ucfirst($row->type) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $buttons = '';

                if (permissions_check('canUpdate', 'role.index')) {
                    $buttons .= '<button class="btn btn-sm btn-info me-2 edit-btn" data-id="' . $row->role_id . '">
                        Edit
                     </button>';
                }

                if (permissions_check('canDelete', 'role.index')) {
                    $buttons .= '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->role_id . '">
                        Delete
                     </button>';
                }

                return $buttons;
            })
            ->rawColumns(['type_badge', 'actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        // Update validation: Type is required only for new records
        if ($request->role_id) {
            $request->validate([
                'role_name' => 'required|string|max:255',
                // Type is not validated here as it is read-only/disabled
            ]);

            $role = RoleModel::find($request->role_id);
            if (!$role) {
                return response()->json(['message' => 'Role not found'], 404);
            }

            // Only update name, type is read-only
            $role->role_name = is_array($request->role_name) ? $request->role_name[0] : $request->role_name;
            $role->updated_by = getCreatedBy();
            $role->save();

            return response()->json(['message' => 'Role updated successfully']);
        }

        // 2. CREATE CASE (Bulk Insertion)
        else {
            $request->validate([
                'role_name.*' => 'required|string|max:255',
                'type.*'      => 'required|in:manager,staff',
            ]);

            $count = count($request->role_name);
            $inserted = 0;

            for ($i = 0; $i < $count; $i++) {
                if (!empty($request->role_name[$i]) && !empty($request->type[$i])) {
                    $role = new RoleModel();
                    $role->company_id = get_company_id();
                    $role->created_by = getCreatedBy();
                    $role->role_name  = $request->role_name[$i];
                    $role->type       = $request->type[$i];
                    $role->status     = 0;
                    $role->save();
                    $inserted++;
                }
            }

            return response()->json(['message' => "$inserted Roles created successfully"]);
        }
    }

    public function getById($id)
    {
        $role = RoleModel::where('role_id', $id)->where('status', 0)->first();

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        return response()->json(['data' => $role]);
    }

    public function delete($id)
    {
        $role = RoleModel::find($id);

        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found'], 404);
        }

        // Check dependencies in Manager table (status 0 = Active)
        $managerCount = Manager::where('role_id', $id)->where('status', 0)->count();
        if ($managerCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete: This role is currently assigned to ' . $managerCount . ' active Manager(s).'
            ]);
        }

        $staffCount = Staffmodel::where('role_id', $id)->where('status', 0)->count();
        if ($staffCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete: This role is currently assigned to ' . $staffCount . ' active Staff member(s).'
            ]);
        }

        // If no dependencies found, perform hard delete
        $role->delete();

        return response()->json(['success' => true, 'message' => 'Role deleted successfully']);
    }
}

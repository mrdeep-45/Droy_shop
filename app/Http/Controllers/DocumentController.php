<?php

namespace App\Http\Controllers;
use App\Models\Document_categoryModel;
use App\Models\Document_mngtModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
class DocumentController extends Controller
{
     // ==========================================
    // CATEGORY MANAGEMENT FUNCTIONALITY
    // ==========================================
     protected $img_path = '';
    protected $upload_path = '';
    public function __construct()
    {
        $this->img_path = rtrim(config('app.img_path'), '/') . '/company-docs/';
        $this->upload_path = public_path('assets/uploads/company-docs');
    }

     public function index()
    {
        $page_title = 'Document Categories';
        $page_name  = 'Document Categories';

        return view('company.documents.index', compact('page_title', 'page_name'));
    }

    public function category_store(Request $request)
    {
        $request->validate([
            'category_name'   => 'required|array',
            'category_name.*' => 'required|string|max:255',
        ]);

        $categories = [];

        foreach ($request->category_name as $categoryName) {
            $categories[] = [
                'd_category' => $categoryName,
                'parent_id'  => null,
                'created_by' => getCreatedBy(),
                'updated_by' => getCreatedBy(),
                'status'     => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        try {
            Document_categoryModel::insert($categories);
            return response()->json([
                'status'  => true,
                'message' => 'Categories added successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to insert categories.',
            ], 500);
        }
    }

    public function get_document_category()
    {
        $categories = Document_categoryModel::where('status', 0)
            ->whereNull('parent_id')
            ->orderBy('d_category', 'asc')
            ->get();

        $loginType = session('login_type');
        $staffId   = session('staff_id');

        // Enforce Backend Access Control for Staff
        if ($loginType === 'Staff' && $staffId) {
            $currentStaff = DB::table('mst_staff')->where('staff_id', $staffId)->first();
            $currentStaffType = strtolower(trim($currentStaff->staff_type ?? ''));

            $categories = $categories->filter(function ($category) use ($staffId, $currentStaffType) {
                // Fetch active documents for this category
                $docs = Document_mngtModel::where('dc_id', $category->dc_id)
                    ->where('status', 0)
                    ->get();

                foreach ($docs as $doc) {
                    $assignedIds = [];
                    if (!empty($doc->assigned_staff_ids)) {
                        $decoded = json_decode($doc->assigned_staff_ids, true);
                        if (is_array($decoded)) {
                            $assignedIds = array_map('intval', $decoded);
                        }
                    }

                    // Rule 1: Specific staff assigned
                    if (!empty($assignedIds)) {
                        if (in_array((int)$staffId, $assignedIds, true)) {
                            return true;
                        }
                        continue;
                    }

                    // Rule 2: Staff type matching
                    $docAssignType = strtolower(trim($doc->assign_to_staff ?? 'all staff'));
                    if ($docAssignType === 'none') {
                        continue;
                    }
                    if ($docAssignType === 'all staff' || $docAssignType === 'all' || empty($docAssignType)) {
                        return true;
                    }

                    if ($currentStaffType === 'both') {
                        return true;
                    }

                    if ($docAssignType === $currentStaffType) {
                        return true;
                    }
                }

                return false;
            })->values();
        }

        $categories->map(function ($category) {
            $category->encrypted_id = encrypt($category->dc_id);
            return $category;
        });

        return response()->json([
            'status'     => true,
            'categories' => $categories,
            // 'canUpdate'  => permissions_check('canUpdate'),
            // 'canDelete'  => permissions_check('canDelete'),
            'canUpdate'  => permissions_check('canUpdate', 'documents.index'),
            'canDelete'  => permissions_check('canDelete', 'documents.index'),
        ]);
    }

    public function category_update(Request $request)
    {
        $validated = $request->validate([
            'encrypted_id' => 'required',
            'd_category'   => 'required|string|max:255',
        ]);

        try {
            $id       = decrypt($validated['encrypted_id']);
            $category = Document_categoryModel::findOrFail($id);

            $category->update([
                'd_category' => $validated['d_category'],
                'updated_at' => now(),
                'updated_by' => getUpdatedBy(),
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Category updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to update category',
            ], 500);
        }
    }

     public function category_delete(Request $request)
    {
        try {
            $dc_id    = decrypt($request->id);
            $category = Document_categoryModel::findOrFail($dc_id);
            $category->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Category deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to delete category',
            ], 500);
        }
    }

    // ==========================================
    // DOCUMENT UPLOAD & MANAGEMENT FUNCTIONALITY
    // ==========================================
     public function show($encrypted_id)
    {
        try {
            $dc_id    = decrypt($encrypted_id);
            $category = Document_categoryModel::findOrFail($dc_id);

            $page_title = 'Documents: ' . $category->d_category;
            $page_name  = 'Manage Documents';

            $staffList = \App\Models\Staffmodel::where('status', 0)
                ->orderBy('staff_name', 'asc')
                ->get(['staff_id', 'staff_name', 'staff_type']);

            return view('company.documents.show', compact('page_title', 'page_name', 'category', 'encrypted_id', 'staffList'));
        } catch (\Exception $e) {
            return redirect()->route('company.documents.index')->with('error', 'Invalid Category.');
        }
    }
     public function document_store(Request $request)
    {
        $request->validate([
            'encrypted_dc_id'    => 'required',
            'file_name'          => 'required|string|max:255',
            'assign_to_staff'    => 'nullable|string|in:All Staff,Export,Domestic,None',
            'assigned_staff_ids'  => 'nullable|array',
            'certificate'        => 'required|array|max:5',
            'certificate.*'      => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'is_expiry_date'     => 'nullable|boolean',
            'expiry_date'        => 'required_if:is_expiry_date,1|nullable|date',
        ]);

        try {
            $dc_id    = decrypt($request->encrypted_dc_id);
            $batch_id = (string) time() . rand(1000, 9999); // Highly unique batch ID

            $assignToStaff = $request->assign_to_staff ?: 'All Staff';
            $assignedStaffIds = null;
            if ($request->has('assigned_staff_ids') && is_array($request->assigned_staff_ids) && count($request->assigned_staff_ids) > 0) {
                $assignedStaffIds = json_encode(array_values(array_map('intval', $request->assigned_staff_ids)));
            }

            if ($request->hasFile('certificate')) {
                foreach ($request->file('certificate') as $file) {
                    $extension       = $file->getClientOriginalExtension();
                    $fileNameToStore = 'doc_' . time() . '_' . uniqid() . '.' . $extension;

                    $file->move($this->upload_path, $fileNameToStore);

                    Document_mngtModel::create([
                        'batch_id'           => $batch_id,
                        'dc_id'              => $dc_id,
                        'file_name'          => $request->file_name,
                        'assign_to_staff'    => $assignToStaff,
                        'assigned_staff_ids'  => $assignedStaffIds,
                        'certificate'        => $fileNameToStore,
                        'is_expiry_date'     => $request->is_expiry_date ? 1 : 0,
                        'expiry_date'        => $request->is_expiry_date && $request->expiry_date
                            ? Carbon::parse($request->expiry_date)->format('Y-m-d')
                            : null,
                        'status'             => 0,
                        'created_by'         => getCreatedBy(),
                        'updated_by'         => getCreatedBy(),
                    ]);
                }
            }

            return response()->json([
                'status'  => true,
                'message' => 'Document(s) uploaded successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to upload document(s). ' . $e->getMessage(),
            ], 500);
        }
    }
     public function document_list($encrypted_id)
    {
        try {
            $dc_id = decrypt($encrypted_id);

            // Fetch active documents for this category
            $raw_documents = Document_mngtModel::where('dc_id', $dc_id)
                ->where('status', 0)
                ->orderBy('created_at', 'desc')
                ->get();

            // Enforce Backend Access Control for Staff
            $loginType = session('login_type');
            $staffId   = session('staff_id');

            if ($loginType === 'Staff' && $staffId) {
                $currentStaff = DB::table('mst_staff')->where('staff_id', $staffId)->first();
                $currentStaffType = strtolower(trim($currentStaff->staff_type ?? ''));

                $raw_documents = $raw_documents->filter(function ($doc) use ($staffId, $currentStaffType) {
                    $assignedIds = [];
                    if (!empty($doc->assigned_staff_ids)) {
                        $decoded = json_decode($doc->assigned_staff_ids, true);
                        if (is_array($decoded)) {
                            $assignedIds = array_map('intval', $decoded);
                        }
                    }

                    // Rule 1: If specific staff members are selected, ONLY those staff can view
                    if (!empty($assignedIds)) {
                        return in_array((int)$staffId, $assignedIds, true);
                    }

                    // Rule 2: If no specific staff selected, check Assign To Staff type
                    $docAssignType = strtolower(trim($doc->assign_to_staff ?? 'all staff'));
                    if ($docAssignType === 'none') {
                        return false;
                    }
                    if ($docAssignType === 'all staff' || $docAssignType === 'all' || empty($docAssignType)) {
                        return true;
                    }

                    if ($currentStaffType === 'both') {
                        return true;
                    }

                    return $docAssignType === $currentStaffType;
                });
            }

            // Group by batch_id
            $grouped   = $raw_documents->groupBy('batch_id');
            $documents = [];

            foreach ($grouped as $batch_id => $docs) {
                $first = $docs->first();

                // Map the physical files attached to this batch
                $files = $docs->map(function ($doc) {
                    $ext = strtolower(pathinfo($doc->certificate, PATHINFO_EXTENSION));
                    return [
                        'id'       => encrypt($doc->dm_id),
                        'url'      => $this->img_path . $doc->certificate,
                        'name'     => $doc->certificate,
                        'is_image' => in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp']),
                    ];
                })->values()->toArray();
                $createdAt = Carbon::parse($first->created_at);

                $assignedStaffArray = [];
                if (!empty($first->assigned_staff_ids)) {
                    $decoded = json_decode($first->assigned_staff_ids, true);
                    if (is_array($decoded)) {
                        $assignedStaffArray = array_map('intval', $decoded);
                    }
                }

                $documents[] = [
                    'batch_id'           => $batch_id,
                    'file_name'          => $first->file_name,
                    'assign_to_staff'    => $first->assign_to_staff ?: 'All Staff',
                    'assigned_staff_ids'  => $assignedStaffArray,
                    'is_expiry_date'     => $first->is_expiry_date,
                    'raw_expiry_date'    => $first->expiry_date ? Carbon::parse($first->expiry_date)->format('Y-m-d') : null,
                    'formatted_date' =>
                    '<span class="fw-semibold text-dark">' . $createdAt->format('d-m-Y') . '</span>'
                        . '<br><small class="text-muted">' . $createdAt->format('h:i:s A') . '</small>'
                        . '<br><small class="text-muted">' . get_createdby_name($first->created_by) . '</small>',
                    'formatted_expiry' => ($first->is_expiry_date && $first->expiry_date) ? Carbon::parse($first->expiry_date)->format('d M, Y') : 'No Expiry',
                    'file_count'         => count($files),
                    'files'              => $files,
                ];
            }

            return response()->json([
                'status'    => true,
                'documents' => array_values($documents),
                // 'canUpdate' => permissions_check('canUpdate'),
                // 'canDelete' => permissions_check('canDelete'),
                'canUpdate' => permissions_check('canUpdate', 'documents.index'),
                'canDelete' => permissions_check('canDelete', 'documents.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to fetch documents. ' . $e->getMessage()], 500);
        }
    }
    public function document_update(Request $request)
    {
        $request->validate([
            'batch_id'           => 'required',
            'file_name'          => 'required|string|max:255',
            'assign_to_staff'    => 'nullable|string|in:All Staff,Export,Domestic,None',
            'assigned_staff_ids'  => 'nullable|array',
            'certificate'        => 'nullable|array|max:5',
            'certificate.*'      => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'is_expiry_date'     => 'nullable|boolean',
            'expiry_date'        => 'required_if:is_expiry_date,1|nullable|date',
        ]);

        try {
            $batch_id = $request->batch_id;
            $isExpiry = $request->is_expiry_date ? 1 : 0;

            if ($isExpiry == 0 && $request->hasFile('certificate')) {

                $oldDocs = Document_mngtModel::where('batch_id', $batch_id)->get();

                foreach ($oldDocs as $doc) {
                    $filePath = $this->upload_path . '/' . $doc->certificate;

                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }

                    $doc->delete();
                }
            }

            $assignToStaff = $request->assign_to_staff ?: 'All Staff';
            $assignedStaffIds = null;
            if ($request->has('assigned_staff_ids') && is_array($request->assigned_staff_ids) && count($request->assigned_staff_ids) > 0) {
                $assignedStaffIds = json_encode(array_values(array_map('intval', $request->assigned_staff_ids)));
            }

            // Update metadata for all existing records under this batch
            Document_mngtModel::where('batch_id', $batch_id)->update([
                'file_name'          => $request->file_name,
                'assign_to_staff'    => $assignToStaff,
                'assigned_staff_ids'  => $assignedStaffIds,
                'is_expiry_date'     => $isExpiry,
                'expiry_date'        => $isExpiry && $request->expiry_date
                    ? Carbon::parse($request->expiry_date)->format('Y-m-d')
                    : null,
                'updated_by'         => getUpdatedBy(),
                'updated_at'         => now(),
            ]);

            // Append new files if uploaded
            if ($request->hasFile('certificate')) {

                $existingDoc = Document_mngtModel::where('batch_id', $batch_id)->first();
                $dc_id = $existingDoc ? $existingDoc->dc_id : null;

                foreach ($request->file('certificate') as $file) {

                    $extension       = $file->getClientOriginalExtension();
                    $fileNameToStore = 'doc_' . time() . '_' . uniqid() . '.' . $extension;

                    $file->move($this->upload_path, $fileNameToStore);

                    Document_mngtModel::create([
                        'batch_id'           => $batch_id,
                        'dc_id'              => $dc_id,
                        'file_name'          => $request->file_name,
                        'assign_to_staff'    => $assignToStaff,
                        'assigned_staff_ids'  => $assignedStaffIds,
                        'certificate'        => $fileNameToStore,
                        'is_expiry_date'     => $isExpiry,
                        'expiry_date'        => $isExpiry && $request->expiry_date
                            ? Carbon::parse($request->expiry_date)->format('Y-m-d')
                            : null,
                        'status'             => 0,
                        'created_by'         => getUpdatedBy(),
                        'updated_by'         => getUpdatedBy(),
                    ]);
                }
            }

            return response()->json([
                'status'  => true,
                'message' => 'Document updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to update document. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function document_file_delete(Request $request)
    {
        try {
            $dm_id = decrypt($request->id);
            $doc   = Document_mngtModel::findOrFail($dm_id);

            $filePath = $this->upload_path . '/' . $doc->certificate;
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $doc->delete();

            return response()->json([
                'status'  => true,
                'message' => 'File removed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to remove file. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function document_delete(Request $request)
    {
        try {
            $batch_id = $request->batch_id;

            $documents = Document_mngtModel::where('batch_id', $batch_id)->get();

            if ($documents->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'Document not found.']);
            }

            foreach ($documents as $document) {
                // Delete actual file from storage directory securely
                $filePath = $this->upload_path . '/' . $document->certificate;
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
                $document->delete();
            }

            return response()->json([
                'status'  => true,
                'message' => 'Document bundle deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to delete documents.',
            ], 500);
        }
    }

}

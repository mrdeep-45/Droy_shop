<?php

namespace App\Http\Controllers;
use App\Models\TermsMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
class TermsController extends Controller
{
    //
     public function index()
    {
        $page_title = 'Terms';
        $page_name = 'Terms';

        return view('company.master.terms', compact('page_title', 'page_name'));
    }

    public function getList()
    {
        try {
            $permissions = checkPermissions(get_index_route($this));
            $canUpdate = $permissions['canUpdate'] ?? false;
            $canDelete = $permissions['canDelete'] ?? false;

            $query = TermsMaster::select(['term_id', 'module', 'terms', 'created_by', 'created_at'])
                ->where('status', 0);
                

            return DataTables::of($query)
                ->addColumn('created_by', fn($row) => get_createdby_name($row->created_by))
                ->addColumn('created_at', fn($row) => \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i:s'))
                ->addColumn('action', function ($row) use ($canUpdate, $canDelete) {
                    $buttons = '<div class="d-flex gap-1 justify-content-center">';
                    if ($canUpdate) {
                        $buttons .= '<button class="btn btn-sm btn-primary edit-terms" data-id="' . $row->term_id . '">Edit</button>';
                    }
                    if ($canDelete) {
                        $buttons .= '<button class="btn btn-sm btn-danger delete-terms" data-id="' . $row->term_id . '">Delete</button>';
                    }
                    $buttons .= '</div>';
                    return $buttons;
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'module' => 'required|array|min:1',
                'module.*' => 'required|string|max:100',
                'terms' => 'required|array|min:1',
                'terms.*' => 'required|string|max:5000',
                'id' => 'nullable|array',
            ], [
                'module.required' => 'At least one module is required.',
                'module.*.required' => 'Module is required.',
                'terms.*.required' => 'Terms & Conditions is required.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $ids = $request->input('id', []);
            $modules = $request->input('module', []);
            $terms = $request->input('terms', []);

            foreach ($modules as $index => $module) {
                $id = $ids[$index] ?? null;
                $term = $terms[$index] ?? '';

                if ($id) {
                    TermsMaster::where('term_id', $id)->update([
                        'module' => trim($module),
                        'terms' => trim($term),
                        'updated_by' => getUpdatedBy(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $newTerm = TermsMaster::create([
                        'module' => trim($module),
                        'terms' => trim($term),
                        'status' => 0,
                        'created_by' => getCreatedBy(),
                        'created_at' => now(),
                    ]);
                    $createdTerms[] = [
                        'term_id' => $newTerm->term_id,
                        'module' => $newTerm->module,
                        'terms' => $newTerm->terms
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success', 
                'message' => 'Terms saved successfully.',
                'created_terms' => $createdTerms ?? []
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            $term = TermsMaster::where('term_id', $id)->where('status', 0)->first();

            if (!$term) {
                return response()->json(['status' => 'error', 'message' => 'Terms not found.'], 404);
            }

            return response()->json(['status' => 'success', 'data' => $term]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $term = TermsMaster::where('term_id', $request->id)->where('status', 0)->first();

            if (!$term) {
                return response()->json(['status' => 'error', 'message' => 'Terms not found.']);
            }

            $term->update([
                'status' => 1,
                'updated_by' => getUpdatedBy(),
                'updated_at' => now(),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Terms deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function termsSelectList()
    {
        try {
            $terms = TermsMaster::where('status', 0)->get(['term_id', 'module', 'terms']);
            return response()->json(['status' => 'success', 'data' => $terms]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

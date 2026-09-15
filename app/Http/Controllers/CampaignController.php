<?php

namespace App\Http\Controllers;

use App\Models\CampaignModel;
use App\Models\CampaignTypeModel;
use App\Models\CampaignLogModel;
use App\Models\Staffmodel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CampaignController extends Controller
{
    public function index()
    {
        // Fetch active campaign types and staff for the dropdowns
        $campaignTypes = CampaignTypeModel::where('status', 0)->orderBy('campaign_type_name', 'asc')->get();
        $staffMembers = Staffmodel::where('status', 0)->orderBy('staff_name', 'asc')->get();

        $data = [
            'page_title' => 'Campaign Management',
            'page_name' => 'Campaign / Exhibition',
            'campaignTypes' => $campaignTypes,
            'staffMembers' => $staffMembers
        ];

        return view('company.campaign.index', $data);
    }

    public function getList()
    {
        try {
            $permissions = checkPermissions(get_index_route($this));
            $canUpdate = $permissions['canUpdate'] ?? false;
            $canDelete = $permissions['canDelete'] ?? false;

            $query = CampaignModel::with(['campaignType', 'assignedStaff'])
                ->where('status', 0);
                $role = session()->get('login_type');
                $staffId = session('staff_id');
                $creatorId = getCreatedBy();
                
                // 3. Apply the visibility filter context if the user is a Staff member
        if ($role === 'Staff' && $staffId) {
            $query->where(function ($q) use ($staffId, $creatorId) {
                $q->where('assigned_to', $staffId)      // Show if assigned to this staff
                  ->orWhere('created_by', $creatorId);  // Show if created by this staff
            });
        }
        
               // $query->orderBy('campaign_id', 'desc');

            return DataTables::of($query)
                ->editColumn('assigned_to', function ($row) {
                    return $row->assignedStaff ? $row->assignedStaff->staff_name : 'N/A';
                })
                ->editColumn('campaign_type_id', function ($row) {
                    return $row->campaignType ? $row->campaignType->campaign_type_name : 'N/A';
                })
                ->editColumn('start_date', function ($row) {
                    return $row->start_date ? Carbon::parse($row->start_date)->format('d-m-Y') : '';
                })
                ->editColumn('end_date', function ($row) {
                    return $row->end_date ? Carbon::parse($row->end_date)->format('d-m-Y') : '';
                })
                ->editColumn('expected_budget', function ($row) {
                    return $row->expected_budget ? number_format($row->expected_budget, 2) : '';
                })
                ->editColumn('actual_cost', function ($row) {
                    return $row->actual_cost ? number_format($row->actual_cost, 2) : '';
                })
                ->addColumn('timeline_status', function ($row) {
                return $row->timeline_status; 
            })
                ->addColumn('action', function ($row) use ($canUpdate, $canDelete) {
                    $buttons = '<div class="d-flex gap-1 justify-content-center">';
                       $buttons .= '<button class="btn btn-sm btn-info view-campaign-details" data-id="'.$row->campaign_id.'"><i class="fas fa-eye"></i> View</button>';
                    
                    if ($canUpdate) {
                        $buttons .= '<button class="btn btn-sm btn-primary edit-campaign" data-id="'.$row->campaign_id.'"><i class="fas fa-edit"></i> Edit</button>';
                    }
                    if ($canDelete) {
                        $buttons .= '<button class="btn btn-sm btn-danger delete-campaign" data-id="'.$row->campaign_id.'"><i class="fas fa-trash"></i> Delete</button>';
                    }
                    $buttons .= '<button class="btn btn-sm btn-info view-history" data-id="'.$row->campaign_id.'"><i class="fas fa-history"></i> History</button>';
                    $buttons .= '</div>';
                    return $buttons;
                })
                ->addIndexColumn()
                ->orderColumn('campaign_name', function ($query, $order) {
                    $query->orderBy('campaign_name', $order);
                })

                ->orderColumn('campaign_type_id', function ($query, $order) {
                    $query->orderBy('campaign_type_id', $order);
                })

                ->orderColumn('assigned_to', function ($query, $order) {
                    $query->orderBy('assigned_to', $order);
                })

                ->orderColumn('start_date', function ($query, $order) {
                    $query->orderBy('start_date', $order);
                })

                ->orderColumn('end_date', function ($query, $order) {
                    $query->orderBy('end_date', $order);
                })

                ->orderColumn('expected_budget', function ($query, $order) {
                    $query->orderBy('expected_budget', $order);
                })

                ->orderColumn('actual_cost', function ($query, $order) {
                    $query->orderBy('actual_cost', $order);
                })
                ->rawColumns(['action', 'timeline_status'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $item = CampaignModel::where('campaign_id', $id)->first();

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Campaign record not found'], 404);
        }
      $designs = [];
        if (is_array($item->design_details)) {
            foreach ($item->design_details as $design) {
                $imageUrls = [];
                if (!empty($design['file_names']) && is_array($design['file_names'])) {
                    foreach ($design['file_names'] as $fname) {
                        $imageUrls[] = [
                            'file_name' => $fname,
                            'url' => asset('uploads/campaigns/' . $fname)
                        ];
                    }
                }
                $designs[] = [
                    'name' => $design['name'] ?? '',
                    'images' => $imageUrls
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $item,
            'designs' => $designs
        ]);
    }

    public function show($id)
    {
        $item = CampaignModel::with(['campaignType', 'assignedStaff'])->where('campaign_id', $id)->first();

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Campaign record not found'], 404);
        }

        $designs = [];
        if (is_array($item->design_details)) {
            foreach ($item->design_details as $design) {
                $imageUrls = [];
                if (!empty($design['file_names']) && is_array($design['file_names'])) {
                    foreach ($design['file_names'] as $fname) {
                        $imageUrls[] = asset('uploads/campaigns/' . $fname);
                    }
                }
                if (!empty($design['name']) || !empty($imageUrls)) {
                    $designs[] = [
                        'name' => $design['name'] ?? '',
                        'images' => $imageUrls
                    ];
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'campaign_name'      => $item->campaign_name,
                'campaign_type_name' => $item->campaignType->campaign_type_name ?? 'N/A',
                'assigned_to_name'   => $item->assignedStaff->staff_name ?? 'N/A',
                'start_date'         => $item->start_date ? Carbon::parse($item->start_date)->format('d-m-Y') : '-',
                'end_date'           => $item->end_date ? Carbon::parse($item->end_date)->format('d-m-Y') : '-',
                'timeline_status'    => $item->timeline_status,
                'expected_budget'    => $item->expected_budget ? number_format($item->expected_budget, 2) : '0.00',
                'actual_cost'        => $item->actual_cost ? number_format($item->actual_cost, 2) : '0.00',
                'description'        => $item->description ?: '-',
                'num_visitors'       => $item->num_visitors ?: '-',
                'machines_displayed' => $item->machines_displayed ?: '-',
                'stall_number'       => $item->stall_number ?: '-',
                'venue_location'     => $item->venue_location ?: '-',
            ],
            'designs' => $designs
        ]);
    }

    public function storeOrUpdate(Request $request)
    {
        $id = $request->input('campaign_id');

        $request->validate([
            'campaign_name' => 'required|string|max:255',
            'campaign_type_id' => 'required|exists:mst_campaign_type,campaign_type_id',
            'assigned_to' => 'nullable|exists:mst_staff,staff_id',
            'start_date' => 'nullable|date_format:d-m-Y',
            'end_date' => 'nullable|date_format:d-m-Y|after_or_equal:start_date',
            'expected_budget' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'design_names' => 'nullable|array',
    
    // 1. Validate that the parent keys are arrays
    'design_files' => 'nullable|array', 
    'design_files.*' => 'nullable|array', 
    
    // 2. Validate that each individual file inside those arrays is an image
    'design_files.*.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        DB::beginTransaction();
        try {
            $start_date = $request->start_date ? Carbon::createFromFormat('d-m-Y', $request->start_date)->format('Y-m-d') : null;
            $end_date = $request->end_date ? Carbon::createFromFormat('d-m-Y', $request->end_date)->format('Y-m-d') : null;
            $design_details = [];
            $destinationPath = public_path('uploads/campaigns');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            $oldCampaign = $id ? CampaignModel::find($id) : null;
            $oldDesigns = ($oldCampaign && is_array($oldCampaign->design_details)) ? $oldCampaign->design_details : [];
            if ($request->has('design_names')) {
                foreach ($request->design_names as $index => $name) {
                  $fileNamesArray = [];
                  if (isset($oldDesigns[$index]['file_names']) && is_array($oldDesigns[$index]['file_names'])) {
                    $fileNamesArray = $oldDesigns[$index]['file_names'];
                }

                   if ($request->hasFile("design_files.$index")) {
                        foreach ($request->file("design_files.$index") as $file) {
                            if ($file->isValid()) {
                                $newFileName = 'design_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                $file->move($destinationPath, $newFileName);
                                $fileNamesArray[] = $newFileName; // Append to array
                            }
                        }
                    }

                    $design_details[] = [
                        'name' => trim($name),
                       'file_names' => $fileNamesArray
                    ];
                }
            }
            $data = [
                'campaign_name' => trim($request->campaign_name),
                'campaign_type_id' => $request->campaign_type_id,
                'assigned_to' => $request->assigned_to ?: null,
               'start_date' => $start_date,
                'end_date' => $end_date,
                'expected_budget' => $request->expected_budget ?: null,
                'actual_cost' => $request->actual_cost ?: null,
                'description' => $request->description,
                'num_visitors' => $request->num_visitors ?: null,
                'machines_displayed' => $request->machines_displayed,
                'design_details' => $design_details,
                'stall_number' => $request->stall_number,
                'venue_location' => $request->venue_location,
            ];
            if ($id) {
               
                if ($oldCampaign) {
                    $data['updated_by'] = getUpdatedBy();
                    $data['updated_at'] = now();
                    $desc = 'Campaign "' . trim($request->campaign_name) . '" details updated.';
                    $oldCampaign->update($data);
                    // LOG UPDATE ACTION
                        CampaignLogModel::create([
                            'campaign_id' => $id,
                            'action' => 'Updated',
                            'description' => $desc,
                            'created_by' => getCreatedBy(), //[cite: 1]
                            'created_at' => now()
                        ]);
                    $message = 'Campaign updated successfully.';
                }
            } else {
                $data['status'] = 0;
                $data['created_by'] = getCreatedBy();
                $data['created_at'] = now();
               $newCampaign = CampaignModel::create($data);
               // LOG CREATE ACTION
                CampaignLogModel::create([
                    'campaign_id' => $newCampaign->campaign_id,
                    'action' => 'Created',
                    'description' => 'Campaign created for customer/event "' . trim($request->campaign_name) . '".',
                    'created_by' => getCreatedBy(), //[cite: 1]
                    'created_at' => now()
                ]);
                $message = 'Campaign created successfully.';
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $id = $request->campaign_id;
            $item = CampaignModel::find($id);

            if (!$item) {
                return response()->json(['status' => 'error', 'message' => 'Campaign not found']);
            }

            if (!empty($item->design_details) && is_array($item->design_details)) {
            $destinationPath = public_path('uploads/campaigns/');

            foreach ($item->design_details as $design) {
                // Look for the nested file_names array we implemented earlier
                if (!empty($design['file_names']) && is_array($design['file_names'])) {
                    foreach ($design['file_names'] as $fileName) {
                        $filePath = $destinationPath . $fileName;
                        
                        // Check if the file physically exists on the server, then delete it
                        if (!empty($fileName) && file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }
                }
            }
        }

            $item->update([
                'status' => 1,
                'updated_by' => getUpdatedBy(),
                'updated_at' => now(),
            ]);

            // LOG DELETE ACTION
                CampaignLogModel::create([
                    'campaign_id' => $id,
                    'action' => 'Deleted',
                    'description' => 'Campaign "' . $item->campaign_name . '" was soft deleted.',
                    'created_by' => getCreatedBy(), //[cite: 1]
                    'created_at' => now()
                ]);

            return response()->json(['status' => 'success', 'message' => 'Campaign has been deleted successfully.']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function getHistory($id)
{
    $logs = CampaignLogModel::where('campaign_id', $id)
        ->orderBy('created_at', 'desc')
        ->get();

    $formattedLogs = $logs->map(function($log) {
        return [
            'action' => $log->action,
            'description' => $log->description,
            'time' => Carbon::parse($log->created_at)->format('h:i A'),
            'date' => Carbon::parse($log->created_at)->format('d M Y'),
            'user' => get_createdby_name($log->created_by) // Uses your custom helper function
        ];
    });

    return response()->json([
        'status' => 'success',
        'data' => $formattedLogs
    ]);
}

public function dashboardOverview()
    {
        try {
            $today = Carbon::today()->format('Y-m-d');

            $baseQuery = CampaignModel::where('status', 0);

            // Same staff-visibility scope used in getList()
            $role = session()->get('login_type');
            $staffId = session('staff_id');
            $creatorId = getCreatedBy();
            if ($role === 'Staff' && $staffId) {
                $baseQuery->where(function ($q) use ($staffId, $creatorId) {
                    $q->where('assigned_to', $staffId)
                      ->orWhere('created_by', $creatorId);
                });
            }

            $totalCampaigns = (clone $baseQuery)->count();

            $activeCampaigns = (clone $baseQuery)
                ->whereNotNull('start_date')->whereNotNull('end_date')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->count();

            $completedCampaigns = (clone $baseQuery)
                ->whereNotNull('end_date')
                ->where('end_date', '<', $today)
                ->count();

            $upcomingCampaigns = (clone $baseQuery)
                ->whereNotNull('start_date')
                ->where('start_date', '>', $today)
                ->count();

            $totalExpectedBudget = (clone $baseQuery)->sum('expected_budget');
            $totalActualCost = (clone $baseQuery)->sum('actual_cost');
            $totalVisitors = (clone $baseQuery)->sum('num_visitors');

            $budgetUtilization = $totalExpectedBudget > 0
                ? round(($totalActualCost / $totalExpectedBudget) * 100, 1)
                : 0;

            $upcomingList = (clone $baseQuery)
                ->whereNotNull('start_date')
                ->where('start_date', '>', $today)
                ->orderBy('start_date', 'asc')
                ->limit(5)
                ->get(['campaign_id', 'campaign_name', 'start_date', 'end_date']);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_campaigns' => $totalCampaigns,
                    'active_campaigns' => $activeCampaigns,
                    'completed_campaigns' => $completedCampaigns,
                    'upcoming_campaigns' => $upcomingCampaigns,
                    'total_expected_budget' => number_format($totalExpectedBudget, 2),
                    'total_actual_cost' => number_format($totalActualCost, 2),
                    'budget_utilization' => $budgetUtilization,
                    'total_visitors' => (int) $totalVisitors,
                    'upcoming_list' => $upcomingList->map(function ($c) {
                        return [
                             'campaign_id' => $c->campaign_id, // added
                            'campaign_name' => $c->campaign_name,
                            'start_date' => $c->start_date ? Carbon::parse($c->start_date)->format('d-m-Y') : '',
                            'end_date' => $c->end_date ? Carbon::parse($c->end_date)->format('d-m-Y') : '',
                        ];
                    }),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

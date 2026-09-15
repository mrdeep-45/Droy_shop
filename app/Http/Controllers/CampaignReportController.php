<?php

namespace App\Http\Controllers;
use App\Models\CampaignModel;
use App\Models\CampaignTypeModel;
use App\Models\Staffmodel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
class CampaignReportController extends Controller
{
    //
    public function getFilteredCampaignsQuery(Request $request)
    {
        $query = CampaignModel::with(['campaignType', 'assignedStaff'])
            ->where('status', 0); // Active campaigns

        // Filter by Date Range (looks into campaign start_date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
        }

        // Filter by Campaign Type
        if ($request->filled('campaign_type_id')) {
            $query->where('campaign_type_id', $request->campaign_type_id);
        }

        // Filter by Assigned Owner
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filter by Creator
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        // Filter by Venue Location search
        if ($request->filled('venue_location')) {
            $query->where('venue_location', 'LIKE', '%' . $request->venue_location . '%');
        }

        // Apply visibility contextual constraints for Staff members
        $role = Session::get('login_type');
        $staffId = session('staff_id');
        $creatorId = getCreatedBy();

        if ($role === 'Staff' && $staffId) {
            $query->where(function ($q) use ($staffId, $creatorId) {
                $q->where('assigned_to', $staffId)
                  ->orWhere('created_by', $creatorId);
            });
        }

        //return $query->orderBy('campaign_id', 'desc');
        return $query;
    }
    public function campaignReport(Request $request)
    {
        $page_title = 'Campaign Report';
        $page_name = 'Campaign / Exhibition Report';

        $campaignTypes = CampaignTypeModel::where('status', 0)->orderBy('campaign_type_name', 'asc')->get();
        $staffList = Staffmodel::where('status', 0)->orderBy('staff_name', 'asc')->get();
        
        $creators = DB::table('tbl_campaign') // Adjust table name if your model uses another table
            ->where('status', 0)
            ->select('created_by')
            ->distinct()
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => $item->created_by,
                    'name' => get_createdby_name($item->created_by)
                ];
            })
            ->filter(function ($item) {
                return !empty($item->name) && $item->name !== '-';
            })
            ->values();

        return view('company.reports.campaign_report', compact('page_title', 'page_name', 'campaignTypes', 'staffList', 'creators'));
    }
    public function campaignReportList(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->getFilteredCampaignsQuery($request);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('campaign_details', function ($row) {
                    return '
                        <strong class="text-primary">' . e($row->campaign_name) . '</strong><br>
                        <small class="text-muted"><i class="fas fa-bullhorn text-secondary"></i> Type: ' . ($row->campaignType ? e($row->campaignType->campaign_type_name) : 'N/A') . '</small>
                    ';
                })
                ->addColumn('schedule_details', function ($row) {
                    $start = $row->start_date ? Carbon::parse($row->start_date)->format('d-M-Y') : 'N/A';
                    $end = $row->end_date ? Carbon::parse($row->end_date)->format('d-M-Y') : 'N/A';
                    return '
                        <small class="text-muted"><i class="fas fa-calendar-alt text-success"></i> Start: ' . $start . '</small><br>
                        <small class="text-muted"><i class="fas fa-calendar-check text-danger"></i> End: ' . $end . '</small><br>
                        <div class="mt-1">' . $row->timeline_status . '</div>
                    ';
                })
                ->addColumn('financial_details', function ($row) {
                    $budget = $row->expected_budget ? number_format($row->expected_budget, 2) : '0.00';
                    $cost = $row->actual_cost ? number_format($row->actual_cost, 2) : '0.00';
                    return '
                        <small class="text-dark"><strong>Expected:</strong> ' . $budget . '</small><br>
                        <small class="text-muted"><strong>Actual Cost:</strong> ' . $cost . '</small>
                    ';
                })
                ->addColumn('exhibition_details', function ($row) {
                    $venue = $row->venue_location ?: '-';
                    $stall = $row->stall_number ?: '-';
                    $visitors = $row->num_visitors ? number_format($row->num_visitors) : '-';
                    return '
                        <small class="text-dark"><i class="fas fa-map-marker-alt text-danger"></i> ' . e($venue) . '</small><br>
                        <small class="text-muted"><strong>Stall:</strong> ' . e($stall) . ' | <strong>Visitors:</strong> ' . $visitors . '</small>
                    ';
                })
                ->addColumn('owner_details', function ($row) {
                    if (Session::get('login_type') !== 'Company') {
                        return '';
                    }
                    $assignedHtml = '';
                    if ($row->assignedStaff) {
                        $assignedHtml = '<small><i class="fas fa-user-check text-success"></i> <span class="text-success fw-bold">' . e($row->assignedStaff->staff_name) . '</span></small><br>';
                    }

                    $createdBy = get_createdby_name($row->created_by);
                    $date = date('d-m-Y h:i A', strtotime($row->created_at));

                    return $assignedHtml . '
                        <small class="text-muted"><i class="fas fa-user-edit text-info"></i> By: ' . $createdBy . '</small><br>
                        <small class="text-muted"><i class="fas fa-clock text-warning"></i> ' . $date . '</small>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-light border text-info view-history" data-id="' . $row->campaign_id . '" data-bs-toggle="tooltip" title="View History">
                            <i class="fas fa-history"></i>
                        </button>
                    ';
                })
                 ->orderColumn('campaign_name', function ($query, $order) {
                        $query->orderBy('campaign_name', $order);
                    })

                    ->orderColumn('start_date', function ($query, $order) {
                        $query->orderBy('start_date', $order);
                    })

                    ->orderColumn('expected_budget', function ($query, $order) {
                        $query->orderBy('expected_budget', $order);
                    })

                    ->orderColumn('venue_location', function ($query, $order) {
                        $query->orderBy('venue_location', $order);
                    })

                    ->orderColumn('created_by', function ($query, $order) {
                        $query->orderBy('created_by', $order);
                    })

                ->rawColumns(['campaign_details', 'schedule_details', 'financial_details', 'exhibition_details', 'owner_details', 'action'])
                ->make(true);
        }
    }
    public function getAppliedFilters(Request $request)
    {
        $filters = [];

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $filters['Date Range'] = date('d-M-Y', strtotime($request->start_date)) . ' to ' . date('d-M-Y', strtotime($request->end_date));
        }
        if ($request->filled('campaign_type_id')) {
            $typeName = CampaignTypeModel::where('campaign_type_id', $request->campaign_type_id)->value('campaign_type_name');
            $filters['Campaign Type'] = $typeName ?: $request->campaign_type_id;
        }
        if ($request->filled('assigned_to')) {
            $staff = Staffmodel::where('staff_id', $request->assigned_to)->value('staff_name');
            $filters['Owner'] = $staff ?: $request->assigned_to;
        }
        if ($request->filled('created_by')) {
            $filters['Created By'] = get_createdby_name($request->created_by);
        }
        if ($request->filled('venue_location')) {
            $filters['Venue / Location'] = $request->venue_location;
        }

        return $filters;
    }
    public function exportCampaignPdf(Request $request)
    {
        $filename = 'Campaign_Report_' . date('Ymd_His') . '.pdf';
        $data = $this->getFilteredCampaignsQuery($request)->get();
        $filters = $this->getAppliedFilters($request);
        $is_pdf = true;

        $pdf = Pdf::loadView('company.reports.campaign_report_pdf', compact('data', 'filters', 'is_pdf'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }
    public function exportCampaignExcel(Request $request)
    {
        $filename = 'Campaign_Report_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new class($request, $this) implements \Maatwebsite\Excel\Concerns\FromView, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
            protected $request;
            protected $controller;

            public function __construct($request, $controller)
            {
                $this->request = $request;
                $this->controller = $controller;
            }

            public function view(): \Illuminate\Contracts\View\View
            {
                return view('company.reports.campaign_report_pdf', [
                    'data' => $this->controller->getFilteredCampaignsQuery($this->request)->get(),
                    'filters' => $this->controller->getAppliedFilters($this->request),
                    'is_pdf' => false
                ]);
            }
        }, $filename);
    }
}

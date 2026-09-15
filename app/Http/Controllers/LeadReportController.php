<?php

namespace App\Http\Controllers;

use App\Models\EnquiryManageModel;
use App\Models\IndustryTypeModel;
use App\Models\Staffmodel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class LeadReportController extends Controller
{
    /**
     * Get query for filtered leads.
     */
    public function getFilteredLeadsQuery(Request $request)
    {
        $query = EnquiryManageModel::with(['customer', 'city', 'sourceInfo', 'leadStatus', 'products.product', 'assignedStaff', 'industryType'])
            ->where('status', 0); // Active leads

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('enquiry_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('lead_status')) {
            $query->where('lead_status', $request->lead_status);
        }

        if ($request->filled('lead_source')) {
            $query->where('source', $request->lead_source);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }
        if ($request->filled('lead_type')) {
            $query->where('lead_type', $request->lead_type);
        }

        if ($request->filled('industry_id')) {
            $query->where('industry_id', $request->industry_id);
        }

        $role    = Session::get('login_type');
        $userId  = getCreatedBy();

        if ($role === 'Staff') {
            $query->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhere('assigned_to', $userId);
            });
        }

        return $query;
       // return $query->orderBy('enquiry_date', 'desc');
    }

    /**
     * Render Lead Report page.
     */
    public function leadReport(Request $request)
    {
        $page_title = 'Lead Report';
        $page_name = 'Lead Report';

        $lead_status = DB::table('lead_status')->select('status_id', 'title')->where('status', 0)->orderBy('title', 'asc')->get();
        $source = DB::table('mst_lead_source')->select('source_id', 'title')->where('status', 0)->orderBy('title', 'asc')->get();
        $staffList = Staffmodel::where('status', 0)->orderBy('staff_name', 'asc')->get();
        $industryTypes = IndustryTypeModel::active()->get();
        $creators = DB::table('enquiry_manage')
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

        return view('company.reports.lead_report', compact('page_title', 'page_name', 'lead_status', 'source', 'staffList', 'creators', 'industryTypes'));
    }

    /**
     * Fetch Lead Report list for DataTable.
     */
    public function leadReportList(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->getFilteredLeadsQuery($request);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('lead_details', function ($row) {
                    $nameHtml = '<a href="' . route('lead.details', encrypt($row->enquiry_id)) . '"><strong class="text-primary" data-bs-toggle="tooltip" title="Enquiry Name">' . e($row->enquiry_name) . '</strong></a>';
                    return '
                        ' . $nameHtml . '<br>
                        <small class="text-muted" data-bs-toggle="tooltip" title="Enquiry Number"><i class="fas fa-hashtag text-secondary"></i> ' . $row->enquiry_no . '</small><br>
                        <small class="text-muted" data-bs-toggle="tooltip" title="Enquiry Date"><i class="fas fa-calendar-alt text-warning"></i> ' . date('d-M-Y', strtotime($row->enquiry_date)) . '</small>
                        <small class="text-muted" data-bs-toggle="tooltip" title="Lead Type"><i class="fas fa-filter text-info"></i> ' . e($row->lead_type) . '</small>
                    ';
                })
                ->orderColumn('lead_details', function ($query, $order) {
                    $query->orderBy('enquiry_name', $order);
                })
                ->addColumn('company_details', function ($row) {
                    $company = $row->customer ? $row->customer->company_name : ($row->company_name ?: '-');
                    $city = $row->city ? $row->city->city_name : '';
                    return '
                        <strong class="text-dark" data-bs-toggle="tooltip" title="Company Name">' . $company . '</strong><br>
                        <small class="text-muted" data-bs-toggle="tooltip" title="City"><i class="fas fa-map-marker-alt text-danger"></i> ' . ($city ?: 'N/A') . '</small>
                    ';
                })
                ->orderColumn('company_details', function ($query, $order) {
                    $query->orderBy('company_name', $order);
                })
                ->addColumn('contact_details', function ($row) {
                    $html = '';
                    if (!empty($row->contact_person_name)) {
                        $html .= '<strong class="text-dark" data-bs-toggle="tooltip" title="Contact Person"><i class="fas fa-user text-primary"></i> ' . $row->contact_person_name . '</strong><br>';
                    }
                    if (!empty($row->phone)) {
                        $html .= '<small data-bs-toggle="tooltip" title="Phone Number"><i class="fas fa-phone-alt text-success"></i> <a href="tel:' . $row->phone . '" class="text-decoration-none text-muted">' . $row->phone . '</a></small><br>';
                    }
                    if (!empty($row->email)) {
                        $html .= '<small data-bs-toggle="tooltip" title="Email Address"><i class="fas fa-envelope text-info"></i> <a href="mailto:' . $row->email . '" class="text-decoration-none text-muted">' . $row->email . '</a></small>';
                    }
                    return $html;
                })
                ->addColumn('product_details', function ($row) {
                    $products = $row->products->map(function ($item) {
                        return $item->product ? '<small class="text-dark" data-bs-toggle="tooltip" title="Product Name"><i class="fas fa-check-circle text-success me-1"></i>' . $item->product->prod_name . '</small>' : null;
                    })->filter()->unique()->implode('<br>');

                    return $products ?: '-';
                })
                ->orderColumn('contact_details', function ($query, $order) {
                    $query->orderBy('contact_person_name', $order);
                })
                ->addColumn('status_source', function ($row) {
                    $status = $row->leadStatus ? $row->leadStatus->title : '-';
                    $source = $row->sourceInfo ? $row->sourceInfo->title : '-';
                    return '
                        <span class="badge bg-info mb-1" data-bs-toggle="tooltip" title="Lead Status">' . $status . '</span><br>
                        <small class="text-muted" data-bs-toggle="tooltip" title="Lead Source"><i class="fas fa-share-alt text-secondary"></i> ' . $source . '</small>
                    ';
                })
                ->orderColumn('status_source', function ($query, $order) {
                    $query->orderBy('lead_status', $order);
                })
                ->addColumn('owner_details', function ($row) {
                    if (Session::get('login_type') !== 'Company') {
                        return '';
                    }
                    $assignedHtml = '';
                    if ($row->assignedStaff) {
                        $assignedHtml = '<small data-bs-toggle="tooltip" title="Assigned To"><i class="fas fa-user-check text-success"></i> <span class="text-success fw-bold">' . $row->assignedStaff->staff_name . '</span></small><br>';
                    }

                    $createdBy = get_createdby_name($row->created_by);
                    $date = date('d-m-Y h:i A', strtotime($row->created_at));

                    return $assignedHtml . '
                        <small class="text-muted" data-bs-toggle="tooltip" title="Created By"><i class="fas fa-user-edit text-info"></i> By: ' . $createdBy . '</small><br>
                        <small class="text-muted" data-bs-toggle="tooltip" title="Created On"><i class="fas fa-clock text-warning"></i> ' . $date . '</small>
                    ';
                })
                ->orderColumn('owner_details', function ($query, $order) {
                    $query->orderBy('created_by', $order);
                })
                ->addColumn('action', function ($row) {
                    $enquiryId = $row->enquiry_id;
                    $leadIdStr = $enquiryId ? $enquiryId : "null";

                    return '
                        <button type="button"
                            class="btn btn-light btn-sm flex-fill shadow-sm border text-info"
                            onclick="LeadTimeline.open({ leadId: ' . $leadIdStr . ' })" data-bs-toggle="tooltip" data-bs-placement="top" title="View Timeline" style="border-radius: 6px;">
                            <i class="fas fa-clock"></i>
                        </button>
                    ';
                })
                ->rawColumns(['lead_details', 'company_details', 'contact_details', 'product_details', 'status_source', 'owner_details', 'action'])
                ->make(true);
        }
    }

    /**
     * Get applied filters list for PDF/Excel titles.
     */
    public function getAppliedFilters(Request $request)
    {
        $filters = [];

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $filters['Date Range'] = date('d-M-Y', strtotime($request->start_date)) . ' to ' . date('d-M-Y', strtotime($request->end_date));
        }
        if ($request->filled('lead_status')) {
            $status = DB::table('lead_status')->where('status_id', $request->lead_status)->value('title');
            $filters['Status'] = $status ?: $request->lead_status;
        }
        if ($request->filled('lead_source')) {
            $source = DB::table('mst_lead_source')->where('source_id', $request->lead_source)->value('title');
            $filters['Source'] = $source ?: $request->lead_source;
        }
        if ($request->filled('assigned_to')) {
            $staff = Staffmodel::where('staff_id', $request->assigned_to)->value('staff_name');
            $filters['Assigned To'] = $staff ?: $request->assigned_to;
        }
        if ($request->filled('created_by')) {
            $filters['Created By'] = get_createdby_name($request->created_by);
        }

        if ($request->filled('lead_type')) {
            $filters['Lead Type'] = $request->lead_type;
        }
        if ($request->filled('industry_id')) {
            $industryName = IndustryTypeModel::where('industry_type_id', $request->industry_id)->value('industry_type_name');
            $filters['Industry Type'] = $industryName ?: $request->industry_id;
        }

        return $filters;
    }

    /**
     * Lead Export PDF.
     */
    public function exportLeadPdf(Request $request)
    {
        $filename = 'Lead_Report_' . date('Ymd_His') . '.pdf';
        $data = $this->getFilteredLeadsQuery($request)->get();
        $filters = $this->getAppliedFilters($request);
        $is_pdf = true;
        
        $pdf = Pdf::loadView('company.reports.lead_report_pdf', compact('data', 'filters', 'is_pdf'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }

    /**
     * Lead Export Excel.
     */
    public function exportLeadExcel(Request $request)
    {
        $filename = 'Lead_Report_' . date('Ymd_His') . '.xlsx';
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
                return view('company.reports.lead_report_pdf', [
                    'data' => $this->controller->getFilteredLeadsQuery($this->request)->get(),
                    'filters' => $this->controller->getAppliedFilters($this->request),
                    'is_pdf' => false
                ]);
            }
        }, $filename);
    }
}

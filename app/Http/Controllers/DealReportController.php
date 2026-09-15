<?php

namespace App\Http\Controllers;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Staffmodel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
class DealReportController extends Controller
{
    //
    public function getFilteredDealsQuery(Request $request)
    {
        $query = Deal::with(['customer.cityRelation', 'dealStage', 'assignedStaff', 'lead.city'])
            ->where('status', 0); // Active deals

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('closing_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('deal_stage')) {
            $query->where('deal_stage', $request->deal_stage);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        $role = Session::get('login_type');
        $userId = getCreatedBy();

        if ($role === 'Staff') {
            $query->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                    ->orWhere('assigned_to', $userId);
            });
        }

        return $query;
       // return $query->orderBy('closing_date', 'desc');
    }
     public function dealReport(Request $request)
    {
        $page_title = 'Deal Report';
        $page_name = 'Deal Report';

        $stages = DealStage::where('status', 0)->orderBy('order_no', 'asc')->get();
        $staffList = Staffmodel::where('status', 0)->orderBy('staff_name', 'asc')->get();

        return view('company.reports.deal_report', compact('page_title', 'page_name', 'stages', 'staffList'));
    }

     public function dealReportList(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->getFilteredDealsQuery($request);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('deal_details', function ($row) {
                    return '<a href="' . route('deal.details', encrypt($row->deal_id)) . '"><strong class="text-primary" data-bs-toggle="tooltip" title="Deal Name">' . e($row->deal_name) . '</strong></a>';
                })
                ->addColumn('company_details', function ($row) {
                    if ($row->customer) {
                        $company = $row->customer->company_name;
                        $city = $row->customer->cityRelation ? $row->customer->cityRelation->city_name : '';
                    } else {
                        $company = $row->lead ? $row->lead->company_name : '-';
                        $city = ($row->lead && $row->lead->city) ? $row->lead->city->city_name : '';
                    }
                    return '
                        <strong class="text-dark" data-bs-toggle="tooltip" title="Company Name">' . e($company) . '</strong><br>
                        <small class="text-muted" data-bs-toggle="tooltip" title="City"><i class="fas fa-map-marker-alt text-danger"></i> ' . e($city ?: 'N/A') . '</small>
                    ';
                })
                ->addColumn('contact_details', function ($row) {
                    $html = '';
                    if ($row->customer) {
                        $person = $row->customer->contact_person;
                        $phone = $row->customer->phone;
                        $email = $row->customer->email;
                    } else {
                        $person = $row->lead ? $row->lead->contact_person_name : '';
                        $phone = $row->lead ? $row->lead->phone : '';
                        $email = $row->lead ? $row->lead->email : '';
                    }

                    if (!empty($person)) {
                        $html .= '<strong class="text-dark" data-bs-toggle="tooltip" title="Contact Person"><i class="fas fa-user text-primary"></i> ' . e($person) . '</strong><br>';
                    }
                    if (!empty($phone)) {
                        $html .= '<small data-bs-toggle="tooltip" title="Phone Number"><i class="fas fa-phone-alt text-success"></i> <a href="tel:' . e($phone) . '" class="text-decoration-none text-muted">' . e($phone) . '</a></small><br>';
                    }
                    if (!empty($email)) {
                        $html .= '<small data-bs-toggle="tooltip" title="Email Address"><i class="fas fa-envelope text-info"></i> <a href="mailto:' . e($email) . '" class="text-decoration-none text-muted">' . e($email) . '</a></small>';
                    }
                    return $html;
                })
                ->addColumn('amount', function ($row) {
                    return '<strong class="text-success">₹ ' . number_format($row->amount, 2) . '</strong>';
                })
                ->addColumn('closing_date', function ($row) {
                    return $row->closing_date ? date('d-M-Y', strtotime($row->closing_date)) : '-';
                })
                ->addColumn('deal_stage', function ($row) {
                    $stage = $row->dealStage ? $row->dealStage->stage_name : '-';
                    return '<span class="badge bg-primary-transparent">' . $stage . '</span>';
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
                ->addColumn('action', function ($row) {
                    $dealId = $row->deal_id;
                    $leadId = $row->lead_id;

                    $leadIdStr = $leadId ? $leadId : "null";
                    $dealIdStr = $dealId ? $dealId : "null";

                    return '
                        <button type="button"
                            class="btn btn-light btn-sm flex-fill shadow-sm border text-info"
                            onclick="LeadTimeline.open({ leadId: ' . $leadIdStr . ', dealId: ' . $dealIdStr . ' })" data-bs-toggle="tooltip" data-bs-placement="top" title="View Timeline" style="border-radius: 6px;">
                            <i class="fas fa-clock"></i>
                        </button>
                    ';
                })
                 ->orderColumn('deal_name', function ($query, $order) {
                    $query->orderBy('deal_name', $order);
                })

                ->orderColumn('customer_id', function ($query, $order) {
                    $query->orderBy('customer_id', $order);
                })

                ->orderColumn('amount', function ($query, $order) {
                    $query->orderBy('amount', $order);
                })

                ->orderColumn('closing_date', function ($query, $order) {
                    $query->orderBy('closing_date', $order);
                })

                ->orderColumn('deal_stage', function ($query, $order) {
                    $query->orderBy('deal_stage', $order);
                })

                ->orderColumn('created_by', function ($query, $order) {
                    $query->orderBy('created_by', $order);
                })
                ->rawColumns(['deal_details', 'company_details', 'contact_details', 'amount', 'deal_stage', 'owner_details', 'action'])
                ->make(true);
        }
    }

      public function getAppliedFilters(Request $request)
    {
        $filters = [];

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $filters['Date Range'] = date('d-M-Y', strtotime($request->start_date)) . ' to ' . date('d-M-Y', strtotime($request->end_date));
        }
        if ($request->filled('deal_stage')) {
            $stage = DealStage::where('id', $request->deal_stage)->value('stage_name');
            $filters['Stage'] = $stage ?: $request->deal_stage;
        }
        if ($request->filled('assigned_to')) {
            $staff = Staffmodel::where('staff_id', $request->assigned_to)->value('staff_name');
            $filters['Assigned To'] = $staff ?: $request->assigned_to;
        }
        if ($request->filled('created_by')) {
            $filters['Created By'] = get_createdby_name($request->created_by);
        }

        return $filters;
    }

    public function exportDealPdf(Request $request)
    {
        $filename = 'Deal_Report_' . date('Ymd_His') . '.pdf';
        $data = $this->getFilteredDealsQuery($request)->get();
        $filters = $this->getAppliedFilters($request);
        $is_pdf = true;

        $pdf = Pdf::loadView('company.reports.deal_report_pdf', compact('data', 'filters', 'is_pdf'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }

     public function exportDealExcel(Request $request)
    {
        $filename = 'Deal_Report_' . date('Ymd_His') . '.xlsx';
        return Excel::download(
            new class ($request, $this) implements \Maatwebsite\Excel\Concerns\FromView, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
            protected $request;
            protected $controller;

            public function __construct($request, $controller)
            {
                $this->request = $request;
                $this->controller = $controller;
            }

            public function view(): \Illuminate\Contracts\View\View
            {
                return view('company.reports.deal_report_pdf', [
                    'data' => $this->controller->getFilteredDealsQuery($this->request)->get(),
                    'filters' => $this->controller->getAppliedFilters($this->request),
                    'is_pdf' => false
                ]);
            }
            },
            $filename
        );
    }
}

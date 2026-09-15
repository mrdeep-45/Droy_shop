<?php

namespace App\Http\Controllers;
use App\Models\EnquiryManageModel;
use App\Models\Deal;
use App\Models\Quotation;
use App\Models\PerformaInvoice;
use App\Models\Staffmodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
class CrmReportController extends Controller
{
    //
     public function crmReport(Request $request)
    {
        $page_title = 'CRM Report';
        $page_name = 'CRM Report';

        $staffList = Staffmodel::where('status', 0)->orderBy('staff_name', 'asc')->get();

        return view('company.reports.crm_report', compact('page_title', 'page_name', 'staffList'));
    }
      public function crmReportList(Request $request)
    {
        if ($request->ajax()) {
            
            $query = EnquiryManageModel::with(['customer', 'city', 'assignedStaff','createdBy'])
                ->whereIn('status', [0, 1]); // Active and Converted leads

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('enquiry_date', [$request->start_date, $request->end_date]);
            }

            if ($request->filled('assigned_to')) {
                $query->where('assigned_to', $request->assigned_to);
            }

            // Scope to staff's own leads
            $role   = Session::get('login_type');
            $userId = getCreatedBy();
            if ($role === 'Staff') {
                $query->where(function ($q) use ($userId) {
                    $q->where('created_by', $userId)
                      ->orWhere('assigned_to', $userId);
                });
            }

            $data = $query->orderByRaw('
                (
                    EXISTS (SELECT 1 FROM tbl_deal WHERE lead_id = enquiry_manage.enquiry_id AND status = 0) OR
                    EXISTS (SELECT 1 FROM tbl_quotation WHERE lead_id = enquiry_manage.enquiry_id AND status = 0) OR
                    EXISTS (SELECT 1 FROM tbl_performa_invoice WHERE quotation_id IN (SELECT quotation_id FROM tbl_quotation WHERE lead_id = enquiry_manage.enquiry_id) AND status = 0)
                ) DESC
            ')->orderBy('enquiry_date', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('enquiry_customer', function ($row) {
                    $company = $row->customer ? $row->customer->company_name : ($row->company_name ?: '-');
                    $city = $row->city ? $row->city->city_name : '';
                    
                    $html = '<strong class="text-dark">' . $company . '</strong>';
                    if ($city) {
                        $html .= ' <small class="text-muted"><i class="fas fa-map-marker-alt text-danger ms-1"></i> ' . $city . '</small>';
                    }
                    $html .= '<br>';

                    if (!empty($row->contact_person_name)) {
                        $html .= '<small class="text-muted me-2"><i class="fas fa-user text-secondary me-1"></i> ' . $row->contact_person_name . '</small>';
                    }
                    if (!empty($row->phone)) {
                        $html .= '<small class="text-muted"><i class="fas fa-phone-alt text-success me-1"></i> ' . $row->phone . '</small>';
                    }

                    return $html;
                })
                ->addColumn('pipeline_status', function ($row) {
                    // Resolve stages
                    $enquiryId = $row->enquiry_id;

                    // 1. Lead (always true)
                    $isLead = true;
                    $leadUrl = route('lead.details', encrypt($enquiryId));
                    $leadCreatedAt  = $row->created_at ? Carbon::parse($row->created_at)->format('d-m-Y H:i:s') : '-';
                    $leadCreatedBy  = $row->createdBy ? $row->createdBy->staff_name : (get_createdby_name($row->created_by) ?: 'System');
                   $leadTooltipText = "Lead Details: Created At: {$leadCreatedAt} Created By: {$leadCreatedBy}";  
                    // 2. Deal
                    $deal = Deal::with('assignedStaff')->where('lead_id', $enquiryId)->where('status', 0)->latest('created_at')->first();
                    $isDeal = !is_null($deal);
                    $dealUrl = $isDeal ? route('deal.details', encrypt($deal->deal_id)) : 'javascript:void(0)';
                    if ($isDeal) {
                        $dealCreatedAt = $deal->created_at ? Carbon::parse($deal->created_at)->format('d-m-Y H:i:s') : '-';
                        $dealClosing   = $deal->closing_date ? Carbon::parse($deal->closing_date)->format('d-m-Y') : 'N/A';
                        $dealCreatedBy = get_createdby_name($deal->created_by) ?: 'System';
                        $dealTooltip   = "data-bs-toggle=\"tooltip\" data-bs-html=\"true\" title=\"<strong>Deal Details</strong><br>Converted At: {$dealCreatedAt}<br>Closing Date: {$dealClosing}<br>Created By: {$dealCreatedBy}\"";
                    } else {
                        $dealTooltip   = 'data-bs-toggle="tooltip" title="Stage not reached"';
                    }
                    // 3. Quotation
                    $quotation = Quotation::where('lead_id', $enquiryId)->where('status', 0)->latest('created_at')->first();
                    if (!$quotation && $deal) {
                        $quotation = Quotation::where('deal_id', $deal->deal_id)->where('status', 0)->latest('created_at')->first();
                    }
                    $isQuot = !is_null($quotation);
                    $quotUrl = $isQuot ? route('quotation.pdf', encrypt($quotation->quotation_id)) : 'javascript:void(0)';
                    if ($isQuot) {
                        $quotCreatedAt = $quotation->created_at ? Carbon::parse($quotation->created_at)->format('d-m-Y H:i:s') : '-';
                        $quotCreatedBy = get_createdby_name($quotation->created_by) ?: 'System';
                        $quotTooltip   = "data-bs-toggle=\"tooltip\" data-bs-html=\"true\" title=\"<strong>Quotation Details</strong><br>Created At: {$quotCreatedAt}<br>Created By: {$quotCreatedBy}\"";
                    } else {
                        $quotTooltip   = 'data-bs-toggle="tooltip" title="Stage not reached"';
                    }
                    // 4. Performa
                    $performa = null;
                    if ($quotation) {
                        $performa = PerformaInvoice::where('quotation_id', $quotation->quotation_id)->where('status', 0)->latest('created_at')->first();
                    }
                    $isPerf = !is_null($performa);
                    $perfUrl = $isPerf ? route('performa_invoice.pdf', encrypt($performa->invoice_id)) : 'javascript:void(0)';
                    if ($isPerf) {
                        $perfCreatedAt = $performa->created_at ? Carbon::parse($performa->created_at)->format('d-m-Y H:i:s') : '-';
                        $perfCreatedBy = get_createdby_name($performa->created_by) ?: 'System';
                        $perfTooltip   = "data-bs-toggle=\"tooltip\" data-bs-html=\"true\" title=\"<strong>Performa Details</strong><br>Created At: {$perfCreatedAt}<br>Created By: {$perfCreatedBy}\"";
                    } else {
                        $perfTooltip   = 'data-bs-toggle="tooltip" title="Stage not reached"';
                    }
                    // 5. Customer
                    $isCust = false;
                    $custTooltip = 'data-bs-toggle="tooltip" title="Stage not reached"';
                    if ($deal && in_array($deal->deal_stage, [12, 13]) && $deal->deal_status == 1) {
                        $isCust = true;
                        $custCreatedAt = $deal->updated_at ? Carbon::parse($deal->updated_at)->format('d-m-Y H:i:s') : '-';
                        $custTooltip   = "data-bs-toggle=\"tooltip\" data-bs-html=\"true\" title=\"<strong>Customer Details</strong><br>Converted At: {$custCreatedAt}\"";
                    }

                    // Progress percentage for visual timeline bar
                    $progressPercent = 0;
                    if ($isCust) {
                        $progressPercent = 100;
                    } elseif ($isPerf) {
                        $progressPercent = 75;
                    } elseif ($isQuot) {
                        $progressPercent = 50;
                    } elseif ($isDeal) {
                        $progressPercent = 25;
                    }

                    // CSS class maps
                    $leadClass = 'circle-lead active';
                    $leadLabelClass = 'pipeline-label active-lead';

                    $dealClass = $isDeal ? 'circle-deal active' : 'bg-light';
                    $dealLabelClass = $isDeal ? 'pipeline-label active-deal' : 'pipeline-label text-muted';
                    //$dealTooltip = $isDeal ? 'data-bs-toggle="tooltip" title="View Deal Details"' : 'data-bs-toggle="tooltip" title="Stage not reached"';

                    $quotClass = $isQuot ? 'circle-quot active' : 'bg-light';
                    $quotLabelClass = $isQuot ? 'pipeline-label active-quot' : 'pipeline-label text-muted';
                    //$quotTooltip = $isQuot ? 'data-bs-toggle="tooltip" title="Download Quotation PDF"' : 'data-bs-toggle="tooltip" title="Stage not reached"';

                    $perfClass = $isPerf ? 'circle-perf active' : 'bg-light';
                    $perfLabelClass = $isPerf ? 'pipeline-label active-perf' : 'pipeline-label text-muted';
                   // $perfTooltip = $isPerf ? 'data-bs-toggle="tooltip" title="Download Performa Invoice PDF"' : 'data-bs-toggle="tooltip" title="Stage not reached"';

                    $custClass = $isCust ? 'circle-cust active' : 'bg-light';
                    $custLabelClass = $isCust ? 'pipeline-label active-cust' : 'pipeline-label text-muted';

                    if ($isCust) {
                        $custClass .= ' latest-cust';
                    } elseif ($isPerf) {
                        $perfClass .= ' latest-perf';
                    } elseif ($isQuot) {
                        $quotClass .= ' latest-quot';
                    } elseif ($isDeal) {
                        $dealClass .= ' latest-deal';
                    } else {
                        $leadClass .= ' latest-lead';
                    }

                    $role = Session::get('login_type');
                    $isCompany = $role === 'Company';
                    $hasAccess = in_array($role, ['Company', 'Staff']);

                    // Lead link — only for Company
                    $leadLink  = $hasAccess ? '<a href="' . $leadUrl . '" class="pipeline-circle ' . $leadClass . ' text-white" data-bs-toggle="tooltip" title="' . e($leadTooltipText) . '"><i class="fas fa-shopping-cart"></i></a>' :
                                              '<span class="pipeline-circle ' . $leadClass . ' text-white"><i class="fas fa-shopping-cart"></i></span>';

                    // Deal link — only for Company
                    $dealLink  = ($hasAccess && $isDeal) ? '<a href="' . $dealUrl . '" class="pipeline-circle ' . $dealClass . '" ' . $dealTooltip . '><i class="fas fa-file-alt"></i></a>' :
                                                           '<span class="pipeline-circle ' . $dealClass . '" ' . $dealTooltip . '><i class="fas fa-file-alt"></i></span>';

                    return '
                        <div class="pipeline-track d-flex align-items-start justify-content-between position-relative px-2" style="width: 400px; margin: 0 auto; min-height: 48px;">
                            <!-- Base Grey Line -->
                            <div class="pipeline-line position-absolute" style="height: 2px; left: 24px; width: calc(100% - 48px); top: 16px; margin-top: -1px; background: #cbd5e1; z-index: 1;"></div>
                            <!-- Active Color Progress Line -->
                            <div class="pipeline-progress position-absolute" style="height: 2px; left: 24px; width: calc((' . $progressPercent . ' / 100) * (100% - 48px)); top: 16px; margin-top: -1px; background: #3b82f6; z-index: 2; transition: width 0.4s ease;"></div>
                            
                            <!-- Lead -->
                            <div class="pipeline-step position-relative z-3 text-center">
                                ' . $leadLink . '
                                <span class="' . $leadLabelClass . '">Lead</span>
                            </div>

                            <!-- Deal -->
                            <div class="pipeline-step position-relative z-3 text-center">
                                ' . $dealLink . '
                                <span class="' . $dealLabelClass . '">Deal</span>
                            </div>

                            <!-- Quotation -->
                            <div class="pipeline-step position-relative z-3 text-center">
                                <a href="' . $quotUrl . '" ' . ($isQuot ? 'target="_blank"' : '') . ' class="pipeline-circle ' . $quotClass . '" ' . $quotTooltip . '>
                                    <i class="fas fa-file-contract"></i>
                                </a>
                                <span class="' . $quotLabelClass . '">Quotation</span>
                            </div>

                            <!-- Performa -->
                            <div class="pipeline-step position-relative z-3 text-center">
                                <a href="' . $perfUrl . '" ' . ($isPerf ? 'target="_blank"' : '') . ' class="pipeline-circle ' . $perfClass . '" ' . $perfTooltip . '>
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                                <span class="' . $perfLabelClass . '">Performa</span>
                            </div>
                            
                            <!-- Customer -->
                            <div class="pipeline-step position-relative z-3 text-center">
                                <a href="javascript:void(0)" class="pipeline-circle ' . $custClass . '" ' . $custTooltip . '>
                                    <i class="fas fa-user-check"></i>
                                </a>
                                <span class="' . $custLabelClass . '">Customer</span>
                            </div>
                        </div>
                    ';
                })
                ->addColumn('value', function ($row) {
                    $enquiryId = $row->enquiry_id;

                    // Resolve the furthest value
                    $deal = Deal::where('lead_id', $enquiryId)->where('status', 0)->latest('created_at')->first();
                    $quotation = Quotation::where('lead_id', $enquiryId)->where('status', 0)->latest('created_at')->first();
                    if (!$quotation && $deal) {
                        $quotation = Quotation::where('deal_id', $deal->deal_id)->where('status', 0)->latest('created_at')->first();
                    }
                    $performa = null;
                    if ($quotation) {
                        $performa = PerformaInvoice::where('quotation_id', $quotation->quotation_id)->where('status', 0)->latest('created_at')->first();
                    }

                    if ($isPerf = !is_null($performa)) {
                        return '
                            <span class="badge-soft-warning mb-1">Performa Value</span><br>
                            <strong class="text-dark fs-13">₹ ' . number_format($performa->grand_total, 2) . '</strong>
                        ';
                    } elseif ($isQuot = !is_null($quotation)) {
                        return '
                            <span class="badge-soft-success mb-1">Quotation Value</span><br>
                            <strong class="text-dark fs-13">₹ ' . number_format($quotation->grand_total, 2) . '</strong>
                        ';
                    } elseif ($isDeal = !is_null($deal)) {
                        return '
                            <span class="badge-soft-info mb-1">Deal Value</span><br>
                            <strong class="text-dark fs-13">₹ ' . number_format($deal->amount, 2) . '</strong>
                        ';
                    }

                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('date', function ($row) {
                    return date('d-m-Y', strtotime($row->enquiry_date));
                })
                ->addColumn('action', function ($row) {
                    $enquiryId = $row->enquiry_id;
                    $deal = Deal::where('lead_id', $enquiryId)->where('status', 0)->latest('created_at')->first();
                    $quotation = Quotation::where('lead_id', $enquiryId)->where('status', 0)->latest('created_at')->first();
                    if (!$quotation && $deal) {
                        $quotation = Quotation::where('deal_id', $deal->deal_id)->where('status', 0)->latest('created_at')->first();
                    }
                    $performa = null;
                    if ($quotation) {
                        $performa = PerformaInvoice::where('quotation_id', $quotation->quotation_id)->where('status', 0)->latest('created_at')->first();
                    }
                    
                    $leadIdStr = $enquiryId ? $enquiryId : "null";
                    $dealIdStr = $deal ? $deal->deal_id : "null";
                    $quotIdStr = $quotation ? $quotation->quotation_id : "null";
                    $perfIdStr = $performa ? $performa->invoice_id : "null";

                    return '
                        <button type="button"
                            class="btn btn-light btn-sm flex-fill shadow-sm border text-info"
                            onclick="LeadTimeline.open({ leadId: ' . $leadIdStr . ', dealId: ' . $dealIdStr . ', quotationId: ' . $quotIdStr . ', performaId: ' . $perfIdStr . ' })" data-bs-toggle="tooltip" data-bs-placement="top" title="View Timeline" style="border-radius: 6px;">
                            <i class="fas fa-clock"></i>
                        </button>
                    ';
                })
                ->rawColumns(['enquiry_customer', 'pipeline_status', 'value', 'action'])
                ->make(true);
        }
    }
}

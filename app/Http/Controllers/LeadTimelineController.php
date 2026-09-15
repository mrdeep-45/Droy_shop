<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\DealLog;
use App\Models\MainInvoice;
use App\Models\PerformaInvoice;
use App\Models\PerformaInvoiceLog;
use App\Models\Quotation;
use App\Models\QuotationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EnquiryManageModel;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LeadTimelineController extends Controller
{
    /**
     * Get the timeline HTML for a given lead or deal.
     */
    public function getTimeline(Request $request)
    {
        $leadId = $request->input('leadId');
        $dealId = $request->input('dealId');

        // Resolve missing IDs to ensure reliable attachment lookup
        if ($dealId && !$leadId) {
            $deal = \App\Models\Deal::find($dealId);
            if ($deal) {
                $leadId = $deal->lead_id;
            }
        } elseif ($leadId && !$dealId) {
            $deal = Deal::where('lead_id', $leadId)->first();
            if ($deal) {
                $dealId = $deal->deal_id;
            }
        }

        $processedLogs = $this->getTimelineLogsData($leadId, $dealId);

        // Group by Date String like "09 Jul 2026"
        $groupedLogs = [];
        foreach ($processedLogs as $log) {
            $groupedLogs[$log['date_str']][] = [
                'time' => $log['time'],
                'title' => $log['title'],
                'description' => $log['description'],
                'user' => $log['user'],
                'icon' => $log['icon'],
                'color' => $log['color'],
                'is_quotation_log' => $log['is_quotation_log'],
                'is_performa_log' => $log['is_performa_log'],
                'is_main_invoice_log' => $log['is_main_invoice_log'],
                'enc_quotation_id' => $log['enc_quotation_id'],
                'enc_performa_id' => $log['enc_performa_id'],
                'enc_main_id' => $log['enc_main_id'],
                'parsed_payload' => $log['parsed_payload'],
                'log_id' => $log['log_id'],
                'file_url' => $log['file_url']
            ];
        }

        return view('components.lead-timeline-partial', compact('groupedLogs'));
    }

    /**
     * Download the timeline in Excel or PDF format.
     */
    public function downloadTimeline(Request $request)
    {
        $leadId = $request->input('leadId');
        $dealId = $request->input('dealId');
        $format = $request->input('format', 'excel'); // excel or pdf

        // Resolve missing IDs to ensure reliable attachment lookup
        if ($dealId && !$leadId) {
            $deal = \App\Models\Deal::find($dealId);
            if ($deal) {
                $leadId = $deal->lead_id;
            }
        } elseif ($leadId && !$dealId) {
            $deal = \App\Models\Deal::where('lead_id', $leadId)->first();
            if ($deal) {
                $dealId = $deal->deal_id;
            }
        }

        $logs = $this->getTimelineLogsData($leadId, $dealId);

        // Determine title and lead details
        $title = '';
        $lead = null;
        $nameSlug = '';
        if ($dealId) {
            $deal = Deal::find($dealId);
            $title = 'Deal: ' . ($deal ? $deal->deal_name : 'Unknown');
            if ($deal) {
                $nameSlug = $deal->deal_name;
                if ($deal->lead_id) {
                    $lead = EnquiryManageModel::with(['customer', 'products.product'])->find($deal->lead_id);
                    if ($lead && $lead->customer_name) {
                        $nameSlug = $lead->customer_name . '_' . $deal->deal_name;
                    }
                }
            }
        } elseif ($leadId) {
            $lead = EnquiryManageModel::with(['customer', 'products.product'])->find($leadId);
            $title = 'Lead: ' . ($lead ? ($lead->customer_name ?? $lead->enquiry_no) : 'Unknown');
            if ($lead) {
                $nameSlug = $lead->customer_name ?? $lead->enquiry_no;
            }
        } else {
            $title = 'All Logs';
        }

        $companyName = '';
        $productNames = '';
        if ($lead) {
            $companyName = $lead->company_name ?? $lead->customer_name ?? '';
            if ($lead->products) {
                $productNames = $lead->products->map(function($p) {
                    return $p->product ? $p->product->product_name : '';
                })->filter()->implode('_');
            }
        }
        
        $companyNameClean = trim(preg_replace('/[^A-Za-z0-9\-]+/', '_', $companyName), '_');
        $productNamesClean = trim(preg_replace('/[^A-Za-z0-9\-]+/', '_', $productNames), '_');
        
        $parts = ['Timeline'];
        if ($companyNameClean) $parts[] = $companyNameClean;
        if ($productNamesClean) $parts[] = $productNamesClean;
        
        $filename = implode('_', $parts);
        if (strlen($filename) <= 9) {
            $filename = 'Timeline_History_' . date('Ymd_His');
        } else {
            $filename = substr($filename, 0, 150);
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('company.crm.lead.timeline_export', [
                'logs' => $logs,
                'title' => $title,
                'lead' => $lead,
                'is_pdf' => true
            ])->setPaper('a4', 'landscape');

            return $pdf->stream($filename . '.pdf');
        } else {
            return Excel::download(
                new class ($logs, $title, $lead) implements \Maatwebsite\Excel\Concerns\FromView, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
                    protected $logs;
                    protected $title;
                    protected $lead;

                    public function __construct($logs, $title, $lead)
                    {
                        $this->logs = $logs;
                        $this->title = $title;
                        $this->lead = $lead;
                    }

                    public function view(): \Illuminate\Contracts\View\View
                    {
                        return view('company.crm.lead.timeline_export', [
                            'logs' => $this->logs,
                            'title' => $this->title,
                            'lead' => $this->lead,
                            'is_pdf' => false
                        ]);
                    }
                },
                $filename . '.xlsx'
            );
        }
    }

    /**
     * Internal helper to fetch and parse raw log entries for a lead or deal.
     */
    private function getTimelineLogsData($leadId, $dealId)
    {
        $rawLogs = [];
        $quotationIds = collect();

        if ($dealId) {
            $dealLogs = DealLog::where('deal_id', $dealId)->get();
            $quotationIds = Quotation::where('deal_id', $dealId)->pluck('quotation_id');
            $quotationLogs = QuotationLog::whereIn('quotation_id', $quotationIds)->get();

            $allLogs = $dealLogs->concat($quotationLogs)->sortByDesc('performed_at')->values();

            foreach ($allLogs as $log) {
                $isQuotationLog = isset($log->quotation_id);
                $encQuotationId = $isQuotationLog ? encrypt($log->quotation_id) : null;
                $parsedPayload = null;

                if ($isQuotationLog && !empty($log->payload)) {
                    $payload = $log->payload;
                    if (is_string($payload))
                        $payload = json_decode($payload, true);
                    if (is_string($payload))
                        $payload = json_decode($payload, true);
                    // Format dates inside parsed payload if present
                    if (is_array($payload)) {
                        foreach (['quotation_date', 'created_at', 'updated_at', 'date'] as $dateKey) {
                            if (!empty($payload[$dateKey])) {
                                try {
                                    $payload[$dateKey] = Carbon::parse($payload[$dateKey])->format('d-m-Y H:i:s');
                                } catch (\Exception $e) {}
                            }
                        }
                    }
                    $parsedPayload = $payload;
                }

                $rawLogs[] = [
                    'log_id' => $log->log_id ?? $log->id ?? uniqid(),
                    'action' => $log->action,
                    'description' => $log->description,
                    'performed_by' => get_createdby_name($log->performed_by) ?: 'System',
                    'date' => Carbon::parse($log->performed_at),
                    'is_quotation_log' => $isQuotationLog,
                    'enc_quotation_id' => $encQuotationId,
                    'parsed_payload' => $parsedPayload
                ];
            }

            // Also fetch Lead logs if lead_id exists
            $deal = Deal::find($dealId);
            if ($deal && $deal->lead_id) {
                $leadLogs = DB::table('enquiry_history')
                    ->where('enquiry_id', $deal->lead_id)
                    ->get();

                foreach ($leadLogs as $log) {
                    $rawLogs[] = [
                        'log_id' => uniqid(),
                        'action' => $log->change_type,
                        'description' => '(Lead Phase) ' . $log->reason,
                        'performed_by' => get_createdby_name($log->changed_by) ?: 'System',
                        'date' => Carbon::parse($log->changed_at),
                        'is_quotation_log' => false,
                        'enc_quotation_id' => null,
                        'parsed_payload' => null
                    ];
                }
            }
        } elseif ($leadId) {
            $logs = DB::table('enquiry_history')
                ->where('enquiry_id', $leadId)
                ->get();

            foreach ($logs as $log) {
                $rawLogs[] = [
                    'log_id' => $log->history_id ?? uniqid(),
                    'action' => $log->change_type,
                    'description' => $log->reason,
                    'performed_by' => get_createdby_name($log->changed_by) ?: 'System',
                    'date' => Carbon::parse($log->changed_at),
                    'is_quotation_log' => false,
                    'enc_quotation_id' => null,
                    'parsed_payload' => null
                ];
            }

            $deal = Deal::where('lead_id', $leadId)->first();
            if ($deal) {
                $dealLogs = DealLog::where('deal_id', $deal->deal_id)->get();
                $quotationIds = Quotation::where('deal_id', $deal->deal_id)->pluck('quotation_id');
                $quotationLogs = QuotationLog::whereIn('quotation_id', $quotationIds)->get();

                $allDealLogs = $dealLogs->concat($quotationLogs);
                foreach ($allDealLogs as $log) {
                    $isQuotationLog = isset($log->quotation_id);
                    $encQuotationId = $isQuotationLog ? encrypt($log->quotation_id) : null;
                    $parsedPayload = null;

                    if ($isQuotationLog && !empty($log->payload)) {
                        $payload = $log->payload;
                        if (is_string($payload)) {
                            $payload = json_decode($payload, true);
                        }
                        if (is_string($payload)) {
                            $payload = json_decode($payload, true);
                        }
                        if (is_array($payload)) {
                            foreach (['quotation_date', 'created_at', 'updated_at', 'date'] as $dateKey) {
                                if (!empty($payload[$dateKey])) {
                                    try {
                                        $payload[$dateKey] = Carbon::parse($payload[$dateKey])->format('d-m-Y H:i:s');
                                    } catch (\Exception $e) {}
                                }
                            }
                        }
                        $parsedPayload = $payload;
                    }

                    $rawLogs[] = [
                        'log_id' => $log->log_id ?? $log->id ?? uniqid(),
                        'action' => $log->action,
                        'description' => '(Deal Phase) ' . $log->description,
                        'performed_by' => get_createdby_name($log->performed_by) ?: 'System',
                        'date' => Carbon::parse($log->performed_at),
                        'is_quotation_log' => $isQuotationLog,
                        'enc_quotation_id' => $encQuotationId,
                        'parsed_payload' => $parsedPayload
                    ];
                }
            }
        }

        // Fetch Performa Invoice logs and Main Invoices if quotationIds exist
        if ($quotationIds->count() > 0) {
            $performaInvoices = PerformaInvoice::whereIn('quotation_id', $quotationIds)->get();
            $performaIds = $performaInvoices->pluck('invoice_id')->toArray();

            if (!empty($performaIds)) {
                $performaLogs = PerformaInvoiceLog::whereIn('invoice_id', $performaIds)->get();
                foreach ($performaLogs as $pLog) {
                    $payload = $pLog->payload;
                    if (is_string($payload)) $payload = json_decode($payload, true);
                    if (is_string($payload)) $payload = json_decode($payload, true);
                    if (is_array($payload)) {
                        foreach (['invoice_date', 'created_at', 'updated_at', 'date'] as $dateKey) {
                            if (!empty($payload[$dateKey])) {
                                try {
                                    $payload[$dateKey] = Carbon::parse($payload[$dateKey])->format('d-m-Y H:i:s');
                                } catch (\Exception $e) {}
                            }
                        }
                    }
                    $rawLogs[] = [
                        'log_id' => 'pi_' . ($pLog->log_id ?? uniqid()),
                        'action' => 'performa_invoice',
                        'description' => $pLog->description,
                        'performed_by' => get_createdby_name($pLog->performed_by) ?: 'System',
                        'date' => Carbon::parse($pLog->performed_at),
                        'is_quotation_log' => false,
                        'is_performa_log' => true,
                        'enc_performa_id' => encrypt($pLog->invoice_id),
                        'parsed_payload' => $payload
                    ];
                }
            }

            $mainInvoices = MainInvoice::whereIn('quotation_id', $quotationIds)
                ->when(!empty($performaIds), fn($q) => $q->orWhereIn('performa_invoice_id', $performaIds))
                ->get();

            foreach ($mainInvoices as $mInv) {
                $rawLogs[] = [
                    'log_id' => 'mi_' . $mInv->main_invoice_id,
                    'action' => 'main_invoice',
                    'description' => 'Main Invoice Created: ' . $mInv->invoice_no . ' (Amount: ' . ($mInv->currency ?: '₹') . ' ' . number_format($mInv->grand_total, 2) . ')',
                    'performed_by' => get_createdby_name($mInv->created_by) ?: 'System',
                    'date' => Carbon::parse($mInv->created_at),
                    'is_quotation_log' => false,
                    'is_main_invoice_log' => true,
                    'enc_main_id' => encrypt($mInv->main_invoice_id),
                    'parsed_payload' => [
                        'invoice_no' => $mInv->invoice_no,
                        'grand_total' => $mInv->grand_total,
                        'currency' => $mInv->currency,
                        'email' => $mInv->email,
                        'phone' => $mInv->phone,
                        'invoice_type' => $mInv->invoice_type,
                        'invoice_date' => $mInv->invoice_date ? Carbon::parse($mInv->invoice_date)->format('d-m-Y H:i:s') : '-'
                    ]
                ];
            }
        }

        // Sort just in case (newest first)
        usort($rawLogs, function ($a, $b) {
            return $b['date']->timestamp - $a['date']->timestamp;
        });

        $totalNotesAdded = count(array_filter($rawLogs, function($l) {
            $actionLower = strtolower($l['action']);
            return str_contains($actionLower, 'note_added') || str_contains($actionLower, 'note added');
        }));

        $allNotes = \App\Models\CrmNote::where(function($q) use ($dealId, $leadId) {
            if ($dealId) {
                $q->where(function($sq) use ($dealId) {
                    $sq->where('reference_type', 'deal')->where('reference_id', $dealId);
                });
                if ($leadId) {
                    $q->orWhere(function($sq) use ($leadId) {
                        $sq->where('reference_type', 'lead')->where('reference_id', $leadId);
                    });
                }
            } elseif ($leadId) {
                $q->where('reference_type', 'lead')->where('reference_id', $leadId);
            }
        })->orderBy('id', 'asc')->get()->values();

        $currentNoteNumber = $totalNotesAdded;

        $processedLogs = [];
        foreach ($rawLogs as $log) {
            $dateStr = $log['date']->format('d M Y');
            $timeStr = $log['date']->format('h:i A');

            $actionLower = strtolower($log['action']);
            $markerClass = 'bg-primary';
            $icon = '<i class="fas fa-info"></i>';
            $boldTitle = 'Activity';

            if (str_contains($actionLower, 'create') || str_contains($actionLower, 'import')) {
                $markerClass = 'bg-success';
                $icon = '<i class="fas fa-plus"></i>';
                $boldTitle = 'Created';
            } elseif (str_contains($actionLower, 'delete') && (str_contains($actionLower, 'enquiry') || str_contains($actionLower, 'lead'))) {
                $markerClass = 'bg-danger';
                $icon = '<i class="fas fa-trash-alt"></i>';
                $boldTitle = 'Deleted';
            } elseif (str_contains($actionLower, 'share') || str_contains($actionLower, 'assign')) {
                $markerClass = 'bg-info';
                $icon = '<i class="fas fa-share-alt"></i>';
                $boldTitle = 'Shared';
            } elseif (str_contains($actionLower, 'status') && (str_contains($actionLower, 'enquiry') || str_contains($actionLower, 'lead'))) {
                $markerClass = 'bg-warning';
                $icon = '<i class="fas fa-toggle-on"></i>';
                $boldTitle = 'Status Changed';
            } elseif (str_contains($actionLower, 'update') || str_contains($actionLower, 'edit')) {
                $markerClass = 'bg-primary';
                $icon = '<i class="fas fa-pen"></i>';
                $boldTitle = 'Updated';
            } elseif (str_contains($actionLower, 'call_scheduled') || str_contains($actionLower, 'call scheduled')) {
                $markerClass = 'bg-info';
                $icon = '<i class="fas fa-phone-alt"></i>';
                $boldTitle = 'Call Scheduled';
            } elseif (str_contains($actionLower, 'call_status_updated') || str_contains($actionLower, 'call status updated') || str_contains($actionLower, 'call_completed') || str_contains($actionLower, 'call completed')) {
                $markerClass = 'bg-success';
                $icon = '<i class="fas fa-phone-volume"></i>';
                $boldTitle = 'Call Activity';
            } elseif (str_contains($actionLower, 'call_deleted') || str_contains($actionLower, 'call deleted') || str_contains($actionLower, 'call_cancelled') || str_contains($actionLower, 'call cancelled')) {
                $markerClass = 'bg-danger';
                $icon = '<i class="fas fa-phone-slash"></i>';
                $boldTitle = 'Call Cancelled';
            } elseif (str_contains($actionLower, 'note_added') || str_contains($actionLower, 'note added')) {
                $markerClass = 'bg-warning';
                $icon = '<i class="fas fa-sticky-note"></i>';
                $boldTitle = 'Note Added';
            } elseif (str_contains($actionLower, 'note_updated') || str_contains($actionLower, 'note updated') || str_contains($actionLower, 'note_status_updated') || str_contains($actionLower, 'note status updated')) {
                $markerClass = 'bg-warning';
                $icon = '<i class="fas fa-edit"></i>';
                $boldTitle = 'Note Updated';
            } elseif (str_contains($actionLower, 'note_deleted') || str_contains($actionLower, 'note deleted')) {
                $markerClass = 'bg-danger';
                $icon = '<i class="fas fa-trash-alt"></i>';
                $boldTitle = 'Note Deleted';
            } elseif (str_contains($actionLower, 'attachment_uploaded') || str_contains($actionLower, 'attachment uploaded')) {
                $markerClass = 'bg-info';
                $icon = '<i class="fas fa-paperclip"></i>';
                $boldTitle = 'Attachment Uploaded';
            } elseif (str_contains($actionLower, 'attachment_deleted') || str_contains($actionLower, 'attachment deleted')) {
                $markerClass = 'bg-danger';
                $icon = '<i class="fas fa-trash"></i>';
                $boldTitle = 'Attachment Deleted';
            } elseif (str_contains($actionLower, 'performa')) {
                $markerClass = 'bg-info';
                $icon = '<i class="fas fa-file-invoice"></i>';
                $boldTitle = 'Performa Invoice';
            } elseif (str_contains($actionLower, 'main_invoice') || str_contains($actionLower, 'main invoice')) {
                $markerClass = 'bg-dark';
                $icon = '<i class="fas fa-receipt"></i>';
                $boldTitle = 'Main Invoice';
            } elseif (str_contains($actionLower, 'quotation')) {
                $markerClass = 'bg-success';
                $icon = '<i class="fas fa-file-invoice-dollar"></i>';
                $boldTitle = 'Quotation';
            }

            $noteN = null;
            if (str_contains($actionLower, 'note_added') || str_contains($actionLower, 'note added')) {
                $noteN = $currentNoteNumber;
                $currentNoteNumber--;
            } elseif (str_contains($actionLower, 'note_updated') || str_contains($actionLower, 'note updated') || str_contains($actionLower, 'note_deleted') || str_contains($actionLower, 'note deleted') || str_contains($actionLower, 'note_status_updated')) {
                if (preg_match('/note N(\d+)/i', $log['description'], $matches)) {
                    $noteN = (int)$matches[1];
                }
            }
            
            if ($noteN !== null) {
                $icon = '<span class="fw-bold" style="font-size:0.75rem;">N' . $noteN . '</span>';
            }

            $fileUrl = $this->resolveAttachmentUrl($log['description'], $leadId, $dealId);
            if ($noteN !== null && !$fileUrl) {
                $noteIndex = $noteN - 1;
                if (isset($allNotes[$noteIndex])) {
                    $note = $allNotes[$noteIndex];
                    if ($note->attachment) {
                        if (str_starts_with($note->attachment, 'http')) {
                            $fileUrl = $note->attachment;
                        } else {
                            $imgPath = config('app.img_path') ?: asset('assets/uploads/');
                            $fileUrl = rtrim($imgPath, '/') . '/notes/' . $note->attachment;
                        }
                    }
                }
            }

            $processedLogs[] = [
                'date_str' => $dateStr,
                'time' => $timeStr,
                'title' => $boldTitle,
                'description' => $log['description'],
                'user' => $log['performed_by'],
                'icon' => $icon,
                'color' => $markerClass,
                'is_quotation_log' => $log['is_quotation_log'] ?? false,
                'is_performa_log' => $log['is_performa_log'] ?? false,
                'is_main_invoice_log' => $log['is_main_invoice_log'] ?? false,
                'enc_quotation_id' => $log['enc_quotation_id'] ?? null,
                'enc_performa_id' => $log['enc_performa_id'] ?? null,
                'enc_main_id' => $log['enc_main_id'] ?? null,
                'parsed_payload' => $log['parsed_payload'] ?? null,
                'log_id' => $log['log_id'] ?? uniqid(),
                'file_url' => $fileUrl
            ];
        }

        return $processedLogs;
    }

    /**
     * Parse logs and match with tbl_crm_attachments to find file URL, or extract external links.
     */
    private function resolveAttachmentUrl($description, $leadId, $dealId)
    {
        // 1. Check if description has a web link (e.g., https://youtube.com...)
        if (preg_match('/(https?:\/\/[^\s\']+)/', $description, $urlMatches)) {
            return $urlMatches[1];
        }

        // 2. Check if description matches uploaded file formats
        $fileName = null;
        if (preg_match("/Uploaded file: '(.*)'/", $description, $matches)) {
            $fileName = $matches[1];
        } elseif (preg_match("/Uploaded attachment: (.*)/", $description, $matches)) {
            $fileName = $matches[1];
        }

        if ($fileName) {
            // Query the tbl_crm_attachments table for this file
            $query = \Illuminate\Support\Facades\DB::table('tbl_crm_attachments')
                ->where('file_name', $fileName)
                ->where('status', 0);

            if ($dealId) {
                $query->where(function($q) use ($dealId, $leadId) {
                    $q->where(function($sq) use ($dealId) {
                        $sq->where('reference_type', 'deal')->where('reference_id', $dealId);
                    });
                    if ($leadId) {
                        $q->orWhere(function($sq) use ($leadId) {
                            $sq->where('reference_type', 'lead')->where('reference_id', $leadId);
                        });
                    }
                });
            } elseif ($leadId) {
                $query->where('reference_type', 'lead')->where('reference_id', $leadId);
            }

            $attachment = $query->orderBy('id', 'desc')->first();
            if ($attachment) {
                  $filePath = trim($attachment->file_path, '/');

                    // Already contains assets/uploads
                    if (preg_match('#^assets/uploads/#', $filePath)) {
                        return url($filePath);
                    }

                    // Already starts with deals/ or leads/ etc.
                    if (preg_match('#^(deals|lead|leads|quotation|company|documents)/#', $filePath)) {
                        return config('app.img_path') . '/' . $filePath;
                    }

                    // Only filename stored
                    return config('app.img_path') . '/' . $filePath;
                //return asset($attachment->file_path);
            }
        }

        return null;
    }
}

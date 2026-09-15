<?php

namespace App\Http\Controllers;
use App\Models\CrmNote;
use App\Models\Deal;
use App\Models\EnquiryManageModel;
use App\Services\DealLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Exports\CrmNotesExport;
use Maatwebsite\Excel\Facades\Excel;

class CrmNoteController extends Controller
{
    //
     private DealLogService $dealLogService;

    public function __construct(DealLogService $dealLogService)
    {
        $this->dealLogService = $dealLogService;
    }

      private function logActivity(string $referenceType, int $referenceId, string $action, string $description): void
    {
        if ($referenceType === 'deal') {
            $deal = Deal::find($referenceId);
            if ($deal) {
                $this->dealLogService->log($deal, $action, $description);
            }
        } elseif ($referenceType === 'lead') {
            \Illuminate\Support\Facades\DB::table('enquiry_history')->insert([
                'enquiry_id'  => $referenceId,
                'change_type' => $action,
                'reason'      => $description,
                'table_name'  => 'enquiry_manage',
                'changed_by'  => getCreatedBy(),
                'changed_at'  => now(),
            ]);
        }
    }
     private function parseDateDMY(?string $date): ?string
    {
        if (!$date) return null;
        try {
            return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $ex) {
                return null;
            }
        }
    }

     private function success(string $msg, $data = [])
    {
        return response()->json(['success' => true, 'message' => $msg, 'data' => $data]);
    }

     private function error(string $msg, int $status = 422)
    {
        return response()->json(['success' => false, 'message' => $msg], $status);
    }

     private function exception(\Exception $e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Internal server error',
            'error'   => $e->getMessage(),
        ], 500);
    }

     public function addNote(Request $request)
    {
        Log::info('addNote request received', ['payload' => $request->all()]);
        // Normalize d-m-Y → Y-m-d before validation
        if ($request->has('follow_up_date') && $request->follow_up_date) {
            $parsed = $this->parseDateDMY($request->follow_up_date);
            $request->merge(['follow_up_date' => $parsed ?: null]);
        } else {
            $request->merge(['follow_up_date' => null]);
        }
        if ($request->has('created_at') && $request->created_at) {
            $parsed = $this->parseDateDMY($request->created_at);
            $request->merge(['created_at' => $parsed ?: null]);
        } else {
            $request->merge(['created_at' => null]);
        }

        $validator = Validator::make($request->all(), [
            'reference_type' => 'required|string|in:deal,lead',
            'reference_id'   => 'required|integer',
            'title'          => 'required|string|max:255',
            'note'           => 'required|string',
            'follow_up_date' => 'nullable|date_format:Y-m-d',
            'created_at'     => 'required|date_format:Y-m-d',
            'assigned_to'    => 'nullable|integer|exists:mst_staff,staff_id',
            'attachment_type'=> 'required|string|in:file,link',
            'link'           => 'nullable|required_if:attachment_type,link|url',
            'attachment'     => 'nullable|required_if:attachment_type,file|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png,jpeg,zip,mp4|max:10240',
        ]);

        if ($validator->fails()) {
            Log::warning('addNote validation failed', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $createdAt = $request->created_at;
            if ($createdAt) {
                $createdAt = Carbon::parse($createdAt)->setTimeFrom(now())->toDateTimeString();
            } else {
                $createdAt = now();
            }

            $attachmentName = null;
            if ($request->attachment_type === 'link') {
                $attachmentName = $request->link;
            } else if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/notes'), $attachmentName);
            }

            $note = CrmNote::create([
                'reference_type' => $request->reference_type,
                'reference_id'   => $request->reference_id,
                'title'          => $request->title,
                'note'           => $request->note,
                'status'         => 0,
                'created_by'     => getCreatedBy(),
                'follow_up_date' => $request->follow_up_date ?: null,
                'assigned_to'    => $request->assigned_to ?: null,
                'created_at'     => $createdAt,
                'attachment'     => $attachmentName,
            ]);

            $this->syncLeadFollowUpDate($request->reference_type, $request->reference_id);

            $noteNumber = CrmNote::where('reference_type', $note->reference_type)
            ->where('reference_id', $note->reference_id)
            ->where('id', '<=', $note->id)
            ->count();

                // Build log message detailing note text & attached items
                $logDescription = 'Added note N' . $noteNumber . ': "' . $request->note . '"';
                if ($attachmentName) {
                    if ($request->attachment_type === 'link') {
                        $logDescription .= ' with link attachment: ' . $attachmentName;
                    } else {
                        $logDescription .= ' with file attachment: ' . $file->getClientOriginalName();
                    }
                }
            $this->logActivity(
                $request->reference_type,
                $request->reference_id,
                'note_added',
                $logDescription
            );

            return $this->success('Note added successfully.', $note);
        } catch (\Exception $e) {
            Log::error('addNote exception occurred', ['exception' => $e->getMessage()]);
            return $this->exception($e);
        }
    }

    public function updateNote(Request $request)
    {
        Log::info('updateNote request received', ['payload' => $request->all()]);
        // Normalize d-m-Y → Y-m-d before validation
        if ($request->has('follow_up_date') && $request->follow_up_date) {
            $parsed = $this->parseDateDMY($request->follow_up_date);
            $request->merge(['follow_up_date' => $parsed ?: null]);
        } else {
            $request->merge(['follow_up_date' => null]);
        }
        if ($request->has('created_at') && $request->created_at) {
            $parsed = $this->parseDateDMY($request->created_at);
            $request->merge(['created_at' => $parsed ?: null]);
        } else {
            $request->merge(['created_at' => null]);
        }

        $validator = Validator::make($request->all(), [
            'id'             => 'required|integer|exists:tbl_crm_notes,id',
            'title'          => 'required|string|max:255',
            'note'           => 'required|string',
            'follow_up_date' => 'nullable|date_format:Y-m-d',
            'created_at'     => 'required|date_format:Y-m-d',
            'assigned_to'    => 'nullable|integer|exists:mst_staff,staff_id',
            'attachment_type'=> 'required|string|in:file,link',
            'link'           => 'nullable|required_if:attachment_type,link|url',
            'attachment'     => 'nullable|required_if:attachment_type,file|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png,jpeg,zip,mp4|max:10240',
        ]);

        if ($validator->fails()) {
            Log::warning('updateNote validation failed', ['errors' => $validator->errors()->toArray()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $note = CrmNote::findOrFail($request->id);

            // Detect changes for activity logging
            $changes = [];
            if ($note->title !== $request->title) {
                $changes[] = "title to '{$request->title}'";
            }
            if ($note->note !== $request->note) {
                $changes[] = "description to '{$request->note}'";
            }
            $oldFollowUp = $note->follow_up_date;
            $newFollowUp = $request->follow_up_date ?: null;
            if ($oldFollowUp !== $newFollowUp) {
                $formattedFollowUp = $newFollowUp ? Carbon::parse($newFollowUp)->format('d-m-Y') : 'None';
                $changes[] = "follow-up date to '{$formattedFollowUp}'";
            }
            if ((int)$note->assigned_to !== (int)$request->assigned_to) {
                $assignedStaffName = $request->assigned_to ? (get_createdby_name($request->assigned_to) ?: 'Staff') : 'None';
                $changes[] = "assigned staff to '{$assignedStaffName}'";
            }
            $oldCreatedDate = Carbon::parse($note->created_at)->format('Y-m-d');
            $newCreatedDate = $request->created_at ?: null;
            if ($oldCreatedDate !== $newCreatedDate) {
                $formattedCreated = $newCreatedDate ? Carbon::parse($newCreatedDate)->format('d-m-Y') : 'None';
                $changes[] = "created date to '{$formattedCreated}'";
            }

            $noteNumber = \App\Models\CrmNote::where('reference_type', $note->reference_type)
                ->where('reference_id', $note->reference_id)
                ->where('id', '<=', $note->id)
                ->count();

            $logDescription = 'Updated note N' . $noteNumber . ' details.';
            if (!empty($changes)) {
                $logDescription = 'Updated note N' . $noteNumber . ': ' . implode(', ', $changes);
            }

            $note->title          = $request->title;
            $note->note           = $request->note;
            $note->follow_up_date = $request->follow_up_date ?: null;
            $note->assigned_to    = $request->assigned_to ?: null;

            if ($request->created_at) {
                $originalTime = Carbon::parse($note->created_at);
                $note->created_at = Carbon::parse($request->created_at)->setTimeFrom($originalTime)->toDateTimeString();
            }

            if ($request->attachment_type === 'link') {
                if ($note->attachment !== $request->link) {
                    // Delete old file if exists and it was not a link
                    if ($note->attachment && !filter_var($note->attachment, FILTER_VALIDATE_URL) && file_exists(public_path('assets/uploads/notes/' . $note->attachment))) {
                        unlink(public_path('assets/uploads/notes/' . $note->attachment));
                    }
                    $note->attachment = $request->link;
                    $changes[] = "attachment updated";
                }
            } else if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/notes'), $attachmentName);
                
                // Delete old file if exists and it was not a link
                if ($note->attachment && !filter_var($note->attachment, FILTER_VALIDATE_URL) && file_exists(public_path('assets/uploads/notes/' . $note->attachment))) {
                    unlink(public_path('assets/uploads/notes/' . $note->attachment));
                }
                
                $note->attachment = $attachmentName;
                $changes[] = "attachment updated";
            } else if ($request->attachment_type === 'file' && !$request->hasFile('attachment') && filter_var($note->attachment, FILTER_VALIDATE_URL)) {
                // If it was a link, but changed to file type without new file uploaded, clear it
                $note->attachment = null;
                $changes[] = "attachment removed";
            }

            $note->updated_by = getUpdatedBy();
            $note->save();

            $this->syncLeadFollowUpDate($note->reference_type, $note->reference_id);

            $this->logActivity(
                $note->reference_type,
                $note->reference_id,
                'note_updated',
                $logDescription
            );

            return $this->success('Note updated successfully.', $note);
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }
     public function updateNoteStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_crm_notes,id',
            'is_completed' => 'required|integer|in:1,2',
            'result' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            Log::error('updateNoteStatus Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $note = CrmNote::findOrFail($request->id);
            $note->is_completed = $request->is_completed;
            $note->result = $request->result;
            $note->save();

            $statusText = $request->is_completed == 1 ? 'completed' : 'cancelled';
            $this->logActivity(
                $note->reference_type,
                $note->reference_id,
                "note_{$statusText}",
                "Marked note follow-up as {$statusText}." . ($request->result ? " Result: {$request->result}" : "")
            );

            return $this->success("Follow up {$statusText} successfully.", $note);
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }
    public function getNotes(string $referenceType, int $referenceId)
    {
        try {
            $role   = Session::get('login_type');
            $userId = getCreatedBy();

            $query = CrmNote::where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->where('status', 0);

            if ($role === 'Staff') {
                $query->where(function ($q) use ($userId) {
                    $q->whereNull('assigned_to')
                      ->orWhere('assigned_to', $userId)
                      ->orWhere('created_by', $userId);
                });
            }

            $notes = $query->orderBy('id', 'desc')->get();
            $totalNotes = $notes->count();
            
            $notes = $notes->map(function ($note, $index) use ($totalNotes) {
                    $note->note_number              = $totalNotes - $index;
                    $note->owner_name               = get_createdby_name($note->created_by) ?: 'System';
                    $note->changed_by_name          = $note->owner_name;
                    $note->assigned_name            = $note->assigned_to ? (get_createdby_name($note->assigned_to) ?: 'Unknown') : 'None';
                    $note->formatted_date           = Carbon::parse($note->created_at)->format('d-m-Y h:i:s A');
                    $note->formatted_created_date   = Carbon::parse($note->created_at)->format('d-m-Y');
                    $note->updated_by_name          = $note->updated_by ? (get_createdby_name($note->updated_by) ?: 'Unknown') : '-';
                    $note->formatted_updated_at     = $note->updated_by ? Carbon::parse($note->updated_at)->format('d-m-Y h:i:s A') : '-';
                    $note->formatted_follow_up_date = $note->follow_up_date
                        ? Carbon::parse($note->follow_up_date)->format('d-m-Y')
                        : '-';
                    
                    $note->created_at_seconds = Carbon::parse($note->created_at)->format('d M Y, h:i:s A');
                    $note->updated_at_seconds = $note->updated_at ? Carbon::parse($note->updated_at)->format('d M Y, h:i:s A') : null;
                    $note->is_edited = $note->updated_at && Carbon::parse($note->created_at)->format('Y-m-d H:i:s') !== Carbon::parse($note->updated_at)->format('Y-m-d H:i:s');
                    
                    return $note;
                });

            return response()->json(['success' => true, 'data' => $notes]);
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }
     public function deleteNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_crm_notes,id',
        ]);

        if ($validator->fails()) {
            return $this->error('Invalid note ID.');
        }

        try {
            $note = CrmNote::findOrFail($request->id);
            $note->update(['status' => 1]);

            $this->syncLeadFollowUpDate($note->reference_type, $note->reference_id);

            $noteNumber = \App\Models\CrmNote::where('reference_type', $note->reference_type)
                ->where('reference_id', $note->reference_id)
                ->where('id', '<=', $note->id)
                ->count();

            $this->logActivity(
                $note->reference_type,
                $note->reference_id,
                'note_deleted',
                'Deleted note N' . $noteNumber . ': "' . substr($note->note, 0, 50) . '..."'
            );

            return $this->success('Note deleted successfully.');
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }

      private function syncLeadFollowUpDate(string $referenceType, int $referenceId)
    {
        $leadId = null;
        if ($referenceType === 'deal') {
            $deal = Deal::find($referenceId);
            if ($deal && $deal->lead) {
                $leadId = $deal->lead->enquiry_id;
            }
        } elseif ($referenceType === 'lead') {
            $leadId = $referenceId;
        }

        if ($leadId) {
            $lead = EnquiryManageModel::find($leadId);
            if ($lead) {
                $minDateForLead = CrmNote::where('reference_type', 'lead')
                    ->where('reference_id', $leadId)
                    ->where('status', 0)
                    ->whereNotNull('follow_up_date')
                    ->min('follow_up_date');

                $dealIds = Deal::where('lead_id', $leadId)->pluck('deal_id');
                $minDateForDeals = null;
                if ($dealIds->isNotEmpty()) {
                    $minDateForDeals = CrmNote::where('reference_type', 'deal')
                        ->whereIn('reference_id', $dealIds)
                        ->where('status', 0)
                        ->whereNotNull('follow_up_date')
                        ->min('follow_up_date');
                }

                $dates = array_filter([$minDateForLead, $minDateForDeals]);
                $nextDate = !empty($dates) ? min($dates) : null;

                $lead->update(['next_follow_up_date' => $nextDate]);
            }
        }
    }

    public function exportPdf(string $referenceType, int $referenceId)
    {
        $role   = Session::get('login_type');
        $userId = getCreatedBy();

        $query = CrmNote::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('status', 0);

        if ($role === 'Staff') {
            $query->where(function ($q) use ($userId) {
                $q->whereNull('assigned_to')
                  ->orWhere('assigned_to', $userId)
                  ->orWhere('created_by', $userId);
            });
        }

        $notes = $query->orderBy('created_at', 'asc')->get();

        $entityName = '';
        $entity = null;
        if ($referenceType === 'lead') {
            $entity = EnquiryManageModel::with('assignedStaff')->find($referenceId);
            $entityName = $entity ? $entity->enquiry_name ?? $entity->lead_name : '';
        } elseif ($referenceType === 'deal') {
            $entity = Deal::with('assignedStaff')->find($referenceId);
            $entityName = $entity ? $entity->deal_name : '';
        }

        $filename = 'Notes_Report_' . ucfirst($referenceType) . '_' . date('Ymd_His') . '.pdf';
        
        $pdf = Pdf::loadView('company.crm.notes_export', [
            'notes' => $notes,
            'referenceType' => $referenceType,
            'entityName' => $entityName,
            'entity' => $entity,
            'is_pdf' => true,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }

    // public function exportExcel(string $referenceType, int $referenceId)
    // {
    //     $filename = 'Notes_Report_' . ucfirst($referenceType) . '_' . date('Ymd_His') . '.xlsx';
    //     return \Maatwebsite\Excel\Facades\Excel::download(new class($referenceType, $referenceId) implements \Maatwebsite\Excel\Concerns\FromView, \Maatwebsite\Excel\Concerns\ShouldAutoSize {
    //         protected $referenceType;
    //         protected $referenceId;

    //         public function __construct($referenceType, $referenceId)
    //         {
    //             $this->referenceType = $referenceType;
    //             $this->referenceId = $referenceId;
    //         }

    //         public function view(): \Illuminate\Contracts\View\View
    //         {
    //             $role   = Session::get('login_type');
    //             $userId = getCreatedBy();

    //             $query = CrmNote::where('reference_type', $this->referenceType)
    //                 ->where('reference_id', $this->referenceId)
    //                 ->where('status', 0);

    //             if ($role === 'Staff') {
    //                 $query->where(function ($q) use ($userId) {
    //                     $q->whereNull('assigned_to')
    //                       ->orWhere('assigned_to', $userId)
    //                       ->orWhere('created_by', $userId);
    //                 });
    //             }

    //             $notes = $query->orderBy('created_at', 'asc')->get();

    //             $entityName = '';
    //             if ($this->referenceType === 'lead') {
    //                 $entity = EnquiryManageModel::find($this->referenceId);
    //                 $entityName = $entity ? $entity->lead_name : '';
    //             } elseif ($this->referenceType === 'deal') {
    //                 $entity = Deal::find($this->referenceId);
    //                 $entityName = $entity ? $entity->deal_name : '';
    //             }

    //             return view('company.crm.notes_export', [
    //                 'notes' => $notes,
    //                 'referenceType' => $this->referenceType,
    //                 'entityName' => $entityName,
    //                 'is_pdf' => false,
    //             ]);
    //         }
    //     }, $filename);
    // }
    public function exportExcel(string $referenceType, int $referenceId)
{
    $filename = 'Notes_Report_' . ucfirst($referenceType) . '_' . date('Ymd_His') . '.xlsx';

    return Excel::download(
        new CrmNotesExport($referenceType, $referenceId),
        $filename
    );
}
}

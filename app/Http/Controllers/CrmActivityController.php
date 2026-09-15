<?php

namespace App\Http\Controllers;

use App\Models\CrmCall;
use App\Models\CrmNote;
use App\Models\CrmAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CrmActivityController extends Controller
{
    // ----------------- CALLS -----------------
    public function getCalls($leadId)
    {
        try {
            $calls = CrmCall::with(['assignedStaff'])
                ->where('reference_type', 'lead')
                ->where('reference_id', $leadId)
                ->where('status', 0)
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($call) {
                    $formattedTime = Carbon::parse($call->call_date)->format('d-m-Y');
                    if ($call->call_time) {
                        $dateObj = Carbon::parse($call->call_date . ' ' . $call->call_time);
                        $formattedTime = $dateObj->format('d-m-Y H:i');
                    }
                    
                    $isEdited = $call->updated_at && $call->created_at->format('Y-m-d H:i:s') !== $call->updated_at->format('Y-m-d H:i:s');
                    return [
                        'id' => $call->id,
                        'subject' => $call->subject,
                        'call_date' => $call->call_date ? Carbon::parse($call->call_date)->format('d-m-Y') : '',
                        'call_time' => $call->call_time,
                        'call_status' => $call->call_status, // 0=Pending, 1=Completed, 2=Cancelled
                        'formatted_time' => $formattedTime,
                        'call_agenda' => $call->call_agenda,
                        'call_result' => $call->call_result,
                        'assigned_to' => $call->assigned_to,
                        'assigned_name' => $call->assignedStaff ? $call->assignedStaff->staff_name : 'N/A',
                        'changed_by_name' => get_createdby_name($call->created_by) ?: 'System',
                        'updated_by_name' => $call->updated_by ? (get_createdby_name($call->updated_by) ?: 'N/A') : 'N/A',
                        'created_at_seconds' => $call->created_at->format('d M Y, h:i:s A'),
                        'updated_at_seconds' => $call->updated_at ? $call->updated_at->format('d M Y, h:i:s A') : null,
                        'is_edited' => $isEdited,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $calls
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function scheduleCall(Request $request)
    {
        try {
            $request->validate([
                'reference_id' => 'required',
                'subject' => 'required|string',
                'call_date' => 'required',
                'call_time' => 'nullable',
                'assigned_to' => 'nullable',
                'call_purpose' => 'nullable|string',
                'call_agenda' => 'nullable|string',
            ]);

            $callDate = Carbon::parse($request->call_date)->format('Y-m-d');

            $call = CrmCall::create([
                'reference_type' => 'lead',
                'reference_id' => $request->reference_id,
                'subject' => $request->subject,
                'call_date' => $callDate,
                'call_time' => $request->call_time,
                'assigned_to' => $request->assigned_to,
                'call_purpose' => $request->call_purpose,
                'call_agenda' => $request->call_agenda,
                'call_status' => 0, // Pending
                'status' => 0,
                'created_by' => getCreatedBy(),
            ]);

            $this->logActivity($request->reference_id, 'Call Scheduled', "Scheduled call: '{$request->subject}' on {$request->call_date}");

            return response()->json([
                'success' => true,
                'message' => 'Call scheduled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateCall(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'subject' => 'required|string',
                'call_date' => 'required',
                'call_time' => 'nullable',
                'assigned_to' => 'nullable',
                'call_purpose' => 'nullable|string',
                'call_agenda' => 'nullable|string',
            ]);

            $call = CrmCall::findOrFail($request->id);
            $callDate = Carbon::parse($request->call_date)->format('Y-m-d');

            // Detect changes
            $changes = [];
            if ($call->subject !== $request->subject) $changes[] = "subject to '{$request->subject}'";
            if ($call->call_date !== $callDate) {
                $changes[] = "date to '" . Carbon::parse($request->call_date)->format('d-m-Y') . "'";
            }
            if ($call->call_time !== $request->call_time) $changes[] = "time to '{$request->call_time}'";

            $call->update([
                'subject' => $request->subject,
                'call_date' => $callDate,
                'call_time' => $request->call_time,
                'assigned_to' => $request->assigned_to,
                'call_purpose' => $request->call_purpose,
                'call_agenda' => $request->call_agenda,
                'updated_by' => getUpdatedBy(),
            ]);

            $logDesc = empty($changes) ? 'Updated scheduled call details.' : 'Updated scheduled call: ' . implode(', ', $changes);
            $this->logActivity($call->reference_id, 'Call Updated', $logDesc);

            return response()->json([
                'success' => true,
                'message' => 'Call updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateCallStatus(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'call_status' => 'required|in:1,2', // 1=Completed, 2=Cancelled
                'call_result' => 'required|string',
            ]);

            $call = CrmCall::findOrFail($request->id);
            $call->update([
                'call_status' => $request->call_status,
                'call_result' => $request->call_result,
                'updated_by' => getUpdatedBy(),
            ]);

            $statusText = $request->call_status == 1 ? 'completed' : 'cancelled';
            $this->logActivity($call->reference_id, 'Call Status Updated', "Call status updated to {$statusText}. Result: {$request->call_result}");

            return response()->json([
                'success' => true,
                'message' => 'Call status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteCall(Request $request)
    {
        try {
            $call = CrmCall::findOrFail($request->id);
            $call->update([
                'status' => 1,
                'updated_by' => getUpdatedBy(),
            ]);

            $this->logActivity($call->reference_id, 'Call Deleted', "Deleted scheduled call: '{$call->subject}'");

            return response()->json([
                'success' => true,
                'message' => 'Call deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ----------------- NOTES -----------------
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

    public function getNotes($leadId)
    {
        try {
            $role   = Session::get('login_type');
            $userId = getCreatedBy();

            $query = CrmNote::with(['assignedStaff', 'createdBy', 'updatedBy'])
                ->where('reference_type', 'lead')
                ->where('reference_id', $leadId)
                ->where('status', 0);

            if ($role === 'Staff') {
                $query->where(function ($q) use ($userId) {
                    $q->whereNull('assigned_to')
                      ->orWhere('assigned_to', $userId)
                      ->orWhere('created_by', $userId);
                });
            }

            $notes = $query->orderBy('id', 'desc')
                ->get()
                ->map(function ($note) {
                    $isEdited = $note->updated_at && $note->created_at->format('Y-m-d H:i:s') !== $note->updated_at->format('Y-m-d H:i:s');
                    return [
                        'id' => $note->id,
                        'title' => $note->title,
                        'note' => $note->note,
                        'follow_up_date' => $note->follow_up_date,
                        'formatted_follow_up_date' => $note->follow_up_date ? Carbon::parse($note->follow_up_date)->format('d-m-Y') : 'N/A',
                        'assigned_to' => $note->assigned_to,
                        'assigned_name' => $note->assignedStaff ? $note->assignedStaff->staff_name : 'N/A',
                        'is_completed' => $note->is_completed, // 0=Pending, 1=Completed, 2=Cancelled
                        'result' => $note->result,
                        'changed_by_name' => $note->createdBy ? $note->createdBy->staff_name : 'System',
                        'updated_by_name' => $note->updatedBy ? $note->updatedBy->staff_name : 'N/A',
                        'formatted_date' => $note->created_at->format('d/m/Y (h:i A)'),
                        'formatted_created_date' => $note->created_at->format('d-m-Y'),
                        'formatted_updated_at' => $note->updated_by ? Carbon::parse($note->updated_at)->format('d-m-Y h:i:s A') : 'N/A',
                        'created_at_seconds' => $note->created_at->format('d M Y, h:i:s A'),
                        'updated_at_seconds' => $note->updated_at ? $note->updated_at->format('d M Y, h:i:s A') : null,
                        'is_edited' => $isEdited,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $notes
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function addNote(Request $request)
    {
        try {
            $request->validate([
                'reference_id' => 'required',
                'title' => 'required|string|max:255',
                'note' => 'required|string',
                'follow_up_date' => 'nullable',
                'assigned_to' => 'nullable',
                'created_at' => 'required',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png,jpeg,zip,mp4|max:10240',
                'link' => 'nullable|url',
            ]);

            // Normalize d-m-Y to Y-m-d
            $followUp = null;
            if ($request->filled('follow_up_date')) {
                $parsed = $this->parseDateDMY($request->follow_up_date);
                $followUp = $parsed ?: null;
            }

            $createdAt = null;
            if ($request->filled('created_at')) {
                $parsed = $this->parseDateDMY($request->created_at);
                if ($parsed) {
                    $createdAt = Carbon::parse($parsed)->setTimeFrom(now())->toDateTimeString();
                }
            }
            if (!$createdAt) {
                $createdAt = now();
            }

            $attachmentName = null;
            $attachmentLogInfo = '';
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/notes'), $attachmentName);
                $attachmentLogInfo = " with file attachment '{$file->getClientOriginalName()}'";
            } elseif ($request->filled('link')) {
                $attachmentName = $request->link;
                $attachmentLogInfo = " with link '{$request->link}'";
            }

            $note = CrmNote::create([
                'reference_type' => 'lead',
                'reference_id' => $request->reference_id,
                'title' => $request->title,
                'note' => $request->note,
                'follow_up_date' => $followUp,
                'assigned_to' => $request->assigned_to ?: null,
                'is_completed' => 0, // Pending
                'status' => 0,
                'created_by' => getCreatedBy(),
                'created_at' => $createdAt,
                'attachment' => $attachmentName,
            ]);

            // Update next_follow_up_date on the lead
            $this->syncLeadFollowUpDate('lead', $request->reference_id);

            $noteNumber = CrmNote::where('reference_type', $note->reference_type)
            ->where('reference_id', $note->reference_id)
            ->where('id', '<=', $note->id)
            ->count();

        // Build log message
        $logDescription = 'Added note N' . $noteNumber . ': "' . $request->note . '"' . $attachmentLogInfo;
           $this->logActivity($request->reference_id, 'Note Added', $logDescription);

            return response()->json([
                'success' => true,
                'message' => 'Note added successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateNote(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'title' => 'required|string|max:255',
                'note' => 'required|string',
                'follow_up_date' => 'nullable',
                'assigned_to' => 'nullable',
                'created_at' => 'required',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,png,jpeg,zip,mp4|max:10240',
                'link' => 'nullable|url',
            ]);

            $note = CrmNote::findOrFail($request->id);

            // Normalize d-m-Y to Y-m-d
            $followUp = null;
            if ($request->filled('follow_up_date')) {
                $parsed = $this->parseDateDMY($request->follow_up_date);
                $followUp = $parsed ?: null;
            }

            $newCreatedDate = null;
            if ($request->filled('created_at')) {
                $newCreatedDate = $this->parseDateDMY($request->created_at);
            }

            // Detect changes for activity logging
            $changes = [];
            if ($note->title !== $request->title) {
                $changes[] = "title to '{$request->title}'";
            }
            if ($note->note !== $request->note) {
                $changes[] = "description to '{$request->note}'";
            }
            $oldFollowUp = $note->follow_up_date;
            if ($oldFollowUp !== $followUp) {
                $formattedFollowUp = $followUp ? Carbon::parse($followUp)->format('d-m-Y') : 'None';
                $changes[] = "follow-up date to '{$formattedFollowUp}'";
            }
            if ((int)$note->assigned_to !== (int)$request->assigned_to) {
                $assignedStaffName = $request->assigned_to ? (get_createdby_name($request->assigned_to) ?: 'Staff') : 'None';
                $changes[] = "assigned staff to '{$assignedStaffName}'";
            }
            $oldCreatedDate = Carbon::parse($note->created_at)->format('Y-m-d');
            if ($newCreatedDate && $oldCreatedDate !== $newCreatedDate) {
                $formattedCreated = Carbon::parse($newCreatedDate)->format('d-m-Y');
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

            if ($newCreatedDate) {
                $originalTime = Carbon::parse($note->created_at);
                $note->created_at = Carbon::parse($newCreatedDate)->setTimeFrom($originalTime)->toDateTimeString();
            }

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/notes'), $attachmentName);
                
                // Delete old file if exists and not a link
                if ($note->attachment && !str_starts_with($note->attachment, 'http') && file_exists(public_path('assets/uploads/notes/' . $note->attachment))) {
                    unlink(public_path('assets/uploads/notes/' . $note->attachment));
                }
                
                $note->attachment = $attachmentName;
                $changes[] = "attachment updated to file";
            } elseif ($request->filled('link')) {
                if ($note->attachment !== $request->link) {
                    $note->attachment = $request->link;
                    $changes[] = "attachment updated to link";
                }
            }

            $note->title          = $request->title;
            $note->note           = $request->note;
            $note->follow_up_date = $followUp;
            $note->assigned_to    = $request->assigned_to ?: null;
            $note->updated_by     = getUpdatedBy();
            $note->save();

            // Update next_follow_up_date on the lead
            $this->syncLeadFollowUpDate($note->reference_type, $note->reference_id);

            $this->logActivity($note->reference_id, 'Note Updated', $logDescription);

            return response()->json([
                'success' => true,
                'message' => 'Note updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function syncLeadFollowUpDate(string $referenceType, int $referenceId)
    {
        $leadId = null;
        if ($referenceType === 'deal') {
            $deal = \App\Models\Deal::find($referenceId);
            if ($deal && $deal->lead) {
                $leadId = $deal->lead->enquiry_id;
            }
        } elseif ($referenceType === 'lead') {
            $leadId = $referenceId;
        }

        if ($leadId) {
            $lead = \App\Models\EnquiryManageModel::find($leadId);
            if ($lead) {
                $minDateForLead = CrmNote::where('reference_type', 'lead')
                    ->where('reference_id', $leadId)
                    ->where('status', 0)
                    ->whereNotNull('follow_up_date')
                    ->min('follow_up_date');

                $dealIds = \App\Models\Deal::where('lead_id', $leadId)->pluck('deal_id');
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

    public function updateNoteStatus(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'is_completed' => 'required|in:1,2', // 1=Completed, 2=Cancelled
                'result' => 'nullable|string',
            ]);

            $note = CrmNote::findOrFail($request->id);
            $note->update([
                'is_completed' => $request->is_completed,
                'result' => $request->result,
                'updated_by' => getUpdatedBy(),
            ]);

            $statusText = $request->is_completed == 1 ? 'completed' : 'cancelled';
            $this->logActivity($note->reference_id, 'Note Status Updated', "Note follow-up marked as {$statusText}. Result: {$request->result}");

            return response()->json([
                'success' => true,
                'message' => 'Note status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteNote(Request $request)
    {
        try {
            $note = CrmNote::findOrFail($request->id);
            $note->update([
                'status' => 1,
                'updated_by' => getUpdatedBy(),
            ]);

            $noteNumber = \App\Models\CrmNote::where('reference_type', $note->reference_type)
                ->where('reference_id', $note->reference_id)
                ->where('id', '<=', $note->id)
                ->count();

            $this->logActivity($note->reference_id, 'Note Deleted', "Deleted note N{$noteNumber}: '" . substr($note->note, 0, 50) . "...'");

            return response()->json([
                'success' => true,
                'message' => 'Note deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ----------------- ATTACHMENTS -----------------
    public function getAttachments($leadId)
    {
        $imgPath = config('app.img_path');
        if (empty($imgPath)) {
            $imgPath = asset('assets/uploads/');
        }
        $img_view_path = rtrim($imgPath, '/') . '/';
        try {
            $attachments = CrmAttachment::where('reference_type', 'lead')
                ->where('reference_id', $leadId)
                ->where('status', 0)
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($att) use ($img_view_path) {
                    $att->file_path = $img_view_path . $att->file_path;
                    $att->owner_name = get_createdby_name($att->created_by) ?: 'System';
                    $att->formatted_date = \Carbon\Carbon::parse($att->created_at)->format('d M Y h:i A');
                    return $att;
                });

            return response()->json([
                'success' => true,
                'data' => $attachments
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function uploadAttachment(Request $request)
    {
        try {
           $validator = Validator::make($request->all(), [
                'reference_type'  => 'required|string|in:lead',
                'reference_id'    => 'required|integer',
                'attachment_type' => 'required|string|in:file,link',
                'link'            => 'nullable|required_if:attachment_type,link|url',
                'files'           => 'nullable|required_if:attachment_type,file|array',
                'files.*'         => 'nullable|file|max:10240',
            ], [
                'files.*.max' => 'Each file must not exceed 10MB.',
                'link.url'    => 'Please enter a valid URL.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422);
            }

             if ($request->attachment_type == 'link') {

            CrmAttachment::create([
                'reference_type' => 'lead',
                'reference_id'   => $request->reference_id,
                'file_name'      => $request->link,
                'file_path'      => $request->link,
                'status'         => 0,
                'created_by'     => getCreatedBy(),
            ]);

            $this->logActivity(
                $request->reference_id,
                'Attachment Uploaded',
                "Added link attachment: {$request->link}"
            );

            return response()->json([
                'success' => true,
                'message' => 'Link attached successfully.'
            ]);
        }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = $file->getClientOriginalName();
                    $fileNameToSave = time() . '_' . uniqid() . '_' . $fileName;

                    // Store in public/assets/uploads folder
                    $file->move(public_path('assets/uploads'), $fileNameToSave);

                    CrmAttachment::create([
                        'reference_type' => 'lead',
                        'reference_id' => $request->reference_id,
                        'file_name' => $fileName,
                        'file_path' => $fileNameToSave, // store filename only
                        'status' => 0,
                        'created_by' => getCreatedBy(),
                    ]);

                    $this->logActivity($request->reference_id, 'Attachment Uploaded', "Uploaded file: '{$fileName}'");
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Attachment(s) uploaded successfully'
                ]);
            }

            return response()->json(['success' => false, 'message' => 'No files uploaded'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteAttachment(Request $request)
    {
        try {
            $att = CrmAttachment::findOrFail($request->id);
            $att->update([
                'status' => 1,
                'updated_by' => getUpdatedBy(),
            ]);

            $this->logActivity($att->reference_id, 'Attachment Deleted', "Deleted file: '{$att->file_name}'");

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Helper method to log activity to lead logs
    private function logActivity(int $enquiryId, string $changeType, string $reason): void
    {
        try {
            DB::table('enquiry_history')->insert([
                'enquiry_id'  => $enquiryId,
                'change_type' => $changeType,
                'reason'      => $reason,
                'table_name'  => 'enquiry_manage',
                'changed_by'  => getCreatedBy(),
                'changed_at'  => now(),
            ]);
        } catch (\Exception $e) {
            // fail-silent
        }
    }
}

<?php

namespace App\Http\Controllers;
use App\Models\CrmCall;
use App\Models\Deal;
use App\Models\EnquiryManageModel;
use App\Services\DealLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
class CrmCallController extends Controller
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

    public function scheduleCall(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_type' => 'required|string|in:deal,lead',
            'reference_id'   => 'required|integer',
            'assigned_to'    => 'nullable|integer|exists:mst_staff,staff_id',
            'call_date'      => 'required|date',
            'call_time'      => 'nullable|string',
            'subject'        => 'required|string|max:255',
            'call_agenda'    => 'nullable|string',
            'call_status'    => 'nullable|integer|in:0,1,2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Resolve lead_name from parent reference
            $leadName = '';
            if ($request->reference_type === 'deal') {
                $deal     = Deal::with('lead')->find($request->reference_id);
                $leadName = $deal && $deal->lead ? $deal->lead->contact_person_name : '';
            } elseif ($request->reference_type === 'lead') {
                $lead     = EnquiryManageModel::find($request->reference_id);
                $leadName = $lead->contact_person_name ?? '';
            }

            $call = CrmCall::create([
                'reference_type' => $request->reference_type,
                'reference_id'   => $request->reference_id,
                'lead_name'      => $leadName,
                'assigned_to'    => $request->assigned_to ?: null,
                'call_date'      => $request->call_date,
                'call_time'      => $request->call_time,
                'subject'        => $request->subject,
                'call_agenda'    => $request->call_agenda ?: null,
                'call_status'    => $request->call_status ?? 0,
                'status'         => 0,
                'created_by'     => getCreatedBy(),
            ]);

            $formattedCallDate = $request->call_date ? Carbon::parse($request->call_date)->format('d-m-Y') : $request->call_date;
            $this->logActivity(
                $request->reference_type,
                $request->reference_id,
                'call_scheduled',
                "Call scheduled: Subject: '{$request->subject}', Date: {$formattedCallDate} {$request->call_time}"
            );

            return $this->success('Call scheduled successfully.', $call);
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }

    public function updateCall(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'             => 'required|integer',
            'assigned_to'    => 'nullable|integer|exists:mst_staff,staff_id',
            'call_date'      => 'required|date',
            'call_time'      => 'nullable|string',
            'subject'        => 'required|string|max:255',
            'call_agenda'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
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
                'assigned_to' => $request->assigned_to ?: null,
                'call_date'   => $request->call_date,
                'call_time'   => $request->call_time,
                'subject'     => $request->subject,
                'call_agenda' => $request->call_agenda ?: null,
                'updated_by'  => getUpdatedBy(),
            ]);

            $logDesc = empty($changes) ? 'Updated scheduled call details.' : 'Updated scheduled call: ' . implode(', ', $changes);
            $this->logActivity($call->reference_type, $call->reference_id, 'call_updated', $logDesc);

            return $this->success('Call updated successfully.', $call);
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }

     public function getCalls(string $referenceType, int $referenceId)
    {
        try {
            $userId = getCreatedBy();
            $role   = Session::get('login_type');

            $query = CrmCall::with('assignedStaff')
                ->where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->where('status', 0);

            if ($role === 'Staff' || $role === 'Manager') {
                $query->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)
                      ->orWhere('created_by', $userId);
                });
            }

            $calls = $query->orderBy('id', 'desc')
                ->get()
                ->map(function ($call) {
                    $formattedTime = Carbon::parse($call->call_date)->format('d-m-Y');
                    if ($call->call_time) {
                        $dateObj = Carbon::parse($call->call_date . ' ' . $call->call_time);
                        $formattedTime = $dateObj->format('d-m-Y H:i');
                    }
                    
                    $isEdited = $call->updated_at && $call->created_at->format('Y-m-d H:i:s') !== $call->updated_at->format('Y-m-d H:i:s');
                    $call->call_owner_name = get_createdby_name($call->created_by) ?: 'System';
                    $call->updated_by_name = $call->updated_by ? (get_createdby_name($call->updated_by) ?: 'N/A') : 'N/A';
                    $call->formatted_time  = $formattedTime;
                    $call->created_at_seconds = $call->created_at->format('d M Y, h:i:s A');
                    $call->updated_at_seconds = $call->updated_at ? $call->updated_at->format('d M Y, h:i:s A') : null;
                    $call->is_edited = $isEdited;
                    return $call;
                });

            return response()->json(['success' => true, 'data' => $calls]);
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }

     public function getDashboardCalls()
    {
        try {
            $userId = getCreatedBy();
            $role   = Session::get('login_type');

            $query = CrmCall::with(['assignedStaff', 'deal.lead', 'lead'])
                ->where('status', 0)
                ->where('call_status', 0);

            if ($role === 'Staff' || $role === 'Manager') {
                $query->where(function ($q) use ($userId) {
                    $q->where('assigned_to', $userId)
                      ->orWhere('created_by', $userId);
                });
            }

            $calls = $query->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($call) {
                    $call->call_owner_name = get_createdby_name($call->created_by) ?: 'System';
                    $call->created_by_name = $call->call_owner_name;
                    $call->assigned_staff_name = $call->assignedStaff ? $call->assignedStaff->staff_name : 'Unassigned';
                    $dateObj = Carbon::parse($call->call_date . ' ' . $call->call_time);
                    $call->formatted_date_time = $dateObj->format('d/m/Y (h:i A)');

                    if ($call->reference_type === 'deal') {
                        $deal = $call->deal;
                        if ($deal && $deal->lead) {
                            $call->reference_encrypted_id = encrypt($deal->deal_id);
                            $call->reference_url = url('deal/details/' . $call->reference_encrypted_id);
                            $name = trim($deal->lead->company_name ?? $deal->lead->contact_person_name ?? '');
                            $call->reference_module = "Deal: " . ($deal->lead->enquiry_no ?? '');
                            if ($name !== '') {
                                $call->reference_module .= " ($name)";
                            }
                        } else {
                            $call->reference_module = 'N/A';
                            $call->reference_url = '#';
                        }
                    } elseif ($call->reference_type === 'lead') {
                        $lead = $call->lead;
                        if ($lead) {
                            $call->reference_encrypted_id = encrypt($lead->enquiry_id);
                            $call->reference_url = url('lead/details/' . $call->reference_encrypted_id);
                            $name = trim($lead->company_name ?? $lead->contact_person_name ?? '');
                            $call->reference_module = "Lead: " . ($lead->enquiry_no ?? '');
                            if ($name !== '') {
                                $call->reference_module .= " ($name)";
                            }
                        } else {
                            $call->reference_module = 'N/A';
                            $call->reference_url = '#';
                        }
                    } else {
                        $call->reference_module = 'N/A';
                        $call->reference_url = '#';
                    }

                    return $call;
                });

            return response()->json(['success' => true, 'data' => $calls]);
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }

     public function updateCallStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'          => 'required|integer|exists:tbl_crm_calls,id',
            'call_status' => 'required|integer|in:0,1,2',
            'call_result' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $call              = CrmCall::findOrFail($request->id);
            $call->call_status = $request->call_status;
            $call->call_result = $request->call_result;
            $call->save();

            $statusTexts = ['Pending', 'Completed', 'Rejected'];
            $statusText  = $statusTexts[$request->call_status] ?? 'Unknown';

            $this->logActivity(
                $call->reference_type,
                $call->reference_id,
                'call_status_updated',
                "Updated scheduled call status to: \"{$statusText}\" (Subject: \"{$call->subject}\", Result: \"{$call->call_result}\")"
            );

            return $this->success('Call status and result updated successfully.', $call);
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }

    public function deleteCall(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_crm_calls,id',
        ]);

        if ($validator->fails()) {
            return $this->error('Invalid call ID.');
        }

        try {
            $call = CrmCall::findOrFail($request->id);
            $call->update(['status' => 1]);

            $this->logActivity(
                $call->reference_type,
                $call->reference_id,
                'call_deleted',
                "Deleted scheduled call: Subject: '{$call->subject}'"
            );

            return $this->success('Call deleted successfully.');
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }
}

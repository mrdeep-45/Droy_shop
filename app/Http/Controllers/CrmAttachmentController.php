<?php

namespace App\Http\Controllers;
use App\Models\CrmAttachment;
use App\Models\Deal;
use App\Services\DealLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CrmAttachmentController extends Controller
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
    public function uploadAttachment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference_type'  => 'required|string|in:deal,lead',
            'reference_id'    => 'required|integer',
            'attachment_type' => 'nullable|string|in:file,link',
            'link'            => 'nullable|required_if:attachment_type,link|url',
            'files'           => 'nullable|required_if:attachment_type,file|array',
            'files.*'         => 'nullable|file|max:10240', // 10MB max per file
        ], [
            'files.*.max' => 'Each file must not exceed 10MB in size.',
            'link.url'    => 'Please provide a valid URL.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $uploadedCount = 0;
            $attachments   = [];

            if ($request->attachment_type === 'link' || $request->filled('link')) {
                $attachment = CrmAttachment::create([
                    'reference_type' => $request->reference_type,
                    'reference_id'   => $request->reference_id,
                    'file_name'      => $request->link,
                    'file_path'      => $request->link,
                    'status'         => 0,
                    'created_by'     => getCreatedBy(),
                ]);

                $this->logActivity(
                    $request->reference_type,
                    $request->reference_id,
                    'attachment_uploaded',
                    "Added link attachment: " . $request->link
                );

                return $this->success('Link attached successfully.', [$attachment]);
            }

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $filename  = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/deals'), $filename);
                    $filepath  = 'deals/' . $filename;

                    $attachment = CrmAttachment::create([
                        'reference_type' => $request->reference_type,
                        'reference_id'   => $request->reference_id,
                        'file_name'      => $file->getClientOriginalName(),
                        'file_path'      => $filepath,
                        'status'         => 0,
                        'created_by'     => getCreatedBy(),
                    ]);

                    $this->logActivity(
                        $request->reference_type,
                        $request->reference_id,
                        'attachment_uploaded',
                        "Uploaded attachment: " . $file->getClientOriginalName()
                    );

                    $attachments[]  = $attachment;
                    $uploadedCount++;
                }

                return $this->success($uploadedCount . ' file(s) uploaded successfully.', $attachments);
            }

            return $this->error('No files were uploaded.');
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }
    public function getAttachments(string $referenceType, int $referenceId)
    {
        $imgPath = config('app.img_path');
        if (empty($imgPath)) {
            $imgPath = asset('assets/uploads/');
        }
        $img_view_path = rtrim($imgPath, '/') . '/';

        try {
            $attachments = CrmAttachment::where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->where('status', 0)
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($att) use ($referenceType, $img_view_path) {
                    $att->owner_name     = get_createdby_name($att->created_by) ?: 'System';
                    $att->formatted_date = Carbon::parse($att->created_at)->format('d M Y h:i A');
                    
                    if (str_starts_with($att->file_path, 'http://') || str_starts_with($att->file_path, 'https://')) {
                        // Keep the link as is
                    } else {
                        $cleanPath = str_replace('assets/uploads/', '', $att->file_path);
                        $att->file_path = $img_view_path . $cleanPath;
                    }
                    return $att;
                });

            return response()->json(['success' => true, 'data' => $attachments], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }
     public function deleteAttachment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tbl_crm_attachments,id',
        ]);

        if ($validator->fails()) {
            return $this->error('Invalid attachment ID.');
        }

        try {
            $att = CrmAttachment::findOrFail($request->id);

            // Delete physical file from disk
            if ($att->file_path) {
                $fullPath = public_path($att->file_path);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }

            $att->update(['status' => 1]);

            $this->logActivity(
                $att->reference_type,
                $att->reference_id,
                'attachment_deleted',
                "Deleted attachment: " . $att->file_name
            );

            return $this->success('Attachment deleted successfully.');
        } catch (\Exception $e) {
            return $this->exception($e);
        }
    }
}

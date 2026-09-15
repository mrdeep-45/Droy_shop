<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
class EmailComposeController extends Controller
{
    //
    /**
     * Send composed email via SMTP configuration
     */
    public function send(Request $request)
    {
        // 1. Load email configuration dynamically
        if (function_exists('get_email_config')) {
            get_email_config();
        }
        // 2. Validate input fields
        $request->validate([
            'to' => ['required', 'string'],
            'cc' => ['nullable', 'string'],
            'bcc' => ['nullable', 'string'],
            'subject' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'attachments.*' => ['nullable', 'file', 'max:10240'], // 10MB limit per file
        ]);
        $toRaw = $request->input('to', '');
        $ccRaw = $request->input('cc', '');
        $bccRaw = $request->input('bcc', '');
        $subject = $request->input('subject', '(No Subject)');
        $body = $request->input('body', '');
        // 3. Parse and validate recipient emails
        $toArr = array_values(array_filter(array_map('trim', explode(',', $toRaw)), function ($email) {
            return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        }));
        if (empty($toArr)) {
            return response()->json([
                'status' => false,
                'message' => 'Please specify at least one valid email address in the "To" field.'
            ], 422);
        }
        $ccArr = array_values(array_filter(array_map('trim', explode(',', $ccRaw)), function ($email) {
            return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        }));
        $bccArr = array_values(array_filter(array_map('trim', explode(',', $bccRaw)), function ($email) {
            return !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
        }));
        // 4. Handle attachment file uploads
        $tempAttachmentFiles = [];
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            $uploadDir = public_path('assets/uploads/compose_attachments');
            if (!File::exists($uploadDir)) {
                File::makeDirectory($uploadDir, 0777, true, true);
            }
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $mimeType = $file->getClientMimeType();
                    $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
                    
                    $file->move($uploadDir, $filename);
                    $fullPath = $uploadDir . '/' . $filename;
                    $tempAttachmentFiles[] = $fullPath;
                    $attachmentPaths[] = [
                        'path' => $fullPath,
                        'name' => $originalName,
                        'mime' => $mimeType
                    ];
                }
            }
        }
        // 5. Send email via helper function
        try {
            sendComposeEmail($toArr, $subject, $body, $ccArr, $bccArr, $attachmentPaths);
            // Cleanup temp attachment files
            foreach ($tempAttachmentFiles as $path) {
                if (File::exists($path)) {
                    @File::delete($path);
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Email sent successfully.'
            ]);
        } catch (\Throwable $e) {
            Log::error('Compose email sending failed: ' . $e->getMessage(), [
                'to' => $toArr,
                'subject' => $subject,
                'exception' => $e
            ]);
            // Cleanup temp attachment files
            foreach ($tempAttachmentFiles as $path) {
                if (File::exists($path)) {
                    @File::delete($path);
                }
            }
            return response()->json([
                'status' => false,
                'message' => 'Email sending failed: ' . $e->getMessage()
            ], 500);
        }
    }
}

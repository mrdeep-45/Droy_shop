<?php

namespace App\Exports;
use App\Models\CrmNote;
use App\Models\Deal;
use App\Models\EnquiryManageModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CrmNotesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $referenceType;
    protected $referenceId;
    public function __construct($referenceType, $referenceId)
    {
        $this->referenceType = $referenceType;
        $this->referenceId = $referenceId;
    }
    public function collection()
    {
         $role   = Session::get('login_type');
        $userId = getCreatedBy();

        $query = CrmNote::where('reference_type', $this->referenceType)
            ->where('reference_id', $this->referenceId)
            ->where('status', 0);

        if ($role === 'Staff') {
            $query->where(function ($q) use ($userId) {
                $q->whereNull('assigned_to')
                    ->orWhere('assigned_to', $userId)
                    ->orWhere('created_by', $userId);
            });
        }

        return $query->orderBy('created_at', 'asc')->get();
    }

    public function headings(): array
    {
        $entityName = '';
        $headerRows = [];

        if ($this->referenceType == 'lead') {
            $lead = EnquiryManageModel::find($this->referenceId);
            $entityName = $lead ? ($lead->enquiry_name ?? $lead->lead_name) : '';
            
            $headerRows[] = ['Notes Report - Lead' . ($entityName ? ' : ' . $entityName : '')];
            $headerRows[] = [];
            $headerRows[] = [
                'Lead No: ' . ($lead->enquiry_no ?? '-'),
                'Lead Name: ' . ($lead->enquiry_name ?? $lead->lead_name ?? '-'),
                'Customer: ' . ($lead->customer_name ?? '-'),
                'Company: ' . ($lead->company_name ?? '-'),
                'Contact Person: ' . ($lead->contact_person_name ?? '-'),
                'Mobile: ' . ($lead->phone ?? '-'),
                'Email: ' . ($lead->email ?? '-'),
                'Assigned Staff: ' . ($lead->assigned_to ? get_createdby_name($lead->assigned_to) : '-'),
            ];
            $headerRows[] = [];

        } elseif ($this->referenceType == 'deal') {
            $deal = Deal::with(['lead', 'dealStage'])->find($this->referenceId);
            $entityName = $deal ? $deal->deal_name : '';
            
            $headerRows[] = ['Notes Report - Deal' . ($entityName ? ' : ' . $entityName : '')];
            $headerRows[] = [];
            $headerRows[] = [
                'Deal No: ' . ($deal->iwo_no ?? '-'),
                'Deal Name: ' . ($deal->deal_name ?? '-'),
                'Customer: ' . ($deal->customer->customer_name ?? $deal->lead->customer_name ?? '-'),
                'Company: ' . ($deal->customer->company_name ?? $deal->lead->company_name ?? '-'),
                'Contact Person: ' . ($deal->customer->contact_person ?? $deal->lead->contact_person_name ?? '-'),
                'Mobile: ' . ($deal->customer->mobile_no ?? $deal->lead->phone ?? '-'),
                'Email: ' . ($deal->customer->email_address ?? $deal->lead->email ?? '-'),
                'Assigned Staff: ' . ($deal->assigned_to ? get_createdby_name($deal->assigned_to) : '-'),
                'Deal Stage: ' . ($deal->dealStage->stage_name ?? '-'),
                'Deal Amount: ' . ($deal->amount ?? '-'),
            ];
            $headerRows[] = [];
        }

        $headerRows[] = [
            'Sr. No.',
            'Title',
            'Follow-up Date',
            'Description',
            'Created By',
            'Created Date & Time',
            'Updated By',
            'Updated Date & Time',
            'Attachment',
        ];

        return $headerRows;
    }

    public function map($note): array
    {
        static $sr = 1;

        return [
            $sr++,
            $note->title,
            $note->follow_up_date
                ? Carbon::parse($note->follow_up_date)->format('d-m-Y')
                : '-',
            strip_tags($note->note),
            get_createdby_name($note->created_by) ?: 'System',
            Carbon::parse($note->created_at)->format('d-m-Y h:i A'),
            $note->updated_by
                ? (get_createdby_name($note->updated_by) ?: 'Unknown')
                : '-',
            $note->updated_by
                ? Carbon::parse($note->updated_at)->format('d-m-Y h:i A')
                : '-',
            $note->attachment ? url('assets/uploads/notes/' . $note->attachment) : '-',
        ];
    }
}

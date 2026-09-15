<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeaveSampleExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        // Must match the column names expected by your importer logic
        return [
            'staff_code', 
            'leave_type', 
            'leave_date', 
            'total_days', 
            'reason', 
            'contact_info'
        ];
    }

    public function collection()
    {
        // Optionally provide one example row
        return new Collection([
            [
                'S001', 
                'CL', 
                'YYYY-MM-DD', // Placeholder for format guidance
                '1', 
                'Reason for the leave.', 
                '9999999999'
            ]
        ]);
    }
}
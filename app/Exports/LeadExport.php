<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Http\Controllers\LeadController;

class LeadExport implements FromView, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $controller = app(LeadController::class);
        $data = $controller->getFilteredLeadsQuery($this->request)->get();
        $filters = $controller->getAppliedFilters($this->request);

        return view('company.crm.lead.export_pdf', [
            'data' => $data,
            'filters' => $filters,
            'is_pdf' => false
        ]);
    }
}

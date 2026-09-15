<?php
namespace App\Http\Controllers;

use App\Models\MaterialIssueDetail;
use App\Models\MaterialIssueHead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{

    public function accept_pending()
    {
        $query = MaterialIssueHead::where('status', 0);

        if (Session::get('login_type') !== 'Company') {
            if (isStaffLogin()) {
                $query->where('staff_id', Session::get('staff_id'));
            } else {
                $query->where('created_by', getCreatedBy());
            }
        }

        $issues = $query->with([
            'details' => function ($q) {
                $q->where('status', 0)->where('accept_status', 0);
            },
            'staff',
            'details.item',
        ])->get();

        $grouped = [];

        foreach ($issues as $issue) {
            foreach ($issue->details as $detail) {
                $grouped[$issue->issue_id][] = [
                    'staff_name'  => $issue->staff->staff_name ?? '-',
                    'issue_no'    => $issue->issue_no ?? '-',
                    'item_code'   => $detail->item->item_code ?? '-',
                    'drawing_no'  => $detail->item->drawing_no ?? '-',
                    'description' => $detail->item->description ?? '-',
                    'uom'         => $detail->item->uom ?? '-',
                    'qty'         => (int) $detail->qty,
                    'issue_id'    => $detail->issue_id,
                    'issue_date'  =>
                    '<span class="badge bg-success"><strong>Issue:</strong> ' .
                    Carbon::parse($detail->issue_date)->format('d-m-Y') .
                    '</span><br><span class="badge bg-danger"><strong>Return:</strong> ' .
                    Carbon::parse($detail->return_date)->format('d-m-Y') .
                    '</span>',
                ];
            }
        }

        return response()->json($grouped);
    }

    public function ack_pending()
    {
        if (isStaffLogin()) {
            return response()->json([]);
        }

        $rows = MaterialIssueDetail::query()
            ->with(['issue.staff', 'item'])
            ->where('status', 0)
            ->where('accept_status', 1)

            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('trn_material_issue_return')
                    ->whereColumn('trn_material_issue_return.issue_id', 'trn_material_issue_detail.issue_id')
                    ->whereColumn('trn_material_issue_return.item_id', 'trn_material_issue_detail.item_id')
                    ->where('trn_material_issue_return.status', 0)
                    ->where('trn_material_issue_return.return_qty', '>', 0);
            })

            ->whereHas('issue', function ($q) {
                $q->where('status', 0);
            })
            ->orderBy('issue_id', 'desc')
            ->get();

        $grouped = [];

        foreach ($rows as $row) {

            $issuedQty = (int) $row->qty;

            $approvedQty = DB::table('trn_material_issue_return')
                ->where('issue_id', $row->issue_id)
                ->where('item_id', $row->item_id)
                ->where('status', 1)
                ->sum('return_qty');

            $pendingQty = DB::table('trn_material_issue_return')
                ->where('issue_id', $row->issue_id)
                ->where('item_id', $row->item_id)
                ->where('status', 0)
                ->sum('return_qty');

            $currentQty = max($issuedQty - ($approvedQty + $pendingQty), 0);

            $qtyHtml  = '<strong>Remaining: ' . $currentQty . '</strong>';
            $qtyHtml .= '<br>
            <span class="badge bg-warning text-dark mt-1">
                        Ack. Pending: ' . $pendingQty . '
                     </span>';

            $groupKey = $row->issue->staff_id ?? 'unknown';

            $grouped[$groupKey][] = [
                'issue_id'    => $row->issue_id,
                'staff_name'  => $row->issue->staff->staff_name ?? '-',
                'issue_no'    => $row->issue->issue_no ?? '-',
                'item_code'   => $row->item->item_code ?? '-',
                'drawing_no'  => $row->item->drawing_no ?? '-',
                'description' => $row->item->description ?? '-',
                'uom'         => $row->item->uom ?? '-',
                'qty'         => $qtyHtml,
                'issue_date'  =>
                '<span class="badge bg-success">
                    <strong>Issue:</strong> ' .
                ($row->issue_date
                        ? Carbon::parse($row->issue_date)->format('d-m-Y')
                        : '-') .
                '</span>',
            ];
        }

        return response()->json($grouped);
    }

    public function accept_material_issue(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'issue_id' => 'required',
        ]);

        MaterialIssueDetail::where('issue_id', $request->issue_id)
            ->where('accept_status', 0)
            ->update([
                'accept_status' => 1,
                'accept_date'   => now(),
            ]);

        return response()->json([
            'status'  => true,
            'message' => 'Issue accepted successfully',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\HrmsApproverChainStep;
use App\SmLeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveInboxController extends Controller
{
    //

    public function index(Request $r)
    {
        $userId = (int) Auth::id();

        // Base: leaves that have a chain with at least one pending HR step actionable by this user
        $q = SmLeaveRequest::query()
            ->with(['type', 'chain.steps' => function($s) { $s->orderBy('step_no'); }])
            ->whereHas('chain', function($cq) use ($userId) {
                $cq->where('overall_status', '!=', 'R')
                   ->whereHas('steps', function($sq) use ($userId) {
                       $sq->actionableByHR($userId);
                   });
            });

        // QUICK search (left search box): id or reason
        if ($term = trim((string) $r->get('q'))) {
            $q->where(function($x) use ($term) {
                $x->where('reason', 'like', "%{$term}%")
                  ->orWhere('id', (int) $term);
            });
        }

        // LONG filters
        if ($status = $r->get('status')) {
            // Map human to code
            $map = ['Pending'=>'P','Approved'=>'A','Rejected'=>'R','Cancelled'=>'C'];
            if (isset($map[$status])) $q->where('approve_status', $map[$status]);
        }
        if ($typeId = $r->get('type_id')) $q->where('type_id', (int) $typeId);
        if ($from = $r->get('from')) $q->whereDate('leave_from', '>=', $from);
        if ($to   = $r->get('to'))   $q->whereDate('leave_to', '<=', $to);

        $leaves = $q->orderBy('id','desc')->paginate(12)->appends($r->query());

        // Which one is active in the details pane?
        $activeId = (int) $r->get('active', 0);
        $selectedLeave = null;
        if ($activeId) {
            $selectedLeave = SmLeaveRequest::with(['type','chain.steps' => function($s){ $s->orderBy('step_no'); }])
                ->find($activeId);
        }
        if (!$selectedLeave && $leaves->count() > 0) {
            $selectedLeave = $leaves->first();
        }
        return $leaves;

        return view('backEnd.employee.leaves.index', compact('leaves','selectedLeave'));
    }

    // AJAX details loader your JS calls
    public function show(Request $r, $id)
    {
        $userId = (int) Auth::id();

        $leave = SmLeaveRequest::with(['type','chain.steps' => function($s){ $s->orderBy('step_no'); }])
            ->findOrFail((int)$id);

        // (Optional) Guard: ensure this leave is relevant to HR inbox (has an actionable HR step)
        $hasActionable = HrmsApproverChainStep::whereHas('chain', function($cq) use ($leave) {
                $cq->where('leave_request_id', $leave->id);
            })
            ->actionableByHR($userId)
            ->exists();

        // If you want to strictly hide non-actionable items from this endpoint, uncomment:
        // abort_unless($hasActionable, 403);

        // Render only the details partial
        return view('backEnd.employee.leaves._details', ['leave' => $leave])->render();
    }


}

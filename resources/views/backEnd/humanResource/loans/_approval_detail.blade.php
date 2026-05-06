{{-- resources/views/backEnd/employee/loans/_approval_detail.blade.php --}}
@php
    use Carbon\Carbon;
    $auth = Auth::user();

    $types = [
        1 => 'Loan',
        2 => 'Salary Advance',
        3 => 'Emergency Advance',
        4 => 'Travel Advance',
        5 => 'Other',
    ];

    $staff = $loan->staffDetails ?? $loan->staff;
    $canAct = false;
    $roleId = $auth->role_id;

    // Normalize manager IDs (handle multiple IDs)
    $managerIds = [];
    if ($staff && !empty($staff->reporting_manager)) {
        $managerIds = array_map('trim', explode(',', (string)$staff->reporting_manager));
    }

    // ✅ Reporting Manager or Admin condition
    if (
        in_array($auth->id, $managerIds) ||            // match by user ID
        in_array($auth->role_id, $managerIds) ||       // match by role ID
        $auth->role_id == 1                            // Admin override
    ) {
        if ($loan->manager_approval == 'Pending') {
            $canAct = true;
        }
    }

    // ✅ Finance role
    if ($roleId == 2 && $loan->manager_approval == 'Approved' && $loan->finance_approval == 'Pending') {
        $canAct = true;
    }

    // ✅ HR role
    if ($roleId == 3 && $loan->finance_approval == 'Approved' && $loan->hr_approval == 'Pending') {
        $canAct = true;
    }

    // ✅ Progress bar calculation
    $steps = 0;
    if ($loan->manager_approval == 'Approved') $steps++;
    if ($loan->finance_approval == 'Approved') $steps++;
    if ($loan->hr_approval == 'Approved') $steps++;
    $percent = ($steps / 3) * 100;
@endphp

{{-- Header with Approve / Reject --}}
<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        Loan / Advance Request
    </h4>

    <div class="purchase-order-content-header-right d-flex align-items-center">
    @if($loan->manager_approval === 'Approved' && ($roleId == 1 || in_array($auth->id, $managerIds)))
        <span class="badge bg-success ms-2 px-3 py-2">Approved by Manager</span>
    @elseif($loan->manager_approval === 'Rejected' && ($roleId == 1 || in_array($auth->id, $managerIds)))
        <span class="badge bg-danger ms-2 px-3 py-2">Rejected by Manager</span>

    @elseif($loan->finance_approval === 'Approved' && $roleId == 2)
        <span class="badge bg-success ms-2 px-3 py-2">Approved by Finance</span>
    @elseif($loan->finance_approval === 'Rejected' && $roleId == 2)
        <span class="badge bg-danger ms-2 px-3 py-2">Rejected by Finance</span>

    @elseif($loan->hr_approval === 'Approved' && $roleId == 3)
        <span class="badge bg-success ms-2 px-3 py-2">Approved by HR</span>
    @elseif($loan->hr_approval === 'Rejected' && $roleId == 3)
        <span class="badge bg-danger ms-2 px-3 py-2">Rejected by HR</span>

    @elseif($canAct)
        {{-- Approve --}}
        <form method="POST" action="{{ route('employee.loans.approve', $loan->id) }}"
              class="d-inline ms-2">@csrf
            <input type="hidden" name="status" value="Approved">
            <button type="submit"
                    class="btn btn-light text-dark d-inline-flex align-items-center gap-2"
                    data-busy-text="Approving...">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                <i class="ico icon-outline-check text-success btn-icon"></i>
                <span class="btn-text fw-semibold">Approve</span>
            </button>
        </form>

        {{-- Reject --}}
        <form method="POST" action="{{ route('employee.loans.approve', $loan->id) }}"
              class="d-inline ms-2">@csrf
            <input type="hidden" name="status" value="Rejected">
            <button type="submit"
                    class="btn btn-light text-dark d-inline-flex align-items-center gap-2"
                    data-busy-text="Rejecting...">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                <i class="ico icon-outline-close text-danger btn-icon"></i>
                <span class="btn-text fw-semibold">Reject</span>
            </button>
        </form>
    @endif
</div>

</div>


<div class="card shadow-sm">
  <div class="card-body">

    {{-- Info Grid --}}
    <div class="row mb-3">
      <div class="col-md-3"><strong>ID:</strong> LN{{ $loan->id }}</div>
      <div class="col-md-3"><strong>Applied On:</strong> {{ optional($loan->created_at)->format('d M Y') }}</div>
      <div class="col-md-3"><strong>Employee:</strong> {{ optional($staff)->first_name ?? '—' }}</div>
      <div class="col-md-3"><strong>Type:</strong> {{ $types[$loan->type_id] ?? '—' }}</div>

      <div class="col-md-3"><strong>Amount:</strong>{{ number_format($loan->amount,2) }}</div>
      <div class="col-md-3"><strong>Installments:</strong> {{ $loan->installments ?? '—' }}</div>
      <div class="col-md-3"><strong>Per Month:</strong> {{ number_format($loan->amount_per_month,2) }}</div>
      <div class="col-md-3"><strong>Mode:</strong> {{ $loan->repayment_mode ?? '—' }}</div>

      <div class="col-md-3"><strong>Repayment Start:</strong>
        {{ $loan->repayment_start ? Carbon::parse($loan->repayment_start)->format('M Y') : '—' }}
      </div>
      <div class="col-md-9"><strong>Purpose:</strong> {{ $loan->purpose ?? '—' }}</div>

      @if($loan->attachment)
        <div class="col-md-12 mt-2">
          <strong>Supporting Document:</strong>
          <a href="{{ asset('uploads/loan_docs/'.$loan->attachment) }}" target="_blank" class="text-primary">
            View File
          </a>
        </div>
      @endif
    </div>

    <hr>

    {{-- Approval Progress --}}
    <div class="mb-3">
      <h6 class="fw-semibold mb-2">Approval Progress</h6>
      <div class="row text-center">
        <div class="col">
          <strong>Manager</strong><br>
          <span class="badge {{ $loan->manager_approval=='Approved'?'bg-success':($loan->manager_approval=='Rejected'?'bg-danger':'bg-warning') }}">
            {{ $loan->manager_approval ?? 'Pending' }}
          </span>
        </div>
        <div class="col">
          <strong>Finance</strong><br>
          <span class="badge {{ $loan->finance_approval=='Approved'?'bg-success':($loan->finance_approval=='Rejected'?'bg-danger':'bg-warning') }}">
            {{ $loan->finance_approval ?? 'Pending' }}
          </span>
        </div>
        <div class="col">
          <strong>HR</strong><br>
          <span class="badge {{ $loan->hr_approval=='Approved'?'bg-success':($loan->hr_approval=='Rejected'?'bg-danger':'bg-warning') }}">
            {{ $loan->hr_approval ?? 'Pending' }}
          </span>
        </div>
      </div>
      <div class="progress mt-3" style="height:8px;">
        <div class="progress-bar bg-success" style="width: {{ $percent }}%;"></div>
      </div>
    </div>

  </div>
</div>

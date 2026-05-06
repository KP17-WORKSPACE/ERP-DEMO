@extends('backEnd.newmasterpage')
@section('mainContent')
@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Attendance Rectification Requests</h5>

    <form method="GET" class="d-flex align-items-center">
      <select name="status" class="form-select form-select-sm me-2" onchange="this.form.submit()">
        <option value="">All</option>
        <option value="Pending" {{ request('status')=='Pending' ? 'selected' : '' }}>Pending</option>
        <option value="Manager Approved" {{ request('status')=='Manager Approved' ? 'selected' : '' }}>Manager Approved</option>
        <option value="HR Approved" {{ request('status')=='HR Approved' ? 'selected' : '' }}>HR Approved</option>
        <option value="Rejected" {{ request('status')=='Rejected' ? 'selected' : '' }}>Rejected</option>
      </select>
      <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
    </form>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" style="white-space:nowrap;">
        <thead class="bg-light">
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Employee</th>
            <th>In Time</th>
            <th>Out Time</th>
            <th>Remarks</th>
            <th>Status</th>
            <th>Approval Status</th>
            <th>Approved By</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rectifications as $i => $r)
            <tr>
              <td>{{ $rectifications->firstItem() + $i }}</td>
              <td>{{ \Carbon\Carbon::parse($r->attendence_date)->format('d M Y (D)') }}</td>
              <td>{{ optional($r->staff)->first_name }} {{ optional($r->staff)->last_name }}</td>
              <td>{{ $r->in_time ?? '—' }}</td>
              <td>{{ $r->out_time ?? '—' }}</td>
              <td>{{ $r->notes ?? '—' }}</td>

              <td>
                @if($r->attendence_type == 'P')
                  <span class="badge bg-success">Present</span>
                @elseif($r->attendence_type == 'A')
                  <span class="badge bg-danger">Absent</span>
                @elseif($r->attendence_type == 'L')
                  <span class="badge bg-warning text-dark">Leave</span>
                @else
                  <span class="badge bg-secondary">—</span>
                @endif
              </td>

              <td>
                @if($r->approval_status == 'Pending')
                  <span class="badge bg-warning text-dark">Pending</span>
                @elseif($r->approval_status == 'Manager Approved')
                  <span class="badge bg-primary">Manager Approved</span>
                @elseif($r->approval_status == 'HR Approved')
                  <span class="badge bg-success">HR Approved</span>
                @elseif($r->approval_status == 'Rejected')
                  <span class="badge bg-danger">Rejected</span>
                @endif
              </td>

              <td>
                @if($r->hr_approved_by)
                  HR ID: {{ $r->hr_approved_by }}
                @elseif($r->manager_approved_by)
                  Manager ID: {{ $r->manager_approved_by }}
                @else
                  —
                @endif
              </td>

              <td>
                @if($r->approval_status == 'Pending' || $r->approval_status == 'Manager Approved')
              <div class="btn-group">
  {{-- ✅ Approve Button --}}
  <form method="POST" action="{{ route('attendance.approve', $r->id) }}" onsubmit="return confirm('Approve this request?');">
    @csrf
    <button type="submit"
    class="btn btn-light text-success d-inline-flex align-items-center gap-2"
    data-busy-text="Approving...">
    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    <i class="ico icon-outline-check text-success btn-icon"></i>
    <span class="btn-text">Approve</span>
</button>
  </form>

  {{-- ❌ Reject Button --}}
  <form method="POST" action="{{ route('attendance.reject', $r->id) }}" onsubmit="return confirm('Reject this request?');">
    @csrf
   <button type="submit"
    class="btn btn-light text-danger d-inline-flex align-items-center gap-2"
    data-busy-text="Rejecting...">
    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    <i class="ico icon-outline-close text-danger btn-icon"></i>
    <span class="btn-text">Reject</span>
</button>
  </form>
</div>

                @else
                  <span class="text-muted small">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="text-center text-muted py-3">No rectification requests found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-3">
      {{ $rectifications->links() }}
    </div>
  </div>
</div>
@endsection

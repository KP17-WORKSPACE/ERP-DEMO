<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="fw-bold mb-2">{{ optional($r->staff)->first_name }}</h5>
    <p class="text-muted small mb-3">{{ \Carbon\Carbon::parse($r->attendence_date)->format('d M Y (D)') }}</p>

    <div class="row small mb-3">
      <div class="col-6">In Time: <strong>{{ $r->in_time ?? '—' }}</strong></div>
      <div class="col-6">Out Time: <strong>{{ $r->out_time ?? '—' }}</strong></div>
      <div class="col-6">Type: <strong>{{ $r->attendence_type ?? '—' }}</strong></div>
      <div class="col-6">Status: <strong>{{ $r->approval_status }}</strong></div>
    </div>

    <p class="small"><strong>Notes:</strong><br>{{ $r->notes ?? '—' }}</p>

    @if($r->approval_status == 'Pending')
      <div class="mt-3 text-end">
        <form method="POST" action="{{ route('attendance.approve', $r->id) }}" style="display:inline;">
          @csrf
          <button class="btn btn-sm btn-success">Approve</button>
        </form>
        <form method="POST" action="{{ route('attendance.reject', $r->id) }}" style="display:inline;">
          @csrf
          <button class="btn btn-sm btn-danger">Reject</button>
        </form>
      </div>
    @endif
  </div>
</div>

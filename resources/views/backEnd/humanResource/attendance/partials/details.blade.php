@php
  use Carbon\Carbon;

  $ym = $month ?? now()->format('Y-m');
  $first = Carbon::createFromFormat('Y-m', $ym)->startOfMonth();
  $last  = Carbon::createFromFormat('Y-m', $ym)->endOfMonth();

  // Map types for badges
  $badge = [
    'P' => 'bg-success',
    'L' => 'bg-warning',
    'A' => 'bg-danger',
    'H' => 'bg-info',
    'F' => 'bg-primary',
  ];

    $weeklyOffCount = $records->filter(function($r){
    return !empty($r->is_offday) && empty($r->in_time) && empty($r->out_time);
  })->count();


@endphp



<div class="container-fluid">

  {{-- EMPLOYEE QUICK SUMMARY (also visible to admin for selected staff) --}}
  <div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
      <div class="mb-2">
        <h5 class="mb-1">{{ $staff->full_name ?? '—' }} <span class="text-muted">(#{{ $staff->id }})</span></h5>
        <div class="small text-muted">
          {{ $staff->department->name ?? '—' }} • {{ $staff->designation->title ?? '—' }}
        </div>
      </div>
      <div class="d-flex align-items-center gap-2 mb-2">
        <input type="month" class="form-control" name="month" value="{{ $ym }}">
        @can('attendance.create')
          <a href="{{ route('attendance.create', ['staff_id' => $staff->id]) }}" class="btn btn-light">Add Entry</a>
        @endcan
      </div>
    </div>
  </div>

  {{-- TODAY WIDGET --}}
  @if(!empty($today))
  <div class="alert alert-light d-flex justify-content-between align-items-center">
    <div>
      <strong>Today ({{ \Carbon\Carbon::today()->format('d M Y') }})</strong> —
      Status:
      <span class="badge {{ $badge[$today->attendence_type] ?? 'bg-secondary' }}">
        {{ $today->attendence_type ?? '—' }}
      </span>
      &nbsp; In: <strong>{{ $today->in_time ?? '—' }}</strong>
      &nbsp; Out: <strong>{{ $today->out_time ?? '—' }}</strong>
    </div>
    @if(!$today->out_time && Auth::user()->id == ($staff->user_id ?? null))
      <a href="{{ route('attendance.markOut', $today->id) }}" class="btn btn-sm btn-outline-dark">
        Mark Out
      </a>
    @endif
  </div>
  @endif

  {{-- MONTH SUMMARY COUNTS --}}
  @php
    $counts = [
      'P' => $records->where('attendence_type','P')->count(),
      'L' => $records->where('attendence_type','L')->count(),
      'A' => $records->where('attendence_type','A')->count(),
      'H' => $records->where('attendence_type','H')->count(),
      'F' => $records->where('attendence_type','F')->count(),
    ];
  @endphp
<div class="d-flex flex-wrap gap-2 mb-2">
  @foreach (['P'=>'Present','L'=>'Late','A'=>'Absent','H'=>'Holiday','F'=>'Half Day'] as $k=>$label)
    <span class="badge {{ $badge[$k] ?? 'bg-secondary' }} p-2">
      {{ $label }}: {{ $counts[$k] ?? 0 }}
    </span>
  @endforeach
  <span class="badge bg-info p-2">Weekly Off: {{ $weeklyOffCount }}</span>
</div>

  {{-- TABLE --}}
  <div class="table-responsive">
    <table class="table table-striped align-middle">
   <thead class="bg-light">
  <tr>
    <th>Day</th>
    <th>In Time</th>
    <th>Out Time</th>
    <th>Late</th>
    <th>Late Time</th>
    <th>Early Out</th>
    <th>Early Out Time</th>
    <th>Working Time</th>
    <th>Over Time</th>
    <th>Rectified Hours</th>
    <th>Status</th>
  </tr>
</thead>

    </table>
  </div>
</div>

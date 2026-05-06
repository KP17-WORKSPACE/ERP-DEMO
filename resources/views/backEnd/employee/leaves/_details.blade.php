{{-- resources/views/backEnd/employee/leaves/_details.blade.php --}}
@php
    use Carbon\Carbon;

    // --- Helpers for PHP 7.1 / Laravel 5.7 ---
    if (!function_exists('safeDate')) {
        function safeDate($d)
        {
            return $d ? Carbon::parse($d)->format('d M Y') : '—';
        }
    }

    // Normalize status (handles P/A/R/C or words)
    $raw = (string) ($leave->approve_status ?? 'P');
    $normalized = strtoupper(trim($raw));
    $map = [
        'P' => 'Pending',
        'A' => 'Approved',
        'R' => 'Rejected',
        'C' => 'Cancelled',
        'PENDING' => 'Pending',
        'APPROVED' => 'Approved',
        'REJECTED' => 'Rejected',
        'CANCELLED' => 'Cancelled',
    ];
    $status = isset($map[$normalized]) ? $map[$normalized] : ucfirst(strtolower($raw));

    // Top status badge (Bootstrap 5)
    $topBadgeClass = 'bg-warning';
    if ($status == 'Approved') {
        $topBadgeClass = 'bg-success';
    } elseif ($status == 'Rejected') {
        $topBadgeClass = 'bg-danger';
    } elseif ($status == 'Cancelled') {
        $topBadgeClass = 'bg-secondary';
    }

    // Half-day label
    $halfLabel = '';
    if (!empty($leave->is_half_day)) {
        $halfLabel = '(Half Day' . (!empty($leave->half_session) ? ' - ' . $leave->half_session : '') . ')';
    }

    // ---- Build Approval Flow ----
    // Preferred: approvals_json (array of steps with keys: name, role, status, acted_at, comment, is_current)
    $flow = [];
    if (!empty($leave->approvals_json)) {
        $decoded = is_array($leave->approvals_json)
            ? $leave->approvals_json
            : json_decode($leave->approvals_json, true);
        if (is_array($decoded)) {
            foreach ($decoded as $step) {
                $flow[] = [
                    'name' => isset($step['name']) ? $step['name'] : 'Approver',
                    'role' => isset($step['role']) ? $step['role'] : '',
                    'status' => isset($step['status']) ? $step['status'] : 'Pending',
                    'acted_at' => !empty($step['acted_at']) ? safeDate($step['acted_at']) : null,
                    'comment' => isset($step['comment']) ? $step['comment'] : null,
                    'is_current' => !empty($step['is_current']),
                ];
            }
        }
    }

    // Fallback: approver_chain + current_index
    if (empty($flow)) {
        // Treat current_index as 0-based; change if your DB stores 1-based
        $currentIndex = is_null($leave->current_index) ? 0 : max(0, (int) $leave->current_index);
        $chainRaw = trim((string) $leave->approver_chain);

        $ids = [];
        if ($chainRaw !== '') {
            $asJson = json_decode($chainRaw, true);
            if (is_array($asJson)) {
                foreach ($asJson as $item) {
                    if (is_array($item) && isset($item['id'])) {
                        $ids[] = (int) $item['id'];
                    } elseif (is_numeric($item)) {
                        $ids[] = (int) $item;
                    }
                }
                $ids = array_values(array_unique(array_filter($ids)));
            } else {
                $parts = array_filter(array_map('trim', explode(',', $chainRaw)));
                foreach ($parts as $p) {
                    if (is_numeric($p)) {
                        $ids[] = (int) $p;
                    }
                }
            }
        }

        // Resolve staff names if IDs are present
        $namesById = [];
        if (!empty($ids)) {
            try {
                $namesById = \DB::table('sm_staffs')->whereIn('id', $ids)->pluck('full_name', 'id')->toArray();
            } catch (\Exception $e) {
                $namesById = [];
            }
        }

        // Build token list
        $tokens = [];
        if (!empty($ids)) {
            foreach ($ids as $sid) {
                $tokens[] = isset($namesById[$sid]) ? $namesById[$sid] : 'Staff #' . $sid;
            }
        } elseif (!empty($chainRaw)) {
            $tokens = array_filter(array_map('trim', explode(',', $chainRaw)));
        }

        // Infer per-step status from current_index and overall
        foreach ($tokens as $i => $nm) {
            $stepStatus = 'Pending';
            if ($i < $currentIndex) {
                $stepStatus = 'Approved';
            } elseif ($i == $currentIndex && $status === 'Rejected') {
                $stepStatus = 'Rejected';
            }

            $flow[] = [
                'name' => $nm ?: 'Approver',
                'role' => '',
                'status' => $stepStatus,
                'acted_at' => null,
                'comment' => null,
                'is_current' => $i === $currentIndex && $status === 'Pending',
            ];
        }
    }

    // Helper for per-card badge class (Bootstrap 5)
    if (!function_exists('flowBadgeClass')) {
        function flowBadgeClass($status)
        {
            $s = strtolower((string) $status);
            if ($s === 'approved') {
                return 'bg-success';
            }
            if ($s === 'rejected') {
                return 'bg-danger';
            }
            if ($s === 'cancelled') {
                return 'bg-secondary';
            }
            return 'bg-warning'; // pending/unknown
        }
    }
@endphp

<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">#{{ 'LR' . $leave->company->other_code . '-' . $leave->id }}
    </h4>
    <div class="purchase-order-content-header-right d-flex align-items-center">
        {{-- Apply Leave --}}
        <a href="{{ url('employee/leaves/create') }}" class="btn btn-light text-dark d-inline-flex align-items-center">
            <i class="ico icon-outline-add-square text-success"></i>
            <span class="btn-text ms-1">Add</span>
        </a>

        {{-- Edit current Leave - --}}
        @if (isset($leave) && $status == 'Pending')
    <a href="{{ route('employee.leaves.edit', $leave->id) }}"
       class="btn btn-light text-dark d-inline-flex align-items-center ms-2">
        <i class="ico icon-outline-pen-2 text-success"></i>
        <span class="btn-text ms-1">Edit</span>
    </a>
@endif

    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-3">
                <label class="fw-bold text-muted small d-block">Type</label>
                <div class="fw-semibold">
                    {{ isset($leave->type) && isset($leave->type->name) ? $leave->type->name : 'Type #' . $leave->type_id }}
                </div>
            </div>

            <div class="col-md-3">
                <label class="fw-bold text-muted small d-block">From</label>
                <div class="fw-semibold">
                    {{ $leave->leave_from ? \Carbon\Carbon::parse($leave->leave_from)->format('d-m-Y') : '—' }}
                </div>
            </div>

            <div class="col-md-3">
                <label class="fw-bold text-muted small d-block">To</label>
                <div class="fw-semibold">
                    {{ $leave->leave_to ? \Carbon\Carbon::parse($leave->leave_to)->format('d-m-Y') : '—' }}
                </div>
            </div>

            <div class="col-md-3">
                <label class="fw-bold text-muted small d-block">Days</label>
                <div class="fw-semibold">
                  {{ (int)$leave->days == $leave->days ? (int)$leave->days : $leave->days }} {!! $halfLabel !!} days


                </div>
            </div>

            <div class="col-md-3">
                <label class="fw-bold text-muted small d-block">Apply Date</label>
                <div class="fw-semibold">
                    {{ $leave->apply_date ? \Carbon\Carbon::parse($leave->apply_date)->format('d-m-Y') : '—' }}
                </div>
            </div>

            <div class="col-md-3">
                <label class="fw-bold text-muted small d-block">Reporting Manager</label>
                <div class="fw-semibold">{{ $leave->reportingManager->full_name ?? '-' }}
                </div>
            </div>

            <div class="col-md-3">
                <label class="fw-bold text-muted small d-block">Hand Over</label>
                <div class="fw-semibold">{{ $leave->handover_to ?? '-' }}
                </div>
            </div>

            {{-- File attachment --}}
            <div class="col-md-3">
                <label class="fw-bold text-muted small d-block">Attachment</label>
                <div>
                    @if (!empty($leave->file))
                        <a href="{{ asset('uploads/leave/' . $leave->file) }}" target="_blank"
                            class="btn btn-sm btn-outline-primary">
                            View File
                        </a>
                    @else
                        <span class="text-muted">No attachment</span>
                    @endif
                </div>
            </div>

            <div class="col-md-3">
                <label class="fw-bold text-muted small d-block">Reason</label>
                <div class="fw-semibold">{{ !empty($leave->reason) ? $leave->reason : '—' }}</div>
            </div>




            {{-- Notes --}}
            @if (!empty($leave->note))
                <div class="col-md-3">
                    <label class="fw-bold text-muted small d-block">Note</label>
                    <div class="fw-semibold">{{ $leave->note }}</div>
                </div>
            @endif
        </div>

        <div class="col-12">
         <h6 class="fw-bold mb-3 mt-4">Emergency Contacts</h6>

    @php
        $contacts = is_array($leave->emergency_contacts)
            ? $leave->emergency_contacts
            : (json_decode($leave->emergency_contacts, true) ?: []);
    @endphp

    @if(count($contacts))
        <div class="row">
            @foreach($contacts as $i => $c)
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-3 bg-light h-100">
                            <strong>Contact {{ $i + 1 }}</strong><br>
                        <span>{{ $c['name'] ?? '—' }}</span><br>
                        <small class="text-muted">
                            {{ $c['relation'] ?? '—' }} |
                            {{ $c['phone'] ?? '—' }} |
                            {{ $c['country'] ?? '—' }}
                        </small>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">No emergency contacts provided.</p>
    @endif
</div>


        {{-- ===== Approval Flow (Bootstrap 5) ===== --}}
        @if (!empty($flow))
            <hr>
            <h6 class="mb-3">Approval Flow</h6>

            <div class="d-flex align-items-stretch flex-wrap gap-3">
                @foreach ($flow as $st)
                    @php
                        $statusText = $st['status'] ?? 'Pending';
                        $statusClassMap = [
                            'Approved' => 'bg-success',
                            'Rejected' => 'bg-danger',
                            'Pending' => 'bg-warning text-dark',
                            'Skipped' => 'bg-secondary',
                        ];
                        $badge = isset($statusClassMap[$statusText]) ? $statusClassMap[$statusText] : 'bg-secondary';

                        $name = !empty($st['name']) ? $st['name'] : 'Unassigned';
                        $role = !empty($st['role']) ? $st['role'] : 'Approver';
                        $actedAt = !empty($st['acted_at']) ? $st['acted_at'] : null;
                        $comment = !empty($st['comment']) ? $st['comment'] : null;
                    @endphp

                    <div class="card text-center shadow-sm"
                        style="width: 16rem; border-top: 4px solid var(--bs-primary);">
                        <div class="card-body">
                            {{-- Role + Approver name --}}
                            <h6 class="card-title mb-1">{{ $role }}</h6>
                            <p class="text-muted small mb-2">{{ $name }}</p>

                            {{-- Status --}}
                            <span class="badge {{ $badge }} mb-2 px-2 py-1">{{ $statusText }}</span>

                            {{-- Action date --}}
                            @if ($actedAt)
                                <p class="small mb-1"><strong>Date:</strong> {{ $actedAt }}</p>
                            @endif

                            {{-- Comment --}}
                            @if ($comment)
                                <p class="small text-muted mb-0">“{{ \Illuminate\Support\Str::limit($comment, 90) }}”
                                </p>
                            @endif
                        </div>
                        <div class="card-footer text-muted small">
                            Step {{ $loop->iteration }}
                        </div>
                    </div>

                    {{-- Arrow between steps --}}
                    @if (!$loop->last)
                        <div class="d-none d-lg-flex align-items-center">
                            <i class="bi bi-arrow-right-circle fs-3 text-secondary"></i>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

    </div>
</div>

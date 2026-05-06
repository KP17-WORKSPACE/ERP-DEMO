<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">#{{ 'LR' . $leave->company->other_code . '-' . $leave->id }}</h4>
    <div class="purchase-order-content-header-right d-flex align-items-center">
        {{-- Apply Leave --}}
        <a href="{{ url('employee/leaves/create') }}"
           class="btn btn-light text-dark d-inline-flex align-items-center">
            <i class="ico icon-outline-add-square text-success"></i>
            <span class="btn-text ms-1">Add</span>
        </a>

        <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu" style="">
                    
                    <li>
                    <a class="dropdown-item d-flex align-items-center text-dark" href="{{ url('employee/leaves') }}">
                        <i class="ico icon-bold-info-minimalistic text-success  title-15 me-2"></i> My Leaves</a>
                    </li>
                </ul>
            </div>

        {{-- Edit current Leave --}}
        {{-- @if(isset($leave) && $status == 'Pending')
            <a href="{{ url('employee/leaves/'.$leave->id.'/edit') }}"
               class="btn btn-light text-dark d-inline-flex align-items-center ms-2">
                <i class="ico icon-outline-pen-2 btn-icon"></i>
                <span class="btn-text ms-1">Edit</span>
            </a>
        @endif --}}
    </div>
</div>
<div class="card shadow-sm">
  <div class="card-body">
    

    {{-- === Leave Details (same fields, 3-3 columns) === --}}
@php
    // Half-day label (optional)
    $halfLabel = '';
    if (!empty($leave->is_half_day)) {
        $session = $leave->half_session
            ? ' (' . str_replace('_', ' ', ucwords(strtolower($leave->half_session))) . ')'
            : '';
        $halfLabel = '<span class="badge bg-warning text-dark ms-1">Half Day' . $session . '</span>';
    }
@endphp

<div class="">

  <div class="row g-2">
    <div class="col-md-3">
      <label class="fw-bold text-muted small d-block">Type</label>
      <div class="fw-semibold">
        {{ $leave->type->name ?? ('Type #'.$leave->type_id) }}
      </div>
    </div>

    <div class="col-md-3">
      <label class="fw-bold text-muted small d-block">From</label>
      <div class="fw-semibold">
        {{ optional($leave->leave_from)->format('d-m-Y') ?? '—' }}
      </div>
    </div>

    <div class="col-md-3">
      <label class="fw-bold text-muted small d-block">To</label>
      <div class="fw-semibold">
        {{ optional($leave->leave_to)->format('d-m-Y') ?? '—' }}
      </div>
    </div>

    <div class="col-md-3">
      <label class="fw-bold text-muted small d-block">Days</label>
      <div class="fw-semibold">
        {{ number_format((float)($leave->days ?? 0), 2) }} {!! $halfLabel !!}
      </div>
    </div>

    <div class="col-md-3">
      <label class="fw-bold text-muted small d-block">Apply Date</label>
      <div class="fw-semibold">
        {{ optional($leave->apply_date)->format('d-m-Y') ?? '—' }}
      </div>
    </div>

    <div class="col-md-3">
      <label class="fw-bold text-muted small d-block">Reporting Manager</label>
      <div class="fw-semibold">
        {{ $leave->reportingManager->full_name ?? '—' }}
      </div>
    </div>

    <div class="col-md-3">
      <label class="fw-bold text-muted small d-block">Hand Over</label>
      <div class="fw-semibold">
        {{ $leave->handover_to ?? '—' }}
      </div>
    </div>

    <div class="col-md-3">
      <label class="fw-bold text-muted small d-block">Attachment</label>
      <div>
        @if (!empty($leave->file))
          {{-- stored via $r->file('file')->store('leaves', 'public') --}}
          <a href="{{ \Illuminate\Support\Facades\Storage::url($leave->file) }}"
             target="_blank" class="btn btn-sm btn-outline-primary">
            View File
          </a>
        @else
          <span class="text-muted">No attachment</span>
        @endif
      </div>
    </div>

    <div class="col-md-3">
      <label class="fw-bold text-muted small d-block">Reason</label>
      <div class="fw-semibold">
        {{ $leave->reason ?: '—' }}
      </div>
    </div>

    @if (!empty($leave->note))
      <div class="col-md-3">
        <label class="fw-bold text-muted small d-block">Note</label>
        <div class="fw-semibold">
          {{ $leave->note }}
        </div>
      </div>
    @endif
  </div>
</div>

      <div class="col-12">
         <h6 class="fw-bold mt-1">Emergency Contacts</h6>

    @php
        $contacts = is_array($leave->emergency_contacts)
            ? $leave->emergency_contacts
            : (json_decode($leave->emergency_contacts, true) ?: []);
    @endphp

    @if(count($contacts))
        <div class="row">
            @foreach($contacts as $i => $c)
                <div class="col-md-6 mb-3">
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


    {{-- Header --}}
    {{-- <div class="d-flex justify-content-between align-items-center mb-3">
     
      <span class="badge badge-{{ $leave->approve_status_badge }}">
        {{ $leave->approve_status_label }}
      </span>
    </div> --}}

    {{-- Applicant info --}}
  
    

  {{-- Approval Flow (Cards) --}}
@if($leave->chain && $leave->chain->steps && $leave->chain->steps->count())
  @php
    $allSteps = $leave->chain->steps;

    // HR ko last me rakho (PHP 7.1 compatible)
    $ordered = $allSteps->reject(function ($s) {
        return stripos((string)($s->role ?? ''), 'hr') !== false;
    })->values()->merge(
        $allSteps->filter(function ($s) {
            return stripos((string)($s->role ?? ''), 'hr') !== false;
        })->values()
    );

    // Kya hum approvals page par hain?
    $routeName    = \Illuminate\Support\Facades\Route::currentRouteName();
    $path         = request()->path();
    $inApprovals  = (\Illuminate\Support\Str::contains((string)$routeName, 'approvals')
                    || \Illuminate\Support\Str::contains((string)$path, 'approvals'));
  @endphp

  <hr>
  <h6 class="mb-3">Approval Flow</h6>

  <div class="row g-4">
    @foreach($ordered as $step)
     @php
  $code       = $step->status ?? 'P';
  $statusText = $code==='A'?'Approved':($code==='R'?'Rejected':($code==='S'?'Skipped':'Pending'));
  $badge      = $statusText==='Approved' ? 'bg-success' : ($statusText==='Rejected' ? 'bg-danger' : ($statusText==='Pending' ? 'bg-warning text-dark' : 'bg-secondary'));

  $role  = $step->role ?: 'Approver';
  $name  = $step->approver_name ?? $step->name ?? (optional($step->user)->first_name ?: '');

  // ✅ approver can always act (P/A/R/S) if this is *their* step
  $isYou      = ((int)($step->approver_id ?? 0) === (int)Auth::id());
  $canAct     = $isYou;
  $actionText = ($code === 'P') ? 'Take Action' : 'Update Action';

  $actedAt = !empty($step->acted_at)
    ? \Carbon\Carbon::parse($step->acted_at)->format('d M Y, h:i A')
    : ((!empty($step->updated_at) && ($code==='A'||$code==='R'))
        ? \Carbon\Carbon::parse($step->updated_at)->format('d M Y, h:i A')
        : null);
@endphp

      <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
        <div class="card text-center shadow-sm h-100" style="border-top:4px solid var(--bs-primary);">
          <div class="card-body">
            <h6 class="card-title mb-1">
              {{ $role }}
              @if($isYou)<span class="badge bg-info ms-1">You</span>@endif
            </h6>
            <p class="text-muted small mb-2">{{ $name ?: '' }}</p>

            <span class="badge {{ $badge }} mb-2 px-2 py-1">{{ $statusText }}</span>

            @php
              $hasAction = ($code !== 'P')
                           || !empty($step->acted_at)
                           || !empty($step->l1_decision)
                           || !empty($step->l2_decision)
                           || !empty($step->l3_decision);

              $r = strtolower((string)($role ?: ''));
              $items = [];

              if ($hasAction) {
                  if (strpos($r, 'report') !== false) {
                      $items = [
                          'Workload Planning'            => $step->l1_workload ?? null,
                          'Team Coverage'                => $step->l1_coverage ?? null,
                          'Leave Eligibility'            => $step->l1_eligibility ?? null,
                          'Leave Duration Reasonable?'   => $step->l1_duration_ok ?? null,
                          'Policy Compliance (Notice)'   => $step->l1_notice_compliance ?? null,
                          'Approval'                     => $step->l1_decision ?? null,
                          'Remark'                       => $step->l1_remark ?? null,
                      ];
                  } elseif (strpos($r, 'finance') !== false) {
                      $items = [
                          'Leave Balance Validation'     => $step->l2_balance ?? null,
                          'Unpaid Leave Impact'          => $step->l2_unpaid ?? null,
                          'Encashment Eligibility'       => $step->l2_encash ?? null,
                          'Cost Implications'            => $step->l2_cost ?? null,
                          'Policy Adherence'             => $step->l2_policy ?? null,
                          'Approval'                     => $step->l2_decision ?? null,
                          'Remark'                       => $step->l2_remark ?? null,
                      ];
                  } else { // HR
                      $items = [
                          'Document Verification'        => $step->l3_docs ?? null,
                          'Policy Compliance (HR Rules)' => $step->l3_policy ?? null,
                          'System Update'                => $step->l3_system ?? null,
                          'Payroll Coordination'         => $step->l3_payroll ?? null,
                          'Legal Compliance (UAE Law)'   => $step->l3_legal ?? null,
                          'Final Approval'               => $step->l3_decision ?? null,
                          'Remark'                       => $step->l3_remark ?? null,
                      ];
                  }
              }
            @endphp

            @php
  // helper (PHP 7.1 compatible) — label+value+step code se icon/color decide
  $decideIcon = function ($label, $value, $code) {
      $v = strtolower(trim((string)$value));

      // Positive/Negative keyword buckets
      $positives = [
          'valid','compliant','updated','shared with finance','sufficient balance','not required',
          'eligible','approve','approved','no extra cost','backup available','no impact',
          'manageable impact','annual','sick','emergency','yes','ok','na'
      ];
      $negatives = [
          'invalid','not compliant','insufficient balance','deduction required','overtime required',
          'temporary staff required','not eligible','reject','rejected','not available','no','needs adjustment'
      ];
      $neutrals = ['pending','modify','n/a','na','-',''];

      // Default state from value
      $state = 'neu';
      if ($v !== '') {
          if (in_array($v, $positives, true)) $state = 'pos';
          elseif (in_array($v, $negatives, true)) $state = 'neg';
          elseif (in_array($v, $neutrals, true)) $state = 'neu';
      } else {
          // Fallback from step status
          if ($code === 'A') $state = 'pos';
          elseif ($code === 'R') $state = 'neg';
          else $state = 'neu';
      }

      // Return icon char + BS color class
      if ($state === 'pos')   return ['✔', 'text-success'];
      if ($state === 'neg')   return ['✖', 'text-danger'];
      return ['•', 'text-muted'];
  };
@endphp


            @if(!empty($items))
             <ul class="list-unstyled text-start small mb-0 mt-2">
  @foreach($items as $lbl => $val)
    @if(!is_null($val) && $val !== '')
      @php list($iChar, $iClass) = $decideIcon($lbl, $val, $code); @endphp
      <li class="mb-1 d-flex">
        <span class="me-2 {{ $iClass }}" aria-hidden="true" style="line-height:1.35;">{{ $iChar }}</span>
        <div><strong>{{ $lbl }}:</strong> {{ \Illuminate\Support\Str::limit($val, 120) }}</div>
      </li>
    @endif
  @endforeach
  @if(!empty($actedAt))
    <li class="text-secondary"><em>Acted at:</em> {{ $actedAt }}</li>
  @endif
</ul>
            @endif

           
          </div>

          {{-- Footer: Approvals page par "Action", warna "Step n" --}}
                @if($canAct)
                <div class="card-footer text-muted small">
                <button
                type="button"
                class="btn btn-light text-dark d-inline-flex align-items-center gap-2"
                data-bs-toggle="modal"
                data-bs-target="#approvalActionModal"
                data-leave-id="{{ $leave->id }}"
                data-step-id="{{ $step->id }}"
                data-role="{{ $role }}"
                data-approver="{{ $name }}"
                > <i class="ico icon-outline-add-circle text-success btn-icon"></i>Take Action</button>
                </div>
                @endif

        </div>
      </div>
    @endforeach
  </div>
@endif




{{-- ===== Global Action Modal (Bootstrap 5) ===== --}}
@push('modals')
<div class="modal fade" id="approvalActionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form method="POST" action="{{ route('approvals.action') }}" id="approvalActionForm">
        @csrf
        <input type="hidden" name="leave_id" id="act_leave_id">
        <input type="hidden" name="step_id"  id="act_step_id">
        <input type="hidden" name="actor_role" id="act_role">

        <div class="modal-header">
          <h5 class="modal-title">Action</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="alert alert-light border small mb-3" id="act_header" style="display:none;"></div>

          {{-- ===== Level 1: Reporting Manager (3x grid) ===== --}}
          <div class="level-section level-l1 d-none">
            <h6 class="mb-3">Level 1: Reporting Manager (Direct Supervisor)</h6>
            <div class="row g-3">
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Workload Planning</label>
                <select class="form-select" name="l1_workload">
                  <option>No Impact</option><option>Manageable Impact</option><option>High Impact</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Team Coverage</label>
                <select class="form-select" name="l1_coverage">
                  <option>Backup Available</option><option>Not Available</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Leave Eligibility</label>
                <select class="form-select" name="l1_eligibility">
                  <option>Annual</option><option>Sick</option><option>Emergency</option><option>Other</option>
                </select>
              </div>

              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Leave Duration Reasonable?</label>
                <select class="form-select" name="l1_duration_ok">
                  <option>Yes</option><option>No</option><option>Needs Adjustment</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Policy Compliance (Notice)</label>
                <select class="form-select" name="l1_notice_compliance">
                  <option>Compliant</option><option>Not Compliant</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Approval</label>
                <select class="form-select" name="l1_decision" required>
                  <option value="Approve">Approve</option>
                  <option value="Reject">Reject</option>
                  <option value="Modify">Modify</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Remark</label>
                <textarea class="form-control" name="l1_remark" rows="2"></textarea>
              </div>
            </div>
          </div>

          {{-- ===== Level 2: Finance Manager (3x grid) ===== --}}
          <div class="level-section level-l2 d-none">
            <h6 class="mb-3">Level 2: Finance Manager</h6>
            <div class="row g-3">
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Leave Balance Validation</label>
                <select class="form-select" name="l2_balance">
                  <option>Sufficient Balance</option><option>Insufficient Balance</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Unpaid Leave Impact</label>
                <select class="form-select" name="l2_unpaid">
                  <option>Deduction Required</option><option>Not Required</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Encashment Eligibility</label>
                <select class="form-select" name="l2_encash">
                  <option>Eligible</option><option>Not Eligible</option><option>NA</option>
                </select>
              </div>

              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Cost Implications</label>
                <select class="form-select" name="l2_cost">
                  <option>No Extra Cost</option><option>Overtime Required</option><option>Temporary Staff Required</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Policy Adherence</label>
                <select class="form-select" name="l2_policy">
                  <option>Compliant</option><option>Not Compliant</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Approval</label>
                <select class="form-select" name="l2_decision" required>
                  <option value="Approve">Approve</option>
                  <option value="Reject">Reject</option>
                  <option value="Modify">Modify</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Remark</label>
                <textarea class="form-control" name="l2_remark" rows="2"></textarea>
              </div>
            </div>
          </div>

          {{-- ===== Level 3: HR (3x grid) ===== --}}
          <div class="level-section level-l3 d-none">
            <h6 class="mb-3">Level 3: HR (Human Resources)</h6>
            <div class="row g-3">
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Document Verification</label>
                <select class="form-select" name="l3_docs">
                  <option>Valid</option><option>Invalid</option><option>Not Submitted</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Policy Compliance (HR Rules)</label>
                <select class="form-select" name="l3_policy">
                  <option>Compliant</option><option>Not Compliant</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">System Update</label>
                <select class="form-select" name="l3_system">
                  <option>Updated</option><option>Pending</option>
                </select>
              </div>

              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Payroll Coordination</label>
                <select class="form-select" name="l3_payroll">
                  <option>Shared with Finance</option><option>Not Applicable</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Legal Compliance (UAE Labour Law)</label>
                <select class="form-select" name="l3_legal">
                  <option>Compliant</option><option>Not Compliant</option>
                </select>
              </div>
              <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label">Final Approval</label>
                <select class="form-select" name="l3_decision" required>
                  <option value="Approve">Approve</option><option value="Reject">Reject</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label">Remark</label>
                <textarea class="form-control" name="l3_remark" rows="2"></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-outline-info text-dark d-inline-flex align-items-center gap-2">
            <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
            Submit
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var modalEl = document.getElementById('approvalActionModal');
  if (!modalEl) return;

  modalEl.addEventListener('show.bs.modal', function (e) {
    var btn  = e.relatedTarget;
    if (!btn) return;

    var id   = btn.getAttribute('data-leave-id') || '';
    var step = btn.getAttribute('data-step-id')  || '';
    var role = (btn.getAttribute('data-role') || '').toLowerCase();
    var appr = (btn.getAttribute('data-approver') || '');

    var f1 = document.getElementById('act_leave_id');
    var f2 = document.getElementById('act_step_id');
    var f3 = document.getElementById('act_role');

    if (f1) f1.value = id;
    if (f2) f2.value = step;
    if (f3) f3.value = role;

    var hdr = document.getElementById('act_header');
    if (hdr) {
      hdr.style.display = 'block';
      hdr.innerHTML = '<strong>Step for:</strong> ' + (appr || '—')
        + ' &nbsp; | &nbsp; <strong>Role:</strong> ' + (btn.getAttribute('data-role') || '—')
        + ' &nbsp; | &nbsp; <strong>Leave #</strong> ' + (id || '—');
    }

    // hide all role sections
    var secs = document.querySelectorAll('.level-section');
    for (var i = 0; i < secs.length; i++) secs[i].classList.add('d-none');

    // show matched role
    if (role.indexOf('report') !== -1) {
      var l1 = document.querySelector('.level-l1'); if (l1) l1.classList.remove('d-none');
    } else if (role.indexOf('finance') !== -1) {
      var l2 = document.querySelector('.level-l2'); if (l2) l2.classList.remove('d-none');
    } else {
      var l3 = document.querySelector('.level-l3'); if (l3) l3.classList.remove('d-none');
    }
  });

  // Optional: hard safety close if something goes wrong
  modalEl.addEventListener('hidden.bs.modal', function () {
    // remove stray backdrops if any (defensive)
    var bds = document.querySelectorAll('.modal-backdrop');
    if (bds.length > 1) {
      for (var i=1; i<bds.length; i++) bds[i].parentNode.removeChild(bds[i]);
    }
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
  });
});
</script>
@endpush

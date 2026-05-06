@extends('backEnd.newmasterpage')
@section('mainContent')
@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    $isEdit = isset($loan);
@endphp

<div class="content-container col-12">
  <div class="tab-content display-flex-tabs" id="loanTabContent">

    <form id="loanForm"
      action="{{ $isEdit ? route('employee.loans.update', $loan->id) : route('employee.loans.store') }}"
      method="POST" enctype="multipart/form-data">
      @csrf
      @if($isEdit) @method('PUT') @endif

      <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
          {{ $isEdit ? 'Edit Loan / Advance Application' : 'Loan / Advance Application Form' }}
        </h4>

        <div class="purchase-order-content-header-right">
          <button type="button" class="btn btn-light text-dark d-inline-flex align-items-center gap-2"
                  id="btnSaveAllCompany" data-busy-text="{{ $isEdit ? 'Updating...' : 'Saving...' }}">
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            <i class="ico icon-outline-bookmark-opened text-success btn-icon"></i>
            <span class="btn-text">{{ $isEdit ? 'Save' : 'Apply' }}</span>
          </button>
          <a class="btn btn-light" href="{{ url('employee/loans') }}">My Loans</a>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-body">

          {{-- Top row (Employee Info) --}}
          <div class="row row-cols-1 row-cols-lg-5 g-3 mb-3">
            <div class="col">
              <label class="form-label">Employee Name</label>
              <input type="text" class="form-control form-control-sm" readonly
                     value="{{ Auth::user()->full_name ?? Auth::user()->name }}">
            </div>

            <div class="col">
              <label class="form-label">Department</label>
              <input type="text" class="form-control form-control-sm" readonly
                     value="{{ Auth::user()->staff->departments->name ?? '' }}">
            </div>

            <div class="col">
              <label class="form-label">Designation</label>
              <input type="text" class="form-control form-control-sm" readonly
                     value="{{ Auth::user()->staff->designations->title ?? '' }}">
            </div>

            <div class="col">
              <label class="form-label">Request Type <span class="text-danger">*</span></label>
 <select name="type_id" class="form-select form-select-sm" required>
    <option value="">-- Select --</option>
    <option value="1" 
        {{ old('type_id', $isEdit ? $loan->type_id : 1) == 1 ? 'selected' : '' }}>
        Loan
    </option>
    <option value="2" 
        {{ old('type_id', $isEdit ? $loan->type_id : 1) == 2 ? 'selected' : '' }}>
        Salary Advance
    </option>
    <option value="3" 
        {{ old('type_id', $isEdit ? $loan->type_id : 1) == 3 ? 'selected' : '' }}>
        Emergency Advance
    </option>
    <option value="4" 
        {{ old('type_id', $isEdit ? $loan->type_id : 1) == 4 ? 'selected' : '' }}>
        Travel Advance
    </option>
    <option value="5" 
        {{ old('type_id', $isEdit ? $loan->type_id : 1) == 5 ? 'selected' : '' }}>
        Other
    </option>
</select>
            </div>

            <div class="col">
              <label class="form-label">Amount Requested (AED) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" name="amount" class="form-control form-control-sm"
                     value="{{ old('amount', $isEdit ? $loan->amount : '') }}" required>
            </div>
          </div>

          {{-- Second row (Installments, per month, repayment start, repayment mode) --}}
          <div class="row row-cols-1 row-cols-lg-5 g-3 mb-3">
            <div class="col">
              <label class="form-label">Installment Number <span class="text-danger">*</span></label>
              <input type="number" min="1" name="installments" id="installments"
                     class="form-control form-control-sm"
                     value="{{ old('installments', $isEdit ? $loan->installments : '') }}" required>
            </div>

            <div class="col">
              <label class="form-label">Amount Per Month (AED)</label>
              <input type="number" step="0.01" name="amount_per_month" id="amount_per_month"
                     class="form-control form-control-sm" readonly
                     value="{{ old('amount_per_month', $isEdit ? $loan->amount_per_month : '') }}">
            </div>

            <div class="col">
              <label class="form-label">Repayment Start Month <span class="text-danger">*</span></label>
              <input type="month" name="repayment_start" class="form-control form-control-sm"
                     value="{{ old('repayment_start', $isEdit ? $loan->repayment_start : '') }}" required>
            </div>

            <div class="col">
              <label class="form-label">Repayment Mode <span class="text-danger">*</span></label>
              <select name="repayment_mode" class="form-select form-select-sm" required>
                <option value="">-- Select --</option>
                <option value="Salary Deduction" {{ old('repayment_mode', $isEdit ? $loan->repayment_mode : '') == 'Salary Deduction' ? 'selected' : '' }} selected>Salary Deduction</option>
                <option value="Direct Payment" {{ old('repayment_mode', $isEdit ? $loan->repayment_mode : '') == 'Direct Payment' ? 'selected' : '' }}>Direct Payment</option>
                <option value="Adjustment" {{ old('repayment_mode', $isEdit ? $loan->repayment_mode : '') == 'Adjustment' ? 'selected' : '' }}>Adjustment</option>
              </select>
            </div>

            <div class="col">
              <label class="form-label">Supporting Documents</label>
              <input type="file" name="attachment" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
              @if($isEdit && !empty($loan->attachment))
                <small class="d-block mt-1">
                  Current: <a href="{{ asset('uploads/loan_docs/'.$loan->attachment) }}" target="_blank">View File</a>
                </small>
              @endif
            </div>
          </div>

          {{-- Purpose / Reason --}}
          <div class="row g-2 mb-3">
            <div class="col-12">
              <label class="form-label">Reason / Purpose <span class="text-danger">*</span></label>
              <textarea name="purpose" rows="3" class="form-control form-control-sm"
                        placeholder="Explain the need (e.g., medical, family, travel, etc.)" required>{{ old('purpose', $isEdit ? $loan->purpose : '') }}</textarea>
            </div>
          </div>

        </div>
      </div>
    </form>

  </div>
</div>

{{-- ===== JS Auto-calculation + Submit spinner ===== --}}
<script>
$(function () {
  var $form  = $('#loanForm');
  var $btn   = $('#btnSaveAllCompany');
  var $msg   = $('#saveAllMsg');
  var $amount = $('input[name="amount"]');
  var $installments = $('input[name="installments"]');
  var $monthly = $('input[name="amount_per_month"]');

  function calcAmount() {
    var total = parseFloat($amount.val()) || 0;
    var inst  = parseInt($installments.val()) || 0;
    if (total > 0 && inst > 0) {
      $monthly.val((total / inst).toFixed(2));
    } else {
      $monthly.val('');
    }
  }

  $amount.on('input', calcAmount);
  $installments.on('input', calcAmount);

  function setError(t){ $msg.html('<span class="text-danger">'+t+'</span>'); }
  function clearError(){ $msg.html(''); }

  function clientValidate() {
    clearError();
    if (!$('select[name="type_id"]').val()) { setError('Select Request Type.'); return false; }
    if (!$amount.val()) { setError('Enter requested amount.'); return false; }
    if (!$installments.val()) { setError('Enter number of installments.'); return false; }
    if (!$('input[name="repayment_start"]').val()) { setError('Select Repayment Start Month.'); return false; }
    if (!$('select[name="repayment_mode"]').val()) { setError('Select Repayment Mode.'); return false; }
    if (!$('textarea[name="purpose"]').val()) { setError('Enter Purpose.'); return false; }
    return true;
  }

  $btn.on('click', function(){
    if (!clientValidate()) return;
    var $icon = $(this).find('.btn-icon');
    var $spin = $(this).find('.spinner-border');
    var $txt  = $(this).find('.btn-text');
    var busy  = $(this).data('busy-text') || 'Saving...';
    $spin.removeClass('d-none');
    $icon.addClass('d-none');
    $txt.text(busy);
    $form.submit();
  });

  calcAmount();
});
</script>
@endsection

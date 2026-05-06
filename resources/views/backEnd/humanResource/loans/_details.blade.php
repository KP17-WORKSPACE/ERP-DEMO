{{-- resources/views/backEnd/employee/loans/_details.blade.php --}}
@php
    use Carbon\Carbon;

    $types = [
        1 => 'Loan',
        2 => 'Salary Advance',
        3 => 'Emergency Advance',
        4 => 'Travel Advance',
        5 => 'Other',
    ];
@endphp
<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left mb-0">
        Loan / Advance Details
    </h4>

    <div class="purchase-order-content-header-right d-flex align-items-center">
        {{-- ✏️ Edit --}}
       <a href="{{ route('employee.loans.edit', $loan->id) }}"
   class="btn btn-light text-dark d-inline-flex align-items-center gap-2 ms-2">
   <i class="ico icon-outline-pen-2 text-success"></i>
   <span class="btn-text fw-semibold">Edit</span>
</a>

        <a href="{{ route('employee.loans.create') }}"
   class="btn btn-light text-dark d-inline-flex align-items-center gap-2 ms-2">
    <i class="ico icon-outline-add-square text-success"></i>
    <span class="btn-text fw-semibold">Request Loan</span>
</a>
    </div>
</div>

<div class="card shadow-sm">


  <div class="card-body small">
    <div class="row g-3 mb-3">
      <div class="col-md-3">
        <strong>ID:</strong>
        <div>LN{{ $loan->id }}</div>
      </div>
      <div class="col-md-3">
        <strong>Type:</strong>
        <div>{{ $types[$loan->type_id] ?? '—' }}</div>
      </div>
      <div class="col-md-3">
        <strong>Amount:</strong>
        <div>₹{{ number_format($loan->amount, 2) }}</div>
      </div>
      <div class="col-md-3">
        <strong>Installments:</strong>
        <div>{{ $loan->installments ?? '—' }}</div>
      </div>

      <div class="col-md-3">
        <strong>Per Month:</strong>
        <div>₹{{ number_format($loan->amount_per_month, 2) }}</div>
      </div>
      <div class="col-md-3">
        <strong>Repayment Start:</strong>
        <div>{{ $loan->repayment_start ? Carbon::parse($loan->repayment_start)->format('M Y') : '—' }}</div>
      </div>
      <div class="col-md-3">
        <strong>Mode:</strong>
        <div>{{ $loan->repayment_mode ?? '—' }}</div>
      </div>
      <div class="col-md-3">
        <strong>Status:</strong>
        <div>{{ $loan->status ?? 'Pending' }}</div>
      </div>

      <div class="col-md-12">
        <strong>Purpose:</strong>
        <div>{{ $loan->purpose ?? '—' }}</div>
      </div>

      @if($loan->attachment)
        <div class="col-md-12">
          <strong>Attachment:</strong>
          <div>
            <a href="{{ asset('uploads/loan_docs/'.$loan->attachment) }}"
               target="_blank"
               class="text-primary text-decoration-underline">
              View File
            </a>
          </div>
        </div>
      @endif
    </div>

    <hr>

    <div class="text-muted small">
      Applied on {{ optional($loan->created_at)->format('d M Y') }}
    </div>
  </div>
</div>

@php
    $role = Auth::user()->role_id;
    $isSuperAdmin = ($role == 1 || $role == 2);

    $dept_status = $selectedReimbursement->dept_head_status;
    $finance_status = $selectedReimbursement->acco_head_status;
    $payment_status = $selectedReimbursement->accounts_status;

    // Reporting Manager Gating
    $canEditDept = false;
    if ($isSuperAdmin) {
        $canEditDept = true;
    } else if ($role == 8 && $dept_status != 1) {
        $canEditDept = true; // can edit if not approved yet
    }

    // Finance Gating
    $canEditFinance = false;
    if ($dept_status == 1) {
        if ($isSuperAdmin || ($role == 27 && $finance_status != 1)) {
            $canEditFinance = true;
        }
    }

    // Payment Processing Gating
    $canEditPayment = false;
    if ($finance_status == 1) {
        if ($isSuperAdmin || ($role == 28 && $payment_status != 1)) {
            $canEditPayment = true;
        }
    }

    $chartOfAccounts = \App\SysChartofAccounts::where('status', 1)->get();
@endphp

<style>
    .header-height { height: 1rem; }
    .bg-lightgreen { background-color: #deebe1 !important; }
</style>

<div class="row">
    <!-- Reporting Manager Approval -->
    <div class="col p-1">
        <div class="card mb-3">
            <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                @php
                    if ($dept_status == 1){
                        $dept_status_class = "bg-success text-white";
                    } else if ($dept_status == 2){
                        $dept_status_class = "bg-danger text-white";
                    } else {
                        $dept_status_class = "bg-lightgreen text-dark";
                    }
                @endphp
                <tr>
                    <td class="{{ $dept_status_class }} d-flex align-items-center justify-content-start gap-1" style="height:23px; padding: 0 15px;">
                        <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1 header-height">
                            <b>Reporting Manager</b>
                            @if($canEditDept)
                                <a class="btn-md light" style="display: contents; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalReportingManager">
                                    <i class="ico icon-outline-pen-new-square title-15 {{ $dept_status_class == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" title="Reporting Manager Approval" style="font-size: 12px"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 10px 15px;">
                        <span class="fw-bold">Status</span> : 
                        @if ($dept_status == 1)
                            Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                        @elseif($dept_status == 2)
                            Disapproved <i class="ico icon-outline-close text-danger"></i>
                        @else
                            Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        @endif
                    </td>
                </tr>
                @if($selectedReimbursement->dept_head_date)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Approval Date</span> : {{ date('d/m/Y', strtotime($selectedReimbursement->dept_head_date)) }}
                    </td>
                </tr>
                @endif
                @if($selectedReimbursement->deptheadby)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Approved By</span> : {{ $selectedReimbursement->deptheadby->full_name }}
                    </td>
                </tr>
                @endif
                @if($selectedReimbursement->dept_head_remarks)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Remarks</span> : {{ $selectedReimbursement->dept_head_remarks }}
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Finance Approval -->
    <div class="col p-1">
        <div class="card mb-3">
            <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                @php
                    if ($finance_status == 1){
                        $finance_status_class = "bg-success text-white";
                    } else if ($finance_status == 2){
                        $finance_status_class = "bg-danger text-white";
                    } else {
                        $finance_status_class = "bg-lightgreen text-dark";
                    }
                @endphp
                <tr>
                    <td class="{{ $finance_status_class }} d-flex align-items-center justify-content-start gap-1" style="height:23px; padding: 0 15px;">
                        <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1 header-height">
                            <b>Finance</b>
                            @if($canEditFinance)
                                <a class="btn-md light" style="display: contents; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalFinance">
                                    <i class="ico icon-outline-pen-new-square title-15 {{ $finance_status_class == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" title="Finance Approval" style="font-size: 12px"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 10px 15px;">
                        <span class="fw-bold">Status</span> : 
                        @if ($finance_status == 1)
                            Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                        @elseif($finance_status == 2)
                            Disapproved <i class="ico icon-outline-close text-danger"></i>
                        @else
                            Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        @endif
                    </td>
                </tr>
                @if($selectedReimbursement->acco_head_approved_amount)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Approved Amount</span> : {{ number_format($selectedReimbursement->acco_head_approved_amount, 2) }}
                    </td>
                </tr>
                @endif
                @if($selectedReimbursement->accoheadby)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Approved By</span> : {{ $selectedReimbursement->accoheadby->full_name }}
                    </td>
                </tr>
                @endif
                @if($selectedReimbursement->acco_head_remarks)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Remarks</span> : {{ $selectedReimbursement->acco_head_remarks }}
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Payment Processing Approval -->
    <div class="col p-1">
        <div class="card mb-3">
            <table class="detail-item-table-sm" width="100%" style="table-layout: fixed;width:100%">
                @php
                    if ($payment_status == 1){
                        $accounts_status_class = "bg-success text-white";
                    } else if ($payment_status == 2){
                        $accounts_status_class = "bg-danger text-white";
                    } else {
                        $accounts_status_class = "bg-lightgreen text-dark";
                    }
                @endphp
                <tr>
                    <td class="{{ $accounts_status_class }} d-flex align-items-center justify-content-start gap-1" style="height:23px; padding: 0 15px;">
                        <div class="d-flex align-items-center justify-content-start flex-grow-1 gap-1 header-height">
                            <b>Payment Processing</b>
                            @if($canEditPayment)
                                <a class="btn-md light" style="display: contents; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalPaymentProcessing">
                                    <i class="ico icon-outline-pen-new-square title-15 {{ $accounts_status_class == 'bg-lightgreen text-dark' ? 'text-dark' : 'text-white' }}" title="Payment Processing Approval" style="font-size: 12px"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 10px 15px;">
                        <span class="fw-bold">Status</span> : 
                        @if ($payment_status == 1)
                            Approved <i class="ico icon-outline-check-read title-15 text-success"></i>
                        @elseif($payment_status == 2)
                            Disapproved <i class="ico icon-outline-close text-danger"></i>
                        @else
                            Pending <i class="ico icon-outline-clock-circle text-info"></i>
                        @endif
                    </td>
                </tr>
                @if($selectedReimbursement->accounts_paid_amount)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Paid Amount</span> : {{ number_format($selectedReimbursement->accounts_paid_amount, 2) }}
                    </td>
                </tr>
                @endif
                @if($selectedReimbursement->accounts_payment_voucher_no)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Voucher No</span> : {{ $selectedReimbursement->accounts_payment_voucher_no }}
                    </td>
                </tr>
                @endif
                @if($selectedReimbursement->accountsby)
                <tr>
                    <td class="text-start truncate-text-custom" style="padding: 5px 15px;">
                        <span class="fw-bold">Paid By</span> : {{ $selectedReimbursement->accountsby->full_name }}
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal side-panel fade" id="modalReportingManager" data-bs-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reporting Manager Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('crm-reimbursement-request-dept-head-approve') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="dept_head_re_id" value="{{ $selectedReimbursement->id }}">
                    <div class="form-group mb-3">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="btn_status" class="form-control" required>
                            <option value="">-Select-</option>
                            <option value="1" {{ $dept_status == 1 ? 'selected' : '' }}>Approve</option>
                            <option value="2" {{ $dept_status == 2 ? 'selected' : '' }}>Reject</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Approval Date</label>
                        <input type="text" name="dept_head_date" class="form-control date form-control-sm" placeholder="DD/MM/YYYY" value="{{ $selectedReimbursement->dept_head_date ? date('d/m/Y', strtotime($selectedReimbursement->dept_head_date)) : date('d/m/Y') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3">{{ $selectedReimbursement->dept_head_remarks }}</textarea>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmit">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal side-panel fade" id="modalFinance" data-bs-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Finance Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('crm-reimbursement-request-accounts-head-approve') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="acco_head_re_id" value="{{ $selectedReimbursement->id }}">
                    <div class="form-group mb-3">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="btn_status" class="form-control" required>
                            <option value="">-Select-</option>
                            <option value="1" {{ $finance_status == 1 ? 'selected' : '' }}>Approve</option>
                            <option value="2" {{ $finance_status == 2 ? 'selected' : '' }}>Reject</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Approved Amount</label>
                        <input type="number" step="0.01" name="acco_head_approved_amount" class="form-control" value="{{ $selectedReimbursement->acco_head_approved_amount ?? $selectedReimbursement->amount }}">
                    </div>
                    <div class="form-group mb-3">
                        <label>Account</label>
                        <select name="acco_head_account_id" class="form-control">
                            <option value="">-Select-</option>
                            @foreach($chartOfAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedReimbursement->acco_head_account_id == $acc->id ? 'selected' : '' }}>{{ $acc->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Payment Required</label>
                        <select name="acco_head_payment_required" class="form-control">
                            <option value="" {{ empty($selectedReimbursement->acco_head_payment_required) ? 'selected' : '' }}>-Select-</option>
                            <option value="Yes" {{ $selectedReimbursement->acco_head_payment_required == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ $selectedReimbursement->acco_head_payment_required == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3">{{ $selectedReimbursement->acco_head_remarks }}</textarea>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmit">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal side-panel fade" id="modalPaymentProcessing" data-bs-backdrop="false" tabindex="-1" role="dialog" aria-hidden="true" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Processing Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('crm-reimbursement-request-account-approve') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="account_re_id" value="{{ $selectedReimbursement->id }}">
                    <div class="form-group mb-3">
                        <label>Status <span class="text-danger">*</span></label>
                        <select name="btn_status" class="form-control" required>
                            <option value="">-Select-</option>
                            <option value="1" {{ $payment_status == 1 ? 'selected' : '' }}>Approve</option>
                            <option value="2" {{ $payment_status == 2 ? 'selected' : '' }}>Reject</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Payment Voucher No.</label>
                        <input type="text" name="accounts_payment_voucher_no" class="form-control" value="{{ $selectedReimbursement->accounts_payment_voucher_no }}">
                    </div>
                    <div class="form-group mb-3">
                        <label>Payment Date</label>
                        <input type="text" name="accounts_payment_date" class="form-control date form-control-sm" placeholder="DD/MM/YYYY" value="{{ $selectedReimbursement->accounts_payment_date ? date('d/m/Y', strtotime($selectedReimbursement->accounts_payment_date)) : date('d/m/Y') }}">
                    </div>
                    <div class="form-group mb-3">
                        <label>Payment Method</label>
                        <select name="accounts_payment_method" class="form-control">
                            <option value="" {{ empty($selectedReimbursement->accounts_payment_method) ? 'selected' : '' }}>-Select-</option>
                            <option value="Cash" {{ $selectedReimbursement->accounts_payment_method == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Bank Transfer" {{ $selectedReimbursement->accounts_payment_method == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="Cheque" {{ $selectedReimbursement->accounts_payment_method == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Bank Account</label>
                        <select name="accounts_bank_account_id" class="form-control">
                            <option value="">-Select-</option>
                            @foreach($chartOfAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedReimbursement->accounts_bank_account_id == $acc->id ? 'selected' : '' }}>{{ $acc->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Paid Amount</label>
                        <input type="number" step="0.01" name="accounts_paid_amount" class="form-control" value="{{ $selectedReimbursement->accounts_paid_amount ?? $selectedReimbursement->acco_head_approved_amount }}">
                    </div>
                    <div class="form-group mb-3">
                        <label>Payment Status</label>
                        <select name="accounts_payment_status" class="form-control">
                            <option value="" {{ empty($selectedReimbursement->accounts_payment_status) ? 'selected' : '' }}>-Select-</option>
                            <option value="Pending" {{ $selectedReimbursement->accounts_payment_status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Paid" {{ $selectedReimbursement->accounts_payment_status == 'Paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Payment Reference</label>
                        <input type="text" name="accounts_payment_reference" class="form-control" value="{{ $selectedReimbursement->accounts_payment_reference }}">
                    </div>
                    <div class="form-group mb-3">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3">{{ $selectedReimbursement->accounts_remarks }}</textarea>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmit">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

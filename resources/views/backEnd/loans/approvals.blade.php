@extends('backEnd.newmasterpage')
@section('mainContent')
@php
    use Illuminate\Support\Str;
    $auth = Auth::user();
    $types = [1=>'Loan',2=>'Salary Advance',3=>'Emergency Advance',4=>'Travel Advance',5=>'Other'];
    $trackPermissions = $trackPermissions ?? ['create' => true, 'view' => true, 'edit' => true, 'delete' => true, 'export' => true, 'attach' => true];
@endphp
<style>
  #filters-long .loan-list-toolbar {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    flex-wrap: wrap;
  }
  #filters-long .loan-list-toolbar .loan-list-search {
    width: 320px;
    flex: 0 0 320px;
    font-size: 13px;
    height: 32px;
  }
  #filters-long .loan-list-toolbar .btn,
  #filters-long .loan-list-toolbar .list_style_expand_btn,
  #filters-long .loan-list-toolbar .list_style_search_btn {
    position: static;
    top: auto;
    right: auto;
    margin-right: 0 !important;
  }
  #filters-short .search-filter-container {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  #filters-short .search-filter-container .input-group {
    flex: 1 1 auto;
    min-width: 0;
  }
  #filters-short .search-filter-container .list_style_expand_btn {
    position: static;
    top: auto;
    right: auto;
    flex: 0 0 auto;
  }
  #long-list .truncate-cell {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  @media (max-width: 992px) {
    #filters-long .loan-list-toolbar {
      justify-content: flex-start;
    }
    #filters-long .loan-list-toolbar .loan-list-search {
      width: 100%;
      flex: 1 1 280px;
    }
  }
</style>

<script>
  function setLoanView(mode) {
    const leftNav = document.getElementById('leftSidebar');
    const content = document.querySelector('.content-container');
    const shortList = document.getElementById('loanShortList');
    const longTable = document.getElementById('long-list');
    const filtersShort = document.getElementById('filters-short');
    const filtersLong  = document.getElementById('filters-long');

    if (mode === 'full') {
      if (leftNav.classList.contains('col-3')) {
        leftNav.classList.remove('col-3');
        leftNav.classList.add('col-12');
      }
      leftNav.style.width = '100%';
      content.classList.add('d-none');

      longTable?.classList.remove('d-none');
      shortList?.classList.add('d-none');
      filtersLong?.classList.remove('d-none');
      filtersShort?.classList.add('d-none');

      leftNav.dataset.view = 'full';
    } else {
      if (leftNav.classList.contains('col-12')) {
        leftNav.classList.remove('col-12');
        leftNav.classList.add('col-3');
      }
      leftNav.style.width = '';
      content.classList.remove('d-none');

      longTable?.classList.add('d-none');
      shortList?.classList.remove('d-none');
      filtersShort?.classList.remove('d-none');
      filtersLong?.classList.add('d-none');

      leftNav.dataset.view = 'compact';
    }
  }

  function list_style_new() {
    const leftNav = document.getElementById('leftSidebar');
    const cur = leftNav.dataset.view || 'compact';
    setLoanView(cur === 'compact' ? 'full' : 'compact');
  }

  document.addEventListener('DOMContentLoaded', function() {
    const leftNav = document.getElementById('leftSidebar');
    if (!leftNav.dataset.view) leftNav.dataset.view = 'compact';
  });
</script>

<?php
$auth = Auth::user();
$types = [1=>'Loan',2=>'Salary Advance',3=>'Emergency Advance',4=>'Travel Advance',5=>'Other'];
$permissions = App\SmRolePermission::where('role_id', $auth->role_id)->get();
?>

<?php try { ?>

<aside class="left-nav col-3" id="leftSidebar">
  <div class="resizer" id="sidebarResizer"></div>

  {{-- SHORT (Compact) --}}
  <div class="short-list" id="filters-short">
    <h4 class="mb-2">Loan Track</h4>

    {{ Form::open(['class'=>'form-horizontal','method'=>'get','url'=>route('employee.loans.approvals'),'id'=>'loan-search']) }}
      <div class="search-filter-container mb-4" id="short-list">
        <div class="input-group flex-nowrap">
          <input type="text" name="q" class="form-control"
                 placeholder="Request No / Employee / Purpose"
                 value="{{ request('q') ?? '' }}">
        </div>
        <button type="button" class="btn btn-light list_style_expand_btn" id="list_style_button" onclick="list_style_new()">
          <i class="ico icon-outline-list-down"></i>
        </button>
      </div>
    {{ Form::close() }}
  </div>

  {{-- LONG (Full Filter) --}}
  <div class="long-list d-none" id="filters-long">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h4 class="mb-0">Loan Track</h4>
      <div class="search-filter-container mb-0 loan-list-toolbar">
        <input type="text" id="tableSearch" class="form-control loan-list-search" placeholder="Search">
        @if(!empty($trackPermissions['export']))
        <a href="{{ route('employee.loans.export', array_merge(request()->query(), ['source' => 'track'])) }}" class="btn btn-light list_style_search_btn">
          <i class="ico icon-outline-export text-success"></i> Export
        </a>
        @endif
        <button type="button" class="btn btn-light list_style_search_btn" onclick="document.getElementById('long-filters-box').classList.toggle('d-none')" title="Search / Filter">
          <i class="ico icon-outline-magnifer"></i>
        </button>
        @if($active_id)
          <a href="{{ url('employee/loans?active=' . $active_id) }}" target="_blank" class="btn btn-light text-dark">
            <i class="ico icon-outline-eye text-success"></i> View
          </a>
        @else
          <a href="javascript:void(0)" onclick="alert('Please select a loan request to view.')" class="btn btn-light text-dark">
            <i class="ico icon-outline-eye text-success"></i> View
          </a>
        @endif
        <button type="button" class="btn btn-light list_style_expand_btn" id="list_style_button" onclick="list_style_new()" title="Compact list">
          <i class="ico icon-outline-list-down"></i>
        </button>
      </div>
    </div>

    <div id="long-filters-box" class="search-filter-container mt-1 mb-4 filter-field d-none border">
      <div class="card">
        <div class="card-body">
          {{ Form::open(['class'=>'form-horizontal','method'=>'get','url'=>route('employee.loans.approvals'),'id'=>'loan-filter']) }}
            <div class="row">
              <div class="col-md-4 mb-2">
                <label class="form-label">Status</label>
                <select class="form-control" name="status">
                  <option value="">All</option>
                  @foreach (['Pending','Approved','Rejected'] as $st)
                    <option value="{{ $st }}" {{ request('status')===$st?'selected':'' }}>{{ $st }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">Type</label>
                <select class="form-control" name="type_id">
                  <option value="">All</option>
                  @foreach ($types as $k=>$v)
                    <option value="{{ $k }}" {{ request('type_id')==$k?'selected':'' }}>{{ $v }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4 mb-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-success w-100">Filter</button>
              </div>
            </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>

  {{-- LEFT NAV LIST (Short) --}}
  <div class="left-nav-list">
    <ul id="loanShortList" class="nav flex-column nav-pills" role="tablist">
      @forelse($loans as $loan)
        <li class="nav-item w-100" role="presentation">
          <button class="nav-link lv-item data-item {{ (isset($active_id) && $active_id == $loan->id) ? 'active' : '' }}"
                  data-id="{{ $loan->id }}" type="button" role="tab">
            <div class="row w-100">
                <div class="col-12">
                    <label class="form-control-plaintext truncate-text">
                        {{ optional($loan->staffDetails)->full_name ?: 'N/A' }}
                    </label>
                </div>
                <div class="col-4">
                    <div class="form-control-plaintext" style="font-size:11px">{{ $loan->document_number }}</div>
                </div>
                <div class="col-4 pl-2">
                    <div class="form-control-plaintext truncate-text" style="font-size:11px">
                        {{ optional($loan->created_at)->format('d/m/Y') }}</div>
                </div>
                <div class="col-4 text-end">
                    <div class="form-control-plaintext truncate-text" style="font-size:11px">
                        {{ number_format((float)$loan->amount, 2) }}
                    </div>
                </div>
            </div>
          </button>
        </li>
      @empty
        <div class="p-3 text-muted">No pending approvals</div>
      @endforelse
    </ul>

    {{-- LONG LIST TABLE --}}
    <div id="long-list" class="d-none">
        <style>
          .loan-table-wrapper {
            max-height: calc(100vh - 160px);
            overflow: auto;
          }
          .loan-table-wrapper thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #deebe1 !important;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
            white-space: nowrap !important;
            vertical-align: middle;
            line-height: 1.2;
            padding: 7px 4px !important;
            overflow: hidden;
            text-overflow: ellipsis;
          }
          .loan-table-wrapper tbody td {
            vertical-align: middle;
            white-space: nowrap;
            word-break: normal;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-left: 4px !important;
            padding-right: 4px !important;
          }
          .loan-table-wrapper .loan-action-buttons {
            gap: 3px;
            min-width: 78px;
          }
          .loan-table-wrapper .loan-action-buttons .btn {
            width: 24px;
            height: 24px;
            padding: 2px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 24px;
          }
          .loan-table-wrapper .badge {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
          }
        </style>
        <div class="table-responsive mb-4 mt-4 loan-table-wrapper">
        <table class="table table-hover mt-0 data-table" style="table-layout: fixed;width:100%">
          <thead>
            <tr>
              <th style="width: 5.5%;" title="Date">Date</th>
              <th style="width: 6.5%;" title="Doc No">Doc No</th>
              <th style="width: 8.5%;" title="Employee Name">Employee Name</th>
              <th style="width: 6.5%;" title="Request Type">Request Type</th>
              <th style="width: 7%;" title="Loan Category">Loan Category</th>
              <th style="width: 7%;" class="text-end" title="Amount Requested">Amount Requested</th>
              <th style="width: 6.5%;" class="text-end" title="Installment Number">Installment Number</th>
              <th style="width: 8%;" class="text-end" title="Monthly Deduction Amount">Monthly Deduction Amount</th>
              <th style="width: 7%;" title="Repayment Start Month">Repayment Start Month</th>
              <th style="width: 7%;" title="Repayment Mode">Repayment Mode</th>
              <th style="width: 6.5%;" title="Disbursement Date">Disbursement Date</th>
              <th style="width: 9%;" title="Purpose">Purpose</th>
              <th style="width: 8%;" title="Guarantor Employee">Guarantor Employee</th>
              <th style="width: 6%;" title="Status">Status</th>
              <th style="width: 7%;" class="text-center" title="Actions">Actions</th>
            </tr>
          </thead>
          <tbody style="font-size:12px">
            @forelse($loans as $loan)
              @php
                $staff = $loan->staffDetails ?? $loan->staff;
                $guarantor = $loan->guarantor_employee_id ? \App\SmStaff::find($loan->guarantor_employee_id) : null;
                $amount = (float) $loan->amount;
                $monthly = (float) $loan->amount_per_month;
                if ($loan->status === 'Rejected' || in_array('Rejected', [$loan->manager_approval, $loan->finance_approval, $loan->hr_approval, $loan->management_approval, $loan->payment_approval], true)) {
                  $workflowStatus = 'Rejected';
                } elseif (($loan->manager_approval ?: 'Pending') !== 'Approved') {
                  $workflowStatus = 'Pending';
                } elseif (($loan->finance_approval ?: 'Pending') !== 'Approved') {
                  $workflowStatus = 'Pending Finance';
                } elseif (($loan->hr_approval ?: 'Pending') !== 'Approved') {
                  $workflowStatus = 'Pending HR';
                } elseif ($loan->status === 'Pending' && (($loan->management_approval ?: 'Pending') !== 'Approved')) {
                  $workflowStatus = 'Pending Management';
                } else {
                  $workflowStatus = 'Approved';
                }
                $statusBadgeClass = 'badge bg-warning';
                if ($workflowStatus === 'Approved') $statusBadgeClass = 'badge bg-success';
                elseif ($workflowStatus === 'Rejected') $statusBadgeClass = 'badge bg-danger';
              @endphp
              <tr>
                <td class="text-center">{{ optional($loan->created_at)->format('d/m/Y') ?: '-' }}</td>
                <td class="text-center lv-item" data-id="{{ $loan->id }}" onclick="list_style_new()">
                  <a class="text-success" style="cursor:pointer">{{ $loan->document_number }}</a>
                </td>
                <td class="truncate-cell" title="{{ optional($staff)->full_name }}">{{ optional($staff)->full_name ?: optional($staff)->first_name ?: '-' }}</td>
                <td title="{{ $loan->request_type ?: ($types[$loan->type_id] ?? '-') }}">{{ $loan->request_type ?: ($types[$loan->type_id] ?? '-') }}</td>
                <td title="{{ $loan->loan_category ?: ($types[$loan->type_id] ?? '-') }}">{{ $loan->loan_category ?: ($types[$loan->type_id] ?? '-') }}</td>
                <td class="text-end">{{ number_format($amount, 2) }}</td>
                <td class="text-end">{{ $loan->installments ?: '-' }}</td>
                <td class="text-end">{{ number_format($monthly, 2) }}</td>
                <td title="{{ $loan->repayment_start ? \Carbon\Carbon::parse($loan->repayment_start)->format('M Y') : '-' }}">{{ $loan->repayment_start ? \Carbon\Carbon::parse($loan->repayment_start)->format('M Y') : '-' }}</td>
                <td title="{{ $loan->repayment_mode ?: '-' }}">{{ $loan->repayment_mode ?: '-' }}</td>
                <td class="text-center">{{ $loan->requested_disbursement_date ? date('d/m/Y', strtotime($loan->requested_disbursement_date)) : '-' }}</td>
                <td class="truncate-cell" title="{{ $loan->purpose }}">{{ $loan->purpose ?: '-' }}</td>
                <td class="truncate-cell" title="{{ optional($guarantor)->full_name }}">{{ optional($guarantor)->full_name ?: '-' }}</td>
                <td class="text-center" title="{{ $workflowStatus }}"><span class="{{ $statusBadgeClass }}">{{ $workflowStatus }}</span></td>
                <td class="text-center align-middle">
                  <div class="d-flex justify-content-center align-items-center loan-action-buttons flex-nowrap">
                    @if(!empty($loan->attachment))
                        <a href="{{ asset('public/uploads/loan_docs/'.$loan->attachment) }}" target="_blank" download class="btn btn-sm btn-light" title="Download Attachment">
                            <i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i>
                        </a>
                    @else
                        -
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="15" class="text-center text-muted p-4">No loan track requests found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-3">{{ $loans->links() }}</div>
    </div>
  </div>
</aside>

{{-- RIGHT PANEL --}}
<div class="content-container col-9">
  <div id="loanTabContent" class="tab-content display-flex-tabs">
    <div class="p-4 text-center text-muted">
      Select a loan request to view details
    </div>
  </div>
</div>

<script>
$(function(){
  const detailTpl = @json(route('employee.loans.detail', [':id']));
  const $detail = $('#loanTabContent');
  function buildUrl(id){ return detailTpl.replace(':id', encodeURIComponent(id)); }

  function loadDetail(id){
    if(!id) return;
    $detail.html('<div class="p-5 text-muted text-center">Loading details...</div>');
    $.get(buildUrl(id), function(html){
      if (html && $.trim(html).length) {
        $detail.html(html);
      } else {
        $detail.html('<p class="text-danger p-3">No details found.</p>');
      }
    }).fail(function(){
      $detail.html('<p class="text-danger p-3">Failed to load details.</p>');
    });
  }

  $(document).on('click', '.lv-item', function(){
    $('.lv-item').removeClass('active');
    $(this).addClass('active');
    loadDetail($(this).data('id'));
  });

  const first = $('.lv-item').first();
  if(first.length){ loadDetail(first.data('id')); }

  $('#tableSearch').on('input', function () {
    var needle = (this.value || '').toLowerCase();
    $('#long-list tbody tr').each(function () {
      $(this).toggle($(this).text().toLowerCase().indexOf(needle) !== -1);
    });
  });
});
</script>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php } ?>
@endsection


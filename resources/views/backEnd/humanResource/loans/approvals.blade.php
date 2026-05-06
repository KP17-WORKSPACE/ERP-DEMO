@extends('backEnd.newmasterpage')
@section('mainContent')
@php
    use Illuminate\Support\Str;
    $auth = Auth::user();
    $types = [1=>'Loan',2=>'Salary Advance',3=>'Emergency Advance',4=>'Travel Advance',5=>'Other'];
@endphp

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
    <h4 class="mb-2">Loan Approvals</h4>

    {{ Form::open(['class'=>'form-horizontal','method'=>'get','url'=>route('employee.loans.approvals'),'id'=>'loan-search']) }}
      <div class="search-filter-container mb-4 d-flex">
        <div class="input-group flex-nowrap">
          <input type="text" name="q" class="form-control"
                 placeholder="Search by ID / Purpose"
                 value="{{ request('q') ?? '' }}">
        </div>
        <button type="submit" class="btn btn-light ms-2">
          <i class="ico icon-outline-magnifer"></i>
        </button>
        <button type="button" class="btn btn-light ms-2" id="list_style_button" onclick="list_style_new()">
          <i class="ico icon-outline-list-down"></i>
        </button>
      </div>
    {{ Form::close() }}
  </div>

  {{-- LONG (Full Filter) --}}
  <div class="long-list d-none" id="filters-long">
    <div class="d-flex align-items-center justify-content-between">
      <h4 class="mb-2">All Loan Requests</h4>
      <div class="search-filter-container mb-0">
        <button class="btn btn-light" onclick="document.getElementById('long-filters-box').classList.toggle('d-none')">
          <i class="ico icon-outline-magnifer"></i>
        </button>
        <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
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
          <button class="nav-link lv-item {{ $loop->first ? 'active' : '' }}"
                  data-id="{{ $loan->id }}" type="button" role="tab">
            <div class="row w-100 align-items-center">
              
              <div class="col-10 ps-2">
                <div class="d-flex justify-content-between">
                  <span class="form-control-plaintext fw-semibold">{{ Str::limit($loan->purpose, 24) }}</span>
                  <span class="badge fw-semibold">{{ $loan->status }}</span>
                </div>
                <div class="xsmall text-muted d-flex justify-content-between mt-1">
                  <span>#LN{{ $loan->id }}</span>
                  <span>{{ number_format($loan->amount,0) }}</span>
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
    <div class="table-responsive mb-4 mt-4">
      <table id="long-list" class="table table-hover d-none" style="table-layout:fixed;width:100%">
        <thead class="text-center">
          <tr>
            <th style="width:70px;">ID</th>
            <th style="width:120px;">Employee</th>
            <th style="width:140px;">Type</th>
            <th style="width:100px;">Amount</th>
            <th style="width:180px;">Purpose</th>
            <th style="width:100px;">Manager</th>
            <th style="width:100px;">Finance</th>
            <th style="width:100px;">HR</th>
            <th style="width:100px;">Status</th>
            <th class="text-center" style="width:160px;">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($loans as $loan)
            @php
              $staff = $loan->staffDetails ?? $loan->staff;
              $roleId = $auth->role_id;
              $canAct = false;

              if($roleId == 1 && $staff) {
                  $ids = explode(',', $staff->reporting_manager_id);
                  if(in_array($auth->id, $ids) && $loan->manager_approval == 'Pending') $canAct = true;
              }
              elseif($roleId == 2 && $loan->manager_approval == 'Approved' && $loan->finance_approval == 'Pending') $canAct = true;
              elseif($roleId == 3 && $loan->finance_approval == 'Approved' && $loan->hr_approval == 'Pending') $canAct = true;
            @endphp

            <tr>
              <td>LN{{ $loan->id }}</td>
              <td>{{ optional($staff)->first_name ?? '—' }}</td>
              <td>{{ $types[$loan->type_id] ?? '—' }}</td>
              <td>{{ number_format($loan->amount,2) }}</td>
              <td class="truncate-text" title="{{ $loan->purpose }}">{{ Str::limit($loan->purpose,25) }}</td>

              <td><span class="badge {{ $loan->manager_approval=='Approved'?'bg-success':($loan->manager_approval=='Rejected'?'bg-danger':'bg-warning') }}">{{ $loan->manager_approval ?? 'Pending' }}</span></td>
              <td><span class="badge {{ $loan->finance_approval=='Approved'?'bg-success':($loan->finance_approval=='Rejected'?'bg-danger':'bg-warning') }}">{{ $loan->finance_approval ?? 'Pending' }}</span></td>
              <td><span class="badge {{ $loan->hr_approval=='Approved'?'bg-success':($loan->hr_approval=='Rejected'?'bg-danger':'bg-warning') }}">{{ $loan->hr_approval ?? 'Pending' }}</span></td>
              <td><span class="badge {{ $loan->status=='Approved'?'bg-success':($loan->status=='Rejected'?'bg-danger':'bg-secondary') }}">{{ $loan->status ?? 'Pending' }}</span></td>

              <td class="text-center">
                @if($canAct)
                  <form method="POST" action="{{ route('employee.loans.approve',$loan->id) }}" class="d-inline">@csrf
                    <input type="hidden" name="status" value="Approved">
                    <button class="btn btn-success btn-sm">Approve</button>
                  </form>
                  <form method="POST" action="{{ route('employee.loans.approve',$loan->id) }}" class="d-inline">@csrf
                    <input type="hidden" name="status" value="Rejected">
                    <button class="btn btn-danger btn-sm">Reject</button>
                  </form>
                @else
                  <em class="text-muted small">No action</em>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
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
});
</script>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php } ?>
@endsection

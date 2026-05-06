{{-- resources/views/backEnd/employee/loans/index.blade.php --}}
@extends('backEnd.newmasterpage')
@section('mainContent')
@php
    use Illuminate\Support\Str;
@endphp

<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<script>
  function setLoansView(mode) {
    const leftNav = document.getElementById('leftSidebar');
    const content = document.querySelector('.content-container');
    const shortList = document.getElementById('loanShortList');
    const longTable = document.getElementById('long-list');
    const filtersShort = document.getElementById('filters-short');
    const filtersLong  = document.getElementById('filters-long');

    if (mode === 'full') {
      leftNav.classList.remove('col-3'); leftNav.classList.add('col-12');
      leftNav.style.width = '100%'; content.classList.add('d-none');
      longTable?.classList.remove('d-none'); shortList?.classList.add('d-none');
      filtersLong?.classList.remove('d-none'); filtersShort?.classList.add('d-none');
      leftNav.dataset.view = 'full';
    } else {
      leftNav.classList.remove('col-12'); leftNav.classList.add('col-3');
      leftNav.style.width = ''; content.classList.remove('d-none');
      longTable?.classList.add('d-none'); shortList?.classList.remove('d-none');
      filtersShort?.classList.remove('d-none'); filtersLong?.classList.add('d-none');
      leftNav.dataset.view = 'compact';
    }
  }
  function list_style_new_loans() {
    const leftNav = document.getElementById('leftSidebar');
    const cur = leftNav.dataset.view || 'compact';
    setLoansView(cur === 'compact' ? 'full' : 'compact');
  }
  document.addEventListener('DOMContentLoaded', function(){
    const leftNav = document.getElementById('leftSidebar');
    if (!leftNav.dataset.view) leftNav.dataset.view = 'compact';
  });
</script>

<aside class="left-nav col-3" id="leftSidebar">
  <div class="resizer" id="sidebarResizer"></div>

  {{-- SHORT (Compact) --}}
  <div class="short-list" id="filters-short">
    <h4 class="mb-2">My Loans & Advances</h4>

    <form class="form-horizontal" method="get" action="{{ route('employee.loans.index') }}" id="loan-search">
      <div class="search-filter-container mb-4 d-flex">
        <div class="input-group flex-nowrap">
          <input type="text" name="q" class="form-control"
                 placeholder="Search by ID / Purpose"
                 value="{{ request('q') ?? '' }}">
        </div>
        <button type="submit" class="btn btn-light ms-2">
          <i class="ico icon-outline-magnifer"></i>
        </button>
        <button type="button" class="btn btn-light ms-2" onclick="list_style_new_loans()">
          <i class="ico icon-outline-list-down"></i>
        </button>
      </div>
    </form>
  </div>

  {{-- LONG (Full) --}}
  <div class="long-list d-none" id="filters-long">
    <div class="d-flex align-items-center justify-content-between">
      <h4 class="mb-2">Loan & Advance Requests</h4>
      <div class="search-filter-container mb-0">
        <button class="btn btn-light" onclick="document.getElementById('long-filters-box').classList.toggle('d-none')">
          <i class="ico icon-outline-magnifer"></i>
        </button>
        <button class="btn btn-light" onclick="list_style_new_loans()">
          <i class="ico icon-outline-list-down"></i>
        </button>
      </div>
    </div>

    <div id="long-filters-box" class="search-filter-container mt-1 mb-4 filter-field d-none border">
      <div class="card">
        <div class="card-body">
          <form class="form-horizontal" method="get" action="{{ route('employee.loans.index') }}" id="loan-filter">
            <div class="row">
              <div class="col-3 mb-2">
                <label class="form-label">Status</label>
                <select class="form-control" name="status">
                  <option value="">All</option>
                  @foreach (['Pending','Approved','Rejected','Disbursed'] as $st)
                    <option value="{{ $st }}" {{ request('status')===$st?'selected':'' }}>{{ $st }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-3 mb-2">
                <label class="form-label">Type</label>
                <select class="form-control" name="type_id">
                  <option value="">All</option>
                  @foreach ($loanTypes as $lt)
                    <option value="{{ $lt->id }}" {{ request('type_id')==$lt->id?'selected':'' }}>{{ $lt->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-3 mb-2">
                <label class="form-label">From</label>
                <input class="form-control" type="date" name="from" value="{{ request('from') }}">
              </div>
              <div class="col-3 mb-2">
                <label class="form-label">To</label>
                <input class="form-control" type="date" name="to" value="{{ request('to') }}">
              </div>
              <div class="col-3 mb-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-success w-100">Filter</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- LEFT NAV LIST (Short) --}}
  <div class="left-nav-list">
    <ul id="loanShortList" class="nav flex-column nav-pills" role="tablist">
      @if ($loans->count() > 0)
        @foreach ($loans as $loan)
          <li class="nav-item w-100" role="presentation">
            <button class="nav-link lv-item {{ (isset($selectedLoan) && $selectedLoan && $selectedLoan->id == $loan->id) ? 'active' : '' }}"
                    data-id="{{ $loan->id }}" type="button" role="tab">
              <div class="row w-100 align-items-center">
                
                <div class="col-10 ps-2">
                  <div class="row">
                    <div class="col-7">
                      <span class="form-control-plaintext fw-semibold truncate-text" title="{{ $loan->purpose }}">
                        {{ $loan->purpose ? Str::limit($loan->purpose, 24) : '—' }}
                      </span>
                    </div>
                    <div class="col-5 text-end">
                      <span class="badge bg-secondary">{{ $loan->status ?? 'Pending' }}</span>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center text-muted xsmall mt-1">
                    <span>{{ number_format($loan->amount,2) }}</span>
                    <span>#{{ $loan->id }}</span>
                  </div>
                </div>
              </div>
            </button>
          </li>
        @endforeach
      @else
        <div class="p-3 text-muted">No loan or advance requests</div>
      @endif
    </ul>

    {{-- LONG LIST TABLE --}}
    <div class="table-responsive mb-4 mt-4">
      <table id="long-list" class="table table-hover d-none" style="table-layout: fixed; width:100%">
        <thead>
          <tr>
            <th style="width: 70px;">ID</th>
            <th style="width: 160px;">Type</th>
            <th style="width: 160px;">Amount</th>
            <th style="width: 160px;">Installments</th>
            <th style="width: 160px;">Applied On</th>
            <th style="width: 120px;">Status</th>
            <th>Purpose</th>
            <th style="width: 100px;">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($loans as $loan)
            <tr>
              <td>
                <a href="javascript:void(0);" onclick="list_style_new_loans()" class="lv-item" data-id="{{ $loan->id }}">
                  LN{{ $loan->id }}
                </a>
              </td>
              <td>{{ $loan->type->name ?? '—' }}</td>
              <td>{{ number_format($loan->amount, 2) }}</td>
              <td>{{ $loan->installments ?? '—' }}</td>
              <td>{{ optional($loan->created_at)->format('d M Y') ?: '—' }}</td>
              <td>
                <span class="badge bg-{{ 
                  ($loan->status=='Approved' ? 'success' : 
                  ($loan->status=='Rejected' ? 'danger' : 
                  ($loan->status=='Disbursed' ? 'info' : 'warning'))) 
                }}">
                  {{ $loan->status ?? 'Pending' }}
                </span>
              </td>
              <td class="truncate-text">{{ $loan->purpose ?? '—' }}</td>
              <td class="text-center">
                <button class="btn btn-sm btn-light lv-item" data-id="{{ $loan->id }}" title="View">
                  <i class="ico icon-outline-eye" style="font-size:16px;"></i>
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-3">
        {{ $loans->links() }}
      </div>
    </div>
  </div>
</aside>

<div class="content-container col-9">
  <div class="tab-content display-flex-tabs" id="loanTabContent">

    <script>
      (function () {
        var detailsTpl = @json(route('employee.loans.show', ['id' => ':id']));
        function buildUrl(tpl, id){ return tpl.replace(':id', encodeURIComponent(id)); }

        $(document).on('click', '.lv-item', function (e) {
          e.preventDefault();
          var id = $(this).data('id');
          if (!id) return;

          $('.lv-item').removeClass('active');
          $('.lv-item[data-id="' + id + '"]').addClass('active');

          var newUrl = "{{ route('employee.loans.index') }}?{{ http_build_query(request()->except('active')) }}&active=" + encodeURIComponent(id);
          if (window.history && window.history.pushState) {
            window.history.pushState({ path: newUrl }, '', newUrl);
          }

          var action = buildUrl(detailsTpl, id);
          var $loader = $('#loading_bg');
          if ($loader.length) $loader.show();

          $.ajax({
            url: action,
            method: 'GET',
            cache: false,
            success: function (html) {
              $('#loan-details').html(html && $.trim(html).length ? html : '<p class="text-danger">No Details Available.</p>');
            },
            error: function (xhr) {
              console.error('loan-details error:', xhr.status, xhr.responseText);
              $('#loan-details').html('<p class="text-danger">No Details Available.</p>');
            },
            complete: function () {
              if ($loader.length) $loader.hide();
            }
          });
        });
      })();
    </script>

    <div role="tabpanel" aria-labelledby="loan-tab" id="loan-details">
      @if ($selectedLoan)
        @include('backEnd.humanResource.loans._details', ['loan' => $selectedLoan])
      @else
        <div class="container-fluid d-flex flex-column justify-content-center align-items-center" style="min-height: 60vh;">
          <div class="text-center mb-4">
            <a href="{{ url('employee/loans/create') }}"
              class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto text-decoration-none"
              style="width: 80px; height: 80px; font-size: 36px; cursor: pointer;">
              <i class="ico icon-outline-add-square"></i>
            </a>

            <h1 class="fw-bold mt-3">
              <a href="{{ url('employee/loans/create') }}" class="text-dark text-decoration-none">
                Loans & Advances
              </a>
            </h1>

            <p class="text-muted">Select a loan request from the list to view details</p>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<script>
$(function(){
  var $q = $('#loan-search input[name="q"]');
  var $shortItems = $('#loanShortList > li');
  var $longRows   = $('#long-list tbody > tr');

  function norm(s){ return (s || '').toString().toLowerCase(); }
  function textOf($el){ return norm($el.text()); }
  function applyFilter(needle){
    if (!needle) { $shortItems.show(); $longRows.show(); return; }
    $shortItems.each(function(){ $(this).toggle(textOf($(this)).indexOf(needle) !== -1); });
    $longRows.each(function(){ $(this).toggle(textOf($(this)).indexOf(needle) !== -1); });
  }
  var deb;
  $q.on('input', function(){
    clearTimeout(deb);
    var needle = norm(this.value);
    deb = setTimeout(function(){ applyFilter(needle); }, 120);
  });
});
</script>
@endsection

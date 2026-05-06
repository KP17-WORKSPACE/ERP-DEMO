{{-- resources/views/backEnd/employee/leaves/index.blade.php --}}
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
  function setLeavesView(mode) {
    const leftNav = document.getElementById('leftSidebar');
    const content = document.querySelector('.content-container');
    const shortList = document.getElementById('leaveShortList'); // UL
    const longTable = document.getElementById('long-list');      // TABLE
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
  function list_style_new_leaves() {
    const leftNav = document.getElementById('leftSidebar');
    const cur = leftNav.dataset.view || 'compact';
    setLeavesView(cur === 'compact' ? 'full' : 'compact');
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
    <h4 class="mb-2">My Leaves</h4>

    <form class="form-horizontal" method="get" action="{{ route('employee.leaves.index') }}" id="leave-search">
      <div class="search-filter-container mb-4 d-flex">
        <div class="input-group flex-nowrap">
          <input type="text" name="q" class="form-control"
                 placeholder="Search by ID / Reason"
                 value="{{ request('q') ?? '' }}">
        </div>
        <button type="submit" class="btn btn-light ms-2">
          <i class="ico icon-outline-magnifer"></i>
        </button>
        <button type="button" class="btn btn-light ms-2" onclick="list_style_new_leaves()">
          <i class="ico icon-outline-list-down"></i>
        </button>
      </div>
    </form>
  </div>

  {{-- LONG (Full) --}}
  <div class="long-list d-none" id="filters-long">
    <div class="d-flex align-items-center justify-content-between">
      <h4 class="mb-2">Leave Requests</h4>
      <div class="search-filter-container mb-0">
        <button class="btn btn-light" onclick="document.getElementById('long-filters-box').classList.toggle('d-none')">
          <i class="ico icon-outline-magnifer"></i>
        </button>
        <button class="btn btn-light" onclick="list_style_new_leaves()">
          <i class="ico icon-outline-list-down"></i>
        </button>
      </div>
    </div>

    <div id="long-filters-box" class="search-filter-container mt-1 mb-4 filter-field d-none border">
      <div class="card">
        <div class="card-body">
          <form class="form-horizontal" method="get" action="{{ route('employee.leaves.index') }}" id="leave-filter">
            <div class="row">
              <div class="col-3 mb-2">
                <label class="form-label">Status</label>
                <select class="form-control" name="status">
                  <option value="">All</option>
                  @foreach (['Pending','Approved','Rejected','Cancelled'] as $st)
                    <option value="{{ $st }}" {{ request('status')===$st?'selected':'' }}>{{ $st }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-3 mb-2">
                <label class="form-label">Type</label>
                <input class="form-control" type="number" name="type_id" value="{{ request('type_id') }}">
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
    <ul id="leaveShortList" class="nav flex-column nav-pills" role="tablist">
      @php $items = $leaves; @endphp
      @if ($items->count() > 0)
        @foreach ($items as $lv)
          <li class="nav-item w-100" role="presentation">
            <button class="nav-link lv-item {{ (isset($selectedLeave) && $selectedLeave && $selectedLeave->id == $lv->id) ? 'active' : '' }}"
                    data-id="{{ $lv->id }}" type="button" role="tab">
              <div class="row w-100 align-items-center">
                <div class="col-2 d-flex justify-content-center">
                  <div class="rounded-circle bg-light border"
                       style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-weight:600;color:#555;">
                    {{ strtoupper(substr(($lv->type->name ?? 'L'), 0, 1)) }}
                  </div>
                </div>
                <div class="col-10 ps-0">
                  <div class="row">
                    <div class="col-7">
                      <span class="form-control-plaintext fw-semibold truncate-text" title="{{ $lv->reason }}">
                        {{ $lv->reason ? Str::limit($lv->reason, 24) : '—' }}
                      </span>
                    </div>
                    <div class="col-5 text-end">
                      <span class="badge bg-secondary">{{ $lv->approve_status ?? 'Pending' }}</span>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between align-items-center text-muted xsmall mt-1">
                    <span class="form-control-plaintext truncate-text">
                      {{ optional($lv->leave_from)->format('d M') }} – {{ optional($lv->leave_to)->format('d M, Y') }}
                    </span>
                    <span class="form-control-plaintext truncate-text">
                      #{{ $lv->id }}
                    </span>
                  </div>
                </div>
              </div>
            </button>
          </li>
        @endforeach
      @else
        <div class="p-3 text-muted">No leave requests</div>
      @endif
    </ul>

    {{-- LONG LIST TABLE --}}
   <div class="table-responsive mb-4 mt-4">
  <table id="long-list" class="table table-hover d-none" style="table-layout: fixed; width:100%">
    <thead>
      <tr>
        <th style="width: 70px;">ID</th>
        <th style="width: 140px;">Type</th>
        <th style="width: 160px;">Designation</th>
        <th style="width: 160px;">Department</th>
        <th style="width: 140px;">Leave From</th>
        <th style="width: 140px;">Leave To</th>
        <th style="width: 90px;">Days</th>
        <th style="width: 120px;">Status</th>
        <th>Reason</th>
        <th class="" style="width: 100px;">Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($items as $lv)
        <tr>
          <td class="">
            <a href="javascript:void(0);" onclick="list_style_new_leaves()" class="lv-item" data-id="{{ $lv->id }}">
              LV{{ $lv->id }}
            </a>
          </td>

          <td>{{ $lv->type->name ?? ('Type #'.$lv->type_id) }}</td>

          {{-- Designations --}}
        <td>{{ $lv->staffs->designations->title ?? '—' }}</td>

          {{-- Department --}}
            <td>{{ $lv->staffs->departments->name ?? '—' }}</td>
          {{-- Leave From --}}
          <td>{{ optional($lv->leave_from)->format('d M Y') ?: '—' }}</td>

          {{-- Leave To --}}
          <td>{{ optional($lv->leave_to)->format('d M Y') ?: '—' }}</td>

          <td>{{ number_format((float)$lv->days, 2) }}</td>

          <td>
            <span class="badge bg-{{ 
              ($lv->approve_status=='Approved' ? 'success' : 
              ($lv->approve_status=='Rejected' ? 'danger' : 
              ($lv->approve_status=='Cancelled' ? 'secondary' : 'warning'))) 
            }}">
              {{ $lv->approve_status ?? 'Pending' }}
            </span>
          </td>

          <td class="truncate-text">{{ $lv->reason ?? '—' }}</td>

          <td class="text-center">
            <button class="btn btn-sm btn-light lv-item" data-id="{{ $lv->id }}" title="View">
              <i class="ico icon-outline-eye" style="font-size:16px;"></i>
            </button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="mt-3">
    {{ $leaves->links() }}
  </div>
</div>


  </div>
</aside>

<div class="content-container col-9">
  <div class="tab-content display-flex-tabs" id="leaveTabContent">

    {{-- CLICK HANDLER (short + long list) --}}
    <script>
      (function () {
        var detailsTpl = @json(route('employee.leaves.show', ['id' => ':id']));
        function buildUrl(tpl, id){ return tpl.replace(':id', encodeURIComponent(id)); }

        $(document).on('click', '.lv-item', function (e) {
          e.preventDefault();
          var id = $(this).data('id');
          if (!id) return;

          $('.lv-item').removeClass('active');
          $('.lv-item[data-id="' + id + '"]').addClass('active');

          // Update URL (?active=id)
          var newUrl = "{{ route('employee.leaves.index') }}?{{ http_build_query(request()->except('active')) }}&active=" + encodeURIComponent(id);
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
              $('#lv-details').html(html && $.trim(html).length ? html : '<p class="text-danger">No Details Available.</p>');
            },
            error: function (xhr) {
              console.error('leave-details error:', xhr.status, xhr.responseText);
              $('#lv-details').html('<p class="text-danger">No Details Available.</p>');
            },
            complete: function () {
              if ($loader.length) $loader.hide();
            }
          });
        });
      })();
    </script>

    <div role="tabpanel" aria-labelledby="lv-tab" id="lv-details">
      @if ($selectedLeave)
        @include('backEnd.employee.leaves._details', ['leave' => $selectedLeave])
      @else
        <div class="container-fluid d-flex flex-column justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="text-center mb-4">
  <a href="{{ url('employee/leaves/create') }}"
     class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto text-decoration-none"
     style="width: 80px; height: 80px; font-size: 36px; cursor: pointer;">
      <i class="ico icon-outline-add-square"></i>
  </a>

  <h1 class="fw-bold mt-3">
    <a href="{{ url('employee/leaves/create') }}" class="text-dark text-decoration-none">
      Leave Requests
    </a>
  </h1>

  <p class="text-muted">Select a leave from the list to view details</p>
</div>

        </div>

      @endif
    </div>
  </div>
</div>

<script>
$(function(){
  var $q = $('#leave-search input[name="q"]');
  var $shortItems = $('#leaveShortList > li');
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

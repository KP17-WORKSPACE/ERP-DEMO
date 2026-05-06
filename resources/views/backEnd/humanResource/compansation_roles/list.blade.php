@extends('backEnd.newmasterpage')
@section('mainContent')

{{-- ====== VIEW TOGGLER (same behavior as company) ====== --}}
<script>
  function setCompensationView(mode) {
    const leftNav = document.getElementById('leftSidebar');
    const content = document.querySelector('.content-container');

    const shortList = document.getElementById('compensationShortList'); // UL
    const longTable = document.getElementById('long-list');        // TABLE

    const filtersShort = document.getElementById('filters-short');
    const filtersLong  = document.getElementById('filters-long');

    if (mode === 'full') {
      if (leftNav.classList.contains('col-3')) {
        leftNav.classList.remove('col-3');
        leftNav.classList.add('col-12');
      }
      leftNav.style.width = '100%';
      content.classList.add('d-none');

      longTable && longTable.classList.remove('d-none');
      shortList && shortList.classList.add('d-none');

      filtersLong && filtersLong.classList.remove('d-none');
      filtersShort && filtersShort.classList.add('d-none');

      leftNav.dataset.view = 'full';
    } else {
      if (leftNav.classList.contains('col-12')) {
        leftNav.classList.remove('col-12');
        leftNav.classList.add('col-3');
      }
      leftNav.style.width = '';
      content.classList.remove('d-none');

      longTable && longTable.classList.add('d-none');
      shortList && shortList.classList.remove('d-none');

      filtersShort && filtersShort.classList.remove('d-none');
      filtersLong && filtersLong.classList.add('d-none');

      leftNav.dataset.view = 'compact';
    }
  }
  function list_style_new_compensation() {
    const leftNav = document.getElementById('leftSidebar');
    const cur = leftNav.dataset.view || 'compact';
    setCompensationView(cur === 'compact' ? 'full' : 'compact');
  }
  document.addEventListener('DOMContentLoaded', function(){
    const leftNav = document.getElementById('leftSidebar');
    if (!leftNav.dataset.view) leftNav.dataset.view = 'compact';
  });
</script>
<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<?php try { ?>

<aside class="left-nav col-3" id="leftSidebar">
  <div class="resizer" id="sidebarResizer"></div>

  {{-- SHORT (Compact) --}}
  <div class="short-list" id="filters-short">
    <h4 class="mb-2">Compensation Records</h4>

    {{-- quick search --}}
    <form class="form-horizontal" method="get" action="{{ route('staff.compensation.list') }}" id="compensation-search">
      <div class="search-filter-container mb-4 d-flex">
        <div class="input-group flex-nowrap">
          <input type="text" name="q" class="form-control"
                 placeholder="Search by Doc No, Employee"
                 value="{{ request('q') ?? '' }}">
        </div>
        <button type="submit" class="btn btn-light ms-2">
          <i class="ico icon-outline-magnifer"></i>
        </button>
        <button type="button" class="btn btn-light ms-2" onclick="list_style_new_compensation()">
          <i class="ico icon-outline-list-down"></i>
        </button>
      </div>
    </form>
  </div>

  {{-- LONG (Full) --}}
  <div class="long-list d-none" id="filters-long">
    <div class="d-flex align-items-center justify-content-between">
      <h4 class="mb-2">Compensation List</h4>
      <div class="search-filter-container mb-0">
        <button class="btn btn-light" onclick="document.getElementById('long-filters-box').classList.toggle('d-none')">
          <i class="ico icon-outline-magnifer"></i>
        </button>
        <button class="btn btn-light" onclick="list_style_new_compensation()">
          <i class="ico icon-outline-list-down"></i>
        </button>
      </div>
    </div>

    <div id="long-filters-box" class="search-filter-container mt-1 mb-4 filter-field d-none border">
      <div class="card">
        <div class="card-body">
          <form class="form-horizontal" method="get" action="{{ route('staff.compensation.list') }}" id="compensation-filter">
            <div class="row">
              <div class="col-3 mb-2">
                <label class="form-label">Doc No</label>
                <input class="form-control" type="text" name="doc_no" value="{{ request('doc_no') }}">
              </div>
              <div class="col-3 mb-2">
                <label class="form-label">Employee</label>
                <input class="form-control" type="text" name="employee" value="{{ request('employee') }}">
              </div>
              <div class="col-3 mb-2">
                <label class="form-label">Transaction Type</label>
                <select class="form-control" name="transaction_type">
                  <option value="">All Types</option>
                  <option value="promotion" {{ request('transaction_type') == 'promotion' ? 'selected' : '' }}>Promotion</option>
                  <option value="demotion" {{ request('transaction_type') == 'demotion' ? 'selected' : '' }}>Demotion</option>
                  <option value="increment" {{ request('transaction_type') == 'increment' ? 'selected' : '' }}>Increment</option>
                </select>
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
    {{-- Debug Info --}}
    @if(config('app.debug'))
      <div class="alert alert-info small mb-2">
        <strong>Debug:</strong> Found {{ count($compensations_data ?? []) }} compensations.
        @if(request('active'))
          Active ID: {{ request('active') }}
          @if(isset($selectedCompensation))
            Selected: ID {{ $selectedCompensation->id ?? 'null' }}
          @else
            Selected: None found
          @endif
        @endif
        <br>Available IDs: 
        @foreach(($compensations_data ?? []) as $c)
          {{ $c->id }},
        @endforeach
      </div>
    @endif
    
    <ul id="compensationShortList" class="nav flex-column nav-pills" role="tablist">
      @php $compensations_data = isset($compensations) ? $compensations : collect(); @endphp
      @if (count($compensations_data) > 0)
        @foreach ($compensations_data as $comp)
          <li class="nav-item w-100" role="presentation">
    <button class="nav-link comp-item {{ (isset($selectedCompensation) && $selectedCompensation && $selectedCompensation->id == $comp->id) ? 'active' : '' }}"
            data-id="{{ $comp->id ?? '' }}" type="button" role="tab">

        <div class="row w-100 align-items-start">

            <div class="col-12">

                {{-- Doc No + Compensation ID --}}
                <div class="row">
                    <div class="col-7">
                        <span class="form-control-plaintext fw-semibold truncate-text"
                              title="{{ $comp->doc_no ?? '' }}">
                            {{ $comp->doc_no ?? '—' }}
                        </span>
                    </div>

                    <div class="col-5 text-end">
                        <span class="form-control-plaintext text-muted">
                            #{{ $comp->id ?? '' }}
                        </span>
                    </div>
                </div>

                {{-- Employee Name --}}
                <div class="text-muted xsmall truncate-text">
                    {{ $comp->employee ? $comp->employee->full_name ?? '—' : '—' }}
                </div>

                {{-- Transaction Type + Department --}}
                <div class="d-flex justify-content-between align-items-center text-muted xsmall mt-1">

                    {{-- Transaction Type --}}
                    <span class="form-control-plaintext truncate-text">
                        {{ ucfirst(str_replace('_', ' ', $comp->transaction_type ?? 'N/A')) }}
                    </span>

                    {{-- Department --}}
                    <span class="form-control-plaintext truncate-text text-end">
                        {{ $comp->employee && $comp->employee->departments ? $comp->employee->departments->name ?? '—' : '—' }}
                    </span>

                </div>

            </div>

        </div>

    </button>
</li>

        @endforeach
      @else
        <div class="p-3 text-muted">No Records</div>
      @endif
    </ul>

    {{-- LONG LIST TABLE --}}
    <div class="table-responsive mb-4 mt-4">
      <table id="long-list" class="table table-hover d-none" style="table-layout: fixed; width:100%">
        <thead class="text-center">
          <tr>
            <th style="width: 80px;">ID</th>
            <th style="width: 220px;">Doc Number</th>
            <th style="width: 180px;">Employee Name</th>
            <th style="width: 120px;">Department</th>
            <th style="width: 140px;">Transaction Type</th>
            <th style="width: 140px;">Status</th>
            <th style="width: 140px;">Effective Date</th>
            <th style="width: 200px;">Created Date</th>
            <th class="text-center" style="width: 110px;">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($compensations_data as $comp)
            <tr>
              <td class="text-center">
                <a href="javascript:void(0);" onclick="list_style_new_compensation()" class="comp-item" data-id="{{ $comp->id ?? '' }}">#{{ $comp->id ?? '' }}</a>
              </td>
              <td>
                <a href="javascript:void(0);" onclick="list_style_new_compensation()" class="comp-item" data-id="{{ $comp->id ?? '' }}">
                  {{ $comp->doc_no ?? '—' }}
                </a>
              </td>
              <td>{{ $comp->employee ? $comp->employee->full_name ?? '—' : '—' }}</td>
              <td>{{ $comp->employee && $comp->employee->departments ? $comp->employee->departments->name ?? '—' : '—' }}</td>
              <td>{{ ucfirst(str_replace('_', ' ', $comp->transaction_type ?? 'N/A')) }}</td>
              <td>
                @switch($comp->current_status ?? 'draft')
                  @case('draft')
                    <span class="badge badge-secondary badge-sm">Draft</span>
                    @break
                  @case('pending')
                    <span class="badge badge-warning badge-sm">Pending</span>
                    @break
                  @case('approved')
                    <span class="badge badge-success badge-sm">Approved</span>
                    @break
                  @case('rejected')
                    <span class="badge badge-danger badge-sm">Rejected</span>
                    @break
                  @default
                    <span class="badge badge-secondary badge-sm">{{ $comp->current_status ?? 'Draft' }}</span>
                @endswitch
              </td>
              <td>{{ $comp->effective_date ? \Carbon\Carbon::parse($comp->effective_date)->format('d-m-Y') : '—' }}</td>
              <td class="truncate-text">{{ $comp->created_at ? \Carbon\Carbon::parse($comp->created_at)->format('d-m-Y H:i') : '—' }}</td>
              <td class="text-center">
                <div class="d-flex justify-content-start align-items-center gap-1">
                  <a href="{{ url('staff/compensation/'.$comp->id.'/edit') }}" class="btn btn-sm btn-light" title="Edit">
                    <i class="ico icon-outline-pen-2" style="font-size:16px;"></i>
                  </a>
                  <a href="{{ url('staff/compensation/'.$comp->id.'/delete') }}" class="btn btn-sm btn-light"
                     onclick="return confirm('Are you sure?')" title="Delete">
                    <i class="ico icon-bold-trash-bin-2" style="font-size:16px;"></i>
                  </a>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

  </div>
</aside>

<div class="content-container col-9">
  <div class="tab-content display-flex-tabs" id="compensationTabContent">

    {{-- CLICK HANDLER (short + long list) --}}
    <script>
      (function () {
        var detailsTpl = @json(route('staff.compensation.view', ['id' => ':id']));
        function buildUrl(tpl, id){ return tpl.replace(':id', encodeURIComponent(id)); }

        $(document).on('click', '.comp-item', function (e) {
          e.preventDefault();
          var id = $(this).data('id');
          if (!id) return;

          $('.comp-item').removeClass('active');
          $('.comp-item[data-id="' + id + '"]').addClass('active');

          // Update URL (?active=id) for back/refresh
          var newUrl = "{{ route('staff.compensation.list') }}?active=" + encodeURIComponent(id);
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
              if (!html || !$.trim(html).length) {
                $('#comp-details').html('<p class="text-danger">No Details Available.</p>');
                return;
              }
              $('#comp-details').html(html);
            },
            error: function (xhr) {
              console.error('compensation-details error:', xhr.status, xhr.responseText);
              $('#comp-details').html('<p class="text-danger">No Details Available.</p>');
            },
            complete: function () {
              if ($loader.length) $loader.hide();
            }
          });
        });
      })();
    </script>

    <div role="tabpanel" aria-labelledby="comp-tab" id="comp-details">
      @php
        $firstCompensation = isset($selectedCompensation) && $selectedCompensation
                          ? $selectedCompensation
                          : ($compensations_data->first() ?? null);
      @endphp

      @if ($firstCompensation)
        {{-- This will be dynamically loaded via AJAX from the view route --}}
        <div class="p-4 text-center text-muted">
          <p>Select a compensation record from the list to view details</p>
        </div>
      @else
        <div class="p-4 text-center text-muted">
          <h5>No Compensation Records Found</h5>
          <p>Click the button below to create your first compensation record.</p>
          <a href="{{ route('staff.compensation.create') }}" class="btn btn-primary">
            <i class="ico icon-outline-plus"></i> Create Compensation Record
          </a>
        </div>
      @endif
    </div>
  </div>
</div>

<?php } catch (\Exception $e) { ?>
<div class="alert alert-danger">
  Error loading compensation list: {{ $e->getMessage() }}
</div>
<?php } ?>

@endsection
@section('mainContent')
<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

@php
// Debug: Check if $compensations variable exists
if (!isset($compensations)) {
    echo '<div class="alert alert-danger">Error: $compensations variable is not defined</div>';
    return;
}
@endphp

{{-- ====== VIEW TOGGLER (same behavior as company list) ====== --}}
<script>
  function setCompensationView(mode) {
    const leftNav = document.getElementById('leftSidebar');
    const content = document.querySelector('.content-container');

    const shortList = document.getElementById('compensationShortList');
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

      longTable && longTable.classList.remove('d-none');
      shortList && shortList.classList.add('d-none');

      filtersLong && filtersLong.classList.remove('d-none');
      filtersShort && filtersShort.classList.add('d-none');

      leftNav.dataset.view = 'full';
    } else {
      if (leftNav.classList.contains('col-12')) {
        leftNav.classList.remove('col-12');
        leftNav.classList.add('col-3');
      }
      leftNav.style.width = '';
      content.classList.remove('d-none');

      longTable && longTable.classList.add('d-none');
      shortList && shortList.classList.remove('d-none');

      filtersShort && filtersShort.classList.remove('d-none');
      filtersLong && filtersLong.classList.add('d-none');

      leftNav.dataset.view = 'compact';
    }
  }
  
  function list_style_compensation() {
    const leftNav = document.getElementById('leftSidebar');
    const cur = leftNav.dataset.view || 'compact';
    setCompensationView(cur === 'compact' ? 'full' : 'compact');
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
    <h4 class="mb-2">Compensation Records</h4>

    {{-- quick search --}}
    <form class="form-horizontal" method="get" action="{{ route('staff.compensation.list') }}" id="compensation-search">
      <div class="search-filter-container mb-4 d-flex">
        <div class="input-group flex-nowrap">
          <input type="text" name="q" class="form-control"
                 placeholder="Search by Doc No, Employee"
                 value="{{ request('q') ?? '' }}">
        </div>
        <button type="submit" class="btn btn-light ms-2">
          <i class="ico icon-outline-magnifer"></i>
        </button>
        <button type="button" class="btn btn-light ms-2" onclick="list_style_compensation()">
          <i class="ico icon-outline-list-down"></i>
        </button>
      </div>
    </form>
  </div>

  {{-- LONG (Full) --}}
  <div class="long-list d-none" id="filters-long">
    <div class="d-flex align-items-center justify-content-between">
      <h4 class="mb-2">Compensation List</h4>
      <div class="search-filter-container mb-0">
        <button class="btn btn-light" onclick="document.getElementById('long-filters-box').classList.toggle('d-none')">
          <i class="ico icon-outline-magnifer"></i>
        </button>
        <button class="btn btn-light" onclick="list_style_compensation()">
          <i class="ico icon-outline-list-down"></i>
        </button>
      </div>
    </div>

    <div id="long-filters-box" class="search-filter-container mt-1 mb-4 filter-field d-none border">
      <div class="card">
        <div class="card-body">
          <form class="form-horizontal" method="get" action="{{ route('staff.compensation.list') }}" id="compensation-filter">
            <div class="row">
              <div class="col-3 mb-2">
                <label class="form-label">Doc No</label>
                <input class="form-control" type="text" name="doc_no" value="{{ request('doc_no') }}">
              </div>
              <div class="col-3 mb-2">
                <label class="form-label">Employee</label>
                <input class="form-control" type="text" name="employee" value="{{ request('employee') }}">
              </div>
              <div class="col-3 mb-2">
                <label class="form-label">Transaction Type</label>
                <select class="form-control" name="transaction_type">
                  <option value="">All Types</option>
                  <option value="promotion" {{ request('transaction_type') == 'promotion' ? 'selected' : '' }}>Promotion</option>
                  <option value="demotion" {{ request('transaction_type') == 'demotion' ? 'selected' : '' }}>Demotion</option>
                  <option value="increment" {{ request('transaction_type') == 'increment' ? 'selected' : '' }}>Increment</option>
                  <option value="increment_promotion" {{ request('transaction_type') == 'increment_promotion' ? 'selected' : '' }}>Increment + Promotion</option>
                  <option value="decrement_demotion" {{ request('transaction_type') == 'decrement_demotion' ? 'selected' : '' }}>Decrement + Demotion</option>
                </select>
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
    <ul id="compensationShortList" class="nav flex-column nav-pills" role="tablist">
      @if (isset($compensations) && count($compensations) > 0)
        @foreach ($compensations as $comp)
          <li class="nav-item w-100" role="presentation">
            <button class="nav-link comp-item" data-id="{{ $comp->id ?? '' }}" type="button" role="tab">
              <div class="row w-100 align-items-start">
                <div class="col-12">
                  {{-- Doc No + Status --}}
                  <div class="row">
                    <div class="col-7">
                      <span class="form-control-plaintext fw-semibold truncate-text" title="{{ $comp->doc_no ?? '' }}">
                        {{ $comp->doc_no ?? '—' }}
                      </span>
                    </div>
                    <div class="col-5 text-end">
                      @switch($comp->current_status ?? 'draft')
                        @case('draft')
                          <span class="badge badge-secondary badge-sm">Draft</span>
                          @break
                        @case('pending')
                          <span class="badge badge-warning badge-sm">Pending</span>
                          @break
                        @case('approved')
                          <span class="badge badge-success badge-sm">Approved</span>
                          @break
                        @case('rejected')
                          <span class="badge badge-danger badge-sm">Rejected</span>
                          @break
                        @default
                          <span class="badge badge-secondary badge-sm">{{ $comp->current_status ?? 'Draft' }}</span>
                      @endswitch
                    </div>
                  </div>

                  {{-- Employee Name --}}
                  <div class="text-muted xsmall truncate-text">
                    {{ $comp->employee ? $comp->employee->full_name ?? '—' : '—' }}
                  </div>

                  {{-- Transaction Type + Date --}}
                  <div class="d-flex justify-content-between align-items-center text-muted xsmall mt-1">
                    <span class="form-control-plaintext truncate-text">
                      {{ ucfirst(str_replace('_', ' ', $comp->transaction_type ?? 'N/A')) }}
                    </span>
                    <span class="form-control-plaintext truncate-text text-end">
                      {{ $comp->doc_date ? \Carbon\Carbon::parse($comp->doc_date)->format('d-m-Y') : '—' }}
                    </span>
                  </div>
                </div>
              </div>
            </button>
          </li>
        @endforeach
      @else
        <div class="p-3 text-muted">No Records</div>
      @endif
    </ul>

    {{-- LONG LIST TABLE --}}
    <div class="table-responsive mb-4 mt-4">
      <table id="long-list" class="table table-hover d-none" style="table-layout: fixed; width:100%">
        <thead class="text-center">
          <tr>
            <th style="width: 120px;">Doc No</th>
            <th style="width: 100px;">Date</th>
            <th style="width: 180px;">Employee</th>
            <th style="width: 140px;">Department</th>
            <th style="width: 140px;">Transaction Type</th>
            <th style="width: 100px;">Effective Date</th>
            <th style="width: 100px;">Status</th>
            <th class="text-center" style="width: 110px;">Action</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($compensations))
          @foreach ($compensations as $comp)
            <tr>
              <td>
                <a href="javascript:void(0);" onclick="list_style_compensation()" class="comp-item" data-id="{{ $comp->id }}">
                  {{ $comp->doc_no ?? '—' }}
                </a>
              </td>
              <td>{{ $comp->doc_date ? \Carbon\Carbon::parse($comp->doc_date)->format('d-m-Y') : '—' }}</td>
              <td>
                <a href="javascript:void(0);" onclick="list_style_compensation()" class="comp-item" data-id="{{ $comp->id }}">
                  {{ $comp->employee ? $comp->employee->full_name ?? '—' : '—' }}
                </a>
              </td>
              <td>{{ $comp->employee && $comp->employee->departments ? $comp->employee->departments->name ?? '—' : '—' }}</td>
              <td>{{ ucfirst(str_replace('_', ' ', $comp->transaction_type ?? 'N/A')) }}</td>
              <td>{{ $comp->effective_date ? \Carbon\Carbon::parse($comp->effective_date)->format('d-m-Y') : '—' }}</td>
              <td>
                @switch($comp->current_status ?? 'draft')
                  @case('draft')
                    <span class="badge badge-secondary badge-sm">Draft</span>
                    @break
                  @case('pending')
                    <span class="badge badge-warning badge-sm">Pending</span>
                    @break
                  @case('approved')
                    <span class="badge badge-success badge-sm">Approved</span>
                    @break
                  @case('rejected')
                    <span class="badge badge-danger badge-sm">Rejected</span>
                    @break
                  @default
                    <span class="badge badge-secondary badge-sm">{{ $comp->current_status ?? 'Draft' }}</span>
                @endswitch
              </td>
              <td class="text-center">
                <div class="d-flex justify-content-start align-items-center gap-1">
                  <a href="{{ route('staff.compensation.view', $comp->id ?? 0) }}" class="btn btn-sm btn-light" title="View">
                    <i class="ico icon-outline-eye" style="font-size:16px;"></i>
                  </a>
                  <a href="{{ route('staff.compensation.create', $comp->id ?? 0) }}" class="btn btn-sm btn-light" title="Edit">
                    <i class="ico icon-outline-pen-2" style="font-size:16px;"></i>
                  </a>
                </div>
              </td>
            </tr>
          @endforeach
          @endif
        </tbody>
      </table>
    </div>
  </div>
</aside>

<div class="content-container col-9">
  <div class="tab-content display-flex-tabs" id="compensationTabContent">

    {{-- CLICK HANDLER (short + long list) --}}
    <script>
      (function () {
        var detailsTpl = @json(route('staff.compensation.view', ['id' => ':id']));
        function buildUrl(tpl, id){ return tpl.replace(':id', encodeURIComponent(id)); }

        $(document).on('click', '.comp-item', function (e) {
          e.preventDefault();
          var id = $(this).data('id');
          if (!id) return;

          $('.comp-item').removeClass('active');
          $('.comp-item[data-id="' + id + '"]').addClass('active');

          // Update URL (?active=id) for back/refresh
          var newUrl = "{{ route('staff.compensation.list') }}?active=" + encodeURIComponent(id);
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
              if (!html || !$.trim(html).length) {
                $('#comp-details').html('<p class="text-danger">No Details Available.</p>');
                return;
              }
              $('#comp-details').html(html);
            },
            error: function (xhr) {
              console.error('compensation-details error:', xhr.status, xhr.responseText);
              $('#comp-details').html('<p class="text-danger">No Details Available.</p>');
            },
            complete: function () {
              if ($loader.length) $loader.hide();
            }
          });
        });
      })();
    </script>

    <div role="tabpanel" aria-labelledby="comp-tab" id="comp-details">
      @php
        $firstCompensation = isset($compensations) ? $compensations->first() : null;
      @endphp

      @if ($firstCompensation)
        {{-- Header Section --}}
        <div class="compensation-header p-3 border-bottom">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Compensation ID - {{ $firstCompensation->id ?? '' }}</h4>
            <div>
              <a href="{{ route('staff.compensation.create', $firstCompensation->id ?? 0) }}" class="btn btn-sm btn-primary">
                <i class="ico icon-outline-pen-2"></i> Edit
              </a>
              <a href="{{ route('staff.compensation.create') }}" class="btn btn-sm btn-success">
                <i class="ico icon-outline-plus"></i> Add
              </a>
              <button class="btn btn-sm btn-light">
                <i class="ico icon-outline-menu"></i>
              </button>
            </div>
          </div>
        </div>

        {{-- Basic Info Section --}}
        <div class="compensation-info p-3">
          <div class="row mb-3">
            <div class="col-2 text-center">
              <div class="compensation-icon bg-light p-3 rounded">
                <i class="ico icon-outline-document" style="font-size: 48px; color: #6c757d;"></i>
              </div>
            </div>
            <div class="col-10">
              <div class="row">
                <div class="col-4">
                  <div class="info-item mb-2">
                    <strong>Doc Number:</strong><br>
                    <span class="text-muted">{{ $firstCompensation->doc_no ?? '—' }}</span>
                  </div>
                  <div class="info-item mb-2">
                    <strong>Country:</strong><br>
                    <span class="text-muted">India</span>
                  </div>
                </div>
                <div class="col-4">
                  <div class="info-item mb-2">
                    <strong>Transaction Type:</strong><br>
                    <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $firstCompensation->transaction_type ?? 'N/A')) }}</span>
                  </div>
                  <div class="info-item mb-2">
                    <strong>State:</strong><br>
                    <span class="text-muted">N/A</span>
                  </div>
                </div>
                <div class="col-4">
                  <div class="info-item mb-2">
                    <strong>Business Entity:</strong><br>
                    <span class="text-muted">HR Management</span>
                  </div>
                  <div class="info-item mb-2">
                    <strong>Industry:</strong><br>
                    <span class="text-muted">Human Resources</span>
                  </div>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-4">
                  <div class="info-item mb-2">
                    <strong>Employee:</strong><br>
                    <span class="text-muted">{{ $firstCompensation->employee ? $firstCompensation->employee->full_name ?? '—' : '—' }}</span>
                  </div>
                </div>
                <div class="col-4">
                  <div class="info-item mb-2">
                    <strong>City:</strong><br>
                    <span class="text-muted">N/A</span>
                  </div>
                </div>
                <div class="col-4">
                  <div class="info-item mb-2">
                    <strong>Sector:</strong><br>
                    <span class="text-muted">Human Resources</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Tabs Section --}}
        <div class="compensation-tabs">
          <ul class="nav nav-tabs" id="compensationDetailTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="basic-details-tab" data-bs-toggle="tab" data-bs-target="#basic-details" type="button" role="tab">
                Basic Details
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="compensation-settings-tab" data-bs-toggle="tab" data-bs-target="#compensation-settings" type="button" role="tab">
                Compensation Settings
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="transaction-details-tab" data-bs-toggle="tab" data-bs-target="#transaction-details" type="button" role="tab">
                Transaction Details
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="approval-history-tab" data-bs-toggle="tab" data-bs-target="#approval-history" type="button" role="tab">
                Approval & History
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="employee-policies-tab" data-bs-toggle="tab" data-bs-target="#employee-policies" type="button" role="tab">
                Employee Policies
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="documentation-tab" data-bs-toggle="tab" data-bs-target="#documentation" type="button" role="tab">
                Documentation
              </button>
            </li>
          </ul>

          <div class="tab-content p-3" id="compensationDetailTabContent">
            {{-- Basic Details Tab --}}
            <div class="tab-pane fade show active" id="basic-details" role="tabpanel">
              <h6 class="mb-3">Employee Settings</h6>
              
              <div class="row mb-3">
                <div class="col-md-4">
                  <strong>Employee Email:</strong><br>
                  <span>{{ $firstCompensation->employee ? $firstCompensation->employee->email ?? '—' : '—' }}</span>
                </div>
                <div class="col-md-4">
                  <strong>Website:</strong><br>
                  <a href="https://company.com/" class="text-decoration-none">https://company.com/</a>
                </div>
                <div class="col-md-4">
                  <strong>Office Phone:</strong><br>
                  <span>{{ $firstCompensation->employee ? $firstCompensation->employee->mobile ?? '—' : '—' }}</span>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4">
                  <strong>State:</strong><br>
                  <span>{{ $firstCompensation->employee && $firstCompensation->employee->departments ? $firstCompensation->employee->departments->name ?? 'N/A' : 'N/A' }}</span>
                </div>
                <div class="col-md-4">
                  <strong>City:</strong><br>
                  <span>N/A</span>
                </div>
                <div class="col-md-4">
                  <strong>Mobile:</strong><br>
                  <span>{{ $firstCompensation->employee ? $firstCompensation->employee->mobile ?? '—' : '—' }}</span>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-12">
                  <strong>Social Media Links:</strong><br>
                  <span class="text-muted">
                    <strong>Facebook:</strong> <a href="#" class="text-decoration-none">Link</a> |
                    <strong>Instagram:</strong> <a href="#" class="text-decoration-none">Link</a> |
                    <strong>LinkedIn:</strong> <a href="#" class="text-decoration-none">Link</a> |
                    <strong>Twitter:</strong> <a href="#" class="text-decoration-none">Link</a>
                  </span>
                </div>
              </div>

              {{-- Employee Information Table --}}
              <div class="table-responsive mt-4">
                <table class="table table-sm">
                  <thead>
                    <tr>
                      <th>Type</th>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>Email</th>
                      <th>Designation</th>
                      <th>Passport Copy</th>
                      <th>Emirates ID</th>
                      <th>Visa Copy</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Employee</td>
                      <td>{{ $firstCompensation->employee ? $firstCompensation->employee->full_name ?? '—' : '—' }}</td>
                      <td>{{ $firstCompensation->employee ? $firstCompensation->employee->mobile ?? '—' : '—' }}</td>
                      <td>{{ $firstCompensation->employee ? $firstCompensation->employee->email ?? '—' : '—' }}</td>
                      <td>{{ $firstCompensation->employee && $firstCompensation->employee->designations ? $firstCompensation->employee->designations->title ?? '—' : '—' }}</td>
                      <td>—</td>
                      <td>—</td>
                      <td>—</td>
                    </tr>
                    <tr>
                      <td>Manager</td>
                      <td>—</td>
                      <td>—</td>
                      <td>—</td>
                      <td>—</td>
                      <td>—</td>
                      <td>—</td>
                      <td>—</td>
                    </tr>
                    <tr>
                      <td>HR Contact</td>
                      <td>—</td>
                      <td>—</td>
                      <td>—</td>
                      <td>—</td>
                      <td>—</td>
                      <td>—</td>
                      <td>—</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            {{-- Compensation Settings Tab --}}
            <div class="tab-pane fade" id="compensation-settings" role="tabpanel">
              <h6 class="mb-3">Compensation Settings</h6>
              <div class="row">
                <div class="col-md-6">
                  <table class="table table-sm">
                    <tr>
                      <th>Transaction Type:</th>
                      <td>{{ ucfirst(str_replace('_', ' ', $firstCompensation->transaction_type ?? 'N/A')) }}</td>
                    </tr>
                    <tr>
                      <th>Current Status:</th>
                      <td>
                        @switch($firstCompensation->current_status ?? 'draft')
                          @case('draft')
                            <span class="badge badge-secondary">Draft</span>
                            @break
                          @case('pending')
                            <span class="badge badge-warning">Pending</span>
                            @break
                          @case('approved')
                            <span class="badge badge-success">Approved</span>
                            @break
                          @case('rejected')
                            <span class="badge badge-danger">Rejected</span>
                            @break
                          @default
                            <span class="badge badge-secondary">{{ $firstCompensation->current_status ?? 'Draft' }}</span>
                        @endswitch
                      </td>
                    </tr>
                    <tr>
                      <th>Document Date:</th>
                      <td>{{ $firstCompensation->doc_date ? \Carbon\Carbon::parse($firstCompensation->doc_date)->format('d-m-Y') : '—' }}</td>
                    </tr>
                  </table>
                </div>
                <div class="col-md-6">
                  <table class="table table-sm">
                    <tr>
                      <th>Effective Date:</th>
                      <td>{{ $firstCompensation->effective_date ? \Carbon\Carbon::parse($firstCompensation->effective_date)->format('d-m-Y') : '—' }}</td>
                    </tr>
                    <tr>
                      <th>Department:</th>
                      <td>{{ $firstCompensation->employee && $firstCompensation->employee->departments ? $firstCompensation->employee->departments->name ?? '—' : '—' }}</td>
                    </tr>
                    <tr>
                      <th>Created By:</th>
                      <td>{{ $firstCompensation->created_by ?? '—' }}</td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>

            {{-- Other Tab Placeholders --}}
            <div class="tab-pane fade" id="transaction-details" role="tabpanel">
              <h6>Transaction Details</h6>
              <p class="text-muted">Transaction details will be loaded here...</p>
            </div>

            <div class="tab-pane fade" id="approval-history" role="tabpanel">
              <h6>Approval & History</h6>
              <p class="text-muted">Approval history will be loaded here...</p>
            </div>

            <div class="tab-pane fade" id="employee-policies" role="tabpanel">
              <h6>Employee Policies</h6>
              <p class="text-muted">Employee policies will be loaded here...</p>
            </div>

            <div class="tab-pane fade" id="documentation" role="tabpanel">
              <h6>Documentation</h6>
              <p class="text-muted">Documentation will be loaded here...</p>
            </div>
          </div>
        </div>
      @else
        <div class="p-4 text-center text-muted">
          <h5>No Compensation Records Found</h5>
          <p>Click the button below to create your first compensation record.</p>
          <a href="{{ route('staff.compensation.create') }}" class="btn btn-primary">
            <i class="ico icon-outline-plus"></i> Create Compensation Record
          </a>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Auto-load compensation if 'active' parameter is present --}}
<script>
  $(document).ready(function() {
    console.log('Document ready, checking for active parameter...');
    var urlParams = new URLSearchParams(window.location.search);
    var activeId = urlParams.get('active');
    
    console.log('Active ID from URL:', activeId);
    console.log('All compensation items:', $('.comp-item').length);
    
    if (activeId) {
      console.log('Looking for compensation ID:', activeId);
      
      // List all available IDs for debugging
      $('.comp-item').each(function() {
        console.log('Found compensation item with ID:', $(this).data('id'));
      });
      
      // Find and click the compensation item with the matching ID
      var $targetItem = $('.comp-item[data-id="' + activeId + '"]');
      console.log('Target item found:', $targetItem.length);
      
      if ($targetItem.length) {
        console.log('Found compensation item, clicking...');
        $targetItem.trigger('click');
      } else {
        console.log('No compensation found with ID:', activeId);
        // If no item found, show error message
        $('#comp-details').html('<p class="text-warning">Compensation record #' + activeId + ' not found in the list.</p>');
      }
    }
  });
</script>

@endsection

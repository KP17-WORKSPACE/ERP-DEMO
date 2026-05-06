@extends('backEnd.newmasterpage')
@section('mainContent')

<script>
  function setResignationView(mode) {
    const leftNav = document.getElementById('leftSidebar');
    const content = document.querySelector('.content-container');

    const shortList = document.getElementById('resignationShortList');   // UL
    const longTable = document.getElementById('long-list');   // TABLE

    const filtersShort = document.getElementById('filters-short');
    const filtersLong  = document.getElementById('filters-long');

    if (mode === 'full') {
      // Sidebar full width, right pane hide
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
      // Compact: sidebar 3 cols, right pane show
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

  function list_style_new() {
    const leftNav = document.getElementById('leftSidebar');
    const cur = leftNav.dataset.view || 'compact';
    setResignationView(cur === 'compact' ? 'full' : 'compact');
  }

  function toggleLongFilters() {
    const filterField = document.querySelector('#filters-long .filter-field');
    if (filterField) {
      filterField.classList.toggle('d-none');
    }
  }

  // optional: ensure initial state
  document.addEventListener('DOMContentLoaded', function(){
    const leftNav = document.getElementById('leftSidebar');
    if (!leftNav.dataset.view) leftNav.dataset.view = 'compact';
  });
</script>

<style>
    .status-badge {
        font-size: 11px;
        padding: 0.25em 0.4em;
        border-radius: 4px;
    }
    .status-draft { background-color: #f8f9fa; color: #6c757d; }
    .status-submitted { background-color: #cff4fc; color: #055160; }
    .status-approved { background-color: #d1e7dd; color: #0f5132; }
    .status-rejected { background-color: #f8d7da; color: #721c24; }
    .status-completed { background-color: #d4edda; color: #155724; }

    .separation-badge {
        font-size: 11px;
        padding: 0.25em 0.4em;
        border-radius: 4px;
        background-color: #e9ecef;
        color: #495057;
    }

    .truncate-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<?php try { ?>

<aside class="left-nav col-3" id="leftSidebar">
    <div class="resizer" id="sidebarResizer"></div>

    {{-- SHORT (Compact) --}}
    <div class="short-list" id="filters-short">
        <h4 class="mb-2">Resignations</h4>

        {{ Form::open([
          'class' => 'form-horizontal',
          'files' => true,
          'route' => 'staff.resignation.list',
          'method' => 'get',
          'id' => 'resignation-search'
        ]) }}
        <div class="search-filter-container mb-4 d-flex">
            <div class="input-group flex-nowrap">
                <input type="text" name="staff_name" class="form-control" placeholder="Search by name or staff no"
                       aria-label="Search" aria-describedby="addon-wrapping" value="{{ request('staff_name') ?? '' }}">
            </div>
            <button type="submit" class="btn btn-light ms-2">
                <i class="ico icon-outline-magnifer"></i>
            </button>
            @if(in_array('staff.resignation.add', array_column($permissions->toArray(), 'route')))
            <a href="{{ route('staff.resignation.add') }}" class="btn btn-primary btn-sm ms-2" title="Add New">
                <i class="ico icon-outline-plus"></i>
            </a>
            @endif
            <button type="button" class="btn btn-light ms-2" id="list_style_button" onclick="list_style_new()">
                <i class="ico icon-outline-list-down"></i>
            </button>
        </div>
        {{ Form::close() }}
    </div>

    {{-- LONG (Full) --}}
    <div class="long-list d-none" id="filters-long">
        <div class="d-flex align-items-center justify-content-between">
            <h4 class="mb-2">Resignation List</h4>
            <div class="search-filter-container mb-0">
                @if(in_array('staff.resignation.add', array_column($permissions->toArray(), 'route')))
                <a href="{{ route('staff.resignation.add') }}" class="btn btn-primary btn-sm" title="Add New">
                    <i class="ico icon-outline-plus"></i>
                </a>
                @endif
                <button class="btn btn-light" onclick="toggleLongFilters()">
                    <i class="ico icon-outline-magnifer"></i>
                </button>
                <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>
        </div>

        <div class="search-filter-container mt-1 mb-4 filter-field d-none border">
            <div class="card">
                <div class="card-body">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'staff.resignation.list', 'method' => 'get', 'id' => 'resignation-filter']) }}
                    <div class="row">
                        <div class="col-md-4 mb-2 filter-field d-none">
                            <label class="form-label">Employee Name</label>
                            <input class="form-control" type="text" name="staff_name" value="{{ request('staff_name') }}" placeholder="Search by name or staff no">
                        </div>

                        <div class="col-md-3 mb-2 filter-field d-none">
                            <label class="form-label">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="">All Status</option>
                                <option value="draft" @if(request('status')=='draft') selected @endif>Draft</option>
                                <option value="submitted" @if(request('status')=='submitted') selected @endif>Submitted</option>
                                <option value="approved" @if(request('status')=='approved') selected @endif>Approved</option>
                                <option value="rejected" @if(request('status')=='rejected') selected @endif>Rejected</option>
                                <option value="completed" @if(request('status')=='completed') selected @endif>Completed</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-2 filter-field d-none">
                            <label class="form-label">Separation Type</label>
                            <select class="form-control" name="separation_type" id="separation_type">
                                <option value="">All Types</option>
                                <option value="Resignation" @if(request('separation_type')=='Resignation') selected @endif>Resignation</option>
                                <option value="Termination" @if(request('separation_type')=='Termination') selected @endif>Termination</option>
                                <option value="End of Contract" @if(request('separation_type')=='End of Contract') selected @endif>End of Contract</option>
                                <option value="Retirement" @if(request('separation_type')=='Retirement') selected @endif>Retirement</option>
                                <option value="Absconding" @if(request('separation_type')=='Absconding') selected @endif>Absconding</option>
                                <option value="Death" @if(request('separation_type')=='Death') selected @endif>Death</option>
                            </select>
                        </div>

                        <div class="col-md-2 filter-field d-none">
                            <button type="submit" class="btn btn-success mt-4 rounded-0" id="btnSubmit">Filter</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    {{-- LEFT NAV LIST (Short) --}}
    <div class="left-nav-list">
        <ul id="resignationShortList" class="nav flex-column nav-pills" role="tablist">
            @if ($resignations->count())
                @foreach ($resignations as $resignation)
                    <li class="nav-item w-100" role="presentation">
                        <button class="nav-link resignation-item {{ (isset($active_id) && $active_id == $resignation->id) ? 'active' : '' }}"
                                data-id="{{ $resignation->id }}" type="button" role="tab">
                            <div class="row w-100 align-items-center">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-4"><span class="form-control-plaintext fw-semibold">{{ $resignation->employee->staff_no ?? '—' }}</span></div>
                                        <div class="col-4"><span class="form-control-plaintext truncate-text">{{ $resignation->employee->departments->name ?? '—' }}</span></div>
                                        <div class="col-4"><span class="form-control-plaintext text-end">
                                            <span class="status-badge status-{{ $resignation->status }}">{{ ucfirst($resignation->status) }}</span>
                                        </span></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center text-muted xsmall mt-1">
                                        <span class="form-control-plaintext truncate-text">{{ $resignation->employee->full_name ?? '—' }}</span>
                                        <span class="form-control-plaintext truncate-text">{{ $resignation->created_at->format('d/m/Y') }}</span>
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
            <table id="long-list" class="table table-hover d-none" style="table-layout: fixed;width:100%">
                <thead class="text-center">
                    <tr>
                        <th style="width: 100px;">Staff No</th>
                        <th style="width: 160px;">Name</th>
                        <th style="width: 140px;">Department</th>
                        <th style="width: 140px;">Designation</th>
                        <th style="width: 120px;">Separation Type</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;">Submitted Date</th>
                        <th class="text-center" style="width: 110px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($resignations as $resignation)
                        <tr>
                            <td class="text-center">
                                <a href="javascript:void(0);" onclick="list_style_new()" class="resignation-item"
                                   data-id="{{ $resignation->id }}">{{ $resignation->employee->staff_no ?? '—' }}</a>
                            </td>
                            <td>
                                <a href="javascript:void(0);" onclick="list_style_new()" class="resignation-item"
                                   data-id="{{ $resignation->id }}">{{ $resignation->employee->full_name ?? '—' }}</a>
                            </td>
                            <td>{{ $resignation->employee->departments->name ?? '—' }}</td>
                            <td>{{ $resignation->employee->designations->title ?? '—' }}</td>
                            <td>
                                <span class="separation-badge">{{ $resignation->separation_type }}</span>
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-{{ $resignation->status }}">{{ ucfirst($resignation->status) }}</span>
                            </td>
                            <td>{{ $resignation->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-start align-items-center gap-1">
                                    @if(in_array('staff.resignation.edit', array_column($permissions->toArray(), 'route')))
                                    <a href="{{ route('staff.resignation.edit', $resignation->id) }}" class="btn btn-sm btn-light" title="Edit">
                                        <i class="ico icon-outline-pen-2" style="font-size:16px;"></i>
                                    </a>
                                    @endif
                                    <a href="javascript:void(0);" class="btn btn-sm btn-light" title="View Details" onclick="viewResignationDetails({{ $resignation->id }})">
                                        <i class="ico icon-outline-eye" style="font-size:16px;"></i>
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
    <div class="tab-content display-flex-tabs" id="resignationTabContent">

        {{-- Click handler: shortlist & longlist dono ke liye --}}
        <script>
            (function () {
                // Build URLs safely from Blade
                var detailsTpl = @json(route('staff.resignation.edit', ['id' => ':id']));

                function buildUrl(tpl, id) {
                    return tpl.replace(':id', encodeURIComponent(id));
                }

                // Event delegation (works for future DOM)
                $(document).on('click', '.resignation-item', function (e) {
                    e.preventDefault();

                    var id = $(this).data('id');
                    if (!id) return;

                    // Active UI
                    $('.resignation-item').removeClass('active');
                    $('.resignation-item[data-id="' + id + '"]').addClass('active');

                    // Update URL without reload
                    var newUrl = buildUrl(detailsTpl, id);
                    if (window.history && window.history.pushState) {
                        window.history.pushState({ path: newUrl }, '', newUrl);
                    }

                    // AJAX load (for now, redirect to edit page)
                    loadResignationDetails(id);
                });
            })();
        </script>

        <div role="tabpanel" aria-labelledby="resignation-tab" id="resignation-details">
            @php
                $firstResignation = isset($selectedResignation) && $selectedResignation 
                                ? $selectedResignation 
                                : ($resignations->first() ?? null);
            @endphp

            @if ($firstResignation)
                {{-- Show resignation details with tabs --}}
                <div class="container-fluid p-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ $firstResignation->employee->full_name ?? 'N/A' }} - Resignation Details</h5>
                                <div>
                                    <span class="status-badge status-{{ $firstResignation->status }} me-2">{{ ucfirst($firstResignation->status) }}</span>
                                    <a href="{{ route('staff.resignation.edit', $firstResignation->id) }}" class="btn btn-primary btn-sm">
                                        <i class="ico icon-outline-pen-2"></i> Edit
                                    </a>
                                </div>
                            </div>
                            
                            {{-- Employee Quick Info --}}
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <small class="text-muted">Staff No:</small><br>
                                    <strong>{{ $firstResignation->employee->staff_no ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Department:</small><br>
                                    <strong>{{ $firstResignation->employee->departments->name ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Designation:</small><br>
                                    <strong>{{ $firstResignation->employee->designations->title ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Separation Type:</small><br>
                                    <span class="separation-badge">{{ $firstResignation->separation_type }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="tab-wrap mb-3">
                            {{-- EOS Process Tabs --}}
                            <ul class="nav nav-tabs" id="eosTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="notice-tab"
                                        data-bs-toggle="tab" data-bs-target="#notice"
                                        type="button" role="tab">
                                        Resignation & Notice Period
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="handover-tab"
                                        data-bs-toggle="tab" data-bs-target="#handover"
                                        type="button" role="tab">
                                        Handover Process
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="asset-tab"
                                        data-bs-toggle="tab" data-bs-target="#asset"
                                        type="button" role="tab">
                                        Asset Clearance
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="it-tab"
                                        data-bs-toggle="tab" data-bs-target="#it"
                                        type="button" role="tab">
                                        IT & Access Clearance
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="eos-calc-tab"
                                        data-bs-toggle="tab" data-bs-target="#eos-calc"
                                        type="button" role="tab">
                                        EOS Calculation
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="final-settlement-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#final-settlement" type="button"
                                        role="tab">
                                        Final Settlement
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="legal-tab"
                                        data-bs-toggle="tab" data-bs-target="#legal"
                                        type="button" role="tab">
                                        Legal & Compliance
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="exit-interview-tab"
                                        data-bs-toggle="tab" data-bs-target="#exit-interview"
                                        type="button" role="tab">
                                        Exit Interview
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="approval-tab"
                                        data-bs-toggle="tab" data-bs-target="#approval"
                                        type="button" role="tab">
                                        Approval Status
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="documents-tab"
                                        data-bs-toggle="tab" data-bs-target="#documents"
                                        type="button" role="tab">
                                        Documents
                                    </button>
                                </li>
                            </ul>

                            {{-- Tab Content --}}
                            <div class="tab-content mt-3" id="eosTabContent">
                                <div class="tab-pane fade show active" id="notice" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Notice Information</h6>
                                            <p><strong>Resignation Date:</strong> {{ $firstResignation->resignation_date ? \Carbon\Carbon::parse($firstResignation->resignation_date)->format('d/m/Y') : 'N/A' }}</p>
                                            <p><strong>Last Working Date:</strong> {{ $firstResignation->last_working_date ? \Carbon\Carbon::parse($firstResignation->last_working_date)->format('d/m/Y') : 'N/A' }}</p>
                                            <p><strong>Notice Period:</strong> {{ $firstResignation->notice_period ?? 'N/A' }} days</p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-primary">Reason & Details</h6>
                                            <p><strong>Reason:</strong> {{ $firstResignation->reason ?? 'N/A' }}</p>
                                            <p><strong>Remarks:</strong> {{ $firstResignation->remarks ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="handover" role="tabpanel">
                                    <div class="alert alert-info">
                                        <i class="ico icon-outline-info"></i>
                                        Handover process details will be displayed here. This section tracks the transfer of responsibilities and tasks.
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="asset" role="tabpanel">
                                    <div class="alert alert-warning">
                                        <i class="ico icon-outline-package"></i>
                                        Asset clearance status and details will be shown here. This includes company property return tracking.
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="it" role="tabpanel">
                                    <div class="alert alert-info">
                                        <i class="ico icon-outline-computer"></i>
                                        IT systems and access clearance details will be displayed here.
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="eos-calc" role="tabpanel">
                                    <div class="alert alert-success">
                                        <i class="ico icon-outline-calculator"></i>
                                        End of Service benefits calculation and breakdown will be shown here.
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="final-settlement" role="tabpanel">
                                    <div class="alert alert-primary">
                                        <i class="ico icon-outline-money"></i>
                                        Final settlement details including payments and deductions will be displayed here.
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="legal" role="tabpanel">
                                    <div class="alert alert-secondary">
                                        <i class="ico icon-outline-document"></i>
                                        Legal compliance and regulatory requirements status will be shown here.
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="exit-interview" role="tabpanel">
                                    <div class="alert alert-info">
                                        <i class="ico icon-outline-user-speak"></i>
                                        Exit interview details and feedback will be displayed here.
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="approval" role="tabpanel">
                                    <div class="alert alert-primary">
                                        <i class="ico icon-outline-check"></i>
                                        Approval workflow and status tracking will be shown here.
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="documents" role="tabpanel">
                                    <div class="alert alert-warning">
                                        <i class="ico icon-outline-folder"></i>
                                        Related documents and attachments will be listed here.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                     style="min-height: 90vh;">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center mx-auto"
                             style="width: 80px; height: 80px; font-size: 36px;">
                            <i class="ico icon-outline-document"></i>
                        </div>
                        <h1 class="fw-bold mt-3">Resignations</h1>
                        <p class="text-muted">Select a resignation record from the list to view details</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Search & Filter functionality --}}
<script>
$(function(){
    // short search input (left compact box)
    var $q = $('#resignation-search input[name="staff_name"]');

    // cache current DOM nodes
    var $shortItems = $('#resignationShortList > li');   // each resignation li item
    var $longRows   = $('#long-list tbody > tr');  // each table row

    function norm(s){ return (s || '').toString().toLowerCase(); }
    function textOf($el){ return norm($el.text()); }

    function applyFilter(needle){
        if (!needle) {
            $shortItems.show();
            $longRows.show();
            return;
        }

        // shortlist filter
        $shortItems.each(function(){
            var $li = $(this);
            var hit = textOf($li).indexOf(needle) !== -1;
            $li.toggle(hit);
        });

        // long table filter
        $longRows.each(function(){
            var $tr = $(this);
            var hit = textOf($tr).indexOf(needle) !== -1;
            $tr.toggle(hit);
        });
    }

    // debounce for smooth typing
    var deb;
    $q.on('input', function(){
        clearTimeout(deb);
        var needle = norm(this.value);
        deb = setTimeout(function(){ applyFilter(needle); }, 120);
    });

    // quick clear on ESC
    $q.on('keydown', function(e){
        if (e.key === 'Escape') {
            $(this).val('');
            applyFilter('');
        }
    });
});

function loadResignationDetails(id) {
    // For now, redirect to edit page
    // In future, this can be replaced with AJAX to load details in right panel
    window.location.href = '{{ route("staff.resignation.edit", ":id") }}'.replace(':id', id);
}

function viewResignationDetails(id) {
    loadResignationDetails(id);
}
</script>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php } ?>

@endsection
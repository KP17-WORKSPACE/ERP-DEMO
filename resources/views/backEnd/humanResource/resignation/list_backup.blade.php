@extends('backEnd.newmasterpage')
@section('mainContent')
<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>
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
</style>

<script>
    function setResignationView(mode) {
        const leftNav = document.getElementById('leftSidebar');
        const content = document.querySelector('.content-container');
        const longTable = document.getElementById('long-list');
        const shortList = document.getElementById('resignationShortList');
        const filtersShort = document.getElementById('filters-short');
        const filtersLong = document.getElementById('filters-long');

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

    function toggleLongFilters() {
        const filterContainer = document.querySelector('#filters-long .filter-field');
        filterContainer.classList.toggle('d-none');
    }

    function list_style_new() {
        const view = document.getElementById('leftSidebar').dataset.view || 'compact';
        setResignationView(view === 'compact' ? 'full' : 'compact');
    }

    document.addEventListener('DOMContentLoaded', function() {
        setResignationView('compact');
    });
</script>

<div class="container-fluid" style="padding: 0;">
    <div class="row">
        <div class="col-3" id="leftSidebar" style="padding-right: 0;">
            {{-- LONG (Full) --}}
            <div class="long-list d-none" id="filters-long">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-2">Resignation List</h4>
                    <div class="search-filter-container mb-0">
                        <a href="{{ route('staff.resignation.add') }}" class="btn btn-primary btn-sm" title="Add New">
                            <i class="ico icon-outline-plus"></i> Add New
                        </a>
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
                                <div class="col-md-4 mb-2">
                                    <label class="form-label">Employee Name</label>
                                    <input class="form-control" type="text" name="staff_name" value="{{ request('staff_name') }}" placeholder="Search by name or staff no">
                                </div>

                                <div class="col-md-3 mb-2">
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

                                <div class="col-md-3 mb-2">
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

                                <div class="col-md-2">
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
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-2">Resignation List</h4>
                    <div class="search-filter-container mb-0">
                        <a href="{{ route('staff.resignation.add') }}" class="btn btn-primary btn-sm" title="Add New">
                            <i class="ico icon-outline-plus"></i>
                        </a>
                        <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                            <i class="ico icon-outline-list-down"></i>
                        </button>
                    </div>
                </div>

                <ul class="nav-list nav-staff" id="resignationShortList">
                    @forelse ($resignations as $resignation)
                        <li class="nav-item">
                            <a href="javascript:void(0);" class="nav-link resignation-item" data-id="{{ $resignation->id }}">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="flex-grow-1">
                                        <div class="staff-name fw-bold">{{ $resignation->employee->full_name ?? 'N/A' }}</div>
                                        <div class="staff-details small text-muted">
                                            <div>{{ $resignation->employee->staff_no ?? 'N/A' }}</div>
                                            <div>{{ $resignation->employee->departments->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="mt-1">
                                            <span class="separation-badge">{{ $resignation->separation_type }}</span>
                                            <span class="status-badge status-{{ $resignation->status }}">{{ ucfirst($resignation->status) }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $resignation->created_at->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="nav-item">
                            <div class="text-center py-4 text-muted">
                                <i class="ico icon-outline-document" style="font-size: 48px;"></i>
                                <div class="mt-2">No resignation records found</div>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>

            {{-- FULL TABLE VIEW --}}
            <div class="long-list d-none" id="long-list">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Employee</th>
                                <th>Staff No</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Separation Type</th>
                                <th>Status</th>
                                <th>Submitted Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($resignations as $resignation)
                                <tr>
                                    <td>
                                        <a href="javascript:void(0);" onclick="list_style_new()" class="resignation-item" data-id="{{ $resignation->id }}">
                                            {{ $resignation->employee->full_name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ $resignation->employee->staff_no ?? 'N/A' }}</td>
                                    <td>{{ $resignation->employee->departments->name ?? 'N/A' }}</td>
                                    <td>{{ $resignation->employee->designations->title ?? 'N/A' }}</td>
                                    <td>
                                        <span class="separation-badge">{{ $resignation->separation_type }}</span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $resignation->status }}">{{ ucfirst($resignation->status) }}</span>
                                    </td>
                                    <td>{{ $resignation->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-start align-items-center gap-1">
                                            <a href="{{ route('staff.resignation.edit', $resignation->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="ico icon-outline-pen-2" style="font-size:14px;"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-info" title="View Details" onclick="viewResignationDetails({{ $resignation->id }})">
                                                <i class="ico icon-outline-eye" style="font-size:14px;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="ico icon-outline-document text-muted" style="font-size: 48px;"></i>
                                        <div class="mt-2 text-muted">No resignation records found</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT CONTENT AREA --}}
        <div class="col-9 content-container" style="padding-left: 0;">
            <div class="content-wrapper" style="background: #f8f9fa; height: 100vh; padding: 20px;">
                <div id="resignation-details-container" style="display: none;">
                    <!-- Resignation details will be loaded here -->
                </div>

                <div id="default-content" class="text-center" style="margin-top: 100px;">
                    <i class="ico icon-outline-document" style="font-size: 80px; color: #dee2e6;"></i>
                    <h4 class="text-muted mt-3">Select a resignation record</h4>
                    <p class="text-muted">Click on any resignation from the list to view details</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle resignation item clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.resignation-item')) {
            e.preventDefault();
            const resignationId = e.target.closest('.resignation-item').dataset.id;
            loadResignationDetails(resignationId);
            
            // Update active state
            document.querySelectorAll('.resignation-item').forEach(item => {
                item.closest('.nav-item, tr').classList.remove('active');
            });
            e.target.closest('.nav-item, tr').classList.add('active');
        }
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

@endsection
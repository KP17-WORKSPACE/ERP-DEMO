@extends('backEnd.newmasterpage')
@section('mainContent')

<script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <script>
        function setResignationView(mode) {
            const leftNav = document.getElementById('leftSidebar');
            const content = document.querySelector('.content-container');

            const shortList = document.getElementById('resignationShortList');   // UL
            const longTable = document.getElementById('long-list');   // TABLE

            const filtersShort = document.getElementById('filters-short');
            const filtersLong = document.getElementById('filters-long');

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
            const filterField = document.getElementById('long-filters-box');
            if (filterField) {
                filterField.classList.toggle('d-none');
            }
        }

        // optional: ensure initial state
        document.addEventListener('DOMContentLoaded', function () {
            const leftNav = document.getElementById('leftSidebar');
            const requestedView = @json(request('view'));
            if (requestedView === 'full') {
                setResignationView('full');
            } else if (!leftNav.dataset.view) {
                leftNav.dataset.view = 'compact';
            }
        });
    </script>

    <style>
        .status-badge {
            font-size: 11px;
            padding: 0.25em 0.4em;
            border-radius: 4px;
        }

        .status-draft {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        .status-submitted {
            background-color: #cff4fc;
            color: #055160;
        }

        .status-approved {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }

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

        #long-list th,
        #long-list td {
            vertical-align: middle;
            font-size: 12px;
        }

        #long-list .resignation-ellipsis {
            display: block;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    $permissionRoutes = $permissions->pluck('route')->filter()->toArray();
    $hasRoutePermissions = count($permissionRoutes) > 0;
    $canEditResignation = Auth::user()->role_id == 1 || !$hasRoutePermissions || in_array('staff.resignation.edit', $permissionRoutes);
    $canDeleteResignation = Auth::user()->role_id == 1 || !$hasRoutePermissions || in_array('staff.resignation.delete', $permissionRoutes);
    $canDownloadResignationAttachment = Auth::user()->role_id == 1 || !$hasRoutePermissions || in_array('staff.resignation.downloadAttachment', $permissionRoutes);
        ?>

    <?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>

        {{-- SHORT (Compact) --}}
        <div class="short-list" id="filters-short">
            <h4 class="mb-2" style=" margin-left: -6px;">End of Service</h4>

            {{ Form::open([
            'class' => 'form-horizontal',
            'files' => true,
            'route' => 'staff.resignation.list',
            'method' => 'get',
            'id' => 'resignation-search'
        ]) }}
            <div class="search-filter-container mb-4" style=" margin-left: -6px;">
                <div class="input-group flex-nowrap">
                    <input type="text" name="staff_name" class="form-control" placeholder="Search by ID / Reason"
                        aria-label="Search" aria-describedby="addon-wrapping" value="{{ request('staff_name') ?? '' }}">
                </div>
                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>
            {{ Form::close() }}
        </div>

        {{-- LONG (Full) --}}
        <div class="long-list d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0">End of Service List</h4>
                <div class="search-filter-container mb-0 d-flex align-items-center gap-2">
                    <input type="text" id="tableSearch" class="form-control" placeholder="Search"
                        style="width:auto; min-width:250px;">
                    <button type="button" class="btn btn-light" id="exportExcelResignations" title="Export to Excel">
                        <i class="ico icon-outline-export text-success"></i> Export
                    </button>
                    <button type="button" class="btn btn-light" onclick="toggleLongFilters()" title="Search / Filter">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()"
                        title="Compact list">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                    <!-- <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false" title="Menu">
                                <i class="ico icon-outline-hamburger-menu"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(in_array('staff.resignation.add', array_column($permissions->toArray(), 'route')))
                                    <li>
                                        <a class="dropdown-item" href="{{ route('staff.resignation.add') }}">
                                            <i class="ico icon-outline-add-square text-success"></i> New End of Service
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a class="dropdown-item" href="{{ route('staff.resignation.list') }}">
                                        <i class="ico icon-outline-list-down text-success"></i> End of Service List
                                    </a>
                                </li>
                            </ul>
                        </div> -->
                </div>
            </div>

            <div id="long-filters-box" class="search-filter-container mt-1 mb-4 filter-field d-none border">
                <div class="card" style="width:100%">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'staff.resignation.list', 'method' => 'get', 'id' => 'resignation-filter']) }}
                        <input type="hidden" name="view" value="full">
                        <div class="row">
                            <div class="col-2 mb-2">
                                <label class="form-label">Request No.</label>
                                <input class="form-control" type="text" name="request_no"
                                    value="{{ request('request_no') }}" placeholder="Request No.">
                            </div>

                            <div class="col-2 mb-2">
                                <label class="form-label">Type</label>
                                <select class="form-control" name="separation_type" id="separation_type">
                                    <option value="">All</option>
                                    <option value="resignation" @if(request('separation_type') == 'resignation') selected
                                    @endif>Resignation</option>
                                    <option value="termination" @if(request('separation_type') == 'termination') selected
                                    @endif>Termination</option>
                                    <option value="end_of_contract" @if(request('separation_type') == 'end_of_contract')
                                    selected @endif>End of Contract</option>
                                    <option value="retirement" @if(request('separation_type') == 'retirement') selected
                                    @endif>Retirement</option>
                                    <option value="absconding" @if(request('separation_type') == 'absconding') selected
                                    @endif>Absconding</option>
                                    <option value="death" @if(request('separation_type') == 'death') selected @endif>Death
                                    </option>
                                </select>
                            </div>

                            <div class="col-2 mb-2">
                                <label class="form-label">Category</label>
                                <select class="form-control" name="resignation_type">
                                    <option value="">All</option>
                                    <option value="voluntary" @if(request('resignation_type') == 'voluntary') selected @endif>
                                        Voluntary</option>
                                    <option value="involuntary" @if(request('resignation_type') == 'involuntary') selected
                                    @endif>Involuntary</option>
                                    <option value="mutual_separation" @if(request('resignation_type') == 'mutual_separation')
                                    selected @endif>Mutual Separation</option>
                                </select>
                            </div>

                            <div class="col-2 mb-2">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="">All</option>
                                    <option value="draft" @if(request('status') == 'draft') selected @endif>Draft</option>
                                    <option value="submitted" @if(request('status') == 'submitted') selected @endif>Submitted
                                    </option>
                                    <option value="approved" @if(request('status') == 'approved') selected @endif>Approved
                                    </option>
                                    <option value="rejected" @if(request('status') == 'rejected') selected @endif>Rejected
                                    </option>
                                    <option value="completed" @if(request('status') == 'completed') selected @endif>Completed
                                    </option>
                                </select>
                            </div>

                            <div class="col-2 mb-2">
                                <label class="form-label">From</label>
                                <input class="form-control" type="date" name="from" value="{{ request('from') }}">
                            </div>

                            <div class="col-2 mb-2">
                                <label class="form-label">To</label>
                                <input class="form-control" type="date" name="to" value="{{ request('to') }}">
                            </div>

                            <div class="col-2 mb-2">
                                <label class="form-label">Attachment</label>
                                <select class="form-control" name="attachment">
                                    <option value="">Select</option>
                                    <option value="1" @if(request('attachment') == '1') selected @endif>With Attachments Only
                                    </option>
                                    <option value="2" @if(request('attachment') == '2') selected @endif>Without Attachments
                                        Only</option>
                                    <option value="all" @if(request('attachment') === 'all') selected @endif>All</option>
                                </select>
                            </div>

                            <div class="col-2 mb-2">
                                <label class="form-label">Filter By</label>
                                <select class="form-control" name="filter_by">
                                    <option value="">-Select-</option>
                                    <option value="today" @if(request('filter_by') === 'today') selected @endif>Today</option>
                                    <option value="this_week" @if(request('filter_by') === 'this_week') selected @endif>This
                                        Week</option>
                                    <option value="last_week" @if(request('filter_by') === 'last_week') selected @endif>Last
                                        Week</option>
                                    <option value="this_month" @if(request('filter_by') === 'this_month') selected @endif>This
                                        Month</option>
                                    <option value="last_month" @if(request('filter_by') === 'last_month') selected @endif>Last
                                        Month</option>
                                    <option value="this_quarter" @if(request('filter_by') === 'this_quarter') selected @endif>
                                        This Quarter</option>
                                    <option value="pre_quarter" @if(request('filter_by') === 'pre_quarter') selected @endif>
                                        Pre Quarter</option>
                                    <option value="this_year" @if(request('filter_by') === 'this_year') selected @endif>This
                                        Year</option>
                                    <option value="last_year" @if(request('filter_by') === 'last_year') selected @endif>Last
                                        Year</option>
                                </select>
                            </div>

                            <div class="col-4 mb-2 filter-field">
                                <label class="form-label d-block">&nbsp;</label>
                                <div class="d-flex align-items-center justify-content-start">
                                    <button type="submit" class="btn btn-light me-2" id="btnSubmit" style="width:auto;">
                                        <i class="ico icon-outline-magnifer text-success"></i> Filter
                                    </button>
                                    <a href="{{ route('staff.resignation.list') }}" class="btn btn-light"
                                        style="width:auto;">
                                        <i class="ico icon-outline-refresh text-success"></i> Reset
                                    </a>
                                </div>
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
                        @php $requestNo = $resignation->display_request_no ?? ($resignation->request_no ?: 'N/A'); @endphp
                        <li class="nav-item w-100" role="presentation">
                            <button
                                class="nav-link resignation-item {{ (isset($active_id) && $active_id == $resignation->id) ? 'active' : '' }}"
                                data-id="{{ $resignation->id }}" type="button">
                                <div class="row w-100 align-items-center">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-4"><span
                                                    class="form-control-plaintext fw-semibold">{{ $requestNo }}</span></div>
                                            <div class="col-4"><span
                                                    class="form-control-plaintext truncate-text">{{ $resignation->employee->departments->name ?? '—' }}</span>
                                            </div>
                                            <div class="col-4"><span class="form-control-plaintext text-end">
                                                    <span
                                                        class="status-badge status-{{ $resignation->status }}">{{ ucfirst($resignation->status) }}</span>
                                                </span></div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center text-muted xsmall mt-1">
                                            <span
                                                class="form-control-plaintext truncate-text">{{ $resignation->employee->full_name ?? '—' }}</span>
                                            <span
                                                class="form-control-plaintext truncate-text">{{ $resignation->created_at->format('d/m/Y') }}</span>
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
                            <th style="width: 9%;" title="Document No.">Document No.</th>
                            <th style="width: 15%;" title="Employee Name">Employee Name</th>
                            <th style="width: 10%;" title="Department">Department</th>
                            <th style="width: 10%;" title="Designation">Designation</th>
                            <th style="width: 13%;" title="Reporting Manager">Reporting Manager</th>
                            <th style="width: 10%;" title="Separation Type">Separation Type</th>
                            <th style="width: 8%;" title="Initiated By">Initiated By</th>
                            <th style="width: 10%;" title="Reason Category">Reason Category</th>
                            <th style="width: 7%;" title="Status">Status</th>
                            <th class="text-center" style="width: 8%;" title="Action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($resignations as $resignation)
                            @php
                                $requestNo = $resignation->display_request_no ?? ($resignation->request_no ?: 'N/A');
                                $employeeName = optional($resignation->employee)->full_name ?: '-';
                                $departmentName = optional($resignation->department)->name ?: optional(optional($resignation->employee)->departments)->name ?: '-';
                                $designationName = optional($resignation->designation)->title ?: optional(optional($resignation->employee)->designations)->title ?: '-';
                                $reportingManagerName = optional($resignation->reportingManager)->full_name ?: '-';
                                $formatText = function ($value) {
                                    return $value ? ucwords(str_replace('_', ' ', $value)) : '-';
                                };
                                $separationType = $formatText($resignation->separation_type);
                                $initiatedBy = $formatText($resignation->initiated_by);
                                $reasonCategory = $formatText($resignation->reason_category);
                                $statusText = $formatText($resignation->status);
                                $hasAttachment = $resignation->documents && $resignation->documents->filter(function ($document) {
                                    return !empty($document->attachment);
                                })->isNotEmpty();
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <a href="javascript:void(0);" onclick="list_style_new()" class="resignation-item"
                                        data-id="{{ $resignation->id }}" title="{{ $requestNo }}">{{ $requestNo }}</a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" onclick="list_style_new()" class="resignation-item"
                                        data-id="{{ $resignation->id }}" title="{{ $employeeName }}">
                                        <span class="resignation-ellipsis">{{ $employeeName }}</span>
                                    </a>
                                </td>
                                <td title="{{ $departmentName }}"><span
                                        class="resignation-ellipsis">{{ $departmentName }}</span></td>
                                <td title="{{ $designationName }}"><span
                                        class="resignation-ellipsis">{{ $designationName }}</span></td>
                                <td title="{{ $reportingManagerName }}"><span
                                        class="resignation-ellipsis">{{ $reportingManagerName }}</span></td>
                                <td title="{{ $separationType }}"><span
                                        class="resignation-ellipsis">{{ $separationType }}</span></td>
                                <td title="{{ $initiatedBy }}"><span class="resignation-ellipsis">{{ $initiatedBy }}</span></td>
                                <td title="{{ $reasonCategory }}"><span
                                        class="resignation-ellipsis">{{ $reasonCategory }}</span></td>
                                <td class="text-center" title="{{ $statusText }}">
                                    <span class="status-badge status-{{ $resignation->status }}">{{ $statusText }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1 flex-nowrap">
                                        @if($canEditResignation)
                                            <a href="{{ route('staff.resignation.edit', $resignation->id) }}"
                                                class="btn btn-sm btn-light" title="Edit">
                                                <i class="ico icon-outline-pen-2 text-dark" style="font-size:16px;"></i>
                                            </a>
                                        @endif
                                        @if($canDeleteResignation)
                                            <form action="{{ route('staff.resignation.delete', $resignation->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Delete this End of Service record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light" title="Delete">
                                                    <i class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                        style="font-size:16px;"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($hasAttachment && $canDownloadResignationAttachment)
                                            <a href="{{ route('staff.resignation.downloadAttachment', $resignation->id) }}"
                                                class="btn btn-sm btn-light" title="Download Attachment">
                                                <i class="ico icon-bold-download-minimalistic text-dark"
                                                    style="font-size:16px;"></i>
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-sm btn-light" title="No Attachment" disabled>
                                                <i class="ico icon-bold-download-minimalistic text-muted"
                                                    style="font-size:16px;"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                                @if(false)
                                    <td class="d-none">
                                        <a href="javascript:void(0);" onclick="list_style_new()" class="resignation-item"
                                            data-id="{{ $resignation->id }}">{{ $resignation->employee->full_name ?? '—' }}</a>
                                    </td>
                                    <td>{{ $resignation->employee->departments->name ?? '—' }}</td>
                                    <td>{{ $resignation->employee->designations->title ?? '—' }}</td>
                                    <td>
                                        <span class="separation-badge">{{ $resignation->separation_type }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="status-badge status-{{ $resignation->status }}">{{ ucfirst($resignation->status) }}</span>
                                    </td>
                                    <td>{{ $resignation->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-start align-items-center gap-1">
                                            <a href="{{ route('staff.resignation.edit', $resignation->id) }}"
                                                class="btn btn-sm btn-light" title="Edit">
                                                <i class="ico icon-outline-pen-2 text-dark" style="font-size:16px;"></i>
                                            </a>


                                            <form action="{{ route('staff.resignation.delete', $resignation->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Delete this End of Service record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light" title="Delete">
                                                    <i class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                        style="font-size:16px;"></i>
                                                </button>
                                            </form>
                                            @if($hasAttachment)
                                                <a href="{{ route('staff.resignation.downloadAttachment', $resignation->id) }}"
                                                    class="btn btn-sm btn-light" title="Download Attachment">
                                                    <i class="ico icon-bold-download-minimalistic text-dark"
                                                        style="font-size:16px;"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                @endif
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
                    var indexUrl = @json(route('staff.resignation.list'));
                    var currentParams = @json(request()->except('active'));

                    function buildUrl(id) {
                        var params = new URLSearchParams(currentParams);
                        params.set('active', id);
                        return indexUrl + '?' + params.toString();
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
                        var newUrl = buildUrl(id);

                        // Reload page to show details in right panel (no AJAX yet)
                        window.location.href = newUrl;
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
                    @include('backEnd.humanResource.resignation.partials._details', ['resignation' => $firstResignation, 'permissions' => $permissions])
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
        $(function () {
            // short search input (left compact box)
            var $q = $('#resignation-search input[name="staff_name"]');
            var $tableSearch = $('#tableSearch');

            // cache current DOM nodes
            var $shortItems = $('#resignationShortList > li');   // each resignation li item
            var $longRows = $('#long-list tbody > tr');  // each table row

            function norm(s) { return (s || '').toString().toLowerCase(); }
            function textOf($el) { return norm($el.text()); }

            function applyFilter(needle) {
                if (!needle) {
                    $shortItems.show();
                    $longRows.show();
                    return;
                }

                // shortlist filter
                $shortItems.each(function () {
                    var $li = $(this);
                    var hit = textOf($li).indexOf(needle) !== -1;
                    $li.toggle(hit);
                });

                // long table filter
                $longRows.each(function () {
                    var $tr = $(this);
                    var hit = textOf($tr).indexOf(needle) !== -1;
                    $tr.toggle(hit);
                });
            }

            // debounce for smooth typing
            var deb;
            $q.add($tableSearch).on('input', function () {
                clearTimeout(deb);
                var needle = norm(this.value);
                deb = setTimeout(function () { applyFilter(needle); }, 120);
            });

            // quick clear on ESC
            $q.add($tableSearch).on('keydown', function (e) {
                if (e.key === 'Escape') {
                    $(this).val('');
                    applyFilter('');
                }
            });

            $('#exportExcelResignations').on('click', function (e) {
                e.preventDefault();

                var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
                var totalResignations = @json($resignations->count() ?? 0);
                var dateFrom = @json(request('from') ?? '');
                var dateTo = @json(request('to') ?? '');

                var $table = $('#long-list');

                var visibleColIndexes = [];
                var headerLabels = [];
                var lastIndex = $table.find('thead tr th').length - 1;

                $table.find('thead tr th').each(function (i) {
                    if (i === lastIndex) return; // skip actions
                    if ($(this).css('display') !== 'none') {
                        var label = $(this).text().trim();
                        if (['actions', 'action', 'actions '].includes(label.toLowerCase().trim())) {
                            return;
                        }
                        visibleColIndexes.push(i);
                        headerLabels.push(label);
                    }
                });

                function formatDMY(value) {
                    if (!value) return '-';
                    var normalized = value.trim().replace(/\s+/g, '');
                    var parts = normalized.split(/[\/\-\.]/);
                    if (parts.length === 3) {
                        if (parts[0].length === 4) {
                            return parts[2] + '/' + parts[1] + '/' + parts[0];
                        }
                        return parts[0] + '/' + parts[1] + '/' + parts[2];
                    }
                    return value;
                }

                var rows = [];
                rows.push([companyName]);
                rows.push(['End Of Service (' + totalResignations + ')']);

                if (dateFrom || dateTo) {
                    var parts = [];
                    if (dateFrom) { parts.push('From: ' + formatDMY(dateFrom)); }
                    if (dateTo) { parts.push('To: ' + formatDMY(dateTo)); }
                    rows.push([parts.join('  ')]);
                }

                rows.push([]);
                rows.push(headerLabels);

                $table.find('tbody tr').each(function () {
                    var $cells = $(this).find('td');
                    var rowData = [];
                    visibleColIndexes.forEach(function (i) {
                        var cellText = $cells.eq(i).text().trim().replace(/\s+/g, ' ');
                        rowData.push(cellText);
                    });
                    rows.push(rowData);
                });

                var hdrIdx = rows.indexOf(headerLabels);
                if (hdrIdx < 0) hdrIdx = rows.length - 1;

                if (rows.length <= hdrIdx + 1) {
                    alert('No data available for export');
                    return;
                }

                var N = headerLabels.length || 1;
                var workbook  = new ExcelJS.Workbook();
                var worksheet = workbook.addWorksheet('End Of Service');
                var wsCols = [];
                for (var ci = 0; ci < N; ci++) { wsCols.push({ width: 22 }); }
                worksheet.columns = wsCols;

                var wsRowNum = 0;
                for (var ri = 0; ri < hdrIdx; ri++) {
                    if (!(rows[ri] && rows[ri][0])) continue;
                    wsRowNum++;
                    var wsRow = worksheet.addRow([]);
                    wsRow.height = ri === 0 ? 26 : ri === 1 ? 20 : 16;
                    if (N > 1) worksheet.mergeCells(wsRowNum, 1, wsRowNum, N);
                    wsRow.getCell(1).value = rows[ri][0] || '';
                    if (ri === 0) wsRow.getCell(1).font = { bold: true, size: 14 };
                    else if (ri === 1) wsRow.getCell(1).font = { bold: true, size: 12 };
                    wsRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
                }

                wsRowNum++;
                worksheet.addRow([]);

                wsRowNum++;
                var wsHdrRow = worksheet.addRow(headerLabels);
                wsHdrRow.height = 20;
                wsHdrRow.eachCell({ includeEmpty: true }, function (cell) {
                    cell.font      = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
                    cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
                    cell.alignment = { horizontal: 'center', vertical: 'middle' };
                    cell.border    = {
                        top:    { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        left:   { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        bottom: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                        right:  { style: 'thin', color: { argb: 'FFB8C4D8' } }
                    };
                });

                for (var di = hdrIdx + 1; di < rows.length; di++) {
                    var wsDataRow = worksheet.addRow(rows[di]);
                    wsDataRow.eachCell({ includeEmpty: true }, function (cell) {
                        cell.border = {
                            top:    { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            left:   { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            right:  { style: 'thin', color: { argb: 'FFCCCCCC' } }
                        };
                    });
                }

                workbook.xlsx.writeBuffer().then(function (buffer) {
                    var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    function pad(n) { return n < 10 ? '0' + n : n; }
                    var d = new Date();
                    var filename = 'end_of_service_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                    saveAs(blob, filename);
                });
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
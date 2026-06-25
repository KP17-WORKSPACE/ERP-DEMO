@extends('backEnd.newmasterpage')
@section('mainContent')
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    $permissionRoutes = $permissions->pluck('route')->filter()->toArray();
    $hasRoutePermissions = count($permissionRoutes) > 0;
    $canEditResignation = Auth::user()->role_id == 1 || !$hasRoutePermissions || in_array('staff.resignation.edit', $permissionRoutes);
    $canDeleteResignation = Auth::user()->role_id == 1 || !$hasRoutePermissions || in_array('staff.resignation.delete', $permissionRoutes);
    $canAddResignation = Auth::user()->role_id == 1 || !$hasRoutePermissions || in_array('staff.resignation.add', $permissionRoutes);
        ?>
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

    <div class="d-flex w-100 h-100" style="min-height: calc(100vh - 100px); position: relative;">
        <aside class="left-nav col-3" id="leftSidebar" data-view="compact">
            <div class="resizer" id="sidebarResizer"></div>

            <div class="short-list" id="filters-short">
                <h4 class="mb-2" style="margin-left: -6px;">End of Service</h4>
                <div class="search-filter-container mb-4" style="margin-left: -6px;">
                    <div class="input-group flex-nowrap">
                        <input type="text" id="shortSearch" class="form-control" placeholder="Search by ID / Name"
                            aria-label="Search">
                    </div>
                    <button type="button" class="btn btn-light" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>

            <div class="long-list d-none" id="filters-long">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h4 class="mb-0">End of Service List</h4>
                    <div class="search-filter-container mb-0 d-flex align-items-center gap-2">
                        <input type="text" id="tableSearch" class="form-control" placeholder="Search">
                        <button type="button" class="btn btn-light" id="exportExcelResignations" title="Export">
                            <i class="ico icon-outline-export text-success"></i> Export
                        </button>
                        <button type="button" class="btn btn-light" onclick="toggleLongFilters()" title="Search / Filter">
                            <i class="ico icon-outline-magnifer"></i>
                        </button>
                        <button type="button" class="btn btn-light" onclick="list_style_new()">
                            <i class="ico icon-outline-list-down"></i>
                        </button>
                    </div>
                </div>

                <div id="long-filters-box" class="search-filter-container mt-1 mb-4 filter-field d-none border">
                    <div class="card" style="width:100%">
                        <div class="card-body">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'staff.resignation.list', 'method' => 'get', 'id' => 'resignation-filter']) }}
                            <input type="hidden" name="view" value="full">
                            <div class="row">
                                <div class="col-2 mb-2">
                                    <label class="form-label">Document No.</label>
                                    <input class="form-control" type="text" name="request_no"
                                        value="{{ request('request_no') }}">
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Employee Name</label>
                                    <input class="form-control" type="text" name="employee_name"
                                        value="{{ request('employee_name') }}">
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Department</label>
                                    <input class="form-control" type="text" name="department"
                                        value="{{ request('department') }}">
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Designation</label>
                                    <input class="form-control" type="text" name="designation"
                                        value="{{ request('designation') }}">
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Reporting Manager</label>
                                    <input class="form-control" type="text" name="reporting_manager"
                                        value="{{ request('reporting_manager') }}">
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Separation Type</label>
                                    <select class="form-control" name="separation_type">
                                        <option value="">All</option>
                                        <option value="resignation" @if(request('separation_type') == 'resignation') selected
                                        @endif>Resignation</option>
                                        <option value="termination" @if(request('separation_type') == 'termination') selected
                                        @endif>Termination</option>
                                    </select>
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="status">
                                        <option value="">All</option>
                                        <option value="draft" @if(request('status') == 'draft') selected @endif>Draft</option>
                                        <option value="submitted" @if(request('status') == 'submitted') selected @endif>
                                            Submitted</option>
                                        <option value="approved" @if(request('status') == 'approved') selected @endif>Approved
                                        </option>
                                        <option value="rejected" @if(request('status') == 'rejected') selected @endif>Rejected
                                        </option>
                                        <option value="completed" @if(request('status') == 'completed') selected @endif>
                                            Completed</option>
                                    </select>
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Date From</label>
                                    <input class="form-control" type="date" name="date_from"
                                        value="{{ request('date_from') }}">
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Date To</label>
                                    <input class="form-control" type="date" name="date_to" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-2 mb-2 filter-field d-flex align-items-end gap-2">
                                    <div class="col-4 mb-2 filter-field">
                                        <label class="form-label d-block">&nbsp;</label>
                                        <div class="d-flex align-items-center justify-content-start">
                                            <button type="submit" class="btn btn-light me-2" style="width:auto;">
                                                <i class="ico icon-outline-magnifer text-success"></i> Filter
                                            </button>
                                            <a href="{{ route('staff.resignation.list') }}?view=full" class="btn btn-light"
                                                style="width:auto;">
                                                <i class="ico icon-outline-refresh text-success"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="left-nav-list">
                <ul id="resignationShortList" class="nav flex-column nav-pills" role="tablist">
                    @if ($resignations->count())
                        @foreach ($resignations as $resignation)
                            @php
                                $requestNo = $resignation->request_no ?: 'N/A';
                                $isActive = (request('active') == $resignation->id) || (!request('active') && !isset($active_id) && $loop->first) || (isset($active_id) && $active_id == $resignation->id);
                            @endphp
                            <li class="nav-item w-100" role="presentation">
                                <button class="nav-link data-item {{ $isActive ? 'active' : '' }}" data-id="{{ $resignation->id }}"
                                    type="button">
                                    <div class="row w-100">
                                        <div class="col-12">
                                            <label
                                                class="form-control-plaintext truncate-text">{{ $resignation->employee->full_name ?? 'N/A' }}</label>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-control-plaintext" style="font-size:11px">{{ $requestNo }}</div>
                                        </div>
                                        <div class="col-4 pl-2">
                                            <div class="form-control-plaintext truncate-text" style="font-size:11px">
                                                {{ $resignation->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="form-control-plaintext truncate-text" style="font-size:11px">
                                                {{ ucfirst($resignation->status) }}
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </li>
                        @endforeach
                    @else
                        <div class="p-3 text-muted text-center">No Records</div>
                    @endif
                </ul>

                <div id="long-list" class="d-none">
                    <div class="table-responsive mb-4 mt-4">
                        <table class="table table-hover" style="table-layout: fixed;width:100%">
                            <thead class="text-center" style="background-color: #f1f8f3;">
                                <tr>
                                    <th style="width: 10%;" title="Document No.">Document No.</th>
                                    <th style="width: 12%;" title="Employee Name">Employee Name</th>
                                    <th style="width: 10%;" title="Department">Department</th>
                                    <th style="width: 10%;" title="Designation">Designation</th>
                                    <th style="width: 12%;" title="Reporting Manager">Reporting Manager</th>
                                    <th style="width: 10%;" title="Separation Type">Separation Type</th>
                                    <th style="width: 10%;" title="Initiated By">Initiated By</th>
                                    <th style="width: 10%;" title="Reason Category">Reason Category</th>
                                    <th style="width: 8%;" title="Status">Status</th>
                                    <th class="text-center" style="width: 8%;" title="Action">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($resignations as $resignation)
                                    @php
                                        $requestNo = $resignation->request_no ?: 'N/A';
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

                                        // Check attachment logic
                                        $hasAttachment = false;
                                        if ($resignation->finalSettlement) {
                                            if (
                                                $resignation->finalSettlement->mohre_clearance_document ||
                                                $resignation->finalSettlement->visa_cancellation_document ||
                                                $resignation->finalSettlement->labour_cancellation_document
                                            ) {
                                                $hasAttachment = true;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center"><a href="javascript:void(0);" onclick="list_style_new()"
                                                class="data-item" data-id="{{ $resignation->id }}"
                                                title="{{ $requestNo }}">{{ $requestNo }}</a></td>
                                        <td title="{{ $employeeName }}"><a href="javascript:void(0);" onclick="list_style_new()"
                                                class="data-item" data-id="{{ $resignation->id }}"><span
                                                    class="resignation-ellipsis">{{ $employeeName }}</span></a></td>
                                        <td title="{{ $departmentName }}"><span
                                                class="resignation-ellipsis">{{ $departmentName }}</span></td>
                                        <td title="{{ $designationName }}"><span
                                                class="resignation-ellipsis">{{ $designationName }}</span></td>
                                        <td title="{{ $reportingManagerName }}"><span
                                                class="resignation-ellipsis">{{ $reportingManagerName }}</span></td>
                                        <td title="{{ $separationType }}"><span
                                                class="resignation-ellipsis">{{ $separationType }}</span></td>
                                        <td title="{{ $initiatedBy }}"><span
                                                class="resignation-ellipsis">{{ $initiatedBy }}</span></td>
                                        <td title="{{ $reasonCategory }}"><span
                                                class="resignation-ellipsis">{{ $reasonCategory }}</span></td>
                                        <td class="text-center" title="{{ $statusText }}"><span
                                                class="status-badge status-{{ $resignation->status }}">{{ $statusText }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center flex-nowrap gap-2">
                                                @if($canEditResignation)
                                                    <a class="btn btn-sm btn-light"
                                                        href="{{ route('staff.resignation.edit', $resignation->id) }}" title="Edit"
                                                        onclick="event.stopPropagation();">
                                                        <i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i>
                                                    </a>
                                                @endif
                                                @if($canDeleteResignation)
                                                    <form action="{{ route('staff.resignation.destroy', $resignation->id) }}"
                                                        method="POST" style="display:inline-block;"
                                                        onclick="event.stopPropagation();">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-light" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this End of Service record?');">
                                                            <i class="ico icon-outline-trash-bin-minimalistic text-danger"
                                                                style="font-size: 16px;"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-light disabled" title="Delete">
                                                        <i class="ico icon-outline-trash-bin-minimalistic text-muted"
                                                            style="font-size: 16px;"></i>
                                                    </button>
                                                @endif
                                                @if($hasAttachment)
                                                    <a class="btn btn-sm btn-light"
                                                        href="{{ route('staff.resignation.downloadAttachment', $resignation->id) }}"
                                                        target="_blank" download title="Download Attachment"
                                                        onclick="event.stopPropagation();">
                                                        <i class="ico icon-bold-download-minimalistic text-dark"
                                                            style="font-size: 16px;"></i>
                                                    </a>
                                                @else
                                                    <a class="btn btn-sm btn-light disabled" href="javascript:void(0);"
                                                        target="_blank" download title="Download Attachment"
                                                        onclick="event.stopPropagation();">
                                                        <i class="ico icon-bold-download-minimalistic text-dark"
                                                            style="font-size: 16px;"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </aside>

        <div class="content-container col-9">
            <div class="tab-content display-flex-tabs" id="resignationTabContent">
                <div role="tabpanel" id="resignation-details">
                    @if (isset($selectedResignation) && $selectedResignation || (isset($resignations) && $resignations->first()))
                        @php
                            $initialResignationId = isset($selectedResignation) && $selectedResignation ? $selectedResignation->id : $resignations->first()->id;
                        @endphp
                        <div class="p-4 text-muted text-center" data-initial-resignation-id="{{ $initialResignationId }}">
                            <div class="spinner-border text-success" role="status"></div><br>Loading details...
                        </div>
                    @else
                        <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                            style="min-height:60vh;">
                            <div class="text-center mb-4">
                                @if ($canAddResignation)
                                    <a href="{{ route('staff.resignation.add') }}"
                                        class="border-0 rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto text-decoration-none"
                                        style="width:80px;height:80px;font-size:36px;">
                                        <i class="ico icon-outline-add-square"></i>
                                    </a>
                                @endif
                                <h1 class="fw-bold mt-3">Resignation / End of Service Request List</h1>
                                <p class="text-muted">No request selected.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function setResignationView(mode) {
            const leftNav = document.getElementById('leftSidebar');
            const content = document.querySelector('.content-container');
            const shortList = document.getElementById('resignationShortList');
            const longTable = document.getElementById('long-list');
            const filtersShort = document.getElementById('filters-short');
            const filtersLong = document.getElementById('filters-long');

            if (!leftNav || !content || !shortList || !longTable || !filtersShort || !filtersLong) return;

            if (mode === 'full') {
                leftNav.classList.remove('col-3');
                leftNav.classList.add('col-12');
                content.classList.add('d-none');

                shortList.classList.add('d-none');
                longTable.classList.remove('d-none');
                filtersShort.classList.add('d-none');
                filtersLong.classList.remove('d-none');
                leftNav.dataset.view = 'full';
            } else {
                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                content.classList.remove('d-none');

                shortList.classList.remove('d-none');
                longTable.classList.add('d-none');
                filtersShort.classList.remove('d-none');
                filtersLong.classList.add('d-none');
                leftNav.dataset.view = 'compact';
            }
        }

        function toggleLongFilters() {
            const filterField = document.getElementById('long-filters-box');
            if (filterField) {
                filterField.classList.toggle('d-none');
            }
        }

        function list_style_new() {
            const leftNav = document.getElementById('leftSidebar');
            const cur = leftNav.dataset.view || 'compact';
            setResignationView(cur === 'compact' ? 'full' : 'compact');
        }

        $(document).ready(function () {
            // Initial state logic
            const requestedView = @json(request('view'));
            if (requestedView === 'full') {
                setResignationView('full');
            } else {
                setResignationView('compact');
            }

            // AJAX loader
            function loadResignationDetails(id) {
                var detailUrl = '{{ url("staff/resignation/details") }}/' + id;
                var $panel = $('#resignation-details');

                $.ajax({
                    url: detailUrl,
                    method: 'GET',
                    cache: false,
                    success: function (html) {
                        if (html && $.trim(html).length) {
                            $panel.html(html);
                        } else {
                            $panel.html('<p class="text-danger p-4 text-center">No Details Available.</p>');
                        }
                    },
                    error: function (xhr) {
                        $panel.html('<p class="text-danger p-4 text-center">Error loading details: ' + (xhr.responseText ? xhr.responseText : xhr.statusText) + '</p>');
                    }
                });
            }

            // Initial detail load
            var initialDiv = document.querySelector('[data-initial-resignation-id]');
            if (initialDiv) {
                var initialId = initialDiv.getAttribute('data-initial-resignation-id');
                if (initialId) {
                    loadResignationDetails(initialId);
                }
            }

            // Click handler
            $(document).on('click', '.data-item', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                if (!id) return;

                // Active UI
                $('.data-item').removeClass('active');
                $('.data-item[data-id="' + id + '"]').addClass('active');

                // URL Update
                var indexUrl = @json(route('staff.resignation.list'));
                var currentParams = @json(request()->except('active'));
                var params = new URLSearchParams(currentParams);
                params.set('active', id);
                var newUrl = indexUrl + '?' + params.toString();

                if (window.history && window.history.pushState) {
                    window.history.pushState({ path: newUrl }, '', newUrl);
                }

                // Ensure compact view
                const leftNav = document.getElementById('leftSidebar');
                if (leftNav && leftNav.dataset.view === 'full') {
                    setResignationView('compact');
                }

                loadResignationDetails(id);
            });

            // Simple search filter for short list and long list
            $('#shortSearch, #tableSearch').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                var isShort = $(this).attr('id') === 'shortSearch';

                if (isShort) {
                    $('#resignationShortList > li').filter(function () {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                    });
                } else {
                    $('#long-list tbody tr').filter(function () {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                    });
                }
            });
        });
    </script>

@endsection
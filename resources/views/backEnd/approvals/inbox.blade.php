@extends('backEnd.newmasterpage')

@section('mainContent')
    @php
        use Illuminate\Support\Str;

        $isTrack = $isTrack ?? false;
        $screenRoute = $isTrack ? route('approvals.leave-track') : route('approvals.inbox');
        $screenTitle = $isTrack ? 'Leave Track' : 'Leave Request';
        $emptyTitle = $isTrack ? 'No leave requests' : 'Add Leave';
    @endphp

    <script>
        function setLeavesView(mode) {
            var leftNav = document.getElementById('leftSidebar');
            var content = document.querySelector('.content-container');
            var shortList = document.getElementById('leaveShortList');
            var longTable = document.getElementById('long-list');
            var filtersShort = document.getElementById('filters-short');
            var filtersLong = document.getElementById('filters-long');

            if (!leftNav || !content) return;

            if (mode === 'full') {
                leftNav.classList.remove('col-3');
                leftNav.classList.add('col-12');
                leftNav.style.width = '100%';
                content.classList.add('d-none');
                if (longTable) longTable.classList.remove('d-none');
                if (shortList) shortList.classList.add('d-none');
                if (filtersLong) filtersLong.classList.remove('d-none');
                if (filtersShort) filtersShort.classList.add('d-none');
                leftNav.setAttribute('data-view', 'full');
            } else {
                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                leftNav.style.width = '';
                content.classList.remove('d-none');
                if (longTable) longTable.classList.add('d-none');
                if (shortList) shortList.classList.remove('d-none');
                if (filtersShort) filtersShort.classList.remove('d-none');
                if (filtersLong) filtersLong.classList.add('d-none');
                leftNav.setAttribute('data-view', 'compact');
            }
        }

        function list_style_new_leaves() {
            var leftNav = document.getElementById('leftSidebar');
            var cur = leftNav ? (leftNav.getAttribute('data-view') || 'compact') : 'compact';
            setLeavesView(cur === 'compact' ? 'full' : 'compact');
        }

        function search_box_show_hide_leaves() {
            var box = document.getElementById('long-filters-box');
            if (box) box.classList.toggle('d-none');
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if(request()->has('status') || request()->has('from') || request()->has('to'))
                setLeavesView('full');
                var box = document.getElementById('long-filters-box');
                if (box) box.classList.remove('d-none');
            @else
                setLeavesView('compact');
            @endif
                            });
    </script>

    <style>
        .truncate-text {
            display: inline-block;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .xsmall {
            font-size: .75rem;
        }

        #leaveShortList .nav-link {
            text-align: left;
        }

        .leave-list-toolbar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .leave-list-search {
            font-size: 13px;
            width: min(350px, 100%);
            max-width: 350px;
        }

        #long-list {
            font-size: 12px;
        }

        #long-list th,
        #long-list td {
            padding: 6px 5px;
            vertical-align: middle;
            word-break: break-word;
        }

        #long-list .leave-ellipsis {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        #long-list .leave-action-cell {
            white-space: nowrap;
        }

        #long-list .leave-action-buttons {
            gap: 3px;
        }
    </style>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>

        <div class="short-list" id="filters-short">
            <h4 class="mb-2" style=" margin-left: -6px;">{{ $screenTitle }}</h4>
            <form class="form-horizontal" method="get" action="{{ $screenRoute }}" id="leave-search">
                <div class="search-filter-container mb-4" style=" margin-left: -6px;">
                    <div class="input-group flex-nowrap">
                        <input type="text" name="q" class="form-control" placeholder="Search by ID / Reason"
                            value="{{ request('q') }}">
                    </div>
                    <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new_leaves()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="long-list d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0">{{ $isTrack ? 'Leave Track List' : 'Leave Request List' }}</h4>
                <div class="search-filter-container mb-0 leave-list-toolbar d-flex align-items-center gap-2">
                    <input type="text" id="tableSearch" class="form-control leave-list-search" placeholder="Search"
                        style="width: auto; min-width: 200px;">
                    <button type="button" class="btn btn-light" id="exportExcelLeaves" title="Export to Excel">
                        <i class="ico icon-outline-export text-success"></i> Export
                    </button>
                    <button type="button" class="btn btn-light" onclick="search_box_show_hide_leaves()"
                        title="Search / Filter">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button type="button" class="btn btn-light" onclick="list_style_new_leaves()" title="Compact list">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false" title="Menu">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if($isTrack)
                                <li><a class="dropdown-item" href="{{ route('approvals.inbox') }}"><i
                                            class="ico icon-outline-list-down text-success"></i> Leaves</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ route('approvals.leave-track') }}"><i
                                            class="ico icon-outline-list-down text-success"></i> Leave Track</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <div id="long-filters-box" class="search-filter-container mt-1 mb-4 filter-field d-none border">
                <div class="card" style="width:100%">
                    <div class="card-body">
                        <form class="form-horizontal" method="get" action="{{ $screenRoute }}" id="leave-filter">
                            <div class="row">
                                <div class="col-2 mb-2">
                                    <label class="form-label">App. No.</label>
                                    <input class="form-control" type="text" name="app_no" value="{{ request('app_no') }}"
                                        placeholder="App No.">
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Type</label>
                                    <select class="form-control" name="type">
                                        <option value="">All</option>
                                        @foreach($leaveTypes as $type)
                                            <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Category</label>
                                    <select class="form-control" name="category">
                                        <option value="">All</option>
                                        @foreach (['Paid', 'Unpaid'] as $cat)
                                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>
                                                {{ $cat }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="status">
                                        <option value="">All</option>
                                        @foreach (['New', 'Pending', 'Approved', 'Rejected', 'Returned'] as $st)
                                            <option value="{{ $st }}" {{ strtoupper(request('status')) === strtoupper($st) ? 'selected' : '' }}>{{ $st }}</option>
                                        @endforeach
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
                                        <option value="1" {{ request('attachment') == '1' ? 'selected' : '' }}>With
                                            Attachments Only</option>
                                        <option value="2" {{ request('attachment') == '2' ? 'selected' : '' }}>Without
                                            Attachments Only</option>
                                        <option value="all" {{ request('attachment') === 'all' ? 'selected' : '' }}>All
                                        </option>
                                    </select>
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Filter By</label>
                                    <select class="form-control js-example-basic-single" name="filter_by">
                                        <option value="">-Select-</option>
                                        <option value="today" {{ request('filter_by') === 'today' ? 'selected' : '' }}>Today
                                        </option>
                                        <option value="this_week" {{ request('filter_by') === 'this_week' ? 'selected' : '' }}>This Week</option>
                                        <option value="last_week" {{ request('filter_by') === 'last_week' ? 'selected' : '' }}>Last Week</option>
                                        <option value="this_month" {{ request('filter_by') === 'this_month' ? 'selected' : '' }}>This Month</option>
                                        <option value="last_month" {{ request('filter_by') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                                        <option value="this_quarter" {{ request('filter_by') === 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                                        <option value="pre_quarter" {{ request('filter_by') === 'pre_quarter' ? 'selected' : '' }}>Pre Quarter</option>
                                        <option value="this_year" {{ request('filter_by') === 'this_year' ? 'selected' : '' }}>This Year</option>
                                        <option value="last_year" {{ request('filter_by') === 'last_year' ? 'selected' : '' }}>Last Year</option>
                                    </select>
                                </div>
                                <div class="col-4 mb-2 filter-field">
                                    <label class="form-label d-block">&nbsp;</label>
                                    <div class="d-flex align-items-center justify-content-start">
                                        <button type="submit" class="btn btn-light me-2" style="width:auto;">
                                            <i class="ico icon-outline-magnifer text-success"></i> Filter
                                        </button>
                                        <a href="{{ $screenRoute }}" class="btn btn-light" style="width:auto;">
                                            <i class="ico icon-outline-refresh text-success"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">
            @php
                $items = $leaves;
                $formatName = function ($staff) {
                    if (!$staff)
                        return 'Employee';
                    $firstWord = explode(' ', $staff->first_name ?? '')[0] ?? '';
                    return trim($firstWord . ' ' . ($staff->last_name ?? '')) ?: ($staff->full_name ?: 'Employee');
                };
            @endphp
            <ul id="leaveShortList" class="nav flex-column nav-pills" role="tablist">
                @if ($items->count() > 0)
                    @foreach ($items as $lv)
                        @php
                            $employeeName = $formatName(optional($lv->staffs));
                            $leaveType = optional($lv->type)->name ?: 'Leave';
                            $leaveNo = $lv->leave_application_no ?: ('LR' . optional($lv->company)->other_code . '-' . $lv->id);
                            $leaveDate = optional($lv->leave_from)->format('d/m/Y') ?: optional($lv->apply_date)->format('d/m/Y') ?: optional($lv->created_at)->format('d/m/Y');
                            $isActive = isset($selectedLeave) && $selectedLeave && $selectedLeave->id == $lv->id;
                        @endphp
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link lv-item {{ $isActive ? 'active' : '' }}" data-id="{{ $lv->id }}" type="button"
                                role="tab">
                                <div class="row w-100">
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ $employeeName }} - {{ $leaveType }}
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext truncate-text" style="font-size:11px">{{ $leaveNo }}
                                        </div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size:11px">{{ $leaveDate ?: '-' }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size:11px">
                                            <span
                                                class="text-{{ $lv->approve_status_badge }}">{{ $lv->approve_status_label }}</span>
                                        </div>
                                    </div>
                                    <span class="d-none">{{ $lv->reason }}</span>
                                </div>
                            </button>
                        </li>
                    @endforeach
                @else
                    <div class="p-3 text-muted">No leave requests</div>
                @endif
            </ul>

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover d-none" style="table-layout:fixed;width:100%">
                    <thead class="text-center">
                        <tr>
                            <th style="width:7%;" title="Application Date">App. Date</th>
                            <th style="width:9%;" title="Leave Application No.">Leave App. No.</th>
                            <th style="width:10%;" title="Employee Name">Employee</th>
                            <th style="width:8%;" title="Leave Type">Type</th>
                            <th style="width:8%;" title="Leave Category">Category</th>
                            <th style="width:7%;" title="Leave From">From</th>
                            <th style="width:7%;" title="Leave To">To</th>
                            <th style="width:5%;" title="Number of Leave Days">Days</th>
                            <th style="width:12%;" title="Reason for Leave">Reason</th>
                            <th style="width:9%;" title="Handover To">Handover To</th>
                            <th style="width:7%;" title="Status">Status</th>
                            <th style="width:5%;" title="Attachment">Attach.</th>
                            <th style="width:6%;" title="Action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $lv)
                            <tr class="lv-item" data-id="{{ $lv->id }}" style="cursor:pointer">
                                <td
                                    title="{{ optional($lv->apply_date)->format('d/m/Y') ?: optional($lv->created_at)->format('d/m/Y') }}">
                                    {{ optional($lv->apply_date)->format('d/m/Y') ?: optional($lv->created_at)->format('d/m/Y') }}
                                </td>
                                <td
                                    title="{{ $lv->leave_application_no ?: ('LR' . optional($lv->company)->other_code . '-' . $lv->id) }}">
                                    <span
                                        class="leave-ellipsis">{{ $lv->leave_application_no ?: ('LR' . optional($lv->company)->other_code . '-' . $lv->id) }}</span>
                                </td>
                                <td title="{{ optional($lv->staffs)->full_name ?: $formatName(optional($lv->staffs)) }}"><span
                                        class="leave-ellipsis">{{ $formatName(optional($lv->staffs)) }}</span></td>
                                <td title="{{ optional($lv->type)->name ?: 'Type #' . $lv->type_id }}"><span
                                        class="leave-ellipsis">{{ optional($lv->type)->name ?: 'Type #' . $lv->type_id }}</span>
                                </td>
                                <td title="{{ $lv->leave_category ?: '-' }}"><span
                                        class="leave-ellipsis">{{ $lv->leave_category ?: '-' }}</span></td>
                                <td>{{ optional($lv->leave_from)->format('d/m/Y') }}</td>
                                <td>{{ optional($lv->leave_to)->format('d/m/Y') }}</td>
                                <td>{{ number_format((float) $lv->days, 2) }}</td>
                                <td title="{{ $lv->reason ?: '-' }}"><span
                                        class="leave-ellipsis">{{ $lv->reason ?: '-' }}</span></td>
                                <td title="{{ $lv->handover_to ?: '-' }}"><span
                                        class="leave-ellipsis">{{ $lv->handover_to ?: '-' }}</span></td>
                                <td><span
                                        class="badge bg-{{ $lv->approve_status_badge }}">{{ $lv->approve_status_label }}</span>
                                </td>
                                <td class="text-center">{{ !empty($lv->file) ? 'Yes' : '-' }}</td>
                                <td class="text-center leave-action-cell">
                                    @php
                                        $canEditLeaveRow = !$isTrack && in_array($lv->approve_status, ['D', 'P'], true);
                                        $canDeleteLeaveRow = !$isTrack && $lv->can_be_deleted;
                                        $hasLeaveAttachment = !empty($lv->file);
                                    @endphp
                                    @if($canEditLeaveRow || $canDeleteLeaveRow || $hasLeaveAttachment)
                                        <div
                                            class="d-flex justify-content-center align-items-center leave-action-buttons flex-nowrap">
                                            @if($canEditLeaveRow)
                                                <a class="btn btn-sm btn-light"
                                                    href="{{ route('approvals.inbox', ['active' => $lv->id, 'leave_action' => 'edit']) }}"
                                                    title="Edit" onclick="event.stopPropagation();">
                                                    <i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i>
                                                </a>
                                            @endif
                                            @if($canDeleteLeaveRow)
                                                <form action="{{ route('employee.leaves.destroy', $lv->id) }}" method="POST"
                                                    style="display:inline-block;" onclick="event.stopPropagation();">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light" title="Delete"
                                                        onclick="return confirm('Are you sure you want to delete this leave request?');">
                                                        <i class="ico icon-outline-trash text-danger" style="font-size: 16px;"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($hasLeaveAttachment)
                                                <a class="btn btn-sm btn-light"
                                                    href="{{ \Illuminate\Support\Facades\Storage::url($lv->file) }}" target="_blank"
                                                    download title="Download Attachment" onclick="event.stopPropagation();">
                                                    <i class="ico icon-bold-download-minimalistic text-dark"
                                                        style="font-size: 16px;"></i>
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">{{ $leaves->links() }}</div>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="leaveTabContent">
            <script>
                (function () {
                    var detailsTpl = @json(route('approvals.show', ['id' => ':id']));
                    var baseRoute = @json($screenRoute);
                    var isTrack = @json($isTrack);

                    function buildUrl(tpl, id) {
                        return tpl.replace(':id', encodeURIComponent(id));
                    }

                    $(document).on('click', '.lv-item', function (e) {
                        e.preventDefault();
                        var id = $(this).data('id');
                        if (!id) return;

                        $('.lv-item').removeClass('active');
                        $('.lv-item[data-id="' + id + '"]').addClass('active');

                        var params = new URLSearchParams(window.location.search);
                        params.delete('leave_action');
                        params.set('active', id);
                        var newUrl = baseRoute + '?' + params.toString();
                        if (window.history && window.history.pushState) {
                            window.history.pushState({ path: newUrl }, '', newUrl);
                        }

                        var action = buildUrl(detailsTpl, id);
                        if (isTrack) action += '?context=track';

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
                                console.error('leave-details error:', xhr.status);
                                $('#lv-details').html('<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function () {
                                if ($loader.length) $loader.hide();
                            }
                        });
                    });
                })();
            </script>

            <div role="tabpanel" id="lv-details">
                @if (!$isTrack && ($action ?? false) === 'add')
                    @include('backEnd.employee.leaves._application_panel', ['leave' => null])
                @elseif (!$isTrack && ($action ?? false) === 'edit' && $editLeave)
                    @include('backEnd.employee.leaves._application_panel', ['leave' => $editLeave])
                @elseif ($selectedLeave)
                    @include('backEnd.approvals._details', ['leave' => $selectedLeave, 'trackMode' => $isTrack])
                @else
                    <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">
                        @if (!$isTrack)
                            <a href="{{ route('approvals.inbox', ['leave_action' => 'add']) }}"
                                class="text-decoration-none text-dark">
                                <div class="text-center mb-4">
                                    <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                        style="width: 80px; height: 80px; font-size: 36px; cursor:pointer">
                                        <i class="ico icon-outline-add-square"></i>
                                    </div>
                                    <h1 class="fw-bold mt-3">Add Leave</h1>
                                    <p class="text-muted">Create and track your leaves with ease</p>
                                </div>
                            </a>
                        @else
                            <div class="text-center mb-4">
                                <h1 class="fw-bold mt-3">{{ $emptyTitle }}</h1>
                                <p class="text-muted">Submitted leave requests assigned to you will appear here.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        $(function () {
            var $q = $('#leave-search input[name="q"]');
            var $tableSearch = $('#tableSearch');
            var $shortItems = $('#leaveShortList > li');
            var $longRows = $('#long-list tbody > tr');

            function norm(s) {
                return (s || '').toString().toLowerCase();
            }

            function textOf($el) {
                return norm($el.text());
            }

            function applyFilter(needle) {
                if (!needle) {
                    $shortItems.show();
                    $longRows.show();
                    return;
                }
                $shortItems.each(function () {
                    $(this).toggle(textOf($(this)).indexOf(needle) !== -1);
                });
                $longRows.each(function () {
                    $(this).toggle(textOf($(this)).indexOf(needle) !== -1);
                });
            }

            var deb;
            $q.on('input', function () {
                clearTimeout(deb);
                var needle = norm(this.value);
                deb = setTimeout(function () {
                    applyFilter(needle);
                }, 120);
            });
            $tableSearch.on('input', function () {
                applyFilter(norm(this.value));
            });

            $('#exportExcelLeaves').on('click', function () {
                var rows = [];
                $('#long-list tr:visible').each(function () {
                    var cols = [];
                    $(this).find('th,td').each(function () {
                        cols.push('"' + $(this).text().replace(/\s+/g, ' ').trim().replace(/"/g, '""') + '"');
                    });
                    if (cols.length) rows.push(cols.join(','));
                });
                if (!rows.length) return;
                var blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'leave_requests.csv';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });
    </script>
@endsection
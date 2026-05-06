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
            var leftNav = document.getElementById('leftSidebar');
            var content = document.querySelector('.content-container');
            var shortList = document.getElementById('leaveShortList');
            var longTable = document.getElementById('long-list');
            var filtersShort = document.getElementById('filters-short');
            var filtersLong = document.getElementById('filters-long');

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
            var cur = leftNav.getAttribute('data-view') || 'compact';
            setLeavesView(cur === 'compact' ? 'full' : 'compact');
        }
        document.addEventListener('DOMContentLoaded', function() {
            var leftNav = document.getElementById('leftSidebar');
            if (!leftNav.getAttribute('data-view')) leftNav.setAttribute('data-view', 'compact');
        });
    </script>

    <style>
        .truncate-text {
            display: inline-block;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .xsmall {
            font-size: .75rem
        }
    </style>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>

        {{-- SHORT FILTER --}}
        <div class="short-list" id="filters-short">
            <h4 class="mb-2">Leave Request</h4>
            <form class="form-horizontal" method="get" action="{{ route('employee.leaves.index') }}" id="leave-search">
                <div class="search-filter-container mb-4 d-flex">
                    <div class="input-group flex-nowrap">
                        <input type="text" name="q" class="form-control" placeholder="Search by ID / Reason"
                            value="{{ request('q') }}">
                    </div>
                    {{-- <button type="submit" class="btn btn-light ms-2"><i class="ico icon-outline-magnifer"></i></button> --}}
                    <button type="button" class="btn btn-light ms-2" onclick="list_style_new_leaves()"><i
                            class="ico icon-outline-list-down"></i></button>
                </div>
            </form>
        </div>

        {{-- LONG FILTER --}}
        <div class="long-list d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Leave Requests</h4>
                <div class="search-filter-container mb-0">
                    <button class="btn btn-light"
                        onclick="document.getElementById('long-filters-box').classList.toggle('d-none')">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button class="btn btn-light" onclick="list_style_new_leaves()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>

            <div id="long-filters-box" class="search-filter-container mt-1 mb-4 filter-field d-none border">
                <div class="card" style="width:100%">
                    <div class="card-body">
                        <form class="form-horizontal" method="get" action="{{ route('employee.leaves.index') }}"
                            id="leave-filter">
                            <div class="row">
                                <div class="col-2 mb-2">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="status">
                                        <option value="">All</option>
                                        @foreach (['Pending', 'Approved', 'Rejected', 'Cancelled'] as $st)
                                            <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                                                {{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2 mb-2">
                                    <label class="form-label">Type</label>
                                    <input class="form-control" type="number" name="type_id"
                                        value="{{ request('type_id') }}">
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
                                   <button type="submit" class="btn btn-light mt-4">
                                    <i class="ico icon-outline-magnifer text-success"></i> Filter
                                </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- SHORT LIST --}}
        <div class="left-nav-list">
            @php $items = $leaves; @endphp
            <ul id="leaveShortList" class="nav flex-column nav-pills" role="tablist">
                @if ($items->count() > 0)
                    @foreach ($items as $lv)
                        <li class="nav-item w-100" role="presentation">
                            <button
                                class="nav-link lv-item {{ isset($selectedLeave) && $selectedLeave && $selectedLeave->id == $lv->id ? 'active' : '' }}"
                                data-id="{{ $lv->id }}" type="button" role="tab">
                                <div class="row w-100 align-items-center">
                                    <div class="col-2 d-flex justify-content-center">
                                        <div class="rounded-circle bg-light border"
                                            style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-weight:600;color:#555;">
                                            {{ strtoupper(substr($lv->type->name ?? 'L', 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="col-10 ps-0">
                                        {{-- 🔹 Line 1: Name · Designation · Status --}}
                                        @php
                                            $name = optional($lv->staffs)->first_name ?? '—';
                                            $desig =
                                                optional(optional($lv->staffs)->designations)->title ??
                                                optional(optional($lv->staffs)->designations)->name;
                                        @endphp
                                        <div class="form-control-plaintext">
                                            {{ $name }}
                                            @if ($desig)
                                                · {{ $desig }}
                                            @endif
                                            ·
                                            # {{ $lv->id }}
                                        </div>

                                        {{-- 🔹 Line 2: Date Range · ID --}}
                                        <div
                                            class="d-flex justify-content-between align-items-center text-muted xsmall mt-1">
                                            <span class="form-control-plaintext truncate-text">
                                                {{ optional($lv->leave_from)->format('d M') }} –
                                                {{ optional($lv->leave_to)->format('d M, Y') }}
                                            </span>
                                            <span class="form-control-plaintext truncate-text">
                                                <span class="text-{{ $lv->approve_status_badge }}">
                                                    {{ $lv->approve_status_label }}
                                                </span>
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
                <table id="long-list" class="table table-hover d-none" style="table-layout:fixed;width:100%">
                    <thead class="text-center">
                        <tr>
                            <th style="width:80px;">ID</th>
                            <th style="width:160px;">Type</th>
                            <th style="width:180px;">Staff</th> {{-- 👈 add this --}}
                            <th style="width:180px;">Date Range</th>
                            <th style="width:110px;">Days</th>
                            <th style="width:140px;">Status</th>
                            <th>Reason</th>
                            <th class="text-center" style="width:110px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $lv)
                            <tr>
                                <td class="text-center">
                                    <a href="javascript:void(0);" onclick="list_style_new_leaves()" class="lv-item"
                                        data-id="{{ $lv->id }}">#{{ $lv->id }}</a>
                                </td>
                                <td>{{ $lv->type->name ?? 'Type #' . $lv->type_id }}</td>

                                {{-- 🧍 Staff Name --}}
                                <td>
                                    <div class="fw-semibold truncate-text">{{ optional($lv->staffs)->full_name ?? '—' }}
                                    </div>
                                    @php
                                        $desig =
                                            optional(optional($lv->staffs)->designations)->title ??
                                            optional(optional($lv->staffs)->designations)->name;
                                        $dept =
                                            optional(optional($lv->staffs)->departments)->name ??
                                            optional(optional($lv->staffs)->departments)->title;
                                    @endphp
                                    @if ($desig || $dept)
                                        <div class="text-muted xsmall truncate-text">
                                            {{ trim(($desig ? $desig : '') . ($dept ? ' · ' . $dept : '')) }}
                                        </div>
                                    @endif
                                </td>

                                <td>{{ optional($lv->leave_from)->format('d M Y') }} –
                                    {{ optional($lv->leave_to)->format('d M Y') }}</td>
                                <td>{{ number_format((float) $lv->days, 2) }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $lv->approve_status_badge }}">{{ $lv->approve_status_label }}</span>
                                </td>
                                <td class="truncate-text">{{ $lv->reason ?? '—' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-light lv-item" data-id="{{ $lv->id }}"
                                        title="View">
                                        <i class="ico icon-outline-eye" style="font-size:16px;"></i>
                                    </button>
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

            {{-- CLICK HANDLER --}}
            <script>
                (function() {
                  var detailsTpl = @json(route('approvals.show', ['id' => ':id']));


                    function buildUrl(tpl, id) {
                        return tpl.replace(':id', encodeURIComponent(id));
                    }
                    $(document).on('click', '.lv-item', function(e) {
                        e.preventDefault();
                        var id = $(this).data('id');
                        if (!id) return;
                        $('.lv-item').removeClass('active');
                        $('.lv-item[data-id="' + id + '"]').addClass('active');
                        var newUrl =
                            "{{ route('approvals.inbox') }}?{{ http_build_query(request()->except('active')) }}&active=" +
                            encodeURIComponent(id);
                        if (window.history && window.history.pushState) {
                            window.history.pushState({
                                path: newUrl
                            }, '', newUrl);
                        }
                        var action = buildUrl(detailsTpl, id);
                        var $loader = $('#loading_bg');
                        if ($loader.length) $loader.show();
                        $.ajax({
                            url: action,
                            method: 'GET',
                            cache: false,
                            success: function(html) {
                                $('#lv-details').html(html && $.trim(html).length ? html :
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            error: function(xhr) {
                                console.error('leave-details error:', xhr.status);
                                $('#lv-details').html('<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                if ($loader.length) $loader.hide();
                            }
                        });
                    });
                })();
            </script>
            
            <div role="tabpanel" id="lv-details">
                @if ($selectedLeave)
                    @include('backEnd.approvals._details', ['leave' => $selectedLeave])
                @else
                    <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height:60vh;">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-success d-flex justify-content-center align-items-center mx-auto"
                                style="width:80px;height:80px;font-size:36px;">
                                <i class="ico icon-outline-calendar text-white"></i>
                            </div>
                            <h1 class="fw-bold mt-3">Leave Requests</h1>
                            <p class="text-muted">Select a leave from the list to view details</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    

    <script>
        $(function() {
            var $q = $('#leave-search input[name="q"]');
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
                $shortItems.each(function() {
                    $(this).toggle(textOf($(this)).indexOf(needle) !== -1);
                });
                $longRows.each(function() {
                    $(this).toggle(textOf($(this)).indexOf(needle) !== -1);
                });
            }
            var deb;
            $q.on('input', function() {
                clearTimeout(deb);
                var needle = norm(this.value);
                deb = setTimeout(function() {
                    applyFilter(needle);
                }, 120);
            });
        });
    </script>
       <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Your modal content goes here...
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div>
@endsection

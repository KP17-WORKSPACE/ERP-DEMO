@extends('backEnd.newmasterpage')
@section('mainContent')
<style>
.calendar-wrapper {
  border: 1px solid #dee2e6;
  border-radius: 8px;
  overflow: hidden;
  font-size: 13px; /* keep text size same */
}

.calendar-header {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  background: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
}

.calendar-header-day {
  padding: 6px 0;
  text-align: center;
  border-right: 1px solid #e9ecef;
}
.calendar-header-day:last-child {
  border-right: none;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  background-color: #fff;
}

.calendar-day {
  min-height: 55px;              /* 🔹 smaller height */
  border: 1px solid #f1f1f1;
  padding: 2px 4px;              /* 🔹 tighter spacing */
  position: relative;
  text-align: center;
  line-height: 1.2;
}

.calendar-day.empty {
  background-color: #fafafa;
}

.calendar-day.offday {
  background-color: #f5faff;
}

.calendar-date {
  font-weight: 600;
  margin-bottom: 2px;
}

.calendar-status {
  margin-bottom: 1px;
}

.calendar-times small {
  color: #6c757d;
  display: block;
  line-height: 1.1;
  font-size: 12px; /* same readable size */
}

</style>

    @php
        // Employee only
        $isEmployee = true;
        $self = isset($activeStaff) ? $activeStaff : Auth::user()->staff ?? null;
        $entries = isset($entries) ? $entries : collect();
        $month = isset($month) ? $month : date('Y-m');

        // columns (adjust if your DB names differ)
        $colDate = 'attendence_date';
        $colIn = 'in_time';
        $colOut = 'out_time';
        $colType = 'attendence_type';

        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    @endphp


<script>
        function setStaffView(mode) {
            const leftNav = document.getElementById('leftSidebar');
            const content = document.querySelector('.content-container');

            const shortList = document.getElementById('staffShortList'); // UL
            const longTable = document.getElementById('long-list'); // TABLE

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
            setStaffView(cur === 'compact' ? 'full' : 'compact');
        }

        // optional: ensure initial state
        document.addEventListener('DOMContentLoaded', function() {
            const leftNav = document.getElementById('leftSidebar');
            if (!leftNav.dataset.view) leftNav.dataset.view = 'compact';
        });
    </script>
    {{-- LEFT: single self item --}}
    {{-- <aside class="left-nav col-3" id="leftSidebar" data-view="compact">
        <div class="left-nav-list">
            <ul id="staffShortList" class="nav flex-column nav-pills" role="tablist">
                <li class="nav-item w-100" role="presentation">
                    <button class="nav-link att-item active" data-staff="{{ optional($self)->id }}" type="button"
                        role="tab">
                        <div class="row w-100 align-items-center">
                            <div class="col-2 d-flex justify-content-center">
                                <div class="rounded-circle bg-light border"
                                    style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;font-weight:600;color:#555;">
                                    {{ strtoupper(substr(optional($self)->first_name ?? 'U', 0, 1)) }}
                                </div>
                            </div>
                            <div class="col-10 ps-0">
                                <div class="row">
                                    <div class="col-7">
                                        <span class="form-control-plaintext fw-semibold truncate-text"
                                            title="{{ optional($self)->full_name }}">
                                            {{ optional($self)->first_name ?? '—' }}
                                        </span>
                                    </div>
                                    <div class="col-5 text-end">
                                        <span class="form-control-plaintext text-muted">#{{ optional($self)->id }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-muted xsmall mt-1">
                                    <span class="form-control-plaintext truncate-text">
                                        {{ optional(optional($self)->department)->title ?? '—' }}
                                    </span>
                                    <span class="form-control-plaintext truncate-text">
                                        {{ optional(optional($self)->designation)->title ?? '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </button>
                </li>
            </ul>
        </div>
    </aside> --}}

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>

        {{-- SHORT (Compact) --}}
        <div class="short-list" id="filters-short">
            <h4 class="mb-2">Staff</h4>

            {{ Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => route('staff_directory'), // ⬅️ use named route
                'method' => 'get',
                'id' => 'staff-search',
            ]) }}
            <div class="search-filter-container mb-4 d-flex">
                <div class="input-group flex-nowrap">
                    <input type="text" name="staff_no" id="staffSearch" class="form-control" placeholder="Search by User ID / Name"
                        aria-label="Search" aria-describedby="addon-wrapping" value="{{ request('staff_no') ?? '' }}">
                </div>

                <button type="button" class="btn btn-light ms-2" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>
            {{ Form::close() }}
        </div>

        {{-- LONG (Full) --}}
        <div class="long-list sticky-top d-none" id="filters-long" style="background-color: white">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Staff List</h4>
                <div class="search-filter-container mb-0">

 <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>

                    <input type="text" id="tableSearch" class="form-control"
                        style="font-size:13px; width: 350px; 
                        top: 12px;
                        right: 120px;"
                        placeholder="Search">


                          <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>


                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>



                        <ul class="dropdown-menu" style="">

                            <li><a href="{{ url('onboarding-employee-list') }}"
                                    class="dropdown-item d-flex align-items-center text-dark"><i
                                        class="ico icon-outline-document-text text-success  title-15 me-2"></i> Onboard
                                    Employee List </a>
                            </li>

                            <li><a data-copy-url="{{ url('onboard-employee/' . session('logged_session_data.company_id')) }}"
                                    title="Click to copy link"
                                    class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"><i
                                        class="ico icon-outline-user-plus text-success  title-15 me-2"></i> Onboard Employee
                                    Link</a>
                            </li>


                           



                        </ul>
                    </div>

                  
                   
                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'staff', 'method' => 'get', 'id' => 'staff-filter']) }}
                        <div class="row">
                            <div class="col-md-3 mb-2 filter-field d-none">
                                <label class="form-label">Role</label>
                                <select class="form-control" name="role_id" id="role_id">
                                    <option value=""></option>
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->id }}"
                                            @if (request('role_id') == $r->id) selected @endif>{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label class="form-label">User ID</label>
                                <input class="form-control" type="text" name="staff_no"
                                    value="{{ request('staff_no') }}">
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label class="form-label">Name</label>
                                <input class="form-control" type="text" name="staff_name"
                                    value="{{ request('staff_name') }}">
                            </div>

                            <div class="col-md-3 filter-field d-none">
                                <button type="submit" class="btn btn-success mt-4 rounded-0" id="btnSubmit">Filter</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
<script>
function openData(id) {
    $("#loading_bg").css("display", "block");
    var url = '{{ route("attendance.index") }}';
    window.location.href = url + "/" + id;
}
</script>
<script>
document.getElementById('staffSearch').addEventListener('keyup', function () {
    let value = this.value.toLowerCase();
    let items = document.querySelectorAll('.staff-item');

    items.forEach(function (item) {
        let text = item.getAttribute('data-search');
        if (text.includes(value)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
        {{-- LEFT NAV LIST (Short) --}}
        <div class="left-nav-list">
            <ul id="staffShortList" class="nav flex-column nav-pills" role="tablist">
                @if ($staffs->count())
                    @foreach ($staffs as $s)
                            <li class="nav-item w-100 staff-item"
    data-search="{{ strtolower($s->staff_no . ' ' . $s->first_name . ' ' . $s->last_name) }}">
                            <button
                                class="nav-link stf-item {{ isset($active_id) && $active_id == $s->id ? 'active' : '' }}" onclick="openData({{ $s->user_id }})"
                                data-id="{{ $s->id }}" type="button" role="tab">
                                <div class="row w-100 align-items-center">

                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-4"><span
                                                    class="form-control-plaintext fw-semibold">{{ $s->staff_no ?? '—' }}</span>
                                            </div>
                                            <div class="col-4"><span
                                                    class="form-control-plaintext truncate-text">{{ optional($s->roles)->name ?? '—' }}</span>
                                            </div>
                                            <div class="col-4"><span
                                                    class="form-control-plaintext truncate-text text-end">
                                                    @if ($s->ext_no)
                                                        Ext: {{ $s->ext_no }}
                                                    @endif
                                                </span></div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center text-muted xsmall mt-1">
                                            <span class="form-control-plaintext truncate-text">{{ $s->first_name }}
                                                {{ $s->last_name }}</span>
                                            @if (!empty($s->ext_no))
                                                <span
                                                    class="form-control-plaintext truncate-text">{{ $s->email ?? '—' }}</span>
                                            @endif
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
            <div class="table-responsive mb-4 mt-2">
                <table id="long-list" class="table table-hover data-table d-none" style="table-layout: fixed;width:100%">
                    <thead class="text-center">
                        <tr>
                            <th style="width: 80px;">User ID</th>
                            <th style="width: 160px;">Name</th>
                            <th style="width: 120px;">Role</th>
                            <th style="width: 180px;">Company Access</th>
                            <th style="width: 160px;">Company</th>
                            <th style="width: 120px;">Department</th>
                            <th style="width: 120px;">Designation</th>
                            <th style="width: 100px;">Mobile</th>
                            <th style="width: 160px;">Email</th>
                            <th class="text-center" style="width:70px">Status</th>
                            <th class="text-center" style="width: 110px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($staffs as $s)
                            <tr @if (@$s->delete_status == 0) style="background-color: rgba(0,0,0,0.05);" @endif>
                                <td class="text-center">
                                    <a href="javascript:void(0);"  class="stf-item" onclick="openData({{ $s->user_id }})"
                                        data-id="{{ $s->id }}">{{ $s->staff_no ?? '—' }}</a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);"  class="stf-item" onclick="openData({{ $s->user_id }})"
                                        data-id="{{ $s->id }}">{{ $s->first_name }} {{ $s->last_name }}</a>
                                </td>
                                <td>{{ optional($s->roles)->name ?? '—' }}</td>

                                @php
                                    $idArr = explode(',', (string) $s->company_access);
                                    $co = $company
                                        ->whereIn('id', $idArr)
                                        ->sortBy(function ($c) use ($idArr) {
                                            return array_search($c->id, $idArr);
                                        })
                                        ->pluck('company_name');
                                @endphp
                                <td>
                                    @foreach ($co as $cname)
                                        <span style="font-size:11px;padding:0.25em 0.4em;background-color:#cfe2ff"
                                            class="text-xs pr-1 pl-1">{{ $cname }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if (optional($s->maincompany)->company_name)
                                        <span style="font-size:11px;padding:0.25em 0.4em;background-color:#d4edda"
                                            class="text-xs pr-1 pl-1">
                                            {{ $s->maincompany->company_name }}
                                        </span>
                                    @endif
                                </td>
                                <td>{{ optional($s->departments)->name ?? '—' }}</td>
                                <td>{{ optional($s->designations)->title ?? '—' }}</td>
                                <td>{{ $s->mobile ?? '—' }}</td>
                                <td class="truncate-text">{{ $s->email ?? '—' }}</td>
                                <td class="text-center">
                                    @if (($s->active_status ?? 0) == 1)
                                        <i class="ico icon-outline-check-read text-success"></i>
                                    @else
                                        <i class="ico icon-outline-close text-danger"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-start align-items-center gap-1">
                                        <a href="{{ url('hrms/staff/' . $s->id . '/edit') }}"
                                            class="btn btn-sm btn-light" title="Edit">
                                            <i class="ico icon-outline-pen-2" style="font-size:16px;"></i>
                                        </a>
                                        {{-- <a href="{{ route('editStaff', $s->id) }}" class="btn btn-sm btn-light" title="Edit">
                                        <i class="ico icon-outline-pen-2" style="font-size:16px;"></i>
                                    </a> --}}
                                        @if ($s->role_id != 1)
                                            <a href="{{ route('deleteStaffView', $s->user_id) }}"
                                                class="btn btn-sm btn-light" onclick="return confirm('Are you sure?')"
                                                title="Delete">
                                                <i class="ico icon-bold-trash-bin-2" style="font-size:16px;"></i>
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
    </aside>

    {{-- RIGHT: content --}}
    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="attTabContent">

            {{-- Header + Month filter --}}
            <div class="card mb-3">
                <div class="card-body">




                            <div class="dropdown" style="float: right;">
                                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="true">
                                    <i class="ico icon-outline-hamburger-menu"></i>
                                </button>
                                <ul class="dropdown-menu" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(-124px, 30px);" data-popper-placement="bottom-end">
                                    <li><a href="{{ url('hrms/todays-attendance') }}" title="Click to copy link" class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"><i class="ico icon-outline-document-text text-success  title-15 me-2"></i>Todays Attendance</a>
                                    </li>
                                </ul>
                            </div>

                    {{-- EMPLOYEE INFO ROW --}}
                    <div class="row g-3 align-items-end mb-2">
                        <div class="col-md-1 col-sm-4">
                            <label class="fw-bold text-muted small d-block mb-1">Employee ID</label>
                            <div class="fw-semibold">{{ optional($activeStaff)->staff_no ?? '—' }}</div>
                        </div>
                        <div class="col-md-1 col-sm-4">
                            <label class="fw-bold text-muted small d-block mb-1">Finger Print ID</label>
                            <div class="fw-semibold">{{ optional($activeStaff)->finger_print_id ?? '—' }}</div>
                        </div>

                        <div class="col-md-2 col-sm-4">
                            <label class="fw-bold text-muted small d-block mb-1">Employee Name</label>
                            <div class="fw-semibold">{{ optional($activeStaff)->employee_salutation ?? '—' }} {{ optional($activeStaff)->full_name ?? '—' }}</div>
                        </div>

                        <div class="col-md-2 col-sm-4">
                            <label class="fw-bold text-muted small d-block mb-1">Designation</label>
                            <div class="fw-semibold">{{ optional(optional($activeStaff)->designations)->title ?? '—' }}
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-4">
                            <label class="fw-bold text-muted small d-block mb-1">Department</label>
                            <div class="fw-semibold">{{ optional(optional($activeStaff)->departments)->name ?? '—' }}</div>
                        </div>

                        <div class="col-md-2 col-sm-6">
                            <label class="fw-bold text-muted small d-block mb-1">Reporting Manager</label>
                            <div class="fw-semibold">
                                {{ optional(optional($activeStaff)->reportingManagernew)->first_name ?? '—' }}</div>
                        </div>
                    </div>
                    {{-- FILTERS --}}
                      {{-- <form method="GET" action="{{ route('attendance.index/$activeStaff->user_id') }}"> --}}
                        <form method="GET" action="{{ route('attendance.index', $activeStaff->user_id) }}">
                        <div class="row g-2 align-items-end">

                            <div class="col-md-3 col-sm-4">
                                <label class="fw-bold text-muted small d-block mb-1">From Date</label>
                                <input type="text" class="form-control form-control-sm date-picker" name="from_date"
                                    value="{{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('m/d/Y') : '' }}">
                            </div>

                            <div class="col-md-3 col-sm-4">
                                <label class="fw-bold text-muted small d-block mb-1">To Date</label>
                                <input type="text" class="form-control form-control-sm date-picker" name="to_date"
                                    value="{{ $toDate ? \Carbon\Carbon::parse($toDate)->format('m/d/Y') : '' }}">
                            </div>

                            <div class="col-md-3 col-sm-4">
                                <label class="fw-bold text-muted small d-block mb-1">Month</label>
                                <input type="month" class="form-control form-control-sm" name="month"
                                    value="{{ $month }}">
                            </div>

                            <div class="col-md-3 col-sm-6 d-flex gap-2">
                                <button type="submit" class="btn btn-light text-dark d-inline-flex align-items-center gap-2">Go</button>
                            </div>

                        </div>
                    </form>
                    {{-- FILTERS --}}

                    
                  

                </div>
            </div>



            {{-- My Attendance table --}}
            <div class="card">
                <div class="tab-wrap">
                                <button type="button" id="toggleBtn" class="btn-sm btn-light" style="float: right;">Detailed</button>
                <ul class="nav nav-tabs" id="hrTabs" role="tablist">
                <li class="nav-item" role="presentation">
                <button class="nav-link active" id="job-tab" data-bs-toggle="tab"
                data-bs-target="#job-details" type="button" role="tab"
                aria-controls="job-details" aria-selected="true">
                Daily Attendance
                </button>
                </li>
                <li class="nav-item" role="presentation">
                <button class="nav-link" id="bank-tab" data-bs-toggle="tab"
                data-bs-target="#bank-details" type="button" role="tab"
                aria-controls="bank-details" aria-selected="false">
                Summary Attendance
                </button>
                </li>
                </ul>
                </div>

                
<style>
.hidden{
    display:none;
}
.table-scroll{
    height: calc(100vh - 220px);
    overflow-y:auto;
}

.table-scroll thead th{
    position:sticky;
    top:0;
    z-index:10;
    background:#f8f9fa; /* same as bg-light */
}
</style>
<script>
document.getElementById("toggleBtn").onclick = function(){

    let boxes = document.querySelectorAll(".detailed");

    boxes.forEach(function(box){
        box.classList.toggle("hidden");
    });

    if(boxes[0].classList.contains("hidden")){
        this.textContent = "Detailed";
    }else{
        this.textContent = "Short";
    }
}
</script>
<div class="tab-content border bg-white" id="hrTabsContent">
    {{-- ====== Daily Attendance TAB ====== --}}
    <div class="tab-pane fade show active" id="job-details" role="tabpanel" aria-labelledby="job-tab">
        <div class="card-body p-0">
            <div class="table-responsive table-scroll">
                <table class="table table-hover mb-0 align-middle" style="table-layout:auto; white-space:nowrap;">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:60px;">S/L</th>
                            <th style="width:130px;">Date</th>
                            <th>Day</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Working Time</th>
                            <th class="detailed hidden">Late</th>
                            <th>Late Time</th>
                            <th class="detailed hidden">Early Out</th>
                            <th>Early Out Time</th>
                            <th>Over Time</th>
                            <th>Rectified Hours</th>
                            <th>Status</th>
                            <th class="detailed hidden">SC% (Shift Compliance %)</th>
                            <th class="detailed hidden">Remarks</th>
                            <th class="detailed hidden">Approval Status</th>
                            <th class="detailed hidden">Approved By</th>
                            <th class="detailed hidden">Supporting Document</th>
                            <th class="detailed hidden">Action</th>
                        </tr>
                    </thead>
                    <tbody>
@forelse($entries as $i => $r)
    @php
        //$isOffDay = !empty($r->is_offday) || (isset($r->day_name) && strtolower($r->day_name) == 'sunday');
        
        $dateDisplay = !empty($r->attendence_date)
            ? \Carbon\Carbon::parse($r->attendence_date)->format('d/m/Y')
            : '—';
        $dayDisplay = !empty($r->attendence_date)
        ? \Carbon\Carbon::parse($r->attendence_date)->format('D')
        : '—';
        

    $isWeeklyOff = @App\SysHelper::get_week_offs($dateDisplay,$weekly_offs);

    @endphp



    {{-- WEEKLY OFF ROW --}}
    @if($isWeeklyOff)
        <tr class="table-secondary text-center fw-semibold">
            <td class="text-center">{{ $i + 1 }}</td>
            <td class="text-start">{{ $dateDisplay }}</td>
            <td colspan="2">— — — Weekly Off — — —</td>
            <td colspan="16" class="text-muted">— — — Weekly Off — — —</td>
        </tr>

    {{-- REGULAR ATTENDANCE ROW --}}
    @else
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $dateDisplay }}</td>
            <td>{{ $dayDisplay }}</td>

            {{-- In / Out --}}
            <td>{{ $r->in_time ?? '—' }}</td>
            <td>{{ $r->out_time ?? '—' }}</td>
            {{-- Working Time --}}
            <td>
                @if (!empty($r->in_time) && !empty($r->out_time))
                    @php
                        try {
                            $in  = \Carbon\Carbon::parse($r->in_time);
                            $out = \Carbon\Carbon::parse($r->out_time);
                            $diff = $in->diff($out);
                            echo sprintf('%02dh %02dm', $diff->h + $diff->days * 24, $diff->i);
                        } catch (\Exception $e) {
                            echo '—';
                        }
                    @endphp
                @else
                    —
                @endif
            </td>

            {{-- Late / Early Out --}}
            <td class="detailed hidden">{{ !empty($r->is_late) ? 'Yes' : 'No' }}</td>
            <td>{{ $r->late_time ?? '—' }}</td>
            <td class="detailed hidden">{{ !empty($r->is_early_out) ? 'Yes' : 'No' }}</td>
            <td>{{ $r->early_out_time ?? '—' }}</td>


            {{-- Over Time / Rectified --}}
            <td>{{ isset($r->over_time) ? $r->over_time : '—' }}</td>
            <td>{{ isset($r->rectified_hours) ? $r->rectified_hours : '—' }}</td>

            {{-- Status --}}
            <td>
                @php $status = strtolower($r->attendence_type ?? ''); @endphp
                @if ($status === 'p')
                    <span class="badge bg-success">Present</span>
                @elseif($status === 'a')
                    <span class="badge bg-danger">Absent</span>
                @elseif($status === 'l')
                    <span class="badge bg-warning text-dark">Leave</span>
                @else
                    <span class="badge bg-secondary">—</span>
                @endif
            </td>

            {{-- SC%, Remarks, Approval, etc. --}}
            <td class="detailed hidden">98%</td>
            <td class="detailed hidden">—</td>
            <td class="detailed hidden"><span class="badge bg-warning text-dark">Pending</span></td>
            <td class="detailed hidden">
                @if (isset($r->approvedBy) && is_object($r->approvedBy))
                    {{ $r->approvedBy->name ?? '—' }}
                @else
                    —
                @endif
            </td>
            <td class="detailed hidden">—</td>

            {{-- Action --}}
            <td class="detailed hidden">
                <a href="#" class="btn btn-sm btn-light text-dark" title="Edit">
                    <i class="ico icon-outline-pen-2 text-success"></i>
                </a>
            </td>
        </tr>
    @endif
@empty
    <tr>
        <td colspan="19" class="text-center text-muted p-4">No records found.</td>
    </tr>
@endforelse
</tbody>

                </table>
            </div>
        </div>
    </div>


    {{-- ====== Summary Attendance TAB ====== --}}
    <div class="tab-pane fade" id="bank-details" role="tabpanel" aria-labelledby="bank-tab">
        <div class="card-body">
               @if(isset($summary))
    <div class="summary-box">
      <h6 class="fw-bold mb-2">Monthly Summary {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}</h6>
      <div class="row small">
        <div class="col-md-3">Total Working Days: <strong>{{ $summary['total_working_days'] }}</strong></div>
        <div class="col-md-3">Total Week Off: <strong>{{ $summary['total_week_off'] }}</strong></div>
        <div class="col-md-3">Total Present: <strong>{{ $summary['total_present'] }}</strong></div>
        <div class="col-md-3">Total Absence: <strong>{{ $summary['total_absent'] }}</strong></div>

        <div class="col-md-3">Total Leave: <strong>{{ $summary['total_leave'] }}</strong></div>
        <div class="col-md-3">Total Late: <strong>{{ $summary['total_late'] }}</strong></div>
        <div class="col-md-3">Expected Working Hour: <strong>{{ $summary['expected_hours'] }}</strong></div>
        <div class="col-md-3">Actual Working Hour: <strong>{{ $summary['actual_hours'] }}</strong></div>

        <div class="col-md-3">Rectified Hours: <strong>{{ $summary['rectified_hours'] }}</strong></div>
        <div class="col-md-3">Over Time: <strong>{{ $summary['overtime_hours'] }}</strong></div>
        <div class="col-md-3">Deficiency: <strong>{{ $summary['deficiency_hours'] }}</strong></div>
      </div>
    </div>
    @endif
    <br>

 <div class="calendar-wrapper">
  {{-- ===== WEEKDAY HEADER ===== --}}
  <div class="calendar-header bg-light fw-bold text-center">
    @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
      <div class="calendar-header-day">{{ $day }}</div>
    @endforeach
  </div>

  {{-- ===== GRID BODY ===== --}}
<div class="calendar-grid">
  @php
    use Carbon\Carbon;

    $month = isset($month) && $month ? $month : Carbon::now()->format('Y-m');
    $firstDay = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $lastDay  = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
    $entries  = $entries ?? collect();
    $entriesByDate = $entries->keyBy(function ($r) {
    return Carbon::parse($r->attendence_date)->toDateString();
});

    $emptyCells = ($firstDay->dayOfWeekIso - 1); // Monday = 1
  @endphp

  {{-- Empty cells before the first day --}}
  @for($i = 0; $i < $emptyCells; $i++)
    <div class="calendar-day empty"></div>
  @endfor

  {{-- Actual days --}}
  @for($day = 1; $day <= $lastDay->day; $day++)
    @php
      $date   = Carbon::createFromFormat('Y-m-d', $month.'-'.str_pad($day, 2, '0', STR_PAD_LEFT));
      $record = $entriesByDate->get($date->toDateString());
      $status = $record->status_label ?? '—';
      $isOff  = (!empty($record) && !empty($record->is_offday)); // ✅ always defined here
    @endphp

    <div class="calendar-day {{ $isOff ? 'offday' : '' }}"
         data-id="{{ $record->id ?? '' }}"
         data-date="{{ $date->toDateString() }}"
         data-in="{{ $record->in_time ?? '' }}"
         data-out="{{ $record->out_time ?? '' }}"
         data-remarks="{{ $record->remarks ?? '' }}"
         onclick="openRectifyModal(this)">
      <div class="calendar-date">{{ $day }}</div>
      <div class="calendar-status">
        @if($status === 'Present')
          <span class="badge bg-success">P</span>
        @elseif($status === 'Absent')
          <span class="badge bg-danger">A</span>
        @elseif($status === 'On Leave')
          <span class="badge bg-warning text-dark">L</span>
        @elseif($status === 'Week Off')
          <span class="badge bg-info text-dark">W/O</span>
        @else
          <span class="badge bg-secondary">{{ $status }}</span>
        @endif
      </div>

      @if(!empty($record))
        <div class="calendar-times">
          <small>In: {{ $record->in_time ?? '—' }}</small><br>
          <small>Out: {{ $record->out_time ?? '—' }}</small>
        </div>
      @endif
    </div>
  @endfor
</div>


</div>

        </div>
    </div>
</div>

            </div>

        </div>
        
    </div>


<script>
function openRectifyModal(el) {
  const id = el.getAttribute('data-id') || '';
  const date = el.getAttribute('data-date') || '';
  const inTime = el.getAttribute('data-in') || '';
  const outTime = el.getAttribute('data-out') || '';
  const remarks = el.getAttribute('data-remarks') || '';

  document.getElementById('rectify_id').value = id;
  document.getElementById('rectify_date').value = date;
  document.getElementById('rectify_in').value = inTime;
  document.getElementById('rectify_out').value = outTime;
  document.getElementById('rectify_remarks').value = remarks;

  $('#rectifyModal').modal('show'); // Bootstrap modal open
}
</script>

@endsection
    <div class="modal fade" id="rectifyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
    <form id="rectifyForm" method="POST" action="{{ route('attendance.rectify') }}">
  {{ csrf_field() }}

  <input type="hidden" name="attendance_id" id="rectify_id">
  <input type="hidden" name="attendence_date" id="rectify_date">
  <input type="hidden" name="staff_id" id="rectify_staff_id" value="{{ auth()->id() }}">
  <input type="hidden" name="attendence_type" value="P"> {{-- Default Present --}}

  <div class="modal-body">
    <div class="mb-2">
      <label class="form-label small mb-1">In Time</label>
      <input type="time" class="form-control form-control-sm" name="in_time" id="rectify_in">
    </div>
    <div class="mb-2">
      <label class="form-label small mb-1">Out Time</label>
      <input type="time" class="form-control form-control-sm" name="out_time" id="rectify_out">
    </div>
    <div class="mb-2">
      <label class="form-label small mb-1">Notes / Remarks</label>
      <textarea class="form-control form-control-sm" rows="2" name="notes" id="rectify_remarks"></textarea>
    </div>
  </div>
  <div class="modal-footer py-2">
    <button type="submit" class="btn btn-primary btn-sm">Save</button>
    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
  </div>
</form>

    </div>
  </div>
</div>
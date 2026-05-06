@extends('backEnd.newmasterpage')
@section('mainContent')

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


    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

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
                    <input type="text" name="staff_no" class="form-control" placeholder="Search by User ID / Name"
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

        {{-- LEFT NAV LIST (Short) --}}
        <div class="left-nav-list">
            <ul id="staffShortList" class="nav flex-column nav-pills" role="tablist">
                @if ($staffs->count())
                    @foreach ($staffs as $s)
                        <li class="nav-item w-100" role="presentation">
                            <button
                                class="nav-link stf-item {{ isset($active_id) && $active_id == $s->id ? 'active' : '' }}"
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
                                            <span class="form-control-plaintext truncate-text">{{ $s->first_name_full }}
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
                                    <a href="javascript:void(0);" onclick="list_style_new()" class="stf-item"
                                        data-id="{{ $s->id }}">{{ $s->staff_no ?? '—' }}</a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" onclick="list_style_new()" class="stf-item"
                                        data-id="{{ $s->id }}">{{ $s->first_name_full }} {{ $s->last_name }}</a>
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

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="staffTabContent">

            {{-- Click handler: shortlist & longlist dono ke liye --}}
            <script>
                (function() {
                    // Build URLs safely from Blade
                    var detailsTpl = @json(route('staff.details', ['id' => ':id']));
                    var dirTpl = @json(url('staff-directory') . '/:id');

                    function buildUrl(tpl, id) {
                        return tpl.replace(':id', encodeURIComponent(id));
                    }

                    // Event delegation (works for future DOM)
                    $(document).on('click', '.stf-item', function(e) {
                        e.preventDefault();

                        var id = $(this).data('id');
                        if (!id) return;

                        // Active UI
                        $('.stf-item').removeClass('active');
                        $('.stf-item[data-id="' + id + '"]').addClass('active');

                        // Update URL without reload
                        var newUrl = buildUrl(dirTpl, id);
                        if (window.history && window.history.pushState) {
                            window.history.pushState({
                                path: newUrl
                            }, '', newUrl);
                        }

                        // AJAX load
                        var action = buildUrl(detailsTpl, id);
                        var $loader = $('#loading_bg');
                        if ($loader.length) $loader.show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            cache: false,
                            success: function(html) {
                                // If got login page / empty response, show fallback
                                if (!html || !$.trim(html).length || /<form[^>]*login/i.test(html)) {
                                    $('#stf-details').html(
                                        '<p class="text-danger">No Details Available.</p>');
                                    return;
                                }
                                $('#stf-details').html(html);
                            },
                            error: function(xhr) {
                                console.error('staff-details error:', xhr.status, xhr.responseText);
                                $('#stf-details').html('<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                if ($loader.length) $loader.hide();
                            }
                        });
                    });
                })();
            </script>


            <div role="tabpanel" aria-labelledby="stf-tab" id="stf-details">




                @if ($firstStaff)
                    @include('backEnd.humanResource.staff_details', ['employee' => $firstStaff])
                @else
                     @include('backEnd.humanResource.staff_details-empty')
                    <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading (clickable to add new staff) -->
                        <a href="{{ url('add-staff') }}" class="text-center mb-4 d-block text-decoration-none text-dark" title="Add staff" role="button" aria-label="Add staff">
                            <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer">Staff</h1>
                            <p class="text-muted">
                                Enter staff details to create a new employee record.
                            </p>
                        </a>

                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- (Optional) Select2 init if needed elsewhere --}}
    <script>
        $(function() {
            // short search input (left compact box)
            var $q = $('#staff-search input[name="staff_no"]');

            // cache current DOM nodes
            var $shortItems = $('#staffShortList > li'); // each staff li item
            var $longRows = $('#long-list tbody > tr'); // each table row

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

                // shortlist filter
                $shortItems.each(function() {
                    var $li = $(this);
                    var hit = textOf($li).indexOf(needle) !== -1;
                    $li.toggle(hit);
                });

                // long table filter
                $longRows.each(function() {
                    var $tr = $(this);
                    var hit = textOf($tr).indexOf(needle) !== -1;
                    $tr.toggle(hit);
                });
            }

            // debounce for smooth typing
            var deb;
            $q.on('input', function() {
                clearTimeout(deb);
                var needle = norm(this.value);
                deb = setTimeout(function() {
                    applyFilter(needle);
                }, 120);
            });

            // quick clear on ESC
            $q.on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $(this).val('');
                    applyFilter('');
                }
            });

            // If you later re-render rows/items dynamically, re-cache:
            // $shortItems = $('#staffShortList > li');
            // $longRows   = $('#long-list tbody > tr');
        });
    </script>


<script>
   // Copy onboard link to clipboard when icon/heading clicked
            $(document).on('click', '.copy-onboard-url', function(e) {
                var url = $(this).data('copy-url');
                if (!url) return;

                function showSuccess() {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Link copied to clipboard');
                    } else {
                        alert('Link copied to clipboard: ' + url);
                    }
                }

                function fallbackCopy(text) {
                    var $temp = $('<textarea>');
                    $('body').append($temp);
                    $temp.val(text).select();
                    try {
                        document.execCommand('copy');
                        showSuccess();
                    } catch (err) {
                        alert('Could not copy text');
                    } finally {
                        $temp.remove();
                    }
                }

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(function() {
                        showSuccess();
                    }).catch(function() {
                        fallbackCopy(url);
                    });
                } else {
                    fallbackCopy(url);
                }
            });
</script>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php } ?>

@endsection

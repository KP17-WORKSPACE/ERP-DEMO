@extends('backEnd.newmasterpage')
@section('mainContent')
    <?php try { ?>



    <script>
        let isFullList = false;

        function list_style_new() {
            const leftNav = document.querySelector('.left-nav');
            const content = document.querySelector('.content-container');



            if (!isFullList) {
                // Switch to FULL LIST VIEW
                isFullList = true;

                leftNav.classList.remove('col-3');
                leftNav.classList.add('col-12');
                leftNav.style.width = '100%';

                content.classList.add('d-none');

                $('#long-list').removeClass('d-none');
                $('#short-list').addClass('d-none');


                $('#filters-long').removeClass('d-none');
                $('#filters-short').addClass('d-none');

                sessionStorage.setItem('listViewLeadList', 'long');
            } else {
                // Switch to COMPACT VIEW
                isFullList = false;


                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                leftNav.style.width = '';



                content.classList.remove('d-none');

                $('#long-list').addClass('d-none');


                $('#short-list').removeClass('d-none');

                $('#filters-short').removeClass('d-none');
                $('#filters-long').addClass('d-none');

                sessionStorage.setItem('listViewLeadList', 'short');

            }


        }


        //added ny kp
        function toggleLongFilters() {

            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }




        // Initialize view from sessionStorage (tab-specific)
        document.addEventListener('DOMContentLoaded', () => {
            // Check if we have customer_action parameter (add/edit mode)
            const urlParams = new URLSearchParams(window.location.search);
            const hasCustomerAction = urlParams.has('lead_action');

            // If in add/edit mode, force short view
            if (hasCustomerAction) {
                sessionStorage.setItem('listViewLeadList', 'short');
                isFullList = true; // Set to true so toggle switches to short
                list_style_new(); // Switch to short view
            } else {
                // Normal behavior - use saved view from sessionStorage
                const savedView = sessionStorage.getItem('listViewLeadList');
                if (savedView === 'long') {
                    isFullList = false; // so that toggling once activates full view
                    list_style_new();
                } else {
                    // Default to short view
                    isFullList = true; // so that toggling once activates short view
                    list_style_new();
                }
            }

            // Attach event to sidebar links to force short view on navigation
            document.querySelectorAll('.sub-nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    sessionStorage.setItem('listViewLeadList', 'short');
                });
            });



        });


        function toggleStats() {

            document.querySelectorAll('#task-cards').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>


    <style>
        /* Smooth collapse transition */
        .collapse {
            transition: height 0.35s ease, opacity 0.35s ease;
        }

        .collapsing {
            opacity: 0.8;
            transition: height 0.35s ease, opacity 0.35s ease;
        }


        .pagination .page-item.active .page-link {
            background-color: #198754 !important;
            /* Bootstrap success green */

            color: #fff !important;
        }


        .col-5-custom {
            flex: 0 0 auto;
            width: 20%;

        }
    </style>
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>


    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <div class="short-list" id="filters-short">
            <h4 class="mb-2" style=" margin-left: -6px;">Onboarding Employee List

            </h4>


            <div class="search-filter-container mb-4" style=" margin-left: -6px;">
                <div class="input-group flex-nowrap">
                    <input type="text" name="lead_id" id="search_lead" class="form-control" placeholder="Document No"
                        aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>




                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list sticky-top  d-none" id="filters-long" style="background-color: #f7f8fd">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Onboarding Employee List
                </h4>


                <div class="search-filter-container mb-0 d-flex align-items-center justify-content-center">


                    <input type="text" id="tableSearch" class="form-control"
                        style="font-size:13px; width: 350px; 
                        top: 12px;
                        right: 120px;"
                        placeholder="Search">


                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">

                        

                 <li><a data-copy-url="{{ url('onboard-employee/' . session('logged_session_data.company_id')) }}"
                        title="Click to copy link" class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"><i
                            class="ico icon-outline-user-plus text-success  title-15 me-2"></i> Onboard Employee Link</a>
                </li>

                            <li><a href="{{ url('staff-directory') }}"
                                    class="dropdown-item d-flex align-items-center text-dark"><i
                                        class="ico icon-outline-document-text text-success  title-15 me-2"></i> Staff
                                    List</a>
                            </li>


                        </ul>
                    </div>



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


                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                @if (count($employees) > 0)
                    @foreach ($employees as $item)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link employee-item {{ $active_id == $item->id ? 'active' : '' }}"
                                data-id="{{ $item->id }}"
                                data-search="{{ strtolower(($item->employee_salutation ?? '') . ' ' . ($item->first_name ?? '') . ' ' . ($item->last_name ?? '') . ' ' . ($item->document_number ?? '') . ' ' . ($item->email ?? '') . ' ' . ($item->mobile ?? '')) }}"
                                id="purchase-order-1-tab" data-bs-toggle="tab" data-bs-target="#purchase-order-1"
                                type="button" role="tab" aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">

                                    <div class="col-8">
                                        <label class="form-control-plaintext truncate-text">

                                            {{ $item->employee_salutation }} {{ $item->first_name }} {{ $item->last_name }}
                                            @if ($item->approved_by)
                                                <i class="ico icon-bold-verified-check text-success"></i>
                                            @endif
                                        </label>
                                    </div>

                                    <div class="col-4">
                                        <div class="form-control-plaintext text-end"
                                            style="font-size:11px">
                                             @if ($item->approved_by)
                                                 {{ $item->staff_no }}
                                            @else
                                               Pending
                                             @endif
                                        </div>
                                    </div>

                                    <div class="col-4">

                                        <div class="form-control-plaintext" style="font-size:11px">
                                            {{ $item->document_number }}</div>

                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size:11px">{{ $item->email }}
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size:11px">
                                            {{ $item->mobile }}



                                        </div>
                                    </div>



                                </div>
                            </button>
                        </li>
                    @endforeach
                @else
                    <li class="w-100 text-center">
                        <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3"
                                style="width:60px; height:60px; font-size:24px;">
                                <i class="ico icon-outline-info-square"></i>
                            </div>
                            <p class="mb-1 fw-semibold">No Records Found</p>
                            <small class="text-secondary">Try adjusting your filters</small>
                        </div>
                    </li>
                @endif
            </ul>








            <div class="table-responsive mb-4 mt-2">

                <table id="long-list" class="table table-hover data-table d-none" style="table-layout: fixed;width:100%">

                    <thead class="">
                        <tr>
                            <th style="width:16%">Name</th>
                            <th style="width:12%">Email</th>
                            <th style="width:6%">Mobile</th>
                            <th style="width:6%">DOB</th>
                            <th style="width:6%">Place of Birth</th>
                            <th style="width:12%">Permanent Address</th>
                            <th style="width:12%">Current Address</th>
                            <th style="width:6%">Father (Name / Mobile)</th>
                            <th style="width:6%">Mother (Name / Mobile)</th>
                            <th style="width:6%">Emergency Contact (Name / Mobile)</th>
                            <th style="width:6%">Qualification</th>
                            <th style="width:5%">Experience</th>
                            <th class="text-center" style="width:7%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $index => $item)
                            <tr>

                                <td>{{ ($item->employee_salutation ? $item->employee_salutation . ' ' : '') . trim($item->first_name . ' ' . $item->last_name) }}
                                </td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->mobile }}</td>

                                <td>{{ $item->date_of_birth ? \Carbon\Carbon::parse($item->date_of_birth)->format('d/m/Y') : '' }}
                                </td>
                                <td>{{ $item->place_of_birth ?? '' }}</td>
                                <td>
                                    {{ trim(
                                        implode(
                                            ', ',
                                            array_filter([
                                                $item->permanent_building_no,
                                                $item->permanent_area,
                                                $item->permanent_city,
                                                optional(App\SysStates::find($item->permanent_state))->name ?? $item->permanent_state,
                                            ]),
                                        ),
                                    ) }}
                                </td>
                                <td>
                                    {{ trim(
                                        implode(
                                            ', ',
                                            array_filter([
                                                $item->current_building_no,
                                                $item->current_area,
                                                $item->current_city,
                                                optional(App\SysStates::find($item->current_state))->name ?? $item->current_state,
                                            ]),
                                        ),
                                    ) }}
                                </td>
                                <td>
                                    {{ trim(($item->fathers_first_name ?? '') . ' ' . ($item->fathers_last_name ?? '')) }}

                                </td>
                                <td>
                                    {{ trim(($item->mothers_first_name ?? '') . ' ' . ($item->mothers_last_name ?? '')) }}

                                </td>
                                <td>
                                    {{ $item->emergency_contact_name ?? '' }}

                                </td>
                                <td>{{ $item->qualification }}</td>
                                <td>{{ $item->experience }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn btn-sm btn-light employee-item"
                                            onclick="list_style_new()" data-id="{{ $item->id }}"> View</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center text-muted">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>


                </table>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    // Delegated click works for both static + dynamic .data-item
                    $(document).on('click', '.employee-item', function() {
                        var id = $(this).data('id');

                        $('.employee-item').removeClass('active');
                        $('.employee-item[data-id="' + id + '"]').addClass('active');

                        var queryString = window.location.search; // keep filters




                        var newUrl = "{{ url('onboarding-employee-list') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);


                        var action = "{{ URL::to('onboarding-employee-view') }}/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#employee-details').html(response);
                                flatpickr(".date-picker", {
                                    dateFormat: "d/m/Y", // dd/mm/yyyy
                                    allowInput: true
                                });
                            },
                            error: function() {
                                $('#employee-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>



            <div class="" role="tabpanel" aria-labelledby="po-tab" id="employee-details">
                @if ($selectedEmployee)
                    @include('backEnd.humanResource.onboarding-employee.view', [
                        'employee' => $selectedEmployee,
                    ])
                @else
                    {{-- <p class="text-danger">No details available.</p> --}}

                    <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4">
                            <div class="copy-onboard-url" data-copy-url="{{ url('onboard-employee') }}"
                                title="Click to copy link" style="cursor:pointer">
                                <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                    style="width: 80px; height: 80px; font-size: 36px;">
                                    <i class="ico icon-outline-add-square"></i>
                                </div>
                                <h1 class="fw-bold mt-3 copy-onboard-link" style="cursor:pointer" data-bs-toggle="modal"
                                    data-bs-target="#addlead">
                                    Onboard Employees
                                </h1>
                                <p class="text-muted">
                                    Easily create, manage, and track employee onboarding
                                </p>
                            </div>

                        </div>

                    </div>
                @endif
            </div>


        </div>
    </div>









    <script>
        $(document).ready(function() {

            // Client-side search: debounced filter, highlight, and keyboard navigation
            (function() {
                const $input = $('#search_lead');
                const $list = $('#short-list');
                const liveId = 'search-no-results';

                // add aria live region for accessibility
                if (!document.getElementById(liveId)) {
                    $list.after('<div id="' + liveId + '" class="no-results" aria-live="polite"></div>');
                }

                // prepare items
                function initItems() {
                    const items = $list.find('.employee-item');
                    items.each(function() {
                        const $btn = $(this);
                        if (!$btn.data('originalHtml')) {
                            $btn.data('originalHtml', $btn.html());
                        }
                        if (!$btn.data('search')) {
                            $btn.attr('data-search', ($btn.text() || '').trim().toLowerCase());
                        }
                        // make focusable
                        $btn.attr('tabindex', 0);
                    });
                }

                initItems();

                // helper: escape regexp
                function escapeRegExp(s) {
                    return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                }

                function clearHighlight($btn) {
                    const orig = $btn.data('originalHtml');
                    if (orig) $btn.html(orig);
                }

                function highlight($btn, q) {
                    const orig = $btn.data('originalHtml') || $btn.html();
                    if (!q) {
                        clearHighlight($btn);
                        return;
                    }
                    const re = new RegExp('(' + escapeRegExp(q) + ')', 'ig');
                    $btn.html(orig.replace(re, '<span class="search-hl">$1</span>'));
                }

                function filter(q) {
                    q = (q || '').trim().toLowerCase();
                    let any = false;
                    $list.find('.employee-item').each(function() {
                        const $btn = $(this);
                        const hay = ($btn.attr('data-search') || '').toLowerCase();
                        const $li = $btn.closest('li');
                        if (!q || hay.indexOf(q) !== -1) {
                            $li.show();
                            highlight($btn, q);
                            any = true;
                        } else {
                            $li.hide();
                            clearHighlight($btn);
                        }
                    });
                    $('#' + liveId).text(any ? '' : 'No results');
                    focusedIndex = -1;
                }

                let debounceTimer = null;
                $input.on('input', function(e) {
                    clearTimeout(debounceTimer);
                    const q = $(this).val();
                    debounceTimer = setTimeout(function() {
                        // if list is already populated on page use client-side filter
                        const count = $list.find('.employee-item').length;
                        if (count > 0) {
                            filter(q);
                        } else {
                            // fallback to server search if no items are present
                            // keep legacy ajax behavior
                            $.ajax({
                                url: "{{ route('leads.search') }}",
                                type: "GET",
                                data: {
                                    query: q
                                },
                                success: function(data) {
                                    $list.html('');
                                    if (data.length > 0) {
                                        $.each(data, function(index, lead) {
                                            let ims =
                                                ` <li class="nav-item w-100" role="presentation">\n` +
                                                `<button class="nav-link employee-item" data-id="${lead.id}">` +
                                                `<div class="row w-100"><div class="col-12"><label class="form-control-plaintext truncate-text">${lead.customername?.name || ''}</label></div>` +
                                                `<div class="col-4"><div class="form-control-plaintext" style="font-size: 11px">${lead.code}</div></div>` +
                                                `<div class="col-4 text-center"><div class="form-control-plaintext" style="font-size: 11px">${get_format_date(lead.created_at)}</div></div>` +
                                                `<div class="col-4 text-end"><div class="form-control-plaintext truncate-text" style="font-size: 11px">${lead.lead_deal_code?.code || ''}</div></div>` +
                                                `</div></button></li>`;
                                            $list.append(ims);
                                        });
                                        initItems();
                                        filter(q);
                                    } else {
                                        $list.html(
                                            '<div class="p-2">No results found</div>'
                                            );
                                    }
                                }
                            });
                        }
                    }, 150);
                });

                // escape clears
                $input.on('keydown', function(e) {
                    if (e.key === 'Escape' || e.keyCode === 27) {
                        $(this).val('');
                        filter('');
                    }
                });

                // keyboard navigation
                let focusedIndex = -1;

                function visibleButtons() {
                    return $list.find('.employee-item').filter(function() {
                        return $(this).closest('li').is(':visible');
                    });
                }
                $input.on('keydown', function(e) {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        const v = visibleButtons();
                        if (v.length === 0) return;
                        focusedIndex = Math.min(v.length - 1, focusedIndex + 1);
                        v.removeClass('active-focus');
                        const target = v.eq(focusedIndex);
                        target.addClass('active-focus');
                        target.focus();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        const v = visibleButtons();
                        if (v.length === 0) return;
                        focusedIndex = Math.max(0, focusedIndex - 1);
                        v.removeClass('active-focus');
                        const target = v.eq(focusedIndex);
                        target.addClass('active-focus');
                        target.focus();
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        const v = visibleButtons();
                        if (v.length === 0) return;
                        if (focusedIndex === -1) v.eq(0).click();
                        else v.eq(focusedIndex).click();
                    }
                });

                // If DOM list changes (AJAX or update), re-init
                const mo = new MutationObserver(function() {
                    initItems();
                });
                mo.observe($list[0], {
                    childList: true,
                    subtree: true
                });

            })();

            // styling for highlight and focused item (no background color for matches)
            $('<style>').prop('type', 'text/css').html(
                '.search-hl{padding:0 2px;font-weight:600} .employee-item:focus{outline:2px solid #198754;outline-offset:2px} .active-focus{box-shadow:0 0 0 3px rgba(25,135,84,0.12);}'
                ).appendTo('head');

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

        });
    </script>
    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

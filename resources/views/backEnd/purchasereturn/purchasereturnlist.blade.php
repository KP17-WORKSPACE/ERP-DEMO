@extends('backEnd.newmasterpage')
@section('mainContent')
   <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
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

                sessionStorage.setItem('listViewPurchaseReturn', 'long');
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

                sessionStorage.setItem('listViewPurchaseReturn', 'short');

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
            // Check if we have pr_action parameter (add/edit mode)
            const urlParams = new URLSearchParams(window.location.search);
            const hasCustomerAction = urlParams.has('pr_action');
            
            // If in add/edit mode, force short view
            if (hasCustomerAction) {
                sessionStorage.setItem('listViewPurchaseReturn', 'short');
                isFullList = true; // Set to true so toggle switches to short
                list_style_new(); // Switch to short view
            } else {
                // Normal behavior - use saved view from sessionStorage
                const savedView = sessionStorage.getItem('listViewPurchaseReturn');
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
                    sessionStorage.setItem('listViewPurchaseReturn', 'short');
                });
            });



        });
    </script>



    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <div class="short-list" id="filters-short">
            <h4 class="mb-2">Purchase Return
            </h4>



            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="search_pr_id" id="search_pr_id" class="form-control"
                        placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>


                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Purchase Return
                </h4>
                <div class="search-filter-container mb-0">

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
                            <!-- export option -->
                            <li>
                                <a href="#" id="exportExcelPurchaseReturn"
                                    class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-export text-success title-15 me-2"></i> Export</a>
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

                <div class="card" style="width:100%">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-return', 'method' => 'get', 'id' => 'purchase-return-search']) }}
                        <div class="row">

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-check-label">Documents Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="documents_number"
                                    value="{{ @$ctrl_doc_number }}">
                            </div>
                            <div class="col-2 mb-2">
                                <label for="" class="form-check-label">Supplier</label>
                                <select class="form-control js-account-select" name="supplier" id="supplier">
                                    <option value=""></option>

                                </select>
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-check-label">Customer</label>
                                <input class="form-control" type="text" autocomplete="off" name="customer"
                                    value="{{ @$ctrl_customer }}">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-check-label">Purchase Order Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="purchase_order_number"
                                    value="{{ @$ctrl_po_number }}">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-check-label">GRN Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="grn_number"
                                    value="{{ @$ctrl_grn_number }}">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-check-label">Purchase Invoice Number</label>
                                <input class="form-control" type="text" autocomplete="off"
                                    name="purchase_invoice_number" value="{{ @$ctrl_pi_number }}">
                            </div>
                            <div class="col-1-5 mb-2">
                                <label for="" class="form-check-label">Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="date"
                                    value="{{ @$ctrl_date }}">
                            </div>

                            <div class="col-1">
                                <button type="submit" class="btn btn-light mt-4">
                                    <i class="ico icon-outline-magnifer text-success"></i> Filter
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                @if (count($purchasereturn) > 0)
                    @foreach ($purchasereturn as $value)
                        <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)"
                                class="nav-link data-item {{ $active_id == $value->id ? 'active' : '' }}"
                                data-id="{{ $value->id }}">

                                <div class="row w-100">
                                    <div class="col-12">
                                        <label
                                            class="form-control-plaintext truncate-text">

                                            {{ $value->accountname->account_name }}
                                            @if (@App\SysHelper::getCompanyCodeSettings()['is_supplier_code'])
                                                 ({{ $value->accountname->account_code }})
                                                
                                            @endif </label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">{{ $value->doc_number }}</div>
                                    </div>
                                    <div class="col-4 pl-2">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            {{ date('d/m/Y', strtotime(@$value->doc_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            {{ @App\SysHelper::com_curr_format($value->amount, 2, '.', ',') }}
                                            {{ $value->currency_name->code }}</div>
                                    </div>
                                    
                                </div>

                            </button>
                        </li>
                    @endforeach
                @endif
            </ul>

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover d-none data-table" style="table-layout: fixed;width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 100px"> @lang('PRT Number')</th>
                            <th class="text-center" style="width: 100px"> @lang('PRT Date')</th>
                            <th style="width: 250px"> @lang('Supplier')</th>
                            <th style="width: 250px"> @lang('Customer')</th>
                            <th class="text-center" style="width: 100px"> @lang('PO No')</th>
                            <th class="text-center" style="width: 100px"> @lang('GRN No')</th>
                            <th class="text-center" style="width: 100px"> @lang('PIV No')</th>
                            <th class="text-center" style="width: 100px">@lang('Deal No')</th>
                            <th class="text-center" style="width: 70px">@lang('Sales Person')</th>
                            <th class="text-end" style="width: 100px"> @lang('Amount')</th>
                            <th class="text-end" style="width: 80px"> @lang('Currency')</th>
                            <th class="text-center" style="width: 100px"> @lang('lang.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($purchasereturn))
                            @foreach ($purchasereturn as $value)
                                <tr @if (@$value->status == 2) class="bg-dark" @endif>
                                    <td class="text-center"><a href="javascript:void(0)" onclick="list_style_new()"
                                            class="data-item"
                                            data-id="{{ $value->id }}">{{ @$value->doc_number }}</a>
                                    </td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</td>
                                    <td>{{ @$value->accountname->account_name }}</td>
                                    <td>

                                     @php
                                    
                                        $selectedCompanies = $value->ref_company_id
        ? explode(',', $value->ref_company_id)
        : [];
      
                                    @endphp
                                    
                                      @forelse ($selectedCompanies as $companyId)
                                          @php
                                              $company = App\SysCustSuppl::find($companyId);
                                          @endphp
                                          @if ($company)
                                              <span>{{ $company->name }}
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                              </span>
                                          @endif
                                      @empty
                                          
                                      @endforelse
                                    
                                </td>
                                    <!-- PO Number (single link) -->
                                    <td class="text-center">
                                        @if (empty($value->po_no))
                                            <span class="text-dark">Pending</span>
                                        @else
                                            @foreach (explode(',', $value->po_no) as $po)
                                                <a href="{{ url('get-url-purchase-order/' . trim($po)) }}"
                                                    target="_blank">{{ trim($po) }}</a>
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>

                                    <!-- GRN Numbers (multiple links if comma-separated) -->
                                    <td class="text-center">
                                        @if (empty($value->grnno))
                                            <span class="text-dark">Pending</span>
                                        @else
                                            @foreach (explode(',', $value->grnno) as $grn)
                                                <a href="{{ url('get-url-purchase-grn/' . trim($grn)) }}"
                                                    target="_blank">{{ trim($grn) }}</a>
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>

                                    <!-- PI Numbers (multiple links if comma-separated) -->
                                    <td class="text-center">
                                        @if (empty($value->pi_number))
                                            <span class="text-dark">Pending</span>
                                        @else
                                            @foreach (explode(',', $value->pi_number) as $pi)
                                                <a href="{{ url('get-url-purchase-invoice/' . trim($pi)) }}"
                                                    target="_blank">{{ trim($pi) }}</a>
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>

                                    <!-- Deal Codes (multiple links if comma-separated) -->
                                    <td class="text-center">
                                        @if (empty($value->code))
                                            <span class="text-dark">Pending</span>
                                        @else
                                            @foreach (explode(',', $value->code) as $code)
                                                <a href="{{ url('get-url-deal-track/' . trim($code)) }}"
                                                    target="_blank">{{ trim($code) }}</a>
                                                @if (!$loop->last)
                                                    ,
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>

                                     <td class=""> @if(@$value->sales_person != null)
                                        {{ @$value->salesperson->first_name.' '.@$value->salesperson->last_name }}
                                    @elseif(@$value->sales_person_name != null)
                                    {{ @$value->sales_person_name}}
                                    @endif</td>

                                    <td class="text-end">
                                        {{ @App\SysHelper::com_curr_format($value->amount, 2, '.', ',') }}
                                    </td>

                                    <td class="text-center">{{ @$value->currency_name->code }}</td>
                                        
                                        


                                    <td class="text-center">
                                                
                                    <div class="d-flex justify-content-center align-items-center gap-1">


                                        <a href="{{ url('purchase-return/' . $value->id . '?pr_action=edit') }}"
                                            onclick="list_style_new()" class="btn btn-sm btn-light" title="Comments">
                                            <i class="ico icon-outline-pen-2" style="font-size: 16px;"></i>
                                        </a>


                                        <a class=" btn-sm btn-light"
                                            href="{{ url('purchase-return/' . $value->id . '/download') }}"><i
                                                class="ico icon-bold-download-minimalistic text-dark"
                                                style="font-size: 16px;"></i></a>


                                      
                                            @if ($value->status == 2)
                                                <a class="btn btn-light btn-sm"
                                                   href="{{url('purchase-return/'.$value->id.'/restore')}}"
                                                    onclick="return confirm('Are you sure you want to restore this item?');">
                                                    <i class="ico icon-bold-restart" style="font-size: 16px;"></i>
                                                </a>
                                            @else
                                                <a class="btn btn-light btn-sm"
                                                    href="{{url('purchase-return/'.$value->id.'/delete')}}"
                                                    onclick="return confirm('Are you sure you want to delete this item?');">
                                                    <i class="ico  icon-outline-trash-bin-minimalistic text-dark"
                                                        style="font-size: 16px;"></i>
                                                </a>
                                            @endif
                                       

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
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    $(document).on('click', '.data-item', function() {




                        var id = $(this).data('id');
                        $('.data-item').removeClass('active');
                        $('.data-item[data-id="' + id + '"]').addClass('active');


                        var newUrl = "{{ url('purchase-return') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('purchase-return-details') }}/" + id;
                        $('#loading_bg').show();
                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#data-details').html(response);
                            },
                            error: function() {
                                $('#data-details').html(
                                    '<p class="text-danger">Error loading details.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });


                    });


                });
            </script>



            <div class="" role="tabpanel" aria-labelledby="po-tab" id="data-details">

                @if ($action === 'add')
             
                    @include('backEnd.purchasereturn.pr_add', $addData)
                @elseif($action === 'edit')
                    @include('backEnd.purchasereturn.pr_edit', $editData)
                @elseif (isset($data) && !empty($data))
                    @include('backEnd.purchasereturn.pr_details', $data)
                @else
                    <div onclick="window.location.href='{{ url('purchase-return?pr_action=add') }}'"
                        class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer"> Purchase Return</h1>
                            {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                        </div>

                    </div>
                @endif
            </div>




        </div>
    </div>




    <script>
   const SHOW_SUPPLIER_CODE = {{ @App\SysHelper::getCompanyCodeSettings()['is_supplier_code'] ? 'true' : 'false' }};

        $(document).ready(function() {
            function initAccountSelect2(selector) {
                $(selector).select2({
                    ajax: {
                        url: '{{ route('autocomplete.get_supp_account_list_ajax') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search_text: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                   let text = "";

                                if (SHOW_SUPPLIER_CODE) {
                                    text = item.account_name + " (" + item.account_code + ")";
                                } else {
                                    text = item.account_name;  // no code
                                }

                                return {
                                    id: item.id,
                                    text: text
                                };
                                })
                            };
                        },
                        cache: true
                    },
                    placeholder: 'Select Account',
                    minimumInputLength: 2
                });
            }

            // Initial init
            initAccountSelect2('.js-account-select');

            // Re-initialize on focus (if needed for dynamically added fields)
            $(document).on('focus', '.js-account-select', function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    initAccountSelect2(this);
                }
            });

            // Open dropdown and focus search box on click
            $(document).on('click', '.js-account-select', function() {
                $(this).select2('open');
            });

            // Focus the search input inside the opened Select2 dropdown
            $(document).on('select2:open', function() {
                setTimeout(function() {
                    const searchInput = document.querySelector(
                        '.select2-container--open .select2-search__field');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }, 0);
            });
        });
    </script>


    <script>
        $(document).ready(function() {

            $('#search_pr_id').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('crm-pr.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#short-list').html('');



                        if (data.length > 0) {
                            $.each(data, function(index, amc_list) {

                                console.log(amc_list);

                                let ims = `<li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)"
                                class="nav-link data-item"
                                data-id="${amc_list.id}">

                                <div class="row w-100">
                                     <div class="col-12">
                                        <label
                                            class="form-control-plaintext truncate-text">${amc_list.accountname.account_code}
                                            - ${amc_list.accountname.account_name}</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">${amc_list.doc_number}</div>
                                    </div>
                                    <div class="col-4 pl-2">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                             ${get_format_date(amc_list.doc_date)}
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                             ${amc_list.formatted_amount}
                                            ${amc_list.currency_name.code}</div>
                                    </div>
                                   
                                </div>
                              
                            </button>
                        </li>`;








                                $('#short-list').append(ims);
                            });
                        } else {
                            $('#short-list').html('<div class="p-2">No results found</div>');
                        }
                    }
                });
            });

        });
    </script>

<script>
$(document).ready(function() {
    $('#exportExcelPurchaseReturn').on('click', function(e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var totalPR = @json($purchasereturn->count() ?? 0);
        var dateFrom = @json($ctrl_date ?? '');
        var dateTo = @json($ctrl_date2 ?? '');

        var $table = $('#long-list');

        var visibleColIndexes = [];
        var headerLabels = [];
        var lastIndex = $table.find('thead tr th').length - 1;

        $table.find('thead tr th').each(function(i) {
            if (i === lastIndex) return;
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
        rows.push(['Purchase Returns (' + totalPR + ')']);

        if (dateFrom || dateTo) {
            var parts = [];
            if (dateFrom) { parts.push('Of: ' + formatDMY(dateFrom)); }
            if (dateTo) { parts.push('To: ' + formatDMY(dateTo)); }
            rows.push([parts.join('  ')]);
        }

        rows.push([]);
        rows.push(headerLabels);

        $table.find('tbody tr').each(function() {
            var $cells = $(this).find('td');
            if ($cells.length === 0) return;
            var rowData = [];
            visibleColIndexes.forEach(function(i) {
                var cellText = $cells.eq(i).text().trim().replace(/\s+/g, ' ');
                rowData.push(cellText);
            });
            rows.push(rowData);
        });

        if (rows.length <= 5) {
            alert('No data available for export');
            return;
        }

        var N = headerLabels.length || 1;
            var workbook  = new ExcelJS.Workbook();
            var worksheet = workbook.addWorksheet('Purchase Return');
            var wsCols = [];
            for (var ci = 0; ci < N; ci++) { wsCols.push({ width: 22 }); }
            worksheet.columns = wsCols;

            var hdrIdx = rows.indexOf(headerLabels);
            if (hdrIdx < 0) hdrIdx = rows.length - 1;

            // Meta rows (company name, page title, optional date rows)
            var wsRowNum = 0;
            for (var ri = 0; ri < hdrIdx; ri++) {
                if (!(rows[ri] && rows[ri][0])) continue; // skip blank separators
                wsRowNum++;
                var wsRow = worksheet.addRow([]);
                wsRow.height = ri === 0 ? 26 : ri === 1 ? 20 : 16;
                if (N > 1) worksheet.mergeCells(wsRowNum, 1, wsRowNum, N);
                wsRow.getCell(1).value = rows[ri][0] || '';
                if (ri === 0) wsRow.getCell(1).font = { bold: true, size: 14 };
                else if (ri === 1) wsRow.getCell(1).font = { bold: true, size: 12 };
                wsRow.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
            }

            // Blank separator
            wsRowNum++;
            worksheet.addRow([]);

            // Column header row
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

            // Data rows
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
                var filename = 'purchase_return_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                saveAs(blob, filename);
            });
    });
});
</script>

    <?php } catch (\Exception $e) { ?> {{ $e }}
    <?php  } ?>
@endsection

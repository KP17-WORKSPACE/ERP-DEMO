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

                localStorage.setItem('listViewPRoforma', 'long');
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

                localStorage.setItem('listViewPRoforma', 'short');

            }


        }


        //added ny kp
        function toggleLongFilters() {

            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }




        // Initialize view from localStorage
        document.addEventListener('DOMContentLoaded', () => {
            const savedView = localStorage.getItem('listViewPRoforma');
            if (savedView === 'long') {
                isFullList = false; // so that toggling once activates full view
                list_style_new();
            } else {
                // Default to short view
                isFullList = true; // so that toggling once activates short view
                list_style_new();
            }

            // Attach event to sidebar links to force short view on navigation
            document.querySelectorAll('.sub-nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    localStorage.setItem('listViewPRoforma', 'short');
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
            <h4 class="mb-2">Proforma Invoice
            </h4>

            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order', 'method' => 'get', 'id' => 'purchase-order-search']) }} --}}

            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="documents_number" id="documents_number" class="form-control" placeholder="Document No"
                        aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>


                
                {{-- {{ Form::close() }} --}}
                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Proforma Invoice List
                </h4>
                <div class="search-filter-container mb-0">

                     <input type="text" id="tableSearch" class="form-control"
                        style="font-size:13px; width: 350px; 
                        top: 12px;
                        right: 120px;
                        margin-right:40px"
                        placeholder="Search"> 


                    <button type="button" class="btn btn-light list_style_search_btn mt-1" id="exportExcelProforma" style="margin-right: 8px;">
                        <i class="ico icon-outline-export text-success"></i> Export
                    </button>
                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>


        </div>

        <div class="left-nav-list">

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                
                @if (count($quotations) > 0)
                    @foreach ($quotations as $item)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link proforma-item {{ $active_id == $item->id ? 'active' : '' }}"
                                data-id="{{ $item->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                      <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ @$item->customername->name }}</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">{{ @$item->doc_number }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                            {{ date('d/m/Y', strtotime(@$item->doc_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            {{ @$item->deal_code->code }}
                                        </div>
                                        
                                    </div>
                                  
                                    
                                </div>
                            </button>
                        </li>
                    @endforeach
                @else
                    No Records
                @endif
            </ul>
            

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover data-table d-none" style="table-layout: fixed;width:100%">

                    <thead class="text-center">
                        <tr>
                            <th style="width: 90px;" class="text-center">@lang('Doc Date')</th>
                            <th style="width: 90px;" class="text-center">@lang('Deal ID')</th>
                            <th style="width: 90px;" class="text-center">@lang('QTN No')</th>
                            <th style="width: 90px;" class="text-center">@lang('PI No')</th>
                            <th style="width: 300px;" class="text-start">@lang('Customer Name')</th>
                            <th style="width: 200px;" class="text-start">@lang('Salesman Name')</th>
                            <th style="width: 90px;" class="text-center">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $count =1; @endphp
                        @foreach ($quotations as $value)
                            <tr>
                                <td class="text-center">{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</td>
                                <td class="text-center"><a
                                        href="{{ url('get-url-deal-track/' . @$value->deal_code->code) }}"
                                        target="_blank">{{ @$value->deal_code->code }}</a></td>
                                <td class="text-center">{{ @$value->deal_code->code }}</td>
                                <td class="text-center">{{ @$value->doc_number }}</td>
                                <td>{{ @$value->customername->name }}</td>
                                <td>{{ @$value->salesman->full_name }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a class="btn btn-light btn-sm"
                                            href="{{ url('proforma-invoice/' . $value->id . '/download') }}"><i
                                                class="ico icon-bold-download-minimalistic text-dark"
                                                style="font-size: 16px;"></i></a>
                                    </div>

                                    {{--  <a class="btn-sm btn-primary" href="{{url('proforma-invoice/'.$value->id.'/edit')}}" class="btn-small"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        <a class="btn-sm btn-info" href="{{url('proforma-invoice/'.$value->id.'/view')}}"><i class="fa fa-eye" aria-hidden="true"></i></a>  --}}

                                </td>
                            </tr>
                        @endforeach
                    </tbody>


                </table>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    $(document).on('click', '.proforma-item', function() {

                  
                        var id = $(this).data('id');
                        console.log(id)
                        $('.proforma-item').removeClass('active');
                        $('.proforma-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('proforma-invoice') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('proforma-invoice-details') }}/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#proforma-details').html(response);
                            },
                            error: function() {
                                $('#proforma-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>




            <div class="" role="tabpanel" aria-labelledby="po-tab" id="proforma-details">

                @if($action == 'add')
                    @include('backEnd.proformainvoice.manage_proforma_invoice', $createData)
                @elseif($action == 'edit')
                    @include('backEnd.proformainvoice.manage_proforma_invoice_edit', $editData)
                @elseif (!empty($selectedInv) && is_array($selectedInv))
                    @include('backEnd.proformainvoice.proforma_invoice_details', $selectedInv)
                @else
                 <form id="supplierForm" method="GET" action="{{ url('proforma-invoice') }}">
                    <input type="hidden" name="proforma_action" value="add" id="">
                      <div onclick="document.getElementById('supplierForm').submit();" class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4" >
                            <div  class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer" >Proforma Invoice </h1>
                            {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                        </div>

                    </div>
                 </form>
                @endif
            </div>

        </div>
    </div>

  <script>
        $(document).ready(function() {

            $('#documents_number').on('input', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('proforma-crm.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#short-list').html('');



                        if (data.length > 0) {
                            $.each(data, function(index, amc_list) {

                                console.log(amc_list);

                                let ims = ` <li class="nav-item w-100" role="presentation">
                            <button class="nav-link proforma-item"
                                data-id="${amc_list.id}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            ${amc_list.customername.name}</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">${amc_list.doc_number}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                            ${get_format_date(amc_list.doc_date)}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            ${amc_list.deal_code.code}
                                        </div>
                                        
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




</script>

<script>
$(document).ready(function() {
    $('#exportExcelProforma').on('click', function(e) {
        e.preventDefault();

        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var totalItems = @json(count($quotations));

        var $table = $('#long-list');
        if ($table.length === 0) {
            alert('Export table not found');
            return;
        }

        var visibleColIndexes = [];
        var headerLabels = [];
        var lastIndex = $table.find('thead tr th').length - 1;

        $table.find('thead tr th').each(function(i) {
            if (i === lastIndex) return;
            if ($(this).css('display') !== 'none') {
                var label = $(this).text().trim();
                if (['action', 'actions', 'actions '].includes(label.toLowerCase().trim())) {
                    return;
                }
                visibleColIndexes.push(i);
                headerLabels.push(label);
            }
        });

        var rows = [];
        rows.push([companyName]);
        rows.push(['Proforma Invoices (' + totalItems + ')']);
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

        if (rows.length <= 4) {
            alert('No data available for export');
            return;
        }

        var N = headerLabels.length || 1;
            var workbook  = new ExcelJS.Workbook();
            var worksheet = workbook.addWorksheet('ProformaInvoice');
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
                var filename = 'proforma_invoice_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
                saveAs(blob, filename);
            });
    });
});
</script>

<div class="modal side-panel  fade" id="paymenttermsModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm draggable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add </h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                       

                          
                            <label class="form-label">Payment Terms <span class="text-danger">*</span></label>
                                <input type="text" id="payment_term_title" name="name" class="form-control" required="" autocomplete="off">
            
                        <div class="modal-footer d-flex justify-content-center p-0">
                            <button type="button" id="savePaymentTerm" class="btn btn-light add-btn ms-2">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Submit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{-- Modal PO --}}


<script>
$(document).on('click', '#savePaymentTerm', function () {

    let title = $('#payment_term_title').val().trim();
    let input = $('#payment_term_title');

    input.removeClass('is-invalid');
    input.next('.invalid-feedback').text('');

    if (!title) {
        input.addClass('is-invalid');
        input.next('.invalid-feedback').text('Payment term is required');
        return;
    }

    $.ajax({
        url: "{{  url('payment-terms-store-ajax') }}", // adjust route
        type: "POST",
        data: {
            title: title,
            _token: "{{ csrf_token() }}"
        },
        beforeSend: function () {
            $('#loading_bg').show();

        },
        success: function (res) {

            if (res.status) {

                // ✅ NEW ID AVAILABLE HERE
                console.log('New ID:', res.data.id);

                // Example: append to dropdown
                $('#payment_terms').append(
                    `<option value="${res.data.id}" selected>${res.data.title}</option>`
                );

                $('#paymenttermsModal').modal('hide');
                $('#payment_term_title').val('');

                toastr.success(res.message, 'Success');
            }
        },
        error: function (xhr) {

            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                if (errors.title) {
                    input.addClass('is-invalid');
                    input.next('.invalid-feedback').text(errors.title[0]);
                }
            } else {
                toastr.error('Something went wrong', 'Error');
            }
        },
        complete: function () {
            $('#loading_bg').hide();
        }
    });
});
</script>


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

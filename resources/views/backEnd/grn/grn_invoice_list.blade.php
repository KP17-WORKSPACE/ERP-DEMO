@extends('backEnd.newmasterpage')
@section('mainContent')

<?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>
<?php try { ?>
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

     


     <div class="content-container col-12">
                    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
                        <div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
                            <div class="purchase-order-content-header">
                                <h4 class="purchase-order-content-header-left">
                                    Goods Receipt Note (GRN)
                                </h4>
                                <div class="purchase-order-content-header-right">
                                    <button class="btn btn-dark">
                                        <i class="ico icon-outline-add-circle"></i> Add
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ico icon-outline-hamburger-menu"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="javascript:void(0)" id="exportExcelGRN"><i class="ico icon-outline-file-text text-success"></i> Export Excel</a></li>
                                        </ul>
                                    </div>
                                    {{-- <div class="dropdown">
                                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ico icon-outline-hamburger-menu"></i>
                                        </button>
                                        <ul class="dropdown-menu" style="">
                                            <li><a class="dropdown-item" href="#"><i class="ico icon-outline-trash-bin-minimalistic text-danger"></i> Delete</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="ico icon-outline-download-minimalistic text-warning"></i> Download</a></li>
                                            <li><a class="dropdown-item" href="#"> <i class="ico icon-outline-printer text-success"></i> Print</a></li>
                                        </ul>
                                    </div> --}}
                                </div>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-body">
                                    <table id="long-list" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>@lang('GRN No')</th>
                                    <th>@lang('GRN Date')</th>
                                    <th>@lang('Supplier')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('PO No')</th>
                                    <th>@lang('PIV No')</th>
                                    <th>@lang('PRT No')</th>
                                    <th>@lang('Deal No')</th>
                                    <th>@lang('Currency')</th>
                                    <th class="text-end">@lang('Amount')</th>
                                    <th><i class="ico icon-bold-paperclip"></i></th>
                                    <th class="text-end">@lang('lang.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $count =1; @endphp
                         @foreach($purchasegrn as $value)
                         <tr @if (@$value->status == 2) class="bg-dark" @endif>
                             <td><a href="{{url('goods-receipt-note/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td>{{date('d/m/Y', strtotime(@$value->grn_date))}}</td>
                             <td>{{@$value->accountname->account_name}}</td>
                             <td>{{@$value->reference}}</td>

                            <td>
                             <?php
                             $lpo = explode(',',$value->lpo_number);
                             if(count($lpo)>0){
                                foreach($lpo as $p){
                                    ?>
                                    <a href="{{url('get-url-purchase-order/'.$p)}}" target="_blank">{{@$p}}</a>
                                    <?php
                                }
                             }
                             ?>                            
                            </td>
                             <td>
                                @if (empty($value->piv_no))
                                    <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', $value->piv_no) as $piv)
                                        <a href="{{ url('get-url-purchase-invoice/' . trim($piv)) }}" target="_blank">{{ trim($piv) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>
                             <td>
                                @if (empty($value->prt_no))
                                    <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', $value->prt_no) as $prt)
                                        <a href="{{ url('get-url-purchase-return/' . trim($prt)) }}" target="_blank">{{ trim($prt) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                             </td>
                            
                             <td> 
                             <?php
                             $code = explode(',',$value->code);
                             if(count($code)>0){
                                foreach($code as $c){
                                    $cd = @App\SysHelper::get_code_from_dealid($c);
                                    ?>
                                    <a href="{{url('get-url-deal-track/'.$cd)}}" target="_blank">{{ $cd }}</a>
                                    <?php
                                }
                             }
                             ?>
                            </td>
                            <td>{{ $value->currency_name->code }}</td>
                             
                             <td class="text-end">{{ @App\SysHelper::com_curr_format($value->amount,2,'.',',')}}</td>
                             <td>
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>
                             <td class="text-end">
                                <a class="btn-md" href="{{url('goods-receipt-note/'.$value->id.'/download')}}"><i class="ico icon-outline-download-square" aria-hidden="true"></i></a>
                                {{--  <a class="btn-sm btn-primary" href="{{url('goods-receipt-note/'.$value->id.'/download')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>  --}}
                                
                             </td>
                         </tr>
                         @endforeach
                            </tbody>
                        </table>
                                </div>
                            </div>

                        </div>
                    </div>
     </div>
<div class="container-fluid" style="display: none;">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Goods Receipt Note (GRN)</h2>
            <span class="page-label">Home - Goods Receipt Note (GRN)</span>
        </div>
        <div>
            <a href="{{ url('goods-receipt-note/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
            <a href="{{ url('goods-receipt-note') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card shadow mb-4 p-4">
            
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'goods-receipt-note', 'method' => 'get', 'id' => 'goods-receipt-note']) }}
            <div class="row">

                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Documents Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="documents_number" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Supplier</label>
                        <select class="form-control js-account-select" name="supplier" id="supplier">
                            <option value=""></option>
                            {{-- @foreach ($supplier_list as $value)
                                <option value="{{ @$value->id }}" >{{ @$value->account_name }}
                                </option>
                            @endforeach --}}
                        </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Customer</label>
                    <input class="form-control" type="text" autocomplete="off" name="customer" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Deal ID</label>
                    <input class="form-control" type="text" autocomplete="off" name="deal_number" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Purchase Invoice Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="purchase_invoice_number" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Purchase Order Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="purchase_order_number" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Purchase Return Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="purchase_return_number" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Date</label>
                    <input class="form-control" type="date" autocomplete="off" name="date" value="">
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning mr-2" id="btnSubmit">Clear</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">Filter</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        @if(session()->has('message-success') != "" ||
                         session()->get('message-danger') != "")
                         <tr>
                             <td colspan="11">
                                  @if(session()->has('message-success'))
                                   <div class="alert alert-success">
                                       {{ session()->get('message-success') }}
                                   </div>
                                 @elseif(session()->has('message-danger'))
                                   <div class="alert alert-danger">
                                       {{ session()->get('message-danger') }}
                                   </div>
                                 @endif
                             </td>
                         </tr>
                          @endif 
                         <tr>
                            <th>@lang('GRN No')</th>
                            <th>@lang('GRN Date')</th>
                             <th>@lang('Supplier')</th>
                             <th>@lang('Customer')</th>
                             <th>@lang('PO No')</th>
                             <th>@lang('PIV No')</th>
                             <th>@lang('PRT No')</th>
                             <th>@lang('Deal No')</th>
                             <th>@lang('Currency')</th>
                             <th class="text-end">@lang('Amount')</th>
                             <th><i class="ico icon-bold-paperclip"></i></th>
                             <th class="text-end">@lang('lang.action')</th>
                         </tr>
                     </thead>
                     <tbody>
                         @php $count =1; @endphp
                         @foreach($purchasegrn as $value)
                         <tr @if (@$value->status == 2) class="bg-dark" @endif>
                             <td><a href="{{url('goods-receipt-note/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td>{{date('d/m/Y', strtotime(@$value->grn_date))}}</td>
                             <td>{{@$value->accountname->account_name}}</td>
                             <td>{{@$value->reference}}</td>

                            <td>
                             <?php
                             $lpo = explode(',',$value->lpo_number);
                             if(count($lpo)>0){
                                foreach($lpo as $p){
                                    ?>
                                    <a href="{{url('get-url-purchase-order/'.$p)}}" target="_blank">{{@$p}}</a>
                                    <?php
                                }
                             }
                             ?>                            
                            </td>
                             <td>
                                @if (empty($value->piv_no))
                                    <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', $value->piv_no) as $piv)
                                        <a href="{{ url('get-url-purchase-invoice/' . trim($piv)) }}" target="_blank">{{ trim($piv) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>
                             <td>
                                @if (empty($value->prt_no))
                                    <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', $value->prt_no) as $prt)
                                        <a href="{{ url('get-url-purchase-return/' . trim($prt)) }}" target="_blank">{{ trim($prt) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                             </td>
                            
                             <td> 
                             <?php
                             $code = explode(',',$value->code);
                             if(count($code)>0){
                                foreach($code as $c){
                                    $cd = @App\SysHelper::get_code_from_dealid($c);
                                    ?>
                                    <a href="{{url('get-url-deal-track/'.$cd)}}" target="_blank">{{ $cd }}</a>
                                    <?php
                                }
                             }
                             ?>
                            </td>
                            <td>{{ $value->currency_name->code }}</td>
                             
                             <td class="text-end">{{ @App\SysHelper::com_curr_format($value->amount,2,'.',',')}}</td>
                             <td>
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>
                             <td class="text-end">
                                <a class="btn-sm btn-warning" href="{{url('goods-receipt-note/'.$value->id.'/download')}}"><i class="fa fa-download" aria-hidden="true"></i></a>
                                {{--  <a class="btn-sm btn-primary" href="{{url('goods-receipt-note/'.$value->id.'/download')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>  --}}
                                <a class="btn-sm btn-primary" href="{{url('goods-receipt-note/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @if (@$value->status == 2)
                                    <a class="btn-sm btn-warning" href="{{url('goods-receipt-note/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-danger" href="{{url('goods-receipt-note/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif
                             </td>
                         </tr>
                         @endforeach
                     </tbody>
                     <footer>
                         <tr>
                             <td colspan="12">
                                 {{ $purchasegrn->appends(request()->input())->links() }}
                             </td>
                         </tr>
                     </footer>
                </table>
            </div>
        </div>
    </div>

</div>
<script>
$(document).ready(function () {
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_supp_account_list_ajax") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search_text: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id,
                                text: item.account_code + ' - ' + item.account_name
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
    $(document).on('focus', '.js-account-select', function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            initAccountSelect2(this);
        }
    });

    // Open dropdown and focus search box on click
    $(document).on('click', '.js-account-select', function () {
        $(this).select2('open');
    });

    // Focus the search input inside the opened Select2 dropdown
    $(document).on('select2:open', function () {
        setTimeout(function () {
            const searchInput = document.querySelector('.select2-container--open .select2-search__field');
            if (searchInput) {
                searchInput.focus();
            }
        }, 0);
    });
});
</script>
<script>
$(document).ready(function () {
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_product_list_ajax") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search_text: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id,
                                text: item.part_number,
                                description: item.description
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Select Product',
            minimumInputLength: 2,
            dropdownParent: $(selector).parent() // optional: ensures dropdown shows in modals
        });

        $(selector).on('select2:select', function (e) {
            var selectedData = e.params.data;
            $('#description_new').val(selectedData.description || '');
        });
    }

    initAccountSelect2('.js-product-select');

    // Re-initialize on focus if needed
    $(document).on('focus', '.js-product-select', function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            initAccountSelect2(this);
        }
    });

    // On click, open dropdown and focus on search field
    $(document).on('click', '.js-product-select', function () {
        $(this).select2('open');
    });

    // Optional: Auto focus on search input when dropdown opens
    $(document).on('select2:open', function () {
        setTimeout(function () {
            document.querySelector('.select2-container--open .select2-search__field')?.focus();
        }, 0);
    });
});
</script>
<script>
$(document).ready(function () {
    $('#exportExcelGRN').on('click', function () {
        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var headerLabels = ['GRN No', 'GRN Date', 'Supplier', 'Customer', 'PO No', 'PIV No', 'PRT No', 'Deal No', 'Currency', 'Amount'];
        var N = headerLabels.length;

        var dataRows = [];
        $('#long-list tbody tr').each(function () {
            var $tds = $(this).find('td');
            var row = [];
            for (var i = 0; i < 10 && i < $tds.length; i++) {
                row.push($tds.eq(i).text().trim().replace(/\s+/g, ' '));
            }
            if (row.length > 0) dataRows.push(row);
        });

        if (dataRows.length === 0) { alert('No data available for export'); return; }

        var workbook  = new ExcelJS.Workbook();
        var worksheet = workbook.addWorksheet('GRN');

        var wsCols = [];
        for (var ci = 0; ci < N; ci++) { wsCols.push({ width: 18 }); }
        worksheet.columns = wsCols;

        // Row 1 — Company name
        var r1 = worksheet.addRow([]);
        r1.getCell(1).value     = companyName;
        r1.getCell(1).font      = { bold: true, size: 14 };
        r1.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
        r1.height = 26;
        worksheet.mergeCells(1, 1, 1, N);

        // Row 2 — Page title
        var r2 = worksheet.addRow([]);
        r2.getCell(1).value     = 'Goods Receipt Note (GRN)';
        r2.getCell(1).font      = { bold: true, size: 12 };
        r2.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
        r2.height = 20;
        worksheet.mergeCells(2, 1, 2, N);

        // Blank separator
        worksheet.addRow([]);

        // Header row
        var headerRow = worksheet.addRow(headerLabels);
        headerRow.height = 20;
        headerRow.eachCell({ includeEmpty: true }, function (cell) {
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
        dataRows.forEach(function (rowData) {
            var dr = worksheet.addRow(rowData);
            dr.eachCell({ includeEmpty: true }, function (cell) {
                cell.border = {
                    top:    { style: 'thin', color: { argb: 'FFCCCCCC' } },
                    left:   { style: 'thin', color: { argb: 'FFCCCCCC' } },
                    bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                    right:  { style: 'thin', color: { argb: 'FFCCCCCC' } }
                };
            });
        });

        workbook.xlsx.writeBuffer().then(function (buffer) {
            var blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
            function pad(n) { return n < 10 ? '0' + n : n; }
            var d = new Date();
            var filename = 'grn_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
            saveAs(blob, filename);
        });
    });
});
</script>
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection
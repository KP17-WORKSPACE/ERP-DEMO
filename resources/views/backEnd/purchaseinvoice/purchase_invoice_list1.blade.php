@extends('backEnd.newmasterpage')
@section('mainContent')

<?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Purchase Invoice</h2>
            <span class="page-label">Home - Purchase Invoice</span>
        </div>
        <div>
            <a href="{{ url('purchase-invoice/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
            <a href="{{ url('purchase-invoice') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card shadow mb-4 p-4">            
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-invoice', 'method' => 'get', 'id' => 'purchase-invoice-search']) }}
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
                    <label for="" class="form-check-label">Purchase Order Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="purchase_order_number" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">GRN Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="grn_number" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Purchase Return Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="purchase_return_number" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">From Date</label>
                    <input class="form-control" type="date" autocomplete="off" name="from_date" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">To Date</label>
                    <input class="form-control" type="date" autocomplete="off" name="to_date" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Sales Person</label>
                    <select class="form-control js-example-basic-single" name="sales_person" id="sales_person">
                        <option value=""></option>
                        @foreach ($sales_person_list as $value)
                            <option value="{{ @$value->user_id }}" >{{ @$value->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Currency</label>
                    <select class="form-control" name="currency" id="currency">
                        <option value=""></option>
                    @foreach ($currency as $value)
                        <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                    @endforeach
                </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Attachment</label>
                    <select class="form-control js-example-basic-single" name="attachments" id="attachments">
                        <option value=""></option>
                        <option value="1" >With Attachments Only</option>
                        <option value="2" >Without Attachments Only</option>
                        <option value="3" >All</option>
                    </select>
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
                            <th>@lang('PIV Date')</th>
                             <th>@lang('PIV&nbsp;No')</th>
                             <th>@lang('Supplier')</th>
                             <th>@lang('Customer')</th>
                             
                             <th class="text-right">@lang('Taxable Amount')</th>
                             <th class="text-right">@lang('Tax')</th>
                             <th class="text-right">@lang('Amount')</th>
                             <th>@lang('Deal Id')</th>
                             <th>@lang('Salesman')</th>
                             <th>@lang('Bill No')</th>
                             <th>@lang('Bill Date')</th>
                             <th>@lang('LPO&nbsp;No')</th>
                             <th>@lang('GRN&nbsp;No')</th>
                             <th>@lang('PRT&nbsp;No')</th>
                             <th>@lang('Currency')</th>
                             <th>@lang('Payment')</th>
                             <th>@lang('lang.status')</th>
                             <th>@lang('Att')</th>
                             <th style="width: 100px;" class="text-right">@lang('lang.action')</th>
                         </tr>
                     </thead>
                     <tbody>
                        @php $count =1; $total_taxable_amount=0; $total_tax=0; $total_amount=0; @endphp
                         @foreach($purchaseinvoice as $value)
                         <tr @if (@$value->status == 2) class="bg-dark" @endif>
                             <td>{{date('d/m/Y', strtotime(@$value->pi_date))}}</td>
                             <td><a href="{{url('purchase-invoice/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td>
                                <div id="desc_sup{{ $value->id }}" onmouseover="show_tool_tip('sup'+{{ $value->id }})" onmouseout="hide_tool_tip('sup'+{{ $value->id }})" style="width:120px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->accountname->account_name}}
                                </div></td>
                             <td>
                                <div id="desc_cus{{ $value->id }}" onmouseover="show_tool_tip('cus'+{{ $value->id }})" onmouseout="hide_tool_tip('cus'+{{ $value->id }})" style="width:120px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->reference}}
                                </div></td>

                                <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->total_taxableamount,2,'.',',') }}<?php $total_taxable_amount += $value->total_taxableamount; ?></td>
                                <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->total_vatamount,2,'.',',') }}<?php $total_tax += $value->total_vatamount; ?></td>
                                <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->amount,2,'.',',') }}<?php $total_amount += $value->amount; ?></td>
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
                               <td>{{ $value->salesman_name }}</td>
                                <td><div id="desc_bill{{ $value->id }}" onmouseover="show_tool_tip('bill'+{{ $value->id }})" onmouseout="hide_tool_tip('bill'+{{ $value->id }})" style="width:100px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                    {{ @$value->bill_number }}</div></td>
                                <td>{{date('d/m/Y', strtotime(@$value->bill_date))}}</td>



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
                                @if (empty($value->grn_no))
                                    <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', $value->grn_no) as $grn)
                                        <a href="{{ url('get-url-purchase-grn/' . trim($grn)) }}" target="_blank">{{ trim($grn) }}</a>@if (!$loop->last), @endif
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

                             <td>{{ @$value->currency_name->code }}</td>
                             <td>
                                <?php $count = $adj_list->where('bi_doc_no',$value->doc_number)->count(); ?>
                                @if($count==1)
                                <span class="text-success">Paid</span>
                                @else
                                <span class="text-danger">Pending</span>
                                @endif
                             </td>
                             <td>
                                @if (@$value->return_status == 1)
                                    <span class="text-danger">Returned</span>
                                @elseif(@$value->return_status == 2)
                                <span class="text-warning">Partial Returned</span>
                                @else
                                <span class="text-success">Active</span>
                                @endif
                             </td>
                             <td>
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>
                             <td class="text-right">
                                <a class="btn-sm btn-warning" href="{{url('purchase-invoice/'.$value->id.'/download')}}"><i class="fa fa-download" aria-hidden="true"></i></a>
                                {{--  <a class="btn-sm btn-primary" href="{{url('crm-quote/'.$value->id.'/download')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>  --}}
                                <a class="btn-sm btn-primary" href="{{url('purchase-invoice/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @if (@$value->status == 2)
                                    <a class="btn-sm btn-warning" href="{{url('purchase-invoice/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-danger" href="{{url('purchase-invoice/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif
                             </td>
                         </tr>
                         @endforeach
                     </tbody>
                     <footer>
                        <tr>
                            <th colspan="4"></th>
                            <th class="text-right">{{ @App\SysHelper::com_curr_format($total_taxable_amount,2,'.',',') }}</th>
                            <th class="text-right">{{ @App\SysHelper::com_curr_format($total_tax,2,'.',',') }}</th>
                            <th class="text-right">{{ @App\SysHelper::com_curr_format($total_amount,2,'.',',') }}</th>
                            <th colspan="14"></th>
                        </tr>
                         <tr>
                             <th colspan="21">
                                 {{ $purchaseinvoice->appends(request()->input())->links() }}
                             </th>
                         </tr>
                     </footer>
                </table>
                <script>
                    function show_tool_tip(id){
                        $('#desc_'+id).css('white-space','');
                    }
                    function hide_tool_tip(id){
                        $('#desc_'+id).css('white-space','nowrap');
                    }
                </script>
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
@endsection
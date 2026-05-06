@extends('backEnd.newmasterpage')
@section('mainContent')

<?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    //$permissions = App\SmRolePermission::where('role_id', 8)->get();
?>

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Sales Invoice</h2>
            <span class="page-label">Home - Sales Invoice</span>
        </div>
        <div>
            <a href="{{ url('sales-invoice/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
            <a href="{{ url('sales-invoice') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card shadow mb-4 p-4">
            
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice', 'method' => 'get', 'id' => 'sales-invoice-search']) }}
            <div class="row">

                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Documents Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="documents_number" value="">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Customer</label>
                        <select class="form-control js-account-select" name="customer" id="customer">
                            <option value=""></option>
                            {{-- @foreach ($customer_list as $value)
                                <option value="{{ @$value->id }}" >{{ @$value->account_name }}
                                </option>
                            @endforeach --}}
                        </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="" class="form-check-label">Supplier</label>
                    <input class="form-control" type="text" autocomplete="off" name="supplier" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Deal ID</label>
                    <input class="form-control" type="text" autocomplete="off" name="deal_number" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Delivery Note</label>
                    <input class="form-control" type="text" autocomplete="off" name="delivery_note" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">SRT Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="srt" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Amount</label>
                    <input class="form-control" type="number" autocomplete="off" name="amount" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">From Date</label>
                    <input class="form-control" type="date" autocomplete="off" name="from_date" id="from_date" value="" onchange="set_filter()">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">To Date</label>
                    <input class="form-control" type="date" autocomplete="off" name="to_date" id="to_date" value="" onchange="set_filter()">
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
                    <label for="" class="form-check-label">Attachment</label>
                    <select class="form-control js-example-basic-single" name="attachments" id="attachments">
                        <option value=""></option>
                        <option value="1" >With Attachments Only</option>
                        <option value="2" >Without Attachments Only</option>
                        <option value="3" >All</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Filter By</label>
                    <select class="form-control" name="filter_by" id="filter_by">
                        <option value="" @if($filter_by == "") selected @endif>-Select-</option>
                        <option value="this_month" @if($filter_by == "this_month") selected @endif>This Month</option>
                        <option value="today" @if($filter_by == "today") selected @endif>Today</option>
                        <option value="this_week" @if($filter_by == "this_week") selected @endif>This Week</option>
                        <option value="last_week" @if($filter_by == "last_week") selected @endif>Last Week</option>                                    
                        <option value="last_month" @if($filter_by == "last_month") selected @endif>Last Month</option>
                        <option value="this_quarter" @if($filter_by == "this_quarter") selected @endif>This Quarter</option>
                        <option value="pre_quarter" @if($filter_by == "pre_quarter") selected @endif>Previous Quarter</option>
                        <option value="this_year" @if($filter_by == "this_year") selected @endif>This Year</option>
                        <option value="last_year" @if($filter_by == "last_year") selected @endif>Last Year</option>
                    </select>
                </div>
                <script>
                    function set_filter(){
                    if($('#from_date').val()!="" || $('#to_date').val() != "")
                    {
                        $('#filter_by').val('')
                    }
                    }
                </script>

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
                            <th>@lang('SI Date')</th>
                             <th style="width: 50px;">@lang('SI No')</th>
                             <th>@lang('Customer')</th>
                             <th>@lang('Supplier')</th>
                             
                             <th class="text-right">@lang('Taxable Amount')</th>
                             <th class="text-right">@lang('Tax')</th>
                             <th class="text-right">@lang('Amount')</th>
                             <th>@lang('Deal ID')</th>
                             <th>@lang('Salesman')</th>

                             
                             <th>@lang('LPO Date')</th>
                             <th>@lang('LPO No')</th>
                             <th>@lang('DLN No')</th>
                             <th>@lang('SRT No')</th>
                             <th>@lang('Currency')</th>
                             <th>@lang('Payment')</th>
                             <th>@lang('Att')</th>

                             <th class="text-right" style="width: 75px;">@lang('lang.action')</th>
                         </tr>
                     </thead>
                     <tbody>
                         @php $count =1; $total_taxable_amount=0; $total_tax=0; $total_amount=0; @endphp
                         @foreach($salesinvoice as $value)
                         
                        @if($pending_dn==1)
                        
                        @if (empty($value->dlnno))
                        <tr @if (@$value->status == 2) class="bg-dark" @endif>
                            <td>{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                             <td><a href="{{url('sales-invoice/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td><div id="desc_cus{{ $value->id }}" onmouseover="show_tool_tip('cus'+{{ $value->id }})" onmouseout="hide_tool_tip('cus'+{{ $value->id }})" style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->accountname->account_name}}
                            </div></td>
                             <td>
                                <div id="desc_sup{{ $value->id }}" onmouseover="show_tool_tip('sup'+{{ $value->id }})" onmouseout="hide_tool_tip('sup'+{{ $value->id }})" style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->supplier_name}}
                                </div></td>

                             <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->total_taxableamount-@$value->deal_discount,2,'.',',') }}<?php $total_taxable_amount += $value->total_taxableamount-@$value->deal_discount; ?></td>
                             <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->total_vatamount-(@$value->deal_discount*$value->net_vat/100),2,'.',',') }}<?php $total_tax += $value->total_vatamount-(@$value->deal_discount*$value->net_vat/100); ?></td>
                             <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->amount,2,'.',',') }}<?php $total_amount += $value->amount; ?></td>
                             <td>@if (@$value->code=="") -- @else <a href="{{url('get-url-deal-track/'.$value->code)}}" target="_blank">{{@$value->code}}</a>@endif</td>
                             <td>{{ @$value->salesman->full_name }}</td>

                             
                             <td>{{ @$value->lpo_date }}</td>
                             <td>{{ @$value->lpo_number }}</td>
                             <!-- Delivery Note Numbers -->
                            <td>
                                <span class="text-warning">Pending</span>
                            </td>

                            <!-- Sales Return Numbers -->
                            <td>
                                @if (empty($value->srtno))
                                    <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', $value->srtno) as $srt)
                                        <a href="{{ url('get-url-sales-return/' . trim($srt)) }}" target="_blank">{{ trim($srt) }}</a>@if (!$loop->last), @endif
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
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>

                             <td class="text-right">
                                @if (in_array(session('logged_session_data.company_id') ?? null, [2, 3, 5, 6]))
                                    <a class="btn-sm btn-primary" href="{{ url('sales-invoice/' . $value->id . '/download/t') }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-primary" href="{{ url('sales-invoice/' . $value->id . '/download') }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>
                                @endif
                                <a class="btn-sm btn-info" href="{{url('sales-invoice/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @if (@$value->status == 2)
                                    <a class="btn-sm btn-warning" href="{{url('sales-invoice/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-danger" href="{{url('sales-invoice/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif

                             </td>
                         </tr>
                         @endif

                        @else
                         <tr @if (@$value->status == 2) class="bg-dark" @endif>
                            <td>{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                             <td><a href="{{url('sales-invoice/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td><div id="desc_cus{{ $value->id }}" onmouseover="show_tool_tip('cus'+{{ $value->id }})" onmouseout="hide_tool_tip('cus'+{{ $value->id }})" style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->accountname->account_name}}
                            </div></td>
                             <td>
                                <div id="desc_sup{{ $value->id }}" onmouseover="show_tool_tip('sup'+{{ $value->id }})" onmouseout="hide_tool_tip('sup'+{{ $value->id }})" style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->supplier_name}}
                                </div></td>

                             <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->total_taxableamount-@$value->deal_discount,2,'.',',') }}<?php $total_taxable_amount += $value->total_taxableamount-@$value->deal_discount; ?></td>
                             <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->total_vatamount-(@$value->deal_discount*$value->net_vat/100),2,'.',',') }}<?php $total_tax += $value->total_vatamount-(@$value->deal_discount*$value->net_vat/100); ?></td>
                             <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->amount,2,'.',',') }}<?php $total_amount += $value->amount; ?></td>
                             <td>@if (@$value->code=="") -- @else <a href="{{url('get-url-deal-track/'.$value->code)}}" target="_blank">{{@$value->code}}</a>@endif</td>
                             <td>{{ @$value->salesman->full_name }}</td>

                             
                             <td>{{ @$value->lpo_date }}</td>
                             <td>{{ @$value->lpo_number }}</td>
                             <!-- Delivery Note Numbers -->
                            <td>
                                @if (empty($value->dlnno))
                                <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', $value->dlnno) as $dln)
                                        <a href="{{ url('get-url-delivery-note/' . trim($dln)) }}" target="_blank">{{ trim($dln) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>

                            <!-- Sales Return Numbers -->
                            <td>
                                @if (empty($value->srtno))
                                <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', $value->srtno) as $srt)
                                        <a href="{{ url('get-url-sales-return/' . trim($srt)) }}" target="_blank">{{ trim($srt) }}</a>@if (!$loop->last), @endif
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
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>

                             <td class="text-right">
                                @if (in_array(session('logged_session_data.company_id') ?? null, [2, 3, 5, 6]))
                                    <a class="btn-sm btn-primary" href="{{ url('sales-invoice/' . $value->id . '/download/t') }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-primary" href="{{ url('sales-invoice/' . $value->id . '/download') }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i></a>
                                @endif
                                <a class="btn-sm btn-info" href="{{url('sales-invoice/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @if (@$value->status == 2)
                                    <a class="btn-sm btn-warning" href="{{url('sales-invoice/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-danger" href="{{url('sales-invoice/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif

                             </td>
                         </tr>
                         @endif

                            @endforeach
                        </tbody>
                        <footer>
                            <tr>
                                <th colspan="4"></th>
                                <th class="text-right">{{ @App\SysHelper::com_curr_format($total_taxable_amount,2,'.',',') }}</th>
                                <th class="text-right">{{ @App\SysHelper::com_curr_format($total_tax,2,'.',',') }}</th>
                                <th class="text-right">{{ @App\SysHelper::com_curr_format($total_amount,2,'.',',') }}</th>
                                <th colspan="10"></th>
                            </tr>
                            <tr>
                                <th colspan="17">
                                    {{ $salesinvoice->appends(request()->input())->links() }}
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
                url: '{{ route("autocomplete.get_cust_account_list_ajax") }}',
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

@endsection
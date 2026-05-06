@extends('backEnd.masterpage')
@section('mainContent')

<?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

    <?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Purchase Order</h2>
            <span class="page-label">Home - Purchase Order</span>
        </div>
        <div>
            <a href="{{ url('purchase-order/create') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
            <a href="{{ url('purchase-order') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
        </div>
    </div>
    <div class="collapse" id="collapseExample">
        <div class="card shadow mb-4 p-4">
            
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order', 'method' => 'get', 'id' => 'purchase-order-search']) }}
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
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Deal ID</label>
                    <input class="form-control" type="text" autocomplete="off" name="deal_number" value="">
                </div>
                <div class="col-md-1 mb-2">
                    <label for="" class="form-check-label">Currency</label>
                    <select class="form-control" name="currency" id="currency">
                        <option value=""></option>
                    @foreach ($currency as $value)
                        <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                    @endforeach
                </select>
                </div>

                


                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">GRN Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="grn_number" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Purchase Invoice Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="purchase_invoice_number" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Purchase Return Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="purchase_return_number" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">From Date</label>
                    <input class="form-control" type="date" autocomplete="off" name="from_date" id="from_date" value="">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">To Date</label>
                    <input class="form-control" type="date" autocomplete="off" name="to_date" id="to_date" value="">
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
                        <option value="" @if(@$filter_by == "") selected @endif>-Select-</option>
                        <option value="this_month" @if(@$filter_by == "this_month") selected @endif>This Month</option>
                        <option value="today" @if(@$filter_by == "today") selected @endif>Today</option>
                        <option value="this_week" @if(@$filter_by == "this_week") selected @endif>This Week</option>
                        <option value="last_week" @if(@$filter_by == "last_week") selected @endif>Last Week</option>                                    
                        <option value="last_month" @if(@$filter_by == "last_month") selected @endif>Last Month</option>
                        <option value="this_quarter" @if(@$filter_by == "this_quarter") selected @endif>This Quarter</option>
                        <option value="pre_quarter" @if(@$filter_by == "pre_quarter") selected @endif>Previous Quarter</option>
                        <option value="this_year" @if(@$filter_by == "this_year") selected @endif>This Year</option>
                        <option value="last_year" @if(@$filter_by == "last_year") selected @endif>Last Year</option>
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
                             <th>@lang('PO Date')</th>
                             <th>@lang('PO No')</th>
                             <th>@lang('Supplier')</th>
                             <th>@lang('Customer')</th>
                             <th>@lang('GRN No')</th>
                             <th>@lang('PIV No')</th>
                             <th>@lang('PRT No')</th>
                             <th>@lang('Deal No')</th>
                             <th>@lang('Currency')</th>
                             <th class="text-right">@lang('Amount')</th>
                             <th>@lang('Att')</th>
                             <th class="text-right">@lang('Action')</th>
                         </tr>
                     </thead>
                     <tbody>
                         @php $count =1; @endphp
                         @foreach($purchaseorder as $value)

                         @if($pending_grn==1 || $pending_pi==1 || $pending_pr==1)
                         @if($pending_grn==1 && $value->grn_no =="")
                         <tr @if (@$value->status == 2) class="bg-dark" @endif>
                            <td>{{date('d/m/Y', strtotime(@$value->po_date))}}</td>
                             <td><a href="{{url('purchase-order/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td>{{@$value->accountname->account_name}}</td>
                             <td>{{@$value->narration}}</td>
                             
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
                                @if (empty(@$value->code))
                                    <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', @$value->code) as $code)
                                        <a href="{{ url('get-url-deal-track/' . trim($code)) }}" target="_blank">{{ trim($code) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ @$value->currency_name->code }}</td>
                             <td class="text-right">{{ @App\SysHelper::com_curr_format($value->amount,2,'.',',') }}</td>
                             <td>
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>
                             <td class="text-right">
                                <a class="btn-sm btn-info" href="{{url('purchase-order/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <a class="btn-sm btn-warning" href="{{url('purchase-order/'.$value->id.'/print')}}"><i class="fa fa-download" aria-hidden="true"></i></a>
                                {{--  <a class="btn-sm btn-warning" href="{{url('purchase-order/'.$value->id.'/printexcel')}}"><i class="fa fa-table" aria-hidden="true"></i></a>  --}}
                                @if (@$value->status == 2)
                                    <a class="btn-sm btn-warning" href="{{url('purchase-order/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-danger" href="{{url('purchase-order/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif
                             </td>
                         </tr>
                         @endif
                         @if($pending_pi==1 && $value->piv_no =="")
                         <tr @if (@$value->status == 2) class="bg-dark" @endif>
                            <td>{{date('d/m/Y', strtotime(@$value->po_date))}}</td>
                             <td><a href="{{url('purchase-order/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td>{{@$value->accountname->account_name}}</td>
                             <td>{{@$value->narration}}</td>                             
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
                                @if (empty(@$value->code))
                                    <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', @$value->code) as $code)
                                        <a href="{{ url('get-url-deal-track/' . trim(@$code)) }}" target="_blank">{{ trim(@$code) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ @$value->currency_name->code }}</td>
                             <td class="text-right">{{ @App\SysHelper::com_curr_format($value->amount,2,'.',',') }}</td>
                             <td>
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>
                             <td class="text-right">
                                <a class="btn-sm btn-info" href="{{url('purchase-order/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <a class="btn-sm btn-warning" href="{{url('purchase-order/'.$value->id.'/print')}}"><i class="fa fa-download" aria-hidden="true"></i></a>
                                {{--  <a class="btn-sm btn-warning" href="{{url('purchase-order/'.$value->id.'/printexcel')}}"><i class="fa fa-table" aria-hidden="true"></i></a>  --}}
                                @if (@$value->status == 2)
                                    <a class="btn-sm btn-warning" href="{{url('purchase-order/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-danger" href="{{url('purchase-order/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif
                             </td>
                         </tr>
                         @endif
                         @if($pending_pr==1 && $value->prt_no =="")
                         <tr @if (@$value->status == 2) class="bg-dark" @endif>
                            <td>{{date('d/m/Y', strtotime(@$value->po_date))}}</td>
                             <td><a href="{{url('purchase-order/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td>{{@$value->accountname->account_name}}</td>
                             <td>{{@$value->narration}}</td>
                             
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
                                @if (empty(@$value->code))
                                    <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', @$value->code) as $code)
                                        <a href="{{ url('get-url-deal-track/' . trim(@$code)) }}" target="_blank">{{ trim(@$code) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ @$value->currency_name->code }}</td>
                             <td class="text-right">{{ @App\SysHelper::com_curr_format($value->amount,2,'.',',') }}</td>
                             <td>
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>
                             <td class="text-right">
                                <a class="btn-sm btn-info" href="{{url('purchase-order/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <a class="btn-sm btn-warning" href="{{url('purchase-order/'.$value->id.'/print')}}"><i class="fa fa-download" aria-hidden="true"></i></a>
                                {{--  <a class="btn-sm btn-warning" href="{{url('purchase-order/'.$value->id.'/printexcel')}}"><i class="fa fa-table" aria-hidden="true"></i></a>  --}}
                                @if (@$value->status == 2)
                                    <a class="btn-sm btn-warning" href="{{url('purchase-order/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-danger" href="{{url('purchase-order/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif
                             </td>
                         </tr>
                         @endif

                         @else
                         <tr @if (@$value->status == 2) class="bg-dark" @endif>
                            <td>{{date('d/m/Y', strtotime(@$value->po_date))}}</td>
                             <td><a href="{{url('purchase-order/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td>{{@$value->accountname->account_name}}</td>
                             <td>{{@$value->narration}}</td>
                             
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
                                @if (empty(@$value->code))
                                    <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', @$value->code) as $code)
                                        <a href="{{ url('get-url-deal-track/' . trim(@$code)) }}" target="_blank">{{ trim(@$code) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>
                             <td>{{ @$value->currency_name->code }}</td>
                             <td class="text-right">{{ @App\SysHelper::com_curr_format($value->amount,2,'.',',') }}</td>
                             <td>
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>
                             <td class="text-right">
                                <a class="btn-sm btn-info" href="{{url('purchase-order/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                <a class="btn-sm btn-warning" href="{{url('purchase-order/'.$value->id.'/print')}}"><i class="fa fa-download" aria-hidden="true"></i></a>
                                {{--  <a class="btn-sm btn-warning" href="{{url('purchase-order/'.$value->id.'/printexcel')}}"><i class="fa fa-table" aria-hidden="true"></i></a>  --}}
                                @if (@$value->status == 2)
                                    <a class="btn-sm btn-warning" href="{{url('purchase-order/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-danger" href="{{url('purchase-order/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif
                             </td>
                         </tr>
                         @endif

                         @endforeach
                     </tbody>
                     <footer>
                         <tr>
                             <td colspan="14">
                                 {{ $purchaseorder->appends(request()->input())->links() }}
                             </td>
                         </tr>
                     </footer>
                </table>
            </div>
        </div>
    </div>

</div>
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
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
@endsection
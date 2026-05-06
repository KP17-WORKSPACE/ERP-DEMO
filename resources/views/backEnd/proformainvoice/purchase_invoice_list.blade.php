@extends('backEnd.masterpage')
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
                        <select class="form-control js-example-basic-single" name="supplier" id="supplier">
                            <option value=""></option>
                            @foreach ($supplier_list as $value)
                                <option value="{{ @$value->id }}" >{{ @$value->account_name }}
                                </option>
                            @endforeach
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
                            <option value="{{ @$value->full_name }}" >{{ @$value->full_name }}
                            </option>
                        @endforeach
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
                             <th>@lang('PIV No')</th>
                             <th>@lang('Supplier')</th>
                             <th>@lang('Customer')</th>
                             
                             <th class="text-right">@lang('Taxable Amount')</th>
                             <th class="text-right">@lang('Tax')</th>
                             <th class="text-right">@lang('Amount')</th>
                             <th>@lang('Deal Id')</th>
                             <th>@lang('Salesman')</th>
                             <th>@lang('LPO Date')</th>
                             <th>@lang('LPO No')</th>
                             
                             <th>@lang('PO No')</th>
                             <th>@lang('GRN No')</th>
                             <th>@lang('PRT No')</th>
                             <th>@lang('Currency')</th>
                             <th>@lang('Payment')</th>
                             <th>@lang('lang.status')</th>
                             <th class="text-right">@lang('lang.action')</th>
                         </tr>
                     </thead>
                     <tbody>
                        @php $count =1; $total_taxable_amount=0; $total_tax=0; $total_amount=0; @endphp
                         @foreach($purchaseinvoice as $value)
                         <tr @if (@$value->status == 2) class="bg-dark" @endif>
                             <td>{{date('d/m/Y', strtotime(@$value->pi_date))}}</td>
                             <td><a href="{{url('purchase-invoice/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td>
                                <div id="desc_sup{{ $value->id }}" onmouseover="show_tool_tip('sup'+{{ $value->id }})" onmouseout="hide_tool_tip('sup'+{{ $value->id }})" style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->accountname->account_name}}
                                </div></td>
                             <td>
                                <div id="desc_cus{{ $value->id }}" onmouseover="show_tool_tip('cus'+{{ $value->id }})" onmouseout="hide_tool_tip('cus'+{{ $value->id }})" style="width:150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{@$value->reference}}
                                </div></td>

                                <td class="text-right">{{ number_format(@$value->total_taxableamount,2,'.',',') }}<?php $total_taxable_amount += $value->total_taxableamount; ?></td>
                                <td class="text-right">{{ number_format(@$value->total_vatamount,2,'.',',') }}<?php $total_tax += $value->total_vatamount; ?></td>
                                <td class="text-right">{{ number_format(@$value->amount,2,'.',',') }}<?php $total_amount += $value->amount; ?></td>
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
                                <td>{{date('d/m/Y', strtotime(@$value->lpo_date))}}</td>
                                <td>{{ $value->lpo_number }}</td>



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
                             <td>@if ($value->grn_no == "") <span class="text-warning">Pending</span> @else <a href="{{url('get-url-purchase-grn/'.$value->grn_no)}}" target="_blank">{{@$value->grn_no}}</a> @endif</td>
                             <td>@if ($value->prt_no == "") <span class="text-warning">Pending</span> @else <a href="{{url('get-url-purchase-return/'.$value->prt_no)}}" target="_blank">{{@$value->prt_no}}</a> @endif</td>

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
                            <th class="text-right">{{ number_format($total_taxable_amount,2,'.',',') }}</th>
                            <th class="text-right">{{ number_format($total_tax,2,'.',',') }}</th>
                            <th class="text-right">{{ number_format($total_amount,2,'.',',') }}</th>
                            <th colspan="11"></th>
                        </tr>
                         <tr>
                             <th colspan="18">
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
@endsection
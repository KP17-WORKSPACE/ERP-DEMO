@extends('backEnd.masterpage')
@section('mainContent')

<?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Stock Register</h2>
            <span class="page-label">Home - Stock Register</span>
        </div>
        <div>
            <a href="{{ url('list-price') }}" type="button" class="btn btn-primary"><i class="fa fa-list"></i> List Price</a>
            <a href="{{ url('item-store') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Add Stock</a>
        </div>
    </div>
    <div class="card p-4 mb-2">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-register', 'method' => 'POST', 'id' => 'stock-register-search']) }}
            <div class="row">
                <div class="col-md-2 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label>@lang('To Date')</label>
                                <input class="form-control" id="to_date" type="date" name="to_date" value="{{ @$to_date }}" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                </div>  
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Part Number</label>
                    <input class="form-control" type="text" autocomplete="off" name="part_number" value="{{ $r_part_number }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Brand</label>
                    <select class="form-control js-example-basic-single" name="brand">
                        <option value="">-Select-</option>
                        @foreach ($brand as $value)
                        <option value="{{ @$value->id }}" @if($r_brand == $value->id) selected @endif>{{ @$value->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Category</label>
                    <select class="form-control js-example-basic-single" name="category">
                        <option value="">-Select-</option>
                        @foreach ($category as $value)
                        <option value="{{ @$value->id }}" @if($r_category == $value->id) selected @endif>{{ @$value->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="" class="form-check-label">Sub Category</label>
                    <select class="form-control js-example-basic-single" name="sub_category">
                        <option value="">-Select-</option>
                        @foreach ($sub_category as $value)
                        <option value="{{ @$value->id }}" @if($r_sub_category == $value->id) selected @endif>{{ @$value->sub_category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 mb-2">
                    <label for="" class="form-check-label">Qty</label>
                    <select class="form-control js-example-basic-single" name="qty">
                        <option value="">-Select-</option>
                        <option value="positive" @if($r_qty == "positive") selected @endif>Positive</option>
                        <option value="negative" @if($r_qty == "negative") selected @endif>Negative</option>
                        <option value="zero" @if($r_qty == "zero") selected @endif>Zero</option>
                    </select>
                </div>
                <div class="col-md-1 mb-2">&nbsp;<br />
                    <button type="submit" class="btn btn-primary mt-1" id="btnSubmit">Search</button>
                </div>
            </div>
            {{ Form::close() }}
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>                        
                        <tr>
                            <th>@lang('Part Number')</th>
                            <th>@lang('Description')</th>
                            <th>@lang('Brand')</th>
                            <th>@lang('Category')</th>
                            <th>@lang('Bal Qty')</th>
                            @if($show_all == 1)
                                <th class="text-right">@lang('Avg Rate')</th>
                                <th class="text-right">@lang('Amount')</th>
                            @else
                                @if(count($show_brand)>0)
                                    <th class="text-right">@lang('Avg Rate')</th>
                                    <th class="text-right">@lang('Amount')</th>
                                @endif
                            @endif
                            <th class="text-right">@lang('Group Qty')</th>
                        </tr>
                    </thead>
    
                    <tbody>
                        
                        @php $count =1; $total_qty=0; $total_price=0; $total_value=0; $total_amount=0; @endphp

                            <?php 
                            if($r_qty == "zero") { $stocklist2 = $stocklist->where('balance_qty',0); }
                            else if($r_qty == "positive") { $stocklist2 = $stocklist->where('balance_qty','>',0); }
                            else if($r_qty == "negative") { $stocklist2 = $stocklist->where('balance_qty','<',0); }
                            else { $stocklist2 = $stocklist; }
                            ?>

                                @foreach($stocklist2 as $value)
                                <?php 
                                    $group_qty = App\SysHelper::get_group_qty($value->partno);
                                ?>
                                @if(($group_qty !=0 && $value->type==2) || $value->type==1)
                                <tr>
                                    <td>
                                        @if ($show_all == 1)
                                            <a href="{{ url('stock-ledger/'.$value->part_number) }}" target="_blank">{{@$value->part_number}}</a>
                                        @else
                                            {{@$value->part_number}}
                                        @endif
                                    </td>
                                    <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{$value->description}}</div></td>
                                    <td>{{$value->brand}}</td>
                                    <td>{{$value->categoryname}} - {{$value->subcategoryname}}</td>

                                    @php
                                    $balance_qty = $value->balance_qty;
                                    $balance_qty += $stocklist_return->where('partno',$value->partno)->sum('qty');
                                    
                                    @endphp
                                    
                                    <td>{{$balance_qty}}</td>



                                    @if($show_all == 1)                                    
                                        <?php  $avg = App\SysHelper::get_avg_price($value->partno,$to_date); ?>
                                        <td class="text-right">{{@App\SysHelper::com_curr_format($avg, 2, '.', ',')}}</td>                                        
                                        <td class="text-right">
                                            @if ($balance_qty > 0)
                                                {{@App\SysHelper::com_curr_format(($avg * $balance_qty), 2, '.', ',')}}
                                                {{--  {{@App\SysHelper::com_curr_format(($value->avg_price * $balance_qty), 2, '.', ',')}}  --}}
                                            @else
                                                {{@App\SysHelper::com_curr_format(($avg * 0), 2, '.', ',')}}
                                                {{--  {{@App\SysHelper::com_curr_format(($value->avg_price * 0), 2, '.', ',')}}  --}}
                                            @endif                                            
                                        </td>
                                        
                                        
                                    @php
                                    $total_price += $avg;
                                    if($balance_qty > 0){
                                        $total_amount += ($avg * $balance_qty);
                                    }
                                    @endphp


                                    @else
                                        @if(count($show_brand)>0)
                                            @if(in_array($value->brandid,$show_brand))
                                                <?php  $avg = App\SysHelper::get_avg_price($value->partno,$to_date); ?>
                                                <td class="text-right">{{@App\SysHelper::com_curr_format($avg, 2, '.', ',')}}</td>                                        
                                                <td class="text-right">
                                                    @if ($balance_qty > 0)
                                                        {{@App\SysHelper::com_curr_format(($avg * $balance_qty), 2, '.', ',')}}
                                                        {{--  {{@App\SysHelper::com_curr_format(($value->avg_price * $balance_qty), 2, '.', ',')}}  --}}
                                                    @else
                                                        {{@App\SysHelper::com_curr_format(($avg * 0), 2, '.', ',')}}
                                                        {{--  {{@App\SysHelper::com_curr_format(($value->avg_price * 0), 2, '.', ',')}}  --}}
                                                    @endif
                                                    
                                    @php
                                    $total_price += $avg;
                                    if($balance_qty > 0){
                                        $total_amount += ($avg * $balance_qty);
                                    }
                                    @endphp

                                                </td>
                                            @else
                                                <td class="text-right">0</td>
                                                <td class="text-right">0</td>
                                            @endif
                                        @endif
                                        
                                    @endif

                                    @php 
                                    $total_qty += $balance_qty; @endphp

                                    

                                    
                                    <td class="text-center"><a style="cursor: pointer;" onclick="group_qty({{ $value->partno }},'{{ $value->part_number }}')">{{ $group_qty }}</a></td>
                                </tr>
                                @endif

                                @endforeach                            
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>{{ $total_qty }}</th>
                            @if($show_all == 1)
                            <th class="text-right"></th>
                            <th class="text-right">{{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}</th>
                            @else
                                @if(count($show_brand)>0)
                                <th class="text-right"></th>
                                <th class="text-right">{{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}</th>
                                @endif                            
                            @endif
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
                <div class="d-flex justify-content-center">
        {{ $stocklist->links() }}
    </div>
            </div>
        </div>
    </div>

    <script>
        function group_qty(pid, pname)
        {
            $('#lbl_group_qty').text(pname);
            
            $("#loading_bg").css("display", "block");
            var partno = pid;
            var action = "{{ URL::to('get-stock-register-group-qty') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    partno: partno,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                var com = dataResult['data'][i].company_id;
                                var qty = dataResult['data'][i].balance_qty;
                                var value = formatAmount(dataResult['data'][i].avg_price);
                                if(dataResult['data'][i].avg_price == 0 || qty == 0){
                                    var rate = '0.00';
                                }
                                else {
                                    var rate = Math.abs(formatAmount(dataResult['data'][i].avg_price/qty));
                                }
                                $("#qty_"+com).text(qty);
                                $("#rate_"+com).text(rate);
                                $("#value_"+com).text(value);
                            }
                        }
                        else{

                        }
                        $("#loading_bg").css("display", "none");
                }
            });
            $('#BtnGroupQty').click();
        }
    </script>

    
<a id="BtnGroupQty" data-toggle="modal" data-target="#ModalGroupQty"></a>
<div class="modal fade" id="ModalGroupQty" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Group Qty - <label id="lbl_group_qty"></label></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                        <tr>
                            <th>Company</th>
                            <th class="text-center">Qty</th>
                            @if($show_all == 1)
                                <th class="text-right">Rate</th>
                                <th class="text-right">Value</th>
                            @endif
                        </tr>
                        @if(count($company_list)>0)
                        @foreach($company_list as $list)
                        <tr>
                            <td>{{ $list->company_name }}</td>
                            <td class="text-center"><label id="qty_{{ $list->id }}">0</label></td>
                            @if($show_all == 1)
                            <td class="text-right"><label id="rate_{{ $list->id }}">0.00</label></td>
                            <td class="text-right"><label id="value_{{ $list->id }}">0.00</label></td>
                            @endif
                        </tr>
                        @endforeach
                        @endif
                    </table>                    
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

      </div>
    </div>
  </div>

@endsection
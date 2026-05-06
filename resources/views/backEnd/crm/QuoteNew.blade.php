@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <?php try { ?>

        <div class="container-fluid mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="mb-3">
                    <h2 class="page-heading m-0">Create Quote</h2>
                    <span class="page-label">Home - Deal - Create Quote</span>
                </div>
                <div>
                    {{--  <a href="{{ url('crm-deals/'.$quotation->id.'/view') }}" type="button" class="btn btn-primary"><i class="fa fa-list"></i> View Deal {{ $quotation->id }}</a>  --}}
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title m-0 pb-0">Products</h4>
                        
                            <a href="{{ url('quote/searchitems') }}" class="btn btn-danger btn-xs text-white mb-2" style="cursor: pointer; float: right;"><i class="fa fa-plus" aria-hidden="true"></i> Add Product</a>
                <?php $t_qty=0; $t_price=0; $t_discount=0; $tt_price=0; ?>
                <table class="table table-bordered table-striped"  width="100%" cellspacing="0">
                
                <thead>
                    <tr>
                        <th style="width: 10%;">@lang('Part Number')</th>
                        <th style="width: 40%;">@lang('Description')</th>
                        <th style="width: 10%;">@lang('Qty')</th>
                        <th style="width: 10%;">@lang('Unit Price')</th>
                        <th style="width: 10%;">@lang('Unit Discount')</th>
                        <th style="width: 10%;">@lang('Total Amount')</th>
                        <th style="width: 10%;"></th>
                    </tr>
                </thead>
                @if(!isset($cart_items))
                <tbody>
                    <tr style="line-height: 35px;">
                        <td colspan="7" style="text-align: center;"><br /><br />
                            <a href="{{ url('quote/searchitems') }}" class="btn btn-danger btn-md text-white mb-2" style="cursor: pointer;"><i class="fa fa-plus" aria-hidden="true"></i> Add Products</a>    
                            <br /><br /><br />
                        </td>
                    </tr>
                </tbody>
                @endif
                @if(isset($cart_items))
                <tbody>
                @foreach ($cart_items as $Item)
                    <tr style="line-height: 35px;">
                        <td>{{ $Item->part_number }}</td>
                        <td><textarea type="text" class="form-control" id="txt_udescription_{{$Item->id}}">{!! nl2br($Item->description) !!}</textarea></td>
                        <td><input type="number" class="form-control" id="txt_uqty_{{$Item->id}}" value="{{$Item->qty}}"></td>
                        <td><input type="number" class="form-control" id="txt_uprice_{{$Item->id}}" value="{{ App\SysHelper::currancy_format_cart(($Item->price))}}"></td>
                        <td><input type="number" class="form-control" id="txt_udiscount_{{$Item->id}}" value="{{ App\SysHelper::currancy_format_cart(($Item->discount))}}"></td>
                        <td><input type="number" class="form-control" id="txt_utotal_price_{{$Item->id}}" value="{{ App\SysHelper::currancy_format_cart((($Item->price * $Item->qty) - ($Item->discount * $Item->qty)))}}" readonly></td>
                        
                        {{--  <td>{!! nl2br($Item->description) !!}</td>
                        <td>{{ $Item->qty }}</td>
                        <td>{{ $Item->price }} {{ $currancy->code }}</td>
                        <td>{{ $Item->discount }} {{ $currancy->code }}</td>
                        <td>{{ @App\SysHelper::com_curr_format(($Item->price * $Item->qty), 2, '.', '')}} {{ $currancy->code }}</td>  --}}

                        <td style="text-align: right;">                            
                            <button class="btn btn-warning" title="Update" id="txt_btn_upd_{{$Item->id}}" onclick="upd_tocart({{$Item->id}})">Update</button>

                            <button class="btn btn-danger" title="Delete" id="txt_btn_{{$Item->id}}" onclick="del_tocart({{$Item->id}})">Delete</button></td>
                    </tr>
                    <?php $t_qty += $Item->qty; $t_price += $Item->price; $t_discount += $Item->discount; $tt_price += $Item->price * $Item->qty; ?>
                @endforeach
            </tbody>
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>{{ $t_qty }}</th>
                        <th>{{ App\SysHelper::currancy_format_cart($t_price) }} {{ $currancy->code }}</th>
                        <th>{{ App\SysHelper::currancy_format_cart($t_discount) }} {{ $currancy->code }}</th>
                        <th>{{ App\SysHelper::currancy_format_cart($tt_price) }} {{ $currancy->code }}</th>
                        <th></th>
                    </tr>
                </thead>
                @endif
                </table>
                @if(isset($cart_items))
                    {{ Form::open(['route' => 'quote.generatequote', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) }}
                        <button class="btn btn-success mt-1 float-right">Save Quote</button>
                    {{ Form::close() }}
                @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    

    <script>

        function add_tocart(id) {
            $("#loading_bg").css("display", "block");
            var qty = $("#txt_qty_"+id).val();
            var price = $("#txt_price_"+id).val();
            var description = $("#txt_description_"+id).val();

            if (qty == "" || qty <= 0) {
                alert("Please Add Qty");
                $("#txt_qty_"+id).focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            $("#txt_btn_"+id).attr('disabled', true);
    
            var action = "{{ URL::to('crm-quote-additems') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    qty: qty,
                    price: price,
                    description: description,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }

        function upd_tocart(id) {
            $("#loading_bg").css("display", "block");
            var qty = $("#txt_uqty_"+id).val();
            var price = $("#txt_uprice_"+id).val();
            var description = $("#txt_udescription_"+id).val();
            var discount = $("#txt_udiscount_"+id).val();

            if (qty == "" || qty <= 0) {
                alert("Please Add Qty");
                $("#txt_qty_"+id).focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            $("#txt_btn_"+id).attr('disabled', true);
    
            var action = "{{ URL::to('crm-quote-updateitems') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    qty: qty,
                    price: price,
                    description: description,
                    discount: discount,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }

        function del_tocart(id) {
            $("#loading_bg").css("display", "block");
            var btn = $("#del_btn_"+id).val();
            $(btn).attr('disabled', true);
    
            var action = "{{ URL::to('crm-quote-deleteitems') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }

    </script>
@endsection
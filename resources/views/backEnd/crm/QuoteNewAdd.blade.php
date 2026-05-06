@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <?php try { ?>

        <div style="width: 90vw; height:90vh; overflow: hidden; background: #00000049; position: absolute; z-index: 999;"></div>

        <div class="modal-dialog modal-lg"
        role="document"style="width: 1000px; max-height: 80vh; overflow: hidden; position: absolute; z-index: 9999; left:0; right:0; margin-left: auto; margin-right: auto; border-radius: 5px; margin-top: 10px; background: #ffffff;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Items</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <a aria-hidden="true" href="{{ url('quote/chooseitems') }}">×</a>
                </button>
            </div>
            <div class="modal-body">
                {{ Form::open(['route' => 'quote.searchitems', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) }}
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="" class="form-label">Part Number</label>
                            <input class="form-control" placeholder="Part Number / Product Name" name="part_number"
                                autocomplete="off" id="part_number" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-sm btn-dark fix-gr-bg pt-1 pb-1 pl-3 pr-3 mt-4"
                                id="btnSubmit"><i class="fa fa-search" aria-hidden="true"></i> Find</button>
                            <a class="btn text-info" style="float: right;" data-toggle="modal"
                                data-target="#exampleModalCenter"><i class="fa fa-plus" aria-hidden="true"></i> Add New
                                Product</a>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}

                @if (isset($product))
                    <div class="row">
                        <div class="col-md-12 max-height">
                            @if (count($product) > 0)
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-addbulkitems', 'method' => 'POST', 'id' => 'crm-deals-form']) }}
                            @endif

                            @if (isset($product))
                                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width:5px;"></th>
                                            <th style="width:120px;">@lang('Part Number')</th>
                                            <th>@lang('Description')</th>
                                            <th style="width:100px;">@lang('Unit Price')</th>
                                            <th style="width:70px;">@lang('Qty')</th>
                                            <th style="width: 30px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($product as $Item)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="add[]" value="0">
                                                    <input type="checkbox" id="a" class="checkbox "
                                                        name="add[]" value="1" />
                                                </td>
                                                <td>
                                                    <span class="text-info">{{ $Item->part_number }}</span>
                                                </td>
                                                <td>
                                                    <textarea type="text" class="form-control" id="txt_description_{{ $Item->id }}" name="b_description[]">{!! $Item->description !!}</textarea>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        id="txt_price_{{ $Item->id }}" name="b_price[]"
                                                        value="{{ $Item->price }}">
                                                </td>
                                                <td>
                                                    <input type="hidden" name="pid[]" value="{{ $Item->id }}">
                                                    <input type="number" class="form-control" name="b_qty[]"
                                                        id="txt_qty_{{ $Item->id }}" />
                                                </td>
                                                <td width="30px">
                                                    <button class="btn btn-warning" id="txt_btn_{{ $Item->id }}"
                                                        onclick="add_tocart({{ $Item->id }})">Add</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                        </div>


                        <div class="col-md-12">
                            <div class="modal-footer">
                                @if (count($product) > 1)
                                    <button type="submit" class="btn btn-sm btn-dark" id="btnSubmit"><i
                                            class="fa fa-cart-plus" aria-hidden="true"></i> Add Selected Items</button>
                                @endif
                            </div>
                        </div>

                    </div>
                    {{ Form::close() }}
                @endif
                @endif
            </div>
        </div>
    </div>

    <div class="container-fluid mb-4" style="height: 90vh; overflow: hidden;">
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



<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="exampleModalLongTitle">Add New Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-addnewproduct', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <div class="modal-body">

                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <label class="txtlbl">Part Number<span>*</span> </label>
                        <input class="txtbx primary-input dynamicstxt_s w-100 form-control" type="text"
                            name="part_number" autocomplete="off" required>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <label class="txtlbl">Brand<span>*</span> </label>
                        <select class="txtbx niceSelect dynamicstxt_s w-100 bb form-control" name="brand"
                            id="brand" required>
                            <option value=""></option>
                            @foreach ($brands as $key => $value)
                                <option value="{{ @$value->id }}">{{ @$value->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <label class="txtlbl">Category<span>*</span> </label>
                        <select class="txtbx niceSelect dynamicstxt_s w-100 bb form-control" name="category_name"
                            id="category_name">
                            <option value=""></option>
                            @foreach ($itemCategories as $key => $value)
                                <option value="{{ @$value->id }}">{{ @$value->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6 mb-4" id="sectionSubcategoryDiv">
                        <label class="txtlbl">Sub Category<span>*</span> </label>
                        <select class="txtbx niceSelect dynamicstxt_s w-100 bb form-control" name="subcategory_name"
                            id="sectionSelectSubcategory">
                            <option value=""></option>
                            @foreach ($SuCategories as $key => $value)
                                <option value="{{ @$value->id }}">{{ @$value->sub_category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-12 mb-4">
                        <label class="txtlbl">Description<span>*</span> </label>
                        <textarea class="txtbx primary-input dynamicstxt_s w-100 form-control" cols="0" rows="2"
                            name="description" id="description" required></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>


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
                        alert("Item Added");
                        var url = "{{ URL::to('quote/chooseitems') }}";
                        window.location.replace(url);
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
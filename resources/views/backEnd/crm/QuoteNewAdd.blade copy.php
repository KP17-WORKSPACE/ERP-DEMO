@extends('backEnd.master')
@section('mainContent')
    @php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] =
    @$permission->moduleLink->module_id;}
    $modules = array_unique(@$modules);
    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol;
    
    if(isset($generalSetting->logo)){ @$logo = @$generalSetting->logo; }
    else{ $logo = 'public/uploads/settings/logo.png'; }

    $sm_staff= App\SmStaff::where('user_id',Auth::user()->id)->first();
    if(!empty(@$sm_staff)){
        @$profile_image = @$sm_staff->staff_photo;
        if(empty(@$profile_image)){
            @$profile_image ='public/uploads/staff/staff1.jpg';
        }
    }
    @endphp


<style>
    .leadbox{    
    border: solid 1px #dbdce1; border-radius: 5px; background: #ffffff; padding: 5px 5px 10px 15px; margin-right: 15px;
}
</style>

<div style="width: 90vw; height:100vh; background: #000000bf; position: absolute; z-index: 9;"></div>    
<section class="sms-breadcrumb mb-20 white-box">
    <div class="container-fluid">
        <div class="row" style="float: left;">
            <h1>@lang('Quote')</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ url('crm-dashboard') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> CRM Dashboard</a>
            <a href="{{ url('crm-deals') }}" class="top-btn-r"><i class="far fa fa-plus" aria-hidden="true"></i> New</a>
            <a href="{{ url('crm-deals/show') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View</a>
            <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh" aria-hidden="true"></i> Refresh</a>
        </div>
    </div>
</section>
<hr style="margin-top: 33px;" />
<div style="clear: both;"></div>
<div class="col-lg-12 text-right">
    @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
        @if (session()->has('message-success'))
            <p class="text-success">
                {{ session()->get('message-success') }}
            </p>
        @elseif(session()->has('message-danger'))
            <p class="text-danger">
                {{ session()->get('message-danger') }}
            </p>
        @endif
    @endif
</div>

    <section class="admin-visitor-area">
        <div class="row ml-2 mr-2">
            <div class="col-lg-12">
                <div class="white-box" style="width: 1000px; max-height: 80vh; overflow: hidden; overflow-y: scroll; position: absolute; z-index: 9999; left:0; right:0; margin-left: auto; margin-right: auto; border-radius: 5px; background: #ffffff;">
                    
                    <a href="{{ url('quote/chooseitems') }}" class="btn btn-danger btn-xs text-white ml-2 pl-2 pr-2" style="cursor: pointer; float: right;"><i class="fa fa-times" aria-hidden="true"></i></a>
                    <a class="btn btn-info btn-xs text-white" style="cursor: pointer; float: right;" data-toggle="modal" data-target="#exampleModalCenter"><i class="fa fa-plus" aria-hidden="true"></i> Add New Product</a>

                    {{ Form::open(['route' => 'quote.searchitems', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) }}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-effect">
                                <label class="txtlbl">Part Number / Product Name</label>
                                <input class="primary-input dynamicstxt_s w-100 form-control" name="part_number" autocomplete="off" id="part_number" required />
                            </div>
                        </div>
                        <div class="col-lg-6"><br />
                            <div class="input-effect">
                                <button type="submit" class="btn btn-dark btn-sm mt-2" id="btnSubmit"><i class="fa fa-search" aria-hidden="true"></i> Find</button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                    @if(isset($product))
                                <br />
                                @if(count($product)>1)
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-addbulkitems', 'method' => 'POST', 'id' => 'crm-deals-form']) }}
                                @endif
                                @foreach ($product as $Item)
                                <div class="white-box leadbox bg-white mb-1">
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <input type="hidden" name="add[]" value="0">
                                            <input type="checkbox" id="a" class="checkbox" name="add[]" value="1" />
                                            <span class="text-info">{{ $Item->part_number }}</span></div>
                                        <div class="col-lg-6">
                                            <textarea class="txtarea w-100" style="border:solid 1px #ededef;" id="txt_description_{{$Item->id}}" name="b_description[]">{!! $Item->description !!}</textarea>
                                            {{--  <span class="text-dark">{{ $Item->description }}</span><br />  --}}
                                             {{--  | Price : <span class="text-danger">{{ $Item->price }} {{ $currancy->code }}</span>  --}}
                                        </div>
                                        <div class="col-lg-2">Price
                                            <input type="text" class="primary-input dynamicstxt w-100 form-control" id="txt_price_{{$Item->id}}" name="b_price[]" value="{{$Item->price}}" style="height: 27px !important;" >
                                        </div>
                                        <div class="col-lg-1">Qty
                                            <input type="hidden" name="pid[]" value="{{$Item->id}}">
                                            <input type="number" class="primary-input dynamicstxt w-100 form-control" name="b_qty[]" id="txt_qty_{{$Item->id}}" style="height: 27px !important;" />
                                        </div>
                                        <div class="col-lg-1"><br />
                                            <button class="btn btn-warning btn-xs" id="txt_btn_{{$Item->id}}" onclick="add_tocart({{$Item->id}})"> Add <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                        </div>
                                    </div>                            
                                </div>
        
                                @endforeach
                                
                                @if(count($product)>1)
                                <button type="submit" class="primary-btn fix-gr-bg" id="btnSubmit"><i class="fa fa-cart-plus" aria-hidden="true"></i> Add Selected Items</button>
                                {{ Form::close() }}
                                @endif
        
                                @endif



                </div>
                <a class="btn btn-danger btn-xs text-white mb-2" style="cursor: pointer; float: right;" data-toggle="modal" data-target="#modalAddProduct"><i class="fa fa-plus" aria-hidden="true"></i> Add Product</a>
                <?php $t_qty=0; $t_price=0; ?>
                <?php $t_qty=0; $t_price=0; $tt_price=0; ?>
                <table id="table_custom" class="display school-table" cellspacing="0" width="100%">
                @if(isset($cart_items))
                <thead>
                    <tr>
                        <th>@lang('Part Number')</th>
                        <th>@lang('Description')</th>
                        <th>@lang('Qty')</th>
                        <th>@lang('Unit Price')</th>
                        {{--  <th>@lang('Unit Discount')</th>  --}}
                        <th>@lang('Total Amount')</th>
                        <th></th>
                    </tr>
                </thead>
                @foreach ($cart_items as $Item)
                <tbody>
                    <tr style="line-height: 35px;">
                        <td>{{ $Item->part_number }}</td>
                        <td>{!! nl2br($Item->description) !!}</td>
                        <td>{{ $Item->qty }}</td>
                        <td>{{ App\SysHelper::currancy_format_cart($Item->price) }} {{ $currancy->code }}</td>
                        {{--  <td>{{ $Item->discount }} {{ $currancy->code }}</td>  --}}
                        <td>{{ App\SysHelper::currancy_format_cart(($Item->price * $Item->qty))}} {{ $currancy->code }}</td>
                        <td><button class="btn btn-danger btn-xs" title="Delete" id="txt_btn_{{$Item->id}}" onclick="del_tocart({{$Item->id}})" style="float: right; font-size: 8px; padding: 1px 3px;">&nbsp;<i class="fa fa-times" aria-hidden="true"></i>&nbsp;</button></td>
                    </tr>
                    <?php $t_qty += $Item->qty; $t_price += $Item->price; $tt_price += $Item->price * $Item->qty; ?>
                </tbody>                
                @endforeach
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>{{ $t_qty }}</th>
                        <th>{{ $t_price }} {{ $currancy->code }}</th>
                        {{--  <th>@lang('Unit Discount')</th>  --}}
                        <th>{{ $tt_price }} {{ $currancy->code }}</th>
                        <th></th>
                    </tr>
                </thead>
                @endif
                </table>
            </div>
        </div>
        
        </div>
    </section>

    <!-- Modal Add Product -->
    <div class="modal fade" id="modalAddProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header p-2" style="background: #8f8f8f;">
            <h5 class="modal-title" id="exampleModalLongTitle">Add Product</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>          
        </div>
      </div>
    </div>
    <!-- Modal Add Product -->

    <!-- Modal Add New Product -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 9999;">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header p-2" style="background: #8f8f8f;">
            <h5 class="modal-title" id="exampleModalLongTitle">Add New Product</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-addnewproduct', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
          <div class="modal-body">

            <div class="row">
                <div class="col-lg-6">
                    <label class="txtlbl">Part Number<span>*</span> </label>
                    <input class="txtbx primary-input dynamicstxt_s w-100 form-control" type="text" name="part_number" autocomplete="off" required>
                </div>
                <div class="col-lg-6">
                    <label class="txtlbl">Brand<span>*</span> </label>
                    <select class="txtbx niceSelect dynamicstxt_s w-100 bb form-control" name="brand" id="brand" required>
                        <option value=""></option>
                        @foreach ($brands as $key => $value)
                            <option value="{{ @$value->id }}">{{ @$value->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-6">
                    <label class="txtlbl">Category<span>*</span> </label>
                    <select class="txtbx niceSelect dynamicstxt_s w-100 bb form-control" name="category_name" id="category_name">
                        <option value=""></option>
                        @foreach ($itemCategories as $key => $value)
                            <option value="{{ @$value->id }}" >{{ @$value->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-6" id="sectionSubcategoryDiv">
                    <label class="txtlbl">Sub Category<span>*</span> </label>
                    <select class="txtbx niceSelect dynamicstxt_s w-100 bb form-control" name="subcategory_name" id="sectionSelectSubcategory">
                        <option value=""></option>
                        @foreach ($SuCategories as $key => $value)
                            <option value="{{ @$value->id }}">{{ @$value->sub_category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-12">
                    <label class="txtlbl">Description<span>*</span> </label>
                    <textarea class="txtbx primary-input dynamicstxt_s w-100 form-control" cols="0" rows="2" name="description" id="description" required></textarea>
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
    <!-- Modal Add New Product -->

@endsection

@section('script')
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
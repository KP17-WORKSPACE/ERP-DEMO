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
<section class="sms-breadcrumb mb-20 white-box">
    <div class="container-fluid">
        <div class="row" style="float: left;">
            <h1>@lang('Price Book')</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ route('user.dashboard') }}" class="top-btn-r-l"><i class="far fa fa-home" aria-hidden="true"></i> Home</a>
            <a href="{{ url('price-book') }}" class="top-btn-r"><i class="far fa fa-plus" aria-hidden="true"></i> New</a>
            <a href="{{ url('price-book/show') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View</a>
            <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh" aria-hidden="true"></i> Refresh</a>
        </div>
    </div>
</section>
<hr style="margin-top: 33px;" />
<div style="clear: both;"></div>

    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                {{-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 text-right">
                    @if (in_array(355, @$module_links) || Auth::user()->role_id == 1)
                        <a href="{{ url('sales-order') }}" class="primary-btn small fix-gr-bg">
                            @lang('View List')</a>
                    @endif
                </div> --}}
            </div>
        </div>
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

            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">                            
                            <div class="white-box">
                                <div class="add-visitor">
                                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'price-book', 'method' => 'get', 'id' => 'price-book2']) }}
                                    <div class="row ">
                                        <div class="col-lg-8">
                                            <div class="row">                                                
                                                <div class="col-lg-8 mb-10">
                                                    <label class="txtlbl">@lang('Product')<span>*</span></label>
                                                    <select class="niceSelect w-100 dynamicstxt bb w-100 form-control" name="pid" id="pid" required>
                                                        <option value=""></option>
                                                        @foreach ($product as $value)
                                                        <option value="{{ @$value->id }}" @if(isset($pid)) @if(@$pid == @$value->id) selected @endif @endif>{{ @$value->part_number }} | {{ @$value->description }}
                                                        </option>
                                                        @endforeach
                                                    </select><div style="display: none;">
                                                    <button type="submit" class="primary-btn fix-gr-bg" id="btnChange">Change</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{ Form::close() }}

                                    @if (count($edit)>0)
                                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'price-book/' . $pid, 'method' => 'PUT', 'id' => 'price-book']) }}
                                    @else
                                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'price-book','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'price-book']) }}
                                    @endif

                                    <?php
                                        if(count($edit)>0){ foreach($edit as $et){
                                    ?>
                                        <input type="hidden" name="pbid[]" value="{{ $et->id }}">
                                    <?php
                                        } }
                                    ?>

                                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                    <input type="hidden" name="pid" value="{{ isset($pid) ? $pid : '' }}">
                                    <div class="row ">
                                        <div class="col-lg-8">
                                            <?php $r_price="0.00"; $e_price="0.00"; ?>
                                        @foreach ($currency as $value)
                                            <div class="row">
                                                <div class="col-lg-1 mb-10">
                                                    <input type="hidden" name="currency_id[]" value="{{ @$value->id }}"/>
                                                    <h5 class="mt-3 mb-0 pb-0">{{ @$value->code }}</h5>Price
                                                </div>
                                                <?php
                                                if(count($edit)>0){
                                                    foreach($edit as $et){
                                                        if($et->currency_id == $value->id){
                                                            $r_price = $et->r_price;
                                                            $e_price = $et->e_price;
                                                        }
                                                    }
                                                }
                                                ?>

                                                <div class="col-lg-2 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">Reseller Price<span>*</span></label>
                                                        <input class="primary-input dynamicstxt_s w-100 form-control" type="text" name="r_price[]" autocomplete="off" id="r_price"
                                                        value="{{ $r_price }}">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">End User Price<span>*</span></label>
                                                        <input class="primary-input dynamicstxt_s w-100 form-control" type="text" name="e_price[]" autocomplete="off" id="e_price" 
                                                        value="{{ $e_price }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row"><div class="col-lg-5"><hr /></div></div>
                                        @endforeach


                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-12 text-left">
                                            <button type="submit" class="primary-btn fix-gr-bg" id="btnSubmit">
                                                <span class="ti-check"></span>
                                                @if (count($edit)>0)
                                                    @lang('Update')
                                                @else
                                                    @lang('Add')
                                                @endif
                                                @lang('Price')
                                            </button>
                                        </div>
                                    </div>
                                    {{ Form::close() }}

                                    @if(isset($pricebook))
                                    <div class="row">
                                        <div class="col-lg-12">                                            
                                            <table id="table_id" class="display school-table" cellspacing="0" width="100%">                    
                                                <thead>
                                                    <tr>
                                                        <th>@lang('lang.sl') </th>
                                                        <th>@lang('Product')</th>
                                                        <th>@lang('Reseller Price')</th>
                                                        <th>@lang('End User Price')</th>
                                                        <th>@lang('Status')</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $count = 1; @endphp
                                                    @foreach($pricebook as $value)
                                                    <tr>
                                                        <td>{{@$count++}}</td>
                                                        <td>{{@$value->productname->part_number}}</td>
                                                        <td>{{@$value->r_price}} {{@$value->currency->code}}</td>
                                                        <td>{{@$value->e_price}} {{@$value->currency->code}}</td>
                                                        <td>
                                                            @if(@$value->status==1)
                                                                <span class="badge-success pl-1 pr-1">Active</span>
                                                            @else
                                                                <span class="badge-danger pl-1 pr-1">Deleted</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(in_array(359, @$module_links) || Auth::user()->role_id == 1)
                                                            <a class="dropdown-item" href="{{url('price-book/'.$pid.'/'.$value->id.'/edit')}}">@lang('Edit')</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('script')
    <script>

$(window).ready(function() {
        $("#item-store-form").on("keypress", function (event) {           
            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {
                event.preventDefault();
                return false;
            }
        });
});


$(document).on("change", "#pid", function () {
    $("#loading_bg").css("display", "block");
    $("#btnChange").click();
});


function get_cust_name(id) {
    $("#loading_bg").css("display", "block");
    var action = "{{ URL::to('crm-leads-customername') }}";
    $.ajax({
        url: action,
        type: "GET",
        data: {
            _token: '{{ csrf_token() }}',
            id: id,
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
                        $("#cust_name").val(dataResult['data'][i].contcat_person);
                        $("#cust_no").val(dataResult['data'][i].contcat_number);
                        $("#cust_email").val(dataResult['data'][i].email);
                    }                        
                }
                else{
                    $("#cust_name").val("");
                    $("#cust_no").val("");
                    $("#cust_email").val("");
                }
                $("#loading_bg").css("display", "none");
        }
    });
}


    </script>
@endsection
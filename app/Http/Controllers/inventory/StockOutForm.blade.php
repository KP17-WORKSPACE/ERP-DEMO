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
            <h1>@lang('Stock Out')</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ route('user.dashboard') }}" class="top-btn-r-l"><i class="far fa fa-home" aria-hidden="true"></i> Home</a>
            <a href="{{ url('stock-out') }}" class="top-btn-r"><i class="far fa fa-plus" aria-hidden="true"></i> New</a>
            <a href="{{ url('stock-out/show') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View</a>
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

            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            @if (isset($edit))
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-out/' . $edit->id, 'method' => 'PUT', 'id' => 'stock-out-form']) }}
                            @else
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-out','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'stock-out-form']) }}
                            @endif
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                            <div class="white-box">

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

                                <div class="add-visitor">
                                    <div class="row ">
                                        <div class="col-lg-8">
                                            <div class="row">
                                                
                                                <div class="col-lg-3 mb-10">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="input-effect">
                                                                <label class="txtlbl">@lang('Date')<span>*</span></label>
                                                                @php
                                                                $value = date('m/d/Y');
                                                                if(isset($edit) && !empty($edit->date) ){ @$value =
                                                                date('m/d/Y', strtotime(@$edit->date)); }
                                                                else{ if(!empty(old('date'))){ @$value = old('date');
                                                                }else{
                                                                @$value = date('m/d/Y'); } }
                                                                @endphp
                                                                <input class="primary-input dynamicstxt w-100 date" id="date" type="text" autocomplete="off"
                                                                    name="date" value="{{ @$value }}">
                                                                @if ($errors->has('date'))
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $errors->first('date') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Part Number')<span>*</span></label>
                                                        <input class="primary-input dynamicstxt w-100 form-control {{ $errors->has('part_number') ? ' is-invalid' : '' }}"
                                                            type="text" name="part_number" autocomplete="off" id="part_number"
                                                            value="{{ isset($edit) ? (!empty(@$edit->part_number) ? @$edit->part_number : old('part_number')) : old('part_number') }}" required>
                                                        @if ($errors->has('part_number'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('part_number') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Qty')<span>*</span></label>
                                                        <input class="primary-input dynamicstxt w-100 form-control {{ $errors->has('qty') ? ' is-invalid' : '' }}"
                                                            type="text" name="qty" autocomplete="off" id="qty"
                                                            value="{{ isset($edit) ? (!empty(@$edit->qty) ? @$edit->qty : old('qty')) : old('qty') }}" required>
                                                        @if ($errors->has('qty'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('qty') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mb-10">
                                                    <label class="txtlbl">@lang('Customer')<span>*</span></label>
                                                    <select
                                                        class="niceSelect w-100 dynamicstxt bb w-100 form-control {{ $errors->has('customer') ? ' is-invalid' : '' }}"
                                                        name="customer" id="customer" required>
                                                        <option value=""></option>
                                                        @foreach ($vendors as $value)
                                                            <option value="{{ @$value->id }}"
                                                                {{ isset($edit) ? (!empty($edit->customer) ? (@$edit->customer == @$value->id ? 'selected' : '') : '') : '' }}>{{ @$value->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('customer'))
                                                        <span class="invalid-feedback invalid-select" role="alert">
                                                            <strong>{{ $errors->first('customer') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="col-lg-3 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('INV')<span></span></label>
                                                        <input class="primary-input dynamicstxt w-100 form-control {{ $errors->has('inv') ? ' is-invalid' : '' }}"
                                                            type="text" name="inv" autocomplete="off" id="inv"
                                                            value="{{ isset($edit) ? (!empty(@$edit->inv) ? @$edit->inv : old('inv')) : old('inv') }}">
                                                        @if ($errors->has('inv'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('inv') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Upload Doc')<span></span></label>
                                                        <input type="file" class="dynamicstxt w-100" name="doc_file" id="doc_file">
                                                        <input type="hidden" name="file_name" id="file_name" value="{{ isset($edit) ? (!empty(@$edit->file) ? @$edit->file : old('file')) : old('file') }}" />
                                                        {{--  <input class="primary-input dynamicstxt w-100 form-control {{ $errors->has('file') ? ' is-invalid' : '' }}"
                                                            type="text" name="file" autocomplete="off" id="file"
                                                            value="">  --}}
                                                        @if ($errors->has('file'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('file') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Created') @lang('By')<span>*</span></label>
                                                        <input class="primary-input dynamicstxt w-100 form-control {{ $errors->has('createdby') ? ' is-invalid' : '' }}" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit) ? (!empty(@$edit->createdby) ? @$edit->createdby->full_name : old('createdby')) : Auth::user()->full_name }}" readonly>
                                                        @if ($errors->has('createdby'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('createdby') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-8 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@if(isset($editserialno)) Add New @endif @lang('Serial Numbers') <span>*</span></label>

                                                        <input
                                                            class="primary-input dynamicstxt w-100 form-control {{ $errors->has('serial_number') ? ' is-invalid' : '' }}"
                                                            type="text" name="serial_number" autocomplete="off"
                                                            value="" onchange="add_srl()"
                                                            id="serial_number">
                                                        @if ($errors->has('serial_number'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('serial_number') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-8 mb-10">
                                                    <div id="srl_div"></div>
                                                </div>


                                                {{--  <input type="hidden" name="srl[]" id="srl_{{ $val->id }}" value="{{ $val->serial_no }}" />
                                                    <span id="srl_spn_{{ $val->id }}" class="btn-xs bg-warning pb-1">{{ $val->serial_no }}</span><a id="srl_btn_{{ $val->id }}"
                                                        class="bg-danger pl-2 pr-2 pb-1" onclick="del_srl('{{ $val->id }}')">x</a>&nbsp;  --}}



                                                @if(isset($editserialno))
                                                <div class="col-lg-6 mb-10">
                                                    @foreach ($editserialno as $val)
                                                    @if($val->status==1)
                                                    <input type="hidden" name="srl[]" id="srl_{{ $val->id }}" value="{{ $val->serial_no }}" />
                                                    <span id="srl_spn_{{ $val->id }}" class="btn-xs bg-warning pb-1">{{ $val->serial_no }}</span><a id="srl_btn_{{ $val->id }}" class="bg-danger pl-2 pr-2 pb-1" onclick="del_srl('{{ $val->id }}')">x</a>&nbsp;
                                                    @else
                                                    <span id="srl_spn_{{ $val->id }}" class="btn-xs bg-info pb-1">{{ $val->serial_no }}</span>&nbsp;
                                                    @endif
                                                    @endforeach
                                                </div>
                                                @endif
                                                
                                                <script>
                                                    function del_srl(id)
                                                    {
                                                        $("#srl_"+id).remove();
                                                        $("#srl_spn_"+id).remove();
                                                        $("#srl_btn_"+id).remove();
                                                    }
                                                </script>

                                            </div>
                                        </div>
                                    </div>


                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button type="submit" class="primary-btn fix-gr-bg" id="btnSubmit">
                                                <span class="ti-check"></span>
                                                @if (isset($edit))
                                                    @lang('lang.update')
                                                @else
                                                    @lang('lang.save')
                                                @endif
                                                @lang('Stock Out')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade admin-query" id="add_to_do">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Add Shipping</h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('shipping_name') ? 'is-invalid' : ' '}}" type="text" id="shipping_name_add" name="shipping_name" value="{{isset($editData)?@$editData->shipping_name:old('shipping_name')}}" >
                                    <label>  @lang('Shipping Name') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_1 red_alert"></span>                                    
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('contact_name') ? 'is-invalid' : ' '}}" type="text" id="contact_name_add" name="contact_name" value="{{isset($editData)?@$editData->contact_name:old('contact_name')}}" >
                                    <label>  @lang('Contact Name') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_2 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('contact_no') ? ' is-invalid' : '' }}" type="number" id="contact_no_add" name="contact_no" value="{{isset($editData)?@$editData->contact_no:old('contact_no')}}">
                                    <label>  @lang('Contact No') <span>*</span> </label>
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_3 red_alert"></span>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('address1') ? ' is-invalid' : '' }}" type="text" id="address1_add" name="address1" value="{{isset($editData)?@$editData->address1:old('address1')}}">
                                    <label>  @lang('Address 1') <span>*</span> </label>  
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_4 red_alert"></span>                                  
                                </div>
                            </div>
                            <div class="col-lg-12 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control{{ $errors->has('address2') ? ' is-invalid' : '' }}" type="text" id="address2_add" name="address2" value="{{isset($editData)?@$editData->address2:old('address2')}}">
                                    <label>  @lang('Address 2') <span>*</span> </label>    
                                    <span class="focus-border"></span>
                                    <span class="modal_input_validation_5 red_alert"></span>                              
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">    
                                <div class="col-lg-12 text-center">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button class="primary-btn tr-bg" data-dismiss="modal" type="button" id="btn_close2">
                                            @lang('lang.cancel')
                                        </button>
                                        <input class="primary-btn fix-gr-bg" type="submit" value="save" onclick="return validateAttachForm()">
    
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        
        function add_srl(){

            $("#loading_bg").css("display", "block");
    
            var part_number = $("#part_number").val();
            var serial_number = $("#serial_number").val();
            
            var action = "{{ URL::to('stock-out-getsrl') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    part_no: part_number,
                    serial_no: serial_number,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
    
                            //alert(dataResult['data'][0].serial_no);
                            for(var i=0; i<len; i++){
                                
                            var abc= "<input type='hidden' name='srl[]' id='srl_"+dataResult['data'][i].id+"' value='"+dataResult['data'][i].serial_no+"' /> <span id='srl_spn_"+dataResult['data'][i].id+"' class='btn-xs bg-warning pb-1'>"+dataResult['data'][i].serial_no+"</span><a id='srl_btn_"+dataResult['data'][i].id+"' class='bg-danger pl-2 pr-2 pb-1' onclick='del_srl('"+dataResult['data'][i].id+"')'>x</a>&nbsp;";

                                $("#srl_div").append(abc);
                                $("#serial_number").val("");
                                $("#loading_bg").css("display", "none");
                            }                        
                        }
                        else{
                            alert("Invalid Serial Number");
                            $("#serial_number").val("");
                            $("#loading_bg").css("display", "none");
                        }
                }
            });
    
        }


        // $(document).ready(function () {
        //     $("#btnSubmit").click(function () {
        //         setTimeout(function () { disableButton(); }, 0);
        //     });
        //     function disableButton() {
        //         $("#btnSubmit").prop('disabled', true);
        //     }
        // });
    </script>
@endsection
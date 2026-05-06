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
            <h1>@lang('Stock In')</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ route('user.dashboard') }}" class="top-btn-r-l"><i class="far fa fa-home" aria-hidden="true"></i> Home</a>
            <a href="{{ url('stock-in') }}" class="top-btn-r"><i class="far fa fa-plus" aria-hidden="true"></i> New</a>
            <a href="{{ url('stock-in/show') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View</a>
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
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-in/' . $edit->id, 'method' => 'PUT', 'id' => 'stock-in-form']) }}
                            @else
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-in','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'stock-in-form']) }}
                            @endif
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                            <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                            <input type="hidden" name="stock_in_id" id="stock_in_id" value="{{ isset($edit) ? $edit->id : '' }}">
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
                                                            type="text" name="qty" autocomplete="off" id="qty" onkeypress="return set_stockin_license_key(event)"
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
                                                        <label class="txtlbl">@lang('DO')<span></span></label>
                                                        <input class="primary-input dynamicstxt w-100 form-control {{ $errors->has('do') ? ' is-invalid' : '' }}"
                                                            type="text" name="do" autocomplete="off" id="do"
                                                            value="{{ isset($edit) ? (!empty(@$edit->do) ? @$edit->do : old('do')) : old('do') }}">
                                                        @if ($errors->has('do'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('do') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mb-10">
                                                    <div class="input-effect">
                                                        <label class="txtlbl">@lang('Upload Doc')<span></span></label>
                                                        <input type="file" class="dynamicstxt w-100" name="doc_file" id="doc_file">
                                                        <input type="hidden" name="file_name" id="file_name" value="{{ isset($edit) ? (!empty(@$edit->file) ? @$edit->file : old('file')) : old('file') }}" />
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
                                                            value=""
                                                            id="serial_number" onclick="return open_stockin_license_key_modal()">
                                                        @if ($errors->has('serial_number'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $errors->first('serial_number') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

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
                                                @lang('Stock In')
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
<div class="modal fade" id="ModalLicenseKey" data-bs-backdrop="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add License Key (<label id="ModalLabelHeading"></label>)</h4>
                <button class="btn btn-sm btn-light ms-auto" data-bs-toggle="modal" data-bs-target="#ModalExcelQuote"><i class="ico icon-outline-import text-success"></i> Import</button>
                <input type="hidden" id="part_number_id" />
                <input type="hidden" id="edit_license_id" value="" />
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="form-label">Qty</label>
                                <input type="number" class="form-control" id="license_qty" value="1" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">License Key (<span id="licenseCountSummary" class="text-muted small">Selected: 0 of 0</span>)</label>
                                <input type="text" class="form-control" id="license_key" />
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Exp Date</label>
                                <input type="text" class="form-control date-picker" id="exp_date" autocomplete="off" />
                            </div>
                            <div class="col-md-2"><br />
                                <button type="button" id="license_add" class="btn btn-light btn-sm" onclick="return add_license_key_stockin_form()">Add</button>
                                <button type="button" id="license_cancel_edit" class="btn btn-sm btn-outline-secondary" onclick="cancel_license_edit_stockin_form()" style="display:none;">x</button>
                            </div>
                        </div>
                        <div id="licenseKeyMessage" class="text-danger small mb-2 mt-2" style="display:none;"></div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <table id="lk-table" class="table table-hover" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">Sr.No</th>
                                            <th style="width: 60%;">Licence Key</th>
                                            <th style="width: 20%;">Expiry Date</th>
                                            <th style="width: 10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="primary-btn tr-bg" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalExcelQuote" data-bs-backdrop="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">License Excel Import</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <label class="form-label">Select File (.csv)</label>
                        <input type="file" name="import_file" id="import_file" class="form-control w-25" accept=".csv, .xls, .xlsx" />
                        <small>(<a href="{{ url('public/uploads/product_upload/grn_license_sample_format.csv') }}" target="_blank">Sample File</a>)</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="return excel_license_key_stockin_form()" type="button" class="primary-btn fix-gr-bg">Upload</button>
            </div>
        </div>
    </div>
</div>
<script>
    function validateAttachForm() {
    var val1 = $("#shipping_name_add").val();
    var val2 = $("#contact_name_add").val();
    var val3 = $("#contact_no_add").val();
    var val4 = $("#address1_add").val();
    var val5 = $("#address2_add").val();

    if (val1 === "") {
        $('.modal_input_validation_1').show();
        $(".modal_input_validation_1").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_1").addClass("red_alert");
        return false;
    }
    if (val2 === "") {
        $('.modal_input_validation_2').show();
        $(".modal_input_validation_2").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_2").addClass("red_alert");
        return false;
    }
    if (val3 === "") {
        $('.modal_input_validation_3').show();
        $(".modal_input_validation_3").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_3").addClass("red_alert");
        return false;
    }
    if (val4 === "") {
        $('.modal_input_validation_4').show();
        $(".modal_input_validation_4").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_4").addClass("red_alert");
        return false;
    }
    if (val5 === "") {
        $('.modal_input_validation_5').show();
        $(".modal_input_validation_5").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_5").addClass("red_alert");
        return false;
    }
    //return true;

    var url = $('#url').val();
    $.ajax({
        type: "POST",
        data: {
            shipping_name: val1,
            contact_name: val2,
            contact_no: val3,
            address1: val4,
            address2: val5
        },

        //url: 'http://syscom.company/venus-erp/shipping-store2',
        //url: 'http://localhost:81/venus-erp/shipping-store2',
        url: url + '/' + 'shipping-store2',
              cache: false,
        success: function(response) {
            var response = JSON.parse(response);
            var len = 0;
            if(response['data']=="ERROR")
            {
                alert("Error found in something!!");
            }
            else{
                if (response['data'] != null) {
                len = response['data'].length;
                }
                if(len > 0){
                    
                    //$('#shipping_name').find('option').not(':first').remove();

                    for(var i=0; i<len; i++){
                        var id = response['data'][i].id;
                        var name = response['data'][i].shipping_name;
                        var option = "<option value='"+id+"'>"+name+"</option>";
                        //$("#shipping_name").append($(option));
                        //$('#shipping_name').append(new Option(name, id));
                        $("#shipping_name").append(option);
                        $("#vendor").append(option);
                    }
                    
                    alert('Shipping Added Successfully!!');
                        $('#btn_close2').click();
                }
            }            
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {}
    });


    //preventDefault();
    }

    function cfc_amount_change(id)
    {
        var amt = $("#cfc_amount_"+id).val();
        $("#cfc_cal_amount_"+id).val(amt);
    }

    </script>
@endsection

@section('script')
    <script>
let stockInFormLicenseRows = [];

function normalizeLicenseDateForStore(value) {
    var raw = (value || '').toString().trim();
    if (!raw || raw === '0000-00-00') return '';
    if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) return raw;
    var parts = raw.replace(/\./g, '/').replace(/-/g, '/').split('/');
    if (parts.length !== 3) return '';
    var day = parts[0].padStart(2, '0');
    var month = parts[1].padStart(2, '0');
    var year = parts[2];
    if (year.length === 2) year = '20' + year;
    if (!/^\d{4}$/.test(year)) return '';
    return year + '-' + month + '-' + day;
}

function formatLicenseDateForDisplay(value) {
    var ymd = normalizeLicenseDateForStore(value);
    if (!ymd) return '';
    var parts = ymd.split('-');
    return parts[2] + '/' + parts[1] + '/' + parts[0];
}

function showLicenseKeyMessage(message, type) {
    var $msg = $('#licenseKeyMessage');
    $msg.removeClass('text-danger text-warning text-success');
    if (!message) {
        $msg.hide();
        return;
    }
    var t = type || 'danger';
    $msg.text(message).addClass(t === 'success' ? 'text-success' : (t === 'warning' ? 'text-warning' : 'text-danger')).show();
}

function updateLicenseAddStateStockInForm() {
    var maxQty = parseInt($('#license_qty').val(), 10) || 0;
    var currentCount = stockInFormLicenseRows.length;
    var isEditMode = ($('#edit_license_id').val() || '').toString().trim() !== '';
    $('#license_add').prop('disabled', maxQty <= 0 || (!isEditMode && currentCount >= maxQty));
    $('#licenseCountSummary').text('Selected: ' + currentCount + ' of ' + maxQty);
}

function renderLicenseRowsStockInForm(rows) {
    var seen = {};
    var uniqueRows = [];
    var tr = '';
    var n = 0;
    (rows || []).forEach(function(row) {
        var key = (row.license_key || '').toString().trim();
        if (!key) return;
        var lk = key.toLowerCase();
        if (seen[lk]) return;
        seen[lk] = true;
        uniqueRows.push(row);
        n++;
        var rowId = parseInt(row.id, 10);
        tr += '<tr><td class="text-center">' + n + '</td><td>' + $('<div>').text(key).html() + '</td><td>' + formatLicenseDateForDisplay(row.exp_date) +
            '</td><td class="text-center"><a onclick="edit_license_key_stockin_form(' + rowId + ', this)" class="btn-sm btn-light">E</a> <a onclick="delete_license_key_stockin_form(' + rowId + ')" class="btn-sm btn-light">D</a></td></tr>';
    });
    stockInFormLicenseRows = uniqueRows;
    $('#serial_number').val(uniqueRows.map(function(r) { return (r.license_key || '').toString().trim(); }).filter(Boolean).join(', '));
    if (!n) {
        tr = '<tr><td colspan="4" class="text-center text-muted">No keys added.</td></tr>';
    }
    $('#lk-table tbody').html(tr);
    updateLicenseAddStateStockInForm();
}

function parseAjaxResponseStockIn(dataResult) {
    if (typeof dataResult === 'string') return JSON.parse(dataResult);
    return dataResult || {};
}

function open_stockin_license_key_modal() {
    var partNumber = ($('#part_number').val() || '').toString().trim();
    if (!partNumber) {
        showLicenseKeyMessage('Enter part number before adding license keys.', 'danger');
        return false;
    }
    $('#part_number_id').val('');
    $('#ModalLabelHeading').text(partNumber);
    $('#license_qty').val($('#qty').val() || 1);
    showLicenseKeyMessage('');
    cancel_license_edit_stockin_form();
    $('#ModalLicenseKey').modal('show');
    view_license_key_stockin_form();
    return false;
}

function set_stockin_license_key(event) {
    var keyPressed = event.keyCode || event.which;
    if (keyPressed === 13) {
        event.preventDefault();
        return open_stockin_license_key_modal();
    }
    return true;
}

function cancel_license_edit_stockin_form() {
    $('#edit_license_id').val('');
    $('#license_key').val('');
    $('#exp_date').val('');
    $('#license_add').text('Add');
    $('#license_cancel_edit').hide();
}

function edit_license_key_stockin_form(id) {
    var row = stockInFormLicenseRows.find(function(item) { return parseInt(item.id, 10) === parseInt(id, 10); });
    if (!row) return;
    $('#edit_license_id').val(row.id);
    $('#license_key').val(row.license_key || '');
    $('#exp_date').val(formatLicenseDateForDisplay(row.exp_date));
    $('#license_add').text('Update');
    $('#license_cancel_edit').show();
}

function add_license_key_stockin_form() {
    var licenseKey = ($('#license_key').val() || '').toString().trim();
    var maxQty = parseInt($('#license_qty').val(), 10) || 0;
    var partNumber = ($('#part_number').val() || '').toString().trim();
    if (!partNumber || !licenseKey || maxQty <= 0) {
        showLicenseKeyMessage('Part number, qty and license key are required.', 'danger');
        return false;
    }
    $.ajax({
        url: ($('#edit_license_id').val() ? "{{ URL::to('update-stkin-license-key') }}" : "{{ URL::to('add-stkin-license-key') }}"),
        type: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            id: $('#edit_license_id').val(),
            part_number: partNumber,
            license_key: licenseKey,
            exp_date: normalizeLicenseDateForStore($('#exp_date').val()),
            license_qty: maxQty,
            stock_in_id: $('#stock_in_id').val()
        },
        cache: false,
        success: function(dataResult) {
            var response = parseAjaxResponseStockIn(dataResult);
            if (response.error) {
                showLicenseKeyMessage(response.error, 'danger');
                return;
            }
            renderLicenseRowsStockInForm(response.data || []);
            cancel_license_edit_stockin_form();
        },
        error: function() {
            showLicenseKeyMessage('Unable to add license key.', 'danger');
        }
    });
    return false;
}

function view_license_key_stockin_form() {
    $.ajax({
        url: "{{ URL::to('view-stkin-license-key') }}",
        type: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            part_number: ($('#part_number').val() || '').toString().trim(),
            stock_in_id: $('#stock_in_id').val()
        },
        cache: false,
        success: function(dataResult) {
            var response = parseAjaxResponseStockIn(dataResult);
            if (response.error) {
                showLicenseKeyMessage(response.error, 'danger');
                return;
            }
            renderLicenseRowsStockInForm(response.data || []);
        }
    });
}

function delete_license_key_stockin_form(id) {
    $.ajax({
        url: "{{ URL::to('delete-stkin-license-key') }}",
        type: "POST",
        data: {
            _token: '{{ csrf_token() }}',
            id: id,
            part_number: ($('#part_number').val() || '').toString().trim(),
            stock_in_id: $('#stock_in_id').val()
        },
        cache: false,
        success: function(dataResult) {
            var response = parseAjaxResponseStockIn(dataResult);
            if (response.error) {
                showLicenseKeyMessage(response.error, 'danger');
                return;
            }
            renderLicenseRowsStockInForm(response.data || []);
        }
    });
}

function excel_license_key_stockin_form() {
    var fileInput = $('#import_file')[0];
    var partNumber = ($('#part_number').val() || '').toString().trim();
    var maxQty = parseInt($('#license_qty').val(), 10) || 0;
    if (!fileInput || !fileInput.files || !fileInput.files.length) {
        showLicenseKeyMessage('Select import file first.', 'danger');
        return false;
    }
    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('part_number', partNumber);
    formData.append('stock_in_id', $('#stock_in_id').val());
    formData.append('license_qty', maxQty);
    formData.append('import_file', fileInput.files[0]);
    $.ajax({
        url: "{{ URL::to('add-stkin-license-key-excel') }}",
        type: "POST",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(dataResult) {
            var response = parseAjaxResponseStockIn(dataResult);
            if (response.error) {
                showLicenseKeyMessage(response.error, 'danger');
                return;
            }
            renderLicenseRowsStockInForm(response.data || []);
            $('#import_file').val('');
        },
        error: function() {
            showLicenseKeyMessage('Unable to import license keys.', 'danger');
        }
    });
    return false;
}

$(window).ready(function() {
        $("#item-store-form").on("keypress", function (event) {           
            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {
                event.preventDefault();
                return false;
            }
        });
});


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
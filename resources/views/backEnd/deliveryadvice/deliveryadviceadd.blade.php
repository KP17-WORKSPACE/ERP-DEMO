@extends('backEnd.master')
@section('mainContent')
    @php
    function showPicName($data){
    $name = explode('/', $data);
    return $name[3];
    }


    @endphp
    <link href="{{ asset('public/css/add_staff.css') }}" type="text/css" rel="stylesheet">

    @php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] =
    @$permission->moduleLink->module_id;}

    $modules = array_unique(@$modules);
    @endphp

    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-6 col-xs-12">
                    <div class="main-title">
                        <h3 class="mb-30">  @lang('Delivery Advice')</h3>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 text-right">
                    <a href="{{url('delivery-advice')}}" class="primary-btn small fix-gr-bg">
                         @lang('List')
                    </a>
                    @if(isset($editData))
                        {{-- <a href="{{url('company',@$editData->id)}}" class="primary-btn small fix-gr-bg">  @lang('lang.view') </a> --}}
                    @endif
                </div>
            </div>
            
            @if(isset($editData))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delivery-advice-update/'. @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'delivery-advice-create-form']) }}
            <input type="hidden" value="{{@$editData->id}}" name="cust_id">
            @else
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delivery-advice-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'delivery-advice-create-form']) }}
            @endif

            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{date('Y-m-d')}}">
            <div class="row">
                <div class="col-lg-12">
                  <div class="white-box">
                        <div class="row mb-0">
                            <div class="col-lg-12">
                                <hr>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            @php $value = date('m/d/Y');
                                            if(isset($editData) && !empty($editData->doc_date) ){ @$value = date('m/d/Y', strtotime(@$editData->doc_date)); }
                                            else{ if(!empty(old('doc_date'))){ @$value = old('doc_date'); }else{ @$value = date('m/d/Y'); } }
                                            @endphp
                                            <input class="primary-input date" id="doc_date" type="text" name="doc_date" value="{{ @$value }}">
                                            <label>@lang('Doc Date')</label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('doc_date'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('doc_date') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="" type="button">
                                            <i class="ti-calendar" id="end-date-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <input class="primary-input form-control {{$errors->has('doc_number') ? 'is-invalid' : ' '}}" type="text" id="doc_number" name="doc_number"
                                            value="{{ isset($editData) ? (!empty(@$editData->doc_number) ? @$edit->doc_number : old('doc_number')) : 'DAV-'.sprintf('%03d', @App\SysDeliveryNote::max('id') + 1) }}">
                                            <span class="focus-border"></span>
                                            <label>  @lang('Doc Number') <span>*</span> </label>
                                            @if ($errors->has('doc_number'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('doc_number') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="" type="button" id="cr_search_btn" onclick="fn_cr_search_btn()">
                                            <i class="ti-search" id="end-date-icon"></i>
                                        </button>
                                        <script>
                                            function fn_cr_search_btn()
                                            {
                                                var cr_search = $('#doc_number').val();
                                                cr_search = cr_search.replace(/\D/g,'');
                                                var url1 = $('#url').val();
                                                window.location.href = url1 + '/' + 'sales-return/' + cr_search;
                                            }
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-10">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{ $errors->has('created_by') ? ' is-invalid' : '' }}" type="text" name="createdby" autocomplete="off" id="created_by" value="{{ isset($editData) ? (!empty(@$editData->created_by) ? @$editData->createdby->full_name : old('created_by')) : Auth::user()->full_name }}" readonly>
                                    <label>@lang('Created') @lang('By')<span>*</span></label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('createdby'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('createdby') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 mb-20">
                                <select class="niceSelect w-100 bb form-control {{ $errors->has('customer_id') ? ' is-invalid' : '' }}" name="customer_id" id="da_customer_id">
                                    <option data-display="@lang('Customer')" value="">@lang('Customer')</option>
                                    @foreach ($customer as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($edit) ? (!empty(@$editData->supplier_id) ? (@$editData->supplier_id == @$value->id ? 'selected' : '') : '') : '' }}>
                                            {{ @$value->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('customer_id'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('customer_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{ $errors->has('narration') ? ' is-invalid' : '' }}" type="text" name="narration" autocomplete="off" value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}" id="narration">
                                    <label>@lang('Narration') <span>*</span></label>
                                    <span class="focus-border"></span>
                                    @if ($errors->has('narration'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('narration') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('salesman') ? 'is-invalid' : ' '}}" type="text" id="salesman" name="salesman"
                                    value="{{ isset($editData) ? (!empty(@$editData->salesman) ? @$edit->salesman : old('salesman')) : '' }}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Salesman') <span>*</span> </label>
                                    @if ($errors->has('salesman'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('salesman') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('contact_person') ? 'is-invalid' : ' '}}" type="text" id="contact_person" name="contact_person"
                                    value="{{ isset($editData) ? (!empty(@$editData->contact_person) ? @$edit->contact_person : old('contact_person')) : '' }}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Contact Person') <span>*</span> </label>
                                    @if ($errors->has('contact_person'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('contact_person') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('mobile_no') ? 'is-invalid' : ' '}}" type="text" id="mobile_no" name="mobile_no"
                                    value="{{ isset($editData) ? (!empty(@$editData->mobile_no) ? @$edit->mobile_no : old('mobile_no')) : '' }}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Mobile No') <span>*</span> </label>
                                    @if ($errors->has('mobile_no'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('mobile_no') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('landline_no') ? 'is-invalid' : ' '}}" type="text" id="landline_no" name="landline_no"
                                    value="{{ isset($editData) ? (!empty(@$editData->landline_no) ? @$edit->landline_no : old('landline_no')) : '' }}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Land Line No') <span>*</span> </label>
                                    @if ($errors->has('landline_no'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('landline_no') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect" id="sectionDaSINumberDiv">
                                    <select class="niceSelect w-100 bb form-control" name="da_si_numbers" id="da_si_numbers">
                                        <option data-display="@lang('Invoive Number') *" value="0">@lang('Select') *</option>
                                    </select>
                                    <span class="focus-border"></span>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            @php $value = date('m/d/Y');
                                            if(isset($editData) && !empty($editData->invoice_date) ){ @$value = date('m/d/Y', strtotime(@$editData->invoice_date)); }
                                            else{ if(!empty(old('invoice_date'))){ @$value = old('invoice_date'); }else{ @$value = date('m/d/Y'); } }
                                            @endphp
                                            <input class="primary-input date" id="invoice_date" type="text" name="invoice_date" value="{{ @$value }}">
                                            <label>@lang('Invoice Date')</label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('invoice_date'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('invoice_date') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="" type="button">
                                            <i class="ti-calendar" id="end-date-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('vehicle_no') ? 'is-invalid' : ' '}}" type="text" id="vehicle_no" name="vehicle_no"
                                    value="{{ isset($editData) ? (!empty(@$editData->vehicle_no) ? @$edit->vehicle_no : old('vehicle_no')) : '' }}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Vehicle No') <span>*</span> </label>
                                    @if ($errors->has('vehicle_no'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('vehicle_no') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('driver') ? 'is-invalid' : ' '}}" type="text" id="driver" name="driver"
                                    value="{{ isset($editData) ? (!empty(@$editData->driver) ? @$edit->driver : old('driver')) : '' }}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Driver') <span>*</span> </label>
                                    @if ($errors->has('driver'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('driver') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('do_no') ? 'is-invalid' : ' '}}" type="text" id="do_no" name="do_no"
                                    value="{{ isset($editData) ? (!empty(@$editData->do_no) ? @$edit->do_no : old('do_no')) : '' }}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('DO No') <span>*</span> </label>
                                    @if ($errors->has('do_no'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('do_no') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            @php $value = date('m/d/Y');
                                            if(isset($editData) && !empty($editData->do_date) ){ @$value = date('m/d/Y', strtotime(@$editData->do_date)); }
                                            else{ if(!empty(old('do_date'))){ @$value = old('do_date'); }else{ @$value = date('m/d/Y'); } }
                                            @endphp
                                            <input class="primary-input date" id="do_date" type="text" name="do_date" value="{{ @$value }}">
                                            <label>@lang('DO Date')</label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('do_date'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('do_date') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="" type="button">
                                            <i class="ti-calendar" id="end-date-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <select class="niceSelect w-100 bb form-control {{ $errors->has('payment_terms') ? ' is-invalid' : '' }}" name="payment_terms" id="payment_terms" onchange="fn_payment_terms()">
                                        <option data-display="@lang('Payment Terms') *" value="" >@lang('Payment Terms') *</option>
                                        @foreach($paymentterms as $value)
                                             <option value="{{@$value->id}}" {{isset($edit)? !empty(@$edit->payment_terms)? @$edit->payment_terms==@$value->id ? 'selected':'':'':''}} >{{@$value->title}}</option>
                                        @endforeach
                                        <option value="150">Other</option>
                                    </select>
                                    @if ($errors->has('payment_terms'))
                                    <span class="invalid-feedback invalid-select" role="alert">
                                        <strong>{{ $errors->first('payment_terms') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            @php $value = date('m/d/Y');
                                            if(isset($editData) && !empty($editData->delivery_date) ){ @$value = date('m/d/Y', strtotime(@$editData->delivery_date)); }
                                            else{ if(!empty(old('delivery_date'))){ @$value = old('delivery_date'); }else{ @$value = date('m/d/Y'); } }
                                            @endphp
                                            <input class="primary-input date" id="delivery_date" type="text" name="delivery_date" value="{{ @$value }}">
                                            <label>@lang('Delivery Date')</label>
                                            <span class="focus-border"></span>
                                            @if ($errors->has('delivery_date'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('delivery_date') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="" type="button">
                                            <i class="ti-calendar" id="end-date-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('delivery_time') ? 'is-invalid' : ' '}}" type="text" id="delivery_time" name="delivery_time"
                                    value="{{ isset($editData) ? (!empty(@$editData->delivery_time) ? @$edit->delivery_time : old('delivery_time')) : '' }}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Delivery Time') <span>*</span> </label>
                                    @if ($errors->has('delivery_time'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('delivery_time') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('delivery_address') ? 'is-invalid' : ' '}}" type="text" id="delivery_address" name="delivery_address"
                                    value="{{ isset($editData) ? (!empty(@$editData->delivery_address) ? @$edit->delivery_address : old('delivery_address')) : '' }}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Delivery Address') <span>*</span> </label>
                                    @if ($errors->has('delivery_address'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('delivery_address') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('invoice_amount') ? 'is-invalid' : ' '}}" type="text" id="invoice_amount" name="invoice_amount"
                                    value="{{ isset($editData) ? (!empty(@$editData->invoice_amount) ? @$edit->invoice_amount : old('invoice_amount')) : '' }}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Invoice Amount') <span>*</span> </label>
                                    @if ($errors->has('invoice_amount'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('invoice_amount') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 mb-20">
                                <div class="input-effect">
                                    <input class="primary-input form-control {{$errors->has('remarks') ? 'is-invalid' : ' '}}" type="text" id="remarks" name="remarks"
                                    value="{{ isset($editData) ? (!empty(@$editData->remarks) ? @$edit->remarks : old('remarks')) : '' }}">
                                    <span class="focus-border"></span>
                                    <label>  @lang('Remarks') <span>*</span> </label>
                                    @if ($errors->has('remarks'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('remarks') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="equipment comon-status row mt-40 d-block">
                            <table class="sstable" cellspacing="0" width="100%" id="DelNoteList_table">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">@lang('#')</th>
                                        <th style="width:300px;">@lang('Part No')</th>
                                        <th style="width:200px;">@lang('Qty')</th>
                                        <th style="width:200px;">@lang('Unit Price')</th>
                                        <th style="width:200px;">@lang('Value')</th>
                                        <th>@lang('Remarks')</th>
                                    </tr>
                                </thead>
                                <tbody>                                    
                                    @for ($roid= 1;  $roid < 11 ; $roid++)
                                    <tr>
                                        <td>{{$roid}}
                                        </td>
                                        <td>
                                            <input class="w-100 sstxtbx" type="text" id="da_part_no_{{$roid}}" name="da_part_no[]" autocomplete="off" min="0">
                                        </td>
                                        <td>
                                            <input class="w-100 sstxtbx" type="number" id="da_qty_{{$roid}}" name="da_qty[]" autocomplete="off" min="0" onchange="da_txt_change({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="w-100 sstxtbx" type="number" id="da_unit_price_{{$roid}}" name="da_unit_price[]" autocomplete="off" onchange="da_txt_change({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="w-100 sstxtbx" type="number" id="da_value_{{$roid}}" name="da_value[]" autocomplete="off" readonly>
                                        </td>
                                        <td>
                                            <input class="w-100 sstxtbx" type="text" id="da_remarks_{{$roid}}" name="da_remarks[]" autocomplete="off">
                                        </td>
                                    </tr>
                                    @endfor
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                      <td></td>
                                      <td></td>
                                      <td class="sstablefoot">0.00</td>
                                      <td class="sstablefoot">0.00</td>
                                      <td></td>
                                    </tr>
                                  </tfoot> --}}
                            </table>

                            <div style="display: none;">
                                @if(!isset($view))
                                    <button type="button" class="primary-btn small fix-gr-bg" id="addRowDN"><span class="ti-plus pr-2"></span>@lang('lang.item')</button>
                                @endif
                            </div>

                        </div>
                <!-- Bank Info Details -->
                <!-- end row -->
                <div class="row mt-40">
                    <div class="col-lg-12 text-center">
                        @if(!isset($view))
                        <button class="primary-btn fix-gr-bg" id="btnSubmit">
                            <span class="ti-check"></span>
                            @if(isset($editData)) @lang('lang.update') @else @lang('lang.add') @endif @lang('Delivery Note')
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>        
    </div>
    </div>
    {{ Form::close() }}
    </div>
</section>

<form id="ta">
    <div class="modal fade admin-query" id="dn_list_popup_win" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Link Info - <label id="lbl-dn-sales-invoice"></label></h4>
                    <button class="close" data-dismiss="modal" type="button">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
                    <input type="hidden" id="pi_ids">
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                    <table class="sstable" cellspacing="0" width="100%" id="siListDnInvo">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;">@lang('#')</th>
                                                <th style="width:100px;">@lang('Part Number')</th>
                                                <th style="width:100px;">@lang('Qty')</th>
                                                <th style="width:100px;">@lang('QOH')</th>
                                                <th style="width:100px;">@lang('ExeQty')</th>
                                                <th style="width:100px;">@lang('BalQty')</th>
                                                <th style="width:100px;">@lang('Link Val')</th>
                                                <th style="width:100px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
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
                                        {{-- <input class="primary-btn fix-gr-bg" type="button" value="Add To Return" id="btnTransfer"> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div>
<script>
    function da_txt_change(id)
    {
        var da_qty = $('#da_qty_'+id+'').val();
        var da_unit_price = $('#da_unit_price_'+id+'').val();
        $('#da_value_'+id+'').val(Number(da_qty) * Number(da_unit_price));        
    }
</script>
@endsection

@section('script')
    <script>
        // $(document).ready(function () {
        //     $("#btnSubmit").click(function () {
        //         setTimeout(function () { disableButton(); }, 0);
        //     });
        //     function disableButton() {
        //         $("#btnSubmit").prop('disabled', true);
        //     }
        // });
        
        $(document).ready(function () {
            $("#btnSubmit2").click(function () {
                setTimeout(function () { disableButton(); }, 0);
            });
            function disableButton() {
                $("#btnSubmit2").prop('disabled', true);
            }
        });
    </script>
@endsection
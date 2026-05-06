@extends('backEnd.master')
@section('mainContent') 
@php 
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get(); 
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}
    $modules = array_unique(@$modules);


    @$generalSetting=App\SmGeneralSettings::where('id',1)->first();
    @$currency_symbol = @$generalSetting->currency_symbol;
    if(isset($generalSetting->logo)){  @$logo = @$generalSetting->logo;  }
    else{ @$logo = 'public/uploads/settings/logo.png'; } 

    $sm_staff= App\SmStaff::where('user_id',Auth::user()->id)->first();
    if(!empty(@$sm_staff)){
        @$profile_image = @$sm_staff->staff_photo; 
        if(empty(@$profile_image)){
            @$profile_image ='public/uploads/staff/staff1.jpg';
        }
    }
@endphp 
<link rel="stylesheet" href="{{ asset('/public/css/quotationView.css') }}">
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('Sales Invoice') @lang('lang.details')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="{{url('sales-invoice')}}">@lang('Sales') </a>
                <a href="{{url('sales-invoice')}}">@lang('Sales Invoice') @lang('lang.details')</a>
            </div>
        </div>
    </div>
</section>


<section class="admin-visitor-area">
<div class="container-fluid p-0">
    
    <div class="row">
        <div class="col-lg-4">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-find', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <div class="white-box m-0 p-3">
                <div class="add-visitor">
                    <div class="row mb-0">
                        <div class="col-lg-12">
                            @if(session()->has('message-success'))
                                <div class="alert alert-success mb-20">
                                    {{ session()->get('message-success') }}
                                </div>
                            @elseif(session()->has('message-danger'))
                                <div class="alert alert-danger">
                                    {{ session()->get('message-danger') }}
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-8 mb-10">
                            <div class="input-effect">
                                <input class="primary-input form-control{{ $errors->has('si_number') ? ' is-invalid' : '' }}"
                                    type="text" name="si_number" autocomplete="off" value="{{old('si_number')}}" required>
                                <label>@lang('SALES INVOICE NUMBER') <span>*</span> </label>
                                <span class="focus-border"></span>
                            </div>
                        </div>
                        <div class="col-lg-4 text-center">
                            <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{ @$tooltip }}">
                                <span class="ti-check"></span>
                                    @lang('View')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>

        <div class="col-lg-8">
            {{-- {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-order-find', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            {{ Form::close() }} --}}
            <div>
                <div class="add-visitor">
                    <div class="row mb-0">
                        <div class="col-lg-12 text-right">
                            <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="General">
                                <span class="ti-files"></span>
                            </button>                            
                            <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="New SI" onclick="location.href = 'create';">
                                <span class="ti-file"></span>
                            </button>
                            <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="Post & Print" onclick="location.href = 'sales-invoice/{{$si->id}}/print';">
                                <span class="ti-save"></span>{{-- <span class="ti-printer"></span> --}}
                            </button>
                            <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="Print" onclick="location.href = 'sales-invoice/{{$si->id}}/print';">
                                <span class="ti-printer"></span>
                            </button>
                            <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="Print Preview" onclick="window.open('sales-invoice/{{$si->id}}/printpreview' + location.search);">
                                <span class="ti-clipboard"></span>
                            </button>
                            <button class="primary-btn fix-gr-bg" data-modal-size="modal-md" data-target="#add_to_do" data-toggle="modal">
                                <span data-toggle="tooltip" title="Attach" class="ti-pin-alt"></span>
                            </button>
                            <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="Edit SI" onclick="window.open('/{{$si->id}}/edit' + location.search);">
                                <span class="ti-clipboard"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br />
    <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                   <div class="row mt-40">
                        <div class="col-lg-12"> 

                            <div class="row" id="salesInvoice">
                                <div class="container-fluid">
                                    <div class="row mb-20">
                                        <div class="col-lg-12">
                                            <table class="quotation_view_table" >
                                                <tr>
                                                    <td class="quotation_view_table_tr_td"> 
                                                        <div class="col-lg-12 ">
                                                            <img src="{{asset(@$company->company_logo)}}"  class="quotation_view_table_tr_img" style="max-width: 150px;">
                                                            <div class="business-info text-left">
                                                                <h3 class="mt-10 primary-color">{{@$company->company_name}}</h3>
                                                                <p class="mt-0 primary-color m-0" class="quotation_view_50">{{@$company->company_address}}</p>
                                                                <p class="mt-0 primary-color m-0" class="quotation_view_50">{{@$company->telephone}}</p>
                                                                <p class="mt-0 primary-color m-0" class="quotation_view_50">{{@$company->email}}</p>
                                                            </div>
                                                        </div>

                                                    </td>
                                                    <td class="quotation_view_50 p-0" class="primary-color"> 
                                                        <div class="col-lg-12 ">
                                                            <div class="invoice-details-left">
                                                                <h2 class="text-uppercase text-center quotation_view_table_tr_td_h2">Sales Invoice</h2>                                                           
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">@lang('Doc Number'):</p>
                                                                    <p class="text-left  primary-color m-0">{{@$si->doc_number}}</p>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">@lang('Sales Order Date'):</p>
                                                                    <p class="text-left  primary-color m-0">{{date('jS M, Y', strtotime(@$si->si_date))}}</p>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">@lang('Customer Name') :</p>
                                                                    <?php @$supplier=App\SysChartofAccounts::where('id',$si->customer)->first(); ?>
                                                                    <p class="text-left  primary-color m-0">{{@$supplier->account_name}}<br />{{@$supplier->address != ""? @$supplier->address : ''}}</p>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">@lang('LPO Date'):</p>
                                                                    <p class="text-left  primary-color m-0">{{date('jS M, Y', strtotime(@$si->lpo_date))}}</p>
                                                                </div>                                                                
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">@lang('LPO Number'):</p>
                                                                    <p class="text-left  primary-color m-0">{{date('jS M, Y', strtotime(@$si->lpo_number))}}</p>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">@lang('Bill Date'):</p>
                                                                    <p class="text-left  primary-color m-0">{{date('jS M, Y', strtotime(@$si->bill_date))}}</p>
                                                                </div>                                                                
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">@lang('Bill Number'):</p>
                                                                    <p class="text-left  primary-color m-0">{{date('jS M, Y', strtotime(@$si->bill_number))}}</p>
                                                                </div>
                                                                {{-- <h2 class="text-uppercase text-center TotalAmount" >@lang('lang.total') @lang('lang.amount') {{ @$total_taxableamount }} </h2>  --}}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>  


                                    <hr>
             
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="quotation_view_table">
                                                <tr>
                                                    <td class="quotation_view_50">
                                                        <div class="col-lg-12 ">
                                                            <div class=" primary-color">
                                                                <h5 class="primary-color">@lang('VAT Details'):</h5>
                                                            </div>

                                                            <div class=" primary-color">
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">Supplier Type : </p>
                                                                    <p class="primary-color m-0">{{@$si->suppliertype->title}}</p>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">Sales Type : </p>
                                                                    <p class="primary-color m-0">{{@$si->sales_type}}</p>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">Supplier Country : </p>
                                                                    <p class="primary-color m-0">{{@$si->supplier_country}}</p>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">Supplier State : </p>
                                                                    <p class="primary-color m-0">{{@$si->supplier_state}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="quotation_view_50" style="vertical-align: top;">
                                                        <div class="col-lg-12 ">
                                                            <div class=" primary-color">
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">Payment Terms : </p>
                                                                    <p class="primary-color m-0">
                                                                       @if(@$si->payment_terms !=105) {{@$si->paymentterms->title}} @else {{@$si->payment_terms2}} @endif
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">Currency : </p>
                                                                    <p class="primary-color m-0">
                                                                        <?php @$cur=App\SysCurrencySettings::find($si->currency); ?>
                                                                        {{@$cur->code}}
                                                                    </p>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <p class="fw-500 w-25 primary-color m-0">Narration : </p>
                                                                    <p class="primary-color m-0">{{@$si->narration}}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <table class="quotation_view_table">
                                                <tr>
                                                    <td class="quotation_view_50">
                                                        <div class="col-lg-12 ">
                                                            <div class=" primary-color">
                                                                <h5 class="primary-color">@lang('Bill To Address'):</h5>
                                                            </div>

                                                            <div class=" primary-color">
                                                                <h5 class="primary-color">{{@$company->company_name}}</h5>
                                                                <p class="primary-color m-0" >{{@$company->company_address}}</p>
                                                                <p class="primary-color m-0" >{{@$company->telephone}}</p>
                                                                <p class="primary-color m-0" >{{@$company->email}}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="quotation_view_50">
                                                         @if(!empty(@$si->shipping_name ))
                                                        <div class="col-lg-12 ">
                                                            <div class=" primary-color">
                                                                <h5 class="primary-color">@lang('Ship To Address'):</h5>
                                                            </div>

                                                            <div class=" primary-color"> 
                                                                <h5 class="primary-color">{{@$si->shipping_name}}</h5>
                                                                <p class="primary-color m-0" >{{@$si->shipping_contact_no}}</p> 
                                                                <p class="primary-color m-0" >{{@$si->shipping_address_1}}</p> 
                                                                <p class="primary-color m-0" >{{@$si->shipping_address_2}}</p> 
                                                            </div>
                                                        </div>
                                                        @else
                                                            <div class="col-lg-12 ">
                                                                <div class=" primary-color">
                                                                    <h5 class="primary-color">@lang('Ship To Address'):</h5>
                                                                </div>

                                                                <div class=" primary-color">
                                                                    <h5 class="primary-color">{{@$company->company_name}}</h5>
                                                                    <p class="primary-color m-0" >{{@$company->company_address}}</p>
                                                                    <p class="primary-color m-0" >{{@$company->telephone}}</p>
                                                                    <p class="primary-color m-0" >{{@$company->email}}</p>
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </td>
                                                </tr>
                                                {{-- <tr>
                                                    <td colspan="2">
                                                        <div class="col-lg-12 ">
                                                            <p class="primary-color mt-40"> {{@$po->note}}   </p>
                                                        </div>
                                                    </td>
                                                </tr> --}}
                                            </table>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="row mt-30 mb-50">
                                        <div class="col-lg-12">
                                            <table class="sstable" cellspacing="0" width="100%" id="po-table">
                                                <thead>
                                                    <tr>
                                                        <th class="text-white" style="width:100px;">@lang('Part No')</th>
                                                        <th class="text-white" style="width:70px;">@lang('Tax')</th>
                                                        <th class="text-white" style="width:70px;">@lang('Qty')</th>
                                                        <th class="text-white" style="width:80px;">@lang('Unit Price')</th>
                                                        <th class="text-white" style="width:70px;">@lang('Value')</th>
                                                        <th class="text-white" style="width:70px;">@lang('Discount')</th>
                                                        <th class="text-white" style="width:130px;">@lang('Custom Charges')</th>
                                                        <th class="text-white" style="width:120px;">@lang('Taxable Amount')</th>
                                                        <th class="text-white" style="width:100px;">@lang('VAT Amount')</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                @php
                                                    $total_qty = 0;
                                                    $total_unitprice = 0;
                                                    $total_value=0;
                                                    $total_discount=0;
                                                    $total_customcharges=0;
                                                    $total_taxableamount=0;
                                                    $total_vatamount=0;
                                                @endphp

                                                @foreach($si_items as $value)
                                                
                                                <tr>
                                                    <?php @$partnumber=App\SmItem::where('id',$value->part_number)->first(); ?>
                                                    <td class="primary-color text-left">{{@$partnumber->part_number}}</td>
                                                    <td class="primary-color text-left">{{@$value->tax}}</td>
                                                    <td class="primary-color text-left">{{@$value->qty}} @php $total_qty=$total_qty+$value->qty; @endphp</td>
                                                    <td class="primary-color text-left">{{@$value->unitprice}} @php $total_unitprice=$total_unitprice+$value->unitprice; @endphp</td>
                                                    <td class="primary-color text-left">{{@$value->value}} @php $total_value=$total_value+$value->value; @endphp</td>
                                                    <td class="primary-color text-left">{{@$value->discount}} @php $total_discount=$total_discount+$value->discount; @endphp</td>
                                                    <td class="primary-color text-left">{{@$value->customcharges}} @php $total_customcharges=$total_customcharges+$value->customcharges; @endphp</td>
                                                    <td class="primary-color text-left">{{@$value->taxableamount}} @php $total_taxableamount=$total_taxableamount+$value->taxableamount; @endphp</td>
                                                    <td class="primary-color text-left">{{@$value->vatamount}} @php $total_vatamount=$total_vatamount+$value->vatamount; @endphp</td>
                                                </tr>
                                                @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                      <td></td>
                                                      <td></td>
                                                      <td class="text-white sstablefoot"><b>{{$total_qty}}</b></td>
                                                      <td class="text-white sstablefoot"><b>{{@App\SysHelper::com_curr_format($total_unitprice,2)}}</b></td>
                                                      <td class="text-white sstablefoot"><b>{{@App\SysHelper::com_curr_format($total_value,2)}}</b></td>
                                                      <td class="text-white sstablefoot"><b>{{@App\SysHelper::com_curr_format($total_discount,2)}}</b></td>
                                                      <td class="text-white sstablefoot"><b>{{@App\SysHelper::com_curr_format($total_customcharges,2)}}</b></td>
                                                      <td class="text-white sstablefoot"><b>{{@App\SysHelper::com_curr_format($total_taxableamount,2)}}</b></td>
                                                      <td class="text-white sstablefoot"><b>{{@App\SysHelper::com_curr_format($total_vatamount,2)}}</b></td>
                                                    </tr>
                                                  </tfoot>
                                            </table>
                                        </div>

                                    </div>

                                    <div class="row mb-20">
                                        <div class="col-lg-12">
                                            @if(!empty(@$si->note))
                                            <p class="primary-color m-0"><b>Note : </b></p>
                                            <p class="primary-color">{{@$si->note}}</p>
                                            <hr>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mt-30 mb-50">
                                        <div class="col-lg-12">
                                            <table class="sstable" cellspacing="0" width="100%" id="po-table">
                                                <thead>
                                                    <tr>
                                                        <th class="text-white" style="width:100px;">@lang('Name')</th>
                                                        <th class="text-white" style="width:350px;">@lang('Credit Account')</th>
                                                        <th class="text-white" style="width:70px;">@lang('Amount')</th>
                                                        <th class="text-white" style="width:70px;">@lang('Calculated Amount')</th>
                                                        <th class="text-white" style="width:80px;">@lang('Remarks')</th>
                                                        <th class="text-white" style="width:70px;">@lang('Currency')</th>
                                                        <th class="text-white" style="width:70px;">@lang('Exchange Rate')</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                @foreach($cfcharges as $value)
                                                
                                                <tr>
                                                    <td class="primary-color text-left">{{@$value->cfc_name}}</td>
                                                    <td class="primary-color text-left">{{@$value->cfccreditaccount->name}}</td>
                                                    <td class="primary-color text-left">{{@$value->cfc_amount}}</td>
                                                    <td class="primary-color text-left">{{@$value->cfc_cal_amount}}</td>
                                                    <td class="primary-color text-left">{{@$value->cfc_remarks}}</td>
                                                    <td class="primary-color text-left">{{@$value->cfc_currency}}</td>
                                                    <td class="primary-color text-left">{{@$value->cfc_exe_rate}}</td>
                                                </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>


                                     <div class="row">
                                        <div class="col-lg-12">
                                            @if(count(@$si_att)>0)
                                            <h5>Attachment : </h5>
                                                @foreach ($si_att as $att)
                                                    <div>
                                                        <a class="primary-btn fix-gr-bg" data-toggle="tooltip" title="Valid Till {{$att->validtill}}" target="_blank" href="{{asset(@$att->si_attach_file)}}">{{$att->file_name}} | {{$att->description}}</a>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                    </div>

                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg" onclick="location.href = '{{$si->id}}/print';">@lang('lang.print')</button>                                            
                                        </div>
                                      </div>
                                </div>
                            </div>
                        </div>
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
                <h4 class="modal-title">Add Attachment</h4>
                <button class="close" data-dismiss="modal" type="button">
                    ×
                </button>
            </div>
            <div class="modal-body m-0 p-3">
                <div class="container-fluid">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-add-attachment',
               'method' => 'POST', 'enctype' => 'multipart/form-data', 'onsubmit' => 'return validateAttachForm()']) }}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row mt-0">
                                <div class="col-lg-12" id="sibling_class_div">
                                    <div class="input-effect">
                                    <input id="po_id" name="si_id" type="hidden" value="{{$si->id}}">
                                        <input class="primary-input form-control" id="file_name" name="file_name" type="text">
                                            <label>File Name<span></span></label>
                                            <span class="focus-border"></span>
                                            <span class="modal_input_validation_1 red_alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-30">
                                <div class="col-lg-12" id="sibling_class_div">
                                    <div class="input-effect">
                                        <input class="primary-input form-control" id="description" name="description" type="text">
                                            <label>Description<span></span></label>
                                            <span class="focus-border"></span>
                                            <span class="modal_input_validation_2 red_alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-30">
                                <div class="col-lg-12" id="">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col">
                                            <div class="input-effect">
                                                <input autocomplete="off" class="read-only-input primary-input date form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" id="validtill" name="validtill" readonly="true" type="text" value="{{date('m/d/Y')}}">
                                                    <label>Valid Till<span></span></label>
                                                </input>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button class="" type="button">
                                                <i class="ti-calendar" id="start-date-icon">
                                                </i>
                                            </button>
                                        </div>
                                        <span class="modal_input_validation_3 red_alert"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-30">
                                <div class="col-lg-12" id="">
                                    <div class="no-gutters input-right-icon">
                                        <div class="col-10">
                                            <div class="input-effect">
                                                <input class="primary-input form-control" type="text" id="placeholderPOAttachFile" autocomplete="off" readonly="true" disabled>
                                            <span class="focus-border"></span>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <button class="" type="button">
                                                <label class="primary-btn small fix-gr-bg" for="po_attach_file">@lang('lang.browse')</label>
                                                <input type="file" class="d-none" name="si_attach_file" id="po_attach_file">
                                            </button>
                                        </div>
                                    </div>                                    
                                    <span class="modal_input_validation_4 red_alert"></span>
                                </div>
                            </div>


                            <div class="col-lg-12 text-center">
                                <div class="mt-40 d-flex justify-content-between">
                                    <button class="primary-btn tr-bg" data-dismiss="modal" type="button">
                                        @lang('lang.cancel')
                                    </button>
                                    <input class="primary-btn fix-gr-bg" type="submit" value="save">

                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

function validateAttachForm() {
    var file_name = $("#file_name").val();
    var description = $("#description").val();
    var validtill = $("#validtill").val();
    var po_attach_file = $("#placeholderPOAttachFile").attr('placeholder');

    if (file_name === "") {
        $('.modal_input_validation_1').show();
        $(".modal_input_validation_1").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_1").addClass("red_alert");
        return false;
    }
    if (description === "") {
        $('.modal_input_validation_2').show();
        $(".modal_input_validation_2").html("<font style='color:red;'>Must be Fill Up</font>");
        $("span.modal_input_validation_2").addClass("red_alert");
        return false;
    }
    if (validtill === "") {
        $('.modal_input_validation_3').show();
        $(".modal_input_validation_3").html("<font style='color:red;'>Must be Select</font>");
        $("span.modal_input_validation_3").addClass("red_alert");
        return false;
    }
    if (!po_attach_file) {
        $('.modal_input_validation_4').show();
        $(".modal_input_validation_4").html("<font style='color:red;'>Must be Choose</font>");
        $("span.modal_input_validation_4").addClass("red_alert");
        return false;
    }
    return true;
    preventDefault();
}
</script>

@endsection
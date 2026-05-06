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
<section class="sms-breadcrumb mb-20 white-box">
    <div class="container-fluid">
        <div class="row" style="float: left;">
            <h1>@lang('Purchase Order')</h1>
        </div>
            <div class="row" style="float: right;">
                <a href="{{route('user.dashboard')}}" class="top-btn-r-l"><i class="far fa fa-home" aria-hidden="true"></i> Home</a>
                <a href="{{url('purchase-order/create')}}" class="top-btn-r"><i class="far fa fa-plus" aria-hidden="true"></i> New</a>
                <a href="{{url('purchase-order')}}" class="top-btn-r"><i class="far fa fa-file-text" aria-hidden="true"></i> View</a>
                <a href="#" class="top-btn-r"><i class="far fa fa-print" aria-hidden="true"></i> Print</a>
                <a href="#" class="top-btn-r"><i class="far fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
                <a href="#" class="top-btn-r"><i class="far fa fa-floppy-o" aria-hidden="true"></i> Save</a>
                <a href="#" class="top-btn-r"><i class="far fa fa-trash" aria-hidden="true"></i> Delete</a>
                <a href="#" class="top-btn-r"><i class="far fa fa-tasks" aria-hidden="true"></i> Process</a>
                <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh" aria-hidden="true"></i> Refresh</a>
        </div>
    </div>
</section>
<hr style="margin-top: 33px;"/>
    <div style="clear: both;"></div>

<section class="sms-breadcrumb mb-20 white-box top-2">
    <div class="container-fluid">
        <div class="row">
            <div class="col-4">
                <img src="{{ asset($company->company_logo) }}" height="40px">                        
            </div>
            <div class="col-8" style="text-align: right;">
                <div class="top-2-text top-2-text-last"><span>{{ Auth::user()->full_name }}</span><br />Owner</div>
                <div class="top-2-text"><b>{{ date('m/d/Y') }}</b><br />Doc Date</div>
                <div class="top-2-text"><b>{{ 'POI-' . sprintf('%03d', @App\SysPurchaseOrder::max('id') + 1) }}</b><br />Doc Number</div>
            </div>
        </div>
    </div>
</section>


<section class="admin-visitor-area">
<div class="container-fluid p-0">
    
    <div class="row">
        <div class="col-lg-4">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-find', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                                <input class="primary-input form-control{{ $errors->has('item_code') ? ' is-invalid' : '' }}"
                                    type="text" name="po_number" autocomplete="off" value="{{old('po_number')}}" required>
                                <label>@lang('PURCHASE ORDER NUMBER') <span>*</span> </label>
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

    </div>

    <br />
    <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                   <div class="row">

                    
                    <div class="col-lg-4">
                        <div class="boxed-formctrl">
                            <div class="invoice-details-left">
                                <h5 class="primary-color">Purchase Order</h5>
                                <div class="d-flex">
                                    <p class="fw-500 w-25 primary-color m-0">@lang('Doc Number'):</p>
                                    <p class="text-left  primary-color m-0">{{@$po->doc_number}}</p>
                                </div>
                                <div class="d-flex">
                                    <p class="fw-500 w-25 primary-color m-0">@lang('Purchase Order Date'):</p>
                                    <p class="text-left  primary-color m-0">{{date('jS M, Y', strtotime(@$po->po_date))}}</p>
                                </div>
                                <div class="d-flex">
                                    <p class="fw-500 w-25 primary-color m-0">@lang('Vendor Name') :</p>
                                    <?php @$supplier=App\SmSupplier::where('id',$po->vendors)->first(); ?>
                                    <p class="text-left  primary-color m-0">{{@$supplier->supplier_name}}<br />{{@$supplier->supplier_address != ""? @$supplier->supplier_address : ''}}</p>
                                </div>
                                <div class="d-flex">
                                    <p class="fw-500 w-25 primary-color m-0">@lang('Delivery Date'):</p>
                                    <p class="text-left  primary-color m-0">{{date('jS M, Y', strtotime(@$po->delivery_date))}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="boxed-formctrl">
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
                                                    <p class="primary-color m-0">{{@$po->suppliertype->title}}</p>
                                                </div>
                                                <div class="d-flex">
                                                    <p class="fw-500 w-25 primary-color m-0">Purchase Type : </p>
                                                    <p class="primary-color m-0">{{@$po->purchase_type}}</p>
                                                </div>
                                                <div class="d-flex">
                                                    <p class="fw-500 w-25 primary-color m-0">Supplier Country : </p>
                                                    <p class="primary-color m-0">{{@$po->supplier_country}}</p>
                                                </div>
                                                <div class="d-flex">
                                                    <p class="fw-500 w-25 primary-color m-0">Supplier State : </p>
                                                    <p class="primary-color m-0">{{@$po->supplier_state}}</p>
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
                                                       @if(@$po->payment_terms !=105) {{@$po->paymentterms->title}} @else {{@$po->payment_terms2}} @endif
                                                    </p>
                                                </div>
                                                <div class="d-flex">
                                                    <p class="fw-500 w-25 primary-color m-0">Currency : </p>
                                                    <p class="primary-color m-0">
                                                        <?php @$cur=App\SysCurrencySettings::find($po->currency); ?>
                                                        {{@$cur->code}}
                                                    </p>
                                                </div>
                                                <div class="d-flex">
                                                    <p class="fw-500 w-25 primary-color m-0">Narration : </p>
                                                    <p class="primary-color m-0">{{@$po->narration}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="boxed-formctrl"></div>
                    </div>




                        <div class="col-lg-12"> 

                            <div class="row" id="purchaseInvoice">
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
                                                            
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>  


                                    <hr>
             
                                    <div class="row">
                                        <div class="col-lg-12">
                                            
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
                                                         @if(!empty(@$po->shipping_name ))
                                                        <div class="col-lg-12 ">
                                                            <div class=" primary-color">
                                                                <h5 class="primary-color">@lang('Ship To Address'):</h5>
                                                            </div>

                                                            <div class=" primary-color"> 
                                                                <h5 class="primary-color">{{@$po->shipping_name}}</h5>
                                                                <p class="primary-color m-0" >{{@$po->shipping_contact_no}}</p> 
                                                                <p class="primary-color m-0" >{{@$po->shipping_address_1}}</p> 
                                                                <p class="primary-color m-0" >{{@$po->shipping_address_2}}</p> 
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
                                                        <th style="width:100px;">@lang('Part No')</th>
                                                        <th style="width:70px;">@lang('Tax')</th>
                                                        <th style="width:70px;">@lang('Qty')</th>
                                                        <th style="width:80px;">@lang('Unit Price')</th>
                                                        <th style="width:70px;">@lang('Value')</th>
                                                        <th style="width:70px;">@lang('Discount')</th>
                                                        <th style="width:130px;">@lang('Custom Charges')</th>
                                                        <th style="width:120px;">@lang('Taxable Amount')</th>
                                                        <th style="width:100px;">@lang('VAT Amount')</th>
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

                                                @foreach($po_items as $value)
                                                
                                                {{-- @php     
                                                    $productDetail = App\Smquotation::productDetail(@$value->product_id,@$quotation->id); 
                                                    $str = !empty(@$productDetail)? @$productDetail->denomination:"()";
                                                    $str = str_replace("(","",@$str);
                                                    @$denomination = str_replace(")","",$str); 
                                                    @$total_subtotal = @$total_subtotal+ @$value->qnt * @$value->unit_price;

                                                @endphp --}}

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
                                                    {{-- <tr>
                                                        <td></td>
                                                        @if(@$quotation->work_order_mode=="equipment")
                                                        <td></td>
                                                        @else
                                                        <td></td>
                                                        <td></td>
                                                        @endif
                                                        <td></td>
                                                        <td></td>
                                                        <td class="fw-600 primary-color text-right">@lang('lang.sub') @lang('lang.total') </td>
                                                        <td class="fw-600 primary-color text-right">
                                                        {{@$currency_symbol}}{{App\User::NumberToBangladeshiTakaFormat(@$total_subtotal)}} 
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        @if(@$quotation->work_order_mode=="equipment")
                                                        <td></td>
                                                        @else
                                                        <td></td>
                                                        <td></td>
                                                        @endif
                                                        <td></td>
                                                        <td></td>
                                                        <td class="fw-600 primary-color text-right">Discount ({{@$quotation->discount_type != ""? (@$quotation->discount_type == "P"? ' %': ' fixed'):'' }})</td>
                                                        <td class="fw-600 primary-color text-right">
                                                        {{@$quotation->discount_amount != ""?  App\User::NumberToBangladeshiTakaFormat(@$quotation->discount_amount): "0.00" }} 
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        @if(@$quotation->work_order_mode=="equipment")
                                                        <td></td>
                                                        @else
                                                        <td></td>
                                                        <td></td>
                                                        @endif
                                                        <td></td>
                                                        <td></td>
                                                        <td class="fw-600 primary-color text-right">@lang('lang.total') @lang('lang.amount')</td>
                                                        <td class="fw-600 primary-color text-right">
                                                        {{@$currency_symbol}}{{App\User::NumberToBangladeshiTakaFormat( @$quotation->amount)}}
                                                        </td>
                                                    </tr> --}}
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                      <td></td>
                                                      <td></td>
                                                      <td class="sstablefoot"><b>{{$total_qty}}</b></td>
                                                      <td class="sstablefoot"><b>{{@App\SysHelper::com_curr_format($total_unitprice,2)}}</b></td>
                                                      <td class="sstablefoot"><b>{{@App\SysHelper::com_curr_format($total_value,2)}}</b></td>
                                                      <td class="sstablefoot"><b>{{@App\SysHelper::com_curr_format($total_discount,2)}}</b></td>
                                                      <td class="sstablefoot"><b>{{@App\SysHelper::com_curr_format($total_customcharges,2)}}</b></td>
                                                      <td class="sstablefoot"><b>{{@App\SysHelper::com_curr_format($total_taxableamount,2)}}</b></td>
                                                      <td class="sstablefoot"><b>{{@App\SysHelper::com_curr_format($total_vatamount,2)}}</b></td>
                                                    </tr>
                                                  </tfoot>
                                            </table>
                                        </div>

                                    </div>

                                     <div class="row mb-20">
                                        <div class="col-lg-12">
                                            @if(!empty(@$po->note))
                                            <p class="primary-color m-0"><b>Note : </b></p>
                                            <p class="primary-color">{{@$po->note}}</p>
                                            <hr>
                                            @endif
                                        </div>
                                    </div>

                                     <div class="row">
                                        <div class="col-lg-12">
                                            @if(count(@$po_att)>0)
                                            <h5>Attachment : </h5>
                                                @foreach ($po_att as $att)
                                                    <div>
                                                        <a class="primary-btn fix-gr-bg" data-toggle="tooltip" title="Valid Till {{$att->validtill}}" target="_blank" href="{{asset(@$att->po_attach_file)}}">{{$att->file_name}} | {{$att->description}}</a>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                        {{-- <div class="col-lg-12">
                                            <table>
                                                <tr>
                                                    <td class="shipment_qorkorder">
                                                        <h5></h5> 
                                                        <ul>  
                                                            

                                                            @if(!empty(@$quotation->shipment_work_order_date))
                                                                <li>@lang('lang.Shipped_On')  <b>{{ date('jS M, Y',strtotime(@$quotation->shipment_work_order_date)) }}</b></li>
                                                            @endif 
                         


                                                            @if(!empty(@$quotation->status_delivery_date))
                                                                <li>@lang('lang.Delivered_On') <b>{{ date('jS M, Y',strtotime(@$quotation->status_delivery_date)) }}</b> by CR# <b>{{@$quotation->status_cr}}</b> to the <b>{{@$quotation->status_destination}}</b></li>
                                                            @endif
                                                            @if(!empty(@$quotation->inspection_completion_date))
                                                                <li>@lang('lang.Inspection_Completed_On') <b>{{ date('jS M, Y',strtotime(@$quotation->inspection_completion_date)) }}</b></li>
                                                            @endif
                          
                                                            @if(!empty(@$quotation->completion_date))
                                                                <li>@lang('lang.QUOTATION_Completed_On') <b>{{ date('jS M, Y',strtotime(@$quotation->completion_date)) }}</b>, Paid Through Cheque No. <b>{{@$quotation->cheque_no}}</b> of <b>{{@$quotation->bank_name}}</b> </li>
                                                            @endif
                                                        </ul>
                                                        

                                                    </td>
                                                    <td class="shipment_qorkorder_td">
                                                        <h5>@lang('lang.Generated_By')</h5>
                                                        <hr>
                                                        @lang('lang.staff') @lang('lang.id'): {{@$quotation->created_by}} <br>
                                                        @lang('lang.name'): {{App\User::getUserDetails(@$quotation->created_by)}} <br>
                                                        @lang('lang.designation'): {{App\User::getUserDesignation(@$quotation->created_by)}} <br>

                                                    </td>
                                                </tr>
                                            </table> 
                                        </div> --}}
                                    </div>

                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            {{-- <button class="primary-btn fix-gr-bg" onclick="javascript:printDiv('purchaseInvoice')" id="printButton">@lang('lang.print')</button> --}}
                                            <button class="primary-btn fix-gr-bg" onclick="location.href = '{{$po->id}}/print';">@lang('lang.print')</button>                                            
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
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'purchase-order-add-attachment',
               'method' => 'POST', 'enctype' => 'multipart/form-data', 'onsubmit' => 'return validateAttachForm()']) }}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row mt-0">
                                <div class="col-lg-12" id="sibling_class_div">
                                    <div class="input-effect">
                                    <input id="po_id" name="po_id" type="hidden" value="{{$po->id}}">
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
                                                <input type="file" class="d-none" name="po_attach_file" id="po_attach_file">
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
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
            <h1>@lang('lang.quotation') @lang('lang.details')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.quotation') </a>
                <a href="#">@lang('lang.quotation') @lang('lang.details')</a>
            </div>
        </div>
    </div>
</section>


<section class="admin-visitor-area">
<div class="container-fluid p-0">
    <div class="row">
            <div class="offset-lg-2 col-lg-8">
                <div class="white-box">
                   <div class="row mt-40">
                        <div class="col-lg-12"> 

                            <div class="row" id="purchaseInvoice">
                                <div class="container-fluid">
                                    <div class="row mb-20">
                                        <div class="col-lg-12">
                                            <table class="quotation_view_table" >
                                                <tr>
                                                    <td class="quotation_view_table_tr_td"> 
                                                        <div class="col-lg-12 ">
                                                            <img src="{{asset(@$logo)}}"  class="quotation_view_table_tr_img">
                                                            <div class="business-info text-left">
                                                                <h3 class="mt-10 primary-color">{{@$generalSetting->company_name}}</h3>
                                                                <p class="mt-0 primary-color" class="quotation_view_50">{{@$generalSetting->address}}</p>
                                                            </div>
                                                        </div>

                                                    </td>
                                                    <td class="quotation_view_50 p-0" class="primary-color"> 
                                                        <div class="col-lg-12 ">
                                                            <div class="invoice-details-right">
                                                                <h2 class="text-uppercase text-center quotation_view_table_tr_td_h2" >@lang('lang.quotation') @lang('lang.details')</h2>

                                                             

                                                                <div class="d-flex  invoice-details-content">
                                                                    <p class="fw-500 primary-color">@lang('lang.quotation') @lang('lang.title'):</p>
                                                                    <p class="text-left  primary-color">{{@$quotation->title}}</p>
                                                                </div>
                                                                <div class="d-flex  invoice-details-content">
                                                                    <p class="fw-500 primary-color">@lang('lang.quotation') @lang('lang.no_'):</p>
                                                                    <p class="text-left  primary-color">{{@$quotation->number}}</p>
                                                                </div>
                                                                <div class="d-flex  invoice-details-content">
                                                                    <p class="fw-500 primary-color">@lang('lang.quotation') @lang('lang.date'):</p>
                                                                    <p class="text-left  primary-color">{{date('jS M, Y', strtotime(@$quotation->date))}}</p>
                                                                </div>
                                                                <div class="d-flex  invoice-details-content">
                                                                    <p class="fw-500 primary-color">@lang('lang.quotation') @lang('lang.reference')</p>
                                                                    <p class="text-left  primary-color">{{@$quotation->reference}}</p>
                                                                </div> 
                                                                <h2 class="text-uppercase text-center TotalAmount" >@lang('lang.total') @lang('lang.amount') {{@$currency_symbol}}{{App\User::NumberToBangladeshiTakaFormat( @$quotation->amount)}} </h2> 
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
                                                                    
                                                         @if(!empty(@$quotation->customer ))  
                                                        <div class="col-lg-12 ">
                                                            <div class=" primary-color">
                                                                <h5 class="primary-color">@lang('lang.Bill_To'):</h5>
                                                            </div>

                                                            <div class=" primary-color">
                                                                <h5 class="primary-color">{{@$quotation->customer != ""? @$quotation->customer->full_name : ''}}</h5>
                                                                <p class="primary-color quotation_view_60" >{{@$quotation->customer != ""? @$quotation->customer->current_address : ''}}</p>
                                                            </div>
                                                        </div>
                                                        @endif

                                                    </td>
                                                    <td class="quotation_view_50">
                                                         @if(!empty(@$quotation->vendor_id ))     
                                                        <div class="col-lg-12 ">
                                                            <div class=" primary-color">
                                                                <h5 class="primary-color">@lang('lang.vendor'):</h5>
                                                            </div>

                                                            <div class=" primary-color"> 
                                                                <h5 class="primary-color">{{@$quotation->vendor_id != ""? App\User::getVendorName(@$quotation->vendor_id) : ''}}</h5>
                                                                <p class="primary-color quotation_view_60" >{{@$quotation->vendor_id != ""? App\User::getVendorAddress(@$quotation->vendor_id) : ''}}</p> 
                                                            </div>
                                                        </div>
                                                        @endif

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <div class="col-lg-12 ">
                                                            <p class="primary-color mt-40"> {{@$quotation->description}}   </p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>

                                            

                                        </div>
                                    </div>

                                    <hr>

                                    <div class="row mt-30 mb-50">
                                        <div class="col-lg-12">
                                            <table class="d-table table-responsive custom-table" cellspacing="0" width="100%" >
                                                <thead>
                                                    <tr>
                                                        <th class="primary-color text-left">@lang('lang.product_name')</th>
                                                    @if(@$quotation->work_order_mode=="equipment")
                                                        <th class="primary-color text-center">@lang('lang.product') @lang('lang.model')</th>
                                                    @else
                                                        <th class="primary-color text-center">@lang('lang.part_number')</th>
                                                        <th class="primary-color text-center">@lang('lang.new_part_number')</th>
                                                    @endif
                                                        <th class="primary-color text-center">@lang('lang.denomination')</th>
                                                        <th class="primary-color text-center">@lang('lang.quantity')</th>
                                                        <th class="primary-color text-right">@lang('lang.sale') @lang('lang.price') ({{@$currency_symbol}})</th>
                                                        <th class=" primary-color text-right">@lang('lang.sub') @lang('lang.total') ({{@$currency_symbol}})</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                @php $grand_total = 0; $sub_total = 0; $total_subtotal=0; @endphp

                                                @foreach($quotation->quotationProducts as $value)

                                                @php     
                                                    $productDetail = App\Smquotation::productDetail(@$value->product_id,@$quotation->id); 
                                                    $str = !empty(@$productDetail)? @$productDetail->denomination:"()";
                                                    $str = str_replace("(","",@$str);
                                                    @$denomination = str_replace(")","",$str); 
                                                    @$total_subtotal = @$total_subtotal+ @$value->qnt * @$value->unit_price;

                                                @endphp

                                                <tr>
                                                    <td class="primary-color text-left">{{@$value->product->productDetail != ""? @$value->product->productDetail->item_name:""}}</td>

                                                    @if($quotation->work_order_mode=="equipment")
                                                    <td class="primary-color text-center">{{@$productDetail != ""? @$productDetail->productModel:""}}</td>
                                                    @else
                                                    <td class="primary-color text-center">{{@$productDetail != ""? @$productDetail->part_number:""}}</td>
                                                    <td class="primary-color text-center">{{@$productDetail != ""? @$productDetail->new_part_number:""}}</td>
                                                    @endif

                                                    <td class="primary-color text-center">{{@$denomination}}</td>
                                                    <td class="primary-color text-center">{{@$productDetail != ""? @$productDetail->qnt:""}}</td>
                                                    <td class="primary-color text-right">{{ @$productDetail != ""? App\User::NumberToBangladeshiTakaFormat(@$productDetail->sale_price):"0.00"}}</td>
                                                    <td class="primary-color text-right"> {{App\User::NumberToBangladeshiTakaFormat(@$value->qnt * $value->unit_price)}}
                                                    </td>
                                                </tr>
                                                @endforeach
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
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                     <div class="row mb-20">
                                        <div class="col-lg-12">
                                            @if(!empty(@$quotation->note))
                                            <p class="primary-color">{{@$quotation->note}}</p>
                                            <hr>
                                            @endif
                                        </div>
                                    </div>

                                     <div class="row">
                                        <div class="col-lg-12">
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
                                        </div>
                                    </div>

                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">
                                            <button class="primary-btn fix-gr-bg" onclick="javascript:printDiv('purchaseInvoice')" id="printButton">@lang('lang.print')</button>
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
@endsection
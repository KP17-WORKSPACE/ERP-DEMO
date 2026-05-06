@extends('backEnd.master')
@section('mainContent')
@php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}


    $modules = array_unique(@$modules);


    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol;
    if(isset($generalSetting->logo)){  $logo = @$generalSetting->logo;  }
    else{ @$logo = 'public/uploads/settings/logo.png'; }

    $sm_staff= App\SmStaff::where('user_id',Auth::user()->id)->first();
    if(!empty(@$sm_staff)){
        @$profile_image = @$sm_staff->staff_photo;
        if(empty(@$profile_image)){
            @$profile_image ='public/uploads/staff/staff1.jpg';
        }
    }
@endphp
<link rel="stylesheet" href="{{ asset('/public/css/invoiceView.css') }}">
<link href="{{asset('packages/spondonit/invoice/src/public/invoice_view.css')}}" type="text/css" rel="stylesheet">

<style>

table, thead, th, tr, td {
    color: #415094 !important;
    -webkit-print-color-adjust: exact;
    width: 100%;
}
#purchaseInvoice{
    margin-left: 20px;
    margin-right: 20px;
}
</style>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.invoice') @lang('lang.view')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="{{ url('infix/invoice-list')  }}">@lang('lang.invoice') @lang('lang.list')</a>
                <a href="#">@lang('lang.invoice') @lang('lang.view')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area overflow-hidden">
<div class="container-fluid p-0">
    <div class="row justify-content-center w_100">
            <div class="col-lg-10">
                <div class="white-box">
                <div class="row p-3" id="purchaseInvoice">
                    <div class="container-fluid">
                        <div class="row mb-20">
                            <table style="width: 100%">
                                <tr>
                                    <td style="width: 70%; vertical-align: top; text-align: left;">
                                        <img src="{{asset($logo)}}">
                                        <div class="business-info text-left">
                                            <h3 class="mt-10 primary-color">{{@$generalSetting->school_name}}</h3>
                                            <p class="mt-0 primary-color">{{@$generalSetting->address}}</p>
                                        </div>
                                    </td>
                                    <td style="width: 30%" class="primary-color">


                                        <div class="col-lg-12 p-0">
                                            <div class="invoice-details-right">
                                                <h2 class="text-uppercase text-center">@lang('lang.invoice') @lang('lang.details')</h2>
                                                <div class="d-flex  invoice-details-content">
                                                    <p class="fw-500 primary-color">@lang('lang.invoice') @lang('lang.no_'):</p>
                                                    <p class="text-left  primary-color" >{{@$invoice_setting->prefix.@$invoice->invoice_number}}</p>
                                                </div>
                                                <div class="d-flex  invoice-details-content">
                                                    <p class="fw-500 primary-color">@lang('lang.invoice') @lang('lang.date'):</p>
                                                    <p class="text-left  primary-color">{{@$invoice->invoice_date != ""? date('jS M, Y', strtotime(@$invoice->invoice_date)):""}}</p>
                                                </div>
                                                <div class="d-flex  invoice-details-content">
                                                    <p class="fw-500 primary-color">@lang('lang.invoice') @lang('lang.due') @lang('lang.date'):</p>
                                                    <p class="text-left  primary-color">{{@$invoice->invoice_due_date != ""? date('jS M, Y', strtotime(@$invoice->invoice_due_date)):""}}</p>
                                                </div>
                                                <div class="d-flex  invoice-details-content">
                                                    <p class="fw-500 primary-color">@lang('lang.payment') @lang('lang.method'):</p>
                                                    <p class="text-left  primary-color">{{@$invoice->paymentMethod != ""? @$invoice->paymentMethod->method:''}}</p>
                                                </div>
                                                <div class="d-flex  invoice-details-content">
                                                    <p class="fw-500 primary-color">@lang('lang.payment') @lang('lang.status'):</p>
                                                    <p class="text-left  primary-color">

                                                            @if(@$invoice->payment_status == "UP")
                                                                {{'Unpaid'}}
                                                            @elseif(@$invoice->payment_status == "P")
                                                                {{'Paid'}}
                                                            @elseif(@$invoice->payment_status == "PP")
                                                                {{'Paid'}}
                                                            @elseif(@$invoice->payment_status == "PR")
                                                            {{'Proforma'}}
                                                            @endif

                                                    </p>
                                                </div>
                                                <div class="d-flex  invoice-details-content">
                                                    <p class="fw-500 primary-color">@lang('lang.reccuring') @lang('lang.cycle'):</p>
                                                    <p class="text-left  primary-color">

                                                            @if(@$invoice->recurring_cycle == "M")
                                                                {{'Monthly'}}
                                                            @elseif(@$invoice->recurring_cycle == "Q")
                                                                {{'Quarterly'}}
                                                            @elseif(@$invoice->recurring_cycle == "SA")
                                                                {{'Semi Annually'}}
                                                            @elseif(@$invoice->recurring_cycle == "A")
                                                            {{'Annually'}}
                                                            @elseif(@$invoice->recurring_cycle == "OT")
                                                            {{'Once Time'}}
                                                            @endif

                                                    </p>
                                                </div>
                                                @php
                                                    $amount = Spondonit\Invoice\Models\InfixInvoice::amountInvoice(@$invoice->id, @$invoice->discount_type, @$invoice->discount_amount, @$invoice_setting->tax_type, @$invoice_setting->tax);
                                                //    $h_total_amount= number_format($amount,2);
                                                @endphp
                
                                                 <h3 class="text-uppercase text-center TotalAmount" style="padding: 5px 15px 5px 5px;text-align: right !important; height:auto !important">@lang('lang.total') @lang('lang.amount') {{@$invoice->currency->code}} {{ @$amount }} </h3> 
               
               
                                                </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>


                        <hr>


                        <div class="row">
                            <div class="col-lg-12">


                                    <table align="center" style="padding-top: 0; width: 100%;" >
                                        <tr>
                                            <td style="width: 500px; float: left; text-align: left;">
                                                <div class=" primary-color">
                                                    <h2 class="primary-color" style="font-size: 12px; text-transform: uppercase;">@lang('lang.Bill_To'):</h2>
                                                </div>
                                                <div class=" primary-color">
                                                    <h3 class="primary-color" style="font-size: 16px;margin-bottom: -5px; ">{{@$invoice->customer != ""? @$invoice->customer->full_name : ''}}</h3>
                                                    <p class="primary-color address" style="font-weight: 300; font-size: 14px;">{{@$invoice->customer != ""? @$invoice->customer->current_address : ''}}</p>
                                                </div>
                                            </td>
                                            <td style="width: 50%; text-align: left;">
                                            </td>
                                        </tr>
                                        @if(!empty(@$invoice->public_note))
                                        <tr>
                                            <td align="left"><p style="width: 95%; font-weight: 300">{{@$invoice->public_note}}</p></td>
                                        </tr>
                                        @endif
                                    </table>


                            </div>


                        </div>

                        <hr>

                        <div class="row mt-30 mb-50">

<style>
   
thead.invoice_head td {
    color: #fff !important;
    border-radius: 1;
    font-size: 12px;
    border-right: 1px solid #ddd;
    padding: 5px;
}
thead.invoice_head {
    background: #415094;
    color: #fff !important;
}
</style>

    <table text-align="center" style="padding-top: 0; width: 100%; border-spacing: 0;" >
        <thead class="invoice_head" >
            <tr style="text-align: center;">
                <td class="primary-color" style="width: 5%; text-align: center !important; font-weight: bold; border-bottom: 1px solid #ddd;">@lang('lang.sl')</td>
                <td class="primary-color" style="width: 20%; text-align: center !important; font-weight: bold; border-bottom: 1px solid #ddd;">@lang('lang.product') @lang('lang.name')</td>
                <td class="primary-color" style="width: 20%; text-align: center !important; font-weight: bold; border-bottom: 1px solid #ddd;">@lang('lang.description')</td>
                <td class="primary-color" style="width: 5%; text-align: center !important; font-weight: bold; border-bottom: 1px solid #ddd;">@lang('lang.quantity')</td>
                <td class="primary-color" style="width: 20%; text-align: center !important; font-weight: bold; border-bottom: 1px solid #ddd;">@lang('lang.unit') @lang('lang.price') ({{@$invoice->currency->code}})</td>
                <td class="primary-color" style="width: 20%; text-align: center !important; font-weight: bold; border-bottom: 1px solid #ddd;">@lang('lang.total') ({{@$invoice->currency->code}})</td>
            </tr>
        </thead>

        <tbody class="table_body" style="">
            @php $grand_total = 0; $sub_total = 0; $serial=1; @endphp
            @foreach($invoice->invoiceProducts as $value)
            <tr>
                <td style="width: 5%; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd; border-left: 1px solid #ddd;" class="primary-color">{{@$serial++}}</td>
                <td style="width: 30%; border-bottom: 1px solid #ddd;border-right: 1px solid #ddd; text-align: center !important; " class="primary-color">{{@$value->productDetail != ""? @$value->productDetail->item_name:""}}</td>
                <td style="width: 30%; border-bottom: 1px solid #ddd;border-right: 1px solid #ddd; text-align: center !important; " class="primary-color">{{@$value->description}}</td>
                <td style="width: 5%; border-bottom: 1px solid #ddd;border-right: 1px solid #ddd; text-align: center !important; " class="primary-color"> {{@$value->quantity}}</td>
                <td style="width: 10%; border-bottom: 1px solid #ddd;border-right: 1px solid #ddd; text-align: center !important; " class="primary-color"> {{number_format(@$value->price, 2)}}</td>
                <td style="width: 20%; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd; " class="primary-color"> {{number_format(@$value->price * @$value->quantity, 2)}}</td>
            </tr>
            @endforeach
            <tr>
                <td class="fw-600 primary-color" colspan="5" style="text-align: right; border: 1px solid #ddd;  font-weight: bold;">
                    Sub Total ({{@$invoice->currency->code}}):
                </td>
                <td class="primary-color" style="text-align: right; border-right: 1px solid #ddd; ">
                    
                @php
                    @$TotalamountInvoice = Spondonit\Invoice\Models\InfixInvoice::TotalamountInvoice(@$invoice->id, @$invoice->discount_type, @$invoice->discount_amount);
                    echo number_format(@$TotalamountInvoice,2);
                @endphp
                </td>
            </tr>
            @php
                $tax = Spondonit\Invoice\Models\InfixInvoice::taxInvoice(@$invoice->id, @$invoice->discount_type, @$invoice->discount_amount, @$invoice_setting->tax_type, @$invoice_setting->tax);
            @endphp
            @if ($tax>1)
                
            
            <tr>
                <td class="fw-600 primary-color" colspan="5" style="text-align: right; border: 1px solid #ddd; border-top: 1px solid #ddd; font-weight: bold;">TAX/VAT/GST ({{@$invoice->currency->code}}) </td>
                <td class="primary-color" style=" border-right: 1px solid #ddd;    border-top: 1px solid #ddd; text-align: right;">
                    
                @php

                    
                    echo number_format(@$tax,2);

                @endphp
                {{-- ({{$invoice->currency !=""? $invoice->currency->code:$currency_code}}) --}}
                </td>
            </tr>
            @endif
            @if ($invoice->discount_amount>0)
            <tr>
                <td class="fw-600 primary-color" colspan="5" style="text-align: right; border: 1px solid #ddd;     border-top: 1px solid #ddd; font-weight: bold;">Discount ({{@$invoice->discount_type != ""? (@$invoice->discount_type == "P"? ' %': ' fixed'):'' }})({{@$invoice->currency->code}})
                </td>
                <td class="primary-color" style=" border-right: 1px solid #ddd;    border-top: 1px solid #ddd; text-align: right;">
              {{@$invoice->discount_amount != ""?  number_format( (float) @$invoice->discount_amount, 2, '.', ''): "0.00" }}
                {{-- ({{$invoice->currency !=""? $invoice->currency->code:$currency_code}}) --}}
                </td>
            </tr>
            @endif
            
            <tr>
               <td class="fw-600 primary-color"  colspan="5" style="text-align: right; border: 1px solid #ddd;   border-top: 1px solid #ddd; font-weight: bold;">Grand Total({{@$invoice->currency->code}}):</td>
               <td class="primary-color" style=" border-right: 1px solid #ddd;   border-top: 1px solid #ddd; text-align: right;">
                
                @php
                    $amount = Spondonit\Invoice\Models\InfixInvoice::amountInvoice(@$invoice->id, @$invoice->discount_type, @$invoice->discount_amount, @$invoice_setting->tax_type, @$invoice_setting->tax);
                    echo number_format(@$amount,2);
                @endphp
                {{-- ({{$invoice->currency !=""? $invoice->currency->code:$currency_code}}) --}}
                </td>
            </tr>
            <tr>
               <td class="fw-600 primary-color"  colspan="5" style="text-align: right; border: 1px solid #ddd;   border-top: 1px solid #ddd; font-weight: bold;">Paid({{@$invoice->currency->code}}):</td>
               <td class="primary-color" style="  border-right: 1px solid #ddd;  border-top: 1px solid #ddd; text-align: right;">
                
                @php

                    if(@$invoice->payment_status == "UP"){
                        echo number_format(0,2);
                    }elseif(@$invoice->payment_status == "P"){
                        echo number_format(@$amount,2);
                    }else{
                        echo number_format(@$invoice->partial_paymemt,2);
                    }


                @endphp
                {{-- ({{$invoice->currency !=""? $invoice->currency->code:$currency_code}}) --}}
                </td>
            </tr>
            <tr>
               <td class="fw-600 primary-color"  colspan="5" style="text-align: right; border: 1px solid #ddd; border-bottom: 1px solid #ddd;  border-top: 1px solid #ddd; font-weight: bold; margin-bottom: 40px;">Unpaid( {{@$invoice->currency->code}}):</td>
               <td class="primary-color" style="  border-right: 1px solid #ddd;  border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; text-align: right; margin-bottom: 40px;">
               
                 @php

                    if(@$invoice->payment_status == "UP"){
                        echo number_format(@$amount,2);
                    }elseif(@$invoice->payment_status == "P"){
                        echo number_format(0,2);
                    }else{
                        echo number_format(@$amount - @$invoice->partial_paymemt,2);
                    }


                @endphp
                {{-- ({{$invoice->currency !=""? $invoice->currency->code:$currency_symbol}}) --}}
                </td>
            </tr>



        @if(!empty(@$invoice->terms_note))
            <tr>
                <td colspan="6" style="text-align: left;">
                   <div style="width: 100%">
                    <h2 class="primary-color" style="font-size: 12px; text-transform: uppercase; margin-top: 40px; ">Terms:</h2>
                    <p style="font-weight: 300;">{{@$invoice->terms_note}}</p>
                   </div>
                </td>
           </tr>
        @endif

        </tbody>
    </table>

                <table width="1000px" align="center" style="height: 300px; padding: 15px;">
                    <tr>
                        <td style="width: 5%;"></td>
                        <td style="width: 30%">
                                <h2 class="primary-color" style="font-size: 12px; text-transform: uppercase; margin-top: 40px; ">Generated By:</h2>
                            <b>{{@$invoice->signature_person}}</b>
                            <br>
                            <span style="text-transform: uppercase;"> {{@$invoice->signature_company}} </span>

                        </td>
                    </tr>
                </table>



                    <div class="col-lg-12 text-center">
                        <a href="{{url('infix/invoice-generate', @$invoice->id)}}" class="primary-btn fix-gr-bg" target="_blank">Print</a>
                    </div>
                  </div>
    </div>
</div>
</div>
</div>
</section>
@endsection

@section('mainContent') 
@php 
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get(); 
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}

 
    $modules = array_unique(@$modules);


    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol;
    // dd($currency_symbol);
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
<!DOCTYPE html>
<head>
    <title>@lang('lang.invoice')  @lang('lang.generate') @lang('lang.view')</title>
</head>
<style>
    .table_body{
        margin-top: 5px;
    }
    .table_body td th{
        font-weight:5px !important;
        font-family:'Poppins', sans-serif !important;

    }
    body{
        font-family:'Poppins', sans-serif;
    }
    tr{
        font-family:'Poppins', sans-serif !important;
    }
    td{
        font-size: 10px;
        font-family:'Poppins', sans-serif !important;
    }
    th{
        font-size: 11px;
        font-family:'Poppins', sans-serif !important;
        padding: 5px;
    }
    .amount{
        padding-right:25px !important;
        text-align: right !important;
    }
    .item_name{
        padding-left: 25px !important;
    }
    td{
        border: 1px solid white;
    }
    .right_border{
    /* border-right: 1px solid white; */
    /* font-size: 12px !important; */

}
</style>
{{-- <link rel="stylesheet" href="{{ asset('/public/css/invoiceGenerate.css') }}" /> --}}
<body>
    <table align="center" style="padding-bottom: 0px; width: 100%;">
        <tr>
            <td valign="top" colspan="3" style="width:50%" align="left"> 
                <img width="150px" height="50px" src="{{asset($logo)}}">
                <div class="business-info text-left">
                    <h3 class="mt-10 primary-color erp_name">{{@$generalSetting->school_name}}</h3>
                    <p class="mt-0 primary-color address" style="width:60%">{{@$generalSetting->address}}</p>
                </div> 

                <div class=" primary-color mt-10" style="">
                    <h2 class="primary-color" style="font-size: 12px; text-transform: uppercase; margin-top: 40px;">{{ __('Bill To') }}:</h2>
                </div>
                <div class=" primary-color">
                    <h3 class="primary-color" style="font-size: 12px;margin-bottom: -5px; ">{{@$invoice->customer != ""? @$invoice->customer->full_name : ''}}</h3>
                    <p class="primary-color address" style="width:60%">{{@$invoice->customer != ""? @$invoice->customer->current_address : ''}}</p>
                </div> 


            </td>
            <td colspan="2" style="float: right; width: 50%; text-align: left;" >
                <h2 class="text-uppercase text-center primary-color" style="text-align:right;font-size: 24px; font-weight: 500; margin-bottom: 14px;  font-family:'Poppins', sans-serif !important;">{{ __('Invoice Details') }}</h2>
                <table style="  width: 100%; padding: 0px; text-align:right">
                
           
                    <tr>
                        <td class="invoice_left_top_bar primary-color">{{ __('invoice No.:') }}</td>
                        <td class="invoice_right_top_bar primary-color"> {{@$invoice_setting->prefix.@$invoice->invoice_number}}</td>
                    </tr>
                    <tr>
                        <td class="invoice_left_top_bar primary-color">{{ __('Invoice Date:') }}</td>
                        <td class="invoice_right_top_bar primary-color">
                             {{@$invoice->invoice_date != ""? date('jS M, Y', strtotime(@$invoice->invoice_date)):""}}</td>
                    </tr>
                    <tr>
                        <td class="invoice_left_top_bar primary-color">{{ __('invoice due Date:') }}</td>
                        <td class="invoice_right_top_bar primary-color">
                             {{@$invoice->invoice_due_date != ""? date('jS M, Y', strtotime(@$invoice->invoice_due_date)):""}}</td>
                    </tr>
                    <tr>
                        <td class="invoice_left_top_bar primary-color">{{ __('Payment method:') }}</td>
                        <td class="invoice_right_top_bar primary-color">
                             {{@$invoice->paymentMethod != ""? @$invoice->paymentMethod->method:''}}</td>
                    </tr>
                    <tr>
                        <td class="invoice_left_top_bar primary-color">{{ __('Payment status:') }}</td>
                        <td class="invoice_right_top_bar primary-color">
                             @if(@$invoice->payment_status == "UP")
                                {{'Unpaid'}}
                            @elseif(@$invoice->payment_status == "P")
                                {{'Paid'}}
                            @elseif(@$invoice->payment_status == "PP")
                                {{'Paid'}}
                            @elseif(@$invoice->payment_status == "PR")
                            {{'Proforma'}}
                            @endif</td>
                    </tr>
                    @php
                    $amount = Spondonit\Invoice\Models\InfixInvoice::amountInvoice(@$invoice->id, @$invoice->discount_type, @$invoice->discount_amount, @$invoice_setting->tax_type, @$invoice_setting->tax);
                   
                @endphp
                    <tr>
                        <td class="invoice_left_top_bar primary-color">{{ __('Reccuring cycle:') }}</td>
                        <td class="invoice_right_top_bar primary-color">
                             @if(@$invoice->recurring_cycle == "M")
                                {{'Monthly'}}
                            @elseif($invoice->recurring_cycle == "Q")
                                {{'Quarterly'}}
                            @elseif(@$invoice->recurring_cycle == "SA")
                                {{'Semi Annually'}}
                            @elseif(@$invoice->recurring_cycle == "A")
                            {{'Annually'}}
                            @elseif(@$invoice->recurring_cycle == "OT")
                            {{'Once Time'}}
                            @endif</td>
                            
                    </tr>
            
                    <tr>
                        <td colspan="2" style="text-align:right">
                            <p class="text-uppercase text-center primary-color "  style="background-color:#414e4e; color:white; display: block; text-align: right; 
        background-size: auto auto;  font-size: 12px; padding:5px; padding-left:25px; display: block;">{{ __('Total Amount') }}:  {{number_format(@$amount,2)}}({{@$amount!=""? @$invoice->currency->code:@$currency_symbol}}) </p> 
        {{-- background-size: 200% auto;  font-size: 18px; font-weight: 500px; height: 60px; line-height: 30px;">{{ __('Total Amount') }}:  {{App\User::NumberToBangladeshiTakaFormat(86778.879)}}({{@$invoice->currency !=""? @$invoice->currency->symbol:@$currency_symbol}}) </p>  --}}
                        </td>
                    </tr>
                </table>
                        
            </td>
        </tr>

        <tr>
            <td colspan="6"> <span  style="background-color: #e2e2e2; height: 1px; padding: 0; margin:0px 0px;display: block;"> </span></td>
        </tr>

    </table>

    <table align="center" style="padding-top: 0; width: 100%;" >


        @if(!empty(@$invoice->public_note))
        <tr>
            <td colspan="2" align="left"><p class="primary-color" align="justify">{{@$invoice->public_note}}</p></td>
        </tr>
        @endif
            <tr>
                <td colspan="6"> <span  style="background-color: #e2e2e2; height: 1px; padding: 0; margin: 10px 0px 0px; display: block;"> </span></td>
            </tr>
    </table>


    <table align="center" style="padding-top: 0; width: 100%; border-spacing: 0;" >
        <thead style="background-color:#414e4e; color:white">
            <tr style="text-align: center;">
                <th class="primary-color right_border" style="width: 5%; text-align: center !important; font-weight: bold; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd;">{{ __('SL') }}</th>
                <th class="primary-color right_border item_name" style="width: 20%; text-align: left !important; font-weight: bold; border-bottom: 1px solid #ddd;border-right: 1px solid #ddd;">{{ __('Product Name') }}</th>
                <th class="primary-color right_border item_name" style="width: 20%; text-align: left !important; font-weight: bold; border-bottom: 1px solid #ddd;border-right: 1px solid #ddd;">{{ __('Description') }}</th>
                <th class="primary-color right_border" style="width: 5%; text-align: center !important; font-weight: bold; border-bottom: 1px solid #ddd;border-right: 1px solid #ddd;">{{ __('Quantity') }}</th>
                <th class="primary-color right_border" style="width: 20%; text-align: center !important; font-weight: bold; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd;">{{ __('Unit Price') }} ({{@$invoice->currency !=""? @$invoice->currency->code:@$currency_symbol}})</th>
                <th class="primary-color" style="width: 20%; text-align: center !important; font-weight: bold; border-bottom: 1px solid #ddd;">{{ __('Total') }} ({{@$invoice->currency !=""? @$invoice->currency->code:@$currency_symbol}})</th>
            </tr>
        </thead>
            
        <tbody class="table_body" style="">
            @php $grand_total = 0; $sub_total = 0; $serial=1; @endphp
            @foreach($invoice->invoiceProducts as $value)
            <tr>
                <td style=" padding: 10px; width: 5%; border-bottom: 1px solid #ddd; border-left: 1px solid #ddd; text-align:center; border-right: 1px solid #ddd;" class="primary-color text-center">{{@$serial++}}</td>
                <td style=" padding: 10px; width: 30%; border-bottom: 1px solid #ddd;border-right: 1px solid #ddd; " class="primary-color item_name">{{@$value->productDetail != ""? @$value->productDetail->item_name:""}}</td>
                <td style=" padding: 10px; width: 30%; border-bottom: 1px solid #ddd;border-right: 1px solid #ddd; " class="primary-color  item_name">{{@$value->description}}</td>
                <td style=" padding: 10px; width: 5%; border-bottom: 1px solid #ddd;border-right: 1px solid #ddd; text-align:center " class="primary-color "> {{@$value->quantity}}</td>
                <td style=" padding: 10px; width: 10%; border-bottom: 1px solid #ddd;border-right: 1px solid #ddd; " class="primary-color amount">{{number_format(@$value->price, 2)}}</td>
                <td style=" padding: 10px; width: 20%; border-bottom: 1px solid #ddd; text-align:right; border-right: 1px solid #ddd; " class="primary-color amount"> {{number_format(@$value->price * @$value->quantity, 2)}}</td>
            </tr>
            @endforeach
            <tr> 
                <td class="fw-600 primary-color" colspan="5" style=" padding: 10px;text-align: right; border: 1px solid #ddd;  font-weight: bold;">
                    {{ __('Sub Total') }}: 
                </td>
                <td class="primary-color amount" style=" padding: 10px;text-align: right; border-right: 1px solid #ddd; " colspan="2">
                    
                @php
                    $TotalamountInvoice = Spondonit\Invoice\Models\InfixInvoice::TotalamountInvoice(@$invoice->id, @$invoice->discount_type, @$invoice->discount_amount);
                    echo number_format(@$TotalamountInvoice,2);
                @endphp
                </td>
            </tr>
            @php
            $tax = Spondonit\Invoice\Models\InfixInvoice::taxInvoice(@$invoice->id, @$invoice->discount_type, @$invoice->discount_amount, @$invoice_setting->tax_type, @$invoice_setting->tax);
            @endphp
            @if ($tax>1)
            <tr> 
                <td class="fw-600 primary-color" colspan="5" style=" padding: 10px;text-align: right; border: 1px solid #ddd; border-top: 1px solid #ddd; font-weight: bold;">{{ __('TAX/VAT/GST') }} </td>
                <td class="primary-color amount" style=" padding: 10px; border-right: 1px solid #ddd;    border-top: 1px solid #ddd; text-align: right;" colspan="2">
                    
                @php

                   echo number_format(@$tax,2);

                @endphp
                {{-- () --}}
                </td>
            </tr>
            @endif
            @if ($invoice->discount_amount>0)
            <tr> 
                <td class="fw-600 primary-color" colspan="5" style=" padding: 10px;text-align: right; border: 1px solid #ddd;     border-top: 1px solid #ddd; font-weight: bold;">{{ __('Discount') }} ({{@$invoice->discount_type != ""? (@$invoice->discount_type == "P"? ' %': ' fixed'):'' }})
                </td>
                <td class="fw-600 primary-color amount" style=" padding: 10px; border-right: 1px solid #ddd;    border-top: 1px solid #ddd; text-align: right;" colspan="2">
              {{@$invoice->discount_amount != ""?  number_format( (float) @$invoice->discount_amount, 2, '.', ''): "0.00" }}  
                {{-- () --}}
                </td>
            </tr>
           @endif
           
            <tr> 
               <td class="fw-600 primary-color"  colspan="5" style=" padding: 10px;text-align: right; border: 1px solid #ddd;   border-top: 1px solid #ddd; font-weight: bold;">{{ __('Grand Total') }}:</td>
               <td class="primary-color amount" style=" padding: 10px; border-right: 1px solid #ddd;   border-top: 1px solid #ddd; text-align: right;" colspan="2">
                
                @php
                    $amount = Spondonit\Invoice\Models\InfixInvoice::amountInvoice(@$invoice->id, @$invoice->discount_type, @$invoice->discount_amount, @$invoice_setting->tax_type, @$invoice_setting->tax);
                    echo number_format(@$amount,2);
                @endphp
                {{-- () --}}
                </td>
            </tr>
            <tr> 
               <td class="fw-600 primary-color"  colspan="5" style="padding: 10px;text-align: right; border: 1px solid #ddd;   border-top: 1px solid #ddd; font-weight: bold;">{{ __('Paid') }}:</td>
               <td class="primary-color amount" style="  padding: 10px; border-right: 1px solid #ddd;  border-top: 1px solid #ddd; text-align: right;" colspan="2">
                
                @php

                    if(@$invoice->payment_status == "UP"){
                        echo number_format(0,2);
                    }elseif(@$invoice->payment_status == "P"){
                        echo number_format(@$amount,2);
                    }else{
                        echo number_format(@$invoice->partial_paymemt,2);
                    }
                    

                @endphp
                {{-- () --}}
                </td>
            </tr>
            <tr> 
               <td class="fw-600 primary-color"  colspan="5" style=" padding: 10px; text-align: right; border: 1px solid #ddd; border-bottom: 1px solid #ddd;  border-top: 1px solid #ddd; font-weight: bold; margin-bottom: 40px;">{{ __('Unpaid') }} ({{@$invoice->currency !=""? @$invoice->currency->code:@$currency_symbol}}):</td>
               <td class="primary-color amount" style=" padding: 10px;   border-right: 1px solid #ddd;  border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; text-align: right; margin-bottom: 40px;" colspan="2">
                
                 @php

                    if(@$invoice->payment_status == "UP"){
                        echo number_format(@$amount,2);
                    }elseif(@$invoice->payment_status == "P"){
                        echo number_format(0,2);
                    }else{
                        echo number_format(@$amount - @$invoice->partial_paymemt,2);
                    }
                    

                @endphp
                

                {{-- () --}}
                </td>
            </tr>

        
             
        @if(!empty($invoice->terms_note))
            <tr>
                <td colspan="6" style="text-align: left;">
                   <div style="width: 100%">
                    <h2 class="primary-color" style="font-size: 12px; text-transform: uppercase; margin-top: 10px; ">{{ __('Terms') }}:</h2>
                    <p>{!! @$invoice->terms_note !!}</p>
                   </div> 
                </td>
           </tr>
        @endif 

        </tbody>
    </table>


    <table width="100%" align="center" style="height: auto; width: 100%;">
        <tr>
            <td style="width: 5%;"></td>
            <td style="text-align: left;">
                    <h2 class="primary-color" style="font-size: 12px; text-transform: uppercase; margin-top: 40px; margin-bottom:20px ">{{ __('Generated By') }}:</h2> 
                <b>{{@$invoice->signature_person}}</b>
                <br>
                <span style="text-transform: uppercase;"> {{@$invoice->signature_company}} </span>
                
            </td>
        </tr> 
    </table>

</body>

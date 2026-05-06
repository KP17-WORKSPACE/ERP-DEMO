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
    if(isset($generalSetting->logo)){  @$logo = @$generalSetting->logo;  }
    else{ $logo = 'public/uploads/settings/logo.png'; }

    $sm_staff= App\SmStaff::where('user_id',Auth::user()->id)->first();
    if(!empty(@$sm_staff)){
        @$profile_image = @$sm_staff->staff_photo;
        if(empty(@$profile_image)){
            @$profile_image ='public/uploads/staff/staff1.jpg';
        }
    }
@endphp

<link href="{{asset('packages/spondonit/invoice/src/public/invoice_edit.css')}}" type="text/css" rel="stylesheet">
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>Edit Invoice </h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="{{url('infix/invoice-list')}}">Invoice</a>
                <a href="#">Edit Invoice</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30">@lang('lang.edit') @lang('lang.invoice')
                            </h3>
                        </div>
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'infix/invoice-update', 'method' => 'POST', 'id' => 'create-invoice-form']) }}

                        <input type="hidden" name="id" value="{{@$invoice->id}}">

                        <div class="white-box">
                            <div class="add-visitor">

                                <div class="row">
                                    <div class="col-lg-12">
                                        @if(session()->has('message-success'))
                                          <div class="alert alert-success">
                                              {{ session()->get('message-success') }}
                                          </div>
                                        @elseif(session()->has('message-danger'))
                                          <div class="alert alert-danger">
                                              {{ session()->get('message-danger') }}
                                          </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12" id="errors-message">

                                    </div>
                                </div>

                                <div class="row mt-40">

                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <select class="niceSelect up_select w-100 bb form-control{{ $errors->has('fees_type') ? ' is-invalid' : '' }}" name="customer" id="customer">
                                                <option data-display="@lang('lang.customer') *" value="">@lang('lang.customer') *</option>
                                                @foreach($customers as $value)
                                                    <option value="{{@$value->id}}" {{@$value->id == @$invoice->customer_id?'selected':''}}>{{@$value->full_name}}</option>
                                                @endforeach
                                            </select>
                                            <a data-toggle="modal" class="up_select_modal" data-target="#AddCustomer"  href="#"><i class="ti-plus"></i></a>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <select class="niceSelect up_select w-100 bb form-control{{ $errors->has('fees_type') ? ' is-invalid' : '' }}" name="payment_method" id="payment_method">
                                                <option data-display="@lang('lang.payment') method *" value="">@lang('lang.payment') method *</option>
                                                @foreach($payment_methods as $value)
                                                    <option value="{{@$value->id}}" {{@$value->id == @$invoice->payment_method_id?'selected':''}}>{{@$value->method}}</option>
                                                @endforeach
                                            </select>
                                            <a data-toggle="modal" class="up_select_modal" data-target="#paymentMethod"  href="#"><i class="ti-plus"></i></a>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <select class="niceSelect w-100 bb form-control{{ $errors->has('fees_type') ? ' is-invalid' : '' }}" name="project" id="project">
                                                <option data-display="project" value="">@lang('lang.project')</option>
                                                @foreach($projects as $value)
                                                    <option value="{{@$value->id}}" {{@$value->id == @$invoice->project_id?'selected':''}}>{{@$value->tender_no}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('tax') ? ' is-invalid' : '' }}" type="text" name="tax" autocomplete="off" step="any" value="{{@$invoice_setting->tax}}" readonly="" id="tax">

                                                <input type="hidden" name="tax_type" id="tax_type" value="{{@$invoice_setting->tax_type}}">

                                                <label>@lang('lang.TAX_VAT_GST') <span>*</span></label>
                                                <span class="focus-border"></span>
                                                @if ($errors->has('tax'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('tax') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>


                                </div>

                                <div class="row mt-40">

                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <select class="niceSelect w-100 bb form-control{{ $errors->has('currency') ? ' is-invalid' : '' }}" name="currency" id="currency">
                                                <option data-display="currency *" value="">currency *</option>
                                                @foreach($currencies as $value)
                                                    <option value="{{@$value->id}}" {{@$value->id == @$invoice->currency_id?'selected':''}}>{{@$value->code}} ({{@$value->symbol}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <input class="primary-input date" id="startDate" type="text" name="invoice_date" value="{{@$invoice->invoice_date != ""? date('d/m/Y', strtotime(@$invoice->invoice_date)):''}}" autocomplete="off">
                                                        <label>@lang('lang.invoice') @lang('lang.date')</label>
                                                        <span class="focus-border"></span>
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
                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <input class="primary-input date" id="endDate" type="text" name="due_date" value="{{@$invoice->invoice_due_date != ""? date('d/m/Y', strtotime($invoice->invoice_due_date)):''}}" autocomplete="off">
                                                        <label>@lang('lang.due') @lang('lang.date')</label>
                                                        <span class="focus-border"></span>
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

                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <div class="input-effect invoice-custom">
                                                <span class="invoive-prefix">{{@$invoice_setting->prefix}}</span>
                                                <input class="invoive-number primary-input form-control{{ $errors->has('invoice_no') ? ' is-invalid' : '' }}" type="text" name="invoice_no" autocomplete="off" step="any" value="{{@$invoice->invoice_number}}" readonly="true">
                                                <label >@lang('lang.invoice') @lang('lang.no_') <span>*</span></label>
                                                <span class="focus-border"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="prefix" value="{{@$invoice_setting->prefix}}">


                                </div>

                                <div class="row mt-40">
                                     <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <select class="niceSelect w-100 bb form-control{{ $errors->has('recurring') ? ' is-invalid' : '' }}" name="recurring" id="recurring">
                                                <option data-display="recurring cycle" value="">recurring cycle</option>
                                                <option value="M" {{@$invoice->recurring_cycle == "M"?'selected':''}}>Monthly</option>
                                                <option value="Q" {{@$invoice->recurring_cycle == "Q"?'selected':''}}>Quarterly</option>
                                                <option value="SA" {{@$invoice->recurring_cycle == "SA"?'selected':''}}>Semi Annually</option>
                                                <option value="A" {{@$invoice->recurring_cycle == "A"?'selected':''}}>Annually</option>
                                                <option value="OT" {{@$invoice->recurring_cycle == "OT"?'selected':''}}>Once Time</option>
                                            </select>
                                        </div>
                                    </div>

                                     <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('purchase_order') ? ' is-invalid' : '' }}" type="text" name="purchase_order" autocomplete="off" step="any">
                                                <label >@lang('lang.purchase') @lang('lang.order')</label>
                                                <span class="focus-border"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <select class="niceSelect w-100 bb form-control{{ $errors->has('payment_status') ? ' is-invalid' : '' }}" onChange="enablePartialPayment();" name="payment_status" id="payment_status">
                                                <option value="UP" {{@$invoice->payment_status == "UP"?'selected':''}}>@lang('lang.unpaid')</option>
                                                <option value="P" {{@$invoice->payment_status == "P"?'selected':''}}>@lang('lang.paid')</option>
                                                <option value="PP" {{@$invoice->payment_status == "PP"?'selected':''}}>@lang('lang.PARTIALLY') @lang('lang.paid')</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3" id="partial_payment_div">
                                        <div class="single_invoice">
                                            <div class="input-effect">
                                            <input class="primary-input form-control{{ $errors->has('partial_payment') ? ' is-invalid' : '' }}" max="{{@$invoice->total}}" type="text" value="{{$invoice->partial_paymemt}}" name="partial_payment" id="partial_payment" autocomplete="off" step="any">
                                                <label >@lang('lang.PARTIALLY') @lang('lang.payment')</label>
                                                <span class="focus-border"></span>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                               







                                <div class="row mt-40">
                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <div class="input-effect">
                                                <input class="primary-input form-control"
                                                        type="number" step="any" id="discount" name="discount" autocomplete="off"  value="{{@$invoice->discount_amount != ""? @$invoice->discount_amount:0}}">
                                                <label>@lang('lang.discount')</label>
                                                <span class="focus-border"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <div class="d-flex radio-btn-flex ml-40">
                                                <div class="mr-30">
                                                    <input type="radio" name="discount_type" id="relationFather" value="P" class="common-radio relationButton" {{@$invoice->discount_type == "P"?'checked':''}}>
                                                    <label for="relationFather">%</label>
                                                </div>
                                                <div class="mr-30">
                                                    <input type="radio" name="discount_type" id="relationMother" value="F" class="common-radio relationButton" {{@$invoice->discount_type == "F"?'checked':''}}>
                                                    <label for="relationMother">@lang('lang.fixed')</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12 text-right">
                                        <button type="button" class="primary-btn small fix-gr-bg mb-3" id="addInvoiceRowProduct">
                                        <span class="ti-plus pr-2"></span>
                                        @lang('lang.add') @lang('lang.item')
                                    </button>
                                    </div>
                                </div>

                                    <table class="display without-box-shadow invoice_table mb-4" width="100%" id="product-table">

                                    <thead>
                                        <tr>
                                            <th>@lang('lang.product_name')</th>
                                            <th width="25%">@lang('lang.description')</th>
                                            <th>@lang('lang.quantity')</th>
                                            <th>@lang('lang.price') (<span id="currency_id">{{@$invoice->currency !=""? @$invoice->currency->symbol:@$currency_symbol}}</span>)</th>
                                            <th>@lang('lang.total') (<span id="currency_id">{{@$invoice->currency !=""? @$invoice->currency->symbol:@$currency_symbol}}</span>)</th>
                                            <th class="text-center">@lang('lang.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @php $i = 0; $total = 0; @endphp
                                        @foreach($invoice->invoiceProducts as $invoiceProduct)
                                        @php $i++; @endphp
                                        <tr>
                                            <td>
                                                <select class="niceSelect w-100 bb form-control" id="infix-received_product" name="products[]">
                                                    <option data-display="@lang('lang.select') @lang('lang.item') *" value="none">@lang('lang.select') @lang('lang.item') *</option>
                                                    @foreach($items as $value)
                                                         <option value="{{@$value->id}}" {{@$invoiceProduct->product_id == @$value->id? 'selected':''}}>{{@$value->item_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            </td>
                                            <td>
                                                <div class="input-effect">
                                                    <input class="primary-input form-control"
                                                    type="text" step="any" id="description" name="description[]" autocomplete="off" value="{{@$invoiceProduct->description}}">
                                                    <span class="focus-border"></span>

                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-effect">
                                                    <input class="primary-input form-control"
                                                    type="number" step="any" id="quantity" name="quantity[]" autocomplete="off"  value="{{@$invoiceProduct->quantity}}">
                                                    <span class="focus-border"></span>

                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-effect">
                                                    <input class="primary-input form-control"
                                                    type="number" step="any" id="unit_price" name="unit_price[]" autocomplete="off"  value="{{@$invoiceProduct->price}}">
                                                    <span class="focus-border"></span>
                                                </div>
                                            </td>
                                            <input type="hidden" name="product_quantity" id="product_quantity">
                                            <td>
                                                <div class="input-effect">
                                                    <input class="primary-input form-control"
                                                    type="number" step="any" id="total_price" name="total_price[]" autocomplete="off"  readonly=""  value="{{@$invoiceProduct->price * $invoiceProduct->quantity}}">
                                                    <span class="focus-border"></span>
                                                </div>
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                         @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr id="discount_amount_tr">

                                            <td colspan="3"></td>


                                            <td>@lang('lang.discount') @lang('lang.amount'):</td>
                                            @php

                                            $amount = Spondonit\Invoice\Models\InfixInvoice::amountInvoice(@$invoice->id, @$invoice->discount_type, @$invoice->discount_amount, @$invoice_setting->tax_type, @$invoice_setting->tax);


                                                $discount_amount = 0;

                                                if(@$invoice->discount_amount != "" && @$invoice->discount_type != ""){
                                                    if(@$invoice->discount_type == "P"){

                                                        @$discount_amount = @$amount / 100 * @$invoice->discount_amount;


                                                    }elseif(@$invoice->discount_type == "F"){

                                                        @$discount_amount = @$invoice->discount_amount;
                                                    }
                                                }


                                            @endphp


                                            <td>
                                                <input class="primary-input form-control"
                                                    type="number" step="any" id="discount_amount" name="discount_amount" autocomplete="off" readonly="" value="{{@$discount_amount}}"></td>
                                            <td></td>
                                        </tr>





                                        <tr>

                                            <td rowspan="4" colspan="3">

                                            </td>

                                            <td>@lang('lang.total') @lang('lang.amount'):</td>



                                            @php



                                            @endphp



                                            <td><input class="primary-input form-control"
                                                    type="number" step="any" id="total" name="total" autocomplete="off" readonly="true" value="{{@$amount}}"></td>
                                            <td></td>



                                        </tr>
                                        <tr>


                                            @php

                                            if(@$invoice->payment_status == 'UP'){
                                                $paid = 0;
                                                $unpaid = @$amount;
                                            }elseif(@$invoice->payment_status == 'P'){
                                                $paid = @$amount;
                                                $unpaid = 0;
                                            }elseif(@$invoice->payment_status == 'PP'){
                                                $paid = @$invoice->partial_paymemt;
                                                $unpaid = @$amount - @$invoice->partial_paymemt;
                                            }



                                            @endphp



                                            <td>@lang('lang.paid') @lang('lang.amount'):</td>
                                            <td><input class="primary-input form-control"
                                                    type="number" step="any" id="paid_amount" name="paid_amount" readonly  autocomplete="off" value="{{@$paid}}"></td>
                                            <td></td>
                                        </tr>
                                        <tr>


                                            <td>@lang('lang.due') @lang('lang.amount'):</td>
                                            <td><input class="primary-input form-control"
                                                    type="number" step="any" id="due_amount" name="due_amount" autocomplete="off" readonly="true" value="{{@$unpaid}}"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <script>
                                    paid_amount.oninput = function() {
                                        var total=document.getElementById('total').value;
                                        var paid_amount=document.getElementById('paid_amount').value;
                                        var due_amount=document.getElementById('due_amount');

                                        var due=total-paid_amount;

                                        document.getElementById('due_amount').value=due;
                                    };
                                </script>

                            <script type="text/javascript">
                                function enablePartialPayment()
                                {
                                    // if (document.getElementById("payment_status").value === "PP") {
                                    //     $("#paid_amount").attr("readonly", false); 
                                    // } else {
                                    //     $("#paid_amount").attr("readonly", true);
                                    // }
                                }
                                </script>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <!-- Start Student Details -->
                                        <div class="student-details invoice-student-details">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item text-center">
                                                    <a class="nav-link active" href="#publicNotes" role="tab" data-toggle="tab">@lang('lang.Public') @lang('lang.notes')</a>
                                                </li>
                                                <li class="nav-item text-center">
                                                    <a class="nav-link" href="#privateNotes" role="tab" data-toggle="tab">@lang('lang.Private') @lang('lang.notes')</a>
                                                </li>
                                                <li class="nav-item text-center">
                                                    <a class="nav-link" href="#terms" role="tab" data-toggle="tab">@lang('lang.Terms')</a>
                                                </li>
                                                <li class="nav-item text-center">
                                                    <a class="nav-link" href="#footer" role="tab" data-toggle="tab">@lang('lang.Footer')</a>
                                                </li>
                                                <li class="nav-item text-center">
                                                    <a class="nav-link" href="#signature" role="tab" data-toggle="tab">@lang('lang.signature')</a>
                                                </li>
                                            </ul>
                                            <!-- Tab panes -->
                                            <div class="tab-content invoice-tab-content">
                                                <!-- Start Profile Tab -->
                                                <div role="tabpanel" class="tab-pane fade  show active" id="publicNotes">
                                                    <textarea class="primary-input form-control" cols="0" rows="4"
                                        name="public_note">{{$invoice->public_note}}</textarea>
                                                </div>
                                                <!-- End Profile Tab -->

                                                <!-- Start Fees Tab -->
                                                <div role="tabpanel" class="tab-pane fade" id="privateNotes">
                                                    <textarea class="primary-input form-control" cols="0" rows="4"
                                        name="private_note">{{@$invoice->private_note}}</textarea>
                                                </div>
                                                <!-- End Profile Tab -->

                                                <!-- Start Fees Tab -->
                                                <div role="tabpanel" class="tab-pane fade" id="terms">
                                                    <textarea class="primary-input form-control" cols="0" rows="4"
                                        name="terms_note">{{@$invoice->terms_note}}</textarea>
                                                </div>
                                                <!-- End Profile Tab -->
                                                <!-- Start Fees Tab -->
                                                <div role="tabpanel" class="tab-pane fade" id="footer">
                                                    <textarea class="primary-input form-control" cols="0" rows="4"
                                        name="footer_note">{{@$invoice->footer_note}}</textarea>
                                                </div>
                                                <!-- End Profile Tab -->
                                                <!-- Start Fees Tab -->
                                                <div role="tabpanel" class="tab-pane fade" id="signature">
                                                    <input type="text" name="signature_person" class="primary-input form-control" placeholder="person name" value="{{@$invoice->signature_person}}"><br>
                                                    <input type="text" name="signature_company" class="primary-input form-control" placeholder="company name" value="{{@$invoice->signature_company}}">
                                                </div>
                                                <!-- End Profile Tab -->
                                            </div>
                                        </div>
                                        <!-- End Student Details -->
                                    </div>
                                </div>

                                <div class="row mt-40">
                                    <div class="col-lg-12 text-center">
                                        <button class="primary-btn fix-gr-bg">
                                            <span class="ti-check"></span>

                                                @lang('lang.update')
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



                                         <div class="modal fade admin-query" id="AddCustomer" >
                                            <div class="modal-dialog modal-dialog-centered  modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">@lang('lang.add') @lang('lang.new') @lang('lang.customer')</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>

                                                    <div class="modal-body">

                                                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customer-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}


                                                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                                                <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{date('Y-m-d')}}">
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                      <div class="white-box without-box-shadow">
                                                                            <div class="row">
                                                                                <div class="col-lg-12">
                                                                                    <div class="main-title">
                                                                                        <h4>@lang('lang.basic') @lang('lang.information')</h4>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row mb-20">
                                                                                <div class="col-lg-12">
                                                                                    <hr>
                                                                                </div>
                                                                            </div>

                                                                            <div class="row mb-40">
                                                                                <div class="col-lg-6 mt-40">
                                                                                    <div class="input-effect">
                                                                                        <input class="primary-input form-control {{$errors->has('first_name') ? 'is-invalid' : ' '}}" type="text"  name="first_name" value="{{isset($editData)?@$editData->first_name:old('first_name')}}">
                                                                                        <span class="focus-border"></span>
                                                                                        <label>@lang('lang.first') @lang('lang.name') <span>*</span> </label>
                                                                                        @if ($errors->has('first_name'))
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $errors->first('first_name') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-lg-6 mt-40">
                                                                                    <div class="input-effect">
                                                                                        <input class="primary-input form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" type="text"  name="last_name" value="{{isset($editData)?@$editData->last_name:old('last_name')}}">
                                                                                        <span class="focus-border"></span>
                                                                                        <label>@lang('lang.last') @lang('lang.name')  </label>
                                                                                        @if ($errors->has('last_name'))
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $errors->first('last_name') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                               <div class="col-lg-6 mt-40">
                                                                                    <div class="input-effect">
                                                                                        <input class="primary-input form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" type="email"  name="email" value="{{isset($editData)?@$editData->email:old('email')}}">
                                                                                        <span class="focus-border"></span>
                                                                                        <label>@lang('lang.email') <span>*</span> </label>
                                                                                        @if ($errors->has('email'))
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $errors->first('email') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>

                                                                                 <div class="col-lg-6 mt-40">
                                                                                    <div class="input-effect">
                                                                                        <input class="primary-input form-control{{ $errors->has('mobile') ? ' is-invalid' : '' }}" type="text"  name="mobile" value="{{isset($editData)?@$editData->mobile:old('mobile')}}">
                                                                                        <span class="focus-border"></span>
                                                                                        <label>@lang('lang.mobile') <span>*</span> </label>
                                                                                        @if ($errors->has('mobile'))
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $errors->first('mobile') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>








                                                                            <div class="row mt-40">
                                                                                <div class="col-lg-12">
                                                                                    <div class="main-title">
                                                                                        <h4>@lang('lang.details')</h4>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row mb-20">
                                                                                <div class="col-lg-12">
                                                                                    <hr>
                                                                                </div>
                                                                            </div>





                                                                            <div class="row mb-30">

                                                                                <div class="col-lg-6 mt-40">
                                                                                    <div class="input-effect">

                                                                                        <input class="primary-input form-control{{ $errors->has('company_name') ? ' is-invalid' : '' }}" type="text"  name="company_name" value="{{isset($editData)?@$editData->company_name:old('company_name')}}">
                                                                                        <span class="focus-border"></span>
                                                                                        <label>@lang('lang.company') @lang('lang.name') </label>
                                                                                        @if ($errors->has('company_name'))
                                                                                        <span class="invalid-feedback" role="alert">
                                                                                            <strong>{{ $errors->first('company_name') }}</strong>
                                                                                        </span>
                                                                                        @endif


                                                                                    </div>
                                                                                </div>

                                                                                 <div class="col-lg-6 mt-40">
                                                                                    <div class="input-effect">
                                                                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('department_id') ? ' is-invalid' : '' }}" name="department_id" id="department_id">
                                                                                            <option data-display="Select Department" value="">Select Department</option>
                                                                                            @foreach($departments as $key=>$value)
                                                                                            <option value="{{@$value->id}}" {{ isset($editData)?@$editData->department_id==$value->id? 'selected="selected"':'':''}} >{{@$value->name}}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                        <span class="focus-border"></span>
                                                                                        @if ($errors->has('department_id'))
                                                                                        <span class="invalid-feedback invalid-select" role="alert">
                                                                                            <strong>{{ $errors->first('department_id') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>


                                                                                <div class="col-lg-6 mt-40">
                                                                                    <div class="input-effect">
                                                                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('designation_id') ? ' is-invalid' : '' }}" name="designation_id" id="designation_id">
                                                                                            <option data-display="Select Designations" value="">Select Designations</option>
                                                                                            @foreach($designations as $key=>$value)
                                                                                            <option value="{{@$value->id}}" {{ isset($editData)?@$editData->designation_id==$value->id? 'selected="selected"':'':''}} >{{@$value->title}}</option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                        <span class="focus-border"></span>
                                                                                        @if ($errors->has('designation_id'))
                                                                                        <span class="invalid-feedback invalid-select" role="alert">
                                                                                            <strong>{{ $errors->first('designation_id') }}</strong>
                                                                                        </span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>



                                                                                <div class="col-lg-6 mt-40">
                                                                                    <div class="row no-gutters input-right-icon">
                                                                                        <div class="col">
                                                                                            <div class="input-effect">

                                                                                                <input class="primary-input form-control input-image {{ $errors->has('staff_photo') ? ' is-invalid' : '' }}" type="text" id="placeholderStaffsName"
                                                                                                placeholder="{{isset($editData->file) && $editData->file != '' ? showPicName($editData->file):'Customer Photo '}}"
                                                                                                disabled>
                                                                                                <span class="focus-border"></span>

                                                                                                @if ($errors->has('staff_photo'))
                                                                                                     <span class="invalid-feedback" role="alert">
                                                                                                        <strong>{{ $errors->first('staff_photo') }}</strong>
                                                                                                    </span>
                                                                                                @endif

                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-auto">
                                                                                            <button class="primary-btn-small-input" type="button">
                                                                                                <label class="primary-btn small fix-gr-bg" for="staff_photo">browse</label>
                                                                                                <input type="file" class="d-none" name="staff_photo" id="staff_photo">
                                                                                            </button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>


                                                                        </div>






                                                                            <div class="row mt-40">
                                                                                <div class="col-lg-12">
                                                                                    <div class="main-title">
                                                                                        <h4>@lang('lang.address')</h4>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="row mb-20">
                                                                                <div class="col-lg-12">
                                                                                    <hr>
                                                                                </div>
                                                                            </div>





                                                                    <div class="row mb-40">
                                                                        <div class="col-lg-12 mt-40">
                                                                            <div class="input-effect">
                                                                                <textarea class="primary-input form-control {{ $errors->has('current_address') ? 'is-invalid' : ''}}" cols="0" rows="4" name="current_address" id="current_address">{{isset($editData)?@$editData->current_address:old('current_address')}}</textarea>
                                                                                <label> @lang('lang.billing') @lang('lang.address') <span>*</span> </label>
                                                                                <span class="focus-border textarea"></span>
                                                                                @if ($errors->has('current_address'))
                                                                                 <span class="invalid-feedback" role="alert">
                                                                                    <strong>{{ $errors->first('current_address') }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 mt-40">
                                                                            <div class="input-effect">
                                                                                <textarea class="primary-input form-control {{ $errors->has('permanent_address') ? 'is-invalid' : ''}}" cols="0" rows="4" name="permanent_address" id="permanent_address">{{isset($editData)?@$editData->permanent_address:old('permanent_address')}}</textarea>
                                                                                <label> @lang('lang.shipping') @lang('lang.address') </label>
                                                                                <span class="focus-border textarea"></span>
                                                                                @if ($errors->has('permanent_address'))
                                                                                 <span class="invalid-feedback" role="alert">
                                                                                    <strong>{{ $errors->first('permanent_address') }}</strong>
                                                                                </span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>



                                                                    <div class="row mt-40">
                                                                        <div class="col-lg-12 text-center">
                                                                            <button class="primary-btn fix-gr-bg">
                                                                                <span class="ti-check"></span>
                                                                                @if(isset($editData)) @lang('lang.update') @else @lang('lang.add') @endif @lang('lang.customer')
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </div>
                                                        {{ Form::close() }}
                                                    </div>

                                                </div>
                                            </div>

                                         <div class="modal fade admin-query" id="paymentMethod">
                                            <div class="modal-dialog modal-dialog-centered  modal-md">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">@lang('lang.add') @lang('lang.new') @lang('lang.payment') @lang('lang.method')</h4>
                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    </div>

                                                    <div class="modal-body">
                                                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'infix/payment-method-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <div class="row mt-25">
                                                                    <div class="col-lg-12">
                                                                        <div class="input-effect">
                                                                            <input class="primary-input form-control" type="text"
                                                                                   name="method" value="" id="title">
                                                                            <label> @lang('lang.method')<span>*</span> </label>
                                                                            <span class="focus-border"></span>

                                                                            <span class=" text-danger" role="alert"
                                                                                  id="amount_error">

                                                                            </span>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12 text-center mt-40">
                                                                <div class="mt-40 d-flex justify-content-between">
                                                                    <button type="button" class="primary-btn tr-bg"
                                                                            data-dismiss="modal">@lang('lang.cancel')
                                                                    </button>

                                                                    <button class="primary-btn fix-gr-bg" type="submit">@lang('lang.save')
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{ Form::close() }}
                                                    </div>

                                                    </div>

                                                </div>
                                            </div>

</section>
@endsection


@section('script')

<script>

$(document).on("click", "#addInvoiceRowProduct", function (event) {




    var url = $('#url').val();

    $.ajax({
        url: url + '/' + 'infix/get-receive-item-tender',
        type: 'GET',
        dataType: 'json',
        success: function(response) {

            console.log(response);

            var tr = "<tr>";
                tr += "<td>";
                tr += "<div class='input-effect'>";
                tr +=  "<select class='niceSelect w-100 bb form-control' name='products[]' id='infix-received_product' style='display:none'>";
                tr +=  "<option data-display='Select product *' value='none'>Select *</option>";

                $.each(response, function(index, value) {
                    tr += '<option value="' + value.id + '">' + value.name + '</option>';
                });

                tr += '</select>';

                tr += "<div class='nice-select w-100 bb niceSelect form-control' tabindex='0'>";

                tr += "<span class='current'>Select product *</span>";
                tr += "<div class='nice-select-search-box'><input type='text' class='nice-select-search' placeholder='Search...'></div>";
                tr += "<ul class='list'>";
                tr += "<li data-value='' data-display='Select product' class='option selected'>Select product</li>";


                $.each(response, function (key, value) {
                    tr += "<li data-value="+ value.id +" class='option'>"+ value.name  +"</li>";
                });

                tr += "</ul>";
                tr += '</div>';
                tr += '</div>';
                tr += '</td>';

                tr += '<td>\
                            <div class="input-effect">\
                                <input class="primary-input form-control" type="text" step="any" id="description" name="description[]" autocomplete="off">\
                                <span class="focus-border"></span>\
                            </div>\
                        </td>\
                        <td>\
                            <div class="input-effect">\
                                <input class="primary-input form-control" type="number" step="any" id="quantity" name="quantity[]" autocomplete="off">\
                                <span class="focus-border"></span>\
                            </div>\
                        </td>\
                        <td>\
                            <div class="input-effect">\
                                <input class="primary-input form-control" type="number" step="any" id="unit_price" name="unit_price[]" autocomplete="off">\
                                <span class="focus-border"></span>\
                            </div>\
                        </td>\
                        <input type="hidden" name="product_quantity" id="product_quantity">\
                        <td>\
                            <div class="input-effect">\
                                <input class="primary-input form-control" type="number" step="any" id="total_price" name="total_price[]" autocomplete="off" readonly="">\
                                <span class="focus-border"></span>\
                            </div>\
                        </td>\
                        <td class="text-center">\
                            <button class="primary-btn icon-only ml-30 fix-gr-bg" type="button" id="delete-tender-product">\
                                 <span class="ti-trash"></span>\
                            </button>\
                        </td>';


                tr += '</tr>';




                $("#product-table tbody tr:last").after(tr);
            } // /success
        }); // get the product data

});





$(document).on("change", "#infix-received_product", function (event) {

        var url = $('#url').val();

        if($(this).val() == 'none' || $(this).val() == null){



            $(this).closest("tr").find('#quantity').val(1);

            $(this).closest("tr").find('#unit_price').val('');
            $(this).closest("tr").find('#product_quantity').val('');
            return false;
        }


        var selected_id = $(this).val();




        var count = 0;
        $('select[id=received_product]').each(function(){

            if($(this).val() == selected_id){
                count++;
            }

        });

        if(count > 1){

             $(this).closest("tr").find('span.current').html('SELECT PRODUCT *');

            $(this).closest("tr").find('#quantity').val(1);

            $(this).closest("tr").find('#unit_price').val('');
            $(this).closest("tr").find('#product_quantity').val('');
            alert('Alreday selected the product');
            return false;
        }






        var formData = {
            id: $(this).val()
        };

        console.log(formData);

        $.ajax({
            type: "GET",
            data: formData,
            context: this,
            dataType: 'json',
            url: url + '/' + 'infix/get-receive-item-details',
            success: function(data) {

                console.log(data);


                if(data[1] == 0 || $(this).val() == 'none'){
                    alert('no product in stock');
                    $(this).closest("tr").find('#quantity').val();

                    $(this).closest("tr").find('#unit_price').val();
                    $(this).closest("tr").find('#product_quantity').val();
                }


                $(this).closest("tr").find('#quantity').val();

                $(this).closest("tr").find('#unit_price').val(data[0].sale_price);
                $(this).closest("tr").find('#product_quantity').val(data[1]);


            },
            error: function(data) {
                console.log('Error:', data);
            }
        });
});


$(document).on("keyup mouseup", "form#create-invoice-form #quantity", function (event) {

    var product_quantity = parseInt($('#product_quantity').val());
    var quantity = parseFloat($(this).val());







                var unit_price = parseFloat($(this).closest("tr").find("input[id=unit_price]").val());


                if(isNaN(unit_price)){
                    unit_price = 0;
                }

                var total_price = quantity * unit_price;
                $(this).closest("tr").find("input[id=total_price]").val(total_price);

                var total = 0;


                $('input[id=total_price]').each(function(){

                    var total_price = parseInt($(this).val());
                        if(isNaN(total_price)){
                            total_price = 0;
                        }





                    total = total + total_price;
                });

                var bid_amount = 0;


                bid_amount = total;


                if($('#tax_type').val() == 'BD'){
                    var tax = total / 100 * parseInt($('#tax').val());

                    bid_amount = total + tax;
                }

                console.log(bid_amount);


                if($('input[name=discount_type]').is(':checked')){

                    var discount = parseInt($('#discount').val());

                    if(isNaN(discount)){
                            discount = 0;
                        }

                    if($('input[name=discount_type]:checked').val() == "P"){
                        var percentage = bid_amount / 100 * discount;
                        bid_amount = bid_amount - percentage;

                        $('#discount_amount').val(percentage);
                    }

                    if($('input[name=discount_type]:checked').val() == "F"){
                        bid_amount = bid_amount - discount;

                        $('#discount_amount').val(parseInt($('#discount').val()));
                    }
                }



                if($('#tax_type').val() == 'AD'){
                    var tax = bid_amount / 100 * parseInt($('#tax').val());

                    bid_amount = bid_amount + tax;
                }



                $('#total').val(bid_amount);


                if($('#payment_status').val() == "PP"){
                    var partial_payment = parseInt($('#partial_payment').val());

                    if(isNaN(partial_payment)){
                        partial_payment = 0;
                    }

                    var total = $('#total').val();

                    var due_amount = total - partial_payment;

                    console.log(due_amount);

                    $('#paid_amount').val(partial_payment);
                    $('#due_amount').val(due_amount);
                }else if($('#payment_status').val() == "UP"){

                    $('#paid_amount').val(0);
                    $('#due_amount').val(total);

                }else if($('#payment_status').val() == "P"){
                    $('#paid_amount').val(total);
                    $('#due_amount').val(0);
                }







});


$(document).on("keyup mouseup", "form#create-invoice-form #unit_price", function (event) {
    var unit_price = parseFloat($(this).val());
    var quantity = parseFloat($(this).closest("tr").find("input[id=quantity]").val());

    if(isNaN(quantity)){
        quantity = 0;
    }

    var total_price = quantity * unit_price;
    $(this).closest("tr").find("input[id=total_price]").val(total_price);


    var total = 0;


    $('input[id=total_price]').each(function(){

        var total_price = parseInt($(this).val());
        if(isNaN(total_price)){
            total_price = 0;
        }


        total = total + total_price;
    });

    var bid_amount = 0;


    bid_amount = total;



    if($('#tax_type').val() == 'BD'){
        var tax = total / 100 * parseInt($('#tax').val());

        bid_amount = total + tax;
    }

    console.log(bid_amount);









    if($('input[name=discount_type]').is(':checked')){


        var discount = parseInt($('#discount').val());

                    if(isNaN(discount)){
                            discount = 0;
                        }


        if($('input[name=discount_type]:checked').val() == "P"){




            var percentage = bid_amount / 100 * discount;
            bid_amount = bid_amount - percentage;
            $('#discount_amount').val(percentage);
        }

        if($('input[name=discount_type]:checked').val() == "F"){
            bid_amount = bid_amount - discount;
            $('#discount_amount').val(parseInt($('#discount').val()));
        }
    }




    if($('#tax_type').val() == 'AD'){
        var tax = bid_amount / 100 * parseInt($('#tax').val());

        bid_amount = bid_amount + tax;
    }



    $('#total').val(bid_amount);

    if($('#payment_status').val() == "PP"){
        var partial_payment = parseInt($('#partial_payment').val());

        if(isNaN(partial_payment)){
            partial_payment = 0;
        }

        var total = $('#total').val();

        var due_amount = total - partial_payment;

        console.log(due_amount);

        $('#paid_amount').val(partial_payment);
        $('#due_amount').val(due_amount);
    }else if($('#payment_status').val() == "UP"){

        $('#paid_amount').val(0);
        $('#due_amount').val(total);

    }else if($('#payment_status').val() == "P"){
        $('#paid_amount').val(total);
        $('#due_amount').val(0);
    }


});


$(document).on("keyup mouseup", "form#create-invoice-form #discount", function (event) {
    var discount = parseFloat($(this).val());

    if(isNaN(discount)){
        discount = 0;
        $('#discount_amount_tr').hide();
    }else{
        $('#discount_amount_tr').show();
    }


    var total = 0;


    $('input[id=total_price]').each(function(){

        var total_price = parseInt($(this).val());
        if(isNaN(total_price)){
            total_price = 0;
        }

        total = total + total_price;
    });

    var bid_amount = 0;


    bid_amount = total;


    if($('#tax_type').val() == 'BD'){
        var tax = total / 100 * parseInt($('#tax').val());

        bid_amount = total + tax;
    }

    console.log(bid_amount);


    if($('input[name=discount_type]').is(':checked')){
        var discount = parseInt($('#discount').val());

                    if(isNaN(discount)){
                            discount = 0;
                        }

        if($('input[name=discount_type]:checked').val() == "P"){
            var percentage = bid_amount / 100 * discount;
            bid_amount = bid_amount - percentage;
            $('#discount_amount').val(percentage);
        }

        if($('input[name=discount_type]:checked').val() == "F"){
            bid_amount = bid_amount - discount;
            $('#discount_amount').val(parseInt($('#discount').val()));
        }
    }



    if($('#tax_type').val() == 'AD'){
        var tax = bid_amount / 100 * parseInt($('#tax').val());

        bid_amount = bid_amount + tax;
    }



    $('#total').val(bid_amount);

    if($('#payment_status').val() == "PP"){
    var partial_payment = parseInt($('#partial_payment').val());

        if(isNaN(partial_payment)){
            partial_payment = 0;
        }

        var total = $('#total').val();

        var due_amount = total - partial_payment;


        $('#paid_amount').val(partial_payment);
        $('#due_amount').val(due_amount);
    } else if($('#payment_status').val() == "UP"){

        $('#paid_amount').val(0);
        $('#due_amount').val(total);

    }else if($('#payment_status').val() == "P"){
        $('#paid_amount').val(total);
        $('#due_amount').val(0);
    }


});



$(document).on("click", "#relationFather, #relationMother", function (event) {

    var total = 0;


    $('input[id=total_price]').each(function(){

        var total_price = parseInt($(this).val());
        if(isNaN(total_price)){
            total_price = 0;
        }


        total = total + parseInt($(this).val());
    });

    var bid_amount = 0;


    bid_amount = total;

    if($('#tax_type').val() == 'BD'){
        var tax = total / 100 * parseInt($('#tax').val());

        bid_amount = total + tax;
    }

    console.log(bid_amount);



    if($(this).is(':checked')){
        var discount = parseInt($('#discount').val());

                    if(isNaN(discount)){
                            discount = 0;
                        }


        if($(this).val() == "P"){
            var percentage = bid_amount / 100 * discount;
            bid_amount = bid_amount - percentage;
            $('#discount_amount').val(percentage);
        }else{
            bid_amount = bid_amount - discount;
            $('#discount_amount').val(parseInt($('#discount').val()));
        }
    }

    if($('#tax_type').val() == 'AD'){
        var tax = bid_amount / 100 * parseInt($('#tax').val());

        bid_amount = bid_amount + tax;
    }



    $('#total').val(bid_amount);

    if($('#payment_status').val() == "PP"){
        var partial_payment = parseInt($('#partial_payment').val());

        if(isNaN(partial_payment)){
            partial_payment = 0;
        }

        var total = $('#total').val();

        var due_amount = total - partial_payment;

        console.log(due_amount);

        $('#paid_amount').val(partial_payment);
        $('#due_amount').val(due_amount);
    }else if($('#payment_status').val() == "UP"){

        $('#paid_amount').val(0);
        $('#due_amount').val(total);

    }else if($('#payment_status').val() == "P"){
        $('#paid_amount').val(total);
        $('#due_amount').val(0);
    }

});



$(document).on("click", "form#create-invoice-form #delete-tender-product", function (event) {
    $(this).closest("tr").remove();


    var total = 0;


    $('input[id=total_price]').each(function(){
        total = total + parseInt($(this).val());
    });

    var bid_amount = 0;


    bid_amount = total;
    if($('input[name=discount_type]').is(':checked')){
        var discount = parseInt($('#discount').val());

                    if(isNaN(discount)){
                            discount = 0;
                        }


        if($('input[name=discount_type]:checked').val() == "P"){
            var percentage = total / 100 * discount;
            bid_amount = total - percentage;
            $('#discount_amount').val(percentage);
        }

        if($('input[name=discount_type]:checked').val() == "F"){
            bid_amount = total - discount;
            $('#discount_amount').val(parseInt($('#discount').val()));
        }
    }



    $('#total').val(bid_amount);

    if($('#payment_status').val() == "PP"){
                    var partial_payment = parseInt($('#partial_payment').val());

                    if(isNaN(partial_payment)){
                        partial_payment = 0;
                    }

                    var total = $('#total').val();

                    var due_amount = total - partial_payment;

                    console.log(due_amount);

                    $('#paid_amount').val(partial_payment);
                    $('#due_amount').val(due_amount);
                }




});

$(document).on("change", "form#create-invoice-form #currency", function (event) {

    var text = $('option:selected', this).text();
    var a = text.replace('(',' ').replace(')',' ').replace(' ',' ').split(' ');

    $('span#currency_id').html(a[2]);


});




$(document ).ready(function() {
    if($('#payment_status').val() != "PP"){
        $('#partial_payment_div').hide();
    }

});

$(document).on("change", "form#create-invoice-form #payment_status", function (event) {

    if($(this).val() == 'PP'){

        $('#partial_payment_div').show();


        var partial_payment = parseInt($('#partial_payment').val());

        if(isNaN(partial_payment)){
            partial_payment = 0;
        }

        var total = $('#total').val();

        var due_amount = total - partial_payment;

        console.log(due_amount);

        $('#paid_amount').val(partial_payment);
        $('#due_amount').val(due_amount);




    }

    if($(this).val() == 'P'){
        $('#partial_payment_div').hide();

        var total = $('#total').val();

        $('#paid_amount').val(total);
        $('#due_amount').val(0);

    }

    if($(this).val() == 'UP'){
        $('#partial_payment_div').hide();

        var total = $('#total').val();

        $('#paid_amount').val(0);
        $('#due_amount').val(total);

    }


});

$(document).on("keyup mouseup", "form#create-invoice-form #partial_payment", function (event) {




        var partial_payment = parseInt($('#partial_payment').val());

        if(isNaN(partial_payment)){
            partial_payment = 0;
        }

        var total = $('#total').val();

        var due_amount = total - partial_payment;

        console.log(due_amount);

        $('#paid_amount').val(partial_payment);
        $('#due_amount').val(due_amount);




});




$(document ).ready(function() {


    if($('#discount').val() != "" && $('input[name=discount_type]').is(":checked")){
        $('#discount_amount_tr').show();
    }else{
        $('#discount_amount_tr').hide();
    }

});



$(document).on("submit", "form#create-invoice-form", function (event) {


    var errors = [];


    if($('#customer').val() == ''){
        errors.push('Customer is required');
    }

    if($('#payment_method').val() == ''){
        errors.push('Payment method is required');
    }


    if($('#invoice_no').val() == ''){
        errors.push('Invoice no is required');
    }



    $("select[name='products[]']").each( function(key){
        if($(this).val() == '' || $(this).val() == 'none'){
            errors.push('product fields are required');
        }
    });


    $("input[name='quantity[]']").each( function(key){
        if($(this).val() == '' || $(this).val() == 'none'){
            errors.push('quantity fields are required');
        }
    });




    var message = "<div class='alert alert-danger'>";

    $.each(errors, function (key, error) {

          message += error;
          message += "</br>";

    });

    message += "</div>";


    $('#errors-message').html(message);


    if(errors.length > 0){
        return false;
    }



});











</script>
@endsection

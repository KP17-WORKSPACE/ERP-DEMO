@extends('backEnd.master')
@section('mainContent')

<link rel="stylesheet" href="{{ asset('/public/css/invoiceCreate.css') }}">
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.edit') @lang('lang.invoice') </h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.invoice')</a>
                <a href="#">@lang('lang.edit') @lang('lang.invoice')</a>
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

                                <div class="row mt-40">

                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <select class="niceSelect up_select w-100 bb form-control{{ $errors->has('fees_type') ? ' is-invalid' : '' }}" name="customer" id="customer">
                                                <option data-display="@lang('lang.select') @lang('lang.customer') *" value="">@lang('lang.select') @lang('lang.customer') *</option>
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
                                                <option data-display="@lang('lang.select') @lang('lang.payment') method *" value="">@lang('lang.select') @lang('lang.payment') method *</option>
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
                                                <option data-display="project *" value="">@lang('lang.project') *</option>
                                                @foreach($projects as $value)
                                                    <option value="{{$value->id}}" {{@$value->id == @$invoice->project_id?'selected':''}}>{{@$value->tender_no}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <div class="input-effect">
                                                <input class="primary-input form-control{{ $errors->has('tax') ? ' is-invalid' : '' }}" type="text" name="tax" autocomplete="off" step="any" value="{{@$invoice->tax_percentage}}">
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
                                                    <option value="{{@$value->id}}" {{@$value->id == $invoice->currency_id?'selected':''}}>{{@$value->code}} ({{@$value->symbol}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <input class="primary-input date" id="endDate" type="text" name="invoice_date" value="">
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
                                                        <input class="primary-input date" id="endDate" type="text" name="due_date" value="{{@$invoice->invoice_due_date != ""? date('d/m/Y', strtotime(@$invoice->invoice_due_date)):''}}">
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

                                    @php

                                        @$invoice_number = explode('-', @$invoice->invoice_number);

                                    @endphp

                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <div class="input-effect invoice-custom">
                                                <span class="invoive-prefix">{{@$invoice_setting->prefix}}-</span>
                                                <input class="invoive-number primary-input form-control{{ $errors->has('invoice_no') ? ' is-invalid' : '' }}" type="text" name="invoice_no" autocomplete="off" step="any" value="{{isset($invoice_number[1])? @$invoice_number[1]:@$invoice_number[0]}}">
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
                                                <option data-display="recurring cycle *" value="">@lang('lang.recurring') @lang('lang.cycle')  *</option>
                                                <option value="M" {{@$invoice->recurring_cycle == "M"?'selected':''}}>@lang('lang.Monthly') </option>
                                                <option value="Q" {{@$invoice->recurring_cycle == "Q"?'selected':''}}>@lang('lang.Quarterly') </option>
                                                <option value="SA" {{@$invoice->recurring_cycle == "SA"?'selected':''}}>@lang('lang.Semi') @lang('lang.Annually') </option>
                                                <option value="A" {{@$invoice->recurring_cycle == "A"?'selected':''}}> @lang('lang.Annually')</option>
                                                <option value="OT" {{@$invoice->recurring_cycle == "OT"?'selected':''}}>@lang('lang.once') @lang('lang.time')</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="single_invoice">
                                            <select class="niceSelect w-100 bb form-control{{ $errors->has('payment_status') ? ' is-invalid' : '' }}" name="payment_status" id="payment_status">
                                                <option data-display="payment status *" value="">@lang('lang.payment') @lang('lang.status') *</option>
                                                <option value="UP" {{@$invoice->payment_status == "UP"?'selected':''}}>@lang('lang.unpaid')</option>
                                                <option value="P" {{@$invoice->payment_status == "P"?'selected':''}}>@lang('lang.paid')</option>
                                                <option value="PP" {{@$invoice->payment_status == "PP"?'selected':''}}>@lang('lang.PARTIALLY') @lang('lang.paid')</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-25">
                                    <div class="col-lg-12 text-right">
                                        <button type="button" class="primary-btn small fix-gr-bg" id="addInvoiceRowProduct">
                                        <span class="ti-plus pr-2"></span>
                                        @lang('lang.add') @lang('lang.item')
                                    </button>
                                    </div>
                                </div>
                                <table class="display school-table school-table-style without-box-shadow" cellspacing="0" width="100%" id="product-table">
                                    <thead>
                                        <tr>
                                             <th>@lang('lang.product_name') *</th>
                                            <th>@lang('lang.description')</th>
                                            <th>@lang('lang.quantity') *</th>
                                            <th>@lang('lang.price')</th>
                                            <th>@lang('lang.total')</th>
                                            <th class="text-center">@lang('lang.action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 0; $total = 0; @endphp
                                        @foreach($invoice->invoiceProducts as $invoiceProduct)
                                        @php $i++; @endphp
                                        <tr class="product_table">
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
                                                    type="number" step="any" id="quantity" name="quantity[]" autocomplete="off" value="{{@$invoiceProduct->quantity}}">
                                                    <span class="focus-border"></span>

                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-effect">
                                                    <input class="primary-input form-control"
                                                    type="number" step="any" id="unit_price" name="unit_price[]" autocomplete="off" value="{{@$invoiceProduct->price}}">
                                                    <span class="focus-border"></span>
                                                </div>
                                            </td>
                                            <input type="hidden" name="product_quantity" id="product_quantity">
                                            <td>
                                                <div class="input-effect">
                                                    <input class="primary-input form-control"
                                                    type="number" step="any" id="total_price" name="total_price[]" autocomplete="off" readonly=""  value="{{@$invoiceProduct->price * @$invoiceProduct->quantity}}">
                                                    <span class="focus-border"></span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($i != 1)
                                                <button class="primary-btn ml-30 icon-only fix-gr-bg" type="button"  id="delete-tender-product">
                                                     <span class="ti-trash"></span>
                                                </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-lg-12">
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
                                                    <textarea class="primary-input form-control" cols="0" rows="4" name="public_note"></textarea>
                                                </div>
                                                <!-- End Profile Tab -->
                                                <!-- Start Fees Tab -->
                                                <div role="tabpanel" class="tab-pane fade" id="privateNotes">
                                                    <textarea class="primary-input form-control" cols="0" rows="4" name="private_note"></textarea>
                                                </div>
                                                <!-- End Profile Tab -->
                                                <!-- Start Fees Tab -->
                                                <div role="tabpanel" class="tab-pane fade" id="terms">
                                                    <textarea class="primary-input form-control" cols="0" rows="4" name="terms_note"></textarea>
                                                </div>
                                                <!-- End Profile Tab -->
                                                <!-- Start Fees Tab -->
                                                <div role="tabpanel" class="tab-pane fade" id="footer">
                                                    <textarea class="primary-input form-control" cols="0" rows="4" name="footer_note"></textarea>
                                                </div>
                                                <!-- End Profile Tab -->
                                                <!-- Start Fees Tab -->
                                                <div role="tabpanel" class="tab-pane fade" id="signature">
                                                    <input type="text" name="signature_person" class="primary-input form-control" placeholder="person name">
                                                    <input type="text" name="signature_company" class="primary-input form-control" placeholder="company name">
                                                </div>
                                                <!-- End Profile Tab -->
                                            </div>
                                        </div>
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
                                                    <option data-display="Select Department *" value="">@lang('lang.select') @lang('lang.department') *</option>
                                                    @foreach($departments as $key=>$value)
                                                    <option value="{{$value->id}}" {{ isset($editData)?$editData->department_id==$value->id? 'selected="selected"':'':''}} >{{@$value->name}}</option>
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
                                                    <option data-display="Select Designations *" value="">@lang('lang.select') @lang('lang.Designation') *</option>
                                                    @foreach($designations as $key=>$value)
                                                    <option value="{{@$value->id}}" {{ isset($editData)?@$editData->designation_id==@$value->id? 'selected="selected"':'':''}} >{{@$value->title}}</option>
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
                                                        placeholder="{{isset($editData->file) && $editData->file != '' ? showPicName(@$editData->file):'Customer Photo '}}"
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
                                                        <label class="primary-btn small fix-gr-bg" for="staff_photo">@lang('lang.browse')</label>
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
                    $(this).closest("tr").find('#quantity').val(1);

                    $(this).closest("tr").find('#unit_price').val();
                    $(this).closest("tr").find('#product_quantity').val();
                }


                $(this).closest("tr").find('#quantity').val(1);

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
    var quantity = parseInt($(this).val());


if($(this).val() != ""){
    if(product_quantity >= quantity){

        if($(this).val() != ""){
            var quantity = parseFloat($(this).val());

                var unit_price = parseFloat($(this).closest("tr").find("input[id=unit_price]").val());


                if(isNaN(unit_price)){
                    unit_price = 0;
                }

                var total_price = quantity * unit_price;
                $(this).closest("tr").find("input[id=total_price]").val(total_price);

                var total = 0;


                $('input[id=total_price]').each(function(){
                    total = total + parseInt($(this).val());
                });

                var bid_amount = 0;


                bid_amount = total;
                if($('input[name=discount_type]').is(':checked')){
                    if($('input[name=discount_type]:checked').val() == "P"){
                        var percentage = total / 100 * parseInt($('#discount').val());
                        bid_amount = total - percentage;
                    }

                    if($('input[name=discount_type]:checked').val() == "A"){
                        bid_amount = total - parseInt($('#discount').val());
                    }
                }



                $('#total').val(total);
                $('#total_amount').val(bid_amount);




        }
    }else{
        $(this).val('');
        $(this).closest("tr").find('#total_price').val('');
    }

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
        total = total + parseInt($(this).val());
    });

    var bid_amount = 0;


    bid_amount = total;
    if($('input[name=discount_type]').is(':checked')){
        if($('input[name=discount_type]:checked').val() == "P"){
            var percentage = total / 100 * parseInt($('#discount').val());
            bid_amount = total - percentage;
        }

        if($('input[name=discount_type]:checked').val() == "A"){
            bid_amount = total - parseInt($('#discount').val());
        }
    }



    $('#total').val(total);
    $('#total_amount').val(bid_amount);


});


$(document).on("keyup mouseup", "form#create-invoice-form #discount", function (event) {
    var discount = parseFloat($(this).val());

    if(isNaN(discount)){
        discount = 0;
    }


    var total = 0;


    $('input[id=total_price]').each(function(){
        total = total + parseInt($(this).val());
    });

    var bid_amount = 0;


    bid_amount = total;
    if($('input[name=discount_type]').is(':checked')){
        if($('input[name=discount_type]:checked').val() == "P"){
            var percentage = total / 100 * parseInt($('#discount').val());
            bid_amount = total - percentage;
        }

        if($('input[name=discount_type]:checked').val() == "A"){
            bid_amount = total - parseInt($('#discount').val());
        }
    }



    $('#total').val(total);
    $('#total_amount').val(bid_amount);


});



$(document).on("click", "#relationFather, #relationMother", function (event) {

    var total = 0;


    $('input[id=total_price]').each(function(){
        total = total + parseInt($(this).val());
    });

    var bid_amount = 0;


    bid_amount = total;
    if($(this).is(':checked')){
        if($(this).val() == "P"){
            var percentage = total / 100 * parseInt($('#discount').val());
            bid_amount = total - percentage;
        }else{
            bid_amount = total - parseInt($('#discount').val());
        }
    }



    $('#total').val(total);
    $('#total_amount').val(bid_amount);
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
        if($('input[name=discount_type]:checked').val() == "P"){
            var percentage = total / 100 * parseInt($('#discount').val());
            bid_amount = total - percentage;
        }

        if($('input[name=discount_type]:checked').val() == "A"){
            bid_amount = total - parseInt($('#discount').val());
        }
    }



    $('#total').val(total);
    $('#total_amount').val(bid_amount);


});




</script>
@endsection

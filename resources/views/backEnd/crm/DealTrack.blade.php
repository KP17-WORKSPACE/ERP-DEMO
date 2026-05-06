
<?php try { ?>
   
    <style>
        .head {font-size: 14px;}
        .card h2{font-size: 14px;}
        .card h4{font-size: 14px;}
        .card h5{font-size: 14px;}
        .card h6{font-size: 12px;}
        .card p{font-size: 11px;}
        .card span{font-size: 11px;}
        .card b{font-size: 11px;}
        .modal-body h4{font-size: 17px;}
        .table th, .table td { padding: 1px; font-size: 12px; }
    </style>

    <style>
            #data-details label {
                font-weight: 600 !important;
                background-color: #deebe1 !important;
                margin-bottom: 3px !important;
                text-align: center !important;
                color: #212529 !important;
            }

            #data-details .green-heading{
              
                text-align: center !important;
               
            }
             #data-details .green-heading p{
                font-weight: 600 !important;
                background-color: #deebe1 !important;
                margin-bottom: 3px !important;
                text-align: center !important;
                color: #212529 !important;
            }

            #data-details .form-control-plaintext {
                text-align: center !important;
            }
        </style>


                            
                            <div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
                                <h4 class="purchase-order-content-header-left">
                                   <a href="{{ url('crm-deals/show/'.$del->id) }}" class="text-dark font-weight-600">  {{ $del->deal_code->code }} </a>
                                    <span class="badge bg-info">{!! App\SysHelper::deal_type_new($del->isproject) !!}</span>
                                </h4>
                                <div class="purchase-order-content-header-right">
                                    
                                    <a href="{{ url('crm-deals/'.$del->id) }}"  class="btn btn-light text-dark">
                                       <i class="ico icon-outline-document-text text-success"></i>  View
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ico icon-outline-hamburger-menu"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                         
                                        </ul>
                                    </div> 
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="tab-pane fade show active" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                                        <div class="row">

                                            <div class="col-3 mb-2">
                                                <label class="form-label">Customer Name </label>

                                                @php

                                                $deliveryCompany = strtolower(str_replace(' ', '', $del->delivery_company));
                                                $customerName = strtolower(str_replace(' ', '', $del->customername->name));

                                                @endphp

                                          
                                                @if($deliveryCompany == $customerName)
                                              <div class="form-control-plaintext truncate-text-custom"><a href="{{url('customers')}}/{{@$del->customername->id}}" target="_blank">{{ $del->customername->name }}</a> </div>

                                                @else
                                              <div class="form-control-plaintext truncate-text-custom"><a class=" text-warning" href="{{url('customers')}}/{{@$del->customername->id}}" target="_blank">{{ $del->customername->name }}</a> </div>

                                                @endif

                                            </div>

                                            <div class="col-2 mb-2" style="width: 15%">
                                                <label class="form-label">Deal Name </label>
                                              <div class="form-control-plaintext truncate-text-custom">{{ $del->deal_name }} </div>
                                            </div>
                                   
                                           
                                            <div class="col-1 mb-2" style="width: 15%">
                                                <label class="form-label">Brand:<br /></label>
                                                <div class="form-control-plaintext truncate-text-custom">

                                                @if($del->tags != "")
                                                <?php $myArray = explode(',', $del->tags); ?>
                                                @foreach ($myArray as $item)
                                                {{ $item }}
                                                @endforeach

                                                @else
                                                --
                                                @endif
                                                </div> 
                                            </div>
                                        
                                            
                                          
                                            <div class="col-2 mb-2" style="width: 15%">
                                                <label class="form-label">Deal Value</label> 
                                                     <div class="form-control-plaintext truncate-text-custom">
                                                    {{ App\SysHelper::currancy_format_deal($del->deal_value,$del->company_id) }} {{ $del->dealcurrency->code }}
                                                </div>
                                            </div>

                                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 12 || Auth::user()->role_id == 8)

                                              <div class="col-2 mb-2" style="width: 15%">
                                                <label class="form-label">Profit</label>  
                                                     <div class="form-control-plaintext truncate-text-custom">

                                                    {{ App\SysHelper::currancy_format_deal(($del->deal_profit),$del->company_id) }} {{ $del->dealcurrency->code }} 
                                                    <?php
                    $dealvalue = $del->deal_value;
                    $dealprofit = $del->deal_profit;
                    if($dealprofit!=0 && $dealvalue != 0){ $dealpercentage = $dealprofit / $dealvalue * 100; }
                    else{ $dealpercentage=0; }
                    ?>

                                                    <span class="text-success">{{ @App\SysHelper::com_curr_format($dealpercentage,2,'.',',') }}%</span></div>
                                            </div>
                                                
                                            @endif
                                          

                                            <div class="col-2 mb-2" style="width: 15%">
                                                <label class="form-label">Sales Person</label>
                                                     <div class="form-control-plaintext truncate-text-custom">

                                                    {{ @$del->ownername->first_name }} {{ @$del->ownername->middle_name }} {{ @$del->ownername->last_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="tab-wrap mb-3">
                                <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="extra-fields-tab" data-bs-toggle="tab" data-bs-target="#customer-info" type="button" role="tab" aria-controls="extra-fields" aria-selected="true">Customer Info</button>
                                    </li>
                                     <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="vat-details-tab" data-bs-toggle="tab" data-bs-target="#delivery-location" type="button" role="tab" aria-controls="vat-details" aria-selected="false">Delivery Location /Address</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="shipping-details-info-tab" data-bs-toggle="tab" data-bs-target="#sales-person-info" type="button" role="tab" aria-controls="shipping-details-info" aria-selected="false">Sales Person Info</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="shipping-details-info-tab" data-bs-toggle="tab" data-bs-target="#submited-details" type="button" role="tab" aria-controls="shipping-details-info" aria-selected="false">Submited Details</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="shipping-details-info-tab" data-bs-toggle="tab" data-bs-target="#internal-note" type="button" role="tab" aria-controls="shipping-details-info" aria-selected="false">Internal Note</button>
                                    </li>
                                    {{-- <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="shipping-details-info-tab" data-bs-toggle="tab" data-bs-target="#downloads" type="button" role="tab" aria-controls="shipping-details-info" aria-selected="false">Downloads</button>
                                    </li> --}}
                                </ul>
                                <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                                    <div class="tab-pane fade " id="customer-info" role="tabpanel" aria-labelledby="extra-fields-tab">

                                        

                                        
                                           <div class="row text-start">

                                                @if (App\SysHelper::get_company_status($del->customername) == 0)
                                                    <a class="btn-sm btn-light" style="float: right" target="_blank" href="{{ url('customers/' . $del->customername->id) . '?customer_action=edit' }}">Update Info</a>
                                                    @else
                                                
                                                @endif


                                                <!-- Sales Person -->
                                                <div class="col-xxl-3 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Customer Name	</p>
                                                    {{  $del->customername->name }}
                                                </div>

                                                <!-- Mobile -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Contact Person	</p>
                                               {{ $del->cust_name }}
                                                </div>

                                                <!-- Email -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Designation</p>
                                                    {{ $del->designation }}
                                                </div>

                                                <!-- Ext No -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Mobile</p>
                                                    {{ $del->cust_no }}
                                                </div>

                                                <!-- Source -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Email</p>
                                                {{ $del->cust_email }}
                                                </div>

                                                <!-- Close Date -->
                                                <div class="col-xxl-3 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Address 1	</p>
                                                    {{ @$del->customername->addresses->first()->address }}
                                                </div>

                                                <!-- Added By -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Address 2	</p>
                                                    {{ @$del->customername->addresses->first()->address2 }}, {{ @$del->customername->addresses->first()->city }}
                                                </div>

                                                <!-- Added On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">State & Country	</p>
                                                   {{ @$del->customername->addresses->first()->statename->name }}, {{ @$del->customername->addresses->first()->countryname->name }}
                                                </div>

                                                <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">PO Box	</p>
                                                   {{ @$del->customername->addresses->first()->zip_code }}
                                                </div>

                                            </div>

                                        {{-- <div class="row gap-rows">
                                            <div class="col-12">
                                                @if (App\SysHelper::get_company_status($del->customername) == 0)
                    <a class="btn-sm btn-light" style="float: right" target="_blank" href="{{ url('customers/' . $del->customername->id) . '?customer_action=edit' }}">Update Info</a>
                    @else
                
                    @endif

                                                <table class="detail-item-table-noborder">
                                                    <thead>

                                                    <tr>
                                                        <td class="text-start">Customer Name</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{  $del->customername->name }}</td>
                                                    </tr>
                                                   
                                                    <tr>
                                                        <td class="text-start">Contact Person</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ $del->cust_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">Designation</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ $del->designation }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">Mobile</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ $del->cust_no }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">Email</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ $del->cust_email }}</td>
                                                    </tr>
                                                    <tr>
                                                       
                                                        <td class="text-start">Address 1</td>
                                                        <td class="text-start" style="width: 400px">:&nbsp;&nbsp;{{ @$del->customername->addresses->first()->address }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">Address 2</td>
                                                        <td class="text-start" style="width: 400px">:&nbsp;&nbsp;{{ @$del->customername->addresses->first()->address2 }}, {{ @$del->customername->addresses->first()->city }}</td>
                                                    </tr>
                                                      <tr>
                                                        <td class="text-start">State & Country</td>
                                                        <td class="text-start" style="width: 400px">:&nbsp;&nbsp;{{ @$del->customername->addresses->first()->statename->name }}, {{ @$del->customername->addresses->first()->countryname->name }}</td>
                                                    </tr>
                                                     </tr>
                                                        <tr>
                                                        <td class="text-start">PO Box</td>
                                                        <td class="text-start" style="width: 400px">:&nbsp;&nbsp;{{ @$del->customername->addresses->first()->zip_code }}</td>
                                                    </tr>
                                
                                                    </thead>
                                                </table>
                                            </div>
                                            <!-- <div class="col-4">
                                                <label class="form-label"><b>Customer:</b> BARAKAT VEGETABLES & FRUITS CO LLC</label>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label"><b>Contact Person:</b> Mr Yousuf Muhammad:</label>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label"><b>Designation:</b> Purchasing Manager</label>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label"><b>Mobile:</b> +971543087433</label>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label"><b>Mobile:</b> yousuf.m@barakatgroup.ae</label>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label"><b>Address:</b> PO Box 11286, Behind Nad Al Hamar Avenue,, 9th Street, Nad Al Hamar, Dubai, Dubai, United Arab Emirates</label>
                                            </div> -->
                                        </div> --}}
                                    </div>

                                     <div class="tab-pane fade" id="delivery-location" role="tabpanel" aria-labelledby="vat-details-tab">


                                         <div class="row text-start">

                                                <!-- Company -->
                                                <div class="col-xxl-3 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Company</p>
                                                   {{ @$del->delivery_company }}
                                                </div>

                                                <!-- Mobile -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Contact Person</p>
                                                {{ @$del->delivery_name }}
                                                </div>

                                                <!-- Email -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Telephone</p>
                                                    {{ @$del->delivery_number }}
                                                </div>

                                                <!-- Ext No -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Email</p>
                                                    {{ @$del->delivery_email }}
                                                </div>

                                                <!-- Source -->
                                                <div class="col-xxl-3 col-lg-6 col-md-6 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Address</p>
                                               {{ @$del->address }}
                                                </div>

                                                <!-- Close Date -->
                                                <div class="col-xxl-3 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">Address 2</p>
                                                   {{ @$del->delivery_address2 }}, {{ @$del->delivery_city }}
                                                </div>

                                                <!-- Added By -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">State & Country</p>
                                                    {{ @$del->state->name }}, {{ @$del->country->name }}
                                                </div>

                                                <!-- Added On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0">PO Box</p>
                                                    {{ @$del->delivery_zip_code  }}
                                                </div>

                                              

                                            </div>

                                        {{-- <div class="row gap-rows">
                                             <div class="col-12">
                                                <table class="detail-item-table-noborder">
                                                    <thead>
                                                    <tr>
                                                        <td class="text-start">Company</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ @$del->delivery_company }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start form-label text-dark">Contact Person</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ @$del->delivery_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">Telephone</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ @$del->delivery_number }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">Email</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ @$del->delivery_email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">Address 
                                                            
                                                            </td>
                                                        <td class="text-start"  style="width: 400px">:&nbsp;&nbsp; {{ @$del->address }}
                    
                                                        </td>
                                                    </tr>
                                                      <tr>
                                                        <td class="text-start">Address 2</td>
                                                        <td class="text-start" style="width: 400px">:&nbsp;&nbsp;{{ @$del->delivery_address2 }}, {{ @$del->delivery_city }}</td>
                                                    </tr>
                                                      <tr>
                                                        <td class="text-start">State & Country</td>
                                                        <td class="text-start" style="width: 410px">:&nbsp;&nbsp;{{ @$del->state->name }}, {{ @$del->country->name }}</td>
                                                    </tr>
                                                        <tr>
                                                        <td class="text-start">PO Box</td>
                                                        <td class="text-start" style="width: 400px">:&nbsp;&nbsp;{{ @$del->delivery_zip_code  }}</td>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div> --}}
                                    </div>

                                    <div class="tab-pane fade" id="sales-person-info" role="tabpanel" aria-labelledby="shipping-details-info-tab">


                                           <div class="row text-start">

                                                <!-- Sales Person -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Sales Person</p>
                                                    {{ @$del->ownername->first_name }} {{ @$del->ownername->middle_name }} {{ @$del->ownername->last_name }}
                                                </div>

                                                <!-- Mobile -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Mobile</p>
                                                {{ @$del->ownername->mobile }}
                                                </div>

                                                <!-- Email -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Email</p>
                                                    {{ @$del->ownername->email }}
                                                </div>

                                                <!-- Ext No -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Ext No</p>
                                                    {{ @$del->ownername->ext_no ?? '--' }}
                                                </div>

                                                <!-- Source -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Source</p>
                                                @if (@$del->source != ''){{ @$del->source }}@if(@$del->source_o != '') - {{ @$del->source_o }} @endif @endif
                                                </div>

                                                <!-- Close Date -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Close Date</p>
                                                    {{ date('m/d/Y', strtotime(@$del->estimated_close_date)) }}
                                                </div>

                                                <!-- Added By -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Added By</p>
                                                    {{ @$deal->createdby->full_name }}
                                                </div>

                                                <!-- Added On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Added On</p>
                                                    {{ date('d/m/Y h:i A', strtotime(@$del->created_at)) }}
                                                </div>

                                                <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Updated On</p>
                                                    {{ date('d/m/Y h:i A', strtotime(@$del->updated_at)) }}
                                                </div>

                                            </div>

                                        {{-- <div class="row gap-rows">
                                            <div class="col-12">
                                                <table class="detail-item-table-noborder">
                                                    <thead>
                                                    <tr>
                                                        <td class="text-start" width="90px">Sales Person</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ @$del->ownername->first_name }} {{ @$del->ownername->middle_name }} {{ @$del->ownername->last_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">Mobile</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ @$del->ownername->mobile }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">Email</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ @$del->ownername->email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">Ext No</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ @$del->ownername->ext_no ?? '--' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">Source</td>
                                                        <td class="text-start">:&nbsp;&nbsp;@if (@$del->source != ''){{ @$del->source }}@if(@$del->source_o != '') - {{ @$del->source_o }} @endif @endif</td>
                                                    </tr>

                                                  
                                                      
                                                    <tr>
                                                        <td class="text-start">Close Date</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ date('m/d/Y', strtotime(@$del->estimated_close_date)) }}</td>
                                                    </tr>
                                                    
                                                     <tr>
                                                        <td class="text-start">Added By</td>
                                                        <td class="text-start">:&nbsp;&nbsp;{{ @$deal->createdby->full_name }}</td>
                                                    </tr>
                                                  <tr>
                                                        <td class="text-start">Added On</td>
                                                        <td class="text-start">
                                                            :&nbsp;&nbsp;{{ date('d/m/Y h:i:s A', strtotime(@$del->created_at)) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-start">Updated On</td>
                                                        <td class="text-start">
                                                            :&nbsp;&nbsp;{{ date('d/m/Y h:i:s A', strtotime(@$del->updated_at)) }}
                                                        </td>
                                                    </tr>

                                                    </thead>
                                                    <tbody>
                                                    
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div> --}}
                                    </div>
                                   
                                    <div class="tab-pane fade show active" id="submited-details" role="tabpanel" aria-labelledby="vat-details-tab">


                                            <div class="row text-start">

                                                <!-- Sales Person -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Expected Delivery</p>
                                                   {{date('d/m/Y', strtotime(@$deal->delivery_date))}}
                                                </div>

                                                <!-- Mobile -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Payment Terms</p>
                                           <span class="truncate-text-custom">{{@$deal->paymentterms->title}} @if(@$deal->payment_terms == 22) - {{@$deal->payment_terms_txt}} @endif</span>    
                                                </div>

                                                <!-- Email -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Payment mode</p>
                                                   @if($deal->payment_mode==1) Cash @endif
                        @if($deal->payment_mode==2) Cheque @endif
                        @if($deal->payment_mode==3) Bank Transfer @endif
                        @if($deal->payment_mode==4) Open Credit @endif
                        @if($deal->payment_mode==5) Credit Card @endif
                        @if($deal->payment_mode==6) Bank TT @endif
                        @if($deal->payment_mode==7) Letter of Credit @endif

                        @if($deal->payment_mode_sec==1) , Cash @endif
                        @if($deal->payment_mode_sec==2) , Cheque @endif
                        @if($deal->payment_mode_sec==3) , Bank Transfer @endif
                        @if($deal->payment_mode_sec==4) , Open Credit @endif
                        @if($deal->payment_mode_sec==5) , Credit Card @endif
                        @if($deal->payment_mode_sec==6) , Bank TT @endif
                        @if($deal->payment_mode_sec==7) , Letter of Credit @endif
                                                </div>

                                           

                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Product Purchase</p>
     <!-- Ext No -->
                                             @if($deal->purchease_required==1)
                                                <span class="">
                                                    Purchase Required
                                                    <?php try { ?>
                                                        @if($purchease[0]->validation == 3) <span class="text-muted"><span
                                                                class="text-success text-bold text-xs">(Under Purchase)</span></span> @endif
                                                    <?php } catch (\Throwable $th) {
                                                    } ?>
                                                    @if(session('logged_session_data.designation_id')==20)
                                                    <script type="text/javascript">
                                                        var blink = document.getElementById('blink');
                                                        setInterval(function () {
                                                            blink.style.opacity = (blink.style.opacity == 0 ? 1 : 0);
                                                        }, 500);
                                                    </script>
                                                    @endif
                                                </span>
                                                @endif
                                                </div>
                                                

                                                
                                                <!-- Source -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Partial Delivery</p>
                                                    @if($deal->partial_delivery==1)
                                                Partial Delivery
                                                @endif
                                                </div>
                                                

                                                <!-- Close Date -->
                                               
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Professional Service</p>
                                                     @if($deal->technical==1 || $deal->technical==0)

                                                      @if($deal->technical==0) NO @endif
                                                     @if($deal->technical==1) YES @endif
                                                        @endif
                                                </div>
                                             

                                        
                                                
                                                <!-- Added By -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Approval Not Required</p>
                                                            @if($deal->purchease_approval==0 || $deal->invoice_approval==0 || $deal->delivery_approval==0 ||
    $deal->receivables_approval==0)
    <span class="truncate-text-custom">
 @if($deal->purchease_approval==0) Purchase @endif
                                                    @if($deal->invoice_approval==0) , Invoice @endif
                                                    @if($deal->delivery_approval==0) , Delivery, @endif
                                                    @if($deal->receivables_approval==0) Receivables @endif
    </span>
                                                   
                                                    @endif
                                                </div>
                                               

                                                <!-- Added On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-1">LPO</p>
                                                     <?php $file = explode("|", $deal->lpo); ?>
                        @foreach ($file as $f)
                        @if (!empty($f))
                        <a class="btn-sm btn-light text-dark"
                            href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i
                                class="ico icon-bold-download-minimalistic fw-bold title-15 text-success"></i>
                            Download</a>
                        @else
                        N/A
                        @endif
                        @endforeach
                                                </div>

                                                <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-1">Cheque//TT Copy</p>
                                                    <?php $file = explode("|", $deal->cheque_copy); ?>
                        @foreach ($file as $f)
                        @if (!empty($f))
                        <a class="btn-sm btn-light text-dark"
                            href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i
                                class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i>
                            Download</a>
                             @else
                        N/A
                        @endif
                        @endforeach
                                                </div>


                                                  <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-1">Puchase Quote</p>
                                                    <?php $file = explode("|", $deal->purchease_quote); ?>
                        @foreach ($file as $f)
                        @if (!empty($f))
                        <a class="btn-sm btn-light text-dark"
                            href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i
                                class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i>
                            Download</a>
                             @else
                        N/A
                        @endif
                        @endforeach
                                                </div>

                                                    <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-1">Quotation</p>
                                                  <a class="btn-sm btn-light text-dark"
            href="{{url('crm-quote/'.$del->id.'/download/'.$del->quote_id)}}" target="_blank"><i
                class="ico text-success icon-bold-download-minimalistic fw-bold title-15 text-success"></i> Download</a>
                                                </div>

                                            </div>

                                     
                                        </div>
                                    
                                    <style>
                                        .comments-card span{
                                            font-size: 13px;
                                        }
                                        #scrollBox::-webkit-scrollbar {
                                            width: 3px;
                                        }

                                        #scrollBox::-webkit-scrollbar-thumb {
                                            border-radius: 10px;
                                        }
                                    </style>
                                    <div class="tab-pane fade" id="internal-note" role="tabpanel" aria-labelledby="vat-details-tab">
                                        <div class="row">

                                             <div class="col-7">
                                           
                                                 <div id="scrollBox"  style="max-height: 400px; overflow-y: auto;">
                    

                                                        @if (isset($comments))
                                                        <div class="mt-3">
                                                            @foreach ($comments as $cmts)



                                                                               <div class="card border-0 rounded-3 mb-3 comments-card">
                            <div class="card-body p-2">

                            

                                <!-- Top Row: Right-Aligned Icons -->
                                <div class="d-flex justify-content-between mb-1">


                        <!-- Comment -->
                                <p class="mb-2 fw-semibold @if ($cmts->deleted_at) text-decoration-line-through text-muted @endif" style="font-size:13px">
                                     {!!   nl2br($cmts->comments) !!}
                                </p>


                                <div class="d-flex align-items-baseline gap-2">
                                        @if ($cmts->commentsdoc)
                                                        <a href="{{ asset('public/uploads/crm_deal_doc/' . $cmts->commentsdoc) }}"
                                                        target="_blank" class="btn btn-sm btn-light me-1">
                                                            <i class="ico icon-bold-paperclip" style="font-size:13px"></i>
                                                        </a>
                                                    @endif

                                                    @if ($cmts->created_by == Auth::user()->id)
                                                        @if ($cmts->deleted_at)
                                                            <a href="{{ url('crm-deals-comments-restore/' . $cmts->id) }}"
                                                            onclick="return confirm('Are you sure you want to restore this comment?')"
                                                            class="btn btn-sm btn-light">
                                                                <i class="ico icon-bold-restart" style="font-size:13px"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ url('crm-deals-comments-delete/' . $cmts->id) }}"
                                                            onclick="return confirm('Are you sure you want to delete this comment?')"
                                                            class="btn btn-sm btn-light">
                                                                <i class="ico icon-outline-trash-bin-minimalistic" style="font-size:13px"></i>
                                                            </a>
                                                        @endif
                                            @endif
                                </div>


                                

                                </div>

                                <!-- Username + Date + Deleted At (Right-Aligned Below Icons) -->
                                <div class="text-end small text-muted">

                                    <span>
                                        <i class="ico icon-bold-user me-1"></i>
                                        {{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}
                                    </span>

                                    <span>•</span>

                                    <span>
                                        <i class="ico icon-bold-clock me-1"></i>
                                        {{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}
                                    </span>

                                    @if ($cmts->deleted_at)
                                    <span>•</span>
                                        
                                        <span class="text-danger">
                                            Deleted: {{ date('d/m/Y h:i A', strtotime($cmts->deleted_at)) }}
                                        </span>
                                    @endif

                                </div>

                            </div>
</div>

                                                            @endforeach
                                                        </div>

                                                        @endif



                                                 </div>
                                                   
                                                </div>
                                             <div class="col-5">

                                             


                                               
                                                <label class="font-weight-bold form-label">Internal Note</label>
                                                <input type="hidden" value="dealtrack" name="page">
                                                 <textarea name="comments" class="form-control" id="comments" cols="10" rows="3" required></textarea>
                                               
                                                <input type="hidden" id="commentsid" name="commentsid" value="{{ $deal->deal_id }}" />
                                                    <div class="row mt-2">
                                                        <div class="col-md-4 d-flex justify-content-start align-items-center">
                                                        <button type="button" id="submitComment"
                                                            class="btn btn-light d-inline-flex align-items-center gap-2">
                                                            <i class="ico icon-outline-add-square fs-5 text-success"></i>
                                                            <span>Add Note</span>
                                                        </button>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="file" class="form-control" name="commentsdoc" id="commentsdoc">
                                                    </div>

                                                    
                                                </div>
                                                

                                                </div>

                                                    <script>
                $(document).on('click', '#submitComment', function (e) {
                    e.preventDefault();

                    let formData = new FormData();
                    formData.append('comments', $('textarea[name="comments"]').val());
                    formData.append('commentsid', $('input[name="commentsid"]').val());

                    let fileInput = $('#commentsdoc')[0];
                    if (fileInput.files.length > 0) {
                        formData.append('commentsdoc', fileInput.files[0]);
                    }


                    $.ajax({
                        url: '{{ url('crm-deals-comments-add') }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        beforeSend: function () {
                           $("#loading_bg").css("display", "block");
                        },
                        success: function (response) {
                            console.log("response", response); // Debugging line to check response
                           
                           
                            $('textarea[name="comments"]').val('');
                            $('#commentsdoc').val('');
                            $("#loading_bg").css("display", "none");
                            location.reload();
                            // Optionally append new comment to comment list
                        },
                        error: function (xhr) {
                            $("#loading_bg").css("display", "none");
                            alert('Something went wrong: ' + xhr.responseText);
                        }
                    });
                });
            </script>

                                               

                                             <style>
                                                   .btn-fixed {
                                                        display: inline-block !important; /* optional if you still want inline behavior */
                                                        width: 116px;                     
                                                                     /* keep text centered */
                                                        white-space: nowrap;        
                                                        padding:0px 5px;      /* prevent wrapping */
                                                        }

                                                </style> 
                                            
                                        </div>
                                    </div>
                                   
                                </div>
                            </div>
                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-update-quote-sort-order', 'method' => 'POST', 'id' => 'crm-update-quote-sort-order']) }}
                            
                            
                            <div class="table-container">
                                <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                    <thead>
                                        <tr>
                                            <th width="50px"  class="text-center">No</th>
                                            <th  width="160px" class="text-center ">Part No</th>
                                            <th width="170px"   class="text-center">Description</th>
                                            {{-- <th width="80px"   class="text-center text-nowrap">Delivery</th> --}}
                                            <th width="80px"   class=" text-nowrap text-center">Cost</th>
                                            <th width="50px"   class=" text-nowrap text-center">Qty</th>
                                            <th width="80px"  class=" text-nowrap text-center">Unit Price</th>
                                            <th width="80px"  class=" text-nowrap text-center">Value</th>
                                            <th width="80px"  class=" text-nowrap text-center">Discount</th>
                                            <th width="80px"   class=" text-nowrap text-center">Taxable</th>
                                            <th width="80px"   class=" text-nowrap text-center">VAT</th>
                                            <th width="120px"   class=" text-nowrap text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $t_qty = 0; $t_value=0; $t_deli=0; $t_discount=0; $t_taxableamount=0; $t_vatamount=0; $t_price = 0; $t_discount = 0; $t_net_amount = 0; $t_cost=0;
                                $vat =$quoteitems->max('vat'); $deal_discount_sum_amount=0;?>
                                
                                
                                <tbody>
                                    @foreach ($quoteitems as $Item)
                                    @php
                                        $value = $Item->price * $Item->qty;
                                        $taxableamount = $value - $Item->discount;
                                        $vatamount = $taxableamount * $Item->vat / 100;
                                        $deli = App\SysHelper::get_deal_delivery_qty($Item->id);

                                        $t_cost += $Item->cost * $Item->qty;
                                        $t_deli += $deli;
                                        $t_qty += $Item->qty;
                                        $t_value += $value;
                                        $t_discount += $Item->discount;
                                        $t_taxableamount += $taxableamount;
                                        $t_vatamount += $vatamount;
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <input type="text" name="sort_id[]" value="{{ $Item->sort_id }}" class="text-center" style="width: 35px; border: none;">
                                            <input type="hidden" class="form-control" name="item_id[]" value="{{ $Item->id }}">
                                        </td>
                                        <td><?php try{ ?> {{ $Item->productname->part_number }} <?php }catch (\Exception $e){} ?></td>{{--  nl2br($Item->description)  --}}
                                        <td>{!! $Item->description !!}</td>
                                        {{-- <td class="text-center">{{ $deli }}</td> --}}
                                        <td class="text-end">{{ $Item->cost }}</td>
                                        <td class="text-center">{{ $Item->qty }}</td>
                                        <td class="text-end">{{ App\SysHelper::currancy_format($Item->price,$Item->currency_id) }}</td>
                                        <td class="text-end">{{ App\SysHelper::currancy_format($value,$Item->currency_id) }}</td>
                                        <td class="text-end">{{ App\SysHelper::currancy_format($Item->discount,$Item->currency_id) }}</td>
                                        <td class="text-end">{{ App\SysHelper::currancy_format($taxableamount,$Item->currency_id) }}</td>
                                        <td class="text-end">{{ App\SysHelper::currancy_format($vatamount,$Item->currency_id) }}</td>
                                        <td class="text-end text-nowrap">{{ App\SysHelper::currancy_format(($taxableamount + $vatamount),$Item->currency_id) }}</td>
                                    </tr>
                                    <?php $currency_id = $Item->currency_id; ?>
                                    @endforeach

                                    {{-- ✅ Empty row --}}
                                        <tr>
                                            <td colspan="11">&nbsp;</td>
                                        </tr>
                                    </tbody>
                                     <thead>
                                    <tr>
                                        <th> &nbsp;&nbsp;&nbsp; </th>
                                        <th></th>
                                        <th></th>
                                        {{-- <th class="text-center">{{ $t_deli }}</th> --}}
                                        <th class="text-end">{{ App\SysHelper::currancy_format($t_cost,$currency_id) }}</th>
                                        <th class="text-center">{{ $t_qty }}</th>
                                        <th></th>
                                        <th class="text-end">{{ App\SysHelper::currancy_format($t_value,$currency_id) }}</th>
                                        <th class="text-end">{{ App\SysHelper::currancy_format($t_discount,$currency_id) }}</th>
                                        <th class="text-end">{{ App\SysHelper::currancy_format($t_taxableamount,$currency_id) }}</th>
                                        <th class="text-end">{{ App\SysHelper::currancy_format($t_vatamount,$currency_id) }}</th>
                                        <th class="text-end text-nowrap">{{ $Item->currency->code }} {{ App\SysHelper::currancy_format($t_taxableamount+$t_vatamount,$currency_id) }}</th>
                                    </tr>
                                    @if($del->deal_discount > 0)
                                    <tr>
                                        <?php
                                        $deal_discount_taxable_amount = $del->deal_discount;
                                        $deal_discount_vat_amount = $del->deal_discount*($vat)/100;
                                        $deal_discount_sum_amount = $deal_discount_taxable_amount+$deal_discount_vat_amount;
                                        ?>
                                        <td colspan="7" class="text-end font-weight-600">Additional Discount</td>
                                        <td class="text-end font-weight-600">{{ App\SysHelper::currancy_format(($del->deal_discount), $currency_id) }}</td>
                                        <td class="text-end font-weight-600">{{ App\SysHelper::currancy_format(($deal_discount_taxable_amount), $currency_id) }}</td>
                                        <td class="text-end font-weight-600">{{ App\SysHelper::currancy_format(($deal_discount_vat_amount), $currency_id) }}</td>
                                        <td class="text-end font-weight-600">{{ $Item->currency->code }} {{ App\SysHelper::currancy_format(($deal_discount_sum_amount), $currency_id) }}</td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                 
                                        <th class="text-end">{{ App\SysHelper::currancy_format($t_cost,$currency_id) }}</th>
                                        <th class="text-center">{{ $t_qty }}</th>
                                        <th></th>
                                        <th class="text-end">{{ App\SysHelper::currancy_format($t_value,$currency_id) }}</th>
                                        <th class="text-end">{{ App\SysHelper::currancy_format($t_discount+$del->deal_discount,$currency_id) }}</th>
                                        <th class="text-end">{{ App\SysHelper::currancy_format($t_taxableamount-$deal_discount_taxable_amount, $currency_id) }}</th>
                                        <th class="text-end">{{ App\SysHelper::currancy_format($t_vatamount-$deal_discount_vat_amount, $currency_id) }}</th>                              
                                        <th class="text-end">{{ $Item->currency->code }} {{ App\SysHelper::currancy_format(($t_taxableamount+$t_vatamount-$deal_discount_sum_amount), $currency_id) }}</th>
                                    </tr>
                                    @endif 
                                </thead>
                                {{ Form::close() }}


                                <tbody >
                                    @if (count($poitems)>0)
                                    <?php $po_sum = 0; ?>
                                  <tr>
                                    <td style="height:20px"></td>
                                  </tr>
                                    <tr> 
                                        <td colspan="11"><b>Aditional Items (Purchase Order)</b></td>
                                    </tr>
                                    @foreach ($poitems as $Item)
                                    <tr>
                                        <td></td>
                                        <td>{{ $Item->partno }}</td>
                                        <td>{{ $Item->description }}</td>
                                      
                                        <td class="text-end">{{ @App\SysHelper::com_curr_format($Item->unitprice,2,'.',',') }}</td>
                                        <td class="text-center">{{ $Item->qty }}</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                    </tr>
                                    <?php $po_sum += $Item->unitprice*$Item->qty; ?>
                                    @endforeach
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format($po_sum,2,'.',',') }}</th>
                                        <th class="text-center">{{ $poitems->sum('qty') }}</th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                    </tr>
                                    @endif
                                </tbody>

                                <tbody>
                                    @if (count($dnitems)>0)
                                     <tr>
                                    <td style="height:20px"></td>
                                  </tr>
                                    <tr>
                                        <th colspan="12"><b>Aditional Items (Delivery Note)</b></th>
                                    </tr>
                                    @foreach ($dnitems as $Item)
                                    <tr>
                                        <td></td>
                                        <td>{{ $Item->partno }}</td>
                                        <td>{{ $Item->description }}</td>
                                        {{-- <td class="text-center">0</td> --}}
                                        <td class="text-end">{{ $Item->taxableamount }}</td>
                                        <td class="text-center">{{ $Item->qty }}</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                        <td class="text-end">0.00</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        {{-- <th class="text-center">0</th> --}}
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format($dnitems->sum('taxableamount'),2,'.',',') }}</th>
                                        <th class="text-center">{{ $dnitems->sum('qty') }}</th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                        <th class="text-end"></th>
                                    </tr>
                                    @endif
                                </tbody>
                                </table>
                            </div>
                            <div class="status-timeline mb-3" style="display: none;">
                                <div class="timeline-item">
                                    <div class="head">
                                        <div class="title">Account Status</div>
                                        <div class="status-circle bg-success"></div>
                                    </div>
                                    <div class="status"><a class="badge bg-success" href="#" data-bs-toggle="modal" data-bs-target="#accountStatusModal">Approved</a>
                                    <!-- <a class="btn btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square text-success"></i></a> -->
                                </div>                                    
                                </div>
                                <div class="timeline-item">
                                    <div class="head">
                                        <div class="title">Sales Status</div>
                                        <div class="status-circle bg-success"></div>
                                    </div>
                                    <div class="status"><a class="badge bg-success" href="#" data-bs-toggle="modal" data-bs-target="#salesStatusModal">Approved</a>
                                    <!-- <a class="btn btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square text-success"></i></a> -->
                                </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="head">
                                        <div class="title">Purchase Status</div>
                                        <div class="status-circle bg-info"></div>
                                    </div>
                                    <div class="status"><div class="badge bg-info">Not Applicable</div>
                                    <!-- <a class="btn btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square text-success"></i></a> -->
                                </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="head">
                                        <div class="title">Invoice Status</div>
                                        <div class="status-circle bg-warning"></div>
                                    </div>
                                    <div class="status"><div class="badge bg-warning">Approval Waiting</div>
                                    <a class="btn-md btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square text-danger"></i></a>
                                </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="head">
                                        <div class="title">Delivery Status</div>
                                        <div class="status-circle bg-warning"></div>
                                    </div>
                                    <div class="status"><div class="badge bg-warning">Approval Waiting</div>
                                    <a class="btn-md btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square text-danger"></i></a>
                                </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="head">
                                        <div class="title">Recievable Status</div>
                                        <div class="status-circle bg-warning"></div>
                                    </div>
                                    <div class="status"><div class="badge bg-warning">Approval Waiting</div>
                                    <a class="btn-md btn-light" style="display: contents;" data-bs-toggle="modal" data-bs-target="#accountModal"><i class="ico icon-outline-pen-new-square text-danger"></i></a>
                                </div>
                                </div>
                            </div>

@if($del->stage!=6)

                                    <div class="" id="allstatus" >
  @include('backEnd.crm.DealTrackApprovalStatus-Sales')
                                    </div>
@endif

                            



  <script>
    function toggle_tool_tip(id) {
        var element = $('#desc_' + id);
        var currentWhiteSpace = element.css('white-space');

        if (currentWhiteSpace === 'nowrap') {
            element.css('white-space', '');
        } else {
            element.css('white-space', 'nowrap');
        }
    }
  </script>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>



    

    <style>
      .files input {
          outline: 2px dashed #92b0b3;
          outline-offset: -10px;
          -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
          transition: outline-offset .15s ease-in-out, background-color .15s linear;
          padding: 20px 0px 60px 35%;
          text-align: center !important;
          margin: 0;
          width: 100% !important;
      }
      .files input:focus{     outline: 2px dashed #92b0b3;  outline-offset: -10px;
          -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
          transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
       }
      .files{ position:relative}
      .files:after {  pointer-events: none;
          position: absolute;
          top: 60px;
          left: 0;
          width: 100%;
          right: 0;
          height: 30px;
          content: "";
          /*background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);*/
          display: block;
          margin: 0 auto;
          background-size: 100%;
          background-repeat: no-repeat;
      }
      .color input{ background-color:#f1f1f1;}
      .files:before {
          position: absolute;
          bottom: 10px;
          left: 0;  pointer-events: none;
          width: 100%;
          right: 0;
          height: 30px;
          content: " or drag it here. ";
          display: block;
          margin: 0 auto;
          color: #2ea591;
          font-weight: 600;
          text-transform: capitalize;
          text-align: center;
      }
  </style>
  
    <script>

$(window).ready(function() {
        $("#item-store-form").on("keypress", function (event) {           
            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {
                event.preventDefault();
                return false;
            }
        });
});


$(document).on("change", "#deliver_by", function () {
  var deliver_by = $("#deliver_by").val();
  var driver = $("#driver").val();
  var action = "{{ URL::to('getdriverbyshipping') }}";
    $.ajax({
        url: action,
        type: "GET",
        data: {
            _token: '{{ csrf_token() }}',
            deliver_by: deliver_by,
        },
        cache: false,
        success: function(dataResult) {
            //alert(dataResult);
            var dataResult = JSON.parse(dataResult);
            var len = 0;
            if(dataResult['data']=="ERROR")
            {
                alert("Error found in something!!");
            }
            else{
                if(dataResult['data'] != null){
                len = dataResult['data'].length;
                }
                if(len > 0){
                    
                    $('#driver').find('option').not(':first').remove();
                    for(var i=0; i<len; i++){
                        var id = dataResult['data'][i].driver_name;
                        var name = dataResult['data'][i].driver_name;
                        var option = "<option value='"+id+"'>"+name+"</option>";
                        $("#driver").append(option);
                    }
                }
            }
          }
    });
});



    </script>

                                <script>
document.addEventListener("DOMContentLoaded", function () {

    // --- Restore last active tab ---
    let lastTab = localStorage.getItem("active-deliveryapproval-tab");
    if (lastTab) {
        let tabTrigger = document.querySelector('[data-bs-target="' + lastTab + '"]');
        if (tabTrigger) {
            let tab = new bootstrap.Tab(tabTrigger);
            tab.show();
        }
    }

    // --- Save tab when user changes it ---
    let tabButtons = document.querySelectorAll('#purchaseDetailsTabs button[data-bs-toggle="tab"]');

    tabButtons.forEach(btn => {
        btn.addEventListener("shown.bs.tab", function (e) {
            localStorage.setItem("active-deliveryapproval-tab", e.target.getAttribute("data-bs-target"));
        });
    });

});
</script>


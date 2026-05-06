
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
                                     {{ $del->deal_code->code }}
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

                                                    {{ @$del->ownername->first_name }}  {{ @$del->ownername->last_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <style>
                               #purchaseDetailsTabsContent .tab-content {
    display: flex;
}

#purchaseDetailsTabsContent .tab-pane {
    flex: 1;           /* All tabs equal */
    height: 135px;      /* Full available height */
}
                            </style>
                            


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

                                    
        @if ($quoteitems->where('product_type', 2)->count() > 0)
              @php
    $hasEndUserData = 
        !empty($enduser->end_user_company_name) ||
        !empty($enduser->end_user_contact_person) ||
        !empty($enduser->mobile_no) ||
        !empty($enduser->email) ||
        !empty($enduser->device_serial);
@endphp

@if ($hasEndUserData)
      <li class="nav-item" role="presentation">
            <button class="nav-link " id="enduser-fields-tab" data-bs-toggle="tab" data-bs-target="#enduser-fields"
                type="button" role="tab" aria-controls="enduser-fields" aria-selected="true">End User Details</button>
        </li>
@endif
        
    
        @endif

                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="shipping-details-info-tab" data-bs-toggle="tab" data-bs-target="#submited-details" type="button" role="tab" aria-controls="shipping-details-info" aria-selected="false">Submited Details</button>
                                    </li>



                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="shipping-details-info-tab" data-bs-toggle="tab" data-bs-target="#internal-note" type="button" role="tab" aria-controls="shipping-details-info" aria-selected="false">Internal Note</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="shipping-details-info-tab" data-bs-toggle="tab" data-bs-target="#downloads" type="button" role="tab" aria-controls="shipping-details-info" aria-selected="false">Downloads</button>
                                    </li>
                                </ul>
                                <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                                    <div class="tab-pane fade " id="customer-info" role="tabpanel" aria-labelledby="extra-fields-tab">

                                        

                                        
                                           <div class="row text-start">

                                                {{-- @if (App\SysHelper::get_company_status($del->customername) == 0)
                                                    <a class="btn-sm btn-light" style="float: right" target="_blank" href="{{ url('customers/' . $del->customername->id) . '?customer_action=edit' }}">Update Info</a>
                                                    @else
                                                
                                                @endif --}}


                                                <!-- Sales Person -->
                                                <div class="col-xxl-3 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Customer Name	</p>
                                                    {{ optional($del->customername)->name ?: 'N/A' }}
                                                </div>

                                                <!-- Mobile -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Contact Person	</p>
                                               {{ $del->cust_name ?: 'N/A' }}
                                                </div>

                                                <!-- Email -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Designation</p>
                                                    {{ $del->designation ?: 'N/A' }}
                                                </div>

                                                <!-- Ext No -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Mobile</p>
                                                    {{ $del->cust_no ?: 'N/A' }}
                                                </div>

                                                <!-- Source -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Email</p>
                                                {{ $del->cust_email ?: 'N/A' }}
                                                </div>

                                                 <!-- Added On -->
                                                <div class="col-xxl-3 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">State & Country	</p>
                                                   {{ optional(optional(optional($del->customername)->addresses)->first())->statename->name ?: 'N/A' }}, {{ optional(optional(optional($del->customername)->addresses)->first())->countryname->name ?: 'N/A' }}

                                                </div>

                                                <!-- Close Date -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">City	</p>
                                                    {{ optional($del->customername->addresses->first())->city ?: 'N/A' }}
                                                </div>

                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Area	</p>
                                                    {{ optional($del->customername->addresses->first())->area ?: 'N/A' }}
                                                </div>

                                                <!-- Added By -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Building Name	</p>
                                                    {{ optional($del->customername->addresses->first())->building_name ?: 'N/A' }}
                                                </div>

                                               

                                                <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">PO Box	</p>
                                                   {{ optional(optional(optional($del->customername)->addresses)->first())->zip_code ?: 'N/A' }}
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
                                                    <p class="font-weight-600 mb-0 ">Customer Name</p>
                                                   {{ @optional($del->deliverycompany)->name  ?: 'N/A' }}
                                                </div>

                                                <!-- Mobile -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Contact Person</p>
                                                {{ @$del->delivery_name  ?: 'N/A' }}
                                                </div>

                                                <!-- Email -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Contact Number</p>
                                                    {{ @$del->delivery_number  ?: 'N/A' }}
                                                </div>

                                                <!-- Ext No -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Contact Email</p>
                                                    {{ @$del->delivery_email  ?: 'N/A' }}
                                                </div>

                                              

                                                <!-- Added By -->
                                                <div class="col-xxl-3 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">State & Country</p>
                                                    {{ optional($del->state)->name ?: 'N/A' }}, {{ optional($del->country)->name ?: 'N/A' }}
                                                </div>

                                                 <!-- Close Date -->
                                                <div class="col-xxl-3 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">City	</p>
                                                    {{ $del->delivery_city ?: 'N/A' }}
                                                </div>

                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Area	</p>
                                                    {{ $del->delivery_area ?: 'N/A' }}
                                                </div>

                                                <!-- Added By -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">Building Name	</p>
                                                    {{ $del->delivery_flat_office_no ?: 'N/A' }}
                                                </div>

                                                <!-- Added On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 truncate-text-custom green-heading">
                                                    <p class="font-weight-600 mb-0 ">PO Box</p>
                                                    {{ @$del->delivery_zip_code ?: 'N/A' }}
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
                                                    <p class="font-weight-600 mb-0 ">Sales Person</p>
                                                   {{ optional($del->ownername)->first_name }} {{ optional($del->ownername)->last_name  }}
                                                </div>

                                                <!-- Mobile -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0 ">Mobile</p>
                                                {{ optional($del->ownername)->mobile ?: 'N/A' }}

                                                </div>

                                                <!-- Email -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0 ">Email</p>
                                                    {{ optional($del->ownername)->email ?: 'N/A' }}

                                                </div>

                                                <!-- Ext No -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0 ">Ext No</p>
                                                    {{ optional($del->ownername)->ext_no ?: '--' }}
                                                </div>

                                                <!-- Source -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0 ">Source</p>
                                                @if (@$del->source != ''){{ @$del->source }}@if(@$del->source_o != '') - {{ @$del->source_o }} @endif @endif
                                                </div>

                                                <!-- Close Date -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0 ">Close Date</p>
                                                    {{ !empty($del->estimated_close_date) ? \Carbon\Carbon::parse($del->estimated_close_date)->format('d/m/Y') : 'N/A' }}
                                                </div>

                                                <!-- Added By -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0 ">Added By</p>
                                                    {{ optional($deal->createdby)->full_name ?: 'N/A' }}

                                                </div>

                                                <!-- Added On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0 ">Added On</p>
                                                    {{ !empty($del->created_at) ? \Carbon\Carbon::parse($del->created_at)->format('d/m/Y h:i A') : 'N/A' }}
                                                </div>

                                                <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0 ">Updated On</p>
                                                    {{ !empty($del->updated_at) ? \Carbon\Carbon::parse($del->updated_at)->format('d/m/Y h:i A') : 'N/A' }}
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

                                      @if ($quoteitems->where('product_type', 2)->count() > 0)

                                    

                                      @if($hasEndUserData)

           <div class="tab-pane fade" id="enduser-fields" role="tabpanel" aria-labelledby="enduser-fields-tab">

           
                
                  
                            <div class="row">

                                <div class="col-3 green-heading">
                                    <p class="font-weight-600 mb-0">Company Name</p>
                                <span class="truncate-text-custom">{{ @$enduser->end_user_company_name }}</span> 
                                </div>

                               

                                <div class="col-2 green-heading">
                                    <p class="font-weight-600 mb-0  ">Contact Person</p>
                                <span class="truncate-text-custom">{{ @$enduser->end_user_contact_person }}</span> 
                                </div>

                                <div class="col-2 green-heading">
                                    <p class="font-weight-600 mb-0 ">Mobile No</p>
                                    <span class="truncate-text-custom">{{ @$enduser->mobile_no }}</span> 
                                </div>

                                <div class="col-2 green-heading">
                                    <p class="font-weight-600 mb-0  ">Email</p>
                                    <span class="truncate-text-custom">{{ @$enduser->email }}</span>
                                </div>

                                 <div class="col-3 green-heading d-flex flex-column align-items-center">
                                       @php
        // Try to parse as JSON (new format: organized by part number)
        $serialDisplay = '';
        $count_serial = 0;
        
        if (!empty($enduser->device_serial)) {
            $decoded = json_decode($enduser->device_serial, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                // New format: JSON with part numbers as keys
                $parts = [];
                foreach ($decoded as $partNumber => $serials) {
                    if (is_array($serials) && count($serials) > 0) {
                        $parts[] = $partNumber . ': ' . implode(', ', $serials);
                        $count_serial += count($serials);
                    }
                }
                $serialDisplay = implode(' | ', $parts);
            } else {
                // Old format: plain comma-separated or pipe-separated
                if (strpos($enduser->device_serial, '|') !== false) {
                    // Already formatted with part numbers
                    $serialDisplay = $enduser->device_serial;
                    // Count serials by splitting on commas and pipes
                    $allSerials = preg_split('/[,|]/', $enduser->device_serial);
                    $count_serial = count(array_filter(array_map('trim', $allSerials), function($s) {
                        return !empty($s) && strpos($s, ':') === false;
                    }));
                } else {
                    // Simple comma-separated
                    $serialDisplay = $enduser->device_serial;
                    $count_serial = count(array_filter(explode(',', $enduser->device_serial)));
                }
            }
        }
    @endphp
                                    <p class="font-weight-600 mb-0 w-100">Device Serial ({{ $count_serial }})</p>
                                    {{-- <span class="truncate-text-custom" style="white-space: pre-wrap;">{{ $serialDisplay }}</span> --}}
                                    <button class="btn btn-light text-success text-center float-center" type="button" data-bs-toggle="modal" data-bs-target="#serialModal">
    View All
                                    </button> 
                                </div>

                                                                <!-- SERIAL MODAL -->
<div class="modal fade" id="serialModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="DeviceSerialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" style="max-width: 22rem;">
        <div class="modal-content">
            
            <div class="modal-header mb-2">
                <h4 class="modal-title" id="DeviceSerialModalLabel">Device Serials</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body pt-0">

                @php
                    $groupedSerials = [];

                    if (!empty($enduser->device_serial)) {
                        $decoded = json_decode($enduser->device_serial, true);

                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            // New JSON format
                            foreach ($decoded as $part => $serials) {
                                if (is_array($serials)) {
                                    $groupedSerials[$part] = $serials;
                                }
                            }
                        } else {
                            // Old formats

                            if (strpos($enduser->device_serial, '|') !== false) {
                                // Already formatted part|serial1,serial2
                                $parts = explode('|', $enduser->device_serial);
                                foreach ($parts as $p) {
                                    [$part, $ser] = array_pad(explode(':', $p), 2, '');
                                    $groupedSerials[trim($part)] = array_map('trim', explode(',', $ser));
                                }
                            } else {
                                // Simple comma separated; put under "Unknown Part"
                                $groupedSerials["Unknown Part"] = array_map('trim', explode(',', $enduser->device_serial));
                            }
                        }
                    }
                @endphp

               @foreach($groupedSerials as $part => $serials)

    @php
        // Clean serial list (Laravel 5 compatible)
        $cleanSerials = [];
        foreach ($serials as $s) {
            if (trim($s) != '') {
                $cleanSerials[] = $s;
            }
        }
        $count = count($cleanSerials);
    @endphp

    <div class="mb-3 p-2 border rounded">

        <!-- Part header with count badge -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold" style="font-size: 0.95rem;">{{ $part }}</span>
            <span class="qty-badge">{{ $count }}</span>
        </div>

        <!-- Serial list -->
        @foreach($cleanSerials as $index => $s)
          
            <div class="mb-1" style="    display: flex
;
    align-items: center;
    margin-bottom: 8px;
    gap: 8px;">
                <span class="text-muted" style="min-width: 20px;"> {{ $index + 1 }}.</span>
                <input type="text" 
                       class="form-control form-control-sm" 
                       value="{{ $s }}" 
                       readonly 
                       style=" background: #fdfdfd;">
            </div>
        @endforeach

    </div>

@endforeach

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

                                {{-- <div class="col-2">
                                    <p class="font-weight-600 mb-0 truncate-text-custom">Project Name</p><br>
                                    <span class="truncate-text-custom">{{ $enduser->project_name }}</span>
                                </div>

                                <div class="col-2 mb-3">
                                    <p class="font-weight-600 mb-0 truncate-text-custom">Brief description about this project</p><br>
                                    <span class="truncate-text-custom">{{ $enduser->project_description }}</span>
                                </div>

                                <div class="col-2">
                                    <p class="font-weight-600 mb-0 truncate-text-custom">When it is expected to Close</p><br>
                                    <span class="truncate-text-custom">{{ date('d/m/Y', strtotime($enduser->expected_close_date)) }}</span>
                                </div> --}}

                            </div>
        </div>
        @endif
        @endif
                                   
                                    <div class="tab-pane fade show active" style="padding-bottom: 5px" id="submited-details" role="tabpanel" aria-labelledby="vat-details-tab">


                                            <div class="row text-start">

                                                <!-- Sales Person -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Expected Delivery</p>
                                                   {{ !empty($deal->delivery_date) ? \Carbon\Carbon::parse($deal->delivery_date)->format('d/m/Y') : 'N/A' }}
                                                </div>

                                                <!-- Mobile -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0" >Payment Terms <i data-bs-toggle="modal" data-bs-target="#modalChangePaymentTerms"  class="ico icon-outline-pen-2 text-dark"></i> </p>
                                           <span class="truncate-text-custom">{{ optional($deal->paymentterms)->title ?: 'N/A' }}
@if(optional($deal)->payment_terms == 22 && !empty($deal->payment_terms_txt))
    - {{ $deal->payment_terms_txt }}
@endif
</span>    


                                                </div>

                                                <!-- Modal Delivery-->
<div class="modal side-panel fade" id="modalChangePaymentTerms" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                      {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-receivables-payment-terms-mode', 'method' => 'POST', 'id' => 'update_payment_terms_mode']) }}

        <div class="modal-content">
            <div class="modal-header m-0"><h4 class="modal-title" id="exampleModalLongTitle">Payment Terms</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          <div class="modal-body">
		  
          @php
            $paymentterms = @App\SysPaymentTerms::all();

          @endphp

                  <div class="row">
                  <div class="col-12 mt-1">
                      <div class="form-check-label">Payment Terms
                          <select class="form-control js-example-basic-single" id="edit_payment_terms" name="edit_payment_terms" required>
                                        @foreach ($paymentterms as $key => $value)
                                            <option value="{{ @$value->id }}"  @if (@$deal->payment_terms == @$value->id) selected @endif>{{ @$value->title }}</option>
                                        @endforeach
                                    </select>
                          </div>
                              <input class="form-control" id="edit_payment_terms_txt" type="text" value="{{ @$deal->payment_terms_txt }}" autocomplete="off" placeholder="Payment Terms" name="edit_payment_terms_txt" @if(@$deal->payment_terms != 22) style="display: none;" @else required @endif>

                    </div>

                   <script>
    $(document).ready(function () {

        $('#edit_payment_terms').on('change', function () {
            if ($(this).val() == 22) {
                $('#edit_payment_terms_txt')
                    .css("display", "block")
                    .prop('required', true);
            } else {
                $('#edit_payment_terms_txt')
                    .css("display", "none")
                    .prop('required', false);
            }
        });

        // Trigger once on load
        $('#edit_payment_terms').trigger('change');

    });
</script>


                    <div class="col-12 mt-2">
                      <div class="form-check-label">Payment Mode 
                          <select class="form-control js-example-basic-single" name="edit_payment_mode" required>
                          <option value="1" @if($deal->payment_mode==1) selected @endif>Cash</option>
                          <option value="2" @if($deal->payment_mode==2) selected @endif>Cheque</option>
                          <option value="3" @if($deal->payment_mode==3) selected @endif>Bank Transfer</option>
                          <option value="4" @if($deal->payment_mode==4) selected @endif>Open Credit</option>
                          <option value="5" @if($deal->payment_mode==5) selected @endif>Credit Card</option>
                          <option value="6" @if($deal->payment_mode==6) selected @endif>Bank TT</option>
                      </select>
                      <input type="hidden" name="edit_payment_mode_id" value="{{ $deal->deal_id }}" />
                          </div>
                    </div>


					
      </div>


		      </div>

              <div class="modal-footer">
                	<button type="submit" class="btn btn-light add-btn ms-2" id="btnSubmit">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Submit
						</button>
              </div>
        </div>
                    {{ Form::close() }}

      </div>
    </div>
<!-- Modal Delivery-->

                                                <!-- Email -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Payment mode <i data-bs-toggle="modal" data-bs-target="#modalChangePaymentTerms"  class="ico icon-outline-pen-2 text-dark"></i></p>
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
                                                   @if(!empty($deal) && $deal->partial_delivery == 1)
                                                        Partial Delivery
                                                    @else
                                                        N/A
                                                    @endif
                                                </div>
                                                

                                                <!-- Close Date -->
                                               
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-0">Professional Service</p>
                                                     @if($deal->technical==1 || $deal->technical==0)

                                                      @if($deal->technical==0) NO @endif
                                                     @if($deal->technical==1) YES @endif
                                                    @else
                                                    N/A
                                                     @endif
                                                </div>
                                             

                                        
                                                
                                                <!-- Added By -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-1">Approval Not Required</p>
                                                    @if($deal->purchease_approval==0 || $deal->invoice_approval==0 || $deal->delivery_approval==0 ||
                                                        $deal->receivables_approval==0)
                                                        <span class="truncate-text-custom">
                                                    @if($deal->purchease_approval==0) Purchase @endif
                                                    @if($deal->invoice_approval==0) , Invoice @endif
                                                    @if($deal->delivery_approval==0) , Delivery, @endif
                                                    @if($deal->receivables_approval==0) Receivables @endif
                                                    </span>
                                                    @else
                                                        N/A
                                                   
                                                    @endif
                                                </div>
                                               

                                                <!-- Added On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-1">LPO</p>
                                                     <?php $file = explode("|", $deal->lpo); ?>
                        @foreach ($file as $f)
                        @if (!empty($f))
                        <a class="btn-sm btn-light text-dark"
                            href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" title="{{ $f }}" target="_blank">{{ $deal->reference_no }} <i
                                class="ico icon-bold-download-minimalistic fw-bold title-15 text-success"></i>
                            </a>
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
                            href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" title="{{ $f }}" target="_blank"><i
                                class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i>
                            </a>
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
                            href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" title="{{ $f }}" target="_blank"><i
                                class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i>
                            </a>
                             @else
                        N/A
                        @endif
                        @endforeach
                                                </div>

                                                    <!-- Updated On -->
                                                <div class="col-xxl-2 col-lg-3 col-md-4 col-6 mb-3 green-heading">
                                                    <p class="font-weight-600 mb-1">Quotation</p>
                                                  <a class="btn-sm btn-light text-dark"
            href="{{url('crm-quote/'.$del->id.'/download/'.$del->quote_id)}}" title="{{ $del->code }}" target="_blank"><i
                class="ico text-success icon-bold-download-minimalistic fw-bold title-15 text-success"></i> {{ @App\SysHelper::getQuoteDocNoByDeal($del->id, $del->quote_id) }} </a>
                                                </div>

                                            </div>

                                     
                                        </div>
                                    
                                   
                                    <div class="tab-pane fade" style="padding-bottom:0;padding-top:3px" id="internal-note"  role="tabpanel" aria-labelledby="vat-details-tab">
                                        <div class="row">

                                             <div class="col-7">
                                           
                                                 <div id="scrollBox"  style="max-height: 8rem; overflow-y: auto;">
                    

                                                        @if (isset($comments))
                                                        <div class="mt-2">
                                                            @foreach ($comments as $cmts)



                            <div class="card border-0 rounded-3 mb-2 comments-card">
                            <div class="card-body py-0">

                            

                                <!-- Top Row: Right-Aligned Icons -->
                                <div class="d-flex justify-content-between mb-0">


                        <!-- Comment -->
                                <p class="mb-0 fw-semibold @if ($cmts->deleted_at) text-decoration-line-through text-muted @endif" style="font-size:11px">
                                     {!!   nl2br($cmts->comments) !!}
                                </p>


                                <div class="d-flex align-items-baseline">
                                        @if ($cmts->commentsdoc)
                                                        <a href="{{ asset('public/uploads/crm_deal_doc/' . $cmts->commentsdoc) }}"
                                                        target="_blank"  class="btn btn-sm btn-light border-0 py-0" style="min-height:17px">
                                                            <i class="ico icon-bold-paperclip" style="font-size:11px"></i>
                                                        </a>
                                                    @endif

                                                    @if ($cmts->created_by == Auth::user()->id)
                                                        @if ($cmts->deleted_at)
                                                            <a href="{{ url('crm-deals-comments-restore/' . $cmts->id) }}"
                                                            onclick="return confirm('Are you sure you want to restore this comment?')"
                                                             class="btn btn-sm btn-light border-0 p-0 py-0" style="min-height:17px">
                                                                <i class="ico icon-bold-restart" style="font-size:11px"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ url('crm-deals-comments-delete/' . $cmts->id) }}" title="Delete Comment"
                                                            onclick="return confirm('Are you sure you want to delete this comment?')"
                                                            class="btn btn-sm btn-light border-0 py-0" style="min-height:17px">
                                                                <i class="ico icon-outline-trash-bin-minimalistic text-danger" style="font-size:11px"></i>
                                                            </a>
                                                        @endif
                                            @endif
                                </div>


                                

                                </div>

                                <!-- Username + Date + Deleted At (Right-Aligned Below Icons) -->
                                <div class="text-end small text-muted">

                                    <span style="font-size:10px">
                                        
                                        {{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}
                                    </span>

                                    <span>•</span>

                                        <span style="font-size:10px">
                                        <i class="ico icon-bold-clock me-1"></i>
                                        {{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}
                                    </span>

                                    @if ($cmts->deleted_at)
                                        <span style="font-size:10px">
                                        
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

                                            
                                           <div class="d-flex justify-content-between align-items-center mb-2 w-100">
    
                                                <label class="font-weight-bold form-label mb-0 w-50">Internal Note</label>

                                                <button type="button" id="viewbnotemodal" data-bs-toggle="modal" data-bs-target="#ViewNotesModal"
                                                    class="btn btn-light btn-sm d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size:11px">
                                                    <i class="ico icon-outline-notebook text-success" style="font-size:15px"></i>
                                                    <span>View Notes</span>
                                                </button>

                                                <!-- Modal Account-->
                                                <div class="modal side-panel fade" id="ViewNotesModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header"><h4 class="modal-title" id="exampleModalLongTitle">Internal Note</h4>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>

                                                            <div class="modal-body">

                                                                <div class="row">
                                                                     <div id="scrollBox"  style="max-height: 420px; overflow-y: auto;">
                    

                                                        @if (isset($comments))
                                                        <div class="mt-3">
                                                            @foreach ($comments as $cmts)



                            <div class="card  rounded-3 mb-3 comments-card">
                            <div class="card-body p-3">

                            

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

                                                            </div>
                                                        
                                                        </div>
                                                    </div>
                                                    </div>
                                                <!-- Modal Account-->

                                            </div>

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
                                                        /* width: 116px;                      */
                                                                     /* keep text centered */
                                                        white-space: nowrap;        
                                                        padding:0px 5px;      /* prevent wrapping */
                                                        }

                                                </style> 
                                            
                                        </div>
                                    </div>
                                    <style>
                                        .detail-item-table-noborder td.text-start {
                                            display: flex;
                                            flex-wrap: wrap;
                                            gap: 6px; /* spacing between buttons */
                                            align-items: center;
                                            }

                                            .detail-item-table-noborder td.text-start a.btn-fixed {
                                            flex: 0 0 auto; /* button width fits its text */
                                            min-width:96px; /* ensures consistent starting alignment */
                                            text-align: center;
                                            white-space: nowrap;
                                            }
                                    </style>
                                    <div class="tab-pane fade" id="downloads" role="tabpanel" aria-labelledby="vat-details-tab">
                                        <div class="row gap-rows">
                                             <div class="col-12">
                                                
                                                <table class="detail-item-table-noborder">
                                                    <tbody>
                                                    <tr>
                                                        <td>Submited</td>
                                                        <td class="text-start"> : 
								                        <a class="btn-sm btn-light text-dark btn-fixed" href="{{url('crm-quote/'.$del->id.'/download/'.$del->quote_id)}}" title="{{ $del->code }}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i> {{ @App\SysHelper::getQuoteDocNoByDeal($del->id, $del->quote_id) }}</a>

                                                            <?php $file = explode("|",$deal->lpo); ?>
                                                            
                                                            @foreach ($file as $f)
                                                            @if (!empty($f))
                                                            <a class="btn-sm btn-light text-dark btn-fixed" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" title="{{ $f }}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i> LPO</a>
                                                           @endif
                                                            @endforeach
					
                                                        <?php $file = explode("|",$deal->cheque_copy); ?>
                                                                    @foreach ($file as $f)
                                                                     @if (!empty($f))

                                                                    <a class="btn-sm btn-light text-dark btn-fixed" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" title="{{ $f }}" target="_blank"><i class="ico icon-bold-download-minimalistic fw-bold title-15 text-success"></i> Cheque/TT Copy</a>
                                                                   @endif
                                                                    @endforeach
                                                                    
                                                                    <?php $file = explode("|",$deal->purchease_quote); ?>
                                                                    @foreach ($file as $f)
                                                                    @if (!empty($f))

                                                                    <a class="btn-sm btn-light text-dark btn-fixed" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" title="{{ $f }}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i> Puchase Quote</a>
                                                                    @endif
                                                                    @endforeach
                                                        </td>
                                                    </tr>

                                                     <tr>
                                                        <td>Purchase</td>
                                                        <td class="text-start"> :
                    @if(count($list_purchase_order)>0)
                            @foreach($list_purchase_order as $list)
                                <a class="btn-sm btn-light text-dark btn-fixed" href="{{url('purchase-order/'.$list->id)}}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i> {{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_goods_receipt_note)>0)
                            @foreach($list_goods_receipt_note as $list)
                                <a class="btn-sm btn-light text-dark btn-fixed" href="{{url('goods-receipt-note/'.$list->id)}}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i> {{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_purchase_invoice)>0)
                            @foreach($list_purchase_invoice as $list)
                                <a class="btn-sm btn-light text-dark btn-fixed" href="{{url('purchase-invoice/'.$list->id)}}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i> {{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_purchase_return)>0)
                            @foreach($list_purchase_return as $list)
                                <a class="btn-sm btn-light text-dark btn-fixed" href="{{url('purchase-return/'.$list->id)}}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i> {{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_payment)>0)
                            @foreach($list_payment as $list)
                                <a class="btn-sm btn-light text-dark btn-fixed" href="{{url('payment/'.$list->id)}}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i> {{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    @if(count($list_journalvoucher)>0)
                    
                            @foreach($list_journalvoucher as $list)
                                <a class="btn-sm btn-light text-dark btn-fixed" href="{{url('journalvoucher/'.$list->id)}}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i> {{ $list->doc_number }}</a>
                            @endforeach
                    @endif
                    
                    @if (count($check_cl) > 0)
                    @foreach ($check_cl as $cl)<a class="btn-sm btn-light text-dark btn-fixed" href="{{url('clearance/'.$cl->id.'/download')}}" target="_blank"><i class="ico icon-bold-download-minimalistic fw-bold title-15 text-success"></i> {{ $cl->invoice_no }}&nbsp;</a>
                    @endforeach
                    @endif
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>Sales</td>
                                                        <td class="text-start"> : 
                            @if(count($list_performa_invoice)>0)
                                    @foreach($list_performa_invoice as $list)
                                        <a class="btn-sm btn-light text-dark btn-fixed" href="{{url('proforma-invoice/'.$list->id)}}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success" ></i> {{ $list->doc_number }}</a>
                                    @endforeach
                            @endif
                            @if(count($list_sales_invoice)>0)                    
                                    @foreach($list_sales_invoice as $list)
                                        <a class="btn-sm btn-light text-dark btn-fixed" href="{{url('sales-invoice/'.$list->id)}}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i> {{ $list->doc_number }}</a>
                                    @endforeach
                            @endif
                            @if(count($list_delivery_note)>0)
                                    @foreach($list_delivery_note as $list)
                                        <a class="btn-sm btn-light text-dark btn-fixed" href="{{url('delivery-note/'.$list->id)}}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i> {{ $list->doc_number }}</a>
                                    @endforeach
                            @endif
                            @if(count($list_sales_return)>0)
                                    @foreach($list_sales_return as $list)
                                        <a class="btn-sm btn-light text-dark btn-fixed" href="{{url('sales-return/'.$list->id)}}" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i> {{ $list->doc_number }}</a>
                                    @endforeach
                            @endif
                            @if(count($list_receipt)>0)
                                    @foreach($list_receipt as $list)
                                        <a class="btn-sm btn-light text-dark btn-fixed" href="{{url('receipt/'.$list->id)}}" target="_blank"><i class="ico icon-bold-download-minimalistic fw-bold title-15 text-success"></i> {{ $list->doc_number }}</a>
                                    @endforeach
                            @endif
                                                        </td>
                                                    </tr>
                                                   
                                                    </tbody>
                                                </table>
                                            </div>
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
                                            <th  width="160px" class="text-start ">Part No</th>
                                            <th width="170px"   class="text-start">Description</th>
                                            {{-- <th width="80px"   class="text-center text-nowrap">Delivery</th> --}}
                                            <th width="65px"   class=" text-nowrap text-end">Cost</th>
                                            <th width="40px"   class=" text-nowrap text-center">Qty</th>
                                            <th width="55px"  class=" text-nowrap text-end">Unit Price</th>
                                            <th width="65px"  class=" text-nowrap text-end">Value</th>
                                            <th width="65px"  class=" text-nowrap text-end">Discount</th>
                                            <th width="65px"   class=" text-nowrap text-end">Taxable</th>
                                            <th width="65px"   class=" text-nowrap text-end">VAT</th>
                                            <th width="85px"   class=" text-nowrap text-end">Total</th>
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
                                        <td class="text-end">{{ App\SysHelper::currancy_format($Item->cost,$Item->currency_id) }}</td>
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
                                        <th> &nbsp;&nbsp;&nbsp; <button     data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Update Serial No"
                            data-bs-placement="bottom" class=" btn-sm btn-light text-dark fw-semibold"  style="background-color:#deebe1; border: none;padding:0 " type="submit"><i  class="ico icon-outline-pen-2 font-weight-600 text-dark title-15"></i></button></th>
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
                                        // Only calculate VAT on discount if deal_discount_vat exists
                                        $deal_discount_vat_amount = !empty($del->deal_discount_vat) ? $del->deal_discount_vat : 0;
                                        $deal_discount_sum_amount = $deal_discount_taxable_amount + $deal_discount_vat_amount;
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

@php
                                        $index = 1;
                                    @endphp
                                <tbody >
                                    @if (count($poitems)>0 || count($excess_po_items)>0)
                                    <?php $po_sum = 0; ?>
                                  <tr>
                                    <td style="height:20px"></td>
                                  </tr>
                                    <tr> 
                                        <td colspan="11"><b>Aditional Items (Purchase Order)</b></td>
                                    </tr>
                                    
                                    @foreach ($poitems as $Item)
                                    {{-- <tr>
                                      
                                        <td class="text-center">{{ $index++ }}</td>
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
                                    </tr> --}}
                                   
                                    @endforeach

                                    @php
                                        $qty_total_excess = 0;
                                    @endphp
                                    @foreach ($excess_po_items as $excess_item)
                                    @php
                                        $part_number = @App\SysHelper::getPartNumberDataByID($excess_item['part_number']);
                                    @endphp
                                        <tr>
                                            <td class="text-center">{{ $index++ }}</td>
                                            <td>{{ $part_number ? $part_number->part_number : null }}</td>
                                            <td>{{ $part_number ? $part_number->description : null }}</td>
                                            <td class="text-end">{{ @App\SysHelper::com_curr_format($excess_item['unitprice'],2,'.',',') }}</td>
                                            <td class="text-center">{{ $excess_item['excess_qty'] }}</td>
                                            <td class="text-end">0.00</td>
                                            <td class="text-end">0.00</td>
                                            <td class="text-end">0.00</td>
                                            <td class="text-end">0.00</td>
                                            <td class="text-end">0.00</td>
                                            <td class="text-end">0.00</td>
                                        </tr>
                                    <?php $po_sum += $excess_item['unitprice'] * $excess_item['excess_qty']; ?>
                                    <?php $qty_total_excess += $excess_item['excess_qty']; ?>
                                    @endforeach

                               <tr><td colspan="11">&nbsp;</td></tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        
                                        <th class="text-end">{{ @App\SysHelper::com_curr_format($po_sum,2,'.',',') }}</th>
                                        <th class="text-center">{{  $qty_total_excess }}</th>
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

                                @if(count($quote_charges) > 0 || count($list_journalvoucher_det)>0)
                             
                            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                                <thead>
                                    <tr>
                                        <th style="width:260px;" class="text-start">Selling Exp Account</th>
                                        <th style="width:240px;" class="text-start">Credit Account</th>
                                        <th style="width:65px;" class="text-end">Amount</th>
                                        <th class="text-start" style="padding-left:50px">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>                                    
                                @if(count($quote_charges) > 0)
                                @foreach ($quote_charges as $charges)
                                <tr>
                                    <td class="text-start">{{ $charges->sellingexpaccount->account_name }}</td>
                                    <td class="text-start">{{ $charges->creditaccount->account_name }}</td>
                                    <td class="text-end">{{ $charges->amount }}</td>
                                    <td class="text-start" style="padding-left:50px">{{ $charges->remarks }}</td>
                                </tr>
                                @endforeach
                                @endif
                                
                            @if(count($list_journalvoucher_det)>0)
                            <?php $total_jv_amount=0; ?>
                            @foreach ($list_journalvoucher_det as $jv_det)
                            
                            @php $main_acc=$list_journalvoucher_det_other->where('is_main_account',0)->where('transaction_no',$jv_det->transaction_no)->where('credit_amount',$jv_det->debit_amount)->max('account_name');
                            $main_acc_credit_amount = $list_journalvoucher_det->where('is_main_account',1)->where('transaction_no',$jv_det->transaction_no)->max('credit_amount'); @endphp

                            @if($jv_det->debit_amount > 0)
                            <tr>
                                <td class="text-start">{{ $jv_det->account_name }}</td>
                                <td class="text-start">{{ $main_acc }}</td>
                                <td class="text-right">{{ @App\SysHelper::com_curr_format($jv_det->debit_amount,2,'.',',') }} <?php $total_jv_amount += $jv_det->debit_amount; ?></td>
                                <td class="text-start pl-5">{{ $jv_det->remarks }} &nbsp; [ {{ $jv_det->transaction_no }} ]</td>
                            </tr>
                            @endif
                            
                            @endforeach
                            <tr>
                                <td class="text-start"></td>
                                <td class="text-start font-weight-bold">Total Expenses</td>
                                <td class="text-right font-weight-bold">{{ @App\SysHelper::com_curr_format($total_jv_amount,2,'.',',') }}</td>
                                <td class="text-start pl-5"></td>
                            </tr>
                            @endif

                                </tbody>
                            </table>
                              <br>
                            @endif

                          

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
  @include('backEnd.crm.DealTrackApprovalStatus')
                                    </div>
                                    
  {{--  @include('backEnd.crm.DealTrackApprovalStatusForms')  --}}
  @include('backEnd.crm.DealTrackApprovalStatusFormsPopup')
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


        $(document).ready(function() {

   // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-popover="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            delay: { show: 500, hide: 100 }
        });
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
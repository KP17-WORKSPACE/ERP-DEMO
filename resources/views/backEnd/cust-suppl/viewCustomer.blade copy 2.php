<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>


<style>
    .fixed-info-table {
        table-layout: fixed;
        /* Fix column widths */
        width: 100%;
    }

    .fixed-info-table th {
        width: 35%;
        /* Always 30% for label */
        white-space: nowrap;
        /* Prevent wrapping */
        text-align: left;
        font-weight: 500;
    }

    .fixed-info-table td {
        width: 65%;
        /* Always 70% for value */
        word-break: break-word;
        /* Wrap long text if needed */
    }
</style>

<?php try { ?>

@php
    function showPicName($data)
    {
        $name = explode('/', $data);
        return $name[4];
    }
    function showJoiningLetter($data)
    {
        $name = explode('/', $data);
        return $name[3];
    }
    function showResume($data)
    {
        $name = explode('/', $data);
        return $name[3];
    }
    function showOtherDocument($data)
    {
        $name = explode('/', $data);
        return $name[3];
    }

@endphp





<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        @if (isset($custDetails))
            {{ @$custDetails->code }}
        @endif
    </h4>
    <div class="purchase-order-content-header-right">

        <form method="GET" action="{{ url('customers/'.@$custDetails->id.'?customer_action=edit') }}">
            <button type="submit" name="customer_action" value="edit" class="btn btn-light">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </button>
        </form>
        <form method="GET" action="{{ url('customers?customer_action=add') }}">
            <button type="submit" name="customer_action" value="add" class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>
        </form>
 <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">
               


            </ul>
        </div>

    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <!-- <h4 class="mb-3 color-sub-head">Supplier Info (SUPS-1060)</h4> -->
        <div class="d-flex align-items-center mb-3">
            <div
                class="font-weight-600 title-15 me-3 
            
             @if (@$custDetails->type == 1) text-success @endif
             @if (@$custDetails->type == 2) text-warning @endif
             @if (@$custDetails->type == 3) text-danger @endif
             @if (@$custDetails->type == 4) text-dark @endif
            ">
                {{ @$custDetails->customer_name_display }}
            </div>

            


            @if (@$custDetails->status == 2)
                <span class="badge bg-danger">Inactive</span>
            @else
                <span class="badge bg-success">Active</span>
            @endif

        </div>
        <div class="row">
            <div class="col-2 mb-3">
                <label class="form-label">Customer Type:</label>
                <div class="form-control-plaintext">
                    @if (@$custDetails->account_type == 1)
                        Reseller
                    @endif
                    @if (@$custDetails->account_type == 2)
                        Enduser
                    @endif
                    @if (@$custDetails->account_type == 3)
                        Ecommerce
                    @endif
                </div>
            </div>
            <div class="col-2 mb-3">
                <label class="form-label">Contact Name:</label>
                <div class="form-control-plaintext truncate-text-custom "> {{ @$custDetails->customer_salutation }}
                    {{ @$custDetails->first_name }} {{ @$custDetails->last_name }}
                </div>
            </div>
            <div class="col-2 mb-3">
                <label class="form-label">Designation:</label>
                <div class="form-control-plaintext truncate-text-custom ">{{ @$custDetails->designation }}</div>
            </div>

            <div class="col-2 mb-3">
                <label class="form-label">Contact Number:</label>
                <div class="form-control-plaintext"> {{ str_replace(' ', '', @$custDetails->contcat_number) }}</div>
            </div>
            <div class="col-2 mb-3">
                <label class="form-label">Mobile:</label>
                <div class="form-control-plaintext">{{ @$custDetails->mobile }}</div>
            </div>
            <div class="col-2 mb-3">
                <label class="form-label">Mail: </label>
                <div class="form-control-plaintext truncate-text-custom "> {{ @$custDetails->email }}</div>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Created By:</label>
                <div class="form-control-plaintext truncate-text-custom ">
                    <a href="" class="text-dark fw-normal">
                        @if (count($editAssign) > 0)
                            @foreach ($editAssign as $e)
                                {{ $e->full_name }},
                            @endforeach
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="dealTrackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="deal-info-tab" data-bs-toggle="tab" data-bs-target="#deal-info"
                type="button" role="tab" aria-controls="deal-info" aria-selected="true">Address</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sales-person-info-tab" data-bs-toggle="tab" data-bs-target="#sales-person-info"
                type="button" role="tab" aria-controls="sales-person-info" aria-selected="false">Contact
                Person</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="vat-info-tab" data-bs-toggle="tab" data-bs-target="#vat-info" type="button"
                role="tab" aria-controls="vat-info" aria-selected="false">VAT
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="payment-info-tab" data-bs-toggle="tab" data-bs-target="#payment-info"
                type="button" role="tab" aria-controls="payment-info" aria-selected="false">Payment
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="customer-info-tab" data-bs-toggle="tab" data-bs-target="#customer-info"
                type="button" role="tab" aria-controls="customer-info" aria-selected="false">Documents</button>
        </li>
        


    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
            <!-- <h4 class="mb-3 color-sub-head">Shipping Address</h4> -->
            <div class="row">

                @if (count($custAddress) > 0)
                    @foreach ($custAddress as $data)
                      <div class="col-5">
                             <h4 class="mb-1 color-sub-head font-size-13 mb-2">

                              

                                @if ($data->set_default == 1 || $data->is_shipping == 0)
                                    Billing Address
                                @elseif($data->is_shipping == 1)
                                    Shipping Address
                                @else
                                    <div class="fw-bold" style="visibility: hidden;">Placeholder</div>
                                @endif
                            </h4>
                            <table class="detail-item-table-noborder table table-hover">
                                <thead>
                                     
                                    <tr>
                                        <td class="text-start" width="100px">Address 1</td>
                                        <td class="truncate-text-custom">:&nbsp;&nbsp;{{ $data->address }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start" width="100px">Address 2</td>
                                        <td>:&nbsp;&nbsp;{{ $data->address2 }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start" width="100px">City</td>
                                        <td>:&nbsp;&nbsp;{{ $data->city }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start" width="100px">State</td>
                                        <td>:&nbsp;&nbsp;{{ $data->statename['name'] }}</td>
                                    </tr>
                                     <tr>
                                        <td class="text-start" width="100px">Country</td>
                                        <td>:&nbsp;&nbsp;{{ $data->countryname['name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start" width="100px">Post Box</td>
                                        <td>:&nbsp;&nbsp;{{ $data->zip_code }}</td>
                                    </tr>
                                   

                                </thead>

                            </table>
                        </div>
                        {{-- <div class="col-md-3">
                            <h4 class="mb-1 color-sub-head font-size-13">

                                @if ($data->set_default == 1 && $data->is_shipping == 0)
                                    Billing Address
                                @elseif($data->is_shipping == 1)
                                    Shipping Address
                                @else
                                    <div class="fw-bold" style="visibility: hidden;">Placeholder</div>
                                @endif
                            </h4>


                            <table id="long-list" class="table table-hover table-sm fixed-info-table">
                                <tbody>
                                    <tr>
                                        <th>Country</th>
                                        <td>{{ $data->countryname['name'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address 1</th>
                                        <td>{{ $data->address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address 2</th>
                                        <td>{{ $data->address2 }}</td>
                                    </tr>
                                    <tr>
                                        <th>City</th>
                                        <td>{{ $data->city }}</td>
                                    </tr>
                                    <tr>
                                        <th>State</th>
                                        <td>{{ $data->statename['name'] }}</td>
                                    </tr>
                                    <tr>
                                        <th>Post Box</th>
                                        <td>{{ $data->zip_code }}</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div> --}}
                    @endforeach
                @endif


            </div>


        </div>
        <div class="tab-pane fade" id="sales-person-info" role="tabpanel" aria-labelledby="sales-person-info-tab">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-hover text-center">
                        <thead>
                            <tr>
                                <th>Salutation</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email Address</th>
                                <th>Work Phone</th>
                                <th>Mobile</th>
                                <th>Designation</th>
                                <th>Department</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if (count($custContact) > 0)
                                @foreach ($custContact as $data)
                                    <tr>
                                        <td>{{ $data->salutation }}</td>
                                        <td>{{ $data->first_name }}</td>
                                        <td>{{ $data->last_name }}</td>
                                        <td>{{ $data->email_address }}</td>
                                        <td>{{ str_replace(' ', '', $data->work_phone) }}</td>
                                        <td>{{ str_replace(' ', '', $data->mobile) }}</td>
                                        <td>{{ $data->designation }}</td>
                                        <td>{{ $data->department }}</td>
                                    </tr>
                                @endforeach
                            @endif



                        </tbody>

                    </table>
                </div>

            </div>
        </div>

        <div class="tab-pane fade" id="vat-info" role="tabpanel" aria-labelledby="vat-info-tab">
            <div class="row">


                <div class="col-2 mb-3">
                    <label class="form-label">Vat Country:</label>
                    <div class="form-control-plaintext"> {{ @$custDetails->vatcountry->name }}</div>
                </div>

                @if (isset($custDetails) && !empty(@$custDetails->vat_state))
                    <div class="col-2 mb-3">
                        <label class="form-label">VAT State</label>
                        <div class="form-control-plaintext">
                            @if (isset($custDetails))
                                {{ @$custDetails->vatstate->name }}
                            @endif
                        </div>
                    </div>
                @endif

                <div class="col-2 mb-3">
                    <label class="form-label">VAT Percentage: </label>
                    <div class="form-control-plaintext d-flex align-items-center gap-2">
                        @if (isset($custDetails))
                            {{ @$custDetails->vat_percentage }}% @if ($custDetails->vat_is_fixed == 1)
                                <button class="btn btn-warning m-0 p-0">&nbsp;Fixed&nbsp;</button>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <label class="form-label">VAT Number:</label>
                    <div class="form-control-plaintext">
                        @if (isset($custDetails))
                            {{ @$custDetails->vat_number }}
                        @endif
                    </div>
                </div>

                <div class="col-2 mb-3">
                    <label class="form-label">Customer Type:</label>
                    <div class="form-control-plaintext">
                        @if (isset($custDetails))
                            {{ @$custDetails->customertype->title }}
                        @endif
                    </div>
                </div>

                <div class="col-2 mb-3">
                    <label class="form-label">Sale Type:</label>
                    <div class="form-control-plaintext">
                        @if (isset($custDetails))
                            {{ @$custDetails->saletype->title }}
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <div class="tab-pane fade" id="payment-info" role="tabpanel" aria-labelledby="payment-info-tab">
            <div class="row">

                <div class="col-2 mb-3">
                    <label class="form-label">Transaction Type:</label>
                    <div class="form-control-plaintext">
                        @if (isset($custDetails))
                            {{ @$custDetails->transaction_type }}
                        @endif
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <label class="form-label">Credit Limit:</label>
                    <div class="form-control-plaintext">
                        @if (isset($custDetails))
                            {{ @$custDetails->credit_limit }}
                        @endif
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <label class="form-label">Credit Days:</label>
                    <div class="form-control-plaintext">
                        @if (isset($custDetails))
                            {{ @$custDetails->credit_days }}
                        @endif
                    </div>
                </div>
                <div class="col-2 mb-3">
                    <label class="form-label">Payment Terms:</label>
                    <div class="form-control-plaintext">
                        @if (isset($custDetails))
                            {{ @$custDetails->paymentterms->title }} {{ @$custDetails->payment_terms_txt }}
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <div class="tab-pane fade" id="customer-info" role="tabpanel" aria-labelledby="customer-info-tab">

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <tbody>

                            @if (count($custDoc) > 0)
                                @foreach ($custDoc as $doc)
                                    <tr>
                                        <td>{{ $doc->doc_name }}</td>
                                        <td>{{ date('d/m/Y', strtotime(@$doc->doc_exp_date)) }}</td>
                                        <td>
                                            <a class="btn-sm btn-light text-dark"
                                                href="{{ asset('public/uploads/cust-suppl/') }}/{{ $doc->doc_file }}"
                                                target="_blank">

                                                <i
                                                    class="ico icon-bold-download-minimalistic text-success fw-bold title-15"></i>

                                                Download</a>

                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
</div>

<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="dealTrackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1-info"
                type="button" role="tab" aria-controls="tab1-info" aria-selected="true">Deals In
                Progress</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2-info" type="button"
                role="tab" aria-controls="tab2" aria-selected="false">Invoice Completed</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3-info" type="button"
                role="tab" aria-controls="tab3" aria-selected="false">Payment Pending</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab4-tab" data-bs-toggle="tab" data-bs-target="#tab4-info" type="button"
                role="tab" aria-controls="tab4" aria-selected="false">Completed Orders</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab5-tab" data-bs-toggle="tab" data-bs-target="#tab5-info" type="button"
                role="tab" aria-controls="tab5" aria-selected="false">AMC</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab6-tab" data-bs-toggle="tab" data-bs-target="#tab6-info" type="button"
                role="tab" aria-controls="tab6" aria-selected="false">Project Service</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="outstanding-tab" data-bs-toggle="tab" data-bs-target="#outstanding-info" type="button"
                role="tab" aria-controls="outstanding" aria-selected="false">Outstandiing</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab7-tab" data-bs-toggle="tab" data-bs-target="#tab7-info" type="button"
                role="tab" aria-controls="tab7" aria-selected="false">History</button>
        </li>

    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active" id="tab1-info" role="tabpanel" aria-labelledby="tab1-info-tab">
            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                <thead>
                    <tr class="text-center">
                        <th>@lang('Deal')</th>
                        <th>@lang('Deal Name')</th>
                        <th>@lang('Stage')</th>
                        <th>@lang('Ownership')</th>
                        <th>@lang('Updated On')</th>
                        <th class="text-end">@lang('Deal Value')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Clossing Date')</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @php
                        $count = 1;
                        $total_deal = 0;
                        $total_amount = 0;
                    @endphp
                    @foreach ($pending as $value)
                        @php $total_deal += 1; @endphp
                        @if (
                            @$value->estimated_close_date <= Carbon\Carbon::today() &&
                                ($value->stage == 1 || $value->stage == 2 || $value->stage == 3))
                            <tr style="background-color:#ffebeb !important; color:#ff0000;">
                            @else
                            <tr>
                        @endif
                        <td><a href="{{ url('get-url-deal-track/' . $value->code) }}"
                                target="_blank">{{ @$value->code }}</a></td>
                        <td class="text-start">

                            {{ @$value->deal_name }}
                        </td>
                        <td>
                            @if ($value->stage == 1)
                                <span class="badge bg-warning">Prospecting</span>
                            @endif
                            @if ($value->stage == 2)
                                <span class="badge bg-success">Quote</span>
                            @endif
                            @if ($value->stage == 3)
                                <span class="badge bg-info">Closure</span>
                            @endif
                            @if ($value->stage == 4)
                                <?php
                                $data = App\SysHelper::deal_track_status3($value->receivables, $value->delivery, $value->invoice, $value->purchease, $value->sales, $value->accounts);
                                ?>
                                {!! $data !!}
                            @endif
                            @if ($value->stage == 5)
                                <span class="badge bg-danger">Lost</span>
                            @endif
                            @if ($value->stage == 6)
                                <span class="badge bg-dark">Cancelled</span>
                            @endif
                        </td>
                        <td class="text-start">{{ @$value->ownername->full_name }}</td>
                        <td>{{ date('d-M-Y', strtotime(@$value->updated_at)) }}</td>
                        <td class="text-end">
                            @php $aed = @App\SysHelper::get_aed_amount($value->deal_currency, $value->deal_value); @endphp
                            {{ @App\SysHelper::currancy_format_deal($aed, $value->company_id) }}
                            @php $total_amount += $aed; @endphp AED
                        </td>
                        <td>{{ date('d-M-Y', strtotime(@$value->created_at)) }}</td>
                        <td>{{ date('d-M-Y', strtotime(@$value->estimated_close_date)) }}</td>
                        <td>
                            <a class="badge text-center" href="{{ url('crm-deals/' . $value->id . '/view') }}">
                                View Deal
                            </a>
                        </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center">{{ $total_deal }}</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-end pr-1">
                            {{ @App\SysHelper::currancy_format_deal($total_amount, $value->company_id) }} AED</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>


        <div class="tab-pane fade" id="tab2-info" role="tabpanel" aria-labelledby="tab2-info-tab">
            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                <thead class="text-center">
                    <tr>
                        <th>@lang('Deal')</th>
                        <th>@lang('Deal Name')</th>
                        <th>@lang('Stage')</th>
                        <th>@lang('Ownership')</th>
                        <th>@lang('Updated On')</th>
                        <th class="text-end">@lang('Deal Value')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Clossing Date')</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @php
                        $count = 1;
                        $total_deal = 0;
                        $total_amount = 0;
                    @endphp
                    @foreach ($invoiced as $value)
                        @php $total_deal += 1; @endphp
                        @if (
                            @$value->estimated_close_date <= Carbon\Carbon::today() &&
                                ($value->stage == 1 || $value->stage == 2 || $value->stage == 3))
                            <tr style="background-color:#ffebeb !important; color:#ff0000;">
                            @else
                            <tr>
                        @endif
                        <td><a href="{{ url('get-url-deal-track/' . $value->code) }}"
                                target="_blank">{{ @$value->code }}</a></td>
                        <td class="text-start">{{ @$value->deal_name }}</td>
                        <td>
                            @if ($value->stage == 1)
                                <span class="warning btn-badge py-1 px-2">Prospecting</span>
                            @endif
                            @if ($value->stage == 2)
                                <span class="success btn-badge py-1 px-2">Quote</span>
                            @endif
                            @if ($value->stage == 3)
                                <span class="info btn-badge py-1 px-2">Closure</span>
                            @endif
                            @if ($value->stage == 4)
                                <?php
                                $data = App\SysHelper::deal_track_status3($value->receivables, $value->delivery, $value->invoice, $value->purchease, $value->sales, $value->accounts);
                                ?>
                                {!! $data !!}
                            @endif
                            @if ($value->stage == 5)
                                <span class="danger btn-badge py-1 px-2">Lost</span>
                            @endif
                            @if ($value->stage == 6)
                                <span class="dark btn-badge py-1 px-2">Cancelled</span>
                            @endif
                        </td>
                        <td class="text-start">{{ @$value->ownername->full_name }}</td>
                        <td>{{ date('d-M-Y', strtotime(@$value->updated_at)) }}</td>
                        <td class="text-end">
                            @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value); @endphp
                            {{ @App\SysHelper::currancy_format_deal($aed, $value->company_id) }}
                            @php $total_amount += $aed; @endphp AED
                        </td>
                        <td>{{ date('d-M-Y', strtotime(@$value->created_at)) }}</td>
                        <td>{{ date('d-M-Y', strtotime(@$value->estimated_close_date)) }}</td>
                        <td>
                            <a class="badge text-center" href="{{ url('crm-deals/' . $value->id . '/view') }}">
                                View Deal
                            </a>
                        </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center">{{ $total_deal }}</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-end pr-1">
                            {{ @App\SysHelper::currancy_format_deal($total_amount, $value->company_id) }} AED</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>


        <div class="tab-pane fade" id="tab3-info" role="tabpanel" aria-labelledby="tab3-info-tab">
            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                <thead>
                    <tr class="text-center">
                        <th>@lang('Deal')</th>
                        <th>@lang('Deal Name')</th>
                        <th>@lang('Stage')</th>
                        <th>@lang('Ownership')</th>
                        <th>@lang('Updated On')</th>
                        <th class="text-end">@lang('Deal Value')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Clossing Date')</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @php
                        $count = 1;
                        $total_deal = 0;
                        $total_amount = 0;
                    @endphp
                    @foreach ($delivery as $value)
                        @php $total_deal += 1; @endphp
                        @if (
                            @$value->estimated_close_date <= Carbon\Carbon::today() &&
                                ($value->stage == 1 || $value->stage == 2 || $value->stage == 3))
                            <tr style="background-color:#ffebeb !important; color:#ff0000;">
                            @else
                            <tr>
                        @endif
                        <td><a href="{{ url('get-url-deal-track/' . $value->code) }}"
                                target="_blank">{{ @$value->code }}</a></td>
                        <td class="text-start">

                            {{ @$value->deal_name }}
                        </td>
                        <td>
                            @if ($value->stage == 1)
                                <span class="warning btn-badge py-1 px-2">Prospecting</span>
                            @endif
                            @if ($value->stage == 2)
                                <span class="success btn-badge py-1 px-2">Quote</span>
                            @endif
                            @if ($value->stage == 3)
                                <span class="info btn-badge py-1 px-2">Closure</span>
                            @endif
                            @if ($value->stage == 4)
                                <?php
                                $data = App\SysHelper::deal_track_status3($value->receivables, $value->delivery, $value->invoice, $value->purchease, $value->sales, $value->accounts);
                                ?>
                                {!! $data !!}
                            @endif
                            @if ($value->stage == 5)
                                <span class="danger btn-badge py-1 px-2">Lost</span>
                            @endif
                            @if ($value->stage == 6)
                                <span class="dark btn-badge py-1 px-2">Cancelled</span>
                            @endif
                        </td>
                        <td>{{ @$value->ownername->full_name }}</td>
                        <td>{{ date('d-M-Y', strtotime(@$value->updated_at)) }}</td>
                        <td class="text-end">

                            <?php $vat = @App\SysHelper::get_deal_vat_amount($value->id, $value->quote_id); ?>

                            @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value+$vat); @endphp
                            {{ @App\SysHelper::currancy_format_deal($aed, $value->company_id) }}
                            @php $total_amount += $aed; @endphp AED
                        </td>
                        <td>{{ date('d-M-Y', strtotime(@$value->created_at)) }}</td>
                        <td>{{ date('d-M-Y', strtotime(@$value->estimated_close_date)) }}</td>
                        <td>


                            <a class="badge text-center" href="{{ url('crm-deals/' . $value->id . '/view') }}">
                                View Deal
                            </a>
                        </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center">{{ $total_deal }}</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-end pr-1">
                            {{ @App\SysHelper::currancy_format_deal($total_amount, $value->company_id) }} AED</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="tab-pane fade" id="tab4-info" role="tabpanel" aria-labelledby="tab4-info-tab">
            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                <thead>
                    <tr class="text-center">
                        <th>@lang('Deal')</th>
                        <th>@lang('Deal Name')</th>
                        <th>@lang('Stage')</th>
                        <th>@lang('Ownership')</th>
                        <th>@lang('Updated On')</th>
                        <th class="text-end">@lang('Deal Value')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Clossing Date')</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @php
                        $count = 1;
                        $total_deal = 0;
                        $total_amount = 0;
                    @endphp
                    @foreach ($receivables as $value)
                        @php $total_deal += 1; @endphp
                        @if (
                            @$value->estimated_close_date <= Carbon\Carbon::today() &&
                                ($value->stage == 1 || $value->stage == 2 || $value->stage == 3))
                            <tr style="background-color:#ffebeb !important; color:#ff0000;">
                            @else
                            <tr>
                        @endif
                        <td><a href="{{ url('get-url-deal-track/' . $value->code) }}"
                                target="_blank">{{ @$value->code }}</a></td>
                        <td class="text-start">

                            {{ @$value->deal_name }}
                        </td>
                        <td>
                            @if ($value->stage == 1)
                                <span class="warning btn-badge py-1 px-2">Prospecting</span>
                            @endif
                            @if ($value->stage == 2)
                                <span class="success btn-badge py-1 px-2">Quote</span>
                            @endif
                            @if ($value->stage == 3)
                                <span class="info btn-badge py-1 px-2">Closure</span>
                            @endif
                            @if ($value->stage == 4)
                                <?php
                                $data = App\SysHelper::deal_track_status3($value->receivables, $value->delivery, $value->invoice, $value->purchease, $value->sales, $value->accounts);
                                ?>
                                {!! $data !!}
                            @endif
                            @if ($value->stage == 5)
                                <span class="danger btn-badge py-1 px-2">Lost</span>
                            @endif
                            @if ($value->stage == 6)
                                <span class="dark btn-badge py-1 px-2">Cancelled</span>
                            @endif
                        </td>
                        <td class="text-start">{{ @$value->ownername->full_name }}</td>
                        <td>{{ date('d-M-Y', strtotime(@$value->updated_at)) }}</td>
                        <td class="text-end">
                            @php $aed=@App\SysHelper::get_aed_amount($value->deal_currency,$value->deal_value); @endphp
                            {{ @App\SysHelper::currancy_format_deal($aed, $value->company_id) }}
                            @php $total_amount += $aed; @endphp AED
                        </td>
                        <td>{{ date('d-M-Y', strtotime(@$value->created_at)) }}</td>
                        <td>{{ date('d-M-Y', strtotime(@$value->estimated_close_date)) }}</td>
                        <td>
                            <a class="badge  text-center" href="{{ url('crm-deals/' . $value->id . '/view') }}">
                                View Deal
                            </a>
                        </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center">{{ $total_deal }}</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th class="text-end pr-1">
                            {{ @App\SysHelper::currancy_format_deal($total_amount, $value->company_id) }} AED</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="tab-pane fade" id="tab5-info" role="tabpanel" aria-labelledby="tab5-info-tab">
            <div class="table-responsive">


                <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                    <thead>

                        <tr class="text-center">
                            <th>@lang('Sr No')</th>
                            <th>@lang('Deal ID')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Customer Name')</th>
                            <th>@lang('Contact Person')</th>
                            <th>@lang('Mobile No')</th>
                            <th>@lang('Start Date')</th>
                            <th>@lang('End Date')</th>
                            <th>@lang('Invoicing')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Sales Person')</th>
                            <th>@lang('Description')</th>
                        </tr>
                    </thead>

                    <tbody class="text-center">
                        @if (count($amcdata) > 0)
                            @foreach ($amcdata as $value)
                                <tr @if (@$value->is_delete == 1) class="bg-dark" @endif>
                                    <td>{{ @$value->id }}</td>
                                    <td><a href="{{ url('get-url-deal-track/' . $value->deal_code->code) }}"
                                            target="_blank">{{ @$value->deal_code->code }}</a></td>
                                    <td>{{ date('d/m/Y', strtotime(@$value->date)) }}</td>
                                    <td>{{ @$value->custname->name }}</td>
                                    <td>{{ @$value->contact_person }}</td>
                                    <td>{{ @$value->mobile_no }}</td>
                                    <td>{{ date('d/m/Y', strtotime(@$value->start_date)) }}</td>
                                    <td>{{ date('d/m/Y', strtotime(@$value->end_date)) }}</td>
                                    <td>{{ @$value->invoice }}</td>
                                    <td>{{ @$value->amount }}</td>
                                    <td>{{ @$value->salesperson->full_name }}</td>
                                    <td>{{ @$value->description }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>



        <div class="tab-pane fade" id="tab6-info" role="tabpanel" aria-labelledby="tab6-info-tab">
            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                <thead>
                    @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
                        <tr>
                            <td colspan="6">
                                @if (session()->has('message-success'))
                                    <div class="alert alert-success">
                                        {{ session()->get('message-success') }}
                                    </div>
                                @elseif(session()->has('message-danger'))
                                    <div class="alert alert-danger">
                                        {{ session()->get('message-danger') }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endif

                    <tr class="text-center">
                        <th>@lang('PS ID')</th>
                        <th>@lang('Deal No')</th>
                        <th>@lang('Date ')</th>
                        <th>@lang('Customer Name')</th>
                        <th>@lang('Contact Person')</th>
                        <th>@lang('Mobile No')</th>
                        <th>@lang('Location of Work')</th>
                        <th>@lang('Amount')</th>
                        <th>@lang('Sales Person')</th>
                        <th>@lang('Description')</th>
                    </tr>
                </thead>

                <tbody class="text-center">
                    @if (count($support) > 0)
                        @foreach ($support as $value)
                            <tr>
                                <td>{{ @$value->id }}</td>

                                <td><a href="{{ url('get-url-deal-track/' . $value->deal_code->code) }}"
                                        target="_blank">{{ @$value->deal_code->code }}</a></td>
                                <td>{{ date('d-M-Y', strtotime(@$value->date)) }}</td>
                                <td>{{ @$value->custname->name }} <input type="hidden"
                                        id="list_custname_{{ $value->id }}"
                                        value="{{ @$value->custname->name }}" /></td>
                                <td>{{ @$value->contact_person }} <input type="hidden"
                                        id="list_contact_person_{{ $value->id }}"
                                        value="{{ @$value->contact_person }}" /></td>
                                <td>{{ @$value->mobile }} <input type="hidden"
                                        id="list_mobile_{{ $value->id }}" value="{{ @$value->mobile }}" /></td>
                                <td>{{ @$value->location_of_work }} <input type="hidden"
                                        id="list_location_of_work_{{ $value->id }}"
                                        value="{{ @$value->location_of_work }}" /></td>
                                <td>{{ @$value->amount }}</td>
                                <td>{{ @$value->ownername->full_name }}</td>
                                <td>{{ @$value->deal_description }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

       <div class="tab-pane fade" id="outstanding-info" role="tabpanel" aria-labelledby="outstanding-info-tab">
            <h1>outstanding</h1>
        </div>




    </div>
</div>


<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

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

    </h4>
    <div class="purchase-order-content-header-right">

        <form method="GET" action="{{ url('customer-from-list/' . @$custDetails->id . '?customerform_action=edit') }}">
            <button type="submit" name="customerform_action" value="edit" class="btn btn-light">
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
            {{-- <div class="col-12 mb-3">
                <label class="form-label">Created By:</label>
                <div class="form-control-plaintext truncate-text-custom ">
                    <a href="" class="text-dark fw-normal">
                        {{ @$custDetails->createdby->full_name }}
                    </a>
                </div>
            </div> --}}
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

                @if (count($editAddressbook) > 0)
                    @foreach ($editAddressbook as $data)
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

                            @if (count($editContact) > 0)
                                @foreach ($editContact as $data)
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

                            @if (count($editDoc) > 0)
                                @foreach ($editDoc as $doc)
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

@if (count($excisting_list) > 0)

    <div class="card mb-3">
        <div class="card-body">
            <hr>
            <b>Similer Customer Accounts</b>
            <table class="table table-bordered table-striped" id="dataTable_exclude" width="100%" cellspacing="0">
                <tr>
                    <th>Customer Code</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Work Phone</th>
                    <th></th>
                </tr>
                @foreach ($excisting_list as $list)
                    <tr>
                        <td>{{ $list->code }}</td>
                        <td>{{ $list->name }}</td>
                        <td>{{ $list->email }}</td>
                        <td>{{ $list->first_name }}</td>
                        <td>{{ $list->mobile }}</td>
                        <td>{{ $list->contcat_number }}</td>
                        <td><a class="btn btn-info pt-0 pb-0"
                                href="{{ url('customer-form-details/' . $list->id . '/merge/' . $editData->id . '') }}"
                                onclick="return confirm('Are you sure you want to merge this item?');">Update
                                with This</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endif

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

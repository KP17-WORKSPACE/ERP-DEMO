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

<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        {{ @$custDetails->code }}
    </h4>
    <div class="purchase-order-content-header-right">

        <form method="GET" action="{{ url('suppliers', @$custDetails->id) }}">
            {{-- <input hidden type="text" value="{{@$po->id}}" name="id"> --}}
            <button type="submit" name="supplier_action" value="edit" class="btn btn-light">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </button>
        </form>

        <form method="GET" action="{{ url('suppliers') }}">
            <button type="submit" name="supplier_action" value="add" class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>
        </form>

       <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">




            @if ($custDetails->customer_id != null && $custDetails->customer_id != '')
                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('customers/' . $custDetails->customer_id) }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        View Customer
                    </a>
                </li>
                
            @else
                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('customers?customer_action=createcustomer&supplier_id=' . $custDetails->id) }}">
                        <i class="ico icon-outline-add-square text-success  title-15 me-2"></i>
                        Create Customer
                    </a>
                </li>
            @endif



            </ul>
        </div>

    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <!-- <h4 class="mb-3 color-sub-head">Supplier Info (SUPS-1060)</h4> -->
        <div class="d-flex align-items-center mb-3">
            <div class="font-weight-600 title-15 me-3 text-success"> {{ @$custDetails->customer_name_display }}
            </div>
            @if (@$custDetails->status == 2)
                <span class="badge bg-danger">Inactive</span>
            @else
                <span class="badge bg-success">Active</span>
            @endif

        </div>
        <div class="row">
            <div class="col-2 mb-3">
                <label class="form-label">Supplier Type:</label>
                <div class="form-control-plaintext">
                    @if (@$custDetails->account_type == 1)
                        Vendor
                    @endif
                    @if (@$custDetails->account_type == 2)
                        Forwarder
                    @endif
                    @if (@$custDetails->account_type == 3)
                        Courier
                    @endif
                </div>
            </div>
            <div class="col-2 mb-3">
                <label class="form-label">Contact Name:</label>
                <div class="form-control-plaintext"> {{ @$custDetails->customer_salutation }}
                    {{ @$custDetails->first_name }} {{ @$custDetails->last_name }}
                </div>
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
                <div class="form-control-plaintext"> {{ @$custDetails->email }}</div>
            </div>
            <div class="col-2 mb-3">
                <label class="form-label">Designation:</label>
                <div class="form-control-plaintext">{{ @$custDetails->designation }}</div>
            </div>
                @if (@$custDetails->website)
                 <div class="col-2 mb-3">
                <label class="form-label">Website: </label>
                <div class="form-control-plaintext truncate-text-custom "> <a href="{{ @$custDetails->website }}" target="_blank">{{ @$custDetails->website }}</a> </div>
            </div>
            @endif

            @if (@$custDetails->maps_location)
                  <div class="col-2 mb-3">
                <label class="form-label">Location: </label>
                <div class="form-control-plaintext truncate-text-custom "> <a href="{{ @$custDetails->maps_location }}" target="_blank">View on Map</a> </div>
            </div>
            @endif
            <div class="col-2 mb-3">
                <label class="form-label">Sales Person:</label>
                <div class="form-control-plaintext">
                    <a href="" class="text-dark fw-normal">{{ $custDetails->salesperson->full_name }}</a>
                </div>
            </div>

                <div class="col-2 mb-3">
                <label class="form-label">Created By:</label>
                <div class="form-control-plaintext truncate-text-custom ">
                    <a href="#" class="text-dark fw-normal">
                        {{ @$custDetails->createdby->full_name }}  {{ optional($custDetails->created_at)->format('d/m/Y h:i A') }}
                    </a>
                </div>
            </div>

             <div class="col-2 mb-3">
                <label class="form-label">Updated By:</label>
                <div class="form-control-plaintext truncate-text-custom ">
                    <a href="#" class="text-dark fw-normal">
                        {{ @$custDetails->updatedby->full_name }}  {{ optional($custDetails->updated_at)->format('d/m/Y h:i A') }}
                       
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
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-info-tab" data-bs-toggle="tab" data-bs-target="#history-info"
                type="button" role="tab" aria-controls="history-info" aria-selected="false">Transaction</button>
        </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-info-tab" data-bs-toggle="tab" data-bs-target="#history-info"
                type="button" role="tab" aria-controls="history-info" aria-selected="false">History</button>
        </li>
    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active pt-0" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
            <!-- <h4 class="mb-3 color-sub-head">Warehouse Address</h4> -->



            <div class="row">



                @if (count($custAddress) > 0)

                    @foreach ($custAddress as $data)
                        <div class="col-3 mt-4">
                             <h4 class="mb-1 color-sub-head font-size-13 mb-2">

                              

                                @if ($data->is_shipping == 0)
                                    Supplier Address
                                @elseif($data->is_shipping == 1)
                                    Warehouse Address
                                @else
                                    <div class="fw-bold" style="visibility: hidden;">Placeholder</div>
                                @endif
                            </h4>
                            <table class="detail-item-table-noborder table table-hover">
                                <thead>
                                     <tr>
                                        <td class="text-start" width="100px">Country</td>
                                        <td>:&nbsp;&nbsp;{{ $data->countryname['name'] }}</td>
                                    </tr>
                                     <tr>
                                        <td class="text-start" width="100px">State</td>
                                        <td>:&nbsp;&nbsp;{{ $data->statename['name'] }}</td>
                                    </tr>

                                     <tr>
                                        <td class="text-start" width="100px">City</td>
                                        <td >:&nbsp;&nbsp; {{ $data->city }}</td>
                                    </tr>

                                 
                                   

                                </thead>

                            </table>
                        </div>

                         <div class="col-3 mt-4 border-end">
                             <h4 class="mb-1 color-sub-head font-size-13 mb-2">

                              

                               
                            </h4>
                            <table class="detail-item-table-noborder table table-hover">
                                <thead>
                                   

                                        <tr>
                                        <td class="text-start" width="100px">Area</td>
                                        @if ($data->area == null || $data->area == '')
                                            <td class="truncate-text-custom">:&nbsp;&nbsp;{{ $custDetails->address2 }}</td>
                                        @else
                                           
                                            <td class="truncate-text-custom">:&nbsp;&nbsp;{{ $data->area }}</td>

                                        @endif
                                        {{-- <td>:&nbsp;&nbsp;{{ $data->area }}</td> --}}
                                    </tr>
                                    <tr>
                                        <td class="text-start" width="100px">Building Name</td>
                                         @if ($data->building_name == null || $data->building_name == '')
                                            <td class="truncate-text-custom">:&nbsp;&nbsp;{{ $custDetails->address }}</td>
                                        @else
                                        <td class="truncate-text-custom">:&nbsp;&nbsp;{{ $data->building_name }}</td>

                                        @endif
                                        {{-- <td >:&nbsp;&nbsp;{{ $data->building_name }}</td> --}}
                                    </tr>
                                    <tr>
                                        <td class="text-start" width="100px">Flat/Office No</td>
                                        <td>:&nbsp;&nbsp;{{ $data->flat_office_no }}</td>
                                    </tr>
                                     
                                    {{-- <tr>
                                        <td class="text-start" width="100px">Address 1</td>
                                        <td class="truncate-text-custom">:&nbsp;&nbsp;{{ $data->address }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start" width="100px">Address 2</td>
                                        <td>:&nbsp;&nbsp;{{ $data->address2 }}</td>
                                    </tr> --}}
                                   
                                   
                                    <tr>
                                        <td class="text-start" width="100px">Post Box</td>
                                        <td>:&nbsp;&nbsp;{{ $data->zip_code }}</td>
                                    </tr>
                                   

                                </thead>

                            </table>
                        </div>

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
                                <!-- <th>Work Phone</th> -->
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
                                        <!-- <td>{{ str_replace(' ', '', $data->work_phone) }}</td> -->
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
                            {{ @App\SysHelper::com_curr_format($custDetails->credit_limit,'','',',') }}
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
                                            <a class="btn-sm  btn-light"
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

<div class="card">
    <div class="card-body">
        <h4 class="mb-1 color-sub-head">Supplier Outstanding</h4>

        <script>
            function download_outstanding(id) {
                var date = $('#till_date').val();
                var url = $("#base_url").val() + "/payables-outstanding-download/" + id + "/" + date;
                window.location.href = url;
            }
        </script>


        <div class="card p-4">

            <div class="accordion gap-0" id="accordionExample">
                @if (count($data_all) > 0)
                    <?php $no = 1;
                    $all_total = 0;
                    $k = 0; ?>
                    @foreach ($data_all as $data)
                        <?php
                                if(count($data)>0){
                                      $data_adjestment = @App\SysPurchaseReturnAdjestment::select('piv_no',DB::raw('sum(paid_amount) as paid_amount'))->wherein('piv_no',$data->pluck("transaction_no"))->groupby('piv_no')->get();
                      
                                      $data_payment = DB::table('sys_payment as p')->select('pa.bi_doc_no','p.doc_number','pa.bi_amount','p.payment_through','p.payment_date','p.cheque_number','p.cheque_bank_name')
                                      ->join('sys_payment_adjustments as pa','pa.bi_doc_number','p.doc_number')->where('pa.account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->where('p.status',1)->get();
                                      
                                      $data_payment2 = DB::table('sys_journalvoucher as j')->select('pa.bi_doc_no','j.doc_number','pa.bi_amount','j.doc_date')
                                      ->join('sys_payment_adjustments as pa','pa.bi_doc_number','j.doc_number')->where('pa.account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->where('j.status',1)->get();
                                ?>

                        <?php $aname = $accounts->where('id', $data[0]->account_id)->first();
                        $cust_det = @App\SysHelper::get_customer_contact_detail($aname->account_code); ?>

                        <script>
                            function set_total(id, at) {
                                $('#sum_' + id).text(at.toFixed(@json(session('logged_session_data.decimal_point'))));
                                $('#collapse' + id).css('display', '');
                                $('#account_table' + id).css('display', '');
                            }
                        </script>

                        <table id="account_table{{ $aname->id }}" class="table"
                            style="border: solid 1px #e3e6f0; margin-bottom: -1px !important;">
                            <thead>
                                <tr>
                                    <th class="border text-center" width="100px"><a
                                            href="{{ url('get-url-supplier/' . $aname->account_code) }}"
                                            target="_blank">{{ $aname->account_code }}</a></th>
                                    <th class="border text-left"><a class="text-left" type="button"
                                            data-toggle="collapse" data-target="#collapse{{ $aname->id }}"
                                            aria-expanded="true"
                                            aria-controls="collapse{{ $aname->id }}">{{ $aname->account_name }}
                                            <span
                                                style="font-weight: normal; color: #3d3d3d;">{!! $cust_det !!}</span></a>
                                        <a style="display: none;" data-id="{{ @$aname->id }}" id="crmajax"
                                            class="btn-badge btn btn-info  py-1 px-2"
                                            style="  font-weight: 500;  border: 1px solid transparent;  padding: 0.375rem 0.75rem;  font-size: 10px;  line-height: .7;  border-radius: 2px;cursor: pointer;float:right;"
                                            data-toggle="modal" data-target="#ModalTrackComment"
                                            title="Click to Fullfill">
                                            Comments</a>
                                        <a class="text-danger ml-2 float-end" title="Download"
                                            onclick="download_outstanding({{ $aname->id }})"><i
                                                class="ico icon-bold-download-minimalistic fw-bold title-15"></i></a>
                                    </th>
                                    <th class="border text-end" width="100px"><label
                                            id="sum_{{ $aname->id }}"></label></th>
                                </tr>
                            </thead>
                        </table>

                        <div id="collapse{{ $aname->id }}" class="" data-parent="#accordionExample">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="border text-center">Doc Date</th>
                                        <th class="border text-center">Doc No</th>
                                        <th class="border text-center">Deal ID</th>
                                        <th class="border text-center">Amount</th>
                                        <th class="border text-center">Adjustments</th>
                                        <th class="border text-center">Balance</th>
                                        <th class="border text-center">Total Balance</th>
                                        <th class="border text-center hidecol_{{ $aname->id }}">Receipt Date</th>
                                        <th class="border text-center hidecol_{{ $aname->id }}">Doc Number</th>

                                        <th class="border text-center">Payment Terms</th>
                                        <th class="border text-center">Due Date</th>
                                        <th class="border text-center">Over Due</th>
                                        <th class="border text-center">0-30</th>
                                        <th class="border text-center">31-60</th>
                                        <th class="border text-center">61-90</th>
                                        <th class="border text-center">>90</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $ats = [];
                                    $k = 0;
                                    foreach ($data as $dt) {
                                        $DueData = App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no, $dt->transaction_date);
                                    
                                        if ($overdue != 999999) {
                                            if ($DueData[1] < $overdue) {
                                                $ats[$k] = $dt;
                                                $k++;
                                            }
                                        }
                                    
                                        if ($ageing != 99999) {
                                            if ($ageing < 0 && $DueData[1] < 0) {
                                                $ats[$k] = $dt;
                                                $k++;
                                            }
                                            if ($ageing >= 0 && $ageing < 31 && $DueData[1] >= 0 && $DueData[1] < 31) {
                                                $ats[$k] = $dt;
                                                $k++;
                                            }
                                            if ($ageing > 30 && $ageing < 61 && $DueData[1] > 30 && $DueData[1] < 61) {
                                                $ats[$k] = $dt;
                                                $k++;
                                            }
                                            if ($ageing >= 60 && $ageing <= 90 && $DueData[1] >= 60 && $DueData[1] <= 90) {
                                                $ats[$k] = $dt;
                                                $k++;
                                            }
                                        }
                                    }
                                    
                                    ?>
                                    @php
                                    $adjustments = 0;
                                    $b = 0;
                                    $grand_credit_amount = 0;
                                    $grand_paid = 0;
                                    $grand_balance = 0;
                                    $grand_total_balance = 0;
                                    $gtot1 = 0;
                                    $gtot2 = 0;
                                    $gtot3 = 0;
                                    $gtot4 = 0;
                                    @endphp 
                                    @if (count($data) > 0)
                                        @php $sum_b=0; @endphp
                                        @foreach ($data as $dt)
                                            @php
                                                $adjustments = 0;
                                                $receipt_date = '';
                                                $doc_number = '';
                                                $cheque_number = '';
                                                $bank_name = '';
                                                $bi_amount = 0;
                                                $bi_amount2 = 0;
                                                $paid = 0;
                                            @endphp
                                            @php
                                                $adjustments = $data_adjestment
                                                    ->where('piv_no', $dt->transaction_no)
                                                    ->max('paid_amount');
                                                $payment = $data_payment->where('bi_doc_no', $dt->transaction_no);
                                                if (count($payment) > 0) {
                                                    foreach ($payment as $p) {
                                                        $receipt_date .=
                                                            date('d/m/Y', strtotime($p->payment_date)) . ',';
                                                        $doc_number .= $p->doc_number . ',';
                                                        if ($p->cheque_number != '') {
                                                            $cheque_number .= $p->cheque_number . ',';
                                                        }
                                                        if ($p->cheque_bank_name != '') {
                                                            $bank_name .= $p->cheque_bank_name . ',';
                                                        }
                                                        $bi_amount += $p->bi_amount;
                                                    }
                                                }

                                                $payment2 = $data_payment2->where('bi_doc_no', $dt->transaction_no);
                                                if (count($payment2) > 0) {
                                                    foreach ($payment2 as $p) {
                                                        $receipt_date .= date('d/m/Y', strtotime($p->doc_date)) . ',';
                                                        $doc_number .= $p->doc_number . ',';

                                                        $bi_amount2 += $p->bi_amount;
                                                    }
                                                }

                                                $paid += $adjustments + $bi_amount + $bi_amount2;

                                                $deal_id = '';
                                                $deal_code = '';
                                                $sales_person = '';
                                                $deal = @App\SysHelper::get_deal_detail_for_payable_outstanding(
                                                    $dt->transaction_no
                                                );
                                                if (isset($deal) && $deal != '') {
                                                    $deal_id = $deal->id;
                                                    $deal_code = $deal->code;
                                                    $sales_person = $deal->full_name;
                                                }

                                            @endphp
                                            <?php
                                            if ($dt->credit_amount != $paid) {
                                                $grand_credit_amount += $dt->credit_amount;
                                                $grand_paid += $paid;
                                                $grand_balance += $dt->credit_amount - abs($paid);
                                            }
                                            ?>
                                            @php $DueData =  @App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no,$dt->transaction_date); @endphp

                                            @if ($dt->credit_amount != $paid)
                                                <tr>
                                                    <td class="border text-center">
                                                        {{ date('d/m/Y', strtotime($dt->transaction_date)) }}</td>
                                                    <td class="border text-center"><a
                                                            href="{{ url('get-url-purchase-invoice/' . $dt->transaction_no) }}"
                                                            target="_blank">{{ $dt->transaction_no }}</a></td>
                                                    <td class="border text-center"><a
                                                            href="{{ url('crm-deals/' . $deal_id . '/view') }}"
                                                            target="_blank">{{ $deal_code }}</a></td>
                                                    <td class="border text-center">{{ $dt->credit_amount }}</td>
                                                    <td class="border text-center">
                                                        {{ @App\SysHelper::com_curr_format($paid, 2, '.', ',') }}</td>
                                                    <td class="border text-center">
                                                        {{ @App\SysHelper::com_curr_format($dt->credit_amount - abs($paid), 2, '.', ',') }}
                                                        @php $b += $dt->credit_amount-abs($paid); @endphp</td>
                                                    <td class="border text-center">
                                                        {{ @App\SysHelper::com_curr_format($b, 2, '.', ',') }}</td>

                                                    @php
                                                        $sum_b += $dt->credit_amount - abs($paid);
                                                        $all_total += $dt->credit_amount - abs($paid);
                                                    @endphp
                                                    <script>
                                                        set_total({{ $aname->id }}, {{ $sum_b }});
                                                    </script>

                                                    <td class="border text-center hidecol_{{ $aname->id }}">
                                                        {{ rtrim($receipt_date, ',') }}</td>
                                                    <td class="border text-center hidecol_{{ $aname->id }}">
                                                        {{ rtrim($doc_number, ',') }}</td>

                                                    @php $DueData =  @App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no,$dt->transaction_date); @endphp

                                                    <td class="border text-center">{{ $DueData[2] }}</td>
                                                    <td class="border text-center">{{ $DueData[0] }}</td>
                                                    <?php 
                                          if($DueData[1] >0){ ?>
                                                    <td class="border text-center" style="color:red">
                                                        {{ $DueData[1] }}</td>
                                                    <?php } else { ?>

                                                    <td class="border text-center">{{ $DueData[1] }}</td>
                                                    <?php }  ?>

                                                    <?php
                                                    if ($DueData[3] == 1) {
                                                        $gtot1 += $dt->debit_amount - abs($paid);
                                                    }
                                                    if ($DueData[3] == 2) {
                                                        $gtot2 += $dt->debit_amount - abs($paid);
                                                    }
                                                    if ($DueData[3] == 3) {
                                                        $gtot3 += $dt->debit_amount - abs($paid);
                                                    }
                                                    if ($DueData[3] == 4) {
                                                        $gtot4 += $dt->debit_amount - abs($paid);
                                                    }
                                                    
                                                    ?>


                                                    @if ($DueData[3] == 1)
                                                        <td class="border text-center">
                                                            {{ @App\SysHelper::com_curr_format($dt->credit_amount - abs($paid), 2, '.', ',') }}
                                                        </td>
                                                    @else
                                                        <td class="border text-center">&nbsp;</td>
                                                    @endif
                                                    @if ($DueData[3] == 2)
                                                        <td class="border text-center">
                                                            {{ @App\SysHelper::com_curr_format($dt->credit_amount - abs($paid), 2, '.', ',') }}
                                                        </td>
                                                    @else
                                                        <td class="border text-center">&nbsp;</td>
                                                    @endif
                                                    @if ($DueData[3] == 3)
                                                        <td class="border text-center">
                                                            {{ @App\SysHelper::com_curr_format($dt->credit_amount - abs($paid), 2, '.', ',') }}
                                                        </td>
                                                    @else
                                                        <td class="border text-center">&nbsp;</td>
                                                    @endif
                                                    @if ($DueData[3] == 4)
                                                        <td class="border text-center">
                                                            {{ @App\SysHelper::com_curr_format($dt->credit_amount - abs($paid), 2, '.', ',') }}
                                                        </td>
                                                    @else
                                                        <td class="border text-center">&nbsp;</td>
                                                    @endif

                                                </tr>
                                            @endif
                                            @if (count($payment) == 0)
                                                <script>
                                                    $('.hidecol_' + {{ $aname->id }}).css('display', 'none');
                                                </script>
                                            @endif
                                        @endforeach
                                    @endif
                                    <tr>
                                        <td colspan="3"></td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($grand_credit_amount, 2, '.', ','); ?> </b></td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($grand_paid, 2, '.', ','); ?> </b></td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($grand_balance, 2, '.', ','); ?></b> </td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($b, 2, '.', ','); ?></b> </td>

                                        <td class="border text-center" colspan="3">&nbsp </td>

                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($gtot1, 2, '.', ','); ?></b> </td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($gtot2, 2, '.', ','); ?></b> </td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($gtot3, 2, '.', ','); ?> </b></td>
                                        <td class="border text-center"><b><?php echo @App\SysHelper::com_curr_format($gtot4, 2, '.', ','); ?> </b></td>

                                    </tr>
                                    <?php $tot = 0;
                                    $total = 0;
                                    $total_dr = 0;
                                    $total_cr = 0; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } ?>
                    @endforeach
                    <table class="table" style="border: solid 1px #e3e6f0;">
                        <thead>
                            <tr>
                                <th class="border fw-bold text-end" colspan="1">Total</th>
                                <th class="border fw-bold text-end" colspan="1" width="200px">
                                    {{ $all_total }}</th>
                            </tr>

                        </thead>
                    </table>
                @endif
            </div>





        </div>
    </div>


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

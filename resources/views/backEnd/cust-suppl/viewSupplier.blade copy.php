@extends('backEnd.masterpage')
@section('mainContent')

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

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

    <?php try { ?>

    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Supplier Details</h2>
                <span class="page-label">Home - Supplier Details</span>
            </div>
            <div>
                <a href="{{ url('add-supplier') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Supplier</a>
                <a href="{{ url('suppliers') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Supplier List</a>
                <a href="{{ url('supplier-edit/' . @$custDetails->id) }}"><button class="btn btn-primary">Edit Supplier</button></a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Supplier Info</h2>
                    
                    <span class="badge badge-danger text-left"
                        @if (@$custDetails->type == 1) style="background: #228c22;" @endif
                        @if (@$custDetails->type == 2) style="background: #FFA500;" @endif
                        @if (@$custDetails->type == 3) style="background: #FF0000;" @endif
                        @if (@$custDetails->type == 4) style="background: #000000;" @endif>
                        @if (isset($custDetails))
                            {{ @$custDetails->name }}
                        @endif
                    </span>
                    <p class="mb-1 text-muted"><span class="font-semibold">Display Name :</span> <span
                            class="f-14 text-dark font-weight-semibold">
                            {{ @$custDetails->customer_name_display }}
                        </span></p>

                    <p class="mb-1 text-muted"><span class="font-semibold">Supplier Code :</span> <span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->code }}
                            @endif
                        </span></p>
                        
                    <p class="mb-1 text-muted"><span class="font-semibold">Name :</span> <span
                        class="f-14 text-dark font-weight-semibold">
                            {{ @$custDetails->customer_salutation }} {{ @$custDetails->first_name }} {{ @$custDetails->last_name }}
                    </span></p>

                        
                    <p style="display: none;" class="mb-1 text-muted"><span class="font-semibold">Contact Person : </span><span
                            class="badge badge-danger">
                            @if (isset($custDetails))
                                {{ @$custDetails->contcat_person }}
                            @endif
                        </span></p>
                        <p class="mb-1 text-muted"><span class="font-semibold">Designation : </span><span
                                class="badge badge-danger">
                                @if (isset($custDetails))
                                    {{ @$custDetails->designation }}
                                @endif
                            </span></p>
                        <p class="mb-1 text-muted"><span class="font-semibold">Contact Number: </span><span
                                class="f-14 text-dark font-weight-semibold">
                                @if (isset($custDetails))
                                    {{ @$custDetails->contcat_number }}
                                @endif
                            </span></p>
                        <p class="mb-1 text-muted"><span class="font-semibold">Mobile: </span><span
                                class="f-14 text-dark font-weight-semibold">
                                @if (isset($custDetails))
                                    {{ @$custDetails->mobile }}
                                @endif
                            </span></p>

                    <p class="mb-1 text-muted"><span class="font-semibold">Mail : </span><span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->email }}
                            @endif
                        </span></p>
                </div>
            </div>
            <div class="col-lg-4 mb-3" style="display: none;">
                <div class="p-4 card h-100">
                    <h2 class="head">Billing Address</h2>
                    <p class="mb-1 text-muted"><span class="font-semibold">Address: </span><span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->address }}
                            @endif
                        </span></p>
                    <p class="mb-1 text-muted"><span class="font-semibold">Address 2: </span><span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->address2 }}
                            @endif
                        </span></p>
                        <p class="mb-1 text-muted"><span class="font-semibold">Mobile: </span><span
                                class="f-14 text-dark font-weight-semibold">
                                @if (isset($custDetails))
                                    {{ @$custDetails->mobile }}
                                @endif
                            </span></p>
                    <hr />
                    <h2 class="head">Shipping Address</h2>
                    <p class="mb-1 text-muted"><span class="font-semibold">Address: </span><span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->address }}
                            @endif
                        </span></p>
                    <p class="mb-1 text-muted"><span class="font-semibold">Address 2: </span><span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->address2 }}
                            @endif
                        </span></p>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">VAT & Payment Info</h2>
                    <div class="card-body p-0">
                        <div class="row">
                            <label class="col-lg-4 text-muted">Created By</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: <span class="text-info">{{ $custDetails->salesperson->full_name }}</span>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-4 text-muted">Transaction Type</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                        {{ @$custDetails->transaction_type }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-4 text-muted">Credit Limit</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                        {{ @$custDetails->credit_limit }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-4 text-muted">Credit Days </label>
                            <div class="col-lg-8 d-flex align-items-center">
                                <span class="font-weight-bold text-gray-800 me-2">: @if (isset($custDetails))
                                        {{ @$custDetails->credit_days }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        @if (isset($custDetails) && !empty(@$custDetails->payment_terms))
                            <div class="row">
                                <label class="col-lg-4 text-muted">Payment Terms </label>
                                <div class="col-lg-8 d-flex align-items-center">
                                    <span class="font-weight-bold text-gray-800 me-2">: @if (isset($custDetails))
                                            {{ @$custDetails->paymentterms->title }} {{ @$custDetails->payment_terms_txt }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                        @if (isset($custDetails) && !empty(@$custDetails->vat_country))
                            <div class="row">
                                <label class="col-lg-4 text-muted">Vat Country</label>
                                <div class="col-lg-8">
                                    <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                            {{ @$custDetails->vatcountry->name }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                        @if (isset($custDetails) && !empty(@$custDetails->vat_state))
                            <div class="row">
                                <label class="col-lg-4 text-muted">VAT State</label>
                                <div class="col-lg-8">
                                    <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                            {{ @$custDetails->vatstate->name }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <label class="col-lg-4 text-muted">VAT Percentage</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                        {{ @$custDetails->vat_percentage }}% @if($custDetails->vat_is_fixed==1) <span class="btn btn-warning m-0 p-0">&nbsp;Fixed&nbsp;</span>@endif
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-4 text-muted">VAT Number</label>
                            <div class="col-lg-8">
                                <a href="#" class="font-weight-bold text-gray-800 text-hover-primary">: @if (isset($custDetails))
                                        {{ @$custDetails->vat_number }}
                                    @endif
                                </a>
                            </div>
                        </div>
                        @if (isset($custDetails) && !empty(@$custDetails->customer_type))
                            <div class="row">
                                <label class="col-lg-4 text-muted">Supplier Type</label>
                                <div class="col-lg-8">
                                    <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                            {{ @$custDetails->suppliertype->title }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endif
                        @if (isset($custDetails) && !empty(@$custDetails->sale_type))
                        <div class="row">
                            <label class="col-lg-4 text-muted">Purchase Type</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                        {{ @$custDetails->purchasetype->title }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif

                    </div>
                </div>
            </div>

            <div class="col-lg-12 mb-3"><br />

{{--  tabs  --}}
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="true">Address</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#contactperson" role="tab" aria-controls="contactperson" aria-selected="true">Contact Person</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#documents" role="tab" aria-controls="documents" aria-selected="false">Documents</a></li>
  </ul>
{{--  tabs  --}}

<div class="tab-content">
    <div class="tab-pane active pt-2" id="address" role="tabpanel" aria-labelledby="address-tab">

        <div class="row">
            <div class="col-md-12">
                @if (count($custAddress)>0)
                <div class="row">
                    @foreach ($custAddress as $data)
                        <div class="col-md-4">
                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                
                                @if($data->set_default==1 && $data->is_shipping==0)<tr><td colspan="2"><b>Billing Address</b></td></tr>@endif
                                @if($data->is_shipping==1)<tr><td colspan="2"><b>Shipping Address</b></td></tr>@endif
                                
                                <tr><td>Country</td><td>{{ $data->countryname["name"] }}</td></tr>
                                <tr><td>Address 1</td><td>{{ $data->address }}</td></tr>
                                <tr><td>Address 2</td><td>{{ $data->address2 }}</td></tr>
                                <tr><td>City</td><td>{{ $data->city }}</td></tr>
                                <tr><td>State</td><td>{{ $data->statename["name"] }}</td></tr>
                                <tr><td>Post Box</td><td>{{ $data->zip_code }}</td></tr>
                            </table>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    {{--  Address  --}}
    
    {{--  Contact  --}}
    <div class="tab-pane pt-2" id="contactperson" role="tabpanel" aria-labelledby="contactperson-tab">        
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-striped" id="pi-ret-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>@lang('Salutation')</th>
                            <th>@lang('First Name')</th>
                            <th>@lang('Last Name')</th>
                            <th>@lang('Email Address')</th>
                            <th>@lang('Work Phone')</th>
                            <th>@lang('Mobile')</th>
                            <th>@lang('Designation')</th>
                            <th>@lang('Department')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($custContact)>0)
                        @foreach ($custContact as $data)                            
                        <tr>
                            <td>{{ $data->salutation }}</td>
                            <td>{{ $data->first_name }}</td>
                            <td>{{ $data->last_name }}</td>
                            <td>{{ $data->email_address }}</td>
                            <td>{{ $data->work_phone }}</td>
                            <td>{{ $data->mobile }}</td>
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
    {{--  Contact  --}}
    
    {{--  Document  --}}
    <div class="tab-pane pt-2" id="documents" role="tabpanel" aria-labelledby="documents">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    @if (count($custDoc)>0)
                        @foreach ($custDoc as $doc)
                        <tr>
                            <td>{{ $doc->doc_name }}</td>
                            <td>{{date('d/m/Y', strtotime(@$doc->doc_exp_date))}}</td>
                            <td><a class="btn-sm btn-primary" href="{{asset('public/uploads/cust-suppl/')}}/{{ $doc->doc_file }}" target="_blank">Download</a></td>
                        </tr>  
                        @endforeach                        
                    @endif
                </table>
            </div>
        </div>
    </div>
    {{--  Document  --}}
    
</div>

            </div>

            
            <script>
                function download_outstanding(id){
                    var date = $('#till_date').val();                                                                        
                    var url = $("#base_url").val()+"/payables-outstanding-download/"+id+"/"+date;
                    window.location.href = url;
                }
            </script>
            <div class="col-lg-12 mb-3"><br />
                <style>
                    .card-header {
                        background-color: #b8caff;
                        color: #000000;
                        margin-right: 5px;
                    }
                    .nav-tabs .active{background-color: #4e73df; color: #ffffff;}
                    .tab-pane {
                        background: #ffffff;                        
                    }
                    .nav-tabs{border: none !important;}
                    .card-body{margin-top:10px; }
                    
                </style>

                <ul class="nav nav-tabs">
                    <li><a class="card-header active" data-toggle="tab" href="#tab1">Supplier Outstanding</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab1" class="tab-pane fade in active show">
                        <div class="card-body">
                            <div class="card p-4">
                
                                <div class="accordion" id="accordionExample">
                                @if(count($data_all)>0)
                                <?php $no=1; $all_total=0;   $k=0;?>
                                @foreach($data_all as $data)
                                
                                <?php
                                if(count($data)>0){
                                      $data_adjestment = @App\SysPurchaseReturnAdjestment::select('piv_no',DB::raw('sum(paid_amount) as paid_amount'))->wherein('piv_no',$data->pluck("transaction_no"))->groupby('piv_no')->get();
                      
                                      $data_payment = DB::table('sys_payment as p')->select('pa.bi_doc_no','p.doc_number','pa.bi_amount','p.payment_through','p.payment_date','p.cheque_number','p.cheque_bank_name')
                                      ->join('sys_payment_adjustments as pa','pa.bi_doc_number','p.doc_number')->where('pa.account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->where('p.status',1)->get();
                                      
                                      $data_payment2 = DB::table('sys_journalvoucher as j')->select('pa.bi_doc_no','j.doc_number','pa.bi_amount','j.doc_date')
                                      ->join('sys_payment_adjustments as pa','pa.bi_doc_number','j.doc_number')->where('pa.account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->where('j.status',1)->get();
                                ?>
                                
                              <?php $aname = $accounts->where('id',$data[0]->account_id)->first();
                              $cust_det = @App\SysHelper::get_customer_contact_detail($aname->account_code); ?>
              
                              <script>
                                  function set_total(id,at){
                                      $('#sum_'+id).text(at.toFixed(@json(session('logged_session_data.decimal_point'))));
                                      $('#collapse'+id).css('display','');
                                      $('#account_table'+id).css('display','');
                                  }
                              </script>
              
                              <table id="account_table{{ $aname->id }}" class="table" style="border: solid 1px #e3e6f0; margin-bottom: -1px !important;">
                                  <thead>
                                    <tr>
                                        <th class="border text-center" width="100px"><a href="{{url('get-url-supplier/'.$aname->account_code)}}" target="_blank">{{ $aname->account_code }}</a></th>
                                        <th class="border text-left"><a class="text-left" type="button" data-toggle="collapse" data-target="#collapse{{ $aname->id }}" aria-expanded="true" aria-controls="collapse{{ $aname->id }}">{{ $aname->account_name }} <span style="font-weight: normal; color: #3d3d3d;">{!! $cust_det !!}</span></a>
                                        <a style="display: none;" data-id="{{@$aname->id}}"      id="crmajax" class="btn-badge btn btn-info  py-1 px-2" style="  font-weight: 500;  border: 1px solid transparent;  padding: 0.375rem 0.75rem;  font-size: 10px;  line-height: .7;  border-radius: 2px;cursor: pointer;float:right;" data-toggle="modal" data-target="#ModalTrackComment" title="Click to Fullfill">
                                                  Comments</a>
                                          <a class="text-danger ml-2 float-right" title="Download" onclick="download_outstanding({{ $aname->id }})"><i class="fa fa-download" aria-hidden="true"></i></a></th>
                                        <th class="border text-right" width="100px"><label id="sum_{{ $aname->id }}"></label></th>
                                    </tr>
                                  </thead>
                              </table>
                                
                              <div id="collapse{{ $aname->id }}" class="" data-parent="#accordionExample" style="margin-left: 100px;">
                              <table class="table" style="border: solid 1px #e3e6f0; width:auto;">
                                  <thead>
              
              
                                  <!-- <tr>
                                      <td colspan="10">&nbsp;</td>
                                      <td colspan="2">
                                      <a data-id="{{@$aname->id}}"      id="crmajax" class="btn-badge btn btn-info  py-1 px-2" style="cursor: pointer;" data-toggle="modal" data-target="#ModalTrackComment" title="Click to Fullfill">
                                                  Comments</a>
                                                 
              
                                      </td>
              
                                      </tr> -->
                                    <tr>
                                        <th class="border text-center" width="68px">Doc Date</th>
                                        <th class="border text-center" width="80px">Doc No</th>
                                        <th class="border text-center" width="70px">Deal ID</th>
                                        <th class="border text-center" width="100px">Amount</th>
                                        <th class="border text-center" width="100px">Adjustments</th>
                                        <th class="border text-center" width="100px">Balance</th>
                                        <th class="border text-center" width="100px">Total Balance</th>
                                        <th class="border text-center hidecol_{{ $aname->id }}" width="150px">Receipt Date</th>
                                        <th class="border text-center hidecol_{{ $aname->id }}" width="150px">Doc Number</th>
                                        {{--  <th class="border text-center" width="150px">Sales Person</th>  --}}
                                        {{--  <th class="border text-center" width="150px">Cheque Number</th>
                                        <th class="border text-center" width="150px">Bank Name</th>  --}}
              
                                         <th class="border text-center" width="150px">Payment Terms</th>
                                        <th class="border text-center" width="150px">Due Date</th>
                                        <th class="border text-center" width="150px">Over Due</th>
                            <th class="border text-center" width="150px">0-30</th>
                            <th class="border text-center" width="150px">31-60</th>
                            <th class="border text-center" width="150px">61-90</th>
                            <th class="border text-center" width="150px">>90</th>
                                    </tr>
                                  </thead>
                                  <tbody>
              
                                  <?php
                                       $ats=Array();   
                                       $k=0;
                                       foreach ($data as $dt){
                                          $DueData =  App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no,$dt->transaction_date); 
                                     
                                          // if( $DueData[1] < $overdue && $DueData[1] < $ageing ){
                                          //     $ats[$k]=$dt;
                                          //     $k++;
                                          // }
              
                                          // if($doc_date != null){  
                                          //     if($dt->transaction_date== Date($doc_date))
                                          //     $ats[$k]=$dt;
                                          // }    
              
                                         
              
                                          if($overdue != 999999){    
                                              if(  $DueData[1] < $overdue ){
                                                  $ats[$k]=$dt;
                                                  $k++;
                                              }
                                          }
              
                                          if($ageing != 99999){
                                             // if(  $DueData[1] < $ageing ){
                                                  if($ageing <0 && $DueData[1] <0 ){
                                                      $ats[$k]=$dt;
                                                      $k++;
                                                  }
                                                  if($ageing >=0 && $ageing <31 && $DueData[1] >=0 && $DueData[1] <31 ){
                                                      $ats[$k]=$dt;
                                                      $k++;
                                                  }
                                                  if($ageing >30 && $ageing <61 &&  $DueData[1] >30 && $DueData[1] <61 ){
                                                      $ats[$k]=$dt;
                                                      $k++;
                                                  }
                                                  if($ageing >=60 && $ageing <=90 && $DueData[1] >=60 && $DueData[1] <=90 ){
                                                      $ats[$k]=$dt;
                                                      $k++;
                                                  }    //echo 'xxxxxxxxxxxxxxxxxxxx'.$k;
                                            //  }
                                          }  
              
                                       }
                                       //if($overdue != 999999 ||  $ageing != 99999 || $doc_date != null)
                                       //   $data=$ats;
                                      
                                  ?>
                                      @php $adjustments = 0;
                                      $b=0;
                                      $grand_credit_amount=0; 
                                      $grand_paid=0;
                                      $grand_balance=0;
                                      $grand_total_balance=0;
                                      $gtot1=0;$gtot2=0;$gtot3=0;$gtot4=0;
                                      @endphp
                                      @if (count($data)>0)
                                      @php $sum_b=0; @endphp
                                      @foreach ($data as $dt)
                                      
                                      @php
                                      $adjustments = 0; $receipt_date=''; $doc_number=''; $cheque_number=''; $bank_name=''; $bi_amount=0; $bi_amount2=0; $paid=0;
                                      @endphp
                                      @php
                                          $adjustments = $data_adjestment->where('piv_no',$dt->transaction_no)->max('paid_amount');
                                          $payment = $data_payment->where('bi_doc_no',$dt->transaction_no);
                                          if(count($payment)>0){
                                              foreach($payment as $p){
                                                  $receipt_date .= date('d/m/Y', strtotime($p->payment_date)).',';
                                                  $doc_number .= $p->doc_number.',';
                                                  if ($p->cheque_number != ""){
                                                      $cheque_number .= $p->cheque_number.',';
                                                  }                                
                                                  if ($p->cheque_bank_name != ""){
                                                      $bank_name .= $p->cheque_bank_name.',';
                                                  }
                                                  $bi_amount += $p->bi_amount;
                                              }
                                          }
              
                                          $payment2 = $data_payment2->where('bi_doc_no',$dt->transaction_no);
                                          if(count($payment2)>0){
                                              foreach($payment2 as $p){
                                                  $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
                                                  $doc_number .= $p->doc_number.',';
              
                                                  $bi_amount2 += $p->bi_amount;
                                              }
                                          }
              
                                          $paid += $adjustments+$bi_amount+$bi_amount2;
              
                                          $deal_id="";
                                          $deal_code="";
                                          $sales_person="";
                                          $deal = @App\SysHelper::get_deal_detail_for_payable_outstanding($dt->transaction_no);
                                          if(isset($deal) && $deal != ""){
                                              $deal_id=$deal->id;
                                              $deal_code=$deal->code;
                                              $sales_person=$deal->full_name;
                                          }
               
                                         
                                      @endphp
                                      <?php 
                                      if($dt->credit_amount != $paid){
                                      $grand_credit_amount+=$dt->credit_amount;
                                      $grand_paid+=$paid;
                                      $grand_balance+=$dt->credit_amount-abs($paid);
                                     
                                      }
                                           ?>  
                                       @php $DueData =  @App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no,$dt->transaction_date); @endphp 
                                      
                                       @if($dt->credit_amount != $paid)
                                      
                                      
                                      <tr>
                                          <td class="border text-center">{{ date('d/m/Y', strtotime($dt->transaction_date)) }}</td>
                                          <td class="border text-center"><a href="{{url('get-url-purchase-invoice/'.$dt->transaction_no)}}" target="_blank">{{ $dt->transaction_no }}</a></td>
                                          <td class="border text-center"><a href="{{url('crm-deals/'.$deal_id.'/view')}}" target="_blank">{{ $deal_code }}</a></td>
                                          <td class="border text-center">{{ $dt->credit_amount }}</td>
                                          <td class="border text-center">{{ @App\SysHelper::com_curr_format($paid,2,'.',',') }}</td>
                                          <td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.',',') }} @php $b += $dt->credit_amount-abs($paid); @endphp </td>
                                          <td class="border text-center">{{ @App\SysHelper::com_curr_format($b,2,'.',',') }}</td>
                                          
                                          @php $sum_b += $dt->credit_amount-abs($paid); $all_total += $dt->credit_amount-abs($paid); @endphp
                                          <script>
                                              set_total({{ $aname->id }},{{ $sum_b }});
                                          </script>
              
                                          <td class="border text-center hidecol_{{ $aname->id }}">{{ rtrim($receipt_date, ',') }}</td>
                                          <td class="border text-center hidecol_{{ $aname->id }}">{{ rtrim($doc_number, ',') }}</td>
                                         
              
                                          @php $DueData =  @App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no,$dt->transaction_date); @endphp
              
                                          <td class="border text-center">{{ $DueData[2] }}</td>
                                          <td class="border text-center">{{ $DueData[0] }}</td>
                                          <?php 
                                          if($DueData[1] >0){ ?>
                                          <td class="border text-center" style="color:red">{{ $DueData[1] }}</td>
                                          <?php } else { ?>
              
                                          <td class="border text-center">{{ $DueData[1] }}</td>
                                          <?php }  ?>
              
                                          <?php 
                               if($DueData[3] ==1)	  {
                                  $gtot1+=$dt->debit_amount-abs($paid);
                               }
                               if($DueData[3] ==2)	  {
                                  $gtot2+=$dt->debit_amount-abs($paid);
                               }
                               if($DueData[3] ==3)	  {
                                  $gtot3+=$dt->debit_amount-abs($paid);
                               }
                               if($DueData[3] ==4)	  {
                                  $gtot4+=$dt->debit_amount-abs($paid);
                               }
                                      
              
                               ?>
                                          
              
                                          @if($DueData[3] ==1)	                            
                          <td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.',',') }}</td>
                           @else 	
                                                          <td class="border text-center">&nbsp;</td>
                           @endif
                             @if($DueData[3] ==2)	                            
                          <td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.',',') }}</td>
                           @else 	
                                                          <td class="border text-center">&nbsp;</td>
                           @endif
                 @if($DueData[3] ==3)	                            
                          <td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.',',') }}</td>
                           @else 	
                                                          <td class="border text-center">&nbsp;</td>
                           @endif		   	
                 @if($DueData[3] ==4)	                            
                          <td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.',',') }}</td>
                           @else 	
                                                          <td class="border text-center">&nbsp;</td>
                           @endif
                           
                                      </tr>
                                      @endif
                                          @if(count($payment)==0)
                                          <script>
                                              $('.hidecol_'+{{ $aname->id }}).css('display','none');
                                          </script>
                                          @endif
              
                                      
                                      <?php // } ?>    
                                          
                                      @endforeach
                                      @endif
                                      <tr><td colspan="3"></td>
                                     <td class="border text-center"><b><?php echo   @App\SysHelper::com_curr_format($grand_credit_amount,2,'.',',')    ?> </b></td>
                                     <td class="border text-center"><b><?php echo  @App\SysHelper::com_curr_format($grand_paid,2,'.',',')   ?> </b></td>
                                     <td class="border text-center"><b><?php echo  @App\SysHelper::com_curr_format($grand_balance,2,'.',',')   ?></b> </td>
                                     <td class="border text-center"><b><?php echo  @App\SysHelper::com_curr_format($b,2,'.',',')   ?></b> </td>
              
                                     <td class="border text-center" colspan="3">&nbsp </td>
                                     
                                     <td class="border text-center" ><b><?php echo  @App\SysHelper::com_curr_format($gtot1,2,'.',',')   ?></b> </td>
                                     <td class="border text-center" ><b><?php echo  @App\SysHelper::com_curr_format($gtot2,2,'.',',')   ?></b> </td>
                                     <td class="border text-center" ><b><?php echo  @App\SysHelper::com_curr_format($gtot3,2,'.',',')   ?> </b></td>
                                     <td class="border text-center" ><b><?php echo  @App\SysHelper::com_curr_format($gtot4,2,'.',',')   ?> </b></td>
                                  
                                  </tr>
                                      <?php $tot = 0; $total=0; $total_dr=0; $total_cr=0; ?>    
                                  </tbody>
                                </table>
                                </div>
                                <?php } ?>
              
                                @endforeach
                                <table class="table table-hover" style="border: solid 1px #e3e6f0;">
                                  <thead>
                                      <tr>
                                          <th class="border text-center" width="100px"></th>
                                          <th class="border text-right">Total</th>
                                          <th class="border text-right" width="200px">{{ $all_total }}</th>
                                      </tr>
                                  </thead>
                                </table>
                                @endif
                                </div>
              
              
              
                          </div>
                        </div>
                    </div>
                    
                    
                    
                </div>
            </div>



        <div class="card p-4 mb-3" style="display: none;">
            <div class="d-flex">
                <div class="profile__img mr-4">
                    @if (file_exists(@$custDetails->staff_photo))
                        <img src="{{ asset($custDetails->staff_photo) }}" alt="">
                    @else
                        <img src="{{ asset('/') }}public/uploads/staff/demo/staff.jpg" alt="">
                    @endif
                </div>
                <div class="text__wrap pt-2">
                    <h4 class="font-weight-bold">
                        {{--  1-Green, 2-Orange, 3-Red, 4-Black  --}}
                        <span class="badge badge-danger"
                            @if (@$custDetails->type == 1) style="background: #228c22;" @endif
                            @if (@$custDetails->type == 2) style="background: #FFA500;" @endif
                            @if (@$custDetails->type == 3) style="background: #FF0000;" @endif
                            @if (@$custDetails->type == 4) style="background: #000000;" @endif>
                            @if (isset($custDetails))
                                {{ @$custDetails->name }}
                            @endif
                        </span>

                    </h4>
                    <p class="mb-1 text-muted">Customer Code : <span class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->code }}
                            @endif
                        </span></p>
                    <p class="mb-1 text-muted">Contact Person : <span class="badge badge-danger">
                            @if (isset($custDetails))
                                {{ @$custDetails->contcat_person }}
                            @endif
                        </span></p>
                    <div class="d-sm-flex">
                        <p class="mb-1 pr-3 text-muted">Contact Number: <span class="f-14 text-dark font-weight-semibold">
                                @if (isset($custDetails))
                                    {{ @$custDetails->contcat_number }}
                                @endif
                            </span>, </p>
                        <p class="mb-1 text-muted">Mobile: <span class="f-14 text-dark font-weight-semibold">
                                @if (isset($custDetails))
                                    {{ @$custDetails->mobile }}
                                @endif
                            </span></p>
                    </div>

                    <p class="mb-1 text-muted">Mail : <span class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->email }}
                            @endif
                        </span></p>

                    <p class="mb-1 text-muted">Address: <span class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->address }}
                            @endif
                        </span></p>
                    <p class="mb-1 text-muted">Address 2: <span class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->address2 }}
                            @endif
                        </span></p>
                </div>
            </div>
        </div>

    </div>


    {{--  <section class="sms-breadcrumb mb-20 white-box top-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12" style="text-align: right;">
                    <div class="top-2-text top-2-text-last"><span>{{ $custDetails->salesperson->full_name }}</span><br />Created By</div>
                    <div class="top-2-text"><b>Sundry Debtors <input type="hidden" value="2" name="sundry_creditors">
                        </b><br />Customer Type</div>
                    <div class="top-2-text">
                        <b>{{ $custDetails->code }}</b><br />Customer
                        Code</div>
                </div>
            </div>
        </div>
    </section>  --}}

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection









<?php /*


@extends('backEnd.masterpage')
@section('mainContent')

@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

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

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Supplier Details</h2>
            <span class="page-label">Home - Brand</span>
        </div>
        <div>
            <a href="{{ url('add-supplier') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Supplier</a>
            <a href="{{ url('suppliers') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Supplier List</a>
            <a href="{{ url('supplier-edit/' . @$custDetails->id) }}"><button class="btn btn-primary">Edit Profile</button></a>
            <a href="{{ url()->previous() }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="p-4 card h-100">
                <h2 class="head">Supplier Info</h2>
                <span class="badge badge-danger text-left"
                    @if (@$custDetails->type == 1) style="background: #228c22;" @endif
                    @if (@$custDetails->type == 2) style="background: #FFA500;" @endif
                    @if (@$custDetails->type == 3) style="background: #FF0000;" @endif
                    @if (@$custDetails->type == 4) style="background: #000000;" @endif>
                    @if (isset($custDetails))
                        {{ @$custDetails->name }}
                    @endif
                </span>
                <p class="mb-1 text-muted"><span class="font-semibold">Supplier Code :</span> <span
                        class="f-14 text-dark font-weight-semibold">
                        @if (isset($custDetails))
                            {{ @$custDetails->code }}
                        @endif
                    </span></p>
                <p class="mb-1 text-muted"><span class="font-semibold">Contact Person : </span><span
                        class="badge badge-danger">
                        @if (isset($custDetails))
                            {{ @$custDetails->contcat_person }}
                        @endif
                    </span></p>
                    <p class="mb-1 text-muted"><span class="font-semibold">Contact Number: </span><span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->contcat_number }}
                            @endif
                        </span></p>
                    <p class="mb-1 text-muted"><span class="font-semibold">Mobile: </span><span
                            class="f-14 text-dark font-weight-semibold">
                            @if (isset($custDetails))
                                {{ @$custDetails->mobile }}
                            @endif
                        </span></p>

                <p class="mb-1 text-muted"><span class="font-semibold">Mail : </span><span
                        class="f-14 text-dark font-weight-semibold">
                        @if (isset($custDetails))
                            {{ @$custDetails->email }}
                        @endif
                    </span></p>

                <p class="mb-1 text-muted"><span class="font-semibold">Address: </span><span
                        class="f-14 text-dark font-weight-semibold">
                        @if (isset($custDetails))
                            {{ @$custDetails->address }}
                        @endif
                    </span></p>
                <p class="mb-1 text-muted"><span class="font-semibold">Address 2: </span><span
                        class="f-14 text-dark font-weight-semibold">
                        @if (isset($custDetails))
                            {{ @$custDetails->address2 }}
                        @endif
                    </span></p>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="p-4 card h-100">
                <h2 class="head">Bill To</h2>
                <p class="mb-1 text-muted"><span class="font-semibold">Address: </span><span
                        class="f-14 text-dark font-weight-semibold">
                        @if (isset($custDetails))
                            {{ @$custDetails->address }}
                        @endif
                    </span></p>
                <p class="mb-1 text-muted"><span class="font-semibold">Address 2: </span><span
                        class="f-14 text-dark font-weight-semibold">
                        @if (isset($custDetails))
                            {{ @$custDetails->address2 }}
                        @endif
                    </span></p>
                <hr />
                <h2 class="head">Ship To</h2>
                <p class="mb-1 text-muted"><span class="font-semibold">Address: </span><span
                        class="f-14 text-dark font-weight-semibold">
                        @if (isset($custDetails))
                            {{ @$custDetails->address }}
                        @endif
                    </span></p>
                <p class="mb-1 text-muted"><span class="font-semibold">Address 2: </span><span
                        class="f-14 text-dark font-weight-semibold">
                        @if (isset($custDetails))
                            {{ @$custDetails->address2 }}
                        @endif
                    </span></p>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="p-4 card h-100">
                <h2 class="head">Other Info</h2>
                <div class="card-body p-0">
                    <div class="row">
                        <label class="col-lg-4 text-muted">Credit Limit</label>
                        <div class="col-lg-8">
                            <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                    {{ @$custDetails->credit_limit }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-lg-4 text-muted">Credit Days </label>
                        <div class="col-lg-8 d-flex align-items-center">
                            <span class="font-weight-bold text-gray-800 me-2">: @if (isset($custDetails))
                                    {{ @$custDetails->credit_days }}
                                @endif
                            </span>
                        </div>
                    </div>
                    @if (isset($custDetails) && !empty(@$custDetails->payment_terms))
                        <div class="row">
                            <label class="col-lg-4 text-muted">Payment Terms </label>
                            <div class="col-lg-8 d-flex align-items-center">
                                <span class="font-weight-bold text-gray-800 me-2">: @if (isset($custDetails))
                                        {{ @$custDetails->paymentterms->title }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <label class="col-lg-4 text-muted">Vat Number</label>
                        <div class="col-lg-8">
                            <a href="#" class="font-weight-bold text-gray-800 text-hover-primary">: @if (isset($custDetails))
                                    {{ @$custDetails->vat_number }}
                                @endif
                            </a>
                        </div>
                    </div>
                    @if (isset($custDetails) && !empty(@$custDetails->vat_country))
                        <div class="row">
                            <label class="col-lg-4 text-muted">Vat Country</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                        {{ @$custDetails->vatcountry->name }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif
                    @if (isset($custDetails) && !empty(@$custDetails->vat_state))
                        <div class="row">
                            <label class="col-lg-4 text-muted">Vat State</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                        {{ @$custDetails->vatstate->name }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif
                    @if (isset($custDetails) && !empty(@$custDetails->vat_type))
                        <div class="row">
                            <label class="col-lg-4 text-muted">Vat Type</label>
                            <div class="col-lg-8">
                                <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                        {{ @$custDetails->vattype->type }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <label class="col-lg-4 text-muted">Vat Persentage</label>
                        <div class="col-lg-8">
                            <span class="font-weight-bold text-gray-800">: @if (isset($custDetails))
                                    {{ @$custDetails->vat_percentage }}%
                                @endif
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>    

</div>
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

    
<?php /*

    <section class="sms-breadcrumb mb-20 white-box top-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-4">
                    {{-- <img src="{{ asset($company->company_logo) }}" height="40px"> --}}
                </div>
                <div class="col-8" style="text-align: right;">
                    <div class="top-2-text top-2-text-last"><span>{{ $custDetails->salesperson->full_name }}</span><br />Owner</div>
                    <div class="top-2-text"><b>Sundry Creditors <input type="hidden" value="4"
                                name="sundry_creditors"> </b><br />Supplier Type</div>
                    <div class="top-2-text">
                        <b>{{ $custDetails->code }}</b><br />Supplier
                        Code</div>
                </div>
            </div>
        </div>
    </section>


    <section class="mb-40 student-details">
        @if (session()->has('message-success'))
            <div class="alert alert-success">
                {{ session()->get('message-success') }}
            </div>
        @elseif(session()->has('message-danger'))
            <div class="alert alert-danger">
                {{ session()->get('message-danger') }}
            </div>
        @endif
        <div class="container-fluid p-2">
            <div class="row">
                <div class="col-lg-3">
                    <!-- Start Student Meta Information -->
                    <div class="student-meta-box">
                        <div class="student-meta-top"></div>

                        @if (file_exists(@$custDetails->staff_photo))
                            <img class="student-meta-img img-100" src="{{ asset($custDetails->staff_photo) }}"
                                alt="">
                        @else
                            <img class="student-meta-img img-100"
                                src="{{ asset('/') }}public/uploads/staff/demo/staff.jpg" alt="">
                        @endif
                        <div class="white-box">

                            @if (isset($custDetails) && !empty(@$custDetails->name))
                                <div class="single-meta mt-10">
                                    <div class="d-flex justify-content-between">
                                        <div class="name">
                                            @lang('Supplier Name')
                                        </div>
                                        <div class="value">

                                            @if (isset($custDetails))
                                                {{ @$custDetails->name }}
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (isset($custDetails) && !empty(@$custDetails->code))
                                <div class="single-meta">
                                    <div class="d-flex justify-content-between">
                                        <div class="name">
                                            @lang('Supplier Code')
                                        </div>
                                        <div class="value">
                                            @if (isset($custDetails))
                                                {{ @$custDetails->code }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- End Student Meta Information -->

                </div>

                <!-- Start Student Details -->
                <div class="col-lg-9 staff-details">

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            {{-- <a class="nav-link active" href="#studentProfile" role="tab" data-toggle="tab">@lang('lang.profile')</a> --}}
                        </li>
                        <li class="nav-item edit-button">
                            <a href="{{ url('supplier-edit/' . @$custDetails->id) }}"
                                class="primary-btn small fix-gr-bg">@lang('lang.edit')
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <!-- Start Profile Tab -->
                        <div role="tabpanel" class="tab-pane fade show active" id="studentProfile">
                            <div class="white-box">
                                <h4 class="stu-sub-head">@lang('lang.info')</h4>
                                                                
                                @if (isset($custDetails) && !empty(@$custDetails->contcat_person))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-5">
                                                <div class="">
                                                    @lang('Contcat Person')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-6">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->contcat_person }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->mobile))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Mobile')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->mobile }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->address))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Address')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->address }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->address2))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Address 2')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->address2 }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->contcat_number))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Contact Number')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->contcat_number }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->email))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Email')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->email }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->sales_person_name))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Sales Person Name')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->sales_person_name }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->credit_limit))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Credit Limit')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->credit_limit }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->credit_days))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Credit Days')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->credit_days }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->paymentterms->title))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Payment Terms')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->paymentterms->title }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (isset($custDetails) && !empty(@$custDetails->vat_number))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Vat Number')
                                                </div>
                                            </div>
                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->vat_number }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->vat_country))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Vat Country')
                                                </div>
                                            </div>
                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->vatcountry->name }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->vat_state))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Vat State')
                                                </div>
                                            </div>
                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->vatstate->name }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->vat_type))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Vat Type')
                                                </div>
                                            </div>
                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->vattype->type }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->vat_percentage))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Vat Percentage')
                                                </div>
                                            </div>
                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->vat_percentage }}%
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (isset($custDetails) && !empty(@$custDetails->accountant_name))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Accountant Name')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->accountant_name }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (isset($custDetails) && !empty(@$custDetails->accountant_email))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Accountant Email')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->accountant_email }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($custDetails) && !empty(@$custDetails->accountant_number))
                                    <div class="single-info">
                                        <div class="row">
                                            <div class="col-lg-5 col-md-6">
                                                <div class="">
                                                    @lang('Accountant Number')
                                                </div>
                                            </div>

                                            <div class="col-lg-7 col-md-7">
                                                <div class="">
                                                    @if (isset($custDetails))
                                                        {{ @$custDetails->accountant_number }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- End Other Information Part -->
                            </div>
                        </div>




                    </div>
                </div>
    </section>
*/ ?>
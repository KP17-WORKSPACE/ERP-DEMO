@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <?php try { ?>

        
<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Payable Outstanding
                </h4>
                <div class="purchase-order-content-header-right">
                    <a class="btn btn-light" href="{{url('payment-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Payment
                    </a>
                    {{-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Search
                    </button> --}}
                </div>
            </div>
            
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row gap-rows">
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payables-outstanding', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                            <div class="row">
                                                <div class="col-md-3 mb-20">
                                                    <div class="input-effect">
                                                        <label>@lang('Account')</label>
                                                        <select class="form-control js-example-basic-single" name="account_id[]" id="account_id" multiple>
                                                            <option data-display="Account *" value="0">@lang('Account Name') *</option>
                                                            @foreach ($accounts as $val)
                                                                <option value="{{ @$val->id }}"
                                                                    @if($account_id != 0)
                                                                    @foreach ($account_id as $id)
                                                                        @if ($id == $val->id) selected @endif
                                                                    @endforeach
                                                                    @endif>{{ @$val->account_code }} - {{ @$val->account_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 mb-20">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="form-check-label">
                                                                <label>@lang('As of Date')</label>
                                                                <input class="form-control date-picker" id="till_date" type="text" name="till_date" value="{{ @App\SysHelper::normalizeToDmy($till_date) }}" autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                                       
                            
                                                <div class="col-md-1 mb-2">
                                                    <label for="" class="form-check-label">Doc No</label>
                                                    <input class="form-control" id="transaction_no" type="text" autocomplete="off" name="transaction_no" >
                                                </div>
                                                <div class="col-md-1 mb-2">
                                                    <label for="" class="form-check-label">Deal ID</label>
                                                    <input class="form-control" id="deal_id" type="text" autocomplete="off" name="deal_id" >
                                                </div>
                                                <div class="col-md-1 mb-2">
                                                    <label for="" class="form-check-label">Amount</label>
                                                    <input class="form-control" name="amount" id="amount" />
                                                </div>
        
                                                
                                                <div class="col-md-2 mb-2">
                                                    <label for="" class="form-check-label">Sales Person</label>
                                                    <select class="form-control js-example-basic-single" name="sales_person[]" id="sales_person" multiple>
                                                        <option value="">-Select-</option>
                                                        @foreach ($sales_person_list as $sp)
                                                            <option value="{{ $sp->user_id }}"> {{ $sp->full_name }} </option>                                                    
                                                        @endforeach
                                                    </select>
        
                                                </div>
        
                               
                           
                                                <div class="col-md-1 mb-2">
                                                    <label for="" class="form-check-label">Over Due</label>
                                                    <select class="form-control js-example-basic-single" name="overdue" id="overdue">
                                                        <option value="">-Select-</option>
                                                        <option value="0"> >0 </option>
                                                        <option value="30"> 0-30 </option>
                                                        <option value="60"> 31-60</option>
                                                        <option value="90"> 61-90 </option>
                                                        <option value="90+"> >90 </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-1 mb-2">
                                                    <label for="" class="form-check-label">Ageing</label>
                                                    <select class="form-control js-example-basic-single" name="ageing" id="ageing">
                                                        <option value="">-Select-</option>
                                                        <option value="0">0-30</option>
                                                        <option value="30">31-60</option>
                                                        <option value="60">61-90</option>
                                                        <option value="90+"> >90 </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-1 mt-4" >
                                                    <div class="input-effect">
                                                        <button class="btn btn-light" type="submit">
                                                            <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Search
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
            
                                            {{ Form::close() }}
                                    </div>
                                </div>
                            </div>


            <div class="card mb-3 card-min-height">
                <div class="card-body">
                    <div class="" id="deal-info" role="tabpanel" aria-labelledby="deal-info-tab">
                        <div class="row">
                            <div class="col-12 mb-2" >
            <input type="hidden" id="base_url" value="{{ url('/') }}" />
                                

            
            <script>
    function download_outstanding(id){
        var date = $('#till_date').val();                                                                        
        var url = $("#base_url").val()+"/payables-outstanding-download/"+id+"/"+date;
        window.location.href = url;
    }
</script>


<script>
        $(document).ready(function() {


           id=''
            

            $('.btn-badge').click(function() {
             //   alert('with btn')
                id = $(this).data('id')
             //   alert(id)
                $('#iddetail').val(id)
               // alert(id)
                comment=$('#comment').val()
              //  alert(comment)

              var action = "outstanding_comment_payable";  
              $.ajax({
                    url: action,
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',

                        id_deal: id,
                     //   comment: comment,

                    },

                    success: function(dataResult) {
                        
                        var dataResult = JSON.parse(dataResult);
                     //   dataResult.foreach(ars)
                     document.getElementById("mydiv").innerHTML='';
                     //var vv="<table border='1'><tr><td><h3>Sr.No</h3></td><td><h3>name</h3></td></tr>";
                     vv="<div>";
                     var i=1;
                     dataResult.forEach((re) => {
                        vv+="<div class='notes border py-2 px-3 p-0 mt-3'><p class='mb-0 p-0 m-0'>"+re.comment+"</p></div>";
                            vv+="<p class='text-muted text-end p-0 m-0'>"+re.username+" Created on {{date('d/m/Y H:i:s')}}</p>";
                        i++;

});
vv+="<div>";
                    document.getElementById("mydiv").innerHTML=vv;
                       
                   
                    }
                });


 
               
            });


            $('#btnSubmit1').click(function() {

            var action = "outstanding_comment_save_payable";
            comment=$('#comment').val()
            id_detail=$('#iddetail').val()
            //id = $(this).data('id')
           // alert(id+'    '+comment+'   '+id_detail)
                $.ajax({
                    url: action,
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',

                        id_deal: id,
                        comment: comment,

                    },

                    success: function(dataResult) {
                        
                        var dataResult = JSON.parse(dataResult);
                        $('#message').append("<div class='alert alert-success'><i class='fa fa-check'></i> Note successfully added!</div>").delay(3000).fadeOut(300);  
                        view(id)
                       // alert(dataResult)
                    }
                });
            });

            function view(id){
                var action = "outstanding_comment_payable";  
              $.ajax({
                    url: action,
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',

                        id_deal: id,
                     //   comment: comment,

                    },

                    success: function(dataResult) {
                        
                        var dataResult = JSON.parse(dataResult);
                    
//                      var vv="<table border='1'><tr><td>name</td></tr>";
//                      dataResult.forEach((re) => {
//                         vv+="<tr><td>"+re.comment+"</td></tr>"
                        

// });
//                     vv+="</table>";
vv="<div>";
dataResult.forEach((re) => {
    vv+="<div class='notes border py-2 px-3 p-0 mt-3'><p class='mb-0 p-0 m-0'>"+re.comment+"</p></div>";
     vv+="<p class='text-muted text-end p-0 m-0'>"+re.username+" Created on {{date('d/m/Y H:i:s')}}</p>";
});
vv+="</div>";
//                         vv+="<tr><td>"+re.comment+"</td></tr>"
                        

// });
                    document.getElementById("mydiv").innerHTML=vv;
                    $('#comment').val('');
                    }
                });
            } 
        });
    </script>       


                                    

                                
                                
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

                        $data_payment3 = DB::table('sys_journalvoucher as j')->select('ra.bi_doc_no','j.doc_number','ra.bi_amount','j.doc_date')
                        ->join('sys_receipt_adjustments as ra','ra.bi_doc_number','j.doc_number')->where('ra.account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->where('j.status',1)->get();

                        $data_return = DB::table('sys_purchase_return as r')->select('ra.piv_no','r.doc_number','ra.paid_amount','r.doc_date')
                        ->join('sys_purchase_return_adjestment as ra','ra.pri_no','r.doc_number')->where('r.vendors',$data[0]->account_id)->wherein('pri_no',$data->pluck("transaction_no"))->where('r.status',1)->get();
                  ?>
                  
                <?php $aname = $accounts->where('id',$data[0]->account_id)->first();
                
                $cust_det = @App\SysHelper::get_customer_contact_detail($aname->account_code); ?>

                <script>
                    function set_total(id,at){
                        $('#sum_'+id).text(at.toFixed(@json(session('logged_session_data.decimal_point'))).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                        $('#collapse'+id).css('display','');
                        $('#account_table'+id).css('display','');
                    }

                    function formatAmountToNumber(input) {
    if (!input) return 0;

    let inputStr = String(input).replace(/,/g, '').trim();
    let number = parseFloat(inputStr);

    return isNaN(number) ? 0 : number;
}


function set_total_addmore(id, amount) {
    let totText = $('#sum_' + id).text();
    let currentTotal = formatAmountToNumber(totText);
    let additionalAmount = formatAmountToNumber(amount);
    let newTotal = currentTotal + additionalAmount;
    $('#sum_' + id).text(newTotal.toLocaleString('en-US', { minimumFractionDigits: @json(session('logged_session_data.decimal_point')), maximumFractionDigits: @json(session('logged_session_data.decimal_point')) }));
}
function set_total_lessmore(id, amount) {
    let totText = $('#sum_' + id).text();
    let currentTotal = formatAmountToNumber(totText);
    let additionalAmount = formatAmountToNumber(amount);
    let newTotal = currentTotal - additionalAmount;
    $('#sum_' + id).text(newTotal.toLocaleString('en-US', { minimumFractionDigits: @json(session('logged_session_data.decimal_point')), maximumFractionDigits: @json(session('logged_session_data.decimal_point')) }));
}
function check_total(id, amount) {
    let totText = $('#sum_' + id).text();
    let currentTotal = formatAmountToNumber(totText);
    let additionalAmount = formatAmountToNumber(amount);
    if(currentTotal != additionalAmount){
        $('#sum_' + id).css('color', 'red');
    }

}
                </script>



                <table id="account_table{{ $aname->id }}" class="table main_table" style="border: solid 1px #e3e6f0; margin-bottom: -1px !important; display: none;">
                    <thead>
                      <tr>
                          <th class="border text-start" width="100px"><a href="{{url('get-url-supplier/'.$aname->account_code)}}" target="_blank">{{ $aname->account_code }}</a></th>
                          <th class="border"><a type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $aname->id }}" aria-expanded="true" aria-controls="collapse{{ $aname->id }}">{{ $aname->account_name }} <span style="font-weight: normal; color: #3d3d3d;">{!! $cust_det !!}</span></a>
                          
                          <a data-id="{{@$aname->id}}" href="#" id="crmajax"  data-bs-toggle="modal" data-bs-target="#ModalTrackComment" title="Comments">
                                    <i class="ico icon-outline-notes text-primary"></i></a>
                            <a class="ml-2 float-right" href="#" title="Download" onclick="download_outstanding({{ $aname->id }})" title="Download"><i class="ico icon-outline-download-square text-danger"></i></a>
                        <div style="width: auto; float: right;">
                {{ Form::open(['class' => 'form-horizontal m-0', 'files' => true, 'url' => 'generalledger', 'target' => '_blank', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <input type="hidden" name="account_id[]" value="{{ $aname->id }}" />
                    <input type="hidden" name="from_date" value="{{ date('Y-01-01') }}" />
                    <input type="hidden" name="to_date" value="{{ date('Y-m-d') }}" /> 
                    <button class="btn-light btn" style="float: right; padding: 0 2px;"><i class="ico icon-outline-notebook text-success"></i> Ledger</button>
                {{ Form::close() }}
                </div>

                        </th>
                          <th class="border text-end" width="100px"><label class="main_sum" id="sum_{{ $aname->id }}"></label></th>
                      </tr>
                    </thead>
                </table>

                <div id="collapse{{ $aname->id }}" class="collapse" data-parent="#accordionExample" style="margin-left: 100px;">  {{-- display: none; --}}
                <table class="table sub_table" style="border: solid 1px #e3e6f0; width:auto;">
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
                          <th class="border text-center" width="80px">LPO No</th>                          
                          <th class="border text-center" width="80px">Bill No</th>
                          <th class="border text-center" width="80px">Bill Date</th>
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
                                    }
                                    if($ageing > 90 && $DueData[1] >90 ){
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
                        $gtot1=0;$gtot2=0;$gtot3=0;$gtot4=0;$gtot5=0;
                        @endphp
                        @if (count($data)>0)
                        @php $sum_b=0; @endphp
                        @foreach ($data as $dt)
                        
                        @php
                        $adjustments = 0; $receipt_date=''; $doc_number=''; $cheque_number=''; $bank_name=''; $bi_amount=0; $bi_amount2=0; $bi_amount3=0; $bi_amount4=0; $paid=0;
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
                            $payment3 = $data_payment3->where('bi_doc_no',$dt->transaction_no);
                            if(count($payment3)>0){
                                foreach($payment3 as $p){
                                    $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
                                    $doc_number .= $p->doc_number.',';

                                    $bi_amount3 += $p->bi_amount;
                                }
                            }
                            $payment4 = $data_return->where('piv_no',$dt->transaction_no);
                            if(count($payment4)>0){
                                foreach($payment4 as $p){
                                    $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
                                    $doc_number .= $p->doc_number.',';

                                    $bi_amount4 += $p->paid_amount;
                                }
                            }
                            

                            $paid += ($adjustments+$bi_amount+$bi_amount2) - ($bi_amount3 - $bi_amount4);

                            $deal_id="";
                            $deal_code="";
                            $lpo_no="";
                            $bill_no="";
                            $bill_date="";
                            $sales_person="";
                            $payment_terms="";
                            $duedate="";
                            $deal = @App\SysHelper::get_deal_detail_for_payable_outstanding($dt->transaction_no);
                            $lpono = @App\SysHelper::get_purchase_invoice_details($dt->transaction_no);
                            if(isset($deal) && $deal != ""){
                                $deal_id=$deal->id;
                                $deal_code=$deal->code;
                                $sales_person=$deal->full_name;
                            }
                            
                            if ($dt->transaction_type=="opbinvoice"){
                                if(count($opbinvoice)>0){
                                $lpo_no = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('po_no')->first();
                                $deal_code = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('deal_id')->first();
                                $payment_terms = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('payment_terms')->first();
                                $duedate = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('due_date')->first();
                                $bill_no = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('bill_no')->first();
                                $bill_date = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('bill_date')->first();
                                }
                            }else{
                                if(isset($lpono) && $lpono != ""){
                                    $lpo_no=$lpono->lpo_number;
                                    $bill_no=$lpono->bill_number;
                                    $bill_date=$lpono->bill_date;
                                }
                            }
                           
                        @endphp
                        <?php 
                        if($dt->credit_amount != $paid){
                            $grand_credit_amount+=$dt->credit_amount;
                            $grand_paid+=$paid;
                            $grand_balance+=$dt->credit_amount-abs($paid);
                            //$grand_total_balance+=$b;
                        }                        
                        if(($dt->debit_amount)>0){
                            $grand_credit_amount-=$dt->debit_amount;
                            //$grand_paid+=$paid;
                            //$grand_balance+=$dt->debit_amount-abs($paid);
                            //$grand_total_balance+=$b;
                        }

                        ?>
                        <?php $is_hide=0; 
                        if(str_contains($dt->transaction_no,'PR')){
                        if($dt->debit_amount >= $paid){

                        $is_hide=1;
                        }} ?>

                         @php $DueData =  @App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no,$dt->transaction_date); @endphp 
                        <?php
                         //$DueData =  App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no,$dt->transaction_date); 
                         //if($overdue == null )
                           // $overdue=-999999;
                       //  if( $DueData[1] < $overdue && $DueData[1] < $ageing ){
                         ?>
                         @if(($dt->credit_amount != $paid || ($dt->debit_amount)>0)  && $is_hide == 0)
                        
                        
                        <tr>
                            <td class="border text-center">{{ date('d/m/Y', strtotime($dt->transaction_date)) }}</td>                            
                            <td class="border text-center">
                                @if ($dt->transaction_type=="opbinvoice")
                                {{ $dt->transaction_no }}
                                @else
                                <a href="{{url('get-url-purchase-invoice/'.$dt->transaction_no)}}" target="_blank">{{ $dt->transaction_no }}</a>
                                @endif
                            </td>
                            <td class="border text-center">
                                {{ $lpo_no }}
                            </td>
                            <td class="border text-center">
                                {{ $bill_no }}
                            </td>
                            <td class="border text-center">
                                @if($bill_date !="" && $bill_date !=null)
                                {{ date('d/m/Y', strtotime($bill_date)) }}
                                @endif
                            </td>
                            <td class="border text-center">
                                @if ($dt->transaction_type=="opbinvoice")
                                {{ $deal_code }}
                                @else
                                <a href="{{url('get-url-deal-track/'.$deal_code)}}" target="_blank">{{ $deal_code }}</a>
                                @endif
                            </td>
                            <td class="border text-center">@if(str_contains($dt->transaction_no,'PR')) - {{ @App\SysHelper::com_curr_format($dt->debit_amount,2,'.',',') }} @else  {{ @App\SysHelper::com_curr_format($dt->credit_amount,2,'.',',') }} @endif </td>
                            <td class="border text-center">{{ @App\SysHelper::com_curr_format($paid,2,'.',',') }}</td>
                            <td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.',',') }}
                                
                                @php 
                                if(str_contains($dt->transaction_no,'PR')){
                                    $b -= $dt->debit_amount;
                                } else{ $b += $dt->credit_amount-abs($paid); } @endphp

                                {{--  @php $b += $dt->credit_amount-abs($paid); @endphp  --}}
                            </td>
                            <td class="border text-center">{{ @App\SysHelper::com_curr_format($b,2,'.',',') }}</td>
                            
                            @php $sum_b += $dt->credit_amount-abs($paid); $all_total += $dt->credit_amount-abs($paid); @endphp
                            <input type="hidden" class="inv_e_total" value="{{ $dt->credit_amount-abs($paid) }}" />
                            <script>
                                set_total({{ $aname->id }},{{ $sum_b }});
                            </script>

                            <td class="border text-center hidecol_{{ $aname->id }}">{{ rtrim($receipt_date, ',') }}</td>
                            <td class="border text-center hidecol_{{ $aname->id }}">{{ rtrim($doc_number, ',') }}</td>
                            {{--  <td class="border text-center">{{ $sales_person }}</td>  --}}
                            {{--  <td class="border text-center">{{ rtrim($cheque_number, ',') }}</td>
                            <td class="border text-center">{{ rtrim($bank_name, ',') }}</td>  --}}

                            @php                            
                            if ($dt->transaction_type=="opbinvoice"){
                                $DueData =  @App\SysHelper::get_due_date_invoice_opbinvoice($dt->transaction_no,$duedate,$payment_terms);
                            } else {
                                $DueData =  @App\SysHelper::get_due_date_purchase_invoice($dt->transaction_no,$dt->transaction_date);
                            }                            
                            @endphp
                            

                            <td class="border text-center">{{ $DueData[2] }}</td>
                            <td class="border text-center">{{ $DueData[0] }}</td>
                            <?php 
                            if($DueData[1] >0){ ?>
                            <td class="border text-center" style="color:red">{{ $DueData[1] }}</td>
                            <script>
                                if ($('#sum_{{ $aname->id }}').css('color') === 'red') { // red
                                    $('#sum_{{ $aname->id }}').css('color', 'red');
                                } else {
                                    $('#sum_{{ $aname->id }}').css('color', 'blue');
                                }
                            </script>
                            <?php } else { ?>

                            <td class="border text-center">{{ $DueData[1] }}</td>
                            <?php }  ?>

                            <?php 
                 if($DueData[3] ==1)	  {
                    $gtot1+=$dt->credit_amount-abs($paid);
                 }
                 if($DueData[3] ==2)	  {
                    $gtot2+=$dt->credit_amount-abs($paid);
                 }
                 if($DueData[3] ==3)	  {
                    $gtot3+=$dt->credit_amount-abs($paid);
                 }
                 if($DueData[3] ==4)	  {
                    $gtot4+=$dt->credit_amount-abs($paid);
                 }
                        

                 ?>
                            

            @if($DueData[3] ==1)	                            
			<td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.',',') }}</td>
            <input type="hidden" class="inv_all_0_30" value="{{ $dt->credit_amount-abs($paid) }}" />
			 @else 	
				                            <td class="border text-center">&nbsp;</td>
			 @endif

			   @if($DueData[3] ==2)	                            
			<td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.',',') }}</td>
            <input type="hidden" class="inv_all_31_60" value="{{ $dt->credit_amount-abs($paid) }}" />
			 @else 	
				                            <td class="border text-center">&nbsp;</td>
			 @endif

   @if($DueData[3] ==3)	                            
			<td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.',',') }}</td>
            <input type="hidden" class="inv_all_61_90" value="{{ $dt->credit_amount-abs($paid) }}" />
			 @else 	
				                            <td class="border text-center">&nbsp;</td>
			 @endif	

   @if($DueData[3] ==4)	                            
			<td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->credit_amount-abs($paid),2,'.',',') }}</td>
            <input type="hidden" class="inv_all_90_above" value="{{ $dt->credit_amount-abs($paid) }}" />
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
                        <tr><td colspan="6"></td>
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
                  
                  <?php $unadj_list = $list_of_unadjusted->where('account_id',$aname->id); ?>
                  <?php $unadj_list_jv_to_jv = $list_of_unadjusted_jv_to_jv->where('account_id',$aname->id); ?>
                  
                  @if (count($unadj_list)>0 || count($unadj_list_jv_to_jv)>0)
                  <b>List of Unadjusted balance:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="border">Doc Date</th>
                            <th class="border">Payment No</th>
                            <th class="border text-end">Amount</th>
                            <th class="border">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($unadj_list)>0)
                        @foreach ($unadj_list as $p)
                        <tr>
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            @php
                                $docNumber = $p->doc_number;
                            @endphp
                            @if(Illuminate\Support\Str::contains($docNumber, ['BP', 'CP']))
                                <td class="border">
                                    <a href="{{ url('get-url-payment/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['JV']))
                                <td class="border">
                                    <a href="{{ url('get-url-journalvoucher/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['PR']))
                                <td class="border">
                                    <a href="{{ url('get-url-purchase-return/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @else
                            <td class="border">
                                    {{ $docNumber }}
                                </td>
                            @endif
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->adj_amount,2,'.',',') }}</td>
                            <td class="border">{{ $p->remarks }}</td>
                            <script>
                                set_total_lessmore({{ $aname->id }},{{ $p->amount - $p->adj_amount }})
                            </script>
                        </tr>
                        @endforeach
                        @endif
                        
                        @if (count($unadj_list_jv_to_jv)>0)
                        @foreach ($unadj_list_jv_to_jv as $p)
                        <tr>
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            @php
                                $docNumber = $p->doc_number;
                            @endphp
                            @if(Illuminate\Support\Str::contains($docNumber, ['JV']))
                                <td class="border">
                                    <a href="{{ url('get-url-journalvoucher/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @else
                            <td class="border">
                                    {{ $docNumber }}
                                </td>
                            @endif
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->amount2,2,'.',',') }}</td>
                            <td class="border">{{ $p->remarks }}</td>
                            <script>
                                set_total_lessmore({{ $aname->id }},{{ $p->amount - $p->amount2 }})
                            </script>
                        </tr>
                        @endforeach
                        @endif                       

                    </tbody>
                  </table>
                  @endif

                  <?php $pdc = $list_of_unadjusted_pdc->where('account_id',$aname->id); ?>
                  @if (count($pdc)>0)
                  <b>List of Unadjusted PDC:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="border">Doc Date</th>
                            <th class="border">Payment No</th>
                            <th class="border text-end">Amount</th>
                            <th class="border">Cheque Date</th>
                            <th class="border">Cheque No</th>
                            <th class="border">Payment Date</th>
                            <th class="border">Remarks</th>
                            <th class="border"></th>
                        </tr>
                    </thead>:
                    <tbody>
                        @foreach ($pdc as $p)
                        <tr id="row_pdc_paid_{{ $p->doc_number }}">
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="border"><a href="{{url('get-url-payment/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->adj_amount,2,'.',',') }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="border">{{ $p->cheque_number }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->payment_date)) }}</td>
                            <td class="border">{{ $p->remarks }}</td>
                            <td class="border"><a class="btn-sm btn-danger" id="btn_pdc_received_{{ $p->doc_number }}" onclick="pdc_update('{{ $p->doc_number }}','{{ $p->payment_date }}',2)">Update</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                  @endif
                  
                  <?php $pdc = $list_of_adjusted_pdc->where('account_id',$aname->id); ?>
                  @if (count($pdc)>0)
                  <b>List of PDC:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="border">Doc Date</th>
                            <th class="border">Payment No</th>
                            <th class="border text-end">Amount</th>
                            <th class="border">Cheque Date</th>
                            <th class="border">Cheque No</th>
                            <th class="border">Payment Date</th>
                            <th class="border">Invoice Adjusted</th>
                            <th class="border text-end">Adjusted</th>
                            <th class="border">Remarks</th>
                            <th class="border"></th>
                        </tr>
                    </thead>:
                    <tbody>
                        @foreach ($pdc as $p)
                        <tr id="row_pdc_paid_{{ $p->doc_number }}">
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="border"><a href="{{url('get-url-payment/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount,2,'.',',') }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="border">{{ $p->cheque_number }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->payment_date)) }}</td>
                            <td class="border">
                                <a style="cursor: pointer;" onclick="row_det_fun('{{ $p->doc_number }}','{{ $p->bi_doc_no }}')">{{ $p->bi_doc_no }}</a>
                            </td>
                            <td class="border text-end">
                                {{ @App\SysHelper::com_curr_format(@$p->adj_amount,2,'.',',') }}
                            </td>
                            <td class="border">{{ $p->remarks }}</td>
                            <td class="border"><a class="btn-sm btn-danger" id="btn_pdc_received_{{ $p->doc_number }}" onclick="pdc_update('{{ $p->doc_number }}','{{ $p->payment_date }}',3)">Update</a></td>
                            
                            <script>
                                set_total_addmore({{ $aname->id }},{{ $p->adj_amount }})
                            </script>
                        </tr>
                        <tr style="display: none;" id="row_det_{{ $p->doc_number }}">
                            <td></td>
                            <td colspan="9">
                                    <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;" id="row_det_table_{{ $p->doc_number }}">
                                        <thead>
                                            <tr>
                                                <th class="border">Doc Date</th>
                                                <th class="border">Doc No</th>
                                                <th class="border">LPO No</th>
                                                <th class="border">Deal ID</th>
                                                <th class="border text-end">Amount</th>
                                                <th class="border text-end">Adjustments</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                  @endif

                  </div>
                  <?php } ?>

                  
                  <?php
                    $record = $opb_balance_amount->where('account_id', $aname->id)->first();
                    $opb = $record ? $record->opb_amount : 0;
                    $opb = @App\SysHelper::com_curr_format($opb,2,'.','')
                  ?>
                <script>
                    check_total({{ $aname->id }},{{ $opb }})
                </script>

                  @endforeach
                  
                  <table class="table" style="border: solid 1px #e3e6f0;">
                    <thead>
                        <tr>
                            <th class="border text-center" width="168px">Total</th>
                            <th class="border text-center" width="70px"><label id="lbl_all_sivno_count"></label></th>
                            <th class="border text-center" width="384px"></th>
                            <th class="border text-end" width="85px"><?php /*{{ $all_total }}*/ ?> <label id="lbl_all_total"></label></th>
                            <th class="border text-center" width="338px"></th>
                            <th class="border text-center" width="105px"></th>
                            <th class="border text-center" width="114px"><?php /*{{ $all_0_30 }}*/ ?> <label id="lbl_all_total_0_30"></label></th>
                            <th class="border text-center" width="103px"><?php /*{{ $all_31_60 }}*/ ?> <label id="lbl_all_total_31_60"></label></th>
                            <th class="border text-center" width="103px"><?php /*{{ $all_61_90 }}*/ ?> <label id="lbl_all_total_61_90"></label></th>
                            <th class="border text-center" width="102px"><?php /*{{ $all_90_above }}*/ ?> <label id="lbl_all_total_90_above"></label></th>
                        </tr>
                    </thead>
                  {{-- </table>
                  <table class="table" style="border: solid 1px #e3e6f0;">
                    <thead>
                        <tr>
                            <th class="border text-center" width="100px"></th>
                            <th class="border text-end">Total</th>
                            <th class="border text-end" width="200px">{{ $all_total }}</th>
                        </tr>
                    </thead>
                  </table> --}}
                  @endif
                  </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ************** --}}


        
    
<script>
    $(document).ready(function () {
        let visibleCount = 0;
        let totalInv = 0;
        let totalall_0_30 = 0;
        let totalall_31_60 = 0;
        let totalall_61_90 = 0;
        let totalall_90_above = 0;

        $('label.main_sum').each(function () {
            var value = $(this).text().trim();
            var $mainTable = $(this).closest('.main_table');

            if (!value || value === '0') {
                $mainTable.hide();
            } else {
                $mainTable.show(); // optional if hidden by default
                visibleCount++;

                // Extract ID from main table to locate sub_table
                var mainTableId = $mainTable.attr('id'); // e.g., "account_table23"
                var anameId = mainTableId.replace('account_table', ''); // get "23"

                // Now find the corresponding .sub_table inside the collapse div
                var $subTable = $('#collapse' + anameId).find('.sub_table');

                // Get the .inv_e_total value
                var invValue = $subTable.find('.inv_e_total').val();
                var numericValue = parseFloat(invValue) || 0;
                totalInv += numericValue;
                
                var all_0_30 = $subTable.find('.inv_all_0_30').val();
                var all_0_30 = parseFloat(all_0_30) || 0;
                totalall_0_30 += all_0_30;
                
                var all_31_60 = $subTable.find('.inv_all_31_60').val();
                var all_31_60 = parseFloat(all_31_60) || 0;
                totalall_31_60 += all_31_60;
                
                var all_61_90 = $subTable.find('.inv_all_61_90').val();
                var all_61_90 = parseFloat(all_61_90) || 0;
                totalall_61_90 += all_61_90;
                
                var all_90_above = $subTable.find('.inv_all_90_above').val();
                var all_90_above = parseFloat(all_90_above) || 0;
                totalall_90_above += all_90_above;
            }
        });

        $('#lbl_all_sivno_count').text(visibleCount);
        $('#lbl_all_total').text(formatAmount(totalInv.toFixed(2)));
        $('#lbl_all_total_0_30').text(formatAmount(totalall_0_30.toFixed(2)));
        $('#lbl_all_total_31_60').text(formatAmount(totalall_31_60.toFixed(2)));
        $('#lbl_all_total_61_90').text(formatAmount(totalall_61_90.toFixed(2)));
        $('#lbl_all_total_90_above').text(formatAmount(totalall_90_above.toFixed(2)));
        
    });
</script>






        <script>
            function row_det_fun(id,docs){
                $('#row_det_table_'+id+' tbody').empty();
                var doc = docs.split(',');
                for (var i = 0; i < doc.length; i++) {
                    doc[i] = doc[i].trim();
                    var inv_e_doc_date = $('#inv_e_doc_date_'+doc[i]).val();
                    var inv_e_doc_no = $('#inv_e_doc_no_'+doc[i]).val();
                    var inv_e_lpo_no = $('#inv_e_lpo_no_'+doc[i]).val();
                    var inv_e_deal_code = $('#inv_e_deal_code_'+doc[i]).val();
                    var inv_e_amount = $('#inv_e_amount_'+doc[i]).val();
                    var inv_e_adjustment = $('#inv_e_adjustment_'+doc[i]).val();

                    var htm = "<tr>\
                        <td class='border'>"+inv_e_doc_date+"</td>\
                        <td class='border'>"+inv_e_doc_no+"</td>\
                        <td class='border'>"+inv_e_lpo_no+"</td>\
                        <td class='border'>"+inv_e_deal_code+"</td>\
                        <td class='border text-end'>"+inv_e_amount+"</td>\
                        <td class='border text-end'>"+inv_e_adjustment+"</td>\
                        </tr>"
                        $('#row_det_table_'+id+' tbody').append(htm);

                }
                var row = $('#row_det_'+id);
                if (row.is(':visible')) {
                    row.hide();
                } else {
                    row.show();
                }
            }
        </script>
    
        <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

        
@endsection




<div class="modal side-panel fade" id="ModalTrackComment" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md"> 
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Payment Follow-up Remark</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'outstanding_comment_save','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-track-edit']) }}

            <div class="modal-body">

                <div class="row">                    
                    <div class="col-lg-12" id="mydiv" style="height: auto; max-height: 300px; overflow-y: scroll;">

                    </div>
                </div>
                
                <div class="row">
                    <div id="message"></div>

                    <div class="col-lg-12 mb-12">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <input type="hidden" id="iddetail" name="id_detail">
                                    <!-- <label class="txtlbl">@lang('Internal Note')<span></span></label> -->
                                    <textarea   id="comment" name="comment" class="form-control"  cols="10" rows="3" ></textarea>
                                     <!-- <input class="form-control" width="60" id="comment" type="text" required name="comment"> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" name="btnSubmit1" id="btnSubmit1" >
							<i class="ico icon-outline-bookmark-opened text-success"></i> Add Remark
						</button>
            </div>

            {{ Form::close() }}

        </div>
    </div>
</div>
<!-- Modal Deal Track-->

<a class="btn-sm btn-danger" data-toggle="modal" data-target="#ModalPDCUpdate" id="PDCUpdate" style="display: none;"></a>
<!-- Modal PDC Update -->
<div class="modal fade" id="ModalPDCUpdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">PDC Update</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">                
            <div class="row">
                <div class="col-lg-6 mb-12">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <input type="hidden" id="pdc_payment_doc_no">
                                <label class="txtlbl">@lang('Payment Date')<span></span></label>
                                <input class="form-control" id="pdc_payment_doc_date" type="date" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-12">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Status')<span></span></label>
                                <select class="form-control" id="pdc_payment_status">
                                    <option value="2">Paid & Removed</option>
                                    <option value="1">Paid</option>
                                    <option value="3">Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <input type="hidden" id="pdc_status">
            <button class="btn-small" type="button" id="btnSubmitPDC_close" data-dismiss="modal">Close</button>
            <button type="button" class=" btn-small" id="btnSubmitPDC" onclick="pdc_update_save()">PDC Paid</button>
        </div>
      </div>
    </div>
  </div>
<!-- Modal PDC Update -->

<script>
    function pdc_update(id,dat,status){
        $('#pdc_payment_doc_no').val(id);
        $('#pdc_payment_doc_date').val(dat);
        $('#pdc_status').val(status);
        $('#PDCUpdate').click();
    }

    function pdc_update_save() {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('update-payable-pdc') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id: $('#pdc_payment_doc_no').val(),
                doc_date: $('#pdc_payment_doc_date').val(),
                status: $('#pdc_payment_status').val(),
                pdc_status: $('#pdc_status').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);

                if(dataResult['data']=="SUCCESS"){
                    alert("Updated Successfully!!");
                    var a = $('#pdc_payment_doc_no').val();
                    $('#btn_pdc_payment_'+a).css("background-color", "#f6c23e");
                    $('#btn_pdc_payment_'+a).text("Updated");
                    if($('#pdc_payment_status').val()==2){
                        $('#row_pdc_paid_'+a).css("display", "none");
                    }
                    $('#btnSubmitPDC_close').click();                    
                } else { alert("Error!!"); }

                $("#loading_bg").css("display", "none");
            }
        });
    }

</script>

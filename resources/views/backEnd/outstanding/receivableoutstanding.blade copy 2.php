@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <?php try { ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

  <script>
            // Export visible Receivable Outstanding rows to Excel (match Payable's format)
            $(document).ready(function () {
                $('#exportExcelReceivable').on('click', function () {
                    // Build temporary table
                    var $tbl = $('<table id="receivable_export_table" border="1"><thead><tr>' +
                        '<th>Account Code</th>' +
                        '<th>Customer</th>' +
                        '<th>Doc Date</th>' +
                        '<th>Doc No</th>' +
                        '<th>LPO No</th>' +
                        '<th>Deal ID</th>' +
                        '<th>Amount</th>' +
                        '<th>Adjustments</th>' +
                        '<th>Balance</th>' +
                        '<th>Total Balance</th>' +
                        '<th>Receipt Date</th>' +
                        '<th>Doc Number</th>' +
                        '<th>Sales Person</th>' +
                        '<th>Payment Terms</th>' +
                        '<th>Due Date</th>' +
                        '<th>Over Due</th>' +
                        '<th>0-30</th>' +
                        '<th>31-60</th>' +
                        '<th>61-90</th>' +
                        '<th>>90</th>' +
                        '</tr></thead><tbody></tbody></table>');

                    $('.main_table:visible').each(function () {
                        var mainId = $(this).attr('id');
                        if (!mainId) return;
                        var aid = mainId.replace('account_table', '');
                        // Remove child spans from header links (contact details)
                        var accountCode = $(this).find('th a').first().clone().children().remove().end().text().trim();
                        var customerName = $(this).find('th a').eq(1).clone().children().remove().end().text().trim();
                        var $subRows = $('#collapse' + aid).find('.sub_table tbody tr');

                        $subRows.each(function () {
                            var firstTd = $(this).find('td').first().text().trim();
                            // only include data rows where first cell is a date (dd/mm/yyyy)
                            if (!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(firstTd)) return;

                            var cells = $(this).find('td').map(function () {
                                return $(this).text().trim().replace(/\s+/g, ' ');
                            }).get();

                            var $tr = $('<tr>');
                            $tr.append($('<td>').text(accountCode));
                            $tr.append($('<td>').text(customerName));
                            for (var i = 0; i < cells.length; i++) {
                                $tr.append($('<td>').text(cells[i]));
                            }
                            $tbl.find('tbody').append($tr);
                        });
                    });

                    if ($tbl.find('tbody tr').length === 0) {
                        alert('No data available for export');
                        return;
                    }

                    // Add temporary table to DOM to allow SheetJS to read it
                    $tbl.css('display', 'none');
                    $('body').append($tbl);

                    var workbook = XLSX.utils.table_to_book(document.getElementById('receivable_export_table'), { sheet: 'Receivable Outstanding' });

                    function pad(n){return n<10?('0'+n):n}
                    var d = new Date();
                    var filename = 'receivable_outstanding_' + pad(d.getDate()) + '-' + pad(d.getMonth()+1) + '-' + d.getFullYear() + '.xlsx';

                    XLSX.writeFile(workbook, filename);

                    // cleanup
                    $('#receivable_export_table').remove();
                });
            });
        </script>

    

        
<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    Receivable Outstanding
                </h4>
                <div class="purchase-order-content-header-right">
                    <a class="btn btn-light text-dark" href="{{url('receipt-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Receipt
                    </a>

                    
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                          
                            <li>
                                <a
                                    href="{{ url('customer-ageing-report') }}"class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i> Customer Ageing Report</a>
                            </li>

                            <li>
                                <a
                                    type="button" id="exportExcelReceivable" class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-export text-success title-15 me-2"></i> Export</a>
                            </li>


                            


                        </ul>
                    </div>

                    {{-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Search
                    </button> --}}
                </div>
            </div>

                   
<script>
    function download_outstanding(id){
        var date = $('#till_date').val();    
             // Replace all slashes with hyphens
    date = date.replaceAll('/', '-');                                                    

        var url = $("#base_url").val()+"/receivable-outstanding-download/"+id+"/"+date;
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

              var action = "outstanding_comment";  
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
                    $('#mydiv').empty();
//                      var vv="<table border='1'><tr><td><h3>Sr.No</h3></td><td><h3>name</h3></td></tr>";
//                      var i=1;
//                      dataResult.forEach((re) => {
//                         vv+="<tr><td>"+i+"</td><td>"+re.comment+"</td></tr>"
//                         i++;

// });
//                     vv+="</table>";


                    $("#mydiv").append("<div style='width=100%;'>");
                    var i=1;
                        dataResult.forEach((re) => {
                            var txt = re.comment;

                            let customItems = re.comment;
                            customItems = customItems.split("\n");
                            for (let i = 0; i < customItems.length; i++) {
                            customItems[i] = "<br />" + customItems[i] + "\n";
                            }
                            customItems = customItems.join("");
                            $("#mydiv").append("<p class='m-0'>" + customItems + "</p>");
                            $("#mydiv").append("<p class='text-end m-0'><i>"+re.username+" | On "+new Date(re.created_at).toLocaleDateString('en-US')+" "+new Date(re.created_at).toLocaleTimeString('en-US')+"</i></p><hr class='mt-0'>");
                            i++;
                        });
                    $("#mydiv").append("</div>");
                                        
                    $('#comment').val('');
                       

                    }
                });


 
               
            });


            $('#btnSubmit1').click(function() {

            var action = "outstanding_comment_save";
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
    //                     setTimeout(function() {
    //     $("#message").hide('blind', {}, 500)
    // }, 2000);
                        view(id)
                       // alert(dataResult)
                       
                       

                    }
                    ,
    complete: function () {
        $('#message').modal('hide');
    },
                });
            });


            function view(id){


                var action = "outstanding_comment";  
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
                    $('#mydiv').empty();
                    $("#mydiv").append("<div style='width=100%;'>");
                    var i=1;
                        dataResult.forEach((re) => {
                            var txt = re.comment;

                            let customItems = re.comment;
                            customItems = customItems.split("\n");
                            for (let i = 0; i < customItems.length; i++) {
                            customItems[i] = "<br />" + customItems[i] + "\n";
                            }
                            customItems = customItems.join("");
                            $("#mydiv").append("<p class='m-0'>" + customItems + "</p>");
                            $("#mydiv").append("<p class='text-end m-0'><i>"+re.username+" | On "+new Date(re.created_at).toLocaleDateString('en-US')+" "+new Date(re.created_at).toLocaleTimeString('en-US')+"</i></p><hr class='mt-0'>");
                            i++;
                        });
                    $("#mydiv").append("</div>");

                    $('#comment').val('');

                    }
                });

            } 
        });       
    </script>
            
                    
          
                            <div class="card mb-3">
                                <div class="card-body">

                                        <input type="hidden" id="base_url" value="{{ url('/') }}" />


            @if(Auth::user()->role_id==1 || Auth::user()->role_id==2)
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receivable-outstanding', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                    
                                    {{-- prepare a normalized array of selected account ids --}}
                                    @php
                                        $selectedAccounts = [];
                                        if (is_array($account_id)) {
                                            $selectedAccounts = $account_id;
                                        } elseif ($account_id instanceof \Illuminate\Support\Collection) {
                                            $selectedAccounts = $account_id->toArray();
                                        } elseif ($account_id && $account_id != 0) {
                                            $selectedAccounts = [$account_id];
                                        }

                                        if($is_view_all_cust){
                                           $selectedAccounts = ['view_all_cust'];
                                        
                                        }



                                    @endphp

                                    <div class="row gap-rows">
                                        <div class="col-3 mb-20">
                                            <div class="input-effect">
                                                <label>@lang('Account')</label>
                                                <select class="form-control js-example-basic-single" name="account_id[]" id="account_id" multiple>
                                                    <option value="view_all_cust" @if(in_array('view_all_cust', $selectedAccounts)) selected @endif>@lang('View All Customers')</option>
                                                    @foreach ($accounts_select as $val)
                                                        <option value="{{ @$val->id }}" @if(in_array($val->id, $selectedAccounts)) selected @endif>{{ @$val->account_name }}
                                                            @if (@App\SysHelper::getCompanyCodeSettings()['is_customer_code'])
                                                                ({{   @$val->account_code }})
                                                            @endif
                                                            
                                                            </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-1-5  mb-20">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="form-check-label">
                                                        <label>@lang('As of Date')</label>
                                                        <input class="form-control date-picker" id="till_date" type="text" name="till_date" value="{{ @App\SysHelper::normalizeToDmy($till_date) }}" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                                       
<!--                     
                                        <div class="col-1-5  mb-2">
                                            <label for="" class="form-check-label">Doc No</label>
                                            <input class="form-control" id="transaction_no" type="text" value="{{ @$ctrl_doc_no }}" autocomplete="off" name="transaction_no" >
                                        </div>
                                        <div class="col-1-5  mb-2">
                                            <label for="" class="form-check-label">Deal ID</label>
                                            <input class="form-control" id="deal_id" type="text" value="{{ @$ctrl_deal_id }}" autocomplete="off" name="deal_id" >
                                        </div>
                                        <div class="col-1-5  mb-2">
                                            <label for="" class="form-check-label">Amount</label>
                                            <input class="form-control" name="amount" id="amount" value="{{ @$ctrl_amount }}" />
                                        </div> -->

                                        <div class="col-2  mb-2">
                                            <label for="" class="form-check-label">Sales Person</label>
                                            <select class="form-control js-example-basic-single" name="sales_person[]" id="sales_person" multiple>
                                                <option value="">-Select-</option>
                                                @foreach ($sales_person_list as $sp)
                                                    <option value="{{ $sp->user_id }}" @if(in_array($sp->user_id, (array) @$ctrl_sales_person)) selected @endif> {{ $sp->full_name }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-1  mb-2">
                                            <label for="" class="form-check-label">Over Due</label>
                                            <select class="form-control js-example-basic-single" name="overdue" id="overdue">
                                                <option value="" @if(@$ctrl_overdue == "") selected @endif>-Select-</option>
                                                <option value="0" @if(@$ctrl_overdue == "0") selected @endif> >0 </option>
                                                <option value="30" @if(@$ctrl_overdue == "30") selected @endif> 0-30 </option>
                                                <option value="60" @if(@$ctrl_overdue == "60") selected @endif> 31-60</option>
                                                <option value="90" @if(@$ctrl_overdue == "90") selected @endif> 61-90 </option>
                                                <option value="90+" @if(@$ctrl_overdue == '90+') selected @endif> >90 </option>
                                            </select>
                                        </div>
                                    
                                        <div class="col-1  mb-2">
                                            <label for="" class="form-check-label">Ageing</label>
                                            <select class="form-control js-example-basic-single" name="ageing" id="ageing">
                                                <option value="" @if(@$ctrl_ageing == "") selected @endif>-Select-</option>
                                                <option value="0" @if(@$ctrl_ageing == "0") selected @endif>0-30</option>
                                                <option value="30" @if(@$ctrl_ageing == "30") selected @endif>31-60</option>
                                                <option value="60" @if(@$ctrl_ageing == "60") selected @endif>61-90</option>
                                                <option value="90+" @if(@$ctrl_ageing == '90+') selected @endif> >90 </option>
                                            </select>
                                        </div>
                                        <div class="col-1-5  mb-2">
                                            <label for="" class="form-check-label">List Option</label>
                                            <select class="form-control js-example-basic-single" name="list_option" id="list_option">
                                                <option value="" @if(@$ctrl_list_option == "") selected @endif>Normal</option>
                                                <option value="unadjusted_balance" @if(@$ctrl_list_option == 'unadjusted_balance') selected @endif>Unadjusted Balance</option>
                                                <option value="unmatched_balance" @if(@$ctrl_list_option == 'unmatched_balance') selected @endif>Unmatched Balance</option>
                                                <option value="overdue_balance" @if(@$ctrl_list_option == 'overdue_balance') selected @endif>Overdue Balance</option>
                                                 <option value="pdc" @if(@$ctrl_list_option == 'pdc') selected @endif>PDC</option>
                                                <option value="consolidated" @if(@$ctrl_list_option == 'consolidated') selected @endif>Consolidated</option>
                                                <option value="grn" @if(@$ctrl_list_option == 'grn') selected @endif>GRN</option>
                                            </select>
                                        </div>
                                        <div class="col-1  mb-2">
                                           
                                            <label for="" class="form-check-label">Internal/External</label>
                                            <select class="form-control js-example-basic-single" name="list_in_ex" id="list_in_ex">
                                                <option value="" @if(@$ctrl_intext == "") selected @endif>-Select-</option>
                                                <option value="1" @if(@$ctrl_intext == "1") selected @endif>Internal</option>
                                                <option value="0" @if(@$ctrl_intext == "0") selected @endif>External</option>
                                            </select>
                                        </div>
                                        <div class="col-1 mt-4" >
                                            <button class="btn btn-light" type="submit">
                                                <i class="ico icon-outline-minimalistic-magnifer text-success"></i> Filter
                                            </button>
                                        </div>
                                    </div>
    
                                    {{ Form::close() }}
            @endif

                                </div>
                            </div>

                            
                            <div class="card mb-3">
                                <div class="card-body">

@if(count($data_all)==0)

@if ($first_load)

@else
     <div class="row">
                    <div class="col-md-12 m-2 text-center">
                        <b>No Receivable Outstanding Found!</b>
                    </div>
                </div>
@endif
               
                @endif
                  
            <div class="accordion" id="accordionExample">
                  @if(count($data_all)>0)
                  <?php $no=1; $all_total=0; $all_sivno_count=0; $all_overdue=0; $all_0_30=0; $all_31_60=0; $all_61_90=0; $all_90_above=0; ?>
                  @foreach($data_all as $data)

                  

                  <?php $aname = $accounts->where('id',$data[0]->account_id)->first();                  
                  $cust_det = @App\SysHelper::get_customer_contact_detail($aname->account_code); ?>

                  @if($ctrl_list_option == 'pdc')
                  <?php $pdc_1 = !empty($list_of_unadjusted_pdc) ? $list_of_unadjusted_pdc->where('account_id',$aname->id) : []; ?>
                  <?php $pdc_2 = !empty($list_of_adjusted_pdc) ? $list_of_adjusted_pdc->where('account_id',$aname->id) : []; ?>

         

                  @if(count($pdc_1)>0 || count($pdc_2)>0)
                   
                  @else
                     @continue
                  @endif
                  @endif


                  
                  




                    
                      
             

                  <?php
                  if(count($data)>0){
                    $a1 = clone $data_adjestment_all;
                    $a2 = clone $data_receipt_all;
                    $a3 = clone $data_receipt2_all;
                    $a4 = clone $data_receipt3_all;
                    $a5 = clone $data_return_all;
                    $a6 = clone $data_receipt_opb;

                    $data_adjestment = $a1->wherein('srn_no',$data->pluck("transaction_no"));

                    $data_receipt = $a2->where('account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->get();
                    
                    $data_receipt2 = $a3->where('account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->get();

                    $data_receipt3 = $a4->where('account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->get();
                    
                    $data_receipt6 = $a6->where('account_id',$data[0]->account_id)->wherein('bi_doc_no',$data->pluck("transaction_no"))->get();

                    $data_return = $a5->where('customer',$data[0]->account_id)->wherein('srn_no',$data->pluck("transaction_no"))->get();

                  ?>
                
                  
                  
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
                          <!-- <th class="border text-start" width="100px"><a href="{{url('get-url-customer/'.$aname->account_code)}}" target="_blank">{{ $aname->account_code }}</a></th> -->
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
                <table class="table sub_table data-table" style="border: solid 1px #e3e6f0; width:auto;">
                    <thead>
                        <!-- <tr>
                        <td colspan="10">&nbsp;</td>
                        <td colspan="2">
                        <a data-id="{{@$aname->id}}"      id="crmajax" class="btn-badge btn btn-info  py-1 px-2" style="  font-weight: 500;  border: 1px solid transparent;  padding: 0.375rem 0.75rem;  font-size: 10px;  line-height: .7;  border-radius: 2px;cursor: pointer;" data-toggle="modal" data-target="#ModalTrackComment" title="Click to Fullfill">
                                    Comments</a>
                        </td>

                        </tr> -->
                      <tr>
                          <th class="border text-center" width="68px">Doc Date</th>
                          <th class="border text-center" width="90px">Doc No</th>
                          <th class="border text-center" width="90px">LPO No</th>
                          <th class="border text-center" width="70px">Deal ID</th>
                          <th class="border text-center" width="100px">Amount</th>
                          <th class="border text-center" width="100px">Adjustments</th>
                          <th class="border text-center" width="100px">Balance</th>
                          <th class="border text-center" width="100px">Total Balance</th>
                          <th class="border text-center hidecol_{{ $aname->id }}" width="150px">Receipt Date</th>
                          <th class="border text-center hidecol_{{ $aname->id }}" width="150px">Doc Number</th>
                          <th class="border text-center" width="150px">Sales Person</th>
                          <th class="border text-center" width="150px">Payment Terms</th>
                          <th class="border text-center" width="150px">Due Date</th>
                          <th class="border text-center" width="150px">Over Due</th>
			  <th class="border text-center" width="150px">0-30</th>
			  <th class="border text-center" width="150px">31-60</th>
			  <th class="border text-center" width="150px">61-90</th>
			  <th class="border text-center" width="150px">>90</th>
                          {{--  <th class="border text-center" width="150px">Cheque Number</th>
                          <th class="border text-center" width="150px">Bank Name</th>  --}}
                      </tr>
                    </thead>
                    <tbody>





                    <?php
                         $ats=Array();   
                         $k=0;
                         foreach ($data as $dt){
                            $DueData =  App\SysHelper::get_due_date_sales_invoice($dt->transaction_no,$dt->transaction_date); 
                       
                           

                            if($overdue != 999999 && $ageing != 99999){    
                                if($ageing <0 && $DueData[1] <0 ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=0 && $ageing <31 && $DueData[1] <0  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >30 && $ageing <61 &&  $DueData[1] >=0 && $DueData[1] <31 ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=60 && $ageing <=90 &&  $DueData[1] >30 && $DueData[1] <61  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }  
                                if($ageing >=90 &&   $DueData[1] >60 && $DueData[1] <90  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }  
                                
                                
                            }

                            if($overdue != 999999 && $ageing == 99999){    
                                if(  $DueData[1] < $overdue ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                            }

                            if($ageing != 99999 && $overdue == 999999){
                             
                                if($ageing <0 && $DueData[1] <0 ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=0 && $ageing <31 && $DueData[1] <0  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=30 && $ageing <61 &&  $DueData[1] >=0 && $DueData[1] <31 ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }
                                if($ageing >=60 && $ageing <=90 &&  $DueData[1] >30 && $DueData[1] <61  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }  
                                if($ageing >=90 &&   $DueData[1] >60 && $DueData[1] <90  ){
                                    $ats[$k]=$dt;
                                    $k++;
                                }  
                              
                            }  

                         }
                         //if(  $ageing != 99999 ||  $overdue != 999999 )
                         //   $data=$ats;
                  
                    ?>
                        @php
                            $adjustments = 0;
                            $b=0;
                            $grand_debit_amount=0; 
                        $grand_paid=0;
                        $grand_balance=0;
                        $grand_total_balance=0;
                        $gtot1=0;$gtot2=0;$gtot3=0;$gtot4=0;
                        @endphp
                        
                        @if (count($data)>0)
                        @php $sum_b=0; @endphp
                        @foreach ($data as $dt)
                        @php
                        $adjustments = 0;
                        $receipt_date='';
                        $doc_number='';
                        $cheque_number='';
                        $bank_name='';
                        $bi_amount=0;
                        $bi_amount2=0;
                        $bi_amount3=0;
                        $bi_amount4=0;
                        $bi_amount6=0;
                        $paid=0;
                        @endphp
                        @php
                            $adjustments = $data_adjestment->where('srn_no',$dt->transaction_no)->max('paid_amount');
                            $receipt = $data_receipt->where('bi_doc_no',$dt->transaction_no);
                            if(count($receipt)>0){
                                foreach($receipt as $p){
                                $receipt_date .= date('d/m/Y', strtotime($p->receipt_date)).',';
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
                            
                            $receipt2 = $data_receipt2->where('bi_doc_no',$dt->transaction_no);
                            if(count($receipt2)>0){
                                foreach($receipt2 as $p){
                                $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
                                $doc_number .= $p->doc_number.',';
                                
                                $bi_amount2 += $p->bi_amount;
                                }
                            }
                            
                            $receipt3 = $data_receipt3->where('bi_doc_no',$dt->transaction_no);
                            if(count($receipt3)>0){
                                foreach($receipt3 as $p){
                                $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
                                $doc_number .= $p->doc_number.',';
                                
                                $bi_amount3 += $p->bi_amount;
                                }
                            }

                            $receipt4 = $data_return->where('siv_no',$dt->transaction_no);
                            if(count($receipt4)>0){
                                foreach($receipt4 as $p){
                                $receipt_date .= date('d/m/Y', strtotime($p->doc_date)).',';
                                $doc_number .= $p->doc_number.',';
                                
                                $bi_amount4 += $p->paid_amount;
                                }
                            }

                            $receipt6 = $data_receipt6->where('bi_doc_no',$dt->transaction_no);
                            if(count($receipt6)>0){
                                foreach($receipt6 as $p){
                                $receipt_date .= date('d/m/Y', strtotime($p->receipt_date)).',';
                                $doc_number .= $p->doc_number.',';
                                
                                $bi_amount6 += $p->bi_amount;
                                }
                            }

                            $paid += ($adjustments+$bi_amount+$bi_amount2+$bi_amount6)-($bi_amount3+$bi_amount4);
                            
                            
                            $deal_id="";
                            $deal_code="";
                            $lpo_no="";
                            $sales_person="";
                            $deal_track_id=0;
                            $payment_terms="";
                            $duedate="";
                            //$deal = @App\SysHelper::get_deal_detail_for_receivable_outstanding($dt->transaction_no);
                            $deal = @App\SysHelper::get_deal_track_detail_for_receivable_outstanding($dt->transaction_no);
                            $lpono = @App\SysHelper::get_sales_invoice_details($dt->transaction_no);
                            if(isset($deal) && $deal != ""){
                                $deal_id=$deal->id;
                                $deal_code=$deal->code;
                                $sales_person=$deal->full_name;
                                $deal_track_id=$deal->track_id;
                            }
                            if ($dt->transaction_type=="opbinvoice"){
                                if(count($opbinvoice)>0){
                                $lpo_no = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('po_no')->first();
                                $deal_code = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('deal_id')->first();
                                $payment_terms = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('payment_terms')->first();
                                $duedate = $opbinvoice->where('transaction_no', $dt->transaction_no)->pluck('due_date')->first();
                                }
                            }else{
                                if(isset($lpono) && $lpono != ""){
                                    $lpo_no=$lpono->lpo_number;
                                }
                            }
                        @endphp


                        <?php 
                        if($dt->debit_amount != $paid){
                            $grand_debit_amount+=$dt->debit_amount;
                            $grand_paid+=$paid;
                            $grand_balance+=$dt->debit_amount-abs($paid);
                            //$grand_total_balance+=$b;
                        }
                        if(($dt->credit_amount)>0){
                            //if(!str_contains($dt->transaction_no,'SR')){
                            $grand_debit_amount-=$dt->credit_amount;
                            $grand_paid+=$dt->credit_amount;
                            //}
                            //$grand_paid+=$paid;
                            //$grand_balance+=$dt->debit_amount-abs($paid);
                            //$grand_total_balance+=$b;
                        }
                        
                        ?>  
                        

                        <?php $is_hide=0;  $is_hide2=0; 
                        if(str_contains($dt->transaction_no,'SR')){
                        if($dt->credit_amount >= $paid){

                        $is_hide2=1;
                        }} 
                        
                        if(str_contains($dt->transaction_no,'SI')){
                            if(abs($dt->debit_amount) == abs($paid)){
                                $is_hide2=1;
                            }
                        }

                        ?>

                        
                        

                        @if(((@App\SysHelper::com_curr_format($dt->debit_amount,2,'.','') != @App\SysHelper::com_curr_format($paid,2,'.','')) || (@App\SysHelper::com_curr_format($dt->credit_amount,2,'.',''))>0) && $is_hide2 == 0)
                        
                        <tr>
                            <td class="border text-center">{{ date('d/m/Y', strtotime($dt->transaction_date)) }}<input type="hidden" id="inv_e_doc_date_{{ $dt->transaction_no }}" value="{{ date('d/m/Y', strtotime($dt->transaction_date)) }}" /></td>
                            <td class="border text-center">
                                @if ($dt->transaction_type=="opbinvoice")
                                {{ $dt->transaction_no }}
                                @else
                                <a href="{{url('get-url-sales-invoice/'.$dt->transaction_no)}}" target="_blank">{{ $dt->transaction_no }}</a><input type="hidden" id="inv_e_doc_no_{{ $dt->transaction_no }}" value="{{ $dt->transaction_no }}" /></td>
                                @endif <?php $all_sivno_count++ ?>
                            <td class="border text-center">{{ $lpo_no }}<input type="hidden" id="inv_e_lpo_no_{{ $dt->transaction_no }}" value="{{ $lpo_no }}" /></td>
                            

                            <td class="border text-center">
                                @if ($dt->transaction_type=="opbinvoice")
                                {{ $deal_code }}
                                @else
                                <a href="{{url('crm-deal-track-approval/'.$deal_track_id)}}" target="_blank">{{ $deal_code }}</a><input type="hidden" id="inv_e_deal_code_{{ $dt->transaction_no }}" value="{{ $deal_code }}" /></td>
                                @endif
                            <td class="border text-center">@if(str_contains($dt->transaction_no,'SR')) - {{ @App\SysHelper::com_curr_format($dt->credit_amount,2,'.',',') }}
                                <input type="hidden" id="inv_e_amount_{{ $dt->transaction_no }}" value="{{ @App\SysHelper::com_curr_format($dt->credit_amount,2,'.',',') }}" />
                                @else  {{ @App\SysHelper::com_curr_format($dt->debit_amount,2,'.',',') }} 
                                <input type="hidden" id="inv_e_amount_{{ $dt->transaction_no }}" value="{{ @App\SysHelper::com_curr_format($dt->debit_amount,2,'.',',') }}" />
                                @endif</td>
                            <td class="border text-center">{{ @App\SysHelper::com_curr_format($paid,2,'.',',') }}<input type="hidden" id="inv_e_adjustment_{{ $dt->transaction_no }}" value="{{ @App\SysHelper::com_curr_format($paid,2,'.',',') }}" /></td>
                            <td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->debit_amount-abs($paid),2,'.',',') }}
                                
                                @php 
                                if(str_contains($dt->transaction_no,'SR')){
                                    if($dt->credit_amount >= $paid){
                                    $b -= $dt->credit_amount;
                                    }
                                } else{ $b += $dt->debit_amount-abs($paid); } @endphp
                            
                            </td>
                            <td class="border text-center">{{ @App\SysHelper::com_curr_format($b,2,'.',',') }}</td>

                            @php $sum_b += $dt->debit_amount-abs($paid); $all_total += $dt->debit_amount-abs($paid); @endphp
                            <input type="hidden" class="inv_e_total" value="{{ $dt->debit_amount-abs($paid) }}" />
                            <script>
                                set_total({{ $aname->id }},{{ $sum_b }});
                            </script>

                            
                            <td class="border text-center hidecol_{{ $aname->id }}">{{ rtrim($receipt_date, ',') }} </td>
                            <td class="border text-center hidecol_{{ $aname->id }}"><a href="{{url('get-url-receipt/' . rtrim($doc_number, ','))}}" target="_blank">{{ rtrim($doc_number, ',') }}</a></td>
                            <td class="border text-center">{{ $sales_person }}</td>
                            @php                            
                            if ($dt->transaction_type=="opbinvoice"){
                                $DueData =  @App\SysHelper::get_due_date_invoice_opbinvoice($dt->transaction_no,$duedate,$payment_terms);
                            } else {
                               $DueData =  @App\SysHelper::get_due_date_sales_invoice($dt->transaction_no,$dt->transaction_date);
                            }                            
                            @endphp

                            
                            <td class="border text-center">{{ $DueData[2] }} </td>
                            <td class="border text-center">{{ $DueData[0] }} </td>
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

                            <td class="border text-center">{{ $DueData[1] }} <?php $all_overdue += $DueData[1]; ?></td>
                            <?php }  ?>
                            <?php 
                            if($DueData[3] ==1)	  {
                                $gtot1+=$dt->debit_amount-abs($paid);
                                $all_0_30 += $dt->debit_amount-abs($paid);
                            ?><input type="hidden" class="inv_all_0_30" value="{{ $dt->debit_amount-abs($paid) }}" /><?php
                            }
                            if($DueData[3] ==2)	  {
                                $gtot2+=$dt->debit_amount-abs($paid);
                                $all_31_60 += $dt->debit_amount-abs($paid);                                
                            ?><input type="hidden" class="inv_all_31_60" value="{{ $dt->debit_amount-abs($paid) }}" /><?php
                            }
                            if($DueData[3] ==3)	  {
                                $gtot3+=$dt->debit_amount-abs($paid);
                                $all_61_90 += $dt->debit_amount-abs($paid);
                            ?><input type="hidden" class="inv_all_61_90" value="{{ $dt->debit_amount-abs($paid) }}" /><?php
                            }
                            if($DueData[3] ==4)	  {
                                $gtot4+=$dt->debit_amount-abs($paid);
                                $all_90_above += $dt->debit_amount-abs($paid);
                            ?><input type="hidden" class="inv_all_90_above" value="{{ $dt->debit_amount-abs($paid) }}" /><?php
                            }                                   

                            ?>
                            

                            @if($DueData[3] ==1)                            
                            <td class="border text-center" >{{ @App\SysHelper::com_curr_format($dt->debit_amount-abs($paid),2,'.',',') }}</td>
                            @else 	
                            <td class="border text-center">&nbsp;</td>
                            @endif
                            @if($DueData[3] ==2)	                            
                            <td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->debit_amount-abs($paid),2,'.',',') }}</td>
                            @else 	
                            <td class="border text-center">&nbsp;</td>
                            @endif
                            @if($DueData[3] ==3)	                            
                            <td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->debit_amount-abs($paid),2,'.',',') }}</td>
                            @else 	
                            <td class="border text-center">&nbsp;</td>
                            @endif		   	
                            @if($DueData[3] ==4)	                            
                            <td class="border text-center">{{ @App\SysHelper::com_curr_format($dt->debit_amount-abs($paid),2,'.',',') }}</td>
                            @else 	
                            <td class="border text-center">&nbsp;</td>
                            @endif

                            {{--  <td class="border text-center">{{ rtrim($cheque_number, ',') }}</td>
                            <td class="border text-center">{{ rtrim($bank_name, ',') }}</td>  --}}
                        </tr>
                        @endif

                        @endforeach
                        @endif

                    {{--  @if($dt->debit_amount == $paid || count($receipt)==0)
                    <tr><td colspan="14" class="text-danger text-center">No Ouitstanding Found!</td></tr>
                    @endif  --}}


                    @if(($dt->sum('debit_amount') != $paid || ($dt->sum('credit_amount'))>0)  && $is_hide == 0)
                    <tr><td colspan="4"></td>
                        <td class="border text-center"><b><?php echo   @App\SysHelper::com_curr_format($grand_debit_amount,2,'.',',')    ?> </b></td>
                        <td class="border text-center"><b><?php echo  @App\SysHelper::com_curr_format($grand_paid,2,'.',',')   ?> </b></td>
                        <td class="border text-center"><b><?php echo  @App\SysHelper::com_curr_format($grand_balance,2,'.',',')   ?> </b></td>
                        <td class="border text-center"><b><?php echo  @App\SysHelper::com_curr_format($b,2,'.',',')   ?> </td>
                        <td class="border text-center hidecol_{{ $aname->id }}" width="150px">&nbsp;</td>
                        <td class="border text-center hidecol_{{ $aname->id }}" width="150px">&nbsp;</td>
                        <td class="border text-center" colspan="4">&nbsp </td>
                       
                        <td class="border text-center" ><b><?php echo  @App\SysHelper::com_curr_format($gtot1,2,'.',',')   ?></b> </td>
                        <td class="border text-center" ><b><?php echo  @App\SysHelper::com_curr_format($gtot2,2,'.',',')   ?></b> </td>
                        <td class="border text-center" ><b><?php echo  @App\SysHelper::com_curr_format($gtot3,2,'.',',')   ?> </b></td>
                        <td class="border text-center" ><b><?php echo  @App\SysHelper::com_curr_format($gtot4,2,'.',',')   ?> </b></td>
                    </tr>
                    @else
                    @if($list_option != "show")
                    <script>                        
                        {{--  $('#account_table'+{{ $aname->id }}).css('display','none');  --}}
                    </script>
                    @endif
                    <tr><td colspan="4"></td>
                        <td class="border text-center"><b>0.00</b></td>
                        <td class="border text-center"><b>0.00</b></td>
                        <td class="border text-center"><b>0.00</b></td>
                        <td class="border text-center"><b>0.00</b></td>
                        <td class="border text-center hidecol_{{ $aname->id }}" width="150px">&nbsp;</td>
                        <td class="border text-center hidecol_{{ $aname->id }}" width="150px">&nbsp;</td>
                        <td class="border text-center" colspan="4">&nbsp </td>
                       
                        <td class="border text-center"><b>0.00</b></td>
                        <td class="border text-center"><b>0.00</b></td>
                        <td class="border text-center"><b>0.00</b></td>
                        <td class="border text-center"><b>0.00</b></td>
                    </tr>
                    @endif
                    @if(count($receipt)==0)
                    <script>
                        $('.hidecol_'+{{ $aname->id }}).css('display','none');
                    </script>
                    @else
                    <script>
                        $('.hidecol_'+{{ $aname->id }}).css('display','');
                    </script>
                    @endif
                    
                    <?php $tot = 0; $total=0; $total_dr=0; $total_cr=0; ?>
    
                    </tbody>
                  </table>


                  <?php $unadj_list = !empty($list_of_unadjusted) ? $list_of_unadjusted->where('account_id',$aname->id) : []; ?>

                  <?php $unadj_list_jv_to_jv = !empty($list_of_unadjusted_jv_to_jv) ? $list_of_unadjusted_jv_to_jv->where('account_id',$aname->id) : []; ?>

                  @if (count($unadj_list)>0 || count($unadj_list_jv_to_jv)>0)
                 
                  @if ($list_option == "pdc")
                    <script>
                        $('#collapse'+{{ $aname->id }}).addClass('show');
                    </script>                      
                  @endif
                  <b>List of Unadjusted balance:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="border">Doc Date</th>
                            <th class="border">Receipt No</th>
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
                            @if(Illuminate\Support\Str::contains($docNumber, ['BR', 'CR']))
                                <td class="border">
                                    <a href="{{ url('get-url-receipt/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['JV']))
                                <td class="border">
                                    <a href="{{ url('get-url-journalvoucher/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
                                </td>
                            @elseif(Illuminate\Support\Str::contains($docNumber, ['SR']))
                                <td class="border">
                                    <a href="{{ url('get-url-sales-return/' . $docNumber) }}" target="_blank">{{ $docNumber }}</a>
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

                  <?php $pdc = !empty($list_of_unadjusted_pdc) ? $list_of_unadjusted_pdc->where('account_id',$aname->id) : []; ?>
                 
                  @if (count($pdc)>0)
                  <b>List of Unadjusted PDC:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="border">Doc Date</th>
                            <th class="border">Receipt No</th>
                            <th class="border text-end">Amount</th>
                            <th class="border">Cheque Date</th>
                            <th class="border">Cheque No</th>
                            <th class="border">Receipt Date</th>
                            <th class="border">Remarks</th>
                            <th class="border"></th>
                        </tr>
                    </thead>:
                    <tbody>
                        @foreach ($pdc as $p)
                        <tr id="row_pdc_received_{{ $p->doc_number }}">
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="border"><a href="{{url('get-url-receipt/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount - $p->adj_amount,2,'.',',') }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="border">{{ $p->cheque_number }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->receipt_date)) }}</td>
                            <td class="border">{{ $p->remarks }}</td>
                            <td class="border"><a class="btn-sm btn-light" id="btn_pdc_received_{{ $p->doc_number }}" onclick="pdc_update('{{ $p->doc_number }}','{{ $p->receipt_date }}',2)">Update</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
                  @endif

                  <?php $pdc = !empty($list_of_adjusted_pdc) ? $list_of_adjusted_pdc->where('account_id',$aname->id) : []; ?>
                  @if (count($pdc)>0)
                  <b>List of PDC:-</b>
                  <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                    <thead>
                        <tr>
                            <th class="border">Doc Date</th>
                            <th class="border">Receipt No</th>
                            <th class="border text-end">Amount</th>
                            <th class="border">Cheque Date</th>
                            <th class="border">Cheque No</th>
                            <th class="border">Receipt Date</th>
                            <th class="border">Invoice Adjusted</th>
                            <th class="border text-end">Adjusted</th>
                            <th class="border">Remarks</th>
                            <th class="border"></th>
                        </tr>
                    </thead>:
                    <tbody>
                        @foreach ($pdc as $p)
                        <tr id="row_pdc_received_{{ $p->doc_number }}">
                            <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                            <td class="border"><a href="{{url('get-url-receipt/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                            <td class="border text-end">{{ @App\SysHelper::com_curr_format($p->amount,2,'.',',') }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->cheque_date)) }}</td>
                            <td class="border">{{ $p->cheque_number }}</td>
                            <td class="border">{{ date('d/m/Y', strtotime($p->receipt_date)) }}</td>
                            <td class="border">
                                <a style="cursor: pointer;" onclick="row_det_fun('{{ $p->doc_number }}','{{ $p->bi_doc_no }}')">{{ $p->bi_doc_no }}</a>
                            </td>
                            <td class="border text-end">
                                {{ @App\SysHelper::com_curr_format(@$p->adj_amount,2,'.',',') }}
                            </td>
                            <td class="border">{{ $p->remarks }}</td>
                            <td class="border"><a class="btn-sm btn-light" id="btn_pdc_received_{{ $p->doc_number }}" onclick="pdc_update('{{ $p->doc_number }}','{{ $p->receipt_date }}',3)">Update</a></td>
                            
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
                  @if(@$ctrl_list_option == 'consolidated')
                    <script>
                        // expand all accordion panels when consolidated view selected
                        $('#accordionExample .collapse').addClass('show');
                    </script>
                  @endif
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
                  </table>
                  @endif
                </div>
                                    {{-- ******************** --}}

                                </div>
                            </div>

            

        </div>
    </div>
</div>


    
<script>
    $(document).ready(function () {
        let visibleCount = 0;
        let totalInv = 0;
        let totalall_0_30 = 0;
        let totalall_31_60 = 0;
        let totalall_61_90 = 0;
        let totalall_90_above = 0;

        var ctrlOption = '{{ @$ctrl_list_option }}';
        $('label.main_sum').each(function () {
            var value = $(this).text().trim();
            var $mainTable = $(this).closest('.main_table');
            var color = $(this).css('color');

            // if unadjusted_balance filter is active, require at least one unadjusted row
            if (ctrlOption === 'unadjusted_balance') {
                // look for the unadjusted list section inside this account's collapse
                var acctId = $mainTable.attr('id').replace('account_table', '');
                var $section = $('#collapse' + acctId);
                var hasUnadj = $section.find('> table').filter(function() {
                    // tables with "Unadjusted balance" header are above
                    return $(this).prev('b').text().trim().toLowerCase().startsWith('list of unadjusted');
                }).find('tbody tr').length > 0;
                if (!hasUnadj) {
                    $mainTable.hide();
                    return;
                }
            }

            // if unmatched_balance filter is active, only keep red totals
            if (ctrlOption === 'unmatched_balance') {
                if (color.indexOf('255, 0, 0') === -1) { // red rgb
                    $mainTable.hide();
                    return;
                }
            }

            // if overdue_balance filter is active, only keep blue totals
            if (ctrlOption === 'overdue_balance') {
                // computed color likely in rgb format; match on blue component
                if (color.indexOf('0, 0, 255') === -1) {
                    $mainTable.hide();
                    return; // skip to next iteration
                }
            }

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



<!-- Modal Payment Follow-up Remark -->
<div class="modal side-panel fade" id="ModalTrackComment" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md"> 
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel" style="font-size: 14px">Payment Follow-up Remark</h4>
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
<!-- Modal Payment Follow-up Remark -->

<a class="btn-sm btn-danger" data-toggle="modal" data-target="#ModalPDCUpdate" id="PDCUpdate" style="display: none;"></a>
<!-- Modal PDC Update -->
<div class="modal fade" id="ModalPDCUpdate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">PDC Update 1</h5>
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
                                <input type="hidden" id="pdc_receipt_doc_no">
                                <label class="txtlbl">@lang('Receipt Date')<span></span></label>
                                <input class="form-control" id="pdc_receipt_doc_date" type="date" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-12">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Status')<span></span></label>
                                <select class="form-control js-example-basic-single" id="pdc_receipt_status">
                                    <option value="2">Received & Removed</option>
                                    <option value="1">Received</option>
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
            <button type="button" class=" btn-small" id="btnSubmitPDC" onclick="pdc_update_save()">PDC Received</button>
        </div>
      </div>
    </div>
  </div>
<!-- Modal PDC Update -->

<script>
    function pdc_update(id,dat,status) {
        $('#pdc_receipt_doc_no').val(id);
        $('#pdc_receipt_doc_date').val(dat);
        $('#pdc_status').val(status);
        $('#PDCUpdate').click();
    }

    function pdc_update_save() {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('update-receivable-pdc') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id: $('#pdc_receipt_doc_no').val(),
                doc_date: $('#pdc_receipt_doc_date').val(),
                status: $('#pdc_receipt_status').val(),
                pdc_status: $('#pdc_status').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);

                if(dataResult['data']=="SUCCESS"){
                    alert("Updated Successfully!!");
                    var a = $('#pdc_receipt_doc_no').val();
                    $('#btn_pdc_received_'+a).css("background-color", "#f6c23e");
                    $('#btn_pdc_received_'+a).text("Updated");
                    if($('#pdc_receipt_status').val()==2){
                        $('#row_pdc_received_'+a).css("display", "none");
                    }
                    $('#btnSubmitPDC_close').click();                    
                } else { alert("Error!!"); }

                $("#loading_bg").css("display", "none");
            }
        });
    }


    

</script>

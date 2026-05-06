<div id="ledger-content">
  <div class="card mb-0" >
                <div class="card-body m-0 pb-2 pt-1">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'generalledger', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'ledger-form']) }}
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                <div class="row  align-items-center justify-content-start">
                                     @foreach ($ctrl_account_id as $id)
                            <input type="hidden" name="account_id[]" value="{{ $id }}" />
                            @endforeach

                                    <div class="col-2 mb-20 mt-2" style="margin-left:20px"> 
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <label class="text-dark mb-0">@lang('From')</label>
                                            </div>
                                            <div class="col p-0">
                                                @php
                                                    $value = date('01/01/Y');
                                                    if ($from_date != "") { $value = \Carbon\Carbon::parse($from_date)->format('d/m/Y'); }
                                                @endphp
                                                <input class="form-control date-picker" id="from_date" type="text" 
                                                    name="from_date" value="{{ $value }}" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                   <div class="col-md-2 mb-20 mt-2" style="margin-left:20px">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <label class="text-dark mb-0">@lang('To')</label>
                                            </div>
                                            <div class="col p-0">
                                                @php
                                                    $value = date('d/m/Y');
                                                    if ($to_date != "") { $value = \Carbon\Carbon::parse($to_date)->format('d/m/Y'); }
                                                @endphp
                                                <input class="form-control date-picker" id="to_date" type="text"
                                                    name="to_date" value="{{ $value }}" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                        <div class="col-md-2 mb-20 mt-2" style="margin-left:20px"> 
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <label class="text-dark mb-0">Filter By</label>
                                            </div>
                                            <div class="col p-0">
                                                <select class="form-control" name="filter_by" id="filter_by" onchange="set_filter2()">
                        <option value="" >-Select-</option>
                        <option value="this_month" @if($filter_by=="this_month") selected @endif>This Month</option>
                        <option value="today" @if($filter_by=="today") selected @endif>Today</option>
                        <option value="this_week" @if($filter_by=="this_week") selected @endif>This Week</option>
                        <option value="last_week" @if($filter_by=="last_week") selected @endif>Last Week</option>
                        <option value="last_month" @if($filter_by=="last_month") selected @endif>Last Month</option>
                        <option value="this_quarter" @if($filter_by=="this_quarter") selected @endif>This Quarter</option>
                        <option value="pre_quarter" @if($filter_by=="pre_quarter") selected @endif>Previous Quarter</option>
                        <option value="this_year" @if($filter_by=="this_year") selected @endif @if($filter_by=="") selected @endif>This Year</option>
                        <option value="last_year" @if($filter_by=="last_year") selected @endif>Last Year</option>
                    </select>
                                            </div>
                                        </div>
                                    </div>

                                                  
                <script>
                    function set_filter(){
                        if($('#from_date').val()!="" || $('#to_date').val() != "")
                        {
                            $('#filter_by').val('')
                        }
                    }
                    function set_filter2(){
                        if($('#filter_by').val()!="")
                        {
                            $('#from_date').val('');
                            $('#to_date').val('');
                        }
                    }
                </script>

                               


                                    <div class="col-auto mt-2">
                                        <button type="button" class="btn btn-light" id="btnSubmit">
                                            <i class="ico icon-outline-magnifer" style="font-size:16px"></i> Search
                                        </button>
                                    </div>

                                </div>

                                {{ Form::close() }}
                </div>
            </div>
            
            <div class="card mb-0">
                <div class="card-body p-0">
                    
                    @if (isset($data_all))
            @for($j=0; $j<count($data_all); $j++)
            
            <?php $data = $data_all[$j]; ?>
            <?php $is_merge = App\SysHelper::ledger_merge_account($account_id_all[$j]); ?>
            <?php $is_merge_notvat = App\SysHelper::ledger_merge_account_notvat($account_id_all[$j]); ?>
            <?php $is_merge_vat = App\SysHelper::ledger_merge_account_vat($account_id_all[$j]); ?>
                
            <div class="table-responsive">
            <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                <thead>
                    {{-- <tr>
                        <th colspan="7" class=" text-left" width="500px" style="color: #000000; font-size: 13px; padding-bottom: 0px;">{{ $account_name[$j]["account_code"] }} - {{ $account_name[$j]["account_name"] }}
                            <hr style="height: 1px; margin: 3px 0px 0px 0px; background: #499258;"/>
                        </th>
                    </tr> --}}
                  <tr>
                      <th class=" text-center" style="width:10%">Date</th>
                      <th class=" text-center" style="width:10%">Doc No</th>
                      <th class=" text-center" style="width:20%">Account</th>
                      <th class=" text-end" style="width:10%">Debit</th>
                      <th class=" text-end" style="width:10%">Credit</th>
                      <th class=" text-end" style="width:10%">Balance</th>
                      <th class=" text-center" style="width:30%">Narration</th>
                  </tr>
                </thead>
                <tbody>
                  
                    <?php $tot = 0; $total=0; $total_dr=0; $total_cr=0; $deb=0; $cre=0; ?>
                  @if (isset($data))
                      @for ($i=0; $i < count($data); $i++)
                      <?php $ac_id = strtolower($data[$i]["account_name"]); ?>

                      @if(($ac_id == "purchase" || $ac_id == "sales" || $ac_id == "purchase return" || $ac_id == "sales return") && $is_merge == true)
                      
                      <?php
                        $date = date('d/m/Y', strtotime($data[$i]["transaction_date"]));
                        $trn_no = $data[$i]["transaction_no"];
                        $acc_name = $data[$i]["account_name"];
                        $rem = $data[$i]["remarks"];
                        $deb = $data[$i]["debit_amount"];
                        $cre = $data[$i]["credit_amount"];
                        
                        if($i+1 < count($data)){
                        if(str_contains(strtolower($data[$i+1]["account_name"]), 'vat on sales') && $ac_id == "sales"){
                        $i++;
                        $deb = $data[$i]["debit_amount"];
                        $cre = $data[$i]["credit_amount"];
                        }
                        else if(str_contains(strtolower($data[$i+1]["account_name"]), 'vat on sales') && $ac_id == "sales return"){
                        $i++;
                        $deb += $data[$i]["debit_amount"];
                        $cre += $data[$i]["credit_amount"];
                        }
                        else if(str_contains(strtolower($data[$i+1]["account_name"]), 'vat on purchase')){
                        $i++;
                        $deb += $data[$i]["debit_amount"];
                        $cre += $data[$i]["credit_amount"];
                        }
                        else if(str_contains(strtolower($data[$i+1]["account_name"]), 'vat')){
                        $i++;
                        $deb = $data[$i]["debit_amount"];
                        $cre = $data[$i]["credit_amount"];
                        }
                        }
                      ?>

                      <tr>
                        <td class=" text-center">{{ $date }}</td>
                        <td class="text-center">
                            @if(substr($trn_no, 0, 2)=="JV")
                                <a href="{{url('get-url-journalvoucher/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>
                            @elseif(substr($trn_no, 0, 2)=="CR" || substr($trn_no, 0, 2)=="BR")
                                <a href="{{url('get-url-receipt/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>
                            @elseif(substr($trn_no, 0, 2)=="CP" || substr($trn_no, 0, 2)=="BP")
                                <a href="{{url('get-url-payment/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>                                
                            @elseif(substr($trn_no, 0, 2)=="PI")
                                <a href="{{url('get-url-purchase-invoice/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>
                            @elseif(substr($trn_no, 0, 2)=="PR")
                                <a href="{{url('get-url-purchase-return/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>
                            @elseif(substr($trn_no, 0, 2)=="SR")
                                <a href="{{url('get-url-sales-return/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>
                            @elseif(in_array(substr($trn_no, 0, 2),$sales_code))
                                <a href="{{url('get-url-sales-invoice/' . $trn_no)}}" target="_blank">{{ $trn_no }}</a>
                            @else
                                {{ $trn_no }}
                            @endif
                        </td>
                        <td class="">{{ $acc_name }}</td>
                        <td class=" text-end ">{{ @App\SysHelper::com_curr_format($deb,2,'.',',') }} @php $total_dr += $deb; @endphp </td>
                        <td class="text-end ">{{ @App\SysHelper::com_curr_format($cre,2,'.',',') }} @php $total_cr += $cre; @endphp </td>
                        <td class=" text-end ">
                        @if ($group == 1 || $group == 3)
                            @php $tot += ($deb); @endphp
                            @php $tot -= ($cre); @endphp
                        @endif
                        @if ($group == 2 || $group == 4 || $group == 5)
                            @php $tot += ($cre); @endphp
                            @php $tot -= ($deb); @endphp
                        @endif
                        @if ($group == 0)
                            @php $tot += ($cre); @endphp
                            @php $tot -= ($deb); @endphp
                        @endif
                            {{ @App\SysHelper::com_curr_format(($tot), 2, '.', ',') }}
                        </td>
                        <td class="">{{ $rem }}</td>
                      </tr>
                      
                      @else

                      <tr>
                        <td class="text-center">{{ date('d/m/Y', strtotime($data[$i]["transaction_date"])) }}</td>
                        <td class="text-center">
                            @if(substr($data[$i]["transaction_no"], 0, 2)=="JV")
                                <a href="{{url('get-url-journalvoucher/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>
                            @elseif(substr($data[$i]["transaction_no"], 0, 2)=="CR" || substr($data[$i]["transaction_no"], 0, 2)=="BR")
                                <a href="{{url('get-url-receipt/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>
                            @elseif(substr($data[$i]["transaction_no"], 0, 2)=="CP" || substr($data[$i]["transaction_no"], 0, 2)=="BP")
                                <a href="{{url('get-url-payment/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>                                
                            @elseif(substr($data[$i]["transaction_no"], 0, 2)=="PI")
                                <a href="{{url('get-url-purchase-invoice/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>
                            @elseif(substr($data[$i]["transaction_no"], 0, 2)=="PR")
                                <a href="{{url('get-url-purchase-return/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>
                            @elseif(substr($data[$i]["transaction_no"], 0, 2)=="SR")
                                <a href="{{url('get-url-sales-return/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>
                            @elseif(in_array(substr($data[$i]["transaction_no"], 0, 2),$sales_code))
                                <a href="{{url('get-url-sales-invoice/' . $data[$i]["transaction_no"])}}" target="_blank">{{ $data[$i]["transaction_no"] }}</a>
                            @else
                                {{ $data[$i]["transaction_no"] }}
                            @endif
                        </td>
                        <td class="text-start">{{ $data[$i]["account_name"] }}</td>
                        <td class="text-end">{{ @App\SysHelper::com_curr_format($data[$i]["debit_amount"], 2, '.', ',') }} @php $total_dr += $data[$i]["debit_amount"]; @endphp </td>
                        <td class="text-end ">{{ @App\SysHelper::com_curr_format($data[$i]["credit_amount"], 2, '.', ',') }} @php $total_cr += $data[$i]["credit_amount"]; @endphp </td>
                        <td class="text-end ">
                        @if ($group == 1 || $group == 3)
                            @php $tot += ($data[$i]["debit_amount"]); @endphp
                            @php $tot -= ($data[$i]["credit_amount"]); @endphp
                        @endif
                        @if ($group == 2 || $group == 4 || $group == 5)
                            @php $tot += ($data[$i]["credit_amount"]); @endphp
                            @php $tot -= ($data[$i]["debit_amount"]); @endphp
                        @endif
                        @if ($group == 0)
                            @php $tot += ($data[$i]["credit_amount"]); @endphp
                            @php $tot -= ($data[$i]["debit_amount"]); @endphp
                        @endif
                            {{ @App\SysHelper::com_curr_format($tot, 2, '.', ',') }}
                        </td>
                        <td class="text-start">{{ $data[$i]["remarks"] }}</td>
                      </tr>
                      @endif

                      @endfor
                  @endif
                  <tr>
                                            <td colspan="11">&nbsp;</td>
                                        </tr>
                  
                </tbody>
                <thead>
                  <tr>
                      <th class=""></th>
                      <th class=""></th>
                      <th class=""></th>
                      <th class=" text-center "></th>
                      <th class=" text-center "></th>
                      @if ($group == 1 || $group == 3)
                        <th class="text-end ">{{ @App\SysHelper::com_curr_format(($total_dr - $total_cr), 2, '.', ',') }}</th>
                      @endif
                      @if ($group == 2 || $group == 4 || $group == 5)
                        <th class="text-end ">{{ @App\SysHelper::com_curr_format(($total_cr - $total_dr), 2, '.', ',') }}</th>
                      @endif
                      <th class=""></th>
                     
                  </tr>
                </thead>
              </table>
              </div>
              @endfor
              @endif

              @if(count($data_all)==0)
              <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                <thead>
                  <tr>
                      <th class=" text-center" width="100px">Date</th>
                      <th class=" text-center" width="120px">Doc No</th>
                      <th class=" text-start" width="250px">Account</th>
                      <th class=" text-end" width="100px">Debit</th>
                      <th class=" text-end" width="100px">Credit</th>
                      <th class=" text-end" width="100px">Balance</th>
                      <th class=" text-start" width="50px">Narration</th>
                  </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center"> No Record Found! </td>
                    </tr>
                </tbody>
              </table>

              @endif
                    
                </div>
            </div>


            



</div>  
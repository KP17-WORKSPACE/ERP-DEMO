
                    @if (isset($data_all))
            @for($j=0; $j<count($data_all); $j++)
            
            <?php $data = $data_all[$j]; ?>
            <?php $is_merge = App\SysHelper::ledger_merge_account($account_id_all[$j]); ?>
            <?php $is_merge_notvat = App\SysHelper::ledger_merge_account_notvat($account_id_all[$j]); ?>
            <?php $is_merge_vat = App\SysHelper::ledger_merge_account_vat($account_id_all[$j]); ?>
            
            <table class="table table-hover" id="long-list" style=": solid 1px #e3e6f0;">
                <thead>
                    {{-- <tr>
                        <th colspan="7" class=" text-left" width="500px" style="color: #000000; font-size: 13px; padding-bottom: 0px;">{{ $account_name[$j]["account_code"] }} - {{ $account_name[$j]["account_name"] }}
                            <hr style="height: 1px; margin: 3px 0px 0px 0px; background: #499258;"/>
                        </th>
                    </tr> --}}
                  <tr>
                      <th class=" text-center" width="100px">Date</th>
                      <th class=" text-center" width="120px">Doc No</th>
                      <th class=" text-start" width="250px">Account</th>
                      <th class=" text-end" width="100px">Debit</th>
                      <th class=" text-end" width="100px">Credit</th>
                      <th class=" text-end" width="100px">Balance</th>
                      <th class=" text-start">Narration</th>
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
                        <td class="text-start">{{ $acc_name }}</td>
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
              @endfor
              @endif

              @if(count($data_all)==0)
              <table class="table table-hover" id="long-list" style=": solid 1px #e3e6f0;">
                <thead>
                  <tr>
                      <th class=" text-center" width="100px">Date</th>
                      <th class=" text-center" width="120px">Doc No</th>
                      <th class=" text-start" width="250px">Account</th>
                      <th class=" text-end" width="100px">Debit</th>
                      <th class=" text-end" width="100px">Credit</th>
                      <th class=" text-end" width="100px">Balance</th>
                      <th class=" text-start">Narration</th>
                  </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center"><br /><br /> No Data Found! <br /><br /></td>
                    </tr>
                </tbody>
              </table>

              @endif
               
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
                        Customer Ageing Report
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light text-dark" href="{{ url('receipt-add') }}">
                            <i class="ico icon-outline-add-square text-success"></i> Add Receipt
                        </a>


                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ico icon-outline-hamburger-menu"></i>
                            </button>
                            <ul class="dropdown-menu" style="">

                                <li>
                                    <a href="{{ url('customer-ageing-report') }}"
                                        class="dropdown-item d-flex align-items-center"><i
                                            class="ico icon-outline-document-text text-success title-15 me-2"></i> Customer
                                        Ageing Report</a>
                                </li>


                            </ul>
                        </div>


                    </div>
                </div>





                <div class="card mb-3">


                </div>
            </div>


            <div class="card mb-3">
                <div class="card-body">

                    @if (count($data_all) == 0)
                        <div class="row">
                            <div class="col-md-12 m-2 text-center">
                                <b>No Customer Ageing Data Found!</b>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive mb-4 mt-4">
                            <table id="long-list" class="table table-hover" style="border: solid 1px #e3e6f0;">
                                <thead>
                                    <tr style="background: #eeeeee; color: #000000;">
                                        <th class="border text-start" width="300px">Customer Name</th>
                                        <th class="border text-end" width="120px">Total Balance</th>
                                        <th class="border text-end" width="120px">0-30</th>
                                        <th class="border text-end" width="120px">31-60</th>
                                        <th class="border text-end" width="120px">61-90</th>
                                        <th class="border text-end" width="120px">>90</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grand_total_balance = 0;
                                        $grand_total_0_30 = 0;
                                        $grand_total_31_60 = 0;
                                        $grand_total_61_90 = 0;
                                        $grand_total_90_above = 0;
                                        $total_customers = 0;
                                    @endphp

                                    @if (count($data_all) > 0)
                                        @foreach ($data_all as $data)
                                            <?php
                                            if(count($data)>0){
                                                $aname = $accounts->where('id', $data[0]->account_id)->first();
                                                
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
                                                
                                                // Initialize customer totals
                                                $customer_total_balance = 0;
                                                $customer_total_0_30 = 0;
                                                $customer_total_31_60 = 0;
                                                $customer_total_61_90 = 0;
                                                $customer_total_90_above = 0;
                                                
                                                // Process each transaction for this customer
                                                foreach ($data as $dt) {
                                                    $paid = 0;
                                                    
                                                    // Calculate adjustments
                                                    $adjustments = $data_adjestment->where('srn_no', $dt->transaction_no)->max('paid_amount');
                                                    $paid += $adjustments;
                                                    
                                                    // Calculate receipts
                                                    $bi_amount = $data_receipt->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
                                                    $paid += $bi_amount;
                                                    
                                                    $bi_amount2 = $data_receipt2->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
                                                    $paid += $bi_amount2;
                                                    
                                                    $bi_amount6 = $data_receipt6->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
                                                    $paid += $bi_amount6;
                                                    
                                                    // Calculate returns (subtract)
                                                    $bi_amount3 = $data_receipt3->where('bi_doc_no', $dt->transaction_no)->sum('bi_amount');
                                                    $bi_amount4 = $data_return->where('siv_no', $dt->transaction_no)->sum('paid_amount');
                                                    $paid -= ($bi_amount3 + $bi_amount4);
                                                    
                                                    // Calculate balance
                                                    $balance = $dt->debit_amount - abs($paid);
                                                    
                                                    // Skip if fully paid or SR entries
                                                    if ($balance <= 0.01 || str_contains($dt->transaction_no, 'SR')) {
                                                        continue;
                                                    }
                                                    
                                                    // Get ageing bucket first
                                                    $DueData = @App\SysHelper::get_due_date_sales_invoice($dt->transaction_no, $dt->transaction_date);
                                                    $ageing_bucket = isset($DueData[3]) ? $DueData[3] : 0;
                                                    
                                                    // Only add to totals if we have a valid ageing bucket
                                                    if($ageing_bucket >= 1 && $ageing_bucket <= 4) {
                                                        $customer_total_balance += $balance;
                                                        
                                                        // Add to appropriate ageing bucket
                                                        if($ageing_bucket == 1) {
                                                            $customer_total_0_30 += $balance;
                                                        } elseif($ageing_bucket == 2) {
                                                            $customer_total_31_60 += $balance;
                                                        } elseif($ageing_bucket == 3) {
                                                            $customer_total_61_90 += $balance;
                                                        } elseif($ageing_bucket == 4) {
                                                            $customer_total_90_above += $balance;
                                                        }
                                                    }
                                                }
                                                
                                                // Only show customer if they have outstanding balance
                                                if($customer_total_balance > 0.01) {
                                                    $grand_total_balance += $customer_total_balance;
                                                    $grand_total_0_30 += $customer_total_0_30;
                                                    $grand_total_31_60 += $customer_total_31_60;
                                                    $grand_total_61_90 += $customer_total_61_90;
                                                    $grand_total_90_above += $customer_total_90_above;
                                                    $total_customers++;
                                            ?>
                                            <tr>
                                                <td style="cursor: pointer;"
                                                onclick="window.open('{{ url('get-url-customer/' . $aname->account_code) }}', '_blank')" class="border text-start">{{ $aname->account_code }} -
                                                    {{ $aname->account_name }}</td>
                                                <td class="border text-end">
                                                    {{ @App\SysHelper::com_curr_format($customer_total_balance, 2, '.', ',') }}
                                                </td>
                                                <td class="border text-end">
                                                    {{ @App\SysHelper::com_curr_format($customer_total_0_30, 2, '.', ',') }}
                                                </td>
                                                <td class="border text-end">
                                                    {{ @App\SysHelper::com_curr_format($customer_total_31_60, 2, '.', ',') }}
                                                </td>
                                                <td class="border text-end">
                                                    {{ @App\SysHelper::com_curr_format($customer_total_61_90, 2, '.', ',') }}
                                                </td>
                                                <td class="border text-end">
                                                    {{ @App\SysHelper::com_curr_format($customer_total_90_above, 2, '.', ',') }}
                                                </td>
                                            </tr>
                                            <?php
                                                }
                                            }
                                            ?>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr style="background: #f8f9fa; font-weight: bold;">
                                        <td class="border text-start">Grand Total ({{ $total_customers }} Records)</td>
                                        <td class="border text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_balance, 2, '.', ',') }}</td>
                                        <td class="border text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_0_30, 2, '.', ',') }}</td>
                                        <td class="border text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_31_60, 2, '.', ',') }}</td>
                                        <td class="border text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_61_90, 2, '.', ',') }}</td>
                                        <td class="border text-end">
                                            {{ @App\SysHelper::com_curr_format($grand_total_90_above, 2, '.', ',') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

<?php }catch (\Exception $e) { ?> {{ $e }}
<?php  } ?>
@endsection

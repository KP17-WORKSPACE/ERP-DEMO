<!-- Modal Adjustment-->
    <div class="modal fade" id="ModalAdjustment" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Unadjusted List</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-update-adjustment', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="border">Doc Date</th>
                                        <th class="border">Receipt No</th>
                                        <th class="border">Account Name</th>
                                        <th class="border text-right">Amount</th>
                                        <th class="border text-right">Adjusement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($adjested_list) > 0)
                                    @foreach ($adjested_list as $p)
                                    <tr>
                                         <td class="border">{{ date('d/m/Y', strtotime($p->bi_doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-receipt/' . $p->bi_doc_number)}}" target="_blank">{{ $p->bi_doc_number }}</a></td>
                                        <td class="border">{{ $p->account_name }}</td>
                                        <td class="border text-right">{{ $p->bi_cheque_amount }}</td>
                                        <td class="border text-right"><input type="text" name="set_amt[]" id="set_amt_{{ $p->bi_doc_number }}" class="form-control text-right" id="" name="" value="{{ $p->bi_paid }}" onclick="set_adjust('{{ $p->bi_cheque_amount }}','{{ $p->bi_doc_number }}')" />
                                            <input type="hidden" name="receiptno[]" value="{{ @$p->bi_doc_number }}"/>
                                            <input type="hidden" name="set_amt_act[]" value="{{ @$p->bi_cheque_amount }}"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    @if(count($unadjested_list) > 0)
                                    @foreach ($unadjested_list as $p)
                                    <tr>
                                         <td class="border">{{ date('d/m/Y', strtotime($p->bi_doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-receipt/' . $p->bi_doc_number)}}" target="_blank">{{ $p->bi_doc_number }}</a></td>
                                        <td class="border">{{ $p->main_account_id }}</td>
                                        <td class="border text-right">{{ $p->rec_balance }}</td>
                                        <td class="border text-right"><input type="text" name="set_amt[]" id="set_amt_{{ $p->bi_doc_number }}" class="form-control text-right" id="" name="" onclick="set_adjust('{{ $p->rec_balance }}','{{ $p->bi_doc_number }}')" />
                                            <input type="hidden" name="receiptno[]" value="{{ @$p->bi_doc_number }}"/>
                                            <input type="hidden" name="set_amt_act[]" value="{{ @$p->rec_balance }}"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    @if(count($unadjested_list2) > 0)
                                    @foreach ($unadjested_list2 as $p)
                                    <tr>                                        
                                        <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-receipt/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                                        <td class="border">{{ $p->main_account_name }}</td>
                                        <td class="border text-right">{{ @$p->amount }}</td>
                                        <td class="border text-right"><input type="text" name="set_amt[]" id="set_amt_{{ $p->doc_number }}" class="form-control text-right" id="" name="" onclick="set_adjust('{{ $p->amount }}','{{ $p->doc_number }}')" />
                                            <input type="hidden" name="receiptno[]" value="{{ @$p->doc_number }}"/>
                                            <input type="hidden" name="set_amt_act[]" value="{{ @$p->amount }}"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                    @if(count($pdc_list) > 0)
                                    @foreach ($pdc_list as $p)
                                    <tr id="row_pdc_received_{{ $p->doc_number }}">
                                        <td class="border">{{ date('d/m/Y', strtotime($p->doc_date)) }}</td>
                                        <td class="border"><a href="{{url('get-url-receipt/' . $p->doc_number)}}" target="_blank">{{ $p->doc_number }}</a></td>
                                        <td class="border">{{ $p->account->account_name }}</td>
                                        <td class="border text-right">{{ $p->credit_amount }}</td>
                                        <td class="border text-right"><input type="text" name="set_amt[]" id="set_amt_{{ $p->doc_number }}" class="form-control text-right" id="" name="" onclick="set_adjust('{{ $p->credit_amount }}','{{ $p->doc_number }}')" />
                                            <input type="hidden" name="receiptno[]" value="{{ @$p->doc_number }}"/>
                                            <input type="hidden" name="set_amt_act[]" value="{{ @$p->credit_amount }}"/>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="adj_cus_id" value="{{ @$edit_si->customer }}"/>
                    <input type="hidden" name="adj_siv_id" value="{{ @$edit_si->id }}"/>
                    <input type="hidden" name="adj_siv_no" value="{{ @$edit_si->doc_number }}"/>
                    <input type="hidden" name="adj_siv_date" value="{{ @$edit_si->doc_date }}"/>
                    <input type="hidden" name="adj_siv_amount" value="{{ $adjusted_amt }}"/>
                    <input type="hidden" name="adj_siv_amount_actual" value="{{ $adjusted_amt_actual }}"/>
                    <input type="hidden" name="adj_siv_amount_adjusted" value="0"/>
                    <button class="btn btn-success" type="submit" >Adjust</button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Adjustment-->
<script>
function set_adjust(amt,id) {
    let maxAdjustable = parseFloat($("input[name='adj_siv_amount']").val());
    let currentAdjusted = 0;

    // Sum up all currently adjusted values
    $("input[id^='set_amt_']").each(function () {
        let val = parseFloat($(this).val());
        if (!isNaN(val)) {
            currentAdjusted += val;
        }
    });

    let remaining = maxAdjustable - currentAdjusted;

    if (remaining <= 0) {
        alert("No more amount left to adjust.");
        return;
    }

    // Check how much is available for this line
    let adjustAmount = parseFloat(amt);
    if (adjustAmount > remaining) {
        adjustAmount = remaining;
    }

    $('#set_amt_' + id).val(adjustAmount);

    // Optional: update hidden adjusted total
    $("input[name='adj_siv_amount_adjusted']").val(currentAdjusted + adjustAmount);
}
</script>
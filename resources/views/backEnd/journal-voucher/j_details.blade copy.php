    <?php try { ?>

        

            



    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            Journal Voucher ({{  $editData->doc_number }})
        </h4>
        <div class="purchase-order-content-header-right">

            <a class="btn btn-light text-dark" href="{{url('journalvoucher/'.$editData->id.'/edit')}}">
                <i class="ico icon-outline-add-square text-success"></i> Edit
            </a>

            <a class="btn btn-light text-dark" href="{{url('journalvoucher-add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>
            
             {{-- <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><button class="dropdown-item"><i class="ico icon-outline-document-medicine text-success"></i> Save & Download</button></li>
                </ul>
            </div> --}}
        </div>
    </div>
    
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row gap-rows">
                                        <div class="col-2">
                                            <label class="form-label">Doc Number</label>
                                            <div class="form-group">                                                
                                                <input class="form-control" type="text" id="doc_number" name="doc_number" readonly
                                            value="{{ $editData->doc_number }}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Doc Date</label>
                                            <div class="form-group">
                                           @php
                                                

                                                if (isset($editData) && !empty($editData->doc_date)) {
                                                    // Convert database Y-m-d into d/m/Y
                                                    $value = Carbon\Carbon::parse($editData->doc_date)->format('d/m/Y');
                                                } elseif (!empty(old('doc_date'))) {
                                                    // old() value might already be in d/m/Y if from form input
                                                    $value = old('doc_date');
                                                } else {
                                                    // Default today in d/m/Y
                                                    $value = Carbon\Carbon::now()->format('d/m/Y');
                                                }
                                            @endphp

                                        <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off"
                                            name="doc_date" value="{{ @$value }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Currency</label>
                                            <div class="form-group">
                                             <select
                                class="form-control"
                                name="currency" id="currency" readonly>
                                @foreach ($currency as $value)
                                    <option value="{{ @$value->id }}"
                                        {{ isset($editData) ? (!empty(@$editData->currency) ? (@$editData->currency == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->code }}
                                    </option>
                                @endforeach
                            </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Created By:</label>
                                            <div class="form-group">
                                              <input
                                    class="form-control"
                                    type="text" name="createdby" autocomplete="off" id="created_by"
                                    value="{{ isset($editData) ? (!empty(@$editData->created_by) ? @$editData->createdby->full_name : old('created_by')) : Auth::user()->full_name }}"
                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label">Narration</label>
                                            <div class="form-group">
                                                <input
                                    class="form-control"
                                    type="text" name="narration" autocomplete="off"
                                    value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}"
                                    id="narration"  readonly>
                                    
                        <input type="hidden" name="deal_id" id="deal_id" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="tab-wrap mb-3">
                                <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                               <div class="table-container" style="border: solid 1px #d9d9d9;">
                                <table class="table table-hover form-item-table" id="myTable">
                                    <thead>                                                            
                                        <tr>
                                            <th class="resizable text-center" width="50px">@lang('No')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="450px">@lang('Account Name') <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#addproductModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="150px">@lang('Debit')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="150px">@lang('Credit')<div class="resizer"></div></th>
                                            <th class="resizable text-center">@lang('Narration')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="150px">@lang('Deal Id')<div class="resizer"></div></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                    $setroid=8;
                                    if(isset($editDataList))
                                    {
                                        if(count($editDataList)>0)
                                        {
                                            $setroid=count($editDataList)+1;
                                        }
                                    }
                                    ?>
                                    @for ($roid= 1;  $roid < $setroid ; $roid++)
                                    <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $roid }}"  readonly/></td>
                                            <td class="noborder">
                                                <select class="form-control" name="account_id[]" readonly>
                                                    <option value="{{ @$editDataList[$roid-1]->account_id }}">{{ @$editDataList[$roid-1]->accounts->account_code }} - {{ @$editDataList[$roid-1]->accounts->account_name }}</option>
                                                </select>
                                            </td> 
                                            <td>
                                                <input class="form-control text-end" type="number" name="amount_dr[]" autocomplete="off" value="{{ @App\SysHelper::com_curr_format(@$editDataList[$roid-1]->debit_amount,2,'.','') }}" onchange="update_totals()" readonly>
                                            </td>
                                            <td>
                                                <input class="form-control text-end" type="number" name="amount_cr[]" autocomplete="off" value="{{ @App\SysHelper::com_curr_format(@$editDataList[$roid-1]->credit_amount,2,'.','') }}" onchange="update_totals()" readonly>
                                            </td>
                                            <td><input type="text" class="form-control" name="remarks[]" value="{{ @$editDataList[$roid-1]->remarks }}" readonly></td>
                                            <td><input type="text" class="form-control" name="dealid[]" value="{{ @$editDataList[$roid-1]->dealid }}"></td>
                                        </tr>
                                    @endfor


                                        {{-- <tr>
                                            <td><input type="text" class="form-control" name="sort_id[]" value="{{ $roid }}"  readonly/></td>
                                            <td class="noborder">
                                                <select class="form-control" name="account_id[]" readonly>
                                                <option value=""></option>
                                            </select>
                                            </td> 
                                            <td>                                                                    
                                                <input class="form-control text-end" type="number" name="amount_dr[]" autocomplete="off" onchange="update_totals()" readonly>
                                            </td>
                                            <td>
                                                <input class="form-control text-end" type="number" name="amount_cr[]" autocomplete="off" onchange="update_totals()" readonly>
                                            </td>
                                            <td><input type="text" class="form-control" name="remarks[]" readonly></td>
                                            <td><input type="text" class="form-control" name="dealid[]" readonly></td>
                                        </tr> --}}
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" scope="col" >Total</th>
                                            <th class="text-end"><label id="dr_total" >0</label></th>
                                            <th class="text-end"><label id="cr_total" >0</label></th>
                                            <th colspan="2" class="text-end" scope="col" ></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div id="contextMenu">
                                    <button type="button" id="addRow">Add Row</button>
                                    <button type="button" id="deleteRow">Delete Row</button>
                                </div>
                            </div>
                                </div>
                            </div>




<script>
   update_totals();

function update_totals() {
    let total_amount_dr = 0;
    let total_amount_cr = 0;

    const decimal_point = @json(session('logged_session_data.decimal_point'));

    $('#myTable tbody tr').each(function () {
        const $row = $(this);
        total_amount_dr += parseFloat($row.find('input[name="amount_dr[]"]').val()) || 0;
        total_amount_cr += parseFloat($row.find('input[name="amount_cr[]"]').val()) || 0;
    });

    $('#dr_total').text(total_amount_dr.toFixed(decimal_point));
    $('#cr_total').text(total_amount_cr.toFixed(decimal_point));
}
</script>


<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
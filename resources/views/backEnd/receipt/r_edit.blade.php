    <?php try { ?>

        
         @if(isset($editData))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receipt-update/'. @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'receipt-create-form']) }}
            @else
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receipt-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'receipt-create-form']) }}
            @endif

            <input type="hidden" id="bankreceipt_process_id" name="process_id" value="{{Auth::user()->id . date("YmdHis")}}">
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{date('Y-m-d')}}">
            <input type="hidden" id="edit_doc_number" name="edit_doc_number" value="{{ $editData->doc_number }}" />
            <input type="hidden" id="receipt_id" name="receipt_id" value="{{ $editData->id }}">
            <div id="receiptAttachmentHiddenInputs"></div>

            
        <?php
            $invno_cash=@App\SysHelper::get_new_code('sys_receipt','CR','doc_number');
            $invno_bank=@App\SysHelper::get_new_code('sys_receipt','BR','doc_number');
        
            if($editData->mode == 1){
                $invno_cash = $editData->doc_number;
            }else{
                $invno_bank = $editData->doc_number;
                
            }
   
        ?>



    <div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
        <h4 class="purchase-order-content-header-left">
             Edit ({{$editData->doc_number}})
        </h4>
        <div class="purchase-order-content-header-right">
           
            <a class="btn btn-light text-dark" href="{{url('receipt-add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>            
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-square text-warning"></i> Update
            </button>

             <div class="dropdown me-2">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a class="dropdown-item" href="#" id="receiptAttachmentsDropdownBtn"><i class="ico icon-outline-paperclip text-success"></i> Attachments</a></li>
                </ul>
            </div>
        </div>
    </div>
    
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row gap-rows">
                                        <div class="col-2">
                                            <label class="form-label">Mode</label>
                                            <div class="form-group">                                                
                                                <input type="hidden" name="actual_mode" id="actual_mode" value="{{ $editData->mode }}">
                                                <select class="form-control" name="mode" id="mode" required>
                                                    <option value="1" @if($editData->mode == 1) selected @endif>Cash</option>
                                                    <option value="2" @if($editData->mode == 2) selected @endif>Bank</option>
                                                </select>
                                                <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                                <input type="hidden" name="new_invno_cash" id="new_invno_cash" value="{{ $invno_cash }}">
                                                <input type="hidden" name="new_invno_bank" id="new_invno_bank" value="{{ $invno_bank }}">
                                            </div>
                                        </div>
                          
                                        <div class="col-2">
                                            <label class="form-label">Doc Number</label>
                                            <div class="form-group">
                                            <input type="hidden" id="cash_doc_number" value="{{ $editData->doc_number }}" />
                                            <input type="hidden" id="bank_doc_number" value="{{ $editData->doc_number }}" />

                                                <input class="form-control"  @if($editData->mode == 1) style="display: block" @else style="display: none" @endif  type="text" id="doc_number_cash" name="doc_number" value="{{ $invno_cash }}" readonly>
                                                <input class="form-control"  @if($editData->mode == 2) style="display: block" @else style="display: none"  @endif  type="text" id="doc_number_bank" name="doc_number" value="{{ $invno_bank }}" readonly>
                                           
                                            {{-- <input class="form-control" type="text" id="doc_number" name="doc_number" value="{{ $editData->doc_number }}"> --}}
                                            </div>
                                        </div>
                                          <script>
                                  $(document).ready(function () {
                                $('#mode').on('change', function() {
                                    
                                    var mode = $('#mode').val();
                                    if(mode == 1){
                                        $('#receipt_mode_cash').prop('required', true);
                                        $('#receipt_mode_bank').prop('required', false);
                                        $('#receipt_mode_cash').css("display", "block");
                                        $('#receipt_mode_bank').css("display", "none");
                                        $('#div_receipt_through').css("display", "none");
                                        // $('#doc_number').val($('#cash_doc_number').val());
                                        $('#btn_submit').text('Update Cash Receipt');
                                        $('#txt_bi_cheque_amount').text('Cheque Amount');

                                        $('#doc_number_bank').css("display", "none");
                                        $('#doc_number_cash').css("display", "block");

                                        $('#div_cheque_date').css("display", "none");
                                        $('#div_cheque_number').css("display", "none");
                                        $('#div_cheque_bank_name').css("display", "none");
                                        $('#cheque_number').prop('required', false);
                                        $('#cheque_bank_name').prop('required', false);
                                        $('#cheque_date').prop('required', false);
                              

                                    } else {
                                        $('#receipt_mode_cash').prop('required', false);
                                        $('#receipt_mode_bank').prop('required', true);
                                        $('#receipt_mode_cash').css("display", "none");
                                        $('#receipt_mode_bank').css("display", "block");
                                        $('#div_receipt_through').css("display", "");
                                        
                                        // $('#doc_number').val($('#bank_doc_number').val());
                                        $('#btn_submit').text('Update Bank Receipt');
                                        $('#txt_bi_cheque_amount').text('Cheque Amount');
                                        
                                        $('#doc_number_bank').css("display", "block");
                                        $('#doc_number_cash').css("display", "none");

                                        
                                    }

                                    // Remove selected attribute from all options
                                    $('#receipt_through option').prop('selected', false);

                                    // Select option with value=1
                                    $('#receipt_through').val(1).trigger('change');

                                   

                                    // if(mode !=  $('#actual_mode').val()){
                                    //     if(mode==1){ $('#doc_number').val($('#new_invno_cash').val()); }
                                    //     if(mode==2){ $('#doc_number').val($('#new_invno_bank').val()); }
                                    // }
                                    // else{                                        
                                    //     if(mode==1){ $('#doc_number').val($('#cash_doc_number').val()); }
                                    //     if(mode==2){ $('#doc_number').val($('#bank_doc_number').val()); }
                                    // }
                                    $('#narration').val('');
                                    document.querySelectorAll('input[name="remarks[]"]').forEach(function(input) {
                                        input.value = '';
                                    });
                                });
                                                    });

                            </script>
                                        <div class="col-2">
                                            <label class="form-label">Payment Mode</label>
                                            <div class="form-group">
                                            <select class="form-control" name="receipt_mode_cash" id="receipt_mode_cash" @if ($editData->mode == 2) style="display: none;" @endif>
                                        {{-- <option data-display="Receipt Mode *" value="">@lang('Receipt Mode') *</option> --}}
                                        @if(isset($receiptmode_cash))
                                        @foreach ($receiptmode_cash as $val)
                                            <option value="{{ @$val->id }}" @if(isset($editData)) @if(@$editData->receipt_mode == @$val->id) selected @endif @endif>{{ @$val->account_name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                                <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                    <select class="form-control" name="receipt_mode_bank" id="receipt_mode_bank" @if ($editData->mode == 1) style="display: none;" @endif>
                                        <option data-display="" value=""></option>
                                        @if(isset($receiptmode_bank))
                                        @foreach ($receiptmode_bank as $val)
                                            <option value="{{ @$val->id }}" @if(isset($editData)) @if(@$editData->receipt_mode == @$val->id) selected @endif @endif>{{ @$val->account_name }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Doc Date</label>
                                            <div class="form-group">
                                         @php
                                            $value = date('d/m/Y');
                                            if(isset($editData) && !empty($editData->doc_date)) {
                                                @$value = date('d/m/Y', strtotime(@$editData->doc_date));
                                            } else {
                                                if(!empty(old('doc_date'))) {
                                                    @$value = old('doc_date');
                                                } else {
                                                    @$value = date('d/m/Y');
                                                }
                                            }
                                        @endphp

                                            <input class="form-control date-picker" id="doc_date" type="text" name="doc_date" value="{{ @$value }}">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <label class="form-label">Currency</label>
                                            <div class="form-group">
                                                <select class="form-control" name="currency" id="currency">
                                    @foreach ($currency as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ isset($edit) ? (!empty(@$editData->currency) ? (@$editData->currency == @$value->id ? 'selected' : '') : '') : '' }}>
                                            {{ @$value->code }}
                                        </option>
                                    @endforeach
                                </select>
                                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                            </div>
                                        </div>

                             
                                       
                                      
                                         <div class="col-2">
                                            <label class="form-label">Created By</label>
                                            <div class="form-group">

                                                  <input
                                        class="form-control"
                                        type="text" name="createdby" autocomplete="off" id="created_by"
                                        value="{{ isset($editData) ? (!empty(@$editData->created_by) ? @$editData->createdby->full_name : old('created_by')) : Auth::user()->full_name }}"
                                        readonly>
                                                {{-- <select class="form-control js-example-basic-single" name="createdby" id="createdby" >
                                                    
                                                        @foreach ($staff_list as $value)
                                                        <option value="{{ $value->user_id }}" 
                                                                @if($value->user_id == $editData->created_by) selected @endif>
                                                                {{ $value->full_name }}
                                                            </option>
                                                            @endforeach
                                                    </select> --}}
                                            </div>
                                        
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>



                            <div class="tab-wrap mb-3">
                                <ul class="nav nav-tabs" id="purchaseDetailsTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="extra-fields-tab" data-bs-toggle="tab" data-bs-target="#extra-fields" type="button" role="tab" aria-controls="extra-fields" aria-selected="true">Extra Fields</button>
                                    </li>
                                </ul>
                                <div class="tab-content mb-3" id="purchaseDetailsTabsContent">
                                    <div class="tab-pane fade show active" id="extra-fields" role="tabpanel" aria-labelledby="extra-fields-tab">
                                        <div class="row gap-rows">

   
                <div class="col-12 mb-2">
                    <div class="row gap-rows">
                        
                        <div class="col-lg-2 mb-4" id="div_receipt_through" @if ($editData->receipt_through == 1) style="display: none;" @endif>
                                <label>@lang('Receipt Through')<span>*</span></label>
                                <div class="form-group">
                                    <select class="form-control" name="receipt_through" id="receipt_through">
                                        <option value="1" @if ($editData->receipt_through == 1) selected @endif>Bank Transfer</option>
                                        <option value="2" @if ($editData->receipt_through == 2) selected @endif>CDC Cheque</option>
                                        <option value="3" @if ($editData->receipt_through == 3) selected @endif>PDC Cheque</option>
                                </select>
                                            <i class="ico icon-outline-alt-arrow-down dropdown-toggle-ico"></i>

                                </div>
                                
                            </div>
                         <script>
    $(document).ready(function () {
        // Delegated binding ensures event fires even if hidden initially
        $(document).on('change', '#receipt_through', function () {
            console.log("changed");

            var receiptthrough = $(this).val();

            if (receiptthrough == "1") {
                // Bank Transfer → hide cheque fields
                $('#div_cheque_date').hide();
                $('#div_cheque_number').hide();
                $('#div_cheque_bank_name').hide();

                $('#cheque_number').prop('required', false);
                $('#cheque_bank_name').prop('required', false);
                $('#cheque_date').prop('required', false);
            }

            if (receiptthrough == "2" || receiptthrough == "3") {
                // CDC/PDC Cheque → show cheque fields
                $('#div_cheque_date').show();
                $('#div_cheque_number').show();
                $('#div_cheque_bank_name').show();

                $('#cheque_number').prop('required', true);
                $('#cheque_bank_name').prop('required', true);
                $('#cheque_date').prop('required', true);
            }
        });
    });
</script>
                            <div class="col-lg-2 mb-4" id="div_cheque_date" @if ($editData->receipt_through == 1) style="display: none;" @endif>
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('Cheque Date')</label>
                                            @php
                                            $value = date('d/m/Y');
                                            if(isset($editData) && !empty($editData->cheque_date) ){ 
                                                @$value = @App\SysHelper::normalizeToDmy(@$editData->cheque_date); 
                                                // @$value = date('Y-m-d', strtotime(@$editData->cheque_date)); 

                                            }
                                            @endphp
                                            <input class="form-control date-picker" id="cheque_date" type="text" name="cheque_date" value="{{ @$value }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 mb-4" id="div_cheque_number" @if ($editData->receipt_through == 1) style="display: none;" @endif>
                                <div class="input-effect">
                                    <label>  @lang('Cheque Number') <span>*</span> </label>
                                    <input class="form-control" type="text" id="cheque_number" name="cheque_number" value="{{isset($editData)?@$editData->cheque_number:old('cheque_number')}}">
                                </div>
                            </div>
                            <div class="col-lg-2 mb-4" id="div_cheque_bank_name" @if ($editData->receipt_through == 1) style="display: none;" @endif>
                                <div class="input-effect">
                                    <label>  @lang('Cheque Bank Name') <span>*</span> </label>
                                    <input class="form-control" type="text" id="cheque_bank_name" name="cheque_bank_name" value="{{isset($editData)?@$editData->cheque_bank_name:old('cheque_bank_name')}}">
                                </div>
                            </div>
                            <div class="col-lg-2 mb-4">
                                <div class="input-effect">
                                    <label>  @lang('Receipt Date') <span>*</span> </label>
                                    <input class="form-control date-picker" type="text" id="receipt_date" name="receipt_date" value="{{isset($editData) && !empty($editData->receipt_date) ? date('d/m/Y', strtotime($editData->receipt_date)) 
    : old('receipt_date')}}" required>
                                </div>
                            </div>

                             <div class="col-lg-2 mb-4">
                                <div class="input-effect">
                                    <label>@lang('Deal ID')<span>*</span></label>
                                    <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" value="{{ @$deal_id }}">
                                </div>
                            </div>
                            <div class="col-lg-6 mb-4">
                                <div class="input-effect">
                                    <label>@lang('Remarks') <span></span></label>
                                    <input
                                        class="form-control"
                                        type="text" name="narration" autocomplete="off"
                                        value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}"
                                        id="narration">

                                        <input type="hidden" id="narration_1" />
                                        <input type="hidden" id="narration_2" />
                                        <input type="hidden" id="narration_row_id" />
                                        <input type="hidden" id="narration_actual" />
                                </div>
                            </div>
                           

                            
                        @if($editData->mode == 2)
                            <script>
                                $('#mode').val(2);
                                $('#doc_number').val($('#bank_doc_number').val());
                                $('#div_receipt_through').css("display", "");                                
                                // $('#div_cheque_date').css("display", "");
                                if($('#receipt_through').val()==1){
                                    $('#div_cheque_number').css("display", "none");
                                    $('#div_cheque_bank_name').css("display", "none");
                                } else {
                                    // $('#div_cheque_number').css("display", "");
                                    // $('#div_cheque_bank_name').css("display", "");
                                }
                                $('#cheque_number').prop('required', true);
                                $('#cheque_bank_name').prop('required', true);
                                $('#cheque_date').prop('required', true);
                                $('#mode').change();
                                $('#txt_bi_cheque_amount').text('Cheque Amount');
                            </script>
                        @endif

                                            


                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-container" style="border: solid 1px #d9d9d9;">
                                <table class="table table-hover form-item-table" id="myTable">
                                    <thead>                                                            
                                        <tr>
                                            <th class="resizable text-center" width="50px">@lang('No')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="450px">@lang('Account Name') <a class="icon icon-outline-book text-dark" data-bs-toggle="modal" data-bs-target="#addproductModal"></a><div class="resizer"></div></th>
                                            <th class="resizable text-center" width="150px">@lang('Amount')<div class="resizer"></div></th>
                                            <th class="resizable text-center">@lang('Narration')<div class="resizer"></div></th>
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

                                                     @php
    $settings = App\SysHelper::getCompanyCodeSettings(session('logged_session_data.company_id'));

    $code = @$editDataList[$roid-1]->account_code;
    $showCode = true;

    // ensure $code is a string before checking
    $codeStr = (string) ($code ?? '');

    if (!$settings['is_account_code'] && \Illuminate\Support\Str::startsWith($codeStr, 'ACC')) {
        $showCode = false;
    } elseif (!$settings['is_subaccount_code'] && \Illuminate\Support\Str::startsWith($codeStr, 'SACC')) {
        $showCode = false;
    } elseif (!$settings['is_customer_code'] && \Illuminate\Support\Str::startsWith($codeStr, 'CUS')) {
        $showCode = false;
    } elseif (!$settings['is_supplier_code'] && \Illuminate\Support\Str::startsWith($codeStr, 'SUP')) {
        $showCode = false;
    }
@endphp

                                    <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $roid }}" /></td>
                                            <td class="noborder">
                                                <select class="form-control" name="account_id[]">
                                                    <option value="{{ @$editDataList[$roid-1]->account_id }}">
                                                        @if ($showCode)
                                                            {{ @$editDataList[$roid-1]->account_name }} ({{ @$editDataList[$roid-1]->account_code }})
                                                        @else
                                                        {{ @$editDataList[$roid-1]->account_name }}
                                                        @endif
                                                       
                                                    </option>
                                                </select>
                                            </td> 
                                            <td>
                                                <input data-enter-skip class="form-control text-end" type="decimal" name="amount[]" autocomplete="off" value="{{ @App\SysHelper::com_curr_format(@$editDataList[$roid-1]->credit_amount,2,'.','') }}" onchange="update_totals()" onblur="formatCurrency(this)">
                                            </td>
                                            <td><input type="text" class="form-control" name="remarks[]" value="{{ @$editDataList[$roid-1]->remarks }}"></td>
                                        </tr>
                                    @endfor
                                    <script>
$(document).ready(function() {
    $('input[name="amount[]"]').each(function() {
        update_totals();
        formatCurrency(this);
    });
});
</script>


                                        <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $roid }}" /></td>
                                            <td class="noborder">
                                                <select class="form-control" name="account_id[]">
                                                <option value=""></option>
                                            </select>
                                            </td> 
                                            <td>                                                                    
                                                <input data-enter-skip class="form-control text-end" type="decimal" name="amount[]" autocomplete="off" onchange="update_totals()" onblur="formatCurrency(this)">

                                            </td>
                                            <td><input type="text" class="form-control" name="remarks[]"></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" scope="col" >Total</th>
                                            <th class="text-end"><label id="lbl_total_amount" >0</label></th>
                                            <th class="text-end" scope="col" ></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div id="contextMenu">
                                    <button type="button" id="addRow">Add Row</button>
                                    <button type="button" id="deleteRow">Delete Row</button>
                                </div>
                            </div>
                            {{ Form::close() }}
                            

                            
                <div class="row mt-40">
                    <div class="col-lg-12 text-left mb-2">
                        <b>Adjusted Items</b>
                            <table class="table table-hover " id="long-list" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:50px;" class="text-center">@lang('#')</th>
                                        <th style="width:100px;"  class="text-center">@lang('Deal ID')</th>
                                        <th style="width:100px;"  class="text-center">@lang('Doc Number')</th>
                                        <th style="width:100px;"  class="text-center">@lang('Doc Date')</th>
                                        <th style="width:100px;"  class="text-center">@lang('LPO NO')</th>
                                        <th style="width:100px;" class="text-center">Total</th>
                                        <th style="width:100px;" class="text-center">Paid</th>
                                        <th style="width:100px;" class="text-center">Balance</th>
                                        <th style="width:100px;" class="text-center">Adjusted</th>
                                        <th style="width:100px;" class="text-center">Unadjusted</th>
                                        <th style="width:100px;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                        @if(count($editDataAdjustments)>0)
                                @foreach ($editDataAdjustments as $item)
                                @php
                                $deal_code = "";
                                    $si = @App\SysSalesInvoice::where('doc_number', $item->bi_doc_no)->first();
                                    if($si){
                                        $deal_code = $si->deal_code->code;
                                    }
                                @endphp
                                    <tr>
                                        <td class="text-center">{{ @$loop->iteration }}</td>
                                        <td class="text-center"><a href="{{ url('get-url-deal',$deal_code) }}">{{ @$deal_code }}</a></td>
                                        <td class="text-center">{{ @$item->bi_doc_no }}</td>
                                        <td class="text-center">{{ date('d/m/Y', strtotime(@$item->bi_doc_date)) }}</td>
                                        <td class="text-center">{{ @$item->bi_lpo_no }}</td>
                                        <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_total,2,'.',',') }}</td>
                                        <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_paid,2,'.',',') }}</td>
                                        <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_balance_to_adjust,2,'.',',') }}</td>
                                        <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_amount,2,'.',',') }}</td>
                                        <td class="text-end">{{ @App\SysHelper::com_curr_format(@$item->bi_cheque_amount - @$item->bi_amount_adjusted,2,'.',',') }}</td>
                                        <td class="text-center"><a class="btn-sm btn-light" onclick="return delete_adjestments({{ $item->id }});"><i class="ico ico ico icon-outline-trash-bin-minimalistic text-darkphp -S text-dark" style="font-size: 16px;"></i></a></td>
                                    </tr>
                                @endforeach
                        @endif
                                </tbody>
                            </table>
                    </div>
                </div>

                
        
                
                            <button type="button" data-bs-toggle="modal" data-bs-target="#cr_popup_win" id="addCtrlBankBookAdjestEdit" hidden></button>

    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'receivable-outstanding-store-update', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'receipt-adj-create-form']) }}
<div class="modal side-panel fade" id="cr_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg"> 
            <div class="modal-content">
                <div class="modal-header m-0 p-0">
                    <h4 class="modal-title">Bill Wise Selection</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <input type="hidden" id="br_account_id" name="br_account_id">
                    <input type="hidden" id="br_account_id_amount" name="br_account_id_amount">
                    <input type="hidden" id="bi_currency2" name="bi_currency2" value="{{ $editData->currency }}" />
                    <input type="hidden" id="doc_number2" name="doc_number2" value="{{ $editData->doc_number }}" />
                    <input type="hidden" id="transaction_type2" name="transaction_type2" value="@if($editData->mode==1) cashreceipt @else bankreceipt @endif" />                    
					<div class="card-body" style=" overflow-y: scroll;">
                        <div class="row">
                            <div class="col mb-20">
                                <div class="input-effect">
                                    <label id="txt_bi_cheque_amount">@lang('Cash Amount') <span>*</span></label>
                                    <input class="primary-input form-control text-end" type="text" id="bi_cheque_amount" name="bi_cheque_amount"  value="0" >
                                </div>
                            </div>
                            <div class="col mb-20">
                                <div class="input-effect">
                                    <label>  @lang('Amount Adjusted') <span>*</span> </label>
                                    <input class="primary-input form-control text-end" type="text" id="bi_amount_adjusted" name="bi_amount_adjusted" value="0" >
                                    
                                    <input type="hidden" id="bi_balance_adjest" value="">

                                </div>
                            </div>
                            <div class="col mb-20">
                                <div class="input-effect">
                                    <label>  @lang('Balance to Adjust') <span>*</span> </label>
                                    <input class="primary-input form-control text-end" type="text" id="bi_extra_amount" name="bi_extra_amount" value="0" >                                    
                                    
                                    <input type="hidden" id="bi_balance_to_adjust" name="bi_balance_to_adjust" value="0" >
                                    
                                </div>
                            </div>
                              <div class="col mb-20">
                                        <div class="input-effect">
                                            <label>  @lang('Search in table') </label>
                                            <input class="primary-input form-control" type="text" id="tableSearchBill" name="tableSearchBill" value="" >                                       
                                        </div>
                                    </div>
                        </div>

                    
                                <div class="equipment comon-status mt-4 d-block">
                                    <table class="table table-hover form-item-table data-table-bill" cellspacing="0" width="100%" id="crListBankBookAdjest">
                                        <thead>
                                            <tr>
                                                <th style="width:100px;" class="text-center">@lang('Deal')</th>
                                                <th style="width:100px;" class="text-center">@lang('Doc No')</th>
                                                <th style="width:100px;" class="text-center">@lang('Doc Date')</th>
                                                <th style="width:100px;" class="text-center">@lang('LPO NO')</th>
                                                <th style="width:100px;" class="text-center">@lang('Total')</th>
                                                <th style="width:100px;" class="text-center">@lang('Paid')</th>
                                                <th style="width:100px;" class="text-center">@lang('Balance')</th>
                                                <th style="width:100px;" class="text-center">@lang('Adjustment')</th>
                                                <th style="width:100px;" class="text-center">@lang('Narration')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input class="w-100 sstxtbx" type="number" step="any" id="bi_doc_no_{{$roid}}" name="bi_doc_no[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" step="any" id="bi_doc_date_{{$roid}}" name="bi_doc_date[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" step="any" id="bi_lpo_no_{{$roid}}" name="bi_lpo_no[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" step="any" id="bi_due_date_{{$roid}}" name="bi_due_date[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" step="any" id="bi_total_{{$roid}}" name="bi_total[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" step="any" id="bi_paid_{{$roid}}" name="bi_paid[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" step="any" id="bi_balance_{{$roid}}" name="bi_balance[]" autocomplete="off" min="0"></td>
                                                <td><input class="w-100 sstxtbx" type="number" step="any" id="bi_amount_{{$roid}}" name="bi_amount[]" autocomplete="off" min="0"></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th class="text-end"><label id="footer_total" /></th>
                                                <th class="text-end"><label id="footer_paid" /></th>
                                                <th class="text-end"><label id="footer_balance" /></th>
                                                <th class="text-end"><label id="footer_adjustment" /></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                           

                         <script>

function get_set_amount(id) {
    var form_amt = Number(($('#bi_cheque_amount').val() || '0').replace(/,/g, '')) || 0;

    if (id !== undefined && id !== null && String(id) !== '') {
        var bal_amt = Number(($('#bi_balance_' + id).val() || '0').replace(/,/g, '')) || 0;
        var cur_val = Number(($('#bi_amount_'  + id).val() || '0').replace(/,/g, '')) || 0;
        var current_doc_adjustment = Number(($('#bi_amount_' + id).data('current-amount') || '0').toString().replace(/,/g, '')) || 0;

        // Sum of all OTHER rows (excluding the current row)
        var other_sum = 0;
        $('.tot_amt').each(function () {
            if ($(this).attr('id') !== 'bi_amount_' + id) {
                var v = Number(($(this).val() || '0').replace(/,/g, ''));
                other_sum += isNaN(v) ? 0 : v;
            }
        });

        // Maximum this row is allowed: capped by row balance AND remaining cheque amount
        var cap = Math.min(bal_amt + current_doc_adjustment, Math.max(0, form_amt - other_sum));

        // Only clamp if user typed more than allowed — auto-fill is handled by the click handler
        if (cur_val > cap) {
            $('#bi_amount_' + id).val(formatAmount(cap));
        }
    }

    // Recalculate total adjusted across all rows
    var adjusted_sum = 0;
    $('.tot_amt').each(function () {
        var v = Number(($(this).val() || '0').replace(/,/g, ''));
        adjusted_sum += isNaN(v) ? 0 : v;
    });

    var remaining_balance = Math.max(0, form_amt - adjusted_sum);
    $('#bi_amount_adjusted').val(formatAmount(adjusted_sum));
    // Keep all balance fields in sync to avoid UI/API mismatch.
    $('#bi_balance_adjest').val(formatAmount(remaining_balance));
    $('#bi_balance_to_adjust').val(formatAmount(remaining_balance));
    $('#bi_extra_amount').val(formatAmount(remaining_balance));

    // Footer total
    var num_tot_amt = $('.tot_amt').length;
    var total = 0;
    for (var i = 1; i <= num_tot_amt; i++) {
        var v = Number(($('#bi_amount_' + i).val() || '0').replace(/,/g, ''));
        total += isNaN(v) ? 0 : v;
    }
    $('#footer_adjustment').text(formatAmount(total));

    // Remarks: collect doc numbers for all rows that have an amount
    var docs = [];
    for (var i = 1; i <= num_tot_amt; i++) {
        var v = Number(($('#bi_amount_' + i).val() || '0').replace(/,/g, ''));
        if (v > 0) {
            docs.push(($('#bi_doc_no_' + i).val() || '').replace(/,/g, ''));
        }
    }
    var re_id = $('#narration_row_id').val();
    if (re_id !== '' && re_id !== undefined) {
        $('#remarks_' + re_id).val(docs.join(', '));
    }
}

// re-run calculations when user edits any adjustment input
$(document).on('input change', '.tot_amt', function () {
    var idMatch = $(this).attr('id') ? $(this).attr('id').match(/(\d+)$/) : null;
    if (idMatch) {
        get_set_amount(idMatch[1]);
    } else {
        get_set_amount();
    }
});

// recalc when cheque amount or adjusted total are edited (or set programmatically)
$(document).on('input change', '#bi_cheque_amount, #bi_amount_adjusted', function () {
    get_set_amount();
});

// Auto-fill adjustment field on click ONLY when it is empty (does not fire on erase)
$(document).on('click', '.tot_amt', function () {
    var cur_val = Number(($(this).val() || '0').replace(/,/g, '')) || 0;
    if (cur_val !== 0) { return; }
    var idMatch = $(this).attr('id') ? $(this).attr('id').match(/(\d+)$/) : null;
    if (!idMatch) { return; }
    var idx = idMatch[1];
    var form_amt = Number(($('#bi_cheque_amount').val() || '0').replace(/,/g, '')) || 0;
    var bal_amt  = Number(($('#bi_balance_' + idx).val() || '0').replace(/,/g, '')) || 0;
    var current_doc_adjustment = Number(($('#bi_amount_' + idx).data('current-amount') || '0').toString().replace(/,/g, '')) || 0;
    var other_sum = 0;
    $('.tot_amt').each(function () {
        if ($(this).attr('id') !== 'bi_amount_' + idx) {
            var v = Number(($(this).val() || '0').replace(/,/g, ''));
            other_sum += isNaN(v) ? 0 : v;
        }
    });
    var cap = Math.min(bal_amt + current_doc_adjustment, Math.max(0, form_amt - other_sum));
    if (cap > 0) {
        $(this).val(formatAmount(cap));
        get_set_amount(idx);
    }
});
</script>

                        
                                <div class="mt-4 mb-3 d-flex justify-content-center">                                        
                                    <button type="button" class="btn btn-light add-btn ms-2" onclick="save_adjestments()">
                                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                                    </button>
                                </div>
                            
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{ Form::close() }}


    <script>
    function save_adjestments() {

        // mirror behavior from r_add.validateAttachForm: when the user saves the bill-wise modal
        // we need to propagate remarks/narration to the main form row and regenerate narration text.
        var numRows = $('.row_ctrl').length;
        var natt_txt = "";
        for (var i = 1; i <= numRows; i++) {
            var lpo = $('#bi_lpo_no_' + i);
            var nar = $('#bi_narration_' + i);
            var invo = $('#bi_doc_no_' + i);
            var amt = $('#bi_amount_' + i).val();
            if (lpo.length && nar.length && invo.length && amt && amt != 0) {
                var lpoText = lpo.val();
                if (lpoText && !lpoText.toLowerCase().startsWith('lpo')) {
                    lpoText = 'LPO ' + lpoText;
                }
                if (natt_txt === "") {
                    natt_txt += invo.val() + " (" + lpoText + ") " + nar.val();
                } else {
                    natt_txt += ", " + invo.val() + " (" + lpoText + ") " + nar.val();
                }
            }
        }
        $('input[name="remarks[]"]').eq($('#narration_row_id').val()).val(natt_txt);
        if (typeof generate_narration_fa === 'function') {
            generate_narration_fa();
        }

        add_deal_code_from_adjestment()
        
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('receivable-outstanding-store-update') }}";

        var baseUrl = "{{ url('/') }}";

        var bi_doc_no = [];
        $('input[name="bi_doc_no[]"]').each(function() { bi_doc_no.push($(this).val()); });

        var bi_lpo_no = [];
        $('input[name="bi_lpo_no[]"]').each(function() { bi_lpo_no.push($(this).val()); });

        var bi_doc_date = [];
        $('input[name="bi_doc_date[]"]').each(function() { bi_doc_date.push($(this).val()); });

        var bi_total = [];
        $('input[name="bi_total[]"]').each(function() { bi_total.push($(this).val()); });

        var bi_amount = [];
        $('input[name="bi_amount[]"]').each(function() { bi_amount.push($(this).val()); });
        
        var bi_balance = [];
        $('input[name="bi_balance[]"]').each(function() { bi_balance.push($(this).val()); });
        
        var bi_narration = [];
        $('input[name="bi_narration[]"]').each(function() { bi_narration.push($(this).val()); });

        var bi_amount = [];
        $('input[name="bi_amount[]"]').each(function() { bi_amount.push($(this).val()); });
        
        var bi_amount = [];
        $('input[name="bi_amount[]"]').each(function() { bi_amount.push($(this).val()); });
        
        var bi_amount = [];
        $('input[name="bi_amount[]"]').each(function() { bi_amount.push($(this).val()); });
        
        var bi_amount = [];
        $('input[name="bi_amount[]"]').each(function() { bi_amount.push($(this).val()); });
        
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                br_account_id : $('#br_account_id').val(),
                br_account_id_amount : $('#br_account_id_amount').val(),
                bi_currency2 : $('#bi_currency2').val(),
                doc_number2 : $('#doc_number2').val(),
                transaction_type2 : $('#transaction_type2').val(),
                txt_bi_cheque_amount : $('#txt_bi_cheque_amount').val(),
                bi_cheque_amount : $('#bi_cheque_amount').val(),
                bi_amount_adjusted : $('#bi_amount_adjusted').val(),
                bi_balance_adjest : $('#bi_balance_adjest').val(),
                bi_extra_amount : $('#bi_extra_amount').val(),
                bi_balance_to_adjust : $('#bi_balance_to_adjust').val(),

                bi_doc_no: bi_doc_no,
                bi_lpo_no: bi_lpo_no,
                bi_doc_date: bi_doc_date,
                bi_total: bi_total,
                bi_amount: bi_amount,
                bi_balance: bi_balance,
                bi_narration: bi_narration,
            },
            cache: false,
           success: function(dataResult) {
            
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows = "";
                var decimalPoint = @json(session('logged_session_data.decimal_point'));

                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }

                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        var item = dataResult['data'][i];
                        getSelectedRows += "<tr>\
                            <td>" + (i + 1) + "</td>\
                            <td>" + (item.bi_doc_no || '') + "</td>\
                            <td>" + (item.bi_doc_date || '') + "</td>\
                            <td>" + (item.bi_lpo_no || '') + "</td>\
                            <td class='text-end'>" + formatNumber(item.bi_total, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatNumber(item.bi_paid, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatNumber(item.bi_balance_to_adjust, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatNumber(item.bi_amount, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatNumber((item.bi_cheque_amount - item.bi_amount_adjusted), decimalPoint) + "</td>\
                            <td class='text-end'>\
                                <a class='btn-sm btn-danger' onclick='return delete_adjestments(" + item.id + ")' >\
                                    <i class='fa fa-trash' aria-hidden='true'></i>\
                                </a>\
                            </td>\
                        </tr>";
                    }
                        $('#adjustment-table tbody').empty();
                        $("#adjustment-table tbody").append(getSelectedRows); 
                    toastr.success('Adjustments Saved Successfully');
                    
                    
                
                    // location.reload();
                } else {
                    $('#adjustment-table tbody').empty();
                    alert('Error: Something went wrong!');
                }
                $("#loading_bg").css("display", "none");
                
                $('#cr_popup_win').modal('hide');
            }
        });
    }
    function delete_adjestments(id) {
        var action = "{{ URL::to('delete-receipt-adjustment-json') }}";

         if (!confirm('Are you sure you want to delete this item?')) {
        return false; // Cancelled
    }
        
        $("#loading_bg").css("display", "block");
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id:id,
                doc_number : $('#doc_number').val(),

            },
            cache: false,
           success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows = "";
                var decimalPoint = @json(session('logged_session_data.decimal_point'));

                if (dataResult['data'] != null) {
                    len = dataResult['data'].length;
                }

                if (len > 0) {
                    for (var i = 0; i < len; i++) {
                        var item = dataResult['data'][i];
                        getSelectedRows += "<tr>\
                            <td>" + (i + 1) + "</td>\
                            <td>" + (item.bi_doc_no || '') + "</td>\
                            <td>" + (item.bi_doc_date || '') + "</td>\
                            <td>" + (item.bi_lpo_no || '') + "</td>\
                            <td class='text-end'>" + formatNumber(item.bi_total, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatNumber(item.bi_paid, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatNumber(item.bi_balance_to_adjust, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatNumber(item.bi_amount, decimalPoint) + "</td>\
                            <td class='text-end'>" + formatNumber((item.bi_cheque_amount - item.bi_amount_adjusted), decimalPoint) + "</td>\
                            <td class='text-end'>\
                                <a class='btn-sm btn-danger' onclick='return delete_adjestments(" + item.id + ")' >\
                                    <i class='fa fa-trash' aria-hidden='true'></i>\
                                </a>\
                            </td>\
                        </tr>";
                    }
                        $('#adjustment-table tbody').empty();
                        $("#adjustment-table tbody").append(getSelectedRows); 
                        $('#narration').val('');
                        $('#deal_id').val('');
                        $("input[name='amount[]']").val('');
                        $("input[name='remarks[]']").val('');
                    // alert('Adjustments Deleted Successfully');
                    //show toastr
                    toastr.success('Adjustments Deleted Successfully');
                    location.reload();
                } else {
                    location.reload();

                    $('#adjustment-table tbody').empty();
                    // alert('Error: Something went wrong!');
                }
                $("#loading_bg").css("display", "none");
                $('#btn_adj_close').click();
            }
        });
    }

    function formatNumber(num, decimals = 2) {
    if (num === null || num === undefined || isNaN(num)) return "0.00";
    return Number(num).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}
</script>

<script>
    function BankBookAdjestBalance(id) {
        var bi_total = $('#bi_total_'+id).val();
        var bi_paid = $('#bi_paid_'+id).val();
        var tot = (parseFloat(bi_total)-parseFloat(bi_paid)).toFixed(@json(session('logged_session_data.decimal_point')));
        $('#bi_balance_'+id).val(tot);
        $('#bi_amount_'+id).val(bi_paid);
    }
    
    // helper copied from r_add: aggregate deal IDs from nonzero adjustments
    function add_deal_code_from_adjestment() {
        var deal_codes = [];
        var existing = $('#deal_id').val();
        if (existing) {
            deal_codes = existing.split(',').map(code => code.trim());
        }

        var num_tot_amt2 = $('.tot_amt').length;
        for (var i = 1; i <= num_tot_amt2; i++) {
            var amount = $('#bi_amount_' + i).val();
            var new_code = $('#bi_deal_id_' + i).val();

            if (amount && amount != 0 && new_code) {
                deal_codes.push(new_code.trim());
            }
        }

        console.log("All deal codes from adjustments (including existing): ", deal_codes);
        var unique_deal_codes = [...new Set(deal_codes)];
        console.log("Unique deal codes to set in input: ", unique_deal_codes);
        //console.log("Unique deal codes to set in input: ", unique_deal_codes);
        $('#deal_id').val(unique_deal_codes.join(', '));
    }

    function validateAttachForm() {
        $("#loading_bg").css("display", "block");
        var numRows = $('.row_ctrl').length;
        var natt_txt = "";
        console.log("Number of adjustment rows: ", numRows);

        if (numRows > 0) {
            delete_before_update();
        }
        for (var i = 1; i <= numRows; i++) {
            validateBankBookAdjestForm(i);
            var lpo = $('#bi_lpo_no_' + i);
            var nar = $('#bi_narration_' + i);
            var invo = $('#bi_doc_no_' + i);
            var amt = $('#bi_amount_' + i).val();


            if (lpo.length && nar.length && invo.length && amt && amt != 0) {
                var lpoText = lpo.val();
                if (lpoText && !lpoText.toLowerCase().startsWith('lpo')) {
                    lpoText = 'LPO ' + lpoText;
                }
                if (natt_txt === "") {
                    natt_txt += invo.val() + " (" + lpoText + ") " + nar.val();
                } else {
                    natt_txt += ", " + invo.val() + " (" + lpoText + ") " + nar.val();
                }
            }
        }
        $('input[name="remarks[]"]').eq($('#narration_row_id').val()).val(natt_txt);

        // regenerate narration text if helper exists
        if (typeof generate_narration_fa === 'function') {
            generate_narration_fa();
        }

        // populate deal_id field after adjustments have been processed
        add_deal_code_from_adjestment();

        $("#btn_close2").click();
        $("#loading_bg").css("display", "none");
    }

    function delete_before_update() {
        var doc_number = $("#doc_number").val();
        var account_id = $('#br_account_id').val();
        var url = $('#url').val();
        $.ajax({
            url: url + '/' + 'receivable-outstanding-store-delete-before-update',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                    doc_number : doc_number,
                    account_id : account_id,

            },
            cache: false,
        success: function(response) {
            var response = JSON.parse(response);
            var len = 0;
            if(response['data']=="ERROR")
            {
                alert("Error found in something!!");                
            }
            else
            {
                //$("#btn_close2").click();
                //$("#addCtrlBankBookAdjest").click();
            }            
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {}
    });
    }

    function validateBankBookAdjestForm(id) {
        var val1 = $("#bi_cheque_amount").val();
        var val2 = $("#bi_amount_adjusted").val();
        var val3 = $("#bi_extra_amount").val();
        var val4 = $("#bi_balance_to_adjust").val();
        var doc_number = $("#doc_number").val();
    
        var bi_doc_no = $('#bi_doc_no_'+id).val();
        var bi_doc_date = $('#bi_doc_date_'+id).val();
        var bi_lpo_no = $('#bi_lpo_no_'+id).val();
        var bi_total = $('#bi_total_'+id).val();
        var bi_paid = $('#bi_paid_'+id).val();
        var bi_balance = $('#bi_balance_'+id).val();
        var bi_amount = $('#bi_amount_'+id).val();
        var bi_narration = $('#bi_narration_'+id).val();
        var account_id = $('#br_account_id').val();
        var entry_date = $('#doc_date').val();
        var bi_currency = $('#currency').val();
        
        if($('#mode').val()==1){
            transaction_type = 'cashreceipt';
        } else {
            transaction_type = 'bankreceipt';
        }
        var entry_type =2; //1 Debit, 2 Credit
        var process_id = $('#receipt_process_id').val();
        
        $("#loading_bg").css("display", "block");
        
        var url = $('#url').val();
    
        $.ajax({
                url: url + '/' + 'receivable-outstanding-store-update',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    bi_cheque_amount : val1,
                    bi_amount_adjusted : val2,
                    bi_extra_amount : val3,
                    bi_balance_to_adjust : val4,
                        bi_currency : bi_currency,
                        bi_doc_no : bi_doc_no,
                        bi_doc_date : bi_doc_date,
                        bi_lpo_no : bi_lpo_no,
                        bi_total : bi_total,
                        bi_paid : bi_paid,
                        bi_balance : bi_balance,
                        bi_amount : bi_amount,
                        bi_narration : bi_narration,
                        account_id : account_id,
                        entry_date : entry_date,
                        transaction_type : transaction_type,
                        entry_type : entry_type,
                        process_id : process_id,
                        doc_number : doc_number,
    
                },
                cache: false,
            success: function(response) {
                var response = JSON.parse(response);
                var len = 0;
                if(response['data']=="ERROR")
                {
                    alert("Error found in something!!");                
                }
                else
                {
                    //$("#btn_close2").click();
                    //$("#addCtrlBankBookAdjest").click();
                }            
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {}
        });
    
        //preventDefault();
            $("#loading_bg").css("display", "none");
        }


    function cfc_amount_change(id)
    {
        var amt = $("#cfc_amount_"+id).val();
        $("#cfc_cal_amount_"+id).val(amt);
    }


    </script>


<script>
  // Run cr_popup_fun only once (no repeated bindings)
  $(document).on('keypress', 'input[name="amount[]"]', function (e) {
    if (e.which === 13) {
      const currentInput = $(this);
      const currentRow = currentInput.closest('tr');
      $('#narration_row_id').val(currentRow.index());


      let br_account_id = currentRow.find('select[name="account_id[]"]').val();
      if (br_account_id === null || br_account_id === undefined) {
        br_account_id = currentRow.find('input[name="account_id[]"]').val();
        }
      const br_amount = currentRow.find('input[name="amount[]"]').val();

      if (br_account_id !== "" && br_amount !== "") {
        $('#br_account_id').val(br_account_id);
        $('#br_account_id_amount').val(br_amount);
        $('#bi_cheque_amount').val(formatAmount(br_amount)).focus();
        $('#addCtrlBankBookAdjestEdit').click().prop("disabled", true);
      } else {
        alert("Account / Amount Missing");
      }

      return false; // prevent default behavior in this case
    }
  });

  // Prevent form submission on Enter for all fields EXCEPT amount[]
  $('#receipt-create-form').on('keypress', function (e) {
    if (e.which === 13 && !$(e.target).is('input[name="amount[]"]')) {
      e.preventDefault();
      return false;
    }
  });
</script>


<script>
   update_totals();

function update_totals() {
    let total_amount = 0;

    const decimal_point = @json(session('logged_session_data.decimal_point'));

    $('#myTable tbody tr').each(function () {
        const $row = $(this);

        total_amount += parseFloat($row.find('input[name="amount[]"]').val().replace(/,/g, '')) || 0;
    });

    $('#lbl_total_amount').text(formatAmount(total_amount.toFixed(decimal_point)));
}
</script>
<script>

       $(document).on('focus', 'select[name="account_id[]"]', function () {
    const $select = $(this);

    // Add the class if not present
    if (!$select.hasClass('js-account-select')) {
        $select.addClass('js-account-select');
        //$select.remove('select2-hidden-accessible');

        // Initialize Select2
        initAccountSelect2(this); // your existing function
    }
});


</script>

<script>
$(document).ready(function () {
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_cust_account_list_ajax") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search_text: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                              let text = item.account_name;
                            if (item.account_code) {
                                text += ' (' + item.account_code + ')';
                            }
                            return {
                                id: item.id,
                                text: text
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: '',
            minimumInputLength: 2,
            dropdownParent: $(selector).parent() // optional: ensures dropdown shows in modals
        });

        $(selector).on('select2:select', function (e) {
            var selectedData = e.params.data;
            var $row = $(this).closest('tr'); // find the closest row
                $row.find('input[name="amount[]"]').focus();

            // Set values using "name" attribute selectors inside the same row
            
        });

            // When any .js-account-select select2 opens, prefill the search box with the currently selected value
        $(document).on('select2:open', function(e) {
            // Find the select2 element that triggered the event
            var $select = $(document.activeElement).closest('.js-account-select');
            if ($select.length === 0) {
                // fallback: try to get the open dropdown's select
                $select = $('.js-account-select').filter(function() {
                    return $(this).data('select2') && $(this).data('select2').isOpen();
                });
            }
            if ($select.length > 0) {
                var sel = $select.select2('data');
                if (sel && sel.length && sel[0].text) {
                    setTimeout(function() {
                        const searchInput = document.querySelector(
                            '.select2-container--open .select2-search__field');
                        if (searchInput) {
                            // Put current selected text into search box so user can edit / refine
                            searchInput.value = sel[0].text.trim();
                            // trigger input so select2 filters on prefilling
                            var event = new Event('input', {
                                bubbles: true
                            });
                            searchInput.dispatchEvent(event);

                            // Move cursor to end of the text
                            try {
                                var len = searchInput.value.length;
                                searchInput.setSelectionRange(len, len);
                            } catch (err) {
                                // ignore if not supported
                            }
                        }
                    }, 0);
                }
            }
        });
    }

    initAccountSelect2('.js-account-select');

    // Re-initialize on focus if needed
    $(document).on('focus', '.js-account-select', function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            initAccountSelect2(this);
            $(this).select2('open');
        }
    });

    // On click, open dropdown and focus on search field
    $(document).on('click', '.js-account-select', function () {
        $(this).select2('open');
    });

    // Optional: Auto focus on search input when dropdown opens
    $(document).on('select2:open', function () {
        setTimeout(function () {
            document.querySelector('.select2-container--open .select2-search__field')?.focus();
        }, 0);
    });
});
</script>

    <script>
/*table row fill based on layout height (limit to 80% of viewport)*/
function fillTableTo80Percent() {
    const table = document.getElementById('myTable');
    if (!table) return;
    const tbody = table.querySelector('tbody');

    // If there are no rows, do nothing
    if (!tbody || tbody.rows.length === 0) return;

    const rowHeight = tbody.rows[0].offsetHeight || 30;

    // target area is 80% of the viewport height
    const viewportHeight = window.innerHeight;
    const targetHeight = Math.floor(viewportHeight * 0.8);

    const tableTop = table.getBoundingClientRect().top;
    const availableHeight = Math.max(0, targetHeight - tableTop);

    const existingRows = tbody.rows.length;
    const totalRows = Math.max(existingRows, Math.floor(availableHeight / rowHeight));

    const lastRow = tbody.rows[tbody.rows.length - 1];

    for (let i = existingRows + 1; i <= totalRows; i++) {
        const newRow = lastRow.cloneNode(true); // clone entire row

        const firstCellInput = newRow.cells[0].querySelector('input');
        if (firstCellInput) {
            firstCellInput.value = i;
        }
        const inputs = newRow.querySelectorAll('input');
        inputs.forEach((input, index) => {
            if (index !== 0) input.value = "";
        });

        tbody.appendChild(newRow);
    }
}

// run on load and on resize (debounced)
window.addEventListener('load', fillTableTo80Percent);
let _resizeTimer;
window.addEventListener('resize', function () {
    clearTimeout(_resizeTimer);
    _resizeTimer = setTimeout(fillTableTo80Percent, 120);
});
/*table row fill based on layout height*/
</script>

<!-- Receipt Attachments Modal -->
<div class="modal fade" id="receiptAttachmentsModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="receiptAttachmentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptAttachmentsModalLabel">Receipt Attachments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 row align-items-end" id="attachmentsUploadSection">
                    <div class="col-md-9">
                        <label class="form-label">Upload files</label>
                        <input type="file" id="receiptAttachmentsFiles" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.txt" />
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="uploadReceiptAttachmentsBtn" class="btn btn-light">
                            Upload
                        </button>
                    </div>
                </div>

                <div id="receiptAttachmentsMessage" class="mb-2"></div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>File Name</th>
                                <th>Uploaded On</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="receiptAttachmentsList">
                            <tr><td colspan="4" class="text-center">No attachments yet.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function formatDMY(dateString) {
        var d = new Date(dateString);
        if (isNaN(d.getTime())) return '';
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var yyyy = d.getFullYear();
        return dd + '/' + mm + '/' + yyyy;
    }

    function setTempReceiptAttachmentHiddenInputs(attachments) {
        var $container = $('#receiptAttachmentHiddenInputs').empty();
        if (!attachments || attachments.length === 0) {
            return;
        }
        attachments.forEach(function (att) {
            $container.append('<input type="hidden" name="temp_attachment_ids[]" value="' + att.id + '">');
        });
    }

    function renderReceiptAttachments(attachments, readOnly) {
        setTempReceiptAttachmentHiddenInputs(attachments);
        var $tbody = $('#receiptAttachmentsList').empty();
        if (!attachments || attachments.length === 0) {
            $tbody.html('<tr><td colspan="4" class="text-center">No attachments found.</td></tr>');
            return;
        }
        attachments.forEach(function (att, index) {
            var viewUrl = '{{ url("receipt/attachments") }}/' + att.id + '/download';
            var attachedDate = att.created_at ? formatDMY(att.created_at) : '';
            var row = '<tr>' +
                '<td>' + (index + 1) + '</td>' +
                '<td>' + $('<div>').text(att.file_name).html() + '</td>' +
                '<td>' + attachedDate + '</td>' +
                '<td class="text-center"><div class="d-flex justify-content-center align-items-center gap-1">' +
                    '<a href="' + viewUrl + '" target="_blank" class="btn btn-sm btn-light" title="View">' +
                        '<i class="ico icon-outline-eye" style="font-size:16px;"></i>' +
                    '</a>' +
                    (readOnly ? '' : '<button type="button" class="btn btn-sm btn-light text-danger delete-receipt-attachment-btn" data-id="' + att.id + '" title="Delete"><i class="ico icon-outline-trash-bin-trash" style="font-size:16px;"></i></button>') +
                '</div></td>' +
                '</tr>';
            $tbody.append(row);
        });
    }

    function fetchAndRenderReceiptAttachments(receiptId, readOnly) {
        if (!receiptId || receiptId == 0) {
            renderReceiptAttachments([], readOnly);
            return;
        }
        $.get('{{ url("receipt") }}/' + receiptId + '/attachments', function (response) {
            if (response.success) {
                renderReceiptAttachments(response.attachments, readOnly);
            } else {
                $('#receiptAttachmentsMessage').html('<div class="text-danger">Unable to load attachments.</div>');
            }
        }).fail(function () {
            $('#receiptAttachmentsMessage').html('<div class="text-danger">Unable to fetch attachments.</div>');
        });
    }

    $(document).on('click', '#receiptAttachmentsBtn, #receiptAttachmentsDropdownBtn', function (e) {
        if (e) e.preventDefault();
        var receiptId = parseInt($('#receipt_id').val() || 0, 10);
        var readOnly = false;
        $('#receiptAttachmentsMessage').html('');
        $('#attachmentsUploadSection').show();
        $('#receiptAttachmentsFiles').val('');
        $('#receiptAttachmentsModal').modal('show');
        fetchAndRenderReceiptAttachments(receiptId, readOnly);
    });

    $('#uploadReceiptAttachmentsBtn').on('click', function () {
        var receiptId = parseInt($('#receipt_id').val() || 0, 10);
        if (!receiptId || receiptId <= 0) {
            $('#receiptAttachmentsMessage').html('<div class="text-danger">Save Receipt first.</div>');
            return;
        }

        var files = $('#receiptAttachmentsFiles')[0].files;
        if (!files.length) {
            $('#receiptAttachmentsMessage').html('<div class="text-warning">Please choose at least one file.</div>');
            return;
        }

        var formData = new FormData();
        formData.append('sys_receipt_id', receiptId);
        for (var i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }
        var csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
        $.ajax({
            url: '{{ url("receipt/attachments/upload") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (response) {
                if (response.success) {
                    toastr.success('Attachments uploaded successfully.');
                    $('#receiptAttachmentsFiles').val('');
                    fetchAndRenderReceiptAttachments(receiptId, false);
                } else {
                    toastr.error(response.message || 'Upload failed.');
                }
            },
            error: function (xhr) {
                var err = 'Upload failed.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    err = Object.values(xhr.responseJSON.errors).map(function (v) { return v.join(', '); }).join(' | ');
                } else if (xhr.responseText) {
                    err = xhr.status + ' ' + xhr.statusText + ': ' + xhr.responseText;
                } else {
                    err = xhr.status + ' ' + xhr.statusText;
                }
                $('#receiptAttachmentsMessage').html('<div class="text-danger">' + err + '</div>');
            }
        });
    });

    $(document).on('click', '.delete-receipt-attachment-btn', function () {
        var id = $(this).data('id');
        var receiptId = parseInt($('#receipt_id').val() || 0, 10);
        $('#receiptAttachmentsMessage').html('');
        var csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
        $.ajax({
            url: '{{ url("receipt/attachments") }}/' + id + '/delete',
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function (response) {
                if (response.success) {
                    toastr.success('Attachment deleted.');
                    fetchAndRenderReceiptAttachments(receiptId, false);
                } else {
                    toastr.error('Unable to delete attachment.');
                    $('#receiptAttachmentsMessage').html('<div class="text-danger">Unable to delete attachment.</div>');
                }
            },
            error: function () {
                toastr.error('Unable to delete attachment.');
                $('#receiptAttachmentsMessage').html('<div class="text-danger">Unable to delete attachment.</div>');
            }
        });
    });
</script>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

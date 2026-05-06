    <?php try { ?>
@if (isset($editData))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'journalvoucher-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'journalvoucher-create-form']) }}
            <input type="hidden" value="{{ @$editData->id }}" name="cust_id" id="jv_id">
        @else
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'journalvoucher-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'journalvoucher-create-form']) }}
        @endif

        <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
        <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{ date('Y-m-d') }}">
            



    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            Edit ({{$editData->doc_number}})
        </h4>
        <div class="purchase-order-content-header-right">
            <a class="btn btn-light text-dark" href="{{url('journalvoucher/'.$editData->id.'?jv_action=add')}}">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </a>            
            <button type="submit" class="btn btn-light">
                <i class="ico icon-outline-bookmark-square text-warning"></i> Update
            </button>
            
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu" style="">
                        <li><a class="dropdown-item d-flex align-items-center" href="{{url('journalvoucher/'.$editData->id.'/delete')}}"><i class="ico icon-outline-close-square title-15 me-2 text-success"></i> Cancel</a></li>
                        <li><a class="dropdown-item d-flex align-items-center" href="{{url('journalvoucher/'.$editData->id.'/download')}}"><i class="ico icon-bold-download-minimalistic title-15 me-2 text-success"></i> Download</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#attachment_popup_win" onclick="view_attachment()"><i class="ico icon-bold-paperclip title-15 me-2"></i> Attachment</a></li>

                </ul>
            </div>
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
                                        <div class="col-1-5">
                                            <label class="form-label">Doc Number</label>
                                            <div class="form-group">                                                
                                                <input class="form-control" type="text" id="doc_number" name="doc_number" readonly
                                            value="{{ $editData->doc_number }}">
                                            </div>
                                        </div>
                                        <div class="col-1-5">
                                            <label class="form-label">Doc Date</label>
                                            <div class="form-group">
                                            

                                        <input class="form-control date-picker" id="doc_date" type="text" autocomplete="off"
                                            name="doc_date" value="{{ @App\SysHelper::normalizeToDmy(@$editData->doc_date) }}">
                                            </div>
                                        </div>
                                        <div class="col-1-5">
                                            <label class="form-label">Currency</label>
                                            <div class="form-group">
                                             <select
                                class="form-control js-example-basic-single"
                                name="currency" id="currency">
                                @foreach ($currency as $value)
                                    <option value="{{ @$value->id }}"
                                        {{ isset($editData) ? (!empty(@$editData->currency) ? (@$editData->currency == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->code }}
                                    </option>
                                @endforeach
                            </select>
                                            </div>
                                        </div>
                                        <div class="col-2-5">
                                            <label class="form-label">Created By:</label>
                                            <input type="text" class="form-control" readonly name="created_by" value="{{ $editData->createdby->full_name }}">
                                         
                                        </div>
                                        <div class="col-5">
                                            <label class="form-label">Remarks</label>
                                            <div class="form-group">
                                                <input
                                    class="form-control"
                                    type="text" name="narration" autocomplete="off"
                                    value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}"
                                    id="narration" required>
                          
                        <input type="hidden" name="deal_id" id="deal_id" value="{{ $editData->deal_id ?? 0 }}">
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
                                            <th class="resizable text-center" width="355px">@lang('Account Name')
                                                  <a class="icon icon-outline-book text-dark"      data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Add Account"
                            data-bs-placement="top" data-bs-toggle="modal" data-bs-target="#accountModal"></a>
                                                  <a class="icon icon-outline-book text-dark"      data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Add Sub Account"
                            data-bs-placement="top" data-bs-toggle="modal" data-bs-target="#accountSubModal"></a>
                                                <div class="resizer"></div></th>
                                            <th class="resizable text-end" width="114px">@lang('Debit')<div class="resizer"></div></th>
                                            <th class="resizable text-end" width="114px">@lang('Credit')<div class="resizer"></div></th>
                                            <th class="resizable text-center">@lang('Narration')<div class="resizer"></div></th>
                                            <th class="resizable text-center" width="90px">@lang('Deal ID')<div class="resizer"></div></th>
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

    $item = @$editDataList[$roid-1];
    $code = @$item->accounts->account_code;
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
                                                        
                                                            @if($showCode)
                                                                    {{ @$editDataList[$roid-1]->accounts->account_name }} ({{ @$editDataList[$roid-1]->accounts->account_code }})
                                                            @else
                                                                   {{ @$editDataList[$roid-1]->accounts->account_name }}
                                                            @endif
                                                        {{-- {{ @$editDataList[$roid-1]->accounts->account_code }} - {{ @$editDataList[$roid-1]->accounts->account_name }} --}}


                                                    </option>
                                                </select>
                                            </td> 
                                            <td>
                                                <input class="form-control text-end" type="text" onblur="formatCurrency(this)" step="any" name="amount_dr[]" autocomplete="off" value="{{ @App\SysHelper::com_curr_format(@$editDataList[$roid-1]->debit_amount,2,'.',',') }}" onchange="update_totals()">
                                            </td>
                                            <td>
                                                <input class="form-control text-end" type="text" onblur="formatCurrency(this)" step="any" name="amount_cr[]" autocomplete="off" value="{{ @App\SysHelper::com_curr_format(@$editDataList[$roid-1]->credit_amount,2,'.',',') }}" onchange="update_totals()">
                                            </td>
                                           
                                            <td><input type="text" class="form-control  text-start" name="remarks[]" value="{{ @$editDataList[$roid-1]->remarks }}"></td>
                                            <td><input type="text" class="form-control text-center" name="dealid[]" value="{{ @$editDataList[$roid-1]->transaction_ref }}"></td>
                                        </tr>
                                    @endfor


                                        <tr>
                                            <td><input type="text" class="form-control text-center" name="sort_id[]" value="{{ $roid }}" /></td>
                                            <td class="noborder">
                                                <select class="form-control" name="account_id[]">
                                                <option value=""></option>
                                            </select>
                                            </td> 
                                            <td>                                                                    
                                                <input class="form-control text-end" type="text" onblur="formatCurrency(this)" step="any" name="amount_dr[]" autocomplete="off" onchange="update_totals()">
                                            </td>
                                            <td>
                                                <input class="form-control text-end" type="text" onblur="formatCurrency(this)" step="any" name="amount_cr[]" autocomplete="off" onchange="update_totals()">
                                            </td>
                                            <td><input type="text" class="form-control  text-start" name="remarks[]"></td>
                                            <td><input type="text" class="form-control text-center" name="dealid[]"></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" scope="col" >Total</th>
                                            <th class="text-end"><label id="dr_total" >{{ @App\SysHelper::com_curr_format(@$editDataList->sum('debit_amount'),2,'.',',') }}</label></th>
                                            <th class="text-end"><label id="cr_total" >{{ @App\SysHelper::com_curr_format(@$editDataList->sum('credit_amount'),2,'.',',') }}</label></th>
                                            <th colspan="2" class="text-end" scope="col" ></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div id="contextMenu">
                                    <button type="button" id="addRow">Add Row</button>
                                    <button type="button" id="deleteRow">Delete Row</button>
                                </div>
                            </div>
                            {{ Form::close() }}


{{-- Models  --}}
<!-- <a ></a> -->
   
                            @include('backEnd.chart-of-accounts.accountadd_form')
                            @include('backEnd.chart-of-accounts.accountsubadd_form')

<div class="modal side-panel modal-draggable fade" id="serialNoModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" style="height: 300px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Narration</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label class="form-label">Narration:</label>
                                <div class="form-group">
                                    <textarea type="text" class="form-control" id="add_remarks" style="height: 150px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" onclick="addSerialNo()">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Add
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    let serialNoModal;
    document.addEventListener("DOMContentLoaded", function() {
        const modalElement = document.getElementById('serialNoModal');
        serialNoModal = new bootstrap.Modal(modalElement);
    });
    let currentSerialInput = null;

    $(document).on('click', 'input[name="remarks[]"]', function() {
        currentSerialInput = $(this);
        $('#add_remarks').val(currentSerialInput.val());
        serialNoModal.show();
    });

    function addSerialNo() {
        if (currentSerialInput) {
            const val = $('#add_remarks').val();
            currentSerialInput.val(val);
            serialNoModal.hide();
            currentSerialInput = null;
        }
    }
</script>

<script>

    $(document).on("change", 'input[name="remarks[]"], input[name="dealid[]"], input[name="amount_dr[]"]', function () {
    let $row = $(this).closest('tr');
    let dealidVal = $row.find('input[name="dealid[]"]').val()?.trim() || "";
    let $remarks = $row.find('input[name="remarks[]"]');

    // If dealid was changed, append it inside remarks (avoid duplicate append)
    if ($(this).is('input[name="dealid[]"]') && dealidVal) {
        let remarksVal = $remarks.val().trim();
        if (!remarksVal.includes("(Deal Id: " + dealidVal + ")")) {
            $remarks.val(remarksVal + (remarksVal ? " " : "") + "(Deal Id: " + dealidVal + ")");
        }
    }

    // Now rebuild narration from all rows
    let narrations = [];
    $('table tr').each(function () {
        let row = $(this);
        let amountCr = row.find('input[name="amount_dr[]"]').val()?.trim() || "";
        let remarks = row.find('input[name="remarks[]"]').val()?.trim() || "";

        if (remarks !== "" && amountCr !== "" && parseFloat(amountCr) !== 0) {
            if (!narrations.includes(remarks)) {
                narrations.push(remarks);
            }
        }
    });

    $("#narration").val(narrations.join(", "));
});


        function activate_button() {        
        $("#addCtrlJournalVoucherAdjestEdit").prop("disabled", false);
        }
    
  // Run cr_popup_fun only once (no repeated bindings)
  $(document).on('keypress', 'input[name="amount_dr[]"], input[name="amount_cr[]"]', function (e) {
    var key = e.which;
    if (key === 13) {
        var $row = $(this).closest('tr'); // current row

        var br_account_id = $row.find('[name="account_id[]"]').val();

        $('#br_account_id').val(br_account_id);

        var acc_name = $row.find('[name="account_id[]"] option:selected').text();
        var acc_type = 0;

        if (acc_name.indexOf('SUP') > -1) {
            $('#account_type').val('SUP');
            $('#add_url').val('payables-outstanding-store-temp');
            $('#delete_url').val('payables-outstanding-store-temp-delete');
            acc_type = 1;
            if ($row.find('input[name="amount_cr[]"]').val() > 0) {
                acc_type = 4;
            }
        }

        if (acc_name.indexOf('CUS') > -1) {
            $('#account_type').val('CUS');
            $('#add_url').val('receivable-outstanding-store-temp');
            $('#delete_url').val('receivable-outstanding-store-temp-delete');
            acc_type = 2;
            if ($row.find('input[name="amount_dr[]"]').val() > 0) {
                acc_type = 3;
            }
        }

        var br_account = $row.find('input[name="amount_cr[]"]').val();
        if (br_account === "") {
            br_account = $row.find('input[name="amount_dr[]"]').val();
        }

        $('#bi_cheque_amount').val(br_account).focus();

        if (acc_type == 1 || acc_type == 2) {
            $("#addCtrlJournalVoucherAdjestEdit").click().prop("disabled", true);
        }
        if (acc_type == 3) {
            $("#btnModalAdjustment").click();
            $('#adj_siv_amount').val($row.find('input[name="amount_dr[]"]').val());
            $('#adj_account_id').val(br_account_id);
            $('#adj_account_id_amount').val($row.find('input[name="amount_dr[]"]').val());
            get_customer_adjustment_list(br_account_id);
        }
        if (acc_type == 4) {
            $("#btnModalPaymentAdjustment").click();
            $('#adj_siv_amount').val($row.find('input[name="amount_cr[]"]').val());
            $('#adj_account_id').val(br_account_id);
            $('#adj_account_id_amount').val($row.find('input[name="amount_cr[]"]').val());
            get_supplier_adjustment_list(br_account_id);
        }

        return false;
    }
});

  // Prevent form submission on Enter for all fields EXCEPT amount[]
  $('#journalvoucher-create-form').on('keypress', function (e) {
    if (e.which === 13 && !$(e.target).is('input[name="amount_dr[]"]') && !$(e.target).is('input[name="amount_cr[]"]')) {
      e.preventDefault();
      return false;
    }
  });
</script>


<script>
   update_totals();

function update_totals() {
    let total_amount_dr = 0;
    let total_amount_cr = 0;

    const decimal_point = @json(session('logged_session_data.decimal_point'));

    $('#myTable tbody tr').each(function () {
        const $row = $(this);        
        total_amount_dr += parseFloat(($row.find('input[name="amount_dr[]"]').val() || '0').replace(/,/g, '')) || 0;
        total_amount_cr += parseFloat(($row.find('input[name="amount_cr[]"]').val() || '0').replace(/,/g, '')) || 0;
    });

    $('#dr_total').text(formatAmount(total_amount_dr));
    $('#cr_total').text(formatAmount(total_amount_cr));
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

// when user types a debit amount, clear the credit column in same row (and vice versa)
$(document).on('input', 'input[name="amount_dr[]"]', function() {
    var $row = $(this).closest('tr');
    if ($.trim($(this).val()) !== '') {
        $row.find('input[name="amount_cr[]"]').val('0');
    }
    update_totals();
});

$(document).on('input', 'input[name="amount_cr[]"]', function() {
    var $row = $(this).closest('tr');
    if ($.trim($(this).val()) !== '') {
        $row.find('input[name="amount_dr[]"]').val('0');
    }
    update_totals();
});
</script>

<script>
$(document).ready(function () {
    
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_account_list_ajax") }}',
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
                $row.find('input[name="amount_dr[]"]').focus();

            // Set values using "name" attribute selectors inside the same row
            
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

});
</script>

    <script>
    /*table row fill based on layout height*/
 window.onload = function () {
    const table = document.getElementById('myTable');
    const tbody = table.querySelector('tbody');

    // If there are no rows, do nothing
    if (tbody.rows.length === 0) return;

    const rowHeight = tbody.rows[0].offsetHeight;
    const pageHeight = window.innerHeight-65;
    const tableTop = table.getBoundingClientRect().top;
    const availableHeight = pageHeight - tableTop;

    let existingRows = tbody.rows.length;
    let totalRows = Math.floor(availableHeight / rowHeight);

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
  };
/*table row fill based on layout height*/
</script>
<script>
function get_customer_adjustment_list(id){
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-receipt-adjustment-list-jv-edit') }}";
        $.ajax({
            url: action,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            //var amt = dataResult['data'][i].amount - dataResult['data'][i].adj_amount;
                            var amt = (dataResult['data'][i].amount - dataResult['data'][i].adj_amount).toFixed(@json(session('logged_session_data.decimal_point'))).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                            getSelectedRows +="<tr>\
                                <td class='border'>"+dataResult['data'][i].doc_date+"</td>\
                                <td class='border'>"+dataResult['data'][i].doc_number+"</td>\
                                <td class='border text-right'>"+amt+"</td>\
                                <td class='border'>"+dataResult['data'][i].remarks+"</td>\
                                <td class='border text-right'><input type='text' name='set_amt[]' id='set_amt_"+dataResult['data'][i].doc_number+"' class='form-control text-right' onclick=set_adjust('"+dataResult['data'][i].amount+"','"+dataResult['data'][i].doc_number+"') value="+dataResult['data'][i].removed_amount+" /></td>\
                                <input type='hidden' name='receiptno[]' value='"+dataResult['data'][i].doc_number+"'/>\
                                <input type='hidden' name='set_amt_act[]' value='"+amt+"'/>\
                                </tr>";
                        }

                        $('#table_jv_receipt_list tbody').empty();
                        $("#table_jv_receipt_list tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#table_jv_receipt_list tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function get_supplier_adjustment_list(id){
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('get-payment-adjustment-list-jv-edit') }}";
        $.ajax({
            url: action,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            var amt = (dataResult['data'][i].amount - dataResult['data'][i].adj_amount).toFixed(@json(session('logged_session_data.decimal_point'))).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                            getSelectedRows +="<tr>\
                                <td class='border'>"+dataResult['data'][i].doc_date+"</td>\
                                <td class='border'>"+dataResult['data'][i].doc_number+"</td>\
                                <td class='border text-right'>"+amt+"</td>\
                                <td class='border'>"+dataResult['data'][i].remarks+"</td>\
                                <td class='border text-right'><input type='text' name='set_amt[]' id='set_amt_"+dataResult['data'][i].doc_number+"' class='form-control text-right' onclick=set_adjust('"+dataResult['data'][i].amount+"','"+dataResult['data'][i].doc_number+"') value="+dataResult['data'][i].removed_amount+" /></td>\
                                <input type='hidden' name='paymentno[]' value='"+dataResult['data'][i].doc_number+"'/>\
                                <input type='hidden' name='set_amt_act[]' value='"+amt+"'/>\
                                </tr>";
                                
                        }

                        $('#table_jv_payment_list tbody').empty();
                        $("#table_jv_payment_list tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#table_jv_payment_list tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function update_adjustment(){
        $("#loading_bg").css("display", "block");

        var action = "{{ URL::to('update-receipt-adjustment-list-jv') }}";

        const set_amt = [];
        document.querySelectorAll('input[name="set_amt[]"]').forEach(input => {
            set_amt.push(input.value);
        });
        const receiptno = [];
        document.querySelectorAll('input[name="receiptno[]"]').forEach(input => {
            receiptno.push(input.value);
        });
        const set_amt_act = [];
        document.querySelectorAll('input[name="set_amt_act[]"]').forEach(input => {
            set_amt_act.push(input.value);
        });
        $.ajax({
            url: action,
            type: "POST",
             data: {
                _token: '{{ csrf_token() }}',
                jv_id:$('#doc_number').val(),
                set_amt: set_amt,
                receiptno: receiptno,
                set_amt_act: set_amt_act,
                account_id: $('#adj_account_id').val(),
                account_amount: $('#adj_account_id_amount').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                if(dataResult == "SUCCESS"){
                    alert("Adjustment Added Successfully");
                }
                else{
                    alert("Error: "+dataResult);
                }
                
            }
        });
        $('#ModalAdjustmentClose').click();
        $("#loading_bg").css("display", "none");
    }
    function update_payment_adjustment(){
        $("#loading_bg").css("display", "block");

        var action = "{{ URL::to('update-payment-adjustment-list-jv') }}";

        const set_amt = [];
        document.querySelectorAll('input[name="set_amt[]"]').forEach(input => {
            set_amt.push(input.value);
        });
        const paymentno = [];
        document.querySelectorAll('input[name="paymentno[]"]').forEach(input => {
            paymentno.push(input.value);
        });
        const set_amt_act = [];
        document.querySelectorAll('input[name="set_amt_act[]"]').forEach(input => {
            set_amt_act.push(input.value);
        });
        $.ajax({
            url: action,
            type: "POST",
             data: {
                _token: '{{ csrf_token() }}',
                jv_id:$('#doc_number').val(),
                set_amt: set_amt,
                paymentno: paymentno,
                set_amt_act: set_amt_act,
                account_id: $('#adj_account_id').val(),
                account_amount: $('#adj_account_id_amount').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                if(dataResult == "SUCCESS"){
                    alert("Adjustment Added Successfully");
                }
                else{
                    alert("Error: "+dataResult);
                }
                
            }
        });
        $('#ModalPaymentAdjustmentClose').click();
        $("#loading_bg").css("display", "none");
    }
</script>
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


<button type="button" data-bs-toggle="modal" data-bs-target="#cr_popup_win" id="addCtrlJournalVoucherAdjestEdit" hidden></button>
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'journalvoucher-get-adjestment-update', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'journalvoucher-get-adjestment-update']) }}
<div class="modal side-panel modal-draggable fade" id="cr_popup_win" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header m-0 p-0">
                    <h4 class="modal-title">Bill Wise Selection</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="activate_button()"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <input type="hidden" id="br_account_id" name="br_account_id">
                    <input type="hidden" id="br_account_id_amount" name="br_account_id_amount">
                    <input type="hidden" name="bi_currency2" value="{{ $editData->currency }}" />
                    <input type="hidden" name="doc_number2" value="{{ $editData->doc_number }}" />                        
                    <input type="hidden" name="transaction_type2" value="@if($editData->mode==1) cashreceipt @else bankreceipt @endif" />
                    <input type="hidden" name="add_url" id="add_url" value="" />
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label id="txt_bi_cheque_amount">@lang('Cash Amount') <span>*</span></label>
                                    <input class="primary-input form-control" type="text" id="bi_cheque_amount" name="bi_cheque_amount"  value="0" >
                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label>  @lang('Amount Adjusted') <span>*</span> </label>
                                    <input class="primary-input form-control" type="text" id="bi_amount_adjusted" name="bi_amount_adjusted" value="0" >
                                    
                                    <input type="hidden" id="bi_balance_adjest" value="">

                                </div>
                            </div>
                            <div class="col-lg-4 mb-20">
                                <div class="input-effect">
                                    <label>  @lang('Balance to Adjust') <span>*</span> </label>
                                    <input class="primary-input form-control" type="text" id="bi_extra_amount" name="bi_extra_amount" value="0" >                                    
                                    
                                    <input type="hidden" id="bi_balance_to_adjust" name="bi_balance_to_adjust" value="0" >
                                    
                                </div>
                            </div>
                        </div>

                   
                           
                                <div class="equipment comon-status  mt-4">
                                        <table class="table table-hover form-item-table" cellspacing="0" width="100%" id="crListBankBookAdjest">
                                            <thead>
                                                <tr>
                                                    <th style="width:100px;">@lang('Doc No')</th>
                                                    <th style="width:100px;">@lang('Doc Date')</th>
                                                    <th style="width:100px;">@lang('LPO NO')</th>
                                                    <th style="width:100px;">@lang('Total')</th>
                                                    <th style="width:100px;">@lang('Paid')</th>
                                                    <th style="width:100px;">@lang('Balance')</th>
                                                    <th style="width:100px;">@lang('Adjustment')</th>
                                                    <th style="width:100px;">@lang('Narration')</th>
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
                                                    <th><label id="footer_total" /></th>
                                                    <th><label id="footer_paid" /></th>
                                                    <th><label id="footer_balance" /></th>
                                                    <th><label id="footer_adjustment" /></th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                </div>
                           

                        <script>

                            // function get_set_amount(id)
                            // {
                            //     var form_amt = Number($('#bi_cheque_amount').val());
                            //     var bal_amt = Number($('#bi_balance_'+id).val());

                            //     var bi_amount = Number($('#bi_amount_'+id).val());

                            //     var adjested_sum = 0;
                            //     $(".tot_amt").each(function () {
                            //         adjested_sum += +$(this).val();
                            //     });
                            //     $('#bi_amount_adjusted').val(Number(adjested_sum));
                            //     $('#bi_balance_adjest').val(Number(form_amt)-Number(adjested_sum));                                

                            //     if($('#bi_balance_adjest').val()==""){
                            //         $('#bi_balance_adjest').val(form_amt);
                            //     }
                            //     var amt = Number($('#bi_balance_adjest').val());
                            //     var pending = Number($('#bi_balance_to_adjust').val());

                            //     if(amt > 0 && amt != "" && pending > 0){
                            //         if(amt == bal_amt) {
                            //             //alert("1.if(amt == bal_amt)");

                            //             $('#bi_amount_'+id).val(amt);
                            //             var adjusted = Number($('#bi_amount_adjusted').val());
                            //             var balance_adjust = Number($('#bi_balance_to_adjust').val());
                            //             $('#bi_amount_adjusted').val(adjusted+amt);
                            //             $('#bi_balance_to_adjust').val(balance_adjust-(adjusted+amt));
                            //             var extra = Number($('#bi_extra_amount').val());

                            //             if(form_amt >= (adjusted+amt))
                            //             {
                            //                 $('#bi_extra_amount').val(form_amt - (adjusted+amt));
                            //             }
                            //             else{
                            //                 $('#bi_extra_amount').val((adjusted+amt) - form_amt);
                            //             }

                            //             $('#bi_balance_adjest').val(0);
                            //         } else if(amt > bal_amt) {
                            //             //alert("2.else if(amt > bal_amt)");

                            //             $('#bi_amount_'+id).val(bal_amt);
                            //             var adjusted = Number($('#bi_amount_adjusted').val());
                            //             var balance_adjust = Number($('#bi_balance_to_adjust').val());
                            //             $('#bi_amount_adjusted').val(adjusted+bal_amt);
                            //             $('#bi_balance_to_adjust').val(balance_adjust-bal_amt);
                            //             var extra = Number($('#bi_extra_amount').val());
                                        
                            //             if(form_amt >= (adjusted+bal_amt))
                            //             {
                            //                 $('#bi_extra_amount').val(form_amt - (adjusted+bal_amt));
                            //             }
                            //             else{
                            //                 $('#bi_extra_amount').val((adjusted+bal_amt) - form_amt);
                            //             }

                            //             if(amt >= bal_amt){
                            //                 $('#bi_balance_adjest').val(amt - bal_amt);
                            //             } else {
                            //                 $('#bi_balance_adjest').val(bal_amt - amt);
                            //             }
                            //         } else if(amt < bal_amt) {
                            //             //alert("3.else if(amt < bal_amt)");

                            //             $('#bi_amount_'+id).val(amt);
                            //             var adjusted = Number($('#bi_amount_adjusted').val());
                            //             var balance_adjust = Number($('#bi_balance_to_adjust').val());
                            //             $('#bi_amount_adjusted').val(adjusted+amt);
                            //             $('#bi_balance_to_adjust').val(balance_adjust- amt);
                            //             var extra = Number($('#bi_extra_amount').val());
                                        
                            //             if(form_amt >= (adjusted+amt))
                            //             {
                            //                 $('#bi_extra_amount').val(form_amt - (adjusted+amt));
                            //             }
                            //             else{
                            //                 $('#bi_extra_amount').val((adjusted+amt) - form_amt);
                            //             }
                                        
                            //             $('#bi_balance_adjest').val(0);
                            //         }
                            //         else {
                            //             //alert("4.else");

                            //             $('#bi_amount_'+id).val(0);
                            //             $('#bi_balance_adjest').val(0);
                            //         }
                                    
                            //             var num_tot_amt = $('.tot_amt').length;
                            //             var n = 0;
                            //             for(i=1; i<=num_tot_amt; i++){
                            //                 if($('#bi_amount_'+i).val() !=""){
                            //                     n += Number($('#bi_amount_'+i).val()); } }
                            //             $('#footer_adjustment').text(n);
                            //     }
                            // }

                            function get_set_amount(id)
                            {
                                var form_amt = Number($('#bi_cheque_amount').val());
                                var bal_amt = Number($('#bi_balance_'+id).val());

                                var bi_amount = Number($('#bi_amount_'+id).val());

                                var adjested_sum = 0;
                                $(".tot_amt").each(function () {
                                    adjested_sum += +$(this).val();
                                });
                                $('#bi_amount_adjusted').val(Number(adjested_sum));
                                $('#bi_balance_adjest').val(Number(form_amt)-Number(adjested_sum));                                

                                if($('#bi_balance_adjest').val()==""){
                                    $('#bi_balance_adjest').val(form_amt);
                                }
                                var amt = Number($('#bi_balance_adjest').val());
                                var pending = Number($('#bi_balance_to_adjust').val());

                                if(amt > 0 && amt != "" && pending > 0){
                                    if(amt == bal_amt) {
                                        //alert("1.if(amt == bal_amt)");

                                        $('#bi_amount_'+id).val(amt);
                                        var adjusted = Number($('#bi_amount_adjusted').val());
                                        var balance_adjust = Number($('#bi_balance_to_adjust').val());
                                        $('#bi_amount_adjusted').val(adjusted+amt);
                                        $('#bi_balance_to_adjust').val(balance_adjust-(adjusted+amt));
                                        var extra = Number($('#bi_extra_amount').val());

                                        if(form_amt >= (adjusted+amt))
                                        {
                                            $('#bi_extra_amount').val(form_amt - (adjusted+amt));
                                        }
                                        else{
                                            $('#bi_extra_amount').val((adjusted+amt) - form_amt);
                                        }

                                        $('#bi_balance_adjest').val(0);
                                    } else if(amt > bal_amt) {
                                        //alert("2.else if(amt > bal_amt)");

                                        $('#bi_amount_'+id).val(bal_amt);
                                        var adjusted = Number($('#bi_amount_adjusted').val());
                                        var balance_adjust = Number($('#bi_balance_to_adjust').val());
                                        $('#bi_amount_adjusted').val(adjusted+bal_amt);
                                        $('#bi_balance_to_adjust').val(balance_adjust-bal_amt);
                                        var extra = Number($('#bi_extra_amount').val());
                                        
                                        if(form_amt >= (adjusted+bal_amt))
                                        {
                                            $('#bi_extra_amount').val(form_amt - (adjusted+bal_amt));
                                        }
                                        else{
                                            $('#bi_extra_amount').val((adjusted+bal_amt) - form_amt);
                                        }

                                        if(amt >= bal_amt){
                                            $('#bi_balance_adjest').val(amt - bal_amt);
                                        } else {
                                            $('#bi_balance_adjest').val(bal_amt - amt);
                                        }
                                    } else if(amt < bal_amt) {
                                        //alert("3.else if(amt < bal_amt)");

                                        $('#bi_amount_'+id).val(amt);
                                        var adjusted = Number($('#bi_amount_adjusted').val());
                                        var balance_adjust = Number($('#bi_balance_to_adjust').val());
                                        $('#bi_amount_adjusted').val(adjusted+amt);
                                        $('#bi_balance_to_adjust').val(balance_adjust- amt);
                                        var extra = Number($('#bi_extra_amount').val());
                                        
                                        if(form_amt >= (adjusted+amt))
                                        {
                                            $('#bi_extra_amount').val(form_amt - (adjusted+amt));
                                        }
                                        else{
                                            $('#bi_extra_amount').val((adjusted+amt) - form_amt);
                                        }
                                        
                                        $('#bi_balance_adjest').val(0);
                                    }
                                    else {
                                        //alert("4.else");

                                        $('#bi_amount_'+id).val(0);
                                        $('#bi_balance_adjest').val(0);
                                    }
                                    
                                        var num_tot_amt = $('.tot_amt').length;
                                        var n = 0;
                                        for(i=1; i<=num_tot_amt; i++){
                                            if($('#bi_amount_'+i).val() !=""){
                                                n += Number($('#bi_amount_'+i).val()); } }
                                        $('#footer_adjustment').text(n);
                                }
                            }
                        </script>

                      
                    
                             
                                    <div class="mt-4 mb-3 d-flex justify-content-center">
                                        
                                    <button class="btn btn-light add-btn ms-2" type="submit" value="Save" onclick="popup_form_submit()">
                                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                                    </button>
                                        <script>
                                            function popup_form_submit(){
                                                $("#loading_bg").css("display", "block");
                                            }
                                        </script>
                                    </div>
                              
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{ Form::close() }}


<button id="btnModalAdjustment" data-bs-toggle="modal" data-bs-target="#ModalAdjustment" hidden></button>
<button id="btnPaymentModalAdjustment" data-bs-toggle="modal" data-bs-target="#ModalPaymentAdjustment" hidden></button>
<input type="hidden" id="adj_siv_amount" name="adj_siv_amount" value="0">
<input type="hidden" id="adj_siv_amount_actual" name="adj_siv_amount_actual" value="0"/>
<input type="hidden" id="adj_siv_amount_adjusted" name="adj_siv_amount_adjusted" value="0"/>
<input type="hidden" id="adj_account_id">
<input type="hidden" id="adj_account_id_amount">
<!-- Modal Adjustment-->
<div class="modal side-panel modal-draggable fade" id="ModalAdjustment" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Customer Unadjusted List</h4>
						<button type="button" id="ModalAdjustmentClose" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-update-adjustment', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover form-item-table" id="table_jv_receipt_list" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="border">Doc Date</th>
                                        <th class="border">Receipt No</th>
                                        <th class="border text-right">Amount</th>
                                        <th class="border">Remarks</th>
                                        <th class="border text-right">Adjusement</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot> 
                                    <tr>
                                        <th colspan="5" class="border text-right">
                        <button type="button" class="btn btn-light add-btn ms-2" onclick="update_adjustment()">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Add Adjusement
						</button>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Adjustment-->
<!-- Modal Adjustment-->
<div class="modal side-panel modal-draggable fade" id="ModalPaymentAdjustment" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Supplier Unadjusted List</h4>
						<button type="button" id="ModalPaymentAdjustmentClose" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-update-adjustment', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover form-item-table" id="table_jv_payment_list" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="border">Doc Date</th>
                                        <th class="border">Payment No</th>
                                        <th class="border text-right">Amount</th>
                                        <th class="border">Remarks</th>
                                        <th class="border text-right">Adjusement</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot> 
                                    <tr>
                                        <th colspan="5" class="border text-right">
                        <button type="button" class="btn btn-light add-btn ms-2" type="submit" onclick="update_payment_adjustment()">
							<i class="ico icon-outline-bookmark-opened text-success"></i> Add Adjusement
						</button>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Adjustment-->



    <div class="modal modal-draggable fade" id="attachment_popup_win" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="height: 464px !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Attachments - <label class="font-weight-600" id="att_cust_name"></label></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">

                    <div class="card-body">
                       <div class="row">
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="dynamicslbl">  @lang('Attach File') <span>*</span> </label>
                                <input class="form-control" type="file" id="att_file" name="att_file" onchange="updateDocName()"/>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="dynamicslbl">  @lang('Date') <span>*</span> </label>
                                <input class="form-control date-picker" type="text" id="att_date" name="att_date" value="{{ date('d/m/Y') }}" autocomplete="off"/>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-20">
                            <div class="input-effect">
                                <label class="dynamicslbl">  @lang('File Name') <span>*</span> </label>
                                <input class="form-control" type="text" id="doc_name" name="doc_name" value=""/>
                            </div>
                        </div>
                        <script>
                            function updateDocName() {
                                var fileInput = document.getElementById('att_file');
                                var fileName = fileInput.files[0] ? fileInput.files[0].name : '';
                                var fileNameWithoutExtension = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
                                document.getElementById('doc_name').value = fileNameWithoutExtension;
                            }
                        </script>
                    </div>

                        <div class="row">
                            <div class="col-md-12 mt-2">
                                <table id="att-table" class="table table-hover" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">No</th>
                                            <th style="width: 30%;">Date</th>
                                            <th style="width: 50%;">Attachment</th>
                                            <th style="width: 10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <br />


                    </div>

                </div>
            </div>
            <div class="modal-footer">

                <input type="hidden" id="srl_id" />

                <button type="button" onclick="add_attachment()" class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

    

<script>
    function add_attachment(){
        console.log('add_attachment');
        
        if($('#att_file').val()==""){ 
            alert('Please select a file to upload');
            $('#att_file').focus(); 
            return false; 
        }
        if($('#att_date').val()==""){ 
            alert('Please select a date');
            $('#att_date').focus(); 
            return false; 
        }
        if($('#doc_name').val()==""){ 
            alert('Please enter a file name');
            $('#doc_name').focus(); 
            return false; 
        }

        $("#loading_bg").css("display", "block");

        var action = "{{ URL::to('add-journal-voucher-attachment') }}";
        
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('doc_id', $('#jv_id').val());
        formData.append('att_date', $('#att_date').val());
        formData.append('att_file', $('#att_file')[0].files[0]); 
        formData.append('doc_name', $('#doc_name').val());

        console.log('Sending data:', {
            doc_id: $('#jv_id').val(),
            att_date: $('#att_date').val(),
            doc_name: $('#doc_name').val()
        });

        $.ajax({
            url: action,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(dataResult) {
                console.log('Response:', dataResult);
                var data = typeof dataResult === 'string' ? JSON.parse(dataResult) : dataResult;
                var len = 0;
                var getSelectedRows="";
                
                if(data['data'] != null){
                    len = data['data'].length;
                }
                
                if(len > 0){
                    for(var i=0; i<len; i++){
                        getSelectedRows +="<tr>\
                            <td>"+ Number(i+1) +"</td>\
                            <td>"+get_format_date(data['data'][i].doc_date)+"</td>\
                            <td><a href='../../"+data['data'][i].doc_file+"' target='_blank'>"+data['data'][i].doc_name+"</a></td>\
                            <td><a onclick='delete_attachment("+data['data'][i].id+")' class='btn-sm btn-light'><i class='ico icon-outline-trash-bin-minimalistic title-15 text-dark' aria-hidden='true'></i></a></td>\
                            </tr>";
                    }
                    $('#att_file').val('');
                    $('#doc_name').val('');
                    $('#att-table tbody').empty();
                    $("#att-table tbody").append(getSelectedRows); 
                    console.log('Attachment added successfully');
                    toastr.success('Attachment added successfully', 'Success');
                    // close the modal after successful upload
                    $('#attachment_popup_win').modal('hide');
                } else {
                    $('#att-table tbody').empty();
                }
                $("#loading_bg").css("display", "none");
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:");
                console.error("Status: " + status);
                console.error("Error: " + error);
                console.error("Response Text: " + xhr.responseText);
                $("#loading_bg").css("display", "none");
                alert("Something went wrong while processing your request. Please try again.");
            }
        });
    }
    function view_attachment(){
        $("#loading_bg").css("display", "block");
        console.log($('#doc_number').val())
        $('#att_cust_name').text(" " + $('#doc_number').val());

        var action = "{{ URL::to('view-journal-voucher-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                doc_id : $('#jv_id').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            getSelectedRows +="<tr>\
                                <td>"+ Number(i+1) +"</td>\
                                <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-light'><i class='ico  title-15 icon-outline-trash-bin-minimalistic text-dark' aria-hidden='true'></i></a></td>\
                                </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows); 
                    }
                    else{
                        $('#att-table tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
    function delete_attachment(id){
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('delete-journal-voucher-attachment') }}";
        $.ajax({
            url: action,
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id : id,
                doc_id : $('#jv_id').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var getSelectedRows="";
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            getSelectedRows +="<tr>\
                                <td>"+ Number(i+1) +"</td>\
                                <td>"+get_format_date(dataResult['data'][i].doc_date)+"</td>\
                                <td><a href='../../"+dataResult['data'][i].doc_file+"' target='_blank'>"+dataResult['data'][i].doc_name+"</a></td>\
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-light'><i class='ico  title-15 icon-outline-trash-bin-minimalistic text-dark' aria-hidden='true'></i></a></td>\
                                </tr>";
                        }
                        $('#att_file').val('');
                        $('#doc_name').val('');
                        $('#att-table tbody').empty();
                        $("#att-table tbody").append(getSelectedRows); 
                        toastr.success('Attachment deleted successfully', 'Success');

                    }
                    else{
                        $('#att-table tbody').empty();
                    }
            }
        });
        $("#loading_bg").css("display", "none");
    }
</script>






<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
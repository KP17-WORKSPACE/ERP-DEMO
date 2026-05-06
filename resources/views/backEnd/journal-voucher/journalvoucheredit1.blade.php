@extends('backEnd.masterpage')
@section('mainContent')
    
    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Journal Voucher Edit</h2>
                <span class="page-label">Home - Journal Voucher</span>
            </div>
            <div>
                <a data-modal-size="modal-md" data-target="#attachment_popup_win" data-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="fa fa-plus"></i> Attachment</a>
                <a href="{{ url('journalvoucher-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                <a href="{{ url('journalvoucher/'.$editData->id.'/view') }}" type="button" class="btn btn-warning"><i class="fa fa-list"></i> View</a>
                <!-- Input with Search -->
                <div style="float: left; margin-right:5px; position: relative; width: 200px;">
                    <input type="text" id="quick_search_doc_number" placeholder="Doc Number" class="form-control pr-4" /> 
                    <span style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #aaa; pointer-events: none;">
                    <i class="fas fa-search"></i>
                    </span>
                </div>
                <script>
                    const baseUrl = "{{ url('get-edit-url-journalvoucher') }}";                
                    document.getElementById('quick_search_doc_number').addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            var val = this.value.trim();
                            if (val !== '') {                                
                                window.location.href = baseUrl + '/' + val;
                            }
                        }
                    });
                </script>
                <!-- Input with Search -->
                <a href="{{ url('journalvoucher') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
    
            </div>
        </div>
        <div class="card shadow mb-4 p-4">
            @if (isset($editData))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'journalvoucher-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'journalvoucher-create-form']) }}
            <input type="hidden" value="{{ @$editData->id }}" name="cust_id" id="jv_id">
        @else
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'journalvoucher-store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'journalvoucher-create-form']) }}
        @endif

        <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
        <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{ date('Y-m-d') }}">
        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    <div class="row">
                        <div class="col-lg-3 mb-4">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl"> @lang('Doc Number') <span>*</span> </label>
                                        <input class="form-control" type="text" id="doc_number" name="doc_number" readonly
                                            value="{{ $editData->doc_number }}">
                                        <span class="focus-border"></span>
                                        @if ($errors->has('doc_number'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('doc_number') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-auto" style="display: none;">
                                    <button class="" type="button" id="cr_search_btn"
                                        onclick="fn_cr_search_btn()">
                                        <i class="ti-search" id="end-date-icon"></i>
                                    </button>
                                    <script>
                                        function fn_cr_search_btn() {
                                            var cr_search = $('#doc_number').val();
                                            cr_search = cr_search.replace(/\D/g, '');
                                            var url1 = $('#url').val();
                                            window.location.href = url1 + '/' + 'journalvoucher/' + cr_search;
                                        }
                                    </script>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Doc Date')</label>
                                        @php
                                            $value = date('Y-m-d');
                                            if (isset($editData) && !empty($editData->doc_date)) {
                                                @$value = date('Y-m-d', strtotime(@$editData->doc_date));
                                            } else {
                                                if (!empty(old('doc_date'))) {
                                                    @$value = old('doc_date');
                                                } else {
                                                    @$value = date('Y-m-d');
                                                }
                                            }
                                        @endphp
                                        <input class="form-control" id="doc_date" type="date" autocomplete="off"
                                            name="doc_date" value="{{ @$value }}">
                                        <span class="focus-border"></span>
                                        @if ($errors->has('doc_date'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('doc_date') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <label class="txtlbl">@lang('Currency')</label>
                            <select
                                class="form-control"
                                name="currency" id="currency">
                                @foreach ($currency as $value)
                                    <option value="{{ @$value->id }}"
                                        {{ isset($editData) ? (!empty(@$editData->currency) ? (@$editData->currency == @$value->id ? 'selected' : '') : '') : '' }}>
                                        {{ @$value->code }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('currency'))
                                <span class="invalid-feedback invalid-select" role="alert">
                                    <strong>{{ $errors->first('currency') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-lg-3 mb-4">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Created') @lang('By')<span>*</span></label>
                                <input
                                    class="form-control"
                                    type="text" name="createdby" autocomplete="off" id="created_by"
                                    value="{{ isset($editData) ? (!empty(@$editData->created_by) ? @$editData->createdby->full_name : old('created_by')) : Auth::user()->full_name }}"
                                    readonly>
                                <span class="focus-border"></span>
                                @if ($errors->has('createdby'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('createdby') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-lg-8 mb-4">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Narration') <span></span></label>
                                <input
                                    class="form-control"
                                    type="text" name="narration" autocomplete="off"
                                    value="{{ isset($editData) ? (!empty(@$editData->narration) ? @$editData->narration : old('narration')) : old('narration') }}"
                                    id="narration" required>
                                <span class="focus-border"></span>
                                @if ($errors->has('narration'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('narration') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        {{-- <div class="col-lg-2 mb-4">
                            <div class="input-effect">
                                <label>@lang('Deal ID')<span>*</span></label>
                                <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" value="{{ @App\SysHelper::get_code_from_dealid($editData->deal_id) }}">
                            </div>
                        </div>                         --}}
                        <input type="hidden" name="deal_id" id="deal_id" value="0">
                    </div>




                    <div class="equipment comon-status row mt-4 d-block">
                        <table class="table table-bordered table-striped" id="jv-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width:20px;">@lang('#')</th>
                                    <th style="width:250px;">@lang('Account Name')</th>
                                    <th style="width:100px;">@lang('Debit')</th>
                                    <th style="width:100px;">@lang('Credit')</th>
                                    <th style="width:200px;">@lang('Narration')
                                        {{-- <a class="btn-sm btn-primary float-right pt-0 pb-0" onclick="add_jv_row()"><i class="fa fa-plus-square" aria-hidden="true"></i></a> --}}
                                    </th>
                                    <th style="width:100px;">@lang('deal_id')</th>
                                    <th style="width:20px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $setroid = 8;
                                if (isset($editDataList)) {
                                    if (count($editDataList) > 0) {
                                        $setroid = count($editDataList) + 1;
                                    }
                                }
                                ?>

                                @for ($roid = 1; $roid < $setroid; $roid++)
                                    <tr id="rowone{{ $roid }}">
                                        <td>{{ $roid }}</td>
                                        <td><select class="form-control js-example-basic-single" name="account_id[]"
                                                id="account_id_{{ $roid }}">
                                                <option value=""></option>
                                                @foreach ($accounts as $key => $value)
                                                    <option value="{{ @$value->id }}"
                                                        {{ isset($editDataList[$roid - 1]) ? (!empty(@$editDataList[$roid - 1]->account_id) ? (@$editDataList[$roid - 1]->account_id == @$value->id ? 'selected' : '') : '') : '' }}>
                                                        {{ @$value->account_code }} - {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control text-right" type="number"
                                                id="amount_dr_{{ $roid }}" name="amount_dr[]"
                                                autocomplete="off" min="0" onchange="calc_change({{$roid}})" step="any"
                                                value="{{ @$editDataList[$roid - 1]->debit_amount }}" onkeypress="all_popup_fun({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="form-control text-right" type="number"
                                                id="amount_cr_{{ $roid }}" name="amount_cr[]"
                                                autocomplete="off" min="0" onchange="calc_change({{$roid}})" step="any"
                                                value="{{ @$editDataList[$roid - 1]->credit_amount }}" onkeypress="all_popup_fun({{$roid}})">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text"
                                                id="remarks_{{ $roid }}" name="remarks[]" autocomplete="off"
                                                value="{{ @$editDataList[$roid - 1]->remarks }}">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" id="dealid_{{ $roid }}" name="dealid[]" autocomplete="off" value="{{ @App\SysHelper::get_code_from_dealid(@$editDataList[$roid - 1]->transaction_ref) }}">
                                        </td>
                                        <td><input type="hidden" id="item_jv_id_{{ $roid }}" value="{{ @$editDataList[$roid - 1]->transaction_id }}" />
                                            <input type="hidden" id="item_id_{{ $roid }}" value="{{ @$editDataList[$roid - 1]->id }}" />
                                            <a class="btn-sm btn-danger" onclick="delete_jv_row({{ $roid }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            <a class="btn-sm btn-primary" onclick="add_jv_row({{ $roid }})"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                    
                        {{-- <a data-toggle="modal" data-target="#dealIdModal_{{ $roid }}" id="atag_dealId_{{ $roid }}"></a>
                        <div class="modal fade" id="dealIdModal_{{ $roid }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document" style="width: 200px;">
                            <div class="modal-content">
                              <div class="modal-body">
                                  Deal ID <input name="dealid[]" type="number" class="form-control" value="{{ @App\SysHelper::get_code_from_dealid($editDataList[$roid - 1]->transaction_ref) }}"/>
                                  <a class="btn-sm btn-primary float-right mt-2" data-dismiss="modal" aria-label="Close">
                                      Save
                                    </a>
                              </div>
                            </div>
                          </div>
                        </div> --}}

                        @for ($i = 1; $i < 5; $i++)
                                <tr id="addrow_{{ $roid }}_{{ $i }}" style="display: none;" >
                                    <td></td>
                                    <td><select class="form-control js-example-basic-single" name="account_id[]" id="account_id_{{ $roid }}_{{ $i }}">
                                            <option value=""></option>
                                            @foreach ($accounts as $key => $value)
                                                <option value="{{ @$value->id }}">
                                                    {{ @$value->account_code }} {{ @$value->account_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input class="form-control" type="number" id="amount_dr_{{ $roid }}_{{ $i }}" name="amount_dr[]" step="any" autocomplete="off" min="0" onchange="calc_change('{{ $roid }}_{{ $i }}')" onkeypress="all_popup_fun('{{ $roid }}_{{ $i }}')" value="">
                                    </td>
                                    <td>
                                        <input class="form-control" type="number" id="amount_cr_{{ $roid }}_{{ $i }}" name="amount_cr[]" step="any" autocomplete="off" min="0" onchange="calc_change('{{ $roid }}_{{ $i }}')" onkeypress="all_popup_fun('{{ $roid }}_{{ $i }}')" value="">
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" id="remarks_{{ $roid }}_{{ $i }}" name="remarks[]" autocomplete="off" value="">
                                    </td>
                                    <td>
                                        <input class="form-control" type="text" id="dealid_{{ $roid }}_{{ $i }}" name="dealid[]" autocomplete="off" value="">
                                    </td>
                                </tr>                                
                                {{-- <a data-toggle="modal" data-target="#dealIdModal_{{ $roid }}_{{ $i }}" id="atag_dealId_{{ $roid }}_{{ $i }}"></a>
                                <div class="modal fade" id="dealIdModal_{{ $roid }}_{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document" style="width: 200px;">
                                    <div class="modal-content">
                                    <div class="modal-body">
                                        Deal ID <input name="dealid[]" type="number" class="form-control" />
                                        <a class="btn-sm btn-primary float-right mt-2" data-dismiss="modal" aria-label="Close">
                                            Save
                                            </a>
                                    </div>
                                    </div>
                                </div>
                                </div> --}}
                                @endfor
                                <input type="hidden" id="add_jv_row_count_{{ $roid }}" value="1">


                                @endfor
                                <?php $roid--; ?>
                                <input type="hidden" id="jv-row-count" value="{{ $roid }}">

                                <script>
                                    function add_jv_row(id){
                                        var r = $('#add_jv_row_count_'+id).val();
                                        var r2 = $('#jv-row-count').val();
                                        $('#addrow_'+id+'_'+r).css("display",'');
                                        $('#add_jv_row_count_'+id).val(Number(r)+1);
                                        $('#jv-row-count').val(Number(r2)+1);
                                    }
                                </script>
                                
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right"><b><label id="dr_total">{{ $editDataList->sum('debit_amount') }}</label></b></td>
                                    <td class="text-right"><b><label id="cr_total">{{ $editDataList->sum('credit_amount') }}</label></b></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        
                        <a data-modal-size="modal-md" data-target="#cr_popup_win" id="addCtrlJournalVoucherAdjestEdit" data-toggle="modal" data-backdrop="static" data-keyboard="false"></a>

                        <div style="display: none;">
                            @if (!isset($view))
                                <button type="button" class="primary-btn small fix-gr-bg" id="addRowJV"><span
                                        class="ti-plus pr-2"></span>@lang('lang.item')</button>
                            @endif
                        </div>


                        <script>
                            function fn_addRow(id) {
                                var rownum = document.getElementById('jv-row-count').value;
                                if (id == rownum) {
                                    document.getElementById('jv-row-count').value = (Number(rownum) + Number(1));
                                    document.getElementById('addRowJV').click();
                                }
                            }
        
                            function calc_change(id) {
                                var dr = $('#amount_dr_' + id + '').val();
                                var cr = $('#amount_cr_' + id + '').val();
                                //$('#amount_cr_' + (id + 1) + '').val(dr);
        
                                calc_total();
                            }
        
                            function calc_total() {
                                var countrow = document.getElementById('jv-row-count').value;
                                var t1 = 0,
                                    t2 = 0,
                                    t3 = 0,
                                    t4 = 0,
                                    t5 = 0,
                                    t6 = 0,
                                    t7 = 0;
                                for (var i = 1; i <= countrow; i++) {
                                    t1 += Number($('#amount_dr_' + i).val());
                                    t2 += Number($('#amount_cr_' + i).val());
                                }
                                $('#dr_total').text(t1.toFixed(@json(session('logged_session_data.decimal_point'))));
                                $('#cr_total').text(t2.toFixed(@json(session('logged_session_data.decimal_point'))));
                            }
        
                            function validete_entries(){
                                
                                var total_dr = $('#dr_total').text();
                                var total_cr = $('#cr_total').text();
                                if(total_dr == 0 && total_cr == 0){
                                    alert("No data found!!");
                                    return false;
                                }
                                if(total_dr != total_cr){
                                    alert("Data missmatch!!");
                                    return false;
                                }
                                var countrow = document.getElementById('jv-row-count').value;
                                for(i=1; i <= countrow; i++){
                                    var val_acc = $('#account_id_' + i).val();
                                    var val_dr = $('#amount_dr_' + i).val();
                                    var val_cr = $('#amount_cr_' + i).val();
                                    var val_rem = $('#remarks_' + i).val();
                                    
                                    if(val_acc != "" && (val_dr != "" || val_cr != "") && val_rem != ""){
                                        $('#rowone' + i).removeAttr("style");
                                    }
                                    else if(val_acc == "" && val_dr == "" && val_cr == "" && val_rem == ""){
                                        $('#rowone' + i).removeAttr("style");
                                    }
                                    else{
                                        $('#rowone' + i).css("background-color", "#ffc7c7");
                                        return false;
                                    }
                                }
                                return true;
                            }
        
                            function delete_jv_row(id){
                                if(confirm("Are you sure you want to delete?")==false)
                                {
                                    return false;
                                }
                                $("#loading_bg").css("display", "block");
                                
                                var jv_itm_id = $('#item_id_'+id).val();
                                var jv_doc_number = $('#doc_number').val();
                                var jv_account_id = $('#account_id_'+id).val();

                                var action = "{{ URL::to('journalvoucher-item-delete') }}";
                                $.ajax({
                                    url: action,
                                    type: "POST",
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        id: jv_itm_id,
                                        doc_number: jv_doc_number,
                                        account_id: jv_account_id,
                                    },
                                    cache: false,
                                    success: function(dataResult) {
                                        var dataResult = JSON.parse(dataResult);
                                            if(dataResult['data'] != null){
                                                
                                            }
                                            if(dataResult['data']=="SUCCESS"){
                                                alert("Deleted Successfully!");
                                                location.reload();
                                            }
                                            else{
                                                alert("Something went wrong please try again!");
                                                location.reload();
                                            }
                                    }
                                });
                                $("#loading_bg").css("display", "none");
                            }

                        </script>



                    </div>



                    <!-- Bank Info Details -->


                    <!-- end row -->
                    
                <div class="row mt-40">
                    <div class="col-lg-12 text-left mb-2">
                        @if(count($editDataAdjustmentsR)>0 || count($editDataAdjustmentsP)>0)
                        <b>Receipt Adjusted Items</b>
                            <table class="table table-bordered table-striped" id="br-table" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">@lang('#')</th>
                                        <th style="width:100px;">@lang('Doc Number')</th>
                                        <th style="width:100px;">@lang('Doc Date')</th>
                                        <th style="width:100px;">@lang('LPO NO')</th>
                                        <th style="width:100px;" class="text-right">Total</th>
                                        <th style="width:100px;" class="text-right">Paid</th>
                                        <th style="width:100px;" class="text-right">Balance</th>
                                        <th style="width:100px;" class="text-right">Adjusted</th>
                                        <th style="width:100px;" class="text-right"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if(count($editDataAdjustmentsR)>0)
                                @foreach ($editDataAdjustmentsR as $item)
                                    <tr>
                                        <td>{{ @$loop->iteration }}</td>
                                        <td>{{ @$item->bi_doc_no }}</td>
                                        <td>{{ @$item->bi_doc_date }}</td>
                                        <td>{{ @$item->bi_lpo_no }}</td>
                                        <td class="text-right">{{ @$item->bi_total }}</td>
                                        <td class="text-right">{{ @$item->bi_paid }}</td>
                                        <td class="text-right">{{ @$item->bi_balance_to_adjust }}</td>
                                        <td class="text-right">{{ @$item->bi_amount }}</td>
                                        <td class="text-right"><a class="btn-sm btn-danger" href="{{url('delete-receipt-adjustment/'.$item->id)}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                    </tr>
                                @endforeach
                                @endif
                                @if(count($editDataAdjustmentsP)>0)
                                 @foreach ($editDataAdjustmentsP as $item)
                                    <tr>
                                        <td>{{ @$loop->iteration }}</td>
                                        <td>{{ @$item->bi_doc_no }}</td>
                                        <td>{{ @$item->bi_doc_date }}</td>
                                        <td>{{ @$item->bi_lpo_no }}</td>
                                        <td class="text-right">{{ @$item->bi_total }}</td>
                                        <td class="text-right">{{ @$item->bi_paid }}</td>
                                        <td class="text-right">{{ @$item->bi_balance_to_adjust }}</td>
                                        <td class="text-right">{{ @$item->bi_amount }}</td>
                                        <td class="text-right"><a class="btn-sm btn-danger" href="{{url('delete-payment-adjustment/'.$item->id)}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                                    </tr>
                                @endforeach
                                @endif
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>



                    <div class="row mt-40">
                        <div class="col-lg-12 text-center">
                            @if (!isset($view))
                                <button class="btn btn-primary" id="btnSubmit" onclick="return validete_entries()">
                                    <span class="ti-check"></span>
                                    @if (isset($editData))
                                        @lang('lang.update')
                                    @else
                                        @lang('lang.add')
                                    @endif @lang('Journal Voucher')
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{ Form::close() }}
        </div>
    </div>

    
    <input type="hidden" id="account_type" value="0" />


    <section class="admin-visitor-area">
        <div class="container-fluid p-0">



            
        </div>
    </section>


    <script>
        function activate_button() {        
        $("#addCtrlJournalVoucherAdjestEdit").prop("disabled", false);
        }

        function all_popup_fun(id){
            $('#amount_dr_'+id+', #amount_cr_'+id+'').keypress(function(e) {
                var key = e.which;            
                if(key === 13)  // the enter key code
                {
                    var br_account_id = $('#account_id_'+id+'').val();                
                    $('#br_account_id').val(br_account_id);
                    var acc_name =  $('#account_id_'+id+' option:selected').text();
                    var acc_type=0;
                    if(acc_name.indexOf('SUP') > -1) {
                        $('#account_type').val('SUP');
                        $('#add_url').val('journalvoucher-get-adjestment-list-edit-sup');
                        //$('#delete_url').val('payables-outstanding-store-temp-delete');
                        acc_type = 1;
                        if($('#amount_cr_'+id+'').val() > 0){
                            acc_type = 4;
                        }
                    }
                    if(acc_name.indexOf('CUS') > -1) {
                        $('#account_type').val('CUS');
                        $('#add_url').val('journalvoucher-get-adjestment-list-edit-cus');
                        //$('#delete_url').val('receivable-outstanding-store-temp-delete');
                        acc_type = 2;
                        if($('#amount_dr_'+id+'').val() > 0){
                            acc_type = 3;
                        }
                    }
                    if(acc_name.indexOf('ACC') > -1 || acc_name.indexOf('SAC') > -1) {
                        $('#atag_dealId_'+id).click();
                    }
                    
                    var br_account = $('#amount_cr_'+id+'').val();
                    if(br_account=="" || br_account==0 || br_account==0.00){
                        br_account = $('#amount_dr_'+id+'').val();
                    }
                    $('#bi_cheque_amount').val(br_account).focus();
                    $('#bi_cheque_amount').focus();
                    if(acc_type == 1 || acc_type == 2){
                        $("#addCtrlJournalVoucherAdjestEdit").click();
                        $("#addCtrlJournalVoucherAdjestEdit").prop("disabled", true);
                    }
                    if(acc_type == 3){
                        $("#btnModalAdjustment").click();
                        $('#adj_siv_amount').val($('#amount_dr_'+id+'').val());
                        $('#adj_account_id').val(br_account_id);
                        $('#adj_account_id_amount').val($('#amount_dr_'+id+'').val());
                        get_customer_adjustment_list(br_account_id);
                    }
                    if(acc_type == 4){
                        $("#btnPaymentModalAdjustment").click();
                        $('#adj_siv_amount').val($('#amount_cr_'+id+'').val());
                        $('#adj_account_id').val(br_account_id);
                        $('#adj_account_id_amount').val($('#amount_cr_'+id+'').val());
                        get_supplier_adjustment_list(br_account_id);
                        //$("#btnModalAdjustment").prop("disabled", true);
                    }
                    return false;                
                }
            });
            return false;
        }
        {{--  function all_popup_fun(id){
            $('#amount_dr_'+id+', #amount_cr_'+id+'').keypress(function(e) {
                var key = e.which;            
                if(key === 13)  // the enter key code
                {
                    var br_account_id = $('#account_id_'+id+'').val();
                    var acc_name =  $('#account_id_'+id+' option:selected').text();
                    var acc_type=0;
                    if(acc_name.indexOf('SUP') > -1) {
                        $('#account_type').val('SUP');
                        $('#add_url').val('Receivable');
                        acc_type = 1;
                        if($('#amount_cr_'+id+'').val() > 0){
                            acc_type = 4;
                        }
                    }
                    if(acc_name.indexOf('CUS') > -1) {
                        $('#account_type').val('CUS');
                        $('#add_url').val('Payable');
                        acc_type = 2;
                        if($('#amount_dr_'+id+'').val() > 0){
                            acc_type = 3;
                        }
                    }                
                    var br_account = $('#amount_cr_'+id+'').val();
                    if(br_account==""){
                        br_account = $('#amount_dr_'+id+'').val();
                    }
                    if(acc_type == 1 || acc_type == 2){
                        $("#addCtrlJournalVoucherAdjestEdit").click();
                        $("#addCtrlJournalVoucherAdjestEdit").prop("disabled", true);
                    }
                    return false;                
                }
            });
            return false;
        }  --}}

    function dr_popup_fun(id)
    {
        $('#amount_dr_'+id+'').keypress(function (e) {
        var key = e.which;
        if(key === 13)  // the enter key code
            {
                var br_account_id = $('#account_id_'+id+'').val();
                var acc_name =  $('#account_id_'+id+' option:selected').text();
                var acc_type=0;

                if(acc_name.indexOf('SUP') > -1) {
                    $('#account_type').val('SUP');
                    $('#add_url').val('Receivable');
                    acc_type = 1;
                    var br_account = $('#amount_dr_'+id+'').val();
                }

                if(br_account_id != "" && br_account != ""){
                    $('#br_account_id').val(br_account_id);
                    $('#br_account_id_amount').val(br_account);
                    $('#bi_cheque_amount').val(br_account).focus();
                    $('#bi_cheque_amount').focus();
                    
                    if(acc_type == 1 || acc_type == 2){
                        $("#addCtrlJournalVoucherAdjestEdit").click();
                        $("#addCtrlJournalVoucherAdjestEdit").prop("disabled", true);
                    }
                }
                else{ alert("Amount Missing") }
                return false;
            }
        });
        return false;
    }
    function cr_popup_fun(id)
    {
        $('#amount_cr_'+id+'').keypress(function (e) {
        var key = e.which;
        if(key === 13)  // the enter key code
            {
                var br_account_id = $('#account_id_'+id+'').val();
                var acc_name =  $('#account_id_'+id+' option:selected').text();
                var acc_type=0;
                
                if(acc_name.indexOf('CUS') > -1) {
                    $('#account_type').val('CUS');
                    $('#add_url').val('Payable');
                    acc_type = 2;
                    var br_account = $('#amount_cr_'+id+'').val();
                }

                if(br_account_id != "" && br_account != ""){
                    $('#br_account_id').val(br_account_id);
                    $('#br_account_id_amount').val(br_account);
                    $('#bi_cheque_amount').val(br_account).focus();
                    $('#bi_cheque_amount').focus();
                    
                    if(acc_type == 1 || acc_type == 2){
                        $("#addCtrlJournalVoucherAdjestEdit").click();
                        $("#addCtrlJournalVoucherAdjestEdit").prop("disabled", true);
                    }
                }
                else{ alert("Amount Missing") }
                return false;
            }
        });
        return false;
    }

    $('#journalvoucher-create-form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
          e.preventDefault();
          return false;
        }
    });
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


{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'journalvoucher-get-adjestment-update', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'journalvoucher-get-adjestment-update']) }}
    <div class="modal fade admin-query" id="cr_popup_win">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header m-0 p-3">
                    <h4 class="modal-title">Bill Wise Selection</h4>
                    <button class="close" data-dismiss="modal" type="button" onclick="activate_button()">
                        ×
                    </button>
                </div>
                <div class="modal-body m-0 p-3">
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

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="equipment comon-status row mt-40 d-block">
                                        <table class="sstable" cellspacing="0" width="100%" id="crListBankBookAdjest">
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
                            </div>
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

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="col-lg-12 text-center">
                                    <div class="mt-40 d-flex justify-content-between">
                                        <button class="btn btn-primary fix-gr-bg" data-dismiss="modal" type="button" onclick="activate_button()" id="btn_close2">
                                            @lang('lang.cancel')
                                        </button>
                                        {{--  <input class="btn btn-success fix-gr-bg" type="button" value="Save" onclick="validateAttachForm()" />  --}}
                                        <button class="btn btn-success fix-gr-bg" type="submit" onclick="popup_form_submit()">Save</button>
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
            </div>
        </div>
    </div>
    
    {{ Form::close() }}


<a id="btnModalAdjustment" data-toggle="modal" data-target="#ModalAdjustment"></a>
<a id="btnPaymentModalAdjustment" data-toggle="modal" data-target="#ModalPaymentAdjustment"></a>
<input type="hidden" id="adj_siv_amount" name="adj_siv_amount" value="0">>
<input type="hidden" id="adj_siv_amount_actual" name="adj_siv_amount_actual" value="0"/>
<input type="hidden" id="adj_siv_amount_adjusted" name="adj_siv_amount_adjusted" value="0"/>
<input type="hidden" id="adj_account_id">
<input type="hidden" id="adj_account_id_amount">
<!-- Modal Adjustment-->
    <div class="modal fade" id="ModalAdjustment" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Unadjusted List</h5>
                    <button class="close" id="ModalAdjustmentClose" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-update-adjustment', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table" id="table_jv_receipt_list" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
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
                                            <button class="btn btn-success" type="button" onclick="update_adjustment()">
                                                Add Adjusement
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
    <div class="modal fade" id="ModalPaymentAdjustment" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Supplier Unadjusted List</h5>
                    <button class="close" id="ModalPaymentAdjustmentClose" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'sales-invoice-update-adjustment', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table" id="table_jv_payment_list" style="border: solid 1px #e3e6f0; width:auto; width:100%;">
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
                                            <button class="btn btn-success" type="button" onclick="update_payment_adjustment()">
                                                Add Adjusement
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

{{-- attachment start--}}
<div class="modal fade admin-query" id="attachment_popup_win" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header m-0 p-3">
                <h4 class="modal-title">Attachments - <label id="att_cust_name"></label></h4>
                <button class="close" data-dismiss="modal" type="button">
                    ×
                </button>
            </div>
            <div class="modal-body m-0 p-3">
                <div class="container-fluid">
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
                                <input class="form-control" type="date" id="att_date" name="att_date" value="{{ date('Y-m-d') }}"/>
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
                        <table id="att-table" class="table table-bordered table-striped" width="100%" cellspacing="0">
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

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-12 text-right">
                                    <button class="btn btn-warning" data-dismiss="modal" type="button" id="add_srl_cls">
                                        @lang('Close')
                                    </button>
                                    <input type="hidden" id="srl_id" />
                                    <button class="btn btn-success" type="button" onclick="add_attachment()">
                                        Add Attachment
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function add_attachment(){
        $("#loading_bg").css("display", "block");

        if($('#att_file').val()==""){ $('#att_file').focus(); $("#loading_bg").css("display", "none"); return false; }

        var action = "{{ URL::to('add-journal-voucher-attachment') }}";
        
        var formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');  // Append CSRF token
        formData.append('doc_id', $('#jv_id').val());
        formData.append('att_date', $('#att_date').val()); // Append other form data
        formData.append('att_file', $('#att_file')[0].files[0]); 
            formData.append('doc_name', $('#doc_name').val());



        $.ajax({
            url: action,
            type: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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
    function view_attachment(){
        $("#loading_bg").css("display", "block");
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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
                                <td><a onclick='delete_attachment("+dataResult['data'][i].id+")' class='btn-sm btn-danger'><i class='fa fa-trash' aria-hidden='true'></i></a></td>\
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
    </script>

{{-- attachment end--}}

@endsection

@section('script')
    <script>

$(window).ready(function() {
        $("#journalvoucher-create-form").on("keypress", function (event) {           
            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {
                event.preventDefault();
                return false;
            }
        });
});

        // $(document).ready(function () {
        //     $("#btnSubmit").click(function () {
        //         setTimeout(function () { disableButton(); }, 0);
        //     });
        //     function disableButton() {
        //         $("#btnSubmit").prop('disabled', true);
        //     }
        // });
    </script>
@endsection
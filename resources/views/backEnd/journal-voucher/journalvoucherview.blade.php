@extends('backEnd.masterpage')
@section('mainContent')
    
    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Journal Voucher View</h2>
                <span class="page-label">Home - Journal Voucher</span>
            </div>
            <div>
                <a data-modal-size="modal-md" data-target="#attachment_popup_win" data-toggle="modal" class="btn btn-primary" onclick="view_attachment()"><i class="fa fa-plus"></i> Attachment</a>
                <a href="{{ url('journalvoucher-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                <a href="{{ url('journalvoucher/'.$editData->id.'/edit') }}" type="button" class="btn btn-warning"><i class="fa fa-edit"></i> Edit</a>
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
        <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
        <input type="hidden" name="date_of_joining" id="date_of_joining" value="{{ date('Y-m-d') }}">
        <input type="hidden" value="{{ @$editData->id }}" name="cust_id" id="jv_id">
        <div class="row">
            <div class="col-lg-12">
                <div class="white-box">
                    <div class="row">
                        <div class="col-lg-3 mb-4">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl"> @lang('Doc Number') <span>*</span> </label>
                                        <input
                                            class="form-control"
                                            type="text" id="doc_number" name="doc_number"
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
                                        {{ isset($edit) ? (!empty(@$editData->currency) ? (@$editData->currency == @$value->id ? 'selected' : '') : '') : '' }}>
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
                    </div>




                    <div class="equipment comon-status row mt-4 d-block">
                        <table class="table table-bordered table-striped" id="jv-table" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width:20px;">@lang('#')</th>
                                    <th style="width:250px;">@lang('Account Name')</th>
                                    <th style="width:100px;">@lang('Debit')</th>
                                    <th style="width:100px;">@lang('Credit')</th>
                                    <th style="width:200px;">@lang('Narration')</th>
                                    <th style="width:100px;">@lang('Deal Id')</th>
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
                                    <tr id="rowone{{ $roid }}" onclick="fn_addRow({{ $roid }})">
                                        <td>{{ $roid }}</td>
                                        <td><select class="form-control js-example-basic-single" name="account_id[]"
                                                id="account_id_{{ $roid }}" disabled>
                                                <option value=""></option>
                                                @foreach ($accounts as $key => $value)
                                                    <option value="{{ @$value->id }}"
                                                        {{ isset($editDataList[$roid - 1]) ? (!empty(@$editDataList[$roid - 1]->account_id) ? (@$editDataList[$roid - 1]->account_id == @$value->id ? 'selected' : '') : '') : '' }}>
                                                        {{ @$value->account_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input class="form-control text-right" type="number"
                                                id="amount_dr_{{ $roid }}" name="amount_dr[]"
                                                autocomplete="off" min="0" onchange="calc_change({{$roid}})"
                                                value="{{ @$editDataList[$roid - 1]->debit_amount }}" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control text-right" type="number"
                                                id="amount_cr_{{ $roid }}" name="amount_cr[]"
                                                autocomplete="off" min="0" onchange="calc_change({{$roid}})"
                                                value="{{ @$editDataList[$roid - 1]->credit_amount }}" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text"
                                                id="remarks_{{ $roid }}" name="remarks[]" autocomplete="off"
                                                value="{{ @$editDataList[$roid - 1]->remarks }}" readonly>
                                        </td>
                                        <td>
                                            <input class="form-control" type="text"
                                                id="dealid_{{ $roid }}" name="dealid[]" autocomplete="off"
                                                value="{{ @App\SysHelper::get_code_from_dealid(@$editDataList[$roid - 1]->transaction_ref) }}" readonly>
                                        </td>
                                    </tr>
                                @endfor
                                <?php $roid--; ?>
                                <input type="hidden" id="jv-row-count" value="{{ $roid }}">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right"><b><label id="dr_total">{{ $editDataList->sum('debit_amount') }}</label></b></td>
                                    <td class="text-right"><b><label id="cr_total">{{ $editDataList->sum('credit_amount') }}</label></b></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div style="display: none;">
                            @if (!isset($view))
                            @endif
                        </div>


                        <script>
                            function fn_addRow(id) {
                                var rownum = document.getElementById('jv-row-count').value;
                                if (id == rownum) {
                                    document.getElementById('jv-row-count').value = (Number(rownum) + Number(1));
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
                                    </tr>
                                @endforeach
                                @endif
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                </div>
            </div>
        </div>
        </div>
    </div>

    

    <section class="admin-visitor-area">
        <div class="container-fluid p-0">



            
        </div>
    </section>


    

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
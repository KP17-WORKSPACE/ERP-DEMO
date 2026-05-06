@if($results->count() > 0)
    <table class="table table-hover table-striped" id="long-list">
        <style>
        #table-head th {
            position: sticky;
            top: 0;
            z-index: 2;
        }
    </style>
        <thead id="table-head">
            <tr>
                <th style="width: 80px;" class="text-center"> @lang('Account Code')</th>
                <th style="width: 80px;" class="text-center"> @lang('Main Heads')</th>
                <th style="width: 80px;"  class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @lang('Group')</th>
                <th style="width: 80px;"  class="">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @lang('Sub Group')</th>
                <th style="width: 80px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @lang('Account Name')</th>
                <th style="width: 80px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @lang('Sub Account Name')</th>
                <th style="width: 50px;"  class="text-center"> @lang('Status')</th>
            </tr>
        </thead>
        <tbody>
            @if($results->count() > 0)
                @php
                    $sortedResults = $results->sortBy('status');
                @endphp

                
                @foreach ($sortedResults as $i => $value)
                    <tr @if ($value->status == 2) class="bg-dark" @endif>
                        <td  style=""  class="text-center">
                            {{ @$value->account_code }}
                        </td>

                        <td style=""  class="text-center">
                            {{ @$value->groupname->title }}
                        </td>
                        <td style=""  class="">
                           <i style="font-size: 14px"  data-id="{{ @$value->subgroupname->id }}" class="ico text-danger icon-outline-pen-2 EditGroupBTN"></i> {{ @$value->subgroupname->title }} 
                        </td>
                        <td style=""  class="">
                          <i style="font-size: 14px"  data-id="{{ @$value->subgroup2name->id }}" class="ico text-danger icon-outline-pen-2 EditSubGroupBTN"></i>  {{ @$value->subgroup2name->title }} 
                        </td>
                         @if(@$value->main_account_id == 0)
                                <td>
                                 <i style="font-size: 14px"  data-id="{{ $value->id }}" class="ico text-danger icon-outline-pen-2 editAccountBtn"></i>   {{ @$value->account_name }} 
                                </td>
                                <td>
                                    --
                                </td>
                                @else
                                <td>
                                   <i style="font-size: 14px"  data-id="{{ $value->mainaccount->id }}" class="ico text-danger icon-outline-pen-2 editAccountBtn"></i> {{ @$value->mainaccount->account_name }} 
                                </td>
                                <td>
                                      <i style="font-size: 14px"  data-id="{{ $value->id }}" class="ico text-danger icon-outline-pen-2 editSubAccountBtn"></i>  {{ @$value->account_name }} 
                                </td>
                        @endif
                        <td   style=""  class="text-center">
                            @if (@$value->status == 1)
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-dark">Deleted</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                @else
                    <p class="text-center text-muted">No results found</p>
                @endif
        </tbody>
    </table>
@else
    <p class="text-center text-muted">No results found</p>
@endif


<div class="modal side-panel fade" id="editAccountModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true" style="z-index: 2099;">
        <div class="modal-dialog modal-lg" style="z-index: 2099;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Edit Account - <span id="edit_account_codesups"></span>
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 bg-white">
                        <div class="card-body">

                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'editAccountForm']) }}
                            <input type="hidden" value="" name="cust_id">


                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                            <input type="hidden" name="catid" id="catid" value="2">

                            <div class="row">
                                <!-- <div class="col-4 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Account Code') <span>*</span> </label>
                                       

                                        <span class="focus-border"></span>

                                        @if ($errors->has('contcat_person'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('contcat_person') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div> -->
                                 <input
                                            class="txtbx primary-input form-control {{ $errors->has('account_code') ? 'is-invalid' : ' ' }}"
                                            type="hidden" id="edit_account_code" name="account_code" value=""
                                            required>
                                <div class="col-4 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Account Name') <span>*</span> </label>
                                        <input
                                            class="txtbx primary-input form-control {{ $errors->has('account_name') ? 'is-invalid' : ' ' }}"
                                            type="text" id="edit_account_name" name="account_name" value=""
                                            required>

                                        <div id="edit_account_name_add_list">
                                        </div>
                                        <script>
                                            $(document).ready(function() {
                                                $('#edit_account_name').keyup(function() {
                                                    var query = $(this).val();
                                                    if (query != '') {
                                                        var _token = $('input[name="_token"]').val();
                                                        $.ajax({
                                                            url: "{{ route('autocomplete.account_name') }}",
                                                            method: "POST",
                                                            data: {
                                                                query: query,
                                                                _token: _token
                                                            },
                                                            success: function(data) {
                                                                $('#edit_account_name_add_list').fadeIn();
                                                                $('#edit_account_name_add_list').html(data);
                                                            }
                                                        });
                                                    }
                                                });
                                                $('#edit_account_name_add_list').on('click', 'li', function() {
                                                    $('#edit_account_name').val($(this).text());
                                                    $('#edit_account_name_add_list').fadeOut();
                                                });
                                            });
                                        </script>
                                    </div>
                                </div>


                                <div class="col-4 mb-4">
                                    <input type="hidden" name="pre_sub_group2" value="">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Account Sub Group') <span>*</span> </label>
                                        <div class="input-effect" id="sectionSubGroup2Div">
                                            <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single"
                                                name="subgroup2" id="edit_subgroup2" onchange="fn_subgroup2()">
                                                @php
                                                    $accountgroupsub2 = @App\SysAccountGroupSub2::where('status', 1)->get();
                                                @endphp
                                                <option value="0"></option>
                                                @if (isset($accountgroupsub2))
                                                    @foreach ($accountgroupsub2 as $val)
                                                        <option value="{{ @$val->id }}">{{ @$val->title }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    function fn_subgroup2() {
                                        if ($('#edit_subgroup2').val() == 6) {
                                            $('.edit_bank_div').css('display', '');
                                        } else {
                                            $('.edit_bank_div').css('display', 'none');
                                        }
                                    }

                                    function fn_stl2() {
                                        if ($('#edit_stl').val() == 1) {
                                            $('#edit_stl_limit_div').css('display', '');
                                            $('#edit_stl_limit').prop('required', true);
                                        } else {
                                            $('#edit_stl_limit_div').css('display', 'none');
                                            $('#edit_stl_limit').prop('required', false);
                                        }
                                    }
                                </script>

                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">Beneficiary Name
                                    <input class="form-control" type="text" id="edit_beneficiary_name"
                                        name="beneficiary_name" value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">Bank Name
                                    <input class="form-control" type="text" id="edit_bank_name" name="bank_name"
                                        value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">A/c No.
                                    <input class="form-control" type="text" id="edit_acc_no" name="acc_no"
                                        value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">IBAN
                                    <input class="form-control" type="text" id="edit_iban" name="iban"
                                        value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">SWIFT Code
                                    <input class="form-control" type="text" id="edit_swift_code" name="swift_code"
                                        value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">Finance /Routing Code
                                    <input class="form-control" type="text" id="edit_routing_code"
                                        name="routing_code" value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">Branch
                                    <input class="form-control" type="text" id="edit_id_branch" name="branch"
                                        value="">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">Location
                                    <input class="form-control" type="text" id="edit_branch_location"
                                        name="branch_location"
                                        value="@if (isset($editData)) {{ @$editData->branch_location }} @endif">
                                </div>
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">Department
                                    <input class="form-control" type="text" id="edit_stl_dept" name="stl_dept"
                                        value="@if (isset($editData)) {{ @$editData->stl_dept }} @endif">
                                </div>
                               
                                <!-- hidden fields to store template coordinates in edit form -->
                                <input type="hidden" name="cheque_company_top" value="">
                                <input type="hidden" name="cheque_company_left" value="">
                                <input type="hidden" name="cheque_date_top" value="">
                                <input type="hidden" name="cheque_date_left" value="">
                                <input type="hidden" name="cheque_amount_w_top" value="">
                                <input type="hidden" name="cheque_amount_w_left" value="">
                                <input type="hidden" name="cheque_amount_top" value="">
                                <input type="hidden" name="cheque_amount_left" value="">
                                <input type="hidden" name="cheque_font_size" value="">
                                <div class="col-4 mb-4 edit_bank_div" style="display: none;">STL
                                    <select class="form-control" name="stl" id="edit_stl" onchange="fn_stl()">
                                        <option value="0"
                                            @if (isset($editData)) @if ($editData->stl == 0) selected @endif
                                            @endif>Not Applicable</option>
                                        <option value="1"
                                            @if (isset($editData)) @if ($editData->stl == 1) selected @endif
                                            @endif>Applicable</option>
                                    </select>
                                </div>
                                <div class="col-4 mb-4" id="stl_limit_div" style="display: none;">STL Limit
                                    <input class="form-control" type="text" name="stl_limit" id="edit_stl_limit"
                                        value="@if (isset($editData)) {{ @App\SysHelper::com_curr_format(@$editData->stl_limit, 2, '.', ',') }} @endif"
                                        onchange="fn_stl_limit2()">
                                </div>
                                <script>
                                    function fn_stl_limit2() {
                                        $('#edit_stl_limit').val(formatAmount($('#edit_stl_limit').val()));
                                    }
                                </script>
                                @if (isset($editData))
                                    <script>
                                        $('#edit_stl').change();
                                    </script>
                                @endif
                                 <!-- toggle for opening balance fields in edit modal -->
                                <div class="col-4 mb-4 mt-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="edit_toggleOpeningBalance">
                                        
                                    </div>
                                </div>
                                <div class="col-4 mb-4 opening-balance-edit" style="display:none;">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Opening Balance Dr') <span>*</span> </label>
                                        @php
                                            $debit_amount = '0.00';
                                            if (isset($editData_tran)) {
                                                $debit_amount = $editData_tran->debit_amount;
                                            }
                                        @endphp
                                        <input
                                            class="primary-input form-control {{ $errors->has('debit_amount') ? 'is-invalid' : ' ' }}"
                                            type="text" id="edit_debit_amount" name="debit_amount"
                                            value="{{ $debit_amount }}" required>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>

                                <div class="col-4 mb-4 opening-balance-edit" style="display:none;">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Opening Balance Cr') <span>*</span> </label>
                                        @php
                                            $credit_amount = '0.00';
                                            if (isset($editData_tran)) {
                                                $credit_amount = $editData_tran->credit_amount;
                                            }
                                        @endphp
                                        <input
                                            class="primary-input form-control {{ $errors->has('credit_amount') ? 'is-invalid' : ' ' }}"
                                            type="text" id="edit_credit_amount" name="credit_amount"
                                            value="{{ $credit_amount }}" required>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>

                                <div class="col-4 mb-4 opening-balance-edit" style="display:none;">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Date') <span>*</span> </label>
                                        @php
                                            $value = session('opening_balance_date');

                                            if (is_null($value)) {
                                                $value = Carbon\Carbon::now()->format('d/m/Y'); // today's date in d/m/Y
}

if (isset($editData_tran) && !empty($editData_tran->transaction_date)) {
    $value = Carbon\Carbon::parse($editData_tran->transaction_date)->format(
        'd/m/Y',
                                                );
                                            }
                                        @endphp
                                        <input
                                            class="primary-input form-control date-picker {{ $errors->has('opening_balance_date') ? 'is-invalid' : ' ' }}"
                                            type="text" id="edit_opening_balance_date" name="opening_balance_date"
                                            value="{{ $value }}" required>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>

                                 <!-- cheque template button inside edit modal -->
                                <div class="col-4 mb-4 edit_bank_div" style="display:none;">
                                    <label>Cheque Template</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" id="openChequeTemplateBtnEdit" class="btn btn-light btn-sm">
                                        <i class="ico icon-outline-document text-success" style="font-size:15px"></i>    Configure
                                        </button>
                                        <span class="cheque_template_status text-success small" id="cheque_template_status_edit"></span>
                                    </div>
                                </div>

                            </div>

                            <input type="hidden" name="btnSubmit"  value="update">

                            <script>
                                $('#edit_subgroup2').change();
                            </script>


                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    <button class="btn btn-light" type="submit" id="btnSubmit" 
                     value="update">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>

                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>




    <script>
        $(document).ready(function() {

          // hide edit-opening fields initially
            $('.opening-balance-edit').hide();
            $('#edit_toggleOpeningBalance').prop('checked', false);

            $('#edit_toggleOpeningBalance').change(function() {
                if (this.checked) {
                    $('.opening-balance-edit').show();
                } else {
                    $('.opening-balance-edit').hide();
                }
            });


          $(document).on('click', '.editAccountBtn', function () {
                let accountId = $(this).data('id');

                console.log("account ID:", accountId)
                $("#loading_bg").show();

                $.ajax({
                    url: '/chartofaccounts/' + accountId + '/get-edit',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            alert('Error: ' + response.message);
                            return;
                        }

                        console.log(response)

                        let editData = response.editData;
                        let editData_tran = response.editData_tran;

                        // Fill form fieldsedit_account_codesups
                        $('#editAccountModal #edit_account_name').val(editData.account_name);
                        $('#editAccountModal #edit_account_codesups').text(editData
                            .account_code);
                        $('#editAccountModal #edit_account_code').val(editData.account_code);
                        $('#editAccountModal #edit_subgroup2').val(editData.subgroup2).trigger(
                            'change');
                        $('#editAccountModal input[name="pre_sub_group2"]').val(editData.subgroup2);
                        $('#editAccountModal #edit_beneficiary_name').val(editData
                            .beneficiary_name);
                        $('#editAccountModal #edit_bank_name').val(editData.bank_name);
                        $('#editAccountModal #edit_acc_no').val(editData.acc_no);
                        $('#editAccountModal #edit_iban').val(editData.iban);
                        $('#editAccountModal #edit_swift_code').val(editData.swift_code);
                        $('#editAccountModal #edit_routing_code').val(editData.routing_code);
                        $('#editAccountModal #edit_id_branch').val(editData.branch);
                        $('#editAccountModal #edit_branch_location').val(editData
                            .branch_location);
                        $('#editAccountModal #edit_stl_dept').val(editData.stl_dept);
                        $('#editAccountModal #edit_stl').val(editData.stl).trigger('change');

                        if (editData_tran) {
                            $('#editAccountModal #edit_debit_amount').val(editData_tran
                                .debit_amount);
                            $('#editAccountModal #edit_credit_amount').val(editData_tran
                                .credit_amount);

                            $('#editAccountModal #edit_opening_balance_date').val(editData_tran
                                .transaction_date ? editData_tran
                                .transaction_date.split('-').reverse().join('/') : '');

                        } else {
                            $('#editAccountModal #edit_debit_amount').val('0.00');
                            $('#editAccountModal #edit_credit_amount').val('0.00');
                            $('#editAccountModal #edit_opening_balance_date').val('');
                        }

                        // load cheque template data into hidden inputs
                        if (response.cheque_template) {
                            var ct = response.cheque_template;
                            var container = $('#editAccountModal');
                            container.find('input[name="cheque_company_top"]').val(ct.company_top || '285px');
                            container.find('input[name="cheque_company_left"]').val(ct.company_left || '425px');
                            container.find('input[name="cheque_date_top"]').val(ct.date_top || '220px');
                            container.find('input[name="cheque_date_left"]').val(ct.date_left || '836px');
                            container.find('input[name="cheque_amount_w_top"]').val(ct.amount_w_top || '316px');
                            container.find('input[name="cheque_amount_w_left"]').val(ct.amount_w_left || '326px');
                            container.find('input[name="cheque_amount_top"]').val(ct.amount_top || '355px');
                            container.find('input[name="cheque_amount_left"]').val(ct.amount_left || '834px');
                            container.find('input[name="cheque_font_size"]').val(ct.font_size || '13px');
                        } else {
                            $('#editAccountModal input[name="cheque_company_top"]').val('285px');
                            $('#editAccountModal input[name="cheque_company_left"]').val('425px');
                            $('#editAccountModal input[name="cheque_date_top"]').val('220px');
                            $('#editAccountModal input[name="cheque_date_left"]').val('836px');
                            $('#editAccountModal input[name="cheque_amount_w_top"]').val('316px');
                            $('#editAccountModal input[name="cheque_amount_w_left"]').val('326px');
                            $('#editAccountModal input[name="cheque_amount_top"]').val('355px');
                            $('#editAccountModal input[name="cheque_amount_left"]').val('834px');
                            $('#editAccountModal input[name="cheque_font_size"]').val('13px');
                        }
                        // refresh template status / global variable for edit modal
                        if (typeof ctLoadFromContainer === 'function') {
                            ctLoadFromContainer($('#editAccountModal'));
                        }

                        // Show the modal
                        $("#loading_bg").hide();
                        $('#editAccountForm').attr('action', 'chartofaccounts-update/' +
                            editData.id);
                        $('#editAccountModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        console.error('Response:', xhr.responseText);
                        alert('An error occurred while fetching data. Please try again later.');
                    }

                });
            });


            $(document).on('click','#btnSubmit', function() {
                
                $('#editAccountForm').submit();
            });


        });


      
    </script>


<!-- Edit Sub Account Modal -->
    <div class="modal side-panel fade" id="editSubAccountModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Edit Sub Account - <span
                            id="edit_account_codesups"></span></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 bg-white">
                        <div class="card-body">

                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-sub-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'id' => 'editSubAccountForm']) }}

                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                            <input type="hidden" name="catid" id="catid" value="2">

                            <div class="row">
                                <div class="col-4 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Account Code') <span>*</span> </label>
                                        <input class="txtbx primary-input form-control" type="text"
                                            id="edit_account_code" name="account_code" value="" required>
                                    </div>
                                </div>
                                <div class="col-4 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Sub Account Name') <span>*</span> </label>
                                        <input class="txtbx primary-input form-control" type="text"
                                            id="edit_account_name" name="account_name" value="" required>
                                        <div id="edit_sub_account_name_add_list"></div>
                                    </div>
                                </div>
                                <div class="col-4 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Main Account') <span>*</span> </label>
                                        <select class="form-control js-example-basic-single-2" name="main_account_id"
                                            id="edit_main_account_id" required>
                                            @php
                                                $com_id = session('logged_session_data.company_id');
                                                $accounts = @App\SysChartofAccounts::select('id', 'account_name', 'account_code')->whereRaw("find_in_set($com_id,sys_chartofaccounts.company_access)")->where('main_account_id', 0)->where('account_code', 'like', 'ACC%')->get();
                                            @endphp
                                            <option value=""></option>
                                            @if (isset($accounts))
                                                @foreach ($accounts as $val)
                                                    <option value="{{ $val->id }}">{{ $val->account_code }} -
                                                        {{ $val->account_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-4 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Opening Balance Dr') <span>*</span> </label>
                                        <input class="primary-input form-control" type="text" id="edit_debit_amount"
                                            name="debit_amount" value="0.00" required>
                                    </div>
                                </div>

                                <div class="col-4 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Opening Balance Cr') <span>*</span> </label>
                                        <input class="primary-input form-control" type="text" id="edit_credit_amount"
                                            name="credit_amount" value="0.00" required>
                                    </div>
                                </div>

                                <input type="hidden" name="btnSubmit" value="update">

                                <div class="col-4 mb-4">
                                    <div class="input-effect">
                                        <label class="form-label"> @lang('Date') <span>*</span> </label>
                                        <input class="primary-input form-control date-picker" type="text"
                                            id="edit_opening_balance_date" name="opening_balance_date" value=""
                                            required>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" id="btnSubmitSubAccount">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            // Autocomplete for Sub Account Name
            $(document).on('keyup', '#edit_account_name', function() {
                var query = $(this).val();
                if (query != '') {
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url: "{{ route('autocomplete.account_name') }}",
                        method: "POST",
                        data: {
                            query: query,
                            _token: _token
                        },
                        success: function(data) {
                            $('#edit_sub_account_name_add_list').fadeIn().html(data);
                        }
                    });
                }
            });
            
            $(document).on('click', '#edit_sub_account_name_add_list li', function() {
                $('#edit_account_name').val($(this).text());
                $('#edit_sub_account_name_add_list').fadeOut();
            });

            $(document).on('click', function(event) {
                if (!$(event.target).closest('#edit_account_name, #edit_sub_account_name_add_list').length) {
                    $('#edit_sub_account_name_add_list').fadeOut();
                }
            });

            $(document).on('click', '#btnSubmitSubAccount', function() {
                $('#editSubAccountForm').submit();
            });


            // Open modal and fill data
            $(document).on('click', '.editSubAccountBtn', function() {
                let subaccountId = $(this).data('id');
                console.log("Account ID:", subaccountId);
                $("#loading_bg").show();

                $.ajax({
                    url: '/chartofaccounts-sub/' + subaccountId + '/get-edit',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        console.log("AJAX Response:", response);
                        if (response.error) {
                            alert('Error: ' + response.message);
                            $("#loading_bg").hide();
                            return;
                        }

                        let editData = response.editData;
                        let editData_tran = response.editData_tran;

                        console.log("Edit Data:", editData.account_code);

                        // Scope to modal to avoid clobbering elements with duplicate IDs elsewhere
                        const $modal = $('#editSubAccountModal');

                        $modal.find('#edit_account_codesups').text(editData.account_code);
                        $modal.find('#edit_account_code').val(editData.account_code).prop('value', editData.account_code);
                        $modal.find('#edit_account_name').val(editData.account_name);
                        $modal.find('#edit_main_account_id').val(editData.main_account_id).trigger('change');

                        if (editData_tran) {
                            $modal.find('#edit_debit_amount').val(editData_tran.debit_amount);
                            $modal.find('#edit_credit_amount').val(editData_tran.credit_amount);
                            $modal.find('#edit_opening_balance_date').val(editData_tran.transaction_date ? editData_tran.transaction_date.split('-').reverse().join('/') : '');
                        } else {
                            $modal.find('#edit_debit_amount').val('0.00');
                            $modal.find('#edit_credit_amount').val('0.00');
                            $modal.find('#edit_opening_balance_date').val('');
                        }

                        $modal.find('#editSubAccountForm').attr('action', '/chartofaccounts-sub-update/' + editData.id);

                        $("#loading_bg").hide();
                        $modal.modal('show');
                    },

                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        console.error('Response:', xhr.responseText);
                        alert('An error occurred while fetching data. Please try again later.');
                        $("#loading_bg").hide();
                    }
                });
            });

        });
    </script>

       <script>

        

        $(document).ready(function() {
            $('.js-example-basic-single').select2();
                $('.js-example-basic-single-2').select2();

        });
        
    </script>


 <!-- Edit Sub Group Modal -->
    <div class="modal side-panel fade" id="editSubGroupModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editSubGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editSubGroupModalLabel">Edit Sub Group</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 bg-white">
                        <div class="card-body">

                            {{ Form::open(['id' => 'editSubGroupForm', 'class' => 'form-horizontal', 'files' => true, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}

                            <input type="hidden" name="url" id="edit_url" value="{{ URL::to('/') }}">
                            <input type="hidden" name="date_of_joining" id="edit_date_of_joining"
                                value="{{ date('Y-m-d') }}">

                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="input-effect">
                                        <label class="txtlbl"> @lang('Sub Group Name') <span>*</span> </label>
                                        <input class="txtbx primary-input form-control" type="text" id="edit_title"
                                            name="title" value="" required>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="input-effect">
                                        @php
            $accountgroupsub = @App\SysAccountGroupSub::where('status', 1)->get();
                                            
                                        @endphp
                                        <label class="txtlbl"> @lang('Select Group') <span>*</span> </label>
                                        <select class="txtbx niceSelect js-example-basic-single-3 w-100 bb form-control" name="sub_id"
                                            id="edit_sub_group_id" required>
                                            <option value="0"></option>
                                            @if (isset($accountgroupsub))
                                                @foreach ($accountgroupsub as $val)
                                                    <option value="{{ $val->id }}">{{ $val->title }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" id="edit_btnSubmit" type="submit">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> @lang('Save')
                    </button>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {

        $(document).on('click', '#edit_btnSubmit', function() {
                $('#editSubGroupForm').submit();
            });

        

            $(document).on('click', '.EditSubGroupBTN', function() {
                console.log("Edit Sub Group Button Clicked");
                let subgroupid = $(this).data('id');

                console.log("Sub Group ID:", subgroupid);
                $("#loading_bg").show();

                $.ajax({
                    url: '/accountgroupsub2/' + subgroupid + '/get-edit',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            alert('Error: ' + response.message);
                            $("#loading_bg").hide();
                            return;
                        }

                        let editData = response.editData;

                        // Fill the form fields with proper IDs
                        $('#editSubGroupModal #edit_title').val(editData.title);
                        $('#editSubGroupModal #edit_sub_group_id').val(editData.sub_id).trigger(
                            'change');

                        // Set the form's action dynamically
                        $('#editSubGroupForm').attr('action', '/accountgroupsub2-update/' +
                            editData.id);

                        $("#loading_bg").hide();
                        $('#editSubGroupModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        console.error('Response:', xhr.responseText);
                        alert('An error occurred while fetching data. Please try again later.');
                        $("#loading_bg").hide();
                    }
                });
            });

        });
    </script>

   


 <!-- Edit Group Modal -->
    <div class="modal side-panel fade" id="editGroupModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editGroupModalLabel">Edit Group</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body bg-white">

                            {{ Form::open(['id' => 'editGroupForm', 'class' => 'form-horizontal', 'files' => true, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}

                            <input type="hidden" name="url" id="edit_url" value="{{ URL::to('/') }}">
                            <input type="hidden" name="date_of_joining" id="edit_date_of_joining"
                                value="{{ date('Y-m-d') }}">

                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="input-effect">
                                        <label class="txtlbl"> @lang('Group Name') <span>*</span> </label>
                                        <input class="form-control" type="text" id="edit_title" name="title"
                                            value="" required>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>

                                <div class="col-12 mb-4">
                                    <div class="input-effect">
                                        @php
            $accountgroup = @App\SysAccountGroup::where('status', 1)->get();
                                            
                                        @endphp
                                        <label class="txtlbl"> @lang('Select Main Heads') <span>*</span> </label>
                                        <select class="form-control js-example-basic-single-3" name="group_id" id="edit_group_id" required>
                                            <option value=""></option>
                                            @if (isset($accountgroup))
                                                @foreach ($accountgroup as $val)
                                                    <option value="{{ $val->id }}">{{ $val->title }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="focus-border"></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="submit" id="edit_btnSubmit_group">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>

     <script>
        $(document).ready(function() {
            $('.js-example-basic-single-3').select2();
        });
    </script>


    <script>
        
          $(document).on('click', '#edit_btnSubmit_group', function() {
                $('#editGroupForm').submit();
            });

        $(document).ready(function() {

            $(document).on('click', '.EditGroupBTN', function() {
                console.log("Edit Group Button Clicked");
                
                let groupid = $(this).data('id');

                console.log("Group ID:", groupid);
                $("#loading_bg").show();

                $.ajax({
                    url: '/accountgroupsub/' + groupid + '/get-edit',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            alert('Error: ' + response.message);
                            $("#loading_bg").hide();
                            return;
                        }

                        let editData = response.editData;

                        // Fill the form fields
                        $('#editGroupModal #edit_title').val(editData.title);
                        $('#editGroupModal #edit_group_id').val(editData.group_id).trigger(
                            'change');

                        // Set the form's action dynamically
                        $('#editGroupForm').attr('action', '/accountgroupsub-update/' + editData
                            .id);

                        $("#loading_bg").hide();
                        $('#editGroupModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        console.error('Response:', xhr.responseText);
                        alert('An error occurred while fetching data. Please try again later.');
                        $("#loading_bg").hide();
                    }
                });
            });

        });
    </script>
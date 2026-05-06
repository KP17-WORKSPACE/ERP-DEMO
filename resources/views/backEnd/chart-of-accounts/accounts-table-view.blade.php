<div class="modal modal-draggable fade" id="AccountTableModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="AccountTableLabel"
    aria-hidden="true">
        <style>
        #table-head th {
            position: sticky;
            top: 0;
            z-index: 2;
        }
       
        </style>
    <div class="modal-dialog  modal-dialog-scrollable" style="max-width:1300px;width:1300px;left:90px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="AccountTableLabel" style=" padding-left: 11px;">Accounts</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0" id="accountModalBody">

                    <div class="card m-0 p-0">
              <div class="card-body bg-white p-0" style="height:auto;max-height:none;">

                  <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%;height:auto;max-height:none;">

                    <thead id="table-head">
                        @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                            <tr>
                                <td colspan="6">
                                    @if (session()->has('message-success-delete'))
                                        <div class="alert alert-success">
                                            {{ session()->get('message-success-delete') }}
                                        </div>
                                    @elseif(session()->has('message-danger-delete'))
                                        <div class="alert alert-danger">
                                            {{ session()->get('message-danger-delete') }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <th class="text-center" style="width:100px;padding-left: 14px" > @lang('Code')</th>
                            <th style="width:350px"> @lang('Account Name')</th>
                            <th> @lang('Main Heads')</th>
                            <th> @lang('Group')</th>
                            <th> @lang('Sub Group')</th>
                            <th class="text-end"> @lang('Opening Dr')</th>
                            <th class="text-end"> @lang('Opening Cr')</th>
                            <th class="text-center"> @lang('Status')</th>
                            <th style="width: 280px;" class="text-center"> @lang('lang.action')</th>
                        </tr>
                    </thead>

                    <tbody id="accountTableBody">
                        {{-- Data will be loaded via infinite scroll --}}
                    </tbody>
                </table>

                <div id="loadingIndicator" class="text-center p-3" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading more accounts...</p>
                </div>
                        
                    </div>
                    </div>

              
            </div>
        </div>
    </div>
</div>

<script>
    // Infinite Scroll for Account Table Modal - Use IIFE to avoid variable conflicts
    (function() {
        let currentPage = 1;
        let isLoading = false;
        let hasMoreData = true;

        function loadMoreAccounts() {
            if (isLoading || !hasMoreData) return;

            isLoading = true;
            $('#loadingIndicator').show();

            $.ajax({
                url: '{{ url('/load-account-modal-paginated') }}',
                method: 'GET',
                data: {
                    page: currentPage
                },
                success: function(response) {
                    $('#accountTableBody').append(response.html);
                    hasMoreData = response.hasMore;
                    currentPage++;
                    isLoading = false;
                    $('#loadingIndicator').hide();
                },
                error: function(xhr, status, error) {
                    console.error('Error loading accounts:', error);
                    isLoading = false;
                    $('#loadingIndicator').hide();
                    alert('Failed to load accounts. Please try again.');
                }
            });
        }

        // Remove any previous event handlers to prevent duplicates
        $('#AccountTableModal').off('shown.bs.modal').on('shown.bs.modal', function() {
            // Reset state
            currentPage = 1;
            hasMoreData = true;
            isLoading = false;

            // Clear existing data
            $('#accountTableBody').empty();

            // Load first page
            loadMoreAccounts();
        });

        // Reset scroll position when modal is hidden
        $('#AccountTableModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            $('#accountModalBody').scrollTop(0);
        });

        // Detect scroll and load more - remove previous handler first
        $('#accountModalBody').off('scroll').on('scroll', function() {
            const scrollTop = $(this).scrollTop();
            const scrollHeight = $(this)[0].scrollHeight;
            const clientHeight = $(this).height();

            // If scrolled to bottom (with 100px threshold)
            if (scrollTop + clientHeight >= scrollHeight - 100) {
                loadMoreAccounts();
            }
        });
    })();
</script>



<script>
    function move_sub_account2(id, name) {
        $('#move_account_id2').val(id);
        $('#move_account_name2').text(name);
        $('#link_move_popup2').click();
    }
</script>
<button id="link_move_popup2" data-bs-toggle="modal" data-bs-target="#exampleModal2" hidden></button>

<div class="modal modal-draggable side-panel fade" id="exampleModal2" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">Move Account to
                    Sub
                    Account</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{ Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'chartofaccounts-maintosub',
                'method' => 'POST',
                'enctype' => 'multipart/form-data',
            ]) }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="input-effect">
                            <input type="hidden" id="move_account_id2" name="move_account_id" value="" />

                            <label id="move_account_name2" class="font-weight-bold"></label> Move to

                            <select class="form-control js-example-basic-single" name="main_account_id"
                                id="main_account_id2" required>
                                <option value=""></option>
                                @if (isset($accounts2))
                                    @foreach ($accounts2 as $val)
                                        @if (@$val->main_account_id == 0)
                                            <option value="{{ @$val->id }}">
                                                {{ @$val->account_code }} -
                                                {{ @$val->account_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light add-btn ms-2" type="submit">
                    <i class="ico icon-outline-bookmark-opened text-success"></i>
                    Move
                </button>
            </div>
            {{ Form::close() }}

        </div>
    </div>
</div>



<div class="modal modal-draggable side-panel fade" id="editAccountModal2" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="/chartofaccounts-update/12" method="POST" enctype="multipart/form-data" id="editAccountForm2">
            @csrf
            <input type="hidden" name="_method" value="PUT">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="editModalLabel">Edit Account - <span id="edit_account_codesups"></span>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body bg-white">

                    

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


                            <div class="col-3 mb-4">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Account Sub Group') <span>*</span> </label>
                                    <div class="input-effect" id="sectionSubGroup2Div">
                                        <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single"
                                            name="subgroup2" id="edit_subgroup2" onchange="fn_subgroup2()">
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
                            @php
                                $departments = @App\SmHumanDepartment::all();
                            @endphp

                            <div class="col-3 mb-4">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Department') <span>*</span> </label>
                                    <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" name="department_id" id="edit_department_id" required>
                                        <option value=""></option>
                                        @if (isset($departments))
                                            @foreach ($departments as $val)
                                                <option value="{{ @$val->id }}">{{ @$val->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-2 mb-4">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Prepaid/Accrued Exp') <span>*</span> </label>
                                    <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" name="credit_account_status" id="edit_credit_account_status" required>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
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
                                <div class="col-12 mb-2 mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input"  data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Edit Opening Balance"
                            data-bs-placement="top" type="checkbox" id="edit_toggleOpeningBalance">
                                    
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

                        </div>

                        <script>
                            $('#edit_subgroup2').change();
                        </script>


                    </div>
                </div>
            </div>
            <div class="modal-footer">

                <button class="btn btn-light" id="btnSubmit" name="btnSubmit" value="update">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                </button>

            </div>
       
            {{-- {{ Form::close() }} --}}
        </div>
         </form>
    </div>
</div>


<script>
    $(document).ready(function() {

    $('.opening-balance-edit').hide();
            $('#edit_toggleOpeningBalance').prop('checked', false);

            $('#edit_toggleOpeningBalance').change(function() {
                if (this.checked) {
                    $('.opening-balance-edit').show();
                } else {
                    $('.opening-balance-edit').hide();
                }
            });

        // Use event delegation for dynamically loaded buttons
        $(document).on('click', '.editAccountBtn2', function() {
            let accountId = $(this).data('id');

            console.log("Clicked edit button for account ID:", accountId);

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
                    $('#editAccountModal2 #edit_account_name').val(editData.account_name);
                    $('#editAccountModal2 #edit_account_codesups').text(editData
                        .account_code);
                    $('#editAccountModal2 #edit_account_code').val(editData.account_code);
                    $('#editAccountModal2 #edit_subgroup2').val(editData.subgroup2).trigger(
                        'change');
                    $('#editAccountModal2 #edit_department_id').val(editData.department_id).trigger('change');
                    $('#editAccountModal2 #edit_credit_account_status').val(editData.yes_no).trigger('change');
                    $('#editAccountModal2 #edit_beneficiary_name').val(editData
                        .beneficiary_name);
                    $('#editAccountModal2 #edit_bank_name').val(editData.bank_name);
                    $('#editAccountModal2 #edit_acc_no').val(editData.acc_no);
                    $('#editAccountModal2 #edit_iban').val(editData.iban);
                    $('#editAccountModal2 #edit_swift_code').val(editData.swift_code);
                    $('#editAccountModal2 #edit_routing_code').val(editData.routing_code);
                    $('#editAccountModal2 #edit_id_branch').val(editData.branch);
                    $('#editAccountModal2 #edit_branch_location').val(editData
                        .branch_location);
                    $('#editAccountModal2 #edit_stl_dept').val(editData.stl_dept);
                    $('#editAccountModal2 #edit_stl').val(editData.stl).trigger('change');

                    if (editData_tran) {
                        $('#editAccountModal2 #edit_debit_amount').val(editData_tran
                            .debit_amount);
                        $('#editAccountModal2 #edit_credit_amount').val(editData_tran
                            .credit_amount);

                        $('#editAccountModal2 #edit_opening_balance_date').val(editData_tran
                            .transaction_date ? editData_tran
                            .transaction_date.split('-').reverse().join('/') : '');


                    } else {
                        $('#editAccountModal2 #edit_debit_amount').val('0.00');
                        $('#editAccountModal2 #edit_credit_amount').val('0.00');
                        $('#editAccountModal2 #edit_opening_balance_date').val('');
                        $('#edit_toggleOpeningBalance').prop('checked', false);

                    }

                    // Show the modal
                    $("#loading_bg").hide();
                    $('#editAccountForm2').attr('action', 'chartofaccounts-update/' +
                        editData.id);
                    $('#editAccountModal2').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    alert('An error occurred while fetching data. Please try again later.');
                }

            });
        });

    });
</script>


  <script>


function initPopovers() {
    // Select using jQuery since elements might be added later
    $('[data-bs-popover="popover"]').each(function () {
        // Dispose any existing instance to avoid duplicates
        var existingPopover = bootstrap.Popover.getInstance(this);
        if (existingPopover) {
            existingPopover.dispose();
        }

        // Initialize new popover
        new bootstrap.Popover(this, {
            delay: { show: 500, hide: 100 },
            trigger: 'hover',
            container: 'body'
        });
    });
}

// Initialize on document ready
$(document).ready(function () {
    initPopovers();
});

// Reinitialize automatically after AJAX loads
$(document).ajaxComplete(function () {
    initPopovers();
});




</script>
 <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();

            // When a modal is opened, reattach Select2 dropdown inside that modal
            $('.modal').on('shown.bs.modal', function() {
                $(this).find('.js-example-basic-single').each(function() {
                    $(this).select2({
                        dropdownParent: $(this).closest('.modal'),
                        width: '100%'
                    });
                });
            });
        });
    </script>

         <script>
                                        $(document).ready(function() {
                                    $(document).on('click', '.moveToSubGroupBtn', function () {
                                                var accountId = $(this).data('id');
                                                var accountName = $(this).data('name');
                                                $('#move_account_id_subgroup').val(accountId);
                                                $('#move_account_name').text(accountName);
                                                $('#move_account_header').text(accountName);
                                                $('#move_account_id_header').text(accountId);
                                                $('#moveToSubGroup').modal('show');
                                            });
                                        });
                                    </script>


<div class="modal modal-draggable side-panel fade" id="moveToSubGroup" data-bs-backdrop="false"
                                        tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="moveToSubGroupLabel">
                                                        Move <strong><span id="move_account_header"></span></strong>
                                                         to Sub Group
                                                    </h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>

                                                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-move', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="input-effect">
                                                                <input type="hidden" id="move_account_id_subgroup"
                                                                    name="move_account_id_subgroup" value="" />
                                                                       @php
                              $accountgroupsub = @App\SysAccountGroupSub::where('status', 1)->orderBy('group_id')->get();
                        @endphp


                                                                <label id="move_account_name"
                                                                    class="font-weight-bold"></label> Group

                                                                <select id="group_account" name="group_account" class="form-control js-example-basic-single"
                                 required>
                             
                                    @foreach ($accountgroupsub as $data)
                                    <option value="{{ $data->id }}">{{ $data->title }}</option>
                                @endforeach
                               
                            </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button  class="btn btn-light add-btn ms-2"
                                                        type="submit">
                                                        <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                        Move
                                                    </button>
                                                </div>
                                                {{ Form::close() }}

                                            </div>
                                        </div>
                                    </div>

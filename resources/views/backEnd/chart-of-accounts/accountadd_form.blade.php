@php
    if (!isset($accountgroupsub2)) {
        $accountgroupsub2 = @App\SysAccountGroupSub2::where('status', 1)->orderBy('title', 'asc')->get();
    }

    if (!isset($accounts)) {
        $com_id = session('logged_session_data.company_id');
        $accounts = @App\SysChartofAccounts::whereRaw("find_in_set($com_id, sys_chartofaccounts.company_access)")
            ->orderBy('group', 'asc')
            ->orderBy('subgroup', 'asc')
            ->orderBy('subgroup2', 'asc')
            ->get();
        $account_tran = @App\SysChartofAccountsTransaction::select(
            'account_id',
            'transaction_date',
            'debit_amount',
            'credit_amount',
        )
            ->where('company_id', $com_id)
            ->where('transaction_type', 'openingbalance')
            ->get();
    }
    $departments = @App\SmHumanDepartment::all();
@endphp



<div class="modal modal-draggable  fade" id="accountModal" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mb-0" id="editModalLabel">Account - {{ @App\SysHelper::get_new_account_code() }}
                </h4>
                <div class="d-flex align-items-center">
                    <a class="btn btn-light btn-sm me-2" style="padding: 2px 5px" href="{{ url('chartofaccounts-import') }}">
                        <i class="ico icon-outline-import text-success"></i> Import
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            {{-- @if (isset($editData))
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-update/' . @$editData->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" value="{{ @$editData->id }}" name="cust_id">
            @else --}}
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'chartofaccounts-store', 'method' => 'POST', 'enctype' => 'multipart/form-data','id'=>'account-form']) }}
            {{-- @endif --}}

            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            <input type="hidden" name="catid" id="catid" value="2">
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body bg-white text-start">
                        <div class="row">
                          

                              <input
                                        class="txtbx primary-input form-control {{ $errors->has('account_code') ? 'is-invalid' : ' ' }}"
                                        type="hidden" name="account_code"
                                        value="{{ isset($editData) ? @$editData->account_code : @App\SysHelper::get_new_account_code() }}"
                                        required>

                        

                                <style>
                                    /* suggestion list initially inside input container */
                                    #account_name_add_list {
                                        position: absolute;
                                        top: 100%;
                                        left: 0;
                                        width: 100%;
                                    }
                                    #account_name_add_list ul {
                                        position: relative;
                                        margin: 0;
                                        padding: 0;
                                        list-style: none;
                                        background: #fff;
                                        border: 1px solid #ddd;
                                        border-top: none;
                                        z-index: 2100; /* should be higher than any modal backdrop */
                                        max-height: 400px;
                                        overflow-y: auto;
                                        overflow-x: hidden;
                                        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
                                        border-radius: 0 0 6px 6px;
                                    }
                                    #account_name_add_list a {
                                      padding: 5px;
                                    }
                                </style>
                            <div class="col-lg-4 mb-2">
                                <div class="input-effect" style="position: relative;">
                                    <label class="form-label"> @lang('Account Name') <span>*</span> </label>
                                    <input
                                        class="txtbx primary-input form-control {{ $errors->has('account_name') ? 'is-invalid' : ' ' }}"
                                        type="text" id="account_name" name="account_name"
                                        value="{{ isset($editData) ? @$editData->account_name : old('account_name') }}"
                                        required>
                                    <div id="account_name_add_list"></div>
                                   <script>
$(document).ready(function() {
    var $list = $('#account_name_add_list');
    var $input = $('#account_name');

    function positionList() {
        var offset = $input.offset();
        $list.css({
            top: offset.top + $input.outerHeight(),
            left: offset.left,
            width: $input.outerWidth()
        });
    }

    $('#account_name').keyup(function(e) {
        var query = $(this).val();

        if (query !== '') {
            $.ajax({
                url: "{{ route('autocomplete.account_name') }}",
                method: "POST",
                data: {
                    query: query,
                    _token: $('input[name="_token"]').val()
                },
                success: function(data) {
                    $list.html(data);
                    // move list to body so it's not clipped by modal overflow
                    $list.appendTo('body');
                    positionList();
                    $list.fadeIn();
                }
            });
        } else {
            $list.fadeOut();
        }

        e.stopPropagation();
    });

    // re-position on window resize/scroll
    $(window).on('resize scroll', positionList);

    // Click on suggestion
    $(document).on('click', '#account_name_add_list li', function(e) {
        $input.val($(this).text());
        $list.fadeOut();
        e.stopPropagation();
    });

    // Click outside → hide list
    $(document).on('click', function() {
        $list.fadeOut();
    });

    // Prevent closing when clicking inside input or list
    $input.on('click', function(e) {
        e.stopPropagation();
        positionList();
    });
    $list.on('click', function(e) {
        e.stopPropagation();
    });
});
</script>

                                </div>
                            </div>


                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Select Sub Group') <span>*</span> </label>
                                    <div class="input-effect" id="sectionSubGroup2Div">
                                        <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" 
                                            name="subgroup2" id="subgroup2" onchange="fn_subgroup()" required>
                                            <option value="0"></option>
                                            @if (isset($accountgroupsub2))
                                                @foreach ($accountgroupsub2 as $val)
                                                    <option value="{{ @$val->id }}"
                                                        @if (isset($editData)) @if (@$editData->subgroup2 == @$val->id) selected @endif
                                                        @endif >{{ @$val->title }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 mb-2">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Department') <span>*</span> </label>
                                    <div class="input-effect" id="sectionDepartmentDiv">
                                        <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" 
                                            name="department_id" id="department_id" required>
                                            <option value=""></option>
                                            @if (isset($departments))
                                                @foreach ($departments as $val)
                                                    <option value="{{ @$val->id }}">{{ @$val->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2 mb-2">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Prepaid/Accrued Exp')  </label>
                                    <div class="input-effect" id="sectionCreditAccountDiv">
                                        <select class="txtbx niceSelect w-100 bb form-control js-example-basic-single" 
                                            name="credit_account_status" id="credit_account_status" required>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                              
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function fn_subgroup() {
                                    if ($('#subgroup2').val() == 6) {
                                        $('.bank_div, .cheque_div').css('display', '');
                                    } else {
                                        $('.bank_div, .cheque_div').css('display', 'none');
                                    }
                                }

                                function fn_stl() {
                                    if ($('#stl').val() == 1) {
                                        $('#stl_limit_div').css('display', '');
                                        $('#stl_limit').prop('required', true);
                                    } else {
                                        $('#stl_limit_div').css('display', 'none');
                                        $('#stl_limit').prop('required', false);
                                    }
                                }
                            </script>

                            <div class="col-lg-4 mb-2 bank_div" style="display: none;">Beneficiary Name
                                <input class="form-control" type="text" name="beneficiary_name"
                                    value="@if (isset($editData)) {{ @$editData->beneficiary_name }} @endif">
                            </div>
                            
                            <div class="col-lg-4 mb-2 bank_div" style="display: none;">Bank Name
                                <input class="form-control" type="text" name="bank_name"
                                    value="@if (isset($editData)) {{ @$editData->bank_name }} @endif">
                            </div>
                            <div class="col-lg-4 mb-2 bank_div" style="display: none;">A/c No.
                                <input class="form-control" type="text" name="acc_no"
                                    value="@if (isset($editData)) {{ @$editData->acc_no }} @endif">
                            </div>
                            <div class="col-lg-4 mb-2 bank_div" style="display: none;">IBAN
                                <input class="form-control" type="text" name="iban"
                                    value="@if (isset($editData)) {{ @$editData->iban }} @endif">
                            </div>
                            <div class="col-lg-4 mb-2 bank_div" style="display: none;">SWIFT Code
                                <input class="form-control" type="text" name="swift_code"
                                    value="@if (isset($editData)) {{ @$editData->swift_code }} @endif">
                            </div>
                            <div class="col-lg-4 mb-2 bank_div" style="display: none;">Finance /Routing Code
                                <input class="form-control" type="text" name="routing_code"
                                    value="@if (isset($editData)) {{ @$editData->routing_code }} @endif">
                            </div>
                            <div class="col-lg-4 mb-2 bank_div" style="display: none;">Branch
                                <input class="form-control" type="text" name="branch"
                                    value="@if (isset($editData)) {{ @$editData->branch }} @endif">
                            </div>
                            <div class="col-lg-4 mb-2 bank_div" style="display: none;">Location
                                <input class="form-control" type="text" name="branch_location"
                                    value="@if (isset($editData)) {{ @$editData->branch_location }} @endif">
                            </div>

                            <div class="col-lg-4 mb-2 bank_div" style="display: none;">Department
                                <input class="form-control" type="text" name="stl_dept"
                                    value="@if (isset($editData)) {{ @$editData->stl_dept }} @endif">
                            </div>
                            
                            <div class="col-lg-4 mb-2 bank_div" style="display: none;">STL
                                <select class="form-control js-example-basic-single" name="stl" id="stl" onchange="fn_stl()">
                                    <option value="0"
                                        @if (isset($editData)) @if (@$editData->stl == 0) selected @endif
                                        @endif>Not Applicable</option>
                                    <option value="1"
                                        @if (isset($editData)) @if (@$editData->stl == 1) selected @endif
                                        @endif>Applicable</option>
                                </select>
                            </div>
                            
                            <div class="col-lg-4 mb-2" id="stl_limit_div" style="display: none;">STL Limit
                                <input class="form-control" type="text" name="stl_limit" id="stl_limit"
                                    value="@if (isset($editData)) {{ @App\SysHelper::com_curr_format(@$editData->stl_limit, 2, '.', ',') }} @endif"
                                    onchange="fn_stl_limit()">
                            </div>
                            <script>
                                function fn_stl_limit() {
                                    $('#stl_limit').val(formatAmount($('#stl_limit').val()));
                                }
                            </script>
                            @if (isset($editData))
                                <script>
                                    $('#stl').change();
                                </script>
                            @endif
                            <!-- toggle for opening balance inputs -->
                            <div class="col-12 mb-2 mt-2">
                                <div class="form-check" >
                                    <input class="form-check-input" data-bs-popover="popover"
                            data-bs-trigger="hover"
                            data-bs-delay="500"
                            data-bs-content="Add Opening Balance"
                            data-bs-placement="top" type="checkbox" id="toggleOpeningBalance">
                                   
                                </div>
                            </div>

                            <div class="col-4 mb-2 opening-balance">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Opening Balance Dr') <span>*</span> </label>
                                    @php
                                        $debit_amount = '0.00';
                                        if (isset($editData_tran)) {
                                            $debit_amount = @$editData_tran->debit_amount;
                                        }
                                    @endphp
                                    <input
                                        class="primary-input form-control {{ $errors->has('debit_amount') ? 'is-invalid' : ' ' }}"
                                        type="text" id="debit_amount" name="debit_amount" value="{{ $debit_amount }}" required>
                                    <span class="focus-border"></span>
                                </div>
                            </div>

                    <script>
    // apply comma formatting on blur (when focus leaves input)
    $(document).ready(function() {

        $('#debit_amount, #credit_amount').on('blur', function() {
            // format the numeric value and update the field
            var formatted = formatAmount($(this).val());
            $(this).val(formatted);
        });

       

    });
</script>

                            <div class="col-4 mb-2 opening-balance">
                                <div class="input-effect">
                                    <label class="form-label"> @lang('Opening Balance Cr') <span>*</span> </label>
                                    @php
                                        $credit_amount = '0.00';
                                        if (isset($editData_tran)) {
                                            $credit_amount = @$editData_tran->credit_amount;
                                        }
                                    @endphp
                                    <input
                                        class="primary-input form-control {{ $errors->has('credit_amount') ? 'is-invalid' : ' ' }}"
                                        type="text" id="credit_amount" name="credit_amount" value="{{ $credit_amount }}" required>
                                    <span class="focus-border"></span>
                                </div>
                            </div>

                            <div class="col-lg-4 mb-2 opening-balance">
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
                                                }else{
                                                $value = @App\SysHelper::normalizeToDmy(App\SysCompany::select('opening_balance_date')->where('id', session('logged_session_data.company_id'))->first()->opening_balance_date);
                                               
                                                }

                                                
                                    @endphp
                                    <input
                                        class="primary-input form-control date-picker {{ $errors->has('opening_balance_date') ? 'is-invalid' : ' ' }}"
                                        type="text" id="opening_balance_date" name="opening_balance_date"
                                        value="{{ $value }}">
                                    <span class="focus-border"></span>
                                </div>
                            </div>

                            
                            {{-- cheque template configuration button when account is bank --}}
                            <div class="col-lg-4 mb-2 bank_div" style="display:none;">
                                <label>Cheque Template</label>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" id="openChequeTemplateBtn" class="btn btn-light btn-sm">
                                    <i class="ico icon-outline-document text-success" style="font-size:15px"></i>    Configure
                                    </button>
                                    <!-- status indicator uses shared class so JS can update either container -->
                                    <span id="cheque_template_status" class="cheque_template_status text-success small"></span>
                                </div>
                            </div>
                        </div>

                        {{-- cheque template hidden fields (loaded only when editing bank accounts) --}}
                        @php
                            $ct = null;
                            if (isset($editData->id)) {
                                $ct = \Illuminate\Support\Facades\DB::table('sys_payment_cheque_template')
                                        ->where('bank_id', $editData->id)
                                        ->orderBy('id', 'desc')
                                        ->first();
                            }
                        @endphp
                        <input type="hidden" name="cheque_company_top" value="{{ $ct->company_top ?? '285px' }}">
                        <input type="hidden" name="cheque_company_left" value="{{ $ct->company_left ?? '425px' }}">
                        <input type="hidden" name="cheque_date_top" value="{{ $ct->date_top ?? '220px' }}">
                        <input type="hidden" name="cheque_date_left" value="{{ $ct->date_left ?? '836px' }}">
                        <input type="hidden" name="cheque_amount_w_top" value="{{ $ct->amount_w_top ?? '316px' }}">
                        <input type="hidden" name="cheque_amount_w_left" value="{{ $ct->amount_w_left ?? '326px' }}">
                        <input type="hidden" name="cheque_amount_top" value="{{ $ct->amount_top ?? '355px' }}">
                        <input type="hidden" name="cheque_amount_left" value="{{ $ct->amount_left ?? '834px' }}">
                        <input type="hidden" name="cheque_font_size" value="{{ $ct->font_size ?? '13px' }}">

                        <script>
                            $('#subgroup2').change();
                        </script>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
               
                    <button class="btn btn-light" id="btnAccountSubmit">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
               
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>



{{-- cheque template modal (copied from company editor) --}}
<div class="modal modal-draggable fade" data-bs-backdrop="false" id="chequeTemplateModal" tabindex="-1" aria-hidden="true" style="z-index:2100;">
    <div class="modal-dialog modal-xl" style="max-width:980px;">
        <div class="modal-content" style="z-index:2101;">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ico icon-outline-banknote text-white me-2"></i>Cheque Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <style>
                    #ct_div_company { position:absolute; z-index:9; background:#f1f1f1; text-align:left; border:1px solid #d3d3d3; top:285px; left:425px; cursor:move; padding:2px 4px; }
                    #ct_div_date    { position:absolute; z-index:9; background:#f1f1f1; text-align:left; border:1px solid #d3d3d3; top:220px; left:836px; cursor:move; padding:2px 4px; }
                    #ct_div_amount_w { position:absolute; z-index:9; background:#f1f1f1; text-align:left; border:1px solid #d3d3d3; width:378px; line-height:28px; top:316px; left:326px; cursor:move; padding:2px 4px; }
                    #ct_div_amount  { position:absolute; z-index:9; background:#f1f1f1; text-align:left; border:1px solid #d3d3d3; top:355px; left:834px; cursor:move; padding:2px 4px; }
                </style>
                @php
                      $company_c = @App\SysCompany::select('company_name')->where('id', session('logged_session_data.company_id'))->first();
            $company_c = $company_c->company_name;
            $cheque_date = "15/01/2025";
            $cheque_amount_w = "Sixty Thousand Eight Hundred Sixty Thousand Eight Hundred Only";
            $cheque_amount = "60,800.00";
                @endphp
                <div style="width:918px; height:605px; border:solid 1px #2e2e2e;">
                    <div id="ct_div_company">{{ $company_c }}</div>
                    <div id="ct_div_date">{{ $cheque_date }}</div>
                    <div id="ct_div_amount_w">{{ $cheque_amount_w }}</div>
                    <div id="ct_div_amount">{{ $cheque_amount }}</div>
                </div>
                <div class="mt-3 d-flex align-items-center gap-3">
                    <label class="mb-0">Font Size:</label>
                    <input type="text" id="ct_font_size" value="13px" class="form-control form-control-sm" style="width:90px" />
                    <button type="button" id="ct_apply_font" class="btn btn-light btn-sm">Apply</button>
                    <span class="text-muted small ms-2">Drag each element to the correct position on the cheque.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" id="saveChequeTemplateBtn"><i class="ico icon-outline-bookmark-opened text-success"></i> Save Template</button>
            </div>
        </div>
    </div>
</div>






<script>
var _ctDefaults = {company_top:'285px',company_left:'425px',date_top:'220px',date_left:'836px',amount_w_top:'316px',amount_w_left:'326px',amount_top:'355px',amount_left:'834px',font_size:'13px'};
var currentAccountChequeTemplate = Object.assign({}, _ctDefaults);
// which form/modal is currently requesting configuration
var currentChequeTargetContainer = null;

function ctInitDrag(){ctDragElement(document.getElementById('ct_div_company'));ctDragElement(document.getElementById('ct_div_date'));ctDragElement(document.getElementById('ct_div_amount_w'));ctDragElement(document.getElementById('ct_div_amount'));}
function ctDragElement(elmnt){if(!elmnt)return;var pos1=0,pos2=0,pos3=0,pos4=0;elmnt.onmousedown=function(e){e=e||window.event; e.preventDefault();pos3=e.clientX;pos4=e.clientY;document.onmouseup=function(){document.onmouseup=null;document.onmousemove=null;};document.onmousemove=function(e){e=e||window.event; e.preventDefault();pos1=pos3-e.clientX;pos2=pos4-e.clientY;pos3=e.clientX;pos4=e.clientY;elmnt.style.top=(elmnt.offsetTop-pos2)+'px';elmnt.style.left=(elmnt.offsetLeft-pos1)+'px';};};}

// read values from a modal/container and update global template object & status indicator
function ctLoadFromContainer($container) {
    if(!$container || !$container.length) return;
    var t = {
        company_top: $container.find('input[name="cheque_company_top"]').val() || _ctDefaults.company_top,
        company_left: $container.find('input[name="cheque_company_left"]').val() || _ctDefaults.company_left,
        date_top: $container.find('input[name="cheque_date_top"]').val() || _ctDefaults.date_top,
        date_left: $container.find('input[name="cheque_date_left"]').val() || _ctDefaults.date_left,
        amount_w_top: $container.find('input[name="cheque_amount_w_top"]').val() || _ctDefaults.amount_w_top,
        amount_w_left: $container.find('input[name="cheque_amount_w_left"]').val() || _ctDefaults.amount_w_left,
        amount_top: $container.find('input[name="cheque_amount_top"]').val() || _ctDefaults.amount_top,
        amount_left: $container.find('input[name="cheque_amount_left"]').val() || _ctDefaults.amount_left,
        font_size: $container.find('input[name="cheque_font_size"]').val() || _ctDefaults.font_size
    };
    var isDefault = t.company_top === _ctDefaults.company_top && t.company_left === _ctDefaults.company_left;
    currentAccountChequeTemplate = Object.assign({}, _ctDefaults, t);
    $container.find('.cheque_template_status').text(isDefault ? '' : 'Template configured ✓');
}

function openChequeTemplateModal(){
    // make sure the target container is set and load from it
    var $src = currentChequeTargetContainer || $('#accountModal');
    ctLoadFromContainer($src);
    $('#ct_div_company').css({top:currentAccountChequeTemplate.company_top,left:currentAccountChequeTemplate.company_left,'font-size':currentAccountChequeTemplate.font_size});
    $('#ct_div_date').css({top:currentAccountChequeTemplate.date_top,left:currentAccountChequeTemplate.date_left,'font-size':currentAccountChequeTemplate.font_size});
    $('#ct_div_amount_w').css({top:currentAccountChequeTemplate.amount_w_top,left:currentAccountChequeTemplate.amount_w_left,'font-size':currentAccountChequeTemplate.font_size});
    $('#ct_div_amount').css({top:currentAccountChequeTemplate.amount_top,left:currentAccountChequeTemplate.amount_left,'font-size':currentAccountChequeTemplate.font_size});
    var ctModal=new bootstrap.Modal(document.getElementById('chequeTemplateModal'),{backdrop:false});ctModal.show();document.getElementById('chequeTemplateModal').addEventListener('shown.bs.modal',function handler(){ctInitDrag();this.removeEventListener('shown.bs.modal',handler);});
}

// bind click for both add and edit buttons
$(document).on('click','#openChequeTemplateBtn,#openChequeTemplateBtnEdit',function(){
    currentChequeTargetContainer = $(this).closest('.modal');
    // ensure modal is attached to body so it stacks above side panel
    $('#chequeTemplateModal').appendTo('body').addClass('venus-app');
    openChequeTemplateModal();
});

$(document).on('click','#ct_apply_font',function(){var s=$('#ct_font_size').val()||'13px';$('#ct_div_company,#ct_div_date,#ct_div_amount_w,#ct_div_amount').css('font-size',s)});

$(document).on('click','#saveChequeTemplateBtn',function(){
    currentAccountChequeTemplate={company_top:$('#ct_div_company').css('top'),company_left:$('#ct_div_company').css('left'),date_top:$('#ct_div_date').css('top'),date_left:$('#ct_div_date').css('left'),amount_w_top:$('#ct_div_amount_w').css('top'),amount_w_left:$('#ct_div_amount_w').css('left'),amount_top:$('#ct_div_amount').css('top'),amount_left:$('#ct_div_amount').css('left'),font_size:$('#ct_font_size').val()||'13px'};
    var $target = currentChequeTargetContainer ? currentChequeTargetContainer : $('body');
    $target.find('input[name="cheque_company_top"]').val(currentAccountChequeTemplate.company_top);
    $target.find('input[name="cheque_company_left"]').val(currentAccountChequeTemplate.company_left);
    $target.find('input[name="cheque_date_top"]').val(currentAccountChequeTemplate.date_top);
    $target.find('input[name="cheque_date_left"]').val(currentAccountChequeTemplate.date_left);
    $target.find('input[name="cheque_amount_w_top"]').val(currentAccountChequeTemplate.amount_w_top);
    $target.find('input[name="cheque_amount_w_left"]').val(currentAccountChequeTemplate.amount_w_left);
    $target.find('input[name="cheque_amount_top"]').val(currentAccountChequeTemplate.amount_top);
    $target.find('input[name="cheque_amount_left"]').val(currentAccountChequeTemplate.amount_left);
    $target.find('input[name="cheque_font_size"]').val(currentAccountChequeTemplate.font_size);
    $target.find('.cheque_template_status').text('Template configured ✓');
    var ctModal=bootstrap.Modal.getInstance(document.getElementById('chequeTemplateModal'));
    if(ctModal)ctModal.hide();
});

$(document).ready(function(){
    ctLoadFromContainer($('#accountModal')); // initialize add modal status
    // ensure cheque modal backdrops also stack
    $('#chequeTemplateModal').on('show.bs.modal', function(){
        $(this).appendTo('body');
    });    
    // ensure opening balance fields start hidden
    $('.opening-balance').hide();

    // opening balance toggle logic
    $('#toggleOpeningBalance').change(function() {
        if (this.checked) {
            $('.opening-balance').show();
        } else {
            $('.opening-balance').hide();
        }
    });
    
    // validation before account form submission
    $('#account-form').on('submit', function(e) {
        var $form = $(this);
        var subgroup = $form.find('#subgroup2').val();
        var acctname = $.trim($form.find('#account_name').val());
        if (!subgroup || subgroup === '0' || acctname === '') {
            e.preventDefault();
            toastr.error('Please provide both sub group and account name.', 'Error');
            if (acctname === '') {
                $form.find('#account_name').focus();
            } else {
                $form.find('#subgroup2').focus();
            }
        }
    });
  });
</script>















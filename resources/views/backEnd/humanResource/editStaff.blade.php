@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    @php
        function showPicName($data)
        {
            $name = explode('/', $data);
            return $name[3];
        }
    @endphp

    @if (Illuminate\Support\Facades\Config::get('app.app_sync'))
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'admin-dashboard', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
    @else
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'staffUpdate', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
    @endif

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        Edit User
                    </h4>
                    <div class="purchase-order-content-header-right">
                        @if (Illuminate\Support\Facades\Config::get('app.app_sync'))
                            <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Disabled For Demo ">
                                <button class="primary-btn small fix-gr-bg  demo_view" style="pointer-events: none;"
                                    type="button" disabled>
                                    @lang('Update User')</button></span>
                        @else
                            <button class="btn btn-light" type="submit" onclick="return validateCheckboxes()">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                                User
                            </button>
                        @endif
                          <a class="btn btn-light" href="{{ url('staff-directory') }}"> <i class="ico icon-outline-add-square text-success"></i> Add
                        </a>
                        <a class="btn btn-light" href="{{ url('staff-directory') }}">User List
                        </a>
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                @if (session()->has('message-success'))
                                    <div class="alert alert-success">
                                        {{ session()->get('message-success') }}
                                    </div>
                                @elseif(session()->has('message-danger'))
                                    <div class="alert alert-danger">
                                        {{ session()->get('message-danger') }}
                                    </div>
                                @endif
                                <div class="white-box">
                                    <div class="">

                                        <input type="hidden" name="staff_id" value="{{ @$editData->id }}">
                                        <div class="row mb-4">
                                            <div class="col-lg-3">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('User No')</label>
                                                    <input class="primary-input " type="hidden" name="staff_no"
                                                        value="{{ @$editData->staff_no }}" readonly>
                                                    <input class="form-control" type="text"
                                                        value="{{ $editData->staff_no }}" readonly>
                                                    <input class="form-control" type="hidden" readonly
                                                        value="@if (isset($editData)) {{ @$editData->staff_no }} @endif">
                                                </div>
                                            </div>

                                            @if (Auth::user()->role_id == 1)
                                                @php
                                                    //$role_change='disabled';
                                                    $role_change = '';
                                                @endphp
                                                <input type="hidden" name="role_id" value="{{ $editData->role_id }}" />
                                            @else
                                                @php
                                                    $role_change = '';
                                                @endphp
                                            @endif
                                            <div class="col-lg-3">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('Role')</label>
                                                    <select class="form-control js-example-basic-single"
                                                        {{ $role_change }} name="role_id" id="role_id" required>
                                                        <option data-display="@lang('lang.role') *" value="">
                                                            @lang('lang.select')</option>
                                                        @foreach ($roles as $key => $value)
                                                            <option value="{{ @$value->id }}"
                                                                @if (isset($editData)) @if (@$editData->role_id == @$value->id) selected @endif
                                                                @endif >{{ @$value->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <script>
                                                function fn_role_id() {
                                                    if ($('#is_target').val() == 1) {
                                                        $('#target_div1').css('display', '');
                                                        $('#target_div2').css('display', '');
                                                        $('#revenue_target_weekly').prop('required', true);
                                                        $('#revenue_target_monthly').prop('required', true);
                                                        $('#revenue_target_quaterly').prop('required', true);
                                                        $('#revenue_target_yearly').prop('required', true);
                                                        $('#gp_target_weekly').prop('required', true);
                                                        $('#gp_target_monthly').prop('required', true);
                                                        $('#gp_target_quaterly').prop('required', true);
                                                        $('#gp_target_yearly').prop('required', true);
                                                        $('#target_month_from').prop('required', true);
                                                    } else {
                                                        $('#target_div1').css('display', 'none');
                                                        $('#target_div2').css('display', 'none');
                                                        $('#revenue_target_weekly').prop('required', false);
                                                        $('#revenue_target_monthly').prop('required', false);
                                                        $('#revenue_target_quaterly').prop('required', false);
                                                        $('#revenue_target_yearly').prop('required', false);
                                                        $('#gp_target_weekly').prop('required', false);
                                                        $('#gp_target_monthly').prop('required', false);
                                                        $('#gp_target_quaterly').prop('required', false);
                                                        $('#gp_target_yearly').prop('required', false);
                                                        $('#target_month_from').prop('required', false);
                                                    }
                                                }
                                            </script>
                                            <div class="col-lg-3">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('Company')</label>
                                                    <select class="form-control js-example-basic-single" name="company_id"
                                                        id="company_id" disabled>
                                                        <option data-display="@lang('Company Name') *" value="">
                                                            @lang('lang.select') </option>
                                                        @foreach ($company1 as $key => $value)
                                                            <option value="{{ @$value->id }}"
                                                                @if (isset($editData)) @if (1 == @$value->id)
                                    selected @endif
                                                                @endif
                                                                >{{ $value->company_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('Main Company') <span>*</span> </label>
                                                    <select class="form-control js-example-basic-single" name="main_company"
                                                        id="main_company" required>
                                                        <option value=""></option>
                                                        @foreach ($company1 as $key => $value)
                                                            <option value="{{ @$value->id }}"
                                                                @if (isset($editData)) @if ($editData->main_company == @$value->id)
                                            selected @endif
                                                                @endif
                                                                >{{ @$value->company_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-4">
                                            <div class="col-lg-4 mb-4">
                                                <div class="input-effect">
                                                    <label class="form-label">@lang('Company Access') <span>*</span> (Drag to
                                                        Reorder)</label>
                                                    <div class="form-control" style="height: auto;">
                                                        <div id="sortable-list">
                                                            @foreach ($company as $key => $value)
                                                                <div class="form-check" data-id="{{ @$value->id }}">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        name="company_access[]" value="{{ @$value->id }}"
                                                                        id="company_access_{{ @$value->id }}"
                                                                        {{ in_array(@$value->id, old('company_access', [])) ? 'checked' : '' }}
                                                                        @if (isset($editData)) @if (!empty($editData->company_access))
                                                            @php
                                                                $companyAccessIds = explode(',', $editData->company_access);
                                                            @endphp
                                                            @if (in_array($value->id, $companyAccessIds)) checked @endif
                                                                        @endif
                                                            @endif
                                                            ><label class="form-check-label"
                                                                for="company_access_{{ @$value->id }}">{{ @$value->company_name }}</label>
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
                                                <script>
                                                    new Sortable(document.getElementById('sortable-list'), {
                                                        handle: '.form-check',
                                                        animation: 150,
                                                        onEnd: function(evt) {
                                                            const reorderedIds = Array.from(evt.from.children).map(item => item.getAttribute('data-id'));
                                                            console.log("Reordered IDs:", reorderedIds);
                                                        }
                                                    });

                                                    function validateCheckboxes() {
                                                        const checkedCheckboxes = document.querySelectorAll('input[name="company_access[]"]:checked');

                                                        // If no checkbox is checked, show an alert and prevent form submission
                                                        if (checkedCheckboxes.length === 0) {
                                                            alert('Please Select Company Access.');
                                                            return false; // Prevent form submission
                                                        }

                                                        return true; // Allow form submission
                                                    }
                                                </script>

                                                {{-- <select class="form-control js-example-basic-single" name="company_access[]" id="company_access" multiple required>
                                        <option value=""></option>
                                        @foreach ($company as $key => $value)
                                            <option value="{{ @$value->id }}"
                                                @if (isset($editData))
                                                @if (!empty($editData->company_access))
                                                    @if (str_contains($editData->company_access, $value->id)) selected @endif
                                                @endif
                                            @endif >{{ @$value->company_name }}</option>
                                        @endforeach
                                    </select> --}}
                                            </div>
                                        </div>
                                        <div class="col-lg-8 mb-4">
                                            <div class="row">
                                                <div class="col-lg-4 mb-4">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('Type') <span>*</span> </label>
                                                        <select class="form-control" name="type" id="type"
                                                            required>
                                                            <option value="1"
                                                                @if ($editData->type == 1) selected @endif>Channel
                                                            </option>
                                                            <option value="2"
                                                                @if ($editData->type == 2) selected @endif>
                                                                Distribution</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('Department')</label>
                                                        <select class="form-control js-example-basic-single"
                                                            name="department_id" id="department_id">
                                                            <option data-display="@lang('lang.department') *" value="">
                                                                @lang('lang.select') </option>
                                                            @foreach ($departments as $key => $value)
                                                                <option value="{{ @$value->id }}"
                                                                    @if (isset($editData)) @if (@$editData->department_id == @$value->id)
                                                    selected @endif
                                                                    @endif
                                                                    >{{ $value->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('Designation')</label>
                                                        <select class="form-control js-example-basic-single"
                                                            name="designation_id" id="designation_id">
                                                            <option data-display="@lang('lang.designation') *" value="">
                                                                @lang('lang.select') </option>
                                                            @foreach ($designations as $key => $value)
                                                                <option value="{{ @$value->id }}"
                                                                    @if (isset($editData)) @if (@$editData->designation_id == @$value->id)
                                                        selected @endif
                                                                    @endif
                                                                    >{{ @$value->title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('First Name')</label>
                                                        <input class="form-control" type="text" name="first_name"
                                                            value="@if (isset($editData)) {{ @$editData->first_name }} @endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('Last Name')</label>
                                                        <input class="form-control" type="text" name="last_name"
                                                            value="@if (isset($editData)) {{ @$editData->last_name }} @endif">
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('Mobile')</label>
                                                        <input class="form-control" type="text" name="mobile"
                                                            value="@if (isset($editData)) {{ @$editData->mobile }} @endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="input-effect">
                                                        <label class="form-label">User Name / Email</label>
                                                        <input class="form-control" type="email" name="email"
                                                            value="@if (isset($editData)) {{ @$editData->email }} @endif"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('Password')</label>
                                                        <input class="form-control" type="text" name="password">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('Emergency Mobile')</label>
                                                        <input class="form-control" type="text"
                                                            name="emergency_mobile"
                                                            value="@if (isset($editData)) {{ @$editData->emergency_mobile }} @endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mb-4">
                                                    <div class="input-effect">
                                                        <label class="form-label">Ext No<span>*</span> </label>
                                                        <input class="form-control" type="text" name="ext_no"
                                                            value="@if (isset($editData)) {{ @$editData->ext_no }} @endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('Gender')</label>
                                                        <select class="form-control" name="gender_id">
                                                            <option data-display="@lang('lang.gender') *" value="">
                                                                @lang('lang.gender') *</option>
                                                            @foreach ($genders as $gender)
                                                                <option value="{{ @$gender->id }}"
                                                                    @if (isset($editData)) @if (@$editData->gender_id == @$gender->id)
                                                        selected @endif
                                                                    @endif
                                                                    >{{ @$gender->base_setup_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="input-effect">
                                                                <label class="form-label">@lang('Date of Joining')</label>
                                                                <input class="form-control date-picker" id="date_of_joining"
                                                                    type="text" required name="date_of_joining"
                                                                    value="{{ @App\SysHelper::normalizeToDmy(@$editData->date_of_joining) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="no-gutters input-right-icon">
                                                        <div class="col">
                                                            <div class="input-effect">
                                                                <label class="form-label">@lang('Date of Resign')</label>
                                                                <?php if (@$editData->date_of_resign == '') {
                                                                    $date_of_resign = '';
                                                                } else {
                                                                    $date_of_resign = $editData->date_of_resign;
                                                                } ?>
                                                                <input class="form-control date-picker" id="date_of_resign"
                                                                    type="text" name="date_of_resign"
                                                                    value="{{ @App\SysHelper::normalizeToDmy(@$date_of_resign) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="row no-gutters input-right-icon mb-20">
                                                        <div class="col">
                                                            <div class="input-effect">
                                                                <label class="form-label">@lang('User Photo')</label>
                                                                <input type="file" class="form-control"
                                                                    name="staff_photo" id="staff_photo">
                                                            </div>
                                                        </div>
                                            
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 mt-2">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('Set Sales Target')</label>
                                                        <select class="form-control" name="is_target" id="is_target"
                                                            onchange="fn_role_id()">
                                                            <option value="0"
                                                                @if (isset($editData)) @if (@$editData->is_target == 0) selected @endif
                                                                @endif >No</option>
                                                            <option value="1"
                                                                @if (isset($editData)) @if (@$editData->is_target == 1) selected @endif
                                                                @endif >Yes</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-9 mt-2">
                                                    <div class="input-effect">
                                                        <label class="form-label">@lang('Brands')</label>
                                                        <select class="form-control js-example-basic-single"
                                                            name="brands[]" id="brands" multiple>
                                                            @foreach ($brand_list as $value)
                                                                <option value="{{ @$value->id }}"
                                                                    @if (in_array($value->id, $selected_brands)) selected @endif>
                                                                    {{ @$value->title }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-30" id="target_div1" style="display: none;">
                                        <div class="col-lg-12">
                                            <b>Sales Target</b>
                                            <hr />
                                        </div>
                                        <div class="col-lg-3 mb-4">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">Revenue Target Weekly<span>*</span> </label>
                                                        <input class="form-control" id="revenue_target_weekly"
                                                            type="number" step="Any" name="revenue_target_weekly"
                                                            value="{{ @$editData->revenue_target_weekly }}"
                                                            onchange="set_rt()">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-4">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">Revenue Target Monthly<span>*</span> </label>
                                                        <input class="form-control" id="revenue_target_monthly"
                                                            type="number" step="Any" name="revenue_target_monthly"
                                                            value="{{ @$editData->revenue_target_monthly }}"
                                                            onchange="set_rt()">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-4">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">Revenue Target Quaterly<span>*</span>
                                                        </label>
                                                        <input class="form-control" id="revenue_target_quaterly"
                                                            type="number" step="Any" name="revenue_target_quaterly"
                                                            value="{{ @$editData->revenue_target_quaterly }}"
                                                            onchange="set_rt()">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-4">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">Revenue Target Yearly<span>*</span> </label>
                                                        <input class="form-control" id="revenue_target_yearly"
                                                            type="number" step="Any" name="revenue_target_yearly"
                                                            value="{{ @$editData->revenue_target_yearly }}"
                                                            onchange="set_rt()">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-30" id="target_div2" style="display: none;">
                                        <div class="col-lg-3 mb-4">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">GP Target Weekly<span>*</span> </label>
                                                        <input class="form-control" id="gp_target_weekly" type="number"
                                                            step="Any" name="gp_target_weekly"
                                                            value="{{ @$editData->gp_target_weekly }}"
                                                            onchange="set_gt()">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-4">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">GP Target Monthly<span>*</span> </label>
                                                        <input class="form-control" id="gp_target_monthly" type="number"
                                                            step="Any" name="gp_target_monthly"
                                                            value="{{ @$editData->gp_target_monthly }}"
                                                            onchange="set_gt()">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-4">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">GP Target Quaterly<span>*</span> </label>
                                                        <input class="form-control" id="gp_target_quaterly"
                                                            type="number" step="Any" name="gp_target_quaterly"
                                                            value="{{ @$editData->gp_target_quaterly }}"
                                                            onchange="set_gt()">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-4">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">GP Target Yearly<span>*</span> </label>
                                                        <input class="form-control" id="gp_target_yearly" type="number"
                                                            step="Any" name="gp_target_yearly"
                                                            value="{{ @$editData->gp_target_yearly }}"
                                                            onchange="set_gt()">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 mb-2">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">Target From Date<span></span> </label>
                                                        <input class="form-control" id="target_month_from" type="month"
                                                            name="target_month_from"
                                                            value="{{ \Carbon\Carbon::parse($editData->target_month_from)->format('Y-m') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-9 mb-2">
                                            <div class="no-gutters input-right-icon">
                                                <div class="col">
                                                    <div class="input-effect">
                                                        <label class="form-label">Combine User<span></span> </label>
                                                        <select class="form-control js-example-basic-single"
                                                            name="combind_user_id[]" multiple>
                                                            @foreach ($staff as $value)
                                                                <option value="{{ @$value->user_id }}"
                                                                    @if (isset($editData)) @if (!empty($editData->combind_user_id))
                                                            @php
                                                                $combindUserIds = explode(',', $editData->combind_user_id);
                                                            @endphp
                                                            @if (in_array($value->user_id, $combindUserIds)) selected @endif
                                                                    @endif
                                                            @endif
                                                            >{{ @$value->full_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        $('#is_target').change();
                                    </script>

                                    <script>
                                        $(document).ready(function() {
                                            // Update values based on Weekly input
                                            $('#revenue_target_weekly').on('input', function() {
                                                var weekly = parseFloat($(this).val());
                                                if (!isNaN(weekly)) {
                                                    var monthly = (weekly * 4).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var quarterly = (weekly * 13).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var yearly = (weekly * 52).toFixed(@json(session('logged_session_data.decimal_point')));

                                                    $('#revenue_target_monthly').val(monthly);
                                                    $('#revenue_target_quaterly').val(quarterly);
                                                    $('#revenue_target_yearly').val(yearly);
                                                }
                                            });

                                            // Update values based on Monthly input
                                            $('#revenue_target_monthly').on('input', function() {
                                                var monthly = parseFloat($(this).val());
                                                if (!isNaN(monthly)) {
                                                    var weekly = (monthly / 4).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var quarterly = (monthly * 3).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var yearly = (monthly * 12).toFixed(@json(session('logged_session_data.decimal_point')));

                                                    $('#revenue_target_weekly').val(weekly);
                                                    $('#revenue_target_quaterly').val(quarterly);
                                                    $('#revenue_target_yearly').val(yearly);
                                                }
                                            });

                                            // Update values based on Quarterly input
                                            $('#revenue_target_quaterly').on('input', function() {
                                                var quarterly = parseFloat($(this).val());
                                                if (!isNaN(quarterly)) {
                                                    var weekly = (quarterly / 13).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var monthly = (quarterly / 3).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var yearly = (quarterly * 4).toFixed(@json(session('logged_session_data.decimal_point')));

                                                    $('#revenue_target_weekly').val(weekly);
                                                    $('#revenue_target_monthly').val(monthly);
                                                    $('#revenue_target_yearly').val(yearly);
                                                }
                                            });

                                            // Update values based on Yearly input
                                            $('#revenue_target_yearly').on('input', function() {
                                                var yearly = parseFloat($(this).val());
                                                if (!isNaN(yearly)) {
                                                    var weekly = (yearly / 52).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var monthly = (yearly / 12).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var quarterly = (yearly / 4).toFixed(@json(session('logged_session_data.decimal_point')));

                                                    $('#revenue_target_weekly').val(weekly);
                                                    $('#revenue_target_monthly').val(monthly);
                                                    $('#revenue_target_quaterly').val(quarterly);
                                                }
                                            });

                                            // Update values based on Weekly input
                                            $('#gp_target_weekly').on('input', function() {
                                                var weekly = parseFloat($(this).val());
                                                if (!isNaN(weekly)) {
                                                    var monthly = (weekly * 4).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var quarterly = (weekly * 13).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var yearly = (weekly * 52).toFixed(@json(session('logged_session_data.decimal_point')));

                                                    $('#gp_target_monthly').val(monthly);
                                                    $('#gp_target_quaterly').val(quarterly);
                                                    $('#gp_target_yearly').val(yearly);
                                                }
                                            });

                                            // Update values based on Monthly input
                                            $('#gp_target_monthly').on('input', function() {
                                                var monthly = parseFloat($(this).val());
                                                if (!isNaN(monthly)) {
                                                    var weekly = (monthly / 4).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var quarterly = (monthly * 3).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var yearly = (monthly * 12).toFixed(@json(session('logged_session_data.decimal_point')));

                                                    $('#gp_target_weekly').val(weekly);
                                                    $('#gp_target_quaterly').val(quarterly);
                                                    $('#gp_target_yearly').val(yearly);
                                                }
                                            });

                                            // Update values based on Quarterly input
                                            $('#gp_target_quaterly').on('input', function() {
                                                var quarterly = parseFloat($(this).val());
                                                if (!isNaN(quarterly)) {
                                                    var weekly = (quarterly / 13).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var monthly = (quarterly / 3).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var yearly = (quarterly * 4).toFixed(@json(session('logged_session_data.decimal_point')));

                                                    $('#gp_target_weekly').val(weekly);
                                                    $('#gp_target_monthly').val(monthly);
                                                    $('#gp_target_yearly').val(yearly);
                                                }
                                            });

                                            // Update values based on Yearly input
                                            $('#gp_target_yearly').on('input', function() {
                                                var yearly = parseFloat($(this).val());
                                                if (!isNaN(yearly)) {
                                                    var weekly = (yearly / 52).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var monthly = (yearly / 12).toFixed(@json(session('logged_session_data.decimal_point')));
                                                    var quarterly = (yearly / 4).toFixed(@json(session('logged_session_data.decimal_point')));

                                                    $('#gp_target_weekly').val(weekly);
                                                    $('#gp_target_monthly').val(monthly);
                                                    $('#gp_target_quaterly').val(quarterly);
                                                }
                                            });
                                        });
                                    </script>


                                    <div class="row mt-40">
                                        <div class="col-lg-12 text-center">


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    @if (count($target) > 0)
                        <div class="row">
                            <div class="col-lg-12">
                                <table id="table_id" class="table table-hover" cellspacing="0" width="100%">
                                    <tr>
                                        <td>Target Month</td>
                                        <td>Revenue Target Weekly</td>
                                        <td>Revenue Target Monthly</td>
                                        <td>Revenue Target Quaterly</td>
                                        <td>Revenue Target Yearly</td>
                                        <td>GP Target Weekly</td>
                                        <td>GP Target Monthly</td>
                                        <td>GP Target Quaterly</td>
                                        <td>GP Target Yearly</td>
                                    </tr>
                                    @foreach ($target as $item)
                                        <tr>
                                            <td>{{ date('M-Y', strtotime($item->target_month_from)) }}</td>
                                            <td>{{ @App\SysHelper::com_curr_format(@$item->revenue_target_weekly, 2, '.', ',') }}
                                            </td>
                                            <td>{{ @App\SysHelper::com_curr_format(@$item->revenue_target_monthly, 2, '.', ',') }}
                                            </td>
                                            <td>{{ @App\SysHelper::com_curr_format(@$item->revenue_target_quaterly, 2, '.', ',') }}
                                            </td>
                                            <td>{{ @App\SysHelper::com_curr_format(@$item->revenue_target_yearly, 2, '.', ',') }}
                                            </td>
                                            <td>{{ @App\SysHelper::com_curr_format(@$item->gp_target_weekly, 2, '.', ',') }}
                                            </td>
                                            <td>{{ @App\SysHelper::com_curr_format(@$item->gp_target_monthly, 2, '.', ',') }}
                                            </td>
                                            <td>{{ @App\SysHelper::com_curr_format(@$item->gp_target_quaterly, 2, '.', ',') }}
                                            </td>
                                            <td>{{ @App\SysHelper::com_curr_format(@$item->gp_target_yearly, 2, '.', ',') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>


        </div>
    </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#btnSubmit").click(function() {
                setTimeout(function() {
                    disableButton();
                }, 0);
            });

            function disableButton() {
                $("#btnSubmit").prop('disabled', true);
            }
        });
    </script>
@endsection

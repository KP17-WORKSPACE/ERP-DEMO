@extends('backEnd.masterpage')
@section('mainContent')
    <?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">AMC Request List</h2>
                <span class="page-label">Home - AMC Request List</span>
            </div>
            <div>
                <a class="btn btn-info" data-toggle="modal" data-target="#ModalCopyLink" data-backdrop="static"
                    data-keyboard="false">Copy URL</a>
                <a class="btn btn-primary" id="btn_add_new_amc" data-toggle="modal" data-target="#ModalAddNewAMCService"
                    data-backdrop="static" data-keyboard="false">Add Request</a>
                <a class="btn btn-info" href="{{ url('crm-amc-list') }}">AMC List</a>

                <button type="button" class="btn btn-warning" data-toggle="collapse" href="#collapseExample" role="button"
                    aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
            </div>
        </div>
        <div class="collapse" id="collapseExample">
            <div class="card shadow mb-4 p-4">

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-request-list', 'method' => 'POST', 'id' => 'crm-amc-search']) }}
                <div class="row">
                    <div class="col-md-1 mb-2">
                        <label for="" class="form-check-label">AMC ID</label>
                        <input class="form-control" id="search_amc_id" type="text" autocomplete="off"
                            name="search_amc_id" value="{{ $ctrl_amc_id }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Customer Name</label>
                        <select class="form-control js-example-basic-single" name="search_customer_name"
                            id="search_customer_name">
                            <option value="">-Select-</option>
                            @foreach ($customer as $value)
                                <option value="{{ @$value->id }}" @if ($ctrl_customer_name == $value->id) selected @endif>
                                    {{ @$value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Service Enginer</label>
                        <select class="form-control" name="search_service_enginer" id="search_service_enginer">
                            <option value="">Select</option>
                            <option @if($ctrl_service_enginer == "NA") selected @endif value="NA">N/A (Not Allocated)</option>
                            @if (count($engineer_list) > 0)
                                @foreach ($engineer_list as $list)
                                    <option value="{{ $list->user_id }}" @if ($ctrl_service_enginer == $list->user_id) selected @endif>
                                        {{ $list->full_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Service Date From</label>
                        <input class="form-control" id="search_from_date" type="date" autocomplete="off"
                            name="search_from_date" value="{{ $ctrl_from_date }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Service Date To</label>
                        <input class="form-control" id="search_to_date" type="date" autocomplete="off"
                            name="search_to_date" value="{{ $ctrl_to_date }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Status</label>
                        <select class="form-control" name="search_status" id="search_status">
                            <option value="">Select</option>
                            <option value="2,3" @if ($ctrl_search_status == '2,3') selected @endif>Pending</option>
                            <option value="5" @if ($ctrl_search_status == '5') selected @endif>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-1 mb-2">
                        <label for="" class="form-check-label">&nbsp;</label><br />
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Search</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="" width="100%" cellspacing="0">
                        <thead>
                            @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
                                <tr>
                                    <td colspan="7">
                                        @if (session()->has('message-success'))
                                            <div class="alert alert-success">
                                                {{ session()->get('message-success') }}
                                            </div>
                                        @elseif(session()->has('message-danger'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger') }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <th>@lang('Track ID')</th>
                                <th>@lang('Deal ID')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Customer Name')</th>
                                <th>@lang('Service Enginer')</th>
                                <th>@lang('Scope of Work')</th>
                                <th>@lang('Service Date')</th>
                                <th>@lang('Service Time')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Attachment')</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($amcdata as $value)
                                <tr @if ($value->status == 3) style="color:#ff0000 !important;" @endif
                                    @if (@$value->is_delete == 1) class="bg-dark" @endif>
                                    <td><a target="_blank"
                                            href="{{ url('crm-amc-service-request-detail/' . @$value->id) }}">{{ @$value->doc_number }}</a>
                                    </td>
                                    <td>
                                        @if (@$value->code == '')
                                            --
                                        @else
                                            {{ @$value->code }}
                                        @endif
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime(@$value->date)) }}</td>
                                    <td>{{ @$value->custname->name }}</td>

                                    @if ($value->service_engineer)
                                        <?php
                                        $st = explode(',', $value->service_engineer);
                                        $engineername = '';
                                        
                                        if (count($st) > 0) {
                                            foreach ($st as $u) {
                                                $s = $staff->where('user_id', $u)->pluck('full_name');
                                                if ($engineername == '') {
                                                    $engineername .= $s[0];
                                                } else {
                                                    $engineername .= ', ' . $s[0];
                                                }
                                            }
                                        }
                                        ?>
                                        <td>{{ @$engineername }}</td>
                                    @else
                                        <td>N/A</td>
                                    @endif

                                    <td>

                                        @php
                                            $work = '';
                                            $scope_of_work = $amc_work->where('amc_id', $value->id);
                                            if (count($scope_of_work) > 0) {
                                                foreach ($scope_of_work as $sw) {
                                                    if ($work == '') {
                                                        $work .= $sw->work;
                                                    } else {
                                                        $work .= '<br />' . $sw->work;
                                                    }
                                                }
                                            }
                                        @endphp
                                        {!! $work !!}
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime(@$value->service_date)) }}</td>
                                    <td>{{ date('h:i A', strtotime(@$value->service_time)) }}</td>

                                    <td>
                                        
                                        {!! @App\SysHelper::get_amc_status($value->status) !!}
                                    </td>

                                    <td>
                                        @if (@$value->attachment == '')
                                            <span class="text-danger"></span>
                                        @else
                                            <a target="_blank"
                                                href="{{ asset('public/uploads/crm_amc_doc/') }}/{{ @$value->attachment }}">View</a>
                                        @endif
                                    </td>

                                    {{-- <td>
                                        <a class="text-primary" data-toggle="modal"
                                            data-target="#servicecomments_{{ $value->id }}" data-backdrop="static"
                                            data-keyboard="false">View</a>
                                        <span class="text-success"></span>
                                    </td> --}}
                                    <td>
                                        <a class="btn-sm btn-primary" data-toggle="modal"
                                            data-target="#servicecomments_{{ $value->id }}" style="cursor: pointer;"
                                            data-backdrop="static" data-keyboard="false"><i class="fa fa-comments"
                                                aria-hidden="true"></i></a>

                                        <a target="_blank" class="btn-sm btn-warning"
                                            href="{{ asset('public/uploads/crm_amc_doc/') }}/{{ @$value->attachment }}"><i
                                                class="fa fa-download" aria-hidden="true"></i>
                                        </a>
                                        <a class="btn-sm btn-primary"
                                            onclick="edit_service_request({{ $value->id }})"><i class="fa fa-edit"
                                                aria-hidden="true"></i></a>
                                        @if (@$value->is_delete == 0)
                                            <a class="btn-sm btn-danger" onclick="return confirm('Are you sure?')"
                                                href="{{ url('crm-amc-service-request-deactivate/' . $value->id . '') }}"><i
                                                    class="fa fa-trash" aria-hidden="true"></i></a>
                                        @endif
                                        @if (@$value->is_delete == 1)
                                            <a class="btn-sm btn-info" onclick="return confirm('Are you sure?')"
                                                href="{{ url('crm-amc-service-request-activate/' . $value->id . '') }}"><i
                                                    class="fa fa-recycle" aria-hidden="true"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>


    @if (count($amcdata) > 0)
        @foreach ($amcdata as $amc)
            <div class="modal fade" id="servicecomments_{{ $amc->id }}" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Service Comments</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">

                                    @php
                                        $amc_comments_data = $amc_comments->where('amc_id', $amc->id);
                                    @endphp

                                    @if (count($amc_comments_data) > 0)
                                        <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th width="50%">Comment</th>
                                                    <th width="10%">Status</th>
                                                    <th style="width: 20%;">By</th>
                                                    <th width="20%">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($amc_comments_data as $cmts)
                                                    <tr>
                                                        <td colspan="3">{{ $cmts->work }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $cmts->comments }}</td>
                                                        <td>
                                                            @if ($cmts->status == 1)
                                                                Pending
                                                            @else
                                                                Completed
                                                            @endif
                                                        </td>
                                                        <td>{{ $cmts->full_name }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($cmts->created_at)->format('d M Y, h:i A') }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif




    <!-- Modal Copy Link -->
    <div class="modal fade" id="ModalCopyLink" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Copy URL</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="" class="form-label">Company Name</label>
                                <select class="form-control js-example-basic-single" id="cust_name_copy" required>
                                    @foreach ($customer as $value)
                                        <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3 mt-3">&nbsp;
                                <button type="submit" class="btn btn-primary" id="copy-button">Copy URL</button>
                            </div>
                        </div>

                        <input type="hidden" id="copy_company_id"
                            value="{{ session('logged_session_data.company_id') }}" />
                        <input type="hidden" id="copy_url" value="{{ url('crm-amc-service-request-customer') }}" />
                        <script>
                            $('#copy-button').click(function() {
                                var textToCopy = $('#copy_url').val();
                                var textToCopy2 = $('#cust_name_copy').val();
                                var textToCopy3 = $('#copy_company_id').val();

                                var tempTextarea = $('<textarea>');
                                $('body').append(tempTextarea);
                                tempTextarea.val(textToCopy + '/' + textToCopy2 + '/' + textToCopy3).select();
                                document.execCommand('copy');
                                tempTextarea.remove();
                                alert("Copied!");
                            });
                        </script>


                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Copy Link -->

    <!-- Modal Support-->
    <div class="modal fade" id="ModalAddNewAMCService" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Request</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-request-list-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="amcid" id="amcid" value="0" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal ID</label>
                                <input type="text" class="form-control" name="deal_id" id="deal_id" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" id="date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Company Name</label>
                                <select class="form-control js-example-basic-single" name="cust_name" id="cust_name"
                                    required>
                                    <option value="">-Select-</option>
                                    @foreach ($customer as $value)
                                        <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Contact Person</label>
                                <input class="form-control" type="text" name="contact_person" id="contact_person"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Mobile No</label>
                                <input class="form-control" type="text" name="mobile_no" id="mobile_no" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Location of Work</label>
                                <input type="text" class="form-control" name="location_of_work" id="location_of_work"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Date</label>
                                <input type="date" class="form-control" name="service_date" id="service_date"
                                    required min="{{ date('Y-m-d') }}" onchange="check_date()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Time</label>
                                <select type="time" class="form-control" name="service_time" id="service_time"
                                    required onchange="check_time()">
                                    <option value="">Select</option>
                                    <option value="00:00:00">12:00 AM</option>
                                    <option value="00:30:00">12:30 AM</option>
                                    <option value="01:00:00">01:00 AM</option>
                                    <option value="01:30:00">01:30 AM</option>
                                    <option value="02:00:00">02:00 AM</option>
                                    <option value="02:30:00">02:30 AM</option>
                                    <option value="03:00:00">03:00 AM</option>
                                    <option value="03:30:00">03:30 AM</option>
                                    <option value="04:00:00">04:00 AM</option>
                                    <option value="04:30:00">04:30 AM</option>
                                    <option value="05:00:00">05:00 AM</option>
                                    <option value="05:30:00">05:30 AM</option>
                                    <option value="06:00:00">06:00 AM</option>
                                    <option value="06:30:00">06:30 AM</option>
                                    <option value="07:00:00">07:00 AM</option>
                                    <option value="07:30:00">07:30 AM</option>
                                    <option value="08:00:00">08:00 AM</option>
                                    <option value="08:30:00">08:30 AM</option>
                                    <option value="09:00:00">09:00 AM</option>
                                    <option value="09:30:00">09:30 AM</option>
                                    <option value="10:00:00">10:00 AM</option>
                                    <option value="10:30:00">10:30 AM</option>
                                    <option value="11:00:00">11:00 AM</option>
                                    <option value="11:30:00">11:30 AM</option>
                                    <option value="12:00:00">12:00 PM</option>
                                    <option value="12:30:00">12:30 PM</option>
                                    <option value="13:00:00">01:00 PM</option>
                                    <option value="13:30:00">01:30 PM</option>
                                    <option value="14:00:00">02:00 PM</option>
                                    <option value="14:30:00">02:30 PM</option>
                                    <option value="15:00:00">03:00 PM</option>
                                    <option value="15:30:00">03:30 PM</option>
                                    <option value="16:00:00">04:00 PM</option>
                                    <option value="16:30:00">04:30 PM</option>
                                    <option value="17:00:00">05:00 PM</option>
                                    <option value="17:30:00">05:30 PM</option>
                                    <option value="18:00:00">06:00 PM</option>
                                    <option value="18:30:00">06:30 PM</option>
                                    <option value="19:00:00">07:00 PM</option>
                                    <option value="19:30:00">07:30 PM</option>
                                    <option value="20:00:00">08:00 PM</option>
                                    <option value="20:30:00">08:30 PM</option>
                                    <option value="21:00:00">09:00 PM</option>
                                    <option value="21:30:00">09:30 PM</option>
                                    <option value="22:00:00">10:00 PM</option>
                                    <option value="22:30:00">10:30 PM</option>
                                    <option value="23:00:00">11:00 PM</option>
                                    <option value="23:30:00">11:30 PM</option>
                                </select>
                                {{--  <input type="time" class="form-control" name="service_time" id="service_time" required onchange="check_time()">  --}}
                                <script>
                                    function check_date() {
                                        var amcDate = new Date($('#date').val());
                                        var serviceDate = new Date($('#service_date').val());
                                        if (amcDate > serviceDate) {
                                            $('#service_date').val('');
                                            $('#service_date').focus();
                                        }
                                    }

                                    function check_time() {
                                        var selected_date = $('#service_date').val();
                                        var selected_time = $('#service_time').val();
                                        var dateToCompare = new Date(selected_date + ' ' + selected_time);
                                        var currentDate = new Date();
                                        if (dateToCompare > currentDate) {

                                        } else {
                                            $('#service_time').val('');
                                            $('#service_time').focus();
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Source</label>
                                <select class="form-control" name="source" id="source" required>
                                    <option selected value="">Select</option>
                                    <option value="Email">Email</option>
                                    <option value="Whatsapp">Whatsapp</option>
                                    <option value="Phone">Phone</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Engineer</label>
                                <select class="form-control js-example-basic-single" name="service_engineer[]"
                                    id="service_engineer" required multiple>
                                    @if (count($engineer_list) > 0)
                                        @foreach ($engineer_list as $list)
                                            <option value="{{ $list->user_id }}">{{ $list->full_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Attachment</label>
                                <input type="file" class="form-control" name="attachment" id="attachment">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Scope of Work</label>
                                <a onclick="add_scope_of_work()" class="btn-sm btn-primary float-right"><i
                                        class="fa fa-plus-square" aria-hidden="true"></i></a>

                                <table width="100%">
                                    <tr>
                                        <td width="1%">1. </td>
                                        <td><input type="text" class="form-control" name="scope_of_work[]"
                                                id="scope_of_work_1" required></td>
                                    </tr>
                                    @for ($i = 2; $i <= 20; $i++)
                                        <tr id="row_{{ $i }}" style="display:none;">
                                            <td>{{ $i }}. </td>
                                            <td><input type="text" class="form-control" name="scope_of_work[]"
                                                    id="scope_of_work_{{ $i }}"></td>
                                        </tr>
                                    @endfor
                                </table>
                                <input type="hidden" id="scope_of_work_row_id" value="1" />
                                <script>
                                    function add_scope_of_work() {
                                        var scope = $('#scope_of_work_row_id').val();
                                        if ($('#scope_of_work_' + scope).val() != "") {
                                            scope++;
                                            $('#row_' + scope).css('display', '');
                                            $('#scope_of_work_row_id').val(scope);
                                            $('#scope_of_work_' + scope).prop("required", true);
                                        }
                                    }
                                </script>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add AMC</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Service-->

    <!-- Modal EditAMC-->

    <a id="btn_edit_service_request" data-toggle="modal" data-target="#ModalAddNewAMCEdit" data-backdrop="static"
        data-keyboard="false"></a>
    <div class="modal fade" id="ModalAddNewAMCEdit" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit AMC Request</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-request-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="amcid_edit" id="amcid_edit" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" id="date_edit"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Company Name</label>
                                {{--  js-example-basic-single  --}}
                                <select class="form-control js-example-basic-single" name="cust_name" id="cust_name_edit"
                                    required>
                                    <option value="">-Select-</option>
                                    @foreach ($customer as $value)
                                        <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Contact Person</label>
                                <input class="form-control" type="text" name="contact_person"
                                    id="contact_person_edit" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Mobile No</label>
                                <input class="form-control" type="text" name="mobile_no" id="mobile_no_edit"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Location of Work</label>
                                <input type="text" class="form-control" name="location_of_work"
                                    id="location_of_work_edit" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Date</label>
                                <input type="date" class="form-control" name="service_date" id="service_date_edit"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Time</label>
                                <select type="time" class="form-control" name="service_time" id="service_time_edit"
                                    required >
                                    <option value="">Select</option>
                                    <option value="00:00:00">12:00 AM</option>
                                    <option value="00:30:00">12:30 AM</option>
                                    <option value="01:00:00">01:00 AM</option>
                                    <option value="01:30:00">01:30 AM</option>
                                    <option value="02:00:00">02:00 AM</option>
                                    <option value="02:30:00">02:30 AM</option>
                                    <option value="03:00:00">03:00 AM</option>
                                    <option value="03:30:00">03:30 AM</option>
                                    <option value="04:00:00">04:00 AM</option>
                                    <option value="04:30:00">04:30 AM</option>
                                    <option value="05:00:00">05:00 AM</option>
                                    <option value="05:30:00">05:30 AM</option>
                                    <option value="06:00:00">06:00 AM</option>
                                    <option value="06:30:00">06:30 AM</option>
                                    <option value="07:00:00">07:00 AM</option>
                                    <option value="07:30:00">07:30 AM</option>
                                    <option value="08:00:00">08:00 AM</option>
                                    <option value="08:30:00">08:30 AM</option>
                                    <option value="09:00:00">09:00 AM</option>
                                    <option value="09:30:00">09:30 AM</option>
                                    <option value="10:00:00">10:00 AM</option>
                                    <option value="10:30:00">10:30 AM</option>
                                    <option value="11:00:00">11:00 AM</option>
                                    <option value="11:30:00">11:30 AM</option>
                                    <option value="12:00:00">12:00 PM</option>
                                    <option value="12:30:00">12:30 PM</option>
                                    <option value="13:00:00">01:00 PM</option>
                                    <option value="13:30:00">01:30 PM</option>
                                    <option value="14:00:00">02:00 PM</option>
                                    <option value="14:30:00">02:30 PM</option>
                                    <option value="15:00:00">03:00 PM</option>
                                    <option value="15:30:00">03:30 PM</option>
                                    <option value="16:00:00">04:00 PM</option>
                                    <option value="16:30:00">04:30 PM</option>
                                    <option value="17:00:00">05:00 PM</option>
                                    <option value="17:30:00">05:30 PM</option>
                                    <option value="18:00:00">06:00 PM</option>
                                    <option value="18:30:00">06:30 PM</option>
                                    <option value="19:00:00">07:00 PM</option>
                                    <option value="19:30:00">07:30 PM</option>
                                    <option value="20:00:00">08:00 PM</option>
                                    <option value="20:30:00">08:30 PM</option>
                                    <option value="21:00:00">09:00 PM</option>
                                    <option value="21:30:00">09:30 PM</option>
                                    <option value="22:00:00">10:00 PM</option>
                                    <option value="22:30:00">10:30 PM</option>
                                    <option value="23:00:00">11:00 PM</option>
                                    <option value="23:30:00">11:30 PM</option>
                                </select>
                                {{--  <input type="time" class="form-control" name="service_time" id="service_time_edit" required onchange="check_time_edit()">  --}}
                                <script>
                                    function check_date_edit() {
                                        var amcDate = new Date($('#date_edit').val());
                                        var serviceDate = new Date($('#service_date_edit').val());
                                        if (amcDate > serviceDate) {
                                            $('#service_date_edit').val('');
                                            $('#service_date_edit').focus();
                                        }
                                    }

                                    function check_time_edit() {
                                        var selected_date = $('#service_date_edit').val();
                                        var selected_time = $('#service_time_edit').val();
                                        var dateToCompare = new Date(selected_date + ' ' + selected_time);
                                        var currentDate = new Date();
                                        if (dateToCompare > currentDate) {

                                        } else {
                                            $('#service_time_edit').val('');
                                            $('#service_time_edit').focus();
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Source</label>
                                <select class="form-control" name="source" id="source_edit" required>
                                    <option value="">Select</option>
                                    <option value="Email">Email</option>
                                    <option value="Whatsapp">Whatsapp</option>
                                    <option value="Phone">Phone</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Engineer</label>
                                <select class="form-control js-example-basic-single" name="service_engineer[]"
                                    id="service_engineer_edit" required multiple>
                                    @if (count($engineer_list) > 0)
                                        @foreach ($engineer_list as $list)
                                            <option value="{{ $list->user_id }}">{{ $list->full_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Attachment</label>
                                <input type="file" class="form-control" name="attachment" id="attachment">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Status</label>
                                <select class="form-control" name="status_edit" id="status_edit">
                                    <option value="2">Pending</option>
                                    <option value="5">Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Scope of Work</label>
                                <a onclick="edit_scope_of_work()" class="btn-sm btn-primary float-right"><i
                                        class="fa fa-plus-square" aria-hidden="true"></i></a>

                                <table width="100%" id="table_work">
                                    <tbody></tbody>
                                </table>
                                <table width="100%">
                                    @for ($i = 20; $i <= 40; $i++)
                                        <tr id="row_edit_{{ $i }}" style="display:none;">
                                            <td width="1%"></td>
                                            <td><input type="text" class="form-control" name="scope_of_work[]"
                                                    id="scope_of_work_edit_{{ $i }}"></td>
                                            <td width="1%"><a class="btn-sm btn-danger" style="float: right;"
                                                    onclick="delete_scope_of_work({{ $i }})"><i
                                                        class="fa fa-trash" aria-hidden="true"></i></a></td>
                                        </tr>
                                    @endfor
                                </table>
                                <input type="hidden" id="scope_of_work_row_id_edit" value="19" />
                                <script>
                                    function edit_scope_of_work() {
                                        var scope = $('#scope_of_work_row_id_edit').val();
                                        scope++;
                                        $('#row_edit_' + scope).css('display', '');
                                        $('#scope_of_work_row_id_edit').val(scope);
                                    }

                                    function delete_scope_of_work(id) {
                                        $('#row_edit_' + id).css('display', 'none');
                                        $('#scope_of_work_edit_' + id).val('');
                                        $('#scope_of_work_edit_' + id).prop("required", false);
                                    }
                                </script>

                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update AMC Service Request</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal EditAMC-->

    <script>
        $(document).on("change", "#cust_name", function() {
            var id = $("#cust_name").val();
            get_cust_name(id);
        });

        function get_cust_name(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-amc-customer-details') }}";
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
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    console.log(dataResult);
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            var name = dataResult['data'][i].customer_salutation + ' ' + dataResult['data'][i]
                                .first_name + ' ' + dataResult['data'][i].last_name;
                            var address = dataResult['data'][i].address + ', ' + dataResult['data'][i]
                                .address2 + ', ' + dataResult['data'][i].city;

                            $("#contact_person").val(name.replace('null ', '').replace('null', ''));
                            $("#mobile_no").val(dataResult['data'][i].mobile);
                            $("#location_of_work").val(dataResult['data'][i].address);
                        }
                    } else {
                        $("#contact_person").val();
                        $("#mobile_no").val();
                        $("#location_of_work").val();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function edit_service_request(id) {
            get_amc_service_request_edit(id);
            $('#btn_edit_service_request').click();
        }

        function get_amc_service_request_edit(id) {
            $("#loading_bg").css("display", "block");
            console.log("ID: " + id);
            var action = "{{ URL::to('crm-amc-service-request-edit') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    console.log(dataResult);
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            $("#amcid_edit").val(dataResult['data'][i].id);
                            $("#date_edit").val(new Date(dataResult['data'][i].date).toLocaleDateString(
                                'en-CA'));

                            $("#cust_name_edit").val(dataResult['data'][i].cust_name);

                            //alert(dataResult['data'][i].cust_name);
                            //$("#select2-cust_name_edit-container").val(dataResult['data'][i].cust_name);   


                            $("#contact_person_edit").val(dataResult['data'][i].contact_person);
                            $("#mobile_no_edit").val(dataResult['data'][i].mobile_no);
                            $("#location_of_work_edit").val(dataResult['data'][i].location_of_work);

                            const scop = dataResult['data'][i].scope_of_work.split("$");
                            for (k = 0; k < scop.length; k++) {
                                $("#scope_of_work_edit_" + (k + 1)).val(scop[k]);
                                $("#row_edit_" + (k + 1)).css('display', '');
                            }

                            $("#service_date_edit").val(dataResult['data'][i].service_date);
                            $("#service_time_edit").val(dataResult['data'][i].service_time);

                            if (dataResult['data'][i].status == 5) {
                                $("#status_edit").val(dataResult['data'][i].status);
                            }else{
                                $("#status_edit").val(2);
                            }

                            $("#source_edit").val(dataResult['data'][i].source);


                            //$("#service_engineer_edit").val(dataResult['data'][i].service_engineer);
                            //$('#service_engineer_edit').removeClass('js-example-basic-single');
                            const selectElement = document.getElementById("service_engineer_edit");
                            const valuesToSelect = dataResult['data'][i].service_engineer;

                            // FIX: Clear previous selections
                            // FIX: Clear previous selections
                            for (let i = 0; i < selectElement.options.length; i++) {
                                selectElement.options[i].selected = false;
                            }

                            if (valuesToSelect !== null) {
                                for (let i = 0; i < selectElement.options.length; i++) {
                                    const option = selectElement.options[i];
                                    if (valuesToSelect.includes(option.value)) {
                                        option.selected = true; // Select the option
                                    }
                                }
                            }


                            //$('#service_engineer_edit').addClass('js-example-basic-single');

                            get_amc_scope_of_work(dataResult['data'][i].id);


                        }
                    } else {
                        $("#amcid_edit").val();
                        $("#date_edit").val();
                        $("#cust_name_edit").val();
                        $("#contact_person_edit").val();
                        $("#mobile_no_edit").val();
                        $("#location_of_work_edit").val();
                        $("#scope_of_work_edit").val();
                        $("#service_date_edit").val();
                        $("#service_time_edit").val();
                        $("#status_edit").val();
                        $("#source_edit").val();
                        $("#service_engineer_edit").val();
                    }
                    $("#cust_name_edit").change();
                    $("#service_engineer_edit").change();
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function get_amc_scope_of_work(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-amc-service-request-work') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var tr = "";
                    if (dataResult['data'] != null) {
                        len = dataResult['data'].length;
                    }
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {

                            tr += '<tr id="row_edit_' + i + '">\
                                                                        <td width="1%"><input type="hidden" value="' +
                                dataResult[
                                    'data'][
                                    i
                                ]
                                .id + '" name="scope_of_work_id[]"></td>\
                                                                        <td><input value="' + dataResult['data'][i].work +
                                '" class="form-control" type="text" id="scope_of_work_edit_' + i +
                                '" name="scope_of_work[]" autocomplete="off"></td><td width="1%"><a class="btn-sm btn-danger" style="float: right;" onclick="delete_scope_of_work(' +
                                i + ')"><i class="fa fa-trash" aria-hidden="true"></i></a></td>\
                                                                    </tr>';
                        }
                    } else {

                    }

                    $('#table_work tbody').empty();
                    $("#table_work tbody").append(tr);
                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection

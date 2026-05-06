@extends('backEnd.masterpage')
@section('mainContent')
    <?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">AMC List <span class="text-primary"> ({{ $ctrl_validity === '0' ? 'Active' : ($ctrl_validity === '1' ? 'Expired' : 'All') }})</span></h2>
                <span class="page-label">Home - AMC List</span>
            </div>
            <div>
                {{-- <a href="" class="btn btn-danger" id="btn_expired_amc" >Expired AMC</a> --}}
                <a class="btn btn-info" id="btn_add_new_amc" data-toggle="modal" data-target="#ModalAddNewAMC"
                    data-backdrop="static" data-keyboard="false">Add New AMC</a>
                <a class="btn btn-info" href="{{ url('crm-amc-service-request-list') }}">AMC Request List</a>

                <button type="button" class="btn btn-warning" data-toggle="collapse" href="#collapseExample" role="button"
                    aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
            </div>
        </div>
        <div class="collapse" id="collapseExample">
            <div class="card shadow mb-4 p-4">

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-list', 'method' => 'POST', 'id' => 'crm-amc-list']) }}
                <div class="row">
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">AMC ID</label>
                        <input class="form-control" id="search_amc_id" type="text" autocomplete="off"
                            name="search_amc_id" value="{{ $ctrl_amc_id }}">
                    </div>
                    <div class="col-md-3 mb-2">
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
                        <label for="" class="form-check-label">Current AMC Date</label>
                        <input class="form-control" id="search_from_date" type="date" autocomplete="off"
                            name="search_from_date" value="{{ $ctrl_date }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-check-label">Expired AMC Date</label>
                        <input class="form-control" id="search_to_date" type="date" autocomplete="off"
                            name="search_to_date" value="{{ $ctrl_date2 }}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="" class="form-label">Validity</label>
                        <select class="form-control" name="validity" id="validity">
                            <option @if ($ctrl_validity == '') selected @endif value="">All</option>
                            <option value="0" @if ($ctrl_validity == '0') selected @endif>Active</option>
                            <option value="1" @if ($ctrl_validity == '1') selected @endif>Expired</option>

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
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
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
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <th width="70px">@lang('Sr No')</th>
                                <th width="50px">@lang('Deal ID')</th>
                                <th width="80px">@lang('Date')</th>
                                <th>@lang('Customer Name')</th>
                                <th width="80px">@lang('Start Date')</th>
                                <th width="80px">@lang('End Date')</th>
                                <th width="100px">@lang('Amount')</th>
                                <th width="100px">@lang('Sales Person')</th>
                                <th width="100px">@lang('AMC Track')</th>
                                <th width="100px">@lang('Invoicing')</th>
                                <th width="150px">@lang('Description')</th>
                                <th width="145px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $amount_total = 0; ?>
                            @foreach ($amcdata as $value)
                                <tr @if (@$value->is_delete == 1) class="bg-dark" @endif>
                                    <td><a href="{{ url('crm-amc-detail/' . @$value->id) }}"
                                            target="_blank">{{ @$value->doc_number }}</a></td>
                                    <td><a href="{{ url('get-url-deal-track/' . @$value->deal_code->code) }}"
                                            target="_blank">{{ @$value->deal_code->code }}</a></td>
                                    <td>{{ date('d/m/Y', strtotime(@$value->date)) }}</td>
                                    <td>{{ @$value->custname->name }}</td>

                                    @if ($value->end_date < date('Y-m-d'))
                                        <td class="text-danger">{{ date('d/m/Y', strtotime(@$value->start_date)) }}</td>
                                        <td class="text-danger">{{ date('d/m/Y', strtotime(@$value->end_date)) }}</td>
                                        <td class="text-danger">{{ @$value->amount }} </td>
                                    @else
                                        <td>{{ date('d/m/Y', strtotime(@$value->start_date)) }}</td>
                                        <td>{{ date('d/m/Y', strtotime(@$value->end_date)) }}</td>
                                        <td>{{ @$value->amount }} <?php $amount_total += $value->amount; ?></td>
                                    @endif

                                    @php $amcData =  @App\SysHelper::getAMCEngAndRequestCount($value->cust_name); @endphp
                                
                                    <td>{{ $value->salesperson->full_name ?? '' }}</td>
                                    <td>{{$amcData['amc_count']}} Req,
                                        {{ $amcData['eng_count'] }} Engg
                                    </td>
                                    <td>{{ $value->invoice ?? '' }}</td>

                                    <td>
                                        <div
                                            style="width:240px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                            {{ @$value->description }}</div>
                                        @php $loc = $location_list->where('id',$value->cust_name)->max('address') @endphp
                                        <input type="hidden" id="location_{{ @$value->id }}"
                                            value="{{ $loc }}" />


                                    </td>
                                    <td>

                                        <a class="btn-sm btn-primary"
                                            onclick="edit_service_request({{ $value->id }})"><i class="fa fa-edit"
                                                aria-hidden="true"></i></a>

                                        @if (@$value->is_delete == 0)
                                            <a class="btn-sm btn-danger" onclick="return confirm('Are you sure?')"
                                                href="{{ url('crm-amc-deactivate/' . $value->id . '') }}"><i
                                                    class="fa fa-trash" aria-hidden="true"></i></a>
                                        @endif
                                        @if (@$value->is_delete == 1)
                                            <a class="btn-sm btn-info" onclick="return confirm('Are you sure?')"
                                                href="{{ url('crm-amc-activate/' . $value->id . '') }}"><i
                                                    class="fa fa-recycle" aria-hidden="true"></i></a>
                                        @endif

                                        @if (@$value->is_delete == 0)
                                            @if (@$value->status == 1)
                                                <a class="btn-sm btn-primary"
                                                    onclick="add_service_request({{ $value->id }})"
                                                    title="Add Request"><i class="fa fa-plus" aria-hidden="true"></i>
                                                    Req</a>
                                            @elseif(@$value->status == 2)
                                                <a class="btn-sm btn-info"
                                                    onclick="add_service_request({{ $value->id }})"
                                                    title="Add More Request"><i class="fa fa-plus"
                                                        aria-hidden="true"></i> Req</a>
                                                {{--  <a class="btn-sm btn-success" onclick="edit_service_request({{ $value->id }})">Service Requested</a>  --}}
                                            @else
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ @App\SysHelper::com_curr_format($amount_total, 2, '.', ',') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>


    <script>
        function handleExpirationToggle() {
            const $checkbox = $('#amc_expired');
            const $commentBox = $('#expired_comment_wrapper');
            const $textarea = $('#expired_comment');

            if ($checkbox.is(':checked')) {
                $commentBox.slideDown();
                $textarea.prop('required', true);
            } else {
                $commentBox.slideUp();
                $textarea.prop('required', false);
                $textarea.val(''); // Optional: clear text if unchecked
            }
        }

        function add_service_request(id) {
            $('#amc_id').val(id);
            $('#location_of_work').val($('#location_' + id).val());
            $('#btn_add_service_request').click();
        }

        function edit_service_request(id) {
            get_amc_edit(id);
            $('#btn_edit_service_request').click();
        }

        function get_amc_edit(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-amc-edit') }}";
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
                            $("#deal_id_edit").val(dataResult['data'][i].code);
                            $("#date_edit").val(dataResult['data'][i].date.split(' ')[0]);
                            $("#cust_name_edit").val(dataResult['data'][i].cust_name);
                            $("#contact_person_edit").val(dataResult['data'][i].contact_person);
                            $("#mobile_no_edit").val(dataResult['data'][i].mobile_no);
                            $("#start_date_edit").val(dataResult['data'][i].start_date);
                            $("#end_date_edit").val(dataResult['data'][i].end_date);
                            $("#invoice_edit").val(dataResult['data'][i].invoice);
                            $("#amount_edit").val(dataResult['data'][i].amount);
                            $("#sales_person_edit").val(dataResult['data'][i].sales_person);
                            $("#description_edit").val(dataResult['data'][i].description);

                            if (dataResult['data'][i].is_expired == 1) {
                                $('#amc_expired').prop('checked', true);
                                $("#expired_comment").val(dataResult['data'][i].comment);
                            } else {
                                $('#amc_expired').prop('checked', false);
                            }


                            // ✅ Add this line after setting checkbox
                            handleExpirationToggle();

                        }
                    } else {
                        $("#amcid_edit").val();
                        $("#deal_id_edit").val();
                        $("#date_edit").val();
                        $("#cust_name_edit").val();
                        $("#contact_person_edit").val();
                        $("#mobile_no_edit").val();
                        $("#start_date_edit").val();
                        $("#end_date_edit").val();
                        $("#invoice_edit").val();
                        $("#amount_edit").val();
                        $("#sales_person_edit").val();
                        $("#description_edit").val();
                        $('#amc_expired').prop('checked', true);

                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>

    <a id="btn_add_service_request" data-toggle="modal" data-target="#ModalAddServiceRequest" data-backdrop="static"
        data-keyboard="false"></a>
    <!-- Modal AddServiceRequest-->
    <div class="modal fade" id="ModalAddServiceRequest" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Service Request</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-add-service-request', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="amc_id" id="amc_id" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Location Of Work</label>
                                <input type="text" class="form-control" name="location_of_work"
                                    id="location_of_work">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Date</label>
                                <input type="date" class="form-control" name="service_date" id="service_date"
                                    min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Time</label>
                                <select type="time" class="form-control" name="service_time" id="service_time"
                                    required>
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
                                {{--  <input type="time" class="form-control" name="service_time" id="service_time" required>  --}}
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
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal AddServiceRequest-->

    <!-- Modal AddNewAMC-->
    <div class="modal fade" id="ModalAddNewAMC" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New AMC</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="amcid" id="amcid" value="0" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal ID</label>
                                <input type="text" class="form-control" name="deal_id" id="deal_id">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" id="date" required>
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
                            <div class="form-group">
                                <label for="">Start Date</label>
                                <input class="form-control" type="date" name="start_date" id="start_date"
                                    min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">End Date</label>
                                <input class="form-control" type="date" name="end_date" id="end_date"
                                    min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Invoicing</label>
                                <select class="form-control" type="text" name="invoice" id="invoice" required>
                                    <option value="">-Select-</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Half Yearly">Half Yearly</option>
                                    <option value="Yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Amount</label>
                                <input class="form-control" type="number" step="any" name="amount" id="amount"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Sales Person</label>
                                <select class="form-control" type="text" name="sales_person" id="sales_person"
                                    required>
                                    <option value="">-Select-</option>
                                    @if (count($salesperson) > 0)
                                        @foreach ($salesperson as $dt)
                                            <option value="{{ $dt->user_id }}">{{ $dt->full_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="4" required></textarea>
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
    <!-- Modal AddNewAMC-->

    <!-- Modal EditNewAMC-->

    <a id="btn_edit_service_request" data-toggle="modal" data-target="#ModalAddNewAMCEdit" data-backdrop="static"
        data-keyboard="false"></a>
    <div class="modal fade" id="ModalAddNewAMCEdit" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit AMC</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="amcid_edit" id="amcid_edit" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal ID</label>
                                <input type="text" class="form-control" name="deal_id" id="deal_id_edit">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" id="date_edit" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                {{--  js-example-basic-single  --}}
                                <label for="" class="form-label">Company Name</label>
                                <select class="form-control" name="cust_name" id="cust_name_edit" required>
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
                            <div class="form-group">
                                <label for="">Start Date</label>
                                <input class="form-control" type="date" name="start_date" id="start_date_edit"
                                    min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">End Date</label>
                                <input class="form-control" type="date" name="end_date" id="end_date_edit"
                                    min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Invoicing</label>
                                <select class="form-control" type="text" name="invoice" id="invoice_edit" required>
                                    <option value="">-Select-</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Half Yearly">Half Yearly</option>
                                    <option value="Yearly">Yearly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Amount</label>
                                <input class="form-control" type="number" step="any" name="amount"
                                    id="amount_edit" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Sales Person</label>
                                <select class="form-control" type="text" name="sales_person" id="sales_person_edit"
                                    required>
                                    <option value="">-Select-</option>
                                    @if (count($salesperson) > 0)
                                        @foreach ($salesperson as $dt)
                                            <option value="{{ $dt->user_id }}">{{ $dt->full_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description_edit" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="amc_expired" name="amc_expired" value="1">
                                        <span class="form-check-label fw-semibold text-danger" for="is_expired">
                                            Mark this AMC <strong>Expired</strong>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <!-- Comment Box (Initially Hidden) -->
                            <div id="expired_comment_wrapper" style="display: none;">
                                <div class="form-group">
                                    <label for="expired_comment" class="form-label text-danger fw-bold">
                                        Reason for Expiration <span class="text-muted">(required)</span>
                                    </label>
                                    <textarea class="form-control" name="expired_comment" id="expired_comment" rows="3"
                                        placeholder="Enter the reason why this AMC is being marked as expired..."></textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update AMC</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal EditNewAMC-->


    <script>
        $(document).on("change", "#cust_name", function() {
            var id = $("#cust_name").val();
            get_cust_name(id);
        });

        function get_cust_name(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-leads-customername') }}";
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
                    if (len > 0) {
                        for (var i = 0; i < len; i++) {
                            var name = dataResult['data'][i].customer_salutation + ' ' + dataResult['data'][i]
                                .first_name + ' ' + dataResult['data'][i].last_name;
                            var address = dataResult['data'][i].address + ', ' + dataResult['data'][i]
                                .address2 + ', ' + dataResult['data'][i].city + ', ' + dataResult['data'][i]
                                .statename + ', ' + dataResult['data'][i].name;
                            $("#contact_person").val(name.replace('null ', '').replace('null', ''));
                            $("#mobile_no").val(dataResult['data'][i].mobile);
                        }
                    } else {
                        $("#contact_person").val();
                        $("#mobile_no").val();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }


        $(document).ready(function() {


            // Run on page load and on change
            handleExpirationToggle();
            $('#amc_expired').on('change', handleExpirationToggle);
        });
    </script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection

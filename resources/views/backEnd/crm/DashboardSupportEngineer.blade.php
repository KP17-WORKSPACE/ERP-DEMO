@extends('backEnd.newmasterpage')
@section('mainContent')




    <style>
        /* ================================
                       Dashboard Grade Styling
                       ================================ */

        /* ================================
                   Reusable Max-Height Scrollable
                   ================================ */
        .max-height {
            max-height: 300px;
            /* adjust as needed */
            overflow-y: auto;
            scrollbar-width: thin;
            /* Firefox */
            scrollbar-color: #b0b8c5 #f1f3f9;
            /* thumb + track */
        }

        /* Chrome/Edge Scrollbar */
        .max-height::-webkit-scrollbar {
            width: 6px;
        }

        .max-height::-webkit-scrollbar-track {
            background: #f1f3f9;
            border-radius: 8px;
        }

        .max-height::-webkit-scrollbar-thumb {
            background-color: #b0b8c5;
            border-radius: 8px;
        }


        /* Card Styling */
        .card {
            border: none;

            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease-in-out;
        }



        /* Card Header */
        .card-header {
            background-color: white;
            color: #212529 !important;
            border-bottom: none
        }

        .card-header h6 {
            font-weight: 600;
            font-size: 1rem;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .card-fixed-lg {
            height: 325px;
            /* large card */
            overflow-y: auto;
        }

        /* Rounded Box Metrics */
        .rounded__box {
            border: 2px solid transparent;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            margin: 0.5rem;
            background: rgb(222, 235, 225);
            min-width: 140px;
            text-align: center;
            transition: all 0.3s ease-in-out;
        }

        .rounded__box:hover {
            background: #eef2fb;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
        }

        /* Font Sizes for Metrics */
        .font-card-large {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1b1e34;
        }

        .font-card-medium {
            font-size: 1.1rem;
            font-weight: 600;
            color: #444;
        }

        /* Sales Table */
        .sales_tab {
            font-size: 0.85rem;
            color: #4e5d78;
        }

        .sales_tab thead {
            background: #f1f3f9;
            font-weight: 600;
        }

        .sales_tab td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .sales_tab tbody tr:hover {
            background: #f9fbff;
        }

        /* Table Striping */
        .table-striped2 tbody tr:nth-child(odd) {
            background-color: #f8f9fc;
        }

        /* Links inside Metrics */
        .rounded__box a {
            text-decoration: none;
            color: inherit;
        }

        .rounded__box a:hover {
            color: #0b2262;
        }
    </style>

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex  justify-content-between ">
                <!-- Left: Heading -->
                <h4 class="mb-0">Support Dashboard</h4>
                <input type="hidden" id="base_url" value="{{ url('/') }}" />



            </div>

        </div>

        <div class="left-nav-list">

            <div class="row mt-4">

                {{--  NEW AMC REQUEST START  --}}
                <div class="col-md-4">
                    <div class="card shadow card-fixed-lg">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title m-0">New AMC Request</h4>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th width="70px">Track ID</th>
                                            <th width="70px">Date</th>
                                            <th width="70px">Time</th>
                                            <th>Customer Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i=1; @endphp
                                        @if (count(@$amc_request) > 0)
                                            @foreach (@$amc_request as $dt)
                                                <tr>
                                                    <td>
                                                        <a class="text-primary" style="cursor: pointer;"
                                                            onclick="new_amc_request_popup({{ $i }})">{{ @$dt->doc_number }}</a>
                                                        <input type="hidden" id="id_{{ $i }}"
                                                            value="{{ $dt->id }}" />
                                                        <input type="hidden" id="doc_number_{{ $i }}"
                                                            value="{{ $dt->doc_number }}" />
                                                        <input type="hidden" id="date_{{ $i }}"
                                                            value="{{ date('d/m/Y', strtotime(@$dt->date)) }}" />
                                                        <input type="hidden" id="date_time_{{ $i }}"
                                                            value="{{ date('h:i A', strtotime(@$dt->date)) }}" />
                                                        <input type="hidden" id="cust_name_{{ $i }}"
                                                            value="{{ $dt->custname->name }}" />
                                                        <input type="hidden" id="contact_person_{{ $i }}"
                                                            value="{{ $dt->contact_person }}" />
                                                        <input type="hidden" id="mobile_no_{{ $i }}"
                                                            value="{{ $dt->mobile_no }}" />
                                                        <input type="hidden" id="service_id_{{ $i }}"
                                                            value="{{ $dt->service_id }}" />
                                                        <input type="hidden" id="service_date_{{ $i }}"
                                                            value="{{ date('d/m/Y', strtotime(@$dt->service_date)) }}" />
                                                        <input type="hidden" id="service_time_{{ $i }}"
                                                            value="{{ date('h:i A', strtotime(@$dt->service_time)) }}" />
                                                        <input type="hidden" id="location_of_work_{{ $i }}"
                                                            value="{{ @$dt->location_of_work }}" />

                                                        @php
                                                            $scope_work = '';
                                                            if ($dt->is_auto == 0) {
                                                                $scope_of_work = $amc_work->where('amc_id', $dt->id);
                                                            } else {
                                                                $scope_of_work = $amc_request_work->where(
                                                                    'service_id',
                                                                    $dt->service_id,
                                                                );
                                                            }

                                                            if (count($scope_of_work) > 0) {
                                                                foreach ($scope_of_work as $w) {
                                                                    $scope_work .= $w->work . "\n";
                                                                }
                                                            }
                                                        @endphp
                                                        <input type="hidden" id="scope_of_work_{{ $i }}"
                                                            value="{!! nl2br($scope_work) !!}" />
                                                    </td>
                                                    <td>{{ date('d/m/Y', strtotime(@$dt->service_date)) }}</td>
                                                    <td>{{ date('h:i A', strtotime(@$dt->service_time)) }}</td>
                                                    <td>{{ @$dt->custname->name }}</td>
                                                </tr>
                                                @php $i++; @endphp
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                {{--  NEW AMC REQUEST END  --}}


                {{--  NEW PS REQUEST START  --}}
                <div class="col-md-4 mb-2">
                    <div class="card shadow card-fixed-lg">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title m-0">New PS Request</h4>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th width="70px">Track ID</th>
                                            <th width="70px">Date</th>
                                            <th width="70px">Time</th>
                                            <th>Customer Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count(@$professional_services) > 0)
                                            @foreach (@$professional_services as $dt)
                                                <tr>
                                                    <td><a class="text-primary" style="cursor: pointer;"
                                                            onclick="new_ps_request_popup({{ $dt->id }})">{{ @$dt->doc_number }}</a>
                                                        <input type="hidden" id="ps_id_{{ $dt->id }}"
                                                            value="{{ $dt->id }}" />
                                                        <input type="hidden" id="ps_doc_number_{{ $dt->id }}"
                                                            value="{{ $dt->doc_number }}" />
                                                        <input type="hidden" id="ps_date_{{ $dt->id }}"
                                                            value="{{ date('d/m/Y', strtotime(@$dt->date)) }}" />
                                                        <input type="hidden" id="ps_date_time_{{ $dt->id }}"
                                                            value="{{ date('h:i A', strtotime(@$dt->date)) }}" />
                                                        <input type="hidden" id="ps_cust_name_{{ $dt->id }}"
                                                            value="{{ $dt->custname->name }}" />
                                                        <input type="hidden" id="ps_contact_person_{{ $dt->id }}"
                                                            value="{{ $dt->contact_person }}" />
                                                        <input type="hidden" id="ps_mobile_{{ $dt->id }}"
                                                            value="{{ $dt->mobile }}" />
                                                        <input type="hidden" id="ps_service_date_{{ $dt->id }}"
                                                            value="{{ date('d/m/Y', strtotime(@$dt->service_date)) }}" />
                                                        <input type="hidden" id="ps_service_time_{{ $dt->id }}"
                                                            value="{{ date('h:i A', strtotime(@$dt->service_time)) }}" />
                                                        <input type="hidden"
                                                            id="ps_location_of_work_{{ $dt->id }}"
                                                            value="{{ @$dt->location_of_work }}" />
                                                        <?php
                                                        $work = $professional_services_work->where('service_id', $dt->id)->pluck('work');
                                                        $work_data = '';
                                                        $i = 1;
                                                        if (count($work) > 0) {
                                                            foreach ($work as $w) {
                                                                if ($work_data == '') {
                                                                    $work_data = $i . '. ' . $w;
                                                                } else {
                                                                    $work_data .= '<br />' . $i . '. ' . $w;
                                                                }
                                                                $i++;
                                                            }
                                                        }
                                                        ?>
                                                        <input type="hidden" id="ps_scope_of_work_{{ $dt->id }}"
                                                            value="{!! $work_data !!}" />

                                                    </td>
                                                    <td>{{ date('d/m/Y', strtotime(@$dt->service_date)) }}</td>
                                                    <td>{{ date('h:i A', strtotime(@$dt->service_time)) }}</td>
                                                    <td>{{ @$dt->custname->name }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow card-fixed-lg">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title m-0">New Pre-Sales Support</h4>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th width="70px">Support ID</th>
                                            <th width="70px">Date</th>
                                            <th width="70px">Time</th>
                                            <th>Customer Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count(@$pre_sales_support_new) > 0)
                                            @foreach (@$pre_sales_support_new as $dt)
                                                <tr>
                                                    <td>
                                                        <a class="text-primary" style="cursor: pointer;"
                                                            onclick="new_pre_sales_support_popup({{ $dt->id }})">{{ @$dt->doc_number }}</a>
                                                        {{--  <a target="_blank" href="{{ url('crm-deal-support/'.$dt->id.'/view') }}">{{ @$dt->id }}</a>  --}}
                                                    </td>
                                                    <td>{{ date('d/m/Y', strtotime(@$dt->support_date)) }}</td>
                                                    <td>{{ date('h:i A', strtotime(@$dt->time_from)) }}</td>
                                                    <td>{{ @$dt->customer->name }}</td>

                                                    <input type="hidden" id="support_doc_number_{{ $dt->id }}"
                                                        value="{{ $dt->doc_number }}" />
                                                    <input type="hidden" id="support_created_date_{{ $dt->id }}"
                                                        value="{{ date('d/m/Y', strtotime(@$dt->created_at)) }}" />
                                                    <input type="hidden" id="support_customer_{{ $dt->id }}"
                                                        value="{{ $dt->customer->name }}" />
                                                    <input type="hidden" id="support_contact_person_{{ $dt->id }}"
                                                        value="{{ $dt->contact_person }}" />
                                                    <input type="hidden" id="support_mobile_{{ $dt->id }}"
                                                        value="{{ $dt->mobile }}" />
                                                    <input type="hidden" id="support_date_{{ $dt->id }}"
                                                        value="{{ date('d/m/Y', strtotime(@$dt->support_date)) }}" />
                                                    <input type="hidden" id="support_time_from_{{ $dt->id }}"
                                                        value="{{ date('h:i A', strtotime(@$dt->time_from)) }}" />
                                                    <input type="hidden" id="support_site_name_{{ $dt->id }}"
                                                        value="{{ $dt->site_name }}" />
                                                    <?php
                                                    $work = $pre_sales_support_work->where('support_id', $dt->id)->pluck('work');
                                                    $work_data = '';
                                                    $i = 1;
                                                    if (count($work) > 0) {
                                                        foreach ($work as $w) {
                                                            if ($work_data == '') {
                                                                $work_data = $i . '. ' . $w;
                                                            } else {
                                                                $work_data .= '<br />' . $i . '. ' . $w;
                                                            }
                                                            $i++;
                                                        }
                                                    }
                                                    ?>
                                                    <input type="hidden" id="support_work_{{ $dt->id }}"
                                                        value="{!! $work_data !!}" />
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="card shadow card-fixed-lg">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title m-0">Completed AMC Request</h4>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th width="70px">Track ID</th>
                                            <th width="70px">Date</th>
                                            <th width="70px">Time</th>
                                            <th>Customer Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count(@$amc_request_completed) > 0)
                                            @foreach (@$amc_request_completed as $dt)
                                                <tr>
                                                    <td><a target="_blank"
                                                            href="{{ url('crm-amc-service-request-detail/' . $dt->id) }}">{{ @$dt->doc_number }}</a>
                                                    </td>
                                                    <td>{{ date('d/m/Y', strtotime(@$dt->service_date)) }}</td>
                                                    <td>{{ date('h:i A', strtotime(@$dt->service_time)) }}</td>
                                                    <td>{{ @$dt->custname->name }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="card shadow card-fixed-lg">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title m-0">Completed PS Request</h4>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th width="70px">Track ID</th>
                                            <th width="70px">Date</th>
                                            <th width="70px">Time</th>
                                            <th>Customer Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count(@$professional_services_completed) > 0)
                                            @foreach (@$professional_services_completed as $dt)
                                                <tr>
                                                    <td><a target="_blank"
                                                            href="{{ url('crm-ps-track-service-detail/' . $dt->id) }}">{{ @$dt->doc_number }}</a>
                                                    </td>
                                                    <td>{{ date('d/m/Y', strtotime(@$dt->service_date)) }}</td>
                                                    <td>{{ date('h:i A', strtotime(@$dt->service_time)) }}</td>
                                                    <td>{{ @$dt->custname->name }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow card-fixed-lg">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="header-title m-0">Completed Pre-Sales Support</h4>
                        </div>
                        <div class="card-body pt-0  max-height">
                            <div class="table-responsive table-bordered">
                                <table class="table table-nowrap table-centered mb-0 table-striped2 table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list">
                                    <thead>
                                        <tr>
                                            <th width="70px">Support ID</th>
                                            <th width="70px">Date</th>
                                            <th width="70px">Time</th>
                                            <th>Customer Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count(@$pre_sales_support_completed) > 0)
                                            @foreach (@$pre_sales_support_completed as $dt)
                                                <tr>
                                                    <td><a target="_blank"
                                                            href="{{ url('crm-deal-support/' . $dt->id . '/view') }}">{{ @$dt->doc_number }}</a>
                                                    </td>
                                                    <td>{{ date('d/m/Y', strtotime(@$dt->support_date)) }}</td>
                                                    <td>{{ date('h:i A', strtotime(@$dt->time_from)) }}</td>
                                                    <td>{{ @$dt->customer->name }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>



            </div>


        </div>
    </aside>


    <script>
        function new_amc_request_popup(id) {

            $('#amc_id').text($('#doc_number_' + id).val());
            $('#amc_id_comment').val($('#id_' + id).val());
            $('#amc_id_work').val($('#service_id_' + id).val());
            $('#date').text($('#date_' + id).val());
            $('#date_time').text($('#date_time_' + id).val());
            $('#cust_name').text($('#cust_name_' + id).val());
            $('#contact_person').text($('#contact_person_' + id).val());
            $('#mobile_no').text($('#mobile_no_' + id).val());
            $('#service_date').text($('#service_date_' + id).val());
            $('#service_time').text($('#service_time_' + id).val());
            $('#location_of_work').text($('#location_of_work_' + id).val());
            $('#scope_of_work').html($('#scope_of_work_' + id).val());

            //var str = $('#scope_of_work_'+id).val();
            //var regex = /<br\s*[\/]?>/gi;
            //$("#scope_of_work").html(str.replace(regex, "<br/>"));

            //$('#scope_of_work').text($('#scope_of_work_'+id).val());

            //view_amc_request_work($('#id_'+id).val());

            $('#a_modalnewamcrequest').click();
            view_amc_request_comments($('#id_' + id).val());
        }

        function view_amc_request_comments(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-amc-service-request-get-comments') }}";
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
                            var status = "";
                            if (dataResult['data'][i].status == 1) {
                                status = "Pending";
                            }
                            if (dataResult['data'][i].status == 2) {
                                status = "Completed";
                            }
                            tr += "<tr>";
                            tr += "<td>" + dataResult['data'][i].comments + "<br /> On : " + get_format_date(
                                dataResult['data'][i].work_date) + " from " + get_format_time(dataResult[
                                'data'][i].work_time_from) + " to " + get_format_time(dataResult['data'][i]
                                .work_time_to) + "</td>";
                            tr += "<td>" + status + "</td>";
                            tr += "<td>" + dataResult['data'][i].full_name + " <br /> " + get_format_date_time(
                                dataResult['data'][i].created_at) + "</td>";
                            tr += "</tr>";
                        }
                    } else {
                        $("#contact_person").val();
                        $("#mobile_no").val();
                        $("#location_of_work").val();
                    }
                    $('#data_new_amc_comments_table tbody').empty();
                    $("#data_new_amc_comments_table tbody").append(tr);

                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function view_amc_request_work(id) {
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
                        tr += "<table width='100%'>";
                        for (var i = 0; i < len; i++) {
                            tr += "<tr>";
                            tr += "<td width='2%'>" + (i + 1) + "</td>";
                            tr += "<td width='85%' class='form-control'>" + dataResult['data'][i].work +
                            "</td>";
                            tr +=
                                "<td width='13%'><a href='#' class='btn-sm btn-info' onclick='open_comments(" +
                                dataResult['data'][i].id + ")'>Add Comment</a></td>";
                            tr += "</tr>";
                        }
                        tr += "</table>";
                    } else {

                    }

                    //$('#scope_of_work').html();
                    $("#scope_of_work").html(tr);

                    $("#loading_bg").css("display", "none");
                }
            });
        }


        function open_comments(id) {
            $('#amc_id_work').val(id);
            $('#a_modalcomments').click();
        }

        function add_amc_request_comments() {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-amc-service-request-comments') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    amc_id: $('#amc_id_comment').val(),
                    comments: $('#comments').val(),
                    status: $('#comment_status').val(),
                    work_id: $('#amc_id_work').val(),
                    work_date: $('#amc_work_date').val(),
                    work_time_from: $('#amc_work_time_from').val(),
                    work_time_to: $('#amc_work_time_to').val(),
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
                            var status = "";
                            if (dataResult['data'][i].status == 1) {
                                status = "Pending";
                            }
                            if (dataResult['data'][i].status == 2) {
                                status = "Completed";
                            }
                            tr += "<tr>";
                            tr += "<td>" + dataResult['data'][i].comments + "<br /> On : " + get_format_date(
                                dataResult['data'][i].work_date) + " from " + get_format_time(dataResult[
                                'data'][i].work_time_from) + " to " + get_format_time(dataResult['data'][i]
                                .work_time_to) + "</td>";
                            tr += "<td>" + status + "</td>";
                            tr += "<td>" + dataResult['data'][i].full_name + " <br /> " + get_format_date_time(
                                dataResult['data'][i].created_at) + "</td>";
                            tr += "</tr>";
                        }
                    } else {

                    }
                    $('#comments').val('');
                    $('#amc_work_time_from').val('');
                    $('#amc_work_time_to').val('');
                    $('#btn_amc_comment_close').click();
                    $('#data_new_amc_comments_table tbody').empty();
                    $("#data_new_amc_comments_table tbody").append(tr);

                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>

    <!-- Modal NewAMCRequest-->
    <a id="a_modalnewamcrequest" data-bs-toggle="modal" data-bs-target="#ModalNewAMCRequest"></a>
    <div class="modal fade" id="ModalNewAMCRequest" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New AMC Request</h5>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Track ID</label>
                                <lable class="form-control" name="id" id="amc_id"></lable>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <lable class="form-control" name="date" id="date"></lable>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Time</label>
                                <lable class="form-control" name="date_time" id="date_time"></lable>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Customer Name</label>
                                <lable class="form-control" name="cust_name" id="cust_name"></lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Contact Person</label>
                                <lable class="form-control" name="contact_person" id="contact_person"></lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Mobile No</label>
                                <lable class="form-control" name="mobile_no" id="mobile_no"></lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Date</label>
                                <lable class="form-control" name="service_date" id="service_date"></lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Time</label>
                                <lable class="form-control" name="service_time" id="service_time"></lable>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Location of Work</label>
                                <div class="form-control" style="height: auto;" id="location_of_work"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Scope of Work</label>
                                <div class="form-control" style="height: auto;" id="scope_of_work"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <b>Engineer Comments</b> <a class="float-right btn-sm btn-primary p-0 pl-1 pr-1"
                                data-bs-toggle="modal" data-bs-target="#ModalComments" >Comment</a>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list" id="data_new_amc_comments_table"
                                width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="70%">Comment</th>
                                        <th width="10%">Status</th>
                                        <th width="20%"></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>



                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-light" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal AddServiceRequest-->

    <!-- Modal Comments-->
    <a id="a_modalcomments" data-bs-toggle="modal" data-bs-target="#ModalComments" ></a>
    <div class="modal fade" id="ModalComments" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add AMC Comments</h5>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="amc_work_date" id="amc_work_date"
                                    readonly value="{{ date('Y-m-d') }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Time From</label>
                                <input type="time" class="form-control" name="amc_work_time_from"
                                    id="amc_work_time_from" onchange="validateTime()" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Time To</label>
                                <input type="time" class="form-control" name="amc_work_time_to" id="amc_work_time_to"
                                    onchange="validateTime()" required />
                            </div>
                        </div>
                        <script>
                            function validateTime() {
                                const workTimeFrom = $('#amc_work_time_from').val();
                                const workTimeTo = $('#amc_work_time_to').val();

                                if (workTimeTo < workTimeFrom && workTimeTo != "") {
                                    alert('Work time "To" cannot be earlier than "From".');
                                    $('#amc_work_time_to').val('');
                                    return false;
                                }
                                return true;
                            }
                        </script>

                        <div class="col-md-10">
                            <div class="mb-3">
                                <label for="" class="form-label">Comments</label>
                                <textarea rows="4" class="form-control" name="comments" id="comments"></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Status</label>
                                <select class="form-control" name="comment_status" id="comment_status">
                                    <option value="1">Pending</option>
                                    <option value="2">Completed</option>
                                </select>
                                <br />
                                <input type="hidden" name="amc_id_comment" id="amc_id_comment">
                                <input type="hidden" name="amc_id_work" id="amc_id_work">
                                <button type="submit" class="btn btn-primary"
                                    onclick="add_amc_request_comments()">Add</button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-light" type="button" data-bs-dismiss="modal"
                        id="btn_amc_comment_close">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal AddServiceRequest-->

    <script>
        function new_ps_request_popup(id) {

            $('#ps_id').text($('#ps_doc_number_' + id).val());
            $('#ps_id_comment').val($('#ps_id_' + id).val());
            $('#ps_date').text($('#ps_date_' + id).val());
            $('#ps_date_time').text($('#ps_date_time_' + id).val());
            $('#ps_cust_name').text($('#ps_cust_name_' + id).val());
            $('#ps_contact_person').text($('#ps_contact_person_' + id).val());
            $('#ps_mobile').text($('#ps_mobile_' + id).val());
            $('#ps_service_date').text($('#ps_service_date_' + id).val());
            $('#ps_service_time').text($('#ps_service_time_' + id).val());
            $('#ps_location_of_work').text($('#ps_location_of_work_' + id).val());
            $('#ps_scope_of_work').html($('#ps_scope_of_work_' + id).val());

            //var str = $('#scope_of_work_'+id).val();
            //var regex = /<br\s*[\/]?>/gi;
            //$("#scope_of_work").html(str.replace(regex, "<br/>"));

            //$('#scope_of_work').text($('#scope_of_work_'+id).val());

            //view_ps_request_work($('#ps_id_'+id).val());

            $('#a_modalnew_ps_request').click();
            view_ps_request_comments($('#ps_id_' + id).val());
        }

        function view_ps_request_comments(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-ps-service-request-get-comments') }}";
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
                            var status = "";
                            if (dataResult['data'][i].status == 1) {
                                status = "Pending";
                            }
                            if (dataResult['data'][i].status == 2) {
                                status = "Completed";
                            }
                            tr += "<tr>";
                            tr += "<td>" + dataResult['data'][i].comments + "<br /> On : " + get_format_date(
                                dataResult['data'][i].work_date) + " from " + get_format_time(dataResult[
                                'data'][i].work_time_from) + " to " + get_format_time(dataResult['data'][i]
                                .work_time_to) + "</td>";
                            tr += "<td>" + status + "</td>";
                            tr += "<td>" + dataResult['data'][i].full_name + " <br /> " + get_format_date_time(
                                dataResult['data'][i].created_at) + "</td>";
                            tr += "</tr>";
                        }
                    } else {
                        $("#contact_person").val();
                        $("#mobile_no").val();
                        $("#location_of_work").val();
                    }
                    $('#data_new_ps_comments_table tbody').empty();
                    $("#data_new_ps_comments_table tbody").append(tr);

                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function add_ps_request_comments() {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-ps-service-request-comments') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    ps_id: $('#ps_id_comment').val(),
                    comments: $('#ps_comments').val(),
                    status: $('#ps_comment_status').val(),
                    work_date: $('#ps_work_date').val(),
                    work_time_from: $('#ps_work_time_from').val(),
                    work_time_to: $('#ps_work_time_to').val(),
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
                            var status = "";
                            if (dataResult['data'][i].status == 1) {
                                status = "Pending";
                            }
                            if (dataResult['data'][i].status == 2) {
                                status = "Completed";
                            }
                            tr += "<tr>";
                            tr += "<td>" + dataResult['data'][i].comments + "<br /> On : " + get_format_date(
                                dataResult['data'][i].work_date) + " from " + get_format_time(dataResult[
                                'data'][i].work_time_from) + " to " + get_format_time(dataResult['data'][i]
                                .work_time_to) + "</td>";
                            tr += "<td>" + status + "</td>";
                            tr += "<td>" + dataResult['data'][i].full_name + " <br /> " + get_format_date_time(
                                dataResult['data'][i].created_at) + "</td>";
                            tr += "</tr>";
                        }
                    } else {

                    }
                    $('#ps_comments').val('');
                    $('#ps_work_time_from').val(''),
                        $('#ps_work_time_to').val(''),
                        $('#data_new_ps_comments_table tbody').empty();
                    $("#data_new_ps_comments_table tbody").append(tr);
                    $("#btn_ps_comment_close").click();
                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>
    <!-- Modal NewPSRequest-->
    <a id="a_modalnew_ps_request" data-bs-toggle="modal" data-bs-target="#ModalNewPSRequest" ></a>
    <div class="modal fade" id="ModalNewPSRequest" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New PS Request</h5>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Track ID</label>
                                <lable class="form-control" name="id" id="ps_id"></lable>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <lable class="form-control" name="date" id="ps_date"></lable>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Time</label>
                                <lable class="form-control" name="date_time" id="ps_date_time"></lable>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Customer Name</label>
                                <lable class="form-control" name="cust_name" id="ps_cust_name"></lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Contact Person</label>
                                <lable class="form-control" name="contact_person" id="ps_contact_person"></lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Mobile No</label>
                                <lable class="form-control" name="mobile" id="ps_mobile"></lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Date</label>
                                <lable class="form-control" name="service_date" id="ps_service_date"></lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Time</label>
                                <lable class="form-control" name="service_time" id="ps_service_time"></lable>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Location of Work</label>
                                <div class="form-control" style="height: auto;" id="ps_location_of_work"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Scope of Work</label>
                                <div class="form-control" style="height: auto;" id="ps_scope_of_work"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <b>Engineer Comments</b> <a class="btn-sm btn-primary float-right" data-bs-toggle="modal"
                                data-bs-target="#ModalPSComments">Comment</a>
                        </div>
                    </div>

                    {{--  <div class="row">
                        <div class="col-md-10">
                            <div class="mb-3">
                                <label for="" class="form-label">Comments</label>
                                <textarea rows="4" class="form-control" name="comments" id="ps_comments"></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Status</label>
                                <select class="form-control" name="comment_status" id="ps_comment_status">
                                    <option value="1">Pending</option>
                                    <option value="2">Completed</option>
                                </select>
                                <br />
                                <input type="hidden" name="ps_id_comment" id="ps_id_comment">
                                <button type="submit" class="btn btn-primary" onclick="ps_request_comments()">Add</button>
                            </div>
                        </div>
                    </div>  --}}

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list" id="data_new_ps_comments_table"
                                width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="70%">Comment</th>
                                        <th width="10%">Status</th>
                                        <th width="20%"></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-light" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal NewPSRequest-->
    <!-- Modal PS Comments-->
    <div class="modal fade" id="ModalPSComments" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add PS Comments</h5>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="ps_work_date" id="ps_work_date"
                                    readonly value="{{ date('Y-m-d') }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Time From</label>
                                <input type="time" class="form-control" name="ps_work_time_from"
                                    id="ps_work_time_from" onchange="validateTimePS()" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Time To</label>
                                <input type="time" class="form-control" name="ps_work_time_to" id="ps_work_time_to"
                                    onchange="validateTimePS()" required />
                            </div>
                        </div>
                        <script>
                            function validateTimePS() {
                                const workTimeFrom = $('#ps_work_time_from').val();
                                const workTimeTo = $('#ps_work_time_to').val();

                                if (workTimeTo < workTimeFrom && workTimeTo != "") {
                                    alert('Work time "To" cannot be earlier than "From".');
                                    $('#ps_work_time_to').val('');
                                    return false;
                                }
                                return true;
                            }
                        </script>

                        <div class="col-md-10">
                            <div class="mb-3">
                                <label for="" class="form-label">Comments</label>
                                <textarea rows="4" class="form-control" name="ps_comments" id="ps_comments"></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Status</label>
                                <select class="form-control" name="ps_comment_status" id="ps_comment_status">
                                    <option value="1">Pending</option>
                                    <option value="2">Completed</option>
                                </select>
                                <br />
                                <input type="hidden" name="ps_id_comment" id="ps_id_comment">
                                <button type="submit" class="btn btn-primary"
                                    onclick="add_ps_request_comments()">Add</button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-light" type="button" data-bs-dismiss="modal"
                        id="btn_ps_comment_close">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal PS Comments-->

    {{--  NEW PS REQUEST END  --}}


    {{--  New Pre-Sales Support Start  --}}
    <script>
        function new_pre_sales_support_popup(id) {
            $('#support_id').text($('#support_doc_number_' + id).val());
            $('#support_created_date').text($('#support_created_date_' + id).val());
            $('#support_cust_name').text($('#support_customer_' + id).val());
            $('#support_contact_person').text($('#support_contact_person_' + id).val());
            $('#support_mobile').text($('#support_mobile_' + id).val());
            $('#support_service_date').text($('#support_date_' + id).val());
            $('#support_service_time').text($('#support_time_from_' + id).val());
            $('#support_location_of_work').text($('#support_site_name_' + id).val());
            $('#presales_scope_of_work').html($('#support_work_' + id).val());
            $('#presales_id_comment').val(id);

            view_presales_request_comments(id);
            $('#a_modalnew_pre_sales_request').click();
        }

        function view_presales_request_comments(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-deal-support-activity-comments-view') }}";
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
                            var status = "";
                            if (dataResult['data'][i].status == 1) {
                                status = "Pending";
                            }
                            if (dataResult['data'][i].status == 2) {
                                status = "Completed";
                            }
                            tr += "<tr>";
                            tr += "<td>" + dataResult['data'][i].comments + "<br /> On : " + get_format_date(
                                dataResult['data'][i].work_date) + " from " + get_format_time(dataResult[
                                'data'][i].work_time_from) + " to " + get_format_time(dataResult['data'][i]
                                .work_time_to) + "</td>";
                            tr += "<td>" + status + "</td>";
                            tr += "<td>" + dataResult['data'][i].full_name + " <br /> " + get_format_date_time(
                                dataResult['data'][i].created_at) + "</td>";
                            tr += "</tr>";
                        }
                    } else {

                    }
                    $('#data_new_presales_comments_table tbody').empty();
                    $("#data_new_presales_comments_table tbody").append(tr);

                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function add_presales_request_comments() {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-deal-support-activity-comments-add') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    support_id: $('#presales_id_comment').val(),
                    comments: $('#presales_comments').val(),
                    status: $('#presales_comment_status').val(),
                    work_date: $('#presales_work_date').val(),
                    work_time_from: $('#presales_work_time_from').val(),
                    work_time_to: $('#presales_work_time_to').val(),
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
                            var status = "";
                            if (dataResult['data'][i].status == 1) {
                                status = "Pending";
                            }
                            if (dataResult['data'][i].status == 3) {
                                status = "Completed";
                            }
                            tr += "<tr>";
                            tr += "<td>" + dataResult['data'][i].comments + "<br /> On : " + get_format_date(
                                dataResult['data'][i].work_date) + " from " + get_format_time(dataResult[
                                'data'][i].work_time_from) + " to " + get_format_time(dataResult['data'][i]
                                .work_time_to) + "</td>";
                            tr += "<td>" + status + "</td>";
                            tr += "<td>" + dataResult['data'][i].full_name + " <br /> " + get_format_date_time(
                                dataResult['data'][i].created_at) + "</td>";
                            tr += "</tr>";
                        }
                    } else {

                    }
                    $('#presales_comments').val('');
                    $('#presales_work_time_from').val('');
                    $('#presales_work_time_to').val('');
                    $('#data_new_presales_comments_table tbody').empty();
                    $("#data_new_presales_comments_table tbody").append(tr);
                    $("#comment_close").click();
                    $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>
    <a id="a_modalnew_pre_sales_request" data-bs-toggle="modal" data-bs-target="#ModalNewPreSalesRequest"
        ></a>
    <div class="modal fade" id="ModalNewPreSalesRequest" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Pre-Sales Support</h5>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Support ID </label>
                                <lable class="form-control" name="support_id" id="support_id"></lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <lable class="form-control" name="support_created_date" id="support_created_date">
                                </lable>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Customer Name</label>
                                <lable class="form-control" name="support_cust_name" id="support_cust_name"></lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Contact Person</label>
                                <lable class="form-control" name="support_contact_person" id="support_contact_person">
                                </lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Mobile No</label>
                                <lable class="form-control" name="support_mobile" id="support_mobile"></lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Date</label>
                                <lable class="form-control" name="support_service_date" id="support_service_date">
                                </lable>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Service Time</label>
                                <lable class="form-control" name="support_service_time" id="support_service_time">
                                </lable>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Location of Work</label>
                                <div class="form-control" style="height: auto;" id="support_location_of_work"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Scope of Work</label>
                                <div class="form-control" style="height: auto;" id="presales_scope_of_work"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <b>Engineer Comments</b> <a class="btn-sm btn-primary float-right" data-bs-toggle="modal"
                                data-bs-target="#ModalPreSalesComments" >Comment</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-striped table-hover"
                                    style="table-layout: fixed;width:100%" id="long-list" id="data_new_presales_comments_table"
                                width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="70%">Comment</th>
                                        <th width="10%">Status</th>
                                        <th width="20%"></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-light" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Comments-->
    <div class="modal fade" id="ModalPreSalesComments" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Pre-Sales Comments</h5>
                    <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="presales_work_date"
                                    id="presales_work_date" readonly value="{{ date('Y-m-d') }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Time From</label>
                                <input type="time" class="form-control" name="presales_work_time_from"
                                    id="presales_work_time_from" onchange="validateTimePreSales()" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Time To</label>
                                <input type="time" class="form-control" name="presales_work_time_to"
                                    id="presales_work_time_to" onchange="validateTimePreSales()" required />
                            </div>
                        </div>
                        <script>
                            function validateTimePreSales() {
                                const workTimeFrom = $('#presales_work_time_from').val();
                                const workTimeTo = $('#presales_work_time_to').val();

                                if (workTimeTo < workTimeFrom && workTimeTo != "") {
                                    alert('Work time "To" cannot be earlier than "From".');
                                    $('#presales_work_time_to').val('');
                                    return false;
                                }
                                return true;
                            }
                        </script>

                        <div class="col-md-10">
                            <div class="mb-3">
                                <label for="" class="form-label">Comments</label>
                                <textarea rows="4" class="form-control" name="presales_comments" id="presales_comments"></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Status</label>
                                <select class="form-control" name="presales_comment_status" id="presales_comment_status">
                                    <option value="1">Pending</option>
                                    <option value="2">Completed</option>
                                </select>
                                <br />
                                <input type="hidden" name="presales_id_comment" id="presales_id_comment">
                                <button type="submit" class="btn btn-primary"
                                    onclick="add_presales_request_comments()">Add</button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-light" type="button" data-bs-dismiss="modal"
                        id="comment_close">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Comments-->
    {{--  New Pre-Sales Support End  --}}

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection

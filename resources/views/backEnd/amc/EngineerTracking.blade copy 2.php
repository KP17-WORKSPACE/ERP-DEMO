@extends('backEnd.newmasterpage')
@section('mainContent')
    <script>
        let isFullList = false;

        function list_style_new() {
            const leftNav = document.querySelector('.left-nav');
            const content = document.querySelector('.content-container');

            if (!isFullList) {
                // Switch to FULL LIST VIEW
                isFullList = true;

                leftNav.classList.remove('col-3');
                leftNav.classList.add('col-12');
                leftNav.style.width = '100%';

                content.classList.add('d-none');

                $('#long-list').removeClass('d-none');
                $('#short-list').addClass('d-none');

                $('#filters-long').removeClass('d-none');
                $('#filters-short').addClass('d-none');
            } else {
                // Switch to COMPACT VIEW
                isFullList = false;

                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                leftNav.style.width = '';

                content.classList.remove('d-none');

                $('#long-list').addClass('d-none');
                $('#short-list').removeClass('d-none');

                $('#filters-short').removeClass('d-none');
                $('#filters-long').addClass('d-none');
            }
        }


        //added ny kp
        function toggleLongFilters() {
            console.log("clicked");
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>


    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Engineer Tracking
                </h4>
                <div class="search-filter-container mb-0">


                    <a href="#" data-bs-toggle="modal" data-bs-target="#ModalAddServiceRequest"
                        class="btn btn-light text-dark add-btn"><i class="ico icon-outline-add-square text-success"></i>
                        Add AMC Request</a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#ModalProfessionalServicesRequest"
                        class="btn btn-light text-dark add-btn"><i class="ico icon-outline-add-square text-success"></i>
                        Add Project Reques</a>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#addPreSalesRequest"
                        class="btn btn-light text-dark add-btn"><i class="ico icon-outline-add-square text-success"></i>
                        Add Pre-Sales Request</a>


                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>

                    <div class="dropdown">
                        <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                            <li><a href="{{ url('brand') }}" class="dropdown-item">
                                    Brand</a></li>

                            <li><a href="{{ url('item-category') }}" class="dropdown-item">
                                    Category</a></li>

                            <li><a href="{{ url('create-sub-category') }}" class="dropdown-item">
                                    Sub Category</a></li>

                        </ul>
                    </div>


                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card" style="width: 100%">
                    <div class="card-body">

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-engineer-tracking', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                        <div class="row">

                            <div class="col-1">
                                <label class="form-label">Track ID</label>
                                <input class="form-control" id="search_track_id" type="text" autocomplete="off"
                                    name="search_track_id" value="{{ $ctrl_track_id }}">
                            </div>

                            <div class="col-1">
                                <label class="form-label">Deal ID</label>
                                <input class="form-control" id="search_deal_id" type="text" autocomplete="off"
                                    name="search_deal_id" value="{{ $ctrl_deal_id }}">

                            </div>

                            <div class="col-2">
                                <label class="form-label">Customer Name</label>
                                <select class="form-control js-example-basic-single" name="search_customer_name"
                                    id="search_customer_name">
                                    <option value="">-Select-</option>
                                    @foreach ($customer as $value)
                                        <option @if ($ctrl_customer == $value->id) selected @endif
                                            value="{{ @$value->id }}">{{ @$value->name }}</option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="col-2">
                                <label class="form-label">Engineer</label>
                                <select class="form-control" name="search_engineer" id="search_engineer">
                                    <option value="">Select</option>
                                    @if (count($salesperson) > 0)
                                        @foreach ($salesperson as $list)
                                            <option @if ($ctrl_engineer == $list->user_id)  @endif value="{{ $list->user_id }}">
                                                {{ $list->full_name }}</option>
                                        @endforeach
                                    @endif
                                </select>

                            </div>

                            <div class="col-1-5">
                                <label class="form-label">Service Date From</label>
                                <input class="form-control date-picker" id="search_from_date" type="text"
                                    autocomplete="off" name="search_from_date" value="{{ !empty($ctrl_from_date) ? date('d-m-Y', strtotime($ctrl_from_date)) : '' }}">
                            </div>

                            <div class="col-1-5">
                                <label class="form-label">Service Date To</label>
                                <input class="form-control date-picker" id="search_to_date" type="text"
                                    autocomplete="off" name="search_to_date" value="{{ !empty($ctrl_to_date) ? date('d-m-Y', strtotime($ctrl_to_date)) : '' }}">


                            </div>

                            <div class="col-1-5">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="search_status" id="search_status">
                                    <option value="">All</option>
                                    <option value="1" @if($ctrl_status == 1) selected @endif >Pending</option>
                                    <option value="2" @if($ctrl_status == 2) selected @endif>Completed</option>
                                </select>


                            </div>


                            <div class="col-1-5 filter-field d-none">
                                <button type="submit" class="btn btn-success mt-4 rounded-0 "
                                    id="btnSubmit">Filter</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">



            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover" style="table-layout: fixed;width:100%">

                    <thead class="text-center">
                        <tr>
                            <th width="100px">@lang('Track No')</th>
                            <th width="70px">@lang('Deal ID')</th>
                            <th width="100px">@lang('Date')</th>
                            <th class="text-start">@lang('Customer Name')</th>
                            <th class="text-start">@lang('Enginer')</th>

                            <th width="100px">@lang('Service Date')</th>
                            <th width="100px">@lang('Time From')</th>
                            <th width="100px">@lang('Time To')</th>
                            <th width="100px">@lang('No. of Hrs')</th>
                            <th width="100px">@lang('Status')</th>
                            <th width="140px">@lang('Stage')</th>

                            <th class="text-center" style="width: 60px;">@lang('Action')</th>
                        </tr>
                    </thead>


                    <tbody>
                        @if (count($data))
                            @foreach ($data as $dt)
                                <tr>
                                    <td class="text-center">{{ $dt->doc_number }}</td>
                                    <td class="text-center">{{ $dt->deal_code }}</a></td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime($dt->date)) }}</td>
                                    <td>{{ $dt->cust_name }}</td>

                                    @if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 32)
                                        <td>{{ Auth::user()->full_name }}</td>
                                    @else
                                        <?php
                                        $engineername = '';
                                        if ($dt->comment_by != null) {
                                            $s = $staff->where('user_id', $dt->comment_by)->pluck('full_name');
                                            $engineername = $s[0];
                                        } elseif ($dt->service_engineer != '') {
                                            $st = explode(',', $dt->service_engineer);
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
                                        }
                                        ?>
                                        <td>{{ $engineername }}</td>
                                    @endif

                                    <td class="text-center">
                                        @if ($dt->work_date != null)
                                            {{ date('d/m/Y', strtotime($dt->work_date)) }}
                                        @elseif($dt->service_date != null)
                                            {{ date('d/m/Y', strtotime($dt->service_date)) }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($dt->work_time_from != null)
                                            {{ date('h:i A', strtotime($dt->work_time_from)) }}
                                        @elseif($dt->service_time != null)
                                            {{ date('h:i A', strtotime($dt->service_time)) }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($dt->work_time_to != null)
                                            {{ date('h:i A', strtotime($dt->work_time_to)) }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($dt->tim != null)
                                            @if ($dt->tim < 60)
                                                {{ $dt->tim }} Min
                                            @else
                                                {{ @App\SysHelper::com_curr_format($dt->tim / 60, 2, ':', '') }} Hrs
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">

                                        @if (@$dt->status == 1)
                                            <span class="text-warning">Pending</span>
                                        @elseif(@$dt->status == 2)
                                            <span class="text-success">Completed</span>
                                        @else
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <?php
                                        $deal_stage = '';
                                        $track = $deal_track->where('deal_id', $dt->deal_id);
                                        if (count($track) > 0) {
                                            foreach ($track as $tr) {
                                                $deal_stage = $deal_stage = @App\SysHelper::deal_track_status3($tr->receivables, $tr->delivery, $tr->invoice, $tr->purchease, $tr->sales, $tr->accounts);
                                            }
                                        } else {
                                            $dl = $deals->where('id', $dt->deal_id);
                                            if (count($dl) > 0) {
                                                foreach ($dl as $d) {
                                                    $deal_stage = @App\SysHelper::deal_stage($d->stage);
                                                }
                                            }
                                        }
                                        ?>
                                        {!! $deal_stage !!}
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            @if ($dt->type == 'AMC')
                                                <button class="btn btn-sm btn-light" style="cursor: pointer;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#amccomments_{{ $dt->id }}"><i
                                                        class="ico icon-outline-chat-round-dots" aria-hidden="true"
                                                        style="font-size: 16px"></i></button>
                                            @endif

                                            @if ($dt->type == 'PS')
                                                <button class="btn btn-sm btn-light" style="cursor: pointer;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#pscomments_{{ $dt->id }}"><i
                                                        class="ico icon-outline-chat-round-dots" aria-hidden="true"
                                                        style="font-size: 16px"></i></button>
                                            @endif

                                            @if ($dt->type == 'PRESALES')
                                                <button class="btn btn-sm btn-light" style="cursor: pointer;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#presalescomments_{{ $dt->id }}"><i
                                                        class="ico icon-outline-chat-round-dots" aria-hidden="true"
                                                        style="font-size: 16px"></i></button>
                                            @endif
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>

                </table>
            </div>
        </div>
    </aside>



    @if (count($amc_list) > 0)
        @foreach ($amc_list as $amc)
            <div class="modal  fade" id="amccomments_{{ $amc->id }}" data-bs-backdrop="false" tabindex="-1"
                aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg ">

                    <div class="modal-content" style="max-height: 80vh">
                        <div class="modal-header">
                            <h4 class="modal-title" id="editModalLabel">AMC - Scope of Work & Comments
                                ({{ $amc->doc_number }})
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body m-0 p-0">
                            <div class="card mb-0 mt-0">
                                <div class="card-body">
                                    <div class="col-md-12">

                                        @php
                                            $amc_comments_data = $amc_comments->where('amc_id', $amc->id);
                                            $sw = $amc_work->where('amc_id', $amc->id);
                                        @endphp

                                        @if (count($sw) > 0)
                                            <table class="table table-hover" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" width="7%">No</th>
                                                        <th width="93%">Scope of Work</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($sw as $w)
                                                        <tr>
                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                            <td>{{ $w->work }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif

                                        @if (count($amc_comments_data) > 0)
                                            <table class="table table-hover" width="100%" cellspacing="0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="30%">Comment</th>
                                                        <th width="15%">Work Date</th>
                                                        <th width="10%">From</th>
                                                        <th width="10%">To</th>
                                                        <th width="10%">Status</th>
                                                        <th width="25%">Engineer / Created At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($amc_comments_data as $cmts)
                                                        <tr>
                                                            <td>{{ $cmts->comments }}</td>
                                                            <td>{{ date('d/m/Y', strtotime($cmts->work_date)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($cmts->work_time_from)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($cmts->work_time_to)) }}</td>
                                                            <td>
                                                                @if ($cmts->status == 1)
                                                                    <span class=" text-dark">Pending</span>
                                                                @else
                                                                    <span class="text-success">Completed</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $cmts->engineerid->full_name }} <br>
                                                                <small
                                                                    class="text-muted">{{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
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
        @endforeach
    @endif



    @if (count($ps_list) > 0)
        @foreach ($ps_list as $ps)
            <div class="modal  fade" id="pscomments_{{ $ps->id }}" data-bs-backdrop="false" tabindex="-1"
                aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg ">

                    <div class="modal-content" style="max-height: 80vh">
                        <div class="modal-header">
                            <h4 class="modal-title" id="editModalLabel">Project Service - Scope of Work & Comments
                                ({{ $ps->doc_number }})
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body m-0 p-0">
                            <div class="card mb-0 mt-0">
                                <div class="card-body">
                                    <div class="col-md-12">

                                        @php
                                            $ps_comments_data = $ps_comments->where('ps_id', $ps->id);
                                            $sw = $ps_work->where('service_id', $ps->id);
                                        @endphp


                                        @if (count($sw) > 0)
                                            <table class="table table-hover" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th width="7%">No</th>
                                                        <th width="93%">Scope of Work</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($sw as $w)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $w->work }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif

                                        @if (count($ps_comments_data) > 0)
                                            <table class="table table-hover" width="100%" cellspacing="0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="30%">Comment</th>
                                                        <th width="15%">Work Date</th>
                                                        <th width="10%">From</th>
                                                        <th width="10%">To</th>
                                                        <th width="10%">Status</th>
                                                        <th width="25%">Engineer / Created At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($ps_comments_data as $cmts)
                                                        <tr>
                                                            <td>{{ $cmts->comments }}</td>
                                                            <td>{{ date('d/m/Y', strtotime($cmts->work_date)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($cmts->work_time_from)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($cmts->work_time_to)) }}</td>
                                                            <td>
                                                                @if ($cmts->status == 1)
                                                                    <span class="text-dark">Pending</span>
                                                                @else
                                                                    <span class="text-success">Completed</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $cmts->engineerid->full_name }} <br>
                                                                <small
                                                                    class="text-muted">{{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
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
        @endforeach
    @endif


    @if (count($presales_list) > 0)
        @foreach ($presales_list as $presales)
            <div class="modal  fade" id="presalescomments_{{ $presales->id }}" data-bs-backdrop="false" tabindex="-1"
                aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg ">

                    <div class="modal-content" style="max-height: 80vh">
                        <div class="modal-header">
                            <h4 class="modal-title" id="editModalLabel">Presales - Scope of Work & Comments
                                ({{ $presales->doc_number }})
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body m-0 p-0">
                            <div class="card mb-0 mt-0">
                                <div class="card-body">
                                    <div class="col-md-12">

                                        @php
                                            $presales_comments_data = $presales_comments->where(
                                                'support_id',
                                                $presales->id,
                                            );
                                            $sw = $presales_work->where('support_id', $presales->id);
                                        @endphp

                                        @if (count($sw) > 0)
                                            <table class="table table-hover" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th width="7%">No</th>
                                                        <th width="93%">Scope of Work</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($sw as $w)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $w->work }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif

                                        @if (count($presales_comments_data) > 0)
                                            <table class="table table-hover" width="100%" cellspacing="0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="30%">Comment</th>
                                                        <th width="15%">Work Date</th>
                                                        <th width="10%">From</th>
                                                        <th width="10%">To</th>
                                                        <th width="10%">Status</th>
                                                        <th width="25%">Engineer / Created At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($presales_comments_data as $cmts)
                                                        <tr>
                                                            <td>{{ $cmts->comments }}</td>
                                                            <td>{{ date('d/m/Y', strtotime($cmts->work_date)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($cmts->work_time_from)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($cmts->work_time_to)) }}</td>
                                                            <td>
                                                                @if ($cmts->status == 1)
                                                                    <span class="text-dark">Pending</span>
                                                                @else
                                                                    <span class="text-success">Completed</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                {{ $cmts->engineerid->full_name }} <br>
                                                                <small
                                                                    class="text-muted">{{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
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
        @endforeach
    @endif


    <div class="modal  fade" id="ModalAddServiceRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg ">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-request-list-add', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-service-request-list-add']) }}

            <div class="modal-content" style="max-height: 80vh">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add AMC Request
                        ({{ @App\SysHelper::get_new_code('sys_crm_amc_table', 'AM', 'doc_number') }}) </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <div class="col-3">
                                    <label for="" class="form-label">Deal ID</label>
                                    <input type="text" class="form-control" name="deal_id" id="deal_id">

                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label"> Date</label>
                                    <input type="text" class="form-control date-picker" name="date" id="date"
                                        value="{{ date('d/m/Y') }}" required>
                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label"> Company Name</label>
                                    <select class="form-control js-example-basic-single" name="cust_name" id="cust_name"
                                        required>
                                        <option value="">-Select-</option>
                                        @foreach ($salespersonamc as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" type="text" name="contact_person" id="contact_person"
                                        required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" type="text" name="mobile_no" id="mobile_no" required>

                                </div>


                                <div class="col-6">
                                    <label for="" class="form-label">Location Of Work</label>
                                    <input type="text" class="form-control" name="location_of_work"
                                        id="location_of_work">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Date</label>
                                    <input type="text" class="form-control date-picker" name="service_date"
                                        id="service_date" required min="{{ date('d/m/Y') }}">
                                </div>




                                <div class="col-3">
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



                                </div>



                                <div class="col-3">
                                    <label for="" class="form-label">Source</label>
                                    <select class="form-control" name="source" id="source" required>
                                        <option selected value="">Select</option>
                                        <option value="Email">Email</option>
                                        <option value="Whatsapp">Whatsapp</option>
                                        <option value="Phone">Phone</option>
                                    </select>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Attachment</label>
                                    <input type="file" class="form-control" name="attachment" id="attachment">
                                </div>

                                <div class="col-12">
                                    <label for="" class="form-label">Service Engineer</label>
                                    <select class="form-control js-example-basic-single" name="service_engineer[]"
                                        id="service_engineer" required multiple>
                                        @if (count($salesperson) > 0)
                                            @foreach ($salesperson as $list)
                                                <option value="{{ $list->user_id }}">{{ $list->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>





                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle text-center mb-1" id="taskTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 10%;">No</th>
                                                    <th style="width: 80%;">Task</th>
                                                    <th style="width: 10%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="text" class="form-control serial text-center"
                                                            value="1"></td>
                                                    <td><input type="text" name="scope_of_work[]"
                                                            class="form-control task" placeholder="Enter task"></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-danger rounded-0 btn-sm deleteRow">
                                                                <i class="ico icon-bold-trash-bin-minimalistic-2"
                                                                    style="font-size: 16px"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" id="addRow" class="btn btn-light rounded-0"><i
                                            class="ico icon-outline-add-square text-success"></i> Add
                                        Row</button>


                                </div>




                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Add Request
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>


    <div class="modal  fade" id="ModalProfessionalServicesRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top:10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-ps-service-request-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Project Service Request
                        ({{ @App\SysHelper::get_new_code('sys_crm_ps_service_table', 'PR', 'doc_number') }})</h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <input type="hidden" name="amc_id" id="amc_id">

                                <div class="col-3">
                                    <label for="" class="form-label">Deal ID</label>
                                    <input type="text" class="form-control" name="add_deal_id" id="add_deal_id"
                                        value="">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Date</label>
                                    <input type="text" class="form-control date-picker" name="date" id="add_date"
                                        value="{{ date('d/m/Y') }}">
                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label">Customer Name</label>
                                    <select class="form-control js-example-basic-single" name="add_cust_name"
                                        id="add_cust_name" required>
                                        <option value="">-Select-</option>
                                        @foreach ($customers_AddRequest as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                        @endforeach
                                    </select>

                                </div>



                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" type="text" name="contact_person"
                                        id="add_contact_person" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" type="text" name="mobile" id="add_mobile_no"
                                        required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">@lang('Location of Work')</label>
                                    <input class="form-control" type="text" name="location_of_work"
                                        id="add_location_of_work" required>
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Date</label>
                                    <input class="form-control date-picker" type="text" name="service_date"
                                        id="service_date">
                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Time</label>
                                    <input class="form-control" id="service_time" type="time" required
                                        name="service_time" value="">

                                </div>

                                <div class="col-9">

                                    <label for="engineer" class="form-label">Service Engineer</label>
                                    <select id="engineer" name="add_engineer[]"
                                        class="form-control js-example-basic-single" multiple>
                                        <option value="">Select</option>
                                        @php
                                            $englist = @App\SysHelper::get_engineer_list();
                                            foreach ($englist as $list) {
                                                echo '<option value="' .
                                                    $list->user_id .
                                                    '" >' .
                                                    $list->full_name .
                                                    '</option>';
                                            }
                                        @endphp
                                    </select>
                                </div>



                                <div class="col-3">
                                    <label for="" class="form-label">Attachment</label>
                                    <input class="form-control" id="attachment" type="file" name="attachment"
                                        value="">

                                </div>






                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle text-center mb-1" id="taskTable2">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 10%;">No</th>
                                                    <th style="width: 80%;">Task</th>
                                                    <th style="width: 10%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="text" class="form-control serial text-center"
                                                            value="1"></td>
                                                    <td><input type="text" name="scope_of_work[]"
                                                            class="form-control task" placeholder="Enter task"></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-danger rounded-0 btn-sm deleteRow2">
                                                                <i class="ico icon-bold-trash-bin-minimalistic-2"
                                                                    style="font-size: 16px"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" id="addRow2" class="btn btn-light rounded-0"><i
                                            class="ico icon-outline-add-square text-success"></i> Add
                                        Row</button>


                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>


    <div class="modal  fade" id="addPreSalesRequest" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10%">
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-sales-req-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Pre-Sales Request
                        ({{ @App\SysHelper::get_new_code('sys_crm_support', 'PS', 'doc_number') }})
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row gap-rows">

                                <input type="hidden" name="support_id" value="0" />




                                <div class="col-3">
                                    <label for="" class="form-label">Deal ID</label>
                                    <input type="number" class="form-control" name="deal_id" id="deal_id" required>
                                </div>




                                <div class="col-6">
                                    <label for="" class="form-label">Customer Name</label>
                                    <select class="form-control js-example-basic-single" name="add_cust_name"
                                        id="sales_add_cust_name" required>
                                        <option value="">-Select-</option>
                                        @foreach ($customer_salesreq as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>




                                <div class="col-3">
                                    <label for="" class="form-label">Contact Person</label>
                                    <input class="form-control" id="sales_add_contact_person" type="text" required
                                        name="contact_person" value="">



                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Mobile No</label>
                                    <input class="form-control" id="sales_add_mobile_no" type="text" required
                                        name="mobile" value="">

                                </div>

                                <div class="col-6">
                                    <label for="" class="form-label">Location of Work</label>
                                    <input type="text" class="form-control" name="add_site_name"
                                        id="sales_add_site_name" required>


                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Date</label>
                                    <input class="form-control date-picker" id="add_service_date" type="text" required
                                        name="service_date" value="">

                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Service Time</label>
                                    <input class="form-control" id="add_service_time" type="time" required
                                        name="service_time" value="">

                                </div>

                                <div class="col-3">
                                    <label for="" class="form-label">Attachment</label>
                                    <input class="form-control" id="attachment" type="file" name="attachment"
                                        value="">
                                </div>


                                <div class="col-6">
                                    <label for="" class="form-label">Service Engineer</label>
                                    <select required id="add_engineer" name="add_engineer[]"
                                        class="form-control js-example-basic-single" multiple>

                                        @php
                                            $englist = @App\SysHelper::get_engineer_list();
                                            foreach ($englist as $list) {
                                                echo '<option value="' .
                                                    $list->user_id .
                                                    '" >' .
                                                    $list->full_name .
                                                    '</option>';
                                            }
                                        @endphp
                                    </select>
                                </div>



                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle text-center mb-1" id="taskTable3">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 10%;">No</th>
                                                    <th style="width: 80%;">Task</th>
                                                    <th style="width: 10%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><input type="text" class="form-control serial text-center"
                                                            value="1"></td>
                                                    <td><input type="text" name="scope_of_work[]"
                                                            class="form-control task" placeholder="Enter task"></td>
                                                    <td>
                                                        <div class="d-flex justify-content-center">
                                                            <button type="button"
                                                                class="btn btn-danger rounded-0 btn-sm deleteRow3">
                                                                <i class="ico icon-bold-trash-bin-minimalistic-2"
                                                                    style="font-size: 16px"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" id="addRow3" class="btn btn-light rounded-0"><i
                                            class="ico icon-outline-add-square text-success"></i> Add
                                        Row</button>


                                </div>



                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>



    <script>
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
                            var name = dataResult['data'][i].customer_salutation + ' ' + dataResult[
                                    'data'][i]
                                .first_name + ' ' + dataResult['data'][i].last_name;
                            var address = dataResult['data'][i].address + ', ' + dataResult['data'][
                                    i
                                ]
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
        $(document).ready(function() {


            $(document).on("change", "#cust_name", function() {
                var id = $("#cust_name").val();
                get_cust_name(id);
            });







            // Function to update serial numbers
            function updateSerialNumbers() {
                $('#taskTable tbody tr').each(function(index) {
                    $(this).find('.serial').val(index + 1);
                });
            }

            // Add row
            $('#addRow').click(function() {
                let rowCount = $('#taskTable tbody tr').length + 1;
                let newRow = `
      <tr>
        <td><input type="text" class="form-control serial text-center" value="${rowCount}" readonly></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-danger rounded-0 btn-sm deleteRow"><i
                                                                class="ico icon-bold-trash-bin-minimalistic-2"
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
                $('#taskTable tbody').append(newRow);
            });

            // Delete row
            $(document).on('click', '.deleteRow', function() {
                $(this).closest('tr').remove();
                updateSerialNumbers();
            });
        });
    </script>



    <script>
        $(document).ready(function() {
            $(document).on("change", "#add_cust_name", function() {
                var id = $("#add_cust_name").val();
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
                                var name = dataResult['data'][i].customer_salutation + ' ' + dataResult[
                                        'data'][i]
                                    .first_name + ' ' + dataResult['data'][i].last_name;
                                var address = dataResult['data'][i].address + ', ' + dataResult['data'][
                                        i
                                    ]
                                    .address2 + ', ' + dataResult['data'][i].city;

                                $("#add_contact_person").val(name.replace('null ', '').replace('null',
                                    ''));
                                $("#add_mobile_no").val(dataResult['data'][i].mobile);
                                $("#add_location_of_work").val(dataResult['data'][i].address);
                            }
                        } else {
                            $("#add_contact_person").val();
                            $("#add_mobile_no").val();
                            $("#add_location_of_work").val();
                        }
                        $("#loading_bg").css("display", "none");
                    }
                });
            }


            // Function to update serial numbers
            function updateSerialNumbers2() {
                $('#taskTable2 tbody tr').each(function(index) {
                    $(this).find('.serial').val(index + 1);
                });
            }

            // Add row
            $('#addRow2').click(function() {
                let rowCount = $('#taskTable2 tbody tr').length + 1;
                let newRow = `
      <tr>
        <td><input type="text" class="form-control serial text-center" value="${rowCount}" readonly></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-danger rounded-0 btn-sm deleteRow2"><i
                                                                class="ico icon-bold-trash-bin-minimalistic-2"
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
                $('#taskTable2 tbody').append(newRow);
            });

            // Delete row
            $(document).on('click', '.deleteRow2', function() {
                $(this).closest('tr').remove();
                updateSerialNumbers2();
            });

        });
    </script>


    <script>
        $(document).ready(function() {

            $(document).on("change", "#sales_add_cust_name", function() {
                var id = $("#sales_add_cust_name").val();

                get_cust_name3(id);

            });

            function get_cust_name3(id) {
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
                            console.log("hereeee")
                            for (var i = 0; i < len; i++) {
                                var name = dataResult['data'][i].customer_salutation + ' ' + dataResult[
                                        'data'][i]
                                    .first_name + ' ' + dataResult['data'][i].last_name;
                                var address = dataResult['data'][i].address + ', ' + dataResult['data'][
                                        i
                                    ]
                                    .address2 + ', ' + dataResult['data'][i].city;

                                $("#sales_add_contact_person").val(name.replace('null ', '').replace(
                                    'null', ''));
                                $("#sales_add_mobile_no").val(dataResult['data'][i].mobile);
                                $("#sales_add_site_name").val(dataResult['data'][i].address);
                            }
                        } else {
                            $("#sales_add_contact_person").val();
                            $("#sales_add_mobile_no").val();
                            $("#sales_add_site_name").val();
                        }
                        $("#loading_bg").css("display", "none");
                    }
                });
            }


            function updateSerialNumbers3() {
                $('#taskTable3 tbody tr').each(function(index) {
                    $(this).find('.serial').val(index + 1);
                });
            }

            // Add row
            $('#addRow3').click(function() {
                let rowCount = $('#taskTable3 tbody tr').length + 1;
                let newRow = `
      <tr>
        <td><input type="text" class="form-control serial text-center" value="${rowCount}" readonly></td>
        <td><input type="text" class="form-control task" name="scope_of_work[]" placeholder="Enter task"></td>
        <td><div class="d-flex justify-content-center">
            <button type="button" class="btn btn-danger rounded-0 btn-sm deleteRow3"><i
                                                                class="ico icon-bold-trash-bin-minimalistic-2"
                                                                style="font-size: 16px"></i></button>
             </div></td>
      </tr>`;
                $('#taskTable3 tbody').append(newRow);
            });

            // Delete row
            $(document).on('click', '.deleteRow3', function() {
                $(this).closest('tr').remove();
                updateSerialNumbers3();
            });
        });
    </script>


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

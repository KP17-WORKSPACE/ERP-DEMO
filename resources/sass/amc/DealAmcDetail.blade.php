@extends('backEnd.masterpage')
@section('mainContent')
    @php
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp


    <?php try { ?>

    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3">
                <h2 class="page-heading m-0">
                </h2>
            </div>
            <div>
                <a href="{{ url('crm-amc-list') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> AMC List</a>
            </div>
        </div>


        @if (@isset($amcdata))
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <div class="p-4 card h-100 bg-1">
                        <h2 class="head">AMC ID : {{ $amcdata->doc_number }}</h2>
                        @if ($amcdata->deal_id != '')
                            <span class="mb-1">Deal ID :
                                {{ App\SysHelper::get_code_from_dealid($amcdata->deal_id) }}</span>
                        @endif
                        <p class="mb-2 text-white-100 text-uppercase">Date : {{ date('d/M/Y', strtotime($amcdata->date)) }}
                        </p>
                        <?php try { ?>
                        <p class="mb-2 text-white-100 text-uppercase">Sales Person : {{ $amcdata->salesperson->full_name }}
                        </p>
                        <?php }catch (\Exception $e) {  } ?>

                        <span class="mb-1">AMC Period : {{ date('d/M/Y', strtotime(@$amcdata->start_date)) }} to
                            {{ date('d/M/Y', strtotime(@$amcdata->end_date)) }}</span>


                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="p-4 card h-100">
                        <h2 class="head">Customer Details</h2>
                        <b class="mb-2">{{ $amcdata->custname->name }}</b>
                        <p class="mb-2 text-white-100 text-uppercase">Contact Person: {{ $amcdata->contact_person }}</p>
                        <p class="mb-2 text-white-100 text-uppercase">Contact Number: {{ $amcdata->mobile_no }}</p>
                        <p class="mb-2 text-white-100 text-uppercase">Invoice: {{ $amcdata->invoice }}</p>
                        <p class="mb-2 text-white-100 text-uppercase">Amount: {{ $amcdata->amount }}</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <div class="p-4 card h-100">
                        <h2 class="head">Description</h2>
                        {{ $amcdata->description }}


                        @if ($amcdata->comment)
                            <div class="expiration-comment mt-4">
                                <h2 class="head">Expiry Comment</h2>
                                {{ $amcdata->comment }}
                            </div>
                        @endif

                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <div class="p-4 card h-100">
                        <h2 class="head">Service Requested Details</h2>
                        @if (count($service_request) > 0)
                            @foreach ($service_request as $sr)
                                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                                    <tr>
                                        <td width="150px">Location</td>
                                        <td>: {{ $sr->location_of_work }}</td>
                                    </tr>
                                    <tr>
                                        <td>Service Date</td>
                                        <td>: {{ date('d/m/Y', strtotime($sr->service_date)) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Service Time</td>
                                        <td>: {{ date('H:i A', strtotime($sr->service_time)) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Source</td>
                                        <td>: {{ $sr->source }}</td>
                                    </tr>
                                    <tr>
                                        <td>Service Engineer</td>
                                        <td>: {{ $sr->serviceengineer->full_name }}</td>
                                    </tr>
                                    @php
                                        $sw = $service_request_work->where('service_id', $sr->id);
                                    @endphp
                                    @if (count($sw) > 0)
                                        @php $i=1; @endphp
                                        <tr>
                                            <td>Scope of Work:-</td>
                                            <td>
                                                @foreach ($sw as $w)
                                                    {{ $i }}. {{ $w->work }} <br />
                                                    @php $i++; @endphp
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                                <hr />
                            @endforeach
                        @endif

                    </div>
                </div>

            </div>
        @endif






        <!-- Modal Support Activity -->
        <div class="modal fade" id="ModalActivity" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Support Activity</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-activity', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                    @if (@isset($support))
                        <input type="hidden" name="support_id" value="{{ $support->id }}" />
                        @php
                            $value = date('m-d-Y');
                            if (isset($support)) {
                                @$support_date = date('Y-m-d', strtotime(@$support->support_date));
                                @$time_from = date('H:i', strtotime(@$support->time_from));
                                @$time_to = date('H:i', strtotime(@$support->time_to));
                            }
                        @endphp
                    @else
                        <input type="hidden" name="support_id" value="0" />
                    @endif

                    <input type="hidden" name="service_id" value="{{ $support->id }}" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">Date</label>
                                    <input type="date" class="form-control" name="activity_date" id="activity_date"
                                        value="{{ $support_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">From</label>
                                    <input type="time" class="form-control" name="activity_from" id="activity_from"
                                        value="{{ $time_from }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">To</label>
                                    <input type="time" class="form-control" name="activity_to" id="activity_to"
                                        value="{{ $time_to }}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="" class="form-label">Description</label>
                                    <textarea class="form-control" name="remarks" id="remarks" rows="10" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="" class="form-label">Document</label>
                                    <input type="file" class="form-control mr-5" name="activitydoc" id="activitydoc">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Activity</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <!-- Modal Support Activity -->

        <!-- Modal Support Close -->
        <div class="modal fade" id="ModalActivityClose" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Close This Task</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-activity-close', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                    @if (@isset($support))
                        <input type="hidden" name="support_id" value="{{ $support->id }}" />
                    @else
                        <input type="hidden" name="support_id" value="0" />
                    @endif

                    <input type="hidden" name="service_id" value="{{ $support->id }}" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="" class="form-label">Description</label>
                                    <textarea class="form-control" name="remarks" id="remarks" rows="10" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="" class="form-label">Document</label>
                                    <input type="file" class="form-control mr-5" name="closingdoc" id="closingdoc">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Close Task</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        <!-- Modal Support Close -->

        <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    @endsection

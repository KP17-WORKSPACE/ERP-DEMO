@extends('backEnd.masterpage')
@section('mainContent')
    @php
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <style>
        .right-aligned {
            right: 0px;
            position: fixed;
            z-index: 9999;
        }
    </style>

    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3">
                <h2 class="page-heading m-0">Lead Number - {{ $leads->lead_code->code }}</h2>
                <span class="page-label">Home - Lead Details</span>
            </div>
            <div>
                <a type="button" data-toggle="modal" data-target="#addlead" class="btn btn-info"><i class="fa fa-plus"></i>
                    New Lead</a>
                <a href="{{ url('crm-leads/show') }}" type="button" class="btn btn-primary"><i class="fa fa-list"></i> View
                    Lead</a>
                <a href="{{ url('crm-leads/' . $leads->id . '/edit') }}" type="button" class="btn btn-info"><i
                        class="fa fa-edit"></i> Edit Lead</a>
                <a href="{{ url()->previous() }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i>
                    Back</a>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100 bg-1">
                    <h2 class="head">Lead Info {!! App\SysHelper::lead_type_new($edit->isproject) !!}
                    </h2>
                    <p class="mb-2 text-white-100 text-uppercase">Lead Name : {{ $leads->lead_name }}</p>
                    <p>
                        @if ($edit->tags != '')
                            Brand :
                            <?php $myArray = explode(',', $edit->tags); ?>
                            @foreach ($myArray as $item)
                                <span class="btn-primary btn-badge py-1 px-3 font-weight-bold">{{ $item }}</span>
                            @endforeach
                        @endif
                    </p>
                    <p class="mb-1">Updated On : {{ date('d/m/Y H:i:s', strtotime(@$leads->updated_at)) }}</p>
                    <div class="text-capitalize">Status :
                        @if ($leads->status == 1)
                            <b class="">New</b>
                        @endif
                        @if ($leads->status == 2)
                            <b class="">Qualified</b>
                        @endif
                        @if ($leads->status == 3)
                            <b class="">Unqualified</b>
                        @endif
                        @if ($leads->status == 4)
                            <b class="">Pending Response</b>
                        @endif
                        @if ($leads->status == 10)
                            <b class="">Closed</b>
                        @endif
                        {{-- <a href="#" class="edit btn-badge rejected py-1 px-3 font-weight-bold ml-2"
                            onclick="updiv()">Edit</a> --}}
                        <div class="border border-primary rounded bg-white text-sm p-2" id="div_update">
                            <select class="dynamicstxt w-50" name="edit_status" id="edit_status" required>
                                <option value="">-Select-</option>
                                <option value="1" {{ $leads->status == 1 ? 'selected' : '' }}>New</option>
                                <option value="2" {{ $leads->status == 2 || $leads->status == 0 ? 'selected' : '' }}>Qualified</option>
                                <option value="3" {{ $leads->status == 3 ? 'selected' : '' }}>Unqualified</option>
                                <option value="4" {{ $leads->status == 4 ? 'selected' : '' }}>Pending Response
                                </option>
                                <option value="10" {{ $leads->status == 10 ? 'selected' : '' }}>Closed</option>
                            </select>

                            <select class="dynamicstxt w-50 mt-2 mb-2" name="edit_sub_status" id="edit_sub_status"
                                style="display:none;" required>
                                <option value="">-Select-</option>

                            </select>
                            <br>

                            <label class="text-dark pb-0 mb-0" for="" style="display: none;" id="follow_up_date_label">follow up</label> 
                            <input class="mb-2" type="date" name="follow_up_date" id="follow_up_date" value="{{ $leads->follow_up_date ?? date('Y-m-d', strtotime('+2 days')) }}"
                                style="display: none;" />
                                

                            <textarea class="form-control mb-1" name="lost_comments" rows="4" style="display: none;" autocomplete="off"
                                id="lost_comments" required placeholder="Reason (Max. 5 Words)">{{$leads->status == 4 ? $leads->sub_status_comment : ''}}</textarea>
                            <button id="btn_edit_status" onclick="change_status({{ $edit->id }})"
                                class="btn btn-xs btn-primary text-xs pt-0 pb-0">Change</button>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Customer Info</h2>
                    <p class="mb-2 text-white-100 text-uppercase">{{ $leads->customername->name }}</p>
                    <span class="mb-1">Contact Person : {{ $leads->cust_name }}</span>
                    @if ($edit->cust_designation != '')
                        <span class="mb-1"> <span class="font-semibold">Designation : </span>
                            {{ $edit->cust_designation }}</span>
                    @endif
                    <span class="mb-1">M: {{ $leads->cust_no }} | E: {{ $leads->cust_email }}</span>
                    <span class="mb-1">Add: {{ $leads->address }}</span>


                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Sales Person Info </h2>
                    <h6 class="sub-head text-capitalize text-dark">{{ $leads->ownername->first_name }}
                        {{ $leads->ownername->middle_name }} {{ $leads->ownername->last_name }}</h6>
                    <p class="mb-1"> M: {{ $leads->ownername->mobile }} | E: {{ $leads->ownername->email }}</p>
                    <p class="mb-1">Source : {{ $edit->source }} @if ($edit->source_o != '')
                            - {{ $edit->source_o }}
                        @endif
                    </p>
                    <p class="mb-1">Added By : {{ @$leads->createdby->first_name }}</p>
                    <p class="mb-1">Added On : {{ date('d/m/Y H:i:s', strtotime(@$leads->created_at)) }}</p>
                    @if ($edit->doc != '')
                        <?php $file = explode('|', $edit->doc); ?>
                        <span class="mb-1"> <span class="font-semibold">Attachment :</span>
                            @foreach ($file as $f)
                                <a class="btn btn-sm btn-primary"
                                    href="{{ asset('public/uploads/crm_lead_doc/') }}/{{ $f }}"
                                    target="_blank">View & Download</a>
                            @endforeach
                    @endif
                    {{-- @if ($edit->doc != '')<span class="mb-1"> <span class="font-semibold">Attachment :</span> <a
                                href="{{asset('public/uploads/crm_lead_doc/')}}/{{ $edit->doc }}" target="_blank">View &
                                Download</a></span> @endif --}}



                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 h-100 mb-3">
                <div class="p-2 card bg-2 ">
                    <div class="d-flex justify-content-between align-items-center">
                        @if ($edit->status != 0)
                            <h2 class=head>Convert This Lead to Deal?</h2>
                            <button data-toggle="modal" data-target="#leadConvertModal" class="btn btn-danger">Convert to
                                Deal</button>
                        @else
                            <h2 class=head>This Lead is Converted to Deal ID : {{ $edit->lead_deal_code->code }}</h2>
                        @endif

                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3 h-100">
                <div class="p-4 card">
                    <div>
                        <span class="font-weight-bold">Internal Note</span>
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads-comments-add', 'method' => 'POST', 'id' => 'crm-leads-comments-add']) }}
                        <textarea name="comments" class="form-control" id="" cols="10" rows="3" required></textarea>
                        <input type="file" class="form-control" name="commentsdoc" id="commentsdoc">
                        <input type="hidden" id="commentsid" name="commentsid" value="{{ $edit->id }}" />
                        <div class="mt-2 justify-content-end d-flex">
                            <button type="submit" class=" btn-small">Add Note</button>
                        </div>
                        {{ Form::close() }}
                    </div>


                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 h-100 mb-3">

            </div>
            <div class="col-lg-6 ">
                <div class="p-4 card">
                    @if ($edit->note != '')
                        <b>Lead Notes :- </b>
                        <div class="notes border-bottom mt-2"> {!! nl2br($edit->note) !!} </div>
                    @endif


                    @if (isset($comments))
                        <div class="notes border-bottom mt-2">
                            @foreach ($comments as $cmts)
                                <div>
                                    @if ($cmts->created_by == Auth::user()->id)
                                        <a href="{{ url('crm-leads-comments-delete/' . $cmts->id . '') }}"
                                            onclick="return confirm('Are you sure?')"><i
                                                class="fa fa-window-close text-sm text-danger float-right"
                                                aria-hidden="true"></i></a>
                                    @endif
                                    <p class="mb-0">{!! $cmts->comments !!}
                                        @if ($cmts->commentsdoc != '')
                                            <br /><br />
                                            <a class="text-info p-0"
                                                href="{{ asset('public/uploads/crm_lead_doc/') }}/{{ $cmts->commentsdoc }}"
                                                target="_blank">&nbsp;&nbsp;<i class="fa fa-paperclip"
                                                    aria-hidden="true"></i>&nbsp;&nbsp;View File&nbsp;&nbsp;</a>
                                        @endif
                                        <span class="text-muted text-right"> Created by:
                                            {{ $cmts->createdby->first_name }}
                                            {{ $cmts->createdby->last_name }}, On
                                            {{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}</span>
                                    </p>
                                </div>
                                <hr>
                            @endforeach
                        </div>
                    @endif

                    {{-- <div class="p-3 card mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="page-heading">Collaboration</h2>
                            <button class="btn-small">Add User</button>
                        </div>
                    </div> --}}

                </div>
            </div>


        </div>


    </div>

    <!-- lead Convert Modal-->
    <div class="modal fade" id="leadConvertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Convert?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Are you sure you want to Convert this Lead to Deal?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{ url('crm-leads/' . $leads->id . '/convert') }}">Convert</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="addlead" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg right-aligned" role="document" style="min-width:50% !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Lead</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-leads-form']) }}

                <div class="modal-body">
                    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Lead Name</label>
                                {{-- <select class="form-control js-example-basic-single" name="lead_name" id="lead_name">
                                    <option value="">Select</option>
                                    @foreach ($product as $value)
                                    <option value="{{ @$value->part_number }}" {{ isset($edit) ? (!empty($edit->lead_name) ?
                                        (@$edit->lead_name == @$value->part_number ? 'selected' : '') : '') : '' }}>{{
                                        @$value->part_number }}</option>
                                    @endforeach
                                </select> --}}
                                <input class="form-control" type="text" name="lead_name" autocomplete="off"
                                    id="lead_name" value="{{ old('lead_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Customer</label>
                                <a style="float: right; cursor: pointer;" class="text-primary" data-toggle="modal"
                                    data-target="#addcompany"><i class="fa fa-user-plus" aria-hidden="true"></i> Add
                                    Company</a>
                                <select class="form-control js-example-basic-single" name="company_name"
                                    id="company_name" required>
                                    <option value="">-Select-</option>
                                    @foreach ($vendors as $value)
                                        <option value="{{ @$value->id }}">
                                            {{ @$value->customer_name_display }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Contact Person Name</label>
                                <input class="form-control" type="text" name="cust_name" autocomplete="off"
                                    id="cust_name" value="{{ old('cust_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Designation</label>
                                <input class="form-control" type="text" name="cust_designation" autocomplete="off"
                                    id="cust_designation" value="{{ old('cust_designation') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Mobile</label>
                                <input class="form-control" type="text" name="cust_no" autocomplete="off"
                                    id="cust_no" value="{{ old('cust_no') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input class="form-control" type="text" name="cust_email" autocomplete="off"
                                    id="cust_email" value="{{ old('cust_email') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Address</label>
                                <input class="form-control" type="text" name="address" autocomplete="off"
                                    id="address" value="{{ old('address') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Brand</label>
                                <select class="form-control js-example-basic-single" name="tags[]" id="tags"
                                    multiple>
                                    @foreach ($brand as $value)
                                        <option value="{{ @$value->title }}">{{ @$value->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Sales Person</label>
                                <select class="form-control js-example-basic-single" name="owner" id="owner"
                                    required>
                                    <option value="">-Select-</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Source</label>
                                <select class="form-control" name="source" id="source">
                                    <option value="">-Select-</option>
                                    <option value="Chat">Chat
                                    </option>
                                    <option value="Call">Call
                                    </option>
                                    <option value="Mail" selected>Mail</option>
                                    <option value="Website">Website
                                    </option>
                                    {{-- <option value="Gitex 2023" @if (@$edit->source == 'Gitex 2023') selected @endif
                                        >Gitex 2023</option> --}}
                                    <option value="Gitex">Gitex
                                    </option>
                                    <option value="Ecommerce">Ecommerce
                                    </option>
                                    <option value="Other">Other
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" id="sourcediv" style="display: none;">
                            <div class="form-group">
                                <label for="">Other Source</label>
                                <input class="form-control" type="text" name="source_o" autocomplete="off"
                                    id="source_o" value="{{ old('source_o') }}" style="display: none;"
                                    placeholder="Source">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Created By</label>
                                <input class="form-control" type="text" name="createdby" autocomplete="off"
                                    id="createdby" value="{{ Auth::user()->full_name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Date</label>
                                @php
                                    $value = date('Y-m-d');

                                @endphp
                                <input class="form-control" id="date" type="date" autocomplete="off"
                                    name="date" value="{{ @$value }}" data-date-format="mm/dd/yyyy" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Lead Type</label>
                                <select class="form-control" name="isproject" id="isproject">
                                    <option value="4">Project
                                    </option>
                                    <option value="1">Reseller
                                    </option>
                                    <option value="2">Enduser
                                    </option>
                                    <option value="3">E-Commerce
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="1">New</option>
                                    <option value="2">Qualified
                                    </option>
                                    <option value="3">Unqualified
                                    </option>
                                    <option value="4">Pending
                                        Response</option>
                                           <option value="10">Closed</option>
                                </select>
                                <textarea class="form-control" name="lost_comments" rows="4" style="display: none;" autocomplete="off"
                                    id="lost_comments" placeholder="Reason"></textarea>
                                <script>
                                    $('#status').on('change', function(e) {
                                        if ($('#status').val() == 3) {
                                            $('#lost_comments').css("display", "block");
                                            $('#lost_comments').prop('required', true);
                                        } else {
                                            $('#lost_comments').css("display", "none");
                                            $('#lost_comments').prop('required', false);
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Attach</label>
                                <input type="file" class="form-control" name="doc[]" id="doc"
                                    multiple="multiple">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Notes</label>
                                <textarea class="form-control" name="note" rows="3" autocomplete="off" id="note">

    </textarea>
                            </div>
                        </div>
                        @if (session('logged_session_data.company_id') == 1)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Company</label>
                                    <select class="form-control" name="company" id="company" required>
                                        <option value="">Select</option>
                                        @foreach ($company as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="company" id="company"
                                value="{{ session('logged_session_data.company_id') }}" />
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnSubmit"><span class="ti-check"></span>

                        @lang('Save & View')
                        @lang('Lead')
                    </button>
                    <a href="{{ url('crm-leads/' . $leads->id . '/view') }}" class="btn btn-danger"><i
                            class="fa fa-times" aria-hidden="true"></i> Close</a>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <div class="modal fade" id="addcompany" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg right-aligned" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Company</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Customer Type</label>
                                <select class="form-control js-example-basic-single" id="account_type" required>
                                    <option value="">-Select-</option>
                                    <option value="1" selected>Reseller</option>
                                    <option value="2">Enduser</option>
                                    <option value="3">Ecommerce</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Company Name</label>
                                <input class="form-control text-uppercase" type="text" aria-describedby=""
                                    autocomplete="off" id="company_name_add" required>
                                <div id="company_name_add_list">
                                </div>
                                <script>
                                    $(document).ready(function() {

                                        $('#company_name_add').keyup(function() {
                                            var query = $(this).val();
                                            if (query != '') {
                                                var _token = $('input[name="_token"]').val();
                                                $.ajax({
                                                    url: "{{ route('autocomplete.customer_name') }}",
                                                    method: "POST",
                                                    data: {
                                                        query: query,
                                                        _token: _token
                                                    },
                                                    success: function(data) {
                                                        $('#company_name_add_list').fadeIn();
                                                        $('#company_name_add_list').html(data);
                                                    }
                                                });
                                            }
                                        });

                                        $(document).on('click', 'li', function() {
                                            $('#company_name_add').val($(this).text());
                                            $('#company_name_add_list').fadeOut();
                                        });

                                    });
                                </script>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Contact Person Name</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_name_add"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Designation</label>
                                <select class="form-control js-example-basic-single" name="designation_add"
                                    id="designation_add" required>
                                    <option value="">--Designation--</option>
                                    @if (count($designation) > 0)
                                        @foreach ($designation as $val)
                                            <option value="{{ $val->title }}"
                                                {{ trim(strtolower($val->title)) == 'purchase' ? 'selected' : '' }}
                                                aria-describedby="">{{ $val->title }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Mobile</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_no_add" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Email</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_email_add"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Country</label>
                                <select class="form-control js-example-basic-single" name="country_ship"
                                    id="country_ship">
                                    <option value="">-Select-</option>
                                    @foreach ($country as $value)
                                        <option value="{{ @$value->id }}"
                                            {{ trim(strtolower($value->name)) == 'united arab emirates' ? 'selected' : '' }}>
                                            {{ @$value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Payment Terms</label>
                                <select class="form-control js-example-basic-single" id="payment_terms" required>
                                    @foreach ($paymentterms as $key => $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($value->id == 3) selected @endif>{{ @$value->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Sales Person</label>
                                <select class="form-control js-example-basic-single" id="cust_sales_person" required>
                                    <option value="">-Select-</option>

                                    @foreach ($sales_person as $value)
                                        <option value="{{ $value->user_id }}">{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Address 1</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_address_add"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Address 2</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_address_add2"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">City</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_city" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">State</label>
                                <div id="sectionStateDiv_ship">
                                    <select class="form-control" name="state_ship" id="state_ship">
                                        <option data-display="" value=""></option>

                                    </select>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">PO Box</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_pobox" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" id="btn_close2" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-success" id="btn_add_company" type="button">Save & Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(window).ready(function() {
            $("#item-store-form").on("keypress", function(event) {
                var keyPressed = event.keyCode || event.which;
                if (keyPressed === 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });


        function change_status(id) {
            $("#loading_bg").css("display", "block");
            var status = $("#edit_status").val();
            var sub_status = $("#edit_sub_status").val();
            var sub_status_text = $("#edit_sub_status option:selected").text(); 
            var comments = $("#lost_comments").val();
            var lead_id = $("#commentsid").val();
            var follow_up_date = null;

            if (status == "" || status <= 0) {
                alert("Please Choose Status");
                $("#edit_status").focus();
                $("#loading_bg").css("display", "none");
                return false;
            }

            if(status == 4){
                follow_up_date = $("#follow_up_date").val();
                if (follow_up_date == "") {
                    alert("Please Choose Follow Up Date");
                    $("#follow_up_date").focus();
                    $("#loading_bg").css("display", "none");
                    return false;
                }
            }

            if(sub_status == 8 || sub_status == 12 || sub_status == 14){
                if(comments == ""){
                    alert("Please Enter Comments");
                    $("#lost_comments").focus();
                    $("#loading_bg").css("display", "none");
                    return false;
                }

            }
           
            $("#btn_edit_status").attr('disabled', true);


            var action = "{{ URL::to('crm-leads-update-status') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    status: status,
                    sub_status: sub_status,
                    comments: comments || sub_status_text,
                    lead_id: lead_id,
                    follow_up_date: follow_up_date || null,
                },
                cache: false,
                success: function(dataResult) {
         
                  

                    console.log(dataResult)
                    // var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] === "REDIRECT") {
                         window.open(dataResult['url'], '_blank'); // opens in new tab
                    } else if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        //$("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                       
                    }

                     location.reload(true);
                }
            });

        }


        $(document).on("change", "#company_name", function() {
            var id = $("#company_name").val();
            get_cust_name(id);
            get_sales_person(id);
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
                            $("#cust_name").val(name.replace('null ', '').replace('null', ''));
                            $("#cust_no").val(dataResult['data'][i].mobile);
                            $("#cust_email").val(dataResult['data'][i].email);
                            $("#address").val(address);
                            $("#cust_designation").val(dataResult['data'][i].designation);

                            //1.Reseller
                            if (dataResult['data'][i].account_type == 1) {
                                $("#isproject").val(1);
                            } //2.Enduser
                            if (dataResult['data'][i].account_type == 2) {
                                $("#isproject").val(2);
                            } //3.Ecommerce
                            if (dataResult['data'][i].account_type == 3) {
                                $("#isproject").val(3);
                            }

                        }
                    } else {
                        $("#cust_name").val();
                        $("#cust_no").val();
                        $("#cust_email").val();
                        $("#address").val();
                        $("#cust_designation").val();
                        $("#isproject").val();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        function get_sales_person(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-salesperson-list') }}";
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
                        $('#owner').find('option').not(':first').remove();
                        for (var i = 0; i < len; i++) {
                            var id = dataResult['data'][i].id;
                            var name = dataResult['data'][i].full_name;
                            var selected = (len === 1) ? "selected" : "";
                            var option = "<option value='" + id + "'" + selected + ">" + name + "</option>";
                            $("#owner").append(option);
                        }
                    } else {
                        $('#owner').find('option').not(':first').remove();
                    }
                    $("#loading_bg").css("display", "none");
                }
            });
        }

        $(document).ready(function() {
            if ($("#source").val() == "Other") {
                $("#source_o").css("display", "block");
                $("#source_o").prop('required', true);
                $("#sourcediv").css("display", "block");
            } else {
                $("#source_o").css("display", "none");
                $("#source_o").prop('required', false);
                $("#sourcediv").css("display", "none");
            }
        });

        $(document).on("change", "#source", function() {
            if ($("#source").val() == "Other") {
                $("#source_o").css("display", "block");
                $("#source_o").prop('required', true);
                $("#sourcediv").css("display", "block");
            } else {
                $("#source_o").css("display", "none");
                $("#source_o").prop('required', false);
                $("#sourcediv").css("display", "none");
            }
        });

        $(document).on("click", "#btn_add_company", function() {

            //$("#btn_add_company").css("display", "none");

            var company_name_add = $("#company_name_add").val();
            var cust_name_add = $("#cust_name_add").val();
            var designation_add = $("#designation_add").val();
            var cust_no_add = $("#cust_no_add").val();
            var cust_email_add = $("#cust_email_add").val();
            var cust_address_add = $("#cust_address_add").val();
            var cust_address_add2 = $("#cust_address_add2").val();
            var country_add = $("#country_ship").val();

            var cust_city = $("#cust_city").val();
            var state_ship = $("#state_ship").val();
            var cust_pobox = $("#cust_pobox").val();
            var sales_person = $("#cust_sales_person").val();
            var payment_terms = $("#payment_terms").val();
            var account_type = $("#account_type").val();
            var company_id = $("#company").val();

            var action = "{{ URL::to('add-customer-detail-popup') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    company_name_add: company_name_add,
                    cust_name_add: cust_name_add,
                    designation_add: designation_add,
                    cust_no_add: cust_no_add,
                    cust_email_add: cust_email_add,
                    cust_address_add: cust_address_add,
                    cust_address_add2: cust_address_add2,
                    vat_country: country_add,
                    city: cust_city,
                    vat_state: state_ship,
                    zip_code: cust_pobox,
                    sales_person: sales_person,
                    payment_terms: payment_terms,
                    account_type: account_type,
                    company_id: company_id,
                },
                cache: false,
                success: function(dataResult) {
                    //alert(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                        $("#btn_add_company").css("display", "block");
                    } else if (dataResult['data'] == "ERROR2") {
                        alert("Company Name already exists!! Please Contact Support");
                        $('#company_name_add').css("border", "1px solid red");
                        $('#company_name_add').focus();
                        $("#btn_add_company").css("display", "block");
                    } else {
                        if (dataResult['data'] != null) {
                            len = dataResult['data'].length;
                        }
                        if (len > 0) {
                            $('#company_name').find('option').not(':first').remove();
                            var newCompanyId = dataResult['new_company_id'];
                            for (var i = 0; i < len; i++) {
                                var id = dataResult['data'][i].id;
                                var name = dataResult['data'][i].customer_name_display;
                                var name2 = dataResult['data'][i].code;
                                var option = "<option value='" + id + "'>" + name + "</option>";
                                $("#company_name").append(option);

                            }

                            if (newCompanyId) {
                                $("#company_name").val(newCompanyId).trigger('change');
                            }
                            alert('Company Name Added Successfully!!');
                            $('#btn_close2').click();
                            $("#btn_add_company").css("display", "block");
                            //location.reload();
                            //$("#company_name").change();
                        }
                    }
                }
            });
        });
        $(document).ready(function() {
            // Trigger change event only if a country is selected by default
            if ($('#country_ship').val() !== '') {
                $('#country_ship').trigger('change');
            }



            const subStatusOptions = {
                1: [ // New
                    {
                        value: '1',
                        text: 'Just received, uncontacted'
                    }
                ],
                2: [ // Qualified
                    {
                        value: '2',
                        text: 'Sent to Sales'
                    }
                ],
                3: [ // Unqualified
                    {
                        value: '3',
                        text: 'Budget Issue'
                    },
                    {
                        value: '4',
                        text: 'Not Interested'
                    }, ,
                    {
                        value: '5',
                        text: 'Wrong Contact'
                    }, ,
                    {
                        value: '6',
                        text: 'Timeline not matching'
                    },
                    {
                        value: '7',
                        text: 'Product/Service mismatch'
                    },
                    {
                        value: '8',
                        text: 'Other'
                    },
                ],
                4: [ // Pending Response
                    {
                        value: '9',
                        text: 'Waiting for EUD'
                    }, {
                        value: '10',
                        text: 'Waiting for Vendor Price'
                    }, , {
                        value: '11',
                        text: 'Quoted - Waiting for Response'
                    }, {
                        value: '12',
                        text: 'Other'
                    },
                ],
                10: [ // Closed
                    {
                        value: '13',
                        text: 'No Response'
                    },
                    {
                        value: '14',
                        text: 'Other'
                    }
                ]
            };

            function updateSubStatusOptions(status,selectedSubStatus = '') {
                const $subStatus = $('#edit_sub_status');
                // const $lostComments = $('#lost_comments');

                // Show/hide lost_comments and toggle required
                // if (['1', '2', '3', '4', '5'].includes(status)) {
                //     $lostComments.show();
                //     $lostComments.find('textarea').prop('required', true);
                // } else {
                //     $lostComments.hide();
                //     $lostComments.find('textarea').prop('required', false);
                // }

                // Clear current sub-status options

                
                $subStatus.empty();

                if (subStatusOptions.hasOwnProperty(status)) {
                    $subStatus.show();
                    // $subStatus.append('<option value="">-Select-</option>');
                    subStatusOptions[status].forEach(function(opt) {
                        const selected = (opt.value === selectedSubStatus) ? 'selected' : '';
                        $subStatus.append(`<option value="${opt.value}" ${selected}>${opt.text}</option>`);
                    });
                    $subStatus.prop('required', true);
                } else {
                    $subStatus.hide().prop('required', false);
                }

                toggleLostComments($('#edit_sub_status').val());
            }

            function toggleFollowUpDate(status) {
                const $followUpDate = $('#follow_up_date');
                const $followUpLabel = $('#follow_up_date_label');
           
                if (status === '4') { // Assuming 4 is the value for "Pending Response"
                    $followUpDate.show().prop('required', true);
                    $followUpLabel.show();
                   
                } else {
                    $followUpDate.hide().prop('required', false);
                    $followUpLabel.hide();
                }
            }

            function toggleLostComments(subStatusValue) {
                const $lostComments = $('#lost_comments');
                if (subStatusValue === '8' || subStatusValue === '12' || subStatusValue === '14') { // Assuming 8 and 14 are the values for "Other" in Unqualified and Closed
                    $lostComments.show();
                    $lostComments.find('textarea').prop('required', true);
                } else {
                    $lostComments.hide();
                    $lostComments.find('textarea').prop('required', false);
                }
            }

            const initialSubStatus = "{{ $leads->sub_status ?? '' }}";

            updateSubStatusOptions($('#edit_status').val(), initialSubStatus);
            toggleFollowUpDate(($('#edit_status').val()));

            $('#edit_status').on('change', function() {
                updateSubStatusOptions($(this).val());
                toggleFollowUpDate($(this).val());
            });

            $('#edit_sub_status').on('change', function() {
                toggleLostComments($(this).val());
            });





            // if ($('#edit_status').val() == 3 || $('#edit_status').val() == 1 || $('#edit_status').val() == 2 || $(
            //         '#edit_status').val() == 4) {
            //     $('#lost_comments').css("display", "block");
            //     $('#lost_comments').prop('required', true);
            // } else {
            //     $('#lost_comments').css("display", "none");
            //     $('#lost_comments').prop('required', false);
            // }



            // Initialize sub-status on page load with current status value



        });
    </script>
@endsection

<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>
<?php try { ?>

    <style>
            #leads-details label {
                font-weight: 600 !important;
                background-color: #deebe1 !important;
                margin-bottom: 3px !important;
                text-align: center !important;
                color: #212529 !important;
            }

            #leads-details .form-control-plaintext {
                text-align: center !important;
            }
        </style>

<div class="purchase-order-content-header sticky-top d-flex justify-content-between align-items-center" style="background-color: #f7f8fd">
    <div class="d-flex align-items-center">
        <h4 class="purchase-order-content-header-left mb-0">
            {{ $leads->code }}
        </h4>

      
        <div class="pipeline-wrapper ms-2">
            <div class="pipeline-arrow {{ $leads->status_info['color'] ?? 'secondary' }}">{{ $leads->status_info['label'] ?? 'Unknown' }}</div>

            @if(optional($edit->lead_deal_code)->code)
            
                <div class="pipeline-arrow sky">
                    <a class="text-white" style="color: inherit; text-decoration: none;" href="{{ url('crm-deals/show/' . $edit->deal_id) }}">
                        {{ $edit->lead_deal_code->code }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="purchase-order-content-header-right d-flex align-items-center">



        <form method="GET" action="{{ url('crm-leads/show', @$edit->id) }}">
            {{-- <input hidden type="text" value="{{@$po->id}}" name="id"> --}}
            <button type="submit" name="lead_action" value="edit" class="btn btn-light">
                <i class="ico icon-outline-pen-2 text-success"></i> Edit
            </button>
        </form>

        <form method="GET" action="{{ url('crm-leads/show') }}">
            <button type="submit" name="lead_action" value="add" class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>
        </form>






        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">



            </ul>
        </div>


    </div>
</div>

<div class="card mb-3">
    <div class="card-body pb-0">
        <!-- <h4 class="mb-3 color-sub-head">Supplier Info (SUPS-1060)</h4> -->

        <div class="row">
            <div class="col-2">
                <label class="form-label">Customer Name:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ $leads->customername->customer_name_display }}
                </div>
            </div>

            <div class="col-2">
                <label class="form-label">Lead Name:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ $leads->lead_name }}
                </div>
            </div>

            @if ($edit->tags != '')
                <div class="col-2">
                    <label class="form-label">Brand:</label>
                    <div class="form-control-plaintext truncate-text-custom">
                        <?php $myArray = explode(',', $edit->tags); ?>
                        {{ implode(', ', $myArray) }}
                    </div>
                </div>
            @endif


            <div class="col-2">
                <label class="form-label">Sales Person:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ $leads->ownername->first_name }} {{ $leads->ownername->middle_name }}
                    {{ $leads->ownername->last_name }}
                </div>
            </div>



            <div class="col-2">
                <label class="form-label">Company:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ $leads->company->company_name }}
                </div>
            </div>


            <div style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#updateLeadStatus" class="col-2 p-0">
                <label style="cursor:pointer" class="form-label">Status: <i
                        class="ico icon-outline-pen-new-square text-danger"></i> </label>
                <div class="form-control-plaintext truncate-text-custom">
                    <p class="badge mb-1 bg-{{ $leads->status_info['color'] }} ">
                        {{ $leads->status_info['label'] }}
                    </p>

                    @if ($leads->sub_status)
                        <p class="badge {{ $leads->sub_status_color }}">
                            {{ $leads->sub_status_text }}</p>
                    @endif


                </div>
            </div>

        </div>



    </div>
</div>

<div class="card mb-3">
    <div class="card-body pb-0">
        <!-- <h4 class="mb-3 color-sub-head">Supplier Info (SUPS-1060)</h4> -->
        {{-- <div class="d-flex align-items-center mb-3">
            <div class="font-weight-600 title-15  text-success"> {{ $leads->lead_name }} {!! App\SysHelper::lead_type_new($edit->isproject) !!}
            </div>
        </div> --}}
        <div class="row">








            <div class="col-2 mb-2">
                <label class="form-label">Contact Person:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ $leads->cust_name }}
                </div>
            </div>

            @if ($edit->cust_designation != '')
                <div class="col-2 mb-2">
                    <label class="form-label">Designation:</label>
                    <div class="form-control-plaintext truncate-text-custom">
                        {{ $edit->cust_designation }}
                    </div>
                </div>
            @endif

            <div class="col-2 mb-2">
                <label class="form-label">Mobile:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ $leads->cust_no }}
                </div>
            </div>

            <div class="col-2 mb-2">
                <label class="form-label">Email:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ $leads->cust_email }}
                </div>
            </div>

            <div class="col-2 mb-2">
                <label class="form-label">Address:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ $leads->address }}
                </div>
            </div>





            <div class="col-2 mb-2">
                <label class="form-label">Sales Person Mobile:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ $leads->ownername->mobile }}
                </div>
            </div>

            <div class="col-2 mb-2">
                <label class="form-label">Sales Person Email:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ $leads->ownername->email }}
                </div>
            </div>

            <div class="col-2 mb-2 d-flex justify-content-between">
                <div style="width:48%">
                    <label class="form-label">Source:</label>
                    <div class="form-control-plaintext truncate-text-custom">
                        {{ $edit->source }} @if ($edit->source_o != '')
                            - {{ $edit->source_o }}
                        @endif
                    </div>
                </div>


                @php
                    $leadTypes = [
                        1 => 'Reseller',
                        2 => 'Enduser',
                        3 => 'E-Commerce',
                        4 => 'Project',
                    ];
                @endphp

                <div style="width:52%">
                    <label class="form-label">Lead Type:</label>
                    <div class="form-control-plaintext truncate-text-custom">
                        {{ $leadTypes[$edit->isproject] ?? 'N/A' }}
                    </div>
                </div>




            </div>






            <div class="col-2 mb-2">
                <label class="form-label">Added By:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ @$leads->createdby->full_name }}
                </div>
            </div>


            <div class="col-2 mb-2">
                <label class="form-label">Added On:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ date('d/m/Y h:i A', strtotime(@$leads->created_at)) }}
                </div>
            </div>

            <div class="col-2 mb-2">
                <label class="form-label">Updated On:</label>
                <div class="form-control-plaintext truncate-text-custom">
                    {{ date('d/m/Y h:i A', strtotime(@$leads->updated_at)) }}
                </div>
            </div>



            @if (optional($edit->lead_deal_code)->code)
                <div style="cursor:pointer" class="col-2 mb-2">
                    <label style="cursor:pointer" class="form-label">Deal ID: </label>
                    <div class="form-control-plaintext truncate-text-custom">

                        <a class="fw-semibold" href="{{ url('crm-deals/show/' . $edit->deal_id) }}">
                            {{ $edit->lead_deal_code->code }}
                        </a>

                    </div>
                </div>
            @endif





            @if ($leads->doc != '')
                <div class="col-2 mb-2">
                    <label class="form-label">Attachment:</label>
                    <div class="form-control-plaintext truncate-text-custom">

                        <a class="btn-sm text-dark btn-light" data-bs-popover="popover" data-bs-trigger="hover"
                            data-bs-delay="500" data-bs-content="{{ $leads->doc }}" data-bs-placement="top"
                            href="{{ asset('public/uploads/crm_lead_doc/') }}/{{ $leads->doc }}"
                            target="_blank"><i
                                class="ico icon-bold-download-minimalistic text-success fw-bold title-15"></i>
                            Download</a>

                    </div>
                </div>
            @endif



        </div>



    </div>
</div>

<div class="tab-wrap mb-3" id="internal-note">
    <ul class="nav nav-tabs" id="dealTrackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="internal-notes-tab" data-bs-toggle="tab"
                data-bs-target="#internal-notes" type="button" role="tab" aria-controls="internal-notes"
                aria-selected="true">Internal Notes</button>
        </li>

    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active" id="internal-notes" role="tabpanel"
            aria-labelledby="internal-notes-tab">
            <!-- <h4 class="mb-3 color-sub-head">Shipping Address</h4> -->
            <div class="row">


                <div class="col-7">
                    <div class="">
                        @if ($edit->note != '')
                            <b>Lead Notes :- </b>

                            <div class="mt-2" style="">


                                <div class="card border-0 rounded-3 mb-2">
                                    <div class="card-body py-0">



                                        <!-- Top Row: Right-Aligned Icons -->
                                        <div class="d-flex justify-content-between mb-0">


                                            <!-- Comment -->
                                            <p class="mb-0 text-break fw-semibold" style="font-size:11px">
                                                {!! nl2br($edit->note) !!}

                                            </p>


                                            <div class="d-flex align-items-baseline gap-2">
                                                @if ($edit->doc)
                                                    <a href="{{ asset('public/uploads/crm_lead_doc/' . $edit->doc) }}"
                                                        target="_blank" class="btn btn-sm btn-light me-1"
                                                        style="min-height:17px">
                                                        <i class="ico icon-bold-paperclip" style="font-size:11px"></i>
                                                    </a>
                                                @endif

                                            </div>

                                        </div>

                                        <!-- Username + Date + Deleted At (Right-Aligned Below Icons) -->
                                        <div class="text-end small text-muted">

                                            <span style="font-size:10px">

                                                {{ $edit->createdby->first_name }} {{ $edit->createdby->last_name }}
                                            </span>

                                            <span>•</span>

                                            <span style="font-size:10px">
                                                <i class="ico icon-bold-clock me-1"></i>
                                                {{ date('d/m/Y h:i A', strtotime($edit->created_at)) }}
                                            </span>

                                            @if ($edit->deleted_at)
                                                <span>•</span>

                                                <span class="text-danger" style="font-size:10px">
                                                    Deleted: {{ date('d/m/Y h:i A', strtotime($edit->deleted_at)) }}
                                                </span>
                                            @endif

                                        </div>

                                    </div>
                                </div>



                            </div>


                        @endif


                        @if (isset($comments))
                            <div class="mt-2" style="">
                                @foreach ($comments as $cmts)
                                    <div class="card border-0 rounded-3 mb-2">
                                        <div class="card-body py-0">



                                            <!-- Top Row: Right-Aligned Icons -->
                                            <div class="d-flex justify-content-between mb-0">


                                                <!-- Comment -->
                                                <p class="mb-0 fw-semibold text-break @if ($cmts->deleted_at) text-decoration-line-through text-muted @endif"
                                                    style="font-size:11px">
                                                    {!! nl2br($cmts->comments) !!}
                                                </p>


                                                <div class="d-flex align-items-baseline gap-2">
                                                    @if ($cmts->commentsdoc)
                                                        <a href="{{ asset('public/uploads/crm_lead_doc/' . $cmts->commentsdoc) }}"
                                                            target="_blank" class="btn btn-sm btn-light me-1"
                                                            style="min-height:17px">
                                                            <i class="ico icon-bold-paperclip"
                                                                style="font-size:11px"></i>
                                                        </a>
                                                    @endif

                                                    @if ($cmts->created_by == Auth::user()->id)
                                                        @if ($cmts->deleted_at)
                                                            <a href="{{ url('crm-leads-comments-restore/' . $cmts->id) }}"
                                                                onclick="return confirm('Are you sure you want to restore this comment?')"
                                                                class="btn btn-sm btn-light" style="min-height:17px">
                                                                <i class="ico icon-bold-restart"
                                                                    style="font-size:11px"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{ url('crm-leads-comments-delete/' . $cmts->id) }}"
                                                                onclick="return confirm('Are you sure you want to delete this comment?')"
                                                                class="btn btn-sm btn-light" style="min-height:17px">
                                                                <i class="ico icon-outline-trash-bin-minimalistic"
                                                                    style="font-size:11px"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>




                                            </div>

                                            <!-- Username + Date + Deleted At (Right-Aligned Below Icons) -->
                                            <div class="text-end small text-muted">

                                                <span style="font-size:10px">

                                                    {{ $cmts->createdby->first_name }}
                                                    {{ $cmts->createdby->last_name }}
                                                </span>

                                                <span>•</span>

                                                <span style="font-size:10px">
                                                    <i class="ico icon-bold-clock me-1"></i>
                                                    {{ date('d/m/Y h:i A', strtotime($cmts->created_at)) }}
                                                </span>

                                                @if ($cmts->deleted_at)
                                                    <span>•</span>

                                                    <span class="text-danger" style="font-size:10px">
                                                        Deleted:
                                                        {{ date('d/m/Y h:i A', strtotime($cmts->deleted_at)) }}
                                                    </span>
                                                @endif

                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        @endif



                    </div>
                </div>

                <div class="col-5">
                    <label class="font-weight-bold form-label">Internal Note</label>
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads-comments-add', 'method' => 'POST', 'id' => 'crm-leads-comments-add']) }}
                    <textarea name="comments" class="form-control capitalize-title" id="" cols="10" rows="3" required></textarea>

                    <input type="hidden" id="commentsid" name="commentsid" value="{{ $edit->id }}" />

                    <div class="row mt-2">

                        <div class="col-md-4 d-flex justify-content-start align-items-center">
                            <button type="submit" class="btn btn-light   d-flex align-items-center gap-2">
                                <i class="ico icon-outline-add-square fs-5 text-success "></i>
                                <span>Add Note</span>
                            </button>
                        </div>
                        <div class="col-md-8">
                            <input type="file" class="form-control" name="commentsdoc" id="commentsdoc">
                        </div>


                    </div>



                    {{ Form::close() }}
                </div>



            </div>


        </div>

    </div>
</div>


<div class="modal side-panel fade" id="updateLeadStatus" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Update Status</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">
                        <div class="row" id="div_update">

                            <div class="col-12">
                                <label class="form-label" for="" id="">Status</label>
                                <select class="dynamicstxt  form-control" name="edit_status" id="edit_status"
                                    required>
                                    <option value="">-Select-</option>
                                    <option value="1" {{ $leads->status == 1 ? 'selected' : '' }}>New</option>
                                    <option value="2"
                                        {{ $leads->status == 2 || $leads->status == 0 ? 'selected' : '' }}>
                                        Qualified</option>
                                    <option value="3" {{ $leads->status == 3 ? 'selected' : '' }}>Unqualified
                                    </option>
                                    <option value="4" {{ $leads->status == 4 ? 'selected' : '' }}>Pending
                                        Response
                                    </option>
                                    <option value="10" {{ $leads->status == 10 ? 'selected' : '' }}>Closed
                                    </option>
                                </select>
                            </div>
                            <div class="col-12 mt-2">
                                <label class="form-label" for="" id="">Sub Status</label>
                                <select class="dynamicstxt form-control" name="edit_sub_status" id="edit_sub_status"
                                    style="display:none;" required>
                                    <option value="">-Select-</option>

                                </select>
                            </div>

                            <div class="col-12 mt-2">
                                <label class="form-label" for="" style="display: none;"
                                    id="qualified_deal_no_label">Deal ID</label>
                                <input class=" form-control" type="text" name="qualified_deal_no"
                                    id="qualified_deal_no" value="{{ optional($edit->lead_deal_code)->code }}"
                                    style="display: none;" />

                            </div>

                            <div class="col-12 mt-2">
                                <label class="form-label" for="" style="display: none;"
                                    id="follow_up_date_label">Next Follow Up</label>
                                <input class="form-control date-picker" type="text" name="follow_up_date"
                                    id="follow_up_date"
                                    value="{{ @App\SysHelper::normalizeToDmy($leads->follow_up_date ?? date('Y-m-d', strtotime('+2 days'))) }}"
                                    style="display: none;" />

                            </div>








                        </div>

                        <textarea class="form-control mt-2" style="height:100px" name="lost_comments" rows="4" style="display: none;"
                            autocomplete="off" id="lost_comments" required placeholder="Reason (Max. 5 Words)">{{ $leads->status == 4 ? $leads->sub_status_comment : '' }}</textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_edit_status" onclick="change_status({{ $edit->id }})"
                    class="btn btn-light add-btn ms-2">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal side-panel fade" id="leadConvertModal" data-bs-backdrop="false" tabindex="-1"
    aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="poexcelimport">Ready to Convert?</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0">
                    <div class="card-body">

                        Are you sure you want to Convert this Lead to Deal?
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a target="_blank" href="{{ url('crm-leads/' . $leads->id . '/convert') }}"
                    class="btn btn-light add-btn ms-2">
                    <i class="ico icon-bold-square-arrow-right  text-success"></i> Convert
                </a>
            </div>
        </div>
    </div>
</div>


<script>
    function change_status(id) {
        $("#loading_bg").css("display", "block");
        var status = $("#edit_status").val();
        var sub_status = $("#edit_sub_status").val();
        var sub_status_text = $("#edit_sub_status option:selected").text();
        var comments = $("#lost_comments").val();
        var lead_id = $("#commentsid").val();
        var follow_up_date = null;
        var qualified_deal_no = null;

        if (status == "" || status <= 0) {
            alert("Please Choose Status");
            $("#edit_status").focus();
            $("#loading_bg").css("display", "none");
            return false;
        }

        if (status == 2) {

            qualified_deal_no = $("#qualified_deal_no").val();
            if (qualified_deal_no == "") {
                $qualified_deal_no = null;
            }
        }

        if (status == 4) {
            follow_up_date = $("#follow_up_date").val();
            if (follow_up_date == "") {
                alert("Please Choose Follow Up Date");
                $("#follow_up_date").focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
        }

        if (sub_status == 8 || sub_status == 12 || sub_status == 14) {
            if (comments == "") {
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
                qualified_deal_no: qualified_deal_no || null,
            },
            cache: false,
            success: function(dataResult) {



                console.log(dataResult)

                if (dataResult.data === "REDIRECT") {
                    localStorage.setItem("active-dealedit-tab", "#extra-fields");
                    window.__redirecting = true;
                    window.location.href = dataResult.url.original.url;
                    return;
                }

                if (dataResult['data'] === "ERROR") {
                    alert("Error found in something!!");
                }


                if (dataResult['data'] === "QC") {

                    let message =
                        "Lead status cannot be set to 'New' as a quotation already exists for this deal.";
                    toastr.error(message);
                }




            },
            error: function(xhr, status, error) {


                console.error("AJAX Error:", status, error);
                alert("Something went wrong! Please try again.");
            },
            complete: function() {
                $("#loading_bg").hide();

                if (!window.__redirecting) {
                    location.reload();
                }
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

        function updateSubStatusOptions(status, selectedSubStatus = '') {
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

        function toggleQualifiedDeal(status) {
            const $qualified_deal_no = $('#qualified_deal_no');
            const $qualified_deal_no_label = $('#qualified_deal_no_label');

            if (status === '2') { // Assuming 4 is the value for "Pending Response"
                $qualified_deal_no.show();
                $qualified_deal_no_label.show();

            } else {
                $qualified_deal_no.hide();
                $qualified_deal_no_label.hide();
            }
        }

        function toggleLostComments(subStatusValue) {
            const $lostComments = $('#lost_comments');
            if (subStatusValue === '8' || subStatusValue === '12' || subStatusValue ===
                '14') { // Assuming 8 and 14 are the values for "Other" in Unqualified and Closed
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
        toggleQualifiedDeal(($('#edit_status').val()));

        $('#edit_status').on('change', function() {
            updateSubStatusOptions($(this).val());
            toggleFollowUpDate($(this).val());
            toggleQualifiedDeal($(this).val());
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


<script>
    $(document).ready(function() {

        // Initialize Bootstrap popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-popover="popover"]'));
        var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                delay: {
                    show: 500,
                    hide: 100
                }
            });
        });
    });
</script>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

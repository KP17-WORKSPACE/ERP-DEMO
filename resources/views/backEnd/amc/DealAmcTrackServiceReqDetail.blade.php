<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>
<style>
    .fixed-info-table {
        table-layout: fixed;
        /* Fix column widths */
        width: 100%;
    }

    .fixed-info-table th {
        width: 35%;
        /* Always 30% for label */
        white-space: nowrap;
        /* Prevent wrapping */
        text-align: left;
        font-weight: 500;
    }

    .fixed-info-table td {
        width: 65%;
        /* Always 70% for value */
        word-break: break-word;
        /* Wrap long text if needed */
    }
</style>
<?php try { ?>

<div class="purchase-order-content-header">
    <h4 class="purchase-order-content-header-left">
        {{ $psdata->doc_number }}
    </h4>
    <div class="purchase-order-content-header-right">




        <button type="submit" onclick="edit_service_request_ps({{ $psdata->id }})" class="btn btn-light">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </button>



        <button type="button" data-bs-target="#ModalProfessionalServicesRequest" data-bs-toggle="modal"
            class="btn btn-light">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </button>


        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">

                <li><a class="dropdown-item"  href="{{ url('crm-ps-track-service-list') }}">

                        Project List</a></li>
            </ul>
        </div>


    </div>
</div>



<div class="card mb-3">
    <div class="card-body">
        <!-- <h4 class="mb-3 color-sub-head">Supplier Info (SUPS-1060)</h4> -->
        <div class="d-flex align-items-center mb-3">
            <div class="font-weight-600 title-15 me-3"> {{ @$psdata->custname->name }}
            </div>
            @if (@$psdata->is_delete != 0)
                <span class="badge bg-danger">Inactive</span>
            @else
                <span class="badge bg-success">Active</span>
            @endif

        </div>
        <div class="row">
            <div class="col-1-5 mb-3">
                <label class="form-label">Deal ID:</label>
                <div class="form-control-plaintext ">        <a
                            href="{{ url('crm-deals/show/' . @$psdata->deal_id) }}">{{ App\SysHelper::get_code_from_dealid_without_company($psdata->deal_id) }}</a>
                </div>
            </div>

            <div class="col-1-5 mb-3">
                <label class="form-label">Date:</label>
                <div class="form-control-plaintext"> {{ date('d/m/Y', strtotime($psdata->date)) }}
                </div>
            </div>


            <div class="col-2 mb-3">
                <label class="form-label">Sales Person:</label>
                <div class="form-control-plaintext"> {{ $psdata->ownername->full_name }}
                </div>
            </div>

            <?php
            $st = array_map('intval', explode(',', $psdata->engineer));
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

            <div class="col-1-5 mb-3">
                <label class="form-label">Engineer:</label>
                <div class="form-control-plaintext truncate-text-custom"> {{ $engineername }}
                </div>
            </div>



            <div class="col-2 mb-3">
                <label class="form-label">Contact Person:</label>
                <div class="form-control-plaintext"> {{ $psdata->contact_person }}
                </div>
            </div>

            <div class="col-1-5 mb-3">
                <label class="form-label">Mobile No:</label>
                <div class="form-control-plaintext"> {{ str_replace(' ', '', $psdata->mobile) }}
                </div>
            </div>

            <div class="col-1-5 mb-3">
                <label class="form-label">Attachment:</label>
                <div class="form-control-plaintext"> <a target="_blank" class="btn-sm btn-light"
                        href="{{ asset('public/uploads/crm_amc_doc/') }}/{{ @$psdata->attachment }}"><i
                            class="ico icon-bold-download-minimalistic text-success fw-bold title-15"></i> Download</a>
                </div>
            </div>

            <div class="col-12 ">
                <label class="form-label">Location of Work:</label>
                <div class="form-control-plaintext truncate-text-custom"> {{ $psdata->location_of_work }}
                </div>
            </div>

        </div>
    </div>
</div>

<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="dealTrackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="service-request-info-tab" data-bs-toggle="tab"
                data-bs-target="#service-request-info" type="button" role="tab"
                aria-controls="service-request-info" aria-selected="true">Scope of Work</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link " id="service-details-info-tab" data-bs-toggle="tab"
                data-bs-target="#service-details-info" type="button" role="tab"
                aria-controls="service-details-info" aria-selected="true">Service Details</button>
        </li>


    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active" id="service-request-info" role="tabpanel"
            aria-labelledby="service-request-info-tab">

            <div class="row">
                <div class="col-6">

                    @if (count($service_request_work) > 0)
                        @php $i=1; @endphp
                        <table id="long-list" class="detail-item-table-noborder">

                            @foreach ($service_request_work as $w)
                                <tr>
                                    <td class="text-start">Task {{ $i }}</td>
                                    <td class="text-start">
                                        :&nbsp;&nbsp;{{ $w->work }}
                                    </td>
                                </tr>
                                @php $i++; @endphp
                            @endforeach
                        </table>
                    @endif
                </div>
            </div>




        </div>

        <div class="tab-pane fade" id="service-details-info" role="tabpanel" aria-labelledby="service-details-info-tab">

            {{-- <div class="row">
                <div class="col-6">



                    @if (count($service_request) > 0)
                        @foreach ($service_request as $sr)
                            Service Date: {{ date('d/M/Y', strtotime($sr->work_date)) }}<br />
                            Time: {{ date('h:i A', strtotime($sr->work_time_from)) }} to
                            {{ date('h:i A', strtotime($sr->work_time_to)) }}<br />
                            Comments: {{ $sr->comments }}<br />
                            Engineer: {{ $sr->engineerid->full_name }}
                            <hr />
                        @endforeach
                    @endif


                </div>
            </div> --}}

            {{-- <table id="long-list" class="detail-item-table-noborder" width="100%">
                @if (count($service_request) > 0)
                    @php $i = 1; @endphp
                    @foreach ($service_request as $sr)
                        <tr>
                            <td class="text-start fw-semibold" style="width: 10%;">Service #{{ $i }}</td>
                            <td class="text-start">:</td>
                            <td class="text-start fw-semibold text-primary">
                                {{ date('d/M/Y', strtotime($sr->work_date)) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-start">Time</td>
                            <td class="text-start">:</td>
                            <td class="text-start">
                                {{ date('h:i A', strtotime($sr->work_time_from)) }}
                                to {{ date('h:i A', strtotime($sr->work_time_to)) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-start">Engineer</td>
                            <td class="text-start">:</td>
                            <td class="text-start">{{ $sr->engineerid->full_name }}</td>
                        </tr>
                        @if (!empty($sr->comments))
                            <tr>
                                <td class="text-start">Comments</td>
                                <td class="text-start">:</td>
                                <td class="text-start">{{ $sr->comments }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="3">
                                <hr class="my-2">
                            </td>
                        </tr>
                        @php $i++; @endphp
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="text-muted fst-italic">No service requests found.</td>
                    </tr>
                @endif
            </table> --}}



            <div class="row g-3">
                @if (count($service_request) > 0)
                    @php $i = 1; @endphp
                    @foreach ($service_request as $sr)
                        <div class="col-md-3 col-sm-6">
                            <div class="border rounded-3 shadow-sm p-2 h-100 bg-white">
                                <table id="long-list" class="detail-item-table-noborder mb-0" style="table-layout: fixed;width:100%" >
                                    <tr>
                                        <td class="text-start fw-semibold" style="width: 30%;">Service
                                            #{{ $i }}</td>
                                        
                                        <td class="text-start font-weight-600" tyle="width: 90%;">
                                           :&nbsp;&nbsp;{{ date('d/m/Y', strtotime($sr->work_date)) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Time</td>
                                      
                                        <td class="text-start">:&nbsp;&nbsp;{{ date('h:i A', strtotime($sr->work_time_from)) }}
                                            – {{ date('h:i A', strtotime($sr->work_time_to)) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Engineer</td>
                        
                                        <td class="text-start">:&nbsp;&nbsp;{{ $sr->engineerid->full_name }}</td>
                                    </tr>
                                    @if (!empty($sr->comments))
                                        <tr>
                                            <td class="text-start">Comments</td>
                                          
                                            <td class="text-start">:&nbsp;&nbsp;{{ $sr->comments }} Lorem, ipsum dolor sit amet consectetur adipisicing elit. Recusandae asperiores libero totam tenetur temporibus dolores nemo nisi quia veritatis. Dicta pariatur fugiat, ipsa magni asperiores cum repellendus eius distinctio harum. </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        @php $i++; @endphp
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-light border text-muted fst-italic">
                            No service details found.
                        </div>
                    </div>
                @endif
            </div>





        </div>

    </div>
</div>




<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

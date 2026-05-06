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


        <button type="button" onclick="add_professional_services_request({{ $psdata->id }})" class="btn btn-light">
            <i class="ico icon-outline-add-square text-success"></i> Request
        </button>



        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">

                <li> <a href="{{ url('crm-ps-service-list-req') }}" class="dropdown-item d-flex align-items-center "><i
                            class="ico icon-outline-document-text text-success title-15 me-2"></i> Request List</a></li>

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

            @if ($psdata->deal_id != '')
                <div class="col-1 mb-3">
                    <label class="form-label">Deal ID:</label>
                    <div class="form-control-plaintext">
                        <a
                            href="{{ url('crm-deals/show/' . @$psdata->deal_id) }}">{{ App\SysHelper::get_code_from_dealid_without_company($psdata->deal_id) }}</a>

                    </div>
                </div>
            @endif

            <div class="col-1-5 mb-3">
                <label class="form-label">Date:</label>
                <div class="form-control-plaintext"> {{ date('d/m/Y', strtotime($psdata->date)) }}
                </div>
            </div>


            <div class="col-2 mb-3">
                <label class="form-label">Sales Person:</label>
                <div class="form-control-plaintext truncate-text-custom"> {{ @$psdata->ownername->full_name }}
                </div>
            </div>




            <div class="col-2 mb-3">
                <label class="form-label">Contact Person:</label>
                <div class="form-control-plaintext truncate-text-custom"> {{ $psdata->contact_person }}
                </div>
            </div>

            <div class="col-1-5 mb-3">
                <label class="form-label">Contact Number:</label>
                <div class="form-control-plaintext"> {{ str_replace(' ', '', $psdata->mobile) }}
                </div>
            </div>



            <div class="col-1 mb-3">
                <label class="form-label">Amount:</label>
                <div class="form-control-plaintext"> {{ number_format($psdata->amount, 2) }}

                </div>
            </div>

            <div class="col-3">
                <label class="form-label">Location of Work:</label>
                <div class="form-control-plaintext truncate-text-custom"> {{ $psdata->location_of_work }}
                </div>
            </div>

            <div class="col-3">
                <label class="form-label">Project In charge :</label>
                <div class="form-control-plaintext truncate-text-custom">
                    <select class="form-control" id="project_in_charge" name="project_in_charge">
                        <option value="">--Select--</option>
                        @if(count($service_person)>0)
                        @foreach($service_person as $sp)
                        <option value="{{ $sp->user_id }}">{{ $sp->full_name }}</option>
                        @endforeach
                        @endif

                    </select>
                </div>
            </div>

            <div class="col-7">
                <label class="form-label">Description:</label>
                <div class="form-control-plaintext truncate-text-custom"> {{ $psdata->deal_description }}
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
                aria-controls="service-request-info" aria-selected="true">Service Details</button>
        </li>

    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active" id="service-request-info" role="tabpanel"
            aria-labelledby="service-request-info-tab">


            <div class="row">

                @if (count($service_request) > 0)

                    @foreach ($service_request as $sr)
                        <div class="col-6">
                            <table id="long-list" class="detail-item-table-noborder">
                                <thead>

                                    <tr>
                                        <td class="text-start">Service Date</td>
                                        <td class="text-start">
                                            :&nbsp;&nbsp;{{ date('d/m/Y', strtotime($sr->work_date)) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Service Time</td>
                                        <td class="text-start">
                                            :&nbsp;&nbsp;{{ date('h:i A', strtotime($sr->work_time_from)) }} to
                                            {{ date('h:i A', strtotime($sr->work_time_to)) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Comments</td>
                                        <td class="text-start">:&nbsp;&nbsp;{{ $sr->comments }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Engineer</td>
                                        <td class="text-start">:&nbsp;&nbsp;{{ $sr->engineerid->full_namee }}</td>
                                    </tr>


                                    @if (count($service_request_work) > 0)
                                        @php $i=1; @endphp
                                        @foreach ($service_request_work as $w)
                                            <tr>
                                                <td class="text-start">
                                                    Task {{ $i++ }}</td>
                                                <td class="text-start">:&nbsp;&nbsp;{{ $w->work }}</td>
                                            </tr>
                                        @endforeach
                                    @endif

                                </thead>
                            </table>
                        </div>
                    @endforeach
                @endif


            </div>


            <div class="row">
                <div class="col-8">
<div class="table-container">
                <table class="table table-hover" id="long-list" style="table-layout: fixed; width:100%">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">No</th>
                            <th style="width: 237px;" class="text-start ">Part No</th>
                            <th style="width: 391px;" class="text-start">Description</th>
                            <th style="width: 40px;" class=" text-nowrap text-center">Qty</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php $t_qty = 0; ?>


                    <tbody>
                        @foreach ($quotationitems as $Item)
                            @php

                                $t_qty += $Item->qty;

                            @endphp
                            <tr>
                                <td class="text-center">
                                    {{ $loop->iteration }}
                                </td>
                                <td><?php try{ ?> {{ $Item->productname->part_number }} <?php }catch (\Exception $e){} ?></td>
                                {{--  nl2br($Item->description)  --}}
                                <td>{{ $Item->description }}</td>

                                <td class="text-center">{{ $Item->qty }}</td>

                            </tr>
                        @endforeach


                        <tr>
                            <td colspan="11">&nbsp;</td>
                        </tr>
                    </tbody>




                </table>
            </div>
                </div>
            </div>
            



        </div>



    </div>
</div>




<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

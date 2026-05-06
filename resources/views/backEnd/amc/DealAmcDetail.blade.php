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
        {{ $amcdata->doc_number }}
    </h4>
    <div class="purchase-order-content-header-right">


        <button type="button" onclick="add_service_request({{ $amcdata->id }},'{{ $amcdata->doc_number }}')"
            class="btn btn-light">
            <i class="ico icon-outline-add-square text-success"></i> Request
        </button>


        <button type="submit" onclick="edit_service_request({{ $amcdata->id }})" class="btn btn-light">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </button>



        <button type="button" data-bs-toggle="modal" data-bs-target="#AddAmcModal" class="btn btn-light">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </button>


        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">

                <li> <a href="{{ url('crm-amc-service-request-list') }}"
                                    class="dropdown-item d-flex align-items-center"><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i> Request
                                    List</a></li>

            </ul>
        </div>


    </div>
</div>



<div class="card mb-3">
    <div class="card-body">
        <!-- <h4 class="mb-3 color-sub-head">Supplier Info (SUPS-1060)</h4> -->
        <div class="d-flex align-items-center mb-3">
            <div class="font-weight-600 title-15 me-3"> {{ @$amcdata->custname->name }}
            </div>
            @if (@$amcdata->is_delete != 0)
                <span class="badge bg-danger">Inactive</span>
            @else
                <span class="badge bg-success">Active</span>
            @endif

        </div>
        <div class="row">
            @if ($amcdata->deal_id != '')
                <div class="col-1 mb-3">
                    <label class="form-label">Deal ID:</label>
                    <div class="form-control-plaintext"> {{ App\SysHelper::get_code_from_dealid_without_company($amcdata->deal_id) }}
                    </div>
                </div>
            @endif

            <div class="col-1-5 mb-3">
                <label class="form-label">Date:</label>
                <div class="form-control-plaintext"> {{ date('d/m/Y', strtotime($amcdata->date)) }}
                </div>
            </div>

            <?php try { ?>
            <div class="col-2 mb-3">
                <label class="form-label">Sales Person:</label>
                <div class="form-control-plaintext truncate-text-custom"> {{ $amcdata->salesperson->full_name }}
                </div>
            </div>
            <?php }catch (\Exception $e) {  } ?>

            <div class="col-2-5 mb-3">
                <label class="form-label">AMC Period:</label>
                <div class="form-control-plaintext"> {{ date('d/M/Y', strtotime(@$amcdata->start_date)) }} to
                    {{ date('d/M/Y', strtotime(@$amcdata->end_date)) }}
                </div>
            </div>

            <div class="col-2 mb-3">
                <label class="form-label">Contact Person:</label>
                <div class="form-control-plaintext"> {{ $amcdata->contact_person }}
                </div>
            </div>

            <div class="col-1-5 mb-3">
                <label class="form-label">Contact Number:</label>
                <div class="form-control-plaintext"> {{ str_replace(' ', '', $amcdata->mobile_no) }}
                </div>
            </div>

            <div class="col-1 mb-3">
                <label class="form-label">Invoice:</label>
                <div class="form-control-plaintext"> {{ $amcdata->invoice }}
                </div>
            </div>

            <div class="col-1 mb-3">
                <label class="form-label">Amount:</label>
                <div class="form-control-plaintext"> {{ number_format($amcdata->amount, 2) }}

                </div>
            </div>

            <div class="col-11">
                <label class="form-label">Description:</label>
                <div class="form-control-plaintext truncate-text-custom"> {{ $amcdata->description }}
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
                aria-controls="service-request-info" aria-selected="true">Service Requested Details</button>
        </li>
        @if ($amcdata->comment)
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="comments-info-tab" data-bs-toggle="tab" data-bs-target="#comments-info"
                    type="button" role="tab" aria-controls="comments-info" aria-selected="false">Expired
                    Reason</button>
            </li>
        @endif

    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active" id="service-request-info" role="tabpanel"
            aria-labelledby="service-request-info-tab">

            <div class="row">


                @if (count($service_request) > 0)
 @php $serviceIndex = 1; @endphp
                    @foreach ($service_request as $sr)

                   

                        <div class="col-4">
                            <table id="long-list" class="table table-hover">
                                          <thead>
                <tr style="background-color:#f2f2f2;">
                    <th colspan="2" style="border:1px solid #ccc; padding:6px; text-align:left;">
                        Service #{{ $serviceIndex++ }}
                    </th>
                </tr>
                <tr>
                    <td style="border:1px solid #ccc; padding:6px;width:30%">Location</td>
                    <td style="border:1px solid #ccc; padding:6px;">{{ $sr->location_of_work }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ccc; padding:6px;">Service Date</td>
                    <td style="border:1px solid #ccc; padding:6px;">
                        {{ date('d/m/Y', strtotime($sr->service_date)) }}
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid #ccc; padding:6px;">Service Time</td>
                    <td style="border:1px solid #ccc; padding:6px;">
                        {{ date('h:i A', strtotime($sr->service_time)) }}
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid #ccc; padding:6px;">Source</td>
                    <td style="border:1px solid #ccc; padding:6px;">{{ $sr->source }}</td>
                </tr>
                <tr>
                    <td style="border:1px solid #ccc; padding:6px;">Service Engineer</td>
                    <td style="border:1px solid #ccc; padding:6px;">{{ $sr->serviceengineer->full_name }}</td>
                </tr>

                @php
                    $sw = $service_request_work->where('service_id', $sr->id);
                @endphp

                @if ($sw->count() > 0)
                    @php $i = 1; @endphp
                    @foreach ($sw as $w)
                        <tr>
                            <td style="border:1px solid #ccc; padding:6px;">Task {{ $i++ }}</td>
                            <td style="border:1px solid #ccc; padding:6px;">{{ $w->work }}</td>
                        </tr>
                    @endforeach
                @endif
            </thead>

                            </table>
                        </div>
                    @endforeach
                @endif


            </div>


        </div>

        <div class="tab-pane fade" id="comments-info" role="tabpanel" aria-labelledby="comments-info-tab">

            @if ($amcdata->comment)
                <div class="expiration-comment">
                    <blockquote class="border-start ps-3 mb-0">
                        {{ $amcdata->comment }}
                    </blockquote>
                </div>
            @endif



        </div>

    </div>
</div>




<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

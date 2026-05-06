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


       


        <button type="submit" onclick="edit_service_request({{ $amcdata->id }})" class="btn btn-light">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </button>



        <button type="button" data-bs-target="#ModalAddServiceRequest" data-bs-toggle="modal" class="btn btn-light">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </button>


        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">

                <li>     <a href="{{ url('crm-amc-list') }}" class="dropdown-item d-flex align-items-center "><i
                                        class="ico icon-outline-document-text text-success title-15 me-2"></i> AMC
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

            <div class="col-1-5 mb-3">
                <label class="form-label">Service Date:</label>
                <div class="form-control-plaintext"> {{ date('d/m/Y', strtotime($amcdata->service_date)) }}
                </div>
            </div>

            <div class="col-1-5 mb-3">
                <label class="form-label">Service Time:</label>
                <div class="form-control-plaintext"> {{ date('h:i A', strtotime($amcdata->service_time)) }}
                </div>
            </div>
            <?php try { ?>
            <div class="col-2 ">
                <label class="form-label">Service Engineer:</label>
                <div class="form-control-plaintext truncate-text-custom ">
                    <?php
                    $st = explode(',', $amcdata->service_engineer);
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
                    {{ $engineername }}
                </div>
            </div>
            <?php }catch (\Exception $e) {  } ?>


            <div class="col-1-5 mb-3">
                <label class="form-label">Source:</label>
                <div class="form-control-plaintext">{{ $amcdata->source }} </div>
            </div>

            <div class="col-2 mb-3">
                <label class="form-label">Contact Person:</label>
                <div class="form-control-plaintext"> {{ $amcdata->contact_person }}
                </div>
            </div>

            <div class="col-1-5 mb-3">
                <label class="form-label">Mobile No:</label>
                <div class="form-control-plaintext"> {{ str_replace(' ', '', $amcdata->mobile_no) }}
                </div>
            </div>

            <div class="col-10 ">
                <label class="form-label">Location of Work:</label>
                <div class="form-control-plaintext truncate-text-custom "> {{ $amcdata->location_of_work }}
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
        @if (count($amc_comments) > 0)
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="comments-info-tab" data-bs-toggle="tab" data-bs-target="#comments-info"
                    type="button" role="tab" aria-controls="comments-info" aria-selected="false">Comments</button>
            </li>
        @endif

    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active" id="service-request-info" role="tabpanel"
            aria-labelledby="service-request-info-tab">

            <div class="row">
                <div class="col-6">
                    @php
                        $sw = $amc_work->where('amc_id', $amcdata->id);
                    @endphp
                    @if (count($sw) > 0)
                        @php $i=1; @endphp
                        <table id="long-list" class="detail-item-table-noborder">
                            <thead>
                             
                           
                            @foreach ($sw as $w)
                                <tr>
                                    <td class="text-start">Task {{ $i }}</td>
                                    <td class="text-start">
                                        :&nbsp;&nbsp;{{ $w->work }}
                                    </td>
                                </tr>
                                @php $i++; @endphp
                            @endforeach
                             </thead>
                        </table>
                    @endif
                </div>
            </div>




        </div>

        <div class="tab-pane fade" id="comments-info" role="tabpanel" aria-labelledby="comments-info-tab">

            @if (count($amc_comments) > 0)
                @foreach ($amc_comments as $dt)
                    Date: {{ date('d/m/Y h:i A', strtotime($dt->created_at)) }}<br />
                    Engineer: {{ $dt->full_name }}<br />
                    Work: {{ $dt->work }}<br />
                    Comments: {{ $dt->comments }}<br />
                    <hr />
                @endforeach
            @endif



        </div>

    </div>
</div>




<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

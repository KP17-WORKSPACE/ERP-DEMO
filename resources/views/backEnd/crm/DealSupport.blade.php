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
        {{ $support->doc_number }}
    </h4>
    <div class="purchase-order-content-header-right">

        @if (Route::has('crm-deal-support-list'))
            <button type="button" data-bs-toggle="modal" data-bs-target="#addPreSalesRequest"
                class="btn btn-light">
                <i class="ico icon-outline-add-square text-success"></i> Request
            </button>
        @endif

         <button type="submit" onclick="edit_support_request({{ $support->id }})" class="btn btn-light">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </button>


          <button data-bs-toggle="modal" data-bs-target="#addPreSalesRequest" type="button" class="btn btn-light">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </button>


       



       


        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">

                  <li>
                    <a href="{{ url('crm-deal-support-list') }}"
                        class="dropdown-item d-flex align-items-center"><i
                            class="ico icon-outline-document-text text-success title-15 me-2"></i> Pre-Sales
                        List</a>
                </li>

               
            </ul>
        </div>


    </div>
</div>



<div class="card mb-3">
    <div class="card-body">



        <div class="d-flex align-items-center mb-3">
            <div class="font-weight-600 title-15 me-3 "> {{ @$support->customer_name }}
            </div>
            @if (@$support->is_delete != 0)
                <span class="badge bg-danger">Inactive</span>
            @else
                <span class="badge bg-success">Active</span>
            @endif

        </div>
        <div class="row">
            @if ($support->deal_id != '')
                <div class="col-1 mb-3">
                    <label class="form-label">Deal ID:</label>
                    <div class="form-control-plaintext"> <a
                            href="{{ url('crm-deals/show/' . @$support->deal_id) }}">{{ @$support->dealid->code }}</a>
                    </div>
                </div>
            @endif

            <div class="col-1-5 mb-3">
                <label class="form-label">Date:</label>
                <div class="form-control-plaintext"> {{ date('d/m/Y', strtotime(@$support->support_date)) }}
                  
                </div>
            </div>

            <div class="col-2 mb-3">
                <label class="form-label">Time:</label>
                <div class="form-control-plaintext"> 
                    {{ date('h:i A', strtotime(@$support->time_from)) }} -
                    {{ date('h:i A', strtotime(@$support->time_to)) }}
                </div>
            </div>

            <?php try { ?>
            <div class="col-2 mb-3">
                <label class="form-label">Sales Person:</label>
                <div class="form-control-plaintext truncate-text-custom"> {{ $support->salesperson->full_name }}
                </div>
            </div>
            <?php }catch (\Exception $e) {  } ?>

            <div class="col-5">
                <label class="form-label">Location:</label>
                <div class="form-control-plaintext truncate-text-custom"> {{ $support->site_name }}
                </div>
            </div>

        </div>
    </div>
</div>

<div class="tab-wrap mb-3">
    <ul class="nav nav-tabs" id="dealTrackTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="support-tab" data-bs-toggle="tab" data-bs-target="#support-info"
                type="button" role="tab" aria-controls="support" aria-selected="true">Support Info</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="task-closing-tab" data-bs-toggle="tab" data-bs-target="#task-closing-info"
                type="button" role="tab" aria-controls="task-closing" aria-selected="true">Task Closing
                Note</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="scope-work-tab" data-bs-toggle="tab" data-bs-target="#scope-work-info"
                type="button" role="tab" aria-controls="scope-work" aria-selected="true">Scope of Work</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="support-activity-tab" data-bs-toggle="tab"
                data-bs-target="#support-activity-info" type="button" role="tab" aria-controls="support-activity"
                aria-selected="true">Support Activity</button>
        </li>


    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">
        <div class="tab-pane fade show active" id="support-info" role="tabpanel" aria-labelledby="support-tab">

            <div class="row">

                @if ($support->support_person_id != '')
                    <div class="table-responsive">
                        <table id="long-list" class="table table-hover" style="table-layout: fixed;width:500px">

                            <thead class="text-start">
                                <tr>
                                    <th width="70px">@lang('Name')</th>
                                    <th width="70px">@lang('Mob No.')</th>
                                    <th width="70px">@lang('Email')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sp = explode(",",$support->support_person_id);
                        foreach($sp as $s) { ?>
                                <tr>
                                    <td>{{ App\SysHelper::get_user_detail($s)->full_name }}</td>
                                    <td> {{ App\SysHelper::get_user_detail($s)->mobile }}</td>
                                    <td> {{ App\SysHelper::get_user_detail($s)->email }}</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                @endif


            </div>


        </div>

        <div class="tab-pane fade show" id="task-closing-info" role="tabpanel" aria-labelledby="task-closing-tab">

            <div class="row">

                @if ($support->status == 3)
                    <div class="card shadow-sm border-start border-4 border-success mb-3">
                        <div class="card-body">

                            {{-- Title --}}
                            <h6 class="card-title mb-3  fw-bold">
                                Task Closed
                            </h6>

                            {{-- Closing Date --}}
                            <div class="mb-2">
                                <small class="text-muted d-block">Closing Date</small>
                                <span class="fw-semibold text-dark">
                                    {{ date('d M Y, h:i A', strtotime(@$support->close_at)) }}
                                </span>
                            </div>

                            {{-- Remarks --}}
                            <div class="mb-3">
                                <small class="text-muted d-block">Remarks</small>
                                <p class="mb-0 text-dark">{!! nl2br($support->close_remarks) !!}</p>
                            </div>

                            {{-- Document --}}
                            @if ($support->closingdoc != '')
                                <div class="mb-3">
                                    <a href="{{ asset('public/uploads/crm_deal_support_doc/' . $support->closingdoc) }}"
                                        target="_blank" class="btn btn-sm btn-light">
                                        <i class="ico icon-bold-paperclip"></i> View Document
                                    </a>
                                </div>
                            @endif

                            {{-- Closed By --}}
                            <div class="border-top pt-2 small text-muted">
                                <i class="fa fa-user-circle"></i>
                                {{ $support->closeby->full_name }}
                                <span class="text-danger">— Closed on
                                    {{ date('d M Y, h:i A', strtotime(@$support->close_at)) }}</span>
                            </div>
                        </div>
                    </div>
                @endif



            </div>


        </div>

        <div class="tab-pane fade show" id="scope-work-info" role="tabpanel" aria-labelledby="scope-work-tab">

              <div class="row">
                <div class="col-6">



                    @php
                        $scope_of_work = explode('$', $support->remarks);
                    @endphp
                    @if (count($scope_of_work) > 0)
                        <table id="long-list" class="detail-item-table-noborder">


                            @php $i=1; @endphp
                            @foreach ($scope_of_work as $work)
                                <tr>
                                    <td class="text-start">
                                        Task {{ $i++ }}</td>
                                    <td class="text-start">:&nbsp;&nbsp;{{ $work }}</td>
                                </tr>
                            @endforeach

                        </table>
                    @endif

                </div>

            </div>


        </div>

        <div class="tab-pane fade show" id="support-activity-info" role="tabpanel"
            aria-labelledby="support-activity-tab">

            <div class="row">

                @if (count($support_activity) > 0)
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3 text-primary">
                                <i class="fa fa-history me-1"></i> Support Activity
                            </h6>

                            @foreach ($support_activity as $val)
                                <div class="mb-4">
                                    {{-- Activity Date & Time --}}
                                    @if ($val->activity_date != null)
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Support Time</small>
                                            <span class="fw-semibold text-dark">
                                                {{ date('d M Y', strtotime($val->activity_date)) }},
                                                {{ date('h:i A', strtotime($val->activity_from)) }} –
                                                {{ date('h:i A', strtotime($val->activity_to)) }}
                                            </span>
                                        </div>
                                    @endif

                                    {{-- Remarks --}}
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Remarks</small>
                                        <p class="mb-0 text-dark">{!! nl2br($val->remarks) !!}</p>
                                    </div>

                                    {{-- Attached File --}}
                                    @if ($val->file != '')
                                        <div class="mb-2">
                                            <a href="{{ asset('public/uploads/crm_deal_support_doc/' . $val->file) }}"
                                                target="_blank" class="btn btn-sm btn-light">
                                                <i class="ico icon-bold-paperclip"></i> View Document
                                            </a>
                                        </div>
                                    @endif

                                    {{-- Created By --}}
                                    <div class="border-top pt-2 small text-muted">
                                        <i class="fa fa-user-circle"></i>
                                        {{ $val->createdby->full_name }}
                                        <span class="text-danger">— updated on
                                            {{ date('d M Y, h:i A', strtotime($val->created_at)) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif



            </div>


        </div>

    </div>
</div>




<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

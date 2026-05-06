@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Sales Report Gitex 2023</h2>
            <span class="page-label">Home - Sales Report Gitex 2023</span>
        </div>
    </div>

    <?php /*
    <div>
                    <div class="card shadow mb-4 p-4">
                        
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-sales-report', 'method' => 'POST', 'id' => 'crm-deals-sales-report']) }}
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="" class="form-check-label">Company</label>
                                <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                                    <option value="">-Select-</option>
                                    @foreach ($company as $value)
                                    <option value="{{ @$value->id }}" @if($ctrl_company ==$value->id) selected @endif>{{ @$value->company_name }} - {{ @$value->city }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Owner</label>
                                <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                                    <option value="">-Select-</option>
                                    @foreach ($staff as $value)
                                    <option value="{{ @$value->user_id }}" @if($ctrl_owner ==$value->user_id) selected @endif>{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">From Date</label>
                                <input class="form-control datepicker" id="date" type="date" autocomplete="off" name="date" value="{{ $ctrl_date }}" required>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">To Date</label>
                                <input class="form-control" id="date2" type="date" autocomplete="off" name="date2" value="{{ $ctrl_date2 }}" required>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Filter By</label>
                                <select class="form-control js-example-basic-single" name="filter_by" id="filter_by">
                                    <option value="">-Select-</option>
                                    <option value="this_month" @if($filter_by == "this_month") selected @endif>This Month</option>
                                    <option value="today" @if($filter_by == "today") selected @endif>Today</option>
                                    <option value="this_week" @if($filter_by == "this_week") selected @endif>This Week</option>
                                    <option value="last_week" @if($filter_by == "last_week") selected @endif>Last Week</option>                                    
                                    <option value="last_month" @if($filter_by == "last_month") selected @endif>Last Month</option>
                                    <option value="this_quarter" @if($filter_by == "this_quarter") selected @endif>This Quarter</option>
                                    <option value="pre_quarter" @if($filter_by == "pre_quarter") selected @endif>Previous Quarter</option>
                                    <option value="this_year" @if($filter_by == "this_year") selected @endif>This Year</option>
                                    <option value="last_year" @if($filter_by == "last_year") selected @endif>Last Year</option>
                                </select>
                            </div>

                            <div class="col-1 mb-2"><br />
                                <button type="submit" class="btn btn-primary" id="btnSubmit">Filter</button>
                            </div>
                        </div>
                    {{ Form::close() }}
                    </div>
    </div>
    */ ?>

                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        @if(session()->has('message-success') != "" || session()->get('message-danger') != "")
                                        <tr>
                                            <td colspan="7">
                                                @if(session()->has('message-success'))
                                                <div class="alert alert-success">
                                                    {{ session()->get('message-success') }}
                                                </div>
                                                @elseif(session()->has('message-danger'))
                                                <div class="alert alert-danger">
                                                    {{ session()->get('message-danger') }}
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        
                                        <tr>
                                            <th>@lang('Name')</th>
                                            <th class="text-right">@lang('Leads')</th>
                                            <th class="text-right">@lang('Deals')</th>
                                            <th class="text-right">@lang('Won')</th>
                                            <th class="text-right">@lang('Invoiced')</th>
                                            <th class="text-right">@lang('Deal Value')</th>
                                            <th style="width: 100px;" class="text-right">Detail</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $deal_value=0; $deal_count=0; $lead_count=0; $won_count=0; $invoice_count=0; ?>
                                        @if($data!='0')
                                        @foreach($data as $value)
                                <tr>
                                    <td><a class="text-dark">{{@$value["full_name"]}}</a></td>

                                    <td class="text-right">
                                            {{ @$value["lead_count"] }}
                                            <?php $lead_count += $value["lead_count"]; ?>
                                    </td>
                                    <td class="text-right">
                                        @if(count($converted)>0)
                                            @foreach ($converted as $cd)
                                                @if ($cd->owner == $value['owner'])
                                                    {{ $cd->deal_count }}
                                                    <?php $deal_count += $cd->deal_count; ?>                                                    
                                                @endif
                                            @endforeach
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if(count($won)>0)
                                            @foreach ($won as $wo)
                                                @if ($wo->owner == $value['owner'])
                                                    {{ $wo->won_count }}
                                                    <?php $won_count += $wo->won_count; ?>
                                                @else
                                                    0
                                                @endif
                                            @endforeach
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if(count($invoiced)>0)
                                            @foreach ($invoiced as $in)
                                                @if ($in->owner == $value['owner'])
                                                    {{ $in->won_count }}
                                                    <?php $invoice_count += $in->invoice_count; ?>
                                                @else
                                                    0
                                                @endif
                                            @endforeach
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if(count($dealvalue)>0)
                                            @foreach ($dealvalue as $dv)
                                                @if ($dv->owner == $value['owner'])
                                                    {{ @App\SysHelper::com_curr_format($dv->deal_value, 2, '.', ',')}}
                                                    <?php $deal_value += $dv->deal_value; ?>
                                                @else
                                                    0
                                                @endif
                                            @endforeach
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{url('crm-deals-gitex2023-report-list/'.$value['owner'].'/gitex-2023')}}" class="bg-info text-white p-2">View Detail</a>
                                    </td>
                                </tr>
                                  
                                @endforeach
                                @endif
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th class="text-right">{{ $lead_count }}</th>
                                        <th class="text-right">{{ $deal_count }}</th>
                                        <th class="text-right">{{ $won_count }}</th>
                                        <th class="text-right">{{ $invoice_count }}</th>
                                        <th class="text-right">{{ @App\SysHelper::com_curr_format($deal_value, 2, '.', ',') }}</th>
                                        <th></th>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>


<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
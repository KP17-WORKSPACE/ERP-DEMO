@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Brand Sales Report</h2>
            <span class="page-label">Home - Brand Sales Report</span>
        </div>
    </div>
    <div>
                    <div class="card shadow mb-4 p-4">
                        
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-brand-sales-report-new', 'method' => 'POST', 'id' => 'crm-deals-sales-report']) }}
                    {{--  <input type="hidden" name="company_id" id="company_id" value="{{ session('logged_session_data.company_id') }}" />  --}}
                        <div class="row">
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Owner</label>
                                <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                                    <option value="">-Select-</option>
                                    @foreach ($company as $value)
                                    <option value="{{ @$value->id }}" @if($ctrl_company ==$value->id) selected @endif>{{ @$value->company_name }}</option>
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
                                            <th>@lang('Brand')</th>
                                            <th class="text-right">@lang('Deals')</th>
                                            <th class="text-right">@lang('Forecast')</th>
                                            <th class="text-right">@lang('On Process')</th>
                                            <th class="text-right">@lang('Revenue')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $deal_value=0; $deal_count=0; $total_sales=0; $total_gp=0; $top_target=0; $total_forecast=0; $toshow=0; $total_on_process=0; ?>
                                        @if($data!='0')
                                        @foreach($data as $value)

                                        @php $toshow=0; @endphp
                                        <?php
                                        if($value["role_id"] == 1 || $value["role_id"] == 2){
                                            if($value["dealcount"] == 0){ $toshow=1; }
                                        }                                        
                                        ?>

                                        @if ($toshow == 0)

                                <tr>
                                    <td><a class="text-dark">{{@$value["title"]}}</a></td>

                                    <td class="text-right">
                                            {{ @$value["dealcount"] }}
                                    </td>
                                    <td class="text-right">{{ @$value["forcast"] }} <?php $total_forecast += $value["forcast"]; ?></td>
                                    <td class="text-right">
                                        {{ @$value["on_process"] }}
                                            <?php $total_on_process += @$value["on_process"]; ?>
                                    </td>
                                    <td class="text-right">
                                        @if (@$value["revenue"][0] == "0.00")
                                            <b>
                                        @else
                                            <b class="text-danger font-bold">
                                        @endif
                                            {{ @$value["revenue"][0] }} 
                                            </b>
                                            <?php $deal_count += @$value["dealcount"]; ?>

                                            <?php  $deal_value += @$value["revenue"][0];  ?>
                                            
                                            <?php $total_sales = @$value["revenue"][1];
                                                  $top_target = @$value["target"];
                                            ?>
                                    </td>
                                    
                                </tr>
                                @endif
                                  
                                @endforeach
                                @endif
                                    </tbody>
                                    <tfoot>
                                        <th></th>
                                        <th class="text-right">{{ $deal_count }}</th>
                                        <th class="text-right">{{ @App\SysHelper::com_curr_format($total_forecast, 2, '.', ',') }}</th>
                                        <th class="text-right">{{ @App\SysHelper::com_curr_format($total_on_process, 2, '.', ',') }}</th>
                                        <th class="text-right">{{ @App\SysHelper::com_curr_format($deal_value, 2, '.', ',') }}</th>
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
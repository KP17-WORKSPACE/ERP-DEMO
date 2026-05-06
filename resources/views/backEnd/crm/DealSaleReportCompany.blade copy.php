@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Company Sales Report</h2>
            <span class="page-label">Home - Company Sales Report</span>
        </div>
    </div>
    <div>
                    <div class="card shadow mb-4 p-4">
                        
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-sales-report-company', 'method' => 'POST', 'id' => 'crm-deals-sales-report-company']) }}
                    {{--  <input type="hidden" name="company_id" id="company_id" value="{{ session('logged_session_data.company_id') }}" />  --}}
                        <div class="row">
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">From Date</label>
                                <input class="form-control datepicker" id="date" type="date" autocomplete="off" name="date" value="{{ $ctrl_date }}" required onchange="set_filter()">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">To Date</label>
                                <input class="form-control" id="date2" type="date" autocomplete="off" name="date2" value="{{ $ctrl_date2 }}" required onchange="set_filter()">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by">
                                    <option value="" @if($filter_by == "") selected @endif>-Select-</option>
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
                            <script>
                                function set_filter(){
                                if($('#date').val()!="" || $('#date2').val() != "")
                                {
                                    $('#filter_by').val('')
                                }
                                }
                            </script>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">PS/AMC</label>
                                <select class="form-control" name="ps_amc" id="ps_amc">
                                    <option value="" @if($ps_amc == "") selected @endif>-View All-</option>
                                    <option value="ps" @if($ps_amc == "ps") selected @endif>PS</option>
                                    <option value="amc" @if($ps_amc == "amc") selected @endif>AMC</option>
                                    <option value="ps_amc" @if($ps_amc == "ps_amc") selected @endif>PS & AMC</option>
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
                                <table class="table table-bordered table-striped" id="dataTable1" width="100%" cellspacing="0">
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
                                            <th class="text-center">@lang('Deals')</th>
                                            <th class="text-right">@lang('Revenue')</th>
                                            <th class="text-right">@lang('GP')</th>
                                            <th class="text-right">@lang('GP %')</th>
                                            <th class="text-right">@lang('Forecast')</th>
                                            <th class="text-right">@lang('On Process')</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $deal_value=0; $deal_count=0; $total_sales=0; $total_gp=0; $top_target=0; $total_forecast=0; $toshow=0; $total_on_process=0; $total_revenue=0; $total_gp=0; $revenue_target=0; $gp_target=0; $internal_sum=0; $internal_gp_sum=0; $external_sum=0; $external_gp_sum=0;
                                        $total_forecast_ex=0; $total_on_process_ex=0; $total_forecast_in=0; $total_on_process_in=0; ?>
                                        @if($data!='0')
                                        @foreach($data as $value)

                                        @php $toshow=0; @endphp                                        

                                        @if ($toshow == 0)

                                <tr>
                                    {{--  Name  --}}                                    
                                    <td><a class="text-dark" href="{{url('crm-deals-sales-report/'.$value["company_id"].'/'.$ctrl_date.'/'.$ctrl_date2)}}">{{@$value["full_name"]}}</a></td>
                                    
                                    {{--  Deals  --}}
                                    <td class="text-center">
                                        
                                        @if ($value["dealcount"]>0)
                                        <a class="border border-info text-info pr-2 pl-2">{{ @$value["dealcount"] }} <?php $deal_count += @$value["dealcount"]; ?></a>
                                        @else
                                        0
                                        @endif
                                    </td>
                                    {{--  REVENUE START  --}}
                                    <td class="text-right">
                                        @if (@$value["revenue"][0] == "0.00")
                                            <b>
                                        @else
                                            <b class="font-bold">
                                        @endif
                                        {{ @App\SysHelper::com_curr_format($value["revenue"][0], 2, '.', ',') }} 
                                        
                                        
                                        

                                        @if($value['combind_user_id'] != "")

                                        <?php $total_revenue += $value["revenue_nocombind"][0]; ?>
                                        @else
                                        <?php $total_revenue += $value["revenue"][0];  ?>
                                        @endif
                                        </b>
                                        {{--  ( {{ @App\SysHelper::com_curr_format($value["revenue"][2], 2, '.', ',') }} )  --}}
                                            
                                    </td>
                                    {{--  GP START --}}
                                    <td class="text-right">
                                        @if (@$value["revenue"][1] == "0.00")
                                            <b>
                                        @else
                                        <b class="font-bold">
                                        @endif{{ @App\SysHelper::com_curr_format($value["revenue"][1], 2, '.', ',') }}</b>
                                        
                                        
                                        @if($value['combind_user_id'] != "")
                                        <?php $total_gp += $value["revenue_nocombind"][1]; ?>
                                        @else
                                        <?php $total_gp += $value["revenue"][1];  ?>
                                        @endif
                                    </td>
                                    <td class="text-right"><?php                                        
                                        try { ?>
                                        {{ round($value["revenue"][1]/$value["revenue"][0]*100,2) }}%
                                        <?php }catch (\Exception $e) {?> 0% <?php } ?></td>
                                    {{--  Forecast  --}}
                                    <td class="text-right"><a class="text-dark">{{ @App\SysHelper::com_curr_format(@$value["forcast"][0],2,'.',',') }}</a> <?php $total_forecast += $value["forcast"][0]; ?></td>
                                    
                                    {{--  On Process  --}}
                                    <td class="text-right">
                                        <a class="text-dark">{{ @App\SysHelper::com_curr_format(@$value["on_process"][0],2,'.',',') }}</a>
                                            <?php $total_on_process += @$value["on_process"][0]; ?>
                                    </td>
                                </tr>
                                @endif
                                  
                                @endforeach
                                @endif
                                    </tbody>
                                    {{-- <tfoot style="background: #7e7e7e; color: #ffffff;">
                                        <tr>
                                            <td></td>
                                            <td class="text-center">{{ $deal_count }}</td>
                                            <td class="text-right">{{ @App\SysHelper::com_curr_format($total_revenue, 2, '.', ',') }}</td>
                                            <td class="text-right">{{ @App\SysHelper::com_curr_format($total_gp, 2, '.', ',') }}</td>
                                            <td></td>
                                            <td class="text-right">{{ @App\SysHelper::com_curr_format($total_forecast, 2, '.', ',') }}</td>
                                            <td class="text-right">{{ @App\SysHelper::com_curr_format($total_on_process, 2, '.', ',') }}</td>
                                        </tr>
                                    </tfoot> --}}

                                </table>
                                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                <script>
                                     function view_in_ex_tr(id)
                                    {
                                        var tr_in = $('#in_tr_' + id);
                                        var tr_ex = $('#ex_tr_' + id);
                                        if (tr_in.css('display') === 'none') {
                                            tr_in.css('display', '');
                                        } else {
                                            tr_in.css('display', 'none');
                                        }
                                        if (tr_ex.css('display') === 'none') {
                                            tr_ex.css('display', '');
                                        } else {
                                            tr_ex.css('display', 'none');
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                    </div>

                </div>
<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>


<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
@extends('backEnd.newmasterpage')
@section('mainContent')
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <style>
        .progress{
            height: 15px;
        }
    </style>

    <script>
        //added ny kp
        function toggleLongFilters() {
            console.log("clicked");
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>
    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0"> Sales Report
                </h4>
                <div class="search-filter-container mb-0">

  <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">
                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>

                    <div class="dropdown">
                        <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">


                        </ul>
                    </div>


                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card" style="width: 100%">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-sales-report', 'method' => 'POST', 'id' => 'crm-deals-sales-report']) }}

                        <div class="row">

                            <div class="col-1-5">
                                <label for="" class="form-label">Company</label>
                                <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                                    <option value="">-Select-</option>
                                    @foreach ($company as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_company == $value->id) selected @endif>{{ @$value->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1-5">
                                <label for="" class="form-label">Owner</label>
                                <select class="form-control js-example-basic-single" name="owner_id" id="owner_id">
                                    <option value="">-Select-</option>
                                    @foreach ($staff as $value)
                                        <option value="{{ @$value->user_id }}"
                                            @if ($ctrl_owner == $value->user_id) selected @endif>{{ @$value->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Form Date</label>
                                <input class="form-control date-picker" id="date" type="text" autocomplete="off"
                                    name="date"
                                    value="{{ $ctrl_date ? @App\SysHelper::normalizeToDmy($ctrl_date) : '' }}">
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">To Date</label>
                                <input class="form-control date-picker" id="date" type="text" autocomplete="off"
                                    name="date2"
                                    value="{{ $ctrl_date2 ? @App\SysHelper::normalizeToDmy($ctrl_date2) : '' }}">
                            </div>


                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Filter By</label>
                                <select class="form-control" name="filter_by" id="filter_by" onchange="this.form.submit()">
                                    <option value="" @if ($filter_by == '') selected @endif>-Select-
                                    </option>
                                    <option value="today">Today</option>
                                    <option value="this_week">This Week
                                    </option>
                                    <option value="last_week">Last Week
                                    </option>
                                    <option value="this_month">This Month
                                    </option>
                                    <option value="last_month">Last Month
                                    </option>
                                    <option value="last_6_months">Last 6
                                        Months
                                    </option>
                                    <option value="this_year">This Year
                                    </option>
                                    <option value="last_year">Last Year
                                    </option>
                                </select>
                            </div>

                            <div class="col-1-5">
                                <label for="" class="form-label">PS/AMC</label>
                                <select class="form-control" name="ps_amc" id="ps_amc">
                                    <option value="" @if ($ps_amc == '') selected @endif>-View All-
                                    </option>
                                    <option value="ps" @if ($ps_amc == 'ps') selected @endif>PS</option>
                                    <option value="amc" @if ($ps_amc == 'amc') selected @endif>AMC</option>
                                    <option value="ps_amc" @if ($ps_amc == 'ps_amc') selected @endif>PS & AMC
                                    </option>
                                </select>
                            </div>

                            <div class="col-1-5">
                                <label for="" class="form-label">Currency</label>
                                <select class="form-control" name="currancy" id="currancy">
                                    <option value="0" @if (@$ctrl_currancy == 0) selected @endif>AED</option>
                                    <option value="1" @if (@$ctrl_currancy == 1) selected @endif>Default
                                    </option>
                                </select>
                            </div>


                            <div class="col-1-5 filter-field d-none">
                                 <button type="submit" class="btn btn-light mt-4">
                                    <i class="ico icon-outline-magnifer text-success"></i> Filter
                                </button>
                            </div>

                        </div>
                        {{ Form::close() }}


                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">



            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th style="width:176px">@lang('Name')</th>
                            <th class="text-center">@lang('Deals')</th>
                            <th class="text-end">@lang('Revenue')</th>
                            <th class="text-end">@lang('R Target')</th>
                            <th style="width: 150px;" class="">@lang('%')</th>
                            <th class="text-end">@lang('GP')</th>
                            <th class="text-end">@lang('GP Target')</th>
                            <th style="width: 150px;" class="">@lang('%')</th>
                            <th class="text-end">@lang('GP %')</th>
                            <th class="text-end">@lang('Total Target %')</th>
                            <th class="text-end">@lang('Forecast')</th>
                            <th class="text-end">@lang('On Process')</th>
                        </tr>
                    </thead>


                    <tbody>
                        <?php $deal_value = 0;
                        $deal_count = 0;
                        $total_sales = 0;
                        $total_gp = 0;
                        $top_target = 0;
                        $total_forecast = 0;
                        $toshow = 0;
                        $total_on_process = 0;
                        $total_revenue = 0;
                        $total_gp = 0;
                        $revenue_target = 0;
                        $gp_target = 0;
                        $internal_sum = 0;
                        $internal_gp_sum = 0;
                        $external_sum = 0;
                        $external_gp_sum = 0;
                        $total_forecast_ex = 0;
                        $total_on_process_ex = 0;
                        $total_forecast_in = 0;
                        $total_on_process_in = 0; ?>
                        @if ($data != '0')
                            @foreach ($data as $value)
                                @php $toshow=0; @endphp
                                <?php
                                if ($value['role_id'] == 1 || $value['role_id'] == 2) {
                                    if ($value['dealcount'] == '0' && $value['forcast'][0] == '0.00' && $value['revenue'][0] == '0.00') {
                                        $toshow = 1;
                                    }
                                }
                                ?>

                                @if ($toshow == 0)
                                    <tr onclick="view_in_ex_tr({{ $value['user_id'] }})"
                                        @if ($value['dealcount'] == 0) style="display:none;" @endif>
                                        {{--  Name  --}}
                                        <td><a class="text-dark">{{ @$value['full_name'] }}</a></td>

                                        {{--  Deals  --}}
                                        <td class="text-center">

                                            @if ($value['dealcount'] > 0)
                                                <a href="{{ url('crm-deals-sales-report-list/' . $value['user_id'] . '/' . $ctrl_company . '/' . $ctrl_date . '/' . $ctrl_date2) }}"
                                                    target="_blank"
                                                    class="border border-success text-success pr-2 pl-2">{{ @$value['dealcount'] }}
                                                    <?php $deal_count += @$value['dealcount']; ?></a>
                                            @else
                                                0
                                            @endif
                                        </td>


                                        {{--  REVENUE START  --}}
                                        <td class="text-end">
                                            @if (@$value['revenue'][0] == '0.00')
                                                <b>
                                                @else
                                                    <b class="font-bold">
                                            @endif
                                            {{ @App\SysHelper::com_curr_format($value['revenue'][0], 2, '.', ',') }}




                                            @if ($value['combind_user_id'] != '')
                                                <?php $total_revenue += $value['revenue_nocombind'][0]; ?>
                                            @else
                                                <?php $total_revenue += $value['revenue'][0]; ?>
                                            @endif
                                            </b>
                                            {{--  ( {{ @App\SysHelper::com_curr_format($value["revenue"][2], 2, '.', ',') }} )  --}}

                                        </td>
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format(@$value['target']['rev_amount'], 2, '.', ',') }}
                                            <?php $revenue_target += $value['target']['rev_amount']; ?></td>
                                        <td>
                                            <?php
                                        
                                        try {
                                                $tp2 = round(($value["revenue"][0]) / $value["target"]['rev_amount'] * 100,0);
                        $tpcolor="bg-danger";
                        if($tp2<40){$tpcolor="bg-danger";}
                        if($tp2>=40 && $tp2<80){$tpcolor="bg-warning text-dark";}
                        if($tp2>=80 && $tp2<=100){$tpcolor="bg-success";}
                        if($tp2>100){$tpcolor="bg-purple";}
                        ?>
                                            <div class="progress">
                                                <div class="progress-bar {{ $tpcolor }}"
                                                    style="width:{{ $tp2 }}%">{{ $tp2 }}%</div>
                                            </div>

                                            <?php }catch (\Exception $e) { $tp2=0; } ?>

                                        </td>
                                        {{--  REVENUE END  --}}

                                        {{--  GP START --}}
                                        <td class="text-end">
                                            @if (@$value['revenue'][1] == '0.00')
                                                <b>
                                                @else
                                                    <b class="font-bold">
                                            @endif
                                            {{ @App\SysHelper::com_curr_format($value['revenue'][1], 2, '.', ',') }}</b>


                                            @if ($value['combind_user_id'] != '')
                                                <?php $total_gp += $value['revenue_nocombind'][1]; ?>
                                            @else
                                                <?php $total_gp += $value['revenue'][1]; ?>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format(@$value['target']['gp_amount'], 2, '.', ',') }}
                                            <?php $gp_target += $value['target']['gp_amount']; ?></td>
                                        <td>
                                            <?php
                                        
                                        try {
                                                $tp = round(($value["revenue"][1]) / $value["target"]['gp_amount'] * 100,0);
                        $tpcolor="bg-danger";
                        if($tp<40){$tpcolor="bg-danger";}
                        if($tp>=40 && $tp<80){$tpcolor="bg-warning text-dark";}
                        if($tp>=80 && $tp<=100){$tpcolor="bg-success";}
                        if($tp>100){$tpcolor="bg-purple";}
                        ?>
                                            <div class="progress">
                                                <div class="progress-bar {{ $tpcolor }}"
                                                    style="width:{{ $tp }}%">{{ $tp }}%</div>
                                            </div>

                                            <?php }catch (\Exception $e) { $tp=0; } ?>

                                        </td>

                                        {{--  GP END --}}


                                        <td class="text-end"><?php                                        
                                        try { ?>
                                            {{ round(($value['revenue'][1] / $value['revenue'][0]) * 100, 2) }}%
                                            <?php }catch (\Exception $e) {?> 0% <?php } ?></td>
                                        <td class="text-end">{{ round(($tp + $tp2) / 2, 2) }}%</td>

                                        {{--  Forecast  --}}
                                        <td class="text-end"><a class="text-dark"
                                                href="{{ url('crm-deals-forecast-report-list/' . $value['user_id'] . '/' . $ctrl_company . '/' . $ctrl_date . '/' . $ctrl_date2) }}"
                                                target="_blank"
                                                title="View Forcast Deals">{{ @App\SysHelper::com_curr_format(@$value['forcast'][0], 2, '.', ',') }}</a>
                                            <?php $total_forecast += $value['forcast'][0]; ?></td>

                                        {{--  On Process  --}}
                                        <td class="text-end">
                                            <a class="text-dark"
                                                href="{{ url('crm-deals-onprocess-report-list/' . $value['user_id'] . '/' . $ctrl_company . '/' . $ctrl_date . '/' . $ctrl_date2) }}"
                                                target="_blank"
                                                title="View On Process Deals">{{ @App\SysHelper::com_curr_format(@$value['on_process'][0], 2, '.', ',') }}</a>
                                            <?php $total_on_process += @$value['on_process'][0]; ?>
                                        </td>
                                    </tr>

                                    <?php $internal = @App\SysHelper::get_internal_external_sales_report($value['user_id'], $ctrl_date, $ctrl_date2, $ctrl_company); ?>

                                    <tr id="ex_tr_{{ $value['user_id'] }}" style="font-style: italic; display: none;">
                                        <td class="text-end">External</td>
                                        <td class="text-center"></td>
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format($internal[2], 2, '.', ',') }}
                                            <?php $external_sum += $internal[2]; ?></td>
                                        <td class="text-end"></td>
                                        <td style="width: 150px;">
                                            @if ($value['revenue'][0] != 0)
                                                {{ round(($internal[2] / $value['revenue'][0]) * 100, 2) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format($internal[3], 2, '.', ',') }}
                                            <?php $external_gp_sum += $internal[3]; ?></td>
                                        <td class="text-end"></td>
                                        <td style="width: 150px;">
                                            @if ($value['revenue'][1] != 0)
                                                {{ round(($internal[3] / $value['revenue'][1]) * 100, 2) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                        <td class="text-end"></td>
                                        <td class="text-end"></td>
                                        <td class="text-end">{{ @$value['forcast'][2] }} <?php $total_forecast_ex += $value['forcast'][2]; ?></td>
                                        <td class="text-end">{{ @$value['on_process'][2] }} <?php $total_on_process_ex += $value['on_process'][2]; ?></td>
                                    </tr>
                                    <tr id="in_tr_{{ $value['user_id'] }}" style="font-style: italic; display: none;">
                                        <td class="text-end">Internal</td>
                                        <td class="text-center"></td>
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format($internal[0], 2, '.', ',') }}
                                            <?php $internal_sum += $internal[0]; ?></td>
                                        <td class="text-end"></td>
                                        <td style="width: 150px;">
                                            @if ($value['revenue'][0] != 0)
                                                {{ round(($internal[0] / $value['revenue'][0]) * 100, 2) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            {{ @App\SysHelper::com_curr_format($internal[1], 2, '.', ',') }}
                                            <?php $internal_gp_sum += $internal[1]; ?></td>
                                        <td class="text-end"></td>
                                        <td style="width: 150px;">
                                            @if ($value['revenue'][1] != 0)
                                                {{ round(($internal[1] / $value['revenue'][1]) * 100, 2) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                        <td class="text-end"></td>
                                        <td class="text-end"></td>
                                        <td class="text-end">{{ @$value['forcast'][1] }} <?php $total_forecast_in += $value['forcast'][1]; ?></td>
                                        <td class="text-end">{{ @$value['on_process'][1] }} <?php $total_on_process_in += $value['on_process'][1]; ?></td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    </tbody>

                    <tfoot style="background: #7e7e7e; color: #ffffff;">
                        <thead onclick="view_in_ex_tr(0)">
                            <th></th>
                            <th class="text-center">{{ $data->sum(['dealcount']) }}</th>
                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_revenue, 2, '.', ',') }}</th>
                            <th class="text-end">{{ @App\SysHelper::com_curr_format($revenue_target, 2, '.', ',') }}
                            </th>
                            <th></th>
                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_gp, 2, '.', ',') }}</th>
                            <th class="text-end">{{ @App\SysHelper::com_curr_format($gp_target, 2, '.', ',') }}</th>
                            <th></th>
                            <th></th>

                            {{--  <th class="text-end">{{ @App\SysHelper::com_curr_format($rev_sum[0], 2, '.', ',') }}</th>
                                            <th class="text-end">{{ @App\SysHelper::com_curr_format($rev_sum[1], 2, '.', ',') }}</th>  --}}
                            <th></th>
                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_forecast, 2, '.', ',') }}
                            </th>
                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_on_process, 2, '.', ',') }}
                            </th>
                        </thead>
                        <thead id="ex_tr_0" style="font-style: italic; display: none;">
                            <th class="text-end">External</th>
                            <th class="text-center"></th>
                            <th class="text-end">{{ @App\SysHelper::com_curr_format($external_sum, 2, '.', ',') }}</th>
                            <th class="text-end"></th>
                            <th>
                            </th>
                            <th class="text-end">{{ @App\SysHelper::com_curr_format($external_gp_sum, 2, '.', ',') }}
                            </th>
                            <th class="text-end"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-end">{{ @App\SysHelper::com_curr_format($total_forecast_ex, 2, '.', ',') }}
                            </th>
                            <th class="text-end">
                                {{ @App\SysHelper::com_curr_format($total_on_process_ex, 2, '.', ',') }}</th>
                            </tr>
                            <thead id="in_tr_0" style="font-style: italic; display: none;">
                                <th class="text-end">Internal</th>
                                <th class="text-center"></th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($internal_sum, 2, '.', ',') }}
                                </th>
                                <th class="text-end"></th>
                                <th></th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($internal_gp_sum, 2, '.', ',') }}
                                </th>
                                <th class="text-end"></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="text-end">
                                    {{ @App\SysHelper::com_curr_format($total_forecast_in, 2, '.', ',') }}
                                </th>
                                <th class="text-end">
                                    {{ @App\SysHelper::com_curr_format($total_on_process_in, 2, '.', ',') }}</th>
                            </thead>
                    </tfoot>



                </table>
            </div>
        </div>
    </aside>









    <script>
        function view_in_ex_tr(id) {
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

    <script>
        $(document).ready(function() {
            $('.collapse').on('show.bs.collapse', function() {
                $(this).closest('.task-card').find('.toggle-icon')
                    .removeClass('ico icon-outline-alt-arrow-down ')
                    .addClass('ico icon-outline-alt-arrow-up');
            });

            $('.collapse').on('hide.bs.collapse', function() {
                $(this).closest('.task-card').find('.toggle-icon')
                    .removeClass('ico icon-outline-alt-arrow-up ')
                    .addClass('ico icon-outline-alt-arrow-down');
            });
        });
    </script>


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

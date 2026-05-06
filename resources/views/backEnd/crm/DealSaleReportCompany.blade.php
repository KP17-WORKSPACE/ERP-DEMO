@extends('backEnd.newmasterpage')
@section('mainContent')
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

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
                <h4 class="mb-0">Company Sales Report
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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-sales-report-company', 'method' => 'POST', 'id' => 'crm-deals-sales-report-company']) }}

                        <div class="row">


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


                            <div class="col-md-3 filter-field d-none">
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
                            <th>@lang('Name')</th>
                            <th class="text-center">@lang('Deals')</th>
                            <th class="text-end">@lang('Revenue')</th>
                            <th class="text-end">@lang('GP')</th>
                            <th class="text-end">@lang('GP %')</th>
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

                                @if ($toshow == 0)
                                    <tr>
                                        {{--  Name  --}}
                                        <td><a class="" target="_blank"
                                                href="{{ url('crm-deals-sales-report/' . $value['company_id'] . '/' . $ctrl_date . '/' . $ctrl_date2) }}">{{ @$value['full_name'] }}</a>
                                        </td>

                                        {{--  Deals  --}}
                                        <td class="text-center">

                                            @if ($value['dealcount'] > 0)
                                                <a class="border border-success text-success pr-2 pl-2" target="_blank"  href="{{ url('crm-deals-sales-report/' . $value['company_id'] . '/' . $ctrl_date . '/' . $ctrl_date2) }}">{{ @$value['dealcount'] }}
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

                                            {{--  ( {{ @App\SysHelper::com_curr_format($value["revenue"][2], 2, '.', ',') }} )  --}}

                                        </td>
                                        {{--  GP START --}}
                                        <td class="text-end">
                                            @if (@$value['revenue'][1] == '0.00')
                                                <b>
                                                @else
                                                    <b class="font-bold">
                                            @endif
                                            {{ @App\SysHelper::com_curr_format($value['revenue'][1], 2, '.', ',') }}


                                            @if ($value['combind_user_id'] != '')
                                                <?php $total_gp += $value['revenue_nocombind'][1]; ?>
                                            @else
                                                <?php $total_gp += $value['revenue'][1]; ?>
                                            @endif
                                        </td>
                                        <td class="text-end"><?php                                        
                                        try { ?>
                                            {{ round(($value['revenue'][1] / $value['revenue'][0]) * 100, 2) }}%
                                            <?php }catch (\Exception $e) {?> 0% <?php } ?></td>
                                        {{--  Forecast  --}}
                                        <td class="text-end"><a
                                                class="text-dark">{{ @App\SysHelper::com_curr_format(@$value['forcast'][0], 2, '.', ',') }}</a>
                                            <?php $total_forecast += $value['forcast'][0]; ?></td>

                                        {{--  On Process  --}}
                                        <td class="text-end">
                                            <a
                                                class="text-dark">{{ @App\SysHelper::com_curr_format(@$value['on_process'][0], 2, '.', ',') }}</a>
                                            <?php $total_on_process += @$value['on_process'][0]; ?>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    </tbody>





                </table>
            </div>
        </div>
    </aside>









    <script>
        function view_in_ex_tr(id) {

            var tr_ex = $('#ex_tr_' + id);

            if (tr_ex.css('display') === 'none') {
                tr_ex.css('display', '');
            } else {
                tr_ex.css('display', 'none');
            }
        }
    </script>




    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

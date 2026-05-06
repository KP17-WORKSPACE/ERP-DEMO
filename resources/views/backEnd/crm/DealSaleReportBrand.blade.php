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
                <h4 class="mb-0"> Brand Sales Report
                </h4>
                <div class="search-filter-container mb-0">


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
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-brand-sales-report-new', 'method' => 'POST', 'id' => 'crm-deals-sales-report']) }}

                        <div class="row">

                            <div class="col-1-5">
                                <label for="" class="form-label">Owner</label>
                                <select class="form-control js-example-basic-single" name="company_id" id="company_id">
                                    <option value="">-Select-</option>
                                    @foreach ($company as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($ctrl_company == $value->id) selected @endif>{{ @$value->company_name }}
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




                            <div class="col-1-5 filter-field d-none">
                                <button type="submit" class="btn btn-success mt-4 rounded-0" id="btnSubmit">Filter</button>
                            </div>

                        </div>
                        {{ Form::close() }}


                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">



            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th>@lang('Brand')</th>
                            <th class="text-end">@lang('Deals')</th>
                            <th class="text-end">@lang('Forecast')</th>
                            <th class="text-end">@lang('On Process')</th>
                            <th class="text-end">@lang('Revenue')</th>
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
                        $total_on_process = 0; ?>
                        @if ($data != '0')
                            @foreach ($data as $value)
                                @php $toshow=0; @endphp
                                <?php
                                if ($value['role_id'] == 1 || $value['role_id'] == 2) {
                                    if ($value['dealcount'] == 0) {
                                        $toshow = 1;
                                    }
                                }
                                ?>

                                @if ($toshow == 0)
                                    <tr>
                                        <td><a class="text-dark">{{ @$value['title'] }}</a></td>

                                        <td class="text-end">
                                            {{ @$value['dealcount'] }}
                                        </td>
                                        <td class="text-end">{{ @$value['forcast'] }} <?php $total_forecast += $value['forcast']; ?></td>
                                        <td class="text-end">
                                            {{ @$value['on_process'] }}
                                            <?php $total_on_process += @$value['on_process']; ?>
                                        </td>
                                        <td class="text-end">
                                            @if (@$value['revenue'][0] == '0.00')
                                                <b>
                                                @else
                                                    <b class="text-danger font-bold">
                                            @endif
                                            {{ @$value['revenue'][0] }}
                                            </b>
                                            <?php $deal_count += @$value['dealcount']; ?>

                                            <?php $deal_value += @$value['revenue'][0]; ?>

                                            <?php $total_sales = @$value['revenue'][1];
                                            $top_target = @$value['target'];
                                            ?>
                                        </td>

                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <th></th>
                        <th class="text-end">{{ $deal_count }}</th>
                        <th class="text-end">{{ @App\SysHelper::com_curr_format($total_forecast, 2, '.', ',') }}</th>
                        <th class="text-end">{{ @App\SysHelper::com_curr_format($total_on_process, 2, '.', ',') }}</th>
                        <th class="text-end">{{ @App\SysHelper::com_curr_format($deal_value, 2, '.', ',') }}</th>
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

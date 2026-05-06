@extends('backEnd.newmasterpage')
@section('mainContent')


    <script>
        let isFullList = false;

        function list_style_new() {
            const leftNav = document.querySelector('.left-nav');
            const content = document.querySelector('.content-container');

            if (!isFullList) {
                // Switch to FULL LIST VIEW
                isFullList = true;

                leftNav.classList.remove('col-3');
                leftNav.classList.add('col-12');
                leftNav.style.width = '100%';

                content.classList.add('d-none');

                $('#long-list').removeClass('d-none');
                $('#short-list').addClass('d-none');

                $('#filters-long').removeClass('d-none');
                $('#filters-short').addClass('d-none');
            } else {
                // Switch to COMPACT VIEW
                isFullList = false;

                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                leftNav.style.width = '';

                content.classList.remove('d-none');

                $('#long-list').addClass('d-none');
                $('#short-list').removeClass('d-none');

                $('#filters-short').removeClass('d-none');
                $('#filters-long').addClass('d-none');
            }
        }


        //added ny kp
        function toggleLongFilters() {
            console.log("clicked");
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>


    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0">List Price
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
                            <li><a href="{{ url('stock-register') }}" class="dropdown-item">
                                    Stock Register</a></li>
                        </ul>
                    </div>


                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card" style="width: 100%">
                    <div class="card-body">

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'list-price', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                        <div class="row">

                            <div class="col-1-5 mb-2 ">
                                <label for="" class="form-label">To Date</label>
                                @php
                                    $formattedToDate = @$to_date
                                        ? \Carbon\Carbon::parse($to_date)->format('d/m/Y')
                                        : \Carbon\Carbon::now()->format('d/m/Y');
                                @endphp
                                <input class="form-control date-picker" id="to_date" type="text" name="to_date"
                                    value="{{ @$formattedToDate }}" autocomplete="off" required>
                            </div>

                            <div class="col-3 mb-2 ">
                                <label class="form-label">Find Part Number / Product Name / Description</label>

                                <input class="form-control" name="part_number" autocomplete="off" id="part_number1"
                                    value="{{ $r_part_number }}" />

                                <div id="part_number_list1">
                                </div>

                            </div>

                            <script>
                                $(document).ready(function() {

                                    $('#part_number1').keyup(function() {
                                        var query = $(this).val();
                                        if (query != '') {
                                            var _token = $('input[name="_token"]').val();
                                            $.ajax({
                                                url: "{{ route('autocomplete.fetch_product_partnumber') }}",
                                                method: "POST",
                                                data: {
                                                    query: query,
                                                    _token: _token
                                                },
                                                success: function(data) {
                                                    $('#part_number_list1').fadeIn();
                                                    $('#part_number_list1').html(data);
                                                }
                                            });
                                        }
                                    });

                                    $(document).on('click', 'li', function() {
                                        $('#part_number1').val($(this).text());
                                        $('#part_number_list1').fadeOut();
                                    });

                                    $(document).click(function(e) {
                                        if (!$(e.target).closest('#part_number1, #part_number_list1').length) {
                                            $('#part_number_list1').fadeOut();
                                        }
                                    });

                                });
                            </script>

                            <div class="col-1-5 mb-2 ">
                                <label for="" class="form-label">Brand</label>
                                <select class="form-control js-example-basic-single" name="brand">
                                    <option value="">-Select-</option>
                                    @foreach ($brand as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($r_brand == $value->id) selected @endif>{{ @$value->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Category</label>
                                <select class="form-control js-example-basic-single" name="category">
                                    <option value="">-Select-</option>
                                    @foreach ($category as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($r_category == $value->id) selected @endif>{{ @$value->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Sub Category</label>
                                <select class="form-control js-example-basic-single" name="sub_category">
                                    <option value="">-Select-</option>
                                    @foreach ($sub_category as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($r_sub_category == $value->id) selected @endif>
                                            {{ @$value->sub_category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">Qty</label>
                                <select class="form-control js-example-basic-single" name="qty">
                                    <option value="">-Select-</option>
                                    <option value="positive" @if ($r_qty == 'positive') selected @endif>Positive
                                    </option>
                                    <option value="negative" @if ($r_qty == 'negative') selected @endif>Negative
                                    </option>
                                    <option value="zero" @if ($r_qty == 'zero') selected @endif>Zero</option>
                                </select>
                            </div>



                            <div class="col-1 filter-field d-none">
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
                            <th style="width: 130px;">@lang('Part Number')</th>
                            <th style="width: 250px">@lang('Description')</th>
                            <th style="width: 100px;">@lang('Brand')</th>
                            <th style="width: 100px;">@lang('Category')</th>
                            <th style="width: 100px;">@lang('Sub Category')</th>
                            <th class="text-end" style="width: 50px;">@lang('Bal Qty')</th>

                            @if (Auth::user()->role_id != 5)
                                <th style="width: 100px;" class="text-end">@lang('Avg Price')</th>
                                <th style="width: 100px;" class="text-end">@lang('Last Purchase Price')</th>
                                <th style="width: 100px;" class="text-end">@lang('List Price')</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody>

                        @php
                            $count = 1;
                            $total_qty = 0;
                            $total_price = 0;
                            $total_value = 0;
                            $total_amount = 0;
                            $total_lp = 0;
                        @endphp

                        <?php
                        if ($r_qty == 'zero') {
                            $stocklist2 = $stocklist->where('balance_qty', 0);
                        } elseif ($r_qty == 'positive') {
                            $stocklist2 = $stocklist->where('balance_qty', '>', 0);
                        } elseif ($r_qty == 'negative') {
                            $stocklist2 = $stocklist->where('balance_qty', '<', 0);
                        } else {
                            $stocklist2 = $stocklist;
                        }
                        ?>

                        @foreach ($stocklist2 as $value)
                            <tr>
                                <td>
                                    <a href="{{ url('stock-ledger/' . $value->part_number) }}" target="_blank">
                                        {{ @$value->part_number }}</a>
                                </td>
                                <td>
                                    {{ $value->description }}
                                </td>
                                <td>{{ $value->brand }}</td>
                                <td>{{ $value->categoryname }}</td>
                                <td>{{ $value->subcategoryname }}</td>

                                @php
                                    $balance_qty = $value->balance_qty;
                                    $balance_qty += $stocklist_return->where('partno', $value->partno)->sum('qty');
                                @endphp

                                <td class="text-end">{{ $balance_qty }}</td>

                                @if (Auth::user()->role_id != 5)
                                    <td class="text-end">
                                        {{ @App\SysHelper::com_curr_format($value->avg_price, 2, '.', ',') }}</td>

                                    <td class="text-end">
                                        {{ @App\SysHelper::com_curr_format($value->lp_price, 2, '.', ',') }}</td>
                                    <td class="text-end">

                                        @if ($value->avg_price > $value->lp_price)
                                            {{ @App\SysHelper::com_curr_format(($value->avg_price * 103) / 100, 2, '.', ',') }}
                                            @php $total_lp += $value->avg_price*103/100; @endphp
                                        @else
                                            {{ @App\SysHelper::com_curr_format(($value->lp_price * 103) / 100, 2, '.', ',') }}
                                            @php $total_lp += $value->lp_price*103/100; @endphp
                                        @endif

                                    </td>
                                @endif

                                @php
                                    $total_qty += $balance_qty;
                                    $total_price += $value->avg_price;
                                    $total_amount += $value->lp_price;
                                @endphp
                            </tr>
                        @endforeach
                    </tbody>

                    <?php try{ ?>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-end">{{ $total_qty }}</th>
                            @if (Auth::user()->role_id != 5)
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($total_price, 2, '.', ',') }}
                                </th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}
                                </th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($total_lp, 2, '.', ',') }}</th>
                            @endif
                        </tr>
                    </tfoot>
                    <?php }catch (\Exception $e) { } ?>

                </table>
               
            </div>
        </div>
    </aside>








    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

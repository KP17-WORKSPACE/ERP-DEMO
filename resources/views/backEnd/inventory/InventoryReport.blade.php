@extends('backEnd.newmasterpage')
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
@endpush
@section('mainContent')

    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        Inventory Report
                    </h4>
                    <div class="purchase-order-content-header-right">
                        {{-- <a class="btn btn-light" href="{{ url('chartofaccounts-import') }}"> Account Import</a> --}}

                        {{-- <a class="btn btn-light" href="{{url('payment-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Payment
                    </a> --}}
                        {{-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Search
                    </button> --}}
                        <button type="button" class="btn btn-light" id="exportInventoryReport" title="Export to Excel">
                            <i class="ico icon-outline-export text-success"></i> Export
                        </button>
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'inventory-report', 'method' => 'POST', 'id' => 'inventory-report']) }}
                        <div class="row">
                            <div class="col-md-2 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('From Date')</label>
                                            <input class="form-control date-picker" id="from_date" type="text"
                                                name="from_date"
                                                value="{{ $from_date ? \Carbon\Carbon::parse($from_date)->format('d/m/Y') : '' }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('To Date')</label>
                                            <input class="form-control date-picker" id="to_date" type="text"
                                                name="to_date"
                                                value="{{ $to_date ? \Carbon\Carbon::parse($to_date)->format('d/m/Y') : '' }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Part Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="part_number"
                                    value="{{ $r_part_number }}">
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Brand</label>
                                <select class="form-control js-example-basic-single" name="brand">
                                    <option value="">-Select-</option>
                                    @foreach ($brand as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($r_brand == $value->id) selected @endif>{{ @$value->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Category</label>
                                <select class="form-control js-example-basic-single" name="category">
                                    <option value="">-Select-</option>
                                    @foreach ($category as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($r_category == $value->id) selected @endif>{{ @$value->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <label for="" class="form-check-label">Sub Category</label>
                                <select class="form-control js-example-basic-single" name="sub_category">
                                    <option value="">-Select-</option>
                                    @foreach ($sub_category as $value)
                                        <option value="{{ @$value->id }}"
                                            @if ($r_sub_category == $value->id) selected @endif>
                                            {{ @$value->sub_category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1 mb-2">
                                <label for="" class="form-check-label">Qty</label>
                                <select class="form-control" name="qty">
                                    <option value="">-Select-</option>
                                    <option value="positive" @if ($r_qty == 'positive') selected @endif>Positive
                                    </option>
                                    <option value="negative" @if ($r_qty == 'negative') selected @endif>Negative
                                    </option>
                                    <option value="zero" @if ($r_qty == 'zero') selected @endif>Zero</option>
                                </select>
                            </div>
                            <div class="col-md-1 mb-2">
                                <label for="" class="form-check-label">Ageing</label>
                                <select class="form-control" name="ageing">
                                    <option value="">-Select-</option>
                                    <option value="0" @if (@$ctrl_ageing == '0') selected @endif>
                                        < 0 </option>
                                    <option value="30" @if (@$ctrl_ageing == '30') selected @endif> 0-30 </option>
                                    <option value="60" @if (@$ctrl_ageing == '60') selected @endif> 31-60
                                    </option>
                                    <option value="90" @if (@$ctrl_ageing == '90') selected @endif> 61-90
                                    </option>
                                    <option value="120" @if (@$ctrl_ageing == '120') selected @endif> 91-120
                                    </option>
                                    <option value="121" @if (@$ctrl_ageing == '121') selected @endif> > 120
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('Doc Number')</label>
                                            <input class="form-control" id="doc_number" type="text" name="doc_number"
                                                value="{{ @$ctrl_doc_number }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('Deal Id')</label>
                                            <input class="form-control" id="deal_id" type="text" name="deal_id"
                                                value="{{ @$ctrl_deal_id }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('Account Name')</label>
                                            <select class="form-control js-example-basic-single" name="supplier"
                                                id="supplier">
                                                <option value=""></option>
                                                @foreach ($supplier_list as $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-20">
                                <div class="no-gutters input-right-icon">
                                    <div class="col">
                                        <div class="input-effect">
                                            <label>@lang('Sales Person')</label>
                                            <select class="form-control js-example-basic-single" name="sales_person"
                                                id="sales_person">
                                                <option value=""></option>
                                                @foreach ($sales_person_list as $value)
                                                    <option value="{{ @$value->user_id }}">{{ @$value->full_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1"><br />
                                <button type="submit" class="btn btn-light" id="btnSubmit">
                                    <i class="ico icon-outline-magnifer text-success"></i> Search
                                </button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        <div class="table-responsive">
<table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">

                            <thead>
                                <tr>
                                    <th style="width:50px">Sl No</th>
                                    <th style="width:100px;">@lang('Part Number')</th>
                                    <th style="width:100px;">@lang('Description')</th>
                                    <th style="width:100px;">@lang('Brand')</th>
                                    <th style="width:100px;">@lang('Category')</th>
                                    <th>In Qty</th>
                                    <th class="text-end">In Rate</th>
                                    <th>Out Qty</th>
                                    <th class="text-end">Out Rate</th>
                                    <th>@lang('Bal Qty')</th>
                                    <th class="text-end">@lang('Avg Rate')</th>
                                    <th class="">@lang('Sales Person')</th>
                                    <th class="text-end">@lang('Profit')</th>
                                    <th class="text-end">@lang('Profit%')</th>

                                    <th style="width:70px;" class="text-end">@lang('< 0')</th>
                                    <th style="width:70px;" class="text-end">@lang('0-30')</th>
                                    <th style="width:70px;" class="text-end">@lang('31-60')</th>
                                    <th style="width:70px;" class="text-end">@lang('61-90')</th>
                                    <th style="width:70px;" class="text-end">@lang('91-120')</th>
                                    <th style="width:70px;" class="text-end">@lang('> 120')</th>
                                    <th style="width:70px;" class="text-end">@lang('Av Moving')</th>
                                </tr>
                            </thead>

                            <tbody>
                                <script>
                                    function set_top(id, inqty, inrate, outqty, outrate, salesp, profit, profitp, m0, m30, m60, m90, m120, m121, mov) {
                                        $('#in_qty_' + id).text(inqty);
                                        $('#in_rate_' + id).text(inrate);
                                        $('#out_qty_' + id).text(outqty);
                                        $('#in_rate_' + id).text(outrate);
                                        $('#sales_person_' + id).text(salesp);
                                        $('#profit_' + id).text(profit);
                                        $('#profit_p_' + id).text(profitp + '%');
                                        $('#m0_' + id).text(m0);
                                        $('#m30_' + id).text(m30);
                                        $('#m60_' + id).text(m60);
                                        $('#m90_' + id).text(m90);
                                        $('#m120_' + id).text(m120);
                                        $('#m121_' + id).text(m121);
                                        $('#mov_' + id).text(mov);
                                    }
                                </script>

                                @php
                                    $count = 1;
                                    $total_qty = 0;
                                    $total_price = 0;
                                    $total_value = 0;
                                    $total_amount = 0;
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
                                
                                @foreach ($stocklist2 as $ind => $value)
                                    <?php $group_qty = App\SysHelper::get_group_qty($value->partno); ?>
                                    @if (($group_qty != 0 && $value->type == 2) || $value->type == 1)
                                        <tr onclick="expand_sub_ledger({{ $value->partno }})"
                                            >
                                            <td>&nbsp; {{ $ind + 1 }}</td>
                                            <td><a href="{{ url('stock-ledger/' . $value->part_number) }}"
                                                    target="_blank">{{ @$value->part_number }}</a></td>
                                            <td>
                                               
                                                    {{ $value->description }}
                                            </td>
                                            <td>{{ $value->brand }}</td>
                                            <td>{{ $value->categoryname }} - {{ $value->subcategoryname }}</td>
                                            <td><label id="in_qty_{{ $value->partno }}">0</label></td>
                                            <td class="text-end"><label id="in_rate_{{ $value->partno }}">0.00</label></td>
                                            <td><label id="out_qty_{{ $value->partno }}">0</label></td>
                                            <td class="text-end"><label id="out_rate_{{ $value->partno }}">0.00</label></td>
                                            <?php
                                            $balance_qty = $value->balance_qty;
                                            $balance_qty += $stocklist_return->where('partno', $value->partno)->sum('qty');
                                            ?>
                                            
                                            <td>{{ $balance_qty }}</td>
                                            <?php $avg = App\SysHelper::get_avg_price($value->partno, $to_date); ?>
                                            <td class="text-end">
                                                {{ @App\SysHelper::com_curr_format($avg, 2, '.', ',') }}</td>
                                            <td class="text-end"><label id="sales_person_{{ $value->partno }}"></label>
                                            </td>
                                            <?php
                                            $total_price += $avg;
                                            if ($balance_qty > 0) {
                                                $total_amount += $avg * $balance_qty;
                                            }
                                            $total_qty += $balance_qty;
                                            ?>
                                            <td class="text-end"><label id="profit_{{ $value->partno }}">0.00</label>
                                            </td>
                                            <td class="text-end"><label id="profit_p_{{ $value->partno }}">0%</label>
                                            </td>

                                            <td class="text-end"><label id="m0_{{ $value->partno }}">0</label></td>
                                            <td class="text-end"><label id="m30_{{ $value->partno }}">0</label></td>
                                            <td class="text-end"><label id="m60_{{ $value->partno }}">0</label></td>
                                            <td class="text-end"><label id="m90_{{ $value->partno }}">0</label></td>
                                            <td class="text-end"><label id="m120_{{ $value->partno }}">0</label></td>
                                            <td class="text-end"><label id="m121_{{ $value->partno }}">0</label></td>
                                            <td class="text-end"><label id="mov_{{ $value->partno }}">0</label></td>

                                        </tr>
                                        {{-- sub section ledger det start --}}
                                        <tr id="sub_ledger_{{ $value->partno }}" style="display: none;">
                                            <td colspan="21">
                                                <style>
                                                    #long-list2 th
                                                     {
                                                      background-color: #eaf1fb;
                                                    }
                                                </style>
                                                <table class="table table-bordered table-striped"  id="long-list2" style="table-layout: fixed;width:100%">
                                                    <tr>
                                                        <th  style="width:100px;">@lang('Part Number')</th>
                                                        <th >@lang('Doc Date')</th>
                                                        <th >@lang('Doc No')</th>
                                                        <th >@lang('Ref No')</th>
                                                        <th >@lang('Deal Id')</th>
                                                        <th>@lang('Account Name')</th>
                                                        <th  class="text-end">@lang('In Qty')</th>
                                                        <th  class="text-end">@lang('In Rate')
                                                        </th>
                                                        <th class="text-end">@lang('Out Qty')</th>
                                                        <th  class="text-end">@lang('Out Rate')
                                                        </th>
                                                        <th  class="text-end">@lang('Bal Qty')</th>
                                                        <th style="width:70px;" class="text-end">@lang('Avg Rate')
                                                        </th>
                                                        <th class="text-end">@lang('Sales Person')
                                                        </th>
                                                        <th style="width:70px;" class="text-end">@lang('Profit')
                                                        </th>
                                                        <th style="width:70px;" class="text-end">@lang('Profit%')
                                                        </th>
                                                        <th style="width:70px;" class="text-end">@lang('< 0')</th>
                                                        <th style="width:70px;" class="text-end">@lang('0-30')</th>
                                                        <th style="width:70px;" class="text-end">@lang('31-60')</th>
                                                        <th style="width:70px;" class="text-end">@lang('61-90')</th>
                                                        <th style="width:70px;" class="text-end">@lang('91-120')</th>
                                                        <th style="width:70px;" class="text-end">@lang('> 120')</th>
                                                        <th style="width:70px;" class="text-end">@lang('Av Mov')</th>
                                                    </tr>

                                                    <?php $count = 1;
                                                    $total_qty_in = 0;
                                                    $total_price_in = 0;
                                                    $total_qty_out = 0;
                                                    $total_price_out = 0;
                                                    $total_value = 0;
                                                    $total_profit = 0;
                                                    $total_profit_p = 0;
                                                    $price_in_qty_in = 0;
                                                    $qty_in = 0;
                                                    $bal_qty = 0;
                                                    $avg_qty = 0;
                                                    $avg_rate = 0;
                                                    $max_name = '';
                                                    $profit = 0;
                                                    $adj_qty = 0;
                                                    $m0 = 0;
                                                    $m30 = 0;
                                                    $m60 = 0;
                                                    $m90 = 0;
                                                    $m120 = 0;
                                                    $m121 = 0;
                                                    $d = 1;
                                                    $da = 0;
                                                    $m = 0;
                                                    $mov = 0;
                                                    $tot_mov = 0;
                                                    $d2 = 0;

                                                
                                                    
                                                    $list = $stockledgerlist->where('partno', $value->partno); ?>


                                                    {{-- foreach sub section start --}}
                                                    @if (count($list) > 0)
                                                        @foreach ($list as $index => $li)
                                                            <tr>
                                                                <td>{{ $value->part_number }}</td>
                                                                <td>{{ date('d-M-Y', strtotime(@$li->doc_date)) }}</td>
                                                                <td>
                                                                    @if (substr($li->doc_number, 0, 2) == 'PO')
                                                                        <a href="{{ url('get-url-purchase-order/' . $li->doc_number) }}"
                                                                            target="_blank">{{ @$li->doc_number }}</a>
                                                                    @elseif(substr($li->doc_number, 0, 2) == 'GR')
                                                                        <a href="{{ url('get-url-purchase-grn/' . $li->doc_number) }}"
                                                                            target="_blank">{{ @$li->doc_number }}</a>
                                                                    @elseif(substr($li->doc_number, 0, 2) == 'PI')
                                                                        <a href="{{ url('get-url-purchase-invoice/' . $li->doc_number) }}"
                                                                            target="_blank">{{ @$li->doc_number }}</a>
                                                                    @elseif(substr($li->doc_number, 0, 2) == 'PR')
                                                                        <a href="{{ url('get-url-purchase-return/' . $li->doc_number) }}"
                                                                            target="_blank">{{ @$li->doc_number }}</a>
                                                                    @elseif(substr($li->doc_number, 0, 2) == 'SI')
                                                                        <a href="{{ url('get-url-sales-invoice/' . $li->doc_number) }}"
                                                                            target="_blank">{{ @$li->doc_number }}</a>
                                                                    @elseif(substr($li->doc_number, 0, 2) == 'DL')
                                                                        <a href="{{ url('get-url-delivery-note/' . $li->doc_number) }}"
                                                                            target="_blank">{{ @$li->doc_number }}</a>
                                                                    @elseif(substr($li->doc_number, 0, 2) == 'DN')
                                                                        <a href="{{ url('get-url-delivery-note/' . $li->doc_number) }}"
                                                                            target="_blank">{{ @$li->doc_number }}</a>
                                                                    @elseif(substr($li->doc_number, 0, 2) == 'SR')
                                                                        <a href="{{ url('get-url-sales-return/' . $li->doc_number) }}"
                                                                            target="_blank">{{ @$li->doc_number }}</a>
                                                                    @else
                                                                        {{ @$li->doc_number }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (substr($li->refno, 0, 2) == 'PO')
                                                                        <a href="{{ url('get-url-purchase-order/' . $li->refno) }}"
                                                                            target="_blank">{{ @$li->refno }}</a>
                                                                    @elseif(substr($li->refno, 0, 2) == 'GR')
                                                                        <a href="{{ url('get-url-purchase-grn/' . $li->refno) }}"
                                                                            target="_blank">{{ @$li->refno }}</a>
                                                                    @elseif(substr($li->refno, 0, 2) == 'PI')
                                                                        <a href="{{ url('get-url-purchase-invoice/' . $li->refno) }}"
                                                                            target="_blank">{{ @$li->refno }}</a>
                                                                    @elseif(substr($li->refno, 0, 2) == 'PR')
                                                                        <a href="{{ url('get-url-purchase-return/' . $li->refno) }}"
                                                                            target="_blank">{{ @$li->refno }}</a>
                                                                    @elseif(substr($li->refno, 0, 2) == 'SI')
                                                                        <a href="{{ url('get-url-sales-invoice/' . $li->refno) }}"
                                                                            target="_blank">{{ @$li->refno }}</a>
                                                                    @elseif(substr($li->refno, 0, 2) == 'DL')
                                                                        <a href="{{ url('get-url-delivery-note/' . $li->refno) }}"
                                                                            target="_blank">{{ @$li->refno }}</a>
                                                                    @elseif(substr($li->refno, 0, 2) == 'SR')
                                                                        <a href="{{ url('get-url-sales-return/' . $li->refno) }}"
                                                                            target="_blank">{{ @$li->refno }}</a>
                                                                    @else
                                                                        {{ @$li->refno }}
                                                                    @endif
                                                                </td>


                                                                <td>
                                                                    @if ($li->deal_id != 0)
                                                                        <a href="{{ url('get-url-deal-track/' . $li->deal_code) }}"
                                                                            target="_blank">{{ $li->deal_code }}</a>
                                                                    @else
                                                                        Without
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (@$li->account_name == '' && substr($li->doc_number, 0, 2) == 'SH')
                                                                        Shortage Stock
                                                                    @elseif (@$li->account_name == '' && substr($li->doc_number, 0, 2) == 'EX')
                                                                        Excess Stock
                                                                    @else
                                                                        {{ @$li->account_name }}
                                                                    @endif
                                                                </td>
                                                                <td class="text-end">{{ $li->qty_in }}</td>
                                                                <td class="text-end">
                                                                    {{ @App\SysHelper::com_curr_format($li->price_in, 2, '.', ',') }}
                                                                </td>
                                                                <td class="text-end">{{ $li->qty_out }}</td>
                                                                <td class="text-end">
                                                                    {{ @App\SysHelper::com_curr_format($li->price_out, 2, '.', ',') }}
                                                                </td>
                                                                <td class="text-end">{{ $li->bal_qty }}</td>
                                                                <td class="text-end">{{ $li->avg_rate }}</td>
                                                                <td class="text-end">{{ $li->full_name }}</td>
                                                                <td class="text-end">{{ $li->profit }}</td>
                                                                <td class="text-end">{{ $li->profit_p }}%</td>

                                                                <td class="text-end">{{ $li->days_m0 }}</td>
                                                                <td class="text-end">{{ $li->days_m30 }}</td>
                                                                <td class="text-end">{{ $li->days_m60 }}</td>
                                                                <td class="text-end">{{ $li->days_m90 }}</td>
                                                                <td class="text-end">{{ $li->days_m120 }}</td>
                                                                <td class="text-end">{{ $li->days_m121 }}</td>

                                                                <?php
                                                    if($li->days_m0 != 0 || $li->days_m30 != 0 || $li->days_m60 != 0 || $li->days_m90 != 0 || $li->days_m120 != 0 || $li->days_m121 != 0){
                                                        $da = $li->days_m0 + $li->days_m30 + $li->days_m60 + $li->days_m90 + $li->days_m120 + $li->days_m121;
                                                        $m += $da;

                                                    ?><td class="text-end">
                                                                    {{ round($m / $d, 2) }} <?php $mov += round($m / $d, 2); ?></td>
                                                                <?php $d=$d+1; $d2++;
                                                    } else {
                                                    ?><td class="text-end">--</td>
                                                                <?php
                                                    }
                                                    ?>
                                                            </tr>
                                                            <?php
                                                            $total_qty_in += $li->qty_in;
                                                            $total_price_in += $li->price_in;
                                                            $total_qty_out += $li->qty_out;
                                                            $total_price_out += $li->price_out;
                                                            $total_value += $li->price_in * $li->qty_in;
                                                            $max_name = $li->full_name;
                                                            $total_profit += $li->profit;
                                                            $total_profit_p += $li->profit_p;
                                                            $m0 += $li->days_m0;
                                                            $m30 += $li->days_m30;
                                                            $m60 += $li->days_m60;
                                                            $m90 += $li->days_m90;
                                                            $m120 += $li->days_m120;
                                                            $m121 += $li->days_m121;
                                                            if ($d2 != 0) {
                                                                $tot_mov = round($mov / $d2, 2);
                                                            }
                                                            
                                                            ?>
                                                            <script>
                                                                set_top(
                                                                    {{ $value->partno }},
                                                                    {{ json_encode($total_qty_in) }},
                                                                    {{ json_encode($total_price_in) }},
                                                                    {{ json_encode($total_qty_out) }},
                                                                    {{ json_encode($total_price_out) }},
                                                                    {!! json_encode($max_name) !!},
                                                                    {{ json_encode($total_profit) }},
                                                                    {{ json_encode(round($total_profit_p, 2)) }},
                                                                    {{ json_encode($m0) }},
                                                                    {{ json_encode($m30) }},
                                                                    {{ json_encode($m60) }},
                                                                    {{ json_encode($m90) }},
                                                                    {{ json_encode($m120) }},
                                                                    {{ json_encode($m121) }},
                                                                    {{ json_encode($tot_mov) }}
                                                                );
                                                            </script>
                                                        @endforeach
                                                        <tr>
                                                            <th class="text-end" colspan="6"> Total</th>
                                                            <th class="text-end">{{ $list->sum('qty_in') }}</th>
                                                            <th class="text-end">{{ $list->last()->price_in }}</th>
                                                            <th class="text-end">{{ $list->sum('qty_out') }}</th>
                                                            <th class="text-end">{{ $list->last()->price_out }}</th>
                                                            <th class="text-end">{{ $list->last()->bal_qty }}</th>
                                                            <th class="text-end">{{ $list->last()->avg_rate }}</th>
                                                            <th class="text-end"></th>
                                                            <th class="text-end">{{ $list->sum('profit') }}</th>
                                                            <th class="text-end">{{ $list->sum('profit_p') }}</th>
                                                            <th class="text-end">{{ $list->sum('days_m0') }}</th>
                                                            <th class="text-end">{{ $list->sum('days_m30') }}</th>
                                                            <th class="text-end">{{ $list->sum('days_m60') }}</th>
                                                            <th class="text-end">{{ $list->sum('days_m90') }}</th>
                                                            <th class="text-end">{{ $list->sum('days_m120') }}</th>
                                                            <th class="text-end">{{ $list->sum('days_m121') }}</th>
                                                            <th class="text-end">{{ $tot_mov }}</th>
                                                        </tr>
                                                    @endif
                                                    {{-- foreach sub section end --}}



                                                </table>
                                            </td>
                                        </tr>
                                        {{-- sub section ledger det end --}}
                                    @endif
                                @endforeach

                                <script>
                                    function expand_sub_ledger(id) {
                                        $('#sub_ledger_' + id).toggle(); // Toggles visibility
                                    }
                                </script>
                            </tbody>
                            <tfoot style="display: none;">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>{{ $total_qty }}</th>
                                    <th class="text-end"></th>
                                    <th class="text-end"></th>
                                    <th class="text-end"></th>
                                    <th class="text-end"></th>
                                    <th class="text-end">
                                        {{ @App\SysHelper::com_curr_format($total_amount, 2, '.', ',') }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                        
                    </div>
                </div>



            </div>
        </div>
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

<script>
$(document).ready(function () {
    $('#exportInventoryReport').on('click', function () {
        var companyName = @json(@App\SysCompany::find(session('logged_session_data.company_id') ?? '')->trade_name ?? '');
        var fromDate    = @json($from_date ?? '');
        var toDate      = @json($to_date ?? '');
        var N = 22; // total columns

        var workbook  = new ExcelJS.Workbook();
        var ws        = workbook.addWorksheet('Inventory Report');

        ws.columns = [
            { width: 16 }, // 1  Part Number
            { width: 20 }, // 2  Description / Doc Date
            { width: 16 }, // 3  Brand / Doc No
            { width: 20 }, // 4  Category / Ref No
            { width: 14 }, // 5  Deal Id
            { width: 22 }, // 6  Account Name
            { width: 10 }, // 7  In Qty
            { width: 12 }, // 8  In Rate
            { width: 10 }, // 9  Out Qty
            { width: 12 }, // 10 Out Rate
            { width: 10 }, // 11 Bal Qty
            { width: 12 }, // 12 Avg Rate
            { width: 18 }, // 13 Sales Person
            { width: 12 }, // 14 Profit
            { width: 10 }, // 15 Profit%
            { width: 8  }, // 16 < 0
            { width: 8  }, // 17 0-30
            { width: 8  }, // 18 31-60
            { width: 8  }, // 19 61-90
            { width: 8  }, // 20 91-120
            { width: 8  }, // 21 > 120
            { width: 10 }, // 22 Av Mov
        ];

        var rowNum = 0;

        function addMetaRow(text, fontSize, bold) {
            rowNum++;
            var r = ws.addRow([text]);
            ws.mergeCells(rowNum, 1, rowNum, N);
            r.getCell(1).value = text;
            r.getCell(1).font      = { bold: bold || false, size: fontSize || 11 };
            r.getCell(1).alignment = { horizontal: 'center', vertical: 'middle' };
            r.height = (fontSize || 11) + 8;
        }

        // ── Title section ──────────────────────────────────────────
        addMetaRow(companyName, 14, true);
        addMetaRow('Inventory Report', 12, true);
        var dateParts = [];
        if (fromDate) dateParts.push('From: ' + fromDate);
        if (toDate)   dateParts.push('To: ' + toDate);
        if (dateParts.length) addMetaRow(dateParts.join('   '), 10, false);
        rowNum++; ws.addRow([]);

        // ── Column header row ──────────────────────────────────────
        var headers = [
            'Part Number', 'Doc Date', 'Doc No', 'Ref No',
            'Deal Id', 'Account Name',
            'In Qty', 'In Rate', 'Out Qty', 'Out Rate',
            'Bal Qty', 'Avg Rate', 'Sales Person',
            'Profit', 'Profit%',
            '< 0', '0-30', '31-60', '61-90', '91-120', '> 120', 'Av Mov'
        ];
        rowNum++;
        var hdrRow = ws.addRow(headers);
        hdrRow.height = 22;
        hdrRow.eachCell({ includeEmpty: true }, function (cell) {
            cell.font      = { bold: true, color: { argb: 'FFFFFFFF' }, size: 11 };
            cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF2D5496' } };
            cell.alignment = { horizontal: 'center', vertical: 'middle' };
            cell.border    = {
                top:    { style: 'thin', color: { argb: 'FFB8C4D8' } },
                left:   { style: 'thin', color: { argb: 'FFB8C4D8' } },
                bottom: { style: 'thin', color: { argb: 'FFB8C4D8' } },
                right:  { style: 'thin', color: { argb: 'FFB8C4D8' } }
            };
        });

        function clean(el) {
            return $(el).text().trim().replace(/\s+/g, ' ');
        }

        // ── Iterate main rows ──────────────────────────────────────
        $('#long-list tbody tr').each(function () {
            var $row   = $(this);
            var onclick = $row.attr('onclick') || '';
            var match   = onclick.match(/expand_sub_ledger\((\d+)\)/);

            if (!match) return; // skip sub-ledger container rows

            var partno = match[1];
            var $c     = $row.find('td');

            // ── Summary row ───────────────────────────────────────
            var summaryData = [
                clean($c.eq(1)),  // Part Number
                clean($c.eq(2)),  // Description
                clean($c.eq(3)),  // Brand
                clean($c.eq(4)),  // Category
                '',               // Deal Id (not on summary)
                '',               // Account Name (not on summary)
                clean($c.eq(5)),  // In Qty  (label)
                clean($c.eq(6)),  // In Rate (label)
                clean($c.eq(7)),  // Out Qty (label)
                clean($c.eq(8)),  // Out Rate (label)
                clean($c.eq(9)),  // Bal Qty
                clean($c.eq(10)), // Avg Rate
                clean($c.eq(11)), // Sales Person (label)
                clean($c.eq(12)), // Profit (label)
                clean($c.eq(13)), // Profit%
                clean($c.eq(14)), // < 0
                clean($c.eq(15)), // 0-30
                clean($c.eq(16)), // 31-60
                clean($c.eq(17)), // 61-90
                clean($c.eq(18)), // 91-120
                clean($c.eq(19)), // > 120
                clean($c.eq(20)), // Av Moving
            ];

            rowNum++;
            var sumRow = ws.addRow(summaryData);
            sumRow.height = 18;
            sumRow.eachCell({ includeEmpty: true }, function (cell) {
                cell.font      = { bold: true, color: { argb: 'FFFFFFFF' }, size: 10 };
                cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF3A6BAD' } };
                cell.alignment = { vertical: 'middle' };
                cell.border    = {
                    top:    { style: 'thin', color: { argb: 'FF7DA4CF' } },
                    left:   { style: 'thin', color: { argb: 'FF7DA4CF' } },
                    bottom: { style: 'thin', color: { argb: 'FF7DA4CF' } },
                    right:  { style: 'thin', color: { argb: 'FF7DA4CF' } }
                };
            });

            // ── Detail rows from sub-ledger ───────────────────────
            var $subLedger = $('#sub_ledger_' + partno);
            if (!$subLedger.length) return;

            var altRow = false;
            $subLedger.find('table tbody tr').each(function () {
                var $dr  = $(this);
                var $ths = $dr.find('th');
                var $tds = $dr.find('td');

                // Determine row type
                var isColumnHeader = $ths.length > 0 && $tds.length === 0
                                     && !$ths.first().attr('colspan');
                var isTotalRow     = $ths.length > 0 && $tds.length === 0
                                     && $ths.first().attr('colspan') === '6';

                if (isColumnHeader) return; // skip inner table column headers

                var cells;

                if (isTotalRow) {
                    cells = [
                        clean($ths.eq(0)),  // "Total" (colspan 6)
                        '', '', '', '', '',
                        clean($ths.eq(1)),  // In Qty
                        clean($ths.eq(2)),  // In Rate
                        clean($ths.eq(3)),  // Out Qty
                        clean($ths.eq(4)),  // Out Rate
                        clean($ths.eq(5)),  // Bal Qty
                        clean($ths.eq(6)),  // Avg Rate
                        clean($ths.eq(7)),  // Sales Person (empty)
                        clean($ths.eq(8)),  // Profit
                        clean($ths.eq(9)),  // Profit%
                        clean($ths.eq(10)), // < 0
                        clean($ths.eq(11)), // 0-30
                        clean($ths.eq(12)), // 31-60
                        clean($ths.eq(13)), // 61-90
                        clean($ths.eq(14)), // 91-120
                        clean($ths.eq(15)), // > 120
                        clean($ths.eq(16)), // Av Mov
                    ];
                } else {
                    cells = [
                        clean($tds.eq(0)),  // Part Number
                        clean($tds.eq(1)),  // Doc Date
                        clean($tds.eq(2)),  // Doc No
                        clean($tds.eq(3)),  // Ref No
                        clean($tds.eq(4)),  // Deal Id
                        clean($tds.eq(5)),  // Account Name
                        clean($tds.eq(6)),  // In Qty
                        clean($tds.eq(7)),  // In Rate
                        clean($tds.eq(8)),  // Out Qty
                        clean($tds.eq(9)),  // Out Rate
                        clean($tds.eq(10)), // Bal Qty
                        clean($tds.eq(11)), // Avg Rate
                        clean($tds.eq(12)), // Sales Person
                        clean($tds.eq(13)), // Profit
                        clean($tds.eq(14)), // Profit%
                        clean($tds.eq(15)), // < 0
                        clean($tds.eq(16)), // 0-30
                        clean($tds.eq(17)), // 31-60
                        clean($tds.eq(18)), // 61-90
                        clean($tds.eq(19)), // 91-120
                        clean($tds.eq(20)), // > 120
                        clean($tds.eq(21)), // Av Mov
                    ];
                }

                rowNum++;
                var detailRow = ws.addRow(cells);
                detailRow.height = 16;

                if (isTotalRow) {
                    detailRow.eachCell({ includeEmpty: true }, function (cell) {
                        cell.font      = { bold: true, size: 10 };
                        cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFF0D0' } };
                        cell.alignment = { horizontal: 'right', vertical: 'middle' };
                        cell.border    = {
                            top:    { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            left:   { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            bottom: { style: 'thin', color: { argb: 'FFCCCCCC' } },
                            right:  { style: 'thin', color: { argb: 'FFCCCCCC' } }
                        };
                    });
                    detailRow.getCell(1).alignment = { horizontal: 'left', vertical: 'middle' };
                } else {
                    var bg = altRow ? 'FFEFF5FF' : 'FFFFFFFF';
                    altRow = !altRow;
                    detailRow.eachCell({ includeEmpty: true }, function (cell) {
                        cell.font      = { size: 10 };
                        cell.fill      = { type: 'pattern', pattern: 'solid', fgColor: { argb: bg } };
                        cell.alignment = { vertical: 'middle' };
                        cell.border    = {
                            top:    { style: 'thin', color: { argb: 'FFDDDDDD' } },
                            left:   { style: 'thin', color: { argb: 'FFDDDDDD' } },
                            bottom: { style: 'thin', color: { argb: 'FFDDDDDD' } },
                            right:  { style: 'thin', color: { argb: 'FFDDDDDD' } }
                        };
                    });
                }
            });

            // Blank row separator between items
            rowNum++;
            ws.addRow([]);
        });

        // ── Download ───────────────────────────────────────────────
        workbook.xlsx.writeBuffer().then(function (buffer) {
            var blob = new Blob([buffer], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            function pad(n) { return n < 10 ? '0' + n : n; }
            var d  = new Date();
            var fn = 'inventory_report_' + pad(d.getDate()) + '-' + pad(d.getMonth() + 1) + '-' + d.getFullYear() + '.xlsx';
            saveAs(blob, fn);
        });
    });
});
</script>

@endsection

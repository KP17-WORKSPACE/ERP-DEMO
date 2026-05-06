@extends('backEnd.newmasterpage')
@section('mainContent')

    <?php try { ?>
    <aside class="left-nav col-12" id="leftSidebar">
        <div class="long-list">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h4 class="mb-0">Inventory Brand Report — Line Detail</h4>
                <div class="d-flex gap-2 align-items-center">
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('inventory-brand-report') }}"
                       class="btn btn-light">
                        <i class="ico icon-outline-arrow-left"></i> Back
                    </a>
                    <a href="{{ url('inventory-brand-report') }}" class="btn btn-light">New report</a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body py-2">
                    <div class="row small">
                        <div class="col-md-4"><strong>Part number:</strong> {{ $itemRow->part_number }}</div>
                        <div class="col-md-8"><strong>Description:</strong> {{ $itemRow->description }}</div>
                        <div class="col-md-3"><strong>Brand:</strong> {{ $itemRow->brand }}</div>
                        <div class="col-md-3"><strong>Category:</strong> {{ $itemRow->categoryname }}</div>
                        <div class="col-md-3"><strong>Sub category:</strong> {{ $itemRow->subcategoryname }}</div>
                        <div class="col-md-3"><strong>Period:</strong> {{ @App\SysHelper::normalizeToDmy($from_date) }}
                            — {{ @App\SysHelper::normalizeToDmy($to_date) }}</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered" style="table-layout: fixed; width: 100%;">
                    <thead>
                        <tr>
                            <th style="width:50px;">@lang('No')</th>
                            <th style="width:100px;">@lang('Part Number')</th>
                            <th style="width:70px;">@lang('Deal Id')</th>
                            <th style="width:100px;">@lang('Doc No')</th>
                            <th style="width:100px;">@lang('Doc Date')</th>
                            <th>@lang('Account Name')</th>
                            <th style="width:70px;" class="text-center">@lang('Qty')</th>
                            <th style="width:100px;" class="text-end">@lang('Rate')</th>
                            <th style="width:100px;" class="text-end">@lang('Value')</th>
                            <th style="width:100px;" class="text-end">@lang('Discount')</th>
                            <th style="width:120px;" class="text-end">@lang('Taxable Amt')</th>
                            <th style="width:100px;" class="text-end">@lang('Vat Amt')</th>
                            <th style="width:100px;" class="text-end">@lang('Total Amt')</th>
                            <th style="width:100px;" class="text-end">@lang('Sales Person')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lines as $li)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div style="width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $itemRow->part_number }}</div>
                                </td>
                                <td>
                                    @if($li->deal_id != 0)
                                        <a href="{{ url('get-url-deal-track/'.$li->deal_code) }}" target="_blank">{{ $li->deal_code }}</a>
                                    @else
                                        Without
                                    @endif
                                </td>
                                <td>
                                    @if(substr($li->doc_number, 0, 2)=="PO")
                                        <a href="{{ url('get-url-purchase-order/'.$li->doc_number) }}" target="_blank">{{ @$li->doc_number }}</a>
                                    @elseif(substr($li->doc_number, 0, 2)=="GR")
                                        <a href="{{ url('get-url-purchase-grn/'.$li->doc_number) }}" target="_blank">{{ @$li->doc_number }}</a>
                                    @elseif(substr($li->doc_number, 0, 2)=="PI")
                                        <a href="{{ url('get-url-purchase-invoice/'.$li->doc_number) }}" target="_blank">{{ @$li->doc_number }}</a>
                                    @elseif(substr($li->doc_number, 0, 2)=="PR")
                                        <a href="{{ url('get-url-purchase-return/'.$li->doc_number) }}" target="_blank">{{ @$li->doc_number }}</a>
                                    @elseif(substr($li->doc_number, 0, 2)=="SI")
                                        <a href="{{ url('get-url-sales-invoice/'.$li->doc_number) }}" target="_blank">{{ @$li->doc_number }}</a>
                                    @elseif(substr($li->doc_number, 0, 2)=="DL")
                                        <a href="{{ url('get-url-delivery-note/'.$li->doc_number) }}" target="_blank">{{ @$li->doc_number }}</a>
                                    @elseif(substr($li->doc_number, 0, 2)=="DN")
                                        <a href="{{ url('get-url-delivery-note/'.$li->doc_number) }}" target="_blank">{{ @$li->doc_number }}</a>
                                    @elseif(substr($li->doc_number, 0, 2)=="SR")
                                        <a href="{{ url('get-url-sales-return/'.$li->doc_number) }}" target="_blank">{{ @$li->doc_number }}</a>
                                    @else
                                        {{ @$li->doc_number }}
                                    @endif
                                </td>
                                <td>{{ @App\SysHelper::normalizeToDmy(@$li->doc_date) }}</td>
                                <td>
                                    @if (@$li->account_name == "" && substr($li->doc_number, 0, 2)=="SH")
                                        Shortage Stock
                                    @elseif (@$li->account_name == "" && substr($li->doc_number, 0, 2)=="EX")
                                        Excess Stock
                                    @else
                                        <div style="width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ @$li->account_name }}</div>
                                    @endif
                                </td>
                                <td class="text-center">{{ @$li->qty }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->unitprice,2,'.',',') }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->value,2,'.',',') }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->discount,2,'.',',') }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->taxableamount,2,'.',',') }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$li->vatamount,2,'.',',') }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format($li->taxableamount + $li->vatamount,2,'.',',') }}</td>
                                <td class="text-end">{{ $li->full_name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center">No invoice lines for this item in the selected period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($lines->count() > 0)
                        <tfoot>
                            <tr>
                                <th class="text-end" colspan="6">Total</th>
                                <th class="text-center">{{ $total_qty }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($avg_rate_total,2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($total_value,2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($total_discount,2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($total_taxableamount,2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($total_vatamount,2,'.',',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($total_amount,2,'.',',') }}</th>
                                <th class="text-end"></th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </aside>
    <?php } catch (\Exception $e) { ?> {{ $e }} <?php } ?>
@endsection

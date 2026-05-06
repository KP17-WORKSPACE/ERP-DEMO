@extends('backEnd.newmasterpage')
@section('mainContent')

@php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<style>
.border { border: solid 1px #e3e6f0; }
</style>

    <?php try { ?>
<div class="content-container col-12">
    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
            <div class="purchase-order-content-header" id="card-1">
                <h4 class="purchase-order-content-header-left">
                    Cash Book
                </h4>
                <div class="purchase-order-content-header-right">
                
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ url('receipt-add/cashbook') }}"><i class="ico icon-outline-bill-list text-success"></i> Receipts</a></li>
                    <li><a class="dropdown-item" href="{{ url('payment-add/cashbook') }}"><i class="ico icon-outline-bill-list text-success"></i> Payments</a></li>
                    <li><a class="dropdown-item" href="{{ url('journalvoucher-add/cashbook') }}"><i class="ico icon-outline-bill-list text-success"></i> Journal Voucher</a></li>
                </ul>
            </div>
                    {{-- <a class="btn btn-light" href="{{url('payment-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Payment
                    </a> --}}
                    {{-- <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addChequeModal">
                        <i class="ico icon-outline-add-square text-success"></i> Search
                    </button> --}}
                </div>
            </div>

            
            <div class="card mb-3" id="card-2">
                <div class="card-body">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'cashbook', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <div class="row">
                <div class="col-md-3 mb-20">
                    <div class="input-effect">
                        <label>@lang('Account')</label>
                        <select class="form-control js-example-basic-single" name="account_id" id="account_id" required>
                            @foreach ($accounts as $val)
                                <option value="{{ @$val->id }}" @if(isset($account_id)) @if(@$account_id == @$val->id) selected @endif @endif >{{ @$val->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-1 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label>@lang('From Date')</label>
                                <input class="form-control date-picker" id="from_date" type="text" name="from_date" value="{{ @$from_date ? @App\SysHelper::normalizeToDmy(@$from_date) : '' }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label>@lang('To Date')</label>
                                <input class="form-control date-picker" id="to_date" type="text" name="to_date" value="{{ @$to_date ? @App\SysHelper::normalizeToDmy(@$to_date) : '' }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                            <div class="col-md-1 mb-2">
                                <label for="" class="form-check-label">Filter By</label>
                                <select onchange="this.form.submit()" class="form-control js-example-basic-single" name="filter_by" id="filter_by">
                                    <option value="" >-Select-</option>
                                    <option value="this_month" @if(@$filter_by=="this_month") selected @endif>This Month</option>
                                    <option value="today" @if(@$filter_by=="today") selected @endif>Today</option>
                                    <option value="this_week" @if(@$filter_by=="this_week") selected @endif>This Week</option>
                                    <option value="last_week" @if(@$filter_by=="last_week") selected @endif>Last Week</option>
                                    <option value="last_month" @if(@$filter_by=="last_month") selected @endif>Last Month</option>
                                    <option value="this_quarter" @if(@$filter_by=="this_quarter") selected @endif>This Quarter</option>
                                    <option value="pre_quarter" @if(@$filter_by=="pre_quarter") selected @endif>Previous Quarter</option>
                                    <option value="this_year" @if(@$filter_by=="this_year") selected @endif>This Year</option>
                                    <option value="last_year" @if(@$filter_by=="last_year") selected @endif>Last Year</option>
                                </select>
                            </div>
                            <script>
                                function set_filter(){
                                    if($('#from_date').val()!="" || $('#to_date').val() != "")
                                    {
                                        $('#filter_by').val('')
                                    }
                                }
                            </script>
                        <div class="col-md-1" style="margin-top:1.4rem">
                            <button class="btn btn-light" id="btnSubmit">
                                <i class="ico icon-outline-minimalistic-magnifer text-success" style="font-size: 18px;"></i> Filter
                            </button>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label for="" class="form-check-label">Search in List</label>
                            <input type="text" id="tableSearch" class="form-control mb-2" placeholder="">
                        </div>
            </div>
        {{ Form::close() }}
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-body p-0">
                        <table id="long-list" class="table table-hover data-table table-fixed-header" style="table-layout: fixed;width:100%">

                          <thead>
                            <tr>
                                <th class=" text-center" width="7%">Date</th>
                                <th class=" text-start" width="7%">Doc No</th>
                                <th class=" " width="20%">Particular</th>
                                <th class=" text-end" width="7%">Debit</th>
                                <th class=" text-end" width="7%">Credit</th>
                                <th class=" text-end" width="7%">Balance</th>
                                <th class=" text-center">Narration</th>
                            </tr>
                          </thead>                          
                          <tbody style="max-height: calc(100vh - 240px);">
                            
                            <?php $tot = 0; $total=0; $total_dr=0; $total_cr=0; ?>
                            @if (count($data)>0)
                            @foreach ($data as $dt)
                            
                            <?php try { ?>
                                @if($dt!="")
                            <tr>
                                <td class="text-center">{{ date('d/m/Y', strtotime($dt["transaction_date"])) }}</td>
                                <td class="text-start">
                                    @if(substr($dt["transaction_no"], 0, 2)=="JV")
                                        <a href="{{url('journalvoucher/' . $dt['transaction_id'])}}" target="_blank">{{ $dt["transaction_no"] }}</a>
                                    @elseif(substr($dt["transaction_no"], 0, 2)=="CR")
                                        <a href="{{url('receipt/' . $dt['transaction_id'])}}" target="_blank">{{ $dt["transaction_no"] }}</a>
                                    @elseif(substr($dt["transaction_no"], 0, 2)=="BR")
                                        <a href="{{url('receipt/' . $dt['transaction_id'])}}" target="_blank">{{ $dt["transaction_no"] }}</a>
                                    @elseif(substr($dt["transaction_no"], 0, 2)=="CP")
                                        <a href="{{url('payment/' . $dt['transaction_id'])}}" target="_blank">{{ $dt["transaction_no"] }}</a>
                                    @elseif(substr($dt["transaction_no"], 0, 2)=="BP")
                                        <a href="{{url('payment/' . $dt['transaction_id'])}}" target="_blank">{{ $dt["transaction_no"] }}</a>
                                    @else
                                        {{ $dt["transaction_no"] }}
                                    @endif
                                </td>
                                <td class="">{{ $dt["account_name"] }}</td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format($dt["debit_amount"], 2, '.', ',') }} @php $total_dr += $dt["debit_amount"]; @endphp </td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format($dt["credit_amount"], 2, '.', ',') }} @php $total_cr += $dt["credit_amount"]; @endphp </td>
                                <td class="text-end {{ $tot < 0 ? 'text-danger' : '' }}">
                                    <?php $tot -= $dt["credit_amount"] ?>
                                    <?php $tot += $dt["debit_amount"] ?>
                                    {{ @App\SysHelper::com_curr_format($tot, 2, '.', ',') }}
                                </td>
                                <td class=""> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $dt["remarks"] }}</td>
                            </tr>
                            @endif
                            <?php }catch (\Exception $e) {  } ?>

                            @endforeach
                            @endif
                                <tr><td colspan="7" class="text-center"  style="height:19px;"></td></tr>

                          </tbody>
                          <tfoot>
                            <tr>
                                <th class=""></th>
                                <th class=""></th>
                                <th class=""></th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($total_dr, 2, '.', ',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($total_cr, 2, '.', ',') }}</th>
                                <th class="text-end">{{ @App\SysHelper::com_curr_format($total_dr - $total_cr, 2, '.', ',') }}</th>
                                <th class=""></th>
                            </tr>
                          </tfoot>
                        </table>
                </div>
            </div>



        </div>
    </div>
</div>

        <script>
$(document).ready(function() {
    function setManualWidths() {
        var $table = $('.table-fixed-header');
        var $theadTh = $table.find('thead th');
        var $tfootTh = $table.find('tfoot th');
        // Match px widths to <th> definitions in main Cash Book table
        // Date(7%), Doc No(7%), Particular(20%), Debit(7%), Credit(7%), Balance(7%), Narration(auto)
        var columnWidthsPx = [90, 90, 220, 90, 90, 90, 350];

        $theadTh.each(function(i) {
            var w = columnWidthsPx[i];
            if (w) {
                $(this).css('width', w + 'px');
                $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', w + 'px');
                $tfootTh.eq(i).css('width', w + 'px');
            } else {
                $(this).css('width', 'auto');
                $table.find('tbody td:nth-child(' + (i + 1) + ')').css('width', 'auto');
                $tfootTh.eq(i).css('width', 'auto');
            }
        });
    }

    setManualWidths();
    $(window).on('resize', setManualWidths);
});
</script>


    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
    
@endsection

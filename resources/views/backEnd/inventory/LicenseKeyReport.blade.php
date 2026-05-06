@extends('backEnd.newmasterpage')
@section('mainContent')
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-12" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">License Key Report
                </h4>
                <div class="search-filter-container mb-0">



                    <a target="_blank" href="{{ url('stock-ledger') }}" class="btn btn-light" id="list_style_button">
                        Stock Ledger
                    </a>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    $('#toggle-all').click(function() {
                        const isExpanded = $('.show-hide').first().hasClass('expanded');

                        $('.show-hide').toggleClass('expanded', !isExpanded);

                        $(this).text(isExpanded ? 'Show All' : 'Show Less');
                    });
                });
            </script>

            <div class="search-filter-container mt-1 mb-4 border">

                <div class="card" style="width:100%">
                    <div class="card-body">

                        {{ Form::open([
                            'class' => 'form-horizontal',
                            'files' => true,
                            'url' => 'license-key-report',
                            'method' => 'POST',
                            'enctype' => 'multipart/form-data',
                        ]) }}
                        <div class="row">

                            <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

                            <div class="col-1-5 mb-2 ">
                                @php

                                    if (!empty($from_date)) {
                                        try {
                                            $from_date = \Carbon\Carbon::parse($from_date)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            $from_date = '';
                                        }
                                    }

                                    if (!empty($to_date)) {
                                        try {
                                            $to_date = \Carbon\Carbon::parse($to_date)->format('d/m/Y');
                                        } catch (\Exception $e) {
                                            $to_date = '';
                                        }
                                    }
                                @endphp
                                <label for="" class="form-label">From Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="from_date"
                                    id="from_date" value="{{ $from_date }}">
                            </div>

                            <div class="col-1-5 mb-2">
                                <label for="" class="form-label">To Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off" name="to_date"
                                    id="to_date" value="{{ $to_date }}">
                            </div>




                            <div class="col-3 mb-2 ">
                                <label class="form-label">Part Number</label>

                                <input class="form-control" type="hidden" id="part_number_array">
                                <input class="form-control" type="text" id="part_number" name="part_number"
                                    value="{{ $str_partno }}" autocomplete="off">
                                <div id="part_number_list">
                                </div>
                                <script>
                                    $(document).ready(function() {

                                        $('#part_number').keyup(function() {
                                            var query = $(this).val();
                                            if (query != '') {
                                                var _token = $('input[name="_token"]').val();
                                                $.ajax({
                                                    url: "{{ route('autocomplete.fetch_product_partnumber_withcoma') }}",
                                                    method: "POST",
                                                    data: {
                                                        query: query,
                                                        _token: _token
                                                    },
                                                    success: function(data) {
                                                        $('#part_number_list').fadeIn();
                                                        $('#part_number_list').html(data);
                                                    }
                                                });
                                            }
                                        });

                                        $(document).on('click', 'li', function() {
                                            $('#part_number').val($(this).text());
                                            $('#part_number_list').fadeOut();
                                        });

                                        $(document).click(function(e) {
                                            if (!$(e.target).closest('#part_number, #part_number_list').length) {
                                                $('#part_number_list').fadeOut();
                                            }
                                        });

                                        $(document).on('click', 'li', function() {
                                            var xval = $('#part_number_array').val();
                                            var nval = $(this).text();
                                            $('#part_number').val(nval);
                                            if (xval == "") {
                                                $('#part_number_array').val(nval);
                                            } else {
                                                $('#part_number_array').val(xval + ',' + nval);
                                            }

                                            $('#part_number').val($('#part_number_array').val());

                                            $('#part_number_list').fadeOut();
                                        });

                                    });
                                </script>

                            </div>


                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success mt-4 rounded-0" id="btnSubmit">Filter</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">

            <div class="table-responsive">

                <script>
                    function show_hide_srl_no(id) {
                        if ($('.all_srl_no_' + id).css('display') == 'none') {
                            $('.all_srl_no_' + id).css("display", "block");
                            $('#atag_srl_no' + id).text("Hide All SrlNo");
                        } else {
                            $('.all_srl_no_' + id).css("display", "none");
                            $('#atag_srl_no' + id).text("View All SrlNo");
                        }
                    }
                </script>

                 <?php $i=0; ?>
                @if (count($stocklist)>0)
                @foreach ($stocklist as $list)
                <div class="bg-success text-left fonmt-weight-bold text-white">&nbsp;&nbsp;{{ $partnolist[$i] }}
                <?php try{ ?>
                    <span style="padding-left: 50px;">{{ $stocklist[$i][0]->productdet->description }}</span>
                <?php }catch (\Exception $e) { } ?>
                <button id="toggle-all" class="btn-sm btn-danger pt-0 pb-0" style="float: right;">Show All</button>
                </div>
                <table class="table table-hover" id="long-list" width="100%" cellspacing="0">
                    <thead>                        
                        <tr>
                            <th style="width:100px; color: #000000;">@lang('Doc Date')</th>
                            <th style="width:100px; color: #000000;">@lang('Doc No')</th>
                            <th style="width:100px; color: #000000;">@lang('Ref No')</th>
                            <th style="width:70px; color: #000000;">@lang('Deal Id')</th>
                            <th style="width:170px; color: #000000;">@lang('Account Name')</th>
                            <th style="width:170px; color: #000000;">@lang('Reference Name')</th>
                            <th style="width:70px; background: #c6e0b4; color: #000000;" class="text-center">@lang('In Qty')</th>
                            <th style="width:100px; background: #c6e0b4; color: #000000;" class="text-center">@lang('Key')</th>
                            <th style="width:70px; background: #c6e0b4; color: #000000;" class="text-center">@lang('Serial No')</th>
                            <th style="width:100px; background: #8ea9db; color: #000000;" class="text-center">@lang('Out Qty')</th>
                            <th style="width:100px; background: #8ea9db; color: #000000;" class="text-center">@lang('Key')</th>
                            <th style="width:70px; background: #8ea9db; color: #000000;" class="text-center">@lang('Serial No')</th>
                            <th style="width:100px; background: #f4b084; color: #000000;" class="text-center">@lang('Balance Qty')</th>
                            <th style="width:100px; background: #f4b084; color: #000000;" class="text-center">@lang('Key')</th>
                            <th style="width:70px; color: #000000;" class="text-center">@lang('Exp Date')</th>
                            {{-- <th style="width:100px;" class="text-end"><a class="btn btn-info p-0 m-0 pl-1 pr-1" id="atag_srl_no{{ $i }}" onclick="show_hide_srl_no({{ $i }})">View All SrlNo</a></th> --}}
                        </tr>

                        
                        @php
                        $opb = @App\SysHelper::get_stock_ledger_opening_stock($partnolist[$i],$opb_date,$company_id);
                        $key1 = "";
                        $key2 = "";
                        $key3 = "";
                        $key3_array=[];
                    @endphp
                    {{-- <tr>
                        <td>{{ date('d-M-Y', strtotime(@$opb_date)) }}</td>
                        <td colspan="3"></td>
                        <td>Opening Balance</td>
                        <td colspan="5"></td>
                        <td class="text-center">{{ $opb[0] }}</td>
                        <td class="text-end">{{ $opb[1] }}</td>
                        <td></td>
                    </tr> --}}

                        
                    </thead>
                @if (count($list)>0)
                    <tbody>
                        @php $count =1; $total_qty_in=0; $total_price_in=0; $total_qty_out=0; $total_price_out=0; $total_value=0; $price_in_qty_in=0; $qty_in=0; $bal_qty=$opb[0]; $avg_qty=0; $avg_rate=0; @endphp

                        </tr>
                                @foreach($list as $value)

                                <tr>
                                    <td>{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</td>
                                    <td>
                                        @if(substr($value->doc_number, 0, 2)=="PO")
                                            <a href="{{url('get-url-purchase-order/'.$value->doc_number)}}" target="_blank">{{@$value->doc_number}}</a>
                                        @elseif(substr($value->doc_number, 0, 2)=="GR")
                                            <a href="{{url('get-url-purchase-grn/'.$value->doc_number)}}" target="_blank">{{@$value->doc_number}}</a>                                        
                                        @elseif(substr($value->doc_number, 0, 2)=="PI")
                                            <a href="{{url('get-url-purchase-invoice/'.$value->doc_number)}}" target="_blank">{{@$value->doc_number}}</a>
                                        @elseif(substr($value->doc_number, 0, 2)=="PR")
                                            <a href="{{url('get-url-purchase-return/'.$value->doc_number)}}" target="_blank">{{@$value->doc_number}}</a>
                                        @elseif(substr($value->doc_number, 0, 2)=="SI")
                                            <a href="{{url('get-url-sales-invoice/'.$value->doc_number)}}" target="_blank">{{@$value->doc_number}}</a>
                                        @elseif(substr($value->doc_number, 0, 2)=="DL")
                                            <a href="{{url('get-url-delivery-note/'.$value->doc_number)}}" target="_blank">{{@$value->doc_number}}</a>
                                            @elseif(substr($value->doc_number, 0, 2)=="DN")
                                                <a href="{{url('get-url-delivery-note/'.$value->doc_number)}}" target="_blank">{{@$value->doc_number}}</a>
                                        @elseif(substr($value->doc_number, 0, 2)=="SR")
                                            <a href="{{url('get-url-sales-return/'.$value->doc_number)}}" target="_blank">{{@$value->doc_number}}</a>
                                        @else
                                            {{@$value->doc_number}}
                                        @endif
                                    </td>
                                    <td>
                                        @if(substr($value->refno, 0, 2)=="PO")
                                            <a href="{{url('get-url-purchase-order/'.$value->refno)}}" target="_blank">{{@$value->refno}}</a>
                                        @elseif(substr($value->refno, 0, 2)=="GR")
                                            <a href="{{url('get-url-purchase-grn/'.$value->refno)}}" target="_blank">{{@$value->refno}}</a>                                        
                                        @elseif(substr($value->refno, 0, 2)=="PI")
                                            <a href="{{url('get-url-purchase-invoice/'.$value->refno)}}" target="_blank">{{@$value->refno}}</a>
                                        @elseif(substr($value->refno, 0, 2)=="PR")
                                            <a href="{{url('get-url-purchase-return/'.$value->refno)}}" target="_blank">{{@$value->refno}}</a>
                                        @elseif(substr($value->refno, 0, 2)=="SI")
                                            <a href="{{url('get-url-sales-invoice/'.$value->refno)}}" target="_blank">{{@$value->refno}}</a>
                                        @elseif(substr($value->refno, 0, 2)=="DL")
                                            <a href="{{url('get-url-delivery-note/'.$value->refno)}}" target="_blank">{{@$value->refno}}</a>
                                        @elseif(substr($value->refno, 0, 2)=="SR")
                                            <a href="{{url('get-url-sales-return/'.$value->refno)}}" target="_blank">{{@$value->refno}}</a>
                                        @else
                                            {{@$value->refno}}
                                        @endif
                                    </td>


                                    <td>@if($value->deal_id != 0)
                                        @php 
                                        $deal_code = @App\SysHelper::get_code_from_dealid($value->deal_id);
                                        @endphp
                                        <a href="{{url('get-url-deal-track/'.$deal_code)}}" target="_blank">{{ $deal_code }}</a> @else Without @endif
                                    </td>
                                    <td>
                                        @if (@$value->accountname->account_name == "" && substr($value->doc_number, 0, 2)=="SH")
                                            Shortage Stock
                                        @elseif (@$value->accountname->account_name == "" && substr($value->doc_number, 0, 2)=="EX")
                                            Excess Stock
                                        @else
                                            {{@$value->accountname->account_name}}
                                        @endif
                                    </td>
                                    <td>
                                        {{ @$value->grn_reference }}
                                        {{ @$value->dln_reference }}
                                        {{ @$value->srt_reference }}
                                        {{ @$value->prt_reference }}
                                    </td>
                                    <td class="text-center" style=" background: #c6e0b4; color: #000000; border-bottom: solid 1px #e3e6f0;">{{$value->qty_in}}</td>
                                    <?php
                                        if(substr($value->doc_number, 0, 2)=="GR"){
                                            $key1 = $license_key->where('trn_doc_no', $value->doc_number)->where('status',1)->pluck('license_key')->implode(' | ');
                                            $srlno = $grn_srl_list->where('doc_number', $value->doc_number)->pluck('srl_no')->implode(', ');
                                        }
                                        elseif(substr($value->doc_number, 0, 2)=="OP"){
                                            $key1 = $license_key->where('trn_doc_no', $value->doc_number)->where('status',1)->pluck('license_key')->implode(' | ');
                                            $srlno = "";
                                        }
                                        elseif(substr($value->doc_number, 0, 2)=="SR"){
                                            $key1 = $license_key->where('trn_doc_no', $value->doc_number)->where('status',1)->pluck('license_key')->implode(' | ');
                                            $srlno = $sr_srl_list->where('doc_number', $value->doc_number)->pluck('srl_no')->implode(', ');
                                        }
                                        else{
                                            $key1 = "";
                                            $srlno="";
                                        }
                                    ?>
                                    <td class="text-center" style=" background: #c6e0b4; color: #000000; border-bottom: solid 1px #e3e6f0;"><div class="show-hide">{{ @$key1 }}</div></td>
                                    <td class="text-center" style=" background: #c6e0b4; color: #000000; border-bottom: solid 1px #e3e6f0;"><div class="show-hide">{{ $srlno }}</div></td>

                                    <?php
                                        if($bal_qty <= 0){ $qty_in=0; $price_in_qty_in = 0; }                                    
                                        if(str_contains($value->doc_number,'SRT')){
                                            $qty_in += $value->qty_in;
                                            $bal_qty += $value->qty_in;
                                            $bal_qty -= $value->qty_out;
                                        
                                            $price_in_qty_in += $value->price_in*$value->qty_in;
                                            $qty_in += $value->qty_in;
                                            $bal_qty += $value->qty_in;
                                            $bal_qty -= $value->qty_out;                                        
                                        }
                                    ?>
                                    <td class="text-center" style=" background: #8ea9db; color: #000000; border-bottom: solid 1px #e3e6f0;">{{ $value->qty_out }}</td>
                                    <?php
                                        if(substr($value->doc_number, 0, 2)=="PR"){
                                            $key2 = $license_key->where('trn_doc_no', $value->doc_number)->pluck('license_key')->implode(' | ');
                                            $srlno = $pr_srl_list->where('doc_number', $value->doc_number)->pluck('srl_no')->implode(', ');
                                        }
                                        elseif(substr($value->doc_number, 0, 2)=="DN"){
                                            $key2 = $license_key->where('trn_doc_no', $value->doc_number)->pluck('license_key')->implode(' | ');
                                            $srlno = $dn_srl_list->where('doc_number', $value->doc_number)->pluck('srl_no')->implode(', ');
                                        }
                                        else{
                                            $key2 = "";
                                            $srlno = "";
                                        }
                                    ?>
                                    <td class="text-center" style=" background: #8ea9db; color: #000000; border-bottom: solid 1px #e3e6f0;"><div class="show-hide">{{ @$key2 }}</div></td>
                                    <td class="text-center" style=" background: #8ea9db; color: #000000; border-bottom: solid 1px #e3e6f0;"><div class="show-hide">{{ $srlno }}</div></td>
                                    <?php
                                        if (!empty($key1)) {
                                            $key1_array = array_map('trim', explode('|', $key1));
                                            $key3_array = array_merge($key3_array, $key1_array);
                                        }

                                        if (!empty($key2)) {
                                            $key2_array = array_map('trim', explode('|', $key2));
                                            $key3_array = array_diff($key3_array, $key2_array);
                                        }

                                        $key3 = implode(' | ', $key3_array);
                                    ?>
                                    <td class="text-center" style=" background: #f4b084; color: #000000; border-bottom: solid 1px #e3e6f0;">{{count($key3_array) }}</td>
                                    <td class="text-center" style=" background: #f4b084; color: #000000; border-bottom: solid 1px #e3e6f0;"><div class="show-hide">{{ $key3 }}</div></td>

                                    <?php                                     
                                        if(substr($value->doc_number, 0, 2)=="GR"){
                                            $exp = $license->where('grn_doc_number', $value->doc_number)->where('status',1)->min('exp_date');
                                            $exp = date('d/m/Y', strtotime(@$exp));
                                        }
                                        elseif(substr($value->doc_number, 0, 2)=="OP"){
                                            $exp = $license->where('ops_doc_number', $value->doc_number)->where('status',1)->min('exp_date');
                                            $exp = date('d/m/Y', strtotime(@$exp));
                                        }
                                        elseif(substr($value->doc_number, 0, 2)=="SR"){
                                            $exp = $license->where('sr_doc_number', $value->doc_number)->where('status',1)->min('exp_date');
                                            $exp = date('d/m/Y', strtotime(@$exp));
                                        }
                                        else{
                                            //$exp = "";
                                        }
                                        
                                    ?>
                                    <td class="text-center">{{ @$exp }}</td>
                                    <td class="text-end" style="display: none;">
                                        @if($value->slno !="")
                                        <a class="btn-sm btn-success pt-t pb-1 pl-2 pr-2" data-bs-toggle="modal" data-bs-target="#exampleModalCenter{{ $value->doc_number }}">View SrlNo</a>@endif
                                        <div class="all_srl_no_{{ $i }}" style="display: none;">{{ str_replace(",",", ",$value->slno)}}</div></td>

                                    <div class="modal fade" id="exampleModalCenter{{ $value->doc_number }}" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h5 class="modal-title" id="exampleModalLongTitle">{{ $partnolist[$i] }} | {{ $value->doc_number }}</h5>
                                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body" style="line-height: 25px;">
                                                {{ str_replace(","," | ",$value->slno)}}
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
                                          </div>
                                        </div>
                                      </div>

                                </tr>
                                @php
                                $total_qty_in += $value->qty_in;
                                $total_price_in += $value->price_in;
                                $total_qty_out += $value->qty_out;
                                $total_price_out += $value->price_out;
                                $total_value += $value->price_in*$value->qty_in;
                                @endphp
                                @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-center">{{ $total_qty_in }}</th>
                            <th class="text-end"></th>
                            <th class="text-center">{{ $total_qty_out }}</th>
                            <th class="text-end"></th>
                            <th class="text-center">{{ $bal_qty }}</th>
                            <th class="text-end"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>                
                @else
                <tbody>
                    <tr class="bg-light">
                        <th colspan="14" class="text-center text-danger"> No Data Found! </th>
                    </tr>
                </tbody>
                @endif
                </table>                
                <?php $i++; ?>
                @endforeach                    
                @endif
            </div>
        </div>
    </aside>







    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

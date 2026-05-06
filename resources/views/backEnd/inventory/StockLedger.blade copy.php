@extends('backEnd.masterpage')
@section('mainContent')

<?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>
  <style type="text/css">
   .box{
    width:600px;
    margin:0 auto;
   }
  </style>

  <?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Stock Ledger</h2>
            <span class="page-label">Home - Stock Ledger</span>
        </div>
        <div>
            <a href="{{ url('license-key-report') }}" type="button" class="btn btn-primary"><i class="fa fa-list"></i> License Key Report</a>
            {{--  <a href="{{ url('item-store') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> Add Stock</a>  --}}
            {{--  <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>  --}}
        </div>
    </div>
    <div class="card p-4 mb-2">
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'stock-ledger', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
            <div class="row">
                <div class="col-md-2 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label>@lang('From Date')</label>
                                <input class="form-control" id="from_date" type="date" name="from_date" value="{{ @$from_date }}" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label>@lang('To Date')</label>
                                <input class="form-control" id="to_date" type="date" name="to_date" value="{{ @$to_date }}" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-20">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label>@lang('Part Number')</label>
                                
                                <input class="form-control" type="hidden" id="part_number_array">
                                <input class="form-control" type="text" id="part_number" name="part_number" value="{{ $str_partno }}" autocomplete="off">
                                            <div id="part_number_list">
                                            </div>                            
                                            <script>
                                                $(document).ready(function(){
                                                
                                                 $('#part_number').keyup(function(){ 
                                                        var query = $(this).val();
                                                        if(query != '')
                                                        {
                                                         var _token = $('input[name="_token"]').val();
                                                         $.ajax({
                                                          url:"{{ route('autocomplete.fetch_product_partnumber_withcoma') }}",
                                                          method:"POST",
                                                          data:{query:query, _token:_token},
                                                          success:function(data){
                                                           $('#part_number_list').fadeIn();  
                                                                    $('#part_number_list').html(data);
                                                          }
                                                         });
                                                        }
                                                    });
                                                
                                                    $(document).on('click', 'li', function(){  
                                                        var xval = $('#part_number_array').val();
                                                        var nval = $(this).text();
                                                        $('#part_number').val(nval);
                                                        if(xval == ""){
                                                        $('#part_number_array').val(nval);}
                                                        else{
                                                        $('#part_number_array').val(xval+','+nval);}

                                                        $('#part_number').val($('#part_number_array').val());
                                                        
                                                        $('#part_number_list').fadeOut();
                                                    });  
                                                
                                                });
                                                </script>


                                {{--  <select class="form-control js-example-basic-single" name="part_number[]" id="part_number" multiple required>
                                    <option value=""></option>
                                    @foreach ($items as $key => $value)
                                        <option value="{{ @$value->part_number }}"
                                            @if ($part_number != "")
                                            @foreach($part_number as $part_no)
                                            @if($part_no == $value->part_number) selected @endif
                                            @endforeach                                                
                                            @endif
                                            >{{ @$value->part_number }}</option>
                                    @endforeach
                                </select>  --}}

                                <?php /*
                                <input class="form-control" id="part_number2" type="text" name="part_number2" value="{{ @$part_number }}" autocomplete="off" required>
                                <div id="part_number_list">
                                </div>                            
                                <script>
                                    $(document).ready(function(){
                                    
                                     $('#part_number').keyup(function(){ 
                                            var query = $(this).val();
                                            if(query != '')
                                            {
                                             var _token = $('input[name="_token"]').val();
                                             $.ajax({
                                              url:"{{ route('autocomplete.fetch') }}",
                                              method:"POST",
                                              data:{query:query, _token:_token},
                                              success:function(data){
                                               $('#part_number_list').fadeIn();  
                                                        $('#part_number_list').html(data);
                                              }
                                             });
                                            }
                                        });
                                    
                                        $(document).on('click', 'li', function(){  
                                            $('#part_number').val($(this).text());  
                                            $('#part_number_list').fadeOut();  
                                        });  
                                    
                                    });
                                    </script>
                                    */ ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 mt-4">
                    <div class="input-effect" id="sectionSubgroupDiv">
                        <button class="btn btn-primary">
                            <span class="ti-search"></span>
                            @lang('Search')
                        </button>
                    </div>
                </div>
            </div>
        {{ Form::close() }}
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                
                <script>
                    function show_hide_srl_no(id){
                        if ($('.all_srl_no_'+id).css('display') == 'none') {
                            $('.all_srl_no_'+id).css("display", "block");
                            $('#atag_srl_no'+id).text("Hide All SrlNo");
                        } else {
                            $('.all_srl_no_'+id).css("display", "none");
                            $('#atag_srl_no'+id).text("View All SrlNo");
                        }
                    }
                </script>

                <?php $i=0; ?>
                @if (count($stocklist)>0)
                @foreach ($stocklist as $list)
                <div class="bg-primary text-left fonmt-weight-bold text-white">&nbsp;&nbsp;{{ $partnolist[$i] }}
                <?php try{ ?>
                    <span style="padding-left: 50px;">{{ $stocklist[$i][0]->productdet->description }}</span>
                <?php }catch (\Exception $e) { } ?>
                </div>
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead>                        
                        <tr>
                            <th style="width:100px;">@lang('Doc Date')</th>
                            <th style="width:100px;">@lang('Doc No')</th>
                            <th style="width:100px;">@lang('Ref No')</th>
                            <th style="width:70px;">@lang('Deal Id')</th>
                            <th>@lang('Account Name')</th>
                            <th>@lang('Reference Name')</th>
                            <th style="width:70px;" class="text-center">@lang('In Qty')</th>
                            <th style="width:100px;" class="text-right">@lang('In Rate')</th>
                            <th style="width:70px;" class="text-center">@lang('Out Qty')</th>
                            <th style="width:100px;" class="text-right">@lang('Out Rate')</th>
                            <th style="width:70px;" class="text-center">@lang('Bal Qty')</th>
                            <th style="width:100px;" class="text-right">@lang('Avg Rate')</th>
                            <th style="width:100px;" class="text-right"><a class="btn btn-info p-0 m-0 pl-1 pr-1" id="atag_srl_no{{ $i }}" onclick="show_hide_srl_no({{ $i }})">View All SrlNo</a></th>
                        </tr>

                        
                        @php
                        $opb = @App\SysHelper::get_stock_ledger_opening_stock($partnolist[$i],$opb_date,$company_id)
                    @endphp
                    <tr>
                        <td>{{ date('d-M-Y', strtotime(@$opb_date)) }}</td>
                        <td colspan="3"></td>
                        <td>Opening Balance</td>
                        <td colspan="5"></td>
                        <td class="text-center">{{ $opb[0] }}</td>
                        <td class="text-right">{{ $opb[1] }}</td>
                        <td></td>
                    </tr>

                        
                    </thead>
                @if (count($list)>0)
                    <tbody>
                        @php $count =1; $total_qty_in=0; $total_price_in=0; $total_qty_out=0; $total_price_out=0; $total_value=0; $price_in_qty_in=0; $qty_in=0; $bal_qty=$opb[0]; $avg_qty=0; $avg_rate=0; @endphp
                                
                        


                        </tr>
                                @foreach($list as $value)

                                <tr>
                                    <td>{{ date('d-M-Y', strtotime(@$value->doc_date)) }}</td>
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
                                    <td class="text-center">{{$value->qty_in}}</td>
                                    <td class="text-right">{{ @App\SysHelper::com_curr_format($value->price_in,2,'.',',') }}</td>

                                    @php
                                    if($bal_qty <= 0){ $qty_in=0; $price_in_qty_in = 0; }
                                    @endphp
                                    @if(str_contains($value->doc_number,'SRT'))
                                        @php 
                                        $qty_in += $value->qty_in;
                                        $bal_qty += $value->qty_in;
                                        $bal_qty -= $value->qty_out;
                                        @endphp
                                    @else

                                        @php $price_in_qty_in += $value->price_in*$value->qty_in;
                                        $qty_in += $value->qty_in;
                                        $bal_qty += $value->qty_in;
                                        $bal_qty -= $value->qty_out;
                                        if($qty_in !=0){
                                        $avg_rate = @App\SysHelper::com_curr_format($price_in_qty_in/$qty_in,2,'.',',');}
                                        @endphp
                                    @endif
                                    <td class="text-center">{{$value->qty_out}}</td>
                                    <td class="text-right">{{ @App\SysHelper::com_curr_format($value->price_out,2,'.',',') }}</td>
                                    <td class="text-center">{{$bal_qty}}</td>
                                    <td class="text-right">{{ $avg_rate }}</td>
                                    <td class="text-right">
                                        @if($value->slno !="")
                                        <a class="btn-sm btn-success pt-t pb-1 pl-2 pr-2" data-toggle="modal" data-target="#exampleModalCenter{{ $value->doc_number }}">View SrlNo</a>@endif
                                        <div class="all_srl_no_{{ $i }}" style="display: none;">{{ str_replace(",",", ",$value->slno)}}</div></td>

                                    <div class="modal fade" id="exampleModalCenter{{ $value->doc_number }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h5 class="modal-title" id="exampleModalLongTitle">{{ $partnolist[$i] }} | {{ $value->doc_number }}</h5>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <div class="modal-body" style="line-height: 25px;">
                                                {{ str_replace(","," | ",$value->slno)}}
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>
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
                            <th class="text-right"></th>
                            <th class="text-center">{{ $total_qty_out }}</th>
                            <th class="text-right"></th>
                            <th class="text-center">{{ $bal_qty }}</th>
                            <th class="text-right"></th>
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
    </div>
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection
@extends('backEnd.newmasterpage')
@section('mainContent')

 

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>

        <aside class="left-nav col-3" id="leftSidebar">
                    <div class="resizer" id="sidebarResizer"></div>
                    <h4 class="mb-2">Purchase Invoice</h4>
                    <div class="search-filter-container mb-4">
                        <div class="input-group flex-nowrap">
                            <input type="text" class="form-control" placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping">
                        </div>
                        <div class="input-group flex-nowrap d-none aditional_search">
                            <input type="text" class="form-control" placeholder="Date" aria-label="Search" aria-describedby="addon-wrapping">
                        </div>
                        <div class="input-group flex-nowrap d-none aditional_search">
                            <input type="text" class="form-control" placeholder="Supplier" aria-label="Search" aria-describedby="addon-wrapping">
                        </div>
                        <div class="input-group flex-nowrap d-none aditional_search">
                            <input type="text" class="form-control" placeholder="Customer" aria-label="Search" aria-describedby="addon-wrapping">
                        </div>
                        <div class="input-group flex-nowrap d-none aditional_search">
                            <input type="text" class="form-control" placeholder="Sales Man" aria-label="Search" aria-describedby="addon-wrapping">
                        </div>
                        <div class="input-group flex-nowrap d-none aditional_search">
                            <input type="text" class="form-control" placeholder="Status" aria-label="Search" aria-describedby="addon-wrapping">
                        </div>

                        <button class="btn btn-light">
                            <i class="ico icon-outline-magnifer"></i>
                        </button>
                        <button class="btn btn-light" id="list_style_button" onclick="list_style()">
                            <i class="ico icon-outline-list-down"></i>
                        </button>
                    </div>
                    <div class="left-nav-list">
                        <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                         @if(count($purchaseinvoice)>0)
                         @foreach($purchaseinvoice as $value)
                            <li class="nav-item w-100" role="presentation">
                                <button href="javascript:void(0)" class="nav-link data-item {{ $active_id == $value->id ? 'active' : '' }}" data-id="{{ $value->id }}">
                                {{-- <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="grn-tab" data-bs-toggle="tab" 
                                    data-bs-target="#grn-{{ $value->id }}" type="button" role="tab" aria-controls="grn-{{ $value->id }}"
                                    aria-selected="true"> --}}
                                    <div class="row w-100">
                                        <div class="col-4">
                                            <div class="form-control-plaintext">{{ $value->doc_number }}</div>
                                        </div>
                                        <div class="col-4 pl-2">
                                            <div class="form-control-plaintext truncate-text">{{ date('d/m/Y', strtotime(@$value->grn_date)) }}</div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="form-control-plaintext truncate-text">{{ @App\SysHelper::com_curr_format($value->amount,2,'.',',')}} {{ $value->currency_name->code }}</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-control-plaintext truncate-text">{{ $value->accountname->account_code }} - {{ $value->accountname->account_name }}</label>
                                        </div>
                                    </div>
                                {{-- </button> --}}
                                </button>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                          <div class="table-responsive mb-4 mt-4">
                        <table id="long-list" class="table table-hover" style="display:none;table-layout: fixed;width:100%">
                            <thead>
                                <tr class="text-center">
                             <th>@lang('PIV No')</th>
                            <th>@lang('PIV Date')</th>
                             <th>@lang('Supplier')</th>
                             <th>@lang('Customer')</th>
                             
                             <th class="text-end">@lang('Taxable Amount')</th>
                             <th class="text-end">@lang('Tax')</th>
                             <th class="text-end">@lang('Amount')</th>
                             <th>@lang('Deal Id')</th>
                             <th>@lang('Salesman')</th>
                             <th>@lang('Bill No')</th>
                             <th>@lang('Bill Date')</th>
                             <th>@lang('LPO&nbsp;No')</th>
                             <th>@lang('GRN&nbsp;No')</th>
                             <th>@lang('PRT&nbsp;No')</th>
                             <th>@lang('Currency')</th>
                             <th>@lang('Payment')</th>
                             <th>@lang('lang.status')</th>
                             <th><i class="ico icon-bold-paperclip"></i></th>
                             <th class="text-center">@lang('lang.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                        @php $count =1; $total_taxable_amount=0; $total_tax=0; $total_amount=0; @endphp
                         @foreach($purchaseinvoice as $value)
                         <tr @if (@$value->status == 2) class="bg-dark" @endif>
                             <td class="text-center"><a href="javascript:void(0)" onclick="list_style()" class="data-item" data-id="{{ $value->id }}" >{{@$value->doc_number}}</a></td>
                             <td>{{date('d/m/Y', strtotime(@$value->pi_date))}}</td>
                             <td>{{@$value->accountname->account_name}}
                                </td>
                             <td>{{@$value->reference}}
                                </td>

                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->total_taxableamount,2,'.',',') }}<?php $total_taxable_amount += $value->total_taxableamount; ?></td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->total_vatamount,2,'.',',') }}<?php $total_tax += $value->total_vatamount; ?></td>
                                <td class="text-end">{{ @App\SysHelper::com_curr_format(@$value->amount,2,'.',',') }}<?php $total_amount += $value->amount; ?></td>
                                <td>
                                <?php
                                $code = explode(',',$value->code);
                                if(count($code)>0){
                                   foreach($code as $c){
                                       $cd = @App\SysHelper::get_code_from_dealid($c);
                                       ?>
                                       <a href="{{url('get-url-deal-track/'.$cd)}}" target="_blank">{{ $cd }}</a>
                                       <?php
                                   }
                                }
                                ?>
                               </td>
                               <td>{{ $value->salesman_name }}</td>
                                <td><div id="desc_bill{{ $value->id }}" onmouseover="show_tool_tip('bill'+{{ $value->id }})" onmouseout="hide_tool_tip('bill'+{{ $value->id }})" style="width:100px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
                                    {{ @$value->bill_number }}</div></td>
                                <td>{{date('d/m/Y', strtotime(@$value->bill_date))}}</td>



                             <td>
                                <?php
                                $lpo = explode(',',$value->lpo_number);
                                if(count($lpo)>0){
                                   foreach($lpo as $p){
                                       ?>
                                       <a href="javacript:void(0);" onclick="list_style()" class="po-item" data-id="{{ @App\SysHelper::getPurchaseOrderID($p) }}" >{{@$p}}</a>
                                       <?php
                                   }
                                }
                                ?>
                            </td>
                             <td>
                                @if (empty($value->grn_no))
                                    <span class="text-dark">Pending</span>
                                @else
                                    @foreach (explode(',', $value->grn_no) as $grn)
                                        <a href="javacript:void(0);" onclick="list_style()" class="grn-item" data-id="{{ @App\SysHelper::getGRNID($grn) }}">{{ trim($grn) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                             </td>
                             <td class="text-center">
                                @if (empty($value->prt_no))
                                    <span class="text-dark">Pending</span>
                                @else
                                    @foreach (explode(',', $value->prt_no) as $prt)
                                        <a href="{{ url('get-url-purchase-return/' . trim($prt)) }}" target="_blank">{{ trim($prt) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>

                             <td class="text-center">{{ @$value->currency_name->code }}</td>
                             <td class="text-center">
                                <?php $count = $adj_list->where('bi_doc_no',$value->doc_number)->count(); ?>
                                @if($count==1)
                                <span class="text-success">Paid</span>
                                @else
                                <span class="text-dark">Pending</span>
                                @endif
                             </td>
                             <td class="text-center">
                                @if (@$value->return_status == 1)
                                    <span class="text-dark">Returned</span>
                                @elseif(@$value->return_status == 2)
                                <span class="text-dark">Partial Returned</span>
                                @else
                                <span class="text-success">Active</span>
                                @endif
                             </td>
                             <td class="text-center">
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>
                             <td class="text-center">
                                <a class="btn btn-sm btn-light d-block" href="{{url('purchase-invoice/'.$value->id.'/download')}}"> <i class="ico icon-bold-download-minimalistic text-dark"
                                                        style="font-size: 16px;"></i></a>
                             </td>
                         </tr>
                         @endforeach
                            </tbody>
                        </table>
                          </div>
                    </div>
                </aside>

                
                <div class="content-container col-9">
                    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

                        
            <script>
                 $(document).ready(function() {
                    $(document).on('click', '.data-item', function() {
                        var id = $(this).data('id');
                      
                        $('.data-item').removeClass('active');
                        $('.data-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('purchase-invoice') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('purchase-invoice-details') }}/"+id;

                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#data-details').html(response);
                            },
                            error: function() {
                                $('#data-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>
                      


  <script>
                $(document).ready(function() {
                    $('.grn-item').on('click', function() {

                        $("#loading_bg").css("display", "block");

                        var id = $(this).data('id');
                        $('.grn-item').removeClass('active');
                        $('.grn-item[data-id="' + id + '"]').addClass('active');

                        var action = "{{ URL::to('purchasegrn-details') }}/" + id;
                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#data-details').html(response);
                            },
                            error: function() {
                                $('#data-details').html(
                                    '<p class="text-danger">Error loading details.</p>');
                            }
                        });

                        $("#loading_bg").css("display", "none");
                    });


                });
            </script>

                <script>
                $(document).ready(function() {
                    $('.po-item').on('click', function() {
                        var id = $(this).data('id');
                        console.log(id)
                        $('.po-item').removeClass('active');
                        $('.po-item[data-id="' + id + '"]').addClass('active');

                    

                        var action = "{{ URL::to('purchase-details') }}/" + id;
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#data-details').html(response);
                            },
                            error: function() {
                                $('#data-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>

                      
                        
                        {{-- <div class="" role="tabpanel" aria-labelledby="grn-tab" id="grn-details">
                            @if(count($purchasegrn) > 0)
                                @include('backEnd.grn.grn_add',$data)
                            @endif
                        </div> --}}


                        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                            @if(count($purchaseinvoice) > 0)
                                @include('backEnd.purchaseinvoice.pi_details',$data)
                            @else
                            <div onclick="window.location.href='{{ url('purchase-invoice/create') }}'" class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <div class="text-center mb-4" >
                            <div  class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                <i class="ico icon-outline-add-square"></i>
                            </div>
                            <h1 class="fw-bold mt-3" style="cursor:pointer" > Purchase Invoice</h1>
                            {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                        </div>

                    </div>
                            @endif
                        </div>
                      
                    </div>
                </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>    
@endsection

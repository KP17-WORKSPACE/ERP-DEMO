@extends('backEnd.newmasterpage')
@section('mainContent')
@php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp
<?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <h4 class="mb-2">Purchase Return</h4>
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
                @if(count($purchasereturn)>0)
                @foreach($purchasereturn as $value)
                <li class="nav-item w-100" role="presentation">
                    <button href="javascript:void(0)" class="nav-link data-item {{ $loop->first ? 'active' : '' }}" data-id="{{ $value->id }}">
                        {{-- <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="grn-tab" data-bs-toggle="tab"
                        data-bs-target="#grn-{{ $value->id }}" type="button" role="tab" aria-controls="grn-{{ $value->id }}"
                        aria-selected="true"> --}}
                        <div class="row w-100">
                            <div class="col-4">
                                <div class="form-control-plaintext">{{ $value->doc_number }}</div>
                            </div>
                            <div class="col-4 pl-2">
                                <div class="form-control-plaintext truncate-text">{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="form-control-plaintext">{{ @App\SysHelper::com_curr_format($value->amount,2,'.',',')}} {{ $value->currency_name->code }}</div>
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
            <table id="long-list" class="table table-hover" style="display: none;">
                <thead>
                    <tr>
                        <th class="text-center"> @lang('PRT Number')</th>
                        <th class="text-center"> @lang('PRT Date')</th>
                        <th> @lang('Supplier')</th>
                        <th> @lang('Customer')</th>
                        <th class="text-center"> @lang('PO No')</th>
                        <th class="text-center"> @lang('GRN No')</th>
                        <th class="text-center"> @lang('PIV No')</th>
                        <th class="text-center">@lang('Deal No')</th>
                        <th class="text-end"> @lang('Amount')</th>
                        <th class="text-center"> @lang('lang.action')</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($purchasereturn))
                    @foreach ($purchasereturn as $value)
                    <tr @if (@$value->status == 2) class="bg-dark" @endif>
                        <td class="text-center"><a href="javascript:void(0)" onclick="list_style()" class="data-item" data-id="{{ $value->id }}">{{ @$value->doc_number }}</a></td>
                        <td class="text-center">{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                        <td>{{@$value->accountname->account_name}}</td>
                        <td>{{@$value->reference}}</td>
                        <!-- PO Number (single link) -->
                        <td class="text-center">
                            @if (empty($value->po_no))
                            <span class="text-dark">Pending</span>
                            @else
                            @foreach (explode(',', $value->po_no) as $po)
                            <a href="{{ url('get-url-purchase-order/' . trim($po)) }}" target="_blank">{{ trim($po) }}</a>@if (!$loop->last), @endif
                            @endforeach
                            @endif
                        </td>

                        <!-- GRN Numbers (multiple links if comma-separated) -->
                        <td class="text-center">
                            @if (empty($value->grnno))
                            <span class="text-dark">Pending</span>
                            @else
                            @foreach (explode(',', $value->grnno) as $grn)
                            <a href="{{ url('get-url-purchase-grn/' . trim($grn)) }}" target="_blank">{{ trim($grn) }}</a>@if (!$loop->last), @endif
                            @endforeach
                            @endif
                        </td>

                        <!-- PI Numbers (multiple links if comma-separated) -->
                        <td class="text-center">
                            @if (empty($value->pi_number))
                            <span class="text-dark">Pending</span>
                            @else
                            @foreach (explode(',', $value->pi_number) as $pi)
                            <a href="{{ url('get-url-purchase-invoice/' . trim($pi)) }}" target="_blank">{{ trim($pi) }}</a>@if (!$loop->last), @endif
                            @endforeach
                            @endif
                        </td>

                        <!-- Deal Codes (multiple links if comma-separated) -->
                        <td class="text-center">
                            @if (empty($value->code))
                            <span class="text-dark">Pending</span>
                            @else
                            @foreach (explode(',', $value->code) as $code)
                            <a href="{{ url('get-url-deal-track/' . trim($code)) }}" target="_blank">{{ trim($code) }}</a>@if (!$loop->last), @endif
                            @endforeach
                            @endif
                        </td>
                        <td class="text-end">{{ @App\SysHelper::com_curr_format($value->amount,2,'.',',') }}</td>
                        <td class="text-center">
                            <a class="btn btn-sm btn-light d-block" href="{{url('purchase-return/'.$value->id.'/download')}}"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
                        </td>
                    </tr>

                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </aside>


    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <script>
                $(document).ready(function() {
                    $('.data-item').on('click', function() {

                        $("#loading_bg").css("display", "block");

                        var id = $(this).data('id');
                        $('.data-item').removeClass('active');
                        $('.data-item[data-id="' + id + '"]').addClass('active');

                        var action = "{{ URL::to('purchase-return-details') }}/" + id;
                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#data-details').html(response);
                            },
                            error: function() {
                                $('#data-details').html('<p class="text-danger">Error loading details.</p>');
                            }
                        });

                        $("#loading_bg").css("display", "none");
                    });


                });
            </script>


            {{-- <div class="" role="tabpanel" aria-labelledby="grn-tab" id="grn-details">
                            @if(count($purchasegrn) > 0)
                                @include('backEnd.grn.grn_add',$data)
                            @endif
                        </div> --}}


            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                @if(count($purchasereturn) > 0)
                @include('backEnd.purchasereturn.pr_details',$data)
                @else
                <div onclick="window.location.href='{{url('purchase-return-add')}}'" class="container-fluid d-flex flex-column justify-content-center align-items-center"
                    style="min-height: 90vh;">

                    <!-- Icon + Heading -->
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                            style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                            <i class="ico icon-outline-add-square"></i>
                        </div>
                        <h1 class="fw-bold mt-3" style="cursor:pointer"> Purchase Return</h1>
                        {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                    </div>

                </div>
                @endif
            </div>

        </div>
    </div>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection
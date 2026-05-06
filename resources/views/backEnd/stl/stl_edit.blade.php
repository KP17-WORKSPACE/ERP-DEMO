@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>

        <aside class="left-nav col-3" id="leftSidebar">
                    <div class="resizer" id="sidebarResizer"></div>
                    <h4 class="mb-2">STL</h4>
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
                         @if(count($stl)>0)
                         @foreach($stl as $value)
                            <li class="nav-item w-100" role="presentation">
                                <button href="javascript:void(0)" class="nav-link data-item {{ $loop->first ? 'active' : '' }}" data-id="{{ $value->id }}">
                                {{-- <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="grn-tab" data-bs-toggle="tab" 
                                    data-bs-target="#grn-{{ $value->id }}" type="button" role="tab" aria-controls="grn-{{ $value->id }}"
                                    aria-selected="true"> --}}
                                    <div class="row w-100">
                                        <div class="col-4">
                                            <div class="form-control-plaintext truncate-text">{{ $value->doc_number }}</div>
                                        </div>
                                        <div class="col-4 pl-2">
                                            <div class="form-control-plaintext truncate-text">{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="form-control-plaintext truncate-text">{{ @App\SysHelper::com_curr_format(@$value->amount_usd,2,'.',',') }} USD</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-control-plaintext truncate-text">{{ @$value->vendor_name->account_code }} - {{ @$value->vendor_name->account_name }}</label>
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
                             <th> @lang('Doc Number')</th>
                        <th> @lang('Doc Date')</th>
                        <th> @lang('Bank')</th>
                        <th> @lang('Exchange Rate')</th>
                        <th class="text-right"> @lang('Amount USD')</th>
                        <th class="text-right"> @lang('Amount AED')</th>                        
                        <th> @lang('Vendor')</th>
                        <th> @lang('Payment Type')</th>
                        <th> @lang('PI/ PI')</th>
                        <th> @lang('Submission Date')</th>
                        <th> @lang('Narration')</th>
                        <th> @lang('Created By')</th>
                        <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($stl))
                        @foreach ($stl as $value)
                            <tr @if($value->status == 2) class="bg-dark" @endif>
                                <td>{{ @$value->doc_number }}</td>
                                <td>{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                                <td>{{ @$value->bank_name->account_name }}</td>
                                <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->exchange_rate,3,'.',',') }}</td>
                                <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->amount_usd,2,'.',',') }}</td>
                                <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->amount_aed,2,'.',',') }}</td>
                                <td>{{ @$value->vendor_name->account_name }}</td>
                                <td>{{ @$value->payment_type }}</td>
                                <td>
                                    @if ($value->pi_no==1)
                                    <span class="text-info">Purchase Invoice</span>
                                    @else
                                    <span class="text-primary">Performa Invoice</span>
                                    @endif
                                </td>
                                <td>{{date('d/m/Y', strtotime(@$value->submition_date))}}</td>
                                <td>{{ @$value->narration }}</td>
                                <td>{{ @$value->createdby->full_name }}</td>
                                <td>
                                    <a class="btn-md" href="{{url('stl/'.$value->id.'/download')}}"><i class="ico icon-outline-download-square" aria-hidden="true"></i></a>
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
                        $(document).ready(function () {
                            $('.data-item').on('click', function () {
                                
                            $("#loading_bg").css("display", "block");
                                
                                var id = $(this).data('id');
                                $('.data-item').removeClass('active');
                                $('.data-item[data-id="' + id + '"]').addClass('active');
                                
                                var action = "{{ URL::to('stl-details') }}/"+id;
                                $.ajax({            
                                    url: action,
                                    method: 'GET',
                                    success: function (response) {
                                        $('#data-details').html(response);
                                    },
                                    error: function () {
                                        $('#data-details').html('<p class="text-danger">Error loading details.</p>');
                                    }
                                });
                                
                            $("#loading_bg").css("display", "none");
                            });
                            
            
                        });
                        </script>
                        @if(count($stl)>0)
                        
                        {{-- <div class="" role="tabpanel" aria-labelledby="grn-tab" id="grn-details">
                            @if(count($purchasegrn) > 0)
                                @include('backEnd.grn.grn_add',$data)
                            @endif
                        </div> --}}


                        <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                            @if(count($stl) > 0)
                                @include('backEnd.stl.s_edit')
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>    
@endsection

@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>

        <aside class="left-nav col-3" id="leftSidebar">
                    <div class="resizer" id="sidebarResizer"></div>
                    <h4 class="mb-2">Goods Receipt Note</h4>
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
                         @if(count($purchasegrn)>0)
                         @foreach($purchasegrn as $value)
                            <li class="nav-item w-100" role="presentation">
                                <button href="javascript:void(0)" class="nav-link grn-item {{ $grn_select_id == $value->id ? 'active' : '' }}" data-id="{{ $value->id }}">
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
                          <table id="long-list" class="table table-hover" style="display: none;">
                <thead>
                    <tr>
                        <th class="text-center">@lang('GRN No')</th>
                        <th class="text-center">@lang('GRN Date')</th>
                        <th>@lang('Supplier')</th>
                        <th>@lang('Customer')</th>
                        <th>@lang('PO No')</th>
                        <th>@lang('PIV No')</th>
                        <th>@lang('PRT No')</th>
                        <th>@lang('Deal No')</th>
                        <th class="text-center">@lang('Currency')</th>
                        <th class="text-end">@lang('Amount')</th>
                        <th class="text-center"><i class="ico icon-bold-paperclip"></i></th>
                        <th class="text-end">@lang('lang.action')</th>
                    </tr>
                </thead>
                <tbody>
                    @php $count =1; @endphp
                    @foreach ($purchasegrn as $value)
                        <tr @if (@$value->status == 2) class="bg-dark" @endif>
                            <td class="text-center"><a href="javascript:void(0)" onclick="list_style()" class="grn-item"
                                    data-id="{{ $value->id }}">{{ @$value->doc_number }}</a>
                            </td>
                            <td class="text-center">{{ date('d/m/Y', strtotime(@$value->grn_date)) }}</td>
                            <td>{{ @$value->accountname->account_name }}</td>
                            <td>{{ @$value->reference }}</td>

                            <td>
                                <?php
                             $lpo = explode(',',$value->lpo_number);
                             if(count($lpo)>0){
                                foreach($lpo as $p){
                                    ?>
                                <a href="{{ url('get-url-purchase-order/' . $p) }}" target="_blank">{{ @$p }}</a>
                                <?php
                                }
                             }
                             ?>
                            </td>
                            <td>
                                @if (empty($value->piv_no))
                                    <span class="text-dark">Pending</span>
                                @else
                                    @foreach (explode(',', $value->piv_no) as $piv)
                                        <a href="{{ url('get-url-purchase-invoice/' . trim($piv)) }}"
                                            target="_blank">{{ trim($piv) }}</a>
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if (empty($value->prt_no))
                                    <span class="text-dark">Pending</span>
                                @else
                                    @foreach (explode(',', $value->prt_no) as $prt)
                                        <a href="{{ url('get-url-purchase-return/' . trim($prt)) }}"
                                            target="_blank">{{ trim($prt) }}</a>
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                @endif
                            </td>

                            <td>
                                <?php
                             $code = explode(',',$value->code);
                             if(count($code)>0){
                                foreach($code as $c){
                                    $cd = @App\SysHelper::get_code_from_dealid($c);
                                    ?>
                                <a href="{{ url('get-url-deal-track/' . $cd) }}" target="_blank">{{ $cd }}</a>
                                <?php
                                }
                             }
                             ?>
                            </td>
                            <td>{{ $value->currency_name->code }}</td>

                            <td class="text-end">{{ @App\SysHelper::com_curr_format($value->amount, 2, '.', ',') }}</td>
                            <td>
                                @if (empty(@$value->attach))
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip"
                                                aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>
                            <td class="text-center">
                                <a class="btn  d-block mx-auto btn-light" href="{{ url('goods-receipt-note/' . $value->id . '/download') }}"> <i
                                        class="ico icon-bold-download-minimalistic text-dark"
                                        style="font-size: 16px;"></i></a>
                                {{--  <a class="btn-sm btn-primary" href="{{url('goods-receipt-note/'.$value->id.'/download')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>  --}}

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
                    </div>
                </aside>

                
                <div class="content-container col-9">
                    <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
                        <script>
                        $(document).ready(function () {
                            $('.grn-item').on('click', function () {
                                
                                var id = $(this).data('id');
                                $('.grn-item').removeClass('active');
                                $('.grn-item[data-id="' + id + '"]').addClass('active');
                                
                                var action = "{{ URL::to('purchasegrn-details') }}/"+id;
                                $.ajax({            
                                    url: action,
                                    method: 'GET',
                                    success: function (response) {
                                        $('#grn-details').html(response);
                                    },
                                    error: function () {
                                        $('#grn-details').html('<p class="text-danger">Error loading details.</p>');
                                    }
                                });
                            });
                        });
                        </script>

                        <div class="" role="tabpanel" aria-labelledby="grn-tab" id="grn-details">
                                @include('backEnd.grn.grn_edit')
                        </div>
                    </div>
                </div>

                <script>
  const leftNav = document.querySelector('.left-nav');
  const content = document.querySelector('.content-container');
  const state = localStorage.getItem("leftNavState");
  if (state === "expanded") {
    leftNav.classList.remove('col-3');
    leftNav.classList.add('col-12');
    if (content) {
      content.classList.remove('col-9');
      content.classList.add('col-0');
    }
    $('#short-list').hide();
    $('#long-list').show();
    $('.aditional_search').removeClass('d-none');
  } 
  else if (state === "collapsed") {
    leftNav.classList.remove('col-12');
    leftNav.classList.add('col-3');
    if (content) {
      content.classList.remove('col-0');
      content.classList.add('col-9');
    }
    $('#long-list').hide();
    $('#short-list').show();
    $('.aditional_search').addClass('d-none');
  }
</script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>    
@endsection

@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <h4 class="mb-2">Payments</h4>
        <div class="search-filter-container mb-4">
            <div class="input-group flex-nowrap">
                <input type="text" class="form-control" placeholder="Document No" aria-label="Search"
                    aria-describedby="addon-wrapping">
            </div>
            <div class="input-group flex-nowrap d-none aditional_search">
                <input type="text" class="form-control" placeholder="Date" aria-label="Search"
                    aria-describedby="addon-wrapping">
            </div>
            <div class="input-group flex-nowrap d-none aditional_search">
                <input type="text" class="form-control" placeholder="Supplier" aria-label="Search"
                    aria-describedby="addon-wrapping">
            </div>
            <div class="input-group flex-nowrap d-none aditional_search">
                <input type="text" class="form-control" placeholder="Customer" aria-label="Search"
                    aria-describedby="addon-wrapping">
            </div>
            <div class="input-group flex-nowrap d-none aditional_search">
                <input type="text" class="form-control" placeholder="Sales Man" aria-label="Search"
                    aria-describedby="addon-wrapping">
            </div>
            <div class="input-group flex-nowrap d-none aditional_search">
                <input type="text" class="form-control" placeholder="Status" aria-label="Search"
                    aria-describedby="addon-wrapping">
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
                @if (count($payment) > 0)
                    @foreach ($payment as $value)
                        <li class="nav-item w-100" role="presentation">
                            <button href="javascript:void(0)" class="nav-link data-item " data-id="{{ $value->id }}">
                                {{-- <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="grn-tab" data-bs-toggle="tab" 
                                    data-bs-target="#grn-{{ $value->id }}" type="button" role="tab" aria-controls="grn-{{ $value->id }}"
                                    aria-selected="true"> --}}
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div class="form-control-plaintext truncate-text">{{ $value->doc_number }}</div>
                                    </div>
                                    <div class="col-4 pl-2">
                                        <div class="form-control-plaintext truncate-text">
                                            {{ date('d/m/Y', strtotime(@$value->doc_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text">
                                            {{ @App\SysHelper::com_curr_format(abs(@$value->debit_amount - @$value->credit_amount), 2, '.', ',') }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label
                                            class="form-control-plaintext truncate-text">{{ @$value->account->account_code }}
                                            - {{ @$value->account->account_name }} -
                                            @if (@$value->mode == 1)
                                                Cash
                                            @else
                                                @if (@$value->payment_through == 1)
                                                    Bank Transfer
                                                @elseif(@$value->payment_through == 2)
                                                    CDC Cheque
                                                @else
                                                    PDC Cheque
                                                @endif
                                            @endif
                                        </label>
                                    </div>
                                </div>
                                {{-- </button> --}}
                            </button>
                        </li>
                    @endforeach
                @endif
            </ul>
            <div class="table-responsive">
                <table id="long-list" class="table table-hover" style="display: none;">
                    <thead>
                        <tr>
                            <th class="text-center"> @lang('Doc Number')</th>
                            <th class="text-center"> @lang('Mode')</th>
                            <th class="text-center"> @lang('Payment Mode')</th>
                            <th> @lang('Payment Through')</th>
                            <th> @lang('Account Name')</th>
                            <th class="text-end"> @lang('Amount')</th>
                            <th class="text-center"> @lang('Doc Date')</th>
                            <th class="text-center"> @lang('Payment Date')</th>
                            <th class="text-center"> @lang('Cheque Date')</th>
                            <th> @lang('Cheque Number')</th>
                            <th class="text-center"> @lang('Deal ID')</th>
                            <th> @lang('Created By')</th>
                            <th> @lang('Narration')</th>
                            <th class="text-center">@lang('lang.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($payment))
                            @foreach ($payment as $value)
                                <tr @if ($value->status == 2) class="bg-dark" @endif
                                    @if (@$value->type == 2) class="text-danger" @endif>
                                    <td class="text-center"><a
                                            href="{{ url('payment/' . @$value->id . '/view') }}">{{ @$value->doc_number }}</a>
                                    </td>
                                    <td class="text-center">
                                        @if (@$value->mode == 1)
                                            Cash
                                        @else
                                            Bank
                                        @endif
                                    </td>
                                    <td>{{ @$value->account->account_name }}</td>
                                    <td>
                                        @if (@$value->mode == 1)
                                            Cash
                                        @else
                                            @if (@$value->payment_through == 1)
                                                Bank Transfer
                                            @elseif(@$value->payment_through == 2)
                                                CDC Cheque
                                            @else
                                                PDC Cheque
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ @$value->account_name }}</td>
                                    <td class="text-end">
                                        {{ @App\SysHelper::com_curr_format(abs(@$value->debit_amount - @$value->credit_amount), 2, '.', ',') }}
                                    </td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->doc_date)) }}</td>
                                    <td class="text-center">{{ date('d/m/Y', strtotime(@$value->payment_date)) }}</td>
                                    <td>
                                        @if (@$value->mode == 2 && @$value->payment_through != 1)
                                            {{ date('d/m/Y', strtotime(@$value->cheque_date)) }}
                                        @endif
                                    </td>
                                    <td>{{ @$value->cheque_number }}</td>
                                    <td class="text-center">
                                        <a href="{{ url('get-url-deal-track/' . @$value->deal_code->code) }}"
                                            target="_blank">{{ @$value->deal_code->code }}</a>
                                    </td>
                                    <td>{{ @$value->full_name }}</td>
                                    <td>{{ @$value->narration }}</td>
                                    <td class="text-center">
                                        <a class="btn btn-light d-block"
                                            href="{{ url('payment/' . $value->id . '/download') }}"><i
                                                class="ico icon-bold-download-minimalistic text-dark"
                                                style="font-size: 16px;"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

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

                        var action = "{{ URL::to('payment-details') }}/" + id;
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
            @if (count($payment) > 0)
                {{-- <div class="" role="tabpanel" aria-labelledby="grn-tab" id="grn-details">
                            @if (count($purchasegrn) > 0)
                                @include('backEnd.grn.grn_add',$data)
                            @endif
                        </div> --}}


                <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                    @if (count($payment) > 0)
                        @include('backEnd.payment.p_add')
                    @endif
                </div>
            @endif
        </div>
    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

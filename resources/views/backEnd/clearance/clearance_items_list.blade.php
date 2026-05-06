@extends('backEnd.newmasterpage')
@section('mainContent')
    <script>
        let isFullList = false;

        function list_style_new() {
            const leftNav = document.querySelector('.left-nav');
            const content = document.querySelector('.content-container');

            if (!isFullList) {
                // Switch to FULL LIST VIEW
                isFullList = true;

                leftNav.classList.remove('col-3');
                leftNav.classList.add('col-12');
                leftNav.style.width = '100%';

                content.classList.add('d-none');

                $('#long-list').removeClass('d-none');
                $('#short-list').addClass('d-none');

                $('#filters-long').removeClass('d-none');
                $('#filters-short').addClass('d-none');
            } else {
                // Switch to COMPACT VIEW
                isFullList = false;

                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                leftNav.style.width = '';

                content.classList.remove('d-none');

                $('#long-list').addClass('d-none');
                $('#short-list').removeClass('d-none');

                $('#filters-short').removeClass('d-none');
                $('#filters-long').addClass('d-none');
            }
        }


        //added ny kp
        function toggleLongFilters() {
            console.log("clicked");
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }
    </script>







    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <div class="short-list" id="filters-short">
            <h4 class="mb-2">Customs Clearance
            </h4>

            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'clearance', 'method' => 'get', 'id' => 'clearance-search']) }}

            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="documents_number" class="form-control" placeholder="Document No"
                        aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>


                <button type="submit" class="btn btn-light">
                    <i class="ico icon-outline-magnifer"></i>
                </button>
                {{ Form::close() }}
                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Customs Clearance List
                </h4>
                <div class="search-filter-container mb-0">


                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>

            <div class="search-filter-container mt-1 mb-4 filter-field d-none border">

                <div class="card" style="width:100%">
                    <div class="card-body">

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'clearance', 'method' => 'get', 'id' => 'clearance-search']) }}
                        <div class="row">


                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Documents Number</label>
                                <input class="form-control" type="text" autocomplete="off" name="documents_number"
                                    value="{{ $ctrl_document_number }}">
                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Invoice No</label>
                                <input class="form-control" type="text" autocomplete="off" name="invoice_no"
                                    value="{{ $ctrl_invoice_no }}">

                            </div>

                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Invoice Date</label>
                                <input class="form-control date-picker" type="text" autocomplete="off"
                                    name="invoice_date" value="{{ $ctrl_invoice_date }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Deal ID</label>
                                <input class="form-control" type="text" autocomplete="off" name="deal_number"
                                    value="{{ $ctrl_deal_number }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Bill To</label>
                                <input class="form-control" type="text" autocomplete="off" name="bill_to"
                                    value="{{ $ctrl_bill_to }}">
                            </div>
                            <div class="col-1-5 mb-2 filter-field d-none">
                                <label for="" class="form-label">Ship To</label>
                                <input class="form-control" type="text" autocomplete="off" name="ship_to"
                                    value="{{ $ctrl_ship_to }}">
                            </div>


                            <div class="col-md-3 filter-field d-none">
                                <button type="submit" class="btn btn-success mt-4 rounded-0" id="btnSubmit">Filter</button>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="left-nav-list">

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                @if (count($clearance) > 0)
                    @foreach ($clearance as $item)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link clr-item {{ $active_id == $item->id ? 'active' : '' }}"
                                data-id="{{ $item->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                    <div class="col-4">
                                        <div class="form-control-plaintext">{{ @$item->doc_no }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext">
                                            {{ date('d/m/Y', strtotime(@$item->invoice_date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text">
                                            <?php $deal_code = @App\SysHelper::get_code_from_dealid($item->deal_id); ?>
                                            {{ $deal_code }}

                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ @$item->bill_to }} | {{ $item->ship_to }}
                                        </label>
                                    </div>
                                </div>
                            </button>
                        </li>
                    @endforeach
                @else
                    No Records
                @endif
            </ul>

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover d-none" style="table-layout: fixed;width:100%">

                    <thead class="text-center">
                        <tr>
                            <th style="width: 60px;">@lang('lang.sl') </th>
                            <th style="width: 60px;">@lang('Doc No')</th>
                            <th style="width: 60px;">@lang('Invoice No')</th>
                            <th style="width: 80px;">@lang('Invoice Date')</th>
                            <th style="width: 60px;">@lang('Deal Id')</th>
                            <th class="text-start" style="width: 300px;">@lang('Bill To')</th>
                            <th class="text-start" style="width: 300px;">@lang('Ship To')</th>
                            <th class="text-start" style="width: 150px;">@lang('Customer Bill Type')</th>
                            <th style="width: 90px;">@lang('Action')</th>
                        </tr>
                    </thead>


                    <tbody>
                        @php $count =1; @endphp
                        @foreach ($clearance as $value)
                            <tr>
                                <td class="text-center">{{ @$count++ }}</td>
                                <td class="text-center"><a href="{{ url('get-url-clearance/' . $value->doc_no) }}"
                                        target="_blank">{{ @$value->doc_no }}</a></td>
                                <td class="text-center"><a
                                        href="{{ url('get-url-sales-invoice/' . $value->invoice_no) }}"
                                        target="_blank">{{ @$value->invoice_no }}</a></td>
                                <td class="text-center">{{ date('d-m-Y', strtotime(@$value->invoice_date)) }}</td>
                                <?php $deal_code = @App\SysHelper::get_code_from_dealid($item->deal_id); ?>

                                <td class="text-center"><a href="{{ url('get-url-deal/' . $deal_code) }}"
                                        target="_blank">{{ $deal_code }}</a></td>
                                <td>{{ @$value->bill_to }}</td>
                                <td>{{ @$value->ship_to }}</td>
                                <td>{{ @$value->customer_bill_type }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ url('clearance/' . $value->id . '/edit') }}"
                                            class="btn btn-sm btn-light" title="Comments">
                                            <i class="ico icon-outline-pen-2 text-dark" style="font-size: 16px;"></i>
                                        </a>

                                        <a href="{{ url('clearance/' . $value->id . '/download') }}"
                                            class="btn btn-sm btn-light" title="Comments">
                                            <i class="ico icon-bold-download-minimalistic text-dark"
                                                style="font-size: 16px;"></i>
                                        </a>
                                    </div>
                                    {{-- <a class="p-0 pl-2 pr-2 btn btn-info btn-xs text-white" title="Download PDF"
                                        href="{{ url('clearance/' . $value->id . '/download') }}"><i
                                            class="fa fa-download" aria-hidden="true"></i></a>
                                    <a class="p-0 pl-2 pr-2 btn btn-success btn-xs text-white" title="Preview"
                                        href="{{ url('clearance/' . $value->id . '/preview') }}" target="_blank"><i
                                            class="fa fa-eye" aria-hidden="true"></i></a>
                                    <a class="p-0 pl-2 pr-2 btn btn-danger btn-xs text-white" title="View & Edit"
                                        href="{{ url('clearance/' . $value->id . '/edit') }}"><i class="fa fa-edit"
                                            aria-hidden="true"></i></a> --}}
                                </td>
                            </tr>
                            <div class="modal fade admin-query" id="deletequotations{{ @$value->id }}">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">@lang('lang.delete') @lang('lang.quotations')</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="text-center">
                                                <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                            </div>

                                            <div class="mt-40 d-flex justify-content-between">
                                                <button type="button" class="primary-btn tr-bg"
                                                    data-dismiss="modal">@lang('lang.cancel')
                                                </button>

                                                <a href="{{ url('quotations/delete', [$value->id]) }}"
                                                    class="primary-btn fix-gr-bg">@lang('lang.delete')</a>

                                            </div>


                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </tbody>

                    <footer>
                        <tr>
                            <td colspan="6">
                                {{ $clearance->appends(request()->input())->links() }}
                            </td>
                        </tr>
                    </footer>


                </table>
            </div>
        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">

            <script>
                $(document).ready(function() {
                    $('.clr-item').on('click', function() {
                        var id = $(this).data('id');
                        console.log(id)
                        $('.clr-item').removeClass('active');
                        $('.clr-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('clearance') }}/" + id ;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('clearance') }}/" + id + "/preview";
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#clr-details').html(response);
                            },
                            error: function() {
                                $('#clr-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>





            <div class="" role="tabpanel" aria-labelledby="po-tab" id="clr-details">
                @if ($action === 'add')
             
                    @include('backEnd.clearance.add_clearance', $addData)
                @elseif($action === 'edit')
                    @include('backEnd.clearance.edit_clearance', $editData)
                    
                @elseif (!empty($selectedCLR) && is_array($selectedCLR))
                    @include('backEnd.clearance.clearance_preview', $selectedCLR)
                @else
                    <form id="supplierForm" method="GET" action="{{ url('clearance') }}">


                        <input type="hidden" name="clr_action" value="add">

                        <div onclick="document.getElementById('supplierForm').submit();"
                            class="container-fluid d-flex flex-column justify-content-center align-items-center"
                            style="min-height: 90vh;">

                            <!-- Icon + Heading -->
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                    style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                    <i class="ico icon-outline-add-square"></i>
                                </div>
                                <h1 class="fw-bold mt-3" style="cursor:pointer"> Customs Clearance</h1>
                                {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                            </div>

                        </div>
                    </form>
                @endif
            </div>


        </div>
    </div>




    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

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
            <h4 class="mb-2">Packing List
            </h4>


            <div class="search-filter-container mb-4">
                <div class="input-group flex-nowrap">
                    <input type="text" name="documents_number" id="search_packinglist" class="form-control"
                        placeholder="Document No" aria-label="Search" aria-describedby="addon-wrapping" value="">
                </div>




                <button type="button" class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                    <i class="ico icon-outline-list-down"></i>
                </button>
            </div>



        </div>

        <div class="long-list  d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Packing List
                </h4>
                <div class="search-filter-container mb-0">

                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">

                    <button class="btn btn-light" onclick="toggleLongFilters()">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button class="btn btn-light" id="list_style_button" onclick="list_style_new()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>

        </div>

        <div class="left-nav-list">

            <ul id="short-list" class="nav flex-column nav-pills" id="companyTabNavs" role="tablist">
                @if (count($data) > 0)
                    @foreach ($data as $item)
                        <li class="nav-item w-100" role="presentation">
                            <button class="nav-link packing-item {{ $active_id == $item->id ? 'active' : '' }}"
                                data-id="{{ $item->id }}" id="purchase-order-1-tab" data-bs-toggle="tab"
                                data-bs-target="#purchase-order-1" type="button" role="tab"
                                aria-controls="purchase-order-1" aria-selected="true">
                                <div class="row w-100">
                                     <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                            {{ @$item->account->account_name  }}</label>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control-plaintext" style="font-size: 11px">{{ @$item->doc_number }}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                            {{ date('d/m/Y', strtotime(@$item->date)) }}</div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                            {{ @$item->refno }}
                                        </div>
                                    </div>
                                   
                                </div>
                            </button>
                        </li>
                    @endforeach
                @else
                   <p class="text-center"> No Records </p>
                @endif
            </ul>

            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover d-none data-table" style="table-layout: fixed;width:100%">

                    <thead class="text-center">
                        <tr class="text-center">
                            <th class="text-start" width="200px">Account</th>
                            <th width="150px">Date</th>
                            <th width="150px">Doc Number</th>
                            <th width="150px">Refno</th>
                            <th width="150px">Refdate</th>
                            <th class="text-start" width="200px">Remarks</th>
                            <th class="text-start" width="150px">Created By</th>
                            <th style="width: 90px;">@lang('Action')</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($data as $value)
                            <tr @if ($value->status == 2) class="bg-dark" @endif>
                                <td>{{ @$value->account->account_name }}</td>
                                <td class="text-center">{{ date('d/m/Y', strtotime(@$value->date)) }}</td>
                                <td class="text-center">
                                    <a href="{{ url('packing-list/' . $value->id) }}"> {{ @$value->doc_number }}</a>


                                </td>
                                <td class="text-center">{{ @$value->refno }}</td>
                                <td class="text-center">{{ date('d/m/Y', strtotime(@$value->refdate)) }}</td>
                                <td>{{ @$value->remarks }}</td>
                                <td>{{ @$value->createdby->full_name }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center gap-1">

                                        <a class="btn btn-sm btn-light"
                                            href="{{ url('packing-list/' . $value->id . '/download') }}" class="btn-small">
                                            <i class="ico icon-bold-download-minimalistic text-dark"
                                                style="font-size: 16px;"></i></a>
                                        {{-- <a class="btn btn-sm btn-light" href="{{ url('packing-list/' . $value->id . '/view') }}"
                                            class="btn-small"><i class="fa fa-eye" aria-hidden="true"></i></a> --}}
                                        <a class="btn btn-sm btn-light"
                                            href="{{ url('packing-list/' . $value->id . '/?packing_action=edit') }}"
                                            class="btn-small"><i class="ico icon-outline-pen-2 text-dark"
                                                style="font-size: 16px;"></i></a>
                                    </div>

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
                    $(document).on('click', '.packing-item', function() {
                        var id = $(this).data('id');
                        console.log(id)
                        $('.packing-item').removeClass('active');
                        $('.packing-item[data-id="' + id + '"]').addClass('active');

                        // Update the browser URL to include selected ID (without reloading)
                        var newUrl = "{{ url('packing-list') }}/" + id;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        var action = "{{ URL::to('packing-list') }}/" + id + "/view";
                        $('#loading_bg').show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            success: function(response) {
                                $('#stock-details').html(response);
                            },
                            error: function(xhr, status, error) {
                                console.error("AJAX Error:", status, error);
                                console.error("Response Text:", xhr.responseText);

                                $('#stock-details').html(
                                    '<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                $('#loading_bg').hide(); // Always hide loader after request completes
                            }
                        });
                    });
                });
            </script>




            <div class="" role="tabpanel" aria-labelledby="po-tab" id="stock-details">
                @if ($action === 'add')
                    @include('backEnd.inventory.PackingListForm', $createData)
                @elseif($action === 'edit')
                    @include('backEnd.inventory.PackingListFormEdit', $editData)
                @elseif (!empty($selectedPack))
                    @include('backEnd.inventory.PackingListFormView', $selectedPack)
                @else
                    <form id="supplierForm" method="GET" action="{{ url('packing-list') }}">

                        <input type="hidden" name="packing_action" value="add">

                        <div onclick="document.getElementById('supplierForm').submit();"
                            class="container-fluid d-flex flex-column justify-content-center align-items-center"
                            style="min-height: 90vh;">

                            <!-- Icon + Heading -->
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                    style="width: 80px; height: 80px; font-size: 36px;cursor:pointer">
                                    <i class="ico icon-outline-add-square"></i>
                                </div>
                                <h1 class="fw-bold mt-3" style="cursor:pointer"> Packing List</h1>
                                {{-- <p class="text-muted">Create and track your leads with ease</p> --}}
                            </div>

                        </div>
                    </form>
                @endif
            </div>


        </div>
    </div>


    <script>
        $(document).ready(function() {

            $('#search_packinglist').on('keyup', function() {
                var query = $(this).val();

                $.ajax({
                    url: "{{ route('packing-list-url.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {

                        console.log(data)


                        $('#short-list').html('');

                        if (data.length > 0) {
                            $.each(data, function(index, suppliers) {





                                let ims = `<li class="nav-item w-100" role="presentation">
                            <button class="nav-link packing-item"
                                data-id="${suppliers.id}" type="button">
                                <div class="row w-100">
                                      <div class="col-12">
                                        <label class="form-control-plaintext truncate-text">
                                               ${suppliers.remarks}</label>
                                    </div>
                                    <div class="col-4">
                                        <div
                                            class="form-control-plaintext" style="font-size: 11px">
                                            ${suppliers.doc_number}</div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="form-control-plaintext" style="font-size: 11px">
                                               ${get_format_date(suppliers.date)}</div>
                                    </div>
                                    <div class="col-4 text-end ">
                                        <div class="form-control-plaintext truncate-text" style="font-size: 11px">
                                               ${suppliers.createdby.full_name}

                                        </div>
                                    </div>
                                  
                                </div>
                            </button>
                        </li>`;

                                $('#short-list').append(ims);
                            });
                        } else {
                            $('#short-list').html('<div class="p-2">No results found</div>');
                        }
                    }
                });
            });

        });
    </script>


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

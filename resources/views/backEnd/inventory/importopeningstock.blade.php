@extends('backEnd.newmasterpage')
@section('mainContent')

    <style>
        .venus-app .table.table-hover td {
            background-color: inherit;
            padding: 5px 5px;
            vertical-align: middle;
        }
    </style>


    <style>
        .venus-app .table.table-hover td {
            padding: 1px 5px;
        }
    </style>

    <style>
        #long-list td,
        #long-list th {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #long-list tr.expand td {
            white-space: normal !important;
            overflow: visible !important;
            text-overflow: unset !important;
            height: auto !important;
        }

        /* Optional for pointer on rows */
        #long-list tbody tr {
            cursor: pointer;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#long-list tbody tr').on('click', function() {
                $(this).toggleClass('expand');
            });
        });
    </script>



    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>







    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel" aria-labelledby="purchase-order-1-tab">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left mt-2">
                        Opening Stock Import
                    </h4>
                    <div class="purchase-order-content-header-right">
 <div class="dropdown">
                        <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ico icon-outline-hamburger-menu"></i>
                        </button>
                        <ul class="dropdown-menu" style="">
                                 <li><a href="{{ url('item-store/show') }}" class="dropdown-item">
                                    Opening Stock</a></li>
                                <li><a href="{{ url('item-add') }}" class="dropdown-item">
                                    Products</a></li>
                            <li><a href="{{ url('brand') }}" class="dropdown-item">
                                    Brand</a></li>

                            <li><a href="{{ url('item-category') }}" class="dropdown-item">
                                    Category</a></li>

                            <li><a href="{{ url('create-sub-category') }}" class="dropdown-item">
                                    Sub Category</a></li>

                        </ul>
                    </div>


                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                             {{ Form::open(['class' => 'form-horizontal','url' => 'item-store-import-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                                <div class="boxed-formctrl">
                                    <div class="add-visitor">
                                        <div class="row mb-10">
                                            <div class="col-lg-12">
                                                @if (session()->has('message-success'))
                                                    <div class="alert alert-success mb-20">
                                                        {{ session()->get('message-success') }}
                                                    </div>
                                                @elseif(session()->has('message-danger'))
                                                    <div class="alert alert-danger">
                                                        {{ session()->get('message-danger') }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-lg-3 mb-2">
                                                <div class="input-effect">
                                                    <label class="txtlbl">Choose File<span>*.csv</span> (<a href="{{ url('public/uploads/product_upload/opening_stock_import_sample_file.xlsx') }}" target="_blank">Sample File</a>)</label>
                                                    <input class="form-control" type="file" accept=".csv"
                                                        name="import_file" required>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="input-effect d-flex gap-2" style="margin-top: 12px">

                                                    <button class="btn btn-light  mt-2">
                                                        Submit
                                                    </button>
                                                    @if (count($data) > 0)
                                                        <a href="{{ url('item-store-import-clear') }}"
                                                            class="btn btn-light  mt-2">Clear Data</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>



                            <div class="col-lg-12 " id="long-list" style="overflow: scroll;">
                                <table class="table table-hover" cellspacing="0">
                                    <thead>
                                        @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                            <tr>
                                                <td colspan="11">
                                                    @if (session()->has('message-success-delete'))
                                                        <div class="alert alert-success">
                                                            {{ session()->get('message-success-delete') }}
                                                        </div>
                                                    @elseif(session()->has('message-danger-delete'))
                                                        <div class="alert alert-danger">
                                                            {{ session()->get('message-danger-delete') }}
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        <tr >
                                      <th width="350px">Part Number</th>
                                    <th width="250px" class="text-center">Qty In</th>
                                    <th width="250px" class="text-end">Price In</th>
                                    <th width="250px" class="text-end">Serial No</th>
                                    <th width="250px" class="text-end">Remarks</th>
                                    <th class="text-center">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @if (count($data)>0)
                                    @foreach ($data as $value)
                                    @php
                                        $part_number_id = $partnumber->where('part_number',$value->partno)->max('part_number');
                                    @endphp

                                        <tr>
                                            <td @if($part_number_id == "") class="bg-warning" @endif>{{ @$value->partno }}</td>
                                            <td class="text-center">{{ @$value->qty_in }}</td>
                                            <td class="text-end">{{ @$value->price_in }}</td>
                                            <td class="text-end">{{ @$value->slno }}</td>
                                            <td class="text-end">{{ @$value->remarks }}</td>
                                            <td class="text-center">
                                                <a href="{{ url('item-store-import-delete', $value->id) }}"
                                                    class=" btn-light btn-sm" >
                                                   <i class="ico icon-bold-trash-bin-2" style="font-size: 16px;"></i>
                                                </a>
                                            </td>
                                    @endforeach
                                @endif
                                    </tbody>
                                    <?php    try { ?>
                                    <footer>
                                        <tr>
                                            <td colspan="11">

                                            </td>
                                        </tr>
                                    </footer>
                                    <?php    } catch (\Exception $e) {
        } ?>
                                </table>
                            </div>
                            @if (count($data) > 0)
                                <div class="col-lg-12 text-center">
                    {{ Form::open(['class' => 'form-horizontal','url' => 'item-store-import-data', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                             

                                    @if (session()->has('message-success'))
                                        <div class="alert alert-success mb-20">
                                            {{ session()->get('message-success') }}
                                        </div>
                                    @elseif(session()->has('message-danger'))
                                        <div class="alert alert-danger">
                                            {{ session()->get('message-danger') }}
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-center mt-2">
                                        <button class="btn btn-light">
                                            <i class="ico icon-outline-import" style="font-size: 16px"></i> Import Data
                                        </button>
                                    </div>
                                </div>
                                {{ Form::close() }}
                        </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>

    </div>
    </div>




    <script>
        $(document).ready(function() {
            // Stop user to press enter in textbox
            $("input:text").keypress(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });
        });
    </script>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection

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

    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Quotations List
                </h4>

            </div>

        </div>

        <div class="left-nav-list">


            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th style="width: 90px;"  class="text-center">@lang('Doc Date')</th>
                            <th style="width: 90px;" class="text-center">@lang('QTN No')</th>
                            <th style="width: 90px;" class="text-center">@lang('Deal Number')</th>
                            <th style="width: 200px;">@lang('Customer Name')</th>
                            <th style="width: 150px;">@lang('Salesman Name')</th>
                            <th style="width: 90px;" class="text-end">@lang('Amount')</th>
                            <th class="text-center" style="width: 90px;">@lang('Action')</th>
                        </tr>
                    </thead>


                    <tbody>

                        @php
                            $count = 1;
                            $total_deal = 0;
                            $total_amount = 0;
                        @endphp
                        @foreach ($quotations as $value)
                      
                            @php $total_deal += 1; @endphp
                            <tr>
                                <td  class="text-center">{{ date('d/m/Y', strtotime(@$value->date)) }}</td>
                                <td  class="text-center"><a class=""
                                        href="{{ url('crm-quote/' . $value->id . '/download/' . $value->quote_id) }}">{{ @$value->code }}</a>
                                </td>
                               
                                <td  class="text-center"><a target="_blank" class=""
                                        href="{{ url('crm-deal-track-approval/' . @$value->track->id) }}">{{ @$value->code }}</a>
                                </td>
                                <td>
                                    
                                        {{ @$value->customername->name }}
                                </td>

                                <td>{{ @$value->ownername->full_name }}</td>
                                <td class="text-end ">
                                    {{ @App\SysHelper::currancy_format_deal($value->deal_value, $value->company_id) }}
                                    @php $total_amount += $value->deal_value; @endphp
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a class="btn btn-sm btn-light"
                                            href="{{ url('crm-quote/' . $value->id . '/download/' . $value->quote_id) }}"
                                            class="btn-small"><i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i></a>
                                        {{-- <a class="btn btn-sm btn-light" href="{{ url('crm-deals/' . $value->id . '/view') }}"><i
                                                class="ico icon-outline-eye" aria-hidden="true"></i></a> --}}
                                    </div>

                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                    <?php try{ ?>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class="text-end pr-1">
                                {{ @App\SysHelper::currancy_format_deal($total_amount, $value->company_id) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                    <?php }catch (\Exception $e) { } ?>

                </table>
            </div>
        </div>
    </aside>






    @include('backEnd.inventory.itemAddModal')

    @include('backEnd.inventory.itemEditModal')







    <div class="modal fade" id="ModalMergeDuplicateProduct" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 464px !important;">

            {!! Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'product-merge-duplicate',
                'method' => 'post',
                'id' => 'product-merge-duplicate',
            ]) !!}

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Merge Duplicate Product</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>


                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">

                            <div class="form-control" style="height: 300px; overflow-y: scroll; overflow-x: hidden;">
                                @foreach ($dup_item_list as $index => $data)
                                    @php $inputId = 'dup_part_no_' . $index; @endphp
                                    <input type="checkbox" id="{{ $inputId }}" name="dup_part_no[]"
                                        value="{{ $data }}" checked>
                                    <label for="{{ $inputId }}">{{ $data }}</label><br>
                                @endforeach
                            </div>


                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" onclick="return confirm('Are you sure you want to Merge this?');"
                        class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Merge
                    </button>
                </div>
            </div>
            {!! Form::close() !!}



        </div>
    </div>



    <div class="modal fade" id="ModalMergeProduct" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="height: 464px !important;">

            {!! Form::open([
                'class' => 'form-horizontal',
                'files' => true,
                'url' => 'product-merge',
                'method' => 'post',
                'id' => 'product-merge',
            ]) !!}


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Merge Product</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>


                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">

                            <div class="row">
                                <div class="col-md-6">From Part Number
                                    <select id="from_partno" name="from_partno[]"
                                        class="form-control js-example-basic-single" multiple required>
                                        @foreach ($item_list as $data)
                                            <option value="{{ $data->id }}">{{ $data->part_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">To Part Number
                                    <select id="to_partno" name="to_partno" class="form-control js-example-basic-single"
                                        required>
                                        <option value="">Select</option>
                                        @foreach ($item_list as $data)
                                            <option value="{{ $data->id }}">{{ $data->part_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" onclick="return confirm('Are you sure you want to Merge this?');"
                        class="btn btn-light add-btn ms-2">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Merge
                    </button>
                </div>
            </div>
            {!! Form::close() !!}



        </div>
    </div>

    <div class="modal fade" id="add_to_do" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog " style="height: 464px !important;">



            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="poexcelimport">Product Detail</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>


                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0 border-0 shadow-none">
                        <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
                            <style>
                                .modal-body .item-label {
                                    font-weight: 500;
                                    color: #444;
                                    min-width: 140px;
                                }

                                .modal-body .item-value {
                                    color: #222;
                                    font-weight: 400;
                                }

                                .modal-body .info-row {
                                    display: flex;

                                    padding: 4px 0;
                                    border-bottom: 1px dashed #ddd;
                                }
                            </style>

                            <div class="modal-body">
                                <div class="info-row">
                                    <div class="item-label">Item Code</div>
                                    <div class="item-value" id="lbl_item_code"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Part Number</div>
                                    <div class="item-value" id="lbl_part_number"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Description</div>
                                    <div class="item-value" id="lbl_description"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Category</div>
                                    <div class="item-value" id="lbl_category_name"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Sub Category</div>
                                    <div class="item-value" id="lbl_subcategory_name"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Brand</div>
                                    <div class="item-value" id="lbl_brand"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Product Type</div>
                                    <div class="item-value" id="lbl_product_type"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">VAT</div>
                                    <div class="item-value" id="lbl_vat"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">UOM</div>
                                    <div class="item-value" id="lbl_uom"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">COO</div>
                                    <div class="item-value" id="lbl_coo"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">HS Code</div>
                                    <div class="item-value" id="lbl_hscode"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Weight</div>
                                    <div class="item-value" id="lbl_weight"></div>
                                </div>
                                <div class="info-row">
                                    <div class="item-label">Created:</div>
                                    <div class="item-value" id="lbl_created_info"></div>
                                </div>

                                <div class="info-row">
                                    <div class="item-label">Updated:</div>
                                    <div class="item-value" id="lbl_updated_info"></div>
                                </div>

                                {{-- <div class="info-row">
                                    <div class="item-label">Created Date</div>
                                    <div class="item-value" id="lbl_created_at"></div>
                                </div> --}}
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ico icon-outline-close"></i> Close
                    </button>

                </div>
            </div>




        </div>
    </div>
    <script>
        function fn_data(id) {
            var item_code = $('#item_code_' + id + '').val();
            var part_number = $('#part_number_' + id + '').val();
            var description = $('#description_' + id + '').val();
            var category_name = $('#category_name_' + id + '').val();
            var subcategory_name = $('#subcategory_name_' + id + '').val();
            var brand = $('#brand_' + id + '').val();
            var product_type = $('#product_type_' + id + '').val();
            var vat = $('#vat_' + id + '').val();
            var uom = $('#uom_' + id + '').val();
            var coo = $('#coo_' + id + '').val();
            var hscode = $('#hscode_' + id + '').val();
            var weight = $('#weight_' + id + '').val();
            var created_by = $('#created_by_' + id + '').val();
            var created_at = $('#created_at_' + id + '').val();

            var updated_by = $('#updated_by_' + id + '').val();
            var updated_at = $('#updated_at_' + id + '').val();

            $('#lbl_created_info').text(created_by + ' (' + created_at + ')');
            $('#lbl_updated_info').text(updated_by + ' (' + updated_at + ')');

            $('#lbl_item_code').html(item_code);
            $('#lbl_part_number').html(part_number);
            $('#lbl_description').html(description);
            $('#lbl_category_name').html(category_name);
            $('#lbl_subcategory_name').html(subcategory_name);
            $('#lbl_brand').html(brand);
            $('#lbl_product_type').html(product_type);
            $('#lbl_vat').html(vat);
            $('#lbl_uom').html(uom);
            $('#lbl_coo').html(coo);
            $('#lbl_hscode').html(hscode);
            $('#lbl_weight').html(weight);
            $('#lbl_created_by').html(created_by);
            $('#lbl_created_at').html(created_at);
        }
    </script>

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection

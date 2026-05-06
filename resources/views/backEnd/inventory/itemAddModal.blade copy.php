<?php

$itemCategories = @App\SmItemCategory::orderby('category_name', 'asc')->get();
$SuCategories = @App\SmItemSubcategory::orderby('sub_category_name', 'asc')->get();
$brands = @App\SysBrand::orderby('title', 'asc')->get();
$producttype = @App\SysProductType::get();

?>
<form id="productForm">
    @csrf
    <div class="modal side-panel fade" id="addproductModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="height: 464px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="lbladdproductModal">Add Product</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Brand:</label>
                                    <div class="form-group">
                                        <select class="form-control" name="brand" id="brand" required>
                                            <option value=""></option>
                                            @foreach ($brands as $key => $value)
                                                <option value="{{ @$value->id }}">{{ @$value->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Product Code:</label>
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="item_code"
                                            value="{{ @App\SysHelper::get_new_code('sm_items', 'ITM', 'item_code') }}"
                                            required readonly>
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label">Part Number:</label>
                                    <div class="form-group">
                                        <input class="form-control" type="text" id="part_number" name="part_number"
                                            autocomplete="off" required value="">
                                        <div id="part_number_list">
                                        </div>
                                        <script>
                                            $(document).ready(function() {

                                                $('#part_number').keyup(function() {
                                                    var query = $(this).val();
                                                    if (query != '') {
                                                        var _token = $('input[name="_token"]').val();
                                                        $.ajax({
                                                            url: "{{ route('autocomplete.fetch_product_partnumber') }}",
                                                            method: "POST",
                                                            data: {
                                                                query: query,
                                                                _token: _token
                                                            },
                                                            success: function(data) {
                                                                $('#part_number_list').fadeIn();
                                                                $('#part_number_list').html(data);
                                                            }
                                                        });
                                                    }
                                                });

                                                $(document).on('click', 'li', function() {
                                                    $('#part_number').val($(this).text());
                                                    $('#part_number_list').fadeOut();
                                                });

                                                $(document).click(function(e) {
                                                    if (!$(e.target).closest('#part_number, #part_number_list').length) {
                                                        $('#part_number_list').fadeOut();
                                                    }
                                                });

                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label">Description:</label>
                                    <div class="form-group">
                                        <input class="form-control" name="description" id="description" value=""
                                            required />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Category:</label>
                                    <div class="form-group">
                                        <select class="form-control" name="category_name" id="category_name" required>
                                            <option value=""></option>
                                            @foreach ($itemCategories as $key => $value)
                                                <option value="{{ @$value->id }}">{{ @$value->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Sub Category:</label>
                                    <div class="form-group">
                                        <select class="form-control" name="subcategory_name"
                                            id="sectionSelectSubcategory" required>
                                            <option value=""></option>
                                            @foreach ($SuCategories as $key => $value)
                                                <option value="{{ @$value->id }}">{{ @$value->sub_category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Product Type:</label>
                                    <div class="form-group">
                                        <select class="form-control" name="product_type" id="product_type" required>
                                            @foreach ($producttype as $key => $value)
                                                <option value="{{ @$value->id }}">{{ @$value->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">VAT:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="vat" name="vat" />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">UOM:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="uom" name="uom" />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">COO:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="coo" name="coo" />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">HS Code:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="hscode" name="hscode" />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Weight:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="weight" name="weight" />
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2" id="add-btn-modal">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#add-btn-modal').on('click', function(e) {
            e.preventDefault();

            var formData = $('#productForm').serialize();

            $.ajax({
                url: "{{ route('product.modalsave') }}", // Update with your route name
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert('Product saved successfully.');
                        $('#addproductModal').modal('hide'); // optional
                        // Optionally reload table or clear form
                    } else {
                        alert('Something went wrong.');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('An error occurred. Please check console.');
                }
            });
        });
    });
</script>

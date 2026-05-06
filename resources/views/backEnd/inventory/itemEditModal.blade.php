<?php

$itemCategories = @App\SmItemCategory::orderby('category_name', 'asc')->get();
$SuCategories = @App\SmItemSubcategory::orderby('sub_category_name', 'asc')->get();
$brands = @App\SysBrand::orderby('title', 'asc')->get();
$producttype = @App\SysProductType::get();

?>
<form id="editProductForm">
    @csrf
    <div class="modal side-panel fade" id="editproductModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="height: 564px !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="lbladdproductModal">Edit Product (<span
                            id="edit_part_number_heading">...</span>)</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body">
                            <div class="row">
                                <input type="hidden" name="edit_product_id" id="edit_product_id">
                                <div class="col-6 mb-2">
                                    <label class="form-label">Brand:
                                        <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#addBrandModal" title="Add Brand"><i class="ico icon-outline-add-square"></i></a>
                                    </label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="brand" id="edit_brand" required>
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
                                        <input class="form-control" id="edit_item_code" type="text" name="item_code"
                                            required readonly>
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label">Part Number:</label>
                                    <div class="form-group">
                                        <input class="form-control" type="text" id="edit_part_number"
                                            name="part_number" autocomplete="off" required value="">

                                        <div id="edit_part_number_list">
                                        </div>
                                        <script>
                                            $(document).ready(function() {

                                                $('#edit_part_number').keyup(function() {
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
                                                                $('#edit_part_number_list').fadeIn();
                                                                $('#edit_part_number_list').html(data);
                                                            }
                                                        });
                                                    }
                                                });

                                                $(document).on('click', 'li', function() {
                                                    $('#edit_part_number').val($(this).text());
                                                    $('#edit_part_number_list').fadeOut();
                                                });

                                                $(document).click(function(e) {
                                                    if (!$(e.target).closest('#edit_part_number, #edit_part_number_list').length) {
                                                        $('#edit_part_number_list').fadeOut();
                                                    }
                                                });

                                            });
                                        </script>

                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label">Description:</label>
                                    <div class="form-group">
                                        <input class="form-control" name="description" id="edit_description"
                                            value="" required />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Category:
                                        <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal" title="Add Category"><i class="ico icon-outline-add-square"></i></a>
                                    </label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="category_name" id="edit_category_name"
                                            required>
                                            <option value=""></option>
                                            @foreach ($itemCategories as $key => $value)
                                                <option value="{{ @$value->id }}">{{ @$value->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Sub Category:
                                        <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#addSubCategoryModal" title="Add Sub Category"><i class="ico icon-outline-add-square"></i></a>
                                    </label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="subcategory_name" id="edit_subcategory_name"
                                            required>
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
                                        <select class="form-control js-example-basic-single" name="product_type" id="edit_product_type"
                                            required>
                                            @foreach ($producttype as $key => $value)
                                                <option value="{{ @$value->id }}">{{ @$value->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-6 mb-2">
                                    <label class="form-label">VAT:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="edit_vat" name="vat" />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">UOM:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="edit_uom" name="uom" />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">COO:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="edit_coo" name="coo" />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">HS Code:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="edit_hscode"
                                            name="hscode" />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Weight:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="edit_weight"
                                            name="weight" />
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label">Company</label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="company_id[]" id="edit_company_id" multiple required>
                                            @foreach (App\SysCompany::all() as $company)
                                                <option value="{{ @$company->id }}">{{ @$company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light add-btn ms-2" id="update-product-btn">
                        <i class="ico icon-outline-bookmark-opened text-success"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>



<script>
    function openEditModal(product) {
        $('#edit_part_number_heading').text(product.item_code);
        $('#edit_product_id').val(product.id);
        $('#edit_item_code').val(product.item_code);
        $('#edit_part_number').val(product.part_number);
        $('#edit_brand').val(product.brand).trigger('change');
        $('#edit_description').val(product.description);
        $('#edit_category_name').val(product.category_name).trigger('change');
        $('#edit_subcategory_name').val(product.subcategory_name).trigger('change');
        $('#edit_product_type').val(product.product_type).trigger('change');
        $('#edit_company_id').val(product.company_id ? product.company_id.split(',') : []).trigger('change');
        $('#edit_vat').val(product.vat);
        $('#edit_uom').val(product.uom);
        $('#edit_coo').val(product.coo);
        $('#edit_hscode').val(product.hscode);
        $('#edit_weight').val(product.weight);
    }
</script>

<script>
    $(document).on('click', '.edit-product-btn', function(e) {
        e.preventDefault();
        const productId = $(this).data('id');

        $('#loading_bg').show();


        $.ajax({
            url: "{{ url('product/get') }}/" + productId, // adjust route
            method: "GET",
            success: function(product) {
                openEditModal(product); // fill the form
                $('#loading_bg').hide();
                $('#editproductModal').modal('show');
            },
            error: function(xhr) {
                $('#loading_bg').hide();
                console.error(xhr.responseText);
                alert('Failed to fetch product details.');
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#update-product-btn').on('click', function(e) {
            e.preventDefault();

            let formData = $('#editProductForm').serialize();

            $.ajax({
                url: "{{ route('product.modalupdate') }}", // Adjust this route
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        alert('Product updated successfully!');
                        $('#editproductModal').modal('hide');

                        // Optionally refresh your product table here
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        alert('Update failed. Please check and try again.');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('An unexpected error occurred.');
                }
            });
        });
    });
</script>

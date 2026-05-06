<?php

$itemCategories = @App\SmItemCategory::orderby('category_name', 'asc')->get();
$SuCategories = @App\SmItemSubcategory::orderby('sub_category_name', 'asc')->get();
$brands = @App\SysBrand::orderby('title', 'asc')->get();
$producttype = @App\SysProductType::get();
$company = @App\SysHelper::get_company_names();


?>
<form id="productForm">
    <?php echo csrf_field(); ?>
    <div class="modal side-panel fade" id="addproductModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="height: 564px !important;">
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
                                    <label class="form-label">Brand:
                                        <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#addBrandModal" title="Add Brand"><i class="ico icon-outline-add-square"></i></a>
                                    </label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="brand" id="brand" required>
                                            <option value=""></option>
                                            <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>"><?php echo e(@$value->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Product Code:</label>
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="item_code"
                                            value="<?php echo e(@App\SysHelper::get_new_product_code('sm_items', 'ITM', 'item_code')); ?>"
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
                                                            url: "<?php echo e(route('autocomplete.fetch_product_partnumber')); ?>",
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

                                                $(document).on('click', '#part_number_list li', function() {
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
                                    <label class="form-label">Category:
                                        <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal" title="Add Category"><i class="ico icon-outline-add-square"></i></a>
                                    </label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="category_name" id="category_name" required>
                                            <option value=""></option>
                                            <?php $__currentLoopData = $itemCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>"><?php echo e(@$value->category_name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Sub Category:
                                        <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#addSubCategoryModal" title="Add Sub Category"><i class="ico icon-outline-add-square"></i></a>
                                    </label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="subcategory_name"
                                            id="sectionSelectSubcategory" required>
                                            <option value=""></option>
                                            <?php $__currentLoopData = $SuCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>"><?php echo e(@$value->sub_category_name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Product Type:</label>
                                    <div class="form-group">
                                        <select class="form-control js-example-basic-single" name="product_type" id="product_type" required>
                                            <?php $__currentLoopData = $producttype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>"><?php echo e(@$value->title); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">VAT:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="vat" name="vat"
                                            value="5" />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">UOM:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="uom" name="uom"
                                            value="0" />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">COO:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="coo" name="coo"
                                            value="0" />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">HS Code:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="hscode" name="hscode"
                                            value="0" />
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <label class="form-label">Weight:</label>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="weight" name="weight"
                                            value="0" />
                                    </div>
                                </div>

                                <div class="col-12 mb-2">
                                    <label class="form-label">Company:</label>
                                    <div class="form-group">
                                        <select
                                            class="form-control js-example-basic-single" name="company_id[]" id="company_id" multiple required>
                                            
                                            <?php $__currentLoopData = $company; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e(@$value->id); ?>" selected ><?php echo e(@$value->company_name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                           
                                        </select>
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

<!-- Brand add popup (reused from Brand module) -->
<div class="modal side-panel fade" id="addBrandModal" data-bs-backdrop="false" style="z-index: 11000 !important;" tabindex="-1" aria-labelledby="addBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" style="height: 143px !important;z-index: 1050;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addBrandModalLabel">Add Brand</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0 border-0 shadow-none">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Brand Name<span>*</span></label>
                                <input type="text" class="form-control" id="new_brand_title" name="title" autocomplete="off" required>
                                <div id="new_brand_error" class="text-danger mt-1" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" id="add-brand-button">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Category add popup -->
<div class="modal side-panel fade" id="addCategoryModal" style="z-index: 11000 !important;" data-bs-backdrop="false" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" style="height: 143px !important; z-index: 1050;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addCategoryModalLabel">Add Category</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0 border-0 shadow-none">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-12">
                                <label class="form-label">Category Name<span>*</span></label>
                                <input type="text" class="form-control" id="new_category_name" name="category_name" autocomplete="off" required>
                                <div id="new_category_error" class="text-danger mt-1" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" id="add-category-button">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sub Category add popup -->
<div class="modal side-panel fade" id="addSubCategoryModal" style="z-index: 11000 !important;" data-bs-backdrop="false" tabindex="-1" aria-labelledby="addSubCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" style="height: 192px !important; z-index: 1050;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="addSubCategoryModalLabel">Add Sub Category</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-0 p-0">
                <div class="card mb-0 mt-0 border-0 shadow-none">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label class="form-label">Category<span>*</span></label>
                                <select class="form-control js-example-basic-single" id="new_subcategory_category_id" name="category" required>
                                    <option value=""></option>
                                    <?php $__currentLoopData = $itemCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e(@$value->id); ?>"><?php echo e(@$value->category_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Sub Category Name<span>*</span></label>
                                <input type="text" class="form-control" id="new_subcategory_name" name="sub_category_name" autocomplete="off" required>
                                <div id="new_subcategory_error" class="text-danger mt-1" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light add-btn ms-2" id="add-subcategory-button">
                    <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#add-brand-button').on('click', function (e) {
        e.preventDefault();
        var title = $('#new_brand_title').val().trim();
        if (!title) {
            $('#new_brand_error').text('Brand name is required').show();
            return;
        }
        var token = $('input[name="_token"]').val();

        $.ajax({
            url: '<?php echo e(url("brand")); ?>',
            type: 'POST',
            data: {
                _token: token,
                title: title
            },
            success: function (response) {
                if (response.success && response.id) {
                    var $newOption = $('<option>').val(response.id).text(response.title).prop('selected', true);
                    $('#brand').append($newOption).trigger('change');
                    $('#edit_brand').append($newOption.clone().prop('selected', true)).trigger('change');
                    $('#addBrandModal').modal('hide');
                    $('#new_brand_title').val('');
                    $('#new_brand_error').hide();
                } else if (response.type === 'duplicate') {
                    $('#new_brand_error').text(response.message).show();
                } else {
                    $('#new_brand_error').text('Unable to add brand').show();
                }
            },
            error: function (xhr) {
                $('#new_brand_error').text('An error occurred while saving brand').show();
            }
        });
    });

    $('#add-category-button').on('click', function (e) {
        e.preventDefault();
        var name = $('#new_category_name').val().trim();
        if (!name) {
            $('#new_category_error').text('Category name is required').show();
            return;
        }
        var token = $('input[name="_token"]').val();

        $.ajax({
            url: '<?php echo e(url("item-category")); ?>',
            type: 'POST',
            data: {
                _token: token,
                category_name: name
            },
            success: function (response) {
                if (response.success && response.id) {
                    var $newOption = $('<option>').val(response.id).text(response.category_name).prop('selected', true);
                    $('#category_name').append($newOption).trigger('change');
                    $('#edit_category_name').append($newOption.clone().prop('selected', true)).trigger('change');
                    $('#new_subcategory_category_id').append($newOption.clone()).trigger('change');
                    $('#addCategoryModal').modal('hide');
                    $('#new_category_name').val('');
                    $('#new_category_error').hide();
                } else if (response.type === 'duplicate') {
                    $('#new_category_error').text(response.message).show();
                } else {
                    $('#new_category_error').text('Unable to add category').show();
                }
            },
            error: function (xhr) {
                $('#new_category_error').text('An error occurred while saving category').show();
            }
        });
    });

    $('#add-subcategory-button').on('click', function (e) {
        e.preventDefault();

        var categoryId = $('#new_subcategory_category_id').val();
        var name = $('#new_subcategory_name').val().trim();

        if (!categoryId) {
            $('#new_subcategory_error').text('Please select a category').show();
            return;
        }
        if (!name) {
            $('#new_subcategory_error').text('Sub category name is required').show();
            return;
        }

        var token = $('input[name="_token"]').val();

        $.ajax({
            url: '<?php echo e(url("store-item-sub-category")); ?>',
            type: 'POST',
            data: {
                _token: token,
                category: categoryId,
                sub_category_name: name
            },
            success: function (response) {
                if (response.success && response.id) {
                    var $newOption = $('<option>').val(response.id).text(response.sub_category_name).prop('selected', true);
                    $('#sectionSelectSubcategory').append($newOption).trigger('change');
                    $('#edit_subcategory_name').append($newOption.clone().prop('selected', true)).trigger('change');
                    
                    // also set the parent category selects to the selected category
                    var selectedCategory = $('#new_subcategory_category_id').val();
                    if (selectedCategory) {
                        $('#category_name').val(selectedCategory).trigger('change');
                        $('#edit_category_name').val(selectedCategory).trigger('change');
                    }

                    $('#addSubCategoryModal').modal('hide');
                    $('#new_subcategory_category_id').val('').trigger('change');
                    $('#new_subcategory_name').val('');
                    $('#new_subcategory_error').hide();
                } else if (response.type === 'duplicate') {
                    $('#new_subcategory_error').text(response.message).show();
                } else {
                    $('#new_subcategory_error').text('Unable to add sub category').show();
                }
            },
            error: function (xhr) {
                $('#new_subcategory_error').text('An error occurred while saving sub category').show();
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#add-btn-modal').on('click', function(e) {
            e.preventDefault();

            var formData = $('#productForm').serialize();

            $.ajax({
                url: "<?php echo e(route('product.modalsave')); ?>", // Update with your route name
                type: "POST",
                data: formData,
                success: function(response) {

                    // ✅ Product saved
                    if (response.success) {

                        $('#addproductModal').modal('hide');

                        if (window.shouldRefreshAfterProductAdd) {
                            location.reload();
                        } else {
                            // Inject the new product into the first empty part_number select in #myTable
                            window.injectNewProductIntoTable(response);
                        }

                    }
                    // ❌ Duplicate part number
                    else if (response.type === 'duplicate') {

                        // Show error near input or alert
                        $('#part_number_error')
                            .text(response.message)
                            .show();

                        // Optional: highlight input
                        $('#part_number').addClass('is-invalid');

                        //show toastr
                        toastr.error(response.message, 'Error', {
                            closeButton: true,
                            progressBar: true,
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('An error occurred. Please check console.');
                }
            });
        });

        $('#addproductModal').on('show.bs.modal', function () {
            var form = $('#productForm')[0];
            if (form) {
                form.reset();
            }

            // Reset any Select2 or dynamic controls
            $('#brand, #category_name, #sectionSelectSubcategory, #product_type, #company_id').trigger('change');
            $('#part_number_list').hide().empty();
            $('#part_number').removeClass('is-invalid');
            $('#new_brand_error, #new_category_error, #new_subcategory_error, #part_number_error').hide();
        });
    });

    /**
     * Universal function: after a product is created via the modal, find the first empty
     * select[name="part_number[]"] inside #myTable, inject the product as a Select2 option,
     * select it, and directly fill all row fields (mirrors the select2:select handler).
     * Works on any page (Deals, PO, GRN, etc.).
     */
    window.injectNewProductIntoTable = function(product) {
        if (!product || !product.id) return;

        // Find the table — prefer #myTable, fall back to first table containing part_number selects
        var $table = $('#myTable');
        if (!$table.length) {
            $table = $('select[name="part_number[]"]').first().closest('table');
        }
        if (!$table.length) return;

        // Find the first select[name="part_number[]"] whose current value is empty
        var $targetSelect = null;
        $table.find('select[name="part_number[]"]').each(function() {
            var val = $(this).val();
            if (!val || val === '') {
                $targetSelect = $(this);
                return false; // break
            }
        });

        if (!$targetSelect) return; // all rows already filled

        var $row = $targetSelect.closest('tr');

        // Inject the new product as a plain <option> and mark it selected
        var newOption = new Option(product.part_number || '', product.id, true, true);
        $targetSelect.append(newOption);

        // If Select2 is already initialised on this element, sync its display value
        if ($targetSelect.hasClass('select2-hidden-accessible')) {
            $targetSelect.val(product.id).trigger('change');
        }

        // Directly fill all row fields — same logic as the select2:select handler on every page
        $row.find('textarea[name="description[]"]').val(product.description || '');
        $row.find('input[name="description[]"]').val(product.description || '');
        $row.find('input[name="part_number_txt[]"]').val(product.part_number || '');
        $row.find('input[name="hscode_txt[]"]').val(product.hscode || '');
        $row.find('input[name="product_type[]"]').val(product.product_type || '');
        $row.find('input[name="product_type_part_number_text[]"]').val(product.description || '');
        $row.find('input[name="discount[]"]').val(0);

        // Tax: use the page's net_vat value if available, else fall back to product vat
        var vatVal = $('#net_vat').val();
        if (vatVal === undefined || vatVal === '') vatVal = product.vat || 0;
        $row.find('input[name="tax[]"]').val(parseInt(vatVal) || 0);

        // Trigger row calculation if the page has one
        var $costInput = $row.find('input[name="cost[]"]');
        if (typeof calc_change_new === 'function') {
            calc_change_new($costInput[0] || $row[0]);
        }

        // Focus cost input ready for entry
        $costInput.focus();
    };
</script>

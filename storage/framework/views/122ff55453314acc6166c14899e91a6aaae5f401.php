<?php $__env->startSection('mainContent'); ?>

    
    <script>
        function setCompanyView(mode) {
            const leftNav = document.getElementById('leftSidebar');
            const content = document.querySelector('.content-container');

            const shortList = document.getElementById('companyShortList'); // UL
            const longTable = document.getElementById('long-list'); // TABLE

            const filtersShort = document.getElementById('filters-short');
            const filtersLong = document.getElementById('filters-long');

            if (mode === 'full') {
                if (leftNav.classList.contains('col-3')) {
                    leftNav.classList.remove('col-3');
                    leftNav.classList.add('col-12');
                }
                leftNav.style.width = '100%';
                content.classList.add('d-none');

                longTable && longTable.classList.remove('d-none');
                shortList && shortList.classList.add('d-none');

                filtersLong && filtersLong.classList.remove('d-none');
                filtersShort && filtersShort.classList.add('d-none');

                leftNav.dataset.view = 'full';
            } else {
                if (leftNav.classList.contains('col-12')) {
                    leftNav.classList.remove('col-12');
                    leftNav.classList.add('col-3');
                }
                leftNav.style.width = '';
                content.classList.remove('d-none');

                longTable && longTable.classList.add('d-none');
                shortList && shortList.classList.remove('d-none');

                filtersShort && filtersShort.classList.remove('d-none');
                filtersLong && filtersLong.classList.add('d-none');

                leftNav.dataset.view = 'compact';
            }
        }

        function list_style_new_company() {
            const leftNav = document.getElementById('leftSidebar');
            const cur = leftNav.dataset.view || 'compact';
            setCompanyView(cur === 'compact' ? 'full' : 'compact');
        }
        document.addEventListener('DOMContentLoaded', function() {
            const leftNav = document.getElementById('leftSidebar');
            if (!leftNav.dataset.view) leftNav.dataset.view = 'compact';
        });
    </script>
    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    ?>

    <?php try { ?>

    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>

        
        <div class="short-list" id="filters-short">
            <h4 class="mb-2">Companies</h4>

            
            <form class="form-horizontal" method="get" action="<?php echo e(route('company')); ?>" id="company-search">
                <div class="search-filter-container mb-4 d-flex">
                    <div class="input-group flex-nowrap">
                        <input type="text" name="q" class="form-control" placeholder="Search by Name "
                            value="<?php echo e(request('q') ?? ''); ?>">
                    </div>

                    <button type="button" class="btn btn-light ms-2" onclick="list_style_new_company()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </form>
        </div>

        
        <div class="long-list d-none" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-2">Company List</h4>
                <div class="search-filter-container mb-0">
                    <button class="btn btn-light"
                        onclick="document.getElementById('long-filters-box').classList.toggle('d-none')">
                        <i class="ico icon-outline-magnifer"></i>
                    </button>
                    <button class="btn btn-light" onclick="list_style_new_company()">
                        <i class="ico icon-outline-list-down"></i>
                    </button>
                </div>
            </div>

            <div id="long-filters-box" class="search-filter-container mt-1 mb-4 filter-field d-none border">
                <div class="card">
                    <div class="card-body">
                        <form class="form-horizontal" method="get" action="<?php echo e(route('company')); ?>" id="company-filter">
                            <div class="row">
                                <div class="col-3 mb-2">
                                    <label class="form-label">ID / Code</label>
                                    <input class="form-control" type="text" name="code" value="<?php echo e(request('code')); ?>">
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="form-label">Name</label>
                                    <input class="form-control" type="text" name="name" value="<?php echo e(request('name')); ?>">
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="form-label">Country</label>
                                    <input class="form-control" type="text" name="country"
                                        value="<?php echo e(request('country')); ?>">
                                </div>
                                <div class="col-3 mb-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-success w-100">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="left-nav-list">
            <ul id="companyShortList" class="nav flex-column nav-pills" role="tablist">
                <?php $companies = $company; ?>
                <?php if(count($companies) > 0): ?>
                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item w-100" role="presentation">
                            <button
                                class="nav-link co-item <?php echo e(isset($selectedCompany) && $selectedCompany && $selectedCompany->id == $c->id ? 'active' : ''); ?>"
                                data-id="<?php echo e($c->id); ?>" type="button" role="tab">

                                <div class="row w-100 align-items-start">
                                    <div class="col-12">

                                        
                                        <div class="row">
                                            <div class="col-7">
                                                <span class="form-control-plaintext fw-semibold truncate-text"
                                                    title="<?php echo e($c->company_name); ?>">
                                                    <?php echo e($c->company_name ?? '—'); ?>

                                                </span>

                                              
                                            </div>

                                            <div class="col-5 text-end">
                                                <span class="form-control-plaintext text-muted">
                                                    #<?php echo e($c->document_number); ?>

                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                </div>


                            </button>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="p-3 text-muted">No Records</div>
                <?php endif; ?>
            </ul>

            
            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover d-none" style="table-layout: fixed; width:100%">
                    <thead>
                        <tr>
                            <th style="width: 80px;" class="text-center">ID</th>
                            <th style="width: 220px;">Company Name</th>
                            <th style="width: 180px;">Trade Name</th>
                            <th style="width: 120px;">Country</th>
                            <th style="width: 140px;">City</th>
                            <th style="width: 140px;">Industry</th>
                            <th style="width: 140px;">Telephone</th>
                            <th style="width: 200px;">Email</th>
                            <th class="text-center" style="width: 110px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="text-center">
                                    <a href="javascript:void(0);" onclick="list_style_new_company()" class="co-item"
                                        data-id="<?php echo e($c->id); ?>">#<?php echo e($c->id); ?></a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" onclick="list_style_new_company()" class="co-item"
                                        data-id="<?php echo e($c->id); ?>">
                                        <?php echo e($c->company_name ?? '—'); ?>

                                    </a>
                                </td>
                                <td><?php echo e($c->trade_name ?? '—'); ?></td>
                                <td><?php echo e(optional($c->countryRelation)->name); ?></td>
                                <td><?php echo e($c->city ?? '—'); ?></td>
                                <td><?php echo e(optional($c->businessIndustry)->name ?? '—'); ?></td>
                                <td><?php echo e($c->telephone ?? '—'); ?></td>
                                <td class="truncate-text"><?php echo e($c->email ?? '—'); ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <a href="<?php echo e(url('company-edit/' . $c->id)); ?>" class="btn btn-sm btn-light"
                                            title="Edit">
                                            <i class="ico icon-outline-pen-2" style="font-size:16px;"></i>
                                        </a>
                                        <a href="<?php echo e(url('company/' . $c->id . '/delete')); ?>" class="btn btn-sm btn-light"
                                            onclick="return confirm('Are you sure?')" title="Delete">
                                            <i class="ico icon-bold-trash-bin-2" style="font-size:16px;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

        </div>
    </aside>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="companyTabContent">

            
            <script>
                (function() {
                    var detailsTpl = <?php echo json_encode(route('company.details', ['id' => ':id']), 512) ?>;

                    function buildUrl(tpl, id) {
                        return tpl.replace(':id', encodeURIComponent(id));
                    }

                    $(document).on('click', '.co-item', function(e) {
                        e.preventDefault();
                        var id = $(this).data('id');
                        if (!id) return;

                        $('.co-item').removeClass('active');
                        $('.co-item[data-id="' + id + '"]').addClass('active');

                        // Update URL (?active=id) for back/refresh
                        var newUrl = "<?php echo e(route('company')); ?>?active=" + encodeURIComponent(id);
                        if (window.history && window.history.pushState) {
                            window.history.pushState({
                                path: newUrl
                            }, '', newUrl);
                        }

                        var action = buildUrl(detailsTpl, id);
                        var $loader = $('#loading_bg');
                        if ($loader.length) $loader.show();

                        $.ajax({
                            url: action,
                            method: 'GET',
                            cache: false,
                            success: function(html) {
                                if (!html || !$.trim(html).length) {
                                    $('#co-details').html(
                                        '<p class="text-danger">No Details Available.</p>');
                                    return;
                                }
                                $('#co-details').html(html);
                            },
                            error: function(xhr) {
                                console.error('company-details error:', xhr.status, xhr.responseText);
                                $('#co-details').html('<p class="text-danger">No Details Available.</p>');
                            },
                            complete: function() {
                                if ($loader.length) $loader.hide();
                            }
                        });
                    });
                })();
            </script>

            <div role="tabpanel" aria-labelledby="co-tab" id="co-details">
                <?php
                    $firstCompany =
                        isset($selectedCompany) && $selectedCompany ? $selectedCompany : $companies->first() ?? null;
                ?>
                <?php if($firstCompany): ?>
                    <?php echo $__env->make('backEnd.company.company_details', ['company' => $firstCompany], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php else: ?>
                    <div class="container-fluid d-flex flex-column justify-content-center align-items-center"
                        style="min-height: 90vh;">

                        <!-- Icon + Heading -->
                        <a href="<?php echo e(url('company-add')); ?>" class="text-decoration-none text-dark">
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-success text-white d-flex justify-content-center align-items-center mx-auto"
                                    style="width: 80px; height: 80px; font-size: 36px; cursor:pointer">
                                    <i class="ico icon-outline-add-square"></i>
                                </div>
                                <h1 class="fw-bold mt-3">Add New Company</h1>
                                <p class="text-muted">Create and track your companies with ease</p>
                            </div>
                        </a>


                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    
    <script>
        $(function() {
            var $q = $('#company-search input[name="q"]');
            var $shortItems = $('#companyShortList > li'); // each li item
            var $longRows = $('#long-list tbody > tr'); // each table row

            function norm(s) {
                return (s || '').toString().toLowerCase();
            }

            function textOf($el) {
                return norm($el.text());
            }

            function applyFilter(needle) {
                if (!needle) {
                    $shortItems.show();
                    $longRows.show();
                    return;
                }
                // filter shortlist (li)
                $shortItems.each(function() {
                    var $li = $(this);
                    var hit = textOf($li).indexOf(needle) !== -1;
                    $li.toggle(hit);
                });
                // filter long table rows (tr)
                $longRows.each(function() {
                    var $tr = $(this);
                    var hit = textOf($tr).indexOf(needle) !== -1;
                    $tr.toggle(hit);
                });
            }

            // live filter on typing
            var deb;
            $q.on('input', function() {
                clearTimeout(deb);
                var needle = norm(this.value);
                deb = setTimeout(function() {
                    applyFilter(needle);
                }, 120);
            });

            // submit still goes to server (optional), so both work
        });

        // Function to show company documents
        function showDocuments(companyId, companyName) {
            // Set modal title
            document.getElementById('documentsModalLabel').innerText = companyName + ' - Documents';

            // Clear previous content
            const modalBody = document.getElementById('documentsModalBody');
            modalBody.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('documentsModal'));
            modal.show();

            // Fetch documents via AJAX
            fetch(`/company/${companyId}/documents`)
                .then(response => response.json())
                .then(data => {
                    let content = '';
                    if (data.documents && data.documents.length > 0) {
                        content =
                            '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>Document Name</th><th>Document Number</th><th>Date</th><th>Expiry</th><th>File</th></tr></thead><tbody>';
                        data.documents.forEach(doc => {
                            content += `<tr>
                                <td>${doc.document_name || '—'}</td>
                                <td>${doc.document_number || '—'}</td>
                                <td>${doc.document_date || '—'}</td>
                                <td>${doc.expiry_date || '—'}</td>
                                <td>${doc.attachment_file ? `<a href="${window.location.origin}/storage/${doc.attachment_file}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>` : '—'}</td>
                            </tr>`;
                        });
                        content += '</tbody></table></div>';
                    } else {
                        content = '<div class="text-center text-muted">No documents found</div>';
                    }
                    modalBody.innerHTML = content;
                })
                .catch(error => {
                    console.error('Error fetching documents:', error);
                    modalBody.innerHTML = '<div class="text-center text-danger">Error loading documents</div>';
                });
        }
    </script>

    <!-- Documents Modal -->
    <div class="modal fade" id="documentsModal" tabindex="-1" aria-labelledby="documentsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentsModalLabel">Company Documents</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="documentsModalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <?php } catch (\Exception $e) { ?> <?php echo e($e); ?> <?php } ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backEnd.newmasterpage', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
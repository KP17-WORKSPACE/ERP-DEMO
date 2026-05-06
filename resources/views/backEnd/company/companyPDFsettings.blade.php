@extends('backEnd.newmasterpage')
@section('mainContent')
    <?php try { ?>



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

                sessionStorage.setItem('listViewLeadList', 'long');
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

                sessionStorage.setItem('listViewLeadList', 'short');

            }


        }


        //added ny kp
        function toggleLongFilters() {

            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }




        // Initialize view from sessionStorage (tab-specific)
        document.addEventListener('DOMContentLoaded', () => {
            // Check if we have customer_action parameter (add/edit mode)
            const urlParams = new URLSearchParams(window.location.search);
            const hasCustomerAction = urlParams.has('lead_action');

            // If in add/edit mode, force short view
            if (hasCustomerAction) {
                sessionStorage.setItem('listViewLeadList', 'short');
                isFullList = true; // Set to true so toggle switches to short
                list_style_new(); // Switch to short view
            } else {
                // Normal behavior - use saved view from sessionStorage
                const savedView = sessionStorage.getItem('listViewLeadList');
                if (savedView === 'long') {
                    isFullList = false; // so that toggling once activates full view
                    list_style_new();
                } else {
                    // Default to short view
                    isFullList = true; // so that toggling once activates short view
                    list_style_new();
                }
            }

            // Attach event to sidebar links to force short view on navigation
            document.querySelectorAll('.sub-nav-item').forEach(link => {
                link.addEventListener('click', () => {
                    sessionStorage.setItem('listViewLeadList', 'short');
                });
            });



        });


        function toggleStats() {

            document.querySelectorAll('#task-cards').forEach(el => {
                el.classList.toggle('d-none');
            });
        }

        // JS helper for downloading an uploaded PDF image
        const downloadUrlBase = "{{ url('company/download-pdf-image') }}";
        function downloadPdfImage(id) {
            const url = downloadUrlBase + '/' + id;
            // open in new tab so the user stays on the settings page
            window.open(url);
        }

    </script>


    <style>
        /* Smooth collapse transition */
        .collapse {
            transition: height 0.35s ease, opacity 0.35s ease;
        }

        .collapsing {
            opacity: 0.8;
            transition: height 0.35s ease, opacity 0.35s ease;
        }


        .pagination .page-item.active .page-link {
            background-color: #198754 !important;
            /* Bootstrap success green */

            color: #fff !important;
        }


        .col-5-custom {
            flex: 0 0 auto;
            width: 20%;

        }
    </style>
    <?php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
                ?>

    @php
        function human_filesize($bytes)
        {
            if (!$bytes)
                return '0 B';
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $i = max(0, floor(log($bytes, 1024)));
            return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
        }
    @endphp
    <aside class="left-nav col-3" id="leftSidebar">
        <div class="resizer" id="sidebarResizer"></div>
        <div class="short-list" id="filters-short">
            <h4 class="mb-2" style=" margin-left: -6px;">Company
            </h4>
        </div>

        <div class="left-nav-list">

            <ul id="short-list" class="nav flex-column nav-pills mt-4" id="companyTabNavs" role="tablist">
                @if ($companies->count() > 0)

                    @foreach ($companies as $c)
                        <li class="nav-item w-100" role="presentation">
                            <button onclick="location.href='{{ route('company.getpdfsettings', ['id' => $c->id]) }}'"
                                class="nav-link co-item {{ isset($selectedCompany) && $selectedCompany && $selectedCompany->id == $c->id ? 'active' : '' }}"
                                data-id="{{ $c->id }}" type="button" role="tab">

                                <div class="row w-100 align-items-start">
                                    <div class="col-12">

                                        {{-- Company Name + Company ID --}}
                                        <div class="row">
                                            <div class="col-11">
                                                <span class="form-control-plaintext fw-semibold truncate-text"
                                                    title="{{ $c->company_name }}">
                                                    {{ $c->company_name ?? '—' }}
                                                </span>


                                            </div>

                                            <div class="col-1 text-end">
                                                <span class="form-control-plaintext text-muted">
                                                    #{{ $c->document_number }}
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                </div>


                            </button>
                        </li>
                    @endforeach
                @else
                    <li class="w-100 text-center">
                        <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3"
                                style="width:60px; height:60px; font-size:24px;">
                                <i class="ico icon-outline-info-square"></i>
                            </div>
                            <p class="mb-1 fw-semibold">No Records Found</p>
                            <small class="text-secondary">Try adjusting your filters or add a new lead</small>
                        </div>
                    </li>
                @endif
            </ul>



        </div>
    </aside>

    <style>
        /* Use theme variables and compact spacing */
        .component-card {
            background: var(--color-white);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 16px;
            box-shadow: 0 6px 18px rgba(32, 41, 46, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none;
            overflow: hidden;
            position: relative;
        }

        .component-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(32, 41, 46, 0.08);
        }

        .card-header-custom {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--color-border-1);
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--color-white);
        }



        .card-title-custom {
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--color-text-4);
            margin: 0;
        }

        .image-container {
            background: var(--bg-content);
            border-radius: 8px;
            padding: 10px;
            max-height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed var(--color-border-1);
            position: relative;
            overflow: hidden;
        }

        .image-preview {
            max-width: 100%;
            max-height: 220px;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.06);
        }

        .placeholder-text {
            color: var(--color-gray-4);
            font-size: 0.95rem;
            text-align: center;
            margin: 0;
        }

        .placeholder-icon {
            font-size: 2.2rem;
            color: var(--color-border-1);
            margin-bottom: 8px;
        }

        .upload-btn {
            margin-top: 10px;
            border-radius: 20px;
            padding: 8px 18px;

            border: none;

            transition: transform 0.15s ease, box-shadow 0.15s ease;
            font-size: 13px;

        }




        .file-input {
            display: none;
        }

        .info-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 8px;
        }

        .badge-primary {
            background: linear-gradient(135deg, var(--color-info), var(--color-primary));
            color: #fff;
        }

        .badge-secondary {
            background: linear-gradient(135deg, var(--color-secondary), #f093fb);
            color: #fff;
        }

        .badge-success {
            background: linear-gradient(135deg, var(--color-success), var(--color-info));
            color: #fff;
        }

        .badge-warning {
            background: linear-gradient(135deg, var(--color-warning), #fee140);
            color: #000;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-action {
            flex: 1;
            border-radius: 14px;
            padding: 6px 14px;
            font-weight: 600;
            border: 1px solid var(--color-border-1);
            transition: transform 0.12s ease, box-shadow 0.12s ease;
            background: transparent;
            font-size: 13px;
        }

        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 12px rgba(32, 41, 46, 0.05);
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 12px;
            }

            .page-title {
                font-size: 1.4rem;
            }

            .card-title-custom {
                font-size: 1rem;
            }

            .image-container {
                min-height: 100px;
            }
        }

        .watermark-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-40deg);
            opacity: 0.08;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--color-text-4);
            pointer-events: none;
        }
    </style>

    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">




            <div class="" role="tabpanel" aria-labelledby="po-tab" id="leads-details">
                @if (isset($selectedCompany) && $selectedCompany)

                    <div class="d-flex align-items-center justify-content-between">


                        <h4 class="page-title mt-2 mb-2">
                            {{ $selectedCompany->trade_name ?? $selectedCompany->company_name ?? 'Company Name' }}
                        </h4>

                        <div class="dropdown mb-3">
                            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ico icon-outline-hamburger-menu"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('company/policy') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Company Policy
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('/department') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Department
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('/designation') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Designation
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('/legal-entity') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Business Entity
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('/industry') }}">
                                        <i class="ico icon-outline-layers text-success title-15 me-2"></i>
                                        Industry Type
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('/business-activity') }}">
                                        <i class="ico icon-outline-layers text-success title-15 me-2"></i>
                                        Business Sector
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ route('role') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Role
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('module') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Module
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ route('base_setup') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Base Setup
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ route('daily-quotes.index') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Daily Quote
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('currency-settings') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Manage Currency
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('payment-terms') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Payment Terms
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('payment-cheque-print-template') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Cheque Print Templates
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('shipping-add') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Shipping
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('vat-settings') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        VAT Settings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('accountgroup-add') }}">
                                        <i class="ico icon-outline-document text-success title-15 me-2"></i>
                                        Main Heads
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('get-company-pdf-settings') }}">
                                        <i class="ico icon-outline-settings text-success title-15 me-2"></i>
                                        PDF Settings
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('book-close') }}">
                                        <i class="ico icon-outline-settings text-success title-15 me-2"></i>
                                        Book Closed
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                                        href="{{ url('book-close-doc-number') }}">
                                        <i class="ico icon-outline-settings text-success title-15 me-2"></i>
                                        Book Close Doc No
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>



                    <div class="row">
                        <!-- PDF Header -->
                        <div class="col-lg-6 col-md-12">
                            <div class="component-card">
                                <div class="card-header-custom d-flex align-items-center justify-content-between">

                                    <!-- LEFT SIDE: Icon + Title -->
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon header-icon" style="background-color:#deebe1">
                                            <i class="ico icon-outline-document-text text-dark" style="font-size:18px"></i>
                                        </div>
                                        <h3 class="card-title-custom ms-3 mb-0">PDF Header</h3>
                                    </div>

                                    <!-- RIGHT SIDE: File input + Button -->
                                    <form action="{{ route('company.uploadPdfImage') }}" method="POST"
                                        enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                                        @csrf

                                        <input type="hidden" name="company_id" value="{{ $selectedCompany->id }}">
                                        <input type="hidden" name="type" value="header">

                                        <input type="file" class="form-control" id="headerInput" accept="image/*"
                                            name="pdf_header[]" multiple style="width:250px">

                                        <button class="btn btn-light" type="submit">
                                            Upload
                                        </button>
                                    </form>

                                </div>

                                <div class="table-responsive mb-4 mt-4">
                                    <table class="table table-hover" style="table-layout: fixed; width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 70%;" class="">Image</th>
                                                <th class="text-center" style="width: 110px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($pdfSettings as $header)
                                                @if ($header->type === 'header')
                                                    <tr class="{{ $header->is_active ? '' : 'table-secondary text-muted' }}">
                                                        <td class="">
                                                            @if(file_exists(public_path($header->attachment)))
                                                                <img src="{{ asset('public/' . $header->attachment) }}" alt="PDF Header"
                                                                    style="max-width: 60%; height: auto;">
                                                            @else
                                                                <span class="text-muted">File not found</span>
                                                            @endif
                                                        </td>

                                                        <td class="text-center d-flex gap-2 justify-content-center align-items-center">

                                                            <button type="button" class="btn btn-sm btn-light"
                                                                onclick="downloadPdfImage({{ $header->id }})" title="Download">
                                                                <i class="ico icon-outline-download-minimalistic me-1"
                                                                    style="font-size: 16px;"></i>
                                                            </button>
                                                            <form action="{{ route('company.deletePdfImage') }}" method="POST"
                                                                onsubmit="return confirm('Are you sure you want to delete this image?');"
                                                                style="display:inline-block;">
                                                                @csrf
                                                                <input type="hidden" name="existing_image_id" value="{{ $header->id }}">
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-light d-flex align-items-center justify-content-center">
                                                                    <i class="ico icon-outline-trash-bin-trash me-1"
                                                                        style="font-size: 16px;"></i>
                                                                </button>
                                                            </form>

                                                            @if($header->is_primary && $header->is_active)
                                                                <span class="badge bg-success">P</span>
                                                            @endif
                                                            <!-- @if(!$header->is_active)
                                                                <span class="badge bg-secondary"><i class="ico icon-outline-close" style="font-size: 14px;"></i></span>
                                                            @endif -->
                                                            @if(!$header->is_primary)
                                                                <form action="{{ route('company.setPrimaryPdfImage') }}" method="POST"
                                                                    style="display:inline-block;">
                                                                    @csrf
                                                                    <input type="hidden" name="image_id" value="{{ $header->id }}">
                                                                    <button type="submit" class="btn btn-sm btn-light"
                                                                        title="{{ $header->is_active ? 'Set primary' : 'Restore' }}">
                                                                        @if($header->is_active)
                                                                            Set
                                                                        @else
                                                                            <i class="ico icon-outline-refresh text-success" style="font-size: 16px;"></i>
                                                                        @endif
                                                                    </button>
                                                                </form>
                                                            @endif


                                                        </td>
                                                    </tr>
                                                @endif
                                            @empty

                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>



                            </div>
                        </div>

                        <!-- PDF Footer -->
                        <div class="col-lg-6 col-md-12">
                            <div class="component-card">
                                <div class="card-header-custom d-flex align-items-center justify-content-between">
                                    <!-- LEFT SIDE: Icon + Title -->
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon header-icon" style="background-color:#deebe1">
                                            <i class="ico icon-outline-document-text text-dark" style="font-size:18px"></i>
                                        </div>
                                        <h3 class="card-title-custom ms-3 mb-0">PDF Footer</h3>
                                    </div>

                                    <!-- RIGHT SIDE: File input + Button -->
                                    <form action="{{ route('company.uploadPdfImage') }}" method="POST"
                                        enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                                        @csrf

                                        <input type="hidden" name="company_id" value="{{ $selectedCompany->id }}">
                                        <input type="hidden" name="type" value="footer">

                                        <input type="file" class="form-control" id="headerInput" accept="image/*"
                                            name="pdf_header[]" multiple style="width:250px">

                                        <button class="btn btn-light" type="submit">
                                            Upload
                                        </button>
                                    </form>
                                </div>
                                <div class="table-responsive mb-4 mt-4">
                                    <table class="table table-hover" style="table-layout: fixed; width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 70%;" class="">Image</th>
                                                <th class="text-center" style="width: 110px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($pdfSettings as $header)
                                                @if ($header->type === 'footer')
                                                    <tr class="{{ $header->is_active ? '' : 'table-secondary text-muted' }}">
                                                        <td class="">
                                                            @if(file_exists(public_path($header->attachment)))
                                                                <img src="{{ asset('public/' . $header->attachment) }}" alt="PDF Header"
                                                                    style="max-width: 60%; height: auto;">
                                                            @else
                                                                <span class="text-muted">File not found</span>
                                                            @endif
                                                        </td>

                                                        <td class="text-center d-flex gap-2 justify-content-center align-items-center">

                                                            <button type="button" class="btn btn-sm btn-light"
                                                                onclick="downloadPdfImage({{ $header->id }})" title="Download">
                                                                <i class="ico icon-outline-download-minimalistic me-1"
                                                                    style="font-size: 16px;"></i>
                                                            </button>
                                                            <form action="{{ route('company.deletePdfImage') }}" method="POST"
                                                                onsubmit="return confirm('Are you sure you want to delete this image?');"
                                                                style="display:inline-block;">
                                                                @csrf
                                                                <input type="hidden" name="existing_image_id" value="{{ $header->id }}">
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-light d-flex align-items-center justify-content-center">
                                                                    <i class="ico icon-outline-trash-bin-trash me-1"
                                                                        style="font-size: 16px;"></i>
                                                                </button>
                                                            </form>

                                                            @if($header->is_primary && $header->is_active)
                                                                <span class="badge bg-success">P</span>
                                                            @endif
                                                            <!-- @if(!$header->is_active)
                                                                <span class="badge bg-secondary"><i class="ico icon-outline-close" style="font-size: 14px;"></i></span>
                                                            @endif -->
                                                            @if(!$header->is_primary)
                                                                <form action="{{ route('company.setPrimaryPdfImage') }}" method="POST"
                                                                    style="display:inline-block;">
                                                                    @csrf
                                                                    <input type="hidden" name="image_id" value="{{ $header->id }}">
                                                                    <button type="submit" class="btn btn-sm btn-light"
                                                                        title="{{ $header->is_active ? 'Set primary' : 'Restore' }}">
                                                                        @if($header->is_active)
                                                                            Set
                                                                        @else
                                                                            <i class="ico icon-outline-refresh text-success" style="font-size: 16px;"></i>
                                                                        @endif
                                                                    </button>
                                                                </form>
                                                            @endif


                                                        </td>
                                                    </tr>
                                                @endif
                                            @empty

                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                        <!-- PDF Watermark -->
                        <div class="col-lg-6 col-md-12">
                            <div class="component-card">
                                <div class="card-header-custom d-flex align-items-center justify-content-between">

                                    <!-- LEFT SIDE: Icon + Title -->
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon header-icon" style="background-color:#deebe1">
                                            <i class="ico icon-outline-document-text text-dark" style="font-size:18px"></i>
                                        </div>
                                        <h3 class="card-title-custom ms-3 mb-0">PDF Watermark</h3>
                                    </div>

                                    <!-- RIGHT SIDE: File input + Button -->
                                    <form action="{{ route('company.uploadPdfImage') }}" method="POST"
                                        enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                                        @csrf

                                        <input type="hidden" name="company_id" value="{{ $selectedCompany->id }}">
                                        <input type="hidden" name="type" value="watermark">

                                        <input type="file" class="form-control" id="headerInput" accept="image/*"
                                            name="pdf_header[]" multiple style="width:250px">

                                        <button class="btn btn-light" type="submit">
                                            Upload
                                        </button>
                                    </form>

                                </div>

                                <div class="table-responsive mb-4 mt-4">
                                    <table class="table table-hover" style="table-layout: fixed; width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 70%;" class="">Image</th>
                                                <th class="text-center" style="width: 110px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($pdfSettings as $header)
                                                @if ($header->type === 'watermark')
                                                    <tr class="{{ $header->is_active ? '' : 'table-secondary text-muted' }}">
                                                        <td class="">
                                                            @if(file_exists(public_path($header->attachment)))
                                                                <img src="{{ asset('public/' . $header->attachment) }}" alt="PDF Header"
                                                                    style="max-width: 60%; height: auto;">
                                                            @else
                                                                <span class="text-muted">File not found</span>
                                                            @endif
                                                        </td>

                                                        <td class="text-center d-flex gap-2 justify-content-center align-items-center">

                                                            <button type="button" class="btn btn-sm btn-light"
                                                                onclick="downloadPdfImage({{ $header->id }})" title="Download">
                                                                <i class="ico icon-outline-download-minimalistic me-1"
                                                                    style="font-size: 16px;"></i>
                                                            </button>
                                                            <form action="{{ route('company.deletePdfImage') }}" method="POST"
                                                                onsubmit="return confirm('Are you sure you want to delete this image?');"
                                                                style="display:inline-block;">
                                                                @csrf
                                                                <input type="hidden" name="existing_image_id" value="{{ $header->id }}">
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-light d-flex align-items-center justify-content-center">
                                                                    <i class="ico icon-outline-trash-bin-trash me-1"
                                                                        style="font-size: 16px;"></i>
                                                                </button>
                                                            </form>

                                                            @if($header->is_primary && $header->is_active)
                                                                <span class="badge bg-success">P</span>
                                                            @endif
                                                            <!-- @if(!$header->is_active)
                                                                <span class="badge bg-secondary"><i class="ico icon-outline-close" style="font-size: 14px;"></i></span>
                                                            @endif -->
                                                            @if(!$header->is_primary)
                                                                <form action="{{ route('company.setPrimaryPdfImage') }}" method="POST"
                                                                    style="display:inline-block;">
                                                                    @csrf
                                                                    <input type="hidden" name="image_id" value="{{ $header->id }}">
                                                                    <button type="submit" class="btn btn-sm btn-light"
                                                                        title="{{ $header->is_active ? 'Set primary' : 'Restore' }}">
                                                                        @if($header->is_active)
                                                                            Set
                                                                        @else
                                                                            <i class="ico icon-outline-refresh text-success" style="font-size: 16px;"></i>
                                                                        @endif
                                                                    </button>
                                                                </form>
                                                            @endif


                                                        </td>
                                                    </tr>
                                                @endif
                                            @empty

                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>



                            </div>
                        </div>

                        <!-- PDF Header -->
                        <div class="col-lg-6 col-md-12">
                            <div class="component-card">
                                <div class="card-header-custom d-flex align-items-center justify-content-between">

                                    <!-- LEFT SIDE: Icon + Title -->
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon header-icon" style="background-color:#deebe1">
                                            <i class="ico icon-outline-document-text text-dark" style="font-size:18px"></i>
                                        </div>
                                        <h3 class="card-title-custom ms-3 mb-0">PDF First Image</h3>
                                    </div>

                                    <!-- RIGHT SIDE: File input + Button -->
                                    <form action="{{ route('company.uploadPdfImage') }}" method="POST"
                                        enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                                        @csrf

                                        <input type="hidden" name="company_id" value="{{ $selectedCompany->id }}">
                                        <input type="hidden" name="type" value="firstpage">

                                        <input type="file" class="form-control" id="headerInput" accept="image/*"
                                            name="pdf_header[]" multiple style="width:250px">

                                        <button class="btn btn-light" type="submit">
                                            Upload
                                        </button>
                                    </form>

                                </div>

                                <div class="table-responsive mb-4 mt-4">
                                    <table class="table table-hover" style="table-layout: fixed; width:100%">
                                        <thead>
                                            <tr>
                                                <th style="width: 70%;" class="">Image</th>
                                                <th class="text-center" style="width: 110px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($pdfSettings as $header)
                                                @if ($header->type === 'firstpage')
                                                    <tr class="{{ $header->is_active ? '' : 'table-secondary text-muted' }}">
                                                        <td class="">
                                                            @if(file_exists(public_path($header->attachment)))
                                                                <img src="{{ asset('public/' . $header->attachment) }}" alt="PDF Header"
                                                                    style="max-width: 60%; height: auto;">
                                                            @else
                                                                <span class="text-muted">File not found</span>
                                                            @endif
                                                        </td>

                                                        <td class="text-center d-flex gap-2 justify-content-center align-items-center">

                                                            <button type="button" class="btn btn-sm btn-light"
                                                                onclick="downloadPdfImage({{ $header->id }})" title="Download">
                                                                <i class="ico icon-outline-download-minimalistic me-1"
                                                                    style="font-size: 16px;"></i>
                                                            </button>
                                                            <form action="{{ route('company.deletePdfImage') }}" method="POST"
                                                                onsubmit="return confirm('Are you sure you want to delete this image?');"
                                                                style="display:inline-block;">
                                                                @csrf
                                                                <input type="hidden" name="existing_image_id" value="{{ $header->id }}">
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-light d-flex align-items-center justify-content-center">
                                                                    <i class="ico icon-outline-trash-bin-trash me-1"
                                                                        style="font-size: 16px;"></i>
                                                                </button>
                                                            </form>

                                                            @if($header->is_primary && $header->is_active)
                                                                <span class="badge bg-success">P</span>
                                                            @endif
                                                            <!-- @if(!$header->is_active)
                                                                <span class="badge bg-secondary"><i class="ico icon-outline-close" style="font-size: 14px;"></i></span>
                                                            @endif -->
                                                            @if(!$header->is_primary)
                                                                <form action="{{ route('company.setPrimaryPdfImage') }}" method="POST"
                                                                    style="display:inline-block;">
                                                                    @csrf
                                                                    <input type="hidden" name="image_id" value="{{ $header->id }}">
                                                                    <button type="submit" class="btn btn-sm btn-light"
                                                                        title="{{ $header->is_active ? 'Set primary' : 'Restore' }}">
                                                                        @if($header->is_active)
                                                                            Set
                                                                        @else
                                                                            <i class="ico icon-outline-refresh text-success" style="font-size: 16px;"></i>
                                                                        @endif
                                                                    </button>
                                                                </form>
                                                            @endif


                                                        </td>
                                                    </tr>
                                                @endif
                                            @empty

                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>



                            </div>
                        </div>
                    </div>




                @endif
            </div>


        </div>
    </div>
    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection
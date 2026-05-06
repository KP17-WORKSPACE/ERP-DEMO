<div class="purchase-order-content-header sticky-top" style="background-color: #f7f8fd">
    <h4 class="purchase-order-content-header-left">
        COM - {{ $company->document_number }} ({{ $company->other_code }})
    </h4>
    <style>
        .nav.nav-tabs {

            gap: 26px !important;
        }
    </style>
    <div class="purchase-order-content-header-right">

        <style>
            #co-details label {
                font-weight: 600 !important;
                background-color: #deebe1 !important;
                margin-bottom: 3px !important;
                text-align: center !important;
                color: #212529 !important;
            }

            #co-details .form-control-plaintext {
                text-align: center !important;
            }
        </style>

        <a href="{{ url('company-edit/' . $company->id) }}" class="btn btn-light text-dark">
            <i class="ico icon-outline-pen-2 text-success"></i> Edit
        </a>
        <a href="{{ url('company-add') }}" class="btn btn-light text-dark">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </a>







        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
           <ul class="dropdown-menu" style="">


  <li>
                   
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('company/policy') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Policy
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('/department') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Department
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/designation') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Designation
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/legal-entity') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Business Entity
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/industry') }}">
                        <i class="ico icon-outline-layers text-success  title-15 me-2"></i>
                        Industry Type
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/business-activity') }}">
                        <i class="ico icon-outline-layers text-success  title-15 me-2"></i>
                        Business Sector
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('role') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Role
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('module') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Module
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('base_setup') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Base Setup
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('daily-quotes.index') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Daily Quote
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('currency-settings') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Manage Currency
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('payment-terms') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Payment Terms
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('payment-cheque-print-template') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Cheque Print Templates
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('shipping-add') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Shipping
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('vat-settings') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        VAT Settings
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('accountgroup-add') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Main Heads
                    </a>
                </li>

                 <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('get-company-pdf-settings') }}">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        PDF Settings
                </a>

                <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('book-close') }}">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        Book Closed
                </a>


                <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('book-close-doc-number') }}">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        Book Close Doc No'
                </a>



            </ul>
        </div>



    </div>
</div>



<div class="card mb-3">
    <div class="card-body" style="padding: 0.5rem 0.5rem">

        <div class="row">
            <div class="col-2 d-flex align-items-center justify-content-center">
                <div class="staff-photo d-flex align-items-center justify-content-center rounded-circle overflow-hidden bg-light"
                    style="width:120px;max-width:100%;height:120px;">
                    @if (!empty($company->company_logo))
                        @php
                            $photoUrl = asset('public/' . $company->company_logo);
                        @endphp
                        <img src="{{ $photoUrl }}" alt="Staff Photo"
                            style="width:100%;height:100%;object-fit:contain;display:block;">
                    @else
                        <span class="text-muted">No Photo</span>
                    @endif
                </div>
            </div>

            <div class="col-10">
                <div class="row row-cols-5">
                    <div class="col">
                        <label class="form-label">Company Name</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ $company->company_name ?? '' }}
                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">Trade Name</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ $company->trade_name ?? '' }}
                        </div>
                    </div>





                    <div class="col">
                        <label class="form-label">Business Entity</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ optional($company->businessEntity)->name }}
                        </div>
                    </div>



                    <div class="col">
                        <label class="form-label">Industry</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ optional($company->businessIndustry)->name }}
                        </div>
                    </div>



                    <div class="col mb-3">
                        <label class="form-label">Business Sector</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ optional($company->businessSector)->name }}

                        </div>
                    </div>

                    <div class="col mb-3">
                        <label class="form-label">Date of Incorporation</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ $company->date_of_incorporation ? @App\SysHelper::normalizeToDmy($company->date_of_incorporation) : '' }}
                        </div>
                    </div>



                    <div class="col">
                        <label class="form-label">Company Type</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ !empty($company->company_type)
                                ? ($company->company_type === 'subsidiary'
                                    ? 'Group'
                                    : ucwords(str_replace('_', ' ', $company->company_type)))
                                : '' }}

                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">Parent Company Name</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            @if ($company->company_type == 'parent')
                                {{ $company->parent_company ?? '' }}
                            @else
                                @php
                                    $parent_company = @App\SysCompany::where('company_type', 'parent')
                                        ->where('id', $company->parent_company_id)
                                        ->first();
                                @endphp

                                {{ $parent_company->company_name ?? '' }}
                            @endif
                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">Digital Stamp</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            @if ($company->digital_stamp)
                                <a href="{{ asset('public/' . $company->digital_stamp) }}" target="_blank">View</a>
                            @endif
                        </div>
                    </div>

                    <div class="col">
                        <label class="form-label">Company Profile</label>
                        <div class="form-control-plaintext truncate-text-custom">
                            @if ($company->company_profile)
                                <a href="{{ asset('public/' . $company->company_profile) }}" target="_blank">View</a>
                            @endif
                        </div>
                    </div>





                </div>
            </div>
        </div>






    </div>
</div>






<div class="tab-wrap mb-3" id="internal-note">

    <style>
        /* Documents column sizing: keep Key and File columns consistent across family and other document tables */
        .documents-table .key-col {
            width: 20%;
        }

        .documents-table .file-col {
            width: 15%;
            text-align: center;
        }

        .documents-table .name-col {
            width: auto;
        }

        .documents-table th,
        .documents-table td {
            vertical-align: middle;
        }
    </style>
    <ul class="nav nav-tabs" id="employeeInfoTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-family-tab" data-bs-toggle="tab" data-bs-target="#tab-contact"
                type="button" role="tab" aria-controls="tab-contact" aria-selected="true">Contact
                Information</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-job-tab" data-bs-toggle="tab" data-bs-target="#tab-company"
                type="button" role="tab" aria-controls="tab-company" aria-selected="true">Company
                Settings</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-job-tab" data-bs-toggle="tab"
                data-bs-target="#tab-company-registration" type="button" role="tab"
                aria-controls="tab-company-registration" aria-selected="true">Company Registration</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-job-tab" data-bs-toggle="tab" data-bs-target="#tab-banking-finance"
                type="button" role="tab" aria-controls="tab-banking-finance" aria-selected="true">Banking &
                Finance</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-job-tab" data-bs-toggle="tab" data-bs-target="#tab-warehouse-info"
                type="button" role="tab" aria-controls="tab-warehouse-info" aria-selected="true">Warehouse
                Info</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-job-tab" data-bs-toggle="tab" data-bs-target="#tab-company-policies"
                type="button" role="tab" aria-controls="tab-company-policies" aria-selected="true">Company
                Policies</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-job-tab" data-bs-toggle="tab" data-bs-target="#tab-hrms-settings"
                type="button" role="tab" aria-controls="tab-hrms-settings" aria-selected="true">HRMS
                Settings</button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-job-tab" data-bs-toggle="tab" data-bs-target="#tab-documents"
                type="button" role="tab" aria-controls="tab-documents" aria-selected="true">Documents</button>
        </li>

    </ul>
    <div class="tab-content mb-3" id="dealTrackTabsContent">

        <div class="tab-pane fade show active" id="tab-contact" role="tabpanel" aria-labelledby="tab-contact-tab">



            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Address Information</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col mb-3">
                            <label class="form-label">Country</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->countryRelation)->name }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label"> State</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->stateRelation)->name }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label"> City</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $company->city ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label"> Area</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $company->area ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Building/flat/Office</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $company->building_no . ', ' ?? '' }} {{ $company->floor_shop_no ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Company Information</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col mb-3">
                            <label class="form-label">Email</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $company->email ?? '' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label"> Website</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $company->website ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label"> Office Phone</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $company->telephone ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label"> Mobile Number</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $company->mobile ?? '' }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            @php
                $owners = $company->people->where('type', 'owner')->values();

            @endphp

            @forelse ($owners as $owner)
                @php
                    $ownerDocs = $owner->documents->map(function ($d) {
                        return [
                            'id' => $d->id,
                            'document_name' => $d->document_name,
                            'document_no' => $d->document_no,
                            'issue_date' => @App\SysHelper::normalizeToDmy($d->issue_date),
                            'expiry_date' => @App\SysHelper::normalizeToDmy($d->expiry_date),
                            'attachment' => $d->attachment,
                        ];
                    });
                @endphp
                <div class="row mb-3">
                    <div class="col-2" style="margin-top:1rem !important;">
                        <span class="font-weight-600 mb-2">Owner Details <i title="View Documents"
                                style="font-size:12px;cursor:pointer"
                                class="ico icon-bold-paperclip text-success owner-docs-btn"
                                data-owner-name="{{ $owner->first_name . ' ' . $owner->last_name }}"
                                data-docs='@json($ownerDocs)'></i></span>
                    </div>
                    <div class="col-10">
                        <div class="row row-cols-5">

                            <div class="col mb-3">
                                <label class="form-label">Full Name</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $owner->salutation . '.' }} {{ $owner->first_name ?? '' }}
                                    {{ $owner->last_name ?? '' }}
                                </div>
                            </div>


                            <div class="col">
                                <label class="form-label"> Mobile</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $owner->mobile ?? '' }}
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label"> Email</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $owner->email ?? '' }}
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label"> Designation</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ @App\SmDesignation::find($owner->designation)->title ?? '' }}
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label"> Shares</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $owner->share_percentage ?? '' }}
                                </div>
                            </div>



                        </div>
                    </div>
                </div>
            @empty
            @endforelse



            <div class="modal fade side-panel" id="OwnerDocuments" data-bs-backdrop="false" tabindex="-1"
                aria-labelledby="OwnerDocuments" aria-hidden="true">

                <div class="modal-dialog modal-lg" style="50rem">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header">
                            <h4 class="modal-title" style="padding-left:0" id="OwnerName"></h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body p-0">
                            <div class="card m-0">
                                <div class="card-body p-0">

                                    <div class="table-responsive">
                                        <table id="long-list" class="table table-hover data-table"
                                            style="table-layout: fixed;width:100%">

                                            <thead>
                                                <tr>
                                                    <th>Document Name</th>
                                                    <th class="text-center">Document Number</th>
                                                    <th class="text-center">Issue Date</th>
                                                    <th class="text-center">Expiry Date</th>
                                                    <th class="text-center">Attachment</th>

                                                </tr>
                                            </thead>
                                            <tbody id="ownerDocumentsTableBody">
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No documents
                                                        uploaded.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>






                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <script>
                (function() {
                    var publicStorage = '{{ asset('public') }}';

                    function safeParseDocs(str) {
                        if (!str) return [];
                        try {
                            return JSON.parse(str);
                        } catch (e) {
                            // if data was already parsed by jQuery into an object
                            return (typeof str === 'object' && str) ? str : [];
                        }
                    }

                    $(document).on('click', '.owner-docs-btn', function(e) {
                        e.preventDefault();
                        var $btn = $(this);
                        var docsAttr = $btn.attr('data-docs') || '';
                        var docs = safeParseDocs(docsAttr);
                        // fallback to jQuery data if parsing fails
                        if ((!docs || !docs.length) && $btn.data('docs')) docs = $btn.data('docs');

                        var ownerName = $btn.attr('data-owner-name') || $btn.data('owner-name') || 'Owner';
                        $('#OwnerName').text(ownerName);

                        var $tbody = $('#ownerDocumentsTableBody');
                        $tbody.empty();

                        if (!docs || docs.length === 0) {
                            $tbody.append(
                                '<tr><td colspan="5" class="text-center text-muted">No documents uploaded.</td></tr>'
                            );
                        } else {
                            docs.forEach(function(d) {
                                var name = d.document_name || '';
                                var number = d.document_no || '';
                                var issue = d.issue_date || '';
                                var expiry = d.expiry_date || '';
                                var attachCell = '';
                                var actionCell = '';
                                if (d.attachment) {
                                    var href = publicStorage + '/' + d.attachment;
                                    attachCell = '<a class="btn-sm btn-light text-dark btn-fixed" href="' +
                                        href + '" title="' + $('<div/>').text(d.attachment).html() +
                                        '" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>';
                                    actionCell = '<a class="btn btn-sm btn-light text-dark" href="' + href +
                                        '" target="_blank">View</a>';
                                } else {
                                    attachCell = '<span class="text-muted">No file</span>';
                                    actionCell = '';
                                }

                                var row = '<tr>' +
                                    '<td>' + $('<div/>').text(name).html() + '</td>' +
                                    '<td class="text-center">' + $('<div/>').text(number).html() + '</td>' +
                                    '<td class="text-center">' + $('<div/>').text(issue).html() + '</td>' +
                                    '<td class="text-center">' + $('<div/>').text(expiry).html() + '</td>' +
                                    '<td class="text-center">' + attachCell + '</td>' +
                                    '</tr>';
                                $tbody.append(row);
                            });
                        }

                        if (typeof bootstrap !== 'undefined') {
                            var m = new bootstrap.Modal(document.getElementById('OwnerDocuments'));
                            m.show();
                        } else if (typeof $ !== 'undefined') {
                            $('#OwnerDocuments').modal('show');
                        }
                    });
                })();
            </script>



            @php
                $sponsors = $company->people->where('type', 'sponsor')->values();
            @endphp

            @forelse ($sponsors as $sponsor)
                @php
                    $sponsorDocs = $sponsor->documents->map(function ($d) {
                        return [
                            'id' => $d->id,
                            'document_name' => $d->document_name,
                            'document_no' => $d->document_no,
                            'issue_date' => @App\SysHelper::normalizeToDmy($d->issue_date),
                            'expiry_date' => @App\SysHelper::normalizeToDmy($d->expiry_date),
                            'attachment' => $d->attachment,
                        ];
                    });
                @endphp
                <div class="row mb-3">
                    <div class="col-2" style="margin-top:1rem !important;">
                        <span class="font-weight-600 mb-2">Spnsor Details

                        </span>
                    </div>
                    <div class="col-10">
                        <div class="row row-cols-5">

                            <div class="col mb-3">
                                <label class="form-label">First Name</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $sponsor->salutation . '.' }} {{ $sponsor->first_name ?? '' }}
                                </div>
                            </div>


                            <div class="col mb-3">
                                <label class="form-label">Last Name</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $sponsor->last_name ?? '' }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label"> Mobile</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $sponsor->mobile ?? '' }}
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label"> Email</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $sponsor->email ?? '' }}
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label"> Documents</label>
                                <div class="form-control-plaintext truncate-text-custom">

                                    @if (!empty($sponsorDocs) && count($sponsorDocs) > 0)
                                        <i title="View Documents" style="font-size:12px;cursor:pointer"
                                            class="ico icon-bold-paperclip text-success sponsor-docs-btn"
                                            data-sponsor-name="{{ $sponsor->first_name . ' ' . $sponsor->last_name }}"
                                            data-docs='@json($sponsorDocs)'>
                                        </i>
                                    @endif


                                </div>
                            </div>




                        </div>
                    </div>
                </div>
            @empty
            @endforelse



            <div class="modal fade side-panel" id="SponsorDocuments" data-bs-backdrop="false" tabindex="-1"
                aria-labelledby="SponsorDocuments" aria-hidden="true">

                <div class="modal-dialog modal-lg" style="50rem">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header">
                            <h4 class="modal-title" style="padding-left:0" id="SponsorName"></h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body p-0">
                            <div class="card m-0">
                                <div class="card-body p-0">

                                    <div class="table-responsive">
                                        <table id="long-list" class="table table-hover data-table"
                                            style="table-layout: fixed;width:100%">

                                            <thead>
                                                <tr>
                                                    <th>Document Name</th>
                                                    <th class="text-center">Document Number</th>
                                                    <th class="text-center">Issue Date</th>
                                                    <th class="text-center">Expiry Date</th>
                                                    <th class="text-center">Attachment</th>

                                                </tr>
                                            </thead>
                                            <tbody id="sponsorDocumentsTableBody">
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No documents
                                                        uploaded.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>






                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <script>
                (function() {
                    var publicStorage = '{{ asset('public') }}';

                    function safeParseDocs(str) {
                        if (!str) return [];
                        try {
                            return JSON.parse(str);
                        } catch (e) {
                            // if data was already parsed by jQuery into an object
                            return (typeof str === 'object' && str) ? str : [];
                        }
                    }

                    $(document).on('click', '.sponsor-docs-btn', function(e) {
                        e.preventDefault();
                        var $btn = $(this);
                        var docsAttr = $btn.attr('data-docs') || '';
                        var docs = safeParseDocs(docsAttr);
                        // fallback to jQuery data if parsing fails
                        if ((!docs || !docs.length) && $btn.data('docs')) docs = $btn.data('docs');

                        var sponsorName = $btn.attr('data-sponsor-name') || $btn.data('sponsor-name') || 'Sponsor';
                        $('#SponsorName').text(sponsorName);

                        var $tbody = $('#sponsorDocumentsTableBody');
                        $tbody.empty();

                        if (!docs || docs.length === 0) {
                            $tbody.append(
                                '<tr><td colspan="5" class="text-center text-muted">No documents uploaded.</td></tr>'
                            );
                        } else {
                            docs.forEach(function(d) {
                                var name = d.document_name || '';
                                var number = d.document_no || '';
                                var issue = d.issue_date || '';
                                var expiry = d.expiry_date || '';
                                var attachCell = '';
                                var actionCell = '';
                                if (d.attachment) {
                                    var href = publicStorage + '/' + d.attachment;
                                    attachCell = '<a class="btn-sm btn-light text-dark btn-fixed" href="' +
                                        href + '" title="' + $('<div/>').text(d.attachment).html() +
                                        '" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>';
                                    actionCell = '<a class="btn btn-sm btn-light text-dark" href="' + href +
                                        '" target="_blank">View</a>';
                                } else {
                                    attachCell = '<span class="text-muted">No file</span>';
                                    actionCell = '';
                                }

                                var row = '<tr>' +
                                    '<td>' + $('<div/>').text(name).html() + '</td>' +
                                    '<td class="text-center">' + $('<div/>').text(number).html() + '</td>' +
                                    '<td class="text-center">' + $('<div/>').text(issue).html() + '</td>' +
                                    '<td class="text-center">' + $('<div/>').text(expiry).html() + '</td>' +
                                    '<td class="text-center">' + attachCell + '</td>' +
                                    '</tr>';
                                $tbody.append(row);
                            });
                        }

                        if (typeof bootstrap !== 'undefined') {
                            var m = new bootstrap.Modal(document.getElementById('SponsorDocuments'));
                            m.show();
                        } else if (typeof $ !== 'undefined') {
                            $('#SponsorDocuments').modal('show');
                        }
                    });
                })();
            </script>






            @php
                $contacts = $company->people->where('type', 'contact')->values();
            @endphp

            @forelse ($contacts as $contact)
                @php
                    $contactDocs = $contact->documents->map(function ($d) {
                        return [
                            'id' => $d->id,
                            'document_name' => $d->document_name,
                            'document_no' => $d->document_no,
                            'issue_date' => @App\SysHelper::normalizeToDmy($d->issue_date),
                            'expiry_date' => @App\SysHelper::normalizeToDmy($d->expiry_date),
                            'attachment' => $d->attachment,
                        ];
                    });
                @endphp
                <div class="row mb-3">
                    <div class="col-2" style="margin-top:1rem !important;">
                        <span class="font-weight-600 mb-2">Contact Details

                        </span>
                    </div>
                    <div class="col-10">
                        <div class="row row-cols-5">

                            <div class="col mb-3">
                                <label class="form-label">Full Name</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $contact->salutation . '.' }} {{ $contact->first_name ?? '' }}
                                    {{ $contact->last_name ?? '' }}
                                </div>
                            </div>




                            <div class="col">
                                <label class="form-label"> Mobile</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $contact->mobile ?? '' }}
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label"> Email</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $contact->email ?? '' }}
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label"> Designation</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $contact->designation ?? '' }}
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label"> Documents</label>
                                <div class="form-control-plaintext truncate-text-custom">

                                    @if (!empty($contactDocs) && count($contactDocs) > 0)
                                        <i title="View Documents" style="font-size:12px;cursor:pointer"
                                            class="ico icon-bold-paperclip text-success contact-docs-btn"
                                            data-contact-name="{{ $contact->first_name . ' ' . $contact->last_name }}"
                                            data-docs='@json($contactDocs)'>
                                        </i>
                                    @endif



                                </div>
                            </div>




                        </div>
                    </div>
                </div>
            @empty
            @endforelse



            <div class="modal fade side-panel" id="ContactDocuments" data-bs-backdrop="false" tabindex="-1"
                aria-labelledby="ContactDocuments" aria-hidden="true">

                <div class="modal-dialog modal-lg" style="50rem">
                    <div class="modal-content">

                        <!-- Header -->
                        <div class="modal-header">
                            <h4 class="modal-title" style="padding-left:0" id="ContactName"></h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body p-0">
                            <div class="card m-0">
                                <div class="card-body p-0">

                                    <div class="table-responsive">
                                        <table id="long-list" class="table table-hover data-table"
                                            style="table-layout: fixed;width:100%">

                                            <thead>
                                                <tr>
                                                    <th>Document Name</th>
                                                    <th class="text-center">Document Number</th>
                                                    <th class="text-center">Issue Date</th>
                                                    <th class="text-center">Expiry Date</th>
                                                    <th class="text-center">Attachment</th>

                                                </tr>
                                            </thead>
                                            <tbody id="contactDocumentsTableBody">
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No documents
                                                        uploaded.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>






                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <script>
                (function() {
                    var publicStorage = '{{ asset('public') }}';

                    function safeParseDocs(str) {
                        if (!str) return [];
                        try {
                            return JSON.parse(str);
                        } catch (e) {
                            // if data was already parsed by jQuery into an object
                            return (typeof str === 'object' && str) ? str : [];
                        }
                    }

                    $(document).on('click', '.contact-docs-btn', function(e) {
                        e.preventDefault();
                        var $btn = $(this);
                        var docsAttr = $btn.attr('data-docs') || '';
                        var docs = safeParseDocs(docsAttr);
                        // fallback to jQuery data if parsing fails
                        if ((!docs || !docs.length) && $btn.data('docs')) docs = $btn.data('docs');

                        var contactName = $btn.attr('data-contact-name') || $btn.data('contact-name') || 'Contact';
                        $('#ContactName').text(contactName);

                        var $tbody = $('#contactDocumentsTableBody');
                        $tbody.empty();

                        if (!docs || docs.length === 0) {
                            $tbody.append(
                                '<tr><td colspan="5" class="text-center text-muted">No documents uploaded.</td></tr>'
                            );
                        } else {
                            docs.forEach(function(d) {
                                var name = d.document_name || '';
                                var number = d.document_no || '';
                                var issue = d.issue_date || '';
                                var expiry = d.expiry_date || '';
                                var attachCell = '';
                                var actionCell = '';
                                if (d.attachment) {
                                    var href = publicStorage + '/' + d.attachment;
                                    attachCell = '<a class="btn-sm btn-light text-dark btn-fixed" href="' +
                                        href + '" title="' + $('<div/>').text(d.attachment).html() +
                                        '" target="_blank"><i class="ico icon-bold-download-minimalistic  fw-bold title-15 text-success"></i></a>';
                                    actionCell = '<a class="btn btn-sm btn-light text-dark" href="' + href +
                                        '" target="_blank">View</a>';
                                } else {
                                    attachCell = '<span class="text-muted">No file</span>';
                                    actionCell = '';
                                }

                                var row = '<tr>' +
                                    '<td>' + $('<div/>').text(name).html() + '</td>' +
                                    '<td class="text-center">' + $('<div/>').text(number).html() + '</td>' +
                                    '<td class="text-center">' + $('<div/>').text(issue).html() + '</td>' +
                                    '<td class="text-center">' + $('<div/>').text(expiry).html() + '</td>' +
                                    '<td class="text-center">' + attachCell + '</td>' +
                                    '</tr>';
                                $tbody.append(row);
                            });
                        }

                        if (typeof bootstrap !== 'undefined') {
                            var m = new bootstrap.Modal(document.getElementById('ContactDocuments'));
                            m.show();
                        } else if (typeof $ !== 'undefined') {
                            $('#ContactDocuments').modal('show');
                        }
                    });
                })();
            </script>



            <div class="row mb-3">
                <div class="col-2">
                    <label class="form-label">Linkedin</label>
                    <div class="form-control-plaintext truncate-text-custom">
                        {{ $company->linkedin ?? '' }}
                    </div>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col mb-3">
                            <label class="form-label">Facebook</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $company->facebook ?? '' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label"> Instagram</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $company->instagram ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label"> Twitter (X)</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $company->twitter_x ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label"> Youtube</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $company->youtube ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Other Social</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ $company->other_social ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>





        </div>


        <div class="tab-pane fade" id="tab-company" role="tabpanel" aria-labelledby="tab-company-tab">

            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Account Setting</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">

                        <div class="col mb-2">
                            <label class="form-label">Currency</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->settings)->currency ?? '' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label"> Symbol</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->settings)->currency_symbol ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Currency Digits</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->settings)->currency_digit ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label"> R Code</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->settings)->r_code ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">P Code</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->settings)->p_code ?? '' }}
                            </div>
                        </div>
                        <div class="col mb-2">
                            <label class="form-label">Book Closed</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ @App\SysHelper::normalizeToDmy(optional($company->settings)->book_closed ?? '') }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Sales Code</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->settings)->sales_code ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">All Other Code</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->settings)->other_code ?? '' }}
                            </div>
                        </div>
                        <div class="col">
                            <label class="form-label">Customer Code</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->settings)->is_customer_code ? 'Yes' : 'No' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Supplier Code</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->settings)->is_supplier_code ? 'Yes' : 'No' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Account Code</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->settings)->is_account_code ? 'Yes' : 'No' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Sub Account Code</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->settings)->is_subaccount_code ? 'Yes' : 'No' }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>


        <div class="tab-pane fade " id="tab-company-registration" role="tabpanel"
            aria-labelledby="tab-company-registration-tab">


            @if ($company->country == 231)
                <div class="row mb-3">
                    <div class="col-2" style="margin-top:1rem !important;">
                        <span class="font-weight-600 mb-2">Trade Information</span>
                    </div>
                    <div class="col-10">
                        <div class="row row-cols-5">


                            <div class="col mb-2">
                                <label class="form-label">Trade License Number </label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ optional($company->compliance)->trade_license_no ?? '' }}
                                </div>
                            </div>

                            <div class="col mb-2">
                                <label class="form-label">License Issue Date</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ @App\SysHelper::normalizeToDmy(optional($company->compliance)->license_issue_date ?? '') }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label"> License Expiry Date</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ @App\SysHelper::normalizeToDmy(optional($company->compliance)->license_expiry_date ?? '') }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Issuing Authority</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ optional($company->compliance)->issuing_authority ?? '' }}
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label">Trade License</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    @if (optional($company->compliance)->business_license_upload)
                                        <div class="mt-1"><a
                                                href="{{ asset('public/' . optional($company->compliance)->business_license_upload) }}"
                                                target="_blank">View</a></div>
                                    @endif
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

                @php
                    $vat_ct = optional($company->compliance)->tax_applicable ?? '';
                @endphp

                @if ($vat_ct == 'vat' || $vat_ct == 'both')
                    <div class="row mb-3">
                        <div class="col-2" style="margin-top:1rem !important;">
                            <span class="font-weight-600 mb-2">VAT Information</span>
                        </div>
                        <div class="col-10">
                            <div class="row row-cols-5">


                                <div class="col mb-2">
                                    <label class="form-label">VAT Registration No (TRN) </label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        {{ optional($company->compliance)->vat_registration_number ?? '' }}
                                    </div>
                                </div>

                                <div class="col mb-2">
                                    <label class="form-label">VAT %</label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        {{ optional($company->compliance)->vat_percentage ?? '' }}
                                    </div>
                                </div>

                                <div class="col">
                                    <label class="form-label"> VAT Registration Date</label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->compliance)->vat_date ?? '') }}
                                    </div>
                                </div>

                                <div class="col">
                                    <label class="form-label">VAT Issuing Authority</label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        {{ optional($company->compliance)->vat_issuing_authority ?? '' }}
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label">VAT Certificate</label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        @if (optional($company->compliance)->vat_certificate)
                                            <div class="mt-1"><a
                                                    href="{{ asset('public/' . optional($company->compliance)->vat_certificate) }}"
                                                    target="_blank">View</a></div>
                                        @endif
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                @endif



                @if ($vat_ct == 'ct' || $vat_ct == 'both')
                    <div class="row mb-3">
                        <div class="col-2" style="margin-top:1rem !important;">
                            <span class="font-weight-600 mb-2">CT Information</span>
                        </div>
                        <div class="col-10">
                            <div class="row row-cols-5">


                                <div class="col mb-2">
                                    <label class="form-label">CT Registration No (CTN) </label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        {{ optional($company->compliance)->corporate_tax_number ?? '' }}
                                    </div>
                                </div>

                                <div class="col mb-2">
                                    <label class="form-label">CT %</label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        {{ optional($company->compliance)->corporate_tax_vat ?? '' }}
                                    </div>
                                </div>

                                <div class="col">
                                    <label class="form-label"> CT Registration Date</label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->compliance)->corporate_tax_date ?? '') }}
                                    </div>
                                </div>

                                <div class="col">
                                    <label class="form-label">CT Issuing Authority</label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        {{ optional($company->compliance)->corporate_issuing_authority ?? '' }}
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label">CT Certificate</label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        @if (optional($company->compliance)->corporate_tax_certificate)
                                            <div class="mt-1"><a
                                                    href="{{ asset('public/' . optional($company->compliance)->corporate_tax_certificate) }}"
                                                    target="_blank">View</a></div>
                                        @endif
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                @endif
            @else
                @php
                    $compliance_items = $company->documentItems->where('document_type', 'compliance')->values();
                @endphp

                @forelse ($compliance_items as $item)
                    <div class="row mb-3">
                        <div class="col-2" style="margin-top:1rem !important;">
                            <span class="font-weight-600 mb-2">Trade Information</span>
                        </div>
                        <div class="col-10">
                            <div class="row row-cols-5">


                                <div class="col mb-2">
                                    <label class="form-label">Document Number </label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        {{ $item->document_number ?? '' }}
                                    </div>
                                </div>

                                <div class="col mb-2">
                                    <label class="form-label">Issue Date</label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        {{ @App\SysHelper::normalizeToDmy($item->document_date ?? '') }}
                                    </div>
                                </div>

                                <div class="col">
                                    <label class="form-label"> Expiry Date</label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        {{ @App\SysHelper::normalizeToDmy($item->expiry_date ?? '') }}
                                    </div>
                                </div>

                                <div class="col">
                                    <label class="form-label">Issuing Authority</label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        {{ $item->document_name ?? '' }}
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label">Attachment</label>
                                    <div class="form-control-plaintext truncate-text-custom">
                                        @if ($item->attachment_file)
                                            <div class="mt-1"><a
                                                    href="{{ asset('public/' . $item->attachment_file) }}"
                                                    target="_blank">View</a></div>
                                        @endif
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                @empty
                @endforelse


            @endif



        </div>





        <div class="tab-pane fade " id="tab-banking-finance" role="tabpanel"
            aria-labelledby="tab-banking-finance-tab">

            @forelse ($company->banking as $i => $bank)
                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Bank Name </label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ $bank->bank_name ?? '' }}
                        </div>
                    </div>
                    <div class="col-10">
                        <div class="row row-cols-5">
                            

                            <div class="col mb-2">
                                <label class="form-label">Account Code </label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ strtoupper(optional($bank->AccountsBank)->account_code) ?? '' }}
                                </div>
                            </div>

                            <div class="col mb-2">
                                <label class="form-label">Branch Name </label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $bank->branch_name ?? '' }}
                                </div>
                            </div>

                             <div class="col mb-2">
                                <label class="form-label">Account Name</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $bank->account_name ?? '' }}
                                </div>
                            </div>


                            <div class="col mb-2">
                                <label class="form-label">Account No</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $bank->account_number ?? '' }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">IBAN</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $bank->iban_number ?? '' }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">SWIFT</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $bank->swift_code ?? '' }}
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label">Finance Code</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $bank->finance_code ?? '' }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Currency</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $bank->currencyDetails->code ?? '' }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Letter</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    @if (optional($bank)->bank_letter)
                                        <div class="mt-1"><a
                                                href="{{ asset('public/' . optional($bank)->bank_letter) }}"
                                                target="_blank">View</a></div>
                                    @endif
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            @empty
            @endforelse



        </div>


        <div class="tab-pane fade " id="tab-warehouse-info" role="tabpanel"
            aria-labelledby="tab-warehouse-info-tab">

            @forelse ($company->warehouses as $i => $w)



                <div class="row mb-3">
                    <div class="col-2">
                        <label class="form-label">Warehouse Code </label>
                        <div class="form-control-plaintext truncate-text-custom">
                            {{ $w->document_number ?? '' }}
                        </div>
                    </div>
                    <div class="col-10">
                        <div class="row row-cols-5">


                            <div class="col mb-2">
                                <label class="form-label">Warehouse Name </label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $w->warehouse_name ?? '' }}
                                </div>
                            </div>

                            <div class="col mb-2">
                                <label class="form-label">Country</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $w->warehouse_country ?? '' }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">State</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $w->warehouse_state ?? '' }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">City</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $w->warehouse_city ?? '' }}
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label">Area</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $w->warehouse_area ?? '' }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Building Name</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $w->warehouse_building_name ?? '' }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Flat/Office No</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $w->warehouse_flat_office_no ?? '' }}

                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Full Name</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $w->contact_salutation ?? '' }} {{ $w->contact_first_name ?? '' }}
                                    {{ $w->contact_last_name ?? '' }}

                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Mobile</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $w->contact_mobile ?? '' }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Email</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $w->contact_email ?? '' }}

                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Designation</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ @App\SmDesignation::find($w->contact_designation)->title ?? '' }}

                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Fire/Safety Status</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ ucfirst($w->fire_safety_compliance_status ?? '') }}


                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Fire NOC / Cert No</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $w->fire_noc_certificate_number ?? '' }}

                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Safety Equipment</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $w->safety_equipment_available ?? '' }}

                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Fire NOC Expiry</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{-- {{ $w->fire_noc_expiry_date ?? '' }} --}}
                                    {{ date('d/m/Y', strtotime($w->fire_noc_expiry_date)) ?? '' }}

                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Last Safety Insp</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{-- {{ @App/SysHelper::normalizeToDmy($w->last_safety_inspection_date) ?? '' }} --}}
                                    {{ date('d/m/Y', strtotime($w->last_safety_inspection_date)) ?? '' }}

                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Documents</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    @php
                                        $docs = $w->contact_documents
                                            ? (is_string($w->contact_documents)
                                                ? json_decode($w->contact_documents, true)
                                                : $w->contact_documents)
                                            : [];
                                    @endphp

                                    @if (is_array($docs) && count($docs))
                                        @foreach ($docs as $doc)
                                            @php
                                                $p = trim($doc);
                                                if ($p === '') {
                                                    continue;
                                                }
                                                $href =
                                                    strpos($p, '/') === false
                                                        ? asset('public/uploads/company/warehouse_docs/' . $p)
                                                        : asset('public/' . ltrim($p, '/'));
                                            @endphp
                                            <div class=""><a href="{{ $href }}"
                                                    target="_blank">View</a></div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No documents</span>
                                    @endif
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            @empty

            @endforelse



        </div>



        <div class="tab-pane fade " id="tab-company-policies" role="tabpanel"
            aria-labelledby="tab-company-policies-tab">

            @forelse ($company->hrPolicies as $i => $p)
                <div class="row mb-3">
                    <div class="col-2" style="margin-top:1rem !important;">
                        <span class="font-weight-600 mb-2">Policy Information</span>
                    </div>
                    <div class="col-10">
                        <div class="row row-cols-5">


                            <div class="col mb-2">
                                <label class="form-label">Date </label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ @App\SysHelper::normalizeToDmy($p->policy_date ?? '') }}
                                </div>
                            </div>

                            <div class="col mb-2">
                                <label class="form-label">Policy Name</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ $p->policy_name ?? '' }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">Valid Till</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ @App\SysHelper::normalizeToDmy($p->policy_valid ?? '') }}
                                </div>
                            </div>

                            <div class="col">
                                <label class="form-label">View To Employeees</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ isset($p->view_to_employees) ? ($p->view_to_employees == 1 ? 'Yes' : 'No') : '' }}

                                </div>
                            </div>


                            <div class="col">
                                <label class="form-label">File</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    @if ($p->policy_file != null || $p->policy_file != '')
                                        <a href="{{ asset('public/' . $p->policy_file) }}">View</a>
                                    @else
                                        <span class="text-muted">No documents</span>
                                    @endif
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            @empty
            @endforelse



        </div>



        <div class="tab-pane fade " id="tab-hrms-settings" role="tabpanel" aria-labelledby="tab-hrms-settings-tab">





            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Leave Policy Types</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">


                        <div class="col mb-2">
                            <label class="form-label">Annual Leave (AL)</label>
                            <div class="form-control-plaintext truncate-text-custom">

                                {{ optional($company->hrpayrollsettings)->annual_leave_cl_sl ?? '' }}
                            </div>
                        </div>

                        <div class="col mb-2">
                            <label class="form-label">Sick Leave (SL)</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->sick_leave_sl ?? '' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Casual Leave (CL)</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->casual_leave_cl ?? '' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Comp-Off Allowed</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->comp_off_allowed == 1 ? 'Yes' : 'No' }}


                            </div>
                        </div>


                        <div class="col">
                            <label class="form-label">Carry Forward Unused Leaves</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->carry_forward_unused_leaves == 1 ? 'Yes' : 'No' }}

                            </div>
                        </div>

                        @if (optional($company->hrpayrollsettings)->carry_forward_unused_leaves == 1)
                            <div class="col">
                                <label class="form-label">Max Carry Forward (Days)</label>
                                <div class="form-control-plaintext truncate-text-custom">
                                    {{ optional($company->hrpayrollsettings)->max_carry_forward_days ?? '' }}
                                </div>
                            </div>
                        @endif



                        <div class="col">
                            <label class="form-label">Encashable Leaves</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->encashable_leaves == 1 ? 'Yes' : 'No' }}

                            </div>
                        </div>


                    </div>
                </div>
            </div>



            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Attendance Policy</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">


                        <div class="col mb-2">
                            <label class="form-label">Attendance Policy</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->attendance_policy
                                    ? ucfirst(str_replace('_', ' ', optional($company->hrpayrollsettings)->attendance_policy))
                                    : '' }}

                            </div>
                        </div>

                        <div class="col mb-2">
                            <label class="form-label">Minimum Working Hours</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->minimum_working_hours !== null
                                    ? (int) optional($company->hrpayrollsettings)->minimum_working_hours
                                    : '' }}

                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Grace Period (Minutes)</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->grace_period_minutes ?? '' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Half Day After (Hours)</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->half_day_after_hours !== null
                                    ? (int) optional($company->hrpayrollsettings)->half_day_after_hours
                                    : '' }}



                            </div>
                        </div>


                        <div class="col">
                            <label class="form-label">Mark Absent If Hours Below</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->absent_if_hours_below !== null
                                    ? (int) optional($company->hrpayrollsettings)->absent_if_hours_below
                                    : '' }}


                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Late Mark Count Allowed (per month)</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->late_mark_count_allowed ?? '' }}
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Consecutive Late Mark → Half Day</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->consecutive_late_to_halfday ?? '' }}

                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Auto Mark Absent After (Days)</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->auto_mark_absent_after_days ?? '' }}

                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Working Shifts</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                @php
                                    $workingShifts = \App\WorkingShift::where('company_id', $company->id)->get();
                                    $shiftCount = $workingShifts->count();
                                @endphp

                                @if ($shiftCount > 0)
                                    <a href="javascript:void(0)" class="text-dark fw-semibold" data-bs-toggle="modal"
                                        data-bs-target="#workingShiftModal">
                                        {{ $shiftCount }} Shifts
                                    </a>
                                @else
                                    —
                                @endif
                            </div>

                            <div class="modal side-panel fade" data-bs-backdrop="false" id="workingShiftModal"
                                tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-sm" style="left:75%">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h6 class="modal-title">Working Shifts</h6>
                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body p-2">
                                            @forelse ($workingShifts as $shift)
                                                <div class="border rounded p-2 mb-2">
                                                    <div class="fw-semibold">{{ $shift->shift_name }}</div>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->start_time)->format('h:i A') }}
                                                        –
                                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->end_time)->format('h:i A') }}
                                                    </small>
                                                </div>
                                            @empty
                                                <div class="text-center text-muted">No shifts available</div>
                                            @endforelse
                                        </div>

                                    </div>
                                </div>
                            </div>





                        </div>


                        <div class="col">
                            <label class="form-label">Weekly Off</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                @php

                                    $weeklyOffs = !empty($company->hrpayrollsettings->weekly_off_days)
                                        ? json_decode($company->hrpayrollsettings->weekly_off_days, true)
                                        : [];

                                    $displayWeeklyOffs = [];

                                @endphp

                                {{ count($weeklyOffs) ? implode(', ', $weeklyOffs) : '—' }}


                            </div>
                        </div>


                    </div>
                </div>
            </div>




            <div class="row mb-3">
                <div class="col-2" style="margin-top:1rem !important;">
                    <span class="font-weight-600 mb-2">Payroll Configuration</span>
                </div>
                <div class="col-10">
                    <div class="row row-cols-5">


                        <div class="col mb-2">
                            <label class="form-label">WPS Establishment ID</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->wps_establishment_id ?? '' }}

                            </div>
                        </div>

                        <div class="col mb-2">
                            <label class="form-label">WPS Bank</label>
                            <div class="form-control-plaintext truncate-text-custom">

                                @php
                                    $wpsBanks = !empty($company->hrpayrollsettings->wps_bank)
                                        ? json_decode($company->hrpayrollsettings->wps_bank, true)
                                        : [];

                                    $bankNames = [];

                                    if (!empty($wpsBanks)) {
                                        $bankNames = @App\SysCompanyBanking::whereIn('id', $wpsBanks)
                                            ->pluck('bank_name')
                                            ->toArray();
                                    }

                                @endphp



                                {{ count($bankNames) ? implode(', ', $bankNames) : '' }}

                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">WPS Salary File Code</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->wps_salary_file_code ?? '' }}
                            </div>
                        </div>


                        <div class="col">
                            <label class="form-label">Payroll Cycle</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->payroll_cycle
                                    ? ucfirst(str_replace('_', ' ', optional($company->hrpayrollsettings)->payroll_cycle))
                                    : '' }}




                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Payroll Start Date</label>
                            <div class="form-control-plaintext truncate-text-custom">


                                {{ optional($company->hrpayrollsettings)->payroll_start_day ?? '' }}

                            </div>
                        </div>


                        <div class="col">
                            <label class="form-label">Payroll End Date</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->payroll_end_day ?? '' }}


                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label">Gratuity Calculation Method</label>
                            <div class="form-control-plaintext truncate-text-custom">
                                {{ optional($company->hrpayrollsettings)->gratuity_calculation_method
                                    ? ucwords(str_replace('_', ' ', optional($company->hrpayrollsettings)->gratuity_calculation_method))
                                    : '' }}

                            </div>
                        </div>






                    </div>
                </div>
            </div>




        </div>



        <div class="tab-pane fade " id="tab-documents" role="tabpanel" aria-labelledby="tab-documents-tab">




            @if ($company->country == 231)
                <div class="row mb-3">
                    <div class="table-responsive mb-4 mt-2">

                        <table id="long-list" class="table table-hover data-table"
                            style="table-layout: fixed;width:100%">

                            <thead class="">
                                <tr>
                                    <th class="text-start" style="width:80px">@lang('Document Name')</th>
                                    <th class="text-center" style="width:40px">@lang('Document No')</th>
                                    <th class="text-center" style="width:40px">@lang('Date')</th>
                                    <th class="text-center" style="width:40px">@lang('Expiry Date')</th>
                                    <th class="text-center" style="width:40px">@lang('Attachment')</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Establishment Card</td>
                                    <td class="text-center">
                                        {{ optional($company->documents)->establishment_number ?? '' }}</td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->establishment_start_date ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->establishment_expiry ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        @if (optional($company->documents)->establishment_file)
                                            <a href="{{ asset('public/' . optional($company->documents)->establishment_file) }}"
                                                target="_blank" class="">View</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Immigration Card</td>
                                    <td class="text-center">
                                        {{ optional(optional($company)->documents)->immigration_number }}</td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->immigration_start_date ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->immigration_expiry ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        @if (optional($company->documents)->immigration_file)
                                            <a href="{{ asset('public/' . optional($company->documents)->immigration_file) }}"
                                                target="_blank" class="">View</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Labour Establishment Card</td>
                                    <td class="text-center">
                                        {{ optional($company->documents)->labour_number ?? '' }}</td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->labour_start_date ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->labour_expiry ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        @if (optional($company->documents)->labour_file)
                                            <a href="{{ asset('public/' . optional($company->documents)->labour_file) }}"
                                                target="_blank" class="">View</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Chamber of Commerce</td>
                                    <td class="text-center">
                                        {{ optional($company->documents)->chamber_number ?? '' }}</td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->chamber_start_date ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->chamber_expiry ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        @if (optional($company->documents)->chamber_file)
                                            <a href="{{ asset('public/' . optional($company->documents)->chamber_file) }}"
                                                target="_blank" class="">View</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Medical Insurance</td>
                                    <td class="text-center">
                                        {{ optional($company->documents)->insurance_certificate_number ?? '' }}</td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->insurance_start_date ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->insurance_certificate_expiry ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        @if (optional($company->documents)->insurance_file)
                                            <a href="{{ asset('public/' . optional($company->documents)->insurance_file) }}"
                                                target="_blank" class="">View</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>MOA / AOA</td>
                                    <td class="text-center">
                                        {{ optional($company->documents)->moa_aoa_number ?? '' }}</td>
                                    <td class="text-center">
                                        -
                                    </td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->moa_aoa_expiry ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        @if (optional($company->documents)->moa_aoa_file)
                                            <a href="{{ asset('public/' . optional($company->documents)->moa_aoa_file) }}"
                                                target="_blank" class="">View</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Board Resolution</td>
                                    <td class="text-center">
                                        {{ optional($company->documents)->board_resolution_number ?? '' }}</td>
                                    <td class="text-center">
                                        -
                                    </td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->board_resolution_expiry ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        @if (optional($company->documents)->board_resolution_file)
                                            <a href="{{ asset('public/' . optional($company->documents)->board_resolution_file) }}"
                                                target="_blank" class="">View</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Power of Attorney</td>
                                    <td class="text-center">
                                        {{ optional($company->documents)->poa_number ?? '' }}</td>
                                    <td class="text-center">
                                        -
                                    </td>
                                    <td class="text-center">
                                        {{ @App\SysHelper::normalizeToDmy(optional($company->documents)->poa_expiry ?? '') }}
                                    </td>
                                    <td class="text-center">
                                        @if (optional($company->documents)->poa_file)
                                            <a href="{{ asset('public/' . optional($company->documents)->poa_file) }}"
                                                target="_blank" class="">View</a>
                                        @endif
                                    </td>
                                </tr>
                                @php
                                    $uae_documents = $company->documentItems->where('document_type', 'uae')->values();
                                @endphp
                                @forelse ($uae_documents as $d)
                                    <tr>
                                        @php
                                            $name = is_array($d) ? $d['name'] ?? '' : $d->document_name ?? '';
                                            $number = is_array($d) ? $d['number'] ?? '' : $d->document_number ?? '';
                                            $date = is_array($d)
                                                ? $d['date'] ?? ''
                                                : (isset($d->document_date)
                                                    ? App\SysHelper::normalizeToDmy($d->document_date)
                                                    : '');
                                            $expiry = is_array($d)
                                                ? $d['expiry'] ?? ''
                                                : (isset($d->expiry_date)
                                                    ? App\SysHelper::normalizeToDmy($d->expiry_date)
                                                    : '');
                                            $file = is_array($d) ? $d['file'] ?? '' : $d->attachment_file ?? '';
                                        @endphp
                                        <td>{{ $name }}</td>
                                        <td class="text-center">{{ $number }}</td>
                                        <td class="text-center">{{ $date }}</td>
                                        <td class="text-center">{{ $expiry }}</td>
                                        <td class="text-center">
                                            @if ($file)
                                                <a href="{{ asset('public/' . ltrim($file, '/')) }}" target="_blank"
                                                    class="">View</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>




                </div>
            @else
                <div class="row mb-3">
                    <div class="table-responsive mb-4 mt-2">

                        <table id="long-list" class="table table-hover data-table"
                            style="table-layout: fixed;width:100%">

                            <thead class="">
                                <tr>
                                    <th class="text-start" style="width:80px">@lang('Document Name')</th>
                                    <th class="text-center" style="width:40px">@lang('Document No')</th>
                                    <th class="text-center" style="width:40px">@lang('Date')</th>
                                    <th class="text-center" style="width:40px">@lang('Expiry Date')</th>
                                    <th class="text-center" style="width:40px">@lang('Attachment')</th>

                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    $non_uae_documents = $company->documentItems
                                        ->where('document_type', 'non_uae')
                                        ->values();
                                @endphp
                                @forelse ($non_uae_documents as $d)
                                    <tr>
                                        @php
                                            $name = is_array($d) ? $d['name'] ?? '' : $d->document_name ?? '';
                                            $number = is_array($d) ? $d['number'] ?? '' : $d->document_number ?? '';
                                            $date = is_array($d)
                                                ? $d['date'] ?? ''
                                                : (isset($d->document_date)
                                                    ? App\SysHelper::normalizeToDmy($d->document_date)
                                                    : '');
                                            $expiry = is_array($d)
                                                ? $d['expiry'] ?? ''
                                                : (isset($d->expiry_date)
                                                    ? App\SysHelper::normalizeToDmy($d->expiry_date)
                                                    : '');
                                            $file = is_array($d) ? $d['file'] ?? '' : $d->attachment_file ?? '';
                                        @endphp
                                        <td>{{ $name }}</td>
                                        <td class="text-center">{{ $number }}</td>
                                        <td class="text-center">{{ $date }}</td>
                                        <td class="text-center">{{ $expiry }}</td>
                                        <td class="text-center">
                                            @if ($file)
                                                <a href="{{ asset('public/' . ltrim($file, '/')) }}" target="_blank"
                                                    class="">View</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>




                </div>
            @endif












        </div>










    </div>
</div>

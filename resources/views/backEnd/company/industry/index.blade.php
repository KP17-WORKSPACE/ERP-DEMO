@extends('backEnd.newmasterpage')
@section('mainContent')

@php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<div class="content-container col-12">

    {{-- ============================================
        PAGE HEADER (FOLLOW ERP STYLE)
    ============================================ --}}
    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">
            Industry List
        </h4>

        <div class="purchase-order-content-header-right d-flex align-items-center gap-2">

            {{-- SEARCH --}}
            <input type="text"
                   id="industrySearch"
                   class="form-control"
                   style="width: 220px;"
                   placeholder="Search..."
                   onkeyup="filterIndustry()">

            {{-- ADD BUTTON --}}
            <button class="btn btn-light text-dark"
                    onclick="openAddIndustry()">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>

            {{-- 3 DOT MENU --}}
                 <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">




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
                        href="{{ url('/company') }}">
                        <i class="ico icon-outline-layers text-success  title-15 me-2"></i>
                        Company Settings
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

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('book-close') }}">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        Book Closed
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('book-close-doc-number') }}">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        Book Close Doc No
                    </a>
                </li>


            </ul>
        </div>


        </div>
    </div>


    {{-- ============================================
        TABLE CARD (FOLLOW EXACT INVENTORY STYLE)
    ============================================ --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-hover" id="long-list" style="table-layout: fixed;width:100%">
                    <thead>
                        <tr>
                            <th style="width:10%">Sl.No</th>
                            <th style="width:100%">Industry Name</th>
                            <th style="width:6%" class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($industries as $industry)
                            <tr>
                                <td> {{ $loop->iteration }}</td>
                                <td> {{ $industry->name }}</td>

                                <td class="d-flex gap-2 justify-content-center">

                                    {{-- EDIT --}}
                                    <button class="btn btn-light btn-sm p-1"
                                            onclick="openEditIndustry({{ $industry->id }}, '{{ $industry->name }}')">
                                        <i class="ico icon-outline-pen-2 text-dark"></i>
                                    </button>

                                    {{-- DELETE --}}
                                    <button class="btn btn-light btn-sm p-1 text-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $industry->id }}">
                                        <i class="ico icon-bold-trash-bin-2"></i>
                                    </button>

                                </td>
                            </tr>

                            {{-- DELETE MODAL --}}
                          
                        @endforeach
                    </tbody>

                </table>

            </div>
        </div>
    </div>
</div>


{{-- ============================================
    ADD / EDIT MODAL
============================================ --}}



{{-- ============================================
    JAVASCRIPT FUNCTIONS
============================================ --}}
<script>

function filterIndustry() {
    var input = document.getElementById("industrySearch");
    var filter = input.value.toLowerCase();
    var rows = document.querySelectorAll("#long-list tbody tr");

    rows.forEach(function(row){
        var text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
}

function openAddIndustry() {

    $("#modalTitle").text("Add Industry");
    $("#industryForm").trigger("reset");

    $("#industryForm").attr("action", "{{ url('/industry') }}");
    $("#industryForm").find("input[name=_method]").remove();

    $("#industry_id").val("");
    $("#saveBtn").text("Save");

    new bootstrap.Modal(document.getElementById('industryModal')).show();
}

function openEditIndustry(id, name) {

    $("#modalTitle").text("Edit Industry");
    $("#industryForm")[0].reset();

    $("#industryForm").attr("action", "/industry/" + id);
    $("#industryForm").find("input[name=_method]").remove();
    $("#industryForm").append('<input type="hidden" name="_method" value="PUT">');

    $("#industry_id").val(id);
    $("#industry_name").val(name);

    $("#saveBtn").text("Update");

    new bootstrap.Modal(document.getElementById('industryModal')).show();
}

</script>

@endsection
<div class="modal fade admin-query"
     id="industryModal"
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header p-3">
                <h4 class="modal-title" id="modalTitle">Add Industry</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3">

                <form id="industryForm" method="POST">
                    @csrf

                    <input type="hidden" name="id" id="industry_id">

                    <label class="form-label">Industry Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="industry_name" required>

                    <button type="submit" class="btn btn-success mt-3 w-100" id="saveBtn">Save</button>
                </form>

            </div>

        </div>
    </div>
</div>

  <div class="modal fade" id="deleteModal{{ $industry->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-sm modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Delete Industry</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body text-center">
                                            <i class="ico icon-bold-trash-bin-2 text-danger" style="font-size:40px;"></i>
                                            <h5 class="mt-2">Are you sure?</h5>
                                            <p class="text-muted small">This action cannot be undone.</p>
                                        </div>

                                        <div class="modal-footer">
                                            {{ Form::open(['url' => '/industry/'. $industry->id, 'method' => 'DELETE']) }}
                                                <button class="btn btn-danger w-100">Delete</button>
                                            {{ Form::close() }}
                                        </div>

                                    </div>
                                </div>
                            </div>
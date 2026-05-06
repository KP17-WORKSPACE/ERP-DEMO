@extends('backEnd.newmasterpage')
@section('mainContent')

@php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<div class="content-container col-12">

    {{-- HEADER --}}
    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">Business Activity List</h4>

        <div class="purchase-order-content-header-right d-flex align-items-center gap-2">

            <input type="text" id="activitySearch" class="form-control"
                   style="width: 220px;" placeholder="Search..." onkeyup="filterActivity()">

            <button class="btn btn-light text-dark" onclick="openAddActivity()">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>

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
                        href="{{ url('/industry') }}">
                        <i class="ico icon-outline-layers text-success  title-15 me-2"></i>
                        Industry Type
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


    {{-- TABLE SECTION --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-hover data-table" id="long-list" style="table-layout:fixed;width:100%">
                    <thead>
                        <tr>
                            <th>Sl.No</th>
                            <th style="width: 30%">Business Activity</th>
                            <th style="width: 50%">Industry</th>
                            <th style="width: 5%" class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($activities as $activity)
                            <tr>
                                <td>{{ $loop->iteration }}</td> 
                                <td>{{ $activity->name }}</td>

                                <td>{{ $activity->industry->name }}</td>

                                <td class="d-flex gap-2 justify-content-center">

                                    <button class="btn btn-light btn-sm p-1"
                                        onclick="openEditActivity({{ $activity->id }}, '{{ $activity->name }}', {{ $activity->industry_id }})">
                                        <i class="ico icon-outline-pen-2 text-dark"></i>
                                    </button>

                                    <button class="btn btn-light btn-sm p-1 text-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $activity->id }}">
                                        <i class="ico icon-bold-trash-bin-2"></i>
                                    </button>

                                </td>
                            </tr>

                            {{-- DELETE MODAL --}}
                            <div class="modal fade" id="deleteModal{{ $activity->id }}" tabindex="-1">
                                <div class="modal-dialog modal-sm modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Delete Activity</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body text-center">
                                            <i class="ico icon-bold-trash-bin-2 text-danger"
                                               style="font-size:40px;"></i>
                                            <h5 class="mt-2">Are you sure?</h5>
                                        </div>

                                        <div class="modal-footer">
                                            {{ Form::open(['url' => 'syscomfresh/business-activity/'.$activity->id, 'method' => 'DELETE']) }}
                                                <button class="btn btn-danger w-100">Delete</button>
                                            {{ Form::close() }}
                                        </div>

                                    </div>
                                </div>
                            </div>

                        @endforeach
                    </tbody>

                </table>

            </div>
        </div>
    </div>
</div>


{{-- ADD / EDIT MODAL --}}



<script>

function filterActivity() {
    var text = document.getElementById("activitySearch").value.toLowerCase();
    document.querySelectorAll("#long-list tbody tr").forEach(function(row){
        row.style.display = row.innerText.toLowerCase().includes(text) ? "" : "none";
    });
}

function openAddActivity() {
    $("#modalTitle").text("Add Business Activity");
    $("#activityForm").trigger("reset");
    $("#activityForm").attr("action", "{{ url('business-activity') }}");
    $("#activityForm").find("input[name=_method]").remove();
    $("#saveBtn").text("Save");
    new bootstrap.Modal(document.getElementById('activityModal')).show();
}

function openEditActivity(id,name,industry_id) {

    $("#modalTitle").text("Edit Business Activity");
    $("#activityForm").trigger("reset");

    $("#activityForm").attr("action", "/syscomfresh/business-activity/" + id);
    $("#activityForm").find("input[name=_method]").remove();
    $("#activityForm").append('<input type="hidden" name="_method" value="PUT">');

    $("#activity_id").val(id);
    $("#activity_name").val(name);
    $("#industry_id").val(industry_id);

    $("#saveBtn").text("Update");

    new bootstrap.Modal(document.getElementById('activityModal')).show();
}

</script>

@endsection
<div class="modal fade admin-query" id="activityModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header p-3">
                <h4 class="modal-title" id="modalTitle">Add Business Activity</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3">
                <form id="activityForm" method="POST">
                    @csrf

                    <input type="hidden" name="id" id="activity_id">

                    <label class="form-label">Industry <span class="text-danger">*</span></label>
                    <select class="form-control" name="industry_id" id="industry_id" required>
                        <option value="">Select Industry</option>
                        @foreach($industries as $ind)
                            <option value="{{ $ind->id }}">{{ $ind->name }}</option>
                        @endforeach
                    </select>

                    <label class="form-label mt-3">Business Activity <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="activity_name" required>

                    <button type="submit" class="btn btn-success mt-3 w-100" id="saveBtn">Save</button>
                </form>
            </div>

        </div>
    </div>
</div>
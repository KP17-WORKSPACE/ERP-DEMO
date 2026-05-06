@extends('backEnd.newmasterpage')
@section('mainContent')

@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<div class="content-container col-12">
   <div class="d-flex align-items-center justify-content-between">

    <!-- LEFT TITLE -->
    <h4 class="mb-0">Department List</h4>

    <!-- RIGHT BUTTONS -->
    <div class="d-flex align-items-center gap-2">

        <!-- REAL-TIME SEARCH (NO ICON) -->
        <input type="text" id="deptSearch" 
               class="form-control"
               style="width: 220px;"
               placeholder="Search..."
               onkeyup="filterDepartment()">

        <!-- ADD BUTTON -->
        <button class="btn btn-light text-dark add-btn" onclick="openAddDepartment()">
            <i class="ico icon-outline-add-square text-success"></i> Add
        </button>

        <!-- 3 DOTS MENU -->
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
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('/company') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Settings
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


    {{-- LIST TABLE --}}
    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($departments as $department)
                        <tr>
                            <td>[{{ $department->id }}] {{ $department->name }}</td>

                            <td class="d-flex gap-2">

                                {{-- EDIT BUTTON --}}
                                <button 
                                    class="btn btn-light btn-sm p-1"
                                    onclick="openEditDepartment({{ $department->id }}, '{{ $department->name }}')">
                                    <i class="ico icon-outline-pen-2 text-dark"></i>
                                </button>

                                {{-- DELETE BUTTON --}}
                                <button 
                                    class="btn btn-light btn-sm p-1 text-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal{{ $department->id }}">
                                    <i class="ico icon-bold-trash-bin-2"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- DELETE MODAL --}}
                        <div class="modal fade" data-bs-backdrop="false" id="deleteModal{{ $department->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Delete Department</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body text-center">
                                        <i class="ico icon-bold-trash-bin-2 text-danger" style="font-size:40px;"></i>
                                        <h5 class="mt-2">Are you sure?</h5>
                                        <p class="text-muted small">This action cannot be undone.</p>
                                    </div>

                                    <div class="modal-footer">
                                        {{ Form::open(['url' => 'department/' . $department->id, 'method' => 'DELETE']) }}
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




{{-- ======================================================
       ADD / EDIT DEPARTMENT MODAL (Bootstrap 5)
====================================================== --}}






{{-- ======================================================
       SCRIPT – OPEN ADD/EDIT MODAL (NO FREEZE)
====================================================== --}}
<script>

    function openAddDepartment() {

        $("#modalTitle").text("Add Department");
        $("#departmentForm").trigger("reset");

        $("#departmentForm").attr("action", "{{ url('department') }}");
        $("#departmentForm").find("input[name=_method]").remove();

        $("#dept_id").val("");
        $("#saveBtn").text("Save");

        new bootstrap.Modal(document.getElementById('departmentModal')).show();
    }



function openEditDepartment(id, name) {

    $("#modalTitle").text("Edit Department");

    $("#departmentForm")[0].reset();

    $("#departmentForm").attr("action", "/department/" + id);

    $("#departmentForm").find("input[name=_method]").remove();
    $("#departmentForm").append('<input type="hidden" name="_method" value="PUT">');

    $("#dept_id").val(id);
    $("#dept_name").val(name);

    $("#saveBtn").text("Update");

    new bootstrap.Modal(document.getElementById('departmentModal')).show();
}



</script>

@endsection
<div class="modal fade admin-query" 
     id="departmentModal" 
     tabindex="-1"
     aria-hidden="true"
     data-bs-backdrop="static"
     data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header p-3">
                <h4 class="modal-title" id="modalTitle">Add Department</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3">

                <form id="departmentForm" method="POST">
                    @csrf

                    <input type="hidden" name="id" id="dept_id">

                    <label class="form-label">Department Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="dept_name"
                           autocomplete="off" required>

                    <button type="submit" class="btn btn-success mt-3 w-100" id="saveBtn">
                        Save
                    </button>
                </form>

            </div>

        </div>
    </div>
</div>

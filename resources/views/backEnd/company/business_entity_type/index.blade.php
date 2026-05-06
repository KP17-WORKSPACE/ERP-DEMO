@extends('backEnd.newmasterpage')
@section('mainContent')

@php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<div class="content-container col-12">

    {{-- HEADER --}}
    <div class="purchase-order-content-header">
        <h4 class="purchase-order-content-header-left">Business Entity Type</h4>

        <div class="purchase-order-content-header-right d-flex align-items-center gap-2">

            <input type="text" id="entitySearch" class="form-control"
                   style="width: 220px;" placeholder="Search..." onkeyup="filterEntity()">

            <button class="btn btn-light text-dark" onclick="openAddEntityType()">
                <i class="ico icon-outline-add-square text-success"></i> Add
            </button>

            <div class="dropdown">
                <button class="btn btn-light text-dark dropdown-toggle syscom-dropdown-toggle"
                        type="button" data-bs-toggle="dropdown">
                    <i class="ico icon-outline-hamburger-menu"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a class="dropdown-item" href="{{ url('syscomfresh/company') }}">Company</a></li>
                    <li><a class="dropdown-item" href="{{ url('syscomfresh/department') }}">Department</a></li>
                    <li><a class="dropdown-item" href="{{ url('syscomfresh/designation') }}">Designation</a></li>
                    <li><a class="dropdown-item" href="{{ url('syscomfresh/industry') }}">Industry</a></li>
                </ul>
            </div>

        </div>
    </div>


    {{-- TABLE SECTION --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">

                <table id="long-list" class="table table-hover data-table" style="width:100%">
                    <thead>
                    <tr>
                        <th>Sl.No</th>
                        <th>Business Entity Type</th>
                        <th class="text-center">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($entities as $entity)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $entity->name }}</td>

                            <td class="d-flex justify-content-center">

                                <button class="btn btn-light btn-sm p-1"
                                        onclick="openEditEntityType({{ $entity->id }}, '{{ $entity->name }}')">
                                    <i class="ico icon-outline-pen-2 text-dark"></i>
                                </button>

                                <button class="btn btn-light btn-sm p-1 text-danger"
        onclick="openDeleteEntity({{ $entity->id }})">
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


{{-- =======================
    ADD / EDIT MODAL
======================= --}}



{{-- =======================
    JS FUNCTIONS
======================= --}}
<script>

function filterEntity() {
    let text = document.getElementById("entitySearch").value.toLowerCase();
    document.querySelectorAll("#long-list tbody tr").forEach(function(row){
        row.style.display = row.innerText.toLowerCase().includes(text) ? "" : "none";
    });
}

function openAddEntityType() {
    $("#entityModalTitle").text("Add Business Entity Type");
    $("#entityTypeForm").trigger("reset");

    $("#entityTypeForm").attr("action", "{{ url('business-entity-type') }}");
    $("#entityTypeForm").find("input[name=_method]").remove();

    $("#entitySaveBtn").text("Save");

    new bootstrap.Modal(document.getElementById('entityTypeModal')).show();
}

function openEditEntityType(id, name) {

    $("#entityModalTitle").text("Edit Business Entity Type");
    $("#entityTypeForm").trigger("reset");

    $("#entityTypeForm").attr("action", "{{ url('business-entity-type/update') }}/" + id);
    $("#entityTypeForm").find("input[name=_method]").remove();

    $("#entity_id").val(id);
    $("#entity_name").val(name);

    $("#entitySaveBtn").text("Update");

    new bootstrap.Modal(document.getElementById('entityTypeModal')).show();
}

function openDeleteEntity(id) {

    let url = "{{ url('business-entity-type/delete') }}/" + id;

    $("#deleteEntityForm").attr("action", url);
    $("#deleteEntityForm").prop("action", url); // Laravel 5.7 fix

    new bootstrap.Modal(document.getElementById('globalDeleteModal')).show();
}


</script>

@endsection
<div class="modal fade admin-query" id="entityTypeModal" tabindex="-1" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">

    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header p-3">
                <h4 class="modal-title" id="entityModalTitle">Add Business Entity Type</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-3">
                <form id="entityTypeForm" method="POST">
                    @csrf

                    <input type="hidden" name="id" id="entity_id">

                    <label class="form-label">Entity Type Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" id="entity_name" required>

                    <button type="submit" class="btn btn-success mt-3 w-100" id="entitySaveBtn">Save</button>
                </form>
            </div>

        </div>
    </div>
</div>

{{-- GLOBAL DELETE MODAL --}}
<div class="modal fade" id="globalDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Delete Entity Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <i class="ico icon-bold-trash-bin-2 text-danger" style="font-size:40px;"></i>
                <h5 class="mt-2">Are you sure?</h5>
            </div>

            <div class="modal-footer">
                <form method="POST" id="deleteEntityForm">
                    @csrf
                    <button class="btn btn-danger w-100">Delete</button>
                </form>
            </div>

        </div>
    </div>
</div>

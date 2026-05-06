@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs">

            <div id="data-details">

                <!-- HEADER Like Department -->
                <div class="d-flex align-items-center justify-content-between mb-3">

                    <!-- LEFT TITLE -->
                    <h4 class="mb-0">Designation List</h4>

                    <!-- RIGHT ACTIONS -->
                    <div class="d-flex align-items-center gap-2">

                        <input type="text" id="tableSearch" class="form-control"
                            style="font-size:13px; width: 350px; 
                        top: 12px;
                        right: 120px;"
                            placeholder="Search">

                        <!-- ADD BUTTON -->
                        <button class="btn btn-light text-dark" onclick="openAddDesignation()">
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
                        href="{{ url('/company') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Settings
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


                <!-- FULL WIDTH TABLE -->
                <div class="card mb-3">
                    <div class="card-body">

                        <table class="table table-hover data-table" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center" width="30">ID</th>
                                    <th>Department</th>
                                    <th>Designation</th>
                                    <th>Grade</th>
                                    <th width="100" class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($designations as $d)
                                    <tr>
                                        <td class="text-center">{{ $d->id }}</td>
                                        <td>{{ $d->department->name ?? '-' }}</td>
                                        <td>{{ $d->title }}</td>
                                        <td>
                                            @if ($d->grade)
                                                {{ str_replace('g', 'Grade ', $d->grade) }}
                                            @else
                                                -
                                            @endif

                                        <td class="d-flex justify-content-center gap-2">

                                            <!-- EDIT -->
                                            <button class="btn btn-light btn-sm p-1"
                                                onclick="openEditDesignation({{ $d->id }}, '{{ $d->title }}', '{{ $d->department_id }}', '{{ $d->grade ?? '' }}')">
                                                <i class="ico icon-outline-pen-2 text-dark" style="font-size:16px"></i>
                                            </button>

                                            <!-- DELETE -->
                                            <button class="btn btn-light btn-sm p-1 text-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteDesignationModal{{ $d->id }}">
                                                <i class="ico icon-outline-trash-bin-minimalistic text-dark"
                                                    style="font-size:16px"></i>
                                            </button>

                                        </td>
                                    </tr>

                                    <!-- DELETE MODAL -->
                                    <div class="modal fade" data-bs-backdrop="false" tabindex="-1"
                                        aria-labelledby="editModalLabel" aria-modal="true" role="dialog"
                                        id="deleteDesignationModal{{ $d->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-sm modal-dialog-centered" style="top:-10%">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Delete Designation</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body text-center">
                                                    <i class="ico icon-outline-trash-bin-minimalistic text-danger"
                                                        style="font-size:40px;"></i>
                                                    <h5 class="mt-2">Are you sure to delete?</h5>
                                                    <p class="text-muted small">This action cannot be undone.</p>
                                                </div>

                                                <div class="modal-footer">
                                                    {{ Form::open(['url' => 'designation/' . $d->id, 'method' => 'DELETE']) }}
                                                    <button class="btn btn-light"><i class="ico icon-outline-trash-bin-minimalistic text-dark" style="font-size:16px"></i> Delete</button>
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


        <div class="modal side-panel fade showadmin-query" id="designationModal" data-bs-backdrop="false" tabindex="-1"
            aria-labelledby="editModalLabel" aria-modal="true" role="dialog">

            <div class="modal-dialog modal-sm modal-dialog-centered" style="top:-18%">
                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="designationModalTitle">Add Designation</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="designationForm" method="POST">
                        @csrf
                        <div class="modal-body p-3">



                            <input type="hidden" name="id" id="designation_id">

                            <!-- Department Dropdown -->
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm js-example-basic-single" name="department_id"
                                id="department_id" required>
                                <option value="">Select Department</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>

                           
                            <!-- Designation Title -->
                            <label class="form-label mt-3">Designation Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control  capitalize-title" name="title"
                                id="designation_title" autocomplete="off" required>



 <!-- Grade Select -->
                            <label class="form-label mt-3">Grade</label>
                            <select class="form-select form-select-sm js-example-basic-single" name="grade" id="designation_grade">
                                <option value="">Select Grade</option>
                                <option value="g1">Grade 1</option>
                                <option value="g2">Grade 2</option>
                                <option value="g3">Grade 3</option>
                                <option value="g4">Grade 4</option>
                                <option value="g5">Grade 5</option>
                                <option value="g6">Grade 6</option>
                            </select>




                        </div>

                        <div class="modal-footer">

                            <button type="submit" class="btn btn-light">
                                <i class="ico icon-outline-bookmark-opened text-success"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>





    {{-- ===================================================================
       ADD / EDIT DESIGNATION MODAL (POPUP)
=================================================================== --}}






    {{-- ===================================================================
       JAVASCRIPT — POPUP ADD / EDIT
=================================================================== --}}
    <script>
        // OPEN ADD
        function openAddDesignation() {

            $("#designationModalTitle").text("Add Designation");
            $("#designationForm").trigger("reset");

            // Reset grade select
            $("#designation_grade").val('').change();

            // Correct STORE URL for syscomfresh route
            $("#designationForm").attr("action", "/designation");

            // Remove any previous PUT method
            $("#designationForm").find("input[name=_method]").remove();

            $("#designationSaveBtn").text("Save");

            new bootstrap.Modal(document.getElementById('designationModal')).show();
        }


        // OPEN EDIT
        function openEditDesignation(id, title, dept, grade) {

            $("#designationModalTitle").text("Edit Designation");
            $("#designationForm").trigger("reset");

            // Correct UPDATE URL for syscomfresh route
            $("#designationForm").attr("action", "/designation/" + id);

            // Add PUT method
            $("#designationForm").find("input[name=_method]").remove();
            $("#designationForm").append('<input type="hidden" name="_method" value="PUT">');

            // SET VALUES (Correct IDs)
            $("#designation_id").val(id);
            $("#designation_title").val(title);

            // Set grade value
            $("#designation_grade").val(grade || '').change();

            // Fix department dropdown fill (little delay improves reliability)
            setTimeout(() => {
                $("#department_id").val(dept).change();
            }, 50);

            // Button text
            $("#designationSaveBtn").text("Update");

            new bootstrap.Modal(document.getElementById('designationModal')).show();
        }
    </script>
@endsection

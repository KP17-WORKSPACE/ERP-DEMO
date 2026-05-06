@extends('backEnd.newmasterpage')
@section('mainContent')




    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
                                        ?>

    <?php try { ?>



    <aside class="left-nav col-12" id="leftSidebar">


        <div class="long-list" id="filters-long">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Book Close Doc Number
                </h4>
                <div class="search-filter-container mb-0">



                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">



                           <div class="dropdown">
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
                               
                            </ul>
                        </div>




                </div>
            </div>


        </div>

        <div class="left-nav-list">



            <div class="table-responsive mb-4 mt-4">
                <table id="long-list" class="table table-hover data-table" style="table-layout: fixed;width:100%">

                    <thead>
                        <tr>
                            <th class="text-start">Modules</th>
                            <th class="text-center">Book Closed</th>
                            <th class="text-center">Doc Number</th>
                            <th class="text-center">Last Updated By</th>
                            <th class="text-center">Last Updated On</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($book_data as $dt)
                            <tr>
                                <td class="text-start">{{ $dt->book_name }}</td>

                                <td class="text-center">
                                    @if(empty($dt->book_closed_date)) -- @else
                                    {{ \Carbon\Carbon::parse($dt->book_closed_date)->format('d/m/Y') }} @endif
                                </td>
                                <td class="text-center">{{ $dt->doc_number ?: '--' }}</td>

                                <td class="text-center">{{ $dt->updated_by ?: '--' }}</td>

                                <td class="text-center">
                                    {{ empty($dt->updated_at) ? '--' : \Carbon\Carbon::parse($dt->updated_at)->format('d/m/Y H:i') }}
                                </td>


                                <td class="text-center">
                                    <button class="btn-sm btn-light" onclick="book_edit({{ $dt->id }})">Set Doc No</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>


                </table>
            </div>
        </div>
    </aside>







    {{-- MODAL --}}
    <div class="modal side-panel fade" id="ModalView" tabindex="-1" data-bs-backdrop="false" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel"><span id="book_name"></span> - Closing Doc Number</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- FORM --}}
                <form id="bookCloseForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="book_id" id="book_id">
                    <input type="hidden" name="company_id" value="{{ session('logged_session_data.company_id') }}">

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Book Closing Date</label>
                                <input type="text" class="form-control date-picker" name="closing_date" id="closing_date" required>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label">Closing Doc Number</label>
                                <input type="text" class="form-control" name="doc_number" id="doc_number" required>
                            </div>

                            <div class="col-md-3 d-flex align-items-end justify-content-end">
                                <button class="btn btn-light w-100"><i class="ico icon-outline-bookmark-opened text-success"></i> Save</button>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- HISTORY TABLE --}}
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Closing Date</th>
                                    <th>Closing Doc Number</th>
                                    <th>Updated By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="historyTable">
                                <tr>
                                    <td colspan="4" class="text-center">No data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
        $('#ModalView').on('hidden.bs.modal', function () {
            $("#loading_bg").css("display", "block");
            location.reload();
        });
        function book_edit(id) {
            $.get('book-close-doc-edit/' + id, function (res) {

                $('#exampleModalLabel').text('Edit ' + res.book.book_name);
                $('#book_id').val(res.book.id);
                $('#book_name').text(res.book.book_name);
                $('#closing_date').val(res.book.closing_date);
                $('#doc_number').val(res.book.doc_number);

                let rows = '';
                if (res.history.length) {
                    res.history.forEach(row => {
                        rows += `
                    <tr>
                        <td>${row.closing_date}</td>
                        <td>${row.doc_number}</td>
                        <td>${row.updatedby}</td>
                        <td>
                            <button class="btn btn-sm btn-light text-danger" type="button" onclick="deleteRow(${row.id})">
                                <i class="ico icon-outline-trash-bin-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                    });
                } else {
                    rows = `<tr><td colspan="5" class="text-center">No data</td></tr>`;
                }

                $('#historyTable').html(rows);
                $('#ModalView').modal('show');
            });
        }

        $(document).on('submit', '#bookCloseForm', function (e) {
            e.preventDefault();

            let form = this;
            let bookId = $('#book_id').val();

            $.ajax({
                url: 'book-close-doc-update',
                type: 'POST',
                data: new FormData(form),
                contentType: false,
                processData: false,
                success: function () {
                    // alert('Saved successfully');
                    //toastr
                    toastr.success('Saved successfully');

                    // reload history list without closing modal
                    book_edit(bookId);

                    // optional
                    // $('#ModalView').modal('hide');
                    // location.reload();
                }
            });
        });

        function deleteRow(id) {
            if (!confirm('Delete record?')) return;

            $.ajax({
                url: 'book-close-doc-delete/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function () {
                    // alert('Deleted');
                    //toastr
                    toastr.success('Deleted Successfully');
                    let bookId = $('#book_id').val();
                    book_edit(bookId);
                }
            });
        }
    </script>


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection
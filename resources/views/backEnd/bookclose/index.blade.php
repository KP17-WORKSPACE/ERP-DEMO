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
                <h4 class="mb-0">Book Close
                </h4>
                <div class="search-filter-container mb-0">



                    <input type="text" id="tableSearch" class="form-control d-inline-block"
                        style="font-size:13px;width: 350px;" placeholder="Search">

                                <button class="btn btn-light float-right" onclick="close_all()">
                                    <i class="ico icon-outline-close-square  text-success"></i> Close All
                                </button>


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
                                        href="{{ url('book-close-doc-number') }}">
                                        <i class="ico icon-outline-settings text-success title-15 me-2"></i>
                                        Book Close Doc No
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

                                <td class="text-center">{{ $dt->updated_by ?: '--' }}</td>

                                <td class="text-center">
                                    {{ empty($dt->updated_at) ? '--' : \Carbon\Carbon::parse($dt->updated_at)->format('d/m/Y H:i') }}
                                </td>

                                <td class="text-center">
                                    <button class="btn-sm btn-light" onclick="book_edit({{ $dt->id }})">View</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>


                </table>
            </div>
        </div>
    </aside>








    <div class="modal side-panel fade" id="ModalViewAll" tabindex="-1" data-bs-backdrop="false" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Book Close All</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- FORM --}}
                <form id="bookCloseFormAll" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="company_id" value="{{ session('logged_session_data.company_id') }}">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Book Close Till</label>
                                <input type="text" class="form-control date-picker" name="book_closed_date" id="book_closed_date"
                                    required>
                            </div>

                            <div class="col-md-5">
                                <label>Reason</label>
                                <input type="text" class="form-control" name="reason" id="reason" required>
                            </div>

                            <div class="col-md-3">
                                <label>Attachment</label>
                                <input type="file" class="form-control" name="attachment[]" multiple>
                            </div>
                            <div class="col-md-1 mt-3">
                                <button class="btn btn-light mt-2"> <i class="ico icon-outline-bookmark-opened text-success"></i> Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function close_all() {
            $('#ModalViewAll').modal('show');
        }
        $(document).on('submit', '#bookCloseFormAll', function (e) {
            e.preventDefault();

            let form = this;

            $.ajax({
                url: 'book-close-data-update-all',
                type: 'POST',
                data: new FormData(form),
                contentType: false,
                processData: false,
                success: function () {
                    toastr.success('Saved successfully');
                    $('#ModalViewAll').modal('hide');
                    $("#loading_bg").css("display", "block");
                    location.reload();
                }
            });
        });
    </script>

    {{-- MODAL --}}
    <div class="modal side-panel fade" id="ModalView" tabindex="-1" data-bs-backdrop="false" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Book Close <label id="book_name"></label></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- FORM --}}
                <form id="bookCloseForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="book_id" id="book_id">
                    <input type="hidden" name="company_id" value="{{ session('logged_session_data.company_id') }}">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-2">
                                <label>Book Close Till</label>
                                <input type="text" class="form-control date-picker" name="book_closed_date" id="book_closed_date"
                                    required>
                            </div>

                            <div class="col-md-5">
                                <label>Reason</label>
                                <input type="text" class="form-control" name="reason" id="reason" required>
                            </div>

                            <div class="col-md-3">
                                <label>Attachment</label>
                                <input type="file" class="form-control" name="attachment[]" multiple>
                            </div>
                            <div class="col-md-1 mt-3">
                                <button class="btn btn-light mt-2"> <i class="ico icon-outline-bookmark-opened text-success"></i> Save</button>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- HISTORY TABLE --}}
                <div class="modal-body">
                    <table class="table table-hover" id="long-list">
                        <thead>
                            <tr>
                                <th>Closed Till</th>
                                <th>Reason</th>
                                <th>Attachment</th>
                                <th>Updated By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="historyTable">
                            <tr>
                                <td colspan="5" class="text-center">No data</td>
                            </tr>
                        </tbody>
                    </table>
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
            $.get('book-close-edit/' + id, function (res) {

                $('#exampleModalLabel').text('Edit ' + res.book.book_name);
                $('#book_id').val(res.book.id);
                $('#book_name').text(res.book.book_name);
                $('#book_closed_date').val(res.book.book_closed_date);
                $('#reason').val('');

                let rows = '';
                if (res.history.length) {
                    res.history.forEach(row => {
                        rows += `
                    <tr>
                        <td>${row.book_closed_date}</td>
                        <td>${row.reason}</td>
                        <td>${row.attachment}</td>
                        <td>${row.updatedby}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-light text-danger"
                                    onclick="deleteRow(${row.id})">
                                <i class="ico icon-outline-trash-bin-trash" style="font-size: 16px;"></i>
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
                url: 'book-close-data-update',
                type: 'POST',
                data: new FormData(form),
                contentType: false,
                processData: false,
                success: function () {
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
                url: 'book-close-delete/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function () {
                    toastr.success('Deleted');
                    let bookId = $('#book_id').val();
                    book_edit(bookId);
                }
            });
        }
    </script>


    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
@endsection
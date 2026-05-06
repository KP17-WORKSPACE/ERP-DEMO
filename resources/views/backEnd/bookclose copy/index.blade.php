@extends('backEnd.masterpage')
@section('mainContent')

@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->where('is_read',1)->where('module_link_id',68)->get();    
    if(Auth::user()->role_id != 1){
        if($permissions->count() == 0){
            echo "you are not authorized to access this page !";
            return view('backEnd.crm.dashboard');
        }
    }
@endphp

<style>
.border { border: solid 1px #e3e6f0; }
</style>

    <?php try { ?>
        
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Book Close</h2>
            <span class="page-label">Home - Book Close</span>
        </div>
        <div>
            {{--  <a href="{{ url('add-customer') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Customer</a>
            <a href="{{ url('customers') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Customer List</a>
            <a href="{{ url('customer-edit/' . @$custDetails->id) }}"><button class="btn btn-primary">Edit Profile</button></a>  --}}
        </div>
    </div>
{{-- BOOK LIST TABLE --}}
<div class="card p-4">
    <div class="table-responsive-sm">
        <button class="btn-sm btn-danger float-right" onclick="close_all()">Close All</button>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th class="text-center">Modules</th>
                <th class="text-center">Book Closed</th>
                <th class="text-center">Last Updated By</th>
                <th class="text-center">Last Updated On</th>
                <th class="text-center">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($book_data as $dt)
                <tr>
                    <td class="text-center">{{ $dt->book_name }}</td>

                    <td class="text-center">
                        @if(empty($dt->book_closed_date)) -- @else {{ \Carbon\Carbon::parse($dt->book_closed_date)->format('d/m/Y') }} @endif
                    </td>

                    <td class="text-center">{{ $dt->updated_by ?: '--' }}</td>

                    <td class="text-center">
                        {{ empty($dt->updated_at) ? '--' : \Carbon\Carbon::parse($dt->updated_at)->format('d/m/Y H:i') }}
                    </td>

                    <td class="text-center">
                        <button class="btn-sm btn-primary" onclick="book_edit({{ $dt->id }})">View</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>


<div class="modal fade" id="ModalViewAll" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Book Close All</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            {{-- FORM --}}
            <form id="bookCloseFormAll" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="company_id" value="{{ session('logged_session_data.company_id') }}">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2">
                            <label>Book Close Till</label>
                            <input type="date" class="form-control" name="book_closed_date" id="book_closed_date" required>
                        </div>

                        <div class="col-md-5">
                            <label>Reason</label>
                            <input type="text" class="form-control" name="reason" id="reason" required>
                        </div>

                        <div class="col-md-4">
                            <label>Attachment</label>
                            <input type="file" class="form-control" name="attachment[]" multiple>
                        </div>
                        <div class="col-md-1">&nbsp;<br />
                            <button class="btn btn-primary mt-2">Save</button>
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
            alert('Saved successfully');
            $('#ModalViewAll').modal('hide');
            $("#loading_bg").css("display", "block");
            location.reload();
        }
    });
});
</script>

{{-- MODAL --}}
<div class="modal fade" id="ModalView" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Book Close <label id="book_name" ></label></h5>
                <button class="close" data-dismiss="modal">&times;</button>
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
                            <input type="date" class="form-control" name="book_closed_date" id="book_closed_date" required>
                        </div>

                        <div class="col-md-5">
                            <label>Reason</label>
                            <input type="text" class="form-control" name="reason" id="reason" required>
                        </div>

                        <div class="col-md-4">
                            <label>Attachment</label>
                            <input type="file" class="form-control" name="attachment[]" multiple>
                        </div>
                        <div class="col-md-1">&nbsp;<br />
                            <button class="btn btn-primary mt-2">Save</button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- HISTORY TABLE --}}
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Closed Till</th>
                        <th>Reason</th>
                        <th>Attachment</th>
                        <th>Updated By</th>
                        <th>Delete</th>
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
                        <button class="btn-sm btn-danger btn-sm"
                                onclick="deleteRow(${row.id})"><i class="fa fa-trash" aria-hidden="true"></i></button>
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
            alert('Saved successfully');

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
        data: {_token: '{{ csrf_token() }}'},
        success: function () {
            alert('Deleted');
            let bookId = $('#book_id').val();
            book_edit(bookId);
        }
    });
}
</script>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
    
@endsection

@extends('backEnd.masterpage')
@section('mainContent')

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Reimbursement List</h2>
            <span class="page-label">Home - Reimbursement List</span>
        </div>
        <div>
            <a href="#" type="button" class="btn btn-primary" data-toggle="modal" data-target="#ModalService"><i class="fa fa-plus"></i> Reimbursement</a>
        </div>
        <div style="display: none;">
            <a href="{{ url('crm-leads') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Lead</a>            
            <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Filter By 
            </button>
            <a href="{{ url()->previous() }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back</a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" href="#" onclick="sort(1)">Today</a>
                <a class="dropdown-item" href="#" onclick="sort(2)">This Week</a>
                <a class="dropdown-item" href="#" onclick="sort(3)">Last Week</a>
                <a class="dropdown-item" href="#" onclick="sort(4)">This Month</a>
                <a class="dropdown-item" href="#" onclick="sort(5)">Last Month</a>
                <a class="dropdown-item" href="#" onclick="sort(6)">Last 6 Month</a>
                <a class="dropdown-item" href="#" onclick="sort(7)">This Year</a>
                <a class="dropdown-item" href="#" onclick="sort(8)">Last Year</a>
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads/show', 'method' => 'POST', 'id' => 'crm-leads-search']) }}
                    <input type="hidden" name="sort_id" id="sort_id" value="1" />
                    <button type="submit" id="btn_sort" style="display: none;"></button>
                {{ Form::close() }}
            </div>
            <script>
                function sort(id) {
                    $("#sort_id").val(id);
                    $("#btn_sort").click();
                }
            </script>
            
        </div>
    </div>
    
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        @if(session()->has('message-success') != "" || session()->get('message-danger') != "")
                                        <tr>
                                            <td colspan="7">
                                                @if(session()->has('message-success'))
                                                <div class="alert alert-success">
                                                    {{ session()->get('message-success') }}
                                                </div>
                                                @elseif(session()->has('message-danger'))
                                                <div class="alert alert-danger">
                                                    {{ session()->get('message-danger') }}
                                                </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        
                                        <tr>
                                            <th width="50px">Date</th>
                                            <th width="50px">Deal ID</th>
                                            <th>Site Name</th>
                                            <th width="100px">Scope of Work</th>
                                            <th width="70px">Invoice No</th>
                                            <th width="70px">Amount</th>
                                            <th width="150px">Remarks (Exp Purpose)</th>
                                            <th>Head Count & Name</th>
                                            <th width="100px">Submited By</th>
                                            <th width="50px">Attachment</th>
                                            <th width="150px">Status</th>
                                            <th width="150px"></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        
                                        @foreach($data as $value)
                                <tr @if($value->status==2) class="bg-dark" @endif>
                                    <td>{{date('d/m/Y', strtotime(@$value->date))}}</td>
                                    <td>{{@$value->deal_code->code}}</td>
                                    <td>{{@$value->site_name}}</td>
                                    <td>{{@$value->scope_of_work}}</td>
                                    <td>{{@$value->invoice_no}}</td>
                                    <td>{{@$value->amount}}</td>
                                    <td>{{@$value->remarks}}</td>
                                    <td>{{@$value->head_count_name}}</td>
                                    <td>{{@$value->createdby->full_name}}</td>
                                    <td>
                                        @if($value->attachmant!="")
                                        <?php $file = explode("|",$value->attachmant); ?>
                                        @foreach ($file as $f)
                                        <a class="text-primary" href="{{asset('public/uploads/crm_amc_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                        @endforeach
                                        @endif
                                    </td>

                                    <td>
                                        @if($value->accounts_status==1) <span class="success btn-badge py-1 px-2">Accounts Approved</span> {{ @$value->accountsby->full_name }}
                                        @elseif($value->accounts_status==2) <span class="rejected btn-badge py-1 px-2">Accounts Rejected</span> {{ @$value->accountsby->full_name }}
                                        @elseif($value->acco_head_status==1) <span class="success btn-badge py-1 px-2">Accounts Head Approved</span> {{ @$value->accoheadby->full_name }}
                                        @elseif($value->acco_head_status==2) <span class="rejected btn-badge py-1 px-2">Accounts Head Rejected</span> {{ @$value->accoheadby->full_name }}
                                        @elseif($value->dept_head_status==1) <span class="success btn-badge py-1 px-2">Dept Head Approved</span> {{ @$value->deptheadby->full_name }}
                                        @elseif($value->dept_head_status==2) <span class="rejected btn-badge py-1 px-2">Dept Head Rejected</span> {{ @$value->deptheadby->full_name }}
                                        @else <span class="warning btn-badge py-1 px-2">New / Pending</span> @endif
                                    </td>
                                    <td>
                                    <input type="hidden" id="edit_date_{{ $value->id }}" value="{{ $value->date }}" />
                                    <input type="hidden" id="edit_deal_id_{{ $value->id }}" value="{{ $value->deal_id }}" />
                                    <input type="hidden" id="edit_site_name_{{ $value->id }}" value="{{ $value->site_name }}" />
                                    <input type="hidden" id="edit_scope_of_work_{{ $value->id }}" value="{{ $value->scope_of_work }}" />
                                    <input type="hidden" id="edit_invoice_no_{{ $value->id }}" value="{{ $value->invoice_no }}" />
                                    <input type="hidden" id="edit_amount_{{ $value->id }}" value="{{ $value->amount }}" />
                                    <input type="hidden" id="edit_remarks_{{ $value->id }}" value="{{ $value->remarks }}" />
                                    <input type="hidden" id="edit_head_count_name_{{ $value->id }}" value="{{ $value->head_count_name }}" />

                                        @if(Auth::user()->id == $value->created_by)
                                            <a class="btn-sm btn-primary" onclick="fun_edit({{ $value->id }})"><i class="fa fa-edit" aria-hidden="true"></i></a>

                                            @if($value->status==1)
                                            <a class="btn-sm btn-danger" onclick="fun_delete({{ $value->id }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            @else
                                            <a class="btn-sm btn-warning" onclick="fun_restore({{ $value->id }})"><i class="fa fa-recycle" aria-hidden="true"></i></a>
                                            @endif

                                        @endif

                                        @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)                                        
                                            @if($value->dept_head_status==0)
                                                <a class="btn-sm btn-info" onclick="fun_dept_head({{ $value->id }})">Update</a>
                                            @elseif($value->dept_head_status==1 && $value->acco_head_status==0)
                                                <a class="btn-sm btn-info" onclick="fun_account_head({{ $value->id }})">Update</a>
                                            @elseif($value->dept_head_status==1 && $value->acco_head_status==1 && $value->accounts_status==0)
                                                <a class="btn-sm btn-info" onclick="fun_account({{ $value->id }})">Update</a>
                                            @else
                                                
                                            @endif                                            
                                        @endif

                                        @if(Auth::user()->role_id == 28)
                                        <a class="btn-sm btn-info" onclick="fun_account({{ $value->id }})">Update</a>
                                        @endif

                                        @if(Auth::user()->role_id == 27)
                                        <a class="btn-sm btn-info" onclick="fun_account_head({{ $value->id }})">Update</a>
                                        @endif

                                        @if(Auth::user()->role_id == 8)
                                        <a class="btn-sm btn-info" onclick="fun_dept_head({{ $value->id }})">Update</a>
                                        @endif

                                    </td>
                                </tr>
                                  
                                @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <script>
                    function fun_account(id){
                        $('#account_re_id').val(id);
                        $('#btnAccounts').click();
                    }
                    function fun_account_head(id){
                        $('#acco_head_re_id').val(id);
                        $('#btnAccountsHead').click();
                    }
                    function fun_dept_head(id){
                        $('#dept_head_re_id').val(id);
                        $('#btnDepartmentHead').click();
                    }
                </script>
<?php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<!-- MODAL REIMBURSEMENT-->
    <div class="modal fade" id="ModalService" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Reimbursement</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-add', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" id="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal ID</label>
                                <input type="text" class="form-control" name="deal_id" id="deal_id" onchange="get_custName()" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="site_name" id="site_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Scope of Work</label>
                                <input type="text" class="form-control" name="scope_of_work" id="scope_of_work" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Invoice No</label>
                                <input type="text" class="form-control" name="invoice_no" id="invoice_no" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Amount</label>
                                <input type="number" class="form-control" name="amount" step="Any" id="amount" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Remarks (Expenses purpose)</label>
                                <select class="form-control" name="remarks" id="remarks" onchange="remarks_change()">
                                        <option value="Food Expenses" >Food Expenses</option>
                                        <option value="Travelling Expenses" >Travelling Expenses</option>
                                        <option value="Accessory" >Accessory</option>
                                        <option value="Other" >Other</option>
                                </select>
                                <input type="text" class="form-control" name="remarks_other" id="remarks_other" style="display: none;">
                                <script>
                                    function remarks_change(){
                                        if($('#remarks').val()=="Other"){
                                            $('#remarks_other').css('display','');
                                            $('#remarks_other').prop("required", true);
                                        } else{
                                            $('#remarks_other').css('display','none');
                                            $('#remarks_other').prop("required", false);
                                        }

                                    }
                                </script>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="" class="form-label">Head Count & Name</label>
                                <input type="text" class="form-control" name="head_count_name" id="head_count_name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Attachmant</label>
                                <input type="file" class="form-control" name="attachmant[]" id="attachmant" multiple>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- MODAL REIMBURSEMENT-->

    <a href="#" type="button" id="btn_edit" data-toggle="modal" data-target="#ModalServiceEdit"></a>
    <div class="modal fade" id="ModalServiceEdit" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Reimbursement</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="date" id="edit_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal ID</label>
                                <input type="text" class="form-control" name="deal_id" id="edit_deal_id" onchange="get_custName()" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="site_name" id="edit_site_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Scope of Work</label>
                                <input type="text" class="form-control" name="scope_of_work" id="edit_scope_of_work" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Invoice No</label>
                                <input type="text" class="form-control" name="invoice_no" id="edit_invoice_no" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Amount</label>
                                <input type="number" class="form-control" name="amount" step="Any" id="edit_amount" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Remarks (Expenses purpose)</label>
                                <select class="form-control" name="remarks" id="edit_remarks" onchange="edit_remarks_change()">
                                        <option value="Food Expenses" >Food Expenses</option>
                                        <option value="Travelling Expenses" >Travelling Expenses</option>
                                        <option value="Accessory" >Accessory</option>
                                        <option value="Other" >Other</option>
                                </select>
                                <input type="text" class="form-control" name="remarks_other" id="edit_remarks_other" style="display: none;">
                                <script>
                                    function edit_remarks_change(){
                                        if($('#edit_remarks').val()=="Other"){
                                            $('#edit_remarks_other').css('display','');
                                            $('#edit_remarks_other').prop("required", true);
                                        } else{
                                            $('#edit_remarks_other').css('display','none');
                                            $('#edit_remarks_other').prop("required", false);
                                        }

                                    }
                                </script>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="" class="form-label">Head Count & Name</label>
                                <input type="text" class="form-control" name="head_count_name" id="edit_head_count_name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Attachmant</label>
                                <input type="file" class="form-control" name="attachmant[]" id="attachmant" multiple>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="edit_id" name="edit_id" />
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <a data-toggle="modal" data-target="#exampleModalAccounts" id="btnAccounts"></a>
    <div class="modal fade" id="exampleModalAccounts" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Accounts Approval</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-account-approve', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <div class="modal-body">
                <label for="" class="form-label">Remarks</label>
                <input class="form-control" type="text" id="remarks" name="remarks">
            </div>
            <div class="modal-footer">
                <input type="hidden" id="account_re_id" name="account_re_id" />
                <button type="submit" value="2" name="btn_status" class="btn btn-danger">DisApprove</button>
                <button type="submit" value="1" name="btn_status" class="btn btn-success">Approve</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>

      <a data-toggle="modal" data-target="#exampleModalAccountsHead" id="btnAccountsHead"></a>
    <div class="modal fade" id="exampleModalAccountsHead" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Accounts Head Approval</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-accounts-head-approve', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <div class="modal-body">
                <label for="" class="form-label">Remarks</label>
                <input class="form-control" type="text" id="remarks" name="remarks" />
            </div>
            <div class="modal-footer">
                <input type="hidden" id="acco_head_re_id" name="acco_head_re_id" />
                <button type="submit" value="2" name="btn_status" class="btn btn-danger">DisApprove</button>
                <button type="submit" value="1" name="btn_status" class="btn btn-success">Approve</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>

      <a data-toggle="modal" data-target="#exampleModalDepartmentHead" id="btnDepartmentHead"></a>
    <div class="modal fade" id="exampleModalDepartmentHead" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Department Head Approval</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-reimbursement-request-dept-head-approve', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <div class="modal-body">
                <label for="" class="form-label">Remarks</label>
                <input class="form-control" type="text" id="remarks" name="remarks" />
            </div>
            <div class="modal-footer">
                <input type="hidden" id="dept_head_re_id" name="dept_head_re_id" />
                <button type="submit" value="2" name="btn_status" class="btn btn-danger">DisApprove</button>
                <button type="submit" value="1" name="btn_status" class="btn btn-success">Approve</button>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>


      <script>
        function fun_edit(id){
            $('#edit_id').val(id);
            $('#edit_date').val($('#edit_date_'+id).val());
            $('#edit_deal_id').val($('#edit_deal_id_'+id).val());
            $('#edit_site_name').val($('#edit_site_name_'+id).val());
            $('#edit_scope_of_work').val($('#edit_scope_of_work_'+id).val());
            $('#edit_invoice_no').val($('#edit_invoice_no_'+id).val());
            $('#edit_amount').val($('#edit_amount_'+id).val());
            if($('#edit_remarks_'+id).val() != "Food Expenses" && $('#edit_remarks_'+id).val() != "Travelling Expenses" && $('#edit_remarks_'+id).val() != "Accessory"){
                $('#edit_remarks_other').val($('#edit_remarks_'+id).val());
                $('#edit_remarks_other').css('display','');
                $('#edit_remarks_other').prop("required", true);
            }
            $('#edit_head_count_name').val($('#edit_head_count_name_'+id).val());                        
            $('#btn_edit').click();
        }
        function fun_delete(id){
            var result = confirm("Are you sure you want to delete this?");
            if (!result) {
                return false;
            }
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-reimbursement-request-delete') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    if(dataResult == "SUCCESS"){
                        alert('Deleted Successfully!');
                    }
                    else{
                        alert('Something went wrong, please try again!');
                    }
                    location.reload();
                    $("#loading_bg").css("display", "none");
                }
            });
        }
        function fun_restore(id){
            var result = confirm("Are you sure you want to restore this?");
            if (!result) {
                return false;
            }
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-reimbursement-request-restore') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    if(dataResult == "SUCCESS"){
                        alert('Restored Successfully!');
                    }
                    else{
                        alert('Something went wrong, please try again!');
                    }
                    location.reload();
                    $("#loading_bg").css("display", "none");
                }
            });
        }
      function get_custName()
      {
        $("#loading_bg").css("display", "block");
        var action = "{{ URL::to('crm-reimbursement-request-get-custname') }}";
        $.ajax({
            url: action,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}',
                deal_id: $('#deal_id').val(),
            },
            cache: false,
            success: function(dataResult) {
                var dataResult = JSON.parse(dataResult);
                var len = 0;
                var len = 0;
                    if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                    }
                    if(len > 0){
                        for(var i=0; i<len; i++){
                            $("#site_name").val(dataResult['data'][i].name);
                        }
                    }
                    else{
                        $("#site_name").val();
                    }
                    $("#loading_bg").css("display", "none");
            }
        });
    }
      </script>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection
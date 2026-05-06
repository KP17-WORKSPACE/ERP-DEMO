@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    @endphp

    @php
    if (isset($edit)){
        $title = "Update Lead";
    }
    else{
        $title = "Add Lead";
    }
    @endphp

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h1 class="h3 mb-0 text-gray-800 page-heading">{{ $title }}</h1>
            <span class="page-label">Home - {{ $title }}</span>
        </div>
        <div>
            <a href="{{ url('crm-leads') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Lead</a>
            <a href="{{ url('crm-leads/show') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> View Leads</a>
            <a href="{{ url()->previous() }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
    </div>

    <div class="card p-4 d-flex mb-3">
        <div class="row justify-content-center">
            <div class="col-md-10 p-4 border rounded">
                <h2 class="sub-head mb-4">{{ $title }}</h2>
                <hr>

    @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
        @if (session()->has('message-success'))
            <p class="text-success">
                {{ session()->get('message-success') }}
            </p>
        @elseif(session()->has('message-danger'))
            <p class="text-danger">
                {{ session()->get('message-danger') }}
            </p>
        @endif
    @endif


                @if (isset($edit))
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads/' . $edit->id, 'method' => 'PUT', 'id' => 'crm-leads-form']) }}
                @else
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-leads','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-leads-form']) }}
                @endif
                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Lead Refrence</label>
                                <input class="form-control" type="text" name="lead_name" autocomplete="off" id="lead_name" value="{{ isset($edit) ? (!empty(@$edit->lead_name) ? @$edit->lead_name : old('lead_name')) : old('lead_name') }}" required>
                                {{--  <select class="form-control js-example-basic-single" name="lead_name" id="lead_name">
                                    <option value="" >Select</option>
                                    @foreach ($product as $value)
                                    <option value="{{ @$value->part_number }}" {{ isset($edit) ? (!empty($edit->lead_name) ? (@$edit->lead_name == @$value->part_number ? 'selected' : '') : '') : '' }}>{{ @$value->part_number }}</option>
                                    @endforeach
                                </select>  --}}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Company</label>
                                @if(Auth::user()->role_id == 1 || session('logged_session_data.company_id') == 4 || session('logged_session_data.company_id') == 6 || session('logged_session_data.company_id') == 8 || session('logged_session_data.company_id') == 11 || session('logged_session_data.company_id') == 12)
                                <a style="float: right; cursor: pointer; display: none;" class="text-primary" data-toggle="modal" data-target="#addcompany"><i class="fa fa-user-plus" aria-hidden="true"></i> Add Company</a>
                                @endif
                                <select class="form-control js-example-basic-single" name="company_name" id="company_name" required>
                                    <option value="">-Select-</option>
                                    @foreach ($vendors as $value)
                                    <option value="{{ @$value->id }}" @if($edit->company_name == @$value->id) selected @endif>{{ @$value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Contact Person Name</label>
                                <input class="form-control" type="text" name="cust_name" autocomplete="off" id="cust_name" value="{{ isset($edit) ? (!empty(@$edit->cust_name) ? @$edit->cust_name : old('cust_name')) : old('cust_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Designation</label>
                                <input class="form-control" type="text" name="cust_designation" autocomplete="off" id="cust_designation" value="{{ isset($edit) ? (!empty(@$edit->cust_designation) ? @$edit->cust_designation : old('cust_designation')) : old('cust_designation') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Mobile</label>
                                <input class="form-control" type="text" name="cust_no" autocomplete="off" id="cust_no" value="{{ isset($edit) ? (!empty(@$edit->cust_no) ? @$edit->cust_no : old('cust_no')) : old('cust_no') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input class="form-control" type="text" name="cust_email" autocomplete="off" id="cust_email" value="{{ isset($edit) ? (!empty(@$edit->cust_email) ? @$edit->cust_email : old('cust_email')) : old('cust_email') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Address</label>
                                <input class="form-control" type="text" name="address" autocomplete="off" id="address" value="{{ isset($edit) ? (!empty(@$edit->address) ? @$edit->address : old('address')) : old('address') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Brand</label>
                                <select class="form-control js-example-basic-single" name="tags[]" id="tags" multiple>
                                    @foreach ($brand as $value)
                                    <option value="{{ @$value->title }}"
                                        @if(isset($edit))
                                            @if(!empty($edit->tags))
                                                @if(str_contains($edit->tags, $value->title)) selected @endif
                                            @endif
                                        @endif >{{ @$value->title }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Sales Person</label>
                                <select class="form-control js-example-basic-single" name="owner" id="owner" required>
                                    <option value="">-Select-</option>
                                    @foreach ($staff as $value)
                                        <option value="{{ @$value->user_id }}" @if (isset($edit)) @if ($edit->owner == $value->user_id) selected @endif @endif >{{ @$value->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Source</label>
                                <select class="form-control" name="source" id="source">
                                    <option value="">-Select-</option>
                                    <option value="Chat" @if(@$edit->source == "Chat") selected @endif >Chat</option>
                                    <option value="Call" @if(@$edit->source == "Call") selected @endif >Call</option>
                                    <option value="Mail" @if(@$edit->source == "Mail") selected @endif >Mail</option>
                                    <option value="Website" @if(@$edit->source == "Website") selected @endif >Website</option>
                                    <option value="Gitex 2023" @if(@$edit->source == "Gitex 2023") selected @endif >Gitex 2023</option>
                                    <option value="Gitex" @if(@$edit->source == "Gitex") selected @endif >Gitex</option>
                                    <option value="Ecommerce" @if(@$edit->source == "Ecommerce") selected @endif >Ecommerce</option>
                                    <option value="Other" @if(@$edit->source == "Other") selected @endif >Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3" id="sourcediv" style="display: none;">
                            <div class="form-group">
                                <label for="">Other Source</label>
                                <input class="form-control" type="text" name="source_o" autocomplete="off" id="source_o" value="{{ isset($edit) ? (!empty(@$edit->source_o) ? @$edit->source_o : old('source_o')) : old('source_o') }}" style="display: none;" placeholder="Source">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Created By</label>
                                <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit) ? (!empty(@$edit->createdby) ? @$edit->createdby->full_name : old('createdby')) : Auth::user()->full_name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Date</label>
                                @php
                                $value = date('Y-m-d');
                                if(isset($edit) && !empty($edit->date) ){
                                    $value = date('Y-m-d', strtotime(@$edit->date)); }                                        
                                @endphp
                                <input class="form-control" id="date" type="date" autocomplete="off" name="date" value="{{ @$value }}" data-date-format="mm/dd/yyyy" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Lead Type</label>
                                <select class="form-control" name="isproject" id="isproject">
                                    <option value="4" @if(@$edit->isproject == "4") selected @endif >Project</option>
                                    <option value="1" @if(@$edit->isproject == "1") selected @endif >Reseller</option>
                                    <option value="2" @if(@$edit->isproject == "2") selected @endif >Enduser</option>
                                    <option value="3" @if(@$edit->isproject == "3") selected @endif >E-Commerce</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group files">
                                <label for="">Attach</label>
                                <input type="file" class="form-control" name="doc[]" id="doc" multiple="multiple">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Notes</label>
                                <textarea class="form-control" name="note" rows="4" autocomplete="off" id="note">@if(isset($edit)) {{$edit->note}} @endif</textarea>
                            </div>
                        </div>
                        @if (session('logged_session_data.company_id')==1)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Company</label>
                                <select class="form-control" name="company" id="company" required>
                                    <option value="">Select</option>
                                    @foreach ($company as $value)
                                    <option value="{{ @$value->id }}" @if(isset($edit)) @if($edit->company_id==$value->id) selected @endif @endif>{{ @$value->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @else
                        <input type="hidden" name="company" id="company" value="{{ $edit->company_id }}" />
                        @endif
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="1" @if(@$edit->status == 1) selected @endif >New</option>
                                    <option value="2" @if(@$edit->status == 2) selected @endif >Qualified</option>
                                    <option value="3" @if(@$edit->status == 3) selected @endif >Unqualified</option>
                                    <option value="4" @if(@$edit->status == 4) selected @endif >Pending Response</option>
                                    <option value="10" @if(@$edit->status == 10) selected @endif >Closed</option>
                                </select>
                                <textarea class="form-control" name="lost_comments" rows="4" style="display: none;" autocomplete="off" id="lost_comments" placeholder="Reason"></textarea>
                                <script>
                                    $('#status').on('change', function(e) {
                                        if ($('#status').val() == 3) {
                                            $('#lost_comments').css("display", "block");
                                            $('#lost_comments').prop('required', true);
                                        } else {
                                            $('#lost_comments').css("display", "none");
                                            $('#lost_comments').prop('required', false);
                                        }
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">                        
                        <button type="submit" class="btn btn-primary" id="btnSubmit"><span class="ti-check"></span>
                            @if (isset($edit)) @lang('Update & View')
                            @else @lang('Save & View')
                            @endif @lang('Lead')
                        </button>
                        &nbsp;&nbsp;
                        <a href="{{ url('crm-leads/show') }}" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i> Close</a>                        
                    </div>
                    {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        if($("#source").val() == "Other"){$("#source_o").css("display", "block"); $("#source_o").prop('required',true); $("#sourcediv").css("display", "block");}
        else{$("#source_o").css("display", "none"); $("#source_o").prop('required',false); $("#sourcediv").css("display", "none");}
    });
    
    $(document).on("change", "#source", function () {
    if($("#source").val() == "Other"){$("#source_o").css("display", "block"); $("#source_o").prop('required',true); $("#sourcediv").css("display", "block");}
    else{$("#source_o").css("display", "none"); $("#source_o").prop('required',false); $("#sourcediv").css("display", "none");}
    });
</script>



{{--  <section class="sms-breadcrumb mb-20 white-box">
    <div class="container-fluid">
        <div class="row" style="float: left;">
            <h1>@lang('Lead')</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ url('crm-dashboard') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> CRM Dashboard</a>
            <a href="{{ url('crm-leads') }}" class="top-btn-r"><i class="far fa fa-plus" aria-hidden="true"></i> New</a>
            <a href="{{ url('crm-leads/show') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View</a>
            <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh" aria-hidden="true"></i> Refresh</a>
        </div>
    </div>
</section>  --}}

<div class="modal fade" id="addcompany" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Company</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Company Name</label>
                                <input class="form-control" type="text" aria-describedby="" autocomplete="off" id="company_name_add" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Contact Name</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_name_add">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Mobile</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_no_add">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Email</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_email_add" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Address</label>
                                <input class="form-control" type="text" autocomplete="off" id="cust_address_add">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="btn_close2" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-secondary" id="btn_add_company" type="button" >Save & Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>

        $(window).ready(function() {
                $("#item-store-form").on("keypress", function (event) {           
                    var keyPressed = event.keyCode || event.which;
                    if (keyPressed === 13) {
                        event.preventDefault();
                        return false;
                    }
                });
        });

                
        $(document).on("click", "#btn_add_company", function () {
            
            $("#btn_add_company").css("display", "none");
        
            var company_name_add = $("#company_name_add").val();
            var cust_name_add = $("#cust_name_add").val();
            var cust_no_add = $("#cust_no_add").val();
            var cust_email_add = $("#cust_email_add").val();
            var cust_address_add = $("#cust_address_add").val();
            var country_add = $("#country_add").val();
        
            if(company_name_add==""){$("#company_name_add").focus(); $("#btn_add_company").css("display", "block"); return false;}
            if(cust_name_add==""){$("#cust_name_add").focus(); $("#btn_add_company").css("display", "block"); return false;}
            if(cust_email_add==""){$("#cust_email_add").focus(); $("#btn_add_company").css("display", "block"); return false;}
            
            var action = "{{ URL::to('crm-leads-addcustomername') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    company_name_add: company_name_add,
                    cust_name_add: cust_name_add,
                    cust_no_add: cust_no_add,
                    cust_email_add: cust_email_add,
                    cust_address_add: cust_address_add,
                    vat_country: country_add,
                },
                cache: false,
                success: function(dataResult) {
                    //alert(dataResult);
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if(dataResult['data']=="ERROR")
                    {
                        alert("Error found in something!!");
                        $("#btn_add_company").css("display", "block");
                    }
                    else if(dataResult['data']=="ERROR2")
                    {
                        alert("Company Name already exists!!");
                        $('#company_name_add').css("border", "1px solid red"); $('#company_name_add').focus();
                        $("#btn_add_company").css("display", "block");
                    }
                    else{
                        if(dataResult['data'] != null){
                        len = dataResult['data'].length;
                        }
                        if(len > 0){
                            
                            $('#company_name').find('option').not(':first').remove();
                            for(var i=0; i<len; i++){
                                var id = dataResult['data'][i].id;
                                var name = dataResult['data'][i].name;
                                var name2 = dataResult['data'][i].code;
                                var option = "<option value='"+id+"'>"+name+"</option>";
                                $("#company_name").append(option);
                            }
                            alert('Company Name Added Successfully!!');
                            $('#btn_close2').click();
                            $("#btn_add_company").css("display", "block");
                            location.reload();
                        }
                    }
                  }
            });
        });
        
        $(document).on("change", "#company_name", function () {
            var id = $("#company_name").val();
            get_cust_name(id);
            get_sales_person(id);
        });
        
        function get_cust_name(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-leads-customername') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
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
                                var name = dataResult['data'][i].customer_salutation +' '+ dataResult['data'][i].first_name +' '+ dataResult['data'][i].last_name;
                                $("#cust_name").val(name);
                                $("#cust_no").val(dataResult['data'][i].mobile);
                                $("#cust_email").val(dataResult['data'][i].email);
                                $("#address").val(dataResult['data'][i].address);
                                $("#cust_designation").val(dataResult['data'][i].designation);
                            }                        
                        }
                        else{
                            $("#cust_name").val();
                            $("#cust_no").val();
                            $("#cust_email").val();
                            $("#address").val();
                            $("#cust_designation").val();
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }

        function get_sales_person(id) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('get-salesperson-list') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
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
                            $('#owner').find('option').not(':first').remove();
                            for(var i=0; i<len; i++){
                                var id = dataResult['data'][i].id;
                                var name = dataResult['data'][i].full_name;
                                var option = "<option value='"+id+"'>"+name+"</option>";
                                $("#owner").append(option);
                            }
                        }
                        else{
                            $('#owner').find('option').not(':first').remove();
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }

        $(document).on("change", "#lead_name", function () {
            $("#loading_bg").css("display", "block");
            var id = $("#lead_name").val();
            var action = "{{ URL::to('get-lead-name-to-brand') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
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
                                var title = dataResult['data'][i].title;
                                $("#tags").val(title);
                                $('#select2-tags-container').html("&nbsp;&nbsp;" + title);
                                
                            }
                        }
                        else{

                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        });

            </script>
            <style>
                .files input {
                    outline: 2px dashed #92b0b3;
                    outline-offset: -10px;
                    -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
                    transition: outline-offset .15s ease-in-out, background-color .15s linear;
                    padding: 30px 0px 55px 35%;
                    text-align: center !important;
                    margin: 0;
                    width: 100% !important;
                }
                .files input:focus{     outline: 2px dashed #92b0b3;  outline-offset: -10px;
                    -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
                    transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
                 }
                .files{ position:relative}
                .files:after {  pointer-events: none;
                    position: absolute;
                    top: 60px;
                    left: 0;
                    width: 50px;
                    right: 0;
                    height: 25px;
                    content: "";
                    /*background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);*/
                    display: block;
                    margin: 0 auto;
                    background-size: 100%;
                    background-repeat: no-repeat;
                }
                .color input{ background-color:#f1f1f1;}
                .files:before {
                    position: absolute;
                    bottom: 10px;
                    left: 0;  pointer-events: none;
                    width: 100%;
                    right: 0;
                    height: 25px;
                    content: " or drag it here. ";
                    display: block;
                    margin: 0 auto;
                    color: #2ea591;
                    font-weight: 600;
                    text-transform: capitalize;
                    text-align: center;
                }
            </style>
@endsection

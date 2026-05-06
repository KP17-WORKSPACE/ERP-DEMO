@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    @endphp

    @php
    if (isset($edit)){
        $title = "Update AMC";
    }
    else{
        $title = "Add AMC";
    }
    @endphp

<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h1 class="h3 mb-0 text-gray-800 page-heading">{{ $title }}</h1>
            <span class="page-label">Home - {{ $title }}</span>
        </div>
        <div>
            <a href="{{ url('crm-amc-deal-list') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> View AMC</a>
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
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc/'.$edit->id.'/update', 'method' => 'post', 'id' => 'crm-amc-add']) }}
                @else
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-add','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-add']) }}
                @endif
                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                <input type="hidden" name="id" value="{{ isset($edit) ? $edit->id : '' }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Start Date</label>
                                @php
                                $value = date('Y-m-d');
                                if(isset($edit) && !empty($edit->from_date) ){
                                    $value = date('Y-m-d', strtotime(@$edit->from_date)); }                                        
                                @endphp
                                <input class="form-control" id="from_date" type="date" autocomplete="off" name="from_date" value="{{ @$value }}" data-date-format="mm/dd/yyyy" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">End Date</label>
                                @php
                                $value = date('Y-m-d');
                                if(isset($edit) && !empty($edit->to_date) ){
                                    $value = date('Y-m-d', strtotime(@$edit->to_date)); }                                        
                                @endphp
                                <input class="form-control" id="to_date" type="date" autocomplete="off" name="to_date" value="{{ @$value }}" data-date-format="mm/dd/yyyy" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Deal ID</label>
                                <input class="form-control" type="text" name="deal_id" autocomplete="off" id="deal_id" value="{{ isset($edit) ? (!empty(@$edit->deal_id) ? @$edit->deal_id : old('deal_id')) : old('deal_id') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Created By</label>
                                <input class="form-control" type="text" name="createdby" autocomplete="off" id="createdby" value="{{ isset($edit) ? (!empty(@$edit->createdby) ? @$edit->createdby->full_name : old('createdby')) : Auth::user()->full_name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Company</label>
                                            <select class="form-control js-example-basic-single" name="company_name" id="company_name" required>
                                                <option value="">-Select-</option>
                                                @foreach ($vendors as $value)
                                                <option value="{{ @$value->id }}" {{ isset($edit) ? (!empty($edit->cust_id) ? (@$edit->cust_id == @$value->id ? 'selected' : '') : '') : '' }}>{{ @$value->name }}
                                                </option>
                                                @endforeach
                                            </select>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Contact Name</label>
                                <input class="form-control" type="text" name="cust_name" autocomplete="off" id="cust_name" value="{{ isset($edit) ? (!empty(@$edit->cust_name) ? @$edit->cust_name : old('cust_name')) : old('cust_name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Mobile</label>
                                <input class="form-control" type="text" name="cust_no" autocomplete="off" id="cust_no" value="{{ isset($edit) ? (!empty(@$edit->cust_no) ? @$edit->cust_no : old('cust_no')) : old('cust_no') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Email</label>
                                <input class="form-control" type="text" name="cust_email" autocomplete="off" id="cust_email" value="{{ isset($edit) ? (!empty(@$edit->cust_email) ? @$edit->cust_email : old('cust_email')) : old('cust_email') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Contact Address</label>
                                <input class="form-control" type="text" name="address" autocomplete="off" id="address" value="{{ isset($edit) ? (!empty(@$edit->address) ? @$edit->address : old('address')) : old('address') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Country</label>
                                <select class="form-control" name="country" id="country">
                                    <option value="">-Select-</option>
                                    @foreach ($country as $value)
                                    <option value="{{ @$value->name }}"
                                        @if(isset($edit))
                                                @if($edit->country == $value->name) selected @endif
                                        @endif >{{ @$value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="">Tags</label>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Owner</label>
                                <select class="form-control js-example-basic-single" name="owner" id="owner" required>
                                    <option value="">-Select-</option>
                                    @foreach ($staff as $value)
                                    <option value="{{ @$value->user_id }}"
                                        @if(isset($edit)) @if($edit->owner == $value->user_id) selected @endif @else @if($value->user_id == Auth::user()->id) selected  @endif @endif
                                        >{{ @$value->full_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="">Notes</label>
                                <textarea class="form-control" name="remarks" rows="4" autocomplete="off" id="remarks">@if(isset($edit)) {{$edit->remarks}} @endif</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group files">
                                <label for="">Attach</label>
                                <input type="file" class="form-control" name="file[]" id="file" multiple="multiple">
                            </div>
                        </div>                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Value</label>
                                <input class="form-control" type="number" step="any" name="amc_value" autocomplete="off" id="amc_value" value="{{ isset($edit) ? (!empty(@$edit->amc_value) ? @ App\SysHelper::currancy_format_deal_no($edit->amc_value,$edit->company_id) : old('amc_value')) : old('amc_value') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select class="form-control" name="status" id="status" required>
                                    <option value="1" @if(@$edit->status == 1) selected @endif >Active</option>
                                    <option value="2" @if(@$edit->status == 2) selected @endif >Hold</option>
                                    <option value="3" @if(@$edit->status == 3) selected @endif >Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 p-2"><br />
                            <button type="submit" class="btn btn-primary" id="btnSubmit"><span class="ti-check"></span>
                                @if (isset($edit)) @lang('Update & View')
                                @else @lang('Save & View')
                                @endif @lang('AMC')
                            </button>
                            &nbsp;&nbsp;
                            <a href="{{ url('crm-amc-deal-list') }}" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i> Close</a>   
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">                        
                                             
                    </div>
                    {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

    <script>

        
        
        
        
        $(document).on("change", "#company_name", function () {
            var id = $("#company_name").val();
            get_cust_name(id);
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
                                $("#cust_name").val(dataResult['data'][i].contcat_person);
                                $("#cust_no").val(dataResult['data'][i].mobile);
                                $("#cust_email").val(dataResult['data'][i].email);
                                $("#address").val(dataResult['data'][i].address);
                                $("#country").val(dataResult['data'][i].name);                       
                            }                        
                        }
                        else{
                            $("#cust_name").val("");
                            $("#cust_no").val("");
                            $("#cust_email").val("");
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }
        
        
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

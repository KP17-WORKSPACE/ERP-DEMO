<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('public/admin-iroid/') }}/img/erp-logo-icon.png" type="image/png"/>
    <title>Venus ERP</title>

    <link href="{{ asset('public/admin-iroid/') }}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('public/admin-iroid/') }}/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('public/admin-iroid/') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="{{ asset('public/admin-iroid/') }}/vendor/jquery/jquery.min.js"></script>
    
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/toastr.min.css"/>

</head>

    <!-- script -->
    <script src="{{ asset('public/admin-iroid/') }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/js/sb-admin-2.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/chart.js/Chart.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/js/demo/chart-area-demo.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/js/demo/chart-pie-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/js/demo/datatables-demo.js"></script>

    
<script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/toastr.min.js"></script>


<script src="{{asset('public/backEnd/')}}/js/custom.js"></script>
<script src="{{asset('public/backEnd/')}}/js/developer.js"></script>
<script src="{{asset('public/backEnd/')}}/js/erpjs.js"></script>


    <script>
        $(document).ready(function () {
            $('.js-example-basic-single').select2();
        });
    </script>


    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2"></div>
            <div class="col-lg-8"><br /><br />
<h2>AMC Service Request Form</h2>
          <div class="card shadow mb-4">

            <div class="card-body">

                <div class="table-responsive">
                    


		 {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-service-request-customer-add','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-track-submit123']) }}

         <div class="modal-body">
                        
                
            <div class="row">
                <div class="col-lg-4 mb-3">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Customer Name')<span></span></label>
                                <input class="form-control"  type="text" required name="customer_name" id="customer_name" value="{{ $custdata->name }}">
                                <input class="form-control"  type="hidden" required name="customer_id" id="customer_id" value="{{ $cusid }}">
                                <input class="form-control"  type="hidden" required name="company_id" id="company_id" value="{{ $comid }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Contact Person')<span></span></label>                                
                                <input class="form-control"  type="text" required name="contact_person" id="contact_person" value="{{ $custdata->first_name }} {{ $custdata->last_name }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Mobile No')<span></span></label>
                                <input class="form-control"  type="text" required name="mobile_no" id="mobile_no" value="{{ $custdata->mobile }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Location of Work')<span></span></label>

                                <textarea class="form-control" rows="4" id="location_of_work" required name="location_of_work">{{ $custdata->address }}, {{ $custdata->address2 }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 mb-3">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Scope of Work')<span></span></label>
                                <a onclick="add_scope_of_work()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
                                
                                <table width="100%">
                                    <tr><td width="1%" style="font-size: 13px;">1. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_1" required></td></tr>
                                    @for ($i=2; $i<=20; $i++)
                                    <tr id="row_{{ $i }}" style="display:none; font-size: 13px;"><td>{{ $i }}. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_{{ $i }}"></td></tr>
                                    @endfor
                                </table>
                                <input type="hidden" id="scope_of_work_row_id" value="1" />
                                <script>
                                    function add_scope_of_work(){
                                        var scope = $('#scope_of_work_row_id').val();
                                        if($('#scope_of_work_'+scope).val() != ""){
                                            scope++;
                                            $('#row_'+scope).css('display','');
                                            $('#scope_of_work_row_id').val(scope);
                                            $('#scope_of_work_'+scope).prop("required", true);
                                        }
                                    }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Suggested Date')<span></span></label>

                                <input class="form-control" id="suggested_date" type="date" required name="suggested_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Suggested Time')<span></span></label>

                                <input class="form-control" id="suggested_time" type="time" required name="suggested_time">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="no-gutters input-right-icon">
                        <div class="col">
                            <div class="input-effect">
                                <label class="txtlbl">@lang('Attachment')<span></span></label>

                                <input class="form-control" id="attachment" type="file" required name="attachment">
                            </div>
                        </div>
                    </div>
                </div>
            


            </div>

           
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" value="approve" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>@lang('Submit Request')</button>
            </div>
            {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-2"></div>
    </div>
</div>



<!-- Modal Deal Track-->







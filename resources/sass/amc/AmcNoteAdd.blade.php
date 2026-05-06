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
<h2>Customer Form</h2>
          <div class="card shadow mb-4">

            <div class="card-body">

                <div class="table-responsive">
                    


		 {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-amc-submit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-amc-track-submit123']) }}

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Deal Id')<span></span></label>
                                     <input type="hidden" name="id" id="id" value="<?php echo $id ?>"> 
                                     <input class="form-control"  type="text" required name="deal_id" id="deal_id">
                                </div>
                            </div>
                        </div>
                    </div>
 <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Mobile')<span></span></label>
                                    
                                     <input class="form-control"  type="text" required name="mobile" id="mob">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Date')<span></span></label>

                                    <input class="form-control" id="date" type="date" autocomplete="off" required name="date" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    <!-- <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Mobile No')<span></span></label>

                                    <input class="form-control" id="contact_person" type="text" required name="mobile" value="">
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Invoicing')<span></span></label>
                                    <select class="form-control" name="invoice" id="invoice" >
                                       <option value="Yearly">Yearly</option> 
                                      
                                       <option value="Quaterly">Quaterly</option> 
                                    </select>
                                    <!-- <input class="form-control" id="invoice" type="text" required name="invoice" value=""> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Description')<span></span></label>

                                    <input class="form-control" id="description" type="text" required name="description" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Start Date')<span></span></label>

                                     <input class="form-control" id="start_date11" type="date" required name="start_date">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('End Date')<span></span></label>

                                     <input class="form-control" id="end_date11" type="date" required name="end_date">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Amount')<span></span></label>

                                     <input class="form-control" id="amount" type="text" required name="amount">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Customer Name')<span></span></label>

                                     <input class="form-control" id="sales_person1" type="text" required name="cust_name">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Sales Person')<span></span></label>

                                     <input class="form-control" id="sales_person" type="text" required name="sales_person">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Contact  Person')<span></span></label>

                                     <input class="form-control" id="contact_person2" type="text" required name="contact_person">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Scope of Work')<span></span></label>

                                    <input class="form-control" id="contact_person" type="text" required name="scope_work" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Source')<span></span></label>

                                    <input class="form-control" id="contact_person" type="text" required name="source" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-3">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Engineer')<span></span></label>

				<select    name="engineer" class="form-control js-example-basic-single" >

				 @php $englist=@App\SysHelper::get_engineer_list();
				foreach($englist as $list)
				
					echo '<option value="'.$list->user_id.'" >'.$list->full_name.'</option>';

				 @endphp

                                 </select>  
                                </div>
                            </div>
                        </div>
                    </div> -->

                </div>

            </div>
           

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <!-- <input type="hidden" id="deal_id" name="deal_id" value="" /> -->
                <!-- <button type="submit" class="btn btn-info" value="save" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>@lang('Save')</button> -->
                <button type="submit" class="btn btn-primary" value="approve" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>@lang('Add AMC')</button>
            </div>
            {{ Form::close() }}
                </div>
            </div>
        </div>
</div>



<!-- Modal Deal Track-->







@extends('backEnd.masterpage')

@section('mainContent')
    @php

        $module_links = [];

        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    @endphp


    <?php try{ ?>

    <div class="container-fluid">

        <div class="d-sm-flex justify-content-between">

            <div class="mb-3">

                <h2 class="page-heading m-0">Supplier Approve</h2>

                <span class="page-label">Home - Supplier Approve</span>

            </div>

            <div>

                <a href="{{ url('suppliers') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Customer
                    List</a>

            </div>

        </div>

        <div class="card p-4 d-flex mb-3">

            <div class="row justify-content-center">

                <div class="col-md-12 p-4 border rounded">

                    <h2 class="sub-head mb-4">Approve Supplier</h2>

                    <hr>
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplier-form-approve', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                    <input type="hidden" name="row_id" id="row_id" value="{{ $row_id }}">
                    <?php /* <input type="hidden" name="customer_code" value="{{ 'CUS' . sprintf('%03d', @App\SysHelper::get_new_maxid('sys_cust_suppl', 'id')) }}"> */ ?>

                    <div class="row mt-2">
                        <div class="col-md-2">
                            <label for="" class="form-check-label">Primary Contact</label></div>                            
                        <div class="col-md-1">
                            <select class="form-control js-example-basic-single" name="customer_salutation" required>
                                <option value="">--</option>
                                <option value="Mr" @if ($editData->customer_salutation == "Mr") selected @endif >Mr</option>
                                <option value="Mrs" @if ($editData->customer_salutation == "Mrs") selected @endif >Mrs</option>
                                <option value="Miss" @if ($editData->customer_salutation == "Miss") selected @endif >Miss</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="text" name="first_name" placeholder="First Name"
                                value="{{ isset($editData) ? @$editData->first_name : '' }}" required>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="text" name="last_name" placeholder="Last Name"
                                value="{{ isset($editData) ? @$editData->last_name : '' }}">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <label for="" class="form-check-label">Company Name</label></div>
                            <div class="col-md-3">
                                <input class="form-control" type="text" name="customer_name" id="customer_name" placeholder="Company Name" value="{{ isset($editData) ? @$editData->name : '' }}" required>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control js-example-basic-single" name="designation" required>
                                <option value="">--Designation--</option>
                                @if (count($designation)>0)
                                    @foreach ($designation as $val)
                                        <option value="{{ $val->title }}" @if($editData->designation==$val->title) selected @endif>{{ $val->title }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <script>
                        $('#customer_name').on('input', function() {
                            var txt = $('#customer_name').val();
                            $('#customer_name_display').val(txt.toUpperCase());
                            var txt2 = capitalizeFirstLetter(txt);
                            $('#customer_name').val(txt2);
                        });
                        function capitalizeFirstLetter(string) {
                            return string.charAt(0).toUpperCase() + string.slice(1);
                        }                        
                    </script>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <label for="" class="form-check-label"><br />Supplier Display Name</label></div>
                        <div class="col-md-3">&nbsp;
                                <input class="form-control" type="text" name="customer_name_display" id="customer_name_display" placeholder="Customer Display Name" value="{{ isset($editData) ? @$editData->customer_name_display : '' }}" required>
                        </div>
                        <div class="col-md-2">Company
                            <select class="form-control js-example-basic-single" name="company_access[]" id="company_access" multiple required>
                                @foreach ($company as $value)
                                    <option value="{{ @$value->id }}" @if (session('logged_session_data.company_id') == @$value->id) selected @endif>{{ @$value->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <label for="" class="form-check-label">Supplier Email</label></div>
                        <div class="col-md-3">
                                <input class="form-control" type="text" name="email" placeholder="Email" value="{{ $editData->email }}" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <label for="" class="form-check-label">Supplier Phone</label></div>
                            <div class="col-md-3">
                                <input class="form-control" type="text" name="mobile_code" placeholder="Work Phone" value="{{ $editData->contcat_number }}" required>
                        </div>
                        <div class="col-md-2">
                                <input class="form-control" type="text" name="mobile" placeholder="Mobile" value="{{ $editData->mobile }}">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">

{{--  tabs  --}}
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#address-panel" role="tab" aria-controls="address" aria-selected="true">Address</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#contactperson-panel" role="tab" aria-controls="contactperson" aria-selected="true">Contact Person</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#vat-panel" role="tab" aria-controls="vat" aria-selected="false">VAT</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#payment-panel" role="tab" aria-controls="payment" aria-selected="false">Payment</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#documents-panel" role="tab" aria-controls="documents" aria-selected="false">Documents</a></li>
  </ul>

  
  <div class="tab-content">
    {{--  Address  --}}
    <div class="tab-pane active pt-2" id="address-panel" role="tabpanel" aria-labelledby="address-tab">

        <a class="float-right" style="cursor: pointer;" data-toggle="modal" data-target="#ModalAddress"><i class="fa fa-address-book" aria-hidden="true"></i> Add More</a>
        
        <div class="row mt-4">
            @if (count($editAddressbook)>0)
            @foreach ($editAddressbook as $itm)
                
            <div class="col-md-3"><p style="border: solid 1px #dbdbdb; padding: 10px; border-radius: 5px;"><a class="text-danger float-right" href="{{url('delete-cust-suppl-address/'.$itm->id)}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
                Country : {{ $itm->countryname->name }}<br />Address : {{ $itm->address }}<br />Address2 : {{ $itm->address2 }}<br />City : {{ $itm->city }}<br />State : {{ $itm->statename->name }}<br />PO Box : {{ $itm->zip_code }}</p></div>

            @endforeach                
            @endif
        </div>
    </div>
    {{--  Address  --}}
    <div class="tab-pane pt-2" id="contactperson-panel" role="tabpanel" aria-labelledby="contactperson-tab">        
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-striped" id="pi-ret-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>@lang('Salutation')</th>
                            <th>@lang('First Name')</th>
                            <th>@lang('Last Name')</th>
                            <th>@lang('Email Address')</th>
                            <th>@lang('Work Phone')</th>
                            <th>@lang('Mobile')</th>
                            <th>@lang('Designation')</th>
                            <th>@lang('Department')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1;?>
                        @foreach ($editContact as $edt)
                        <tr id="pr_row_{{ $i }}">
                            <td><select class="form-control js-example-basic-single" name="e_salutation[]" id="e_salutation_{{ $i }}">
                                <option value="">-Salutation-</option>
                                <option value="Mr" @if($edt->salutation == "Mr") selected @endif>Mr</option>
                                <option value="Mrs" @if($edt->salutation == "Mrs") selected @endif>Mrs</option>
                                <option value="Miss" @if($edt->salutation == "Miss") selected @endif>Miss</option>
                            </select></td>
                            <td><input type="text" class="form-control" name="e_first_name[]" id="e_first_name_{{ $i }}" value="{{ $edt->first_name }}" /></td>
                            <td><input type="text" class="form-control" name="e_last_name[]" id="e_last_name_{{ $i }}" value="{{ $edt->last_name }}" /></td>
                            <td><input type="text" class="form-control" name="e_email_address[]" id="e_email_address_{{ $i }}" value="{{ $edt->email_address }}" /></td>
                            <td><input type="text" class="form-control" name="e_work_phone[]" id="e_work_phone_{{ $i }}" value="{{ $edt->work_phone }}" /></td>
                            <td><input type="text" class="form-control" name="e_mobile[]" id="e_mobile_{{ $i }}" value="{{ $edt->mobile }}" /></td>
                            <td>
                                <select class="form-control js-example-basic-single" name="e_designation[]" id="e_designation_{{ $i }}">
                                    <option value="">--Designation--</option>
                                    @if (count($designation)>0)
                                        @foreach ($designation as $val)
                                            <option value="{{ $val->title }}" @if($edt->designation==$val->title) selected @endif>{{ $val->title }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </td>
                            <td>
                                <select class="form-control js-example-basic-single" name="e_department[]" id="e_department_{{ $i }}">
                                    <option value="">--Designation--</option>
                                    @if (count($department)>0)
                                        @foreach ($department as $val)
                                            <option value="{{ $val->name }}" @if($edt->department==$val->name) selected @endif>{{ $val->name }}</option>
                                        @endforeach
                                    @endif
                                </select></td>
                        </tr>
                        <?php $i++;?>
                        @endforeach
                        @for ($r=$i; $r <= 5; $r++)
                        <tr id="pr_row_{{ $i }}">
                            <td><select class="form-control js-example-basic-single" name="e_salutation[]" id="e_salutation_{{ $i }}">
                                <option value="">-Salutation-</option>
                                <option value="Mr">Mr</option>
                                <option value="Mrs">Mrs</option>
                                <option value="Miss">Miss</option>
                            </select></td>
                            <td><input type="text" class="form-control" name="e_first_name[]" id="e_first_name_{{ $i }}" value="" /></td>
                            <td><input type="text" class="form-control" name="e_last_name[]" id="e_last_name_{{ $i }}" value="" /></td>
                            <td><input type="text" class="form-control" name="e_email_address[]" id="e_email_address_{{ $i }}" value="" /></td>
                            <td><input type="text" class="form-control" name="e_work_phone[]" id="e_work_phone_{{ $i }}" value="" /></td>
                            <td><input type="text" class="form-control" name="e_mobile[]" id="e_mobile_{{ $i }}" value="" /></td>
                            <td>
                                <select class="form-control js-example-basic-single" name="e_designation[]" id="e_designation_{{ $i }}">
                                <option value="">--Designation--</option>
                                @if (count($designation)>0)
                                    @foreach ($designation as $val)
                                        <option value="{{ $val->title }}">{{ $val->title }}</option>
                                    @endforeach
                                @endif
                            </select>
                            </td>
                            <td>
                                <select class="form-control js-example-basic-single" name="e_department[]" id="e_department_{{ $i }}">
                                <option value="">--Designation--</option>
                                @if (count($department)>0)
                                    @foreach ($department as $val)
                                        <option value="{{ $val->name }}">{{ $val->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            </td>
                        </tr>
                        @endfor
                        <input type="hidden" value="{{ $i-- }}" id="pr_row_count" />
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
    <div class="tab-pane pt-2" id="vat-panel" role="tabpanel" aria-labelledby="vat-tab">
        <div class="row">
            <div class="col-md-6">
                <div class="row mt-2">
                    <div class="col-md-3">VAT Country</div>
                    <div class="col-md-8"><select class="form-control js-example-basic-single" name="country_vat" id="country_vat" required>
                        <option data-display="" value=""></option>
                        @foreach ($vat as $key => $value)
                                <option value="{{ @$value->vat_country }}" @if($editData->vat_country == $value->vat_country) selected @endif>{{ @$value->name }} </option>
                        @endforeach
                    </select></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">VAT %</div>
                    <div class="col-md-2"><input class="form-control" type="number"  name="vat_percentage" id="vat_percentage" value="{{ $editData->vat_percentage }}" readonly required></div>
                    <div class="col-md-4 mt-2"><input type="checkbox"  name="vat_percentage_fixed" id="vat_percentage_fixed" value="1" @if($editData->vat_is_fixed == 1) checked @endif> Fixed Rate</div>
                    <script>
                        $( "#vat_percentage_fixed" ).click(function() {
                            if(this.checked){
                                $('#vat_percentage').attr('readonly', false);
                            }
                            if(!this.checked){
                                $('#vat_percentage').attr('readonly', true);
                            }
                        });      
                    </script>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Supplier Type</div>
                    <div class="col-md-8"><select class="form-control" name="supplier_type" id="supplier_type" required>
                        <option data-display="" value=""></option>
                        @foreach ($supplier_type as $key => $value)
                            <option value="{{ @$value->id }}" @if($value->id == $editData->supplier_type) selected @endif>{{ @$value->title }} </option>
                        @endforeach
                    </select></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Purchase Type</div>
                    <div class="col-md-8"><select class="form-control" name="purchase_type" id="purchase_type" required>
                        <option data-display="" value=""></option>
                        @foreach ($purchase_type as $key => $value)
                            <option value="{{ @$value->id }}" @if($value->id == $editData->purchase_type) selected @endif>{{ @$value->title }} </option>
                        @endforeach
                    </select></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">VAT Number</div>
                    <div class="col-md-8"><input class="form-control" type="text"  name="vat_number" value="{{ $editData->vat_number }}"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane pt-2" id="payment-panel" role="tabpanel" aria-labelledby="payment-tab">
        <div class="row">
            <div class="col-md-6">
                <div class="row mt-2">
                    <div class="col-md-3">Transaction Type</div>
                    <div class="col-md-8"><select class="form-control js-example-basic-single" name="transaction_type" id="transaction_type" required>
                        <option value="">Select</option>
                        <option value="Cash" @if($editData->transaction_type == "Cash") selected @endif>Cash</option>
                        <option value="Credit" @if($editData->transaction_type == "Credit") selected @endif>Credit</option>
                    </select></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Credit Limit</div>
                    <div class="col-md-8"><input class="form-control" type="number"  name="credit_limit" value="{{ $editData->credit_limit }}" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Credit Days</div>
                    <div class="col-md-8"><input class="form-control" type="number"  name="credit_days" value="{{ $editData->credit_days }}" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Payment Terms</div>
                    <div class="col-md-8"><select class="form-control js-example-basic-single"
                        name="payment_terms" id="payment_terms">
                        <option value=""></option>
                        @foreach ($paymentterms as $key => $value)
                            <option value="{{ @$value->id }}" @if($editData->payment_terms == $value->id) selected @endif>{{ @$value->title }}</option>
                        @endforeach
                    </select></div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane pt-4" id="documents-panel" role="tabpanel" aria-labelledby="documents-tab">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    @if (count($editDoc)>0)
                        @foreach ($editDoc as $doc)
                        <tr>
                            <td>{{ $doc->doc_name }}</td>
                            <td>{{date('d/m/Y', strtotime(@$doc->doc_exp_date))}}</td>
                            <td><a class="btn-sm btn-primary" href="{{asset('public/uploads/cust-suppl/')}}/{{ $doc->doc_file }}" target="_blank">Download</a></td>
                            <td><a class="btn-sm btn-danger" href="{{url('delete-cust-suppl-doc/'.$doc->id)}}">Delete</a></td>
                        </tr>  
                        @endforeach                        
                    @endif
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <a class="btn-info btn-sm float-right" style="cursor: pointer;" onclick="add_doc_row()"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
                <input type="hidden" id="doc_row" value="4" />
                <script>
                    function add_doc_row(){
                        var r = $('#doc_row').val()
                        $('#d_'+r).css('display','');
                        r++;
                        $('#doc_row').val(r);
                    }
                </script>
            </div>
        </div>
        @for ($i = 1; $i <= 10; $i++)
        <div class="row pb-2" id="d_{{ $i }}" @if($i > 3) style="display:none;" @endif>
            <div class="col-md-3">
                <label for="" class="form-check-label">Document Name</label>
                <input class="form-control" type="text" name="doc_name[]" />
            </div>
            <div class="col-md-3">
                <label for="" class="form-check-label">Expiry Date</label>
                <input class="form-control" type="date" name="doc_exp_date[]" />
            </div>
            <div class="col-md-3">
                <label for="" class="form-check-label">Document</label>
                <input class="form-control" type="file" name="customer_documents_{{ $i }}" />
            </div>
            <div class="col-md-3">&nbsp;</div>
        </div>
        @endfor
    </div>
  </div>

{{--  tabs  --}}                           


@if (count($excisting_list)>0)
<hr >
<b>Similer Supplier Accounts</b>
<table class="table table-bordered table-striped" id="dataTable_exclude" width="100%" cellspacing="0">
    <tr>
        <th>Supplier Code</th>
        <th>Supplier Name</th>
        <th>Email</th>
        <th>Name</th>
        <th>Mobile</th>
        <th>Work Phone</th>
        <th></th>
    </tr>
    @foreach ($excisting_list as $list)
        <tr>
            <td>{{ $list->code }}</td>
            <td>{{ $list->name }}</td>
            <td>{{ $list->email }}</td>
            <td>{{ $list->first_name }}</td>
            <td>{{ $list->mobile }}</td>
            <td>{{ $list->contcat_number }}</td>
            <td><a class="btn btn-danger pt-0 pb-0" href="{{ url('supplier-form-details/'.$list->id.'/merge/'.$editData->id.'') }}" onclick="return confirm('Are you sure you want to merge this item?');">Update with This</a></td>
        </tr>
    @endforeach
</table>
@endif
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                @lang('Approve Supplier')
                        </button>
                        </div>
                    </div>

                    <script>
                        $('#btnSubmit').click(function () {
                            $('input:invalid').each(function () {
                                var $closest = $(this).closest('.tab-pane');
                                var id = $closest.attr('id');
                                $('.nav a[href="#' + id + '"]').tab('show');
                                return false;
                            });
                        });
                    </script>

    {{ Form::close() }}

    <!-- Modal Address-->
    <div class="modal fade" id="ModalAddress" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add New Address</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'add-cust-suppl-address', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="cust_suppl_id" value="{{ $editData->id }}" />
                <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">Address Type</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single" name="address_type_n">
                                <option value="0">Billing Address</option>
                                <option value="1">Shipping Address</option>
                            </select></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Country</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single" id="country_n" name="country_n" required>
                                <option data-display="" value=""></option>
                                @foreach ($countries as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->name }} </option>
                                @endforeach
                            </select></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Address 1</div>
                            <div class="col-md-8"><input class="form-control" type="text" name="address_n" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Address 2</div>
                            <div class="col-md-8"><input class="form-control" type="text" name="address2_n" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">City</div>
                            <div class="col-md-8"><input class="form-control" type="text" name="city_n" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">State</div>
                            <div class="col-md-8"><div id="sectionStateDiv_n">
                                <select class="form-control" id="state_n" name="state_n" required>
                                    <option data-display="" value=""></option>
                                    <?php try { ?>
                                    @if (isset($editData) && $editData->vat_state != '')
                                        <option data-display="{{ $editData->vatstate->name }}"
                                            value="{{ $editData->vat_state }}" selected>
                                            {{ $editData->vatstate->name }}</option>
                                    @endif
                                    <?php }catch (\Exception $e) {   } ?>
                                </select>
                            </div></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">PO Box</div>
                            <div class="col-md-8"><input class="form-control" type="text" name="zip_code_n" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Set Default</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single" id="set_default_n">
                                <option value="0">None</option>
                                <option value="1">Default Billing Address</option>
                                <option value="1">Default Shipping Address</option>
                            </select></div>
                        </div>

                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-secondary" data-dismiss="modal">Close</a>
                    <button type="submit" class="btn btn-primary" >Add</button>
                </div>
                {{ Form::close() }}
                
            </div>
        </div>
    </div>
    <!-- Modal Address-->

    <script>
        function add_address(){
            if($("#country_n").val()==""){$("#country_n").focus(); return false;}
            if($("#address_n").val()==""){$("#address_n").focus(); return false;}
            if($("#address2_n").val()==""){$("#address2_n").focus(); return false;}
            if($("#city_n").val()==""){$("#city_n").focus(); return false;}
            if($("#state_n").val()==""){$("#state_n").focus(); return false;}

            $("#loading_bg").css("display", "block");
            var address_type_n=$("#address_type_n").val();
            var country_n=$("#country_n").val();
            var address_n=$("#address_n").val();
            var address2_n=$("#address2_n").val();
            var city_n=$("#city_n").val();
            var state_n=$("#state_n").val();
            var zip_code_n=$("#zip_code_n").val();
            var set_default_n=$("#set_default_n").val();

            var action = "{{ URL::to('add-supplier-script') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    address_type: address_type_n,
                    country: country_n,
                    address: address_n,
                    address2: address2_n,
                    city:city_n,
                    state:state_n,
                    zip_code:zip_code_n,
                    set_default:set_default_n,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found!!");
                        $("#loading_bg").css("display", "none");
                        return false;
                    }
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            $("#address_div").empty();
                            for(var i=0; i<len; i++){
                                var id = dataResult['data'][i].id;
                                var country = dataResult['data'][i].c_name;
                                var address = dataResult['data'][i].address;
                                var address2 = dataResult['data'][i].address2;
                                var city = dataResult['data'][i].city;
                                var state = dataResult['data'][i].s_name;
                                var zip_code = dataResult['data'][i].zip_code;
                                var innerHtml = "<div class='col-md-3'><p style='border: solid 1px #dbdbdb; padding: 10px; border-radius: 5px;'><a class='text-danger float-right' onclick='del_address("+id+")'><i class='fa fa-window-close' aria-hidden='true'></i></a>Country : " + country +"<br />Address : " + address +"<br />Address2 : " + address2 +"<br />City : " + city +"<br />State : " + state +"<br />PO Box : " + zip_code +"</p></div>";
                                $("#address_div").append(innerHtml);
                            }
                            alert("Address Added!!");
                        }
                        else{
                            $("#address_div").empty();
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }
        function del_address(id){
            $("#loading_bg").css("display", "block");

            var action = "{{ URL::to('delete-supplier-script') }}";
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
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found!!");
                        $("#loading_bg").css("display", "none");
                        return false;
                    }
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            $("#address_div").empty();
                            for(var i=0; i<len; i++){
                                var id = dataResult['data'][i].id;
                                var country = dataResult['data'][i].c_name;
                                var address = dataResult['data'][i].address;
                                var address2 = dataResult['data'][i].address2;
                                var city = dataResult['data'][i].city;
                                var state = dataResult['data'][i].s_name;
                                var zip_code = dataResult['data'][i].zip_code;
                                var innerHtml = "<div class='col-md-3'><p style='border: solid 1px #dbdbdb; padding: 10px; border-radius: 5px;'><a class='text-danger float-right' onclick='del_address("+id+")'><i class='fa fa-window-close' aria-hidden='true'></i></a>Country : " + country +"<br />Address : " + address +"<br />Address2 : " + address2 +"<br />City : " + city +"<br />State : " + state +"<br />PO Box : " + zip_code +"</p></div>";
                                $("#address_div").append(innerHtml);
                            }
                            alert("Address Deleted!!");
                        }
                        else{
                            $("#address_div").empty();
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }
    </script>

    <?php  }catch (\Exception $e) {?> {{ $e }} <?php } ?>
    
    <script>
        $(document).ready(function() {

            $("#btnSubmit").click(function() {

                setTimeout(function() {

                    disableButton();

                }, 0);

            });



            function disableButton() {

                //$("#btnSubmit").prop('disabled', true);

            }

        });
    </script>
@endsection
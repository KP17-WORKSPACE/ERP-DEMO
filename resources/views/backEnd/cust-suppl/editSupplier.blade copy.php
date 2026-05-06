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

                <h2 class="page-heading m-0">Supplier</h2>

                <span class="page-label">Home - Supplier</span>

            </div>

            <div>

                <a href="{{ url('suppliers') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Supplier
                    List</a>

            </div>

        </div>

        <div class="card p-4 d-flex mb-3">

            <div class="row justify-content-center">

                <div class="col-md-12 p-4 border rounded">

                    <h2 class="sub-head mb-4">Edit Supplier</h2>

                    <hr>

                    @if (isset($editData))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplier-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        <input type="hidden" value="{{ @$editData->id }}" name="cust_id">
                    @else
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplier-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    @endif
                    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
                    <input type="hidden" name="catid" id="catid" value="2">
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
                                    <option value="{{ @$value->id }}" 
                                        @if(!empty($editData->company_access))
                                            @if(str_contains($editData->company_access, $value->id)) selected @endif
                                        @endif>{{ @$value->company_name }}</option>
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
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#stl-panel" role="tab" aria-controls="stl" aria-selected="false">STL</a></li>
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
                <a class="text-success float-right pr-2" onclick="edit_popup_data({{ $itm->id }})" style="cursor: pointer;" data-toggle="modal" data-target="#ModalAddressEdit"><i class="fa fa-edit" aria-hidden="true"></i></a>
                Country : {{ $itm->countryname->name }}<br />Address : {{ $itm->address }}<br />Address2 : {{ $itm->address2 }}<br />City : {{ $itm->city }}<br />State : {{ $itm->statename->name }}<br />PO Box : {{ $itm->zip_code }}</p></div>

                <input type="hidden" id="country_n_e_{{ $itm->id }}" value="{{ $itm->country }}" />
                <input type="hidden" id="address_n_e_{{ $itm->id }}" value="{{ $itm->address }}" />
                <input type="hidden" id="address2_n_e_{{ $itm->id }}" value="{{ $itm->address2 }}" />
                <input type="hidden" id="city_n_e_{{ $itm->id }}" value="{{ $itm->city }}" />
                <input type="hidden" id="state_n_e_{{ $itm->id }}" value="{{ $itm->state }}" />
                <input type="hidden" id="zip_code_n_e_{{ $itm->id }}" value="{{ $itm->zip_code }}" />
                <input type="hidden" id="set_default_n_e_{{ $itm->id }}" value="{{ $itm->set_default }}" />
            @endforeach                
            @endif
            <script>
                function edit_popup_data(id){
                    $('#cust_suppl_edit_id').val(id);
                    $('#country_n_e').val($('#country_n_e_'+id).val());
                    $('#address_n_e').val($('#address_n_e_'+id).val());
                    $('#address2_n_e').val($('#address2_n_e_'+id).val());
                    $('#city_n_e').val($('#city_n_e_'+id).val());
                    $('#state_n_e').val($('#state_n_e_'+id).val());
                    $('#zip_code_n_e').val($('#zip_code_n_e_'+id).val());
                    $('#set_default_n_e').val($('#set_default_n_e_'+id).val());
                }

            </script>
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
                        @foreach ($paymentterms as $key => $value)
                            <option value="{{ @$value->id }}" @if($editData->payment_terms == $value->id) selected @else @if($value->id==3) selected @endif @endif>{{ @$value->title }}</option>
                        @endforeach
                    </select>
                <input class="form-control" id="payment_terms_txt" type="text" value="{{ $editData->payment_terms_txt }}" autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
                        <script>
                            $('#payment_terms').on('change', function(e) {
                                if ($('#payment_terms').val() == 22) {
                                    $('#payment_terms_txt').css("display", "block");
                                    $('#payment_terms_txt').prop('required', true);
                                } else {
                                    $('#payment_terms_txt').css("display", "none");
                                    $('#payment_terms_txt').prop('required', false);
                                }
                            });
                            $('#payment_terms').change();
                        </script></div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane pt-2" id="stl-panel" role="tabpanel" aria-labelledby="stl-tab">
        <div class="row">
            <div class="col-md-6">
                <div class="row mt-2">
                    <div class="col-md-3">Vendor Name</div>
                    <div class="col-md-8"><input class="form-control" type="text"  name="vendor_name" value="{{ $editData->vendor_name }}"></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Beneficiary Bank Name</div>
                    <div class="col-md-8"><input class="form-control" type="text"  name="beneficiary_name" value="{{ $editData->beneficiary_name }}"></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Account No./ IBAN</div>
                    <div class="col-md-8"><input class="form-control" type="text"  name="iban" value="{{ $editData->iban }}"></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Bank Swift Code</div>
                    <div class="col-md-8"><input class="form-control" type="text"  name="swift_code" value="{{ $editData->swift_code }}"></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">City and Country</div>
                    <div class="col-md-8"><input class="form-control" type="text"  name="city_country" value="{{ $editData->city_country }}"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row mt-2">
                    <div class="col-md-3">STL</div>
                    <div class="col-md-8">
                        <select class="form-control" name="stl" id="stl" onchange="fn_stl()">
                            <option value="0" @if($editData->stl==0) selected @endif>Not Applicable</option>
                            <option value="1" @if($editData->stl==1) selected @endif>Applicable</option>
                        </select>
                    </div>
                </div>
                <script>
                function fn_stl(){
                    if($('#stl').val() == 1){
                        $('.stl_div').css('display','');
                        $('#stl_bank').prop('required',true);
                        $('#stl_dept').prop('required',true);
                        $('#stl_limit').prop('required',true);
                        $('#stl_per_trn_limit').prop('required',true);
                        $('#stl_opb').prop('required',true);
                    } else{
                        $('.stl_div').css('display','none');
                        $('#stl_bank').prop('required',false);
                        $('#stl_dept').prop('required',false);
                        $('#stl_limit').prop('required',false);
                        $('#stl_per_trn_limit').prop('required',false);
                        $('#stl_opb').prop('required',false);
                    }
                }
                </script>
                <div class="row mt-2 stl_div" style="display: none;">
                    <div class="col-md-3">Bank</div>
                    <div class="col-md-8">
                        <select class="form-control js-example-basic-single" type="text" name="stl_bank[]" id="stl_bank" multiple onchange="generateFields()">
                            <option value="">Select</option>
                            @if(count($stl_bank)>0)
                                @foreach ($stl_bank as $s)
                                    <option value="{{ $s->id }}"
                                        @if(count($stl_det)>0)
                                            @foreach ($stl_det as $stl)
                                                @if($stl->stl_bank == $s->id) selected @endif
                                            @endforeach
                                        @endif
                                        data-name="{{ $s->account_name }}">{{ $s->account_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                
                <div class="row mt-2 stl_div" style="display: none;" id="stl_dept_div">
                    <div class="col-md-3">STL Department</div>
                    <div class="col-md-8" id="stl_dept_container">
                        @if(count($stl_det)>0)
                            @foreach ($stl_det as $sd)
                                <input class="form-control" type="text" name="stl_dept[{{ $sd->stl_bank }}]" id="stl_dept_{{ $sd->stl_bank }}" value="{{ @$sd->stl_dept }}">
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="row mt-2 stl_div" style="display: none;" id="stl_limit_div">
                    <div class="col-md-3">STL Limit</div>
                    <div class="col-md-8" id="stl_limit_container">
                        @if(count($stl_det)>0)
                            @foreach ($stl_det as $sd)
                                <input class="form-control" type="text" name="stl_limit[{{ $sd->stl_bank }}]" id="stl_limit_{{ $sd->stl_bank }}" value="{{ @App\SysHelper::com_curr_format(@$sd->stl_limit,2,'.',',') }}" onchange="fn_stl_limit()">
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <div class="row mt-2 stl_div" style="display: none;" id="stl_per_trn_limit_div">
                    <div class="col-md-3">Per Transaction Limit</div>
                    <div class="col-md-8" id="stl_per_trn_limit_container">
                        @if(count($stl_det)>0)
                            @foreach ($stl_det as $sd)
                                <input class="form-control" type="text" name="stl_per_trn_limit[{{ $sd->stl_bank }}]" id="stl_per_trn_limit_{{ $sd->stl_bank }}" value="{{ @App\SysHelper::com_curr_format(@$sd->stl_per_trn_limit,2,'.',',') }}" onchange="fn_stl_per_trn_limit()">
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <div class="row mt-2 stl_div" style="display: none;" id="stl_opb_div">
                    <div class="col-md-3">Opening Balance</div>
                    <div class="col-md-8" id="stl_opb_container">
                        @if(count($stl_det)>0)
                            @foreach ($stl_det as $sd)
                                <input class="form-control" type="text" name="stl_opb[{{ $sd->stl_bank }}]" id="stl_opb_{{ $sd->stl_bank }}" value="{{ @App\SysHelper::com_curr_format(@$sd->stl_opb,2,'.',',') }}" onchange="fn_stl_opb()">
                            @endforeach
                        @endif
                    </div>
                </div>
                
                <script>
                    function generateFields() {
                        // Get selected bank IDs and their names
                        const selectedBanks = Array.from(document.getElementById('stl_bank').selectedOptions).map(option => ({
                            id: option.value,
                            name: option.getAttribute('data-name')
                        }));
                
                        // Save existing values of the fields to retain them
                        const existingValues = {
                            dept: {},
                            limit: {},
                            perTrnLimit: {},
                            opb: {}
                        };
                
                        // Loop through all current input fields and save their current values
                        Array.from(document.querySelectorAll('[id^="stl_dept_"]')).forEach(input => {
                            existingValues.dept[input.id] = input.value;
                        });
                        Array.from(document.querySelectorAll('[id^="stl_limit_"]')).forEach(input => {
                            existingValues.limit[input.id] = input.value;
                        });
                        Array.from(document.querySelectorAll('[id^="stl_per_trn_limit_"]')).forEach(input => {
                            existingValues.perTrnLimit[input.id] = input.value;
                        });
                        Array.from(document.querySelectorAll('[id^="stl_opb_"]')).forEach(input => {
                            existingValues.opb[input.id] = input.value;
                        });
                
                        // Show/hide divs based on selection
                        const fieldsToDisplay = selectedBanks.length > 0;
                        document.getElementById('stl_dept_div').style.display = fieldsToDisplay ? '' : 'none';
                        document.getElementById('stl_limit_div').style.display = fieldsToDisplay ? '' : 'none';
                        document.getElementById('stl_per_trn_limit_div').style.display = fieldsToDisplay ? '' : 'none';
                        document.getElementById('stl_opb_div').style.display = fieldsToDisplay ? '' : 'none';
                
                        // Clear existing inputs from containers
                        document.getElementById('stl_dept_container').innerHTML = '';
                        document.getElementById('stl_limit_container').innerHTML = '';
                        document.getElementById('stl_per_trn_limit_container').innerHTML = '';
                        document.getElementById('stl_opb_container').innerHTML = '';
                
                        // Create input fields for each selected bank
                        selectedBanks.forEach((bank) => {
                            // Create STL Department input
                            const deptInput = document.createElement('input');
                            deptInput.type = 'text';
                            deptInput.name = `stl_dept[${bank.id}]`;
                            deptInput.classList.add('form-control');
                            deptInput.id = `stl_dept_${bank.id}`;
                            deptInput.placeholder = `STL Department for ${bank.name}`;
                            deptInput.value = existingValues.dept[deptInput.id] || ''; // Reapply existing value
                            document.getElementById('stl_dept_container').appendChild(deptInput);

                            // Create STL Limit input
                            const limitInput = document.createElement('input');
                            limitInput.type = 'text';
                            limitInput.name = `stl_limit[${bank.id}]`;
                            limitInput.classList.add('form-control');
                            limitInput.id = `stl_limit_${bank.id}`;
                            limitInput.placeholder = `STL Limit for ${bank.name}`;
                            limitInput.value = existingValues.limit[limitInput.id] || ''; // Reapply existing value
                            limitInput.onchange = fn_stl_limit; // Add any function you want to call on change
                            document.getElementById('stl_limit_container').appendChild(limitInput);
                
                            // Create Per Transaction Limit input
                            const perTrnLimitInput = document.createElement('input');
                            perTrnLimitInput.type = 'text';
                            perTrnLimitInput.name = `stl_per_trn_limit[${bank.id}]`;
                            perTrnLimitInput.classList.add('form-control');
                            perTrnLimitInput.id = `stl_per_trn_limit_${bank.id}`;
                            perTrnLimitInput.placeholder = `Per Transaction Limit for ${bank.name}`;
                            perTrnLimitInput.value = existingValues.perTrnLimit[perTrnLimitInput.id] || ''; // Reapply existing value
                            perTrnLimitInput.onchange = fn_stl_per_trn_limit; // Add any function you want to call on change
                            document.getElementById('stl_per_trn_limit_container').appendChild(perTrnLimitInput);
                
                            // Create Opening Balance input
                            const opbInput = document.createElement('input');
                            opbInput.type = 'text';
                            opbInput.name = `stl_opb[${bank.id}]`;
                            opbInput.classList.add('form-control');
                            opbInput.id = `stl_opb_${bank.id}`;
                            opbInput.placeholder = `Opening Balance for ${bank.name}`;
                            opbInput.value = existingValues.opb[opbInput.id] || ''; // Reapply existing value
                            opbInput.onchange = fn_stl_opb; // Add any function you want to call on change
                            document.getElementById('stl_opb_container').appendChild(opbInput);
                        });
                    }
                
                    // Example placeholder functions for change event
                    function fn_stl_limit() {
                        // Your logic for STL Limit change
                        console.log('STL Limit changed');
                    }
                
                    function fn_stl_per_trn_limit() {
                        // Your logic for Per Transaction Limit change
                        console.log('Per Transaction Limit changed');
                    }
                
                    function fn_stl_opb() {
                        // Your logic for Opening Balance change
                        console.log('Opening Balance changed');
                    }
                </script>
                
                

                <script>
                    function fn_stl_limit(){ $('#stl_limit').val(formatAmount($('#stl_limit').val())); }
                    function fn_stl_per_trn_limit(){ $('#stl_per_trn_limit').val(formatAmount($('#stl_per_trn_limit').val())); }
                    function fn_stl_opb(){ $('#stl_opb').val(formatAmount($('#stl_opb').val())); }
                </script>
                @if(isset($editData))
                    <script>
                        $('#stl').change();
                    </script>
                @endif
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
                <input class="form-control" type="text" name="doc_name[]"
                value="@if($i==1) Trade License/Commercial Registration @elseif($i==2) VAT Certificate @else Other Documents @endif"
                @if($i==1) readonly @endif @if($i==2) readonly @endif/>
            </div>
            <div class="col-md-3">
                <input class="form-control" type="file" name="customer_documents_{{ $i }}" />
            </div>
            @if($i==1)
            <div class="col-md-3">
                <input class="form-control" type="text" name="doc_exp_date[]" placeholder="Expiry Date" onfocus="(this.type='date')" onblur="(this.type='text')"/>

            </div>
            @endif
            <div class="col-md-3">&nbsp;</div>
        </div>
        @endfor
    </div>
  </div>

{{--  tabs  --}}                           


                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                            @if (isset($editData)) @lang('lang.update')
                            @else
                                @lang('lang.add') @endif @lang('Supplier')
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


                    
<?php /*
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Customer Name</label>
                                <input class="form-control" type="text" name="customer_name" required>
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="">Contcat Person</label>

                                <input class="form-control" type="text" name="contcat_person"
                                    value="{{ isset($editData) ? @$editData->contcat_person : old('contcat_person') }}"
                                    required>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="">Contact Number</label>

                                <input class="form-control" type="text" name="contcat_number"
                                    value="{{ isset($editData) ? @$editData->contcat_number : old('contcat_number') }}">

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="">Mobile Number</label>

                                <div class="row">

                                    <div class="col-md-4">

                                        <select class="form-control js-example-basic-single" name="mobile_code"
                                            id="mobile_code">

                                            <option data-display="" value="">Code</option>

                                            @foreach ($countries as $key => $value)

                                                <option value="{{ @$value->phonecode }}">{{ @$value->iso2 }} -
                                                    {{ @$value->phonecode }} </option>

                                            @endforeach

                                        </select>

                                    </div>

                                    <div class="col-md-8">

                                        <input class="form-control" type="text" name="mobile"
                                            value="{{ isset($editData) ? @$editData->mobile : old('mobile') }}" required>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="">Email</label>

                                <input class="form-control" type="text" name="email"
                                    value="{{ isset($editData) ? @$editData->email : old('email') }}" required>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="">Address</label>

                                <input class="form-control" type="text" name="address"
                                    value="{{ isset($editData) ? @$editData->address : old('address') }}" required>

                            </div>

                        </div>

                        <div class="col-md-4" style="display: none;">

                            <div class="form-group">

                                <label for="">Address 2</label>

                                <input class="form-control" type="text" name="address2"
                                    value="{{ isset($editData) ? @$editData->address2 : old('address2') }}">

                            </div>

                        </div>


                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="">Country</label>

                                

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="">State</label>

                                

                            </div>

                        </div>
                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="">Sales Person</label>

                                <select class="form-control js-example-basic-single" name="sales_person[]"
                                    id="sales_person" multiple required>

                                    <option data-display="" value="">Select</option>

                                    @foreach ($staffs as $value)

                                        <option value="{{ @$value->user_id }}"
                                            @if (isset($editData)) @foreach ($salespersons as $sp)  @if (@$sp->user_id == @$value->user_id) selected @endif
                                            @endforeach
                                    @endif>{{ @$value->full_name }}</option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label for="">Credit Limit</label>

                                <input class="form-control" type="text" name="credit_limit"
                                    value="{{ isset($editData) ? @$editData->credit_limit : old('credit_limit') }}">

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label for="">Credit Days</label>

                                <input class="form-control" type="text" name="credit_days"
                                    value="{{ isset($editData) ? @$editData->credit_days : old('credit_days') }}">

                            </div>

                        </div>



                        <div class="col-md-3">

                            <div class="form-group">

                                <label for="">Payment Terms</label>

                                <select class="form-control js-example-basic-single" name="payment_terms"
                                    id="payment_terms">

                                    <option value=""></option>

                                    @foreach ($paymentterms as $key => $value)

                                        <option value="{{ @$value->id }}"
                                            @if (isset($editData)) @if (@$editData->payment_terms == @$value->id) selected @endif
                                        @else {{ old('payment_terms') == @$value->id ? 'selected' : '' }} @endif >{{ @$value->title }}</option>

                                    @endforeach

                                </select>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">

                                <label for="">Color</label>

                                <select class="form-control" name="type" id="type">

                                    <option value="1"
                                        @if (isset($editData)) @if (@$editData->type == 1) selected @endif
                                        @endif>Green</option>

                                    <option value="2"
                                        @if (isset($editData)) @if (@$editData->type == 2) selected @endif
                                        @endif>Orange</option>

                                    <option value="3"
                                        @if (isset($editData)) @if (@$editData->type == 3) selected @endif
                                        @endif>Red</option>

                                    <option value="4"
                                        @if (isset($editData)) @if (@$editData->type == 4) selected @endif
                                        @endif>Black</option>

                                </select>

                            </div>

                        </div>

                    </div>

                    <h2 class="sub-head mb-4">VAT Details</h2>

                    <hr>

                    <div class="row">

                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="">VAT Type</label>

                                <select class="form-control" name="vat_type" id="vat_type">

                                    <option data-display="" value=""></option>

                                    @foreach ($vattype as $key => $value)

                                        <option value="{{ @$value->id }}"
                                            @if (isset($editData)) @if (@$editData->vat_type == $value->id) selected @endif
                                            @endif>{{ @$value->type }} </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>



                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="">VAT %</label>

                                <input class="form-control" type="text" name="vat_percentage"
                                    value="{{ isset($editData) ? @$editData->vat_percentage : old('vat_percentage') }}">

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="form-group">

                                <label for="">VAT Number</label>

                                <input class="form-control" type="text" name="vat_number"
                                    value="{{ isset($editData) ? @$editData->vat_number : old('vat_number') }}">

                            </div>

                        </div>

                    </div>



                    <div class="d-flex justify-content-end">

                        

                    </div>


                </div>

            </div>

        </div>



    </div>
    */ ?>
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

    <!-- Modal Address Edit-->
    <div class="modal fade" id="ModalAddressEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Address</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'update-cust-suppl-address', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" id="cust_suppl_edit_id" name="cust_suppl_edit_id" />
                <input type="hidden" name="cust_suppl_edit" value="{{ $editData->id }}" />
                <div class="modal-body">
                        <div class="row">
                            <div class="col-md-3">Address Type</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single" id="address_type_n_e" name="address_type_n_e">
                                <option value="0">Billing Address</option>
                                <option value="1">Shipping Address</option>
                            </select></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Country</div>
                            <div class="col-md-8"><select class="form-control" id="country_n_e" name="country_n_e" required>
                                <option data-display="" value=""></option>
                                @foreach ($countries as $key => $value)
                                    <option value="{{ @$value->id }}">{{ @$value->name }} </option>
                                @endforeach
                            </select></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Address 1</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="address_n_e" name="address_n_e" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Address 2</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="address2_n_e" name="address2_n_e" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">City</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="city_n_e" name="city_n_e" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">State</div>
                            <div class="col-md-8"><div id="sectionStateDiv_n_e">
                                <select class="form-control" id="state_n_e" name="state_n_e" required>
                                    <option data-display="" value=""></option>
                                    <?php try { ?>
                                    @if (isset($states))
                                        @foreach ($states as $st)
                                            <option data-display="{{ $st->name }}" value="{{ $st->id }}" selected> {{ $st->name }}</option>
                                        @endforeach
                                    @endif
                                    <?php }catch (\Exception $e) {   } ?>
                                </select>
                            </div></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">PO Box</div>
                            <div class="col-md-8"><input class="form-control" type="text" id="zip_code_n_e" name="zip_code_n_e" placeholder="" required></div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3">Set Default</div>
                            <div class="col-md-8"><select class="form-control js-example-basic-single" id="set_default_n_e" name="set_default_n_e">
                                <option value="0">None</option>
                                <option value="1">Default Billing Address</option>
                                <option value="1">Default Shipping Address</option>
                            </select></div>
                        </div>

                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-secondary" data-dismiss="modal">Close</a>
                    <button type="submit" class="btn btn-primary" >Update</button>
                </div>
                {{ Form::close() }}
                
            </div>
        </div>
    </div>
    <!-- Modal Address Edit-->

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
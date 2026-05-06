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

                    <h2 class="sub-head mb-4">New Supplier</h2>

                    <hr>

                    @if (isset($editData))

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplier-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                        <input type="hidden" value="{{ @$editData->id }}" name="cust_id">
                    @else
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplier-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                    @endif


                    <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

                    <div class="row mt-2">
                        <div class="col-md-2">
                            <label for="" class="form-check-label">Primary Contact</label></div>                            
                        <div class="col-md-1">
                            <select class="form-control js-example-basic-single" name="customer_salutation" required>
                                <option value="">--</option>
                                <option value="Mr">Mr</option>
                                <option value="Mrs">Mrs</option>
                                <option value="Miss">Miss</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="text" name="first_name" placeholder="First Name"
                                value="{{ isset($editData) ? @$editData->name : old('name') }}" required>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="text" name="last_name" placeholder="Last Name"
                                value="{{ isset($editData) ? @$editData->name : old('name') }}">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <label for="" class="form-check-label">Company Name</label></div>
                            <div class="col-md-3">
                                <input class="form-control" type="text" name="customer_name" id="customer_name" placeholder="Company Name" required>
                                <div id="company_name_add_list">
                                </div>                            
                                <script>
                                    $(document).ready(function(){                                    
                                     $('#customer_name').keyup(function(){ 
                                            var query = $(this).val();
                                            if(query != '')
                                            {
                                             var _token = $('input[name="_token"]').val();
                                             $.ajax({
                                              url:"{{ route('autocomplete.supplier_name') }}",
                                              method:"POST",
                                              data:{query:query, _token:_token},
                                              success:function(data){
                                               $('#company_name_add_list').fadeIn();
                                                        $('#company_name_add_list').html(data);
                                              }
                                             });
                                            }
                                        });
                                        $('#company_name_add_list').on('click', 'li', function(){  
                                            $('#customer_name').val($(this).text());  
                                            $('#company_name_add_list').fadeOut();  
                                        });                                    
                                    });
                                    </script>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control js-example-basic-single" name="designation" required>
                                <option value="">--Designation--</option>
                                @if (count($designation)>0)
                                    @foreach ($designation as $val)
                                        <option value="{{ $val->title }}">{{ $val->title }}</option>
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
                                <input class="form-control" type="text" name="customer_name_display" id="customer_name_display" placeholder="Supplier Display Name" required>
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
                                <input class="form-control" type="text" name="email" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-2">
                            <label for="" class="form-check-label">Supplier Phone</label></div>
                            <div class="col-md-3">
                                <input class="form-control" type="text" name="mobile_code" placeholder="Work Phone" required>
                        </div>
                        <div class="col-md-2">
                                <input class="form-control" type="text" name="mobile" placeholder="Mobile">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-12">

{{--  tabs  --}}
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#address-panel" role="tab"  aria-selected="true">Address</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#contactperson-panel" role="tab"  aria-selected="true">Contact Person</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#vat-panel" role="tab"  aria-selected="false">VAT</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#payment-panel" role="tab"  aria-selected="false">Payment</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#stl-panel" role="tab"  aria-selected="false">STL</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#documents-panel" role="tab"  aria-selected="false">Documents</a></li>
  </ul>

  
  <div class="tab-content">
    {{--  Address  --}}
    <div class="tab-pane active pt-2" id="address-panel" role="tabpanel" aria-labelledby="address-tab">
        
        <a class="float-right" style="cursor: pointer;" data-toggle="modal" data-target="#ModalAddress"><i class="fa fa-address-book" aria-hidden="true"></i> Add More</a>

        <div class="row">
            <div class="col-md-6">
                <p><b>Billing Address</b></p>
                <div class="row">
                    <div class="col-md-3">Country</div>
                    <div class="col-md-8"><select class="form-control js-example-basic-single" name="country" id="country" required>
                        <option data-display="" value=""></option>
                        @foreach ($countries as $key => $value)
                            <option value="{{ @$value->id }}">{{ @$value->name }} </option>
                        @endforeach
                    </select></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Address 1</div>
                    <div class="col-md-8"><input class="form-control" type="text" name="address" placeholder="" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Address 2</div>
                    <div class="col-md-8"><input class="form-control" type="text" name="address2" placeholder="" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">City</div>
                    <div class="col-md-8"><input class="form-control" type="text" name="city" placeholder="" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">State</div>
                    <div class="col-md-8"><div id="sectionStateDiv">
                        <select class="form-control" name="state" id="state">
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
                    <div class="col-md-3">Post Box</div>
                    <div class="col-md-8"><input class="form-control" type="text" name="zip_code" placeholder=""></div>
                </div>
            </div>
            <div class="col-md-6"><p style="float: right;"><input type="checkbox" name="same_billing_address" id="same_billing_address" value="1"> Same as Billing Address</p>
                <script>
                    $( "#same_billing_address" ).click(function() {
                        if(this.checked){
                            $('[name=address_ship]').val($('[name=address]').val());
                            $('[name=address2_ship]').val($('[name=address2]').val());
                            $('[name=city_ship]').val($('[name=city]').val());
                            $('#select2-country_ship-container').html($('#country option:selected').text());
                            $('#state_ship').append( new Option($('#state option:selected').text(),'0',true,true) );
                            $('[name=zip_code_ship]').val($('[name=zip_code]').val());
                            $('#country_ship').removeAttr('required');
                            $('#address_ship').removeAttr('required');
                            $('#address2_ship').removeAttr('required');
                            $('#city_ship').removeAttr('required');
                            $('#state_ship').removeAttr('required');
                            $('#zip_code_ship').removeAttr('required');
                        }
                        if(!this.checked){
                            $('[name=address_ship]').val('');
                            $('[name=address2_ship]').val('');
                            $('[name=city_ship]').val('');
                            $('[name=country_ship]').val('');
                            $('[name=state_ship]').val('');
                            $('[name=zip_code_ship]').val('');
                            $('#country_ship').attr('required');
                            $('#address_ship').attr('required');
                            $('#address2_ship').attr('required');
                            $('#city_ship').attr('required');
                            $('#state_ship').attr('required');
                            $('#zip_code_ship').attr('required');
                        }
                    });                    
                </script>
                <p><b>Shipping Address</b></p>
                <div class="row">
                    <div class="col-md-3">Country</div>
                    <div class="col-md-8"><select class="form-control js-example-basic-single" name="country_ship" id="country_ship" required>
                        <option data-display="" value=""></option>
                        @foreach ($countries as $key => $value)
                            <option value="{{ @$value->id }}">{{ @$value->name }} </option>
                        @endforeach
                    </select></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Address 1</div>
                    <div class="col-md-8"><input class="form-control" type="text" name="address_ship" placeholder="" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Address 2</div>
                    <div class="col-md-8"><input class="form-control" type="text" name="address2_ship" placeholder="" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">City</div>
                    <div class="col-md-8"><input class="form-control" type="text" name="city_ship" placeholder="" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">State</div>
                    <div class="col-md-8"><div id="sectionStateDiv_ship">
                        <select class="form-control" name="state_ship" id="state_ship">
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
                    <div class="col-md-3">Post Box</div>
                    <div class="col-md-8"><input class="form-control" type="text" name="zip_code_ship" placeholder=""></div>
                </div>            
            </div>
        </div>
        <div class="row mt-4" id="address_div">
            @if (count($address_cart)>0)
            @foreach ($address_cart as $itm)
                
            <div class="col-md-3">
                <p style="border: solid 1px #dbdbdb; padding: 10px; border-radius: 5px;"><a class="text-danger float-right" onclick="del_address({{ $itm->id }})"><i class="fa fa-window-close" aria-hidden="true"></i></a>
                    Country : {{ $itm->c_name }}<br />Address : {{ $itm->address }}<br />Address2 : {{ $itm->address2 }}<br />City : {{ $itm->city }}<br />State : {{ $itm->state }}<br />PO Box : {{ $itm->zip_code }}</p>
            </div>

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
                            <th><a class="btn-sm btn-primary"><i class="fa fa-plus" aria-hidden="true"></i></a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1;?>
                        @for ($r=1; $r <= 5; $r++)
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
                            <td><select class="form-control js-example-basic-single" name="e_designation[]" id="e_designation_{{ $i }}">
                                <option value="">--Designation--</option>
                                @if (count($designation)>0)
                                    @foreach ($designation as $val)
                                        <option value="{{ $val->title }}">{{ $val->title }}</option>
                                    @endforeach
                                @endif
                            </select></td>
                        <td><select class="form-control js-example-basic-single" name="e_department[]" id="e_department_{{ $i }}">
                            <option value="">--Department--</option>
                            @if (count($department)>0)
                                @foreach ($department as $val)
                                    <option value="{{ $val->name }}">{{ $val->name }}</option>
                                @endforeach
                            @endif
                        </select></td>
                            <td>
                                <input type="hidden" name="isdelete[]" id="isdelete_{{ $i }}" value="0" />
                                <a onclick="row_delete({{ $i }})" class="btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a></td>
                        </tr>
                        <?php $i++;?>
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
                                <option value="{{ @$value->vat_country }}">{{ @$value->name }} </option>
                        @endforeach
                    </select></div>
                </div>
                <div class="row mt-2" style="display: none;">
                    <div class="col-md-3">VAT State</div>
                    <div class="col-md-8"><div class="input-effect" id="sectionStateDiv_vat">
                        <select class="form-control" name="state_vat" id="state_vat">
                            @if (isset($editData))
                                    <option data-display="{{ $editData->vatstate->name }}"
                                        value="{{ $editData->vat_state }}" selected>
                                        {{ $editData->vatstate->name }}</option>
                                @endif
                        </select>
                    </div></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">VAT %</div>
                    <div class="col-md-2"><input class="form-control" type="number"  name="vat_percentage" id="vat_percentage" readonly required></div>
                    <div class="col-md-4 mt-2"><input type="checkbox"  name="vat_percentage_fixed" id="vat_percentage_fixed" value="1"> Fixed Rate</div>
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
                            <option value="{{ @$value->id }}" @if($value->id == 5) selected @endif>{{ @$value->title }} </option>
                        @endforeach
                    </select></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Purchase Type</div>
                    <div class="col-md-8"><select class="form-control" name="purchase_type" id="purchase_type" required>
                        <option data-display="" value=""></option>
                        @foreach ($purchase_type as $key => $value)
                            <option value="{{ @$value->id }}" @if($value->id == 6) selected @endif>{{ @$value->title }} </option>
                        @endforeach
                    </select></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">VAT Number</div>
                    <div class="col-md-8"><input class="form-control" type="text"  name="vat_number"></div>
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
                        <option value="Cash">Cash</option>
                        <option value="Credit">Credit</option>
                    </select></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Credit Limit</div>
                    <div class="col-md-8"><input class="form-control" type="number"  name="credit_limit" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Credit Days</div>
                    <div class="col-md-8"><input class="form-control" type="number"  name="credit_days" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Payment Terms</div>
                    <div class="col-md-8"><select class="form-control js-example-basic-single"
                        name="payment_terms" id="payment_terms">
                        @foreach ($paymentterms as $key => $value)
                            <option value="{{ @$value->id }}" @if ($value->id==3) selected @endif>{{ @$value->title }}</option>
                        @endforeach
                    </select>
                    <input class="form-control" id="payment_terms_txt" type="text" value="" autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
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
                        </script> </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane pt-2" id="stl-panel" role="tabpanel" aria-labelledby="stl-tab">
        <div class="row">
            <div class="col-md-6">
                <div class="row mt-2">
                    <div class="col-md-3">Vendor Name</div>
                    <div class="col-md-8"><input class="form-control" type="text"  name="vendor_name" ></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Beneficiary Bank Name</div>
                    <div class="col-md-8"><input class="form-control" type="text"  name="beneficiary_name" ></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Account No./ IBAN</div>
                    <div class="col-md-8"><input class="form-control" type="text"  name="iban" ></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">Bank Swift Code</div>
                    <div class="col-md-8"><input class="form-control" type="text"  name="swift_code" ></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">City and Country</div>
                    <div class="col-md-8"><input class="form-control" type="text"  name="city_country" ></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row mt-2">
                    <div class="col-md-3">STL</div>
                    <div class="col-md-8">
                        <select class="form-control" name="stl" id="stl" onchange="fn_stl()">
                            <option value="0">Not Applicable</option>
                            <option value="1">Applicable</option>
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
                                    <option value="{{ $s->id }}" data-name="{{ $s->account_name }}">{{ $s->account_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                
                <div class="row mt-2 stl_div" style="display: none;" id="stl_dept_div">
                    <div class="col-md-3">STL Department</div>
                    <div class="col-md-8" id="stl_dept_container"></div>
                </div>

                <div class="row mt-2 stl_div" style="display: none;" id="stl_limit_div">
                    <div class="col-md-3">STL Limit</div>
                    <div class="col-md-8" id="stl_limit_container"></div>
                </div>
                
                <div class="row mt-2 stl_div" style="display: none;" id="stl_per_trn_limit_div">
                    <div class="col-md-3">Per Transaction Limit</div>
                    <div class="col-md-8" id="stl_per_trn_limit_container"></div>
                </div>
                
                <div class="row mt-2 stl_div" style="display: none;" id="stl_opb_div">
                    <div class="col-md-3">Opening Balance</div>
                    <div class="col-md-8" id="stl_opb_container"></div>
                </div>
                
                <script>
                    function generateFields() {
                        // Get selected bank IDs and their names
                        const selectedBanks = Array.from(document.getElementById('stl_bank').selectedOptions).map(option => ({
                            id: option.value,
                            name: option.getAttribute('data-name')
                        }));
                
                        // Show/hide divs based on selection
                        const fieldsToDisplay = selectedBanks.length > 0;
                        document.getElementById('stl_dept_div').style.display = fieldsToDisplay ? '' : 'none';
                        document.getElementById('stl_limit_div').style.display = fieldsToDisplay ? '' : 'none';
                        document.getElementById('stl_per_trn_limit_div').style.display = fieldsToDisplay ? '' : 'none';
                        document.getElementById('stl_opb_div').style.display = fieldsToDisplay ? '' : 'none';
                
                        // Clear existing inputs
                        document.getElementById('stl_dept_container').innerHTML = '';
                        document.getElementById('stl_limit_container').innerHTML = '';
                        document.getElementById('stl_per_trn_limit_container').innerHTML = '';
                        document.getElementById('stl_opb_container').innerHTML = '';
                
                        // Create input fields for each selected bank
                        selectedBanks.forEach((bank, index) => {
                            // Create STL Department input
                            const deptInput = document.createElement('input');
                            deptInput.type = 'text';
                            deptInput.name = `stl_dept[${bank.id}]`;
                            deptInput.classList.add('form-control');
                            deptInput.id = `stl_dept_${bank.id}`;
                            deptInput.placeholder = `STL Department for ${bank.name}`;
                            document.getElementById('stl_dept_container').appendChild(deptInput);

                            // Create STL Limit input
                            const limitInput = document.createElement('input');
                            limitInput.type = 'text';
                            limitInput.name = `stl_limit[${bank.id}]`;
                            limitInput.classList.add('form-control');
                            limitInput.id = `stl_limit_${bank.id}`;
                            limitInput.placeholder = `STL Limit for ${bank.name}`;
                            limitInput.onchange = fn_stl_limit; // Add any function you want to call on change
                            document.getElementById('stl_limit_container').appendChild(limitInput);
                
                            // Create Per Transaction Limit input
                            const perTrnLimitInput = document.createElement('input');
                            perTrnLimitInput.type = 'text';
                            perTrnLimitInput.name = `stl_per_trn_limit[${bank.id}]`;
                            perTrnLimitInput.classList.add('form-control');
                            perTrnLimitInput.id = `stl_per_trn_limit_${bank.id}`;
                            perTrnLimitInput.placeholder = `Per Transaction Limit for ${bank.name}`;
                            perTrnLimitInput.onchange = fn_stl_per_trn_limit; // Add any function you want to call on change
                            document.getElementById('stl_per_trn_limit_container').appendChild(perTrnLimitInput);
                
                            // Create Opening Balance input
                            const opbInput = document.createElement('input');
                            opbInput.type = 'text';
                            opbInput.name = `stl_opb[${bank.id}]`;
                            opbInput.classList.add('form-control');
                            opbInput.id = `stl_opb_${bank.id}`;
                            opbInput.placeholder = `Opening Balance for ${bank.name}`;
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
            </div>
        </div>
    </div>



    <div class="tab-pane pt-4" id="documents-panel" role="tabpanel" aria-labelledby="documents-tab">
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
        
        <div class="row pb-2">
            <div class="col-md-3">
                <input class="form-control" type="text" name="doc_name[]" value="Trade License/Commercial Registration" readonly/>
            </div>
            <div class="col-md-3">
                <input class="form-control" type="file" name="customer_documents_1" />
            </div>
            <div class="col-md-3">
                <input class="form-control" type="text" name="doc_exp_date[]" placeholder="Expiry Date" onfocus="(this.type='date')" onblur="(this.type='text')"/>
            </div>
            <div class="col-md-3">&nbsp;</div>
        </div>
        <div class="row pb-2">
            <div class="col-md-3">
                <input class="form-control" type="text" name="doc_name[]" value="VAT Certificate" readonly/>
            </div>
            <div class="col-md-3">
                <input class="form-control" type="file" name="customer_documents_2" />
            </div>
            <div class="col-md-3">&nbsp;</div>
        </div>

        @for ($i = 3; $i <= 10; $i++)
        <div class="row pb-2" id="d_{{ $i }}" @if($i > 3) style="display:none;" @endif>
            <div class="col-md-3">
                <input class="form-control" type="text" name="doc_name[]" value="Other Documents"/>
            </div>
            <div class="col-md-3">
                <input class="form-control" type="file" name="customer_documents_{{ $i }}" />
            </div>
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
                
                                <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-3">Address Type</div>
                                            <div class="col-md-8"><select class="form-control js-example-basic-single" id="address_type_n">
                                                <option value="0">Billing Address</option>
                                                <option value="1">Shipping Address</option>
                                            </select></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3">Country</div>
                                            <div class="col-md-8"><select class="form-control js-example-basic-single" id="country_n">
                                                <option data-display="" value=""></option>
                                                @foreach ($countries as $key => $value)
                                                    <option value="{{ @$value->id }}">{{ @$value->name }} </option>
                                                @endforeach
                                            </select></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3">Address 1</div>
                                            <div class="col-md-8"><input class="form-control" type="text" id="address_n" placeholder=""></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3">Address 2</div>
                                            <div class="col-md-8"><input class="form-control" type="text" id="address2_n" placeholder=""></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3">City</div>
                                            <div class="col-md-8"><input class="form-control" type="text" id="city_n" placeholder=""></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3">State</div>
                                            <div class="col-md-8"><div id="sectionStateDiv_n">
                                                <select class="form-control" id="state_n">
                                                    <option data-display="" value=""></option>
                                                   
                                                </select>
                                            </div></div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-3">PO Box</div>
                                            <div class="col-md-8"><input class="form-control" type="text" id="zip_code_n" placeholder=""></div>
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
                                    <a id="btn_add_address" class="btn btn-primary" onclick="add_address()">Add</a>
                                </div>
                                
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

                    //disableButton();

                }, 0);

            });



            function disableButton() {

                //$("#btnSubmit").prop('disabled', true);

            }

        });
    </script>
@endsection














<?php /*

@extends('backEnd.masterpage')
@section('mainContent')
@php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Supplier</h2>
            <span class="page-label">Home - Supplier</span>
        </div>
        <div>
            <a href="{{ url('suppliers') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Supplier List</a>
        </div>
    </div>
    
    <div class="card p-4 d-flex mb-3">
        <div class="row justify-content-center">
            <div class="col-md-8 p-4 border rounded">
                <h2 class="sub-head mb-4">Basic Information</h2>
                <hr>
        @if(isset($editData))
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplier-update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
        <input type="hidden" value="{{@$editData->id}}" name="cust_id">
        @else
        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'supplier-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
        @endif

        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">  
        <input type="hidden" name="catid" id="catid" value="2">
        <div class="row">
            <div class="col-lg-6">
                    <div style="display: none;">                                   
                        <input type="hidden"  name="supplier_code" value="{{ 'SUP' . sprintf('%03d', @App\SysHelper::get_new_maxid('sys_cust_suppl', 'id')) }}">
                    </div>

                    <div class="input-effect">
                        <label class="txtlbl">  @lang('Supplier Name') <span>*</span> </label>
                        <input class="form-control" type="text"  name="supplier_name" value="{{isset($editData)?@$editData->name:old('name')}}" required>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="input-effect">
                        <label class="txtlbl">  @lang('Contcat Person') <span>*</span> </label>
                        <input class="form-control" type="text"  name="contcat_person" value="{{isset($editData)?@$editData->contcat_person:old('contcat_person')}}" required>
                    </div>
                </div>
                
                <div class="col-lg-6 mt-4">
                    <div class="input-effect">
                        <label class="txtlbl">  @lang('Contact Number') <span>*</span> </label>
                        <input class="form-control" type="number"  name="contcat_number" value="{{isset($editData)?@$editData->contcat_number:old('contcat_number')}}" required>
                    </div>
                </div>
                
                <div class="col-lg-6 mt-4">
                    <div class="input-effect">
                        <label class="txtlbl">  @lang('Mobile Number') <span>*</span> </label>
                        <div class="row">
                        <div class="col-lg-4">
                        <select class="form-control js-example-basic-single"
                            name="mobile_code" id="mobile_code">
                            <option data-display="" value="">Code</option>
                            @foreach ($countries as $key => $value)
                                <option value="{{ @$value->phonecode }}"
                                
                                {{-- @if(isset($editData))
                                    @if(@$editData->sales_person_name == @$value->id) selected @endif
                                @else
                                    {{ old('salesperson') == @$value->id ? 'selected' : '' }}
                                @endif --}}
                                >{{ @$value->iso2 }} - {{ @$value->phonecode }} </option>
                            @endforeach
                        </select>
                        </div>
                        <div class="col-lg-8">
                        <input class="form-control" type="number"  name="mobile" value="{{isset($editData)?@$editData->mobile:old('mobile')}}" required>
                        </div>
                        </div>
                        
                    </div>
                </div>
                
                <div class="col-lg-6 mt-4">
                    <div class="input-effect">
                        <label class="txtlbl">  @lang('Email') <span>*</span> </label>
                        <input class="form-control" type="email"  name="email" value="{{isset($editData)?@$editData->email:old('email')}}" required>
                    </div>
                </div>
                
                <div class="col-lg-6 mt-4">
                    <div class="input-effect">
                        <label class="txtlbl"> @lang('Sales Person') <span>*</span> </label>
                        <select class="form-control js-example-basic-single"
                            name="sales_person" id="sales_person">
                            <option data-display="" value="">Select</option>
                            @foreach ($staffs as $value)
                                <option value="{{ @$value->user_id }}"
                                    @if (isset($editData))
                                        @if (@$editData->sales_person == @$value->user_id) selected @endif
                                    @else
                                        {{ old('sales_person') == @$value->user_id ? 'selected' : '' }}
                                    @endif>{{ @$value->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
            <div class="col-lg-6 mt-4">
                <div class="input-effect">
                    <label class="txtlbl">  @lang('Address') <span>*</span> </label>
                    <input class="form-control" type="text"  name="address" value="{{isset($editData)?@$editData->address:old('address')}}" required>
                </div>
            </div>
            
            <div class="col-lg-6 mt-4">
                <div class="input-effect">
                    <label class="txtlbl">  @lang('Address 2') <span></span> </label>
                    <input class="form-control" type="text"  name="address2" value="{{isset($editData)?@$editData->address2:old('address2')}}">
                </div>
            </div>
            
            <div class="col-lg-6 mt-4">
                    <div class="input-effect">
                        <label class="txtlbl">@lang('Credit Limit') <span>*</span> </label>
                        <input class="form-control" type="text"  name="credit_limit" value="{{isset($editData)?@$editData->credit_limit:old('credit_limit')}}" required>
                    </div>
                </div>
                
                <div class="col-lg-6 mt-4">
                    <div class="input-effect">                        
                        <label class="txtlbl">@lang('Credit Days') <span>*</span> </label>
                        <input class="form-control" type="text"  name="credit_days" value="{{isset($editData)?@$editData->credit_days:old('credit_days')}}" required>
                    </div>
                </div>
                
                <div class="col-lg-6 mt-4">
                    <div class="input-effect">                        
                        <label class="txtlbl">@lang('Payment Terms') <span>*</span> </label>
                        <select class="form-control js-example-basic-single"
                            name="payment_terms" id="payment_terms">
                            <option value=""></option>
                            @foreach ($paymentterms as $key => $value)
                                <option value="{{ @$value->id }}"
                                
                                @if(isset($editData))
                                    @if(@$editData->payment_terms == @$value->id) selected @endif
                                @else
                                    {{ old('payment_terms') == @$value->id ? 'selected' : '' }}
                                @endif
                                >{{ @$value->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <h2 class="sub-head mt-4">VAT Details</h2>
            <hr>
            <div class="row">

                <div class="col-lg-6">
                    <div class="input-effect">
                        <label class="txtlbl">  @lang('Supplier Type') <span>*</span> </label>
                        <select class="form-control" name="vat_type" id="vat_type" required>
                            <option data-display="" value=""></option>
                            @foreach ($vattype as $key => $value)
                                <option value="{{ @$value->id }}" @if(isset($editData)) @if(@$editData->vat_type == $value->id) selected @endif @endif>{{ @$value->type }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="input-effect">
                        <label class="txtlbl">  @lang('Purchase Type') <span>*</span> </label>
                        <select class="form-control" name="purchase_type" id="purchase_type" required>
                            <option data-display="" value=""></option>
                            @foreach ($purchase_type as $key => $value)
                                <option value="{{ @$value->id }}" @if(isset($editData)) @if(@$editData->purchase_type == $value->id) selected @endif @endif>{{ @$value->title }} </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-6 mt-4">
                    <div class="input-effect">
                        <label class="txtlbl">  @lang('VAT Country') <span>*</span> </label>
                        <select class="form-control js-example-basic-single" name="country" id="country" required>
                            <option data-display="" value=""></option>
                            @foreach ($countries as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if (isset($editData)) @if (@$editData->vat_country == $value->id) selected @endif
                                        @endif>{{ @$value->name }} </option>
                                @endforeach
                        </select>
                        {{-- <input class="primary-input form-control{{ $errors->has('vat_country') ? ' is-invalid' : '' }}" type="text"  name="vat_country" value="{{isset($editData)?@$editData->vat_country:old('vat_country')}}"> --}}
                    </div>
                </div>
                
                <div class="col-lg-6 mt-4">
                    <div class="input-effect">
                        <div class="input-effect" id="sectionStateDiv">
                            <label class="txtlbl">  @lang('VAT State') <span>*</span> </label>
                            <select class="form-control" name="state" id="state" required>
                                <option data-display="" value=""></option>
                                @if (isset($editData))
                                        <option data-display="{{ $editData->vatstate->name }}"
                                            value="{{ $editData->vat_state }}" selected>
                                            {{ $editData->vatstate->name }}</option>
                                    @endif
                            </select>
                        </div>
                        {{-- <input class="primary-input form-control{{ $errors->has('vat_state') ? ' is-invalid' : '' }}" type="text"  name="vat_state" value="{{isset($editData)?@$editData->vat_state:old('vat_state')}}"> --}}
                    </div>
                </div>
                
                <div class="col-lg-6 mt-4">
                    <div class="input-effect">
                        <label class="txtlbl">  @lang('VAT %') <span>*</span> </label>
                        <input class="form-control" type="number"  name="vat_percentage" value="{{isset($editData)?@$editData->vat_percentage:old('vat_percentage')}}" required>
                    </div>
                </div>
                
                <div class="col-lg-6 mt-4">
                    <div class="input-effect">
                        <label class="txtlbl">  @lang('VAT Number') <span>*</span> </label>
                        <input class="form-control" type="text"  name="vat_number" value="{{isset($editData)?@$editData->vat_number:old('vat_number')}}" required>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" id="btnSubmit">
                    @if (isset($editData)) @lang('lang.update')
                    @else @lang('lang.add') @endif @lang('Supplier')
                </button>
            </div>        
                
        {{ Form::close() }} 
            </div>
        </div>
    </div>


</div>









<section class="admin-visitor-area">
    <div class="container-fluid p-0">

        <div class="row">
            <div class="col-lg-12"> 
              <div class="white-box">
                    




            
                    {{-- Additional Contact Details --}}
                    <div class="row" style="display: none;">
                        <div class="col-lg-12">
                            <div class="boxed-formctrl">
                                <h5 class="primary-color">@lang('Additional Contact Details'):</h5>
                                <div class="row">
                                <div class="col-lg-4">
                                    <div class="input-effect">
                                     <label class="txtlbl">@lang('Name')</label>    
                                     <input class="primary-input form-control{{ $errors->has('accountant_name') ? ' is-invalid' : '' }}" type="text"  name="accountant_name" value="{{isset($editData)?@$editData->accountant_name:old('accountant_name')}}">
                                         
                                         <span class="focus-border"></span>
                                         @if ($errors->has('payment_terms'))
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $errors->first('payment_terms') }}</strong>
                                         </span>
                                         @endif
                                     </div>
                                 </div>
                 
                                 <div class="col-lg-4">
                                     <div class="input-effect">
                                         <label class="txtlbl">@lang('Email') </label>
                                         <input class="primary-input form-control{{ $errors->has('accountant_email') ? ' is-invalid' : '' }}" type="text"  name="accountant_email" value="{{isset($editData)?@$editData->accountant_email:old('accountant_email')}}">
                                         
                                         <span class="focus-border"></span>
                                         @if ($errors->has('payment_terms'))
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $errors->first('payment_terms') }}</strong>
                                         </span>
                                         @endif
                                     </div>
                                 </div>
                 
                                 <div class="col-lg-4">
                                     <div class="input-effect">
                                         <label class="txtlbl">@lang('Contact Number') </label>
                                         <input class="primary-input form-control{{ $errors->has('accountant_number') ? ' is-invalid' : '' }}" type="text"  name="accountant_number" value="{{isset($editData)?@$editData->accountant_number:old('accountant_number')}}">
                                         
                                         <span class="focus-border"></span>
                                         @if ($errors->has('payment_terms'))
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $errors->first('payment_terms') }}</strong>
                                         </span>
                                         @endif
                                     </div>
                                 </div>
                            </div>
                            
                            <div class="row">                                
                                <div class="col-lg-12 text-right"><br />
                                    <button type="button" class="primary-btn small fix-gr-bg" id="{{@$edit->quotation_type=="equipment"? 'addRowEquipment':'addRowProduct'}}">
                                       <span class="ti-plus pr-2"></span>
                                       @lang('Add') @lang('More')
                                   </button>

                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    {{-- Additional Contact Details --}}


                    {{-- Bank Details --}}
                    <div class="row" style="display: none;">
                        <div class="col-lg-12">
                            <div class="boxed-formctrl">
                                <h5 class="primary-color">@lang('Bank Details'):</h5>
                                <div class="row">
                                <div class="col-lg-4">
                                    <div class="input-effect">
                                     <label class="txtlbl">@lang('Bank Name')</label>    
                                     <input class="primary-input form-control{{ $errors->has('accountant_name') ? ' is-invalid' : '' }}" type="text"  name="accountant_name" value="{{isset($editData)?@$editData->accountant_name:old('accountant_name')}}">
                                         
                                         <span class="focus-border"></span>
                                         @if ($errors->has('payment_terms'))
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $errors->first('payment_terms') }}</strong>
                                         </span>
                                         @endif
                                     </div>
                                 </div>
                 
                                 <div class="col-lg-4">
                                     <div class="input-effect">
                                         <label class="txtlbl">@lang('Branch Name') </label>
                                         <input class="primary-input form-control{{ $errors->has('accountant_email') ? ' is-invalid' : '' }}" type="text"  name="accountant_email" value="{{isset($editData)?@$editData->accountant_email:old('accountant_email')}}">
                                         
                                         <span class="focus-border"></span>
                                         @if ($errors->has('payment_terms'))
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $errors->first('payment_terms') }}</strong>
                                         </span>
                                         @endif
                                     </div>
                                 </div>
                 
                                 <div class="col-lg-4">
                                     <div class="input-effect">
                                         <label class="txtlbl">@lang('Account Number') </label>
                                         <input class="primary-input form-control{{ $errors->has('accountant_number') ? ' is-invalid' : '' }}" type="text"  name="accountant_number" value="{{isset($editData)?@$editData->accountant_number:old('accountant_number')}}">
                                         
                                         <span class="focus-border"></span>
                                         @if ($errors->has('payment_terms'))
                                         <span class="invalid-feedback" role="alert">
                                             <strong>{{ $errors->first('payment_terms') }}</strong>
                                         </span>
                                         @endif
                                     </div>
                                 </div>
                            </div>
                            
                            <div class="row">                                
                                <div class="col-lg-12 text-right"><br />
                                    <button type="button" class="primary-btn small fix-gr-bg" id="{{@$edit->quotation_type=="equipment"? 'addRowEquipment':'addRowProduct'}}">
                                       <span class="ti-plus pr-2"></span>
                                       @lang('Add') @lang('More')
                                   </button>

                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                    {{-- Bank Details --}}

                    {{-- Accountant Details --}}
                    <div class="row" style="display: none;">
                        <div class="col-lg-12">
                            <div class="boxed-formctrl">
                                <h5 class="primary-color">@lang('Accountant Details'):</h5>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="input-effect">
                                         <label class="txtlbl">@lang('Accountant Name')</label>    
                                         <input class="primary-input form-control{{ $errors->has('accountant_name') ? ' is-invalid' : '' }}" type="text"  name="accountant_name" value="{{isset($editData)?@$editData->accountant_name:old('accountant_name')}}">
                                             
                                             <span class="focus-border"></span>
                                             @if ($errors->has('payment_terms'))
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $errors->first('payment_terms') }}</strong>
                                             </span>
                                             @endif
                                         </div>
                                     </div>
                     
                                     <div class="col-lg-4">
                                         <div class="input-effect">
                                             <label class="txtlbl">@lang('Accountant Email') </label>
                                             <input class="primary-input form-control{{ $errors->has('accountant_email') ? ' is-invalid' : '' }}" type="text"  name="accountant_email" value="{{isset($editData)?@$editData->accountant_email:old('accountant_email')}}">
                                             
                                             <span class="focus-border"></span>
                                             @if ($errors->has('payment_terms'))
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $errors->first('payment_terms') }}</strong>
                                             </span>
                                             @endif
                                         </div>
                                     </div>
                     
                                     <div class="col-lg-4">
                                         <div class="input-effect">
                                             <label class="txtlbl">@lang('Accountant Contact Number') </label>
                                             <input class="primary-input form-control{{ $errors->has('accountant_number') ? ' is-invalid' : '' }}" type="text"  name="accountant_number" value="{{isset($editData)?@$editData->accountant_number:old('accountant_number')}}">
                                             
                                             <span class="focus-border"></span>
                                             @if ($errors->has('payment_terms'))
                                             <span class="invalid-feedback" role="alert">
                                                 <strong>{{ $errors->first('payment_terms') }}</strong>
                                             </span>
                                             @endif
                                         </div>
                                     </div>
                                </div>
                                <div class="row">                                
                                    <div class="col-lg-12 text-right"><br />
                                        <button type="button" class="primary-btn small fix-gr-bg" id="{{@$edit->quotation_type=="equipment"? 'addRowEquipment':'addRowProduct'}}">
                                            <span class="ti-plus pr-2"></span>
                                            @lang('Add') @lang('More')
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            </div>
                        </div>
                    {{-- Accountant Details --}}


                        



        </div>
    </div>
</div>
</div>
</div>
</section>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $("#btnSubmit").click(function () {
                setTimeout(function () { disableButton(); }, 0);
            });
            function disableButton() {
                //$("#btnSubmit").prop('disabled', true);
            }
        });
    </script>
@endsection

*/ ?>
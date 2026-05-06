@extends('backEnd.masterpage')
@section('mainContent')

@php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<?php try { ?>
<div class="container-fluid">
    <div class="d-sm-flex justify-content-between">
        <div class="mb-3">
            <h2 class="page-heading m-0">Import Supplier</h2>
            <span class="page-label">Home - Import Supplier</span>
        </div>
        <div>
            <a href="{{ url('add-supplier') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New Supplier</a>
            <a href="{{ url('suppliers') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Supplier List</a>
            <a href="{{ url('supplier-import') }}" type="button" class="btn btn-warning"><i class="far fa fa-plus" aria-hidden="true"></i> Import</a>
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    {{ Form::open(['class' => 'form-horizontal','url' => 'supplier-import-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <div class="boxed-formctrl">
                        <div class="add-visitor">
                            <div class="row mb-10">
                                <div class="col-lg-12">
                                    @if (session()->has('message-success'))
                                        <div class="alert alert-success mb-20">
                                            {{ session()->get('message-success') }}
                                        </div>
                                    @elseif(session()->has('message-danger'))
                                        <div class="alert alert-danger">
                                            {{ session()->get('message-danger') }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="col-lg-3 mb-2">
                                    <div class="input-effect">
                                        <label class="txtlbl">Choose File<span>*.csv</span> (<a href="{{ url('public/uploads/product_upload/supplier_import_sample_file.xlsx') }}" target="_blank">Sample File</a>)</label>
                                        <input class="form-control" type="file" accept=".csv" name="import_file" required>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-effect">
                                        <label class="txtlbl"><br />
                                        <button class="btn btn-primary mt-2">
                                            <span class="ti-check"></span> Submit
                                        </button>
                                        @if (count($data)>0)
                                        <a href="{{ url('supplier-import-clear') }}" class="btn btn-info mt-2">Clear Data</a> @endif
                                    </div>
                                </div>
                            </div>                            
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>


                
                <div class="col-lg-12" style="overflow: scroll;">
                    <table class="table table-bordered table-striped" cellspacing="0">
                            <thead>
                                @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                    <tr>
                                        <td colspan="11">
                                            @if (session()->has('message-success-delete'))
                                                <div class="alert alert-success">
                                                    {{ session()->get('message-success-delete') }}
                                                </div>
                                            @elseif(session()->has('message-danger-delete'))
                                                <div class="alert alert-danger">
                                                    {{ session()->get('message-danger-delete') }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th width="">customer_salutation</th>
                                    <th width="">first_name</th>
                                    <th width="">last_name</th>
                                    <th width="">name</th>
                                    <th width="">customer_name_display</th>
                                    <th width="">address</th>
                                    <th width="">address2</th>
                                    <th width="">country</th>
                                    <th width="">state</th>
                                    <th width="">city</th>
                                    <th width="">zip_code</th>
                                    <th width="">contcat_person_salutation</th>
                                    <th width="">contcat_person_first_name</th>
                                    <th width="">contcat_person_last_name</th>
                                    <th width="">designation</th>
                                    <th width="">contcat_number</th>
                                    <th width="">mobile</th>
                                    <th width="">email</th>
                                    <th width="">sales_person</th>
                                    <th width="">purchase_type</th>
                                    <th width="">supplier_type</th>
                                    <th width="">vat_country</th>
                                    <th width="">vat_percentage</th>
                                    <th width="">vat_is_fixed</th>
                                    <th width="">vat_number</th>
                                    <th width="">credit_limit</th>
                                    <th width="">credit_days</th>
                                    <th width="">payment_terms</th>
                                    <th width="">transaction_type</th>
                                </tr>
                            </thead>

                            <tbody>
                                @if (count($data)>0)
                                @php
                                $customer_id = "";
                                        $sales_person_id = "";
                                        $customer_type_id = "";
                                        $sale_type_id = "";
                                        $payment_terms_id = "";
                                        $country_id = "";
                                        $state_id = ""; @endphp
                                    @foreach ($data as $value)
                                    @php
                                        $customer_id = $customer->where('name',$value->name)->max('name');
                                        $sales_person_id = $sales_person->where('first_name',$value->sales_person)->max('user_id');
                                        $supplier_type_id = $supplier_type->where('title',$value->supplier_type)->max('id');
                                        $purchase_type_id = $purchase_type->where('title',$value->purchase_type)->max('id');
                                        $payment_terms_id = $paymentterms->where('title',$value->payment_terms)->max('id');
                                        $country_id = $country->where('name',$value->country)->max('id');
                                        $state_id = $state->where('name',$value->state)->max('id');
                                    @endphp
                                        <tr>
                                            <td>{{ @$value->customer_salutation }}</td>
                                            <td>{{ @$value->first_name }}</td>
                                            <td>{{ @$value->last_name }}</td>
                                            <td @if($customer_id != "") class="bg-warning" @endif >{{ @$value->name }}</td>
                                            <td>{{ @$value->customer_name_display }}</td>
                                            <td>{{ @$value->address }}</td>
                                            <td>{{ @$value->address2 }}</td>
                                            <td @if($country_id == "") class="bg-warning" @endif>{{ @$value->country }}</td>
                                            <td @if($state_id == "") class="bg-warning" @endif>{{ @$value->state }}</td>
                                            <td>{{ @$value->city }}</td>
                                            <td>{{ @$value->zip_code }}</td>
                                            <td>{{ @$value->contcat_person_salutation }}</td>
                                            <td>{{ @$value->contcat_person_first_name }}</td>
                                            <td>{{ @$value->contcat_person_last_name }}</td>
                                            <td>{{ @$value->designation }}</td>
                                            <td>{{ @$value->contcat_number }}</td>
                                            <td>{{ @$value->mobile }}</td>
                                            <td>{{ @$value->email }}</td>
                                            <td @if($sales_person_id == "") class="bg-warning" @endif>{{ @$value->sales_person }}</th>
                                            <td @if($purchase_type_id == "") class="bg-warning" @endif>{{ @$value->purchase_type }}</th>
                                            <td @if($supplier_type_id == "") class="bg-warning" @endif>{{ @$value->supplier_type }}</td>
                                            <td>{{ @$value->vat_country }}</td>
                                            <td>{{ @$value->vat_percentage }}</td>
                                            <td>{{ @$value->vat_is_fixed }}</td>
                                            <td>{{ @$value->vat_number }}</td>
                                            <td>{{ @$value->credit_limit }}</td>
                                            <td>{{ @$value->credit_days }}</td>
                                            <td @if ($payment_terms_id == "") class="bg-warning" @endif>{{ @$value->payment_terms }}</td>
                                            <td>{{ @$value->transaction_type }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <?php try{ ?>
                            <footer>
                                <tr>
                                    <td colspan="11">
                                        
                                    </td>
                                </tr>
                            </footer>
                            <?php }catch (\Exception $e) { } ?>
                        </table>
                </div>
                @if (count($data)>0)
                <div class="col-lg-12 text-center">
                    {{ Form::open(['class' => 'form-horizontal','url' => 'supplier-import-data', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                        @if (session()->has('message-success'))
                            <div class="alert alert-success mb-20">
                                {{ session()->get('message-success') }}
                            </div>
                        @elseif(session()->has('message-danger'))
                            <div class="alert alert-danger">
                                {{ session()->get('message-danger') }}
                            </div>
                        @endif
                            <button class="btn btn-danger mt-2">
                                <span class="ti-check"></span> Import Data
                            </button>
                    </div>
                    {{ Form::close() }}
                </div>
                @endif

            </div>
        </div>
    </div>    

</div>
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection

@section('script')
    <script>

$(document).ready(function()
    {
        // Stop user to press enter in textbox
        $("input:text").keypress(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
});

    </script>
@endsection

@extends('backEnd.newmasterpage')
@section('mainContent')

    <style>
        .venus-app .table.table-hover td {
            background-color: inherit;
            padding: 5px 5px;
            vertical-align: middle;
        }
    </style>

    <script>

        let isFullList = false;

        function list_style_new() {
            const leftNav = document.querySelector('.left-nav');
            const content = document.querySelector('.content-container');

            if (!isFullList) {
                // Switch to FULL LIST VIEW
                isFullList = true;

                leftNav.classList.remove('col-3');
                leftNav.classList.add('col-12');
                leftNav.style.width = '100%';

                content.classList.add('d-none');

                $('#long-list').removeClass('d-none');
                $('#short-list').addClass('d-none');

                $('#filters-long').removeClass('d-none');
                $('#filters-short').addClass('d-none');
            } else {
                // Switch to COMPACT VIEW
                isFullList = false;

                leftNav.classList.remove('col-12');
                leftNav.classList.add('col-3');
                leftNav.style.width = '';

                content.classList.remove('d-none');

                $('#long-list').addClass('d-none');
                $('#short-list').removeClass('d-none');

                $('#filters-short').removeClass('d-none');
                $('#filters-long').addClass('d-none');
            }
        }


        //added ny kp
        function toggleLongFilters() {
            document.querySelectorAll('#filters-long .filter-field').forEach(el => {
                el.classList.toggle('d-none');
            });
        }

    </script>

    <style>
        .venus-app .table.table-hover td {
            padding: 1px 5px;
        }
    </style>

    <style>
        #long-list td,
        #long-list th {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #long-list tr.expand td {
            white-space: normal !important;
            overflow: visible !important;
            text-overflow: unset !important;
            height: auto !important;
        }

        /* Optional for pointer on rows */
        #long-list tbody tr {
            cursor: pointer;
        }
    </style>

    <script>
        $(document).ready(function () {
            $('#long-list tbody tr').on('click', function () {
                $(this).toggleClass('expand');
            });
        });
    </script>



    <?php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
                                                                                                                                                                                                                                                                ?>

    <?php try { ?>







    <div class="content-container col-9">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="tab-pane fade show active" id="purchase-order-1" role="tabpanel"
                aria-labelledby="purchase-order-1-tab">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        Import Supplier
                    </h4>
                    <div class="purchase-order-content-header-right">

                        

                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                {{ Form::open(['class' => 'form-horizontal', 'url' => 'supplier-import-list', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
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
                                                    <label class="txtlbl">Choose File<span>*.csv</span> (<a
                                                            href="{{ url('public/uploads/product_upload/supplier_import_sample_file.xlsx') }}"
                                                            target="_blank">Sample File</a>)</label>
                                                    <input class="form-control" type="file" accept=".csv" name="import_file"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="input-effect d-flex gap-2" style="margin-top: 12px">
                                                    
                                                        <button class="btn btn-primary rounded-0 mt-2">
                                                            <span class="ti-check"></span> Submit
                                                        </button>
                                                        @if (count($data) > 0)
                                                            <a href="{{ url('supplier-import-clear') }}"
                                                        class="btn btn-info rounded-0 mt-2">Clear Data</a> @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>



                            <div class="col-lg-12 " id="long-list" style="overflow: scroll;">
                                <table class="table table-hover" cellspacing="0">
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
                                        <tr class="text-center">
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
                                        @if (count($data) > 0)
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
                                                    $customer_id = $customer->where('name', $value->name)->max('name');
                                                    $sales_person_id = $sales_person->where('first_name', $value->sales_person)->max('user_id');
                                                    $supplier_type_id = $supplier_type->where('title', $value->supplier_type)->max('id');
                                                    $purchase_type_id = $purchase_type->where('title', $value->purchase_type)->max('id');
                                                    $payment_terms_id = $paymentterms->where('title', $value->payment_terms)->max('id');
                                                    $country_id = $country->where('name', $value->country)->max('id');
                                                    $state_id = $state->where('name', $value->state)->max('id');
                                                @endphp
                                                <tr>
                                                    <td>{{ @$value->customer_salutation }}</td>
                                                    <td>{{ @$value->first_name }}</td>
                                                    <td>{{ @$value->last_name }}</td>
                                                    <td @if($customer_id != "") class="bg-warning" @endif>{{ @$value->name }}</td>
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
                                                    <td @if($sales_person_id == "") class="bg-warning" @endif>
                                                        {{ @$value->sales_person }}</th>
                                                    <td @if($purchase_type_id == "") class="bg-warning" @endif>
                                                        {{ @$value->purchase_type }}</th>
                                                    <td @if($supplier_type_id == "") class="bg-warning" @endif>
                                                        {{ @$value->supplier_type }}</td>
                                                    <td>{{ @$value->vat_country }}</td>
                                                    <td>{{ @$value->vat_percentage }}</td>
                                                    <td>{{ @$value->vat_is_fixed }}</td>
                                                    <td>{{ @$value->vat_number }}</td>
                                                    <td>{{ @$value->credit_limit }}</td>
                                                    <td>{{ @$value->credit_days }}</td>
                                                    <td @if ($payment_terms_id == "") class="bg-warning" @endif>
                                                        {{ @$value->payment_terms }}</td>
                                                    <td>{{ @$value->transaction_type }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <?php    try { ?>
                                    <footer>
                                        <tr>
                                            <td colspan="11">

                                            </td>
                                        </tr>
                                    </footer>
                                    <?php    } catch (\Exception $e) {
        } ?>
                                </table>
                            </div>
                            @if (count($data) > 0)
                                    <div class="col-lg-12 text-center">
                                        {{ Form::open(['class' => 'form-horizontal', 'url' => 'supplier-import-data', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                        @if (session()->has('message-success'))
                                            <div class="alert alert-success mb-20">
                                                {{ session()->get('message-success') }}
                                            </div>
                                        @elseif(session()->has('message-danger'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger') }}
                                            </div>
                                        @endif
                                      
    <div class="d-flex justify-content-center mt-2">
        <button class="btn btn-danger rounded-0">
            <span class="ti-check"></span> Import Data
        </button>
    </div>
                                    </div>
                                    {{ Form::close() }}
                                </div>
                            @endif

                    </div>
                </div>
            </div>

        </div>

    </div>
    </div>




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

    <?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


@endsection
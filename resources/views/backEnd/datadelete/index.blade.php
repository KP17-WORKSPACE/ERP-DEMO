@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <style>
        .border {
            border: solid 1px #deebe1;
            border-radius: 10px;
            padding: 10px;
        }

        .col-4 {
            flex: 0 0 auto;
            width: 31.5%;
        }
    </style>

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        Delete All Data
                    </h4>
                </div>



                <div class="card mb-3">
                    <div class="card-body">

                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delete-data-by-table', 'method' => 'POST', 'id' => 'crm-deals-form', 'novalidate' => true]) }}

                        <div class="row">

                            <div class="col-2 border m-2">
                                <div class="form-group">
                                    <!-- Select All for Purchase -->
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all-accounts">
                                        <label class="form-check-label" for="select-all-accounts"><strong>Chart of
                                                Accounts</strong></label>
                                    </div>

                                    <!-- Individual Purchase options -->
                                    <div class="form-check">
                                        <input class="form-check-input chart-checkbox" type="checkbox" name="heads" id="heads"
                                            value="heads">
                                        <label class="form-check-label" for="heads">Main Heads</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input chart-checkbox" type="checkbox" name="groups" id="groups"
                                            value="groups">
                                        <label class="form-check-label" for="groups">Groups</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input chart-checkbox" type="checkbox" name="subgroups"
                                            id="subgroups" value="subgroups">
                                        <label class="form-check-label" for="subgroups">Sub Groups</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input chart-checkbox" type="checkbox" name="accounts"
                                            id="accounts" value="accounts">
                                        <label class="form-check-label" for="accounts">Accounts</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input chart-checkbox" type="checkbox" name="subaccounts"
                                            id="subaccounts" value="subaccounts">
                                        <label class="form-check-label" for="subaccounts">Sub Accounts</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input chart-checkbox" type="checkbox"
                                            name="chartofaccounts_invoice_import" id="chartofaccounts_invoice_import"
                                            value="chartofaccounts_invoice_import">
                                        <label class="form-check-label" for="chartofaccounts_invoice_import">Chartofaccounts
                                            Invoice
                                            Import</label>
                                    </div>


                                    <div class="form-check">
                                        <input class="form-check-input chart-checkbox" type="checkbox" name="cheque_book"
                                            id="cheque_book" value="cheque_book">
                                        <label class="form-check-label" for="cheque_book">Cheque Book</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input chart-checkbox" type="checkbox" name="stl" id="stl"
                                            value="stl">
                                        <label class="form-check-label" for="stl">STL</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-2 border m-2">
                                <div class="form-group">
                                    <!-- Select All for Purchase -->
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all-crm">
                                        <label class="form-check-label" for="select-all-crm"><strong>CRM</strong></label>
                                    </div>

                                    <!-- Individual Purchase options -->
                                    <div class="form-check">
                                        <input class="form-check-input crm-checkbox" type="checkbox" name="leads" id="leads"
                                            value="leads">
                                        <label class="form-check-label" for="leads">Leads</label>
                                    </div>

                                    <!-- Individual Purchase options -->
                                    <div class="form-check">
                                        <input class="form-check-input crm-checkbox" type="checkbox" name="deals" id="deals"
                                            value="deals">
                                        <label class="form-check-label" for="deals">Deals</label>
                                    </div>

                                
                                </div>
                            </div>


                        </div>



                        <div class="col-12 d-flex justify-content-center">
                            <button type="submit" class="btn btn-light">Submit</button>
                        </div>
                        {{ Form::close() }}

                        <br>


                        <div class="row">
                            <div class="col-12 p-3">
                                <div class="white-box">
                                    <div class="row">
                                        <div class="col-1 p-3">Backup List:</div>
                                        <div class="col-11 p-3">
                                            @if(count($backup_folders) > 0)
                                                <ul>
                                                    @foreach($backup_folders as $folder)
                                                        <li>
                                                            <a href="{{url('restore-data-all/' . $folder)}}"
                                                                onclick="return loader()">{{ $folder }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p>No backup folders found.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 p-3">
                                <div class="white-box">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <h5 class="mb-3">Database: <strong>{{ $databaseName ?? 'Unknown' }}</strong>
                                            </h5>
                                            @if (!empty($tableRecords))
                                                <form id="delete-table-data-form" action="{{ url('delete-table-data') }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Delete data from selected tables?');">
                                                    @csrf
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <div>
                                                            <input type="checkbox" id="select-all-tables">
                                                            <label for="select-all-tables" class="mb-0">Select All</label>
                                                        </div>
                                                        <button type="submit" class="btn btn-danger btn-sm">Delete Selected
                                                            Table Data</button>
                                                    </div>

                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-sm mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center" style="width: 50px;">#</th>
                                                                    <th class="text-center" style="width: 40px;"> </th>
                                                                    <th>Table Name</th>
                                                                    <th style="width: 120px;">Record Count</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($tableRecords as $index => $table)
                                                                    <tr>
                                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                                        <td class="text-center">
                                                                            <input type="checkbox" name="table_names[]"
                                                                                value="{{ $table['name'] }}"
                                                                                class="table-select-checkbox">
                                                                        </td>
                                                                        <td>{{ $table['name'] }}</td>
                                                                        <td>{{ $table['count'] !== null ? number_format($table['count']) : 'N/A' }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </form>
                                            @else
                                                <p class="mb-0">No table information available.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var selectAllAccounts = document.getElementById('select-all-accounts');
                        if (selectAllAccounts) {
                            selectAllAccounts.addEventListener('change', function () {
                                var accountCheckboxes = document.querySelectorAll('.form-group .chart-checkbox');
                                accountCheckboxes.forEach(function (checkbox) {
                                    checkbox.checked = selectAllAccounts.checked;
                                });
                            });
                        }

                        var selectAllCrm = document.getElementById('select-all-crm');
                        if (selectAllCrm) {
                            selectAllCrm.addEventListener('change', function () {
                                var crmCheckboxes = document.querySelectorAll('.form-group .crm-checkbox');
                                crmCheckboxes.forEach(function (checkbox) {
                                    checkbox.checked = selectAllCrm.checked;
                                });
                            });
                        }
                    });
                </script>

            </div>
        </div>
    </div>




@endsection
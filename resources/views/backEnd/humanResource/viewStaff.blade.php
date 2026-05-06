@extends('backEnd.newmasterpage')
@section('mainContent')
    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        User Details
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light text-dark" href="{{ url('add-staff') }}">
                            <i class="ico icon-outline-add-square text-success"></i> User
                        </a>
                        <a class="btn btn-light" href="{{ url('staff-directory') }}">
                            View Users
                        </a>
                        <a class="btn btn-light text-dark" href="{{ url('edit-staff/' . $staffDetails->id) }}">
                            <i class="ico icon-outline-pen-2 text-success" style="font-size: 16px;"></i> Edit
                        </a>
                    </div>
                </div>

                <style>
                    .profile-table {
                        border: none !important;
                    }

                    .profile-table td {
                        padding: 0.75rem 1rem;
                        vertical-align: middle;
                    }

                    .profile-table tr td:first-child {
                        font-weight: 600;
                        color: #495057;
                        width: 200px;
                        background: #f8f9fa;
                    }

                    tbody,
                    td,
                    tfoot,
                    th,
                    thead,
                    tr {
                         border-color: none; 
                        border-style: none; 
                         border-width: 0; 
                    }

                    .profile-table tr td:last-child {
                        color: #212529;
                    }
                </style>


                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-2" style="text-align: center">

                                <div class="student-meta-box">
                                    <div class="student-meta-top"></div>

                                    <img class="student-meta-img img-fluid"
                                        src="{{ file_exists(@$staffDetails->staff_photo) ? asset($staffDetails->staff_photo) : asset('public/uploads/staff/demo/staff.png') }}"
                                        alt="" width="190px;">


                                </div>
                            </div>


                            <div class="col-lg-10">

                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#studentProfile" role="tab"
                                            data-toggle="tab">Personal Info</a>
                                    </li>
                                </ul>


                                <div class="tab-content">

                                    <div role="tabpanel" class="tab-pane fade show active" id="studentProfile">
                                        <div class="white-box">

                                            <table class="table profile-table" width="100%" cellspacing="0">
                                                <tr>
                                                    <td width="200px">Mobile</td>
                                                    <td>
                                                        @if (isset($staffDetails))
                                                            {{ @$staffDetails->mobile }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Emergency Mobile</td>
                                                    <td>
                                                        {{ @$staffDetails->emergency_mobile ?? '--' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Email</td>
                                                    <td>
                                                        {{ @$staffDetails->email ?? '--' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Gender</td>
                                                    <td>{{ @$staffDetails->genders->base_setup_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Ext No</td>
                                                    <td>{{ @$staffDetails->ext_no ?? '--' }}</td>
                                                </tr>
                                            </table>





                                        </div>
                                    </div>




                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="white-box">
                                    <table class="table profile-table" width="100%" cellspacing="0">
                                        <tr>
                                            <td width="150px">Staff Name</td>
                                            <td class="font-weight-bold">
                                                @if (isset($staffDetails))
                                                    {{ @$staffDetails->full_name }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Role</td>
                                            <td>{{ @$staffDetails->roles->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Company</td>
                                            <td>{{ @$staffDetails->company->company_name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Company Access</td>
                                            <td>
                                                @if (count($company_access_list) > 0)
                                                    @foreach ($company_access_list as $list)
                                                        <div style="font-size:11px;padding:0.25em 0.4em;background-color:#cfe2ff"
                                                            class="btn-xs d-inline-block  pr-1 pl-1 m-1">
                                                            {{ $list->company_name }}</div>
                                                    @endforeach
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Main Company</td>
                                            <td>
                                                <span style="font-size:11px;padding:0.25em 0.4em;background-color:#d4edda"
                                                    class="btn-xs d-inline-block  pr-1 pl-1 m-1">{{ @$staffDetails->maincompany->company_name }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Designation</td>
                                            <td>{{ @$staffDetails->designations->title }}</td>
                                        </tr>
                                        <tr>
                                            <td>Department</td>
                                            <td>{{ @$staffDetails->departments->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Staff id</td>
                                            <td>{{ @$staffDetails->staff_no }} </td>
                                        </tr>
                                        <tr>
                                            <td>Date of Joining</td>
                                            <td>
                                                @if (isset($staffDetails))
                                                    {{ date('jS M, Y', strtotime(@$staffDetails->date_of_joining)) }}
                                                @endif
                                            </td>
                                        </tr>
                                    </table>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
@endsection

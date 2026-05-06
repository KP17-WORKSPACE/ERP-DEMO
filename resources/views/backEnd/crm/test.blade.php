@extends('backEnd.masterpage')
@section('mainContent')
    @php
        $modules = [];
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
        foreach ($permissions as $permission) {
            @$module_links[] = @$permission->module_link_id;
            @$modules[] = @$permission->moduleLink->module_id;
        }
        $modules = array_unique(@$modules);
        $generalSetting = App\SmGeneralSettings::where('id', 1)->first();
        $currency_symbol = @$generalSetting->currency_symbol;
        
        if (isset($generalSetting->logo)) {
            @$logo = @$generalSetting->logo;
        } else {
            $logo = 'public/uploads/settings/logo.png';
        }
        
        $sm_staff = App\SmStaff::where('user_id', Auth::user()->id)->first();
        if (!empty(@$sm_staff)) {
            @$profile_image = @$sm_staff->staff_photo;
            if (empty(@$profile_image)) {
                @$profile_image = 'public/uploads/staff/staff1.jpg';
            }
        }
    @endphp







    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3">
                <h2 class="page-heading m-0">Deal</h2>
                <span class="page-label">Home - Deal</span>
            </div>
            <div>
                <button class="btn-topnav" type="button" class="btn btn-primary" data-toggle="modal" data-target="#adddeal">
                    <i class="fa fa-plus mr-1"></i> Add User</button>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100 bg-1">
                    <h2 class="head">Deal Info <span class="bg-primary btn-small text-white">Corprate</span>
                    </h2>
                    <p class="mb-2 text-white-50">Gulf Medical</p>
                    <span class="mb-1">Deal Value : 1124554114</span>
                    <div class="text-capitalize">stage : <b class="">Quote</b> <span
                            class="edit btn-badge rejected py-1 px-3 font-weight-bold">Edit</span> </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Owner Info</h2>
                    <h6 class="sub-head text-capitalize text-dark">Shaji jeorge thomas</h6>
                    <p class="mb-2 text-gray-500">Added On : 12/2/2012 | Sourse : Mail</p>
                    <span class="mb-1"> <span class="font-semibold">Mob Num :</span> 7588958888</span>
                    <span class="mb-1"><span class="font-semibold">Mail :</span> suort@gmail.com</span>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Customer Info </h2>
                    <h6 class="sub-head text-capitalize text-dark">Mex Tec Chemical</h6>
                    <p class="mb-1">Added On : 12/2/2012 | Sourse : Mail</p>
                    <p class="mb-2">Added On : 12/2/2012 | Sourse : Mail</p>
                </div>
            </div>
        </div>

        <div class="card p-4 mb-4 ">
            <h2 class="page-heading mb-3">Submited</h2>
            <div class="border bg__light p-4">
                <div class="row">
                    <div class="col-lg-2 col-md-3 col-sm-4">
                        <div class="">
                            <h6 class="sub-head mb-1">Expected Delivery Date</h6>
                            <p class="text-muted">28-Nov-2021</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4">
                        <div class="">
                            <h6 class="sub-head mb-1">Peymant Terms</h6>
                            <p class="text-muted">28-Nov-2021</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4">
                        <div class="">
                            <h6 class="sub-head mb-1">Payment mode</h6>
                            <p class="text-muted">28-Nov-2021</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4">
                        <div class="">
                            <h6 class="sub-head mb-1">Payment Purchase</h6>
                            <p class="text-muted">28-Nov-2021</p>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4">
                        <div class="">
                            <h6 class="sub-head mb-1">LPO</h6>
                            <button class="btn btn-sm btn-primary"> <i class="fa fa-download"></i> </button>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4">
                        <div class="">
                            <h6 class="sub-head mb-1">Cheque//TT Copy</h6>
                            <p class="text-muted">28-Nov-2021</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="">
                            <h6 class="sub-head mb-1">Puchase Quote</h6>
                            <button class="btn btn-sm btn-primary"> <i class="fa fa-download mr-2"></i>Puchase Quote
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="">
                            <h6 class="sub-head mb-1">Puchase Quote</h6>
                            <button class="btn btn-sm btn-dark"> <i class="fa fa-download mr-2"></i>Quatation</button>
                            <button class="btn btn-sm btn-info"> <i class="fa fa-download mr-2"></i>VAT
                                Excluded</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 mb-3">
                <div class="card p-4">
                    <h2 class="page-heading mb-3">Account Status</h2>
                    <div>
                        <button class="btn btn-success btn-block mb-2">Approved</button>
                        <p class="my-1 mb-3"><b>Customer Status : </b><span class="check_bg">Approved <i
                                    class="fa fa-check"></i></span> </p>
                        <p class="my-1 mb-3"><b>Credit Limit : </b><span class="check_bg">Approved <i
                                    class="fa fa-check"></i></span> </p>
                        <p class="my-1 mb-3"><b>Payment Terms: </b><span class="check_bg">Approved <i
                                    class="fa fa-check"></i></span> </p>
                        <p class="my-1 mb-3"><b>Overdue Payment : </b><span class="check_bg">No <i
                                    class="fa fa-check"></i></span> </p>
                        <p class="my-1 mb-3"><b>Other: </b><span class="check_bg">Approved <i
                                    class="fa fa-check"></i></span> </p>
                        <p><b>Remarks : </b><span class="">Pc On Delivery</span> </p>
                    </div>

                </div>
            </div>
            <div class="col-lg-3 mb-3">
                <div class="card p-4">
                    <h2 class="page-heading mb-3">Sales Status</h2>
                    <div>
                        <button class="btn btn-success btn-block mb-2">Approved</button>
                        <p class="my-1 mb-3"><b>Customer Status : </b><span class="check_bg">Approved <i
                                    class="fa fa-check"></i></span> </p>
                        <p class="my-1 mb-3"><b>Credit Limit : </b><span class="check_bg">Approved <i
                                    class="fa fa-check"></i></span> </p>
                        <p class="my-1 mb-3"><b>Payment Terms: </b><span class="check_bg">Approved <i
                                    class="fa fa-check"></i></span> </p>
                        <p class="my-1 mb-3"><b>Overdue Payment : </b><span class="check_bg">No <i
                                    class="fa fa-check"></i></span> </p>
                        <p class="my-1 mb-3"><b>Other: </b><span class="check_bg">Approved <i
                                    class="fa fa-check"></i></span> </p>
                        <p><b>Remarks : </b><span class="">Pc On Delivery</span> </p>
                    </div>

                </div>
            </div>
            <div class="col-lg-3 mb-3">
                <div class="card p-4 h-100">
                    <h2 class="page-heading mb-3">Purchase Status</h2>
                    <div>
                        <button class="btn btn-info btn-block mb-2">Waiting For Approval</button>
                    </div>

                </div>
            </div>
            <div class="col-lg-3 mb-3">
                <div class="card p-4 h-100">
                    <h2 class="page-heading mb-3">Invoice Status</h2>
                    <div>
                        <button class="btn btn-info btn-block mb-2">Waiting For Approval</button>
                    </div>

                </div>
            </div>
            <div class="col-lg-3 mb-3">
                <div class="card p-4">
                    <h2 class="page-heading mb-3">Delivery Status</h2>
                    <div>
                        <button class="btn btn-info btn-block mb-2">Waiting For Approval</button>
                    </div>

                </div>
            </div>
            <div class="col-lg-3 mb-3">
                <div class="card p-4">
                    <h2 class="page-heading mb-3">Receivales Status</h2>
                    <div>
                        <button class="btn btn-info btn-block mb-2">Waiting For Approval</button>
                    </div>

                </div>
            </div>
        </div>



        <div class="card p-4 mb-4">
            <h2 class="page-heading mb-3">For Parchase Approval</h2>
            <div class="border p-4">
                <div class="row">
                    <div class="col-lg-3 mb-3">
                        <label for="" class="form-check-label">Purchase Quote</label>
                        <select name="" class="form-control" id="">
                            <option value="Select">Select</option>
                        </select>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label for="" class="form-check-label">Purchase Quote</label>
                        <select name="" class="form-control" id="">
                            <option value="Select">Select</option>
                        </select>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label for="" class="form-check-label">Purchase Quote</label>
                        <select name="" class="form-control" id="">
                            <option value="Select">Select</option>
                        </select>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <label for="" class="form-check-label">Purchase Quote</label>
                        <select name="" class="form-control" id="">
                            <option value="Select">Select</option>
                        </select>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <label for="" class="form-check-label">Purchase Quote</label>
                        <input type="file" class="form-control" name="" id="">
                    </div>
                    <div class="col-lg-4 mb-3">
                        <label for="" class="form-check-label">Purchase Quote</label>
                        <input type="file" class="form-control" name="" id="">
                    </div>
                    <div class="col-lg-4 mb-3">
                        <label for="" class="form-check-label">Purchase Quote</label>
                        <input type="file" class="form-control" name="" id="">
                    </div>
                    <div class="col-lg-12 mb-3">
                        <label for="" class="form-check-label">Purchase Quote</label>
                        <textarea name="" id="" class="form-control" cols="30" rows="4"></textarea>
                    </div>
                    <div class="col-md-12 justify-content-end d-flex">
                        <button class="btn btn-primary">Submit</button>
                    </div>


                </div>
            </div>
        </div>



        <div class="row">
            <div class="col-lg-6 mb-3 h-100">
                <div class="p-4 card">
                    <div>
                        <label for="" class="font-weight-bold">Internal Note</label>
                        <textarea name="" class="form-control" id="" cols="10" rows="3"></textarea>
                        <div class="mt-2 justify-content-end d-flex">
                            <button class=" btn-small">Save</button>
                        </div>
                    </div>
                    <div class="notes border py-2 px-3 mt-3">
                        <div>
                            <p class="mb-0">Item comes ready to delivery today</p>
                            <p class="text-muted text-right">Moncs on 18/08/2022 12:54 PM</p>
                        </div>
                        <hr>
                        <div>
                            <p class="mb-0">Item comes ready to delivery today</p>
                            <p class="text-muted text-right">Moncs on 18/08/2022 12:54 PM</p>
                        </div>
                        <hr>

                    </div>
                </div>
            </div>
            <div class="col-lg-6 ">

                <div class="p-4 card bg-2 ">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class=head>Delivery Location /Address</h2>
                        <span class="bg-white btn-small text-dark">Edit</span>
                    </div>
                    <div class="row">
                        <div class="mb-1 col-4"> <b> Company </b></div>
                        <span class="col-8">: Company Name</span>
                    </div>
                    <div class="row">
                        <div class="mb-1 col-4"> <b>Address </b></div>
                        <span class="col-8">: Nashik Mumbai</span>
                    </div>
                    <div class="row">
                        <div class="mb-1 col-4"> <b>Contact Person</b></div>
                        <span class="col-8">: james</span>
                    </div>
                    <div class="row">
                        <div class="mb-1 col-4"> <b>Mob Num</b></div>
                        <span class="col-8">: +91 788985887</span>
                    </div>
                    <div class="row">
                        <div class="mb-1 col-4"> <b>Email </b></div>
                        <span class="col-8">: email@gmail.com</span>
                    </div>

                </div>

            </div>

            <div class="col-lg-6 h-100 mb-3">

            </div>

        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center py-4">
                        <h4 class="header-title m-0">Quote items</h4>
                        <!-- <button class=" btn-small">View All</button> -->
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>Deal Id</th>
                                        <th>Company</th>
                                        <th>Owner</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Senior Sales Executive </td>
                                        <td>Ananthu</td>
                                        <td>11/2/2022</td>
                                        <td>Rejected</span>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>Senior Sales Executive </td>
                                        <td>Ananthu</td>
                                        <td>11/2/2022</td>
                                        <td>Rejected</span>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>Senior Sales Executive </td>
                                        <td>Ananthu</td>
                                        <td>11/2/2022</td>
                                        <td>Rejected</span>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>Senior Sales Executive </td>
                                        <td>Ananthu</td>
                                        <td>11/2/2022</td>
                                        <td>Rejected</span>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>Senior Sales Executive </td>
                                        <td>Ananthu</td>
                                        <td>11/2/2022</td>
                                        <td>Rejected</span>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

                    </div> <!-- end card-body-->
                </div> <!-- end card-->

            </div>
        </div>

    </div>
@endsection

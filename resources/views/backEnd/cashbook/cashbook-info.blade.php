<?php
$module_links = [];
$permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
?>

<?php try { ?>

 <div class="purchase-order-content-header">
                                <h4 class="purchase-order-content-header-left">
                                    Supplier Info (SUPS-1060)
                                </h4>
                                <div class="purchase-order-content-header-right">
                                    <button class="btn btn-light">
                                        <i class="ico icon-outline-pen-2"></i> Edit
                                    </button>
                                    <button class="btn btn-dark">
                                        <i class="ico icon-outline-add-circle"></i> Add
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="ico icon-outline-hamburger-menu"></i>
                                        </button>
                                        <ul class="dropdown-menu" style="">
                                            <li><a class="dropdown-item" href="#">
                                                    <i class="ico icon-outline-minimize-square"></i>
                                                    Merge</a></li>
                                            <li><a class="dropdown-item" href="#">
                                                    <i class="ico icon-outline-import text-success"></i>
                                                    Import</a></li>
                                            <li><a class="dropdown-item" href="#"><i
                                                        class="ico icon-outline-trash-bin-minimalistic text-danger"></i>
                                                    Delete</a></li>

                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-3">
                                <div class="card-body">
                                    <!-- <h4 class="mb-3 color-sub-head">Supplier Info (SUPS-1060)</h4> -->
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="font-weight-600 title-15 me-3 text-primary">COMPUTERS TRADING LLC
                                        </div>
                                        <div class="badge bg-info-light">Active</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-2 mb-3">
                                            <label class="form-label">Name:</label>
                                            <div class="form-control-plaintext">Mr Hameed</div>
                                        </div>
                                        <div class="col-2 mb-3">
                                            <label class="form-label">Designation:</label>
                                            <div class="form-control-plaintext">Managing Director</div>
                                        </div>

                                        <div class="col-2 mb-3">
                                            <label class="form-label">Contact Number:</label>
                                            <div class="form-control-plaintext">+971 04 3522433</div>
                                        </div>
                                        <div class="col-2 mb-3">
                                            <label class="form-label">Mobile:</label>
                                            <div class="form-control-plaintext">+971 04 3522432</div>
                                        </div>
                                        <div class="col-2 mb-3">
                                            <label class="form-label">Mail: </label>
                                            <div class="form-control-plaintext">info@additixcom</div>
                                        </div>
                                        <div class="col-2 mb-3">
                                            <label class="form-label">Created By:</label>
                                            <div class="form-control-plaintext">
                                                <a href="" class="text-dark fw-normal">Zahid Khan</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-wrap mb-3">
                                <ul class="nav nav-tabs" id="dealTrackTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="deal-info-tab" data-bs-toggle="tab"
                                            data-bs-target="#deal-info" type="button" role="tab"
                                            aria-controls="deal-info" aria-selected="true">Address</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="sales-person-info-tab" data-bs-toggle="tab"
                                            data-bs-target="#sales-person-info" type="button" role="tab"
                                            aria-controls="sales-person-info" aria-selected="false">Contact
                                            Person</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="vat-info-tab" data-bs-toggle="tab"
                                            data-bs-target="#vat-info" type="button" role="tab" aria-controls="vat-info"
                                            aria-selected="false">VAT
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="payment-info-tab" data-bs-toggle="tab"
                                            data-bs-target="#payment-info" type="button" role="tab"
                                            aria-controls="payment-info" aria-selected="false">Payment
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="customer-info-tab" data-bs-toggle="tab"
                                            data-bs-target="#customer-info" type="button" role="tab"
                                            aria-controls="customer-info" aria-selected="false">Documents</button>
                                    </li>
                                     <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="history-info-tab" data-bs-toggle="tab"
                                            data-bs-target="#history-info" type="button" role="tab"
                                            aria-controls="history-info" aria-selected="false">History</button>
                                    </li>
                                </ul>
                                <div class="tab-content mb-3" id="dealTrackTabsContent">
                                    <div class="tab-pane fade show active" id="deal-info" role="tabpanel"
                                        aria-labelledby="deal-info-tab">
                                        <!-- <h4 class="mb-3 color-sub-head">Shipping Address</h4> -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h4 class="mb-1 color-sub-head font-size-13">Billing Address</h4>
                                                <table class="table table-bordered table-sm">
                                                    <tbody>
                                                        <tr>
                                                            <th class="w-25">Country</th>
                                                            <td>United Arab Emirates</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Address 1</th>
                                                            <td>1st Floor - Easa Saleh, Al Gurg Building</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Address 2</th>
                                                            <td>24 12 St, Khalid Bin Al Waleed Rd</td>
                                                        </tr>
                                                        <tr>
                                                            <th>City</th>
                                                            <td>Bur Dubai</td>
                                                        </tr>
                                                        <tr>
                                                            <th>State</th>
                                                            <td>Dubai</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Post Box</th>
                                                            <td>124402</td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="col-md-4">
                                                <h4 class="mb-1 color-sub-head font-size-13">Shipping Address</h4>
                                                <table class="table table-bordered table-sm">
                                                    <tbody>
                                                        <tr>
                                                            <th class="w-25">Country</th>
                                                            <td>United Arab Emirates</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Address 1</th>
                                                            <td>1st Floor - Easa Saleh, Al Gurg Building</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Address 2</th>
                                                            <td>24 12 St, Khalid Bin Al Waleed Rd</td>
                                                        </tr>
                                                        <tr>
                                                            <th>City</th>
                                                            <td>Bur Dubai</td>
                                                        </tr>
                                                        <tr>
                                                            <th>State</th>
                                                            <td>Dubai</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Post Box</th>
                                                            <td>124402</td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>


                                    </div>
                                    <div class="tab-pane fade" id="sales-person-info" role="tabpanel"
                                        aria-labelledby="sales-person-info-tab">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped text-center">
                                                    <thead>
                                                        <tr>
                                                            <th>Salutation</th>
                                                            <th>First Name</th>
                                                            <th>Last Name</th>
                                                            <th>Email Address</th>
                                                            <th>Work Phone</th>
                                                            <th>Mobile</th>
                                                            <th>Designation</th>
                                                            <th>Department</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        <tr>
                                                            <td>Mr</td>
                                                            <td>Subeesh</td>
                                                            <td>Sukumaran</td>
                                                            <td>subeesh@sysllc.com</td>
                                                            <td>+97143522433</td>
                                                            <td>+97143522433</td>
                                                            <td>General Manager</td>
                                                            <td>Administrative</td>
                                                        </tr>
                                                    </tbody>

                                                </table>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="vat-info" role="tabpanel"
                                        aria-labelledby="vat-info-tab">
                                        <div class="row">


                                            <div class="col-2 mb-3">
                                                <label class="form-label">Vat Country:</label>
                                                <div class="form-control-plaintext">United Arab Emirates</div>
                                            </div>
                                            <div class="col-2 mb-3">
                                                <label class="form-label">VAT Percentage: </label>
                                                <div class="form-control-plaintext">5%</div>
                                            </div>
                                            <div class="col-2 mb-3">
                                                <label class="form-label">VAT Number:</label>
                                                <div class="form-control-plaintext">100394474900003</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="payment-info" role="tabpanel"
                                        aria-labelledby="payment-info-tab">
                                        <div class="row">

                                            <div class="col-2 mb-3">
                                                <label class="form-label">Transaction Type:</label>
                                                <div class="form-control-plaintext">Cheque</div>
                                            </div>
                                            <div class="col-2 mb-3">
                                                <label class="form-label">Credit Limit:</label>
                                                <div class="form-control-plaintext">0.00</div>
                                            </div>
                                            <div class="col-2 mb-3">
                                                <label class="form-label">Credit Days:</label>
                                                <div class="form-control-plaintext">30</div>
                                            </div>
                                            <div class="col-2 mb-3">
                                                <label class="form-label">Payment Terms:</label>
                                                <div class="form-control-plaintext">30 DAYS PDC</div>
                                            </div>

                                        </div>
                                    </div>



                                    <div class="tab-pane fade" id="customer-info" role="tabpanel"
                                        aria-labelledby="customer-info-tab">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-bordered table-striped" width="100%"
                                                    cellspacing="0">
                                                    <tbody>
                                                        <tr>
                                                            <td>Trade License/Commercial Registration</td>
                                                            <td>16/10/2025</td>
                                                            <td><a class="btn-sm btn-primary"
                                                                    href="https://erp.venushrms.com/public/uploads/cust-suppl/5b0cbba4ad1cf4b09c10a9a50cdc1fc1_customer_doc_1.pdf"
                                                                    target="_blank">

                                                                    <i
                                                                        class="ico icon-bold-download-minimalistic fw-bold title-15"></i>

                                                                    Download</a></td>
                                                        </tr>
                                                        <tr>
                                                            <td>VAT Certificate</td>
                                                            <td>24/04/2025</td>
                                                            <td><a class="btn-sm btn-primary"
                                                                    href="https://erp.venushrms.com/public/uploads/cust-suppl/000983bf6c8cb29f2eff34b258cafec4_customer_doc_2.pdf"
                                                                    target="_blank">
                                                                    <i
                                                                        class="ico icon-bold-download-minimalistic fw-bold title-15"></i>
                                                                    Download</a></td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mb-1 color-sub-head">Supplier Outstanding</h4>


                                    <!-- <table id="account_table88" class="table"
                                        style="border: solid 1px #e3e6f0; margin-bottom: -1px !important;">
                                        <thead>
                                            <tr>
                                                <th class="border text-center"><a
                                                        href="https://erp.venushrms.com/get-url-supplier/SUPS-1444"
                                                        target="_blank">SUPS-1060</a></th>
                                                <th class="border text-left">
                                                    <a class="text-start" type="button"
                                                        data-toggle="collapse" data-target="#collapse88"
                                                        aria-expanded="true" aria-controls="collapse88">Computers
                                                        Trading LLC <span
                                                            style="font-weight: normal; color: #3d3d3d;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Komal
                                                                Shah</b> (Purchase
                                                            Incharge)&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;+971
                                                            48 05
                                                            5653&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;k.shah@logicom.net</span></a>

                                                    <a class="text-danger ml-2 float-end" title="Download">
                                                        <i
                                                            class="ico icon-bold-download-minimalistic fw-bold title-15"></i>
                                                    </a>
                                                </th>
                                                <th class="border text-end"><label class="fw-bold">9450.00</label></th>
                                            </tr>
                                        </thead>
                                    </table> -->


                                    <table class="table w-100" style="border: solid 1px #e3e6f0; width:auto;">
                                        <thead>
                                            <tr>
                                                <th class="border text-center"><a
                                                        href="https://erp.venushrms.com/get-url-supplier/SUPS-1444"
                                                        target="_blank">SUPS-1060</a></th>
                                                <th colspan="11" class="border text-start"> <a class="text-start"
                                                        type="button" data-toggle="collapse" data-target="#collapse88"
                                                        aria-expanded="true" aria-controls="collapse88">Computers
                                                        Trading LLC <span
                                                            style="font-weight: normal; color: #3d3d3d;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Komal
                                                                Shah</b> (Purchase
                                                            Incharge)&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;+971
                                                            48 05
                                                            5653&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;k.shah@logicom.net</span></a>

                                                    <a class="text-danger ml-2 float-end" title="Download">
                                                        <i
                                                            class="ico icon-bold-download-minimalistic fw-bold title-15"></i>
                                                    </a>
                                                </th>

                                                <th colspan="2" class="border text-end"><label
                                                        class="fw-bold">9450.00</label></th>



                                            </tr>
                                        </thead>
                                        <thead style="background:#4992582e;">
                                            <tr>
                                                <th class="border border-bottom text-center">Doc Date</th>
                                                <th class="border text-center">Doc No</th>
                                                <th class="border text-center">Deal ID</th>
                                                <th class="border text-center">Amount</th>
                                                <th class="border text-center">Adjustments</th>
                                                <th class="border text-center">Balance</th>
                                                <th class="border text-center">Total Balance</th>
                                                <th class="border text-center hidecol_88" style="display: none;">Receipt
                                                    Date</th>
                                                <th class="border text-center hidecol_88" style="display: none;">Doc
                                                    Number</th>
                                                <th class="border text-center">Payment Terms</th>
                                                <th class="border text-center">Due Date</th>
                                                <th class="border text-center">Over Due</th>
                                                <th class="border text-center">0-30</th>
                                                <th class="border text-center">31-60</th>
                                                <th class="border text-center">61-90</th>
                                                <th class="border text-center">&gt;90</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr>
                                                <td class="border text-center">29/02/2024</td>
                                                <td class="border text-center"><a
                                                        href="https://erp.venushrms.com/get-url-purchase-invoice/PIV-1002"
                                                        target="_blank">PIV-1002</a></td>
                                                <td class="border text-center"><a
                                                        href="https://erp.venushrms.com/crm-deals/1002/view"
                                                        target="_blank">1002</a></td>
                                                <td class="border text-center">6300.000</td>
                                                <td class="border text-center">0.00</td>
                                                <td class="border text-center">6,300.00 </td>
                                                <td class="border text-center">6,300.00</td>


                                                <td class="border text-center">60 DAYS PDC ON DELIVERY</td>
                                                <td class="border text-center">29/04/2024</td>
                                                <td class="border text-center" style="color:red">437</td>
                                                <td class="border text-center">&nbsp;</td>

                                                <td class="border text-center">&nbsp;</td>

                                                <td class="border text-center">&nbsp;</td>


                                                <td class="border text-center">6,300.00</td>

                                            </tr>


                                            <tr>
                                                <td class="border text-center">05/04/2024</td>
                                                <td class="border text-center"><a
                                                        href="https://erp.venushrms.com/get-url-purchase-invoice/PRT-1001"
                                                        target="_blank">PRT-1001</a></td>
                                                <td class="border text-center"><a
                                                        href="https://erp.venushrms.com/crm-deals//view"
                                                        target="_blank"></a></td>
                                                <td class="border text-center">3150.000</td>
                                                <td class="border text-center">0.00</td>
                                                <td class="border text-center">3,150.00 </td>
                                                <td class="border text-center">9,450.00</td>


                                                <td class="border text-center hidecol_88" style="display: none;">
                                                </td>
                                                <td class="border text-center hidecol_88" style="display: none;">
                                                </td>

                                                <td class="border text-center"></td>
                                                <td class="border text-center">09/07/2025</td>

                                                <td class="border text-center">0</td>

                                                <td class="border text-center">3,150.00</td>

                                                <td class="border text-center">&nbsp;</td>

                                                <td class="border text-center">&nbsp;</td>


                                                <td class="border text-center">&nbsp;</td>

                                            </tr>


                                            <tr>
                                                <td colspan="3" class="border"></td>
                                                <td class="border text-center"><b>9,450.00 </b></td>
                                                <td class="border text-center"><b>0.00 </b></td>
                                                <td class="border text-center"><b>9,450.00</b> </td>
                                                <td class="border text-center"><b>9,450.00</b> </td>
                                                <td class="border text-center" colspan="3">&nbsp; </td>
                                                <td class="border text-center"><b>3,150.00</b> </td>
                                                <td class="border text-center"><b>0.00</b> </td>
                                                <td class="border text-center"><b>0.00 </b></td>
                                                <td class="border text-center"><b>6,300.00 </b></td>
                                            </tr>

                                        </tbody>
                                        <thead>
                                            <tr>
                                                <th colspan="12" class="border text-end fw-bold">Total</th>
                                                <th colspan="2" class="border text-end fw-bold">9,450.00</th>
                                            </tr>
                                        </thead>
                                    </table>


                                    <!-- <table class="table" style="border: solid 1px #e3e6f0;">
                                        <thead>
                                            <tr>
                                                <th colspan="12" class="border text-end fw-bold">Total</th>
                                                <th colspan="2" class="border text-end fw-bold">9,450.00</th>
                                            </tr>
                                        </thead>
                                    </table> -->


                                </div>
                            </div>


<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

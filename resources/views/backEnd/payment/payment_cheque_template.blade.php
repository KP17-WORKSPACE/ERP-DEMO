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
                    Cheque Template
                </h4>
                <div class="purchase-order-content-header-right">
                     <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">




                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('company/policy') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Policy
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('/department') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Department
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/designation') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Designation
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/legal-entity') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Business Entity
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/industry') }}">
                        <i class="ico icon-outline-layers text-success  title-15 me-2"></i>
                        Industry Type
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/business-activity') }}">
                        <i class="ico icon-outline-layers text-success  title-15 me-2"></i>
                        Business Sector
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('role') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Role
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('module') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Module
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('base_setup') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Base Setup
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('daily-quotes.index') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Daily Quote
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('currency-settings') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Manage Currency
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('payment-terms') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Payment Terms
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('company') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Settings
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('shipping-add') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Shipping
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('vat-settings') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        VAT Settings
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('accountgroup-add') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Main Heads
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('book-close') }}">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        Book Closed
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('book-close-doc-number') }}">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        Book Close Doc No
                    </a>
                </li>


            </ul>
        </div>
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-body">
                    <style>
                #div_company {
                    position: absolute;
                    z-index: 9;
                    background-color: #f1f1f1;
                    text-align: left;
                    border: 1px solid #d3d3d3;
                    top: {{ @$company_top }};
                    left: {{ @$company_left }};
                    font-size: {{ @$font_size }};
                }

                #div_date {
                    position: absolute;
                    z-index: 9;
                    background-color: #f1f1f1;
                    text-align: left;
                    border: 1px solid #d3d3d3;
                    top: {{ @$date_top }};
                    left: {{ @$date_left }};
                    font-size: {{ @$font_size }};
                }

                #div_amount_w {
                    position: absolute;
                    z-index: 9;
                    background-color: #f1f1f1;
                    text-align: left;
                    border: 1px solid #d3d3d3; width: 378px; line-height: 28px;
                    top: {{ @$amount_w_top }};
                    left: {{ @$amount_w_left }};
                    font-size: {{ @$font_size }};
                }

                #div_amount {
                    position: absolute;
                    z-index: 9;
                    background-color: #f1f1f1;
                    text-align: left;
                    border: 1px solid #d3d3d3;
                    top: {{ @$amount_top }};
                    left: {{ @$amount_left }};
                    font-size: {{ @$font_size }};
                }
            </style>

            <div style="width: 918px; height: 605px; border: solid 1px #2e2e2e;">
                <div id="div_company">{{ $company }}</div>
                <div id="div_date">{{ $cheque_date }}</div>
                <div id="div_amount_w">{{ $cheque_amount_w }}</div>
                <div id="div_amount">{{ $cheque_amount }}</div>
            </div>

            <div >
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'payment-cheque-print-template','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'payment-cheque-print-template']) }}
                <table>
                    <tr>
                        <td></td>
                        <td><input type="hidden" class="form-control" name="company_top" id="company_top" value="{{ @$company_top }}" /></td>
                        <td><input type="hidden" class="form-control" name="company_left" id="company_left" value="{{ @$company_left }}" /></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="hidden" class="form-control" name="date_top" id="date_top" value="{{ @$date_top }}" /></td>
                        <td><input type="hidden" class="form-control" name="date_left" id="date_left" value="{{ @$date_left }}" /></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="hidden" class="form-control" name="amount_w_top" id="amount_w_top" value="{{ @$amount_w_top }}" /></td>
                        <td><input type="hidden" class="form-control" name="amount_w_left" id="amount_w_left" value="{{ @$amount_w_left }}" /></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="hidden" class="form-control" name="amount_top" id="amount_top" value="{{ @$amount_top }}" /></td>
                        <td><input type="hidden" class="form-control" name="amount_left" id="amount_left" value="{{ @$amount_left }}" /></td>
                    </tr>
                    <tr>
                        <td>Bank : </td>
                        <td>
                            <select class="form-control" name="bank_id" autocomplete="off" id="bank_id" onchange="fun_submit()" style="width: 250px;" required>
                                @if (count($bank)>0)
                                    @foreach ($bank as $b)
                                        <option value="{{ $b->id }}" @if($bankid==$b->id) selected @endif>{{ $b->account_name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        <td align="right"></td>
                    </tr>
                    <tr>
                        <td>Font Size : </td>
                        <td><input type="text" class="form-control" name="font_size" id="font_size" value="{{ $font_size }}" onchange="change_font_size()" />
                        </td>
                        <td align="right">
                            <button type="submit" name="btn_submit" value="change" class="btn btn-light" id="btn_submit" style="display: none;"><i class="ico icon-outline-bookmark-opened text-success"></i> change</button>
                            <button type="submit" name="btn_submit" value="save" class="btn btn-light"><span class="ti-check"></span><i class="ico icon-outline-bookmark-opened text-success"></i> Save</button>
                        </td>
                    </tr>
                </table>
                {{ Form::close() }}
            </div>

            <script>

                function change_font_size(){
                    var s = $('#font_size').val();
                    $('#div_company').css('font-size',s);
                    $('#div_date').css('font-size',s);
                    $('#div_amount_w').css('font-size',s);
                    $('#div_amount').css('font-size',s);
                }

                function fun_submit(){
                    $('#btn_submit').click();
                    $("#loading_bg").css("display", "block");
                }
                //Make the DIV element draggagle:

                dragElement(document.getElementById("div_company"), "company");
                dragElement(document.getElementById("div_date"), "date");
                dragElement(document.getElementById("div_amount_w"), "amount_w");
                dragElement(document.getElementById("div_amount"), "amount");

                function dragElement(elmnt, div_id) {
                    var pos1 = 0,
                        pos2 = 0,
                        pos3 = 0,
                        pos4 = 0;
                    if (document.getElementById(elmnt.id + "header")) {
                        document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
                    } else {
                        elmnt.onmousedown = dragMouseDown;
                    }

                    function dragMouseDown(e) {
                        e = e || window.event;
                        e.preventDefault();
                        pos3 = e.clientX;
                        pos4 = e.clientY;
                        document.onmouseup = closeDragElement;
                        document.onmousemove = elementDrag;
                    }

                    function elementDrag(e) {
                        e = e || window.event;
                        e.preventDefault();
                        pos1 = pos3 - e.clientX;
                        pos2 = pos4 - e.clientY;
                        pos3 = e.clientX;
                        pos4 = e.clientY;
                        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
                        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
                        $('#' + div_id + '_top').val(elmnt.style.top);
                        $('#' + div_id + '_left').val(elmnt.style.left);
                    }

                    function closeDragElement() {
                        document.onmouseup = null;
                        document.onmousemove = null;
                    }
                }
            </script>
                </div>
            </div>
			
			
		</div>
	</div>
</div>
@endsection

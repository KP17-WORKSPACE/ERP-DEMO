@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <?php try { ?>

        <div class="container-fluid mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="mb-3">
                    <h2 class="page-heading m-0">Select BOQ Products</h2>
                    <span class="page-label">Home - Deal - Select BOQ Products</span>
                </div>
                <div>

                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title m-0 pb-0">Products</h4>                            

                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="3"><b class="text-danger">Basic Phones</b>
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fas fa-minus"></i></button>
                                            <input type="hidden" id="customer_type" value="{{$BCart[0]->customer}}">
                                            <input type="hidden" id="company" value="{{$BCart[0]->company}}"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($BasicPhones))
                                        @foreach ($BasicPhones as $Item)
                                            <tr>
                                                <td class="p-2 w-75">
                                                    <b>{{$Item->description}}</b><br />
                                                    <i>SKU : {{$Item->part_number}}</i> |
                                                        <span class="text-md text-info">{{$Item->price}} {{$currency}}</span>
                                                        <input type="hidden" id="txt_price_{{$Item->id}}" value="{{$Item->price}}">
                                                </td>
                                                <td class="text-right">
                                                    <input type="number" class="form-control" id="txt_qty_{{$Item->id}}">
                                                </td>
                                                <td class="text-right">
                                                    <button class="btn btn-warning btn-xs" id="txt_btn_{{$Item->id}}" onclick="add_tocart({{$Item->id}})"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>

                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="3"><b class="text-danger">Reception Module</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (isset($ReceptionModule))
                                    @foreach ($ReceptionModule as $Item)
                                        <tr>
                                            <td class="p-2 w-75">
                                                <b>{{$Item->description}}</b><br />
                                                <i>SKU : {{$Item->part_number}}</i> |
                                                    <span class="text-md text-info">{{$Item->price}} {{$currency}}</span>
                                                    <input type="hidden" id="txt_price_{{$Item->id}}" value="{{$Item->price}}">
                                            
                                            </td>
                                            @if(App\SysHelper::check_j189j179($Item->part_number) != 0)
                                            <td class="text-right">
                                                <input type="number" class="form-control" id="txt_qty_{{$Item->id}}">
                                            </td>
                                            <td class="text-right">
                                                <button class="btn btn-warning btn-xs" id="txt_btn_{{$Item->id}}" onclick="add_tocart({{$Item->id}})"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>

                            
                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="3"><b class="text-danger">Manager Level Phones</b></th>
                                    </tr>
                                </thead>
                                <tbody>

                                @if (isset($ManagerLevelPhones))
                                <tr>
                                    <td class="p-2 w-75">
                                        <b>Vantage K155 Device</b><br /><i>SKU : 700513907</i> | 
                                            <span class="text-md text-info">
                                            <?php echo App\SysHelper::get_price_first('700513907',$BCart[0]->customer,$BCart[0]->company)->price ?> {{$currency}}</span>
            
                                            <hr clear="both"/>
                                            <b>Avaya Vantage Crdls Handset Kit</b><br /><i>SKU : 700512398</i> | 
                                            <span class="text-md text-info">
                                            <?php echo App\SysHelper::get_price_first('700512398',$BCart[0]->customer,$BCart[0]->company)->price ?> {{$currency}}</span>
                                            
                                            <hr clear="both"/>
                                            <b>J100/K100 Series IP Phone Wireless Module</b><br /><i>SKU : 700512402</i> | 
                                            <span class="text-md text-info">
                                            <?php echo App\SysHelper::get_price_first('700512402',$BCart[0]->customer,$BCart[0]->company)->price ?> {{$currency}}</span>
                                    </td>
                                    <td class="text-right">
                                            <input type="number" class="form-control" id="txt_qty_b1">
                                    </td>
                                    <td class="text-right">
                                            <button class="btn btn-warning btn-xs" id="txt_btn_b1" onclick="add_tocart2('b1')"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                        <td class="p-2 w-75">
                                            <b>Vantage K155 Device</b><br /><i>SKU : 700513907</i> | 
                                            <span class="text-md text-info">
                                            <?php echo App\SysHelper::get_price_first('700513907',$BCart[0]->customer,$BCart[0]->company)->price ?> {{$currency}}</span>
                                            
                                            <hr clear="both"/>
                                            <b>Avaya Vantage Corded Handset Kit</b><br /><i>SKU : 700512399</i> | 
                                            <span class="text-md text-info">
                                            <?php echo App\SysHelper::get_price_first('700512399',$BCart[0]->customer,$BCart[0]->company)->price ?> {{$currency}}</span>
                                            
                                        </td>
                                        <td class="text-right">
                                            <input type="number" class="form-control" id="txt_qty_b2">
                                        </td>
                                        <td class="text-right">
                                            <button class="btn btn-warning btn-xs" id="txt_btn_b2" onclick="add_tocart2('b2')"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                        </td>
                                </tr>
                                <tr>
                                        <td class="p-2 w-75">
                                            <b>Vantage K175 Dual Port with Camera</b><br /><i>SKU : 700513905</i> | 
                                            <span class="text-md text-info">
                                            <?php echo App\SysHelper::get_price_first('700513905',$BCart[0]->customer,$BCart[0]->company)->price ?> {{$currency}}</span>
                                            
                                            <hr clear="both"/>
                                            <b>Avaya Vantage Crdls Handset Kit</b><br /><i>SKU : 700512398</i> | 
                                            <span class="text-md text-info">
                                            <?php echo App\SysHelper::get_price_first('700512398',$BCart[0]->customer,$BCart[0]->company)->price ?> {{$currency}}</span>
                                        </td>
                                        <td class="text-right">
                                            <input type="number" class="form-control" id="txt_qty_b3">
                                        </td>
                                        <td class="text-right">
                                            <button class="btn btn-warning btn-xs" id="txt_btn_b3" onclick="add_tocart2('b3')"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                        </td>
                                </tr>
                                <tr>
                                        <td class="p-2 w-75">
                                            <b>Vantage K175 Dual Port with Camera</b><br /><i>SKU : 700513905</i> | 
                                            <span class="text-md text-info">
                                            <?php echo App\SysHelper::get_price_first('700513905',$BCart[0]->customer,$BCart[0]->company)->price ?> {{$currency}}</span>
                                            
                                            <hr clear="both"/>
                                            <b>Avaya Vantage Corded Handset Kit</b><br /><i>SKU : 700512399</i> | 
                                            <span class="text-md text-info">
                                            <?php echo App\SysHelper::get_price_first('700512399',$BCart[0]->customer,$BCart[0]->company)->price ?> {{$currency}}</span>
                                        </td>
                                        <td class="text-right">
                                            <input type="number" class="form-control" id="txt_qty_b4">
                                        </td>
                                        <td class="text-right">
                                            <button class="btn btn-warning btn-xs" id="txt_btn_b4" onclick="add_tocart2('b4')"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                        </td>
                                </tr>
                                @endif
                                </tbody>
                            </table>

                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="3"><b class="text-danger">Confrence Phone</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (isset($ConfrencePhone))
                                    @foreach ($ConfrencePhone as $Item)
                                    <tr>
                                        <td class="p-2 w-75">
                                            <b>{{$Item->description}}</b><br />
                                            <i>SKU : {{$Item->part_number}}</i> | 
                                                <span class="text-md text-info">{{$Item->price}} {{$currency}}</span>
                                                <input type="hidden" id="txt_price_{{$Item->id}}" value="{{$Item->price}}">
                                        </td>
                                        <td class="text-right">
                                            <input type="number" class="form-control" id="txt_qty_{{$Item->id}}">
                                        </td>
                                        <td class="text-right">
                                            <button class="btn btn-warning btn-xs" id="txt_btn_{{$Item->id}}" onclick="add_tocart({{$Item->id}})"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif                        
                                </tbody>
                            </table>

                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="3"><b class="text-danger">3rd Party Integration (CRM or ERP)</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (isset($CRMorERP))
                                    @foreach ($CRMorERP as $Item)
                                    <tr>
                                        <td class="p-2 w-75">
                                            <b>{{$Item->description}}</b><br />
                                            <i>SKU : {{$Item->part_number}}</i> | 
                                                <span class="text-md text-info">{{$Item->price}} {{$currency}}</span>
                                                <input type="hidden" id="txt_price_{{$Item->id}}" value="{{$Item->price}}">
                                        </td>
                                        <td class="text-right">
                                            <input type="number" class="form-control" id="txt_qty_{{$Item->id}}">
                                        </td>
                                        <td class="text-right">
                                            <button class="btn btn-warning btn-xs" id="txt_btn_{{$Item->id}}" onclick="add_tocart({{$Item->id}})"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif                        
                                </tbody>
                            </table>

                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="3"><b class="text-danger">Softphone or Mobile</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (isset($Softphone))
                                    @foreach ($Softphone as $Item)
                                    <tr>
                                        <td class="p-2 w-75">
                                            <b>{{$Item->description}}</b><br />
                                            <i>SKU : {{$Item->part_number}}</i> | 
                                                <span class="text-md text-info">{{$Item->price}} {{$currency}}</span>
                                                <input type="hidden" id="txt_price_{{$Item->id}}" value="{{$Item->price}}">
                                        </td>
                                        <td class="text-right">
                                            <input type="number" class="form-control" id="txt_qty_{{$Item->id}}">
                                        </td>
                                        <td class="text-right">
                                            <button class="btn btn-warning btn-xs" id="txt_btn_{{$Item->id}}" onclick="add_tocart({{$Item->id}})"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif                        
                                </tbody>
                            </table>

                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="3"><b class="text-danger">Call Recording</b><br />
                                            <b>Note : Recording Server or system has provided by Client Side</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (isset($CallRecording))
                                    @foreach ($CallRecording as $Item)
                                    <tr>
                                        <td class="p-2 w-75">
                                            <b>{{$Item->description}}</b><br />
                                            <i>SKU : {{$Item->part_number}}</i> | 
                                                <span class="text-md text-info">{{$Item->price}} {{$currency}}</span>
                                                <input type="hidden" id="txt_price_{{$Item->id}}" value="{{$Item->price}}">
                                        </td>
                                        <td class="text-right">
                                            <input type="number" class="form-control" id="txt_qty_{{$Item->id}}">
                                        </td>
                                        <td class="text-right">
                                            <button class="btn btn-warning btn-xs" id="txt_btn_{{$Item->id}}" onclick="add_tocart({{$Item->id}})"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif                        
                                </tbody>
                            </table>

                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="3"><b class="text-danger">Call Billing Reporting</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- 1 to 50 user - 2800 | 1 tp 100 user - 4900 | 1 to 150 user - 6300 | 1 to 200 user - 8350 --}}
                                @if (isset($CallBilling))
                                    @foreach ($CallBilling as $Item)
                                    <tr>
                                        <td class="p-2 w-75">
                                            <b>{{$Item->description}}</b><br />
                                            <i>SKU : {{$Item->part_number}}</i> | 
                                                <span class="text-md text-info">{{$Item->price}} {{$currency}}</span>
                                                <input type="hidden" id="txt_price_{{$Item->id}}" value="{{$Item->price}}">
                                        </td>
                                        <td class="text-right">
                                            <input type="number" class="form-control" id="txt_qty_{{$Item->id}}">
                                        </td>
                                        <td class="text-right">
                                            <button class="btn btn-warning btn-xs" id="txt_btn_{{$Item->id}}" onclick="add_tocart({{$Item->id}})"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif                        
                                </tbody>
                            </table>

                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="3"><b class="text-danger">Welcome Message</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (isset($WelcomeMessage))
                                    @foreach ($WelcomeMessage as $Item)
                                    <tr>
                                        <td class="p-2 w-75">
                                            <b>{{$Item->description}}</b><br />
                                            <i>SKU : {{$Item->part_number}}</i> | 
                                                <span class="text-md text-info">{{$Item->price}} {{$currency}}</span>
                                                <input type="hidden" id="txt_price_{{$Item->id}}" value="{{$Item->price}}">
                                        </td>
                                        <td class="text-right">
                                            <input type="number" class="form-control" id="txt_qty_{{$Item->id}}">
                                        </td>
                                        <td class="text-right">
                                            <button class="btn btn-warning btn-xs" id="txt_btn_{{$Item->id}}" onclick="add_tocart({{$Item->id}})"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif                        
                                </tbody>
                            </table>

                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="3"><b class="text-danger">3rd Party Phone</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if (isset($PartyPhone))
                                    @foreach ($PartyPhone as $Item)
                                    <tr>
                                        <td class="p-2 w-75">
                                            <b>{{$Item->description}}</b><br />
                                            <i>SKU : {{$Item->part_number}}</i> | 
                                                <span class="text-md text-info">{{$Item->price}} {{$currency}}</span>
                                                <input type="hidden" id="txt_price_{{$Item->id}}" value="{{$Item->price}}">
                                        </td>
                                        <td class="text-right">
                                            <input type="number" class="form-control" id="txt_qty_{{$Item->id}}">
                                        </td>
                                        <td class="text-right">
                                            <button class="btn btn-warning btn-xs" id="txt_btn_{{$Item->id}}" onclick="add_tocart({{$Item->id}})"> <i class="fa fa-cart-plus" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif                        
                                </tbody>
                            </table>



                @if(isset($cart_items))
                    {{ Form::open(['route' => 'quote.generatequote', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) }}
                        <button class="btn btn-success mt-1 float-right">Save Quote</button>
                    {{ Form::close() }}
                @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-3">
                    <div class="card card-dark">
                        <div class="card-header">
                            <h3 class="card-title">Items Selected</h3>
                        </div>
                        <div class="card-body row">
                            @if (isset($BCart))
                            @foreach ($BCart as $Itm)
                                <div class="col-md-12">No of Location : <b>{{$Itm->nooflocation}}</b></div><hr class="w-100"/>
                                <div class="col-md-12">Connectivity Required : <b>{{$Itm->connectivity}}</b></div><hr class="w-100"/>
                                <div class="col-md-12">ISP Telephone Type :
                                    <?php $string = $Itm->telephonetype;
                                    $str_arr = explode (",", $string);
                                    $string_val = $Itm->nolines;
                                    $str_arr2 = explode (",", $string_val);
                                    for($i=0; $i< count($str_arr); $i++) { ?>
                                        <b>{{$str_arr[$i]}} - {{ $str_arr2[$i] }} Line</b>, 
                                    <?php } ?>
                                </div><hr class="w-100"/>
        
        
                                {{-- <div class="col-md-12">ISP Telephone Type : <b>{{$Itm->telephonetype}}</b></div><hr class="w-100"/> --}}
                            @endforeach
                            @endif
                            
                            @if (isset($Cart))
                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <tbody>
                            @foreach ($Cart as $Item)
                            <tr>
                                <td class="p-2">
                                        <span>{{$Item->description}}</span><br />
                                        <b class="font-bold">{{$Item->part_number}} | {{$Item->price}} {{$currency}} | {{$Item->qty}} Qty</b>
                                </td>
                                <td class="p-2">
                                    
                                        <button class="btn btn-danger" id="del_btn_{{$Item->id}}" onclick="del_tocart({{$Item->id}})">
                                            <i class="fa fa-times" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                            @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
        
                        <div class="card-body row">
                            <b>Total Amount : <span class="text-danger">{{ App\SysHelper::cart_sum() }} {{$currency}}</span></b>
                        </div>
                        <div class="card-body row">
                            @if(isset($Cart))
                                {{ Form::open(['route' => 'boq.generatequote', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) }}
                                    <button class="btn btn-success mt-1 float-right">Generate Quotation</button>
                                {{ Form::close() }}
                            @endif
                        </div>
        
        
                    </div>
                </div>



            </div>
        </div>
        
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    

    <script>

        function add_tocart(id) {
            $("#loading_bg").css("display", "block");
            var btn = $("#txt_btn_"+id).val();
            var qty = $("#txt_qty_"+id).val();
            var price = $("#txt_price_"+id).val();
            var customer_type = $("#customer_type").val();
            var company = $("#company").val();
    
            if (qty == "" || qty <= 0) {
                alert("Please Add Qty");
                $("#txt_qty_"+id).focus();
                $("#cart_add_bg").css("display", "none");
                return false;
            }
            $(btn).attr('disabled', true);
    
            var action = "{{ URL::to('crm-boq-addtocart') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    qty: qty,
                    price: price,
                    customer_type: customer_type,
                    company: company,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#cart_add_bg").css("display", "none");
                        location.reload(true);
                    }
                }
            });
        }
        function add_tocart2(id) {
            $("#loading_bg").css("display", "block");
            var btn = $("#txt_btn_"+id).val();
            var qty = $("#txt_qty_"+id).val();
            var customer_type = $("#customer_type").val();
            var company = $("#company").val();
            if (qty == "" || qty <= 0) {
                alert("Please Add Qty");
                $("#txt_qty_"+id).focus();
                $("#cart_add_bg").css("display", "none");
                return false;
            }
            $(btn).attr('disabled', true);
    
            var action = "{{ URL::to('crm-boq-addtocartgroup') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    qty: qty,
                    customer_type: customer_type,
                    company: company,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#cart_add_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }

        function del_tocart(id) {
            $("#loading_bg").css("display", "block");
            var btn = $("#del_btn_"+id).val();
            $(btn).attr('disabled', true);
            var customer_type = $("#customer_type").val();
            var company = $("#company").val();
    
            var action = "{{ URL::to('crm-boq-deltocart') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    customer_type: customer_type,
                    company: company,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#cart_add_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }
        

    </script>
@endsection
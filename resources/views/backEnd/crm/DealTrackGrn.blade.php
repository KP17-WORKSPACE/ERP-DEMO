@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

<?php try { ?>

    <style>
        .head {font-size: 14px;}
        .card h2{font-size: 14px;}
        .card h4{font-size: 14px;}
        .card h5{font-size: 14px;}
        .card h6{font-size: 12px;}
        .card p{font-size: 11px;}
        .card span{font-size: 11px;}
        .card b{font-size: 11px;}
        .modal-body h4{font-size: 17px;}
        .table th, .table td { padding: 1px; font-size: 12px; }
    </style>

  <div class="container-fluid mb-1">
      <div class="d-flex justify-content-between align-items-center">
          <div class="mb-1">
              <h2 class="page-heading m-0">GRN Approval (Deal ID - {{ $deal->id }})</h2>
              <span class="page-label">Home - Deal - Deal Track Approval</span>
          </div>
          <div>
              <a href="{{ url()->previous() }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back</a>
          </div>
      </div>

      <div class="row">
        <div class="col-lg-3 mb-1 p-1">
            <div class="p-3 card h-100">
                <h2 class="head">Deal Info {!! App\SysHelper::deal_type_new($deal->isproject) !!}
                </h2>
                <p class="mb-1 text-white-100 text-uppercase">{{ $deal->customername->name }}</p>
                <span class="mb-1">Deal Value : {{ App\SysHelper::currancy_format_deal($deal->deal_value,$deal->company_id) }} {{ $deal->dealcurrency->code }}</span>
                @if ($deal->estimated_close_date != '')
                <span class="mb-1">Estimated Close Date : {{ date('m/d/Y', strtotime($deal->estimated_close_date)) }}</span>
                @endif
                <div class="text-capitalize">Stage : <b class="">
                    @if($deal->stage==1) <span class="btn-warning btn-badge py-1 px-2">Prospecting</span> @endif
                    @if($deal->stage==2) <span class="btn-success btn-badge py-1 px-2">Quote</span> @endif
                    @if($deal->stage==3) <span class="btn-info btn-badge py-1 px-2">Closure</span> @endif
                    @if($deal->stage==4) 
                    <?php
                    $data = App\SysHelper::deal_track_status($deal->id);
                    $color = "btn-danger";
                    if($data=="Pending"){
                        $color = "btn-warning";
                    } else if($data=="completed"){
                        $color = "btn-primary";                                            
                    } else if($data=="OnProcess"){
                        $color = "btn-info";                                            
                    } else{
                        $color = "btn-danger";
                    }
                    ?>
                    @if($data!="completed")
                    <span class="btn-primary btn-badge py-1 px-2">Won</span>@endif
                    
                    @if(App\SysHelper::set_track($deal->id)==1)
                        <span class="{{ $color }} btn-badge py-1 px-2" >
                        @if($data=="Fulfill")<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>@endif {{ $data }} </span>
                    @endif
                        
                    @endif
                    @if($deal->stage==5) <span class="btn-danger btn-badge py-1 px-2">Lost</span> @endif
                    @if($deal->stage==6) <span class="btn-dark btn-badge py-1 px-2">Cancelled</span> @endif
                </b>
              </div>
            </div>
        </div>
        <div class="col-lg-3 mb-1 p-1">
            <div class="p-3 card h-100">
                <h2 class="head">Owner Info</h2>
                <h6 class="sub-head text-capitalize text-dark">{{ $deal->ownername->first_name }} {{ $deal->ownername->middle_name }} {{ $deal->ownername->last_name }}</h6>
                <p class="mb-1 text-gray-800">Added On : {{ date('d/m/Y H:i:s', strtotime(@$deal->created_at)) }} @if ($deal->source != '') | Source : {{ $deal->source }}
                    @if ($deal->source_o != '') - {{ $deal->source_o }} @endif @endif
            </p>
                <span class="mb-1"> <span class="font-semibold">Mob No :</span> {{ $deal->ownername->mobile }}</span>
                <span class="mb-1"><span class="font-semibold">Mail :</span> {{ $deal->ownername->email }}</span>
            </div>
        </div>
        <div class="col-lg-3 mb-1 p-1">
            <div class="p-3 card h-100">
                <h2 class="head p-0 m-0">Customer Info </h2>
                <h6 class="sub-head text-capitalize text-dark p-0 m-0">
                    <span class="pt-0 pb-0 pl-1 pr-1 text-sm"
                    @if($deal->customername->type==1) style="background: #228c22; color: #ffffff;" @endif
                    @if($deal->customername->type==2) style="background: #FFA500; color: #ffffff;" @endif
                    @if($deal->customername->type==3) style="background: #FF0000; color: #ffffff;" @endif
                    @if($deal->customername->type==4) style="background: #000000; color: #ffffff;" @endif>
                    {{ $deal->customername->name }}</span>
                    @if(Auth::user()->role_id==1 || session('logged_session_data.designation_id')==8 || session('logged_session_data.designation_id')==2)
                        <a class="btn text-info m-0 p-0" onclick="updiv()" title="Edit Color"><i class="fa fa-edit pb-2" aria-hidden="true"></i></a>
                    @endif
                    <div class="border border-primary rounded bg-white text-sm p-2" id="div_update_color" style="display: none;">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-customer-color', 'method' => 'POST', 'id' => 'color_customer_form']) }}
                        Change Color :
                        <select class="dynamicstxt w-50" name="edit_color" id="edit_color" required>
                            <option value="1">Green</option>
                            <option value="2">Orange</option>
                            <option value="3">Red</option>
                            <option value="4">Black</option>
                        </select>
                        <input type="hidden" name="color_customer_id" value="{{ $deal->customername->id }}" />
                        <button id="btn_edit_color" type="submit" class="btn btn-xs btn-primary text-xs pt-0 pb-0">Change</button>
                        
                      {{ Form::close() }}
                    </div>
                    <script>
                        function updiv() {
                            $("#div_update_color").css("display", "block");
                        }
                    </script>
                
                
                </h6>
                @if ($deal->customername->address !="")
                <p class="mb-1 text-gray-800">{{ $deal->customername->address }}</p>
                @endif
                <span class="mb-1"> <span class="font-semibold">Contact :</span> {{ $deal->cust_name }}</span>
                <span class="mb-1"><span class="font-semibold">Mob No :</span> {{ $deal->cust_no }} | <span class="font-semibold">Mail :</span> {{ $deal->cust_email }}</span>
            </div>
        </div>

        <div class="col-lg-3 mb-1 p-1">
        <div class="p-3 card ">
            <div class="d-flex justify-content-between align-items-center mb-0">
                <h2 class=head>Delivery Location /Address</h2>
            </div>
            @if (isset($addressbook))
            <div class="row">
                <div class="col-3"> <b> Company </b></div>
                <span class="col-9">: {{ $addressbook->customername->name }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Address </b></div>
                <span class="col-9">: {{ $addressbook->address }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Contact Person</b></div>
                <span class="col-9">: {{ $addressbook->contact_person }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Mob Num</b></div>
                <span class="col-9">: {{ $addressbook->contact_number }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Email </b></div>
                <span class="col-9">: {{ $addressbook->contact_email }}</span>
            </div>
            @else
            <div class="row">
                <div class="col-3"> <b> Company </b></div>
                <span class="col-9">: {{ $deal->customername->name }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Address </b></div>
                <span class="col-9">: {{ $deal->customername->address }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Name</b></div>
                <span class="col-9">: {{ $deal->cust_name }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Mob</b></div>
                <span class="col-9">: {{ $deal->cust_no }}</span>
            </div>
            <div class="row">
                <div class="col-3"> <b>Email </b></div>
                <span class="col-9">: {{ $deal->cust_email }}</span>
            </div>

            @endif

        </div> 
        </div>


    </div>
    
    <div class="row">
    <div class="col-lg-12 h-100 mb-1 p-1">
        @if (count($quoteitems) > 0)
        <div class="row">
            <div class="col-md-12">
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-grn-update/' . $grn->id, 'method' => 'post', 'id' => 'crmdealtrackgrnupdate']) }}
                <div class="card">
                    <div class="card-header p-3">
                        <h4 class="header-title m-0 p-0" style="float: left;">Quote items</h4>
                        <a class="btn-small mr-1" style="float: right;" href="{{url('crm-quote/'.$deal->id.'/download/'.$deal->quote_id)}}"><i class="fa fa-download" aria-hidden="true"></i> Download Quotation</a>
                    </div>
                    <div class="card-body p-0 pl-3 pr-3 pb-2">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;"></th>
                                        <th style="width: 150px;">Part Number</th>
                                        <th style="width: 350px;">Description</th>
                                        <th style="width: 80px;" class="text-center">Qty</th>
                                        <th style="width: 100px;">GRN Qty</th>
                                        <th>Supplier Name</th>
                                        <th style="width: 150px;">Expected Date</th>
                                    </tr>
                                </thead>                                
                            <?php $t_qty = 0; $t_price = 0; $t_discount = 0; $t_net_amount = 0; $i=1; ?>
                                <tbody>
                                    @foreach ($quoteitems as $Item)
                                    <tr>
                                        <td><input type="checkbox" name="chk_{{ $i }}" class="form-control"/>
                                            <input type="hidden" name="roid" value="{{ $i }}" />
                                            <input type="hidden" name="partno{{ $i }}" value="{{ $Item->productname->part_number }}" />
                                        </td>
                                        <td><?php try{ ?> {{ $Item->productname->part_number }} <?php }catch (\Exception $e){} ?></td>
                                        <td><div style="width:350px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{!! $Item->description !!}</div></td>
                                        <td class="text-center">{{ $Item->qty }}</td>
                                        <td><input type="text" name="txtqty_{{ $i }}" class="form-control"/></td>
                                        <td><input type="text" name="txtsupplier_{{ $i }}" class="form-control"/></td>
                                        <td><input type="date" name="txtdate_{{ $i }}" class="form-control"/></td>
                                    </tr>
                                    @php $i++; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->                        
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
                <div class="card">
                    <div class="card-header">
                        <table  style="width: 100%;">
                            <tr>
                                <td style="width: 50%;"><b class="header-title m-0 p-0">Remarks</b> : <input type="text" name="remarks" class="form-control"/></td>
                                <td style="width: 20%;"><b class="header-title m-0 p-0">Status</b> : <select class="form-control" name="status" id="status" required>
                                    <option value="0">Pending</option>
                                    <option value="1">Approved</option>
                                    <option value="2">Disapproved</option>
                                    <option value="3">Partial Approved</option>
                                </select>
                                </td>
                                <td style="width: 30%;"><br />
                                    <button type="submit" class="btn btn-primary mt-1" id="btnSubmit"><span class="ti-check"></span>Submit</button>
                                </td>
                            </tr>
                        </table>
                        
                    </div>
                </div>
                {{ Form::close() }}
                <br /><br /><br />

            </div>
        </div>
        @endif
    </div>
    </div>
    


  



  </div>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>



    

    <style>
      .files input {
          outline: 2px dashed #92b0b3;
          outline-offset: -10px;
          -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
          transition: outline-offset .15s ease-in-out, background-color .15s linear;
          padding: 20px 0px 60px 35%;
          text-align: center !important;
          margin: 0;
          width: 100% !important;
      }
      .files input:focus{     outline: 2px dashed #92b0b3;  outline-offset: -10px;
          -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
          transition: outline-offset .15s ease-in-out, background-color .15s linear; border:1px solid #92b0b3;
       }
      .files{ position:relative}
      .files:after {  pointer-events: none;
          position: absolute;
          top: 60px;
          left: 0;
          width: 100%;
          right: 0;
          height: 30px;
          content: "";
          /*background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);*/
          display: block;
          margin: 0 auto;
          background-size: 100%;
          background-repeat: no-repeat;
      }
      .color input{ background-color:#f1f1f1;}
      .files:before {
          position: absolute;
          bottom: 10px;
          left: 0;  pointer-events: none;
          width: 100%;
          right: 0;
          height: 30px;
          content: " or drag it here. ";
          display: block;
          margin: 0 auto;
          color: #2ea591;
          font-weight: 600;
          text-transform: capitalize;
          text-align: center;
      }
  </style>
  
    <script>

$(window).ready(function() {
        $("#item-store-form").on("keypress", function (event) {           
            var keyPressed = event.keyCode || event.which;
            if (keyPressed === 13) {
                event.preventDefault();
                return false;
            }
        });
});


$(document).on("change", "#deliver_by", function () {
  var deliver_by = $("#deliver_by").val();
  var driver = $("#driver").val();
  var action = "{{ URL::to('getdriverbyshipping') }}";
    $.ajax({
        url: action,
        type: "GET",
        data: {
            _token: '{{ csrf_token() }}',
            deliver_by: deliver_by,
        },
        cache: false,
        success: function(dataResult) {
            //alert(dataResult);
            var dataResult = JSON.parse(dataResult);
            var len = 0;
            if(dataResult['data']=="ERROR")
            {
                alert("Error found in something!!");
            }
            else{
                if(dataResult['data'] != null){
                len = dataResult['data'].length;
                }
                if(len > 0){
                    
                    $('#driver').find('option').not(':first').remove();
                    for(var i=0; i<len; i++){
                        var id = dataResult['data'][i].driver_name;
                        var name = dataResult['data'][i].driver_name;
                        var option = "<option value='"+id+"'>"+name+"</option>";
                        $("#driver").append(option);
                    }
                }
            }
          }
    });
});



    </script>
@endsection
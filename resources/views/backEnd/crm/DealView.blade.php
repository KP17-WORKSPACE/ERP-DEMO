@extends('backEnd.masterpage')
@section('mainContent')
    @php
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
        $editcheck = App\SysHelper::deal_edit_disable($edit->id);
    @endphp

    
<?php try { ?>

    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3">
                <h2 class="page-heading m-0">Deal Number - {{ $edit->deal_code->code }}</h2>
                <span class="page-label">Home - Deal Details - {{ $edit->companyname->company_name }}</span>
                <input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">
            </div>
            <div>

                {{--  @if($leads->stage==1 || $leads->stage==2 || $leads->stage==3)
                    @if (count($service)==0)
                        <button class="btn btn-primary" data-toggle="modal" data-target="#ModalService">Pre-Sales Support</button>
                    @else
                        <button class="btn btn-primary" data-toggle="modal" data-target="#ModalService">Add Pre-Sales Comments</button>
                    @endif
                @else  --}}

                <a type="button" data-toggle="modal" data-target="#adddeal" class="btn btn-info" data-backdrop="static" data-keyboard="false"><i class="fa fa-plus"></i> New Deal</a>

                @if($leads->stage == 4 || $leads->stage == 1)
                    @if (count($support)==0)
                        <button class="btn btn-primary" data-toggle="modal" data-target="#ModalSupport">Add Pre-Sales Request</button>
                    @else
                        <button class="btn btn-primary" data-toggle="modal" data-target="#ModalSupportCmt">Add Pre-Sales Request Comments</button>
                    @endif
                @endif

                

        <a class="btn btn-primary" data-toggle="modal" data-target="#ModalCollaboration">Add Collaboration</a>
        
        <button class="btn btn-primary" data-toggle="modal" data-target="#ModalEndUserDetails">End User Details</button>

                <a href="{{ url('crm-deals/show') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> View Deals</a>
                <a href="{{url('crm-deals/'.$leads->id.'/edit')}}" type="button" class="btn btn-danger"><i class="fa fa-edit"></i> Edit Deal</a>
                {{--  <a href="{{ url('crm-deals') }}" type="button" class="btn btn-success"><i class="fa fa-plus"></i> New Deal</a>  --}}
                {{-- <a href="{{ url()->previous() }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back</a> --}}
                <!-- Input with Search -->
                <div style="float: left; margin-right:5px; position: relative; width: 200px;">
                    <input type="text" id="quick_search_doc_number" placeholder="Deal ID" class="form-control pr-4" /> 
                    <span style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); color: #aaa; pointer-events: none;">
                    <i class="fas fa-search"></i>
                    </span>
                </div>
                <script>
                    const baseUrl = "{{ url('get-url-deal') }}";                
                    document.getElementById('quick_search_doc_number').addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            var val = this.value.trim();
                            if (val !== '') {                                
                                window.location.href = baseUrl + '/' + val;
                            }
                        }
                    });
                </script>
                <!-- Input with Search -->
            </div>
        </div>


        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100 bg-1">
                    <h2 class="head">Deal Info :-  {!! App\SysHelper::deal_type_new($edit->isproject) !!} @if($edit->is_professional_service==1) <i style="font-size: 11px;">(Project Service)</i>  @endif
                    </h2>
                    <b class="mb-2 text-white-100 text-uppercase">Deal Name : {{ $edit->deal_name }}</b>
                    <p class="mb-2 text-white-100 text-uppercase">
                    @if($edit->tags != "")
                    Brand : 
                        <?php $myArray = explode(',', $edit->tags); ?>
                        @foreach ($myArray as $item)
                        <span class="btn-primary btn-badge py-1 px-3 font-weight-bold">{{ $item }}</span>
                        @endforeach
                    @endif
                    </p>
                    <span class="mb-1">Deal Value : {{ App\SysHelper::currancy_format_deal($leads->deal_value,$leads->company_id) }} {{ $leads->dealcurrency->code }}

                    @if(Auth::user()->id==1 || Auth::user()->id==49)
                    <a class="btn btn-xs btn-danger p-0 pl-1 pr-1" data-toggle="modal" data-target="#ModalDealPercent">                        
                        @if($edit->deal_percent!=0 && $edit->deal_percent!=null){{ $edit->deal_percent }}% Sales Value @else Set Sales % @endif</a></span>
                    @else
                        @if($edit->deal_percent!=0 && $edit->deal_percent!=null)
                        <span class="btn-xs btn-danger p-0 pl-1 pr-1">{{ $edit->deal_percent }}% Sales Value</span>
                        @endif
                        </span>
                    @endif



                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-profit-update', 'method' => 'POST', 'id' => 'crm-deal-profit-update']) }}
                        <input type="hidden" name="profit_deal_id" value="{{ $leads->id }}"/>
                            <table>
                                <tr>
                                    <td>Profit Amount : </td>
                                    <td><input type="text" class="form-control" style="height: 22px;" step="any" id="deal_profit" name="deal_profit" readonly value="{{ App\SysHelper::currancy_format_deal(($leads->deal_profit),$leads->company_id) }} {{ $leads->dealcurrency->code }}" required /></td>
                                    <?php /* <td><button class="btn btn-success float-left p-0 pl-2 pr-2">Update</button></td> */ ?>
                                    <td>
                                        <?php
                                        $dealvalue = $leads->deal_value;
                                        $dealprofit = $leads->deal_profit;
                                        if($dealprofit!=0 && $dealvalue != 0){ $dealpercentage = $dealprofit / $dealvalue * 100; }
                                        else{ $dealpercentage=0; }
                                        ?>
                                        <a class="btn-xs @if($dealpercentage < 0) btn-danger @else btn-success @endif p-0 pl-1 pr-1">{{ @App\SysHelper::com_curr_format($dealpercentage,2,'.',',') }}%</a></td>
                                </tr>
                            </table>
                    {{ Form::close() }}
                    

                    @if ($leads->estimated_close_date != '')
                    <span class="mb-1 mt-2">Estimated Close Date : {{ date('m/d/Y', strtotime($leads->estimated_close_date)) }}</span>
                    @endif

                    <div class="text-capitalize">Stage : <b class="">
                        @if($leads->stage==1) <span class="btn-warning btn-badge py-1 px-2">Prospecting</span> @endif
                        @if($leads->stage==2) <span class="btn-success btn-badge py-1 px-2">Quote</span> @endif
                        @if($leads->stage==3) <span class="btn-info btn-badge py-1 px-2">Closure</span> @endif
                        @if($leads->stage==4) 
                        <?php
                        $data = App\SysHelper::deal_track_status($leads->id);
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
                        
                        @if(App\SysHelper::set_track($leads->id)==1)
                            @if($data=="Fulfill")
                            <a class="{{ $color }} btn-badge py-1 px-2" style="cursor: pointer;" data-toggle="modal" data-target="#ModalDealTrack" title="Click to Fullfill">
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span> {{ $data }} </a>
                            @else
                            <a class="{{ $color }} btn-badge py-1 px-2" href="{{url('crm-deal-track/'.$leads->id.'/view')}}">{{ $data }} </a>
                            
                            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || $check_edit_fullfill == 0)
                            <button class="btn btn-danger btn-badge p-0 m-0 ml-1 pr-1 pl-1" style="cursor: pointer;" data-toggle="modal" data-target="#ModalDealTrackEdit">Edit Fulfill</button>
                            @endif

                            @endif
                        @endif


                        {{--  <button class="btn btn-primary" data-toggle="modal" data-target="#ModalDealTrack">Fulfill</button>  --}}
                            
                        @endif
                        @if($leads->stage==5) <span class="btn-danger btn-badge py-1 px-2">Lost</span> @endif
                        @if($leads->stage==6) <span class="btn-dark btn-badge py-1 px-2">Cancelled</span> @endif
    
                        @if($editcheck==0)
                        <a href="#" class="edit btn-badge rejected py-1 px-3 font-weight-bold ml-2" onclick="updiv()">Edit</a>@endif
                        

                        @if($leads->stage==4)  
                        <button class="btn btn-danger float-right pl-1 pr-1 p-0" data-toggle="modal" data-target="#ModalReturn">Return Deal</button>
                        @endif

                        @if($leads->stage!=6)  

                        
                        <button class="btn btn-danger font-weight-normal ml-2 p-0 pl-2 pr-2" data-toggle="modal" data-target="#ModalCancel">Cancel Deal</button>
                        {{--  <button class="btn btn-warning float-right mr-2 pt-1 pb-1" data-toggle="modal" data-target="#ModalCancel">Cancel Deal</button>  --}}
                        @endif

                    </b>
                    <div class="border border-primary rounded bg-white text-sm p-2" id="div_update" style="display: none;">
                        <select class="dynamicstxt w-50" name="edit_stage" id="edit_stage" required>
                            <option value="">-Select-</option>
                            <option value="1">Prospecting</option>
                            <option value="2">Quote</option>
                            <option value="3">Closure</option>
                            <option value="4">Won</option>
                            <option value="5">Lost</option>
                        </select>
                        <textarea class="primary-input dynamicstxt_s w-100 form-control" name="lost_comments" rows="4" style="height: 50px !important; display: none;" autocomplete="off" id="lost_comments" placeholder="Reason"></textarea>
                        <button id="btn_edit_stage" onclick="change_stage({{ $edit->id }})" class="btn btn-xs btn-primary text-xs pt-0 pb-0">Change</button>
                    </div>
                    <script>
                        function updiv() {
                            if($('#div_update').css('display') == 'none'){
                                $("#div_update").css("display", "block");
                            }
                            else{
                                $("#div_update").css("display", "none");
                            }
                        }
                        $('#edit_stage').on('change', function(e) {
                            if ($('#edit_stage').val() == 5) {
                                $('#lost_comments').css("display", "block");
                                $('#lost_comments').prop('required', true);
                            } else {
                                $('#lost_comments').css("display", "none");
                                $('#lost_comments').prop('required', false);
                            }
                        });
                    </script>
                         </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Customer Info </h2>
                    <h6 class="sub-head text-capitalize text-dark"><a href="{{ url('view-customer/'.$edit->cust_id) }}">{{ $edit->customername->name }}</a></h6>
                    <span class="mb-1"> <span class="font-semibold">Contact :</span> {{ $leads->cust_name }}</span>
                    <span class="mb-1"> <span class="font-semibold">Designation :</span> {{ $leads->designation }}</span>
                    <span class="mb-1"><span class="font-semibold">M :</span> {{ $leads->cust_no }} | <span class="font-semibold">E :</span> {{ $leads->cust_email }}</span>
                    <p class="mb-2 text-gray-800">Add: {{ $edit->address }}</p>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Sales Person Info</h2>
                    <h6 class="sub-head text-capitalize text-dark">{{ $leads->ownername->first_name }} {{ $leads->ownername->middle_name }} {{ $leads->ownername->last_name }}</h6>
                    <span class="mb-1"><span class="font-semibold">M :</span> {{ $leads->ownername->mobile }} | <span class="font-semibold">E :</span> {{ $leads->ownername->email }} | <span class="font-semibold">Ext No :</span> {{ $leads->ownername->ext_no ?? '--' }}</span>
                    <p class="mb-2 text-gray-800">@if ($edit->source != '')Source : {{ $edit->source }}
                        @if ($edit->source_o != '') - {{ $edit->source_o }} @endif @endif</p>
                        <p class="mb-2 text-gray-800">Added On : {{ date('d/m/Y', strtotime(@$leads->date)) }}</p>
                        <p class="mb-2 text-gray-800">Updated On : {{ date('d/m/Y H:i:s', strtotime(@$leads->updated_at)) }}</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-3 h-100">
                <div class="p-4 card bg-2 ">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class=head>Delivery Location /Address</h2>
                        {{--  @if($editcheck==0)  --}}
                        
                        <a href="#" class="bg-white btn-small text-dark" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#ModalAddress">Edit</a>
                        {{--  @endif  --}}
                    </div>
                    {{--  @if (isset($addressbook))
                    <div class="row">
                        <div class="mb-1 col-4"> <b> Company </b></div>
                        <span class="col-8">: {{ $addressbook->customername->name }}</span>
                    </div>
                    <div class="row">
                        <div class="mb-1 col-4"> <b>Address </b></div>
                        <span class="col-8">: {{ $addressbook->address }}</span>
                    </div>
                    <div class="row">
                        <div class="mb-1 col-4"> <b>Contact Person</b></div>
                        <span class="col-8">: {{ $addressbook->contact_person }}</span>
                    </div>
                    <div class="row">
                        <div class="mb-1 col-4"> <b>Mob Num</b></div>
                        <span class="col-8">: {{ $addressbook->contact_number }}</span>
                    </div>
                    <div class="row">
                        <div class="mb-1 col-4"> <b>Email </b></div>
                        <span class="col-8">: {{ $addressbook->contact_email }}</span>
                    </div>
                    @else  --}}
                    <div class="row">
                        <div class="mb-1 col-3"> <b> Company</b></div>
                        <span class="col-9">@if($edit->delivery_company != "") {{ $edit->delivery_company }} @else {{ $edit->customername->name }} @endif</span>
                    </div>
                    
                    <div class="row">
                        <div class="mb-1 col-3"> <b>Contact Person</b></div>
                        <span class="col-9">@if($edit->delivery_name != "") {{ $edit->delivery_name }} @else {{ $leads->cust_name }} @endif</span>
                    </div>
                    <div class="row">
                        <div class="mb-1 col-3"> <b>Telephone</b></div>
                        <span class="col-9">@if($edit->delivery_number != "") {{ $edit->delivery_number }} @else {{ $leads->cust_no }} @endif</span>
                    </div>
                    <div class="row">
                        <div class="mb-1 col-3"> <b>Email</b></div>
                        <span class="col-9">@if($edit->delivery_email != "") {{ $edit->delivery_email }} @else {{ $leads->cust_email }} @endif</span>
                    </div>

                    <div class="row">
                        <div class="mb-1 col-3"> <b>Address 1</b></div>
                        <span class="col-9">@if($edit->delivery_address1 != "") {{ $edit->delivery_address1 }} @else {{ $edit->address }} @endif</span>
                    </div>

                    @if($edit->delivery_address2 != "")
                    <div class="row">
                        <div class="mb-1 col-3"> <b>Address 2</b></div>
                        <span class="col-9">{{ $edit->delivery_address2 }}</span>
                    </div>@endif
                    @if($edit->delivery_city != "")
                    <div class="row">
                        <div class="mb-1 col-3"> <b>City</b></div>
                        <span class="col-9">{{ $edit->delivery_city }}</span>
                    </div>@endif
                    @if($edit->delivery_state != "")
                    <div class="row">
                        <div class="mb-1 col-3"> <b>State</b></div>
                        @if(@$edit->state->name == "")
                            <span class="col-9 font-weight-bold text-danger">Update State</span>
                        @else
                            <span class="col-9">{{ @$edit->state->name }}</span>
                        @endif
                    </div>@endif
                    @if($edit->delivery_country != "")
                    <div class="row">
                        <div class="mb-1 col-3"> <b>Country</b></div>
                        <span class="col-9">{{ @$edit->country->name }}</span>
                    </div>@endif
                    @if($edit->delivery_zip_code != "")
                    <div class="row">
                        <div class="mb-1 col-3"> <b>Zip Code</b></div>
                        <span class="col-9">{{ $edit->delivery_zip_code }}</span>
                    </div>@endif



                    {{--  @endif  --}}

                </div>
                
                
            </div>
            <div class="col-lg-6 mb-3 h-100">
                <div class="p-4 card">
                    <span class="font-weight-bold">Internal Note</span>
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals-comments-add', 'method' => 'POST', 'id' => 'crm-deals-comments-add']) }}
                    <textarea name="comments" class="form-control" id="comments" cols="10" rows="3"></textarea>
                    <input type="file" class="form-control" name="commentsdoc" id="commentsdoc">
                    <input type="hidden" id="commentsid" name="commentsid" value="{{ $edit->id }}" />
                    <div class="mt-2 justify-content-end d-flex">
                        <button type="submit" class=" btn-small">Add Internal Note</button>
                    </div>                        
                    {{ Form::close() }}
                </div>
                <div class="p-4 card mt-2">
                    @if($edit->note != "")<b>Deal Notes :- </b>
                    <div class="notes border-bottom mt-2"> {!! nl2br($edit->note) !!} </div>
                    @endif
                    @if(count($comments)>0)
                    <div class="notes border-bottom mt-3">
                        @foreach ($comments as $cmts)
                        <div>
                            @if ($cmts->created_by == Auth::user()->id)
                            <a href="{{url('crm-deals-comments-delete/'.$cmts->id.'')}}" onclick="return confirm('Are you sure?')"><i class="fa fa-window-close text-sm text-danger float-right" aria-hidden="true"></i></a>
                            @endif
                            <p class="mb-0">{!! nl2br($cmts->comments) !!}
                                @if ($cmts->commentsdoc!="")<br /><br />
                                    <a class="text-info p-0" href="{{asset('public/uploads/crm_deal_doc/')}}/{{ $cmts->commentsdoc }}" target="_blank">&nbsp;&nbsp;<i class="fa fa-paperclip" aria-hidden="true"></i>&nbsp;&nbsp;View File&nbsp;&nbsp;</a>
                                @endif
                                <span class="text-muted text-right">{{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}, On {{date('d/m/Y h:i A', strtotime($cmts->created_at))}}</span>
                            </p>
                            
                        </div>
                        <hr>
                        @endforeach
                    </div>
                    @endif
                </div>


                    



            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-3 h-100">


                @if (count($service)>0)
                <br />
                <div class="p-4 card">
                    <div>
                        <label for="" class="font-weight-bold">Pre-Sales Support</label>
                    </div>
        <div class="pl-3 pr-3 pb-3 pt-2 card mb-3 ">
            <h5 class="sub-head m-0"></h5>
        @foreach ($service as $val)Description:-<br />
        <span class="py-1 px-3 font-weight-bold">{!! nl2br($val->comments) !!}</span>
        <p class="text-muted text-right">Updated By {{ $val->createdby->first_name }} {{ $val->createdby->last_name }}, On {{date('d/m/Y h:i A', strtotime($val->created_at))}}
            @if ($val->status==4)
            <span class="btn-success pl-1 pr-1">Close</span>
            @elseif($val->status==3)
            <span class="btn-warning pl-1 pr-1">On Hold</span>
            @else
            <span class="btn-info pl-1 pr-1">Open</span>
            @endif
        </p>
        @endforeach
        @foreach ($servicecomments as $val)Comments:-<br />
        <span class="py-1 px-3 font-weight-bold">{!! nl2br($val->comments) !!}</span>
        <p class="text-muted text-right">Updated By {{ $val->createdby->first_name }} {{ $val->createdby->last_name }}, On {{date('d/m/Y h:i A', strtotime($val->created_at))}}
            {{--  @if ($val->status==4)
            <span class="btn-success pl-1 pr-1">Compleated</span>
            @elseif($val->status==3)
            <span class="btn-warning pl-1 pr-1">On Hold</span>
            @elseif($val->status==5)
            <span class="btn-danger pl-1 pr-1">Followup</span>
            @else
            <span class="btn-info pl-1 pr-1">On Process</span>
            @endif  --}}
            <hr class="p-0 m-0" />
        </p>
        @endforeach
        </div>
    </div>
    @endif

    

            </div>

            <div class="col-lg-6 h-100 mb-0">
                <div class="p-3 card mb-0" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        
                    @if (count($quoteitems) > 0)
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote/' . $edit->id . '/download/'.$leads->quote_id, 'method' => 'POST', 'id' => 'crm-deals-comments-add']) }}
                    <h2 class="page-heading">Quote</h2>
                        
                    <input class="" type="checkbox" value="1" id="flexCheckDefault1" name="with_partnumber">
                    <label class="pr-3" for="flexCheckDefault1"> With Part No </label>

                    <input class="" type="checkbox" value="1" id="flexCheckDefault2" name="without_vat">
                    <label class="pr-3" for="flexCheckDefault2"> Exclude VAT </label>

                    <input class="" type="checkbox" value="1" id="flexCheckDefault3" name="without_total">
                    <label class="pr-3" for="flexCheckDefault3"> Without Total </label>

                    <button class="btn btn-primary mr-3"><i class="fa fa-download" aria-hidden="true"></i> Download Quotation</button>
                    {{ Form::close() }}
                    @else
                        <h2 class="page-heading">Create Quote</h2>
                        <button class="btn btn-danger" data-toggle="modal" data-target="#ModalQuote">Generate</button>
                    @endif
                    </div>
                </div>
            </div>
        </div>

        @if (count($quoteitems) > 0)
        <div class="row">
            <div class="col-md-12 mb-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center py-4">
                        <h4 class="header-title m-0">Quote items | Quote No {{ $leads->quote_id }}</h4>
                        @if($editcheck==0)
                            <a href="{{ url('crm-quote/' . $edit->id . '/edit/'.$leads->quote_id) }}" class="btn-small"><i class="fa fa-edit" aria-hidden="true"></i> Edit Quotation</a>
                        @endif
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:150px;">@lang('Part No')</th>
                                        <th>@lang('Description')</th>
                                        <th style="width:100px;" class="text-right">@lang('Cost')</th>
                                        <th style="width:70px;" class="text-center">@lang('Qty')</th>
                                        <th style="width:120px;" class="text-right">@lang('Unit Price')</th>
                                        <th style="width:120px;" class="text-right">@lang('Value')</th>
                                        <th style="width:100px;" class="text-right">@lang('Discount')</th>
                                        <th style="width:130px;" class="text-right">@lang('Taxable Amount')</th>
                                        <th style="width:130px;" class="text-right">@lang('VAT Amount')</th>
                                        <th style="width:150px;" class="text-right">@lang('Total')</th>
                                    </tr>
                                </thead>                                
                            <?php $t_qty = 0; $t_value=0; $t_discount=0; $t_taxableamount=0; $t_vatamount=0; $t_price = 0; $t_discount = 0; $t_net_amount = 0;
                            $vat =$quoteitems->max('vat');
                            $net_vat=App\SysHelper::get_vat($quoteitems[0]->currency_id); $currency_id=0; $t_cost=0; ?>
                                <tbody>
                                    <script>
                                        function toggle_tool_tip(id) {
                                            var element = $('#desc_' + id);
                                            var currentWhiteSpace = element.css('white-space');

                                            if (currentWhiteSpace === 'nowrap') {
                                                element.css('white-space', '');
                                            } else {
                                                element.css('white-space', 'nowrap');
                                            }
                                        }
                                    </script>
                                    <input type="hidden" id="technical_detail_hide" />

                                    <script>
                                        function add_ps_id(d){
                                            $('#technical_detail_hide').val(d);
                                        }
                                    </script>

                                    @foreach ($quoteitems as $Item)
                                    @if($Item->status !=0)
                                    @php
                                        $value = $Item->price * $Item->qty;
                                        $taxableamount = $value - $Item->discount;
                                        $vatamount = $taxableamount * $Item->vat / 100;
                                        
                                        $t_cost += $Item->cost * $Item->qty;
                                        $t_qty += $Item->qty;
                                        $t_value += $value;
                                        $t_discount += $Item->discount;
                                        $t_taxableamount += $taxableamount;
                                        $t_vatamount += $vatamount;
                                    @endphp

                                    <tr>
                                        <td><?php try{ ?> {{ $Item->part_number }}
                                            @if ($Item->part_number == "Professional Services")
                                                <script>
                                                    add_ps_id('{{ $Item->description }}');
                                                </script>
                                            @endif
                                            <?php }catch (\Exception $e){} ?></td>
                                        <td>
                                            <div id="desc_{{ $Item->id }}" onclick="toggle_tool_tip({{ $Item->id }})" style="width:350px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{!! nl2br(e($Item->description)) !!}
                                            </div>
                                        </td>
                                        <td class="text-right">{{ $Item->cost }}</td>
                                        <td class="text-center">{{ $Item->qty }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format($Item->price,$Item->currency_id) }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format($value,$Item->currency_id) }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format($Item->discount,$Item->currency_id) }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format($taxableamount,$Item->currency_id) }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format($vatamount,$Item->currency_id) }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format(($taxableamount + $vatamount),$Item->currency_id) }}</td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_cost,$currency_id) }}</th>
                                        <th class="text-center">{{ $t_qty }}</th>
                                        <th></th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_value,$currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_discount,$currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_taxableamount, $currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_vatamount, $currency_id) }}</th>                              
                                        <th class="text-right">{{ $Item->currency->code }} {{ App\SysHelper::currancy_format(($t_taxableamount+$t_vatamount), $currency_id) }}</th>
                                    </tr>
                                    @if($edit->deal_discount > 0)
                                    <tr>
                                        <?php
                                        $deal_discount_taxable_amount = $edit->deal_discount;
                                        $deal_discount_vat_amount = $edit->deal_discount*($vat)/100;
                                        $deal_discount_sum_amount = $deal_discount_taxable_amount+$deal_discount_vat_amount;
                                        ?>
                                        <th colspan="6" class="text-right font-weight-bold">Aditional Discount</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format(($edit->deal_discount), $currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format(($deal_discount_taxable_amount), $currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format(($deal_discount_vat_amount), $currency_id) }}</th>
                                        <th class="text-right">{{ $Item->currency->code }} {{ App\SysHelper::currancy_format(($deal_discount_sum_amount), $currency_id) }}</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_cost,$currency_id) }}</th>
                                        <th class="text-center">{{ $t_qty }}</th>
                                        <th></th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_value,$currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_discount+$edit->deal_discount,$currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_taxableamount-$deal_discount_taxable_amount, $currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_vatamount-$deal_discount_vat_amount, $currency_id) }}</th>                              
                                        <th class="text-right">{{ $Item->currency->code }} {{ App\SysHelper::currancy_format(($t_taxableamount+$t_vatamount-$deal_discount_sum_amount), $currency_id) }}</th>
                                    </tr>
                                    @endif
                                </thead>
                            </table>
                            <br />
                            <table class="table table-nowrap table-centered mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:350px;" class="text-left">Selling Exp Account</th>
                                        <th style="width:350px;" class="text-left">Credit Account</th>
                                        <th style="width:150px;" class="text-right">Amount</th>
                                        <th class="text-left pl-5">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>                                    
                                @if(count($quote_charges) > 0)
                                @foreach ($quote_charges as $charges)
                                <tr>
                                    <td class="text-left">{{ $charges->sellingexpaccount->account_name }}</td>
                                    <td class="text-left">{{ $charges->creditaccount->account_name }}</td>
                                    <td class="text-right">{{ $charges->amount }}</td>
                                    <td class="text-left pl-5">{{ $charges->remarks }}</td>
                                </tr>
                                @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

                    </div> <!-- end card-body-->
                </div> <!-- end card-->

            </div>
        </div>

        @endif

        <div class="row">
            <div class="col-md-6">
        <div class="p-3 card mt-0 mb-3">
            <div class="d-flex  align-items-center">         
                @if (count($quoteitems) > 0)
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote/' . $edit->id . '/download/'.$leads->quote_id, 'method' => 'POST', 'id' => 'crm-deals-comments-add']) }}
                <h2 class="page-heading">Quote</h2>                    
                <input class="" type="checkbox" value="1" id="flexCheckDefault1" name="with_partnumber"> <label class="pr-3" for="flexCheckDefault1"> With Part No </label>
                <input class="" type="checkbox" value="1" id="flexCheckDefault2" name="without_vat"> <label class="pr-3" for="flexCheckDefault2"> Exclude VAT </label>
                <input class="" type="checkbox" value="1" id="flexCheckDefault3" name="without_total"> <label class="pr-3" for="flexCheckDefault3"> Without Total </label>
                <button class="btn btn-primary mr-3"><i class="fa fa-download" aria-hidden="true"></i> Download Quotation</button>
                {{ Form::close() }}

                <a class="btn btn-danger mt-4 ml-2" href="{{ url('crm-quote/'.$edit->id.'/create') }}">Generate Quote</a>
                <?php /*
                    <button class="btn btn-danger mt-3 ml-2" data-toggle="modal" data-target="#ModalQuoteAdd">Generate</button>
                    {{--  Technical  --}}
                    @if(session('logged_session_data.department_id')==3 || Auth::user()->role_id == 1)
                        <button class="btn btn-danger mt-3 ml-2" data-toggle="modal" data-target="#ModalQuoteBOQ">Generate BOQ</button>
                    @endif
                */ ?>                        
                @else
                    <h2 class="page-heading">Create Quote</h2>
                    <a class="btn btn-danger ml-2" href="{{ url('crm-quote/'.$edit->id.'/create') }}">Generate Quote</a>
                    <?php /*
                    <button class="btn btn-danger ml-2" data-toggle="modal" data-target="#ModalQuote">Generate</button>
                    {{--  Technical  --}}
                    @if(session('logged_session_data.department_id')==3 || Auth::user()->role_id == 1)
                        <button class="btn btn-danger ml-2" data-toggle="modal" data-target="#ModalQuoteBOQ">Generate BOQ</button>
                    @endif
                    */ ?>
                @endif
            </div>
        </div>
        <div class="p-3 card mt-0 mb-3">
            <div class="d-flex  align-items-center">  
                <h4 class="header-title m-0">Quote Revisions</h4>
            </div>
            <div class="card-body pt-0">
                <table class="table table-nowrap table-centered mb-0 table-striped">
                <?php $quote_no = App\SysCrmQuoteItems::select('quote_id')->where('deal_id',$edit->id)->groupBy('quote_id')->orderby('quote_id','asc')->get(); ?>
                @foreach ($quote_no as $item)
                <tr><td>
                    <b>Quote No: {{ $edit->deal_code->code }} - {{ $item->quote_id }} </b>
                    <a class="btn btn-info pt-0 pb-0 ml-3" href="{{url('crm-quote/'.$edit->id.'/download/'.$item->quote_id)}}">Download</a>
                    @if($editcheck==0)
                        <a class="btn btn-primary pt-0 pb-0 ml-3" href="{{ url('crm-quote/' . $edit->id . '/edit/'.$item->quote_id) }}">Edit</a>
                        <a class="btn btn-warning pt-0 pb-0 ml-3" href="{{url('crm-quote/'.$edit->id.'/createcopy/'.$item->quote_id)}}">Create Copy</a>                            
                    @endif
                    @if ($item->quote_id != $edit->quote_id)                                
                        @if($editcheck==0)    
                            <a class="btn btn-danger pt-0 pb-0 ml-3" href="{{url('crm-quote/'.$edit->id.'/setprimary/'.$item->quote_id)}}">Set as Final Quote</a>
                        @endif
                    @else
                        <span class="btn-success pt-1 pb-1 pl-2 pr-2 ml-3">Final Quote</span>
                    @endif
                </td>
                </tr>
                @endforeach
            </table>
            </div>
        </div>
            </div>



        
            <div class="col-md-6">
                
                @if (count($support)>0)
                <br />
                <div class="p-4 card">
                    <div>
                        <label for="" class="font-weight-bold">Pre-Sales Support</label>
                    </div>
        <div class="pl-3 pr-3 pb-3 pt-2 card mb-3 ">
            <h5 class="sub-head m-0"></h5>
        @foreach ($support as $val)
        <span class="font-weight-bold"> Site Name:-</span>{!! nl2br($val->site_name) !!}
        <p class="text-muted text-right">Created By {{ $val->createdby->first_name }} {{ $val->createdby->last_name }}, On {{date('d/m/Y h:i A', strtotime($val->created_at))}}</p>
        @if ($val->support_date != null)
        <p class="mb-2 text-dark"><b>Support Time:</b> {{date('d/m/Y', strtotime(@$val->support_date))}}, {{ date('h:i A', strtotime(@$val->time_from)) }} to {{ date('h:i A', strtotime(@$val->time_to)) }}</p>
        @endif

        <?php
            $engineername="";
            if($val->support_person_id != ""){
                $st = explode(', ', $val->support_person_id);
                if(count($st)>0){
                    foreach($st as $u){
                        $s = $staff->where('user_id',$u)->pluck('full_name');
                        if($engineername==""){
                            $engineername .= $s[0];
                        } else { $engineername .= ", " . $s[0]; }
                        
                    }
                }?>                
            <p class="mb-2 text-dark"><b>Support Engineer: </b> {{ @$engineername }}</p>
        <?php
            }
        ?>

        @if (count($support_work)>0)
        @php $i=1; @endphp
        <span class="font-weight-bold"> Scope of Work:-</span>
        @foreach ($support_work as $w)
            {{ $i }}. {{ $w->work }}<br />
            @php $i++; @endphp
        @endforeach
        @endif

<br />
        <hr class="p-0 m-0 mt-1 mb-2" />
        @endforeach
        
        
        @if (count($supportcomments)>0)
        <span class="font-weight-bold">Comments :- </span>
        <hr class="p-0 m-0 mt-1 mb-2" />
        @foreach ($supportcomments as $val)
        <span>{!! nl2br($val->remarks) !!}</span>
        @if ($val->activity_date != null)
                    <p class="mt-2">Support Time: {{date('d/M/Y', strtotime(@$val->activity_date))}}, {{ date('h:i A', strtotime(@$val->activity_from)) }} to {{ date('h:i A', strtotime(@$val->activity_to)) }}</p>
                    @endif
        <p class="text-muted text-right p-0 m-0">By {{ $val->createdby->first_name }} {{ $val->createdby->last_name }}, On {{date('d/m/Y h:i A', strtotime($val->created_at))}}
            <hr class="p-0 m-0" />
        </p>
        @endforeach
        @endif
        </div>
    </div>
    @endif

    
            </div>




        </div>



        

        

    </div>
    
    <script>
        function change_stage(id) {

            $("#loading_bg").css("display", "block");
            var stage = $("#edit_stage").val();
            var comments = $("#lost_comments").val();
            var deal_id = $("#commentsid").val();
            
            if (stage == "" || stage <= 0) {
                alert("Please Choose Stage");
                $("#edit_stage").focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            if (stage == 5 && comments == "") {
                alert("Please Enter Comments");
                $("#lost_comments").focus();
                $("#loading_bg").css("display", "none");
                return false;            
            }
            $("#btn_edit_stage").attr('disabled', true);

            var action = "{{ URL::to('crm-deals-update-stage') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    stage: stage,
                    comments: comments,
                    deal_id: deal_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        //$("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }
    </script>

    <!-- Modal Collaboration-->
    <div class="modal fade" id="ModalCollaboration" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Collaboration</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-collaboration', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="collaboration_deal_id" value="{{ $edit->id }}" />
                <input type="hidden" name="collaboration_cust_id" value="{{ $edit->cust_id }}" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Users</label>
                                <select class="form-control js-example-basic-single" name="user_id[]" multiple>
                                    @foreach ($staff as $value)
                                        <option value="{{ @$value->user_id }}"
                                            @if (isset($collaboration)) @foreach ($collaboration as $coll)
                                        @if ($coll->user_id == $value->user_id) selected @endif
                                            @endforeach
                                    @endif >{{ @$value->full_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{--  <div class="row">
                        <div class="col-md-12">
                            @if (count($collaboration)>0)
                            <hr />
                            <h5 class="sub-head m-0">Collaboration Users</h5><br/>
                            @foreach ($collaboration as $val)
                            <span class="border border-primary rounded py-1 px-3 font-weight-normal">{{ $val->userid->full_name }}</span>
                            @endforeach
                            @endif
                        </div>
                    </div>  --}}
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add to Collaboration</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Collaboration-->

    <!-- Modal Service-->
    <div class="modal fade" id="ModalService" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    @if (count($service)==0)  
                    <h5 class="modal-title" id="exampleModalLabel">Add Pre-Sales</h5>
                    @else
                    <h5 class="modal-title" id="exampleModalLabel">Add Pre-Sales Comments</h5>
                    @endif
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                @if (count($service)==0)                
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-service', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                @else
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-service-comments-additional', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <input type="hidden" name="service_id" value="{{ $service[0]->id }}" />
                    <input type="hidden" name="status" value="5" />
                @endif
                
                <input type="hidden" name="service_deal_id" value="{{ $edit->id }}" />
                <input type="hidden" name="service_cust_id" value="{{ $edit->cust_id }}" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="comments" id="comments" rows="5"></textarea>
                            </div>
                        </div>
                        
                        <?php /*
                        @if (count($service)==0) 
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Part Number</label>
                                <select class="form-control js-example-basic-single" name="part_number[]" id="part_number" multiple>
                                    @foreach ($product_list as $value)
                                        <option value="{{ @$value->part_number }}">{{ @$value->part_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Users</label>
                                <select class="form-control js-example-basic-single" name="user_id[]" id="user_id" multiple>
                                    @foreach ($support_person as $value)
                                        <option value="{{ @$value->user_id }}" >{{ @$value->full_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        */ ?>

                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    @if (count($service)==0)  
                    <button type="submit" class="btn btn-primary">Add to Pre-Sales</button>
                    @else
                    <button type="submit" class="btn btn-primary">Add Comments</button>
                    @endif
                </div>
                {{ Form::close() }}

            </div>
        </div>
    </div>
    <!-- Modal Service-->

    <!-- Modal End User -->
    <div class="modal fade" id="ModalEndUserDetails" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header"> 
                    <h5 class="modal-title" id="exampleModalLabel">End User Details</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
    @if ($enduser=="")
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-add-end-user', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="end_user_deal_id" value="{{ $edit->id }}" />
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Ultimate End User Company Name *</label>
                                <input type="text" class="form-control" name="end_user_company_name" id="end_user_company_name" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Address *</label>
                                <input type="text" class="form-control" name="address_line_a" id="address_line_a" required />
                            </div>
                        </div>
                        {{--  <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Address Line 2</label>
                                <input type="text" class="form-control" name="address_line_b" id="address_line_b" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">City</label>
                                <input type="text" class="form-control" name="city" id="city" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">PO. Box</label>
                                <input type="text" class="form-control" name="po_box" id="po_box" />
                            </div>
                        </div>  --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">End User Contact Person *</label>
                                <input type="text" class="form-control" name="end_user_contact_person" id="end_user_contact_person" required />
                            </div>
                        </div>
                        {{--  <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Job Title</label>
                                <input type="text" class="form-control" name="job_title" id="job_title" />
                            </div>
                        </div>  --}}
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Mobile No</label>
                                <input type="text" class="form-control" name="mobile_no" id="mobile_no" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="email" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Project Name</label>
                                <input type="text" class="form-control" name="project_name" id="project_name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Brief description about this project</label>
                                <textarea class="form-control" name="project_description" id="project_description"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">When it is expected to Close</label>
                                <input type="date" class="form-control" name="expected_close_date" id="expected_close_date" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
                {{ Form::close() }}        
                @else
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="" class="form-label">Ultimate End User Company Name </label> : {{ $enduser->end_user_company_name }}<hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">Address</label> : {{ $enduser->address_line_a }}<hr class="m-0 p-0 mb-1" />
                            {{--  <label for="" class="form-label">Address Line 2</label> : {{ $enduser->address_line_b }}<hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">City</label> : {{ $enduser->city }}<hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">PO. Box</label> : {{ $enduser->po_box }}<hr class="m-0 p-0 mb-1" />  --}}
                            <label for="" class="form-label">End User Contact Person</label> : {{ $enduser->end_user_contact_person }}<hr class="m-0 p-0 mb-1" />
                            {{--  <label for="" class="form-label">Job Title</label> : {{ $enduser->job_title }}<hr class="m-0 p-0 mb-1" />  --}}
                            <label for="" class="form-label">Mobile No</label> : {{ $enduser->mobile_no }}<hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">Email</label> : {{ $enduser->email }}<hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">Project Name</label> : {{ $enduser->project_name }}<hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">Brief description about this project</label> : {{ $enduser->project_description }}<hr class="m-0 p-0 mb-1" />
                            <label for="" class="form-label">When it is expected to Close</label> : {{ date('d-M-Y', strtotime($enduser->expected_close_date)) }}
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
                <!-- Modal End User -->

    <!-- Modal Address-->
    <style>
        .modal-dialog {
            right:0px;
            position: fixed;
            z-index: 9999;
        }
        </style>
        <script>
            $(document).on("change", "#delivery_company", function () {
            var name = $("#delivery_company").val();
            get_cust_name(name);
        });
        function get_cust_name(name) {
            $("#loading_bg").css("display", "block");
            var action = "{{ URL::to('crm-deals-customername') }}";
            $.ajax({
                url: action,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    var len = 0;
                        if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                        }
                        if(len > 0){
                            for(var i=0; i<len; i++){
                                var name = dataResult['data'][i].first_name +' '+ dataResult['data'][i].last_name;
                                $("#delivery_name").val(name.replace('null ','').replace('null',''));
                                $("#delivery_number").val(dataResult['data'][i].mobile);
                                $("#delivery_email").val(dataResult['data'][i].email);
                                $("#delivery_address1").val(dataResult['data'][i].address);
                                $("#delivery_address2").val(dataResult['data'][i].address2);
                                
                                $("#delivery_city").val(dataResult['data'][i].city);
                                $("#delivery_zip_code").val(dataResult['data'][i].zip_code);
                                $("#delivery_state").val(dataResult['data'][i].vat_state);
                                $("#delivery_country").val(dataResult['data'][i].vat_country);
                                
                            }
                        }
                        else{
                            $("#delivery_name").val('');
                            $("#delivery_number").val('');
                            $("#delivery_email").val('');
                            $("#cust_email").val('');
                            $("#delivery_address1").val('');
                            $("#delivery_address2").val('');
                            $("#delivery_city").val('');
                            $("#delivery_zip_code").val('');
                            $("#delivery_state").val('');
                            $("#delivery_country").val('');
                        }
                        $("#loading_bg").css("display", "none");
                }
            });
        }
        </script>
    <div class="modal fade bd-example-modal-lg" id="ModalAddress" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Delivery Address</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>  
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-changedeliveryaddress', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="cust_deal_id" value="{{ $edit->id }}" />
                        <input type="hidden" name="cust_id" value="{{ $edit->cust_id }}" />
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Company Name</label>
                                <select class="form-control js-example-basic-single" name="delivery_company" id="delivery_company" required>
                                    <option value="">-Select-</option>
                                    @foreach ($cust_supp as $value)
                                    <option value="{{ @$value->name }}" {{ isset($edit) ? (!empty($edit->delivery_company) ? (@$edit->delivery_company == @$value->name ? 'selected' : '') : '') : '' }}>{{ @$value->name }}
                                    </option>
                                    @endforeach
                                </select>

                                {{-- <input class="form-control" name="delivery_company" id="delivery_company" value="@if($edit->delivery_company != "") {{ $edit->delivery_company }} @else {{ $edit->customername->name }} @endif" required /> --}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Address 1</label>
                                <input class="form-control" type="text" id="delivery_address1" name="delivery_address1" placeholder="" value="@if($edit->delivery_address1 != "") {{ $edit->delivery_address1 }} @else {{ $addressbook->address }} @endif" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" name="delivery_name" id="delivery_name" value="@if($edit->delivery_name != "") {{ $edit->delivery_name }} @else {{ $leads->cust_name }} @endif" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Address 2</label>
                                <input class="form-control" type="text" id="delivery_address2" name="delivery_address2" placeholder="" value="@if($edit->delivery_address2 != "") {{ $edit->delivery_address2 }} @else {{ $addressbook->address2 }} @endif" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" name="delivery_number" id="delivery_number" value="@if($edit->delivery_number != "") {{ $edit->delivery_number }} @else {{ $leads->cust_no }} @endif" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">City</label>
                                <input class="form-control" type="text" id="delivery_city" name="delivery_city" placeholder="" value="@if($edit->delivery_city != "") {{ $edit->delivery_city }} @else {{ $addressbook->city }} @endif" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" name="delivery_email" id="delivery_email" value="@if($edit->delivery_email != "") {{ $edit->delivery_email }} @else {{ $leads->cust_email }} @endif" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Country</label>
                                <select class="form-control" id="country_n_e" name="delivery_country" required>
                                    <option data-display="" value=""></option>
                                    @foreach ($countries as $key => $value)
                                        <option value="{{ @$value->id }}" @if($edit->delivery_country != "") @if($edit->delivery_country == $value->id) selected @endif @else @if($addressbook->country == $value->id) selected @endif @endif >{{ @$value->name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">PO Box</label>
                                <input class="form-control" type="text" name="delivery_zip_code" placeholder="" value="@if($edit->delivery_zip_code != "") {{ $edit->delivery_zip_code }} @else {{ $addressbook->zip_code }} @endif" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">State</label>
                                <div id="sectionStateDiv_n_e">
                                    <select class="form-control" id="state_n_e" name="delivery_state" required>
                                        <option data-display="" value=""></option>
                                        <?php try { ?>
                                        @if (isset($states))
                                            @foreach ($states as $st)
                                                <option data-display="{{ $st->name }}" @if($edit->delivery_state != "") @if($edit->delivery_state == $st->id) selected @endif @else @if($addressbook->state == $st->id) selected @endif @endif value="{{ $st->id }}"> {{ $st->name }}</option>
                                            @endforeach
                                        @endif
                                        <?php }catch (\Exception $e) {   } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        
                        {{--  <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Delivery Address</label>
                                <textarea class="form-control" name="delivery_address" id="delivery_address" required>@if($edit->delivery_address != "") {{ $edit->delivery_address }} @else {{ $edit->address }} @endif</textarea>
                            </div>
                        </div>  --}}


                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" id="btn_contact_update" class="btn btn-primary">Update</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Address-->

    <!-- Modal Cancel-->
    <div class="modal fade" id="ModalCancel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Deal Cancel</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-cancel', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="cancel_deal_id" value="{{ $edit->id }}" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Reason</label>
                                <textarea class="form-control" name="reason" id="reason" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel Deal</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Cancel-->

    <!-- Modal DealPercent-->
    <div class="modal fade" id="ModalDealPercent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Set Deal Value Percent (Sales Person)</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-percent', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="deal_percent_id" value="{{ $edit->id }}" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal Value Percent</label>
                                <input type="number" class="form-control" name="deal_percent" id="deal_percent" value="{{ $edit->deal_percent }}" required />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Update Deal Percent</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Cancel-->

    

    <!-- Modal Return-->
    <div class="modal fade" id="ModalReturn" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Return Deal</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-return-submit', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="return_deal_id" value="{{ $edit->id }}" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Reason</label>
                                <textarea class="form-control" name="return_remarks" id="return_remarks" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Return Deal</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Cancel-->

    <!-- Modal Quote-->
    <div class="modal fade" id="ModalQuote" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Quote</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                {{ Form::open(['route' => 'quote.index', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) }}
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="cust_id" value="{{ $edit->cust_id }}" />
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Company</label>
                                <select class="form-control js-example-basic-single" name="company_id" id="company_id" required>
                                    <option value="">-Select-</option>
                                    @foreach ($companylist as $value)
                                        <option value="{{ @$value->id }}" @if($value->id == session('logged_session_data.company_id')) selected @endif>{{ @$value->company_name }} - {{ @$value->city }}</option>
                                    @endforeach
                                </select>
                                <script>
                                    $('#company_id').on('change', function(e) {
                                        
                                            if ($('#company_id').val() == 3) {var $txt = "Magnus Infotech Trading LLC";}
                                            if ($('#company_id').val() == 4) {var $txt = "Supreme KSA";}
                                            if ($('#company_id').val() == 6) {var $txt = "Supreme System Distributors SPC";}
                                            if ($('#company_id').val() == 10) {var $txt = "Syscom Distribution";}
                                            if ($('#company_id').val() == 11) {var $txt = "Syscom Distribution INC";}
                                            if ($('#company_id').val() == 8) {var $txt = "Syscom Distribution WLL";}
                                            if ($('#company_id').val() == 1) {var $txt = "Syscom Distributions LLC";}
                                            if ($('#company_id').val() == 9) {var $txt = "Syscom Distributions LLC";}
                                            if ($('#company_id').val() == 7) {var $txt = "Syscom Distributions LLC";}
                                            if ($('#company_id').val() == 5) {var $txt = "Syscom FZE";}
                                            var $tc="1. Quote/Order will be subject to approval of payment/credit terms by our finance.\n2. Please mention our Quotation No.in your Purchase Order\n3. In case of non-availability of quote products "+$txt+" reserved the rights to supply a functionally similar or better product.";
                                            $('#terms_and_condition').val($tc);                                            
                                        
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Customer Type</label>
                                <select class="form-control" name="customer_type" id="customer_type" required>
                                    <option value="">-Select-</option>
                                    <option value="1">Reseller</option>
                                    <option value="2">Enduser</option>
                                </select>
                            </div>
                        </div>                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Quote Validity</label>
                                <input class="form-control" id="quote_validity" type="text" autocomplete="off" placeholder="Quote Validity" name="quote_validity" value="2 Weeks" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Payment Terms</label>
                                <select class="form-control" name="payment_terms" id="payment_terms" required>
                                    <option value="">-Select-</option>
                                    @foreach ($paymentterms as $key => $value)
                                        <option value="{{ @$value->id }}">{{ @$value->title }}</option>
                                    @endforeach
                                </select>
                                <input class="form-control" id="payment_terms_txt" type="text"
                                    autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
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
                                </script>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Delivery Time</label>
                                <input class="form-control" id="delivery_time" type="text" autocomplete="off" placeholder="Delivery Time" name="delivery_time" value="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Currency</label>
                                <select class="form-control" name="currency_id" id="currency_id" required>
                                    <option value="">-Select-</option>
                                    @foreach ($currencylist as $value)
                                        <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Closing Date</label>
                                <input class="form-control" id="delivery_date" type="date" name="delivery_date" value="" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Terms and Condition</label>
                                <textarea class="form-control" rows="6" id="terms_and_condition" autocomplete="off" name="terms_and_condition">1. Quote/Order will be subject to approval of payment/credit terms by our finance.
2. Please mention our Quotation No.in your Purchase Order
3. In case of non-availability of quote products {{ $edit->companyname->company_name }} reserved the rights to supply a functionally similar or better product.</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    
                    <input type="hidden" id="deal_id" name="deal_id" value="{{ $edit->id }}">
                    @if($editcheck==0)
                    <button class="btn btn-primary" name="submit" value="GQ">Generate Quote</button>@endif
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Quote-->

    <!-- Modal Quote Add Another-->
    <div class="modal fade" id="ModalQuoteAdd" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Quote</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                {{ Form::open(['route' => 'quote.index', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) }}
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="cust_id" value="{{ $edit->cust_id }}" />
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Company</label>
                                <select class="form-control js-example-basic-single" name="company_id" id="company_id" required>
                                    <option value="">-Select-</option>
                                    @foreach ($companylist as $value)
                                        <option value="{{ @$value->id }}" @if($value->id == session('logged_session_data.company_id')) selected @endif>{{ @$value->company_name }} - {{ @$value->city }}</option>
                                    @endforeach
                                </select>
                                <script>
                                    $('#company_id').on('change', function(e) {                                        
                                            if ($('#company_id').val() == 3) {var $txt = "Magnus Infotech Trading LLC";}
                                            if ($('#company_id').val() == 4) {var $txt = "Supreme KSA";}
                                            if ($('#company_id').val() == 6) {var $txt = "Supreme System Distributors SPC";}
                                            if ($('#company_id').val() == 10) {var $txt = "Syscom Distribution";}
                                            if ($('#company_id').val() == 11) {var $txt = "Syscom Distribution INC";}
                                            if ($('#company_id').val() == 8) {var $txt = "Syscom Distribution WLL";}
                                            if ($('#company_id').val() == 1) {var $txt = "Syscom Distributions LLC";}
                                            if ($('#company_id').val() == 9) {var $txt = "Syscom Distributions LLC";}
                                            if ($('#company_id').val() == 7) {var $txt = "Syscom Distributions LLC";}
                                            if ($('#company_id').val() == 5) {var $txt = "Syscom FZE";}
                                            var $tc="1. Quote/Order will be subject to approval of payment/credit terms by our finance.\n2. Please mention our Quotation No.in your Purchase Order\n3. In case of non-availability of quote products "+$txt+" reserved the rights to supply a functionally similar or better product.";
                                            $('#terms_and_condition').val($tc);                                            
                                        
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Customer Type</label>
                                <select class="form-control" name="customer_type" id="customer_type" required>
                                    <option value="">-Select-</option>
                                    <option value="1">Reseller</option>
                                    <option value="2">Enduser</option>
                                </select>
                            </div>
                        </div>                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Quote Validity</label>
                                <input class="form-control" id="quote_validity" type="text" autocomplete="off" placeholder="Quote Validity" name="quote_validity" value="2 Weeks">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Payment Terms</label>
                                <select class="form-control" name="payment_terms" id="payment_terms" required>
                                    <option value="">-Select-</option>
                                    @foreach ($paymentterms as $key => $value)
                                        <option value="{{ @$value->id }}">{{ @$value->title }}</option>
                                    @endforeach
                                </select>
                                <input class="form-control" id="payment_terms_txt" type="text"
                                    autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
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
                                </script>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Delivery Time</label>
                                <input class="form-control" id="delivery_time" type="text" autocomplete="off" placeholder="Delivery Time" name="delivery_time" value="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Currency</label>
                                <select class="form-control" name="currency_id" id="currency_id" required>
                                    <option value="">-Select-</option>
                                    @foreach ($currencylist as $value)
                                        <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Closing Date</label>
                                <input class="form-control" id="delivery_date" type="date" name="delivery_date" value="" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Terms and Condition</label>
                                <textarea class="form-control" rows="6" id="terms_and_condition" autocomplete="off" name="terms_and_condition">1. Quote/Order will be subject to approval of payment/credit terms by our finance.
2. Please mention our Quotation No.in your Purchase Order
3. In case of non-availability of quote products {{ $edit->companyname->company_name }} reserved the rights to supply a functionally similar or better product.</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    
                    <input type="hidden" id="deal_id" name="deal_id" value="{{ $edit->id }}">
                    @if($editcheck==0)
                    <button class="btn btn-primary" name="submit" value="GQ">Generate Quote</button>@endif
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Quote Another-->

    <!-- Modal Quote BOQ-->
    <div class="modal fade" id="ModalQuoteBOQ" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Quote</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-boq-create', 'method' => 'POST', 'id' => 'crm-leads-search']) }}
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="cust_id" value="{{ $edit->cust_id }}" />
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Company</label>
                                <select class="form-control js-example-basic-single" name="company_id" id="company_id" required>
                                    <option value="">-Select-</option>
                                    @foreach ($companylist as $value)
                                        <option value="{{ @$value->id }}" @if($value->id == session('logged_session_data.company_id')) selected @endif>{{ @$value->company_name }} - {{ @$value->city }}</option>
                                    @endforeach
                                </select>
                                <script>
                                    $('#company_id').on('change', function(e) {
                                        
                                            if ($('#company_id').val() == 3) {var $txt = "Magnus Infotech Trading LLC";}
                                            if ($('#company_id').val() == 4) {var $txt = "Supreme KSA";}
                                            if ($('#company_id').val() == 6) {var $txt = "Supreme System Distributors SPC";}
                                            if ($('#company_id').val() == 10) {var $txt = "Syscom Distribution";}
                                            if ($('#company_id').val() == 11) {var $txt = "Syscom Distribution INC";}
                                            if ($('#company_id').val() == 8) {var $txt = "Syscom Distribution WLL";}
                                            if ($('#company_id').val() == 1) {var $txt = "Syscom Distributions LLC";}
                                            if ($('#company_id').val() == 9) {var $txt = "Syscom Distributions LLC";}
                                            if ($('#company_id').val() == 7) {var $txt = "Syscom Distributions LLC";}
                                            if ($('#company_id').val() == 5) {var $txt = "Syscom FZE";}
                                            var $tc="1. Quote/Order will be subject to approval of payment/credit terms by our finance.\n2. Please mention our Quotation No.in your Purchase Order\n3. In case of non-availability of quote products "+$txt+" reserved the rights to supply a functionally similar or better product.";
                                            $('#terms_and_condition').val($tc);                                            
                                        
                                    });
                                </script>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Customer Type</label>
                                <select class="form-control" name="customer_type" id="customer_type" required>
                                    <option value="">-Select-</option>
                                    <option value="1">Reseller</option>
                                    <option value="2">Enduser</option>
                                </select>
                            </div>
                        </div>                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Quote Validity</label>
                                <input class="form-control" id="quote_validity" type="text" autocomplete="off" placeholder="Quote Validity" name="quote_validity" value="2 Weeks" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Payment Terms</label>
                                <select class="form-control" name="payment_terms" id="payment_terms" required>
                                    <option value="">-Select-</option>
                                    @foreach ($paymentterms as $key => $value)
                                        <option value="{{ @$value->id }}">{{ @$value->title }}</option>
                                    @endforeach
                                </select>
                                <input class="form-control" id="payment_terms_txt" type="text"
                                    autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
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
                                </script>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="" class="form-label">Delivery Time</label>
                                <input class="form-control" id="delivery_time" type="text" autocomplete="off" placeholder="Delivery Time" name="delivery_time" value="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Currency</label>
                                <select class="form-control" name="currency_id" id="currency_id" required>
                                    <option value="">-Select-</option>
                                    @foreach ($currencylist as $value)
                                        <option value="{{ @$value->id }}">{{ @$value->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="" class="form-label">Closing Date</label>
                                <input class="form-control" id="delivery_date" type="date" name="delivery_date" value="" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Terms and Condition</label>
                                <textarea class="form-control" rows="6" id="terms_and_condition" autocomplete="off" name="terms_and_condition">1. Quote/Order will be subject to approval of payment/credit terms by our finance.
2. Please mention our Quotation No.in your Purchase Order
3. In case of non-availability of quote products {{ $edit->companyname->company_name }} reserved the rights to supply a functionally similar or better product.</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    
                    <input type="hidden" id="deal_id" name="deal_id" value="{{ $edit->id }}">
                    @if($editcheck==0)
                    <button class="btn btn-primary" name="submit" value="GQ">Generate Quote</button>@endif
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Quote BOQ-->

    <!-- Modal Support-->
    <div class="modal fade" id="ModalSupport" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Pre-Sales Request</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="support_id" value="0" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="mb-3">
                                <label for="" class="form-label">Customer</label>
                                <input type="text" class="form-control" value="{{ $edit->customername->name }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="" class="form-label">Deal Id</label>
                                <input type="number" class="form-control" value="{{ $edit->deal_code->code }}" readonly>
                                <input type="hidden" name="deal_id" id="deal_id" value="{{ $edit->id }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="site_name" id="site_name" value="{{ $edit->address }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">Date</label>
                                <input type="date" class="form-control" name="support_date" id="support_date" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">From</label>
                                <input type="time" class="form-control" name="time_from" id="time_from" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="" class="form-label">To</label>
                                <input type="time" class="form-control" name="time_to" id="time_to" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Scope of Work</label>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <a onclick="add_scope_of_work()" class="btn-sm btn-primary float-right"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
                                        
                                        <table width="100%">
                                            <tr><td width="1%">1. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_1" required></td><td width="1%"></td></tr>
                                            @for ($i=2; $i<=20; $i++)
                                            <tr id="row_{{ $i }}" style="display:none;"><td>{{ $i }}. </td><td><input type="text" class="form-control" name="scope_of_work[]" id="scope_of_work_{{ $i }}"></td>
                                            <td><a class="btn-sm btn-danger float-right" onclick="delete_work({{ $i }})"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            
                                            </td></tr>
                                            @endfor
                                        </table>
                                        <input type="hidden" id="scope_of_work_row_id" value="1" />
                                        <script>
                                            function add_scope_of_work(){
                                                var scope = $('#scope_of_work_row_id').val();
                                                if($('#scope_of_work_'+scope).val() != ""){
                                                    scope++;
                                                    $('#row_'+scope).css('display','');
                                                    $('#scope_of_work_row_id').val(scope);
                                                    $('#scope_of_work_'+scope).prop("required", true);
                                                }
                                            }
                                            function delete_work(id){
                                                $('#row_'+id).css('display','none');
                                                $('#scope_of_work_'+id).val('');
                                            }
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="customer_id" id="customer_id" required value="{{ $edit->cust_id }}" />
                    <input type="hidden" name="sales_person_id" id="sales_person_id" required value="{{ $leads->owner }}" />
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Service</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Support-->
    <!-- Modal Support Cmt-->
    <div class="modal fade" id="ModalSupportCmt" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Service Comments</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-support-activity-comments', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                @if (count($support)!=0)
                    <input type="hidden" name="support_id" value="{{ $support[0]->id }}" />
                @endif
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Comments</label>
                                <textarea class="form-control" name="remarks" id="remarks" rows="10" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Comments</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Support Cmt-->


    <!-- Modal Deal Track-->
    <div class="modal fade" id="ModalDealTrack" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
            <div class="modal-content">
                <div class="modal-header">                    
                    <h5 class="modal-title" id="exampleModalLabel">Deal Track (Deal ID - {{ $edit->deal_code->code }})</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                @if (App\SysHelper::get_company_status($edit->customername)==0)
                <div style="padding: 100px 20px; width: 100%; text-align: center;">
                    <h5>Customer Information is Incomplete! Please Update Customer.</h5>
                    <a class="btn-sm btn-primary" target="_blank" href="{{url('customer-edit', $edit->customername->id)}}">Click Here to Update</a>
                </div>                
                @else
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-submit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-leads-form']) }}
                
                @php 
                $delivery_date="";
                $payment_terms="";
                $payment_mode="";
                $purchease_required="";
                $partial_delivery="";
                $technical="";
                $technical_detail="";
                $lpo="";
                $cheque_copy="";
                $purchease_quote="";
                $remarks="";
                $reference_no="";
                $reference_date="";
                $purchease_approval=0;
                $invoice_approval=1;
                $delivery_approval=1;
                $receivables_approval=1;
                $start_date="";
                $end_date="";

                if(isset($deal_track_temp)){
                    $delivery_date=$deal_track_temp->delivery_date;
                    $payment_terms=$deal_track_temp->payment_terms;
                    $payment_mode=$deal_track_temp->payment_mode;
                    $purchease_required=$deal_track_temp->purchease_required;
                    $partial_delivery=$deal_track_temp->partial_delivery;
                    $technical=$deal_track_temp->technical;
                    $technical_detail=$deal_track_temp->technical_detail;
                    $lpo=$deal_track_temp->lpo;
                    $cheque_copy=$deal_track_temp->cheque_copy;
                    $purchease_quote=$deal_track_temp->purchease_quote;
                    $remarks=$deal_track_temp->remarks;
                    $reference_no=$deal_track_temp->reference_no;
                    $reference_date=$deal_track_temp->reference_date;
                    $purchease_approval=$deal_track_temp->purchease_approval;
                    $invoice_approval=$deal_track_temp->invoice_approval;
                    $delivery_approval=$deal_track_temp->delivery_approval;
                    $receivables_approval=$deal_track_temp->receivables_approval;
                    $start_date=$deal_track_temp->start_date;
                    $end_date=$deal_track_temp->end_date;
                    $invoicing = $deal_track_temp->invoicing;
                }
                @endphp
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Expected Delivery Date')<span></span></label>
                                        
                                        <input class="form-control" id="delivery_date" type="date" autocomplete="off" required name="delivery_date" value="{{ $delivery_date }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="input-effect">
                                <label class="txtlbl">Payment Terms<span></span></label>
                                <select class="form-control" name="payment_terms" id="payment_terms1" required>
                                    <option value="">-Select-</option>
                                @foreach ($paymentterms as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if($payment_terms !="") @if (@$payment_terms == @$value->id) selected @endif 
                                        @else
                                        @if (isset($quoteitems)) @if (@$quoteitems[0]->payment_terms == @$value->id) selected @endif @endif
                                        @endif
                                        >{{ @$value->title }}</option>
                                @endforeach                                                    
                                </select>
                                <script>
                                    $('#payment_terms1').on('change', function(e) {
                                        if ($('#payment_terms1').val() == 20 || $('#payment_terms1').val() == 21) {
                                            $('#payment_mode_sec_div').css("display", "none");
                                            //$('#payment_mode_sec').prop('required', true);
                                        } else {
                                            $('#payment_mode_sec_div').css("display", "none");
                                            //$('#payment_mode_sec').prop('required', false);
                                        }

                                        if($('#payment_terms1').val() == 1 || $('#payment_terms1').val() == 2){
                                            $('#payment_mode').val(1);
                                        } else { $('#payment_mode').val(2); }
                                        
                                        if ($('#payment_terms1').val() == 22) {
                                            $('#payment_terms1_txt').css("display", "block");
                                            $('#payment_terms1_txt').prop('required', true);
                                        } else {
                                            $('#payment_terms1_txt').css("display", "none");
                                            $('#payment_terms1_txt').prop('required', false);
                                        }
                                    });
                                </script>
                                <input class="form-control" id="payment_terms1_txt" type="text" value="" autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
                            </div>
                        </div>
                        @php
                        $mode_sel=0;
                        if(@$quoteitems[0]->payment_terms== 1 || @$quoteitems[0]->payment_terms== 2){ $mode_sel=1;} else { $mode_sel=2;} 

                        @endphp
                        <div class="col-lg-4 mb-3">
                            <div class="input-effect">
                                <label class="txtlbl">Payment Mode<span></span></label>
                                <select class="form-control" name="payment_mode" id="payment_mode" required>
                                    <option value="">-Select-</option>
                                    <option value="1" @if($payment_mode==1) selected @else @if($mode_sel==1) selected @endif @endif>Cash</option>
                                    <option value="2" @if($payment_mode==2) selected @else @if($mode_sel==2) selected @endif @endif>Cheque</option>
                                    <option value="3" @if($payment_mode==3) selected @endif>Bank Transfer</option>
                                    <option value="4" @if($payment_mode==4) selected @endif>Open Credit</option>
                                    <option value="5" @if($payment_mode==5) selected @endif>Credit Card</option>
                                    <option value="6" @if($payment_mode==6) selected @endif>Bank TT</option>
                                    <option value="7" @if($payment_mode==7) selected @endif>Letter of Credit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3" id="payment_mode_sec_div" style="display: none;">
                            <div class="input-effect">
                                <label class="txtlbl">Payment Mode<span></span></label>
                                <select class="form-control" name="payment_mode_sec" id="payment_mode_sec" >
                                    <option value="">-Select-</option>
                                    <option value="1">Cash</option>
                                    <option value="2">Cheque</option>
                                    <option value="3">Bank Transfer</option>
                                    <option value="4">Open Credit</option>
                                    <option value="5">Credit Card</option>
                                    <option value="6">Bank TT</option>
                                    <option value="7">Letter of Credit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect ">
                                <label class="txtlbl">Purchase Required<span></span></label><br />
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="purchease_required" name="purchease_required" @if($purchease_required==1) checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="purchease_required">Yes, Required</label></div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect">
                                <label class="txtlbl">Partial Delivery<span></span></label><br />
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="partial1" name="partial_delivery" @if($partial_delivery==1) checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="partial1">Yes, Partial Delivery</label></div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect">
                                <label class="txtlbl">Professional Service<span></span></label><br />
                                <div class="form-control">
                                <input type="hidden" name="technical" value="0" />
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="technical1" name="technical" @if($technical==1 ||$edit->is_professional_service == 1 ) checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="technical1">Yes, Professional Service</label></div>
                            </div>
                            <script>
                                $('#technical1').on('change', function(e) {
                                    if ($('#technical1').prop('checked') == true) {
                                        $('#technical_div').css("display", "block");
                                        $('#technical_detail').prop('required', true);
                                        $('#technical_detail').val($('#technical_detail_hide').val());
                                    } else {
                                        $('#technical_div').css("display", "none");
                                        $('#technical_detail').prop('required', false);
                                    }
                                });
                            </script>
                        </div>
                        @if($is_amc_item >0 )
                        <div class="col-lg-2 mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Start Date')<span></span></label>
                                        <input class="form-control" id="start_date" type="date" autocomplete="off" required name="start_date" value="{{ $start_date }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('End Date')<span></span></label>
                                        <input class="form-control" id="end_date" type="date" autocomplete="off" required name="end_date" value="{{ $end_date }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 mb-2">
                            <div class="form-group">
                                <label for="">Invoicing</label>
                                <select class="form-control" type="text" name="amc_invoice" id="amc_invoice" required>
                                    <option value="">-Select-</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Half Yearly">Half Yearly</option>
                                    <option value="Yearly" selected>Yearly</option>
                                </select>
                            </div>
                        </div>




                        @endif
                        <div class="col-lg-3 mb-3" id="technical_div" style="display: none;">
                            <div class="input-effect">
                                <label class="txtlbl">Professional Service Note<span></span></label><br />
                                <textarea class="dynamicstxt_s w-100 form-control" style="height: 35px !important" name="technical_detail" rows="4" autocomplete="off" id="technical_detail" placeholder="Remarks">{{ $technical_detail }}</textarea>
                            </div>
                        </div>
                        @if($technical==1 ||$edit->is_professional_service == 1 )
                        <script>
                            $('#technical_div').css("display", "block");
                            $('#technical_detail').prop('required', true);
                        </script>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect ">
                                <label class="txtlbl">Purchase Approval<span></span></label><br />
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="purchease_approval" name="purchease_approval" @if($purchease_approval==0) @else checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="purchease_approval">Yes, Required</label></div>
                            </div>
                        </div>
                        <script>
                            $(document).ready(function() {
                                $('#purchease_required').change(function() {
                                    if(this.checked) {
                                        $('#purchease_approval').attr("checked", true);
                                        $('#purchease_required').attr("checked", true);                                        
                                    }
                                    else{
                                        $('#purchease_approval').attr("checked", false);
                                        $('#purchease_required').attr("checked", false);                                        
                                    }
                                });
                            });
                            
                            $('#purchease_required').change(function() {
                                if(this.checked == true) {
                                    $('#purchease_approval').attr("checked", true);
                                    $('#purchease_required').attr("checked", true);                                        
                                }
                                else{
                                    $('#purchease_approval').attr("checked", false);
                                    $('#purchease_required').attr("checked", false);                                        
                                }
                            });
                            $('#purchease_approval').change(function() {
                                    if(this.checked == true) {
                                        $('#purchease_approval').attr("checked", true);
                                        $('#purchease_required').attr("checked", true);                                        
                                    }
                                    else{
                                        $('#purchease_approval').attr("checked", false);
                                        $('#purchease_required').attr("checked", false);                                        
                                    }
                            });
                        </script>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect ">
                                <label class="txtlbl">Invoice Approval<span></span></label><br />
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault" name="invoice_approval" @if($invoice_approval==0) @else checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="flexCheckDefault">Yes, Required</label></div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect ">
                                <label class="txtlbl">Delivery Approval<span></span></label><br />
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault" name="delivery_approval" @if($delivery_approval==0) @else checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="flexCheckDefault">Yes, Required</label></div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect ">
                                <label class="txtlbl">Receivables Approval<span></span></label><br />
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault" name="receivables_approval" @if($receivables_approval==0) @else checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="flexCheckDefault">Yes, Required</label></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 mb-10">
                            <div class="input-effect">
                                <label class="txtlbl float-left">@lang('LPO')<span></span></label>
                                @if($lpo!="")
                                <?php $file = explode("|",$lpo); ?>
                                @foreach ($file as $f)
                                <a class="text-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                @endforeach
                                @endif
                                <br />
                                <div class="form-group files">
                                    <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="lpo[]">
                                  </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-10">
                            <div class="input-effect">
                                <label class="txtlbl float-left">@lang('Cheque/TT Copy')<span></span></label>
                                @if($cheque_copy!="")
                                <?php $file = explode("|",$cheque_copy); ?>
                                @foreach ($file as $f)
                                <a class="text-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                @endforeach
                                @endif
                                <br />            
                                <div class="form-group files">
                                    <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="cheque_copy[]">
                                  </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-10">
                            <div class="input-effect">
                                <label class="txtlbl float-left">@lang('Purchase Quote')<span></span></label>
                                @if($purchease_quote!="")
                                <?php $file = explode("|",$purchease_quote); ?>
                                @foreach ($file as $f)
                                <a class="text-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                @endforeach
                                @endif
                                <br />
                                <div class="form-group files">
                                    <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="purchease_quote[]">
                                  </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-10">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Remarks')<span></span></label>
                                        <textarea class="dynamicstxt_s w-100 form-control" style="height: 100px !important" name="remarks" rows="4" autocomplete="off" id="remarks" placeholder="Remarks">{{ $remarks }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-lg-3 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('LPO/Reference No')<span></span></label>
                                        <input class="form-control" id="reference_no" type="text" autocomplete="off" required name="reference_no" value="{{ $reference_no }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('LPO/Reference Date')<span></span></label>
                                        <input class="form-control" id="reference_date" type="date" autocomplete="off" required name="reference_date" value="{{ $reference_date }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <input type="hidden" id="deal_id" name="deal_id" value="{{ $edit->id }}"/>
                    <button type="submit" class="btn btn-info" value="save" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>@lang('Save')</button>
                    <button type="submit" class="btn btn-primary" value="approve" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>@lang('Submit For Approval')</button>
                </div>
                {{ Form::close() }}
                @endif
            </div>
        </div>
    </div>
    <!-- Modal Deal Track-->

    <!-- Modal Deal Track Edit-->
    <div class="modal fade" id="ModalDealTrackEdit" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="min-width:50% !important;">
            <div class="modal-content">
                <div class="modal-header">                    
                    <h5 class="modal-title" id="exampleModalLabel">Edit Deal Track (Deal ID - {{ $edit->deal_code->code }})</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                @if (App\SysHelper::get_company_status($edit->customername)==0)
                <div style="padding: 100px 20px; width: 100%; text-align: center;">
                    <h5>Customer Information is Incomplete! Please Update Customer.</h5>
                    <a class="btn-sm btn-primary" target="_blank" href="{{url('customer-edit', $edit->customername->id)}}">Click Here to Update</a>
                </div>                
                @else
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-submit-edit','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-leads-form']) }}
                
                @php 
                $edit_delivery_date="";
                $edit_payment_terms="";
                $edit_payment_mode="";
                $edit_purchease_required="";
                $edit_partial_delivery="";
                $edit_technical="";
                $edit_technical_detail="";
                $edit_lpo="";
                $edit_cheque_copy="";
                $edit_purchease_quote="";
                $edit_remarks="";
                $edit_reference_no="";
                $edit_reference_date="";
                $edit_purchease_approval=1;
                $edit_invoice_approval=1;
                $edit_delivery_approval=1;
                $edit_receivables_approval=1;
                $start_date="";
                $end_date="";

                if(isset($deal_track)){
                    $edit_delivery_date=$deal_track->delivery_date;
                    $edit_payment_terms=$deal_track->payment_terms;
                    $edit_payment_mode=$deal_track->payment_mode;
                    $edit_purchease_required=$deal_track->purchease_required;
                    $edit_partial_delivery=$deal_track->partial_delivery;
                    $edit_technical=$deal_track->technical;
                    $edit_technical_detail=$deal_track->technical_detail;
                    $edit_lpo=$deal_track->lpo;
                    $edit_cheque_copy=$deal_track->cheque_copy;
                    $edit_purchease_quote=$deal_track->purchease_quote;
                    $edit_remarks=$deal_track->remarks;
                    $edit_reference_no=$deal_track->reference_no;
                    $edit_reference_date=$deal_track->reference_date;
                    $edit_purchease_approval=$deal_track->purchease_approval;
                    $edit_invoice_approval=$deal_track->invoice_approval;
                    $edit_delivery_approval=$deal_track->delivery_approval;
                    $edit_receivables_approval=$deal_track->receivables_approval;
                    $start_date=$deal_track->start_date;
                    $end_date=$deal_track->end_date;
                    $invoicing = $deal_track->invoicing;
                }
                @endphp
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Expected Delivery Date')<span></span></label>
                                        
                                        <input class="form-control" id="delivery_date" type="date" autocomplete="off" required name="delivery_date" value="{{ $edit_delivery_date }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <div class="input-effect">
                                <label class="txtlbl">Payment Terms<span></span></label>
                                <select class="form-control" name="payment_terms" id="payment_terms2" required>
                                    <option value="">-Select-</option>
                                @foreach ($paymentterms as $key => $value)
                                    <option value="{{ @$value->id }}"
                                        @if($edit_payment_terms !="") @if (@$edit_payment_terms == @$value->id) selected @endif 
                                        @else
                                        @if (isset($quoteitems)) @if (@$quoteitems[0]->payment_terms == @$value->id) selected @endif @endif
                                        @endif
                                        >{{ @$value->title }}</option>
                                @endforeach                                                    
                                </select>
                                <script>
                                    $('#payment_terms2').on('change', function(e) {
                                        if ($('#payment_terms2').val() == 20 || $('#payment_terms2').val() == 21) {
                                            $('#payment_mode_sec_div2').css("display", "none");
                                            //$('#payment_mode_sec').prop('required', true);
                                        } else {
                                            $('#payment_mode_sec_div2').css("display", "none");
                                            //$('#payment_mode_sec').prop('required', false);
                                        }

                                        if($('#payment_terms2').val() == 1 || $('#payment_terms2').val() == 2){
                                            $('#payment_mode2').val(1);
                                        } else { $('#payment_mode2').val(2); }

                                        if ($('#payment_terms2').val() == 22) {
                                            $('#payment_terms2_txt').css("display", "block");
                                            $('#payment_terms2_txt').prop('required', true);
                                        } else {
                                            $('#payment_terms2_txt').css("display", "none");
                                            $('#payment_terms2_txt').prop('required', false);
                                        }
                                    });
                                </script>
                                <input class="form-control" id="payment_terms2_txt" type="text" value="{{ @$quoteitems[0]->payment_terms_txt }}" autocomplete="off" placeholder="Payment Terms" name="payment_terms_txt" style="display: none;">
                            </div>
                        </div>
                        @php
                        $mode_sel=0;
                        if(@$quoteitems[0]->payment_terms== 1 || @$quoteitems[0]->payment_terms== 2){ $mode_sel=1;} else { $mode_sel=2;} 

                        @endphp
                        <div class="col-lg-4 mb-3">
                            <div class="input-effect">
                                <label class="txtlbl">Payment Mode<span></span></label>
                                <select class="form-control" name="payment_mode" id="payment_mode2" required>
                                    <option value="">-Select-</option>
                                    <option value="1" @if($edit_payment_mode==1) selected @else @if($mode_sel==1) selected @endif @endif>Cash</option>
                                    <option value="2" @if($edit_payment_mode==2) selected @else @if($mode_sel==2) selected @endif @endif>Cheque</option>
                                    <option value="3" @if($edit_payment_mode==3) selected @endif>Bank Transfer</option>
                                    <option value="4" @if($edit_payment_mode==4) selected @endif>Open Credit</option>
                                    <option value="5" @if($edit_payment_mode==5) selected @endif>Credit Card</option>
                                    <option value="6" @if($edit_payment_mode==6) selected @endif>Bank TT</option>
                                    <option value="7" @if($edit_payment_mode==7) selected @endif>Letter of Credit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3" id="payment_mode_sec_div2" style="display: none;">
                            <div class="input-effect">
                                <label class="txtlbl">Payment Mode<span></span></label>
                                <select class="form-control" name="payment_mode_sec" id="payment_mode_sec" >
                                    <option value="">-Select-</option>
                                    <option value="1">Cash</option>
                                    <option value="2">Cheque</option>
                                    <option value="3">Bank Transfer</option>
                                    <option value="4">Open Credit</option>
                                    <option value="5">Credit Card</option>
                                    <option value="6">Bank TT</option>
                                    <option value="7">Letter of Credit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect ">
                                <label class="txtlbl">Purchase Required<span></span></label><br />
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="purchease_required2" name="purchease_required" @if($edit_purchease_required==1) checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="purchease_required2">Yes, Required</label></div>
                            </div>
                        </div>
                        <script>
                            $('#payment_terms2').change();
                            $(document).ready(function() {
                                $('#purchease_required2').change(function() {
                                    if(this.checked) {
                                        $('#purchease_approval2').attr("checked", true);
                                    }
                                    else{
                                        $('#purchease_approval2').attr("checked", false);
                                    }
                                });
                            });
                        </script>
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect">
                                <label class="txtlbl">Partial Delivery<span></span></label><br />
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="partial2" name="partial_delivery" @if($edit_partial_delivery==1) checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="partial2">Yes, Partial Delivery</label></div>
                            </div>

                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="input-effect">
                                <label class="txtlbl">Professional Service<span></span></label><br />
                                <div class="form-control">
                                <input type="hidden" name="technical" value="0" />
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="technical2" name="technical" @if($edit_technical==1) checked @endif>
                                <label class="form-check-label ml-4 mt-1" for="technical2">Yes, Professional Service</label></div>
                            </div>
                            <script>
                                $('#technical2').on('change', function(e) {
                                    if ($('#technical2').prop('checked') == true) {
                                        $('#technical_div2').css("display", "block");
                                        $('#technical_detail2').prop('required', true);
                                    } else {
                                        $('#technical_div2').css("display", "none");
                                        $('#technical_detail2').prop('required', false);
                                        alert('Project service will be delete!!');
                                    }
                                });
                            </script>
                        </div>
                        @if($is_amc_item >0 )
                        <div class="col-lg-2 mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Start Date')<span></span></label>
                                        <input class="form-control" id="start_date" type="date" autocomplete="off" required name="start_date" value="{{ $start_date }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 mb-2">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('End Date')<span></span></label>
                                        <input class="form-control" id="end_date" type="date" autocomplete="off" required name="end_date" value="{{ $end_date }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                         <div class="col-lg-2 mb-2">
                            <div class="form-group">
                                <label for="">Invoicing</label>
                                <select class="form-control" type="text" name="amc_invoice" id="amc_invoice" required>
                                    <option value="">-Select-</option>
                                    <option @if($invoicing=="Monthly") selected @endif value="Monthly">Monthly</option>
                                    <option @if($invoicing=="Quarterly") selected @endif value="Quarterly">Quarterly</option>
                                    <option @if($invoicing=="Half Yearly") selected @endif value="Half Yearly">Half Yearly</option>
                                    <option @if($invoicing=="Yearly") selected @endif value="Yearly">Yearly</option>
                                </select>
                            </div>
                        </div>

                        @endif
                        <div class="col-lg-3 mb-3" id="technical_div2" style="display: none;">
                            <div class="input-effect">
                                <label class="txtlbl">Professional Service Note<span></span></label><br />
                                <textarea class="dynamicstxt_s w-100 form-control" style="height: 35px !important" name="technical_detail" rows="4" autocomplete="off" id="technical_detail2" placeholder="Remarks">{{ $edit_technical_detail }}</textarea>
                            </div>
                        </div>
                        @if($edit_technical==1) 
                        <script>
                            $('#technical_div2').css("display", "block");
                            $('#technical_detail2').prop('required', true);
                        </script>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect ">
                                <label class="txtlbl">Purchase Approval<span></span></label><br />
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="purchease_approval2" name="purchease_approval" @if($edit_purchease_approval==0) @else checked @endif 
                                @if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2)
                                @if($deal_track->accounts == 1) disabled @endif
                                @endif>
                                <label class="form-check-label ml-4 mt-1" for="purchease_approval2">Yes, Required</label></div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect ">
                                <label class="txtlbl">Invoice Approval<span></span></label><br />
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault" name="invoice_approval" @if($edit_invoice_approval==0) @else checked @endif 
                                @if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2)
                                @if($deal_track->accounts == 1) disabled @endif
                                @endif>
                                <label class="form-check-label ml-4 mt-1" for="flexCheckDefault">Yes, Required</label></div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect ">
                                <label class="txtlbl">Delivery Approval<span></span></label><br />
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault" name="delivery_approval" @if($edit_delivery_approval==0) @else checked @endif 
                                @if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2)
                                @if($deal_track->accounts == 1) disabled @endif
                                @endif>
                                <label class="form-check-label ml-4 mt-1" for="flexCheckDefault">Yes, Required</label></div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-2">
                            <div class="input-effect ">
                                <label class="txtlbl">Receivables Approval<span></span></label><br />
                                <div class="form-control">
                                <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="flexCheckDefault" name="receivables_approval" @if($edit_receivables_approval==0) @else checked @endif 
                                @if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2)
                                @if($deal_track->accounts == 1) disabled @endif
                                @endif>
                                <label class="form-check-label ml-4 mt-1" for="flexCheckDefault">Yes, Required</label></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 mb-10">
                            <div class="input-effect">
                                <label class="txtlbl float-left">@lang('LPO')<span></span></label>
                                @if($edit_lpo!="")
                                <?php $file = explode("|",$edit_lpo); ?>
                                @foreach ($file as $f)
                                <a class="text-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                @endforeach
                                @endif
                                <br />
                                <div class="form-group files">
                                    <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="lpo[]">
                                  </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-10">
                            <div class="input-effect">
                                <label class="txtlbl float-left">@lang('Cheque/TT Copy')<span></span></label>
                                @if($edit_cheque_copy!="")
                                <?php $file = explode("|",$edit_cheque_copy); ?>
                                @foreach ($file as $f)
                                <a class="text-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                @endforeach
                                @endif
                                <br />            
                                <div class="form-group files">
                                    <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="cheque_copy[]">
                                  </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-10">
                            <div class="input-effect">
                                <label class="txtlbl float-left">@lang('Purchase Quote')<span></span></label>
                                @if($edit_purchease_quote!="")
                                <?php $file = explode("|",$edit_purchease_quote); ?>
                                @foreach ($file as $f)
                                <a class="text-primary" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>
                                @endforeach
                                @endif
                                <br />
                                <div class="form-group files">
                                    <input type="file" class="form-control dynamicstxt_s" multiple="multiple" name="purchease_quote[]">
                                  </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6 mb-10">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('Remarks')<span></span></label>
                                        <textarea class="dynamicstxt_s w-100 form-control" style="height: 100px !important" name="remarks" rows="4" autocomplete="off" id="remarks" placeholder="Remarks">{{ $edit_remarks }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="col-lg-3 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('LPO/Reference No')<span></span></label>
                                        <input class="form-control" id="reference_no" type="text" autocomplete="off" required name="reference_no" value="{{ $edit_reference_no }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <div class="no-gutters input-right-icon">
                                <div class="col">
                                    <div class="input-effect">
                                        <label class="txtlbl">@lang('LPO/Reference Date')<span></span></label>
                                        <input class="form-control" id="reference_date" type="date" autocomplete="off" required name="reference_date" value="{{ $edit_reference_date }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <input type="hidden" id="deal_id" name="deal_id" value="{{ $edit->id }}"/>
                    <button type="submit" class="btn btn-primary" value="approve" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>@lang('Update')</button>
                </div>
                {{ Form::close() }}
                @endif
            </div>
        </div>
    </div>
    <!-- Modal Deal Track Edit-->





    <div class="modal fade bd-example-modal-lg" id="adddeal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog right-aligned modal-lg" role="document" style="min-width:50% !important;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Deal</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deals', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deals-form']) }}
                
                    <div class="modal-body">
                        <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                     

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Deal Name</label>
                                    <input class="form-control" type="text" name="deal_name" autocomplete="off" id="deal_name" value="{{ old('deal_name') }}" required>

                                
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Company</label>
                                    <a style="float: right; cursor: pointer;" class="text-primary" data-toggle="modal" data-target="#addcompany"><i class="fa fa-user-plus" aria-hidden="true"></i> Add Company</a>
                                    <select class="form-control js-example-basic-single" name="cust_id" id="cust_id" required>
                                            <option value="">-Select-</option>
                                            @foreach ($vendors as $value)
                                            <option value="{{ @$value->id }}">{{ @$value->customer_name_display }}
                                            </option>
                                            @endforeach
                                        </select>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Contact Person Name</label>
                                    <input class="form-control" type="text" name="cust_name" autocomplete="off" id="cust_name" value="{{ old('cust_name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Designation</label>
                                    <input class="form-control" type="text" name="designation" autocomplete="off" id="designation" value="{{  old('designation') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Mobile</label>
                                    <input class="form-control" type="text" name="cust_no" autocomplete="off" id="cust_no" value="{{ old('cust_no') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input class="form-control" type="text" name="cust_email" autocomplete="off" id="cust_email" value="{{ old('cust_email') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Address</label>
                                    <input class="form-control" type="text" name="address" autocomplete="off" id="address" value="{{  old('address') }}">
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="form-group">
                                    <label for="">Brand</label>
                                    <select class="form-control js-example-basic-single" name="tags[]" id="tags" multiple>
                                        @foreach ($brand as $value)
                                        <option value="{{ @$value->title }}"
                                            >{{ @$value->title }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="form-group">
                                    <label for="">Value</label>
                                    <input class="form-control" type="number" step="any" name="deal_value" autocomplete="off" id="deal_value" value="{{   old('deal_value') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Est. Closing Date *</label>
                                    @php
        $value = date('m-d-Y');
        
            if (!empty(old('estimated_close_date'))) {
                @$value = old('estimated_close_date');
            } else {

            }
        
                                    @endphp
                                    <input class="form-control" id="estimated_close_date" type="date" autocomplete="off" name="estimated_close_date" value="{{ @$value }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Date</label>
                                    @php
        $value = date('Y-m-d');
      
                                    @endphp
                                    <input class="form-control" id="date" type="date" name="date" value="{{ $value }}">
                                </div>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <div class="form-group">
                                    <label for="">Stage<span></span></label>
                                    <select class="form-control" name="stage" id="stage">
                                        <option value="1"  >Prospecting</option>
                                        <option value="2"  >Quote</option>
                                        <option value="3"  >Closure</option>
                                        <option value="4"  >Won</option>
                                        <option value="5"  >Lost</option>
                                    </select>
                                    <textarea class="primary-input dynamicstxt_s w-100 form-control" name="lost_comments" rows="4" style="height: 50px !important; display: none;" autocomplete="off" id="lost_comments" placeholder="Reason"></textarea>
                                    <script>
                                        $('#stage').on('change', function(e) {
                                            if ($('#stage').val() == 5) {
                                                $('#lost_comments').css("display", "block");
                                                $('#lost_comments').prop('required', true);
                                            } else {
                                                $('#lost_comments').css("display", "none");
                                                $('#lost_comments').prop('required', false);
                                            }
                                        });
                                    </script>
                                </div>
                            </div>
                            <div class="col-md-2" style="display: none;">
                                <div class="form-group">
                                    <label for="">Source</label>
                                    <select class="form-control" name="source" id="source">
                                        <option value="">-Select-</option>
                                        <option value="Chat"  >Chat</option>
                                        <option value="Call"  >Call</option>
                                        <option value="Mail" selected>Mail</option>
                                        <option value="Website"  >Website</option>
                                        <option value="Gitex 2023" >Gitex 2023</option>
                                        <option value="Gitex" >Gitex</option>
                                        <option value="Fulfillment" >Fulfillment</option>
                                        <option value="Ecommerce"  >Ecommerce</option>
                                        <option value="Other" >Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3" id="sourcediv" style="display: none;">
                                <div class="form-group">
                                    <label for="">Other Source</label>
                                    <input class="form-control" type="text" name="source_o" autocomplete="off" id="source_o" value="{{  old('source_o') }}" style="display: none;" placeholder="Source">
                                </div>
                            </div>                        
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Created By</label>
                                    <select class="form-control js-example-basic-single" name="owner" id="owner" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Deal Type<span></span></label>
                                    <select class="form-control" name="isproject" id="isproject">
                                        {{--  <option value="4" @if(@$edit->isproject == "4") selected @endif >Project</option>  --}}
                                        <option value="1"  >Reseller</option>
                                        <option value="2"  >Enduser</option>
                                        <option value="3"  >E-Commerece</option>
                                        <option value="5" >Marketing</option>
                                    </select>
                                    <script>
                                        $('#isproject').on('change', function(e) {
                                            if ($('#isproject').val() == 4) {
                                                $('#is_professional_service').prop( "checked", true );
                                            } else {
                                                $('#is_professional_service').prop( "checked", false );
                                            }
                                        });
                                    </script>
                                </div>
                            </div>
                            <div class="col-md-2" style="display: none;">
                                <div class="form-group">
                                    <label for="">Status</label>
                                    <select class="form-control" name="status" id="status" required>
                                        <option value="1">New</option>
                                        <option value="2"  >Qualified</option>
                                        <option value="3"  >Unqualified </option>
                                    </select>
                                    <textarea class="form-control" name="lost_comments" rows="4" style="display: none;" autocomplete="off" id="lost_comments" placeholder="Reason"></textarea>
                                    <script>
                                        $('#status').on('change', function(e) {
                                            if ($('#status').val() == 3) {
                                                $('#lost_comments').css("display", "block");
                                                $('#lost_comments').prop('required', true);
                                            } else {
                                                $('#lost_comments').css("display", "none");
                                                $('#lost_comments').prop('required', false);
                                            }
                                        });
                                    </script>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group files">
                                    <label for="">Project Service</label>
                                    <div class="form-control">
                                    <input class="form-check-input ml-2 mt-2" type="checkbox" value="1" id="is_professional_service" name="is_professional_service" checked>
                                    <label class="form-check-label ml-4 mt-1" for="is_professional_service">Yes, Project Service</label></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group files">
                                    <label for="">Attach</label>
                                    <input type="file" class="form-control" name="doc" id="doc">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Notes</label>
                                    <textarea class="form-control" name="note" rows="3" autocomplete="off" id="note"></textarea>
                                </div>
                            </div>
                            @if (session('logged_session_data.company_id') == 1)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Company</label>
                                    <select class="form-control" name="company" id="company" required>
                                        <option value="">Select</option>
                                        @foreach ($company as $value)
                                        <option value="{{ @$value->id }}" @if(session('logged_session_data.company_id') == @$value->id) selected @endif>{{ @$value->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @else
                            <input type="hidden" name="company" id="company" value="{{ session('logged_session_data.company_id') }}" />
                            @endif
                        </div>

                    </div>
                    <div class="modal-footer">
                        
                                <button type="submit" value="2" class="btn btn-info" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>Save & Generate Quote</button>
                                <button type="submit" value="1" class="btn btn-primary" name="btnSubmit" id="btnSubmit"><span class="ti-check"></span>Save & View Deal</button>                          
                           
                            &nbsp;&nbsp;
                            <a href="{{ url('crm-deals/show') }}" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i> Close</a>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>


        <div class="modal fade" id="addcompany" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog right-aligned modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Company</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Customer Type</label>
                                    <select class="form-control js-example-basic-single" id="account_type" required>
                                        <option value="">-Select-</option>
                                        <option value="1" selected>Reseller</option>
                                        <option value="2">Enduser</option>
                                        <option value="3">Ecommerce</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Company Name</label>
                                    <input class="form-control text-uppercase" type="text" aria-describedby="" autocomplete="off" id="company_name_add" required>
                                    <div id="company_name_add_list">
                                    </div>                            
                                    <script>
                                        $(document).ready(function(){

                                         $('#company_name_add').keyup(function(){ 
                                                var query = $(this).val();
                                                if(query != '')
                                                {
                                                 var _token = $('input[name="_token"]').val();
                                                 $.ajax({
                                                  url:"{{ route('autocomplete.customer_name') }}",
                                                  method:"POST",
                                                  data:{query:query, _token:_token},
                                                  success:function(data){
                                                   $('#company_name_add_list').fadeIn();  
                                                            $('#company_name_add_list').html(data);
                                                  }
                                                 });
                                                }
                                            });

                                            $(document).on('click', 'li', function(){  
                                                $('#company_name_add').val($(this).text());  
                                                $('#company_name_add_list').fadeOut();  
                                            });  

                                        });
                                        </script>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Contact Person Name</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_name_add" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Designation</label>
                                    <select class="form-control js-example-basic-single" name="designation_add" id="designation_add" required>
                                        <option value="">--Designation--</option>
                                        @if (count($designation) > 0)
                                            @foreach ($designation as $val)
                                                <option value="{{ $val->title }}" {{ trim(strtolower($val->title)) == 'purchase' ? 'selected' : '' }}>{{ $val->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Mobile</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_no_add" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Email</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_email_add" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">Country</label>
                                    <select class="form-control js-example-basic-single" name="country_ship" id="country_ship">
                                        <option value="">-Select-</option>
                                        @foreach ($country as $value)
                                        <option value="{{ @$value->id }}" {{ trim(strtolower($value->name)) == 'united arab emirates' ? 'selected' : '' }}>{{ @$value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">Payment Terms</label>
                                    <select class="form-control js-example-basic-single" id="payment_terms_company" required>
                                        @foreach ($paymentterms as $key => $value)
                                            <option value="{{ @$value->id }}" @if ($value->id == 3) selected @endif>{{ @$value->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">Sales Person</label>
                                    <select class="form-control js-example-basic-single" id="cust_sales_person" required>
                                        <option value="">-Select-</option>

                                        @foreach ($sales_person as $value)
                                            <option value="{{ $value->user_id }}">{{ @$value->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Address 1</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_address_add" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="" class="form-label">Address 2</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_address_add2" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">City</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_city" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">State</label>                                
                                    <div id="sectionStateDiv_ship">
                                        <select class="form-control" name="state_ship" id="state_ship">
                                            <option data-display="" value=""></option>
                                            <?php    try { ?>
                                            @if (isset($editData) && $editData->vat_state != '')
                                                <option data-display="{{ $editData->vatstate->name }}"
                                                    value="{{ $editData->vat_state }}" selected>
                                                    {{ $editData->vatstate->name }}</option>
                                            @endif
                                            <?php    } catch (\Exception $e) {
        } ?>
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="" class="form-label">PO Box</label>
                                    <input class="form-control" type="text" autocomplete="off" id="cust_pobox" required>
                                </div>
                            </div>



                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" id="btn_close2" type="button" data-dismiss="modal">Close</button>
                        <button class="btn btn-success" id="btn_add_company" type="button" >Save & Close</button>
                    </div>
                </div>
            </div>
        </div>



    @if($edit->delivery_address1 == "" && $edit->delivery_address2 == "" && $edit->delivery_city == "" && $edit->delivery_country == "")
    <script type="text/javascript">
            $("#btn_contact_update").trigger('click'); 
        </script>
    @endif

    <script>
         
            $(document).ready(function(){
                if($("#source").val() == "Other"){$("#source_o").css("display", "block"); $("#source_o").prop('required',true); $("#sourcediv").css("display", "block");}
                else{$("#source_o").css("display", "none"); $("#source_o").prop('required',false); $("#sourcediv").css("display", "none");}
            });

            $(document).on("change", "#source", function () {
            if($("#source").val() == "Other"){$("#source_o").css("display", "block"); $("#source_o").prop('required',true); $("#sourcediv").css("display", "block");}
            else{$("#source_o").css("display", "none"); $("#source_o").prop('required',false); $("#sourcediv").css("display", "none");}
            });

            $(document).on("change", "#cust_id", function () {
                var id = $("#cust_id").val();
                var user = $("#user_id").val();
                get_cust_name(id);
                get_sales_person(id,user);
            });


             function get_cust_name(id) {
                $("#loading_bg").css("display", "block");
                var action = "{{ URL::to('crm-leads-customername') }}";
                $.ajax({
                    url: action,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                    },
                    cache: false,
                    success: function(dataResult) {
                        var dataResult = JSON.parse(dataResult);
                        var len = 0;
                        var len = 0;
                            if(dataResult['data'] != null){
                                len = dataResult['data'].length;
                            }
                            if(len > 0){
                                for(var i=0; i<len; i++){
                                    var name = dataResult['data'][i].customer_salutation +' '+ dataResult['data'][i].first_name +' '+ dataResult['data'][i].last_name;
                                    var address = dataResult['data'][i].address +', '+dataResult['data'][i].address2 +', '+dataResult['data'][i].city +', '+dataResult['data'][i].statename +', '+dataResult['data'][i].name;
                                    $("#cust_name").val(name.replace('null ','').replace('null',''));
                                    $("#designation").val(dataResult['data'][i].designation);
                                    $("#cust_no").val(dataResult['data'][i].mobile);
                                    $("#cust_email").val(dataResult['data'][i].email);
                                    $("#address").val(address);
                                    //1.Reseller
                                    if(dataResult['data'][i].account_type == 1){
                                        $("#isproject").val(1);
                                        $('#is_professional_service').prop( "checked", false );
                                    }//2.Enduser
                                    if(dataResult['data'][i].account_type == 2){
                                        $("#isproject").val(2);
                                        $('#is_professional_service').prop( "checked", false );
                                    }//3.Ecommerce
                                    if(dataResult['data'][i].account_type == 3){
                                        $("#isproject").val(3);
                                        $('#is_professional_service').prop( "checked", false );
                                    }
                                }
                            }
                            else{
                                $("#cust_name").val();
                                $("#designation").val();
                                $("#cust_no").val();
                                $("#cust_email").val();
                                $("#address").val();
                                $("#isproject").val();
                            }
                            $("#loading_bg").css("display", "none");
                    }
                });
            }
            function get_sales_person(id,user) {
                $("#loading_bg").css("display", "block");
                var action = "{{ URL::to('get-salesperson-list') }}";
                $.ajax({
                    url: action,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                    },
                    cache: false,
                    success: function(dataResult) {
                        var dataResult = JSON.parse(dataResult);
                        var len = 0;
                        var len = 0;
                            if(dataResult['data'] != null){
                                len = dataResult['data'].length;
                            }
                            if(len > 0){
                                $('#owner').find('option').remove();
                                for(var i=0; i<len; i++){
                                    var id = dataResult['data'][i].id;
                                    var name = dataResult['data'][i].full_name;
                                    var sele='';
                                    if(user == id) { sele='selected'; }
                                    var option = "<option value='"+id+"' "+sele+">"+name+"</option>";
                                    $("#owner").append(option);
                                }
                            }
                            else{
                                $('#owner').find('option').remove();
                            }
                            $("#loading_bg").css("display", "none");
                    }
                });
            }


             $(document).on("click", "#btn_add_company", function () {

                

                //$("#btn_add_company").css("display", "none");

                var company_name_add = $("#company_name_add").val();
                var cust_name_add = $("#cust_name_add").val();
                var designation_add = $("#designation_add").val();
                var cust_no_add = $("#cust_no_add").val();
                var cust_email_add = $("#cust_email_add").val();
                var cust_address_add = $("#cust_address_add").val();
                var cust_address_add2 = $("#cust_address_add2").val();
                var country_add = $("#country_ship").val();

                var cust_city = $("#cust_city").val();
                var state_ship = $("#state_ship").val();
                var cust_pobox = $("#cust_pobox").val();
                var sales_person = $("#cust_sales_person").val();
                var payment_terms = $("#payment_terms_company").val();
                var account_type = $("#account_type").val();
                var company_id = $("#company").val();

               

                var action = "{{ URL::to('add-customer-detail-popup') }}";
                $.ajax({
                    url: action,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}',
                        company_name_add: company_name_add,
                        cust_name_add: cust_name_add,
                        designation_add: designation_add,
                        cust_no_add: cust_no_add,
                        cust_email_add: cust_email_add,
                        cust_address_add: cust_address_add,
                        cust_address_add2: cust_address_add2,
                        vat_country: country_add,
                        city: cust_city,
                        vat_state: state_ship,
                        zip_code: cust_pobox,
                        sales_person: sales_person,
                        payment_terms: payment_terms,
                        account_type: account_type,
                        company_id: company_id,
                    },
                    cache: false,
                    success: function(dataResult) {
                        //alert(dataResult);
                        var dataResult = JSON.parse(dataResult);
                        var len = 0;
                        if(dataResult['data']=="ERROR")
                        {
                            alert("Error found in something!!");
                            $("#btn_add_company").css("display", "block");
                        }
                        else if(dataResult['data']=="ERROR2")
                        {
                            alert("Company Name already exists!! Please Contact Support");
                            $('#company_name_add').css("border", "1px solid red"); $('#company_name_add').focus();
                            $("#btn_add_company").css("display", "block");
                        }
                        else{
                            console.log(dataResult);
                            if(dataResult['data'] != null){
                            len = dataResult['data'].length;
                            }
                            if(len > 0){

                                $('#cust_id').find('option').not(':first').remove();
                                var newCompanyId = dataResult['new_company_id'];

                                for(var i=0; i<len; i++){
                                    var id = dataResult['data'][i].id;
                                    var name = dataResult['data'][i].name;
                                    var name2 = dataResult['data'][i].code;
                                    var option = "<option value='"+id+"'>"+name+"</option>";
                                    $("#cust_id").append(option);
                                }
                                 if (newCompanyId) {
                                    $("#cust_id").val(newCompanyId).trigger('change');
                                }
                                alert('Company Name Added Successfully!!');
                                $('#btn_close2').click();
                                $("#btn_add_company").css("display", "block");
                                //location.reload();
                                //$("#company_name").change();
                            }
                        }
                      }
                });
            });
             $(document).ready(function() {
                // Trigger change event only if a country is selected by default
                if ($('#country_ship').val() !== '') {
                    $('#country_ship').trigger('change');
                }
            });
       
            
     

    </script>

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    



    {{--  <section class="sms-breadcrumb mb-20 white-box">
        <div class="container-fluid">
            <div class="row" style="float: left;">
                <h1>Deal {{ $edit->id }}</h1>
            </div>
            <div class="row" style="float: right;">
                <a href="{{ url('crm-dashboard') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> CRM
                    Dashboard</a>
                <a href="{{ url('crm-deals/' . $edit->id . '/edit') }}" class="top-btn-r"><i class="fa fa-pencil-square-o"
                        aria-hidden="true"></i> Edit</a>
                <a href="{{ url('crm-deals') }}" class="top-btn-r"><i class="far fa fa-plus" aria-hidden="true"></i> New</a>
                <a href="{{ url('crm-deals/show') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i>
                    View</a>
                <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh"
                        aria-hidden="true"></i> Refresh</a>
                <a href="{{ url('crm-deals/show') }}" class="top-btn-r btn btn-xs btn-danger pt-1 pr-3 pl-3"><< Back</a>
                
            </div>
        </div>
    </section>  --}}
        {{--  @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
            @if (session()->has('message-success'))
                <p class="text-success">
                    {{ session()->get('message-success') }}
                </p>
            @elseif(session()->has('message-danger'))
                <p class="text-danger">
                    {{ session()->get('message-danger') }}
                </p>
            @endif
        @endif  --}}
    @endsection

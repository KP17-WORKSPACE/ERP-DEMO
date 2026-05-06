@extends('backEnd.master')
@section('mainContent')
    @php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] =
    @$permission->moduleLink->module_id;}
    $modules = array_unique(@$modules);
    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol;
    
    if(isset($generalSetting->logo)){ @$logo = @$generalSetting->logo; }
    else{ $logo = 'public/uploads/settings/logo.png'; }

    $sm_staff= App\SmStaff::where('user_id',Auth::user()->id)->first();
    if(!empty(@$sm_staff)){
        @$profile_image = @$sm_staff->staff_photo;
        if(empty(@$profile_image)){
            @$profile_image ='public/uploads/staff/staff1.jpg';
        }
    }
    @endphp

<style>
    .input-group-text{font-weight: normal !important; font-size: 13px;}
    .datestyle {
      background-image: none !important; border: solid 1px #ced4da !important; height: 35px !important; border-radius: 0px 5px 5px 0px !important; background-color: #ffffff !important;
  }
</style>
    
<section class="sms-breadcrumb mb-20 white-box">
    <div class="container-fluid">
        <div class="row" style="float: left;">
            <h1>Deal {{ $del->id }} - Approval</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ url('crm-dashboard') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> CRM Dashboard</a>
            <a href="{{ url('crm-deal-track-approval-list') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View</a>
            <a href="javascript:location.reload();" class="top-btn-r-nobar"><i class="far fa fa-refresh" aria-hidden="true"></i> Refresh</a>
        </div>
    </div>
</section>
<hr style="margin-top: 33px;" />
<div style="clear: both;"></div>
<div class="col-lg-12 text-right">
    @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
        @if (session()->has('message-success'))
            <p class="text-success">
                {{ session()->get('message-success') }}
            </p>
        @elseif(session()->has('message-danger'))
            <p class="text-danger">
                {{ session()->get('message-danger') }}
            </p>
        @endif
    @endif
</div>

<?php try { ?>
    <section class="admin-visitor-area">
        
        <div class="row">
            <div class="col-lg-4 pl-4 pt-2 text-dark">
              <div class="leadbox">
              <h5 class="mt-2 text-dark">Deal Info:- {!! App\SysHelper::deal_type($del->isproject) !!}</h5>
                <h5 class="mt-2 text-dark">{{ $del->deal_name }}</h5>
                Deal Value : 
                    <span class="text-bold pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">
                    {{ $del->deal_value }}</span>
                    @if($del->estimated_close_date !="")
                    | Close Date : 
                        <span class="pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">
                        {{ date('m/d/Y', strtotime($del->estimated_close_date)) }}</span>
                    @endif
                    <br />
                    Stage : 
                        @if($del->stage==1) <span class="text-bold pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">Prospecting</span> @endif
                        @if($del->stage==2) <span class="text-bold pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">Quote</span> @endif
                        @if($del->stage==3) <span class="text-bold pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">Closure</span> @endif
                        @if($del->stage==4) <span class="text-bold pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">Won</span>@endif
                        @if($del->stage==5) <span class="text-bold pl-2 pr-2 pb-1 pt-1" style="border-radius: 5px;">Lost</span> @endif
                        
              </div> 
            </div>
            <div class="col-lg-4 pl-2 pt-2 text-dark">
              <div class="leadbox">
              <h5 class="mt-2 text-dark">Owner Info:-</h5> 
                        <span class="bg-gray text-xs pl-2 pr-2 pb-0 pt-0" style="border-radius: 5px;">
                        {{ $del->ownername->first_name }}
                        {{ $del->ownername->middle_name }} {{ $del->ownername->last_name }}</span> | Added On : <b>{{ date('d/m/Y H:i:s', strtotime(@$del->created_at)) }}</b>
                         | Source : <b>{{ $del->source }} @if ($del->source_o != "") - {{ $del->source_o }} @endif</b>
                        <br />Mob : {{ $del->ownername->mobile }} | Email : {{ $del->ownername->email }}
                        @if($del->tags != "")<br />Tags : {{ $del->tags }} @endif
                        @if($del->doc != "")<br />Attachment : <a href="{{asset('public/uploads/crm_lead_doc/')}}/{{ $del->doc }}" target="_blank">View & Download</a> <br /> @endif
                        
                        @if($del->lead_id !="")
                        <br />Converted From : <b>Lead {{ @$del->lead_id }}</b>
                        @endif
              </div>
            </div>
            <div class="col-lg-4 pl-2 pt-2 text-dark">
              <div class="leadbox">
              <h5 class="mt-2 text-dark">Customer Info:-</h5>
              <span class="pt-0 pb-0 pl-2 pr-2 text-sm"
                @if($del->customername->type==1) style="background: #228c22; color: #ffffff;" @endif
                @if($del->customername->type==2) style="background: #FFA500; color: #ffffff;" @endif
                @if($del->customername->type==3) style="background: #FF0000; color: #ffffff;" @endif
                @if($del->customername->type==4) style="background: #000000; color: #ffffff;" @endif>
                {{ $del->customername->name }}</span>
                
                @if(Auth::user()->role_id==1 || session('logged_session_data.designation_id')==8 || session('logged_session_data.designation_id')==2)
                  <a class="btn btn-xs text-xs" onclick="updiv()" title="Edit Color"><i class="fa fa-pencil-square-o pb-2" aria-hidden="true"></i></a>
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
                  <input type="hidden" name="color_customer_id" value="{{ $del->customername->id }}" />
                  <button id="btn_edit_color" type="submit" class="btn btn-xs btn-primary text-xs pt-0 pb-0">Change</button>
                  
                {{ Form::close() }}
              </div>
              <script>
                  function updiv() {
                      $("#div_update_color").css("display", "block");
                  }
              </script>


                <div class="txtlbl">{{ $del->customername->address }}<br />
                    <b>Contact :</b> {{ $del->cust_name }} | <b>M :</b> {{ $del->cust_no }} | <b>E :</b> {{ $del->cust_email }}</div>
              </div>
            </div>
            <div class="col-lg-12">
                <hr />
            </div>
        </div>


        <div class="white-box leadbox mr-3 ml-3">
            <h5>Submited</h5>
            <div class="row">
            <div class="col-lg-2 mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Expected Delivery Date')<span></span></label><br />
                            {{date('d-M-Y', strtotime(@$deal->delivery_date))}}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">Payment Terms<span></span></label><br />
                    {{@$deal->paymentterms->title}}
                    @if(session('logged_session_data.designation_id')==8)
                      <a class="btn btn-xs text-xs" onclick="update_payment_terms_mode()" title="Edit Color"><i class="fa fa-pencil-square-o pb-2" aria-hidden="true"></i></a>
                    @endif
                </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">Payment Mode<span></span></label><br />
                    @if($deal->payment_mode==1) Cash @endif
                    @if($deal->payment_mode==2) Cheque @endif
                    @if($deal->payment_mode==3) Bank Transfer @endif
                    @if($deal->payment_mode==4) Open Credit @endif
                    @if($deal->payment_mode==5) Credit Card @endif
                    @if($deal->payment_mode==6) Bank TT @endif

                    @if($deal->payment_mode_sec==1) , Cash @endif
                    @if($deal->payment_mode_sec==2) , Cheque @endif
                    @if($deal->payment_mode_sec==3) , Bank Transfer @endif
                    @if($deal->payment_mode_sec==4) , Open Credit @endif
                    @if($deal->payment_mode_sec==5) , Credit Card @endif
                    @if($deal->payment_mode_sec==6) , Bank TT @endif
                    
                    @if(session('logged_session_data.designation_id')==8)
                      <a class="btn btn-xs text-xs" onclick="update_payment_terms_mode()" title="Edit Color"><i class="fa fa-pencil-square-o pb-2" aria-hidden="true"></i></a>
                    @endif                    
                </div>
            </div>

            @if($deal->purchease_required==1)
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">Product Purchase<span></span></label><br />
                    <span class="text-danger text-bold text-xs" id="blink">Purchase Required</span>                    
                    
                    <?php try{ ?>
                    @if($purchease[0]->validation == 3) <br /><span class="text-success text-bold text-xs">Under Purchase</span> @endif
                    <?php } catch (\Throwable $th){}?>

                    @if(session('logged_session_data.designation_id')==20)
                    <script type="text/javascript">
                    var blink = document.getElementById('blink');
                    setInterval(function() {
                        blink.style.opacity = (blink.style.opacity == 0 ? 1 : 0);
                    }, 500);
                </script>
                @endif
                </div>
            </div>
            @endif
            @if($deal->partial_delivery==1)
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">Partial Delivery<span></span></label><br />
                    <span class="text-danger text-bold text-xs" id="blink">Partial Delivery</span>
                </div>
            </div>
            @endif
            
            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">@lang('LPO')<span></span></label><br />
                    @if($deal->lpo !="")
                    <?php $file = explode("|",$deal->lpo); ?>
                    @foreach ($file as $f)
                      <a class="btn-info btn-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> LPO</a><br />
                    @endforeach
                    @endif
                    {{--  @if($deal->lpo != "")<a class="btn-info btn-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $deal->lpo }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> LPO</a> @endif  --}}
                </div>
            </div>

            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Cheque/TT Copy')<span></span></label><br />
                    @if($deal->cheque_copy !="")
                    <?php $file = explode("|",$deal->cheque_copy); ?>
                    @foreach ($file as $f)
                    <a class="btn-info btn-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Cheque Copy</a><br />
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="col-lg-2 mb-10">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Purchase Quote')<span></span></label><br />
                    @if($deal->purchease_quote !="")
                    <?php $file = explode("|",$deal->purchease_quote); ?>
                    @foreach ($file as $f)
                    <a class="btn-info btn-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Purchase Quote</a><br />
                    @endforeach
                    @endif
                </div>
            </div>

            <div class="col-lg-2 mb-10">
                <div class="input-effect"><br />
                    <a class="btn btn-primary btn-xs text-white mt-1" href="{{url('crm-quote/'.$del->id.'/downloadwp')}}"><i class="fa fa-download" aria-hidden="true"></i> Quotation</a>
                    <a class="btn btn-primary btn-xs text-white mt-1" href="{{url('crm-quote/'.$del->id.'/downloadev')}}"><i class="fa fa-download" aria-hidden="true"></i> VAT Excluded</a>
                </div>
            </div>

            <div class="col-lg-6 mb-10">
            <div id="div_update_payment_mode" style="display: none; width: 500px;" class="border border-danger p-4">
              {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-receivables-payment-terms-mode', 'method' => 'POST', 'id' => 'update_payment_terms_mode']) }}
              <b>Payment Terms :</b>
              <select class="w-100 dynamicstxt_s bb w-100 form-control" name="edit_payment_terms" required>
                <option value="">-Select-</option>
                @foreach ($paymentterms as $key => $value)
                    <option value="{{ @$value->id }}" @if (@$deal->payment_terms == @$value->id) selected @endif >{{ @$value->title }}</option>
                @endforeach                                                    
            </select>
              <b>Change Payment Mode :</b>
              <select class="w-100 dynamicstxt_s bb w-100 form-control" name="edit_payment_mode" required>
                  <option value="1" @if($deal->payment_mode==1) selected @endif>Cash</option>
                  <option value="2" @if($deal->payment_mode==2) selected @endif>Cheque</option>
                  <option value="3" @if($deal->payment_mode==3) selected @endif>Bank Transfer</option>
                  <option value="4" @if($deal->payment_mode==4) selected @endif>Open Credit</option>
                  <option value="5" @if($deal->payment_mode==5) selected @endif>Credit Card</option>
                  <option value="6" @if($deal->payment_mode==6) selected @endif>Bank TT</option>
              </select>
              <input type="hidden" name="edit_payment_mode_id" value="{{ $deal->deal_id }}" />
              <button type="submit" class="btn btn-xs btn-primary text-xs pt-1 pb-1">Change</button>
              
            {{ Form::close() }}
            </div>
            <script>
                function update_payment_terms_mode() {
                  if($('#div_update_payment_mode').css('display') == 'none'){
                    $("#div_update_payment_mode").css("display", "block");
                  }
                  else{
                    $("#div_update_payment_mode").css("display", "none");
                  }
                }
            </script>
            </div>


        </div>
        </div><br />


      <div class="row">
        <div class="col-lg-2">
          
          <div class="white-box leadbox ml-3">
          <h6 class="text-dark">Accounts Status</h6>
          @if(session('logged_session_data.designation_id')==8 || (App\SysHelper::is_approval_access() && $deal->accounts!=1))
            <a data-toggle="modal" data-target="#modalEdit" class="btn btn-info btn-xs text-white ml-2 float-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
          @endif
@if ($deal->accounts==1)
<div class="progress-bar bg-success" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Approved</div>
@elseif ($deal->accounts==2)
<div class="progress-bar bg-danger" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Rejected</div>
@elseif ($deal->accounts==3)
<div class="progress-bar bg-info" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Pending</div>
@else
@if(count($accounts)>0)
  <div class="progress-bar bg-purple" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Resubmited For Approval</div>
@else
  <div class="progress-bar bg-warning" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Waiting For Approval</div>
@endif
@endif

    @if(count($accounts)>0) {{-- && session('logged_session_data.designation_id')==1 --}}
    

    @foreach ($accounts as $val)
    {{--  <b>Status</b> : @if($val->status == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->status == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />  --}}
    <b>Customer Status</b> : @if($val->customer_status == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->customer_status == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Credit Limit</b> : @if($val->credit_limit == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->credit_limit == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Payment Terms</b> : @if($val->payment_terms == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->payment_terms == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Overdue Payment</b> : @if($val->pending_payment == 1) No <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->pending_payment == 2) Yes <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Other</b> : @if($val->other == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->other == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->remarks != "")<b>Remarks</b> : {!! $val->remarks !!}@endif
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span>
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span>
    @endforeach
    @endif
    </div>
  </div>

    <div class="col-lg-2">
      <div class="white-box leadbox">
          <h6 class="text-dark">Sales Status</h6>
          @if(session('logged_session_data.designation_id')==27 || (App\SysHelper::is_approval_access() && $deal->accounts==1 && $deal->sales!=1))
            <a data-toggle="modal" data-target="#modalEdit" class="btn btn-info btn-xs text-white ml-2 float-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
          @endif
          @if ($deal->sales==1)
          <div class="progress-bar bg-success" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Approved</div>
      @elseif ($deal->sales==2)
          <div class="progress-bar bg-danger" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Rejected</div>
      @elseif ($deal->sales==3)
          <div class="progress-bar bg-info" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Pending</div>
      @else
        @if(count($sales)>0)
          <div class="progress-bar bg-purple" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Resubmited For Approval</div>
        @else
          <div class="progress-bar bg-warning" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Waiting For Approval</div>
        @endif
      @endif
    @if(count($sales)>0) {{-- && session('logged_session_data.designation_id')==27 --}}
    @foreach ($sales as $val)
    {{--  <b>Status</b> : @if($val->status == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->status == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />  --}}
    <b>Margin</b> : @if($val->margin == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->margin == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Stock</b> : @if($val->stock == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->stock == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Purchase Quote</b> : @if($val->purcease_quote == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->purcease_quote == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Other</b> : @if($val->other == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->other == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->remarks != "")<b>Remarks</b> : {!! $val->remarks !!}@endif
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span>
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span>
    @endforeach
    @endif
  </div>
    </div>
    
    <div class="col-lg-2">
      <div class="white-box leadbox">
          <h6 class="text-dark">Purchase Status</h6>
          @if(session('logged_session_data.designation_id')==20 || (App\SysHelper::is_approval_access() && $deal->accounts==1 && $deal->sales==1 && $deal->purchease!=1))
            <a data-toggle="modal" data-target="#modalEdit" class="btn btn-info btn-xs text-white ml-2 float-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
          @endif
          @if ($deal->purchease==1)
            @if ($deal->purchease==1 && count($purchease)==0)
              <div class="progress-bar bg-info" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Not Applicable</div>
            @else
            <div class="progress-bar bg-success" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Approved</div>
            @endif
          @elseif ($deal->purchease==2)
          <div class="progress-bar bg-danger" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Rejected</div>
          @elseif ($deal->purchease==3)
          <div class="progress-bar bg-info" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Pending</div>
          @elseif ($deal->purchease==4)
              <div class="progress-bar bg-primary" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Partial Delivery</div>
          @else
          @if(count($purchease)>0)
            <div class="progress-bar bg-purple" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Resubmited For Approval</div>
          @else
            <div class="progress-bar bg-warning" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Waiting For Approval</div>
          @endif
          @endif

    @if(count($purchease)>0) {{-- && session('logged_session_data.designation_id')==20 --}}
    @foreach ($purchease as $val)
    {{--  <b>Status</b> : @if($val->status == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->status == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />  --}}
    <b>Purchase Quote</b> : @if($val->purchease_quote == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->purchease_quote == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>3Quote Qequest</b> : @if($val->three_quote_request == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->three_quote_request == 3) Not Required <i class="fa fa-check text-success" aria-hidden="true"></i> @elseif($val->three_quote_request == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Purchase</b> : @if($val->validation == 1) Purchase Completed <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->validation == 3) Under Purchase <i class="fa fa-clock text-warning" aria-hidden="true"></i> @elseif($val->validation == 4) Partial Delivery <i class="fa fa-check text-success" aria-hidden="true"></i> @elseif($val->validation == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->validation == 3)
      @if($val->lpo_no != "")<b>LPO No</b> : {{ $val->lpo_no }}<br />@endif
      @if($val->delivery_date != "" && $val->delivery_date != "1970-01-01")<b>Expected Delivery</b> : {{ date('d/m/Y', strtotime($val->delivery_date)) }}<br />@endif
    @endif

    <b>Other</b> : @if($val->other == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->other == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if(session('logged_session_data.designation_id')==20 || Auth::user()->role_id == 1)
    @if($val->fileone != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->fileone }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Quote 1</a>&nbsp;@endif
    @if($val->filetwo != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->filetwo }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Quote 2</a>&nbsp;@endif
    @if($val->filethree != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->filethree }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Quote 3</a>@endif
    @endif
    <br />
    @if($val->remarks != "")<b>Remarks</b> : {!! $val->remarks !!}@endif
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span>
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span>
    @endforeach
    @endif
  </div>
  </div>
  
  <div class="col-lg-2">
    <div class="white-box leadbox">
        <h6 class="text-dark">Invoice Status</h6>
        @if(session('logged_session_data.designation_id')==35 || (App\SysHelper::is_approval_access() && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice!=1))
          <a data-toggle="modal" data-target="#modalEdit" class="btn btn-info btn-xs text-white ml-2 float-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
        @endif
    @if ($deal->invoice==1)
<div class="progress-bar bg-success" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Approved</div>
@elseif ($deal->invoice==2)
<div class="progress-bar bg-danger" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Rejected</div>
@elseif ($deal->invoice==3)
<div class="progress-bar bg-info" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Pending</div>
@else
@if(count($invoice)>0)
  <div class="progress-bar bg-purple" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Resubmited For Approval</div>
@else
  <div class="progress-bar bg-warning" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Waiting For Approval</div>
@endif
@endif
    @if(count($invoice)>0) {{-- && session('logged_session_data.designation_id')==35 --}}
    @foreach ($invoice as $val)
    {{--  <b>Status</b> : @if($val->status == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->status == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />  --}}
    <b>Delivery Advice</b> : @if($val->delivery_advice == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->delivery_advice == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Validation</b> : @if($val->validation == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->validation == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Hold</b> : @if($val->hold == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->hold == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Print</b> : @if($val->print == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->print == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->invoice_no != "")<b>Invoice No</b> : {{ $val->invoice_no }}@endif
    <br />
    @if($val->remarks != "")<b>Remarks</b> : {!! $val->remarks !!}@endif
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span>
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span>
    @endforeach
    @endif
  </div>
  </div>
  
  <div class="col-lg-2">
    <div class="white-box leadbox">
        <h6 class="text-dark">Delivery Status</h6>
        @if(session('logged_session_data.designation_id')==34 || (App\SysHelper::is_approval_access() && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice==1 && $deal->delivery!=1))
          <a data-toggle="modal" data-target="#modalEdit" class="btn btn-info btn-xs text-white ml-2 float-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
        @endif
    @if ($deal->delivery==1)
                        <div class="progress-bar bg-success" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Delivery Completed</div>
                    @elseif ($deal->delivery==2)
                        <div class="progress-bar bg-danger" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Rejected</div>
                    @elseif ($deal->delivery==3)
                        <div class="progress-bar bg-primary" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Out For Delivery</div>
                    @elseif ($deal->delivery==5)
                        <div class="progress-bar bg-primary" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Ready For Delivery</div>
                    @elseif ($deal->delivery==4)
                        <div class="progress-bar bg-info" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Pending For Delivery</div>
                    @else
                    @if(count($delivery)>0)
                      <div class="progress-bar bg-purple" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Resubmited For Approval</div>
                    @else
                      <div class="progress-bar bg-warning" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Waiting For Approval</div>
                    @endif
                    @endif
    @if(count($delivery)>0) {{-- && session('logged_session_data.designation_id')==34 --}}
    @foreach ($delivery as $val)
    {{--  <b>Status</b> : @if($val->status == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->status == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />  --}}
    <b>DO Status</b> : @if($val->do_status == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->do_status == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->do_no != "")<b>Do No</b> : {{ $val->do_no }}<br />@endif
    
    @if($val->print_invoice_no != "")<b>Print Invoice No</b> : {{ $val->print_invoice_no }}<br />@endif

    @if($val->cheque_collection != "")<b>Cheque Collection</b> : @if($val->cheque_collection == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->cheque_collection == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />@endif
    
    @if($val->cheque_collection_file != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->cheque_collection_file }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Cheque Copy</a><br />@endif
    
    <b>Delivery Status</b> : @if($val->delivery_status == 1) Delivery Completed <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->delivery_status == 2) Pending For Delivery <i class="fa fa-times text-danger" aria-hidden="true"></i> @elseif($val->delivery_status == 4) Ready For Delivery <i class="fa fa-clock text-info" aria-hidden="true"></i> @else Out For Delivery <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->deliver_by != "")<b>Deliver By</b> : {{ $val->deliver_by }}
    @if($val->driver != ""), {{ $val->driver }}<br />@endif @endif

    @if($val->cash_collected != "")<b>Cash Collected</b> : {{ $val->cash_collected }}<br />@endif

    @if($val->id_no != "")<b>ID No</b> : {{ $val->id_no }}<br />@endif
    @if($val->contact_no != "")<b>Contact No</b> : {{ $val->contact_no }}<br />@endif
    @if($val->awb_no != "")<b>AWB No</b> : {{ $val->awb_no }}<br />@endif
    @if($val->attach_file != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->attach_file }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Attachment</a><br />@endif

    @if($val->remarks != "")<b>Remarks</b> : {!! $val->remarks !!}@endif
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span>
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span>
    @endforeach
    @endif
  </div>
  </div>
  
  <div class="col-lg-2">
    <div class="white-box leadbox">
        <h6 class="text-dark">Receivables Status</h6>
        @if(session('logged_session_data.designation_id')==2 || (App\SysHelper::is_approval_access() && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice==1 && $deal->delivery==1 && $deal->receivables!=1))
          <a data-toggle="modal" data-target="#modalEdit" class="btn btn-info btn-xs text-white ml-2 float-right"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
        @endif
    @if ($deal->receivables==1)
                        <div class="progress-bar bg-success" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Payment Received</div>
                    @elseif ($deal->receivables==2)
                        <div class="progress-bar bg-danger" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Rejected</div>
                    @elseif ($deal->receivables==3)
                        <div class="progress-bar bg-info" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Payment Pending</div>
                    @elseif ($deal->receivables==4)
                        <div class="progress-bar bg-dark" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Order Cancelled</div>
                    @else
                    @if(count($receivables)>0)
                      <div class="progress-bar bg-purple" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Resubmited For Approval</div>
                    @else
                      <div class="progress-bar bg-warning" role="progressbar" style="width: auto;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Waiting For Approval</div>
                    @endif
                    @endif
    @if(count($receivables)>0) {{-- && session('logged_session_data.designation_id')==34 --}}
    @foreach ($receivables as $val)
    {{--  <b>Status</b> : @if($val->status == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->status == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />  --}}
@if($val->payment_collection == 3)
<b>Credit Note No : {{ $val->credit_note }}</b>
@else
    <b>Payment Collection</b> : @if($val->payment_collection == 1) Approved <i class="fa fa-check text-success" aria-hidden="true"></i> @elseif($val->payment_collection == 2) Disapproved <i class="fa fa-times text-danger" aria-hidden="true"></i> @elseif($val->payment_collection == 3) Order Cancelled <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    <b>Payment Status</b> : @if($val->payment_status == 1) Payment Received <i class="fa fa-check text-success" aria-hidden="true"></i>@elseif($val->payment_status == 2) Pending <i class="fa fa-times text-danger" aria-hidden="true"></i> @else Pending <i class="fa fa-clock text-info" aria-hidden="true"></i> @endif
    <br />
    @if($val->reminder_date != "1970-01-01" && $val->reminder_date != "")<b>Reminder Date</b> : {{ date('d/m/Y h:i:A', strtotime($val->reminder_date)) }}<br />@endif

    @if($val->amount != "")<b>Amount</b> : {{ $val->amount }}<br />@endif
    @if($val->amount2 != "")<b>Amount</b> : {{ $val->amount2 }}<br />@endif
    @if($val->amount3 != "")<b>Amount</b> : {{ $val->amount3 }}<br />@endif
    @if($val->balance_amount != "")<b>Balance</b> : {{ $val->balance_amount }}<br />@endif
    <b>Payment Mode</b> :    
@if($val->paymenttype == 1) Cash @endif
@if($val->paymenttype == 2) Cheque @endif
@if($val->paymenttype == 3) Bank Transfer @endif
@if($val->paymenttype == 4) Open Credit @endif
@if($val->paymenttype == 5) Credit Card @endif
@if($val->paymenttype == 6) Bank TT @endif
    <br />
    @if($val->cash_date != "" && $val->cash_date != "1970-01-01")<b>Date</b> : {{ date('d/m/Y', strtotime($val->cash_date)) }}<br />@endif
    @if($val->cash_date2 != "" && $val->cash_date2 != "1970-01-01")<b>Date</b> : {{ date('d/m/Y', strtotime($val->cash_date2)) }}<br />@endif
    @if($val->cash_date3 != "" && $val->cash_date3 != "1970-01-01")<b>Date</b> : {{ date('d/m/Y', strtotime($val->cash_date3)) }}<br />@endif

    @if($val->cheque_no != "")<b>Cheque No</b> : {{ $val->cheque_no }}<br />@endif
    @if($val->cheque_date != "1970-01-01" && $val->cheque_date != "")<b>Cheque Date</b> : {{ date('d/m/Y', strtotime($val->cheque_date)) }}<br />@endif
    @if($val->cheque_no2 != "")<b>Cheque No</b> : {{ $val->cheque_no2 }}<br />@endif
    @if($val->cheque_date2 != "1970-01-01" && $val->cheque_date2 != "")<b>Cheque Date</b> : {{ date('d/m/Y', strtotime($val->cheque_date2)) }}<br />@endif
    @if($val->cheque_no3 != "")<b>Cheque No</b> : {{ $val->cheque_no3 }}<br />@endif
    @if($val->cheque_date3 != "1970-01-01" && $val->cheque_date3 != "")<b>Cheque Date</b> : {{ date('d/m/Y', strtotime($val->cheque_date3)) }}<br />@endif
    
    @if($val->cheque_copy !="")
    <?php $file = explode("|",$val->cheque_copy); ?>
      @foreach ($file as $f)
        <a class="btn-info btn-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Cheque</a> 
      @endforeach <br /> @endif
    {{--  @if($val->cheque_copy != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->cheque_copy }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> Cheque Copy</a><br />@endif  --}}
    
    @if($val->bank_name != "")<b>Bank Name</b> : {{ $val->bank_name }}<br />@endif
    @if($val->deposit_date != "" && $val->deposit_date != "1970-01-01")<b>Deposit Date</b> : {{ date('d/m/Y', strtotime($val->deposit_date)) }}<br />@endif
    @if($val->deposit_date2 != "" && $val->deposit_date2 != "1970-01-01")<b>Deposit Date</b> : {{ date('d/m/Y', strtotime($val->deposit_date2)) }}<br />@endif
    
    @if($val->open_credit_date != "" && $val->open_credit_date != "1970-01-01")<b>Open Credit</b> : {{ date('d/m/Y', strtotime($val->open_credit_date)) }}<br />@endif
    
    @if($val->credit_card_type != "")<b>Credit Card</b> : {{ $val->credit_card_type }}<br />@endif
    @if($val->payment_date != "" && $val->payment_date != "1970-01-01")<b>Payment Date</b> : {{ date('d/m/Y', strtotime($val->payment_date)) }}<br />@endif
    @if($val->credit_card_deposit_date != "" && $val->credit_card_deposit_date != "1970-01-01")<b>Deposit Date</b> : {{ date('d/m/Y', strtotime($val->credit_card_deposit_date)) }}<br />@endif
    
    @if($val->banktt_date != "" && $val->banktt_date != "1970-01-01")<b>BankTT Date</b> : {{ date('d/m/Y', strtotime($val->banktt_date)) }}<br />@endif
    @if($val->banktt_date2 != "" && $val->banktt_date2 != "1970-01-01")<b>BankTT Date</b> : {{ date('d/m/Y', strtotime($val->banktt_date2)) }}<br />@endif
    @if($val->banktt_date3 != "" && $val->banktt_date3 != "1970-01-01")<b>BankTT Date</b> : {{ date('d/m/Y', strtotime($val->banktt_date3)) }}<br />@endif

    @if($val->banktt_copy !="")
    <?php $file = explode("|",$val->banktt_copy); ?>
    @foreach ($file as $f)
      <a class="btn-info btn-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $f }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> BankTT</a> 
    @endforeach <br /> @endif
    {{--  @if($val->banktt_copy != "")<a class="text-info text-xs" href="{{asset('public/uploads/crm_deal_track_doc/')}}/{{ $val->banktt_copy }}" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> BankTT Copy</a><br />@endif  --}}
@endif
    @if($val->remarks != "")<b>Remarks</b> : {!! $val->remarks !!}@endif
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">At : {{ date('d/m/Y h:i A', strtotime($val->created_at)) }}</span>
    <br /><span class="text-gray border rounded pt-1 pb-1 pl-2 pr-2">By : {{ $val->createdby->full_name }}</span>
    @endforeach
    @endif
  </div>
  </div>
      </div>

      <br />

{{--  crm-deal-track-approval-accounts  --}}
        @if($deal->accounts==0 && (session('logged_session_data.designation_id')==8 || (App\SysHelper::is_approval_access() && $deal->accounts!=1)))
        <div class="white-box leadbox mr-3 ml-3 border-danger">
            <h5>For Accounts Approval</h5><br />
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-accounts','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-accounts']) }}
            
            <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
            <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
            <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />

            <div class="row">
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Customer Status
                    <select class="w-100 dynamicstxt_s form-control" name="customer_status" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Credit Limit
                    <select class="w-100 dynamicstxt_s form-control" name="credit_limit" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Payment Terms
                    <select class="w-100 dynamicstxt_s form-control" name="payment_terms" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Overdue Payment
                    <select class="w-100 dynamicstxt_s form-control" name="pending_payment" required>
                      <option value="" selected>-Select-</option>
                      <option value="2">Yes</option>
                      <option value="1">No</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Other
                    <select class="w-100 dynamicstxt_s form-control" name="other" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-4 mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Remarks')<span></span></label>
                            <textarea class="primary-input dynamicstxt_s w-100" cols="5" id="remarks" style="height: 100px !important" name="remarks"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 pt-2"><br />
                <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
                <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
                <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
                        @lang('Submit')
                </button>
                
                
            </div>
            
            
        </div>
        {{ Form::close() }}
        </div><br />
        @endif
{{--  crm-deal-track-approval-accounts  --}}

{{--  crm-deal-track-approval-sales  --}}
        @if($deal->sales==0 && (session('logged_session_data.designation_id')==27 || (App\SysHelper::is_approval_access() && $deal->accounts==1)))
        <div class="white-box leadbox mr-3 ml-3 border-danger">
            <h5>For Sales Manager Approval</h5><br />
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-sales','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-sales']) }}
            
            <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
            <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
            <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
            
            <div class="row">
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Margin
                    <select class="w-100 dynamicstxt_s form-control" name="margin" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Stock
                    <select class="w-100 dynamicstxt_s form-control" name="stock" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Purchase Quote
                    <select class="w-100 dynamicstxt_s form-control" name="purcease_quote" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Other
                    <select class="w-100 dynamicstxt_s form-control" name="other" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Purchase Approval
                    <select class="w-100 dynamicstxt_s form-control" name="purchease_approval" required>
                      <option value="1">Required</option>
                      <option value="2">Not Required</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-2 mb-10"></div>
            <div class="col-lg-4 mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Remarks')<span></span></label>
                            <textarea class="primary-input dynamicstxt_s w-100" cols="5" id="remarks" style="height: 100px !important" name="remarks"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 pt-2"><br />
                <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
                <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
                <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
                        @lang('Submit')
                </button>
                
                
            </div>
            
            
        </div>
        {{ Form::close() }}
        </div><br />
        @endif
{{--  crm-deal-track-approval-sales  --}}


{{--  crm-deal-track-approval-purchease  20--}}
        @if($deal->purchease==0 && (session('logged_session_data.designation_id')==20 || (App\SysHelper::is_approval_access() && $deal->sales==1)))
        <div class="white-box leadbox mr-3 ml-3 border-danger">
            <h5>For Purchase Approval</h5><br />
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-purchease','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-purchease']) }}
            
            <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
            <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
            <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
            
            <div class="row">
            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Purchase Quote
                    <select class="w-100 dynamicstxt_s form-control" name="purchease_quote" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">3 Quote Request
                    <select class="w-100 dynamicstxt_s form-control" name="quote_request" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="3">Not Required</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Purchase Status
                    <select class="w-100 dynamicstxt_s form-control" id="validation" name="validation" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Purchase Completed</option>
                      <option value="3">Under Purchase</option>
                      <option value="4">Partial Delivery</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <script>
              $('#validation').on('change', function(e) {
                if ($('#validation').val() == 3) {
                  $('#div_validation').css("display", "block");
                  $('#lpo_no').prop('required', true);
                  $('#delivery_date').prop('required', true);
                } else {
                  $('#div_validation').css("display", "none");
                  $('#lpo_no').prop('required', false);
                  $('#delivery_date').prop('required', false);
                }
              });
              </script>
            <div class="col-lg-3 mb-10" id="div_validation" style="display: none;">
              <div class="input-group mb-3">LPO No
                <input type="text" class="w-100 dynamicstxt_s form-control primary-input" id="lpo_no" name="lpo_no"/>
                Delivery Date
                <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" id="delivery_date" name="delivery_date"/>
              </div>
            </div>

            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Other
                    <select class="w-100 dynamicstxt_s form-control" name="other" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Choose Quote 1
                    <input type="file" class="w-100 dynamicstxt_s form-control" id="fileone" name="fileone" style="background-image: none !important;">
                  </div>
            </div>
            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Choose Quote 2
                    <input type="file" class="w-100 dynamicstxt_s form-control" id="filetwo" name="filetwo" style="background-image: none !important;">
                  </div>
            </div>
            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Choose Quote 3
                    <input type="file" class="w-100 dynamicstxt_s form-control" id="filethree" name="filethree" style="background-image: none !important;">
                  </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4 mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Remarks')<span></span></label>
                            <textarea class="primary-input dynamicstxt_s w-100" cols="5" id="remarks" style="height: 100px !important" name="remarks"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 pt-2"><br />
                <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
                <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
                <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
                        @lang('Submit')
                </button>
                
                
            </div>
            
            
        </div>
        {{ Form::close() }}
        </div><br />
        @endif
{{--  crm-deal-track-approval-purchease  --}}

{{--  crm-deal-track-approval-invoice  --}}
        @if(($deal->invoice==0 || $deal->invoice==3) && (session('logged_session_data.designation_id')==35 || (App\SysHelper::is_approval_access() && $deal->purchease==1)))
        <div class="white-box leadbox mr-3 ml-3 border-danger">
            <h5>For Invoice Approval</h5><br />
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-invoice','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-invoice']) }}
            
            <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
            <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
            <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
            
            <div class="row">
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Delivery Advice
                    <select class="w-100 dynamicstxt_s form-control" name="delivery_advice" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Validation
                    <select class="w-100 dynamicstxt_s form-control" name="validation" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Hold
                    <select class="w-100 dynamicstxt_s form-control" name="hold" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                      <option value="3">Pending</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Print
                    <select class="w-100 dynamicstxt_s form-control" name="print" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                      <option value="3">Pending</option>
                    </select>
                  </div>
            </div>            
            <div class="col-lg-2 mb-10">
                <div class="input-group mb-3">Invoice No
                    <input type="text" class="w-100 dynamicstxt_s form-control" id="invoice_no" name="invoice_no" required />
                  </div>
            </div>
            <div class="col-lg-2 mb-10"></div>
            <div class="col-lg-4 mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Remarks')<span></span></label>
                            <textarea class="primary-input dynamicstxt_s w-100" cols="5" id="remarks" style="height: 100px !important" name="remarks"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 pt-2"><br />
                <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
                <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
                <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
                        @lang('Submit')
                </button>
                
                
            </div>
            
            
        </div>
        {{ Form::close() }}
        </div><br />
        @endif
{{--  crm-deal-track-approval-invoice  --}}

{{--  crm-deal-track-approval-delivery  --}}
        @if(($deal->delivery==0 || $deal->delivery==3) && (session('logged_session_data.designation_id')==34 || (App\SysHelper::is_approval_access() && $deal->invoice==1)))
        <div class="white-box leadbox mr-3 ml-3 border-danger">
            <h5>For Delivery Approval</h5><br />
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-delivery','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-delivery']) }}
            
            <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
            <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
            <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
            
            <div class="row">
            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Do Status
                    <select class="w-100 dynamicstxt_s form-control" name="do_status" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                    </select>
                  </div>
            </div>


            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Do No
                    <input type="text" class="w-100 dynamicstxt_s form-control" style="background-image: none !important;"  name="do_no" required />
                  </div>
            </div>
            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Print Invoice No
                    <input type="text" class="w-100 dynamicstxt_s form-control" style="background-image: none !important;"  name="print_invoice_no" required />
                  </div>
            </div>
            
            @if($deal->payment_mode==1)            
            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Cash Collected
                    <input type="text" class="w-100 dynamicstxt_s form-control" style="background-image: none !important;"  name="cash_collected" required />
                  </div>
            </div>
            @else
            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Cheque Collection
                    <select class="w-100 dynamicstxt_s form-control" name="cheque_collection" required>
                      <option value="" selected>-Select-</option>
                      <option value="1">Approved</option>
                      <option value="2">Disapproved</option>
                      <option value="3">Pending</option>
                    </select>
                  </div>
            </div>
            
            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Cheque Copy
                    <input type="file" class="w-100 dynamicstxt_s form-control" id="cheque_collection_file" name="cheque_collection_file" style="background-image: none !important;">
                  </div>
            </div>            
            @endif

            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Delivery Status
                    <select class="w-100 dynamicstxt_s form-control" name="delivery_status" required>
                      <option value="" selected>-Select-</option>
                      <option value="2">Pending For Delivery</option>
                      <option value="4">Ready For Delivery</option>
                      <option value="3">Out For Delivery</option>
                      <option value="1">Delivery Completed</option>
                    </select>
                  </div>
            </div>
            
            <div class="col-lg-3 mb-10">
                <div class="input-group mb-3">Deliver By
                    <select class="w-100 dynamicstxt_s form-control" id="deliver_by_new" name="deliver_by" required>
                        <option value="" selected>-Select-</option>
                        <option value="1">Courier</option>
                        <option value="2">Driver</option>
                        <option value="3">Local Delivery</option>
                        <option value="4">Office Boy</option>
                        <option value="5">Collection by Client</option>
                        <option value="6">By Email</option>
                    </select>
                  </div>
            </div>
            <div class="col-lg-3 mb-10" id="div_byemail" style="display: none;">
              <div class="input-group mb-3">Email IDs
                  <input type="text" class="w-100 dynamicstxt_s form-control" id="byemail" name="byemail" placeholder="Email Ids">
              </div>
            </div>
            <div class="col-lg-3 mb-10">
              <div class="input-group mb-3">Attachment/AWB Copy
              <input type="file" class="w-100 dynamicstxt_s form-control" id="attach_file" name="attach_file" style="background-image: none !important;">
              </div>
            </div>
            <div class="col-lg-3 mb-10">
              <script>
                $('#deliver_by_new').on('change', function(e) {
                  if ($('#deliver_by_new').val() == 1) {
                    $('#div_courier').css("display", "block");
                    $('#div_attach_file').css("display", "block");
                    $('#div_driver').css("display", "none");
                    $('#div_localdelivery').css("display", "none");
                    $('#div_officeboy').css("display", "none");
                    $('#div_collectionbyclient').css("display", "none");
                    $('#div_byemail').css("display", "none");
                  }
                  if ($('#deliver_by_new').val() == 2) {
                    $('#div_courier').css("display", "none");
                    $('#div_attach_file').css("display", "none");
                    $('#div_driver').css("display", "block");
                    $('#div_localdelivery').css("display", "none");
                    $('#div_officeboy').css("display", "none");
                    $('#div_collectionbyclient').css("display", "none");
                    $('#div_byemail').css("display", "none");
                  }
                  if ($('#deliver_by_new').val() == 3) {
                    $('#div_courier').css("display", "none");
                    $('#div_attach_file').css("display", "none");
                    $('#div_driver').css("display", "none");
                    $('#div_localdelivery').css("display", "block");
                    $('#div_officeboy').css("display", "none");
                    $('#div_collectionbyclient').css("display", "none");
                    $('#div_byemail').css("display", "none");
                  }
                  if ($('#deliver_by_new').val() == 4) {
                    $('#div_courier').css("display", "none");
                    $('#div_attach_file').css("display", "none");
                    $('#div_driver').css("display", "none");
                    $('#div_localdelivery').css("display", "none");
                    $('#div_officeboy').css("display", "block");
                    $('#div_collectionbyclient').css("display", "none");
                    $('#div_byemail').css("display", "none");
                  }
                  if ($('#deliver_by_new').val() == 5) {
                    $('#div_courier').css("display", "none");
                    $('#div_attach_file').css("display", "none");
                    $('#div_driver').css("display", "none");
                    $('#div_localdelivery').css("display", "none");
                    $('#div_officeboy').css("display", "none");
                    $('#div_collectionbyclient').css("display", "block");
                    $('#div_byemail').css("display", "none");
                  }
                  if ($('#deliver_by_new').val() == 6) {
                    $('#div_courier').css("display", "none");
                    $('#div_attach_file').css("display", "none");
                    $('#div_driver').css("display", "none");
                    $('#div_localdelivery').css("display", "none");
                    $('#div_officeboy').css("display", "none");
                    $('#div_collectionbyclient').css("display", "none");
                    $('#div_byemail').css("display", "block");
                  }
                  if ($('#deliver_by_new').val() == "") {
                    $('#div_courier').css("display", "none");
                    $('#div_attach_file').css("display", "none");
                    $('#div_driver').css("display", "none");
                    $('#div_localdelivery').css("display", "none");
                    $('#div_officeboy').css("display", "none");
                    $('#div_collectionbyclient').css("display", "none");
                    $('#div_byemail').css("display", "none");
                  }
              });
              </script>
            {{--  options  --}}
              <div class="input-group mb-3" id="div_courier" style="display: none;">Courier
                  <select class="w-100 dynamicstxt_s form-control" id="courier" name="courier">
                      <option value="" selected>-Select-</option>
                      @foreach ($shipping as $value)<option value="{{ @$value->shipping_name }}">{{ @$value->shipping_name }}</option>@endforeach
                  </select>
              </div>
              <div class="input-group mb-3" id="div_driver" style="display: none;">Driver
                  <select class="w-100 dynamicstxt_s form-control" id="driver" name="driver">
                      <option value="" selected>-Select-</option>
                      @foreach ($driver as $value)<option value="{{ @$value->driver_name }}">{{ @$value->driver_name }}</option>@endforeach
                  </select>
              </div>
              <div class="input-group mb-3" id="div_localdelivery" style="display: none;">Local Delivery
                  <select class="w-100 dynamicstxt_s form-control" id="localdelivery" name="localdelivery">
                      <option value="" selected>-Select-</option>
                      <option value="Salman">Salman</option>
                      <option value="Mohid">Mohid</option>
                      <option value="Manan">Mannan</option>
                      <option value="Usman">Usman</option>
                      <option value="Ziyad">Ziyad</option>
                      <option value="Akhil">Akhil</option>
                  </select>
              </div>
              <div class="input-group mb-3" id="div_officeboy" style="display: none;">Office Boy
                  <select class="w-100 dynamicstxt_s form-control" id="officeboy" name="officeboy">
                      <option value="" selected>-Select-</option>
                      <option value="Salman">Salman</option>
                      <option value="Mohid">Mohid</option>
                      <option value="Manan">Mannan</option>
                      <option value="Usman">Usman</option>
                      <option value="Ziyad">Ziyad</option>
                      <option value="Akhil">Akhil</option>
                  </select>
              </div>
              <div class="input-group mb-3" id="div_collectionbyclient" style="display: none;">Collection by Client
                  <input type="text" class="w-100 dynamicstxt_s form-control" id="collectionbyclient" name="collectionbyclient" placeholder="Name">
                  <input type="text" class="w-100 dynamicstxt_s form-control mt-2" id="contact_no" name="contact_no" placeholder="Contact No">
                  <input type="text" class="w-100 dynamicstxt_s form-control mt-2" id="id_no" name="id_no" placeholder="ID No">
              </div>
              <div class="input-group mb-3" id="div_attach_file" style="display: none;">
                <input type="text" class="w-100 dynamicstxt_s form-control mt-2" id="awb_no" name="awb_no" placeholder="AWB No">
              </div>
            {{--  options  --}}
            </div>

            </div>
            <div class="row">
            <div class="col-lg-4 mb-10">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Remarks')<span></span></label>
                            <textarea class="primary-input dynamicstxt_s w-100" cols="5" id="remarks" style="height: 100px !important" name="remarks"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 pt-2"><br />
                <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
                <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
                <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
                        @lang('Submit')
                </button>
                
                
            </div>
            
            
        </div>
        {{ Form::close() }}
        </div><br />
        @endif
{{--  crm-deal-track-approval-delivery  --}}


{{--  crm-deal-track-approval-receivables  --}}
@if(($deal->receivables==0 || $deal->receivables==3) && (session('logged_session_data.designation_id')==2 || (App\SysHelper::is_approval_access() && $deal->delivery==1)))
<div class="white-box leadbox mr-3 ml-3 border-danger">
    <h5>For Receivables Approval
      
       <span class="text-sm text-primary"> ( Payment Mode - 
      @if($deal->payment_mode==1) Cash @endif
      @if($deal->payment_mode==2) Cheque @endif
      @if($deal->payment_mode==3) Bank Transfer @endif
      @if($deal->payment_mode==4) Open Credit @endif
      @if($deal->payment_mode==5) Credit Card @endif
      @if($deal->payment_mode==6) Bank TT @endif )</span>
      <a class="btn btn-xs text-xs" onclick="update_payment_mode()" title="Edit Color"><i class="fa fa-pencil-square-o pb-2" aria-hidden="true"></i></a>    
    </h5>


    <div id="div_update_payment_mode" style="display: none; width: 500px;">
      {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-receivables-payment-mode', 'method' => 'POST', 'id' => 'update_payment_mode']) }}
      <b>Change Payment Mode :</b>
      <select class="dynamicstxt_s w-50" name="edit_payment_mode" id="edit_payment_mode" required>
          <option value="1" @if($deal->payment_mode==1) selected @endif>Cash</option>
          <option value="2" @if($deal->payment_mode==2) selected @endif>Cheque</option>
          <option value="3" @if($deal->payment_mode==3) selected @endif>Bank Transfer</option>
          <option value="4" @if($deal->payment_mode==4) selected @endif>Open Credit</option>
          <option value="5" @if($deal->payment_mode==5) selected @endif>Credit Card</option>
          <option value="6" @if($deal->payment_mode==6) selected @endif>Bank TT</option>
      </select>
      <input type="hidden" name="edit_payment_mode_id" value="{{ $deal->deal_id }}" />
      <button type="submit" class="btn btn-xs btn-primary text-xs pt-1 pb-1">Change</button>
      
    {{ Form::close() }}
    </div>
    <script>
        function update_payment_mode() {
            $("#div_update_payment_mode").css("display", "block");
        }
    </script>
    <br />



    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-receivables','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-delivery']) }}
    
    <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
    <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
    <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
    
    <div class="row">
    <div class="col-lg-2 mb-10">
        <div class="input-group mb-3">Collection
            <select class="w-100 dynamicstxt_s form-control payment_collection" name="payment_collection" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
              <option value="3">Order Cancelled</option>
            </select>
          </div>
    </div>
    <script>
      $('.payment_collection').on('change', function(e) {
        if ($('.payment_collection').val() == 3) {
          $('.credit_note_div').css("display", "block");
          $('.no_cn_div').css("display", "none");
          $('.credit_note').prop('required', true);
          $('.no_cn_req').prop('required', false);
        }
        else{
          $('.credit_note_div').css("display", "none");
          $('.no_cn_div').css("display", "block");
          $('.credit_note').prop('required', false);
          $('.no_cn_req').prop('required', true);
        }
      });
      </script>
    <div class="col-lg-2 mb-10 credit_note_div" style="display: none;">
        <div class="input-group mb-3">Credit Note
            <input type="text" class="w-100 dynamicstxt_s form-control credit_note" name="credit_note" />
          </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
        <div class="input-group mb-3">Payment Status
            <select class="w-100 dynamicstxt_s form-control no_cn_req" name="payment_status" id="payment_status" required>
              <option value="" selected>-Select-</option>
              <option value="1">Payment Received</option>
              <option value="2">Pending</option>
            </select>
          </div>
    </div>
    <script>
      $('#payment_status').on('change', function(e) {
        if ($('#payment_status').val() == 2) {
          $('#payment_status_div').css("display", "block");
          $('#reminder_date').prop('required', true);
          $('#cheque_date').prop('required', false);
          $('#deposit_date').prop('required', false);
          $('#open_credit_date').prop('required', false);
          $('#payment_date').prop('required', false);
          $('#credit_card_deposit_date').prop('required', false);
          $('#banktt_date').prop('required', false);
        }
        else{
          $('#payment_status_div').css("display", "none");
          $('#reminder_date').prop('required', false);
          $('#cheque_date').prop('required', true);
          $('#deposit_date').prop('required', true);
          $('#open_credit_date').prop('required', true);
          $('#payment_date').prop('required', true);
          $('#credit_card_deposit_date').prop('required', true);
          $('#banktt_date').prop('required', true);
        }
      });
      </script>
    <div class="col-lg-2 mb-10" id="payment_status_div" style="display: none;">
        <div class="input-group mb-3 text-danger">Reminder Date
            <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="reminder_date" id="reminder_date" placeholder="Select Date"/>
            <select class="w-100 dynamicstxt_s form-control" name="reminder_time">
              <option value="" selected>-Select Time-</option>
              <option value="09:00:00">09:00 AM</option>
              <option value="10:00:00">10:00 AM</option>
              <option value="11:00:00">11:00 AM</option>
              <option value="12:00:00">12:00 PM</option>
              <option value="13:00:00">01:00 PM</option>
              <option value="14:00:00">02:00 PM</option>
              <option value="15:00:00">03:00 PM</option>
              <option value="16:00:00">04:00 PM</option>
              <option value="17:00:00">05:00 PM</option>
              <option value="18:00:00">06:00 PM</option>
              <option value="19:00:00">07:00 PM</option>
              <option value="20:00:00">08:00 PM</option>
              <option value="21:00:00">09:00 PM</option>
            </select>
        </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
        <div class="input-group mb-3">Amount &nbsp;&nbsp;<a id="addAmount1" class="text-success btn btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
            <input type="text" class="w-100 dynamicstxt_s form-control no_cn_req" id="amount" name="amount" required />
          </div>
    </div>
    <div class="col-lg-2 mb-10" id="addAmountDiv1" style="display: none;">
        <div class="input-group mb-3">Amount &nbsp;&nbsp;<a id="addAmount2" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
            <input type="text" class="w-100 dynamicstxt_s form-control" name="amount2" />
          </div>
    </div>
    <div class="col-lg-2 mb-10" id="addAmountDiv2" style="display: none;">
        <div class="input-group mb-3">Amount &nbsp;&nbsp;<a id="addAmount3" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
            <input type="text" class="w-100 dynamicstxt_s form-control" name="amount3" />
          </div>
    </div>
    <script>
      $('#addAmount1').on('click', function(e) {
        if( $('#addAmountDiv1'). css("display") == "block" ){
          $('#addAmountDiv2').css("display", "block");
        }
          $('#addAmountDiv1').css("display", "block");

          if( $('#addAmountDiv1'). css("display") == "block" && $('#addAmountDiv2'). css("display") == "block" ){          
            $('#addAmount1').css("display", "none");
          }else{$('#addAmount1').css("display", "block");}
      });
      $('#addAmount2').on('click', function(e) {
          $('#addAmountDiv1').css("display", "none");
          if( $('#addAmountDiv1'). css("display") == "block" && $('#addAmountDiv2'). css("display") == "block" ){          
            $('#addAmount1').css("display", "none");
          }else{$('#addAmount1').css("display", "block");}
      });
      $('#addAmount3').on('click', function(e) {
          $('#addAmountDiv2').css("display", "none");
          if( $('#addAmountDiv1'). css("display") == "block" && $('#addAmountDiv2'). css("display") == "block" ){          
            $('#addAmount1').css("display", "none");
          }else{$('#addAmount1').css("display", "block");}
      });
    </script>

    <div class="col-lg-2 mb-10 no_cn_div">
        <div class="input-group mb-3">Balance Amount
            <input type="text" class="w-100 dynamicstxt_s form-control" id="balance_amount" name="balance_amount" />
          </div>
    </div>
    <input type="hidden" name="payment_mode" value="{{ $deal->payment_mode }}" />
    <input type="hidden" name="payment_mode_sec" value="{{ $deal->payment_mode_sec }}" />
    
    

    @if($deal->payment_mode==1) {{--  Cash  --}}    
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">Date &nbsp;&nbsp;<a id="addCashDate1" class="text-success btn btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
          <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" name="cash_date" required />
        </div>
    </div>
    <div class="col-lg-2 mb-10" id="addCashDateDiv1" style="display: none;">
      <div class="input-group mb-3">Date No &nbsp;&nbsp;<a id="addCashDate2" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
          <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="cash_date2" />
        </div>
    </div>
    <div class="col-lg-2 mb-10" id="addCashDateDiv2" style="display: none;">
      <div class="input-group mb-3">Date No &nbsp;&nbsp;<a id="addCashDate3" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
          <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="cash_date3" />
        </div>
    </div>
    <script>
      $('#addCashDate1').on('click', function(e) {
        if( $('#addCashDateDiv1'). css("display") == "block" ){
          $('#addCashDateDiv2').css("display", "block");
        }
          $('#addCashDateDiv1').css("display", "block");

          if( $('#addCashDateDiv1'). css("display") == "block" && $('#addCashDateDiv2'). css("display") == "block" ){          
            $('#addCashDate1').css("display", "none");
          }else{$('#addCashDate1').css("display", "block");}
      });
      $('#addCashDate2').on('click', function(e) {
          $('#addCashDateDiv1').css("display", "none");
          if( $('#addCashDateDiv1'). css("display") == "block" && $('#addCashDateDiv2'). css("display") == "block" ){          
            $('#addCashDate1').css("display", "none");
          }else{$('#addCashDate1').css("display", "block");}
      });
      $('#addCashDate3').on('click', function(e) {
          $('#addCashDateDiv2').css("display", "none");
          if( $('#addCashDateDiv1'). css("display") == "block" && $('#addCashDateDiv2'). css("display") == "block" ){          
            $('#addCashDate1').css("display", "none");
          }else{$('#addCashDate1').css("display", "block");}
      });
    </script>

    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">1000 x
          <input type="number" class="w-100 dynamicstxt_s form-control" id="thousand" name="thousand" />
        </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">500 x
          <input type="number" class="w-100 dynamicstxt_s form-control" id="fivehundred" name="fivehundred" />
        </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">100 x
          <input type="number" class="w-100 dynamicstxt_s form-control" id="hundred" name="hundred" />
        </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">50 x
          <input type="number" class="w-100 dynamicstxt_s form-control" id="fifty" name="fifty" />
        </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">20 x
          <input type="number" class="w-100 dynamicstxt_s form-control" id="twenty" name="twenty" />
        </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">10 x
          <input type="number" class="w-100 dynamicstxt_s form-control" id="ten" name="ten" />
        </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">5 x
          <input type="number" class="w-100 dynamicstxt_s form-control" id="five" name="five" />
        </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">1 x
          <input type="number" class="w-100 dynamicstxt_s form-control" id="one" name="one" />
        </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">50 x
          <input type="number" class="w-100 dynamicstxt_s form-control" id="fiftyp" name="fiftyp" />
        </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">25 x
          <input type="number" class="w-100 dynamicstxt_s form-control" id="twentyfivep" name="twentyfivep" />
        </div>
    </div>
    @elseif($deal->payment_mode==2) {{--  Cheque  --}}
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">Cheque No &nbsp;&nbsp;<a id="addChequeNo1" class="text-success btn btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
          <input type="text" class="w-100 dynamicstxt_s form-control" id="cheque_no" name="cheque_no" />
        </div>
    </div>
    <div class="col-lg-2 mb-10" id="addChequeNoDiv1" style="display: none;">
      <div class="input-group mb-3">Cheque No &nbsp;&nbsp;<a id="addChequeNo2" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
          <input type="text" class="w-100 dynamicstxt_s form-control" name="cheque_no2" />
        </div>
    </div>
    <div class="col-lg-2 mb-10" id="addChequeNoDiv2" style="display: none;">
      <div class="input-group mb-3">Cheque No &nbsp;&nbsp;<a id="addChequeNo3" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
          <input type="text" class="w-100 dynamicstxt_s form-control" name="cheque_no3" />
        </div>
    </div>
    <script>
      $('#addChequeNo1').on('click', function(e) {
        if( $('#addChequeNoDiv1'). css("display") == "block" ){
          $('#addChequeNoDiv2').css("display", "block");
        }
          $('#addChequeNoDiv1').css("display", "block");
          
          if( $('#addChequeNoDiv1'). css("display") == "block" && $('#addChequeNoDiv2'). css("display") == "block" ){          
            $('#addChequeNo1').css("display", "none");
          }else{$('#addChequeNo1').css("display", "block");}
      });
      $('#addChequeNo2').on('click', function(e) {
          $('#addChequeNoDiv1').css("display", "none");
          if( $('#addChequeNoDiv1'). css("display") == "block" && $('#addChequeNoDiv2'). css("display") == "block" ){          
            $('#addChequeNo1').css("display", "none");
          }else{$('#addChequeNo1').css("display", "block");}
      });
      $('#addChequeNo3').on('click', function(e) {
          $('#addChequeNoDiv2').css("display", "none");
          if( $('#addChequeNoDiv1'). css("display") == "block" && $('#addChequeNoDiv2'). css("display") == "block" ){          
            $('#addChequeNo1').css("display", "none");
          }else{$('#addChequeNo1').css("display", "block");}
      });
    </script>
    <div class="col-lg-2 mb-10 no_cn_div">
        <div class="input-group mb-3">Cheque Date &nbsp;&nbsp;<a id="addChequeDate1" class="text-success btn btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
            <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" id="cheque_date" name="cheque_date" required />
          </div>
    </div>
    <div class="col-lg-2 mb-10" id="addChequeDateDiv1" style="display: none;">
        <div class="input-group mb-3">Cheque Date &nbsp;&nbsp;<a id="addChequeDate2" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
            <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="cheque_date2" />
          </div>
    </div>
    <div class="col-lg-2 mb-10" id="addChequeDateDiv2" style="display: none;">
        <div class="input-group mb-3">Cheque Date &nbsp;&nbsp; &nbsp;&nbsp;<a id="addChequeDate3" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
            <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="cheque_date3" />
          </div>
    </div>
    <script>
      $('#addChequeDate1').on('click', function(e) {
        if( $('#addChequeDateDiv1'). css("display") == "block" ){
          $('#addChequeDateDiv2').css("display", "block");
        }
          $('#addChequeDateDiv1').css("display", "block");

          if( $('#addChequeDateDiv1'). css("display") == "block" && $('#addChequeDateDiv2'). css("display") == "block" ){          
            $('#addChequeDate1').css("display", "none");
          }else{$('#addChequeDate1').css("display", "block");}
      });
      $('#addChequeDate2').on('click', function(e) {
          $('#addChequeDateDiv1').css("display", "none");
          if( $('#addChequeDateDiv1'). css("display") == "block" && $('#addChequeDateDiv2'). css("display") == "block" ){          
            $('#addChequeDate1').css("display", "none");
          }else{$('#addChequeDate1').css("display", "block");}
      });
      $('#addChequeDate3').on('click', function(e) {
          $('#addChequeDateDiv2').css("display", "none");
          if( $('#addChequeDateDiv1'). css("display") == "block" && $('#addChequeDateDiv2'). css("display") == "block" ){          
            $('#addChequeDate1').css("display", "none");
          }else{$('#addChequeDate1').css("display", "block");}
      });
    </script>

    <div class="col-lg-3 mb-10 no_cn_div">Cheque Copy<br />
      <div class="input-group mb-3">
        <div class="form-group files">
          <input type="file" class="w-100 dynamicstxt_s form-control" id="cheque_copy" multiple="multiple" name="cheque_copy[]" style="background-image: none !important;"></div>
        </div>
  </div>
    @elseif($deal->payment_mode==3) {{--  Bank Transfer  --}}
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">Bank Name
          <input type="text" class="w-100 dynamicstxt_s form-control" id="bank_name" name="bank_name" />
        </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">Deposit Date
          <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" style="background-image: none !important;" id="deposit_date" name="deposit_date" required />
        </div>
  </div>
  <div class="col-lg-2 mb-10">
    <div class="input-group mb-3">Deposit Date 2
        <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle " style="background-image: none !important;" id="deposit_date2" name="deposit_date2"/>
      </div>
</div>
    @elseif($deal->payment_mode==4) {{--  Open Credit  --}}
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">Open Credit Date
          <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" style="background-image: none !important;" id="open_credit_date" name="open_credit_date" required />
        </div>
  </div>
    @elseif($deal->payment_mode==5) {{--  Credit Card  --}}
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">Card Type
          <input type="text" class="w-100 dynamicstxt_s form-control" id="credit_card_type" name="credit_card_type" />
        </div>
    </div>
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">Payment Date
          <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" style="background-image: none !important;" id="payment_date" name="payment_date" required />
        </div>
  </div>
  <div class="col-lg-2 mb-10 no_cn_div">
    <div class="input-group mb-3">Deposit Date
        <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" style="background-image: none !important;" id="credit_card_deposit_date" name="credit_card_deposit_date" required />
      </div>
</div>
    @elseif($deal->payment_mode==6) {{--  Bank TT  --}}
    <div class="col-lg-2 mb-10 no_cn_div">
      <div class="input-group mb-3">BankTT Date &nbsp;&nbsp;<a id="addBankTTDate1" class="text-success btn btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
          <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" id="banktt_date" name="banktt_date" required />
        </div>
  </div>
  <div class="col-lg-2 mb-10" id="addBankTTDateDiv1" style="display: none;">
    <div class="input-group mb-3">BankTT Date &nbsp;&nbsp;<a id="addBankTTDate2" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
        <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="banktt_date2" />
      </div>
</div>
<div class="col-lg-2 mb-10" id="addBankTTDateDiv2" style="display: none;">
  <div class="input-group mb-3">BankTT Date &nbsp;&nbsp;<a id="addBankTTDate3" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
      <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="banktt_date3" />
    </div>
</div>
<script>
  $('#addBankTTDate1').on('click', function(e) {
    if( $('#addBankTTDateDiv1'). css("display") == "block" ){
      $('#addBankTTDateDiv2').css("display", "block");
    }
      $('#addBankTTDateDiv1').css("display", "block");

      if( $('#addBankTTDateDiv1'). css("display") == "block" && $('#addBankTTDateDiv2'). css("display") == "block" ){          
        $('#addBankTTDate1').css("display", "none");
      }else{$('#addBankTTDate1').css("display", "block");}
  });
  $('#addBankTTDate2').on('click', function(e) {
      $('#addBankTTDateDiv1').css("display", "none");
      if( $('#addBankTTDateDiv1'). css("display") == "block" && $('#addBankTTDateDiv2'). css("display") == "block" ){          
        $('#addBankTTDate1').css("display", "none");
      }else{$('#addBankTTDate1').css("display", "block");}
  });
  $('#addBankTTDate3').on('click', function(e) {
      $('#addBankTTDateDiv2').css("display", "none");
      if( $('#addBankTTDateDiv1'). css("display") == "block" && $('#addBankTTDateDiv2'). css("display") == "block" ){          
        $('#addBankTTDate1').css("display", "none");
      }else{$('#addBankTTDate1').css("display", "block");}
  });
</script>

  <div class="col-lg-3 mb-10 no_cn_div">BankTT Copy<br />
    <div class="input-group mb-3">
      <div class="form-group files">
        <input type="file" class="w-100 dynamicstxt_s form-control" id="banktt_copy" multiple="multiple" name="banktt_copy[]" style="background-image: none !important;"></div>
      </div>
</div>
    @endif
    
    
  </div>
<div class="row">
    <div class="col-lg-4 mb-10">
        <div class="no-gutters input-right-icon">
            <div class="col">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                    <textarea class="primary-input dynamicstxt_s w-100" cols="5" id="remarks" style="height: 100px !important" name="remarks"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 pt-2"><br />
        <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
        <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
        <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
                @lang('Submit')
        </button>
        
        
    </div>
    
    
</div>
{{ Form::close() }}
</div><br />
@endif
{{--  crm-deal-track-approval-receivables  --}}


<div class="row">
  <div class="col-lg-8">
    <div class="white-box leadbox ml-3 p-0">
        @if (count($quoteitems)>0)
        <h6 class="mb-2 mt-2 ml-2">Quote Items</h6>
        <table id="table_custom" class="display school-table" cellspacing="0" width="100%">
          <tr>
          <td>Part Number</td>
          <td>Description</td>
          <td>Qty</td>
          <td align="right">Price</td>
          <td align="right">Discount</td>
          <td align="right">Total</td>
          </tr>
        <?php $t_qty=0; $t_price=0; $t_discount=0; $t_net_amount=0;?>
        <div class=" mb-1" style="max-height: 400px; overflow-x:hidden; overflow-y: scroll;">
            @foreach ($quoteitems as $Item)
            <tbody>
              <tr>
                  <td><?php try{ ?><span class="text-info">{{ $Item->productname->part_number }}</span><?php }catch (\Exception $e){} ?></td>
                  <td><span class="text-dark">{!! nl2br($Item->description) !!}</span></td>
                  <td><span class="text-success">{{ $Item->qty }}</span></td>
                  <td><span class="text-danger float-right">{{ $Item->price }}</span></td>
                  <td><span class="text-success float-right">{{ $Item->discount }}</span></td>
                  <td><span class="text-success float-right">{{ @App\SysHelper::com_curr_format((($Item->price * $Item->qty)-($Item->discount * $Item->qty)), 2, '.', ',')}}</span></td>
              </tr>
          </tbody>
          <?php $t_qty += $Item->qty;
          $t_price += $Item->price * $Item->qty;
          $t_discount += $Item->discount * $Item->qty;
          $t_net_amount += ($Item->price * $Item->qty) - ($Item->discount * $Item->qty);
          ?>
            @endforeach
            <tr>
              <td></td>
              <td></td>
              <td>{{ $t_qty }}
                  <?php $t_discount += $del->deal_discount?></td>
              <td class="text-bold" align="right">{{ @App\SysHelper::com_curr_format($t_price, 2, '.', '') }}</td>
              <td class="text-bold" align="right">{{ @App\SysHelper::com_curr_format($t_discount, 2, '.', '') }}</td>
              <td class="text-bold" align="right">
                  <?php $vat = (($t_price * $quoteitems[0]->company->net_vat/100) - ($t_discount * $quoteitems[0]->company->net_vat/100)); ?>
                  {{ @App\SysHelper::com_curr_format($vat, 2, '.', '') }} VAT<br />
                  {{@App\SysHelper::com_curr_format($t_price - $t_discount + $vat, 2, '.', '') }} {{ $Item->currency->code }}
              </td>
          </tr>
      </table>
        @endif
    </div>
</div>
    <div class="col-lg-4">

        @if(isset($comments))
        <div class="white-box leadbox" style="background: #ffffff;">
          <h6 class="mb-2 mt-2">Internal Notes</h6>        
          <hr />
          @if($del->note != "")Note : {!! nl2br($del->note) !!}@endif
        @foreach ($comments as $cmts)
            {!! $cmts->comments !!}
            <div class="text-right"><span class="text-dark">
            {{ $cmts->createdby->first_name }} {{ $cmts->createdby->last_name }}</span>
             , On {{date('d/m/Y h:i A', strtotime($cmts->created_at))}}&nbsp;</div>
            <hr class="mt-2 mb-2"/>
        @endforeach
        </div>
        @endif
        <br />
        <div class="white-box leadbox ml-2">
            <div class="add-visitor">
                <div class="row">
                    <div class="col-lg-12">
                        <h6 class="mb-2 mt-2">Delivery Location/Address</h6>
                        <hr />
                        @if(isset($addressbook))
                            <span class="text-sm text-dark">Company : {{ $addressbook->customername->name }}</span>
                            <div class="text-sm">Address : {{ $addressbook->address }}<br />
                            Contact Person : {{ $addressbook->contact_person }}<br />
                            Contact Number :</b> {{ $addressbook->contact_number }} | Email : {{ $addressbook->contact_email }}</div>
                        @else
                            <span class="text-sm text-dark">Company : {{ $del->customername->name }}</span>
                            <div class="text-sm">Address : {{ $del->customername->address }}<br />
                            Contact Person : {{ $del->cust_name }}<br />
                            Contact Number :</b> {{ $del->cust_no }} | Email : {{ $del->cust_email }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
    
</div>

        </div>
    </section>
    <?php 
  }catch (\Exception $e) {
    ?> {{ $e }} <?php 
} ?>

    <!-- Modal-->
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header p-2" style="background: #8f8f8f;">
            <h5 class="modal-title" id="exampleModalLongTitle">
              @if(session('logged_session_data.designation_id')==8) For Accounts Approval @endif
              @if(session('logged_session_data.designation_id')==27) For Sales Manager Approval @endif
              @if(session('logged_session_data.designation_id')==20) For Purchase Approval @endif
              @if(session('logged_session_data.designation_id')==35) For Invoice Approval @endif
              @if(session('logged_session_data.designation_id')==34) For Delivery Approval @endif
              @if(session('logged_session_data.designation_id')==2) For Receivables Approval @endif            
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

{{--  crm-deal-track-approval-accounts  --}}
@if(session('logged_session_data.designation_id')==8 || (App\SysHelper::is_approval_access() && $deal->accounts!=1))
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-accounts','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-accounts']) }}
    
    <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
    <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
    <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />

    <h4 class="m-0 p-0">Accounts Approval</h4>
    <div class="row">
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Customer Status
            <select class="w-100 dynamicstxt_s form-control" name="customer_status" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Credit Limit
            <select class="w-100 dynamicstxt_s form-control" name="credit_limit" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Payment Terms
            <select class="w-100 dynamicstxt_s form-control" name="payment_terms" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Overdue Payment
            <select class="w-100 dynamicstxt_s form-control" name="pending_payment" required>
              <option value="" selected>-Select-</option>
              <option value="2">Yes</option>
              <option value="1">No</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Other
            <select class="w-100 dynamicstxt_s form-control" name="other" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    </div>
    <div class="row">
    <div class="col-lg-8 mb-10">
        <div class="no-gutters input-right-icon">
            <div class="col">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                    <textarea class="primary-input dynamicstxt_s w-100" cols="5" id="remarks" style="height: 100px !important" name="remarks"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 pt-2"><br />
        <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
        <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
        <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
                @lang('Submit')
        </button>
        
        
    </div>    
</div>
{{ Form::close() }}
@endif
{{--  crm-deal-track-approval-accounts  --}}

{{--  crm-deal-track-approval-sales  --}}
@if(session('logged_session_data.designation_id')==27 || (App\SysHelper::is_approval_access() && $deal->accounts==1 && $deal->sales!=1))
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-sales','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-sales']) }}
    
    <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
    <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
    <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
    
    <h4 class="m-0 p-0">Sales Approval</h4>
    <div class="row">
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Margin
            <select class="w-100 dynamicstxt_s form-control" name="margin" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Stock
            <select class="w-100 dynamicstxt_s form-control" name="stock" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Purchase Quote
            <select class="w-100 dynamicstxt_s form-control" name="purcease_quote" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Other
            <select class="w-100 dynamicstxt_s form-control" name="other" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Purchase Approval
            <select class="w-100 dynamicstxt_s form-control" name="purchease_approval" required>
              <option value="1">Required</option>
              <option value="2">Not Required</option>
            </select>
          </div>
    </div>
    </div>
    <div class="row">
    <div class="col-lg-8 mb-10">
        <div class="no-gutters input-right-icon">
            <div class="col">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                    <textarea class="primary-input dynamicstxt_s w-100" cols="5" id="remarks" style="height: 100px !important" name="remarks"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 pt-2"><br />
        <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
        <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
        <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
                @lang('Submit')
        </button>
        
        
    </div>
    
    
</div>
{{ Form::close() }}
@endif
{{--  crm-deal-track-approval-sales  --}}


{{--  crm-deal-track-approval-purchease  20--}}
@if(session('logged_session_data.designation_id')==20 || (App\SysHelper::is_approval_access() && $deal->accounts==1 && $deal->sales==1 && $deal->purchease!=1))
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-purchease','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-purchease']) }}
    
    <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
    <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
    <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
    
    <h4 class="m-0 p-0">Purchase Approval</h4>
    <div class="row">
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Purchase Quote
            <select class="w-100 dynamicstxt_s form-control" name="purchease_quote" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">3 Quote Request
            <select class="w-100 dynamicstxt_s form-control" name="quote_request" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="3">Not Required</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Purchase Status
            <select class="w-100 dynamicstxt_s form-control" id="validation_re" name="validation" required>
              <option value="" selected>-Select-</option>
              <option value="1">Purchase Completed</option>
              <option value="3">Under Purchase</option>
              <option value="4">Partial Delivery</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <script>
      $('#validation_re').on('change', function(e) {
      if ($('#validation_re').val() == 3) {
        $('#div_validation_re').css("display", "block");
        $('#div_validation_re2').css("display", "block");
        $('#lpo_no_re').prop('required', true);
        $('#delivery_date_re').prop('required', true);
      } else {
        $('#div_validation_re').css("display", "none");
        $('#div_validation_re2').css("display", "none");
        $('#lpo_no_re').prop('required', false);
        $('#delivery_date_re').prop('required', false);
      }
      });
    </script>
    <div class="col-lg-4 mb-10" id="div_validation_re" style="display: none;">
      LPO No
      <input type="text" class="w-100 dynamicstxt_s form-control primary-input" id="lpo_no_re" name="lpo_no"/>
    </div>
    <div class="col-lg-4 mb-10" id="div_validation_re2" style="display: none;">
      Delivery Date
      <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" id="delivery_date_re" name="delivery_date"/>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Other
            <select class="w-100 dynamicstxt_s form-control" name="other" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Choose Quote 1
            <input type="file" class="w-100 dynamicstxt_s form-control" id="fileone" name="fileone" style="background-image: none !important;">
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Choose Quote 2
            <input type="file" class="w-100 dynamicstxt_s form-control" id="filetwo" name="filetwo" style="background-image: none !important;">
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Choose Quote 3
            <input type="file" class="w-100 dynamicstxt_s form-control" id="filethree" name="filethree" style="background-image: none !important;">
          </div>
    </div>
    </div>
    <div class="row">
    <div class="col-lg-8 mb-10">
        <div class="no-gutters input-right-icon">
            <div class="col">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                    <textarea class="primary-input dynamicstxt_s w-100" cols="5" id="remarks" style="height: 100px !important" name="remarks"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 pt-2"><br />
        <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
        <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
        <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
                @lang('Submit')
        </button>        
    </div>    
</div>
{{ Form::close() }}
@endif
{{--  crm-deal-track-approval-purchease  --}}

{{--  crm-deal-track-approval-invoice  35--}}
@if(session('logged_session_data.designation_id')==35 || (App\SysHelper::is_approval_access() && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice!=1))
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-invoice','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-invoice']) }}
    
    <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
    <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
    <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
    
    <h4 class="m-0 p-0">Invoice Approval</h4>
    <div class="row">
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Delivery Advice
            <select class="w-100 dynamicstxt_s form-control" name="delivery_advice" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Validation
            <select class="w-100 dynamicstxt_s form-control" name="validation" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Hold
            <select class="w-100 dynamicstxt_s form-control" name="hold" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
              <option value="3">Pending</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Print
            <select class="w-100 dynamicstxt_s form-control" name="print" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
              <option value="3">Pending</option>
            </select>
          </div>
    </div>            
    <div class="col-lg-6 mb-10">
        <div class="input-group mb-3">Invoice No
            <input type="text" class="w-100 dynamicstxt_s form-control" id="invoice_no" name="invoice_no" required />
          </div>
    </div>
    </div>
    <div class="row">
    <div class="col-lg-8 mb-10">
        <div class="no-gutters input-right-icon">
            <div class="col">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                    <textarea class="primary-input dynamicstxt_s w-100" cols="5" id="remarks" style="height: 100px !important" name="remarks"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 pt-2"><br />
        <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
        <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
        <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
                @lang('Submit')
        </button>        
    </div>    
</div>
{{ Form::close() }}
@endif
{{--  crm-deal-track-approval-invoice  --}}

{{--  crm-deal-track-approval-delivery  34--}}
@if(session('logged_session_data.designation_id')==34 || (App\SysHelper::is_approval_access() && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice==1 && $deal->delivery!=1))
    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-delivery','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-delivery']) }}
    
    <input type="hidden" name="owner_id" value="{{ $del->owner }}" />
    <input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
    <input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />
    
    <h4 class="m-0 p-0">Delivery Approval</h4>
    <div class="row">
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Do Status
            <select class="w-100 dynamicstxt_s form-control" name="do_status" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
            </select>
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Do No
            <input type="text" class="w-100 dynamicstxt_s form-control" style="background-image: none !important;"  name="do_no" required />
          </div>
    </div>
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Print Invoice No
            <input type="text" class="w-100 dynamicstxt_s form-control" style="background-image: none !important;"  name="print_invoice_no" required />
          </div>
    </div>
    
    @if($deal->payment_mode==1)
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Cash Collected
            <input type="text" class="w-100 dynamicstxt_s form-control" style="background-image: none !important;"  name="cash_collected" required />
          </div>
    </div>
    @else
    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Cheque Collection
            <select class="w-100 dynamicstxt_s form-control" name="cheque_collection" required>
              <option value="" selected>-Select-</option>
              <option value="1">Approved</option>
              <option value="2">Disapproved</option>
              <option value="3">Pending</option>
            </select>
          </div>
    </div>            
    <div class="col-lg-6 mb-10">
        <div class="input-group mb-3">Cheque Copy
            <input type="file" class="w-100 dynamicstxt_s form-control" id="cheque_collection_file" name="cheque_collection_file" style="background-image: none !important;">
          </div>
    </div>
    @endif

    <div class="col-lg-4 mb-10">
        <div class="input-group mb-3">Delivery Status
            <select class="w-100 dynamicstxt_s form-control" name="delivery_status" required>
              <option value="" selected>-Select-</option>
              <option value="2">Pending For Delivery</option>
              <option value="4">Ready For Delivery</option>
              <option value="3">Out For Delivery</option>
              <option value="1">Delivery Completed</option>
            </select>
          </div>
    </div>
    
    <div class="col-lg-4 mb-10">
      <div class="input-group mb-3">Deliver By
          <select class="w-100 dynamicstxt_s form-control" id="deliver_by_new_re" name="deliver_by" required>
              <option value="" selected>-Select-</option>
              <option value="1">Courier</option>
              <option value="2">Driver</option>
              <option value="3">Local Delivery</option>
              <option value="4">Office Boy</option>
              <option value="5">Collection by Client</option>
              <option value="6">By Email</option>
          </select>
        </div>
  </div>
  <div class="col-lg-4 mb-10" id="div_byemail_re" style="display: none;">
    <div class="input-group mb-3">Email IDs
        <input type="text" class="w-100 dynamicstxt_s form-control" name="byemail" placeholder="Email Ids">
    </div>
  </div>
  <div class="col-lg-4 mb-10">
    <div class="input-group mb-3">Attachment/AWB Copy
      <input type="file" class="w-100 dynamicstxt_s form-control" id="attach_file" name="attach_file" style="background-image: none !important;">
    </div>
  </div>
  <div class="col-lg-4 mb-10">
    <script>
      $('#deliver_by_new_re').on('change', function(e) {
        if ($('#deliver_by_new_re').val() == 1) {
          $('#div_courier_re').css("display", "block");
          $('#div_attach_file_re').css("display", "block");
          $('#div_driver_re').css("display", "none");
          $('#div_localdelivery_re').css("display", "none");
          $('#div_officeboy_re').css("display", "none");
          $('#div_collectionbyclient_re').css("display", "none");
          $('#div_byemail_re').css("display", "none");
        }
        if ($('#deliver_by_new_re').val() == 2) {
          $('#div_courier_re').css("display", "none");
          $('#div_attach_file_re').css("display", "none");
          $('#div_driver_re').css("display", "block");
          $('#div_localdelivery_re').css("display", "none");
          $('#div_officeboy_re').css("display", "none");
          $('#div_collectionbyclient_re').css("display", "none");
          $('#div_byemail_re').css("display", "none");
        }
        if ($('#deliver_by_new_re').val() == 3) {
          $('#div_courier_re').css("display", "none");
          $('#div_attach_file_re').css("display", "none");
          $('#div_driver_re').css("display", "none");
          $('#div_localdelivery_re').css("display", "block");
          $('#div_officeboy_re').css("display", "none");
          $('#div_collectionbyclient_re').css("display", "none");
          $('#div_byemail_re').css("display", "none");
        }
        if ($('#deliver_by_new_re').val() == 4) {
          $('#div_courier_re').css("display", "none");
          $('#div_attach_file_re').css("display", "none");
          $('#div_driver_re').css("display", "none");
          $('#div_localdelivery_re').css("display", "none");
          $('#div_officeboy_re').css("display", "block");
          $('#div_collectionbyclient_re').css("display", "none");
          $('#div_byemail_re').css("display", "none");
        }
        if ($('#deliver_by_new_re').val() == 5) {
          $('#div_courier_re').css("display", "none");
          $('#div_attach_file_re').css("display", "none");
          $('#div_driver_re').css("display", "none");
          $('#div_localdelivery_re').css("display", "none");
          $('#div_officeboy_re').css("display", "none");
          $('#div_collectionbyclient_re').css("display", "block");
          $('#div_byemail_re').css("display", "none");
        }
        if ($('#deliver_by_new_re').val() == 6) {
          $('#div_courier_re').css("display", "none");
          $('#div_attach_file_re').css("display", "none");
          $('#div_driver_re').css("display", "none");
          $('#div_localdelivery_re').css("display", "none");
          $('#div_officeboy_re').css("display", "none");
          $('#div_collectionbyclient_re').css("display", "none");
          $('#div_byemail_re').css("display", "block");
        }
        if ($('#deliver_by_new_re').val() == "") {
          $('#div_courier_re').css("display", "none");
          $('#div_attach_file_re').css("display", "none");
          $('#div_driver_re').css("display", "none");
          $('#div_localdelivery_re').css("display", "none");
          $('#div_officeboy_re').css("display", "none");
          $('#div_collectionbyclient_re').css("display", "none");
          $('#div_byemail_re').css("display", "none");
        }
    });
    </script>
  {{--  options  --}}
    <div class="input-group mb-3" id="div_courier_re" style="display: none;">Courier
        <select class="w-100 dynamicstxt_s form-control" id="courier" name="courier">
            <option value="" selected>-Select-</option>
            @foreach ($shipping as $value)<option value="{{ @$value->shipping_name }}">{{ @$value->shipping_name }}</option>@endforeach
        </select>
    </div>
    <div class="input-group mb-3" id="div_driver_re" style="display: none;">Driver
        <select class="w-100 dynamicstxt_s form-control" id="driver" name="driver">
            <option value="" selected>-Select-</option>
            @foreach ($driver as $value)<option value="{{ @$value->driver_name }}">{{ @$value->driver_name }}</option>@endforeach
        </select>
    </div>
    <div class="input-group mb-3" id="div_localdelivery_re" style="display: none;">Local Delivery
        <select class="w-100 dynamicstxt_s form-control" id="localdelivery" name="localdelivery">
            <option value="" selected>-Select-</option>
            <option value="Salman">Salman</option>
            <option value="Mohid">Mohid</option>
            <option value="Manan">Mannan</option>
            <option value="Usman">Usman</option>
            <option value="Ziyad">Ziyad</option>
            <option value="Akhil">Akhil</option>
        </select>
    </div>
    <div class="input-group mb-3" id="div_officeboy_re" style="display: none;">Office Boy
        <select class="w-100 dynamicstxt_s form-control" id="officeboy" name="officeboy">
            <option value="" selected>-Select-</option>
            <option value="Salman">Salman</option>
            <option value="Mohid">Mohid</option>
            <option value="Manan">Mannan</option>
            <option value="Usman">Usman</option>
            <option value="Ziyad">Ziyad</option>
            <option value="Akhil">Akhil</option>
        </select>
    </div>
    <div class="input-group mb-3" id="div_collectionbyclient_re" style="display: none;">Collection by Client
        <input type="text" class="w-100 dynamicstxt_s form-control" id="collectionbyclient" name="collectionbyclient" placeholder="Name">
        <input type="text" class="w-100 dynamicstxt_s form-control mt-2" id="contact_no" name="contact_no" placeholder="Contact No">
        <input type="text" class="w-100 dynamicstxt_s form-control mt-2" id="id_no" name="id_no" placeholder="ID No">
    </div>
    <div class="input-group mb-3" id="div_attach_file_re" style="display: none;">
        <input type="text" class="w-100 dynamicstxt_s form-control mt-2" id="awb_no" name="awb_no" placeholder="AWB No">
    </div>
  {{--  options  --}}
  </div>
    </div>

  <div class="row">
    <div class="col-lg-8 mb-10">
        <div class="no-gutters input-right-icon">
            <div class="col">
                <div class="input-effect">
                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                    <textarea class="primary-input dynamicstxt_s w-100" cols="5" id="remarks" style="height: 100px !important" name="remarks"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 pt-2"><br />
        <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
        <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
        <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
                @lang('Submit')
        </button>        
    </div>    
</div>
{{ Form::close() }}
@endif
{{--  crm-deal-track-approval-delivery  --}}


{{--  crm-deal-track-approval-receivables  2--}}
@if(session('logged_session_data.designation_id')==2 || (App\SysHelper::is_approval_access() && $deal->accounts==1 && $deal->sales==1 && $deal->purchease==1 && $deal->invoice==1 && $deal->delivery==1 && $deal->receivables!=1))
{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-track-approval-receivables','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-deal-track-approval-delivery']) }}

<input type="hidden" name="owner_id" value="{{ $del->owner }}" />
<input type="hidden" name="owner_name" value="{{ $del->ownername->full_name }}" />
<input type="hidden" name="owner_email" value="{{ $del->ownername->email }}" />

<h4 class="m-0 p-0">Receivables Approval</h4>
<div class="row">
  <div class="col-lg-4 mb-10">
      <div class="input-group mb-3">Collection
          <select class="w-100 dynamicstxt_s form-control payment_collection" name="payment_collection" required>
            <option value="" selected>-Select-</option>
            <option value="1">Approved</option>
            <option value="2">Disapproved</option>
            <option value="3">Order Cancelled</option>
          </select>
        </div>
  </div>
  <script>
    $('.payment_collection').on('change', function(e) {
      if ($('.payment_collection').val() == 3) {
        $('.credit_note_div').css("display", "block");
        $('.no_cn_div').css("display", "none");
        $('.credit_note').prop('required', true);
        $('.no_cn_req').prop('required', false);
      }
      else{
        $('.credit_note_div').css("display", "none");
        $('.no_cn_div').css("display", "block");
        $('.credit_note').prop('required', false);
        $('.no_cn_req').prop('required', true);
      }
    });
    </script>
  <div class="col-lg-4 mb-10 credit_note_div" style="display: none;">
      <div class="input-group mb-3">Credit Note
          <input type="text" class="w-100 dynamicstxt_s form-control credit_note" name="credit_note" />
        </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
      <div class="input-group mb-3">Payment Status
          <select class="w-100 dynamicstxt_s form-control no_cn_req" name="payment_status" id="payment_status_re" required>
            <option value="" selected>-Select-</option>
            <option value="1">Payment Received</option>
            <option value="2">Pending</option>
          </select>
        </div>
  </div>
  <script>
    $('#payment_status_re').on('change', function(e) {
      if ($('#payment_status_re').val() == 2) {
        $('#payment_status_div_re').css("display", "block");
        $('#reminder_date_re').prop('required', true);
        $('#cheque_date_re').prop('required', false);
        $('#deposit_date_re').prop('required', false);
        $('#open_credit_date_re').prop('required', false);
        $('#payment_date_re').prop('required', false);
        $('#credit_card_deposit_date_re').prop('required', false);
        $('#banktt_date_re').prop('required', false);
      }
      else{
        $('#payment_status_div_re').css("display", "none");
        $('#reminder_date_re').prop('required', false);
        $('#cheque_date_re').prop('required', true);
        $('#deposit_date_re').prop('required', true);
        $('#open_credit_date_re').prop('required', true);
        $('#payment_date_re').prop('required', true);
        $('#credit_card_deposit_date_re').prop('required', true);
        $('#banktt_date_re').prop('required', true);
      }
    });
    </script>
  <div class="col-lg-4 mb-10" id="payment_status_div_re" style="display: none;">
      <div class="input-group mb-3 text-danger">Reminder Date
          <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="reminder_date" id="reminder_date_re" placeholder="Select Date"/>
          <select class="w-100 dynamicstxt_s form-control" name="reminder_time">
            <option value="" selected>-Select Time-</option>
            <option value="09:00:00">09:00 AM</option>
            <option value="10:00:00">10:00 AM</option>
            <option value="11:00:00">11:00 AM</option>
            <option value="12:00:00">12:00 PM</option>
            <option value="13:00:00">01:00 PM</option>
            <option value="14:00:00">02:00 PM</option>
            <option value="15:00:00">03:00 PM</option>
            <option value="16:00:00">04:00 PM</option>
            <option value="17:00:00">05:00 PM</option>
            <option value="18:00:00">06:00 PM</option>
            <option value="19:00:00">07:00 PM</option>
            <option value="20:00:00">08:00 PM</option>
            <option value="21:00:00">09:00 PM</option>
          </select>
        </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">Amount &nbsp;&nbsp;<a id="addAmount1p" class="text-success btn btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
        <input type="text" class="w-100 dynamicstxt_s form-control no_cn_req" id="amount" name="amount" required />
      </div>
</div>
<div class="col-lg-4 mb-10" id="addAmountDiv1p" style="display: none;">
    <div class="input-group mb-3">Amount &nbsp;&nbsp;<a id="addAmount2p" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
        <input type="text" class="w-100 dynamicstxt_s form-control" name="amount2" />
      </div>
</div>
<div class="col-lg-4 mb-10" id="addAmountDiv2p" style="display: none;">
    <div class="input-group mb-3">Amount &nbsp;&nbsp;<a id="addAmount3p" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
        <input type="text" class="w-100 dynamicstxt_s form-control" name="amount3" />
      </div>
</div>
<script>
  $('#addAmount1p').on('click', function(e) {
    if( $('#addAmountDiv1p'). css("display") == "block" ){
      $('#addAmountDiv2p').css("display", "block");
    }
      $('#addAmountDiv1p').css("display", "block");

      if( $('#addAmountDiv1p'). css("display") == "block" && $('#addAmountDiv2p'). css("display") == "block" ){          
        $('#addAmount1p').css("display", "none");
      }else{$('#addAmount1p').css("display", "block");}
  });
  $('#addAmount2p').on('click', function(e) {
      $('#addAmountDiv1p').css("display", "none");
      if( $('#addAmountDiv1p'). css("display") == "block" && $('#addAmountDiv2p'). css("display") == "block" ){          
        $('#addAmount1p').css("display", "none");
      }else{$('#addAmount1p').css("display", "block");}
  });
  $('#addAmount3p').on('click', function(e) {
      $('#addAmountDiv2p').css("display", "none");
      if( $('#addAmountDiv1p'). css("display") == "block" && $('#addAmountDiv2p'). css("display") == "block" ){          
        $('#addAmount1p').css("display", "none");
      }else{$('#addAmount1p').css("display", "block");}
  });
</script>
  <div class="col-lg-4 mb-10 no_cn_div">
      <div class="input-group mb-3">Balance Amount
          <input type="text" class="w-100 dynamicstxt_s form-control" id="balance_amount" name="balance_amount" />
        </div>
  </div>
  <input type="hidden" name="payment_mode" value="{{ $deal->payment_mode }}" />
  <input type="hidden" name="payment_mode_sec" value="{{ $deal->payment_mode_sec }}" />
  
  

  @if($deal->payment_mode==1) {{--  Cash  --}}
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">Date &nbsp;&nbsp;<a id="addCashDate1p" class="text-success btn btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
        <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" name="cash_date" required />
      </div>
  </div>
  <div class="col-lg-4 mb-10" id="addCashDateDiv1p" style="display: none;">
    <div class="input-group mb-3">Date No &nbsp;&nbsp;<a id="addCashDate2p" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
        <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="cash_date2" />
      </div>
  </div>
  <div class="col-lg-4 mb-10" id="addCashDateDiv2p" style="display: none;">
    <div class="input-group mb-3">Date No &nbsp;&nbsp;<a id="addCashDate3p" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
        <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="cash_date3" />
      </div>
  </div>
  <script>
    $('#addCashDate1p').on('click', function(e) {
      if( $('#addCashDateDiv1p'). css("display") == "block" ){
        $('#addCashDateDiv2p').css("display", "block");
      }
        $('#addCashDateDiv1p').css("display", "block");

        if( $('#addCashDateDiv1p'). css("display") == "block" && $('#addCashDateDiv2p'). css("display") == "block" ){          
          $('#addCashDate1p').css("display", "none");
        }else{$('#addCashDate1p').css("display", "block");}
    });
    $('#addCashDate2p').on('click', function(e) {
        $('#addCashDateDiv1p').css("display", "none");
        if( $('#addCashDateDiv1p'). css("display") == "block" && $('#addCashDateDiv2p'). css("display") == "block" ){          
          $('#addCashDate1p').css("display", "none");
        }else{$('#addCashDate1p').css("display", "block");}
    });
    $('#addCashDate3p').on('click', function(e) {
        $('#addCashDateDiv2p').css("display", "none");
        if( $('#addCashDateDiv1p'). css("display") == "block" && $('#addCashDateDiv2p'). css("display") == "block" ){          
          $('#addCashDate1p').css("display", "none");
        }else{$('#addCashDate1p').css("display", "block");}
    });
  </script>

  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">1000 x
        <input type="number" class="w-100 dynamicstxt_s form-control" id="thousand" name="thousand" />
      </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">500 x
        <input type="number" class="w-100 dynamicstxt_s form-control" id="fivehundred" name="fivehundred" />
      </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">100 x
        <input type="number" class="w-100 dynamicstxt_s form-control" id="hundred" name="hundred" />
      </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">50 x
        <input type="number" class="w-100 dynamicstxt_s form-control" id="fifty" name="fifty" />
      </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">20 x
        <input type="number" class="w-100 dynamicstxt_s form-control" id="twenty" name="twenty" />
      </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">10 x
        <input type="number" class="w-100 dynamicstxt_s form-control" id="ten" name="ten" />
      </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">5 x
        <input type="number" class="w-100 dynamicstxt_s form-control" id="five" name="five" />
      </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">1 x
        <input type="number" class="w-100 dynamicstxt_s form-control" id="one" name="one" />
      </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">50 x
        <input type="number" class="w-100 dynamicstxt_s form-control" id="fiftyp" name="fiftyp" />
      </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">25 x
        <input type="number" class="w-100 dynamicstxt_s form-control" id="twentyfivep" name="twentyfivep" />
      </div>
  </div>
  @elseif($deal->payment_mode==2) {{--  Cheque  --}}
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">Cheque No &nbsp;&nbsp;<a id="addChequeNo1p" class="text-success btn btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
        <input type="text" class="w-100 dynamicstxt_s form-control" id="cheque_no" name="cheque_no" />
      </div>
  </div>
  <div class="col-lg-4 mb-10" id="addChequeNoDiv1p" style="display: none;">
    <div class="input-group mb-3">Cheque No &nbsp;&nbsp;<a id="addChequeNo2p" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
        <input type="text" class="w-100 dynamicstxt_s form-control" name="cheque_no2" />
      </div>
  </div>
  <div class="col-lg-4 mb-10" id="addChequeNoDiv2p" style="display: none;">
    <div class="input-group mb-3">Cheque No &nbsp;&nbsp;<a id="addChequeNo3p" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
        <input type="text" class="w-100 dynamicstxt_s form-control" name="cheque_no3" />
      </div>
  </div>
  <script>
    $('#addChequeNo1p').on('click', function(e) {
      if( $('#addChequeNoDiv1p'). css("display") == "block" ){
        $('#addChequeNoDiv2p').css("display", "block");
      }
        $('#addChequeNoDiv1p').css("display", "block");
        
        if( $('#addChequeNoDiv1p'). css("display") == "block" && $('#addChequeNoDiv2p'). css("display") == "block" ){          
          $('#addChequeNo1p').css("display", "none");
        }else{$('#addChequeNo1p').css("display", "block");}
    });
    $('#addChequeNo2p').on('click', function(e) {
        $('#addChequeNoDiv1p').css("display", "none");
        if( $('#addChequeNoDiv1p'). css("display") == "block" && $('#addChequeNoDiv2p'). css("display") == "block" ){          
          $('#addChequeNo1p').css("display", "none");
        }else{$('#addChequeNo1p').css("display", "block");}
    });
    $('#addChequeNo3p').on('click', function(e) {
        $('#addChequeNoDiv2p').css("display", "none");
        if( $('#addChequeNoDiv1p'). css("display") == "block" && $('#addChequeNoDiv2p'). css("display") == "block" ){          
          $('#addChequeNo1p').css("display", "none");
        }else{$('#addChequeNo1p').css("display", "block");}
    });
  </script>
  <div class="col-lg-4 mb-10 no_cn_div">
      <div class="input-group mb-3">Cheque Date &nbsp;&nbsp;<a id="addChequeDate1p" class="text-success btn btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
          <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" id="cheque_date_re" name="cheque_date" required />
        </div>
  </div>
  <div class="col-lg-4 mb-10" id="addChequeDateDiv1p" style="display: none;">
      <div class="input-group mb-3">Cheque Date &nbsp;&nbsp;<a id="addChequeDate2p" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
          <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="cheque_date2" />
        </div>
  </div>
  <div class="col-lg-4 mb-10" id="addChequeDateDiv2p" style="display: none;">
      <div class="input-group mb-3">Cheque Date &nbsp;&nbsp; &nbsp;&nbsp;<a id="addChequeDate3p" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
          <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="cheque_date3" />
        </div>
  </div>
  <script>
    $('#addChequeDate1p').on('click', function(e) {
      if( $('#addChequeDateDiv1p'). css("display") == "block" ){
        $('#addChequeDateDiv2p').css("display", "block");
      }
        $('#addChequeDateDiv1p').css("display", "block");

        if( $('#addChequeDateDiv1p'). css("display") == "block" && $('#addChequeDateDiv2p'). css("display") == "block" ){          
          $('#addChequeDate1p').css("display", "none");
        }else{$('#addChequeDate1p').css("display", "block");}
    });
    $('#addChequeDate2p').on('click', function(e) {
        $('#addChequeDateDiv1p').css("display", "none");
        if( $('#addChequeDateDiv1p'). css("display") == "block" && $('#addChequeDateDiv2p'). css("display") == "block" ){          
          $('#addChequeDate1p').css("display", "none");
        }else{$('#addChequeDate1p').css("display", "block");}
    });
    $('#addChequeDate3p').on('click', function(e) {
        $('#addChequeDateDiv2p').css("display", "none");
        if( $('#addChequeDateDiv1p'). css("display") == "block" && $('#addChequeDateDiv2p'). css("display") == "block" ){          
          $('#addChequeDate1p').css("display", "none");
        }else{$('#addChequeDate1p').css("display", "block");}
    });
  </script>

  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">Cheque Copy<br />
      <div class="form-group files">
        <input type="file" class="w-100 dynamicstxt_s form-control" id="cheque_copy" multiple="multiple" name="cheque_copy[]" style="background-image: none !important;"></div>
      </div>
</div>
  @elseif($deal->payment_mode==3) {{--  Bank Transfer  --}}
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">Bank Name
        <input type="text" class="w-100 dynamicstxt_s form-control" id="bank_name" name="bank_name" />
      </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">Deposit Date
        <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" style="background-image: none !important;" id="deposit_date_re" name="deposit_date" required />
      </div>
</div>
<div class="col-lg-4 mb-10">
  <div class="input-group mb-3">Deposit Date 2
      <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" style="background-image: none !important;" name="deposit_date2" />
    </div>
</div>
  @elseif($deal->payment_mode==4) {{--  Open Credit  --}}
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">Open Credit Date
        <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" style="background-image: none !important;" id="open_credit_date_re" name="open_credit_date" required />
      </div>
</div>
  @elseif($deal->payment_mode==5) {{--  Credit Card  --}}
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">Card Type
        <input type="text" class="w-100 dynamicstxt_s form-control" id="credit_card_type" name="credit_card_type" />
      </div>
  </div>
  <div class="col-lg-4 mb-10 no_cn_div">
    <div class="input-group mb-3">Payment Date
        <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" style="background-image: none !important;" name="payment_date_re" name="payment_date" required />
      </div>
</div>
<div class="col-lg-4 mb-10 no_cn_div">
  <div class="input-group mb-3">Deposit Date
      <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" style="background-image: none !important;" name="credit_card_deposit_date_re" name="credit_card_deposit_date" required />
    </div>
</div>
  @elseif($deal->payment_mode==6) {{--  Bank TT  --}}
<div class="col-lg-4 mb-10 no_cn_div">
  <div class="input-group mb-3">BankTT Date &nbsp;&nbsp;<a id="addBankTTDate1p" class="text-success btn btn-xs"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
      <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle no_cn_req" id="banktt_date_re" name="banktt_date" required />
    </div>
</div>
<div class="col-lg-4 mb-10" id="addBankTTDateDiv1p" style="display: none;">
<div class="input-group mb-3">BankTT Date &nbsp;&nbsp;<a id="addBankTTDate2p" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
    <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="banktt_date2" />
  </div>
</div>
<div class="col-lg-4 mb-10" id="addBankTTDateDiv2p" style="display: none;">
<div class="input-group mb-3">BankTT Date &nbsp;&nbsp;<a id="addBankTTDate3p" class="text-danger btn btn-xs"><i class="fa fa-minus-square" aria-hidden="true"></i> Delete</a>
  <input type="text" class="w-100 dynamicstxt_s form-control primary-input date datestyle" name="banktt_date3" />
</div>
</div>
<script>
$('#addBankTTDate1p').on('click', function(e) {
if( $('#addBankTTDateDiv1p'). css("display") == "block" ){
  $('#addBankTTDateDiv2p').css("display", "block");
}
  $('#addBankTTDateDiv1p').css("display", "block");

  if( $('#addBankTTDateDiv1p'). css("display") == "block" && $('#addBankTTDateDiv2p'). css("display") == "block" ){          
    $('#addBankTTDate1p').css("display", "none");
  }else{$('#addBankTTDate1p').css("display", "block");}
});
$('#addBankTTDate2p').on('click', function(e) {
  $('#addBankTTDateDiv1p').css("display", "none");
  if( $('#addBankTTDateDiv1p'). css("display") == "block" && $('#addBankTTDateDiv2p'). css("display") == "block" ){          
    $('#addBankTTDate1p').css("display", "none");
  }else{$('#addBankTTDate1p').css("display", "block");}
});
$('#addBankTTDate3p').on('click', function(e) {
  $('#addBankTTDateDiv2p').css("display", "none");
  if( $('#addBankTTDateDiv1p'). css("display") == "block" && $('#addBankTTDateDiv2p'). css("display") == "block" ){          
    $('#addBankTTDate1p').css("display", "none");
  }else{$('#addBankTTDate1p').css("display", "block");}
});
</script>

<div class="col-lg-4 mb-10 no_cn_div">
  <div class="input-group mb-3">BankTT Copy<br />
    <div class="form-group files">
      <input type="file" class="w-100 dynamicstxt_s form-control" id="banktt_copy" multiple="multiple" name="banktt_copy[]" style="background-image: none !important;"></div>
    </div>
</div>
  @endif  
  
</div>
<div class="row">
  <div class="col-lg-8 mb-10">
      <div class="no-gutters input-right-icon">
          <div class="col">
              <div class="input-effect">
                  <label class="txtlbl">@lang('Remarks')<span></span></label>
                  <textarea class="primary-input dynamicstxt_s w-100" cols="5" id="remarks" style="height: 100px !important" name="remarks"></textarea>
              </div>
          </div>
      </div>
  </div>
  <div class="col-lg-4 pt-2"><br />
      <input type="hidden" id="deal_id" name="deal_track_id" value="{{ $deal->id }}" />
      <input type="hidden" id="deal_id" name="deal_id" value="{{ $deal->deal_id }}" />
      <button type="submit" class="btn btn-sm btn-dark pl-3 pr-3" id="btnSubmit">
              @lang('Submit')
      </button>
      
      
  </div>
  
  
</div>
{{ Form::close() }}
@endif
{{--  crm-deal-track-approval-receivables  --}}

            
          </div>
        </div>
      </div>
    </div>
    <!-- Modal-->

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
@endsection

@section('script')
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
$(document).on("change", "#deliver_by2", function () {
  var deliver_by = $("#deliver_by2").val();
  var driver = $("#driver2").val();
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
                    
                    $('#driver2').find('option').not(':first').remove();
                    for(var i=0; i<len; i++){
                        var id = dataResult['data'][i].driver_name;
                        var name = dataResult['data'][i].driver_name;
                        var option = "<option value='"+id+"'>"+name+"</option>";
                        $("#driver2").append(option);
                    }
                }
            }
          }
    });
});

$(document).on("click", "#btn_add_company", function () {
    
    $("#btn_add_company").css("display", "none");

    var company_name_add = $("#company_name_add").val();
    var cust_name_add = $("#cust_name_add").val();
    var cust_no_add = $("#cust_no_add").val();
    var cust_email_add = $("#cust_email_add").val();
    var cust_address_add = $("#cust_address_add").val();
    
    var action = "{{ URL::to('crm-leads-addcustomername') }}";
    $.ajax({
        url: action,
        type: "GET",
        data: {
            _token: '{{ csrf_token() }}',
            company_name_add: company_name_add,
            cust_name_add: cust_name_add,
            cust_no_add: cust_no_add,
            cust_email_add: cust_email_add,
            cust_address_add: cust_address_add,
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
                alert("Company Name already exists!!");
                $('#company_name_add').css("border", "1px solid red"); $('#company_name_add').focus();
                $("#btn_add_company").css("display", "block");
            }
            else{
                if(dataResult['data'] != null){
                len = dataResult['data'].length;
                }
                if(len > 0){
                    
                    //$('#company_name').find('option').not(':first').remove();
                    for(var i=0; i<len; i++){
                        var id = dataResult['data'][i].id;
                        var name = dataResult['data'][i].name;
                        var name2 = dataResult['data'][i].code;
                        var option = "<option value='"+id+"'>"+name+"</option>";
                        $("#company_name").append(option);
                    }
                    alert('Company Name Added Successfully!!');
                    $('#btn_close2').click();
                    $("#btn_add_company").css("display", "block");
                }
            }
          }
    });
});

$(document).on("change", "#company_name", function () {
    var id = $("#company_name").val();
    get_cust_name(id);
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
                        $("#cust_name").val(dataResult['data'][i].contcat_person);
                        $("#cust_no").val(dataResult['data'][i].mobile);
                        $("#cust_email").val(dataResult['data'][i].email);
                        $("#address").val(dataResult['data'][i].address);
                    }                        
                }
                else{
                    $("#cust_name").val("");
                    $("#cust_no").val("");
                    $("#cust_email").val("");
                }
                $("#loading_bg").css("display", "none");
        }
    });
}

function change_stage(id) {
    $("#loading_bg").css("display", "block");
    var stage = $("#edit_stage").val();

    if (stage == "" || stage <= 0) {
        alert("Please Choose Stage");
        $("#edit_stage").focus();
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
@endsection
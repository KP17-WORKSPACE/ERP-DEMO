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
    .leadbox{border: solid 1px #e5e5de; border-radius: 5px; background: #fffff2; padding: 5px 5px 10px 15px; margin-right: 15px;}
    .leadbox2{border: solid 1px #e5e5de; border-radius: 5px; background: #fffff2; padding: 7px 5px 0px 15px; font-size: 17px;}
    .pro-box{ border: solid 1px #e5e5de; margin: 5px 15px 5px 5px; padding: 10px;}
    .pro-box:hover{ background: #eff3e7;}
</style>

    
<section class="sms-breadcrumb mb-20 white-box">
    <div class="container-fluid">
        <div class="row" style="float: left;">
            <h1>@lang('Edit Quote')</h1>
        </div>
        <div class="row" style="float: right;">
            <a href="{{ url('crm-dashboard') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> CRM Dashboard</a>
            <a href="{{ url('crm-deals') }}" class="top-btn-r"><i class="far fa fa-plus" aria-hidden="true"></i> New</a>
            <a href="{{ url('crm-deals/show') }}" class="top-btn-r"><i class="far fa-file-text" aria-hidden="true"></i> View</a>
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

    <section class="admin-visitor-area">


            <div class="row">
                <div class="col-lg-8 pl-3">

                    <div class="white-box leadbox">
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote-cs/'. $deal_id .'/add', 'method' => 'POST', 'id' => 'crm-deals-form']) }}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="input-effect">
                                    <label class="txtlbl">Description</label>
                                    <textarea class="w-100" name="description" autocomplete="off" required></textarea>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <label class="txtlbl">Employees/Work Stations</label>
                                    <input class="primary-input dynamicstxt_s w-100 form-control" name="work_stations" autocomplete="off" required />
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <label class="txtlbl">Price per Employee per Month</label>
                                    <input class="primary-input dynamicstxt_s w-100 form-control" name="price_per_month" autocomplete="off" required />
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <label class="txtlbl">Critical Assets included (Special Deal)</label>
                                    <input class="primary-input dynamicstxt_s w-100 form-control" name="critical_assets" autocomplete="off" required />
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <label class="txtlbl">Additional Critical Assets</label>
                                    <input class="primary-input dynamicstxt_s w-100 form-control" name="additional_critical_assets" autocomplete="off" required />
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <label class="txtlbl">Price per Critical Asset per Month</label>
                                    <input class="primary-input dynamicstxt_s w-100 form-control" name="price_per_critical_asset" autocomplete="off" required />
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-effect">
                                    <label class="txtlbl">Total Price per Month</label>
                                    <input class="primary-input dynamicstxt_s w-100 form-control" name="total_price_per_month" autocomplete="off" required />
                                </div>
                            </div>

                            <div class="col-lg-12 text-right">
                                <div class="input-effect mt-2"><br />
                                    <input type="hidden" name="deal_id" id="deal_id" value="{{ $deal_id }}"/>
                                    <input type="hidden" name="company_id" id="company_id" value="{{ $company_id }}"/>
                                    <input type="hidden" name="currency_id" id="currency_id" value="{{ $currency_id }}"/>
                                    <input type="hidden" name="customer_type" id="customer_type" value="{{ $customer_type }}"/>
                                    <input type="hidden" name="payment_terms" id="payment_terms" value="{{ $payment_terms }}"/>
                                    <input type="hidden" name="delivery_date" id="delivery_date" value="{{ $delivery_date }}"/>
                                    <button type="submit" class="primary-btn fix-gr-bg pt-2 pb-2 pl-3 pr-3" id="btnSubmit"><i class="fa fa-plus" aria-hidden="true"></i> Add</button>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                    
                    <br />

                    <div class="white-box leadbox">
                        <div class="add-visitor">
                            <div class="row">
                                <div class="col-lg-12"><a style="float: right;" class="btn btn-xs btn-info" href="{{url('crm-quote/'.$deal_id.'/addnew')}}">
                                    <i class="fa fa-plus" aria-hidden="true"></i> Add New</a>
                                    <h5 class="mb-2 mt-2">Quote Items</h5>
                                        <hr />
                                        <?php
                                        $t_qty=0; $t_price=0; $t_discount=0; $t_netamount=0; $t_currency="";
                                        ?>
                                    @if(count($quotationitems)>0)
                                        @foreach ($quotationitems as $Item)
                                            <div class="white-box pro-box">
                                                <div class="row">
                                                    <div class="col-md-12"><span class="text-xs text-bold">Description</span><br />
                                                        {{ $Item->description }}</div>
                                                    <?php $t_price += ($Item->total_price_per_month * 12);
                                                    $t_netamount += ($Item->total_price_per_month * 12);
                                                    $t_currency = $quotationitems[0]->currency->code; ?>

                                                    <div class="col-md-4"><span class="text-xs text-bold">Employees/Work Stations</span><br />
                                                        {{ $Item->work_stations }}</div>
                                                    <div class="col-md-4"><span class="text-xs text-bold">Price per Employee per Month</span><br />
                                                        {{ $Item->price_per_month }}</div>
                                                    <div class="col-md-4"><span class="text-xs text-bold">Critical Assets included (Special Deal)</span><br />
                                                        {{ $Item->critical_assets }}</div>
                                                    <div class="col-md-4"><span class="text-xs text-bold">Additional Critical Assets</span><br />
                                                        {{ $Item->additional_critical_assets }}</div>
                                                    <div class="col-md-4"><span class="text-xs text-bold">Price per Critical Asset per Month</span><br />
                                                        {{ $Item->price_per_critical_asset }}</div>
                                                    <div class="col-md-4"><span class="text-xs text-bold">Total Price per Month</span><br />
                                                        {{ $Item->total_price_per_month }} {{ $t_currency }}</div>
                                                    <div class="col-md-12 text-right"><br />
                                                        <button class="btn btn-danger btn-xs" title="Delete" id="txt_btn_del_{{$Item->id}}" onclick="del_csquote({{$Item->id}})">Delete</button>
                                                    </div>

                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="col-lg-4">
                    <div class="white-box leadbox">
                        @if(isset($quotation))
                        <div class="row">
                            <div class="col-lg-12">
                                <h5 class="mb-2 mt-2 text-lg">Deal Summary</h5>
                                <hr />
                                <table width="100%" class="text-sm">
                                    <tr><td width="130px">Deal Name </td><td>: <b>{{ $quotation->deal_name }}</b></td></tr>
                                    <tr><td>Customer Name </td><td>: {{ $quotation->customername->name }}</td></tr>
                                    <tr><td colspan="2"><hr /></td></tr>
                                    <tr><td>Contact Person </td><td>: {{ $quotation->cust_name }}</td></tr>
                                    <tr><td>Address </td><td>: {{ $quotation->customername->address }}</td></tr>
                                    <tr><td>Mobile </td><td>: {{ $quotation->cust_no }}</td></tr>
                                    <tr><td>Email </td><td>: {{ $quotation->cust_email }}</td></tr>
                                    <tr><td colspan="2"><hr /></td></tr>
                                    
                                    {{--  <tr><td>Company </td><td>: {{ $quotationitems[0]->company->company_name }}</td></tr>  --}}
                                    <tr><td>Quote Owner </td><td>: {{ $quotation->ownername->first_name }} {{ $quotation->ownername->middle_name }} {{ $quotation->ownername->last_name }}</td></tr>
                                    <tr><td>Quote Currency </td><td>: {{ $currency_code }}</td></tr>
                                    <tr><td>Payment Terms </td><td>: {{ $payment_terms_name }}</td></tr>
                                    <tr><td>Delivery Date </td><td>: {{ date('d/m/Y', strtotime($delivery_date)) }} 
                                        {{--  | <a class="text-primary" style="cursor: pointer;" data-toggle="modal" data-target="#exampleModalCenter">Change Date</a>  --}}
                                    </td></tr>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
            
            @if(count($quotationitems)>0)
            <div style="width: 100%;position: fixed;bottom: 0px;background: #eff3e7;z-index:9; padding: 20px 20px">
                <div class="row">
                    <div class="col-lg-2 leadbox2">Amount : <b>{{ @App\SysHelper::com_curr_format(($t_netamount), 2, '.', ',') }} {{ $t_currency }}</b></div>
                    <?php $t_vat = ($t_netamount * $quotationitems[0]->company->net_vat/100); ?>
                    <div class="col-lg-2 leadbox2">Vat : <b>{{ @App\SysHelper::com_curr_format(($t_vat), 2, '.', ',') }} {{ $t_currency }}</b></div>
                    <div class="col-lg-2 leadbox2">Net Amount : <b>{{ @App\SysHelper::com_curr_format(($t_netamount+$t_vat), 2, '.', ',') }} {{ $t_currency }}</b></div>                    
                    <div class="col-lg-1">
                    <a class="btn btn-danger mt-1 float-right" href="{{url('crm-deals/'.$deal_id.'/view')}}">Save</a></div>
                </div>
            </div>
            @endif

        </div>

    </section>

@endsection

@section('script')
    <script>
        
        function del_csquote(id) {
            $("#loading_bg").css("display", "block");
            var btn = $("#del_btn_del_"+id).val();
            $(btn).attr('disabled', true);
    
            var action = "{{ URL::to('crm-quote-cs-deleteitems') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                    } else {
                        $("#loading_bg").css("display", "none");
                        //alert("Renewed! Please update and continue");
                        location.reload(true);
                    }
                }
            });
        }

    </script>
@endsection
@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    
    <?php try { ?>

    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3">
                <h2 class="page-heading m-0">Deal Track (Deal ID - {{ $edit->id }})</h2>
                <span class="page-label">Home - Deal - Deal Track</span>
            </div>
            <div>
                {{--  <a href="{{ url('crm-deals/show') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> View Deals</a>
                <a href="{{ url()->previous() }}" type="button" class="btn btn-warning"><i class="fa fa-arrow-left"></i> Back</a>  --}}
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Deal Info {!! App\SysHelper::deal_type_new($edit->isproject) !!}
                    </h2>
                    <p class="mb-2 text-white-100 text-uppercase">{{ $edit->customername->name }}</p>
                    <span class="mb-1">Deal Value : {{ App\SysHelper::currancy_format_deal($edit->deal_value,$edit->company_id) }} {{ $edit->dealcurrency->code }}</span>
                    @if ($edit->estimated_close_date != '')
                    <span class="mb-1">Estimated Close Date : {{ date('m/d/Y', strtotime($edit->estimated_close_date)) }}</span>
                    @endif
                    <div class="text-capitalize">Stage : <b class="">
                        @if($edit->stage==1) <span class="btn-warning btn-badge py-1 px-2">Prospecting</span> @endif
                        @if($edit->stage==2) <span class="btn-success btn-badge py-1 px-2">Quote</span> @endif
                        @if($edit->stage==3) <span class="btn-info btn-badge py-1 px-2">Closure</span> @endif
                        @if($edit->stage==4) 
                        <?php
                        $data = App\SysHelper::deal_track_status($edit->id);
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
                        
                        @if(App\SysHelper::set_track($edit->id)==1)
                            <span class="{{ $color }} btn-badge py-1 px-2" >
                            @if($data=="Fulfill")<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>@endif {{ $data }} </span>
                        @endif
                            
                        @endif
                        @if($edit->stage==5) <span class="btn-danger btn-badge py-1 px-2">Lost</span> @endif
    

                    </b>
                    

                         </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Owner Info</h2>
                    <h6 class="sub-head text-capitalize text-dark">{{ $edit->ownername->first_name }} {{ $edit->ownername->middle_name }} {{ $edit->ownername->last_name }}</h6>
                    <p class="mb-2 text-gray-800">Added On : {{ date('d/m/Y H:i:s', strtotime(@$edit->created_at)) }} @if ($edit->source != '') | Source : {{ $edit->source }}
                        @if ($edit->source_o != '') - {{ $edit->source_o }} @endif @endif
                </p>
                    <span class="mb-1"> <span class="font-semibold">Mob No :</span> {{ $edit->ownername->mobile }}</span>
                    <span class="mb-1"><span class="font-semibold">Mail :</span> {{ $edit->ownername->email }}</span>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Customer Info </h2>
                    <h6 class="sub-head text-capitalize text-dark">{{ $edit->customername->name }}</h6>
                    <p class="mb-2 text-gray-800">{{ $edit->customername->address }}</p>                    
                    <span class="mb-1"> <span class="font-semibold">Contact :</span> {{ $edit->cust_name }}</span>
                    <span class="mb-1"><span class="font-semibold">Mob No :</span> {{ $edit->cust_no }} | <span class="font-semibold">Mail :</span> {{ $edit->cust_email }}</span>
                </div>
            </div>
        </div>

        @if(isset($dealreturn))

        <div class="card pt-3 pb-2 pl-4 pr-4 mb-2">
            <h2 class="page-heading">Return Submited Details</h2>
            <div class="border bg__light p-4">
                <div class="row">
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <h6 class="sub-head mb-1">Date</h6>
                        <p class="text-muted">{{date('d-M-Y', strtotime(@$dealreturn->created_at))}}</p>
                    </div>
                    <div class="col-lg-5 col-md-4 col-sm-6">
                        <h6 class="sub-head mb-1">Remarks</h6>
                        <p class="text-muted">{{@$dealreturn->remarks}}</p>
                    </div>
                    <div class="col-lg-2 col-md-4 col-sm-6">
                        <h6 class="sub-head mb-1">Status</h6>
                        <p class="text-muted">
                            @if($dealreturn->status==1) Approved @endif
                            @if($dealreturn->status==2) Rejected @endif
                            @if($dealreturn->status==0) Pending @endif
                        </p>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <h6 class="sub-head mb-1">Quotation</h6>
                        <a class="btn btn-sm btn-dark" href="{{url('crm-quote/'.$edit->id.'/downloadwp')}}"> <i class="fa fa-download mr-2"></i>Quotation</a>
                    <a class="btn btn-sm btn-info" href="{{url('crm-quote/'.$edit->id.'/downloadev')}}"> <i class="fa fa-download mr-2"></i>VAT Excluded</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
        <div class="col-lg-4 mb-3">
            <div class="card p-4 h-100">
                <h6 class="page-heading mb-3">Collection</h6>
                <div>
                    @php $rem = ""; @endphp
                    @if (count($collection)>0)
                        @foreach ($collection as $item)
                        <b>Part No:</b> {{ $item->partno }} - {{ $item->qty }}Qty On <b>Date:</b> {{ date('d/M/Y', strtotime($item->ret_date)) }}<br />
                        @php $rem = $item->remarks @endphp
                        @endforeach
                        <b>Remarks:</b> {{ $rem }}<br />
                    @else
                        Waiting for Update
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card p-4 h-100">
                <h2 class="page-heading mb-3">Sales</h2>
                <div>
                    @if (count($sales)>0)
                        @foreach ($sales as $item)
                        <b>Sales Return No:</b> {{ $item->sales_ret_no }}<br />
                        <b>Amount:</b> {{ $item->amount }}<br />
                        <b>VAT Amount:</b> {{ $item->amountvat }}<br />
                        <b>Remarks:</b> {{ $item->remarks }}
                        @endforeach
                    @else
                        Waiting for Update
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card p-4 h-100">
                <h2 class="page-heading mb-3">Payable</h2>
                <div>
                    @if (count($payable)>0)
                        @foreach ($payable as $item)
                        <b>Mode of Pay:</b> @if ($item->mode_of_pay==1)
                        Adjust against next order
                        @else
                        Paid Off
                        @endif
                        
                        <br />
                        <b>Remarks:</b> {{ $item->remarks }}
                        @endforeach
                    @else
                        Waiting for Update
                    @endif
                </div>
            </div>
        </div>
        </div>

            </div>
        </div>
        @endif

        @if($dealreturn->collection==0 && (session('logged_session_data.designation_id')==34 || (App\SysHelper::is_return_approval_access() && $dealreturn->collection==0)))
        <div class="card p-4 mb-4">
            <h2 class="page-heading mb-3">Collection Update</h2>
            <div class="border p-4">
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-return-collection','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-leads-form']) }}


<div class="form-group row">
    
    <div class="col-lg-4">
        <div class="input-effect">
            <label class="txtlbl">@lang('Part Number')<span></span></label><br />
            <div class="form-group">
                <input type="text" class="form-control" id="partno" name="partno[]" />
              </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="input-effect">
            <label class="txtlbl">@lang('Qty')<span></span></label><br />

            <div class="form-group">
                <input type="number" class="form-control" id="qty" name="qty[]" />
              </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="input-effect">
            <label class="txtlbl">@lang('Date')<span></span></label><br />
            <div class="form-group">
                <input type="date" class="form-control" id="ret_date" name="ret_date[]" />
              </div>
        </div>
    </div>
    <div class="col-lg-1"><br /><br />
        <a class="btn btn-xs btn-dark text-white float-right p-0 pl-2 pr-2 mt-1 addmoore_wificontroller"><i class="fa fa-plus text-blue" aria-hidden="true"></i> Add</a>
    </div>
</div>
<div class="row_wificontroller_div  m-0 p-0"></div>
<div class="form-group row">
    <div class="col-lg-12 m-1">
    </div>
</div>    
<div style="display:none;">
    <div class="row_wificontroller">
        <div class="col-lg-4">
            <input type="text" class="form-control" id="partno" name="partno[]" />
        </div>
        <div class="col-lg-4">
            <input type="number" class="form-control" id="qty" name="qty[]" />
        </div>
        <div class="col-lg-3">
            <input type="date" class="form-control" id="ret_date" name="ret_date[]" />
        </div>
        <div class="col-lg-1">
            <a class="btn btn-xs btn-danger text-white float-right p-1 pl-2 pr-2 remove_wificontroller"><i class="fa fa-times" aria-hidden="true"></i></a>
        </div>
    </div>
</div>

<script>
	jQuery(function (){
        
        $(document).on("click", ".addmoore_wificontroller", function () {
        $('.row_wificontroller_div').append('<div class="form-group row">' + $('.row_wificontroller').html() + '</div>');});
        $(document).on("click", ".remove_wificontroller", function () {$(this).closest('.row').remove();});
        });
    </script>

                <div class="row">
                    <div class="col-lg-8 mb-10">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                                    <textarea class="dynamicstxt_s w-100 form-control" style="height: 100px !important" name="remarks" rows="4" autocomplete="off" id="remarks" placeholder="Remarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 mb-3"><br /><br /><br /><br />
                        <input type="hidden" id="ret_id" name="ret_id" value="{{ $dealreturn->id }}"/>
                        <input type="hidden" id="deal_id" name="deal_id" value="{{ $dealreturn->deal_id }}"/>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <span class="ti-check"></span>
                                @lang('Submit')
                        </button>
                    </div>
                    

                </div>
                {{ Form::close() }}
            </div>
        </div>
        @endif

        @if($dealreturn->return==0 && (session('logged_session_data.designation_id')==35 || (App\SysHelper::is_return_approval_access() && $dealreturn->return==0)))
        <div class="card p-4 mb-4">
            <h2 class="page-heading mb-3">Sales Update</h2>
            <div class="border p-4">
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-return-sales','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-leads-form']) }}
                
                <div class="row">
                    <div class="col-lg-4 mb-10">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Sales Return No')<span></span></label><br />
                            <div class="form-group">
                                <input type="text" class="form-control" id="sales_ret_no" name="sales_ret_no" required />
                              </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-10">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('Amount')<span></span></label><br />
        
                            <div class="form-group">
                                <input type="number" class="form-control" id="amount" name="amount" />
                              </div>
                        </div>
                    </div>
                    <div class="col-lg-4 mb-10">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('VAT Amount')<span></span></label><br />
        
                            <div class="form-group">
                                <input type="number" class="form-control" id="amountvat" name="amountvat" />
                              </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-8 mb-10">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                                    <textarea class="dynamicstxt_s w-100 form-control" style="height: 100px !important" name="remarks" rows="4" autocomplete="off" id="remarks" placeholder="Remarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 mb-3"><br /><br /><br /><br />
                        <input type="hidden" id="ret_id" name="ret_id" value="{{ $dealreturn->id }}"/>
                        <input type="hidden" id="deal_id" name="deal_id" value="{{ $dealreturn->deal_id }}"/>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <span class="ti-check"></span>
                                @lang('Submit')
                        </button>
                    </div>
                    

                </div>
                {{ Form::close() }}
            </div>
        </div>
        @endif

        @if($dealreturn->payable==0 && (session('logged_session_data.designation_id')==2 || (App\SysHelper::is_return_approval_access() && $dealreturn->payable==0)))
        <div class="card p-4 mb-4">
            <h2 class="page-heading mb-3">Payable Update</h2>
            <div class="border p-4">
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-return-payable','method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'crm-leads-form']) }}
                
                <div class="row">
                    <div class="col-lg-4 mb-10">
                        <div class="input-effect">
                            <label class="txtlbl">@lang('mode_of_pay')<span></span></label><br />
                            <div class="form-group">
                                <select class="form-control" id="mode_of_pay" name="mode_of_pay" required>
                                    <option value="" selected>-Select Time-</option>
                                    <option value="1">Adjust against next order</option>
                                    <option value="2">Paid Off</option>
                                  </select>
                              </div>
                        </div>
                    </div>                    
                    <div class="col-lg-5 mb-10">
                        <div class="no-gutters input-right-icon">
                            <div class="col">
                                <div class="input-effect">
                                    <label class="txtlbl">@lang('Remarks')<span></span></label>
                                    <textarea class="dynamicstxt_s w-100 form-control" style="height: 100px !important" name="remarks" rows="4" autocomplete="off" id="remarks" placeholder="Remarks"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 mb-3"><br /><br /><br /><br />
                        <input type="hidden" id="ret_id" name="ret_id" value="{{ $dealreturn->id }}"/>
                        <input type="hidden" id="deal_id" name="deal_id" value="{{ $dealreturn->deal_id }}"/>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <span class="ti-check"></span>
                                @lang('Submit')
                        </button>
                    </div>
                    

                </div>
                {{ Form::close() }}
            </div>
        </div>
        @endif

    </div>

    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
    


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
</script>

@endsection
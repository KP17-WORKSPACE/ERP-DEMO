@extends('backEnd.masterpage')
@section('mainContent')
    @php
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    
<?php try { ?>

    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="mb-3">
                <h2 class="page-heading m-0">
                    @if(@isset($deal))
                        {{ $deal->deal_name }}
                    @else
                        {{ $service->subject }}
                    @endif
                </h2>
                <span class="page-label">Home - Pre-Sales Support</span>
            </div>
            <div>
                <a href="{{ url('crm-deal-service-list') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> Pre-Sales Support List</a>
            </div>
        </div>


        @if(@isset($deal))
        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100 bg-1">
                    <h2 class="head">Deal Info : {{ $deal->id }} {!! App\SysHelper::deal_type_new($deal->isproject) !!}
                    </h2>
                    <p class="mb-2 text-white-100 text-uppercase">{{ $deal->customername->name }}</p>
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
                        @endif
                        @if($deal->stage==5) <span class="btn-danger btn-badge py-1 px-2">Lost</span> @endif
                        @if($deal->stage==6) <span class="btn-dark btn-badge py-1 px-2">Cancelled</span> @endif
    
                        

                    </b>
                    

                         </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Owner Info</h2>
                    <h6 class="sub-head text-capitalize text-dark">{{ $deal->ownername->first_name }} {{ $deal->ownername->middle_name }} {{ $deal->ownername->last_name }}</h6>
                    <p class="mb-2 text-gray-800">Added On : {{ date('d/m/Y H:i:s', strtotime(@$deal->created_at)) }} @if ($deal->source != '') | Source : {{ $deal->source }}
                        @if ($deal->source_o != '') - {{ $deal->source_o }} @endif @endif
                </p>
                    <span class="mb-1"> <span class="font-semibold">Mob No :</span> {{ $deal->ownername->mobile }}</span>
                    <span class="mb-1"><span class="font-semibold">Mail :</span> {{ $deal->ownername->email }}</span>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="p-4 card h-100">
                    <h2 class="head">Customer Info </h2>
                    <h6 class="sub-head text-capitalize text-dark">{{ $deal->customername->name }}</h6>
                    <p class="mb-2 text-gray-800">{{ $deal->customername->address }}</p>                    
                    <span class="mb-1"> <span class="font-semibold">Contact :</span> {{ $deal->cust_name }}</span>
                    <span class="mb-1"><span class="font-semibold">Mob No :</span> {{ $deal->cust_no }} | <span class="font-semibold">Mail :</span> {{ $deal->cust_email }}</span>
                </div>
            </div>
        </div>
        @endif


        <div class="row">            
            <div class="col-lg-6 ">
                <div class="p-3 card @if ($service=='') mb-3 @endif @if($service !='') bg-3 @endif">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="page-heading">Pre-Sales Support Detail</h2>
                        @if ($service=='')
                        <button class="btn-small bg-info" data-toggle="modal" data-target="#ModalService">Add to Service</button>@endif
                    </div>
                </div>
                @if ($service != '')     
                <div class="pl-3 pr-3 pb-3 pt-2 card mb-3 ">
                    <h5 class="sub-head m-0"></h5>
                    Comments:-<br />
                    <span class="py-1 px-3 font-weight-bold">{{ $service->comments }}</span>
                </div>
                @endif

                @if($service->part_number != "")
                <div class="p-3 card mb-3">
                    <h5 class="sub-head m-0">Tags : 
                    <?php $myArray = explode(',', $service->part_number); ?>
                    @foreach ($myArray as $item)
                    <span class="btn-primary btn-badge py-1 px-3 font-weight-bold">{{ $item }}</span>
                    @endforeach
                    </h5>
                </div>
                @endif
                
                @if (Auth::user()->id==33)
                <div class="p-3 card @if (count($service_assign)==0) mb-3 @endif">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="page-heading">Service Assign</h2>
                        <button class="btn-small" data-toggle="modal" data-target="#ModalCollaboration">Assign User</button>
                    </div>
                </div>
                @endif

                @if (count($service_assign)>0)     
                <div class="pl-3 pr-3 pb-3 pt-0 card mb-3">
                    <h5 class="sub-head m-0">
                    @foreach ($service_assign as $val)
                    <div class="text-danger py-1 px-3 pb-2 font-weight-bold">{{ $val->userid->full_name }} <span class="text-dark font-weight-normal">- Assigned On {{date('d/m/Y h:i A', strtotime($val->created_at))}}</span></div>
                    @endforeach
                        </h5>
                </div>
                @endif
                
                <div class="p-3 card @if ($service=='') mb-3 @endif @if($service !='') bg-2 @endif">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="page-heading">Pre-Sales Support Status</h2>
                    </div>

                    @if(Auth::user()->role_id == 1 || session('logged_session_data.department_id')==3 || Auth::user()->id==20)
                    @if(@isset($comments))
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-service-comments-update', 'method' => 'POST', 'id' => 'crm-deals-comments-edit']) }}
                    @else
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-service-comments', 'method' => 'POST', 'id' => 'crm-deals-comments-add']) }}
                    @endif
                        <textarea name="comments" class="form-control" id="comments" cols="10" rows="3">@if(@isset($comments)) {{ $comments->comments }} @endif</textarea>
                        <input type="hidden" id="commentsid" name="commentsid" value="{{ $service->id }}" />
                        <div class="mt-2 justify-content-end d-flex">
                            <input type="file" class="form-control mr-5" name="commentsdoc" id="commentsdoc">

                            <select name="status" id="status" class="form-control mr-5">
                                <option value="2" @if(@isset($comments)) @if($comments->status==2) selected @endif @endif>Open</option>
                                <option value="3" @if(@isset($comments)) @if($comments->status==3) selected @endif @endif>Close</option>
                            </select>

                            @if(@isset($comments))
                            <input type="hidden" name="id" value="{{ $comments->id }}" />
                            <input type="hidden" name="doc" value="{{ $comments->commentsdoc }}" />
                            @endif


                            <button type="submit" class=" btn-small">Update</button>
                        </div>
                        <div class="mt-2 justify-content-end d-flex">
                        </div>                        
                        {{ Form::close() }}
                    @endif

                    </div>
                @if (count($service_comments) > 0)
                <div class="pl-3 pr-3 pb-3 pt-2 card mb-3 ">
                    @foreach ($service_comments as $val)                    
                    <p class="mb-0">{!! $val->comments !!}
                        @if ($val->commentsdoc!="")<br /><br />
                            <a class="btn-xs btn-purple p-0" href="{{asset('public/uploads/crm_deal_doc/')}}/{{ $val->commentsdoc }}" target="_blank">&nbsp;&nbsp;<i class="fa fa-paperclip" aria-hidden="true"></i>&nbsp;&nbsp;View File&nbsp;&nbsp;</a>
                        @endif
                    </p>
                    <p class="text-muted text-right">{{ $val->createdby->first_name }} {{ $val->createdby->last_name }}, On {{date('d/m/Y h:i A', strtotime($val->created_at))}}
                        @if ($val->status==3)
                        <span class="btn-success pl-1 pr-1">Close</span>
                        @else
                        <span class="btn-warning pl-1 pr-1">Open</span>
                        @endif
                    </p>
                    
                    @if ($val->created_by == Auth::user()->id)
                    {{--  <a class="text-right" href="{{url('crm-deal-service/'.$val->service_id.'/view/'.$val->id.'')}}">Edit</a>  --}}
                    @endif
                    <hr />
                    @endforeach
                </div>
                @endif
            </div>


        @if (count($quoteitems) > 0)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center py-4">
                        <h4 class="header-title m-0">Quote items</h4>

                    @if(Auth::user()->role_id == 1 || Auth::user()->id==33 || Auth::user()->id==20)

                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-quote/' . $deal->id . '/download/'.$deal->quote_id, 'method' => 'POST', 'id' => 'crm-deals-comments-add']) }}
                                            
                    <input class="" type="checkbox" value="1" id="flexCheckDefault1" name="with_partnumber"> <label class="pr-3" for="flexCheckDefault1"> With Part No </label>

                    <input class="" type="checkbox" value="1" id="flexCheckDefault2" name="without_vat"> <label class="pr-3" for="flexCheckDefault2"> Exclude VAT </label>

                    <input class="" type="checkbox" value="1" id="flexCheckDefault3" name="without_total"> <label class="pr-3" for="flexCheckDefault3"> Without Total </label>

                    <button class="btn btn-info p-0 pl-2 pr-2 mr-3"><i class="fa fa-download" aria-hidden="true"></i> Download</button>
                    {{ Form::close() }}

                    @endif

                    
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive table-bordered">
                            <table class="table table-nowrap table-centered mb-0 table-striped">
                                <thead>
                                    <tr>
                                        <th>Part Number</th>
                                        <th>Description</th>
                                        <th>Qty</th>
                                        @if(Auth::user()->role_id == 1 || Auth::user()->id==33 || Auth::user()->id==20)
                                        <th class="text-right">Price</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Total</th>
                                        @endif
                                    </tr>
                                </thead>                                
                            <?php $t_qty = 0; $t_price = 0; $t_discount = 0; $t_net_amount = 0;  $net_vat=App\SysHelper::get_vat($quoteitems[0]->currency_id);?>
                                <tbody>
                                    @foreach ($quoteitems as $Item)
                                    <tr>
                                        <td><?php try{ ?> {{ $Item->part_number }} <?php }catch (\Exception $e){} ?></td>
                                        <td>{!! nl2br($Item->description) !!}</td>
                                        <td>{{ $Item->qty }}</td>
                                        @if(Auth::user()->role_id == 1 || Auth::user()->id==33 || Auth::user()->id==20)
                                        <td class="text-right">{{ App\SysHelper::currancy_format($Item->price,$Item->currency_id) }}</td>
                                        <td class="text-right">{{ App\SysHelper::currancy_format($Item->discount,$Item->currency_id) }}</td>
                                        
                                        <td class="text-right">{{ App\SysHelper::currancy_format(($Item->price * $Item->qty - $Item->discount * $Item->qty),$Item->currency_id) }}</td>
                                        @endif
                                    </tr>
                                    <?php $t_qty += $Item->qty;
                                    $t_price += $Item->price * $Item->qty;
                                    $t_discount += $Item->discount * $Item->qty;
                                    $t_net_amount += $Item->price * $Item->qty - $Item->discount * $Item->qty;
                                    $currency_id = $Item->currency_id;
                                    ?>
                                    @endforeach
                                </tbody>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>{{ $t_qty }} <?php $t_discount += $deal->deal_discount;?></th>
                                        @if(Auth::user()->role_id == 1 || Auth::user()->id==33 || Auth::user()->id==20)
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_price,$currency_id) }}</th>
                                        <th class="text-right">{{ App\SysHelper::currancy_format($t_discount,$currency_id) }}</th>
                                        <th class="text-right"><?php $vat = ($t_price * $net_vat) / 100 - ($t_discount * $net_vat) / 100; ?>
                                            {{ App\SysHelper::currancy_format(($t_price - $t_discount + $vat), $currency_id) }} {{ $Item->currency->code }}
                                            {{--  @if($vat!='0.00')
                                            <br /> {{ App\SysHelper::currancy_format($vat,$currency_id) }} VAT</th>
                                            @endif  --}}
                                        @endif
                                    </tr>
                                </thead>
                            </table>
                        </div> <!-- end table-responsive-->

                    </div> <!-- end card-body-->
                </div> <!-- end card-->

            </div>
        </div>
        @endif

    </div>
    

    <!-- Modal Collaboration-->
    <div class="modal fade" id="ModalCollaboration" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Assign Service</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-service-assign', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                
                @if(@isset($deal))
                    <input type="hidden" name="service_deal_id" value="{{ $deal->id }}" />
                @else
                    <input type="hidden" name="service_deal_id" value="0" />
                @endif
                
                <input type="hidden" name="service_id" value="{{ $service->id }}" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Select Users</label>
                                <select class="form-control js-example-basic-single" name="user_id[]" id="user_id" multiple>
                                    <option value="">Select</option>
                                    @foreach ($staff as $value)
                                        <option value="{{ @$value->user_id }}"
                                            @if (isset($service_assign)) @foreach ($service_assign as $coll)
                                        @if ($coll->user_id == $value->user_id) selected @endif
                                            @endforeach
                                    @endif >{{ @$value->full_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Service</button>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add Service</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-deal-service', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                <input type="hidden" name="service_deal_id" value="{{ $deal->id }}" />
                <input type="hidden" name="service_cust_id" value="{{ $deal->cust_id }}" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="" class="form-label">Description</label>
                                <textarea class="form-control" name="comments" id="comments" rows="10"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add to Service</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <!-- Modal Service-->

<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    
@endsection

@extends('backEnd.master')
@section('mainContent')
<?php 
$modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}
 
    $modules = array_unique(@$modules);
    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol;
?>
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.product_receive') @lang('lang.list')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.inventory')</a>
                <a href="#">@lang('lang.product_receive') @lang('lang.list')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-lg-8 col-md-6">
                
            </div>
            @if(in_array(166, @$module_links) ||  Auth::user()->role_id == 1)
            <div class="col-lg-4 text-md-right text-left col-md-6 mb-30-lg">
                <a href="{{url('item-receive')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('lang.new') @lang('lang.product_receive')
                </a>
            </div>
            @endif

        </div>

 <div class="row mt-40">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-4 no-gutters">
                    <div class="main-title">
                        <h3 class="mb-0">@lang('lang.product_receive') @lang('lang.list')</h3>
                    </div>
                </div>
            </div>

         <div class="row">
                <div class="col-lg-12">
                    <table id="table_id" class="display school-table" cellspacing="0" width="100%">
                        <thead>
                            @if(session()->has('message-success') != "" ||
                                session()->get('message-danger') != "")
                                <tr>
                                    <td colspan="9">
                                         @if(session()->has('message-success'))
                                          <div class="alert alert-success">
                                              {{ session()->get('message-success') }}
                                          </div>
                                        @elseif(session()->has('message-danger'))
                                          <div class="alert alert-danger">
                                              {{ session()->get('message-danger') }}
                                          </div>
                                        @endif
                                    </td>
                                </tr>
                                 @endif
                            <tr>
                          <th> @lang('lang.product_name') </th>
                          <th> @lang('lang.supplier_name') </th>
                          <th> @lang('lang.received_date') </th>
                          <th> @lang('lang.denomination') </th>
                          <th> @lang('lang.quantity') </th>
                          <th>@lang('lang.unit_price') ({{ @$currency_symbol}})</th>
                          <th>sale price ({{ @$currency_symbol}})</th>
                          <th>@lang('lang.action')</th>
                      </tr>
                        </thead>

                        <tbody>
                            @foreach($allItemReceiveLists as $value)
                            {{-- {{dd($value)}} --}}
                            <tr>
                                <td>{{App\SmItem::getItemName(@$value->product_id)}}</td>
                               
                                <td>{{@$value->supplier !=""? @$value->supplier->company_name:""}}</td>
                                <td>{{ date('jS M, Y', strtotime(@$value->received_date)) }}</td>
                                <td>{{@$value->denomination}}</td>
                                <td>{{@$value->qnt}}</td>
                                <td>{{App\User::NumberToBangladeshiTakaFormat(  @$value->unit_price )}}</td>
                                <td>{{App\User::NumberToBangladeshiTakaFormat(  @$value->sale_price )}}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                            @lang('lang.select')
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if(in_array(167, @$module_links) ||  Auth::user()->role_id == 1)
                                            <a class="deleteUrl dropdown-item" data-modal-size="modal-lg" title="Product Edit" href="{{url('item-receive/edit', @$value->id)}}">@lang('lang.edit')</a>
                                            @endif
                                            @if(in_array(168, @$module_links) ||  Auth::user()->role_id == 1)
                                            
                                            <a class="dropdown-item" data-toggle="modal" data-target="#deleteProductModal{{@$value->id}}"  href="#">@lang('lang.delete')</a>
                                            @endif
                                     
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <div class="modal fade admin-query" id="deleteProductModal{{$value->id}}" >
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">@lang('lang.delete') @lang('lang.product')</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                </div>

                                                <div class="mt-40 d-flex justify-content-between">
                                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>
                                                    <a href="{{url('item-receive/delete', [@$value->id])}}" class="text-light">
                                                    <button class="primary-btn fix-gr-bg" type="submit">@lang('lang.delete')</button>
                                                     </a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
@endsection

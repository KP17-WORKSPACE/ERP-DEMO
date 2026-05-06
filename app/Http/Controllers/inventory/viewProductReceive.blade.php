@extends('backEnd.master')
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.product_receive') @lang('lang.details')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.inventory')</a>
                <a href="{{url('item-receive-list')}}">@lang('lang.product_receive') @lang('lang.list')</a>
                <a href="#">@lang('lang.product_receive') @lang('lang.details')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">

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
                            <tr>
                              <th> @lang('lang.part_number') </th>
                              <th> @lang('lang.product_name') </th>
                              <th> @lang('lang.supplier') </th>
                              <th> @lang('lang.denomination') </th>
                              <th> @lang('lang.price') </th>
                          </tr>
                        </thead>

                        <tbody>
                            @foreach($product_part_numbers as $value)
                            <tr>
                                <td>{{@$value->part_number}}</td>
                                <td>{{@$value->productReceivedDetail != ""? @$value->productReceivedDetail->product->item_name:""}}</td>
                                <td>{{@$value->productReceivedDetail !=""? @$value->productReceivedDetail->supplier->company_name:""}}</td>
                                
                                <td>{{@$value->productReceivedDetail !=""? @$value->productReceivedDetail->denomination:""}}</td>
                                <td>{{@$value->productReceivedDetail !=""? number_format( (float) @$value->productReceivedDetail->unit_price, 2, '.', ''):""}}</td>

                            </tr>
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

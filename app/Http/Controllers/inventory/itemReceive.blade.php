@extends('backEnd.master')
@section('mainContent')
<link href="{{asset('public/css/edit_item.css')}}" type="text/css" rel="stylesheet">
@php



    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol; 

@endphp
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.product_receive')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.inventory')</a>
                <a href="#">@lang('lang.product_receive')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
       
       {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-receive/store',
       'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'item-receive-form']) }}

       <div class="row">

        <div class="col-lg-3">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-title">
                        <h3 class="mb-30">
                            @lang('lang.New_Product_Details')
                        </h3>
                    </div>

                    <div class="white-box">
                        <div class="add-visitor">
                            <div class="row">
                                <div class="col-lg-12 mb-30">
                                    <div id="errorMessage1">
                                        <div id="supplierError" class="text-danger"></div>
                                        <div id="storeError" class="text-danger"></div>                     
                                    </div>
                                    <div class="input-effect">
                                        <select class="niceSelect w-100 bb form-control{{ $errors->has('supplier_id') ? ' is-invalid' : '' }}" name="supplier" id="supplier_id">
                                            <option data-display=" @lang('lang.select_supplier') *" value=""> @lang('lang.select')</option>
                                            @foreach($suppliers as $key=>$value)
                                                <option value="{{@$value->id}}">{{@$value->company_name}}</option>
                                            @endforeach
                                            </select>
                                            <span class="focus-border"></span>
                                        </div>
                                    </div>

                                        <div class="col-lg-12 no-gutters input-right-icon mb-30">
                                            <div class="col">
                                                <div class="input-effect">
                                                    <input class="primary-input date form-control{{ $errors->has('from_date') ? ' is-invalid' : '' }}"  id="receive_date" type="text"
                                                    name="received_date" value="{{isset($editData)? date('m/d/Y', strtotime(@$editData->receive_date)): date('m/d/Y')}}" autocomplete="off">
                                                    <label>@lang('lang.receive_date') <span></span> </label>
                                                    <span class="focus-border"></span>
                                                    @if ($errors->has('receive_date'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('receive_date') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>

                                            </div>
                                            <div class="col-auto">
                                                <button class="" type="button">
                                                    <i class="ti-calendar" id="receive_date_icon"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mb-20">
                                            <div class="input-effect">
                                                <textarea class="primary-input form-control" cols="0" rows="4" name="description" id="description">{{isset($editData) ? @$editData->description : ''}}</textarea>
                                                <label>@lang('lang.description') <span></span> </label>
                                                <span class="focus-border textarea"></span>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-9">
              <div class="row">
                <div class="col-lg-4 no-gutters">
                    <div class="main-title">
                        <h3 class="mb-30">@lang('lang.item_receive')</h3>
                    </div>
                </div>

               
                <div class="col-lg-6 text-md-right text-left col-md-6 mb-30-lg">
                        <a href="{{ url('/item-receive-list') }}" class="primary-btn small fix-gr-bg">
                           
                            @lang('lang.product_receive') @lang('lang.list')
                        </a>
                    </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
               <div class="white-box">
                    <div id="errorMessage2">
                        <div id="itemError" class="text-danger"></div>
                        <div id="quantityError" class="text-danger"></div> 
                        <div id="priceError" class="text-danger"></div>
                                            
                    </div>
                   <table class="table" id="productTable">
                    <thead>
                        @if(session()->has('message-success') != "" ||
                                session()->get('message-danger') != "")
                      <tr>
                          <th colspan="7"> 
                            @if(session()->has('message-success'))
                              <div class="alert alert-success">
                                  {{ session()->get('message-success') }}
                              </div>
                            @elseif(session()->has('message-danger'))
                              <div class="alert alert-danger">
                                  {{ session()->get('message-danger') }}
                              </div>
                            @endif
                          </th>
                      </tr>
                      @endif

                      <tr>
                          <th  class="inventory_product_table_style"> @lang('lang.product_name') </th>
                          <th> @lang('lang.Part')<br> @lang('lang.number') </th>
                          <th> @lang('lang.new') @lang('lang.Part')<br> @lang('lang.number') </th>
                          <th  class="inventory_product_table_style"> @lang('lang.denomination') </th>
                          <th>@lang('lang.unit_price') ({{ @$currency_symbol}})</th>
                          <th> @lang('lang.quantity') </th>
                          <th>@lang('lang.total') @lang('lang.price') ({{ @$currency_symbol}})</th>
                          <th>sale @lang('lang.price') ({{ @$currency_symbol}})</th>
                          {{-- <th>@lang('lang.action')</th> --}}
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                        <td class="border-top-0">
                            <input type="hidden" name="url" id="url" value="{{URL::to('/')}}"> 
                            <div class="input-effect">
                                <select class="niceSelect w-100 bb form-control" name="products[]" id="productName">
                                    <option data-display="Select product " value="">Select</option>
                                    @foreach($items as $key=>$value)
                                    <option value="{{@$value->id}}"
                                        @if(isset($editData))
                                        @if(@$editData->category_name == @$value->id)
                                            @lang('lang.selected')
                                        @endif
                                        @endif
                                        >{{@$value->item_name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="focus-border"></span>
                                </div>
                            </td>
                            <td class="border-top-0">
                                <div class="input-effect">
                                    <input class="primary-input form-control"
                                    type="text" step="any" id="part_number" name="part_number[]" autocomplete="off">
                                    <span class="focus-border"></span>
                                </div>
                            </td>
                            <td class="border-top-0">
                                <div class="input-effect">
                                    <input class="primary-input form-control"
                                    type="text" step="any" id="new_part_number" name="new_part_number[]" autocomplete="off">
                                    <span class="focus-border"></span>
                                </div>
                            </td>
                            <td class="border-top-0">
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}"> 

                                <div class="input-effect">
                                    <select class="niceSelect w-100 bb form-control" name="denomination[]" id="denomination">
                                        <option data-display="Select Denomination " value="">@lang('lang.select') @lang('lang.denomination')</option>
                                        @php 
                                         $lists = ['Meter (m)','Centimeter (cm)','Millimeter (mm)','Foot (ft)','Inch (in)','Yard (yd)','Kilogram (kg)','Gram(gm)','Milligram (mg)','Pound (lb)','Ounce (oz)','Liter (l)','Milliliter (ml)','Gallon (gal)','Piece (pcs)', 'Unit (u)'];
                                         // $shortForm = ['(m)','(cm)','(mm)','(ft)','(in)','(yd)','(kg)','(gm)','(mg)','(lb)','(oz)','(l)','(ml)','(gal)','(pcs)', '(unit)'];
                                         $countList =0;
                                        @endphp
                                         @foreach($lists as $list)
                                        <option value="{{@$list}}">{{@$list}}</option>
                                        @endforeach
                                    </select>
                                    <span class="focus-border"></span>
                                </div>
                            </td>

                            <td class="border-top-0">
                                <div class="input-effect">
                                    <input class="primary-input form-control"
                                    type="number" step="any" id="unit_price1" oninput="getTotalPrice()" name="unit_price[]" autocomplete="off" placeholder="0.00" value=''>
                                    <span class="focus-border"></span>
                                </div>
                            </td>
                            <td class="border-top-0">
                                <div class="input-effect">
                                    <input class="primary-input form-control"
                                    type="number" step="any" id="quantity1" oninput="getTotalPrice()" name="quantity[]" autocomplete="off">
                                    <span class="focus-border"></span>
                                    
                                </div>
                            </td>
                            <td class="border-top-0">
                                <div class="input-effect">
                                    <input class="primary-input form-control"
                                    type="text" id="total_price" name="total_price[]" autocomplete="off" value="0.00" readonly="">
                                    <span class="focus-border"></span>
                                </div>
                            </td>
                            <td class="border-top-0">
                                <div class="input-effect">
                                    <input class="primary-input form-control"
                                    type="number" id="sale_price" name="sale_price[]" autocomplete="off" placeholder="0.00">
                                    <span class="focus-border"></span>
                                </div>
                            </td>
                            <script>
                                function getTotalPrice() {
                                    var unit_price=document.getElementById('unit_price1').value;
                                    var quantity=document.getElementById('quantity1').value;
                                    var total_price=document.getElementById('total_price');
                                    
                                    var calculated_price=unit_price*quantity;
                                    total_price.value=calculated_price
                                    // alert(calculated_price);
                                }
                            </script>
                            {{-- <td>
                                 <button class="primary-btn icon-only fix-gr-bg" type="button">
                                     <span class="ti-trash"></span>
                                </button>
                               
                            </td> --}}
                        </tr>
                        <tfoot>
                            <tr>
                               <th class="border-top-0 text-center" colspan="9">
                                   <button class="primary-btn fix-gr-bg" type="submit"> 
                                        <span class="ti-check"></span>
                                         @lang('lang.save')
                                    </button>

                               </th>
                           </tr>
                       </tfoot>

                   </tbody>
               </table>
           </div>
       </div>
   </div>
</div>
{{ Form::close() }}
</div>
</section>
@endsection

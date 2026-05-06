<script src="{{asset('public/backEnd/')}}/js/main.js"></script>
<script src="{{asset('public/backEnd/')}}/js/custom.js"></script>

{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'item-receive/update', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
<input type="hidden" value="{{@$product->id}}" name="id">      
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-6">
                    <div class="alert alert-danger" id="errorMessage1">
                    <div id="supplierError"></div>
                    <div id="storeError"></div>                     
                </div>
                <div class="input-effect">
                    <select class="niceSelect w-100 bb form-control{{ $errors->has('supplier_id') ? ' is-invalid' : '' }}" name="supplier" id="supplier_id">
                        <option data-display=" @lang('lang.select_supplier') *" value=""> @lang('lang.select')</option>
                        @foreach($suppliers as $key=>$value)
                            <option value="{{@$value->id}}" {{@$product->supplier_id == @$value->id? 'selected':''}}>{{@$value->company_name}}</option>
                        @endforeach
                        </select>
                        <span class="focus-border"></span>
                    </div>
                </div>
            
                <div class="col-lg-6 no-gutters input-right-icon mb-30">
                    <div class="col">
                        <div class="input-effect">
                            <input class="primary-input date form-control{{ $errors->has('from_date') ? ' is-invalid' : '' }}"  id="receive_date" type="text"
                            name="received_date" value="{{ @$product->receive_date != ""? date('m/d/Y', strtotime(@$product->receive_date)): date('m/d/Y', strtotime(date('Y-m-d')))}}" autocomplete="off">
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
            </div>
            <div class="row">

                <div class="col-lg-12 mb-20">
                    <div class="input-effect">
                        <textarea class="primary-input form-control" cols="0" rows="4" name="description" id="description">{{@$product->description}}</textarea>
                        <label>@lang('lang.description') <span></span> </label>
                        <span class="focus-border textarea"></span>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="input-effect">
                        <select class="niceSelect w-100 bb form-control" name="product" id="productName1">
                            <option data-display="Select product " value="">Select</option>
                            @foreach($items as $key=>$value)
                            <option value="{{@$value->id}}" {{@$product->product_id == @$value->id? 'selected':''}}>{{@$value->item_name}}</option>
                                @endforeach
                            </select>
                            <span class="focus-border"></span>
                        </div>
                </div>
                <div class="col-lg-6">
                    <div class="input-effect">
                        <input class="primary-input form-control"
                        type="text" id="unit_price1" name="part_number" autocomplete="off" value="{{@$product->part_number}}">
                        <label>@lang('lang.part_number')</label>

                        <span class="focus-border"></span>
                    </div>
                </div>
            </div>
            <div class="row mt-25">
                <div class="col-lg-6">
                    <div class="input-effect">
                        <input class="primary-input form-control"
                        type="text" id="new_part_number" name="new_part_number" autocomplete="off" value="{{@$product->new_part_number}}">
                        <label>@lang('lang.new_part_number')</label>
                        <span class="focus-border"></span>
                    </div>
                </div>
           
                <div class="col-lg-6">
                    <input type="hidden" name="url" id="url" value="{{URL::to('/')}}"> 

                    <div class="input-effect">
                        <select class="niceSelect w-100 bb form-control" name="denomination" id="denomination">
                            <option data-display="Select denomination " value="">@lang('lang.select') @lang('lang.denomination')</option>
                            <option value="Meter(m)" {{@$product->denomination == "Meter(m)"? 'selected':''}}>@lang('lang.meter')({{ __('m') }})</option>
                            <option value="Centimeter(cm)" {{@$product->denomination == "Centimeter(cm)"? 'selected':''}}>@lang('lang.Centimeter')({{ __('cm') }})</option>
                            <option value="Millimeter(mm)" {{@$product->denomination == "Millimeter(mm)"? 'selected':''}}>@lang('lang.Millimeter')({{ __('mm') }})</option>
                            <option value="Kilogram(kg)" {{@$product->denomination == "Kilogram(kg)"? 'selected':''}}>@lang('lang.Kilogram')({{ __('kg') }})</option>
                        </select>
                        <span class="focus-border"></span>
                    </div>
                </div>
            </div>
            <div class="row mt-25">
                <div class="col-lg-6">
                    <div class="input-effect">
                        <input class="primary-input form-control"
                        type="number" id="quantity" name="quantity" oninput="getTotalPrice()" autocomplete="off" value="{{@$product->qnt}}">
                        <label>@lang('lang.quantity')</label>
                        <span class="focus-border"></span>
                        
                    </div>
                </div>
            
                <div class="col-lg-6">
                    <div class="input-effect">
                        <input class="primary-input form-control"
                        type="number" id="unit_price" name="unit_price" oninput="getTotalPrice()" autocomplete="off" value="{{@$product->unit_price}}">
                        <label>@lang('lang.unit_price')</label>
                        <span class="focus-border"></span>
                    </div>
                </div>
            </div>
            <div class="row mt-25">
           
                @php
                    $purchase_price=$product->unit_price*$product->qnt;
                @endphp
            <div class="col-lg-6">
                <div class="input-effect">
                    <input class="primary-input form-control"
                    type="number" id="total_price" name="total_price" autocomplete="off" value="{{@$purchase_price}}">
                    <label>@lang('lang.purchase') @lang('lang.price')</label>
                    <span class="focus-border"></span>
                </div>
            </div>
                <div class="col-lg-6">
                    <div class="input-effect">
                        <input class="primary-input form-control"
                        type="number" id="sale_price" name="sale_price" autocomplete="off" value="{{@$product->sale_price}}">
                        <label>Sale Price</label>
                        <span class="focus-border"></span>
                    </div>
                </div>
              
            </div>
        </div>
        <script>
            function getTotalPrice() {
                var unit_price=document.getElementById('unit_price').value;
                var quantity=document.getElementById('quantity').value;
                var total_price=document.getElementById('total_price');
                
                var calculated_price=unit_price*quantity;
                total_price.value=calculated_price
                // alert(calculated_price);
            }
        </script>
        <div class="col-lg-12 text-center mt-40">
            <div class="mt-40 d-flex justify-content-between">
                <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>

                <button class="primary-btn fix-gr-bg" id="save_button_query" type="submit">@lang('lang.save')</button>
            </div>
        </div>
    </div>
{{ Form::close() }}

@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp

    <?php try { ?>

        <div class="container-fluid mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="mb-3">
                    <h2 class="page-heading m-0">Create BOQ</h2>
                    <span class="page-label">Home - Deal - Create BOQ</span>
                </div>
                <div>
                    {{--  <a href="{{ url('crm-deals/'.$quotation->id.'/view') }}" type="button" class="btn btn-primary"><i class="fa fa-list"></i> View Deal {{ $quotation->id }}</a>  --}}
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title m-0 pb-0">Solution Information</h4>
                        </div>
                        <div class="card-body">
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'crm-boq-chooseitems', 'method' => 'POST', 'id' => 'crm-leads-search']) }}
                            <div class="row mt-0 mb-0 pt-0 pb-0">
                                <div class="form-group col-md-2">
                                    <label >No of Location</label>
                                </div>
                                <div class="form-group col-md-4">
                                    <select class="form-control nooflocation required" required name="nooflocation">
                                        <option value="">---- @lang('common.please_select') ----</option>
                                        @for ($i = 1; $i <= 25; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-0 mb-0 pt-0 pb-0">
                                <div class="form-group col-md-2">
                                    <label for="exampleInputEmail1">Connectivity Required</label>
                                </div>
                                <div class="form-group col-md-4">
                                    <select class="form-control connectivity required" required name="connectivity">
                                        <option value="">---- @lang('common.please_select') ----</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                            
                            <a class="addmoore_isp"></a>
                            <div class="card-body row ">
                                <div class="row_isp_div w-100"></div>
                            </div>

                            <button type="submit" class="btn btn-danger text-sm">@lang('Next') <i class="fa fa-angle-double-right"></i></button>
                                
                            {{ Form::close() }}

                            {{-- ------------------------------ --}}
                            <div style="display:none;">
                                <div class="row_isp">
                                    <div class="form-group col-md-2">
                                        <label for="exampleInputEmail1">ISP Telephone Type</label>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <select class="form-control telephonetype w-100" name="telephonetype[]" style="width: 100%;">
                                            <option value="">---- @lang('common.please_select') ----</option>
                                            <option value="Analog">Analog</option>
                                            <option value="PRI">PRI</option>
                                            <option value="SIP">SIP</option>
                                            <option value="BRI">BRI</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="number" placeholder="No of Lines" class="form-control nolines w-100" name="nolines[]" style="width: 100%;"/>
                                    </div>
                                </div>
                            </div>
                            {{-- ------------------------------ --}}

                        </div>                        
                    </div>
                </div>
            </div>
        </div>
        
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    

    <script>

        function add_tocart(id) {
            $("#loading_bg").css("display", "block");
            var qty = $("#txt_qty_"+id).val();
            var price = $("#txt_price_"+id).val();
            var description = $("#txt_description_"+id).val();

            if (qty == "" || qty <= 0) {
                alert("Please Add Qty");
                $("#txt_qty_"+id).focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            $("#txt_btn_"+id).attr('disabled', true);
    
            var action = "{{ URL::to('crm-quote-additems') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    qty: qty,
                    price: price,
                    description: description,
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

        function upd_tocart(id) {
            $("#loading_bg").css("display", "block");
            var qty = $("#txt_uqty_"+id).val();
            var price = $("#txt_uprice_"+id).val();
            var description = $("#txt_udescription_"+id).val();
            var discount = $("#txt_udiscount_"+id).val();

            if (qty == "" || qty <= 0) {
                alert("Please Add Qty");
                $("#txt_qty_"+id).focus();
                $("#loading_bg").css("display", "none");
                return false;
            }
            $("#txt_btn_"+id).attr('disabled', true);
    
            var action = "{{ URL::to('crm-quote-updateitems') }}";
            $.ajax({
                url: action,
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    qty: qty,
                    price: price,
                    description: description,
                    discount: discount,
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

        function del_tocart(id) {
            $("#loading_bg").css("display", "block");
            var btn = $("#del_btn_"+id).val();
            $(btn).attr('disabled', true);
    
            var action = "{{ URL::to('crm-quote-deleteitems') }}";
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






            jQuery(function() {
        
                $(document).on("click", ".addmoore_isp", function () {
                $('.row_isp_div').append('<div class="form-group row">' + $('.row_isp').html() + '</div>');});
        
                $(document).on("change", ".nooflocation", function () {
                    var nol = $(".nooflocation").val();
                    $('.row_isp_div').empty("");
                    for(i=1; i <= nol; i++)
                    {
                        $('.addmoore_isp').click();
                    }
                });
        
        
        
                
            });
        

    </script>
@endsection
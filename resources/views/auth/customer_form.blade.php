<?php
$setting = App\SmGeneralSettings::find(1);
if(isset($setting->copyright_text)){ $copyright_text = $setting->copyright_text; }else{ $copyright_text = 'Copyright © 2019 All rights reserved | This template is made with by Codethemes'; }
if(isset($setting->logo)) { $logo = $setting->logo; } else{ $logo = 'public/uploads/settings/logo.png'; }
if(isset($setting->favicon)) { $favicon = $setting->favicon; } else{ $favicon = 'public/backEnd/img/favicon.png'; }
$login_background = App\SmBackgroundSetting::where([['is_default',1],['title','Login Background']])->first(); 
if(empty($login_background)){ $css = "background: url(".url('public/backEnd/img/login-bg.jpg').")  no-repeat center; background-size: cover; ";}
else{ if(!empty($login_background->image)){  $css = "background: url('". url($login_background->image) ."')  no-repeat center;  background-size: cover;"; }else{ $css = "background:".$login_background->color; } } 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{asset($favicon)}}" type="image/png"/>
    <title>Customer Form</title>
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/themify-icons.css" />

	{{--  -----------------  --}}
	
    <link href="{{ asset('public/admin-iroid/') }}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('public/admin-iroid/') }}/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('public/admin-iroid/') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="{{ asset('public/admin-iroid/') }}/vendor/jquery/jquery.min.js"></script>    
    <link rel="stylesheet" href="{{asset('public/backEnd/')}}/vendors/css/toastr.min.css"/>


</head>
<body class="hight_100" style="background: #c2c2c2;">

	
    <!--================ Start Login Area =================-->
	

	<div class="row p-0 m-0">
		<div class="col-md-2">&nbsp;</div>
	<div class="col-md-8 mt-4 p-4" style="background: #ffffff;">
		<b style="font-size: 25px;">Customer Form</b>
		<img  src="{{asset(@$company->company_logo)}}" width="200px" align="right"/><br /><br />


		<hr>
			{{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'customer-form-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

			<input type="hidden" name="company_id" id="company_id" value="{{ $company_id }}">
			

		<input type="hidden" name="url" id="url" value="{{ URL::to('/') }}">

		<div class="row">
			<div class="col-md-2">
				<label for="" class="form-check-label">Customer Type</label></div>
				<div class="col-md-3">
				<select class="form-control js-example-basic-single" name="account_type" required>
					<option value="">-Select-</option>
					<option value="1">Reseller</option>
					<option value="2">Enduser</option>
					<option value="3">Ecommerce</option>
				</select>
			</div>
		</div>
		<div class="row mt-2">
			<div class="col-md-2">
				<label for="" class="form-check-label">Primary Contact</label></div>                            
			<div class="col-md-1">
				<select class="form-control js-example-basic-single" name="customer_salutation" required>
					<option value="">--</option>
					<option value="Mr">Mr</option>
					<option value="Mrs">Mrs</option>
					<option value="Miss">Miss</option>
				</select>
			</div>
			<div class="col-md-2">
				<input class="form-control" type="text" name="first_name" placeholder="First Name"
					value="{{ isset($editData) ? @$editData->name : old('name') }}" required>
				</select>
			</div>
			<div class="col-md-2">
				<input class="form-control" type="text" name="last_name" placeholder="Last Name"
					value="{{ isset($editData) ? @$editData->name : old('name') }}">
			</div>
		</div>
		<div class="row mt-2">
			<div class="col-md-2">
				<label for="" class="form-check-label">Company Name</label></div>
				<div class="col-md-3">
					<input class="form-control" type="text" name="customer_name" id="customer_name" placeholder="Company Name" required>
			</div>
			<div class="col-md-2">
				<select class="form-control js-example-basic-single" name="designation" required>
					<option value="">--Designation--</option>
					@if (count($designation)>0)
						@foreach ($designation as $val)
							<option value="{{ $val->title }}">{{ $val->title }}</option>
						@endforeach
					@endif
				</select>
			</div>
		</div>
		<script>
			$('#customer_name').on('input', function() {
				var txt = $('#customer_name').val();
				$('#customer_name_display').val(txt.toUpperCase());
				var txt2 = capitalizeFirstLetter(txt);
				$('#customer_name').val(txt2);
			});
			function capitalizeFirstLetter(string) {
				return string.charAt(0).toUpperCase() + string.slice(1);
			}                        
		</script>
		<div class="row mt-2">
			<div class="col-md-2">
				<label for="" class="form-check-label">Company Display Name</label></div>
			<div class="col-md-3">
					<input class="form-control" type="text" name="customer_name_display" id="customer_name_display" placeholder="Customer Display Name" required>
			</div>
			<div class="col-md-2" style="display: none;">Company Type
				<select class="form-control" name="type" id="type">
					<option value="1" @if (isset($editData)) @if (@$editData->type == 1) selected @endif @endif>Green</option>
				</select>
			</div>
		</div>
		<div class="row mt-2">
			<div class="col-md-2">
				<label for="" class="form-check-label">Company Email</label></div>
			<div class="col-md-3">
					<input class="form-control" type="text" name="email" placeholder="Email" required>
			</div>
		</div>
		<div class="row mt-2">
			<div class="col-md-2">
				<label for="" class="form-check-label">Company Phone</label></div>
				<div class="col-md-3">
					<input class="form-control" type="text" name="mobile_code" placeholder="Work Phone" required>
			</div>
			<div class="col-md-2">
					<input class="form-control" type="text" name="mobile" placeholder="Mobile">
			</div>
		</div>
		<div class="row mt-4">
			<div class="col-md-12">

{{--  tabs  --}}
<ul class="nav nav-tabs" id="myTab" role="tablist">
<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#address-panel" role="tab" aria-controls="address" aria-selected="true">Address</a></li>
<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#contactperson-panel" role="tab" aria-controls="contactperson" aria-selected="true">Contact Person</a></li>
<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#vat-panel" role="tab" aria-controls="vat" aria-selected="false">VAT</a></li>
<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#documents-panel" role="tab" aria-controls="documents" aria-selected="false">Documents</a></li>
</ul>


<div class="tab-content">
{{--  Address  --}}
<div class="tab-pane active pt-2" id="address-panel" role="tabpanel" aria-labelledby="address-tab">


<div class="row">
<div class="col-md-6">
	<p><b>Billing Address</b></p>
	<div class="row">
		<div class="col-md-3">Country</div>
		<div class="col-md-8"><select class="form-control js-example-basic-single" name="country" id="country" required>
			<option data-display="" value=""></option>
			@foreach ($countries as $key => $value)
				<option value="{{ @$value->id }}">{{ @$value->name }} </option>
			@endforeach
		</select></div>
	</div>
	<div class="row mt-2">
		<div class="col-md-3">Address 1</div>
		<div class="col-md-8"><input class="form-control" type="text" name="address" placeholder="" required></div>
	</div>
	<div class="row mt-2">
		<div class="col-md-3">Address 2</div>
		<div class="col-md-8"><input class="form-control" type="text" name="address2" placeholder="" required></div>
	</div>
	<div class="row mt-2">
		<div class="col-md-3">City</div>
		<div class="col-md-8"><input class="form-control" type="text" name="city" placeholder="" required></div>
	</div>
	<div class="row mt-2">
		<div class="col-md-3">State</div>
		<div class="col-md-8"><div id="sectionStateDiv">
			<select class="form-control" name="state" id="state">
				<option data-display="" value=""></option>
				<?php try { ?>
				@if (isset($editData) && $editData->vat_state != '')
					<option data-display="{{ $editData->vatstate->name }}"
						value="{{ $editData->vat_state }}" selected>
						{{ $editData->vatstate->name }}</option>
				@endif
				<?php }catch (\Exception $e) {   } ?>
			</select>
		</div></div>
	</div>
	<div class="row mt-2">
		<div class="col-md-3">PO Box</div>
		<div class="col-md-8"><input class="form-control" type="text" name="zip_code" placeholder=""></div>
	</div>
</div>
<div class="col-md-6"><p style="float: right;"><input type="checkbox" name="same_billing_address" id="same_billing_address" value="1"> Same as Billing Address</p>
	<script>
		$( "#same_billing_address" ).click(function() {
			if(this.checked){
				$('[name=address_ship]').val($('[name=address]').val());
				$('[name=address2_ship]').val($('[name=address2]').val());
				$('[name=city_ship]').val($('[name=city]').val());
				$('#select2-country_ship-container').html($('#country option:selected').text());
				$('#state_ship').append( new Option($('#state option:selected').text(),'0',true,true) );
				$('[name=zip_code_ship]').val($('[name=zip_code]').val());
				$('#country_ship').removeAttr('required');
				$('#address_ship').removeAttr('required');
				$('#address2_ship').removeAttr('required');
				$('#city_ship').removeAttr('required');
				$('#state_ship').removeAttr('required');
				$('#zip_code_ship').removeAttr('required');
			}
			if(!this.checked){
				$('[name=address_ship]').val('');
				$('[name=address2_ship]').val('');
				$('[name=city_ship]').val('');
				$('[name=country_ship]').val('');
				$('[name=state_ship]').val('');
				$('[name=zip_code_ship]').val('');
				$('#country_ship').attr('required');
				$('#address_ship').attr('required');
				$('#address2_ship').attr('required');
				$('#city_ship').attr('required');
				$('#state_ship').attr('required');
				$('#zip_code_ship').attr('required');
			}
		});                    
	</script>
	<p><b>Shipping Address</b></p>
	<div class="row">
		<div class="col-md-3">Country</div>
		<div class="col-md-8"><select class="form-control js-example-basic-single" name="country_ship" id="country_ship" required>
			<option data-display="" value=""></option>
			@foreach ($countries as $key => $value)
				<option value="{{ @$value->id }}">{{ @$value->name }} </option>
			@endforeach
		</select></div>
	</div>
	<div class="row mt-2">
		<div class="col-md-3">Address 1</div>
		<div class="col-md-8"><input class="form-control" type="text" name="address_ship" placeholder="" required></div>
	</div>
	<div class="row mt-2">
		<div class="col-md-3">Address 2</div>
		<div class="col-md-8"><input class="form-control" type="text" name="address2_ship" placeholder="" required></div>
	</div>
	<div class="row mt-2">
		<div class="col-md-3">City</div>
		<div class="col-md-8"><input class="form-control" type="text" name="city_ship" placeholder="" required></div>
	</div>
	<div class="row mt-2">
		<div class="col-md-3">State</div>
		<div class="col-md-8"><div id="sectionStateDiv_ship">
			<select class="form-control" name="state_ship" id="state_ship">
				<option data-display="" value=""></option>
				<?php try { ?>
				@if (isset($editData) && $editData->vat_state != '')
					<option data-display="{{ $editData->vatstate->name }}"
						value="{{ $editData->vat_state }}" selected>
						{{ $editData->vatstate->name }}</option>
				@endif
				<?php }catch (\Exception $e) {   } ?>
			</select>
		</div></div>
	</div>
	<div class="row mt-2">
		<div class="col-md-3">PO Box</div>
		<div class="col-md-8"><input class="form-control" type="text" name="zip_code_ship" placeholder=""></div>
	</div>            
</div>
</div>

</div>
{{--  Address  --}}
<div class="tab-pane pt-2" id="contactperson-panel" role="tabpanel" aria-labelledby="contactperson-tab">        
<div class="row">
<div class="col-md-12">
	<table class="table table-bordered table-striped" id="pi-ret-table" width="100%" cellspacing="0">
		<thead>
			<tr>
				<th>@lang('Salutation')</th>
				<th>@lang('First Name')</th>
				<th>@lang('Last Name')</th>
				<th>@lang('Email Address')</th>
				<th>@lang('Work Phone')</th>
				<th>@lang('Mobile')</th>
				<th>@lang('Designation')</th>
				<th>@lang('Department')</th>
			</tr>
		</thead>
		<tbody>
			<?php $i=1;?>
			@for ($r=1; $r <= 5; $r++)
			<tr id="pr_row_{{ $i }}">
				<td><select class="form-control js-example-basic-single" name="e_salutation[]" id="e_salutation_{{ $i }}">
					<option value="">-Salutation-</option>
					<option value="Mr">Mr</option>
					<option value="Mrs">Mrs</option>
					<option value="Miss">Miss</option>
				</select></td>
				<td><input type="text" class="form-control" name="e_first_name[]" id="e_first_name_{{ $i }}" value="" /></td>
				<td><input type="text" class="form-control" name="e_last_name[]" id="e_last_name_{{ $i }}" value="" /></td>
				<td><input type="text" class="form-control" name="e_email_address[]" id="e_email_address_{{ $i }}" value="" /></td>
				<td><input type="text" class="form-control" name="e_work_phone[]" id="e_work_phone_{{ $i }}" value="" /></td>
				<td><input type="text" class="form-control" name="e_mobile[]" id="e_mobile_{{ $i }}" value="" /></td>
				<td><select class="form-control js-example-basic-single" name="e_designation[]" id="e_designation_{{ $i }}">
					<option value="">--Designation--</option>
					@if (count($designation)>0)
						@foreach ($designation as $val)
							<option value="{{ $val->title }}">{{ $val->title }}</option>
						@endforeach
					@endif
				</select></td>
				<td><select class="form-control js-example-basic-single" name="e_department[]" id="e_department_{{ $i }}">
				<option value="">--Department--</option>
				@if (count($department)>0)
					@foreach ($department as $val)
						<option value="{{ $val->name }}">{{ $val->name }}</option>
					@endforeach
				@endif
			</select></td>
			</tr>
			<?php $i++;?>
			@endfor
			<input type="hidden" value="{{ $i-- }}" id="pr_row_count" />
		</tbody>
		
	</table>

	<script>
		$(document).ready(function () {
			// Sync function
			function syncPrimaryToFirstContact() {
				const fieldMap = [
					{ from: 'customer_salutation', to: 'e_salutation_1' },
					{ from: 'first_name', to: 'e_first_name_1' },
					{ from: 'last_name', to: 'e_last_name_1' },
					{ from: 'email', to: 'e_email_address_1' },
					{ from: 'mobile_code', to: 'e_work_phone_1' },
					{ from: 'mobile', to: 'e_mobile_1' },
					{ from: 'designation', to: 'e_designation_1' },
				];
		
				fieldMap.forEach(({ from, to }) => {
					const sourceVal = $(`[name="${from}"]`).val();
					const $target = $(`#${to}`);
					$target.val(sourceVal).trigger('change'); // Important for select2
				});
			}
		
			// Initial sync
			syncPrimaryToFirstContact();
		
			// Watch text inputs
			$('input[name="first_name"], input[name="last_name"], input[name="email"], input[name="mobile_code"], input[name="mobile"]').on('input', syncPrimaryToFirstContact);
		
			// Watch native select changes
			$('select[name="customer_salutation"], select[name="designation"]').on('change', syncPrimaryToFirstContact);
		
			// Watch Select2 specific events (in case they suppress native `change`)
			$('select[name="customer_salutation"], select[name="designation"]').on('select2:select', syncPrimaryToFirstContact);
		});
		</script>
		
		
</div>
</div>
</div>
<div class="tab-pane pt-2" id="vat-panel" role="tabpanel" aria-labelledby="vat-tab">
	<div class="row">
		<div class="col-md-6">
			<div class="row mt-2">
				<div class="col-md-3">VAT Country</div>
				<div class="col-md-8"><select class="form-control js-example-basic-single" name="country_vat" id="country_vat">
					@foreach ($vat as $key => $value)
							<option value="{{ @$value->vat_country }}">{{ @$value->name }} </option>
					@endforeach
				</select></div>
				<script>
					$(function () {
						$('country_vat option:first-child').attr("selected", "selected");
						$("#country_vat").change();
					});
				</script>
			</div>
			<div class="row mt-2" style="display: none;">
				<div class="col-md-3">VAT State</div>
				<div class="col-md-8"><div class="input-effect" id="sectionStateDiv_vat">
					<select class="form-control" name="state_vat" id="state_vat">
						@if (isset($editData))
								<option data-display="{{ $editData->vatstate->name }}"
									value="{{ $editData->vat_state }}" selected>
									{{ $editData->vatstate->name }}</option>
							@endif
					</select>
				</div></div>
			</div>
			<div class="row mt-2">
				<div class="col-md-3">VAT %</div>
				<div class="col-md-2"><input class="form-control" type="number"  name="vat_percentage" id="vat_percentage" readonly></div>
				<div class="col-md-4 mt-2"><input type="checkbox"  name="vat_percentage_fixed" id="vat_percentage_fixed" value="1"> Fixed Rate</div>
				<script>
					$( "#vat_percentage_fixed" ).click(function() {
						if(this.checked){
							$('#vat_percentage').attr('readonly', false);
						}
						if(!this.checked){
							$('#vat_percentage').attr('readonly', true);
						}
					});      
				</script>
			</div>
			<div class="row mt-2">
				<div class="col-md-3">Customer Type</div>
				<div class="col-md-8"><select class="form-control" name="customer_type" id="customer_type">
					<option data-display="" value=""></option>
					@foreach ($customer_type as $key => $value)
						<option value="{{ @$value->id }}" @if($value->id == 5) selected @endif>{{ @$value->title }} </option>
					@endforeach
				</select></div>
			</div>
			<div class="row mt-2">
				<div class="col-md-3">Sale Type</div>
				<div class="col-md-8"><select class="form-control" name="sale_type" id="sale_type">
					<option data-display="" value=""></option>
					@foreach ($sale_type as $key => $value)
						<option value="{{ @$value->id }}" @if($value->id == 5) selected @endif>{{ @$value->title }} </option>
					@endforeach
				</select></div>
			</div>
			<div class="row mt-2">
				<div class="col-md-3">VAT Number</div>
				<div class="col-md-8"><input class="form-control" type="text"  name="vat_number"></div>
			</div>
		</div>
	</div>
</div>
<div class="tab-pane pt-4" id="documents-panel" role="tabpanel" aria-labelledby="documents-tab">
	<div class="row">
		<div class="col-md-12">
			<a class="btn-info btn-sm float-right" style="cursor: pointer;" onclick="add_doc_row()"><i class="fa fa-plus-square" aria-hidden="true"></i> Add More</a>
			<input type="hidden" id="doc_row" value="4" />
			<script>
				function add_doc_row(){
					var r = $('#doc_row').val()
					$('#d_'+r).css('display','');
					r++;
					$('#doc_row').val(r);
				}
			</script>
		</div>
	</div>
	
	<div class="row pb-2">
		<div class="col-md-3">
			<input class="form-control" type="text" name="doc_name[]" value="Trade License/Commercial Registration" readonly/>
		</div>
		<div class="col-md-3">
			<input class="form-control" type="file" name="customer_documents_1" />
		</div>
		<div class="col-md-3">
			<input class="form-control" type="text" name="doc_exp_date[]" placeholder="Expiry Date" onfocus="(this.type='date')" onblur="(this.type='text')"/>
		</div>
		<div class="col-md-3">&nbsp;</div>
	</div>
	<div class="row pb-2">
		<div class="col-md-3">
			<input class="form-control" type="text" name="doc_name[]" value="VAT Certificate" readonly/>
		</div>
		<div class="col-md-3">
			<input class="form-control" type="file" name="customer_documents_2" />
		</div>
		<div class="col-md-3">&nbsp;</div>
	</div>

	@for ($i = 3; $i <= 10; $i++)
	<div class="row pb-2" id="d_{{ $i }}" @if($i > 3) style="display:none;" @endif>
		<div class="col-md-3">
			<input class="form-control" type="text" name="doc_name[]" value="Other Documents"/>
		</div>
		<div class="col-md-3">
			<input class="form-control" type="file" name="customer_documents_{{ $i }}" />
		</div>
		<div class="col-md-3">&nbsp;</div>
	</div>
	@endfor
</div>
</div>

{{--  tabs  --}}                           


			</div>
			<div class="col-md-12 mt-4 text-right">
				<button type="submit" class="btn btn-primary" id="btnSubmit">
				@if (isset($editData)) @lang('lang.update')
				@else
					@lang('Submit') @endif @lang('Form')
			</button>
			</div>

		</div>
		<div class="col-md-2">&nbsp;</div>
	</div>

		<script>
			$('#btnSubmit').click(function () {
				$('input:invalid').each(function () {
					var $closest = $(this).closest('.tab-pane');
					var id = $closest.attr('id');
					$('.nav a[href="#' + id + '"]').tab('show');
					return false;
				});
			});

			
// Get State List By Country Id
$("#country").on('change', function() {
    $("#loading_bg").css("display", "block");
    var url = $('#url').val();
    var country_id = $('#country').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { country_id: country_id },
        dataType: 'json',
        url: url + '/' + 'get_state_not_logged',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {

                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#state').append($('<option>', {
                            value: pin.id,
                            text: pin.name
                        }));

                        $("#sectionStateDiv ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.name + "</li>");
                    });
                } else {
                    $('#sectionStateDiv .current').html('');
                    $('#state').find('option').not(':first').remove();
                    $('#sectionStateDiv ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
            $("#loading_bg").css("display", "none");
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});

// Get State List By Country Id shipping
$("#country_ship").on('change', function() {
    $("#loading_bg").css("display", "block");
    var url = $('#url').val();
    var country_id = $('#country_ship').val();
    console.log(url);
    $.ajax({
        type: "GET",
        data: { country_id: country_id },
        dataType: 'json',
        url: url + '/' + 'get_state_not_logged',
        success: function(data) {
            console.log(data);
            var a = '';
            $.each(data, function(i, item) {
                if (item.length) {

                    $('#state_ship').find('option').not(':first').remove();
                    $('#sectionStateDiv_ship ul').find('li').not(':first').remove();

                    $.each(item, function(i, pin) {
                        $('#state_ship').append($('<option>', {
                            value: pin.id,
                            text: pin.name
                        }));

                        $("#sectionStateDiv_ship ul").append("<li data-value='" + pin.id + "' value='" + pin.id + "' class='option'>" + pin.name + "</li>");
                    });
                } else {
                    $('#sectionStateDiv_ship .current').html('');
                    $('#state_ship').find('option').not(':first').remove();
                    $('#sectionStateDiv_ship ul').find('li').not(':first').remove();
                }
            });
            console.log(a);
            $("#loading_bg").css("display", "none");
        },
        error: function(data) {
            console.log('Error:', data);
        }
    });
});
// Get VAT Details By Country Id vat
$("#country_vat").on('change', function() {
    $("#loading_bg").css("display", "block");

    var url = $('#url').val();
    var vat_id = $('#country_vat').val();
            $.ajax({
                url: url + '/' + 'get_vat_details_not_logged',
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}',
                    vat_id: vat_id,
                },
                cache: false,
                success: function(dataResult) {
                    var dataResult = JSON.parse(dataResult);
                    var len = 0;
                    if (dataResult['data'] == "ERROR") {
                        alert("Error found in something!!");
                        $("#vat_percentage").val();
                        $("#loading_bg").css("display", "none");
                    } else {
                        $("#vat_percentage").val(dataResult['data'].vat_percentage);
                        $("#loading_bg").css("display", "none");
                    }
                }
            });
});
		</script>



	<!--================ Start End Login Area =================-->

	<!--================ Footer Area =================-->
	<footer class="footer_area">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-12 text-center"> 
				</div>
			</div>
		</div>
	</footer>
	<!--================ End Footer Area =================-->

    <!-- script -->
    <script src="{{ asset('public/admin-iroid/') }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/js/sb-admin-2.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/chart.js/Chart.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/js/demo/chart-area-demo.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/js/demo/chart-pie-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('public/admin-iroid/') }}/js/demo/datatables-demo.js"></script>

    
<script type="text/javascript" src="{{asset('public/backEnd/')}}/vendors/js/toastr.min.js"></script>


<script src="{{asset('public/backEnd/')}}/js/custom.js"></script>
<script src="{{asset('public/backEnd/')}}/js/developer.js"></script>


    <script>
        $(document).ready(function () {
            $('.js-example-basic-single').select2();
        });
    </script>
</body>
</html>

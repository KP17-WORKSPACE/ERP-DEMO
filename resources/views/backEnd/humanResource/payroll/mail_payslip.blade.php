@php
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}


    $modules = array_unique(@$modules);


    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol;
    if(isset($generalSetting->logo)){  @$logo = @$generalSetting->logo;  }
    else{ @$logo = 'public/uploads/settings/logo.png'; }

    $sm_staff= App\SmStaff::where('user_id',Auth::user()->id)->first();
    if(!empty(@$sm_staff)){
        @$profile_image = @$sm_staff->staff_photo;
        if(empty(@$profile_image)){
            @$profile_image ='public/uploads/staff/staff1.jpg';
        }
    }
@endphp





<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
		<link rel="stylesheet" href="{{ asset('/public/css/mail_payslip.blade.css') }}">
	</head>
<body style="margin:20px;padding:0px;background-color:#e4e5e7">
<div style="margin:0px;padding:0px;background-color:#e4e5e7">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e4e5e7">
        <tbody>
			
		
			<!-- ACCOUNT INFORMATION START -->
			<tr>
				<td align="center">
					<table width="800" bgcolor="#ffffff" border="0"  cellspacing="0" cellpadding="0" style="background:#ffffff;" class="m_wd_full">
						<tbody>
							<tr>
								<td>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tbody>
											<tr>
												<td align="center">
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tbody>
															<tr>
																<td align="center" style="padding: 20px;">
																	<table width="100%" border="0" cellspacing="0" cellpadding="0">
																		<tbody>
																			<tr>
																				<th valign="top" align="left" width="50%">
																					<table width="100%" border="0" cellpadding="0" cellspacing="0">
																						<tbody>	
																							<tr>
																								<div class="business-info text-left">
														                                            <img src="http://spondan.com/erp/public/uploads/settings/96d997c5804d348158e58a4818fee1ff.jpg"  style="min-width: 160px; max-width: 160px; height: auto;">
														                                                <h3 class="mt-10 primary-color" style=" color: #415094 !important; font-size: 24px; margin-top: 20px; margin-bottom: 0; padding-right: 26px; line-height: 25px; font-family: 'Poppins', sans-serif;">@if(isset($schoolDetails)){{@$schoolDetails->school_name}} @endif</h3>
														                                                <div style="width: 70%;">
														                                                    <p style="font-weight: 300; color: #828bb2 !important;"></p>
														                                                    <p style="font-weight: 300; color: #828bb2 !important;  margin-top: 15px;">@if(isset($schoolDetails)){{@$schoolDetails->address}} @endif</p>
														                                                </div>
														                                            </div>
																							</tr>
																						</tbody>
																					</table>
																				</th>
																				<th valign="top" align="center" width="50%">
																					<table width="100%" border="0" cellpadding="0" cellspacing="0" >
																						<tbody>
																							<tr>
																								<td align="center" style="padding: 10px 25px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; color:#24252a; line-height:18px; text-align:left; display:block;">

																								{{ __('Staff Name') }}: @if(isset($payrollDetails)){{@$payrollDetails->staffDetails->full_name}} @endif<br>
																									{{ __('Staff ID') }}: @if(isset($payrollDetails)){{@$payrollDetails->staffs->staff_no}} @endif<br>
																									{{ __('Department') }}: @if(isset($payrollDetails)){{@$payrollDetails->staffDetails->departments->name}} @endif<br>
																									{{ __('Designation') }}: @if(isset($payrollDetails)){{@$payrollDetails->staffDetails->designations->title}} @endif<br>

																									
																								</td>
																							</tr>
																						</tbody>
																					</table>
																				</th>
																			</tr>
																		</tbody>
																	</table>
																</td>
															</tr>
															<tr>
																<td align="center" >
																	<table width="100%" border="0" cellspacing="0" cellpadding="0" >
																		<tbody>
																			<tr>
																				<th valign="top" align="left" width="50%">
																					<table width="100%" border="0" cellpadding="0" cellspacing="0">
																						<tbody>					    
																							<tr>
																								<td align="center" style="padding: 5px 0px 5px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:14px; font-weight:bold; color:#24252a; line-height:18px; text-align:left; display:block; ">
																									<p>{{ __('Payslip') }} #@if(isset($payrollDetails)){{$payrollDetails->id}} @endif</p>
																								</td>
																							</tr>
																						</tbody>
																					</table>
																				</th>
																				<th valign="top" align="center" width="50%">
																					<table width="100%" border="0" cellpadding="0" cellspacing="0">
																						<tbody>
																							<tr>
																								<td align="center" style="padding: 10px 25px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; color:#24252a; line-height:18px; text-align:left; display:block;">
																									<p>{{ __('Payment Date') }} :@if(isset($payrollDetails)){{date('jS M, Y', strtotime(@$payrollDetails->payment_date))}} @endif</p>

																									
																								</td>
																							</tr>
																						</tbody>
																					</table>
																				</th>
																			</tr>
																		</tbody>
																	</table>
																</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center" style="padding:0 5px 0 5px;">
					<table width="800" bgcolor="#ffffff" border="0"  cellspacing="0" cellpadding="0" style="background:#ffffff;" class="m_wd_full">
						<tbody>
							<tr>
								<td>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tbody>
											<tr>
												<td colspan="2">


												<table width="100%" border="0" cellpadding="0" cellspacing="0">
													<tbody>					    
														<tr>
															<td bgcolor="#dedede" align="center" style="padding: 15px 10px 15px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:14px; font-weight:bold; color:#24252a; line-height:18px; text-align:left; border-left: 1px solid #c5c1c1; border-top: 1px solid #c5c1c1;">
																 {{ __('Earnings') }}
															</td>
															<td bgcolor="#dedede" style="padding: 15px 10px 15px 0px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:14px; font-weight:bold; color:#24252a; line-height:18px; border-right: 1px solid #c5c1c1; border-top: 1px solid #c5c1c1;">
																{{ __('Deductions') }}
															</td>
														</tr>
													
													</tbody>
												</table>



											</td>
											</tr>
											<tr>
												<td align="center">
													<table width="100%" border="0" cellspacing="0" cellpadding="0"  style="border: 1px solid #c5c1c1">
														<tbody>
															<tr>
																<td align="center">
																	<table width="100%" border="0" cellspacing="0" cellpadding="0">
																		<tbody>
																			<tr>
																				<th valign="top" align="left" width="50%">
																					<table width="100%" border="0" cellpadding="0" cellspacing="0">
																						<tbody>					    
																							<tr>
																								<td bgcolor="#dedede" align="center" style="padding: 5px 10px 5px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:14px; font-weight:bold; color:#24252a; line-height:18px; text-align:left; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1;">
																									{{ __('Title') }}
																								</td>
																								<td bgcolor="#dedede" align="right" style="padding: 5px 10px 5px 0px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:14px; font-weight:bold; color:#24252a; line-height:18px; text-align:right; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1;">
																									{{ __('Amount') }} ({{'BDT'}})
																								</td>
																							</tr>
																						
																							<tr>
																								<td style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:left; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1;">
																									{{ __('Basic Salary') }}
																								</td>
																								<td align="right" style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:right; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1;">
																									@if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$payrollDetails->basic_salary)}} @endif
																								</td>
																							</tr>
																							@php @$gross_earning = @$payrollDetails->basic_salary; @endphp

																							@foreach($payrollEarnDetails as $payrollEarnDetail)

                                                											@php @$gross_earning = @$gross_earning + @$payrollEarnDetail->amount; @endphp
																							<tr>
																								<td style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:left; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1;">
																									{{$payrollEarnDetail->type_name}}
																								</td>
																								<td align="right" style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:right; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1;">
																									@if(isset(@$payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$payrollEarnDetail->amount)}} @endif
																								</td>
																							</tr>
																							@endforeach
																							<tr>
																								<td style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:left;  border-right: 1px solid #c5c1c1;">
																									{{ __('Gross earnings') }} ({{'BDT'}})
																								</td>
																								<td align="right" style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:right;  border-right: 1px solid #c5c1c1;">
																									@if(isset(@$payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$gross_earning)}} @endif
																								</td>
																							</tr>
																						</tbody>
																					</table>
																				</th>
																			</tr>
																		</tbody>
																	</table>
																</td>
															</tr>
														</tbody>
													</table>
												</td>
												<td align="center" valign="top">
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tbody>
															<tr>
																<td align="center">
																	<table width="100%" border="0" cellspacing="0" cellpadding="0">
																		<tbody>
																			<tr>
																				<th valign="top" align="left" width="50%">
																					<table width="100%" border="0" cellpadding="0" cellspacing="0">
																						<tbody>					    
																							<tr>
																								<td bgcolor="#dedede" align="center" style="padding: 5px 10px 5px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:14px; font-weight:bold; color:#24252a; line-height:18px; text-align:left; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1; border-top: 1px solid #c5c1c1;">
																									{{ __('Title') }}
																								</td>
																								<td bgcolor="#dedede"align="right" style="padding: 5px 10px 5px 0px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:14px; font-weight:bold; color:#24252a; line-height:18px; text-align:right; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1; border-top: 1px solid #c5c1c1;">
																									{{ __('Amount') }} ({{'BDT'}})
																								</td>
																							</tr>
																						
																						@php $total_deduction = 0; @endphp
										                                                   @foreach($payrollDedcDetails as $payrollDedcDetail)
										                                                   @php @$total_deduction = @$total_deduction + @$payrollDedcDetail->amount; @endphp
																							<tr>
																								<td style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:left; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1;">
																									{{@$payrollDedcDetail->type_name}}
																								</td>
																								<td align="right" style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:right; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1;">
																									@if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$payrollDedcDetail->amount)}} @endif
																								</td>
																							</tr>
																							@endforeach
																							<tr>
																								<td style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:left; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1;">
																									{{ __('Total Deductions') }} ({{'BDT'}})
																								</td>
																								<td align="right" style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:right; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1;">
																									@if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$total_deduction)}} @endif
																								</td>
																							</tr>
																						</tbody>
																					</table>
																				</th>
																			</tr>
																		</tbody>
																	</table>
																</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>

											<tr>
												<td colspan="2">


												<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top: 30px; border-top: 1px solid #c5c1c1;">
													<tbody>					    
														<tr>
															<td bgcolor="#dedede" align="center" style="padding: 5px 10px 5px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:14px; font-weight:bold; color:#24252a; line-height:18px; text-align:left; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1; border-left: 1px solid #c5c1c1;">
																{{ __('Gross Earnings') }} ({{'BDT'}})
															</td>
															<td bgcolor="#dedede"  align="right" style="padding: 5px 10px 5px 0px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:14px; font-weight:bold; color:#24252a; line-height:18px; text-align:right; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1; ">
																{{ __('Total Deductions') }} ({{'BDT'}})
															</td>
															<td bgcolor="#dedede" align="right" style="padding: 5px 10px 5px 0px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:14px; font-weight:bold; color:#24252a; line-height:18px; text-align:right; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1; ">
																{{ __('Net Salary') }} ({{'BDT'}})
															</td>
														</tr>
													
													
														<tr>
															<td  style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:left; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1; border-left: 1px solid #c5c1c1;">
																@if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$gross_earning)}} @endif
															</td>
															<td  align="right" style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:right; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1;">
																@if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$total_deduction)}} @endif
															</td>
															<td align="right" style="padding: 10px 10px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:right; border-bottom: 1px solid #c5c1c1;  border-right: 1px solid #c5c1c1;">
																@if(isset($payrollDetails)){{App\User::NumberToBangladeshiTakaFormat(@$gross_earning - @$total_deduction)}} @endif
															</td>
														</tr>
													</tbody>
												</table>



											</td>
											</tr>


											<tr>
												<td colspan="2">
													<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-top: 30px">
														<tbody>	
														
														
															<tr>
																<td  style="padding: 10px 0px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:left;">

																	<p class="fw-500 primary-color"> <b></b> <span class="text-left" style="color: #828bb2 !important;">{{-- Fifty two thousands taka only. --}}</span></p>
																</td>
																<td align="right" style="padding: 10px 0px 10px 20px; font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:12px; font-weight:bold; color:#24252a; line-height:18px; text-align:right; ">

																	@if($payrollDetails->payment_mode == 3)


																		{{ __('Bank Name.') }} {{@$payrollDetails->accountInfo != ""? @$payrollDetails->accountInfo->bank_name:""}}<br>
																		{{ __('Account Name.') }} {{@$payrollDetails->accountInfo != ""? @$payrollDetails->accountInfo->account_name:""}}<br>
																		{{ __('Account  No.') }} {{@$payrollDetails->accountInfo != ""? @$payrollDetails->accountInfo->account_no:""}}<br>
																		{{ __('Deposite date.') }} {{@$payrollDetails->cheque_deposite_date != ""? date('jS M, Y', strtotime(@$payrollDetails->cheque_deposite_date)):''}}<br>



																	@elseif($payrollDetails->payment_mode == 2)


																	{{ __('Bank Name.') }} {{@$payrollDetails->bank_name}}<br>
																		{{ __('Cheque No.') }} {{@$payrollDetails->cheque_no}}<br>
																		{{ __('Cheque issue date.') }} {{@$payrollDetails->cheque_deposite_date != ""? date('jS M, Y', strtotime(@$payrollDetails->cheque_deposite_date)):''}}<br>

																	@endif
																</td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>




											<tr><td height="30"><img src="https://gallery.mailchimp.com/d942a4805f7cb9a8a6067c1e6/images/1a808f19-c541-48d8-afad-3d9529131c98.gif" alt="" width="1" style="width:1px;display:block"></td></tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		
			
			<!-- ACCOUNT INFORMATION START -->
			<tr>
				<td align="center" style="padding:0 5px 0 5px;">
					<table width="800" bgcolor="#ffffff" border="0" cellspacing="0" cellpadding="0" style="background:#ffffff;" class="m_wd_full">
						<tbody>
							<tr>
								<td style="padding:0 25px 0 25px;">
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tbody>
										    <tr><td height="50"><img src="https://gallery.mailchimp.com/d942a4805f7cb9a8a6067c1e6/images/1a808f19-c541-48d8-afad-3d9529131c98.gif" alt="" width="1" style="width:1px;display:block"></td></tr>
											<tr>
												<td align="center" style="font-family: 'PT Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size:14px; font-weight:normal; color:#24252a; line-height:22px; text-align:left; display:block;">
													{{ __('If you are having any issues with your salary info, please do not hesitate to contact us.') }}<br/>
													{{ __('Thanks!') }}
												</td>
											</tr>
											<tr><td height="30"><img src="https://gallery.mailchimp.com/d942a4805f7cb9a8a6067c1e6/images/1a808f19-c541-48d8-afad-3d9529131c98.gif" alt="" width="1" style="width:1px;display:block"></td></tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<!-- ACCOUNT INFORMATION END -->
		
			
        </tbody>
    </table>
</div>
</body>
</html>

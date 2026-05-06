@php $generalSetting=App\SmGeneralSettings::where('id',1)->first(); @endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>{{ __('Confirm Password')}}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
		<link rel="stylesheet" href="{{asset('/public/css')}}/confirmation_reset.css">
	</head>


<body>
<div class="confirmation_div1">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#e4e5e7">
        <tbody> 
			<tr>
				<td align="center">
					<table width="600" bgcolor="#ffffff" border="0" cellspacing="0" cellpadding="0" class="m_wd_full">
						<tbody>
							<tr>
								<td>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tbody>
										    <tr><td height="30"><img src="" alt="" width="1" ></td></tr>
											<tr>
												<td align="center" class="m_img_mc_fix">
													<a href="" target="_blank">
														<img align="center" src="{{asset($generalSetting->logo)}}" alt="" width="" height="" border="0">
													</a>
												</td>
											</tr>
											<tr><td height="30"><img src="" alt="" width="1" class="confirmation_div1_table_tbody_tr_td"></td></tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<!-- LOGO END -->
			
			<!-- HEADING + ICON START -->
			<tr>
				<td align="center" class="table_tr_button">
					<table width="600" border="0" cellspacing="0" cellpadding="0"  class="m_wd_full header_table">
						<tbody>
							<tr>
								<td>
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tbody>
										    <tr><td height="50"><img src="https://gallery.mailchimp.com/d942a4805f7cb9a8a6067c1e6/images/1a808f19-c541-48d8-afad-3d9529131c98.gif" alt="" width="1" class="header_img"></td></tr>
											<tr>
												<td align="center" class="m_img_mc_fix">
													<img align="center" src="https://gallery.mailchimp.com/d942a4805f7cb9a8a6067c1e6/images/4440afa1-9973-4508-8483-272869e7bbf5.png" alt="" width="83" height="83" border="0" class="header_img2">
												</td>
											</tr>
											<tr>
												<td align="center" class="reset_button">
													{{ __('Reset Password') }}
												</td>
											</tr>
											<tr><td height="50"><img src="" alt="" width="1" class="reset_button_2"></td></tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<!-- HEADING + ICON END -->
		
			<!-- HEADING + ICON START -->
			<tr>
				<td align="center" class="last_table">
					<table width="600" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0"  class="m_wd_full last_table_css">
						<tbody>
							<tr>
								<td class="account_info_table_tr_td">
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tbody>
											<!-- Button START -->
											<tr>
												<td class="last_table_td_css">
													<table align="center" cellspacing="0" cellpadding="0" border="0">
														<tr>
															<td class="last_table_td_css_table_td">
																<table cellspacing="0" cellpadding="0" border="0" width="100%">
																	<tr>
																		<td>
																			<table cellspacing="0" cellpadding="0" border="0" width="100%">
																				<tr>
																					<td class="last_table_td_css_table_td_tr">
																						<a href="{{url('reset/password'.'/'.$data['email'].'/'.$data['random'])}}" class="last_table_td_css_table_td_tr_a" target="_blank">Click Here </a>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<!-- Button END -->
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<!-- HEADING + ICON END -->
			
			<!-- ACCOUNT INFORMATION START -->
			<tr>
				<td align="center" class="account_info_bg;">
					<table width="600"  border="0" cellspacing="0" cellpadding="0"  class="m_wd_full account_info_table">
						<tbody>
							<tr>
								<td class="account_info_table_tr_td">
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tbody>
										    <tr><td height="50"><img src="" alt="" width="1" class="account_info_table_tbody_img"></td></tr>
											<tr>
												<td align="center" class="account_info_table_tbody_warning">
													{{ __('If you are having any issues with your account,please do not hesitate to contact us by replying to this mail.') }} <br/>
													{{ __('Thanks!') }}
												</td>
											</tr>
											<tr><td height="30"><img src="" alt="" width="1" class="account_info_table_tbody_img"></td></tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<!-- ACCOUNT INFORMATION END -->
			
			<!-- Footer -->
			<tr>
				<td align="center" class="footer_td">
					<table width="600" bgcolor="#f6f7f9" border="0" cellspacing="0" cellpadding="0"  class="m_wd_full acount_info_footer_table">
						<tbody>
							<tr>
								<td class="footer_td_table_tr_tbody">
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<tbody>
										    <tr><td height="25"><img src="" alt="" width="1" class="account_info_table_tbody_img"></td></tr>
											<tr>
												<td align="center" class="account_info_table_tbody_warning">
													{{ __('You are receiving this email because you have an account in Hu. If you are not sure why you are receiving this, please') }} <a href="mailto:" target="_blank" class="contact_us">{{ __('contact us') }}</a>. 
												</td>
											</tr>
											<tr><td height="25"><img src="" alt="" width="1" class="account_info_table_tbody_img"></td></tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<!-- Footer END -->
			
        </tbody>
    </table>
</div>
</body>
</html>

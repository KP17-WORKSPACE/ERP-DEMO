{{ __('Hello') }} {{@$result['name']}},
<br><br>
<strong>{{@$result['email_sms_title']}}</strong><br>
{{@$result['description']}}<br><br>
{{ __('Thanks') }}<br>
<br><br>

{{__('Sincerely')}}<br>
{{ __('On behalf of ') }} {{@$result['FROM_COMPANY']}}<br>
{{ __('Email: ') }} {{@$result['FROM_EMAIL']}}<br>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@lang('lang.Support') @lang('lang.Ticket') @lang('lang.information')</title>
</head>
<body>
    <p>
       @lang('lang.Thank_you') {{ ucfirst($user->name) }} for contacting our support team. A support ticket has been opened for you. You will be notified when a response is made by email. The details of your ticket are shown below:
    </p>

    <p>@lang('lang.title'): {{ $ticket->title }}</p>
    <p>@lang('lang.priority'): {{ $ticket->priority }}</p>
    <p>@lang('lang.Status'): {{ $ticket->status }}</p>

    <p>
        @lang('lang.You_can_view_ticket') {{ url('tickets/'. $ticket->ticket_id) }}
    </p>

</body>
</html>
@lang('lang.ticket_is_created').


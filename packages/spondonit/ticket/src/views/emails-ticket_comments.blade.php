<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@lang('lang.Support') @lang('lang.Ticket')</title>
</head>
<body>
    <p>
        {{ $comment->comment }}
    </p>

    ---
    <p>Replied by: {{ $user->name }}</p>

    <p> @lang('lang.title'): {{ $ticket->title }}</p>
    <p> @lang('lang.title'): {{ $ticket->ticket_id }}</p>
    <p> @lang('lang.Status'): {{ $ticket->status }}</p>

    <p>
        @lang('lang.You_can_view_ticket') {{ url('tickets/'. $ticket->ticket_id) }}
    </p>

</body>
</html>

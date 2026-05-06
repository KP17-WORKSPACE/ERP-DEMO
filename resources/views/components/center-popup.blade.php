<style>
    #long-list-notification td,
    #long-list-notification th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #long-list-notification tr.expand td {
        white-space: wrap !important;
        overflow: visible !important;
        text-overflow: unset !important;
        height: auto !important;
        word-break: break-word;
    }

    /* Optional for pointer on rows */
    #long-list-notification tbody tr {
        cursor: pointer;
    }
</style>

@if(isset($centerNotifications) && count($centerNotifications))


@php
$centerNotifications = $centerNotifications->groupBy('type');
@endphp

<div class="modal fade show" id="globalPopup" tabindex="-1" style="display:block; background:rgba(0,0,0,0.4)">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <!-- BIG modal -->
        <div class="modal-content shadow-lg">

            <div class="modal-header">
                <h4 class="modal-title">Pending Notifications ({{ count($centerNotifications) }})</h4>
            </div>

            <div class="modal-body p-0">
                <div class="table-responsive">

                    @if(isset($centerNotifications['dealtrack']))

                    <table class="table table-hover m-0" id="long-list-notification"
                        style="table-layout: fixed;width:100%">

                        <thead>
                            <tr>
                                <th class="text-center" style="width:100px">Deal ID</th>
                                <th style="width:100px">Customer</th>
                                <th class="text-end" style="width:100px">Amount</th>
                                <th style="width:100px">Sales Person</th>
                                <th style="width:100px">Pending From</th>
                                <th class="text-center" style="width:100px">Company</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($centerNotifications as $note)

                            <tr>
                                <td class="text-center">
                                    <a href="{{ url('crm-deal-track-approval-list/' . $note->record_id) }}">
                                        {{ $note->deal_id ?? '-' }}
                                    </a>
                                </td>

                                <td>{{ $note->customer->customer_name_display ?? '-' }}</td>
                                <td class="text-end">{{ $note->value ?? '-' }}</td>



                                <td>{{ optional($note->salesperson)->first_name ?? '-' }} {{
                                    optional($note->salesperson)->last_name ?? '' }}</td>




                                <td>
                                    @php
                                    // Parse created_at as Dubai time WITHOUT shifting it
                                    $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $note->created_at,
                                    'Asia/Dubai');
                                    $end = \Carbon\Carbon::now('Asia/Dubai');

                                    $diffInSeconds = $end->timestamp - $start->timestamp;

                                    $days = floor($diffInSeconds / (24 * 3600));
                                    $hours = floor(($diffInSeconds % (24 * 3600)) / 3600);
                                    $minutes = floor(($diffInSeconds % 3600) / 60);

                                    $parts = [];
                                    if ($days > 0) $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
                                    if ($hours > 0) $parts[] = $hours . ' hr' . ($hours > 1 ? 's' : '');
                                    if ($minutes > 0) $parts[] = $minutes . ' min' . ($minutes > 1 ? 's' : '');

                                    if (empty($parts)) $parts[] = 'just now';

                                    echo implode(' ', $parts);
                                    @endphp





                                </td>





                                <td class="text-center">{{ $note->company->company_name ?? '-' }}</td>

                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                    @endif

                    @if(isset($centerNotifications['user']))

                    <table class="table table-hover m-0" id="long-list-notification"
                        style="table-layout: fixed;width:100%">

                        <thead>
                            <tr>
                                <th class="text-center" style="width:100px">Deal ID</th>
                                <th style="width:100px">Customer</th>
                                <th class="text-end" style="width:100px">Amount</th>
                                <th style="width:100px">Sales Person</th>
                                <th style="width:300px">Message</th>
                                <th style="width:100px">Pending From</th>
                                <th class="text-center" style="width:100px">Company</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($centerNotifications['user'] as $note)

                            <tr>
                                <td class="text-center">
                                    <a href="{{ url('crm-deals/show/' . $note->record_id) }}">
                                        {{ $note->deal_id ?? '-' }}
                                    </a>
                                </td>

                                <td>{{ $note->customer->customer_name_display ?? '-' }}</td>
                                <td class="text-end">{{ $note->value ?? '-' }}</td>



                                <td>{{ optional($note->salesperson)->first_name ?? '-' }} {{
                                    optional($note->salesperson)->last_name ?? '' }}</td>

                                <td>{{ $note->title ?? '' }}</td>



                                <td>
                                    @php
                                    // Parse created_at as Dubai time WITHOUT shifting it
                                    $start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $note->created_at,
                                    'Asia/Dubai');
                                    $end = \Carbon\Carbon::now('Asia/Dubai');

                                    $diffInSeconds = $end->timestamp - $start->timestamp;

                                    $days = floor($diffInSeconds / (24 * 3600));
                                    $hours = floor(($diffInSeconds % (24 * 3600)) / 3600);
                                    $minutes = floor(($diffInSeconds % 3600) / 60);

                                    $parts = [];
                                    if ($days > 0) $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
                                    if ($hours > 0) $parts[] = $hours . ' hr' . ($hours > 1 ? 's' : '');
                                    if ($minutes > 0) $parts[] = $minutes . ' min' . ($minutes > 1 ? 's' : '');

                                    if (empty($parts)) $parts[] = 'just now';

                                    echo implode(' ', $parts);
                                    @endphp





                                </td>





                                <td class="text-center">{{ $note->company->company_name ?? '-' }}</td>

                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                    @endif

                </div>

                <div class="d-flex justify-content-center mt-4 mb-2">
                    <button class="btn btn-light" onclick="dismissNotifications()">Dismiss All</button>

                </div>
            </div>

        </div>
    </div>
</div>

@endif


<script>
    function dismissNotifications() {
    $.post("{{ url('/notifications/dismiss') }}", {
        _token: "{{ csrf_token() }}"
    }, function() {
        $("#globalPopup").hide();
    });
}


$(document).ready(function () {
   $(document).on('click', '#long-list-notification > tbody > tr', function (e) {
    // prevent triggering when clicking inside a nested table
    if ($(e.target).closest('table').attr('id') !== 'long-list-notification') {
      return;
    }

    if ($(e.target).closest('td').hasClass('no-toggle')) {
      return; // do nothing if inside excluded cells
    }

    $(this).toggleClass('expand');
  });
});

</script>
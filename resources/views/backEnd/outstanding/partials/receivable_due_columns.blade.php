@php
    $installments = $breakdown['installments'] ?? [];
    $recvDueCrossPopover = function ($inst, $focus) {
        $od = (int) ($inst['overdue_days'] ?? 0);
        if ($od > 0) {
            $odLabel = $od . ' days overdue';
        } elseif ($od < 0) {
            $odLabel = 'Due in ' . abs($od) . ' days';
        } else {
            $odLabel = 'Due today';
        }
        $amt = App\SysHelper::com_curr_format($inst['amount'] ?? 0, 2, '.', ',');
        $date = $inst['due_date'] ?? '';
        $html = '<div class="small text-start ageing-grn-popover text-nowrap">';
        if ($focus !== 'date') {
            $html .= '<div><strong>Due date:</strong> ' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . '</div>';
        }
        if ($focus !== 'od') {
            $html .= '<div><strong>Over due:</strong> ' . htmlspecialchars($odLabel, ENT_QUOTES, 'UTF-8') . '</div>';
        }
        if ($focus !== 'amt') {
            $html .= '<div><strong>Amount:</strong> ' . htmlspecialchars($amt, ENT_QUOTES, 'UTF-8') . '</div>';
        }
        if (!empty($inst['label'])) {
            $html .= '<div><strong>Terms:</strong> ' . htmlspecialchars($inst['label'], ENT_QUOTES, 'UTF-8') . '</div>';
        }
        $html .= '</div>';

        return htmlspecialchars($html, ENT_QUOTES, 'UTF-8');
    };
    $recvDueOdShort = function ($od) {
        $od = (int) $od;
        if ($od > 0) {
            return (string) $od;
        }
        if ($od < 0) {
            return (string) $od;
        }

        return '0';
    };
    $recvDueOdClass = function ($od) {
        $od = (int) $od;
        if ($od > 0) {
            return 'recv-sched-od-late';
        }
        if ($od < 0) {
            return 'recv-sched-od-soon';
        }

        return 'recv-sched-od-today';
    };
@endphp

<td class="text-start recv-sched-col">
    @if (count($installments) > 0)
        <span class="recv-sched-list">
            @foreach ($installments as $idx => $inst)
                @if ($idx > 0)<span class="recv-sched-sep">, </span>@endif
                <span class="recv-sched-item ageing-grn-pop ageing-grn-tip {{ $recvDueOdClass($inst['overdue_days'] ?? 0) }}"
                      tabindex="0" role="button"
                      data-bs-toggle="popover" data-bs-html="true"
                      data-bs-trigger="hover focus" data-bs-placement="auto"
                      data-bs-content="{!! $recvDueCrossPopover($inst, 'date') !!}">{{ $inst['due_date'] }}</span>
            @endforeach
        </span>
    @endif
</td>
<td class="text-start recv-sched-col">
    @if (count($installments) > 0)
        <span class="recv-sched-list">
            @foreach ($installments as $idx => $inst)
                @if ($idx > 0)<span class="recv-sched-sep">, </span>@endif
                <span class="recv-sched-item ageing-grn-pop ageing-grn-tip {{ $recvDueOdClass($inst['overdue_days'] ?? 0) }}"
                      tabindex="0" role="button"
                      data-bs-toggle="popover" data-bs-html="true"
                      data-bs-trigger="hover focus" data-bs-placement="auto"
                      data-bs-content="{!! $recvDueCrossPopover($inst, 'od') !!}">{{ $recvDueOdShort($inst['overdue_days'] ?? 0) }}</span>
            @endforeach
        </span>
    @endif
</td>
<td class="text-start recv-sched-col">
    @if (count($installments) > 0)
        <span class="recv-sched-list">
            @foreach ($installments as $idx => $inst)
                @if ($idx > 0)<span class="recv-sched-sep">, </span>@endif
                <span class="recv-sched-item ageing-grn-pop ageing-grn-tip"
                      tabindex="0" role="button"
                      data-bs-toggle="popover" data-bs-html="true"
                      data-bs-trigger="hover focus" data-bs-placement="auto"
                      data-bs-content="{!! $recvDueCrossPopover($inst, 'amt') !!}">{{ App\SysHelper::com_curr_format($inst['amount'] ?? 0, 2, '.', ',') }}</span>
            @endforeach
        </span>
    @endif
</td>

@php
    $maxInst = isset($max_installments) ? (int) $max_installments : 1;
    $installments = $breakdown['installments'] ?? [];
@endphp
@for ($i = 0; $i < $maxInst; $i++)
    @php
        $inst = $installments[$i] ?? null;
        $od = $inst ? (int) ($inst['overdue_days'] ?? 0) : 0;
        if ($od > 0) {
            $odBadgeClass = 'recv-od-badge recv-od-late';
            $odLabel = $od . 'd overdue';
        } elseif ($od < 0) {
            $odBadgeClass = 'recv-od-badge recv-od-soon';
            $odLabel = 'Due in ' . abs($od) . 'd';
        } else {
            $odBadgeClass = 'recv-od-badge recv-od-today';
            $odLabel = 'Due today';
        }
    @endphp
    <td class="receivable-due-cell align-middle">
        @if ($inst)
            <div class="recv-due-box">
                <div class="recv-due-date">{{ $inst['due_date'] }}</div>
                <div class="recv-due-amt">
                    @if (!empty($inst['popover_content_attr']))
                        <span class="ageing-grn-pop ageing-grn-tip" tabindex="0" role="button" data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover focus" data-bs-placement="auto" data-bs-content="{!! $inst['popover_content_attr'] !!}">{{ App\SysHelper::com_curr_format($inst['amount'], 2, '.', ',') }}</span>
                    @else
                        {{ App\SysHelper::com_curr_format($inst['amount'], 2, '.', ',') }}
                    @endif
                </div>
                <span class="{{ $odBadgeClass }}">{{ $odLabel }}</span>
                <div class="recv-due-more">
                    <div class="recv-due-more-row"><span>Terms</span><span>{{ $inst['label'] }}</span></div>
                    @if (($inst['finance_cost'] ?? 0) > 0)
                        <div class="recv-due-more-row"><span>Finance</span><span>{{ App\SysHelper::com_curr_format($inst['finance_cost'], 2, '.', ',') }}</span></div>
                    @endif
                </div>
            </div>
        @endif
    </td>
@endfor

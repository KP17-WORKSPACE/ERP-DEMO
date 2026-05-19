@php $maxInst = isset($max_installments) ? (int) $max_installments : 1; @endphp
@for ($i = 1; $i <= $maxInst; $i++)
    <th class="text-center receivable-due-th" style="width:9%; min-width:88px;">Due {{ $i }}</th>
@endfor

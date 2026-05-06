<div class="table-responsive">
    <table  class="table table-hover manage-u-table">
        <thead>
            <tr style="background: #eeeeee; color: #000000;">
                <th width="25%" style="padding: 7px;">@lang('Part Number')</th>
                <th width="30%" style="padding: 7px;">@lang('Description')</th>
                <th width="10%" style="padding: 7px;" class="text-center">@lang('Qty')</th>
                <th width="10%" style="padding: 7px;" class="text-center">Reserved Qty</th>
                <th width="10%" style="padding: 7px;" class="text-center">Avl Qty</th>
                <th width="15%" style="padding: 7px;" class="text-center">Group Qty</th>
            </tr>
        </thead>

        <tbody>
            @if(count($data_list) > 0)
            @php 
                $last_part_number = null;
                $logged_company_id = session('logged_session_data.company_id');
            @endphp
        
            @foreach($data_list as $d)
        
                @if($last_part_number !== $d->part_number)
                    @php
                        // Calculate total balance_qty for this part number for current logged-in company only
                        $total_balance_qty = $data_list->where('part_number', $d->part_number)
                                                       ->where('company_id', $logged_company_id)
                                                       ->sum('balance_qty');
                        
                        // Calculate group total (all companies)
                        $group_total_qty = $data_list->where('part_number', $d->part_number)->sum('balance_qty');
                        
                 
                      
                        // Get reserved quantity for logged-in company using stock_id (sys_item_stock.id)
                        $reserved_qty = @App\SysHelper::get_reserved_qty($d->partno, $d->part_number, $logged_company_id);
                        
                        // Calculate available qty
                        $available_qty = $total_balance_qty - $reserved_qty;
                    @endphp
                    <tr style="font-size: 13px; cursor: pointer;"> <?php /*  onclick="expand_part_number_det({{ $d->partno }})" */ ?>
                        <td style="padding: 7px;">{{ $d->part_number }}</td>
                        <td style="padding: 7px;">{{ $d->description }}</td>
                        <td style="padding: 7px;" class="text-center">{{ $total_balance_qty }}</td>
                        <td style="padding: 7px;" class="text-center">{{ $reserved_qty }}</td>
                        <td style="padding: 7px;" class="text-center font-weight-bold text-success">{{ $available_qty }}</td>
                        {{-- <td class="text-end">{{ round($d->avg_price, 0) }}</td>
                        @if($d->avg_price == 0 || $d->balance_qty == 0)
                            <td class="text-end">0.00</td>
                        @else
                            <td class="text-end">{{ round($d->avg_price / $d->balance_qty, 2) }}</td>
                        @endif --}}
                        <td style="padding: 7px;" class="text-center">
                            <a class="btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#exampleModalCenter_{{ $d->partno }}">{{ $group_total_qty }}</a></td>
                    </tr>
                    @php $last_part_number = $d->part_number; @endphp
                @endif
                @endforeach
        @endif
        </tbody>
    </table>
    <div class="text-center">
        @if(count($data_list)>0)
        {{$data_list->links()}}
        @endif
    </div>
</div>

@if(count($data_list) > 0)
@php $last_part_number2 = null; @endphp
@foreach($data_list as $d)
                @if($last_part_number2 !== $d->part_number)
<div class="modal side-panel fade" id="exampleModalCenter_{{ $d->partno }}" data-bs-backdrop="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h4 class="modal-title" id="exampleModalLongTitle">{{ $d->part_number }}</h4>
					        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <table  class="table table-hover manage-u-table">
                                @php $data_list2 =$data_list->where('partno',$d->partno); @endphp
                                @if(count($data_list2) > 0)
                                <tr>
                                    <th width="25%">@lang('Part Number')</th>
                                    <th width="40%">@lang('Company')</th>
                                    <th width="10%" class="text-end">@lang('Qty')</th>
                                    <th width="15%" class="text-end">@lang('Rate')</th>
                                    <th width="10%" class="text-end"></th>
                                </tr>
                                @foreach($data_list2 as $d2)
                                <tr>
                                    <td>{{ $d2->part_number }}</td>
                                    <td>{{ $d2->company_name }}</td>
                                    <td class="text-end">{{ $d2->balance_qty }}</td>
                        
                                    @if($show_all == 1)
                                        <td class="text-end">{{ round($d2->avg_price*110/100, 0) }}</td>
                                        {{-- @if($d2->avg_price == 0 || $d2->balance_qty == 0)
                                            <td class="text-end">0.00</td>
                                        @else
                                            <td class="text-end">{{ round($d2->avg_price / $d2->balance_qty, 2) }}</td>
                                        @endif --}}
                                    @endif
                        
                                    <td class="text-end"></td>
                                </tr>
                                @endforeach
                                @endif
                            </table>
                        </div>
                      </div>
                    </div>
                  </div>
                
                            

                @php $last_part_number2 = $d->part_number; @endphp
                @endif
            @endforeach
            @endif


<script>
    $('#accordionSidebar').addClass('toggled');
    function expand_part_number_det(id) {
        $('.part_number_detail_' + id).toggle(); // Toggles visibility
    }
</script>
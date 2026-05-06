{{-- @foreach($stocklist as $value)
<tr>
    <td>{{ $value->part_number }}</td>
    <td>{{ $value->description }}</td>
    <td>{{ $value->brand }}</td>
    <td>{{ $value->categoryname }} - {{ $value->subcategoryname }}</td>
    <td>{{ $value->balance_qty }}</td>
    <td class="text-right">{{ number_format($value->avg_price, 2) }}</td>
    <td class="text-right">{{ number_format($value->avg_price * $value->balance_qty, 2) }}</td>
    <td class="text-center">0</td>
</tr>
@endforeach --}}


    <?php try { ?>
@php $count =1; $total_qty=0; $total_price=0; $total_value=0; $total_amount=0; @endphp

                            <?php 
                            if($r_qty == "zero") { $stocklist2 = $stocklist->where('balance_qty',0); }
                            else if($r_qty == "positive") { $stocklist2 = $stocklist->where('balance_qty','>',0); }
                            else if($r_qty == "negative") { $stocklist2 = $stocklist->where('balance_qty','<',0); }
                            else { $stocklist2 = $stocklist; }
                            ?>

                                @foreach($stocklist2 as $value)
                                <?php 
                                    $group_qty = App\SysHelper::get_group_qty($value->partno);
                                ?>
                                @if(($group_qty !=0 && $value->type==2) || $value->type==1)
                                <tr>
                                    <td>
                                        @if ($show_all == 1)
                                            <a href="{{ url('stock-ledger/'.$value->part_number) }}" target="_blank">{{@$value->part_number}}</a>
                                        @else
                                            {{@$value->part_number}}
                                        @endif
                                    </td>
                                    <td><div style="width:250px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">{{$value->description}}</div></td>
                                    <td>{{$value->brand}}</td>
                                    <td>{{$value->categoryname}} - {{$value->subcategoryname}}</td>

                                    @php
                                    $balance_qty = $value->balance_qty;
                                    $balance_qty += $stocklist_return->where('partno',$value->partno)->sum('qty');
                                    
                                    @endphp
                                    
                                    <td>{{$balance_qty}}</td>



                                    @if($show_all == 1)                                    
                                        <?php  $avg = App\SysHelper::get_avg_price($value->partno,$to_date); ?>
                                        <td class="text-right">{{@App\SysHelper::com_curr_format($avg, 2, '.', ',')}}</td>                                        
                                        <td class="text-right">
                                            @if ($balance_qty > 0)
                                                {{@App\SysHelper::com_curr_format(($avg * $balance_qty), 2, '.', ',')}}
                                                {{--  {{@App\SysHelper::com_curr_format(($value->avg_price * $balance_qty), 2, '.', ',')}}  --}}
                                            @else
                                                {{@App\SysHelper::com_curr_format(($avg * 0), 2, '.', ',')}}
                                                {{--  {{@App\SysHelper::com_curr_format(($value->avg_price * 0), 2, '.', ',')}}  --}}
                                            @endif                                            
                                        </td>
                                        
                                        
                                    @php
                                    $total_price += $avg;
                                    if($balance_qty > 0){
                                        $total_amount += ($avg * $balance_qty);
                                    }
                                    @endphp


                                    @else
                                        @if(count($show_brand)>0)
                                            @if(in_array($value->brandid,$show_brand))
                                                <?php  $avg = App\SysHelper::get_avg_price($value->partno,$to_date); ?>
                                                <td class="text-right">{{@App\SysHelper::com_curr_format($avg, 2, '.', ',')}}</td>                                        
                                                <td class="text-right">
                                                    @if ($balance_qty > 0)
                                                        {{@App\SysHelper::com_curr_format(($avg * $balance_qty), 2, '.', ',')}}
                                                        {{--  {{@App\SysHelper::com_curr_format(($value->avg_price * $balance_qty), 2, '.', ',')}}  --}}
                                                    @else
                                                        {{@App\SysHelper::com_curr_format(($avg * 0), 2, '.', ',')}}
                                                        {{--  {{@App\SysHelper::com_curr_format(($value->avg_price * 0), 2, '.', ',')}}  --}}
                                                    @endif
                                                    
                                    @php
                                    $total_price += $avg;
                                    if($balance_qty > 0){
                                        $total_amount += ($avg * $balance_qty);
                                    }
                                    @endphp

                                                </td>
                                            @else
                                                <td class="text-right">0</td>
                                                <td class="text-right">0</td>
                                            @endif
                                        @endif
                                        
                                    @endif

                                    @php 
                                    $total_qty += $balance_qty; @endphp

                                    

                                    
                                    <td class="text-center"><a style="cursor: pointer;" onclick="group_qty({{ $value->partno }},'{{ $value->part_number }}')">{{ $group_qty }}</a></td>
                                </tr>
                                @endif

                                @endforeach 
                                
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
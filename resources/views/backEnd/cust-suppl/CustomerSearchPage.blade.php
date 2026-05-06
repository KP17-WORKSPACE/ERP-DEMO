<div id="pdivdata">
    <div class="table-responsive">
        <table class="table table-hover manage-u-table">
            <thead>
                <tr style="background: #eeeeee; color: #000000;">
                    <th width="10%" style="padding: 7px;">@lang('Code')</th>
                    <th width="30%" style="padding: 7px;">@lang('Customer Name')</th>
                    <th width="15%" style="padding: 7px;">@lang('Contact Person')</th>
                    <th width="15%" style="padding: 7px;">@lang('Mobile')</th>
                    <th width="20%" style="padding: 7px;">@lang('Email')</th>
                    <th width="10%" style="padding: 7px;">@lang('Created Date')</th>
                </tr>
            </thead>

            <tbody>
                @if(isset($data_list) && count($data_list) > 0)
                    @foreach($data_list as $d)
                        <tr style="font-size: 13px; cursor: pointer;" onclick="window.open('{{ url('customers/' . $d->id) }}', '_blank')">
                            <td style="padding: 7px;">{{ $d->code }}</td>
                            <td style="padding: 7px;">{{ $d->name }}</td>
                            <td style="padding: 7px;">{{ $d->contcat_person }}</td>
                            <td style="padding: 7px;">{{ $d->mobile }}</td>
                            <td style="padding: 7px;">{{ $d->email }}</td>
                            <td style="padding: 7px;">{{ date('d/m/Y', strtotime($d->created_at)) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 20px;">
                            @if(isset($cust_name) && strlen($cust_name) >= 5)
                                No customers found matching "{{ $cust_name }}"
                            @else
                                Please enter at least 5 characters to search
                            @endif
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="text-center">
            @if(isset($data_list) && count($data_list) > 0)
                {{ $data_list->appends(['cust_name' => $cust_name])->links() }}
            @endif
        </div>
    </div>
</div>

<script>
    $('#accordionSidebar').addClass('toggled');
    
    // Handle pagination links
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        getData(page);
    });
</script>
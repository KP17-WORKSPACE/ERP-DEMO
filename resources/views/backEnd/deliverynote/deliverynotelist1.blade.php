@extends('backEnd.masterpage')
@section('mainContent')
    @php
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>
    <div class="container-fluid">
        <div class="d-sm-flex justify-content-between">
            <div class="mb-3">
                <h2 class="page-heading m-0">Delivery Note</h2>
                <span class="page-label">Home - Delivery Note</span>
            </div>
            <div>
                <a href="{{ url('delivery-note-add') }}" type="button" class="btn btn-info"><i class="fa fa-plus"></i> New</a>
                <a href="{{ url('delivery-note') }}" type="button" class="btn btn-info"><i class="fa fa-list"></i> List</a>
                <button type="button" class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-filter mr-1"></i>Search</button>
            </div>
        </div>
        <div class="collapse" id="collapseExample">
            <div class="card shadow mb-4 p-4">
                
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'delivery-note', 'method' => 'get', 'id' => 'delivery-note-search']) }}
                <div class="row">
    
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Documents Number</label>
                        <input class="form-control" type="text" autocomplete="off" name="documents_number" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Customer</label>
                            <select class="form-control js-account-select" name="customer" id="customer">
                                <option value=""></option>
                                {{-- @foreach ($customer_list as $value)
                                    <option value="{{ @$value->id }}" >{{ @$value->account_name }}
                                    </option>
                                @endforeach --}}
                            </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Supplier</label>
                        <input class="form-control" type="text" autocomplete="off" name="supplier" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Deal ID</label>
                        <input class="form-control" type="text" autocomplete="off" name="deal_number" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Sales Invoice Number</label>
                        <input class="form-control" type="text" autocomplete="off" name="sales_invoice_number" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">SRT Number</label>
                        <input class="form-control" type="text" autocomplete="off" name="srt" value="">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="" class="form-check-label">Date</label>
                        <input class="form-control" type="date" autocomplete="off" name="date" value="">
                    </div>
    
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning mr-2" id="btnSubmit">Clear</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Filter</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            @if(session()->has('message-success') != "" ||
                             session()->get('message-danger') != "")
                             <tr>
                                 <td colspan="11">
                                      @if(session()->has('message-success'))
                                       <div class="alert alert-success">
                                           {{ session()->get('message-success') }}
                                       </div>
                                     @elseif(session()->has('message-danger'))
                                       <div class="alert alert-danger">
                                           {{ session()->get('message-danger') }}
                                       </div>
                                     @endif
                                 </td>
                             </tr>
                              @endif 
                             <tr>
                                <th>@lang('DN Date')</th>
                                 <th>@lang('DN No')</th>
                                 <th>@lang('Customer')</th>
                                 <th>@lang('Supplier')</th>
                                 <th>@lang('SIV No')</th>
                                 <th>@lang('SRT No')</th>
                                 <th>@lang('Deal ID')</th>
                                 <th>@lang('Currency')</th>
                                 <th class="text-right">@lang('Amount')</th>
                                 <th>@lang('Att')</th>
                                 <th class="text-right">@lang('lang.action')</th>
                             </tr>
                         </thead>
                         <tbody>
                             @php $count =1; @endphp
                             @foreach($deliverynote as $value)
                             
                        @if($pending_si==1)
                        @if (empty($value->invoice_no))
                        
                        <tr @if (@$value->status == 2) class="bg-dark" @endif>
                            <td>{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                             <td><a href="{{url('delivery-note/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                             <td>{{@$value->accountname->account_name}}</td>
                             <td>{{@$value->supplier_name}}</td>
                             
                             <!-- Sales Invoice Numbers -->
                            <td>
                                <span class="text-warning">Pending</span>
                            </td>

                            <!-- Sales Return Numbers -->
                            <td>
                                @if (empty($value->srtno))
                                <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', $value->srtno) as $srt)
                                        <a href="{{ url('get-url-sales-return/' . trim($srt)) }}" target="_blank">{{ trim($srt) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>

                            <!-- Deal Codes -->
                            <td>
                                @if (empty($value->code))
                                <span class="text-warning">Pending</span>
                                @else
                                    @foreach (explode(',', $value->code) as $code)
                                        <a href="{{ url('get-url-deal-track/' . trim($code)) }}" target="_blank">{{ trim($code) }}</a>@if (!$loop->last), @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ @$value->currency_name->code }}</td>
                             <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->amount,2,'.',',') }}</td>
                             <td>
                                @if (empty(@$value->attach))
                                    
                                @else
                                    @foreach (explode(',', @$value->attach) as $att)
                                        <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                    @endforeach
                                @endif
                            </td>
                             <td class="text-right">
                                @if (in_array(session('logged_session_data.company_id') ?? null, [2, 3, 5, 6]))
                                    <a class="btn-sm btn-primary" href="{{url('delivery-note/'.$value->id.'/download/t')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>
                                @else
                                    <a class="btn-sm btn-primary" href="{{url('delivery-note/'.$value->id.'/download')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>
                                @endif
                                <a class="btn-sm btn-info" href="{{url('delivery-note/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                @if (@$value->status == 2)
                                <a class="btn-sm btn-warning" href="{{url('delivery-note/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                @else
                                <a class="btn-sm btn-danger" href="{{url('delivery-note/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif
                             </td>
                         </tr>
                         @endif

                        @else
                             <tr @if (@$value->status == 2) class="bg-dark" @endif>
                                <td>{{date('d/m/Y', strtotime(@$value->doc_date))}}</td>
                                 <td><a href="{{url('delivery-note/'.$value->id.'/view')}}" target="_blank">{{@$value->doc_number}}</a></td>
                                 <td>{{@$value->accountname->account_name}}</td>
                                 <td>{{@$value->supplier_name}}</td>
                                 
                                 <!-- Sales Invoice Numbers -->
                                <td>
                                    @if (empty($value->invoice_no))
                                    <span class="text-warning">Pending</span>
                                    @else
                                        @foreach (explode(',', $value->invoice_no) as $inv)
                                            <a href="{{ url('get-url-sales-invoice/' . trim($inv)) }}" target="_blank">{{ trim($inv) }}</a>@if (!$loop->last), @endif
                                        @endforeach
                                    @endif
                                </td>

                                <!-- Sales Return Numbers -->
                                <td>
                                    @if (empty($value->srtno))
                                    <span class="text-warning">Pending</span>
                                    @else
                                        @foreach (explode(',', $value->srtno) as $srt)
                                            <a href="{{ url('get-url-sales-return/' . trim($srt)) }}" target="_blank">{{ trim($srt) }}</a>@if (!$loop->last), @endif
                                        @endforeach
                                    @endif
                                </td>

                                <!-- Deal Codes -->
                                <td>
                                    @if (empty($value->code))
                                    <span class="text-warning">Pending</span>
                                    @else
                                        @foreach (explode(',', $value->code) as $code)
                                            <a href="{{ url('get-url-deal-track/' . trim($code)) }}" target="_blank">{{ trim($code) }}</a>@if (!$loop->last), @endif
                                        @endforeach
                                    @endif
                                </td>
                                <td>{{ @$value->currency_name->code }}</td>
                                 <td class="text-right">{{ @App\SysHelper::com_curr_format(@$value->amount,2,'.',',') }}</td>
                                 <td>
                                    @if (empty(@$value->attach))
                                        
                                    @else
                                        @foreach (explode(',', @$value->attach) as $att)
                                            <a href="{{ url(trim($att)) }}" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i></a>&nbsp;
                                        @endforeach
                                    @endif
                                </td>
                                 <td class="text-right">
                                    @if (in_array(session('logged_session_data.company_id') ?? null, [2, 3, 5, 6]))
                                        <a class="btn-sm btn-primary" href="{{url('delivery-note/'.$value->id.'/download/t')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>
                                    @else
                                        <a class="btn-sm btn-primary" href="{{url('delivery-note/'.$value->id.'/download')}}" class="btn-small"><i class="fa fa-download" aria-hidden="true"></i></a>
                                    @endif
                                    <a class="btn-sm btn-info" href="{{url('delivery-note/'.$value->id.'/edit')}}"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                    @if (@$value->status == 2)
                                    <a class="btn-sm btn-warning" href="{{url('delivery-note/'.$value->id.'/restore')}}" onclick="return confirm('Are you sure you want to restore this item?');"><i class="fa fa-undo" aria-hidden="true"></i></a>
                                    @else
                                    <a class="btn-sm btn-danger" href="{{url('delivery-note/'.$value->id.'/delete')}}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    @endif
                                 </td>
                             </tr>
                            @endif

                             @endforeach
                         </tbody>
                         <footer>
                             <tr>
                                 <td colspan="11">
                                     {{ $deliverynote->appends(request()->input())->links() }}
                                 </td>
                             </tr>
                         </footer>
                    </table>
                </div>
            </div>
        </div>
    
    </div>
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>
    <script>
$(document).ready(function () {
    function initAccountSelect2(selector) {
        $(selector).select2({
            ajax: {
                url: '{{ route("autocomplete.get_cust_account_list_ajax") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search_text: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (item) {
                            return {
                                id: item.id,
                                text: item.account_code + ' - ' + item.account_name
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Select Account',
            minimumInputLength: 2
        });
    }

    // Initial init
    initAccountSelect2('.js-account-select');

    // Re-initialize on focus (if needed for dynamically added fields)
    $(document).on('focus', '.js-account-select', function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
            initAccountSelect2(this);
        }
    });

    // Open dropdown and focus search box on click
    $(document).on('click', '.js-account-select', function () {
        $(this).select2('open');
    });

    // Focus the search input inside the opened Select2 dropdown
    $(document).on('select2:open', function () {
        setTimeout(function () {
            const searchInput = document.querySelector('.select2-container--open .select2-search__field');
            if (searchInput) {
                searchInput.focus();
            }
        }, 0);
    });
});
</script>
@endsection
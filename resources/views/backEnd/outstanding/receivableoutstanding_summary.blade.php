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
                <h2 class="page-heading m-0">Receivable Outstanding Summary</h2>
                <span class="page-label">Home - Receivable Outstanding Summary</span>
            </div>
        </div>
        
        <div class="card shadow mb-4 p-4">
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <table class="table table-bordered table-striped" id="dataTable_exe" width="100%" cellspacing="0">
                        <thead>
                            @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                <tr>
                                    <td colspan="6">
                                        @if (session()->has('message-success-delete'))
                                            <div class="alert alert-success">
                                                {{ session()->get('message-success-delete') }}
                                            </div>
                                        @elseif(session()->has('message-danger-delete'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger-delete') }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <th>@lang('Total Receivable')</th>
                                <th class="text-right">@lang('Total Amount')</th>
                                <th class="text-right">@lang('Due')</th>
                                <th class="text-right">@lang('Over Due')</th>
                                <th class="text-right">@lang('Overdue 0-30')</th>
                                <th class="text-right"> @lang('Overdue 31-60')</th>
                                <th class="text-right"> @lang('Overdue 61-90')</th>
                                <th class="text-right">@lang('Overdue >90')</th>
                                <th class="text-right"></th>
                            </tr>
                        </thead>
        
                        <tbody>
                            <tr>
                                <td>{{ $os_det[1] }}</td>
                                <td class="text-right">{{ $os_det[0] }}</td>                                
                                <td class="text-right">{{ $os_det[2] }}</td>
                                <td class="text-right">{{ $os_det[4] }}</td>
                                <td class="text-right">{{ $due_by_det[0] }}</td>
                                <td class="text-right">{{ $due_by_det[2] }}</td>
                                <td class="text-right">{{ $due_by_det[4] }}</td>
                                <td class="text-right">{{ $due_by_det[6] }}</td>
                                <td class="text-right"><a class="btn-sm btn-success" href="{{ url('receivable-outstanding') }}">View Detail</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
        <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

        
@endsection



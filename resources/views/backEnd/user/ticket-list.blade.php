@extends('backEnd.master')
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.ticket_list')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="{{ route('user.ticket') }}">@lang('lang.ticket_system')</a>
                <a href="#">@lang('lang.ticket_list')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
            <div class="row justify-content-between p-3">
                    <div class="bc-pages">

                            <a href="{{ route('user.active_ticket') }}" id="active" class="primary-btn small fix-gr-bg">
                                    @lang('lang.active') @lang('lang.ticket_system')
                             </a>
                            <a href="{{ route('user.completed_ticket') }}" id="complete" class="primary-btn small fix-gr-bg">
                                    @lang('lang.completed') @lang('lang.ticket_system')
                             </a>
                            <a href="{{ route('user.ticket') }}" id="all" class="primary-btn small fix-gr-bg" class="d-none">
                                    @lang('lang.all') @lang('lang.ticket_system')
                             </a>

                    </div>
                    <div class="bc-pages">
                            <a href="{{ route('user.add_ticket') }}" class="primary-btn small fix-gr-bg">
                                    <span class="ti-plus pr-2"></span>
                                    @lang('lang.add')
                                </a>
                    </div>
            </div>
      
        <div class="row">

            <div class="col-lg-12">
                
          <div class="row">
            <div class="col-lg-4 no-gutters mt-2">
                <div class="main-title">
                    <h3 class="mb-0"> @lang('lang.ticket_list')</h3>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-12">
                <table id="table_id" class="display school-table pl-2" cellspacing="0" width="100%">

                    <thead>
                        <div class="p-3">
                            @if(session()->has('message-success'))
                                <div class="alert alert-success">
                                    {{ session()->get('message-success') }}
                                </div>
                                @elseif(session()->has('message-danger'))
                                <div class="alert alert-danger">
                                    {{ session()->get('message-danger') }}
                                </div>   
                                @endif
                            </div>            
                        @if(session()->has('message-success-delete') != "" ||
                                session()->get('message-danger-delete') != "")
                                <tr>
                                    <td colspan="2">
                                         @if(session()->has('message-success-delete'))
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
                        <tr >
                            <th width="16%">@lang('lang.subject')</th>
                            <th width="16%">@lang('lang.category')</th>
                            <th width="16%">@lang('lang.user') @lang('lang.name')</th>
                            <th width="16%">@lang('lang.ticket_priority')</th>
                            <th width="16%">@lang('lang.user_agent')</th>
                            <th width="10%">@lang('lang.Status')</th>
                            <th width="16%">@lang('lang.actions')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @if(isset($ticket))
                        @foreach($ticket as $value)
                        <tr>

                            <td>{{str_limit(@$value->subject,35)}}</td>
                            <td>{{@$value->category->name}}</td>
                            <td>{{$value->user->username}}</td>
                            <td>{{@$value->priority->name}}</td>
                            <td>{{@$value->agent_user?@$value->agent_user->username:'Not assign yet !'}}</td>
                            @if (@$value->active_status == 0)
                            <td>Pending</td>
                            @endif
                            @if (@$value->active_status == 1)
                            <td>Ongoing</td>
                            @endif
                            @if (@$value->active_status == 2)
                            <td>Complete</td>
                            @endif
                            @if (@$value->active_status == 3)
                            <td>Close</td>
                            @endif
                            <td>
                                <div class="row">
                                <div class="col-sm-6">

                                <div class="dropdown">
                                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                        @lang('lang.select')
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
 
                                

                                        <a class="dropdown-item" href="{{ route('user.ticket_view',@$value->id)}}"> @lang('lang.view')</a>
                                        <a class="dropdown-item" href="{{ route('user.ticket_edit',@$value->id)}}"> @lang('lang.edit')</a>
                                        <a class="deleteUrl dropdown-item" data-modal-size="modal-md" title="Delete Ticket" href="{{ route('user.ticket_delete_view',@$value->id)}}"> @lang('lang.delete')</a>

                                        

                                    </div>
                                </div>
                            </div>
                        </div>
                            </td>
                        </tr>

                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
</section>
@endsection
@section('script')
 
@endsection

@extends('backEnd.master')
@section('mainContent')
@php 
    $modules = [];
    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();

 
    foreach($permissions as $permission){ @$module_links[] = @$permission->module_link_id; @$modules[] = @$permission->moduleLink->module_id;}

 
    $modules = array_unique(@$modules);




    $generalSetting=App\SmGeneralSettings::where('id',1)->first();
    $currency_symbol = @$generalSetting->currency_symbol; 
    if(isset($generalSetting->logo)){  @$logo = @$generalSetting->logo;  }
    else{ @$logo = 'public/uploads/settings/logo.png'; } 
 
@endphp
<link href="{{asset('public/css/add_loan.css')}}" type="text/css" rel="stylesheet">



<section class="sms-breadcrumb mb-40 white-box">
  <div class="container-fluid">
    <div class="row justify-content-between">
      <h1>@lang('lang.cash_issue') @lang('lang.list')</h1>
      <div class="bc-pages">
        <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
        <a href="#">@lang('lang.inventory')</a>
        <a href="#">@lang('lang.cash_issue') @lang('lang.list')</a>
      </div>
    </div>
  </div>
</section>
<section class="admin-visitor-area">
  <div class="container-fluid p-0">
    <div class="row">
      <div class="col-lg-3">
        <div class="row">
          <div class="col-lg-12">
            <div class="main-title">
              <h3 class="mb-30">
                  @lang('lang.add') @lang('lang.new')
              </h3>
            </div>
            @if(isset($editData))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'cash-issue-update', 'enctype' => 'multipart/form-data']) }}
            @else
            @if(in_array(292, @$modules) || Auth::user()->role_id != 7)
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'cash-issue-store',
            'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            @endif
            @endif
            <div class="white-box">
              <div class="add-visitor">
                <div class="row">
                  @if(session()->has('message-success'))
                  <div class="alert alert-success">
                    {{ session()->get('message-success') }}
                  </div>
                  @elseif(session()->has('message-danger'))
                  <div class="alert alert-danger">
                    {{ session()->get('message-danger') }}
                  </div>
                  @endif

              

                  <div class="col-lg-12 mb-30">
                    <select class="niceSelect w-100 bb form-control{{ $errors->has('staff') ? ' is-invalid' : '' }}" name="staff">
                      <option data-display="@lang('lang.issue_to') *" value="">@lang('lang.issue_to') *</option>

                      @foreach($staffs as $value)
                      <option value="{{@$value->id}}">{{@$value->full_name}}</option>
                      @endforeach
                    </select>
                    @if ($errors->has('staff'))
                    <span class="invalid-feedback invalid-select" role="alert">
                      <strong>{{ $errors->first('staff') }}</strong>
                    </span>
                    @endif
                  </div>

                </div>

                <div class="row no-gutters input-right-icon mb-30 w-100">

                  <div class="col">
                    <div class="input-effect">
                      <input class="primary-input date form-control{{ $errors->has('issue_date') ? ' is-invalid' : '' }}" id="startDate" type="text"
                      name="issue_date" value="{{isset($editData)? date('m/d/Y', strtotime(@$editData->issue_date)): date('m/d/Y')}}">
                      <label>@lang('lang.issue_date') <span>*</span> </label>
                      <span class="focus-border"></span>
                      @if ($errors->has('issue_date'))
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('issue_date') }}</strong>
                      </span>
                      @endif
                    </div>

                  </div>
                  <div class="col-auto">
                    <button class="" type="button">
                      <i class="ti-calendar" id="start-date-icon"></i>
                    </button>
                  </div>
                </div>

                <div class="row no-gutters input-right-icon mb-30 w-100">

                  <div class="col">
                    <div class="input-effect">
                      <input class="primary-input date form-control{{ $errors->has('return_date') ? ' is-invalid' : '' }}" id="endDate" type="text"
                      name="return_date" value="{{date('m/d/Y')}}">
                      <label>@lang('lang.return_date') <span>*</span> </label>
                      <span class="focus-border"></span>
                      @if ($errors->has('return_date'))
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('return_date') }}</strong>
                      </span>
                      @endif
                    </div>

                  </div>
                  <div class="col-auto">
                    <button class="" type="button">
                      <i class="ti-calendar" id="end-date-icon"></i>
                    </button>
                  </div>
                </div>

                <div class="row">
                

                  <div class="col-lg-12 mb-30">
                    <div class="input-effect">
                      <input class="primary-input form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}"
                      type="number" name="amount" autocomplete="off" value="{{old('amount')}}">
                      <label>@lang('lang.amount') <span>*</span> </label>
                      <span class="focus-border"></span>
                      @if ($errors->has('amount'))
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('amount') }}</strong>
                      </span>
                      @endif
                    </div>
                  </div>
                  <div class="col-lg-12 mb-30">
                    <div class="input-effect">
                      <textarea class="primary-input form-control" cols="0" rows="4" name="note" id="note">{{isset($editData)? @$editData->note:old('note')}}</textarea>
                      <label>@lang('lang.note') <span></span> </label>
                      <span class="focus-border textarea"></span>

                    </div>
                  </div>
                </div>
                @php 
                    $tooltip = "";
                     if(in_array(292, @$module_links) ||  Auth::user()->role_id == 1){
                          $tooltip = "";
                      }else{
                          $tooltip = "You have no permission to add";
                      }
                  @endphp
                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                <div class="row mt-40">
                  <div class="col-lg-12 text-center">
                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{$tooltip}}">
                      <span class="ti-check"></span>
                        @if(isset($editData))
                            @lang('lang.update')
                        @else
                        
                            @lang('lang.save')
                        @endif
                    </button>
                  </div>
                </div>
              </div>
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>

      <div class="col-lg-9">
      
      <div class="row">
        <div class="col-lg-4 no-gutters">
          <div class="main-title">
            <h3 class="mb-0"> @lang('lang.cash_issue') @lang('lang.list')</h3>
          </div>
        </div>
      </div>

      <div class="row">

        <div class="col-lg-12">
          <table id="table_id" class="display school-table" cellspacing="0" width="100%">

            <thead>
              @if(session()->has('message-success-delete') != "" ||
              session()->get('message-danger-delete') != "")
              <tr>
                  <td colspan="6">
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
              <tr>
                <th> @lang('lang.issue_to')</th>
                <th> @lang('lang.issue_date')</th>
                <th> @lang('lang.return_date')</th>
                <th> @lang('lang.amount') ({{$currency_symbol}})</th>
                <th> @lang('lang.Status')</th>
                <th> @lang('lang.action')</th>
              </tr>
            </thead>

            <tbody>
              @foreach($cash_issues as $value)
              <tr>

                

                <td> {{@$value->staffDetails->full_name}} </td>

                  <td>{{@$value->issue_date != ""? date('jS M, Y', strtotime(@$value->issue_date)):''}}</td>
                  <td>{{@$value->return_date != ""? date('jS M, Y', strtotime(@$value->return_date)):''}}</td> 

                  <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->amount)}}</td>
                  <td> 
                      @if(@$value->is_return == 0)
                     <button class="primary-btn small bg-success text-white border-0"> @lang('lang.issued')</button>
                     @else
                      <button class="primary-btn small bg-primary text-white border-0">@lang('lang.returned')</button>
                     @endif
                  </td>

                   <td>
                   @if($value->is_return == 0)
                    <div class="dropdown">
                      <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                          @lang('lang.select')
                      </button>

                      <div class="dropdown-menu dropdown-menu-right">
                        @if(in_array(293, @$modules) || Auth::user()->role_id != 7)
                       <a class="dropdown-item modalLink" title="Return amount" data-modal-size="modal-md" href="{{url('return-cash-view/'.@$value->id)}}">@lang('lang.return')</a>
                       @endif
                    </div>

                   </div>
                   @endif
                 </td>
               </tr>
               @endforeach
             </tbody>
           </table>
         </div>
       </div>
     </div>
   </div>
 </div>
</section>
@endsection

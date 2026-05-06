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
      <h1>@lang('lang.advance_salary')/@lang('lang.loan')</h1>
      <div class="bc-pages">
        <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
        <a href="#">@lang('lang.inventory')</a>
        <a href="#">@lang('lang.advance_salary')/@lang('lang.loan')</a>
      </div>
    </div>
  </div>
</section>

<section class="admin-visitor-area">
  <div class="container-fluid p-0">
    <div class="row">
      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-3">
            <div class="main-title">
              <h3 class="mb-30">
                @if(isset($editData))
                  @lang('lang.edit')
                  @else
                  @lang('lang.add')
                  @endif

                  @lang('lang.loan')
              </h3>
            </div>
            @if(isset($editData))
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'loan-update', 'enctype' => 'multipart/form-data']) }}
            @else
                @if(in_array(264, @$module_links) ||  Auth::user()->role_id == 1)
            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'loan-store', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            @endif

            @endif
            <input type="hidden" name="id" value="{{isset($editData)? @$editData->id:''}}">
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

              

                  <div class="col-lg-12 mb-50">
                    <select class="niceSelect w-100 bb form-control{{ $errors->has('staff') ? ' is-invalid' : '' }}" name="staff">
                      <option data-display="@lang('lang.issue_to') *" value="">@lang('lang.issue_to') *</option>

                      @foreach($staffs as $value)
                      <option value="{{@$value->id}}" {{isset($editData)? (@$editData->staff_id == @$value->id? 'selected':''):""}}>{{@$value->full_name}}</option>
                      @endforeach
                    </select>
                    @if ($errors->has('staff'))
                    <span class="invalid-feedback invalid-select" role="alert">
                      <strong>{{ $errors->first('staff') }}</strong>
                    </span>
                    @endif
                  </div>

                  <div class="col-lg-12 mb-30">
                    <div class="row no-gutters input-right-icon mb-30 w-100">

                  <div class="col">
                    <div class="input-effect">
                      <input class="primary-input date form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" id="startDate" type="text"
                      name="date" value="{{isset($editData)? date('m/d/Y', strtotime(@$editData->date)): date('m/d/Y')}}">
                      <label>@lang('lang.date') <span>*</span> </label>
                      <span class="focus-border"></span>
                      @if ($errors->has('date'))
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('date') }}</strong>
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
                  </div>

                </div>

                <div class="row">
                

                  <div class="col-lg-12 mb-30">
                    <div class="input-effect">
                      <input class="primary-input form-control{{ $errors->has('amount') ? ' is-invalid' : '' }}"
                      type="number" name="amount" autocomplete="off" value="{{isset($editData)? @$editData->amount: old('amount')}}">
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

                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">


                @php 
                  $tooltip = "";

                  if(in_array(264, @$module_links) ||  Auth::user()->role_id == 1){
                    $tooltip = "";
                  }else{
                    $tooltip = "You have no permission to add";
                  }


                @endphp
                
               
                <div class="row mt-40">
                  <div class="col-lg-12 text-center">
                    <button class="primary-btn fix-gr-bg" data-toggle="tooltip" title="{{@$tooltip}}">
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


          <div class="col-lg-9">
            <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-0">@lang('lang.loan') @lang('lang.list')</h3>
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
                                    <td colspan="5">
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
                                    <th>@lang('lang.issue_to')</th>
                                    <th>@lang('lang.date')</th>
                                    <th>@lang('lang.note')</th>
                                    <th>@lang('lang.amount') ({{@$currency_symbol}})</th>
                                    <th>@lang('lang.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loan_lists as $value)
                                <tr>
                                    <td>{{@$value->staffDetails != ""? @$value->staffDetails->full_name:''}}</td>
                                    <td>{{date('jS M, Y', strtotime(@$value->date))}}</td>
                                    <td>{{@$value->note}}</td>
                                    <td>{{App\User::NumberToBangladeshiTakaFormat(@$value->amount)}}</td>
                                    
                                    <td valign="top">
                                        <div class="dropdown">
                                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                                @lang('lang.select')
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                              @if(in_array(265, @$module_links) ||  Auth::user()->role_id == 1)
                                                <a class="dropdown-item" href="{{url('loan-edit', [@$value->id])}}">@lang('lang.edit')</a>
                                              @endif
                                              @if(in_array(266, @$module_links) ||  Auth::user()->role_id == 1)
                                                <a class="dropdown-item" data-toggle="modal" data-target="#deleteLoanModal{{@$value->id}}"  href="{{route('class_delete', [@$value->id])}}">@lang('lang.delete')</a>
                                              @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                  <div class="modal fade admin-query" id="deleteLoanModal{{@$value->id}}" >
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">@lang('lang.delete') @lang('lang.loan')</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="text-center">
                                                    <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                </div>

                                                <div class="mt-40 d-flex justify-content-between">
                                                    <button type="button" class="primary-btn tr-bg" data-dismiss="modal">@lang('lang.cancel')</button>
                                                    <a href="{{url('loan-delete', [@$value->id])}}" class="text-light">
                                                    <button class="primary-btn fix-gr-bg" type="submit">@lang('lang.delete')</button>
                                                     </a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
          </div>
        </div>
      </div>
 </div>
</section>
@endsection

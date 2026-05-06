@extends('backEnd.master')
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.update_system')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.system_settings')</a>
                <a href="#">@lang('lang.update_system')</a>

            </div>
        </div>
    </div>
</section>

<section class="admin-visitor-area">
    <div class="container-fluid p-0">
        @if(isset($edit_languages))
        <div class="row">
            <div class="offset-lg-10 col-lg-2 text-right col-md-12 mb-20">
                <a href="{{url('marks-grade')}}" class="primary-btn small fix-gr-bg">
                    <span class="ti-plus pr-2"></span>
                    @lang('lang.add')
                </a>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-lg-3">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="main-title">
                            <h3 class="mb-30">@lang('lang.System_Status') </h3>
                        </div> 
                        <div class="row">
                            <table class="display school-table school-table-style" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>  @lang('lang.Existing') @lang('lang.Version')</th>
                                        <th>  @lang('lang.Available') @lang('lang.Version')</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$existing->system_version}}</td>
                                        <td>{{$version_number}}</td>
                                    </tr>
                                </tbody>
                            </table>  
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-4 no-gutters">
                        <div class="main-title">
                            <h3 class="mb-30">@lang('lang.Version') @lang('lang.list')</h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">  

                        <table class="display school-table school-table-style" cellspacing="0" width="100%">


                            <thead>
                                @if(session()->has('message-success') != "" ||
                                session()->get('message-danger-delete') != "")
                                <tr>
                                    <td colspan="3">
                                        @if(session()->has('message-success'))
                                        <div class="alert alert-success">
                                            {{ session()->get('message-success') }}
                                        </div> 
                                        @endif
                                    </td>
                                </tr>
                                @endif
                                <tr>  
                                    <th> @lang('lang.Available') @lang('lang.Version') </th>  
                                    <th>@lang('lang.New_Features')</th>
                                    <th>@lang('lang.Alert')</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $i=0; ?>  

                                <tr> 
                                    <td rowspan="{{count($versions)}}">{{$version_number}}</td> 
                                    <td>
                                        @foreach($versions as $version)
                                        <li>{{$version->features}}</li>
                                        @endforeach
                                    </td>   
                                    <td>
                                        @foreach($versions as $version)
                                            @if(!empty($version->note))
                                                <li>{{$version->note}}</li>
                                            @endif
                                        @endforeach
                                    </td>    
                                </tr>  

                                    
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">
                                        
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'upgrade-settings', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                    <input type="hidden" name="version" value="{{$version_number}}"> 

                        <div class="row mt-40">
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="primary-btn fix-gr-bg isDisabled"  data-toggle="tooltip" title="Disabled for demo version"> 
                                    <span class="ti-check"></span>
                                    @lang('lang.Upgrade') 
                                </button>
                            </div>
                        </div>

                    {{ Form::close() }}

                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

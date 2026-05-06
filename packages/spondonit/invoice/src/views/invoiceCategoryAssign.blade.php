@extends('backEnd.master')
@section('mainContent')
<section class="sms-breadcrumb mb-40 white-box">
    <div class="container-fluid">
        <div class="row justify-content-between">
            <h1>@lang('lang.invoice') @lang('lang.permission')</h1>
            <div class="bc-pages">
                <a href="{{url('dashboard')}}">@lang('lang.dashboard')</a>
                <a href="#">@lang('lang.invoice')</a>
                <a href="{{url('infix/invoice-category')}}">@lang('lang.invoice') @lang('lang.category')</a>
                <a href="#">@lang('lang.invoice') @lang('lang.permission')</a>
            </div>
        </div>
    </div>
</section>
<section class="admin-visitor-area">
    <div class="container-fluid p-0">
            <div class="row mt-40">
                <div class="col-lg-12">
                    {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'infix/invoice-permission-store', 'method' => 'POST']) }}
                    <input type="hidden" name="category_id" value="{{@$category->id}}">
                    <div class="row">
                        <div class="col-lg-12 base-setup role-permission">
                            <table id="school-table-style" class="display school-table-style" cellspacing="0" width="100%">
                                <thead>
                                    @if(session()->has('message-danger') != "")
                                    <tr>
                                        <td colspan="2">
                                            @if(session()->has('message-danger'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger') }}
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td colspan="2">
                                            <div class="text-center">
                                                <h2>@lang('lang.invoice') @lang('lang.Field') @lang('lang.Permission_For') {{@$category->name}}</h2>
                                            </div>
                                        </td>
                                    </tr>
                                </thead>

                                <tbody>

                                    <tr>
                                    <td>
                                        <div class="row">
                                            <div class="offset-md-3 col-md-9">
                                                <div class="row">
                                                @foreach($links as $link)
                                                <div class="col-md-6">

                                       
                                                    <input type="checkbox" id="permissions{{@$link->id}}" class="common-checkbox" name="permissions[]" value="{{@$link->id}}"
                                                {{in_array(@$link->id, @$assigned_ids)? 'checked':''}}>
                                                    <label for="permissions{{@$link->id}}">{{@$link->name}}</label>
                                                </div>

                                                @endforeach
                                            </div>
                                                </div>
                                        </div>
                                    </td>
                                </tr>


                                    <tr>
                                        <td>
                                            <div class="col-lg-12 mt-20 text-right">
                                                <button type="submit" class="primary-btn fix-gr-bg">
                                                    <span class="ti-check"></span>
                                                    @lang('lang.save')
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </section>
            


@endsection

@section('script')
<script type="text/javascript">

    

</script>

@endsection


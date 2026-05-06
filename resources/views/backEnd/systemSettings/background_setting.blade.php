@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
    <?php try { ?>

    <div class="content-container col-12">
        <div class="tab-content display-flex-tabs" id="purchaseOrderTabContent">
            <div class="" role="tabpanel" aria-labelledby="data-tab" id="data-details">
                <div class="purchase-order-content-header">
                    <h4 class="purchase-order-content-header-left">
                        Background Settings
                    </h4>
                    {{-- <div class="purchase-order-content-header-right">
                    <a class="btn btn-light" href="{{url('payment-add')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Add Payment
                    </a>
                </div> --}}
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="main-title">
                                            <h4 class="mb-30">
                                                @lang('lang.add') @lang('lang.style')
                                            </h4>
                                        </div>
                                        @if (isset($visitor))
                                            {{ Form::open([
                                                'class' => 'form-horizontal',
                                                'files' => true,
                                                'url' => 'background-settings-update',
                                                'method' => 'POST',
                                                'enctype' => 'multipart/form-data',
                                            ]) }}
                                        @else
                                            {{ Form::open([
                                                'class' => 'form-horizontal',
                                                'files' => true,
                                                'url' => 'background-settings-store',
                                                'method' => 'POST',
                                                'enctype' => 'multipart/form-data',
                                            ]) }}
                                        @endif
                                        <div class="white-box">
                                            <div class="add-visitor">


                                                @if (session()->has('message-success'))
                                                    <div class="alert alert-success">
                                                        @lang('lang.inserted_message')
                                                    </div>
                                                @elseif(session()->has('message-danger'))
                                                    <div class="alert alert-danger">
                                                        @lang('lang.error_message')
                                                    </div>
                                                @endif

                                                @if ($errors->any())
                                                    @foreach ($errors->all() as $error)
                                                        <div class="alert alert-danger">
                                                            {{ $error }}
                                                        </div>
                                                    @endforeach
                                                @endif



                                                <div class="row mt-3">
                                                    <div class="col-lg-12">
                                                        <label class="form-label">@lang('Select Position')<span>*</span></label>

                                                        <select
                                                            class="niceSelect w-100 bb form-control{{ $errors->has('style') ? ' is-invalid' : '' }}"
                                                            name="style" id="style">

                                                            <option value="1">Dashboard Background</option>
                                                            <option value="2">Login Page Background</option>
                                                        </select>
                                                        @if ($errors->has('style'))
                                                            <span class="invalid-feedback invalid-select" role="alert">
                                                                <strong>{{ $errors->first('style') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>




                                                <div class="row mt-3">
                                                    <div class="col-lg-12">
                                                        <label class="form-label">@lang('Background Type')<span>*</span></label>

                                                        <select
                                                            class="niceSelect w-100 bb form-control {{ $errors->has('background_type') ? ' is-invalid' : '' }}"
                                                            name="background_type" id="background-type">

                                                            <option value="color">Color</option>
                                                            <option value="image">Image (1920x1400)</option>
                                                        </select>
                                                        @if ($errors->has('background_type'))
                                                            <span class="invalid-feedback invalid-select" role="alert">
                                                                <strong>{{ $errors->first('background_type') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>



                                                <div class="row mt-3 mb-3" id="background-color">
                                                    <div class="col-lg-12">
                                                        <label class="form-label">@lang('lang.color')<span>*</span></label>
                                                        <div class="input-effect">
                                                            <input
                                                                class="form-control {{ $errors->has('color') ? ' is-invalid' : '' }}"
                                                                type="color" name="color" autocomplete="off"
                                                                value="{{ isset($visitor) ? $visitor->purpose : old('color') }}">
                                                            <input type="hidden" name="id"
                                                                value="{{ isset($visitor) ? $visitor->id : '' }}">

                                                            <span class="focus-border"></span>
                                                            @if ($errors->has('color'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('color') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>






                                                <div class="row no-gutters input-right-icon mt-35" id="background-image">
                                                    <div class="col">
                                                        <div class="input-effect">

                                                            <input type="file" class="form-control" id="browseFile"
                                                                name="image">

                                                        </div>
                                                    </div>

                                                </div>





                                                <div class="row mt-3">
                                                    <div class="col-lg-12 text-center">
                                                        <button class="btn-sm btn-light btn fix-gr-bg">
                                                            <span class="ti-check"></span>
                                                            @if (isset($visitor))
                                                                <i
                                                                    class="ico icon-outline-bookmark-opened text-success"></i>
                                                                @lang('lang.update')
                                                            @else
                                                                <i
                                                                    class="ico icon-outline-bookmark-opened text-success"></i>
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
                                            <h4 class="mb-0">@lang('lang.view')</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">

                                        <table id="long-list" class="table table-hover" cellspacing="0" width="100%">

                                            <thead>
                                                @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                                    <tr>
                                                        <td colspan="4">
                                                            @if (session()->has('message-success-delete'))
                                                                <div class="alert alert-success">
                                                                    @lang('lang.deleted_message')
                                                                </div>
                                                            @elseif(session()->has('message-danger-delete'))
                                                                <div class="alert alert-danger">
                                                                    @lang('lang.error_message')
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th>@lang('lang.title')</th>
                                                    <th>@lang('lang.type')</th>
                                                    <th>@lang('lang.background') @lang('lang.image')</th>
                                                    <th>@lang('lang.status')</th>
                                                    <th>@lang('lang.action')</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($background_settings as $background_setting)
                                                    @php
                                                        $style =
                                                            'width: 200px; height: 100px;background-color:' .
                                                            @$background_setting->color;
                                                    @endphp

                                                    <tr>
                                                        <td>{{ @$background_setting->title }}</td>
                                                        <td>
                                                            <p class="primary-btn small tr-bg">
                                                                {{ @$background_setting->type }}</p>
                                                        </td>
                                                        <td>
                                                            @if (@$background_setting->type == 'image')
                                                                <img src="{{ asset($background_setting->image) }}"
                                                                    width="200px" height="100px">
                                                            @else
                                                                <div style="{{ $style }}"></div>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex">

                                                                @if ($background_setting->is_default == 1)
                                                                    <a class="btn btn-light small fix-gr-bg "
                                                                        href="{{ url('/background_setting-status') }}/{{ @$background_setting->id }}">
                                                                        @lang('lang.Activated') </a>
                                                                @else
                                                                    <a class="btn btn-light small tr-bg"
                                                                        href="{{ url('/background_setting-status') }}/{{ @$background_setting->id }}">
                                                                        @lang('lang.Make_Default')</a>
                                                                @endif
                                                            </div>
                                                        </td>

                                                        <td>
                                                            @if (@$background_setting->id == 1 || @$background_setting->id == 2)
                                                                <p class="primary-btn small tr-bg">Not Allowed</p>
                                                            @else
                                                                <div class="dropdown">
                                                                    <button type="button" class="btn btn-light border dropdown-toggle"
                                                                        data-bs-toggle="dropdown">
                                                                        @lang('Action')
                                                                    </button>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <a class="dropdown-item text-danger" data-bs-toggle="modal"
                                                                            data-target="#deletebackground_settingModal"
                                                                            href="#">@lang('lang.delete')</a>
                                                                    </div>
                                                                </div>

                                                                <div class="modal fade admin-query"
                                                                    id="deletebackground_settingModal">
                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title">@lang('lang.delete')
                                                                                </h4>
                                                                                <button type="button" class="close"
                                                                                    data-dismiss="modal">&times;
                                                                                </button>
                                                                            </div>

                                                                            <div class="modal-body">
                                                                                <div class="text-center">
                                                                                    <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                                                </div>

                                                                                <div
                                                                                    class="mt-4 d-flex justify-content-between">
                                                                                    <button type="button"
                                                                                        class="primary-btn tr-bg"
                                                                                        data-dismiss="modal">@lang('lang.cancel')
                                                                                    </button>

                                                                                    <a href="{{ url('background-setting-delete') }}/{{ @$background_setting->id }}"
                                                                                        class="primary-btn fix-gr-bg">@lang('lang.delete')</a>

                                                                                </div>
                                                                            </div>

                                                                        </div>
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
                </div>


            </div>
        </div>
    </div>
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection

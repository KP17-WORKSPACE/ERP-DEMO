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
                        Modules
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light" href="{{ url('module-pages/'.$editmode->id) }}">
                            <i class="ico icon-outline-add-square text-success"></i> Add
                        </a>
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="row">
                                    <div class="col-lg-12">
                                        @if (isset($editpage))
                                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'module-pages/' . @$editpage->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                                        @else
                                            @if (in_array(105, @$module_links) || Auth::user()->role_id == 1)
                                                {{ Form::open([
                                                    'class' => 'form-horizontal',
                                                    'files' => true,
                                                    'url' => 'module-pages',
                                                    'method' => 'POST',
                                                    'enctype' => 'multipart/form-data',
                                                ]) }}
                                            @endif
                                        @endif
                                        <div class="white-box">
                                            <div class="add-visitor">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        @if (session()->has('message-success'))
                                                            <div class="alert alert-success">
                                                                {{ session()->get('message-success') }}
                                                            </div>
                                                        @elseif(session()->has('message-danger'))
                                                            <div class="alert alert-danger">
                                                                {{ session()->get('message-danger') }}
                                                            </div>
                                                        @endif
                                                        <div class="input-effect">
                                                            <label class="txtlbl">@lang('Page')
                                                                <span>*</span></label>
                                                            <input class="form-control" type="text" name="name"
                                                                autocomplete="off"
                                                                value="{{ isset($editpage) ? @$editpage->name : '' }}"
                                                                required>

                                                            <input type="hidden" name="id"
                                                                value="{{ isset($editpage) ? $editpage->id : '' }}">
                                                            <input type="hidden" name="mid"
                                                                value="{{ isset($editmode) ? $editmode->id : '' }}">


                                                        </div>
                                                    </div>

                                                    <div class="col-lg-12">
                                                        <div class="input-effect">
                                                            <label class="txtlbl">@lang('Page Name')
                                                                <span>*</span></label>
                                                            <input class="form-control" type="text" name="page_name"
                                                                autocomplete="off"
                                                                value="{{ isset($editpage) ? @$editpage->page_name : '' }}"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $tooltip = '';
                                                    if (in_array(105, $module_links) || Auth::user()->role_id == 1) {
                                                        $tooltip = '';
                                                    } else {
                                                        $tooltip = 'You have no permission to add';
                                                    }
                                                @endphp
                                                <div class="row mt-2">
                                                    <div class="col-lg-12">
                                                        <button class="btn btn-light" type="submit" id="btnSubmit">
                                                            <i class="ico icon-outline-bookmark-opened text-success"></i>
                                                            {{ isset($editpage) ? 'Update' : 'Save' }}
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
                                    <div class="col-lg-12">

                                        <table class="table table-hover" id="long-list" width="100%" cellspacing="0">

                                            <thead>
                                                @if (session()->has('message-success-delete') != '' || session()->get('message-danger-delete') != '')
                                                    <tr>
                                                        <td colspan="2">
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
                                                    <th>@lang('Page')</th>
                                                    {{-- <th>@lang('Created By')</th> --}}
                                                    <th>@lang('Page Name')</th>
                                                    <th class="text-center" style="width: 90px">@lang('lang.action')</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($pagelist as $mdle)
                                                    <tr>
                                                        <td>{{ @$mdle->name }}</td>
                                                        <td>{{ @$mdle->page_name }}</td>
                                                        {{-- <td>{{@$mdle->created_by->createdby }}</td> --}}
                                                        <td>
                                                            <div class="d-flex justify-content-center">
                                                                <a class="btn btn-sm btn-light text-dark"
                                                                    href="{{ url('module-pages', [@$mdle->id, 'edit']) }}">
                                                                    <i class="ico icon-outline-pen-2 text-success"
                                                                        style="font-size: 16px;"></i> @lang('lang.edit')</a>
                                                            </div>


                                                        </td>
                                                    </tr>
                                                    <div class="modal fade admin-query"
                                                        id="deleteDesignationModal{{ @$mdle->id }}">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">@lang('lang.delete')
                                                                        @lang('lang.paymentterms')</h4>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <div class="text-center">
                                                                        <h4>@lang('lang.are_you_sure_to_delete')</h4>
                                                                    </div>

                                                                    <div class="mt-40 d-flex justify-content-between">
                                                                        <button type="button" class="primary-btn tr-bg"
                                                                            data-dismiss="modal">@lang('lang.cancel')</button>
                                                                        {{ Form::open(['url' => 'module/' . @$mdle->id, 'method' => 'DELETE', 'enctype' => 'multipart/form-data']) }}
                                                                        <button class="primary-btn fix-gr-bg"
                                                                            type="submit">@lang('lang.delete')</button>
                                                                        {{ Form::close() }}
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


            </div>
        </div>
    </div>


    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection


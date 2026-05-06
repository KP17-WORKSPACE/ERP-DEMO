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
                        Backup Settings
                    </h4>
                    <div class="purchase-order-content-header-right">
                        <a class="btn btn-light text-black" href="{{ url('sms-settings') }}">
                            <i class="ico icon-outline-add-square text-success"></i> Backup Settings
                        </a>
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 d-none">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="main-title">
                                            <h4 class="mb-30">@lang('lang.upload_from_local_directory')</h4>
                                        </div>
                                        @if (isset($session))
                                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'session/' . @$session->id, 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                                        @else
                                            @if (in_array(220, @$module_links) || Auth::user()->role_id == 1)
                                                {{ Form::open([
                                                    'class' => 'form-horizontal',
                                                    'files' => true,
                                                    'url' => 'backup-store',
                                                    'method' => 'POST',
                                                    'enctype' => 'multipart/form-data',
                                                ]) }}
                                            @endif
                                        @endif
                                        <div class="white-box">
                                            <div class="add-visitor">

                                                <div class="row no-gutters input-right-icon mb-20">
                                                    <div class="col">
                                                        <div class="input-effect">
                                                            <input
                                                                class="primary-input form-control {{ $errors->has('content_file') ? ' is-invalid' : '' }}"
                                                                readonly="true" type="text"
                                                                placeholder="{{ isset($editData->file) && @$editData->file != '' ? showPicName(@$editData->file) : 'Upload File' }} "
                                                                id="placeholderUploadContent" name="content_file">
                                                            <span class="focus-border"></span>
                                                            @if ($errors->has('content_file'))
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $errors->first('content_file') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button class="btn btn-sm btn-light-small-input" type="button">
                                                            <label class="btn btn-sm btn-light small fix-gr-bg"
                                                                for="upload_content_file">@lang('lang.browse')</label>
                                                            <input type="file" class="d-none form-control"
                                                                name="content_file" id="upload_content_file">
                                                        </button>

                                                    </div>
                                                </div>


                                                @php
                                                    $tooltip = '';
                                                    if (in_array(220, @$module_links) || Auth::user()->role_id == 1) {
                                                        $tooltip = '';
                                                    } else {
                                                        $tooltip = 'You have no permission to add';
                                                    }
                                                @endphp

                                                <div class="row mt-40">
                                                    <div class="col-lg-12 text-center">




                                                        <button class="btn btn-sm btn-light fix-gr-bg" data-toggle="tooltip"
                                                            title="{{ @$tooltip }}">
                                                            <span class="ti-check"></span>
                                                            @if (isset($session))
                                                                @lang('lang.update')
                                                            @else
                                                                @lang('lang.save')
                                                            @endif
                                                            @lang('lang.file')
                                                        </button>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-4 no-gutters">
                                        <div class="main-title">
                                            <h4 class="mb-0"> @lang('lang.database_backup_list')</h4>
                                        </div>
                                    </div>
                                    <div class="offset-lg-12 col-lg-12 text-end col-md-12 mb-20">
                                        @if (in_array(221, @$module_links) || Auth::user()->role_id == 1)
                                            @if (Illuminate\Support\Facades\Config::get('app.app_sync'))
                                                <span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                                                    title="Disabled For Demo "> <button
                                                        class=" btn-sm btn-light small fix-gr-bg  demo_view"
                                                        style="pointer-events: none;" type="button" disabled>
                                                        @lang('lang.image') @lang('lang.backup')</button></span>
                                            @else
                                                <a href="{{ url('get-backup-files/1') }}"
                                                    class=" btn-sm btn-light small fix-gr-bg">
                                                    <span class="ti-arrow-circle-down pr-2"></span> @lang('lang.image')
                                                    @lang('lang.backup')
                                                </a>
                                            @endif
                                        @endif
                                        @if (in_array(222, @$module_links) || Auth::user()->role_id == 1)
                                            @if (Illuminate\Support\Facades\Config::get('app.app_sync'))
                                                <span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                                                    title="Disabled For Demo "> <button
                                                        class=" btn-sm btn-light small fix-gr-bg  demo_view"
                                                        style="pointer-events: none;" type="button" disabled>
                                                        @lang('lang.project') @lang('lang.backup')</button></span>
                                            @else
                                                <a href="{{ url('get-backup-files/2') }}"
                                                    class=" btn-sm btn-light small fix-gr-bg ">
                                                    <span class="ti-arrow-circle-down pr-2"></span> @lang('lang.project')
                                                    @lang('lang.backup')
                                                </a>
                                            @endif
                                        @endif
                                        @if (in_array(223, @$module_links) || Auth::user()->role_id == 1)
                                            @if (Illuminate\Support\Facades\Config::get('app.app_sync'))
                                                <span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                                                    title="Disabled For Demo "> <button
                                                        class=" btn-sm btn-light small fix-gr-bg  demo_view"
                                                        style="pointer-events: none;" type="button" disabled>
                                                        @lang('lang.database') @lang('lang.backup')</button></span>
                                            @else
                                                <a href="{{ url('get-backup-db') }}" class=" btn-sm btn-light small fix-gr-bg ">
                                                    <span class="ti-arrow-circle-down pr-2"></span> @lang('lang.database')
                                                    @lang('lang.backup')

                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">

                                        <table class="table table-hover form-item-table" cellspacing="0" width="100%">
                                            <thead>


                                                @if (session()->has('message-success') != '' || session()->get('message-danger') != '')
                                                    <tr>
                                                        <td colspan="5">
                                                            @if (session()->has('message-success'))
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
                                                    <th>Size</th>
                                                    <th>@lang('lang.created_date_time')</th>
                                                    <th>@lang('lang.backup_files')</th>
                                                    <th class="text-center">File Type</th>
                                                    <th class="text-center">@lang('lang.action')</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($sms_dbs as $sms_db)
                                                    <tr>
                                                        <td>
                                                            @php
                                                                if (file_exists(@$sms_db->source_link)) {
                                                                    $size = filesize(@$sms_db->source_link);
                                                                    $units = [
                                                                        'B',
                                                                        'KB',
                                                                        'MB',
                                                                        'GB',
                                                                        'TB',
                                                                        'PB',
                                                                        'EB',
                                                                        'ZB',
                                                                        'YB',
                                                                    ];
                                                                    $power = @$size > 0 ? floor(log(@$size, 1024)) : 0;
                                                                    echo @App\SysHelper::com_curr_format(
                                                                        @$size / pow(1024, @$power),
                                                                        2,
                                                                        '.',
                                                                        ','
                                                                    ) .
                                                                        ' ' .
                                                                        @$units[@$power];
                                                                } else {
                                                                    echo 'File already deleted.';
                                                                }
                                                            @endphp
                                                        </td>
                                                        <td>{{ date('jS M, Y h:i A', strtotime(@$sms_db->created_at)) }}
                                                        </td>
                                                        <td>{{ @$sms_db->file_name }}</td>
                                                        <td class="text-center">
                                                            @php
                                                                if (@$sms_db->file_type == 0) {
                                                                    echo 'Database';
                                                                } elseif (@$sms_db->file_type == 1) {
                                                                    echo 'Images';
                                                                } else {
                                                                    echo 'Whole Project';
                                                                }
                                                            @endphp
                                                        </td>


                                                        <td class="d-flex gap-2 justify-content-center">
                                                            @if (in_array(347, @$module_links) || Auth::user()->role_id == 1)
                                                                <a class="btn btn-sm btn-light small tr-bg  text-dark"
                                                                    href="{{ url('/download-files/' . @$sms_db->id) }}"
                                                                    class="backupSetting_do_up">

                                                                    <span class="pl ti-download"></span> <i class="ico icon-bold-download-minimalistic text-dark" style="font-size: 16px;"></i>  @lang('lang.download')
                                                                </a>
                                                            @endif

                                                            @if (in_array(349, @$module_links) || Auth::user()->role_id == 1)
                                                                <a data-target="#deleteDatabase{{ @$sms_db->id }}"
                                                                    data-toggle="modal" class="btn btn-sm btn-light small tr-bg  text-dark"
                                                                    href="{{ url('/' . @$sms_db->id) }}"
                                                                    class="backupSetting_do_up">
                                                                    <span class="pl ti-close"></span> <i class="ico icon-bold-trash-bin-2 text-dark" style="font-size: 16px;"></i> @lang('lang.delete')
                                                                </a>
                                                            @endif

                                                        </td>
                                                    </tr>



                                                    <div class="modal fade admin-query"
                                                        id="deleteDatabase{{ @$sms_db->id }}">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title"> @lang('lang.delete')
                                                                        @lang('lang.item')</h4>

                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal">&times;</button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <div class="text-center">
                                                                        <h4> @lang('lang.are_you_sure_to_delete')</h4>
                                                                    </div>

                                                                    <div class="mt-40 d-flex justify-content-between">
                                                                        <button type="button" class="btn btn-sm btn-light tr-bg"
                                                                            data-dismiss="modal">
                                                                            @lang('lang.cancel')</button>
                                                                        <a href="{{ route('delete_database', [@$sms_db->id]) }}"
                                                                            class="text-light">
                                                                            <button class="btn btn-sm btn-light fix-gr-bg"
                                                                                type="submit"> @lang('lang.delete')</button>
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


            </div>
        </div>
    </div>
    <?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

@endsection

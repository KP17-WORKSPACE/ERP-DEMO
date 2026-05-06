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
                    @lang('Role Permission Settings') | @lang('lang.assign_permission') to ({{ @$role->name }})
                </h4>
                <div class="purchase-order-content-header-right">
                    <a class="btn btn-light" href="{{url('role')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Role
                    </a>
                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                <div class="col-lg-12">
                    @if (Illuminate\Support\Facades\Config::get('app.app_sync'))
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'url' => 'admin-dashboard', 'method' => 'GET', 'enctype' => 'multipart/form-data']) }}
                    @else
                        {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'role_permission_store', 'method' => 'POST']) }}
                    @endif
                    <input type="hidden" name="role_id" value="{{ @$role->id }}">
                    <div class="row">
                        <div class="col-lg-12 base-setup role-permission">
                            <table id="table table-hover" class="display school-table-style" cellspacing="0"
                                width="100%">
                                <thead>
                                    @if (session()->has('message-danger') != '')
                                        <tr>
                                            <td colspan="9">
                                                @if (session()->has('message-danger'))
                                                    <div class="alert alert-danger">{{ session()->get('message-danger') }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th><b>Module</b></th>
                                        <th><b>Permission</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="pr-0">
                                            <div id="accordion" role="tablist">
                                                @php $i = 0; @endphp
                                                <?php $row=0; ?>
                                                @foreach ($modules as $module)
                                                    <div class="card">
                                                        <div class="" id="headingOne" style="background-color:#deebe1">
                                                            <div class="row">
                                                                <div class="col-lg-2">
                                                                    <div>
                                                                        <p class="m-0 pt-1 pb-0 pl-1 pr-1">
                                                                            {{ @$module->name }}
                                                                            @php $class = str_replace(' ','_',@$module->name); @endphp
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-10">
                                                                    <div class="row m-0 p-0">
                                                                        <div class="col-lg-1 m-0 p-0">
                                                                            <p class="mt-0 mb-0"><input type="checkbox" id="chk_create_{{ $module->id }}" onclick="create_all({{ $module->id }})" /> Create</p>
                                                                        </div>
                                                                        <div class="col-lg-1 m-0 p-0">
                                                                            <p class="mt-0 mb-0"><input type="checkbox" id="chk_view_{{ $module->id }}" onclick="view_all({{ $module->id }})" /> View</p> {{--  Read  --}}
                                                                        </div>
                                                                        <div class="col-lg-1 m-0 p-0">
                                                                            <p class="mt-0 mb-0"><input type="checkbox" id="chk_edit_{{ $module->id }}" onclick="edit_all({{ $module->id }})" /> Edit</p>
                                                                        </div>
                                                                        <div class="col-lg-1 m-0 p-0">
                                                                            <p class="mt-0 mb-0"><input type="checkbox" id="chk_delete_{{ $module->id }}" onclick="delete_all({{ $module->id }})" /> Delete</p>
                                                                        </div>
                                                                        <div class="col-lg-1 m-0 p-0">
                                                                            <p class="mt-0 mb-0"><input type="checkbox" id="chk_print_{{ $module->id }}" onclick="print_all({{ $module->id }})" /> Print</p>
                                                                        </div>
                                                                        <div class="col-lg-1 m-0 p-0">
                                                                            <p class="mt-0 mb-0"><input type="checkbox" id="chk_copy_{{ $module->id }}" onclick="copy_all({{ $module->id }})" /> Copy</p>
                                                                        </div>
                                                                        <div class="col-lg-1 m-0 p-0">
                                                                            <p class="mt-0 mb-0"><input type="checkbox" id="chk_recreate_{{ $module->id }}" onclick="recreate_all({{ $module->id }})" /> Recreate</p>
                                                                        </div>
                                                                        <div class="col-lg-1 m-0 p-0">
                                                                            <p class="mt-0 mb-0"><input type="checkbox" id="chk_save_print_{{ $module->id }}" onclick="save_print_all({{ $module->id }})" /> Save Print</p>
                                                                        </div>
                                                                        <div class="col-lg-1 m-0 p-0">
                                                                            <p class="mt-0 mb-0"><input type="checkbox" id="chk_revice_{{ $module->id }}" onclick="revice_all({{ $module->id }})" /> Revice</p>
                                                                        </div>
                                                                        <div class="col-lg-1 m-0 p-0">
                                                                            <p class="mt-0 mb-0"><input type="checkbox" id="chk_export_{{ $module->id }}" onclick="export_all({{ $module->id }})" /> Export</p>
                                                                        </div>
                                                                        <div class="col-lg-1 m-0 p-0">
                                                                            <p class="mt-0 mb-0"><input type="checkbox" id="chk_edit_printed_{{ $module->id }}" onclick="edit_printed_all({{ $module->id }})" /> Edit Printed</p>
                                                                        </div>
                                                                        <div class="col-lg-1 m-0 p-0">
                                                                            <p class="mt-0 mb-0"><input type="checkbox" id="chk_attach_{{ $module->id }}" onclick="attach_all({{ $module->id }})" /> Attach</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $module_links = @$module
                                                                ->moduleLink()
                                                                ->where('active_status', 1)->orderby('id','asc')
                                                                ->get();
                                                        @endphp
                                                        <div id="collapseOne" class="show" aria-labelledby="headingOne"
                                                            data-parent="#accordion">
                                                            <div class="card-body m-2 p-0 hlight">
                                                                @foreach ($module_links as $module_link)
                                                                    @if (strpos(@$module_link->name, '➡') !== false)
                                                                        @php
                                                                            $css = 'background:white;';
                                                                            @$css2 = 'padding-left:0px !important;';
                                                                        @endphp
                                                                    @else
                                                                        @php
                                                                            $css = 'background:#f4f4f4;';
                                                                            @$css2 = 'padding-left:0px !important;';
                                                                        @endphp
                                                                    @endif
                                                                    <div class="row border-bottom" style="{{ isset($css) ? @$css : '' }}">
                                                                        <div class="col-lg-2" style="{{ isset($css2) ? @$css2 : '' }}">
                                                                            &nbsp;{{ @$module_link->name }} [{{ $module_link->id }}]
                                                                            <input type="hidden" name="permissions[]" value="{{ @$module_link->id }}">
                                                                        </div>
                                                                        <div class="col-lg-10">
                                                                            <div class="row m-0 p-0">
                                                                                <div class="col-lg-1 m-0 p-0">
                                                                                    <input type="hidden" name="permissions_create[]" value="0">
                                                                                    <input type="checkbox" id="permissions_create_{{ @$module_link->id }}" class="common-checkbox {{ @$class }} create_{{ $module->id }}" name="permissions_create[]" value="1" {{ set_checked($is_create,$row) }}>
                                                                                    <label for="permissions_create_{{ @$module_link->id }}"></label>
                                                                                </div>
                                                                                <div class="col-lg-1 m-0 p-0">
                                                                                    <input type="hidden" name="permissions_read[]" value="0">
                                                                                    <input type="checkbox" id="permissions_read_{{ @$module_link->id }}" class="common-checkbox {{ @$class }} view_{{ $module->id }}" name="permissions_read[]" value="1" {{ set_checked($is_read,$row) }}>
                                                                                    <label for="permissions_read_{{ @$module_link->id }}"></label>
                                                                                </div>
                                                                                <div class="col-lg-1 m-0 p-0">
                                                                                    <input type="hidden" name="permissions_edit[]" value="0">
                                                                                    <input type="checkbox" id="permissions_edit_{{ @$module_link->id }}" class="common-checkbox {{ @$class }} edit_{{ $module->id }}" name="permissions_edit[]" value="1" {{ set_checked($is_edit,$row) }}>
                                                                                    <label for="permissions_edit_{{ @$module_link->id }}"></label>
                                                                                </div>                                                                                
                                                                                <div class="col-lg-1 m-0 p-0">
                                                                                    <input type="hidden" name="permissions_delete[]" value="0">
                                                                                    <input type="checkbox" id="permissions_delete_{{ @$module_link->id }}" class="common-checkbox {{ @$class }} delete_{{ $module->id }}" name="permissions_delete[]" value="1" {{ set_checked($is_delete,$row) }}>
                                                                                    <label for="permissions_delete_{{ @$module_link->id }}"></label>
                                                                                </div>

                                                                                <div class="col-lg-1 m-0 p-0">
                                                                                    <input type="hidden" name="permissions_print[]" value="0">
                                                                                    <input type="checkbox" id="permissions_print_{{ @$module_link->id }}" class="common-checkbox {{ @$class }} print_{{ $module->id }}" name="permissions_print[]" value="1" {{ set_checked($is_print,$row) }}>
                                                                                    <label for="permissions_print_{{ @$module_link->id }}"></label>
                                                                                </div>
                                                                                <div class="col-lg-1 m-0 p-0">
                                                                                    <input type="hidden" name="permissions_copy[]" value="0">
                                                                                    <input type="checkbox" id="permissions_copy_{{ @$module_link->id }}" class="common-checkbox {{ @$class }} copy_{{ $module->id }}" name="permissions_copy[]" value="1" {{ set_checked($is_copy,$row) }}>
                                                                                    <label for="permissions_copy_{{ @$module_link->id }}"></label>
                                                                                </div>
                                                                                <div class="col-lg-1 m-0 p-0">
                                                                                    <input type="hidden" name="permissions_recreate[]" value="0">
                                                                                    <input type="checkbox" id="permissions_recreate_{{ @$module_link->id }}" class="common-checkbox {{ @$class }} recreate_{{ $module->id }}" name="permissions_recreate[]" value="1" {{ set_checked($is_recreate,$row) }}>
                                                                                    <label for="permissions_recreate_{{ @$module_link->id }}"></label>
                                                                                </div>
                                                                                <div class="col-lg-1 m-0 p-0">
                                                                                    <input type="hidden" name="permissions_saveprint[]" value="0">
                                                                                    <input type="checkbox" id="permissions_saveprint_{{ @$module_link->id }}" class="common-checkbox {{ @$class }} save_print_{{ $module->id }}" name="permissions_saveprint[]" value="1" {{ set_checked($is_saveprint,$row) }}>
                                                                                    <label for="permissions_saveprint_{{ @$module_link->id }}"></label>
                                                                                </div>
                                                                                <div class="col-lg-1 m-0 p-0">
                                                                                    <input type="hidden" name="permissions_revice[]" value="0">
                                                                                    <input type="checkbox" id="permissions_revice_{{ @$module_link->id }}" class="common-checkbox {{ @$class }} revice_{{ $module->id }}" name="permissions_revice[]" value="1" {{ set_checked($is_revice,$row) }}>
                                                                                    <label for="permissions_revice_{{ @$module_link->id }}"></label>
                                                                                </div>
                                                                                <div class="col-lg-1 m-0 p-0">
                                                                                    <input type="hidden" name="permissions_export[]" value="0">
                                                                                    <input type="checkbox" id="permissions_export_{{ @$module_link->id }}" class="common-checkbox {{ @$class }} export_{{ $module->id }}" name="permissions_export[]" value="1" {{ set_checked($is_export,$row) }}>
                                                                                    <label for="permissions_export_{{ @$module_link->id }}"></label>
                                                                                </div>
                                                                                <div class="col-lg-1 m-0 p-0">
                                                                                    <input type="hidden" name="permissions_editprinted[]" value="0">
                                                                                    <input type="checkbox" id="permissions_editprinted_{{ @$module_link->id }}" class="common-checkbox {{ @$class }} edit_printed_{{ $module->id }}" name="permissions_editprinted[]" value="1" {{ set_checked($is_editprinted,$row) }}>
                                                                                    <label for="permissions_editprinted_{{ @$module_link->id }}"></label>
                                                                                </div>
                                                                                <div class="col-lg-1 m-0 p-0">
                                                                                    <input type="hidden" name="permissions_attach[]" value="0">
                                                                                    <input type="checkbox" id="permissions_attach_{{ @$module_link->id }}" class="common-checkbox {{ @$class }} attach_{{ $module->id }}" name="permissions_attach[]" value="1" {{ set_checked($is_attach,$row) }}>
                                                                                    <label for="permissions_attach_{{ @$module_link->id }}"></label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <?php $row++; ?>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td></td>
                                        <td> </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <div class="col-lg-12 mt-20 text-right">
                                                @if (Illuminate\Support\Facades\Config::get('app.app_sync'))
                                                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Disabled For Demo "><button class="primary-btn small fix-gr-bg  demo_view" style="pointer-events: none;" type="button" disabled> lang('lang.save')</button></span>
                                                @else
                                                
                                            <button class="btn btn-light" type="submit">
                                                <i class="ico icon-outline-bookmark-opened text-success"></i> Save Role Permission
                                            </button>
                                                @endif
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
            </div>
			
			
		</div>
	</div>
</div>
	
<style>
.hlight div:hover{ background: #d1d1d1; }
</style>
<script>
    function create_all(id) {
        if($('#chk_create_'+id).prop('checked')) {
            $('.create_'+id).prop( "checked", true );
        } else {
            $('.create_'+id).prop( "checked", false );
        }
    }
    function view_all(id) {
        if($('#chk_view_'+id).prop('checked')) {
            $('.view_'+id).prop( "checked", true );
        } else {
            $('.view_'+id).prop( "checked", false );
        }
    }
    function edit_all(id) {
        if($('#chk_edit_'+id).prop('checked')) {
            $('.edit_'+id).prop( "checked", true );
        } else {
            $('.edit_'+id).prop( "checked", false );
        }
    }
    function delete_all(id) {
        if($('#chk_delete_'+id).prop('checked')) {
            $('.delete_'+id).prop( "checked", true );
        } else {
            $('.delete_'+id).prop( "checked", false );
        }
    }
    function print_all(id) {
        if($('#chk_print_'+id).prop('checked')) {
            $('.print_'+id).prop( "checked", true );
        } else {
            $('.print_'+id).prop( "checked", false );
        }
    }
    function copy_all(id) {
        if($('#chk_copy_'+id).prop('checked')) {
            $('.copy_'+id).prop( "checked", true );
        } else {
            $('.copy_'+id).prop( "checked", false );
        }
    }
    function recreate_all(id) {
        if($('#chk_recreate_'+id).prop('checked')) {
            $('.recreate_'+id).prop( "checked", true );
        } else {
            $('.recreate_'+id).prop( "checked", false );
        }
    }
    function save_print_all(id) {
        if($('#chk_save_print_'+id).prop('checked')) {
            $('.save_print_'+id).prop( "checked", true );
        } else {
            $('.save_print_'+id).prop( "checked", false );
        }
    }
    function revice_all(id) {
        if($('#chk_revice_'+id).prop('checked')) {
            $('.revice_'+id).prop( "checked", true );
        } else {
            $('.revice_'+id).prop( "checked", false );
        }
    }
    function export_all(id) {
        if($('#chk_export_'+id).prop('checked')) {
            $('.export_'+id).prop( "checked", true );
        } else {
            $('.export_'+id).prop( "checked", false );
        }
    }
    function edit_printed_all(id) {
        if($('#chk_edit_printed_'+id).prop('checked')) {
            $('.edit_printed_'+id).prop( "checked", true );
        } else {
            $('.edit_printed_'+id).prop( "checked", false );
        }
    }
    function attach_all(id) {
        if($('#chk_attach_'+id).prop('checked')) {
            $('.attach_'+id).prop( "checked", true );
        } else {
            $('.attach_'+id).prop( "checked", false );
        }
    }
</script>

    
    
<?php }catch (\Exception $e) { ?> {{ $e }} <?php  } ?>

    <?php 
    function set_checked($array_id,$row_id){
        try{
        if(count($array_id)>0){
            if($array_id[$row_id]==1){
                return "checked";
            }
            else{
                return "";
            }
        }
        }catch (\Exception $e) {
            return $e;
        }
    }

    ?>
@endsection
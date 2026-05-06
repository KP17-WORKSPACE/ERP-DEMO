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
                    Role Permission
                </h4>
                <div class="purchase-order-content-header-right">
                    <a class="btn btn-light text-dark" href="{{url('role')}}">
                        <i class="ico icon-outline-add-square text-success"></i> Role
                    </a>

                    
                      <div class="dropdown">
            <button class="btn btn-light dropdown-toggle syscom-dropdown-toggle" type="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ico icon-outline-hamburger-menu"></i>
            </button>
            <ul class="dropdown-menu" style="">




                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('company/policy') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Policy
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('/department') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Department
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/designation') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Designation
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/legal-entity') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Business Entity
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/industry') }}">
                        <i class="ico icon-outline-layers text-success  title-15 me-2"></i>
                        Industry Type
                    </a>
                </li>

                <li>
                    <a class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"
                        href="{{ url('/business-activity') }}">
                        <i class="ico icon-outline-layers text-success  title-15 me-2"></i>
                        Business Sector
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('company') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Company Settings
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('module') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Module
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('base_setup') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Base Setup
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ route('daily-quotes.index') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Daily Quote
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('currency-settings') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Manage Currency
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('payment-terms') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Payment Terms
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('payment-cheque-print-template') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Cheque Print Templates
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('shipping-add') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Shipping
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('vat-settings') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        VAT Settings
                    </a>
                </li>


                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('accountgroup-add') }}">
                        <i class="ico icon-outline-document text-success  title-15 me-2"></i>
                        Main Heads
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('book-close') }}">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        Book Closed
                    </a>
                </li>

                <li>
                    <a
                        class="dropdown-item d-flex align-items-center text-dark copy-onboard-url"href="{{ url('book-close-doc-number') }}">
                        <i class="ico icon-outline-settings text-success  title-15 me-2"></i>
                        Book Close Doc No
                    </a>
                </li>


            </ul>
        </div>



                </div>
            </div>

            
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                <div class="col-lg-3">
                    <div class="row">
                        <div class="col-lg-12">
                            @if(isset($role))
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'role_update',
                            'method' => 'POST']) }}
                            @else
                            @if(in_array(192, @$module_links) || Auth::user()->role_id == 1)
                            {{ Form::open(['class' => 'form-horizontal', 'files' => true, 'route' => 'role_store', 'method'
                            => 'POST']) }}
                            @endif
                            @endif
                            <div class="white-box">
                                <div class="add-visitor">
                                    <div class="row  mt-25">
                                        <div class="col-lg-12">
                                            @if(session()->has('message-success'))
                                            <div class="alert alert-success">
                                                {{ session()->get('message-success') }}
                                            </div>
                                            @elseif(session()->has('message-danger'))
                                            <div class="alert alert-danger">
                                                {{ session()->get('message-danger') }}
                                            </div>
                                            @endif
                                            <div class="input-effect">
                                                <label class="form-label">@lang('lang.name') <span>*</span></label>
                                                <input class="form-control" type="text" name="name" autocomplete="off" value="{{isset($role)? @$role->name: ''}}">
                                                <input type="hidden" name="id" value="{{isset($role)? @$role->id: ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    @php 
                                      $tooltip = "";
                                       if(in_array(192, @$module_links) ||  Auth::user()->role_id == 1){
                                            $tooltip = "";
                                        }else{
                                            $tooltip = "You have no permission to add";
                                        }
                                    @endphp
                                    <div class="row mt-2">
                                        <div class="col-lg-12">
                                            
                                            <button class="btn btn-light" type="submit" id="btnSubmit">
                                                <i class="ico icon-outline-bookmark-opened text-success"></i> {{!isset($role)? 'Save' : 'Update'}}
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
    
                            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
    
                                <thead>
                                    @if(session()->has('message-success-delete') != "" ||
                                    session()->get('message-danger-delete') != "")
                                    <tr>
                                        <td colspan="3">
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
                                        <th width="30%">@lang('lang.role')</th>
                                        <th width="40%">@lang('lang.type')</th>
                                        <th width="30%">@lang('lang.action')</th>
                                    </tr>
                                </thead>
    
                                <tbody>
                                    @foreach($roles as $role)
                                    <tr>
                                        <td>{{@$role->name}} [{{ @$role->id }}]</td>
                                        <td>{{@$role->type}}</td>
                                        <td>
                                            @if($role->id != 1 && $role->id != 2)
                                                @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                                <a href="{{route('assign_permission', [@$role->id])}}" class="btn-sm btn-light text-dark"> @lang('Assign Permission')</a>
                                                    @endif
                                                @else
                                            @endif
    
                                            <a class="btn-light btn-sm" href="{{route('role_edit', [@$role->id])}}"> @lang('lang.edit')</a>
                                                                                 
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

@section('script')
    <script>
        $(document).ready(function () {
            $("#btnSubmit").click(function () {
                setTimeout(function () { disableButton(); }, 0);
            });
            function disableButton() {
                $("#btnSubmit").prop('disabled', true);
            }
        });
    </script>
@endsection
@extends('backEnd.newmasterpage')
@section('mainContent')

    @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
<div id="app">
<div class="content-container col-12">

    <!-- HEADER with title and buttons -->
    <div class="d-flex align-items-center justify-content-between">

        <h4 class="mb-0">@{{ isEdit ? "Edit Company" : "Add Company" }}</h4>

        <div class="d-flex align-items-center gap-2">

            <button class="btn btn-light d-inline-flex align-items-center gap-2"
                    @click="submitAll" :disabled="loading">

                <span class="spinner-border spinner-border-sm" 
                      v-show="loading" role="status"></span>

                <i class="ico icon-outline-bookmark-opened text-success" v-show="!loading"></i>

                <span>@{{ loading ? 'Saving...' : 'Save' }}</span>
            </button>

            <span class="ms-2 text-success">@{{ flash }}</span>

        </div>
    </div>


    <!-- COMPANY BASIC INFO -->
    <div class="card mt-3">
        <div class="card-body">
            @include('backEnd.company.partials.basic') 
        </div>
    </div>

    {{-- <!-- TABS -->
    <div class="tab-wrap mb-3 mt-3">
        @include('backEnd.company.partials.tabs')
    </div>

    <!-- TAB PANELS -->
    <div class="tab-content">

        <div class="tab-pane fade show active" id="contactinfo">
            @include('company.partials.contact')
        </div>

        <div class="tab-pane fade" id="compliance-regulatory">
            @include('company.partials.registration')
        </div>

        <div class="tab-pane fade" id="Banking-Finance">
            @include('company.partials.banking')
        </div>

        <div class="tab-pane fade" id="hr-payroll">
            @include('company.partials.hr')
        </div>

        <div class="tab-pane fade" id="Policies">
            @include('company.partials.policies')
        </div>

        <div class="tab-pane fade" id="documentation">
            @include('company.partials.docs')
        </div>

        <div class="tab-pane fade" id="company-setting">
            @include('company.partials.settings')
        </div>

    </div> --}}

</div>
</div> <!-- #app -->
@endsection



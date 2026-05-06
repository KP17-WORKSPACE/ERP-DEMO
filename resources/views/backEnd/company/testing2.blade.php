@extends('backEnd.newmasterpage')

@section('mainContent')

<style>
    .form-scroll {
        overflow-y: auto;
        padding-right: 6px;
    }
    .nav-tabs .nav-link.active {
        background: #dff0d8;
    }
    .modal-backdrop {
        position: relative !important;
    }
</style>
  @php
        $module_links = [];
        $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
    @endphp
<div id="companyApp">

    <div class="form-scroll">
        <div class="content-container col-12">

            <div class="purchase-order-content-header">
                <h4 class="purchase-order-content-header-left">
                    <span>Add Company (CID-@{{ form.company_id }})</span>
                </h4>

                <span class="ms-2 text-success">@{{ flash }}</span>

                <div class="purchase-order-content-header-right">

                    <!-- SAVE BUTTON -->
                    <button type="button"
                            class="btn btn-light d-inline-flex align-items-center gap-2"
                            @click="saveAllTabs" :disabled="loading">
                        <span class="spinner-border spinner-border-sm" v-show="loading"></span>
                        <i class="ico icon-outline-bookmark-opened text-success" v-show="!loading"></i>
                        <span>@{{ loading ? 'Saving...' : 'Save' }}</span>
                    </button>

                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">

                    <!-- ❌ Removed: @submit.prevent -->
                    <form enctype="multipart/form-data">

                        @include('backEnd.company.partials._basic_information')

                        <div class="tab-wrap mb-3">
                            <ul class="nav nav-tabs">

                                <li class="nav-item">
                                    <button class="nav-link active"
                                            data-bs-toggle="tab"
                                            data-bs-target="#contactinfo">
                                        Contact Information
                                    </button>
                                </li>

                                <li class="nav-item">
                                    <button class="nav-link"
                                            data-bs-toggle="tab"
                                            data-bs-target="#compliance-tab">
                                        Company Registration
                                    </button>
                                </li>

                                <li class="nav-item">
                                    <button class="nav-link"
                                            data-bs-toggle="tab"
                                            data-bs-target="#Banking-Finance">
                                        Banking & Finance
                                    </button>
                                </li>

                            </ul>
                        </div>

                        <div class="tab-content">

                            <div class="tab-pane fade show active" id="contactinfo">
                                @include('backEnd.company.partials._contact_information')
                            </div>

                            <div class="tab-pane fade" id="compliance-tab">
                                @include('backEnd.company.partials._compliance')
                            </div>

                            <div class="tab-pane fade" id="Banking-Finance">
                                @include('backEnd.company.partials._banking_finance')
                            </div>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

    @include('backEnd.company.partials._modals')

</div>

<script>
    window.industriesData = @json($industries);
    window.activitiesData = @json($activities);
    window.entitiesData = @json($entities);
    window.nextId = @json($nextId);

    window.PARENT_COMPANY_URL = "{{ url('/parent-companies') }}";
    window.GET_STATE_URL = "{{ url('/get_state') }}";

    window.URLS = {
        basic: "{{ route('company.basic.store') }}",
        contact: "{{ route('company.contact.store') }}",
        comp: "{{ route('company.compliance.store') }}",
        docs: "{{ route('company.docs.store') }}",
        bank: "{{ route('company.banking.store') }}",
        hr: "{{ route('company.hrpayroll.store') }}",
        policy: "{{ route('company.hrpolicy.store') }}"
    };

    window.CSRF = "{{ csrf_token() }}";
</script>

<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('public/company/js/company-app.js') }}"></script>

@endsection

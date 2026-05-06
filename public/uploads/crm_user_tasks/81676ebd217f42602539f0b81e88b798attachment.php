@extends('backEnd.newmasterpage')
@section('mainContent')
<style>
    .btn-light.text-dark.d-inline-flex:hover {
    background-color: #499258 !important;
    border-color: #499258 !important;
    color: #fff !important;
}

</style>
@php
    use Carbon\Carbon;

    $module_links = [];
    $permissions = App\SmRolePermission::where('role_id', Auth::user()->role_id)->get();
@endphp

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">

            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0">Company Policy</h4>

                {{-- Small, subtle progress --}}
                @if(($total ?? 0) > 0)
                    @php
                        $progress = $total ? round((($index + 1) / $total) * 100) : 0;
                    @endphp
                    <div class="w-50 ms-3">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar progress-bar-striped" role="progressbar"
                                 style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"
                                 aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="small text-muted mt-1 text-end">
                            Step <strong>{{ $index + 1 }}</strong> / {{ $total }} ({{ $progress }}%)
                        </div>
                    </div>
                @endif
            </div>

            @if(($total ?? 0) === 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="alert alert-info mb-0">No policies found for this company.</div>
                    </div>
                </div>
            @else
                @php
                    $policy     = $policies[$index];
                    $policyDate = $policy->policy_date ? Carbon::parse($policy->policy_date) : null;
                    $validTill  = $policy->policy_valid ? Carbon::parse($policy->policy_valid) : null;
                    $isExpired  = $validTill ? $validTill->isPast() : false;
                @endphp

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <h5 class="card-title mb-0">{{ $policy->policy_name ?? 'Untitled Policy' }}</h5>

                            @if(!empty($policy->policy_category))
                                <span class="badge bg-secondary text-capitalize">
                                    {{ $policy->policy_category }}
                                </span>
                            @endif>

                            @if($validTill)
                                <span class="badge {{ $isExpired ? 'bg-danger' : 'bg-success' }}">
                                    {{ $isExpired ? 'Expired' : 'Active' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        {{-- Meta grid --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="p-3 rounded border bg-light h-100">
                                    <div class="small text-muted">Policy Date</div>
                                    <div class="fw-semibold">
                                        {{ $policyDate ? $policyDate->format('d M, Y') : '—' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded border bg-light h-100">
                                    <div class="small text-muted">Valid Till</div>
                                    <div class="fw-semibold">
                                        {{ $validTill ? $validTill->format('d M, Y') : '—' }}
                                        @if($validTill)
                                            <span class="ms-1 badge {{ $isExpired ? 'bg-danger' : 'bg-success' }}">
                                                {{ $isExpired ? 'Expired' : 'Valid' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 rounded border bg-light h-100">
                                    <div class="small text-muted">Visible to Employees</div>
                                    <div class="fw-semibold">
                                        {{ (int)$policy->view_to_employees === 1 ? 'Yes' : 'No' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Attached file (HIDDEN for now) --}}
                        <div class="mb-3">
                            {{-- <div class="alert alert-secondary d-flex align-items-center mb-2" role="alert">
                                <div>
                                    <strong>Attachment:</strong> Hidden for now (enable later).
                                </div>
                            </div> --}}

                            {{-- 
                                To show the file, remove the "d-none" class from the <a> below.
                                Or conditionally toggle with @if / @endif.
                            --}}
                            <a href="{{ $policy->policy_file ? asset($policy->policy_file) : '#' }}"
                               target="_blank"
                               class="btn btn-outline-primary btn-sm d-none">
                                View File
                            </a>
                        </div>

                        {{-- Details --}}
                        <div class="p-3 border rounded bg-white">
                            <div class="small text-muted mb-2">Details</div>
                            <div class="fs-6 lh-base">
                                {!! nl2br(e($policy->policy_details ?? '')) !!}
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-0 pb-4">
                        <form method="post" action="{{ route('policy') }}" class="d-flex gap-2 justify-content-between">
                            @csrf
                            <button type="submit" name="prev" class="btn btn-outline-secondary"
                                    @if($index==0) disabled @endif>
                                ‹ Previous
                            </button>

                          <button type="submit" name="next"
        class="btn btn-light text-dark d-inline-flex align-items-center gap-2">
    @if($index+1 < $total)
        Next <i class="bi bi-arrow-right"></i>
    @else
        Finish <i class="bi bi-check2-circle"></i>
    @endif
</button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

@endsection

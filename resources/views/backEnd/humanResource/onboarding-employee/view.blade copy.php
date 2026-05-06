
<?php try { ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Employee Details</h4>
        <a href="{{ url('onboarding-employee') }}" class="btn btn-light">Back to list</a>
    </div>



    <div class="row">
        <div class="col-md-3 text-center">
            @if(!empty($employee->staff_photo))
                <img src="{{ asset('storage/app/public/' . $employee->staff_photo) }}" class="img-fluid rounded mb-2" alt="Staff Photo">
            @else
                <div class="rounded bg-light d-flex align-items-center justify-content-center" style="height:200px">No Photo</div>
            @endif
            <h5 class="mt-2">{{ ($employee->employee_salutation ? $employee->employee_salutation . ' ' : '') . ($employee->full_name ?? '') }}</h5>
            <small class="text-muted">{{ $employee->email }}</small>
            <br>
            <small class="text-muted">{{ $employee->mobile }}</small>
        </div>

        <div class="col-md-9">
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-2">Personal Information</h6>
                    <div class="row">
                        <div class="col-md-4"><strong>DOB:</strong> {{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d/m/Y') : '-' }}</div>
                        <div class="col-md-4"><strong>Place of Birth:</strong> {{ $employee->place_of_birth ?? '-' }}</div>
                        <div class="col-md-4"><strong>Gender:</strong> {{ $employee->gender_id ?? '-' }}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4"><strong>Religion:</strong> {{ $employee->religion ?? '-' }}</div>
                        <div class="col-md-4"><strong>Marital Status:</strong> {{ $employee->marital_status ?? '-' }}</div>
                        <div class="col-md-4"><strong>Employee Code:</strong> {{ $employee->staff_no ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-2">Addresses</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Permanent</strong>
                            <div>{{ trim(implode(', ', array_filter([$employee->permanent_building_no, $employee->permanent_area, $employee->permanent_city, $employee->permanent_state]))) ?: '-' }}</div>
                            <small class="text-muted">Country: {{ $employee->permanent_country ?? '-' }}</small>
                        </div>
                        <div class="col-md-6">
                            <strong>Current</strong>
                            <div>{{ trim(implode(', ', array_filter([$employee->current_building_no, $employee->current_area, $employee->current_city, $employee->current_state]))) ?: '-' }}</div>
                            <small class="text-muted">Country: {{ $employee->current_country ?? '-' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-2">Parents & Emergency</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Father</strong>
                            <div>{{ trim(($employee->fathers_first_name ?? '') . ' ' . ($employee->fathers_last_name ?? '')) ?: '-' }}</div>
                            <small class="text-muted">{{ $employee->father_mobile ?? '' }} {{ $employee->father_email ? (' | ' . $employee->father_email) : '' }}</small>
                        </div>
                        <div class="col-md-4">
                            <strong>Mother</strong>
                            <div>{{ trim(($employee->mothers_first_name ?? '') . ' ' . ($employee->mothers_last_name ?? '')) ?: '-' }}</div>
                            <small class="text-muted">{{ $employee->mother_mobile ?? '' }} {{ $employee->mother_email ? (' | ' . $employee->mother_email) : '' }}</small>
                        </div>
                        <div class="col-md-4">
                            <strong>Emergency</strong>
                            <div>{{ $employee->emergency_contact_name ?? '-' }}</div>
                            <small class="text-muted">{{ $employee->emergency_mobile ?? '' }} {{ $employee->emergency_email ? (' | ' . $employee->emergency_email) : '' }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="mb-2">Qualification & Experience</h6>
                    <div><strong>Qualification:</strong> {{ $employee->qualification ?? '-' }}</div>
                    <div class="mt-1"><strong>Experience:</strong> {{ $employee->experience ?? '-' }}</div>
                </div>
            </div>

        </div>
    </div>

    {{-- Banks --}}
    <div class="card mb-3">
        <div class="card-body">
            <h6 class="mb-3">Bank Details</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Bank</th>
                            <th>Branch</th>
                            <th>Holder</th>
                            <th>Account No</th>
                            <th>IBAN</th>
                            <th>SWIFT</th>
                            <th>Currency</th>
                            <th>IBAN Letter</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employee->bankDetails ?? [] as $b)
                            <tr>
                                <td>{{ $b->bank_name }}</td>
                                <td>{{ $b->bank_branch }}</td>
                                <td>{{ $b->bank_ac_holder }}</td>
                                <td>{{ $b->bank_ac_number }}</td>
                                <td>{{ $b->iban_number }}</td>
                                <td>{{ $b->swift_code }}</td>
                                <td>{{ $b->bank_currency }}</td>
                                <td>@if($b->att_iban_letter) <a href="{{ asset('storage/app/public/' . $b->att_iban_letter) }}" target="_blank">View</a> @else - @endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No bank details</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Educations --}}
    <div class="card mb-3">
        <div class="card-body">
            <h6 class="mb-3">Educational Qualification</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Qualification</th>
                            <th>University</th>
                            <th>Spec</th>
                            <th>Year</th>
                            <th>Result</th>
                            <th>GPA</th>
                            <th>Duration</th>
                            <th>Certificate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employee->educations ?? [] as $e)
                            <tr>
                                <td>{{ $e->qualification }}</td>
                                <td>{{ $e->university }}</td>
                                <td>{{ $e->specialization }}</td>
                                <td>{{ $e->year }}</td>
                                <td>{{ $e->result }}</td>
                                <td>{{ $e->gpa }}</td>
                                <td>{{ $e->duration_years }}</td>
                                <td>@if($e->certificate_path) <a href="{{ asset('storage/app/public/' . $e->certificate_path) }}" target="_blank">View</a> @else - @endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No education records</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Experiences --}}
    <div class="card mb-3">
        <div class="card-body">
            <h6 class="mb-3">Professional Experience</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Organization</th>
                            <th>Designation</th>
                            <th>Duration</th>
                            <th>Responsibilities</th>
                            <th>Certificate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employee->experiences ?? [] as $ex)
                            <tr>
                                <td>{{ $ex->organization }}</td>
                                <td>{{ $ex->designation }}</td>
                                <td>{{ ($ex->years ?: 0) . ' Y, ' . ($ex->months ?: 0) . ' M' }}</td>
                                <td>{{ $ex->responsibilities }}</td>
                                <td>@if($ex->certificate_path) <a href="{{ asset('storage/app/public/' . $ex->certificate_path) }}" target="_blank">View</a> @else - @endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No experience records</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Documents --}}
    <div class="card mb-5">
        <div class="card-body">
            <h6 class="mb-3">Documents</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Key</th>
                            <th>Name</th>
                            <th>Remarks</th>
                            <th>Expiry</th>
                            <th>File</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employee->documents ?? [] as $d)
                            <tr>
                                <td>{{ $d->group }}</td>
                                <td>{{ $d->key }}</td>
                                <td>{{ $d->name }}</td>
                                <td>{{ $d->remarks ?? '-' }}</td>
                                <td>{{ $d->expiry_date ? \Carbon\Carbon::parse($d->expiry_date)->format('d/m/Y') : '-' }}</td>
                                <td>@if($d->path) <a href="{{ asset('storage/app/public/' . $d->path) }}" target="_blank">View</a> @else - @endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No documents</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php } catch (\Exception $e) { ?> {{ $e }} <?php  } ?>


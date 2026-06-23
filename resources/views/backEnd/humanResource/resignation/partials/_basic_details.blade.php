<div class="col-lg-12 mb-4">
                                                        <div class="row gy-2">

                                                            {{-- Employee Name --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Employee Name <span
                                                                            class="text-danger">*</span></label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="employee_id" id="employee_id" required>
                                                                        <option value="">Select Employee</option>
                                                                        @foreach ($staffs as $staff)
                                                                            <option value="{{ $staff->id }}" {{ old('employee_id', isset($eosData['main']) ? $eosData['main']->employee_id : null) == $staff->id ? 'selected' : '' }}>
                                                                                {{ $staff->full_name }}
                                                                                ({{ $staff->staff_no }})</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Department --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Department</label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="department_id" id="department_id">
                                                                        <option value="">Select Department</option>
                                                                        @foreach ($departments as $department)
                                                                            <option value="{{ $department->id }}" {{ old('department_id', isset($eosData['main']) ? $eosData['main']->department_id : null) == $department->id ? 'selected' : '' }}>
                                                                                {{ $department->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Designation --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Designation</label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="designation_id" id="designation_id">
                                                                        <option value="">Select Designation</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Reporting Manager --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Reporting Manager</label>
                                                                    <select
                                                                        class="form-select form-select-sm js-example-basic-single"
                                                                        name="reporting_manager" id="reporting_manager">
                                                                        <option value="">Select Manager</option>
                                                                        @foreach ($staffs as $staff)
                                                                            <option value="{{ $staff->id }}" {{ old('reporting_manager', isset($eosData['main']) ? $eosData['main']->reporting_manager_id : null) == $staff->id ? 'selected' : '' }}>
                                                                                {{ $staff->full_name }}
                                                                                ({{ $staff->staff_no }})</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Separation Type --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Separation Type <span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="separation_type" id="separation_type"
                                                                        >
                                                                        <option value="">Select Type</option>
                                                                        <option value="resignation" {{ old('separation_type', isset($eosData['main']) ? $eosData['main']->separation_type : null) == 'resignation' ? 'selected' : '' }}>Resignation</option>
                                                                        <option value="termination" {{ old('separation_type', isset($eosData['main']) ? $eosData['main']->separation_type : null) == 'termination' ? 'selected' : '' }}>Termination</option>
                                                                        <option value="end_of_contract" {{ old('separation_type', isset($eosData['main']) ? $eosData['main']->separation_type : null) == 'end_of_contract' ? 'selected' : '' }}>End of Contract
                                                                        </option>
                                                                        <option value="retirement" {{ old('separation_type', isset($eosData['main']) ? $eosData['main']->separation_type : null) == 'retirement' ? 'selected' : '' }}>Retirement</option>
                                                                        <option value="absconding" {{ old('separation_type', isset($eosData['main']) ? $eosData['main']->separation_type : null) == 'absconding' ? 'selected' : '' }}>Absconding</option>
                                                                        <option value="death" {{ old('separation_type', isset($eosData['main']) ? $eosData['main']->separation_type : null) == 'death' ? 'selected' : '' }}>Death</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Resignation Type --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Resignation Type</label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="resignation_type" id="resignation_type">
                                                                        <option value="">Select Type</option>
                                                                        <option value="voluntary" {{ old('resignation_type', isset($eosData['main']) ? $eosData['main']->resignation_type : null) == 'voluntary' ? 'selected' : '' }}>Voluntary</option>
                                                                        <option value="involuntary" {{ old('resignation_type', isset($eosData['main']) ? $eosData['main']->resignation_type : null) == 'involuntary' ? 'selected' : '' }}>Involuntary</option>
                                                                        <option value="mutual_separation" {{ old('resignation_type', isset($eosData['main']) ? $eosData['main']->resignation_type : null) == 'mutual_separation' ? 'selected' : '' }}>Mutual Separation
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    {{-- Second Row: Initiated By, Reason Category, Detailed Reason --}}
                                                    <div class="col-lg-12 mb-4">
                                                        <div class="row gy-2">

                                                            {{-- Initiated By --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Initiated By</label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="initiated_by" id="initiated_by">
                                                                        <option value="">Select</option>
                                                                        <option value="employee" {{ old('initiated_by', isset($eosData['main']) ? $eosData['main']->initiated_by : null) == 'employee' ? 'selected' : '' }}>Employee</option>
                                                                        <option value="company" {{ old('initiated_by', isset($eosData['main']) ? $eosData['main']->initiated_by : null) == 'company' ? 'selected' : '' }}>Company</option>
                                                                        <option value="management" {{ old('initiated_by', isset($eosData['main']) ? $eosData['main']->initiated_by : null) == 'management' ? 'selected' : '' }}>Management</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Reason Category --}}
                                                            <div class="col-lg-2">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Reason Category</label>
                                                                    <select class="form-select form-select-sm"
                                                                        name="reason_category" id="reason_category">
                                                                        <option value="">Select Category</option>
                                                                        <option value="personal" {{ old('reason_category', isset($eosData['main']) ? $eosData['main']->reason_category : null) == 'personal' ? 'selected' : '' }}>Personal</option>
                                                                        <option value="performance" {{ old('reason_category', isset($eosData['main']) ? $eosData['main']->reason_category : null) == 'performance' ? 'selected' : '' }}>Performance</option>
                                                                        <option value="misconduct" {{ old('reason_category', isset($eosData['main']) ? $eosData['main']->reason_category : null) == 'misconduct' ? 'selected' : '' }}>Misconduct</option>
                                                                        <option value="redundancy" {{ old('reason_category', isset($eosData['main']) ? $eosData['main']->reason_category : null) == 'redundancy' ? 'selected' : '' }}>Redundancy</option>
                                                                        <option value="health" {{ old('reason_category', isset($eosData['main']) ? $eosData['main']->reason_category : null) == 'health' ? 'selected' : '' }}>Health</option>
                                                                        <option value="relocation" {{ old('reason_category', isset($eosData['main']) ? $eosData['main']->reason_category : null) == 'relocation' ? 'selected' : '' }}>Relocation</option>
                                                                        <option value="better_opportunity" {{ old('reason_category', isset($eosData['main']) ? $eosData['main']->reason_category : null) == 'better_opportunity' ? 'selected' : '' }}>Better
                                                                            Opportunity</option>
                                                                        <option value="other" {{ old('reason_category', isset($eosData['main']) ? $eosData['main']->reason_category : null) == 'other' ? 'selected' : '' }}>Other</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Detailed Reason --}}
                                                            <div class="col-lg-8">
                                                                <div class="input-effect">
                                                                    <label class="form-label mb-1">Detailed Reason</label>
                                                                    <textarea class="form-control form-control-sm" name="detailed_reason" id="detailed_reason" rows="2"
                                                                        placeholder="Provide detailed reason for separation">{{ old('detailed_reason', isset($eosData['main']) ? $eosData['main']->detailed_reason : '') }}</textarea>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    

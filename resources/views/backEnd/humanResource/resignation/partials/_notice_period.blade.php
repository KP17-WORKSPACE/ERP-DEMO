<div class="tab-pane fade show active" id="notice"
                                                            role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Notice Waiver --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Notice
                                                                            Waiver</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="notice_waiver">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Notice Waiver Approved By --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Notice Waiver
                                                                            Approved By</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="notice_waiver_approved_by">
                                                                            <option value="">Select</option>
                                                                            <option value="manager">Manager</option>
                                                                            <option value="hr">HR</option>
                                                                            <option value="management">Management</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Notice Period Served --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Notice Period
                                                                            Served</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="notice_period_served">
                                                                            <option value="">Select</option>
                                                                            <option value="full">Full</option>
                                                                            <option value="partial">Partial</option>
                                                                            <option value="not_served">Not Served</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Resignation Submitted Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Resignation
                                                                            Submitted Date <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="resignation_submitted_date" 
                                                                            value="{{ isset($eosData['notice']) ? $eosData['notice']->resignation_submitted_date : '' }}" >
                                                                    </div>
                                                                </div>

                                                                {{-- Notice Period (Days) --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Notice Period (Days)
                                                                            <span class="text-danger">*</span></label>
                                                                        <input type="number"
                                                                            class="form-control form-control-sm"
                                                                            name="notice_period_days" placeholder="30"
                                                                            value="{{ isset($eosData['notice']) ? $eosData['notice']->notice_period_days : '' }}" >
                                                                    </div>
                                                                </div>

                                                                {{-- Last Working Day --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Last Working
                                                                            Day</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="last_working_day">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2">
                                                                {{-- Garden Leave Applicable --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Garden Leave
                                                                            Applicable</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="garden_leave_applicable">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Garden Leave Start Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Garden Leave Start
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="garden_leave_start_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Garden Leave End Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Garden Leave End
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="garden_leave_end_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Relieving Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Relieving
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="relieving_date">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 3: Handover Process --}}
                                                        
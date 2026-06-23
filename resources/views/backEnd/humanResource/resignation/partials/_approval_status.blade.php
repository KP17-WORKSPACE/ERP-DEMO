<div class="tab-pane fade" id="approval" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- HR Approval Status --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">HR Approval
                                                                            Status</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="hr_approval_status">
                                                                            <option value="pending">Pending</option>
                                                                            <option value="approved">Approved</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Finance Approval Status --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Finance Approval
                                                                            Status</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="finance_approval_status">
                                                                            <option value="pending">Pending</option>
                                                                            <option value="approved">Approved</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Management Approval Status --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Management Approval
                                                                            Status</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="management_approval_status">
                                                                            <option value="pending">Pending</option>
                                                                            <option value="approved">Approved</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Exit Closed --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Exit Closed</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="exit_closed">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Exit Closure Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Exit Closure
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="exit_closure_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Record Locked --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Record
                                                                            Locked</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="record_locked">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2">
                                                                {{-- Confidential HR Remarks --}}
                                                                <div class="col-lg-6">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Confidential HR
                                                                            Remarks</label>
                                                                        <textarea class="form-control form-control-sm" name="confidential_hr_remarks" rows="3"
                                                                            placeholder="Confidential HR remarks (internal use only)"></textarea>
                                                                    </div>
                                                                </div>

                                                                {{-- Attachment --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Attachment
                                                                            (Approval)</label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="hr_remarks_attachment"
                                                                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                        <small class="text-muted">PDF, DOC, JPG,
                                                                            PNG</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 11: Documents --}}
                                                        
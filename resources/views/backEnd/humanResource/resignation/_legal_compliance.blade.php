<div class="tab-pane fade" id="legal" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Visa Type --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Visa Type</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="visa_type">
                                                                            <option value="">Select</option>
                                                                            <option value="company">Company</option>
                                                                            <option value="partner">Partner</option>
                                                                            <option value="family">Family</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Visa Cancellation Required --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Visa Cancellation
                                                                            Required</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="visa_cancellation_required">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Visa Cancellation Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Visa Cancellation
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="visa_cancellation_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Labour Card Cancellation Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Labour Card
                                                                            Cancellation Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="labour_card_cancellation_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Immigration Clearance Status --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Immigration
                                                                            Clearance Status</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="immigration_clearance_status">
                                                                            <option value="">Select</option>
                                                                            <option value="pending">Pending</option>
                                                                            <option value="in_progress">In Progress
                                                                            </option>
                                                                            <option value="completed">Completed</option>
                                                                            <option value="not_applicable">Not Applicable
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Exit Permit Issued --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Exit Permit
                                                                            Issued</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="exit_permit_issued">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2">
                                                                {{-- MOHRE Clearance Uploaded --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">MOHRE Clearance
                                                                            Uploaded</label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="mohre_clearance_document"
                                                                            accept=".pdf,.jpg,.jpeg,.png">
                                                                        <small class="text-muted">PDF, JPG, PNG</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Visa Cancellation Document --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Visa Cancellation
                                                                            Document</label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="visa_cancellation_document"
                                                                            accept=".pdf,.jpg,.jpeg,.png">
                                                                        <small class="text-muted">PDF, JPG, PNG</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Labour Cancellation Document --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Labour Cancellation
                                                                            Document</label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="labour_cancellation_document"
                                                                            accept=".pdf,.jpg,.jpeg,.png">
                                                                        <small class="text-muted">PDF, JPG, PNG</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 9: Exit Interview --}}
                                                        
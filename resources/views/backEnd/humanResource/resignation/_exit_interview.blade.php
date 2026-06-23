<div class="tab-pane fade" id="exit-interview" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Exit Interview Conducted --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Exit Interview
                                                                            Conducted</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="exit_interview_conducted"
                                                                            id="exit_interview_conducted">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Exit Interview Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Exit Interview
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="exit_interview_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Interview Mode --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Interview
                                                                            Mode</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="interview_mode">
                                                                            <option value="">Select</option>
                                                                            <option value="in_person">In-Person</option>
                                                                            <option value="online">Online</option>
                                                                            <option value="phone">Phone</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Overall Satisfaction Rating --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Overall Satisfaction
                                                                            Rating</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="satisfaction_rating">
                                                                            <option value="">Select (1-5)</option>
                                                                            <option value="1">1 - Very Dissatisfied
                                                                            </option>
                                                                            <option value="2">2 - Dissatisfied
                                                                            </option>
                                                                            <option value="3">3 - Neutral</option>
                                                                            <option value="4">4 - Satisfied</option>
                                                                            <option value="5">5 - Very Satisfied
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Manager Feedback --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Manager
                                                                            Feedback</label>
                                                                        <textarea class="form-control form-control-sm" name="manager_feedback" rows="2"
                                                                            placeholder="Manager feedback"></textarea>
                                                                    </div>
                                                                </div>

                                                                {{-- HR Feedback --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">HR Feedback</label>
                                                                        <textarea class="form-control form-control-sm" name="hr_feedback" rows="2" placeholder="HR feedback"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 10: Approval Status --}}
                                                        
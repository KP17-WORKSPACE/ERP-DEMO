<div class="tab-pane fade" id="eos-calc" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Leave Balance at Exit --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Leave Balance at
                                                                            Exit</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="leave_balance_at_exit" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Leave Encashment Eligible --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Leave Encashment
                                                                            Eligible</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="leave_encashment_eligible">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Leave Encashment Days --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Leave Encashment
                                                                            Days</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="leave_encashment_days" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Leave Encashment Amount --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Leave Encashment
                                                                            Amount</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="leave_encashment_amount" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- EOS Eligibility --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">EOS
                                                                            Eligibility</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="eos_eligibility" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- EOS Calculation Method --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">EOS Calculation
                                                                            Method</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="eos_calculation_method" readonly
                                                                            placeholder="Auto (UAE Law / Contract)">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2 mb-3">
                                                                {{-- Basic Salary for EOS --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Basic Salary for
                                                                            EOS</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="basic_salary_for_eos" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Gratuity Amount --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Gratuity
                                                                            Amount</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="gratuity_amount" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Other Allowances Payable --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Other Allowances
                                                                            Payable</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="other_allowances_payable" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Loan / Advance Outstanding --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Loan / Advance
                                                                            Outstanding</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="loan_advance_outstanding" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Deductions (Fines, Assets, Notice) --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Deductions (Fines,
                                                                            Assets, Notice)</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="deductions_total" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Total Deductions --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Total
                                                                            Deductions</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light"
                                                                            name="total_deductions" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2">
                                                                {{-- Net EOS Payable --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Net EOS
                                                                            Payable</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm bg-light fw-bold"
                                                                            name="net_eos_payable" readonly
                                                                            placeholder="Auto">
                                                                        <small class="text-muted">Auto calculated</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Payroll Closure Status --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Payroll Closure
                                                                            Status</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="payroll_closure_status">
                                                                            <option value="">Select</option>
                                                                            <option value="open">Open</option>
                                                                            <option value="processing">Processing</option>
                                                                            <option value="closed">Closed</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 7: Final Settlement --}}
                                                        
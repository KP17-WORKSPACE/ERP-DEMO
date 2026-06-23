<div class="tab-pane fade" id="final-settlement" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Full & Final Settlement Status --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Full & Final
                                                                            Settlement Status</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="fnf_settlement_status">
                                                                            <option value="pending">Pending</option>
                                                                            <option value="processed">Processed</option>
                                                                            <option value="paid">Paid</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Mode of Payment --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Mode of
                                                                            Payment</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="mode_of_payment">
                                                                            <option value="">Select</option>
                                                                            <option value="bank_transfer">Bank Transfer
                                                                            </option>
                                                                            <option value="cheque">Cheque</option>
                                                                            <option value="cash">Cash</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Payment Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Payment Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="payment_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Bank / Cheque Reference No. --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Bank / Cheque
                                                                            Reference No.</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm"
                                                                            name="bank_cheque_reference"
                                                                            placeholder="Reference No.">
                                                                    </div>
                                                                </div>

                                                                {{-- Final Settlement Sheet --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Final Settlement
                                                                            Sheet</label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="final_settlement_sheet"
                                                                            accept=".pdf,.doc,.docx,.xls,.xlsx">
                                                                        <small class="text-muted">PDF, DOC, XLS</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Payslip (Final Month) --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Payslip (Final
                                                                            Month)</label>
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="final_payslip" accept=".pdf,.doc,.docx">
                                                                        <small class="text-muted">PDF, DOC</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 8: Legal & Compliance --}}
                                                        
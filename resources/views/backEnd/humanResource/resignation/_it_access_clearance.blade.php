<div class="tab-pane fade" id="it" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Email Access Disabled --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Email Access
                                                                            Disabled</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="email_access_disabled">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                        <small class="text-muted">Triggered after HR
                                                                            approval</small>
                                                                    </div>
                                                                </div>

                                                                {{-- ERP/System Access Revoked --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">ERP/System Access
                                                                            Revoked</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="erp_access_revoked">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                        <small class="text-muted">SAP / ERP / CRM /
                                                                            HRMS</small>
                                                                    </div>
                                                                </div>

                                                                {{-- SIM Deactivation Confirmed --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">SIM Deactivation
                                                                            Confirmed</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="sim_deactivation">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                        <small class="text-muted">Telecom
                                                                            confirmation</small>
                                                                    </div>
                                                                </div>

                                                                {{-- VPN Access Revoked --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">VPN Access
                                                                            Revoked</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="vpn_access_revoked">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                        <small class="text-muted">Remote access
                                                                            closure</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Data Backup Completed --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Data Backup
                                                                            Completed</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="data_backup_completed">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                        <small class="text-muted">Business data backed
                                                                            up</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Passwords Handed Over --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Passwords Handed
                                                                            Over</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="passwords_handed_over">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                        <small class="text-muted">Admin / system
                                                                            credentials</small>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2">
                                                                {{-- Asset Return Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Asset Return
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="it_asset_return_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Asset Damage/Missing --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Asset
                                                                            Damage/Missing</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="it_asset_damage">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Asset Recovery Amount --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Asset Recovery
                                                                            Amount</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control form-control-sm"
                                                                            name="it_recovery_amount" placeholder="0.00">
                                                                    </div>
                                                                </div>

                                                                {{-- Clearance Completed Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Clearance Completed
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="clearance_completed_date">
                                                                        <small class="text-muted">Locks F&F
                                                                            processing</small>
                                                                    </div>
                                                                </div>

                                                                {{-- Final Clearance Approved By --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Final Clearance
                                                                            Approved By</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="final_clearance_approved_by">
                                                                            <option value="">Select</option>
                                                                            <option value="1">IT Head</option>
                                                                            <option value="2">System Admin</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 6: EOS Calculation --}}
                                                        
<div class="tab-pane fade" id="handover" role="tabpanel">
                                                            <div class="row gy-2 mb-3">
                                                                {{-- Knowledge Transfer Required --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Knowledge Transfer
                                                                            Required</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="knowledge_transfer_required" >
                                                                            <option value="">Select</option>
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Handover Start Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Handover Start
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="handover_start_date" >
                                                                    </div>
                                                                </div>

                                                                {{-- Handover End Date --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Handover End
                                                                            Date</label>
                                                                        <input type="text"
                                                                            class="form-control form-control-sm date-picker"
                                                                            name="handover_end_date">
                                                                    </div>
                                                                </div>

                                                                {{-- Handover To (Employee) --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Handover To
                                                                            (Employee)</label>
                                                                        <select
                                                                            class="form-select form-select-sm js-example-basic-single"
                                                                            name="handover_to_employee">
                                                                            <option value="">Select Employee</option>
                                                                            <option value="1">John Smith</option>
                                                                            <option value="2">Sarah Johnson</option>
                                                                            <option value="3">Michael Brown</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Successor Assigned --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Successor
                                                                            Assigned</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="successor_assigned">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Successor Name --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Successor
                                                                            Name</label>
                                                                        <select
                                                                            class="form-select form-select-sm js-example-basic-single"
                                                                            name="successor_name">
                                                                            <option value="">Select</option>
                                                                            <option value="1">John Smith</option>
                                                                            <option value="2">Sarah Johnson</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row gy-2">
                                                                {{-- Client/Project Handover Completed --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Client/Project
                                                                            Handover Completed</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="client_handover_completed">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- SOP/Documentation Shared --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">SOP/Documentation
                                                                            Shared</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="sop_shared">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Handover Checklist Completed --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Handover Checklist
                                                                            Completed</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="handover_checklist_completed">
                                                                            <option value="no">No</option>
                                                                            <option value="yes">Yes</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Manager Handover Approval --}}
                                                                <div class="col-lg-2">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Manager Handover
                                                                            Approval</label>
                                                                        <select class="form-select form-select-sm"
                                                                            name="manager_handover_approval">
                                                                            <option value="pending">Pending</option>
                                                                            <option value="approved">Approved</option>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                {{-- Handover Notes --}}
                                                                <div class="col-lg-4">
                                                                    <div class="input-effect">
                                                                        <label class="form-label mb-1">Handover
                                                                            Notes</label>
                                                                        <textarea class="form-control form-control-sm" name="handover_notes" rows="2" placeholder="Handover notes"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- TAB 4: Asset Clearance --}}
                                                        
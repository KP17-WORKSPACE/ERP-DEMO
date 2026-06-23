<div class="tab-pane fade" id="documents" role="tabpanel">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered table-sm"
                                                                    id="documentsTable">
                                                                    <thead class="table-light">
                                                                        <tr class="text-center">
                                                                            <th style="width: 35%;">Document Name</th>
                                                                            <th style="width: 15%;">Date</th>
                                                                            <th style="width: 25%;">Attachment</th>
                                                                            <th style="width: 25%;">Remarks</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        {{-- A. Resignation / Termination Documents --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>A. Resignation /
                                                                                    Termination Documents</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Resignation Letter / Email</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_resignation_letter_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_resignation_letter"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_resignation_letter_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Termination Letter</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_termination_letter_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_termination_letter"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_termination_letter_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Mutual Separation Agreement</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_mutual_separation_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_mutual_separation"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_mutual_separation_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Notice Period Waiver Letter</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_notice_waiver_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_notice_waiver"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_notice_waiver_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Garden Leave Confirmation</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_garden_leave_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_garden_leave"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_garden_leave_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- B. Disciplinary & Performance Records --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>B. Disciplinary &
                                                                                    Performance Records</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Warning Letters (1st / 2nd / Final)</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_warning_letters_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_warning_letters"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                                                    multiple></td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_warning_letters_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Show Cause Notice</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_show_cause_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_show_cause"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_show_cause_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Disciplinary Action Record</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_disciplinary_action_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_disciplinary_action"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_disciplinary_action_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Performance Improvement Plan (PIP)</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_pip_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_pip"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_pip_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- C. Medical & Personal Justification --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>C. Medical &
                                                                                    Personal Justification</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Medical Certificate</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_medical_cert_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_medical_cert"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_medical_cert_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Fitness / Unfitness Report</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_fitness_report_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_fitness_report"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_fitness_report_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Compassionate / Emergency Proof</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_emergency_proof_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_emergency_proof"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_emergency_proof_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- D. Employment Confirmation Documents --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>D. Employment
                                                                                    Confirmation Documents</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Service Certificate</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_service_cert_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_service_cert"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_service_cert_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Experience Letter</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_experience_letter_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_experience_letter"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_experience_letter_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Relieving Letter</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_relieving_letter_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_relieving_letter"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_relieving_letter_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>No Objection Certificate (NOC)</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_noc_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_noc"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_noc_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- E. Knowledge Transfer & Handover --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>E. Knowledge
                                                                                    Transfer & Handover</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Handover Checklist</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_handover_checklist_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_handover_checklist"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_handover_checklist_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Knowledge Transfer Sign-off</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_kt_signoff_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_kt_signoff"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_kt_signoff_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Successor Acceptance Form</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_successor_form_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_successor_form"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_successor_form_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- F. Asset & Access Clearance Documents --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>F. Asset & Access
                                                                                    Clearance Documents</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Asset Return Acknowledgement</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_asset_return_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_asset_return"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_asset_return_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>IT Access Revocation Confirmation</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_it_revocation_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_it_revocation"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_it_revocation_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>SIM / Email Deactivation Proof</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_sim_email_deactivation_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_sim_email_deactivation"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_sim_email_deactivation_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- G. Payroll & Financial Settlement --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>G. Payroll &
                                                                                    Financial Settlement</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Full & Final Settlement Statement</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_fnf_statement_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_fnf_statement"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_fnf_statement_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Gratuity Calculation Sheet</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_gratuity_sheet_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_gratuity_sheet"
                                                                                    accept=".pdf,.doc,.docx,.xls,.xlsx">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_gratuity_sheet_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Leave Encashment Calculation</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_leave_encashment_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_leave_encashment"
                                                                                    accept=".pdf,.doc,.docx,.xls,.xlsx">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_leave_encashment_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Salary Deduction Approval</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_salary_deduction_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_salary_deduction"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_salary_deduction_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Final Payslip</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_final_payslip_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_final_payslip"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_final_payslip_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- H. Compliance & Legal Records --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>H. Compliance &
                                                                                    Legal Records</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Exit Interview Form</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_exit_interview_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_exit_interview"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_exit_interview_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Legal Clearance Confirmation</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_legal_clearance_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_legal_clearance"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_legal_clearance_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Labour / MOHRE Acknowledgement</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_mohre_ack_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_mohre_ack"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_mohre_ack_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Immigration / Visa Cancellation Proof</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_visa_cancel_proof_date">
                                                                            </td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_visa_cancel_proof"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_visa_cancel_proof_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>

                                                                        {{-- I. Miscellaneous / Supporting --}}
                                                                        <tr class="table-secondary">
                                                                            <td colspan="4"><strong>I. Miscellaneous /
                                                                                    Supporting</strong></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Other Supporting Documents</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_other_supporting_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_other_supporting"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                                                                    multiple></td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_other_supporting_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>HR Remarks / Internal Notes</td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm date-picker"
                                                                                    name="doc_hr_notes_date"></td>
                                                                            <td><input type="file"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_hr_notes"
                                                                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                                            </td>
                                                                            <td><input type="text"
                                                                                    class="form-control form-control-sm"
                                                                                    name="doc_hr_notes_remarks"
                                                                                    placeholder="Remarks"></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    
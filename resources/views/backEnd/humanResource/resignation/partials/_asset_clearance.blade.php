<div class="tab-pane fade" id="asset" role="tabpanel">
    <div class="mb-2 text-end">
        <button type="button" class="btn btn-light text-dark d-inline-flex align-items-center" id="addAssetRow">
            <i class="ico icon-outline-add-square text-success"></i><span class="btn-text ms-1">Asset</span>
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm" id="assetTable">
            <thead class="table-light">
                <tr>
                    <th>Name of Assets</th>
                    <th>Applicable</th>
                    <th>Serial Number</th>
                    <th>Asset Return Date</th>
                    <th>Asset Condition</th>
                    <th>Asset Recovery Amount</th>
                    <th>Verified By</th>
                    <th>Damage Remarks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" class="form-control form-control-sm" name="assets[0][name]"
                            value="Laptop Returned"></td>
                    <td>
                        <select class="form-select form-select-sm" name="assets[0][applicable]">
                            <option value="na">N/A</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control form-control-sm" name="assets[0][serial_number]"></td>
                    <td><input type="text" class="form-control form-control-sm date-picker"
                            name="assets[0][return_date]"></td>
                    <td>
                        <select class="form-select form-select-sm asset-condition" name="assets[0][condition]">
                            <option value="good">Good</option>
                            <option value="damaged">Damaged
                            </option>
                            <option value="missing">Missing
                            </option>
                        </select>
                    </td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm"
                            name="assets[0][recovery_amount]"></td>
                    <td>
                        <select class="form-select form-select-sm" name="assets[0][verified_by]">
                            <option value="">Select</option>
                            <option value="1">IT Admin
                            </option>
                            <option value="2">HR Manager
                            </option>
                        </select>
                    </td>
                    <td>
                        <textarea class="form-control form-control-sm damage-remarks" name="assets[0][damage_remarks]"
                            rows="1" placeholder="Mandatory if damaged/missing"></textarea>
                    </td>
                    <td><button type="button" class="btn btn-sm btn-light remove-asset-row"><i
                                class="ico icon-outline-trash-bin-minimalistic text-dark"></i></button>
                    </td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control form-control-sm" name="assets[1][name]"
                            value="Mobile Phone Returned"></td>
                    <td>
                        <select class="form-select form-select-sm" name="assets[1][applicable]">
                            <option value="na">N/A</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control form-control-sm" name="assets[1][serial_number]"></td>
                    <td><input type="text" class="form-control form-control-sm date-picker"
                            name="assets[1][return_date]"></td>
                    <td>
                        <select class="form-select form-select-sm asset-condition" name="assets[1][condition]">
                            <option value="good">Good</option>
                            <option value="damaged">Damaged
                            </option>
                            <option value="missing">Missing
                            </option>
                        </select>
                    </td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm"
                            name="assets[1][recovery_amount]"></td>
                    <td>
                        <select class="form-select form-select-sm" name="assets[1][verified_by]">
                            <option value="">Select</option>
                            <option value="1">IT Admin
                            </option>
                            <option value="2">HR Manager
                            </option>
                        </select>
                    </td>
                    <td>
                        <textarea class="form-control form-control-sm damage-remarks" name="assets[1][damage_remarks]"
                            rows="1"></textarea>
                    </td>
                    <td><button type="button" class="btn btn-sm btn-light remove-asset-row"><i
                                class="ico icon-outline-trash-bin-minimalistic text-dark"></i></button>
                    </td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control form-control-sm" name="assets[2][name]"
                            value="Access Card / ID Returned"></td>
                    <td>
                        <select class="form-select form-select-sm" name="assets[2][applicable]">
                            <option value="na">N/A</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control form-control-sm" name="assets[2][serial_number]"></td>
                    <td><input type="text" class="form-control form-control-sm date-picker"
                            name="assets[2][return_date]"></td>
                    <td>
                        <select class="form-select form-select-sm asset-condition" name="assets[2][condition]">
                            <option value="good">Good</option>
                            <option value="damaged">Damaged
                            </option>
                            <option value="missing">Missing
                            </option>
                        </select>
                    </td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm"
                            name="assets[2][recovery_amount]"></td>
                    <td>
                        <select class="form-select form-select-sm" name="assets[2][verified_by]">
                            <option value="">Select</option>
                            <option value="1">Security
                            </option>
                            <option value="2">HR Manager
                            </option>
                        </select>
                    </td>
                    <td>
                        <textarea class="form-control form-control-sm damage-remarks" name="assets[2][damage_remarks]"
                            rows="1"></textarea>
                    </td>
                    <td><button type="button" class="btn btn-sm btn-light remove-asset-row"><i
                                class="ico icon-outline-trash-bin-minimalistic text-dark"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

{{-- TAB 5: IT & Access Clearance --}}
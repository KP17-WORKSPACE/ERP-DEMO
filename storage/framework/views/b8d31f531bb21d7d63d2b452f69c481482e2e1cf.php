<?php
$com_ids = session('logged_session_data.company_id');
$accounts = @App\SysChartofAccounts::select('id', 'account_name', 'account_code')->whereRaw("find_in_set($com_ids,sys_chartofaccounts.company_access)")->where('main_account_id', 0)->where('account_code', 'like', 'ACC%')->get();
$employees = @App\SmStaff::select('id', 'full_name')
    ->where('active_status', 1)
    ->where(function ($query) use ($com_ids) {
        $query->where('company_id', $com_ids)
            ->orWhereRaw("find_in_set($com_ids,company_access)");
    })
    ->orderBy('full_name', 'asc')
    ->get();
?>
    <div class="modal modal-draggable side-panel fade" id="accountSubEmployeeModal" data-bs-backdrop="false" tabindex="-1"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="editModalLabel">Add Employee Sub Account</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                        <?php echo Form::open([
                    'class' => 'form-horizontal',
                    'files' => true,
                    'url' => 'chartofaccounts-employee-sub-store',
                    'method' => 'post',
                    'id' => 'chartofaccounts-employee-sub-store',
                ]); ?>


                        <input type="hidden" name="url" id="url" value="<?php echo e(URL::to('/')); ?>">
                        <input type="hidden" name="catid" id="catid" value="2">
                <div class="modal-body m-0 p-0">
                    <div class="card mb-0 mt-0">
                        <div class="card-body bg-white">
                            <div class="row">
                        <div class="col-md-6">Employee Name
                            <select id="employee_id" name="employee_id" class="form-control js-example-basic-single" required
                                onchange="set_acc_name()">
                                <option value="">Select Employee</option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($employee->id); ?>"><?php echo e($employee->full_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">Accounts To Create
                            <div class="form-control" style="height: auto;">
                                <input type="checkbox" id="account_id_emp1" name="account_id_emp[]"
                                    value="employee_telephone_expenses" checked>
                                <label for="account_id_emp1"><span id="accname_1"></span> Telephone Expenses</label><br>
                                <input type="checkbox" id="account_id_emp2" name="account_id_emp[]"
                                    value="employee_airfare_expenses" checked>
                                <label for="account_id_emp2"><span id="accname_2"></span> Airfare Expenses</label><br>
                                <input type="checkbox" id="account_id_emp3" name="account_id_emp[]"
                                    value="employee_food_expenses" checked>
                                <label for="account_id_emp3"><span id="accname_3"></span> Food Expenses</label><br>
                                <input type="checkbox" id="account_id_emp4" name="account_id_emp[]"
                                    value="employee_salary" checked>
                                <label for="account_id_emp4"><span id="accname_4"></span> Salary</label><br>
                                <input type="checkbox" id="account_id_emp5" name="account_id_emp[]"
                                    value="employee_gratuity" checked>
                                <label for="account_id_emp5"><span id="accname_5"></span> Gratuity</label><br>
                                <input type="checkbox" id="account_id_emp6" name="account_id_emp[]"
                                    value="employee_visa_expenses" checked>
                                <label for="account_id_emp6"><span id="accname_6"></span> Visa Expenses</label><br>
                                <input type="checkbox" id="account_id_emp7" name="account_id_emp[]"
                                    value="employee_travelling_expenses" checked>
                                <label for="account_id_emp7"><span id="accname_7"></span> Travelling Expenses</label><br>
                                <input type="checkbox" id="account_id_emp8" name="account_id_emp[]"
                                    value="employee_parking_expenses" checked>
                                <label for="account_id_emp8"><span id="accname_8"></span> Parking Expenses</label><br>
                                <input type="checkbox" id="account_id_emp9" name="account_id_emp[]"
                                    value="employee_petrol_expenses" checked>
                                <label for="account_id_emp9"><span id="accname_9"></span> Petrol Expenses</label><br>
                                <input type="checkbox" id="account_id_emp10" name="account_id_emp[]"
                                    value="employee_vehicle_maintenance" checked>
                                <label for="account_id_emp10"><span id="accname_10"></span> Vehicle Maintenance</label>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSubEEAccountSubmit" class="btn btn-light"
                        onclick="return confirm('Are you sure you want to Create this Accounts?');"> <i class="ico icon-outline-bookmark-opened text-success"></i> Create Accounts</button>
                </div>
                        <?php echo e(Form::close()); ?>

            </div>
        </div>
    </div>
<script>
    function set_acc_name() {
            var ename = $('#employee_id option:selected').text();
            if (ename === 'Select Employee') {
                ename = '';
            }
            $('#accname_1').text(ename);
            $('#accname_2').text(ename);
            $('#accname_3').text(ename);
            $('#accname_4').text(ename);
            $('#accname_5').text(ename);
            $('#accname_6').text(ename);
            $('#accname_7').text(ename);
            $('#accname_8').text(ename);
            $('#accname_9').text(ename);
            $('#accname_10').text(ename);
        }
    $(document).ready(function() {
        $("#btnSubEEAccountSubmit").click(function() {
            setTimeout(function() {
                disableButton();
            }, 0);
        });

        function disableButton() {
            $("#btnSubEEAccountSubmit").prop('disabled', true);
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
    const modal = document.querySelector("#accountSubEmployeeModal .modal-dialog");
    const header = document.querySelector("#accountSubEmployeeModal .modal-header");

    let isDragging = false, offsetX, offsetY;

    header.addEventListener("mousedown", function (e) {
        isDragging = true;
        const rect = modal.getBoundingClientRect();
        offsetX = e.clientX - rect.left;
        offsetY = e.clientY - rect.top;
        modal.style.position = "absolute";
        modal.style.margin = "0"; // remove bootstrap margin
    });

    document.addEventListener("mousemove", function (e) {
        if (isDragging) {
            modal.style.left = (e.clientX - offsetX) + "px";
            modal.style.top = (e.clientY - offsetY) + "px";
        }
    });

    document.addEventListener("mouseup", function () {
        isDragging = false;
    });
});
</script>
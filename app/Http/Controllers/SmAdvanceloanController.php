<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SmStaff;
use App\SmAdvanceloan;
use App\SmModuleLink;
use App\SmRolePermission;
use Auth;
use App\SmHrPayrollGenerate;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SmAdvanceloanController extends Controller
{
    private $loanTypes = [
        ['id' => 1, 'name' => 'Salary Advance'],
        ['id' => 2, 'name' => 'Personal Loan'],
        ['id' => 3, 'name' => 'Emergency Loan'],
        ['id' => 4, 'name' => 'Festival Advance'],
    ];

    private $loanCategories = [
        'Personal', 'Medical', 'Education', 'Emergency', 'Vehicle', 'Housing',
        'Travel', 'Marriage', 'Family Support', 'Other',
    ];

    public function addLoanCreate()
    {
        $staffs = SmStaff::where('active_status', 1)->get();
        $loan_lists = SmAdvanceloan::all();
        return view('backEnd.humanResource.advanceloan.addLoanCreate', compact('staffs', 'loan_lists'));
    }

    public function loanStore(Request $request)
    {
        $request->validate([
            'staff' => 'required',
            'date' => 'required',
            'amount' => 'required'
        ]);

        $loan = new SmAdvanceloan();
        $loan->staff_id = $request->staff;
        $loan->date = $request->date != "" ? date('Y-m-d', strtotime($request->date)) : '';
        $loan->amount = $request->amount;
        $loan->note = $request->note;
        $result = $loan->save();

        if ($result) {
            return redirect()->back()->with('message-success', 'Loan has been created successfully');
        }
        return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
    }

    public function loanList()
    {
        $loan_lists = SmAdvanceloan::all()->groupBy('staff_id');
        return view('backEnd.humanResource.advanceloan.loanList', compact('loan_lists'));
    }

    public function loanView($staff_id)
    {
        $staffDetails = SmStaff::find($staff_id);
        $loan_lists = SmAdvanceloan::where('staff_id', $staff_id)->get();
        $deduct_lists = SmHrPayrollGenerate::where('staff_id', $staff_id)->get();
        return view('backEnd.humanResource.advanceloan.loanView', compact('staffDetails', 'loan_lists', 'deduct_lists'));
    }

    public function loanEdit($id)
    {
        $staffs = SmStaff::all();
        $loan_lists = SmAdvanceloan::all();
        $editData = SmAdvanceloan::find($id);
        return view('backEnd.humanResource.advanceloan.addLoanCreate', compact('staffs', 'loan_lists', 'editData'));
    }

    public function loanUpdate(Request $request)
    {
        $request->validate([
            'staff' => 'required',
            'date' => 'required',
            'amount' => 'required'
        ]);

        $loan = SmAdvanceloan::find($request->id);
        $loan->staff_id = $request->staff;
        $loan->date = $request->date != "" ? date('Y-m-d', strtotime($request->date)) : '';
        $loan->amount = $request->amount;
        $loan->note = $request->note;
        $result = $loan->save();

        if ($result) {
            return redirect('add-loan')->with('message-success', 'Loan has been updated successfully');
        }
        return redirect()->back()->with('message-danger', 'Something went wrong, please try again');
    }

    public function loanDelete($id)
    {
        $loan = SmAdvanceloan::findOrFail($id);
        if (!$this->hasLoanPermission('request', 'delete') || !$this->canAccessLoanRequest($loan)) {
            abort(403, 'You are not authorized to delete this loan request.');
        }
        $result = SmAdvanceloan::destroy($id);
        if ($result) {
            return redirect('add-loan')->with('message-success-delete', 'Loan has been deleted successfully');
        }
        return redirect()->back()->with('message-danger-delete', 'Something went wrong, please try again');
    }

    public function index(Request $request)
    {
        if (!$this->hasLoanPermission('request', 'view')) {
            abort(403, 'You are not authorized to view Loan & Advances.');
        }

        $staffId = optional(Auth::user()->staff)->id ?? Auth::user()->id;
        $loanColumns = [
            'id','staff_id','type_id','request_type','loan_category','amount','installments','amount_per_month',
            'repayment_start','repayment_end_month','repayment_mode','requested_disbursement_date','purpose',
            'guarantor_employee_id','guarantor_employee_no','guarantor_department','guarantor_contact_number',
            'early_settlement_allowed','grace_period_required','grace_period_months','attachment',
            'attachment_remarks','paid_amount','status','manager_approval','finance_approval','hr_approval',
            'management_approval','payment_approval','declaration_accepted_at','date','created_at','updated_at',
            'urgency_level'
        ];
        if (\Schema::hasColumn('sm_advanceloans', 'loan_number')) {
            $loanColumns[] = 'loan_number';
        }
        $staffColumns = ['id', 'full_name', 'first_name', 'last_name', 'staff_no'];
        $query = SmAdvanceloan::select($loanColumns)
            ->with([
                'staffDetails:' . implode(',', $staffColumns),
                'guarantorStaff:' . implode(',', $staffColumns),
            ]);
        if (!$this->isLoanAdmin()) {
            $query->where('staff_id', $staffId);
        }
        $this->applyRequestFilters($query, $request);

        $loans = $query->orderBy('id', 'desc')->simplePaginate(10)->appends($request->query());
        $loans->getCollection()->transform(function ($loan) {
            return $this->prepareLoanListRow($loan);
        });
        $loanTypes = $this->loanTypeCollection();
        $loanCategories = $this->loanCategories;
        $employees = SmStaff::select('id', 'full_name', 'first_name', 'last_name', 'staff_no')
            ->where('active_status', 1)
            ->orderBy('full_name')
            ->get();
        $employee = $this->currentStaff();
        
        $requestNumber = $this->nextLoanNumber(optional($employee)->company_id);

        $selectedLoan = null;
        if ($request->get('active')) {
            $selectedLoan = SmAdvanceloan::with('staffDetails')
                ->find($request->get('active'));
            if ($selectedLoan && !$this->canAccessLoanRequest($selectedLoan)) {
                $selectedLoan = null;
            }
        }
        if (!$selectedLoan && $loans->count()) {
            $selectedLoan = SmAdvanceloan::with('staffDetails')
                ->find($loans->first()->id);
        }

        $loanPermissions = $this->loanPermissionSet('request');
        $trackPermissions = $this->loanPermissionSet('track');
        $reportPermissions = $this->loanPermissionSet('report');
        $permissions = SmRolePermission::where('role_id', Auth::user()->role_id)->get();

        return view('backEnd.humanResource.loans.index', compact('loans', 'selectedLoan', 'loanTypes', 'loanCategories', 'employees', 'employee', 'requestNumber', 'loanPermissions', 'trackPermissions', 'reportPermissions', 'permissions'));
    }

    public function show($id)
    {
        $loan = SmAdvanceloan::with('staffDetails')->find($id);
        if (!$loan) {
            return response('<p class="text-danger">Record not found.</p>');
        }
        if (!$this->hasLoanPermission('request', 'view') || !$this->canAccessLoanRequest($loan)) {
            return response('<p class="text-danger">You are not authorized to view this loan request.</p>', 403);
        }

        $loanTypes = $this->loanTypeCollection();
        $isApprovals = false;
        $loanPermissions = $this->loanPermissionSet('request');
        $trackPermissions = $this->loanPermissionSet('track');
        $reportPermissions = $this->loanPermissionSet('report');
        return view('backEnd.humanResource.loans._details', compact('loan', 'loanTypes', 'isApprovals', 'loanPermissions', 'trackPermissions', 'reportPermissions'));
    }

    public function create()
    {
        return redirect()->route('employee.loans.index');
    }

    public function store(Request $request)
    {
        if (!$this->hasLoanPermission('request', 'create')) {
            abort(403, 'You are not authorized to create Loan & Advances.');
        }
        if ($request->hasFile('attachment') && !$this->hasLoanPermission('request', 'attach')) {
            return $this->loanPermissionDeniedResponse($request, 'You are not authorized to attach loan documents.');
        }

        $action = $request->input('action_type') === 'draft' ? 'draft' : 'submit';
        $loan = new SmAdvanceloan();
        $this->validateLoanRequest($request, $action, $loan);

        $staff = $this->currentStaff();
        $staffId = optional($staff)->id ?? Auth::user()->id;
        $this->fillLoanRequest($loan, $request, $staffId, $action);

        $companyId = $staff ? $staff->company_id : session('logged_session_data.company_id');
        $loan->company_id = $companyId;
        $loan->loan_number = $this->nextLoanNumber($companyId);

        $loan->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'id' => $loan->id,
                'request_number' => $loan->document_number,
                'message' => $action === 'draft' ? 'Loan / Advance draft saved successfully.' : 'Loan / Advance request submitted successfully.',
            ]);
        }

        return redirect()->route('employee.loans.index', ['active' => $loan->id])
            ->with('success', $action === 'draft' ? 'Loan / Advance draft saved successfully.' : 'Loan / Advance request submitted successfully.');
    }

    public function guarantor($id)
    {
        $staff = SmStaff::with('departments')->find($id);
        if (!$staff) {
            return response()->json(['found' => false], 404);
        }

        return response()->json([
            'found' => true,
            'employee_id' => $staff->staff_no ?: $staff->id,
            'department' => optional($staff->departments)->name ?: '',
            'contact_number' => $staff->mobile ?: $staff->emergency_mobile,
        ]);
    }

    private function validateLoanRequest(Request $request, $action = 'submit', SmAdvanceloan $loan = null)
    {
        $request->merge([
            'guarantor_employee_id' => $request->input('guarantor_employee_id') ?: null,
        ]);

        $rules = [
            'request_type' => 'required|in:Loan,Salary Advance',
            'loan_category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'installments' => 'required|integer|min:1',
            'repayment_start' => 'required|date_format:Y-m',
            'repayment_mode' => 'required|string|max:50',
            'requested_disbursement_date' => 'required|date|after_or_equal:today',
            'purpose' => 'required|string',
            'urgency_level' => 'required|in:Normal,Urgent,Critical',
            'grace_period_required' => 'nullable|in:Yes,No',
            'grace_period_months' => 'required_if:grace_period_required,Yes|nullable|integer|min:1',
            'attachment' => 'nullable|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            'guarantor_employee_id' => ($action === 'submit' ? 'required' : 'nullable') . '|integer|exists:sm_staffs,id',
        ];

        $declarationAlreadyAccepted = $loan && $loan->declaration_accepted_at;
        $declarationRequired = $action === 'submit'
            && !$declarationAlreadyAccepted
            && (!$loan || !$loan->exists || $loan->status === 'Draft');
        if ($declarationRequired) {
            $rules['declaration_info_confirmed'] = 'accepted';
            $rules['declaration_salary_deduction_authorized'] = 'accepted';
            $rules['declaration_policy_agreed'] = 'accepted';
            $rules['declaration_final_settlement_agreed'] = 'accepted';
            $rules['declaration_false_info_understood'] = 'accepted';
        }

        $this->validate($request, $rules, [
            'guarantor_employee_id.required' => 'Guarantor Employee is required.',
            'guarantor_employee_id.integer' => 'Guarantor Employee is required.',
            'guarantor_employee_id.exists' => 'Please select a valid Guarantor Employee.',
        ]);
    }

    private function fillLoanRequest(SmAdvanceloan $loan, Request $request, $staffId, $action)
    {
        $amount = (float) $request->input('amount');
        $installments = (int) $request->input('installments');
        $repaymentStart = Carbon::createFromFormat('Y-m-d', $request->input('repayment_start') . '-01');
        $guarantorEmployeeId = $request->input('guarantor_employee_id') ?: null;
        $guarantor = $guarantorEmployeeId
            ? SmStaff::with('departments')->find($guarantorEmployeeId)
            : null;

        $loan->staff_id = $staffId;
        $loan->request_type = $request->input('request_type');
        $loan->loan_category = $request->input('loan_category');
        $loan->type_id = $this->legacyTypeId($request->input('request_type'), $request->input('loan_category'));
        $loan->amount = $amount;
        $loan->installments = $installments;
        $loan->amount_per_month = $installments > 0 ? round($amount / $installments, 2) : null;
        $loan->repayment_start = $repaymentStart->format('Y-m-d');
        $loan->repayment_end_month = $repaymentStart->copy()->addMonths(max($installments - 1, 0))->format('Y-m-d');
        $loan->repayment_mode = $request->input('repayment_mode');
        $loan->requested_disbursement_date = $request->input('requested_disbursement_date');
        $loan->purpose = $request->input('purpose');
        $loan->urgency_level = $request->input('urgency_level');
        $loan->guarantor_employee_id = $guarantorEmployeeId;
        $loan->guarantor_employee_no = $guarantor ? ($guarantor->staff_no ?: $guarantor->id) : $request->input('guarantor_employee_no');
        $loan->guarantor_department = $guarantor ? optional($guarantor->departments)->name : $request->input('guarantor_department');
        $loan->guarantor_contact_number = $guarantor ? ($guarantor->mobile ?: $guarantor->emergency_mobile) : $request->input('guarantor_contact_number');
        $loan->early_settlement_allowed = $request->input('early_settlement_allowed', 'No');
        $loan->grace_period_required = $request->input('grace_period_required', 'No');
        $loan->grace_period_months = $request->input('grace_period_required') === 'Yes' ? $request->input('grace_period_months') : null;
        $loan->attachment_remarks = $request->input('attachment_remarks');
        $loan->note = $request->input('note');
        $loan->status = $action === 'draft' ? 'Draft' : 'Pending Reporting Manager Approval';
        $loan->manager_approval = 'Pending';
        $loan->finance_approval = 'Pending';
        $loan->hr_approval = 'Pending';
        $loan->management_approval = 'Pending';
        $loan->payment_approval = 'Pending';
        $loan->date = date('Y-m-d');
        if ($request->filled('declaration_info_confirmed')) {
            $loan->declaration_info_confirmed = true;
            $loan->declaration_salary_deduction_authorized = true;
            $loan->declaration_policy_agreed = true;
            $loan->declaration_final_settlement_agreed = true;
            $loan->declaration_false_info_understood = true;
            $loan->declaration_accepted_at = Carbon::now();
            $loan->declaration_accepted_by = Auth::user()->id;
        }

        if ($request->hasFile('attachment')) {
            $oldAttachment = $loan->attachment;
            $file = $request->file('attachment');
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->move(public_path('uploads/loan_docs'), $fileName);
            $loan->attachment = $fileName;
            if (!empty($oldAttachment) && file_exists(public_path('uploads/loan_docs/' . $oldAttachment))) {
                @unlink(public_path('uploads/loan_docs/' . $oldAttachment));
            }
        }
    }

    public function edit($id)
    {
        $loan = SmAdvanceloan::findOrFail($id);
        if (!$this->hasLoanPermission('request', 'edit') || !$this->canAccessLoanRequest($loan)) {
            abort(403, 'You are not authorized to edit this loan request.');
        }

        if (in_array($loan->status, ['Approved', 'Disbursed', 'Rejected']) && !in_array(Auth::user()->role_id, [1, 2])) {
            return redirect()->route('employee.loans.index')
                ->with('error', 'Approved or rejected loans cannot be edited.');
        }

        return redirect()->route('employee.loans.index', ['active' => $loan->id]);
    }

    public function update(Request $request, $id)
    {
        $loan = SmAdvanceloan::findOrFail($id);
        if (!$this->hasLoanPermission('request', 'edit') || !$this->canAccessLoanRequest($loan)) {
            return $this->loanPermissionDeniedResponse($request, 'You are not authorized to edit this loan request.');
        }
        if ($request->hasFile('attachment') && !$this->hasLoanPermission('request', 'attach')) {
            return $this->loanPermissionDeniedResponse($request, 'You are not authorized to attach loan documents.');
        }

        if (in_array($loan->status, ['Approved', 'Disbursed', 'Rejected']) && !in_array(Auth::user()->role_id, [1, 2])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Approved or rejected loans cannot be edited.',
                ], 403);
            }
            return redirect()->route('employee.loans.index')
                ->with('error', 'Approved or rejected loans cannot be edited.');
        }

        $action = $request->input('action_type') === 'draft' ? 'draft' : 'submit';
        $this->validateLoanRequest($request, $action, $loan);
        $this->fillLoanRequest($loan, $request, $loan->staff_id, $action);

        if (empty($loan->company_id)) {
            $loan->company_id = $loan->staffDetails ? $loan->staffDetails->company_id : session('logged_session_data.company_id');
        }
        if (empty($loan->loan_number)) {
            $loan->loan_number = $this->nextLoanNumber($loan->company_id);
        }

        $loan->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'id' => $loan->id,
                'request_number' => $loan->document_number,
                'message' => $action === 'draft' ? 'Loan / Advance request updated successfully.' : 'Loan / Advance request submitted successfully.',
            ]);
        }

        return redirect()->route('employee.loans.index', ['active' => $loan->id])
            ->with('success', 'Loan / Advance request updated successfully.');
    }

    public function export(Request $request)
    {
        $isTrackExport = $request->get('source') === 'track';
        if (!$this->hasLoanPermission($isTrackExport ? 'track' : 'request', 'export')) {
            abort(403, 'You are not authorized to export Loan & Advances.');
        }
        $staffId = optional(Auth::user()->staff)->id ?? Auth::user()->id;
        $query = SmAdvanceloan::with('staffDetails');
        if ($isTrackExport) {
            $query->where('status', '!=', 'Draft');
            if (!$this->isLoanAdmin()) {
                $this->applyTrackVisibility($query, Auth::user());
            }
        } elseif (!$this->isLoanAdmin()) {
            $query->where('staff_id', $staffId);
        }
        $this->applyRequestFilters($query, $request);
        $loans = $query->orderBy('id', 'desc')->get();
        $types = $this->loanTypeCollection()->pluck('name', 'id')->toArray();

        $headers = [
            'Request Number', 'Request Type', 'Loan Category', 'Employee', 'Request Date',
            'Loan Amount', 'Installments', 'Monthly Deduction', 'Repayment Start',
            'Repayment Mode', 'Purpose', 'Loan Status', 'Reporting Manager Approval',
            'Finance Manager Approval', 'HR Manager Approval', 'Management Approval',
            'Payment Processing', 'Payment Reference'
        ];

        return new StreamedResponse(function () use ($loans, $types, $headers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($loans as $loan) {
                $staff = $loan->staffDetails;
                fputcsv($out, [
                    $loan->document_number,
                    $loan->request_type ?: ($types[$loan->type_id] ?? ''),
                    $loan->loan_category ?: ($types[$loan->type_id] ?? ''),
                    optional($staff)->full_name ?: optional($staff)->first_name,
                    optional($loan->created_at)->format('d/m/Y'),
                    $loan->amount,
                    $loan->installments,
                    $loan->amount_per_month,
                    $loan->repayment_start ? Carbon::parse($loan->repayment_start)->format('M Y') : '',
                    $loan->repayment_mode,
                    $loan->purpose,
                    $loan->status,
                    $loan->manager_approval ?: 'Pending',
                    $loan->finance_approval ?: 'Pending',
                    $loan->hr_approval ?: 'Pending',
                    $this->managementRequired($loan) ? ($loan->management_approval ?: 'Pending') : 'Not Required',
                    $loan->payment_approval ?: 'Pending',
                    $loan->payment_reference,
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Loan_Advance_Requests.csv"',
        ]);
    }

    public function report(Request $request)
    {
        if (!$this->hasLoanPermission('report', 'view')) {
            abort(403, 'You are not authorized to view Loan Report.');
        }

        $query = $this->loanReportQuery($request);
        $loans = $query->orderBy('id', 'desc')->simplePaginate(10)->appends($request->query());
        $loans->getCollection()->transform(function ($loan) {
            $loan = $this->prepareLoanListRow($loan);
            $loan->report_status = $this->loanReportStatus($loan);
            return $loan;
        });

        $employees = SmStaff::select('id', 'full_name', 'first_name', 'last_name', 'staff_no')
            ->where('active_status', 1)
            ->orderBy('full_name')
            ->get();

        $loanPermissions = $this->loanPermissionSet('request');
        $trackPermissions = $this->loanPermissionSet('track');
        $reportPermissions = $this->loanPermissionSet('report');
        $permissions = SmRolePermission::where('role_id', Auth::user()->role_id)->get();

        return view('backEnd.humanResource.loans.report', compact('loans', 'employees', 'loanPermissions', 'trackPermissions', 'reportPermissions', 'permissions'));
    }

    public function reportExport(Request $request)
    {
        if (!$this->hasLoanPermission('report', 'export')) {
            abort(403, 'You are not authorized to export Loan Report.');
        }

        $loans = $this->loanReportQuery($request)->orderBy('id', 'desc')->get();
        $loans->transform(function ($loan) {
            $loan = $this->prepareLoanListRow($loan);
            $loan->report_status = $this->loanReportStatus($loan);
            return $loan;
        });

        $headers = [
            'Date', 'Doc No', 'Employee Name', 'Amount Requested', 'Installment Number',
            'Monthly Deduction Amount', 'Repayment Start Month', 'Repayment Mode',
            'Original Loan Amount', 'Recovered Amount', 'Outstanding Balance',
            'Remaining Installments', 'Next Deduction Date', 'Status',
        ];

        return new StreamedResponse(function () use ($loans, $headers) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($loans as $loan) {
                fputcsv($out, [
                    $loan->list_date ?: '',
                    $loan->document_number,
                    $loan->list_employee_name ?: '',
                    $loan->list_original_amount,
                    $loan->installments ?: '',
                    $loan->list_monthly_deduction === '-' ? '0.00' : $loan->list_monthly_deduction,
                    $loan->list_repayment_start === '-' ? '' : $loan->list_repayment_start,
                    $loan->repayment_mode ?: '',
                    $loan->list_original_amount,
                    $loan->list_recovered_amount,
                    $loan->list_outstanding_amount,
                    $loan->list_remaining_installments,
                    $loan->list_next_deduction_date === '-' ? '' : $loan->list_next_deduction_date,
                    $loan->report_status,
                ]);
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Loan_Report.csv"',
        ]);
    }

    public function approvalsList(Request $request, $id = null)
    {
        if (!$this->hasLoanPermission('track', 'view')) {
            abort(403, 'You are not authorized to view Loan Track.');
        }

        $auth = Auth::user();
        $query = SmAdvanceloan::with('staffDetails')->where('status', '!=', 'Draft');

        if (!$this->isLoanAdmin($auth)) {
            $this->applyTrackVisibility($query, $auth);
        }

        $this->applyRequestFilters($query, $request);
        $loans = $query->orderBy('id', 'desc')->paginate(10)->appends($request->query());

        if ($id) {
            $selectedLoan = SmAdvanceloan::find($id);
            if (!$selectedLoan) {
                return redirect()->route('employee.loans.approvals')->with('message-danger', 'Loan request not found.');
            }
            if ($selectedLoan->status === 'Draft') {
                return redirect()->route('employee.loans.approvals')->with('message-danger', 'Draft requests are not available in Loan Track. Please submit for approval first.');
            }
            if (!$this->isLoanAdmin($auth) && !$this->canSeeLoanInTrack($selectedLoan, $auth)) {
                return redirect()->route('employee.loans.approvals')->with('message-danger', 'You are not authorized to view this loan request in Loan Track.');
            }
            $active_id = $id;
        } else {
            $active_id = $loans->first() ? $loans->first()->id : null;
        }

        $trackPermissions = $this->loanPermissionSet('track');
        $permissions = SmRolePermission::where('role_id', Auth::user()->role_id)->get();
        return view('backEnd.humanResource.loans.approvals', compact('loans', 'active_id', 'trackPermissions', 'permissions'));
    }

    public function track($id)
    {
        $loan = SmAdvanceloan::findOrFail($id);
        if (!$this->canAccessLoanRequest($loan)) {
            abort(403, 'You are not authorized to view this loan request.');
        }
        return redirect()->route('employee.loans.index', ['active' => $loan->id, 'track' => 1]);
    }

    public function showDetail($id)
    {
        $loan = SmAdvanceloan::with('staffDetails')->findOrFail($id);
        if (!$this->hasLoanPermission('track', 'view') || (!$this->isLoanAdmin() && !$this->canSeeLoanInTrack($loan, Auth::user()))) {
            return response('<p class="text-danger">You are not authorized to view this loan request in Loan Track.</p>', 403);
        }
        $loanTypes = $this->loanTypeCollection();
        $isApprovals = true;
        $loanPermissions = $this->loanPermissionSet('request');
        $trackPermissions = $this->loanPermissionSet('track');
        $reportPermissions = $this->loanPermissionSet('report');
        return view('backEnd.humanResource.loans._details', compact('loan', 'loanTypes', 'isApprovals', 'loanPermissions', 'trackPermissions', 'reportPermissions'));
    }

    public function approve(Request $request, $id)
    {
        $auth = Auth::user();
        $loan = SmAdvanceloan::with('staffDetails')->findOrFail($id);
        $status = $request->input('status', 'Pending');
        $stage = $this->nextStage($loan);

        if (!$stage) {
            return redirect()->back()->with('error', 'Previous approval is pending or approval is already complete.');
        }
        if (!$this->canActOnStage($stage, $loan, $auth)) {
            return redirect()->back()->with('error', 'You are not authorized to approve this stage.');
        }

        if ($stage === 'manager') {
            $loan->manager_approval = $status;
            $loan->approved_by = $auth->id;
            $loan->manager_remarks = $request->input('remarks');
            $loan->recommended_amount = $request->input('recommended_amount');
            $loan->approved_at = Carbon::now();
        } elseif ($stage === 'finance') {
            $loan->finance_approval = $status;
            $loan->finance_approved_by = $auth->id;
            $loan->finance_approved_amount = $request->input('approved_amount');
            $loan->finance_management_approval_req = $request->input('management_approval_req');
            $loan->finance_remarks = $request->input('remarks');
            
            $loan->financial_review_status = $request->input('financial_review_status');
            $loan->outstanding_loan_verification = $request->input('outstanding_loan_verification');
            $loan->monthly_deduction_feasibility = $request->input('monthly_deduction_feasibility');
            
            $loan->finance_approved_at = Carbon::now();
        } elseif ($stage === 'hr') {
            $loan->hr_approval = $status;
            $loan->hr_approved_by = $auth->id;
            $loan->hr_management_approval_req = $request->input('management_approval_req');
            $loan->hr_remarks = $request->input('remarks');
            
            $loan->hr_approval_status = $request->input('hr_approval_status');
            $loan->policy_compliance = $request->input('policy_compliance');
            $loan->eligibility_verified = $request->input('eligibility_verified');
            
            $loan->hr_approved_at = Carbon::now();
        } elseif ($stage === 'management') {
            $loan->management_approval = $status;
            $loan->management_approved_by = $auth->id;
            $loan->management_remarks = $request->input('remarks');
            
            $loan->management_approval_status = $request->input('management_approval_status');
            
            $loan->management_approved_at = Carbon::now();
        } elseif ($stage === 'payment') {
            $loan->payment_approval = $status;
            $loan->payment_approved_by = $auth->id;
            $loan->payment_voucher_no = $request->input('payment_voucher_no');
            $loan->payment_date = $request->input('payment_date') ?: date('Y-m-d');
            $loan->payment_method = $request->input('payment_method');
            $loan->bank_account_id = $request->input('bank_account_id');
            $loan->paid_amount = $request->input('paid_amount');
            $loan->payment_status = $request->input('payment_status');
            $loan->payment_reference = $request->input('payment_reference');
            $loan->payment_remarks = $request->input('remarks');
            $loan->payment_approved_at = Carbon::now();
        }

        if (in_array('Rejected', [
            $loan->manager_approval, $loan->finance_approval, $loan->hr_approval,
            $loan->management_approval, $loan->payment_approval
        ])) {
            $loan->status = 'Rejected';
        } elseif (in_array('Returned', [
            $loan->manager_approval, $loan->finance_approval, $loan->hr_approval,
            $loan->management_approval, $loan->payment_approval
        ])) {
            $loan->status = 'Draft';
        } elseif ($loan->payment_approval === 'Approved') {
            $loan->status = 'Disbursed';
        } elseif ($loan->hr_approval === 'Approved' && (!$this->managementRequired($loan) || $loan->management_approval === 'Approved')) {
            $loan->status = 'Approved';
        } else {
            $loan->status = 'Pending';
        }

        $loan->save();

        return redirect()->back()->with('success', 'Approval updated successfully.');
    }

    private function loanTypeCollection()
    {
        return collect($this->loanTypes)->map(function ($type) {
            return (object) $type;
        });
    }

    private function legacyTypeId($requestType, $loanCategory)
    {
        if ($requestType === 'Salary Advance') return 1;
        if ($loanCategory === 'Emergency') return 3;
        if ($loanCategory === 'Travel') return 4;
        return 2;
    }

    private function currentStaff()
    {
        $user = Auth::user();
        if (optional($user->staff)->id) {
            return SmStaff::with(['departments', 'designations'])->find($user->staff->id);
        }
        return SmStaff::with(['departments', 'designations'])->where('user_id', $user->id)->first();
    }

    private function currentStaffId()
    {
        $staff = $this->currentStaff();
        return optional($staff)->id ?: Auth::user()->id;
    }

    private function isLoanAdmin($auth = null)
    {
        $auth = $auth ?: Auth::user();
        return $auth && in_array((int) $auth->role_id, [1, 2], true);
    }

    private function loanPermissionPage($module)
    {
        $pages = [
            'request' => 'loans_advances',
            'track' => 'loan_track',
            'report' => 'loan_report',
        ];

        return $pages[$module] ?? $module;
    }

    private function loanPermissionColumn($action)
    {
        $columns = [
            'create' => 'is_create',
            'view' => 'is_read',
            'edit' => 'is_edit',
            'delete' => 'is_delete',
            'export' => 'is_export',
            'attach' => 'is_attach',
        ];

        return $columns[$action] ?? 'is_read';
    }

    private function hasLoanPermission($module, $action = 'view', $auth = null)
    {
        $auth = $auth ?: Auth::user();
        if (!$auth) {
            return false;
        }
        if ($this->isLoanAdmin($auth)) {
            return true;
        }

        $link = SmModuleLink::where('page_name', $this->loanPermissionPage($module))->first();
        if (!$link) {
            return true;
        }

        $permission = SmRolePermission::where('role_id', $auth->role_id)
            ->where('module_link_id', $link->id)
            ->first();
        if (!$permission) {
            return false;
        }

        $column = $this->loanPermissionColumn($action);
        return (int) $permission->{$column} === 1;
    }

    private function loanPermissionSet($module)
    {
        return [
            'create' => $this->hasLoanPermission($module, 'create'),
            'view' => $this->hasLoanPermission($module, 'view'),
            'edit' => $this->hasLoanPermission($module, 'edit'),
            'delete' => $this->hasLoanPermission($module, 'delete'),
            'export' => $this->hasLoanPermission($module, 'export'),
            'attach' => $this->hasLoanPermission($module, 'attach'),
        ];
    }

    private function canAccessLoanRequest($loan, $auth = null)
    {
        $auth = $auth ?: Auth::user();
        if ($this->isLoanAdmin($auth)) {
            return true;
        }

        return (int) $loan->staff_id === (int) $this->currentStaffId();
    }

    private function loanPermissionDeniedResponse(Request $request, $message)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], 403);
        }

        return redirect()->back()->with('error', $message);
    }

    private function nextLoanNumber($companyId = null)
    {
        $companyId = $companyId ?: session('logged_session_data.company_id');

        if ($companyId) {
            return \App\SysHelper::get_new_code_lead('sm_advanceloans', 'LN', 'loan_number', $companyId);
        }

        return SmAdvanceloan::documentNumber(((int) SmAdvanceloan::max('id')) + 1);
    }

    private function prepareLoanListRow(SmAdvanceloan $loan)
    {
        $staff = $loan->staffDetails;
        $guarantor = $loan->guarantorStaff;
        $originalAmount = (float) $loan->amount;
        $recoveredAmount = (float) ($loan->paid_amount ?: 0);
        $outstandingAmount = max($originalAmount - $recoveredAmount, 0);
        $monthlyDeduction = (float) ($loan->amount_per_month ?: 0);
        $remainingInstallments = $monthlyDeduction > 0 ? (int) ceil($outstandingAmount / $monthlyDeduction) : ($loan->installments ?: '-');
        $recoveredInstallments = $monthlyDeduction > 0 ? (int) floor($recoveredAmount / $monthlyDeduction) : 0;
        $nextDeductionDate = null;

        if ($loan->repayment_start && $outstandingAmount > 0) {
            $nextDeductionDate = Carbon::parse($loan->repayment_start)->addMonths($recoveredInstallments)->format('d/m/Y');
        }

        $loan->list_date = $loan->date ? date('d/m/Y', strtotime($loan->date)) : optional($loan->created_at)->format('d/m/Y');
        $loan->list_employee_name = optional($staff)->full_name ?: trim(optional($staff)->first_name . ' ' . optional($staff)->last_name);
        $loan->list_guarantor_name = optional($guarantor)->full_name ?: trim(optional($guarantor)->first_name . ' ' . optional($guarantor)->last_name);
        $loan->list_original_amount = number_format($originalAmount, 2);
        $loan->list_recovered_amount = number_format($recoveredAmount, 2);
        $loan->list_outstanding_amount = number_format($outstandingAmount, 2);
        $loan->list_monthly_deduction = $monthlyDeduction > 0 ? number_format($monthlyDeduction, 2) : '-';
        $loan->list_remaining_installments = $remainingInstallments;
        $loan->list_repayment_start = $loan->repayment_start ? Carbon::parse($loan->repayment_start)->format('M Y') : '-';
        $loan->list_edit_repayment_start = $loan->repayment_start ? Carbon::parse($loan->repayment_start)->format('Y-m') : '';
        $loan->list_edit_repayment_end = $loan->repayment_end_month ? Carbon::parse($loan->repayment_end_month)->format('Y-m') : '';
        $loan->list_disbursement_date = $loan->requested_disbursement_date ? date('d/m/Y', strtotime($loan->requested_disbursement_date)) : '-';
        $loan->list_next_deduction_date = $nextDeductionDate ?: '-';
        $loan->list_approval_status = $loan->payment_approval === 'Approved' ? 'Payment Approved' :
            ($loan->hr_approval === 'Approved' ? 'HR Approved' :
            ($loan->finance_approval === 'Approved' ? 'Finance Approved' :
            ($loan->manager_approval === 'Approved' ? 'Manager Approved' : 'Pending')));

        return $loan;
    }

    private function applyRequestFilters($query, Request $request)
    {
        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->get('request_type')) {
            $requestType = $request->get('request_type');
            if (in_array($requestType, ['Loan', 'Salary Advance'])) {
                $query->where(function ($sub) use ($requestType) {
                    $sub->where('request_type', $requestType);
                    if ($requestType === 'Loan') {
                        $sub->orWhere('type_id', 2);
                    } else {
                        $sub->orWhereIn('type_id', [1, 3, 4]);
                    }
                });
            } elseif ($requestType === 'Advance') {
                $query->whereIn('type_id', [1, 3, 4]);
            }
        }
        if ($request->get('approval_status')) {
            $status = $request->get('approval_status');
            $query->where(function ($sub) use ($status) {
                $sub->where('manager_approval', $status)
                    ->orWhere('finance_approval', $status)
                    ->orWhere('hr_approval', $status)
                    ->orWhere('management_approval', $status)
                    ->orWhere('payment_approval', $status);
            });
        }
        if ($request->get('loan_category')) {
            $category = $request->get('loan_category');
            $query->where(function ($sub) use ($category) {
                $sub->where('loan_category', $category);
                if (is_numeric($category)) {
                    $sub->orWhere('type_id', $category);
                }
            });
        } elseif ($request->get('type_id')) {
            $query->where('type_id', $request->get('type_id'));
        }
        if ($request->get('employee_id')) {
            $query->where('staff_id', $request->get('employee_id'));
        }
        if ($request->get('repayment_mode')) {
            $query->where('repayment_mode', $request->get('repayment_mode'));
        }
        if ($request->get('urgency_level')) {
            $urgency = $request->get('urgency_level');
            $query->where(function ($sub) use ($urgency) {
                $sub->where('urgency_level', $urgency)
                    ->orWhere('purpose', 'like', '%' . $urgency . '%');
            });
        }
        if ($request->get('from')) {
            $query->whereDate('created_at', '>=', $request->get('from'));
        }
        if ($request->get('to')) {
            $query->whereDate('created_at', '<=', $request->get('to'));
        }
        if ($request->get('q')) {
            $q = $request->get('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('id', 'like', "%$q%")
                    ->orWhere('purpose', 'like', "%$q%")
                    ->orWhere('repayment_mode', 'like', "%$q%")
                    ->orWhereHas('staffDetails', function ($staff) use ($q) {
                        $staff->where('full_name', 'like', "%$q%")
                            ->orWhere('first_name', 'like', "%$q%")
                            ->orWhere('last_name', 'like', "%$q%");
                    });
            });
        }
    }

    private function loanReportQuery(Request $request)
    {
        $staffId = optional(Auth::user()->staff)->id ?? Auth::user()->id;
        $query = SmAdvanceloan::with([
                'staffDetails:id,full_name,first_name,last_name,staff_no',
                'guarantorStaff:id,full_name,first_name,last_name,staff_no',
            ])
            ->where('status', '!=', 'Draft');

        if (!$this->isLoanAdmin()) {
            $query->where('staff_id', $staffId);
        }

        if ($request->get('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->get('employee_id')) {
            $query->where('staff_id', $request->get('employee_id'));
        }
        if ($request->get('repayment_mode')) {
            $query->where('repayment_mode', $request->get('repayment_mode'));
        }
        if ($request->get('from')) {
            $query->whereDate('created_at', '>=', $request->get('from'));
        }
        if ($request->get('to')) {
            $query->whereDate('created_at', '<=', $request->get('to'));
        }
        if ($request->get('q')) {
            $q = $request->get('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('id', 'like', "%$q%")
                    ->orWhere('loan_number', 'like', "%$q%")
                    ->orWhere('status', 'like', "%$q%")
                    ->orWhereHas('staffDetails', function ($staff) use ($q) {
                        $staff->where('full_name', 'like', "%$q%")
                            ->orWhere('first_name', 'like', "%$q%")
                            ->orWhere('last_name', 'like', "%$q%");
                    });
            });
        }

        return $query;
    }

    private function loanReportStatus($loan)
    {
        if (in_array($loan->status, ['Disbursed', 'Active'])) {
            return 'Active';
        }
        if (in_array($loan->status, ['Closed', 'Completed'])) {
            return 'Closed';
        }
        if ($loan->status === 'Approved') {
            return 'Approved';
        }
        if ($loan->status === 'Rejected') {
            return 'Rejected';
        }
        return 'Pending';
    }

    private function managementRequired($loan)
    {
        if ($loan->hr_management_approval_req === 'No') {
            return false;
        }
        if ($loan->hr_management_approval_req === 'Yes') {
            return true;
        }
        return $loan->finance_management_approval_req === 'Yes';
    }

    private function reportingStaffIds($auth)
    {
        return SmStaff::where(function ($q) use ($auth) {
            $q->whereRaw("FIND_IN_SET(?, reporting_manager)", [$auth->id])
                ->orWhereRaw("FIND_IN_SET(?, reporting_manager)", [$auth->role_id]);
        })->pluck('id')->toArray();
    }

    private function isReportingApprover($loan, $auth)
    {
        if (!$this->hasLoanPermission('track', 'edit', $auth)) {
            return false;
        }

        $managerIds = [];
        $staff = $loan->staffDetails;
        if ($staff && !empty($staff->reporting_manager)) {
            $managerIds = array_map('trim', explode(',', (string) $staff->reporting_manager));
        }

        return (int) $auth->role_id === 8
            || in_array((string) $auth->id, $managerIds, true)
            || in_array((string) $auth->role_id, $managerIds, true);
    }

    private function isFinanceApprover($auth)
    {
        return (int) $auth->role_id === 27 && $this->hasLoanPermission('track', 'edit', $auth);
    }

    private function isHrApprover($auth)
    {
        return (int) $auth->role_id === 3 && $this->hasLoanPermission('track', 'edit', $auth);
    }

    private function isManagementApprover($auth)
    {
        return $this->hasLoanPermission('track', 'edit', $auth)
            && !in_array((int) $auth->role_id, [3, 8, 27, 28], true);
    }

    private function isPaymentApprover($auth)
    {
        return (int) $auth->role_id === 28 && $this->hasLoanPermission('track', 'edit', $auth);
    }

    private function applyTrackVisibility($query, $auth)
    {
        $staffIds = $this->reportingStaffIds($auth);
        $canTrackEdit = $this->hasLoanPermission('track', 'edit', $auth);

        if (!$canTrackEdit) {
            $query->whereRaw('1 = 0');
            return;
        }

        $query->where(function ($stageQuery) use ($auth, $staffIds) {
            if ((int) $auth->role_id === 8 || count($staffIds)) {
                $stageQuery->orWhere(function ($q) use ($staffIds) {
                    $q->whereIn('staff_id', $staffIds);
                });
            }

            if ((int) $auth->role_id === 27) {
                $stageQuery->orWhere(function ($q) {
                    $q->where('manager_approval', 'Approved');
                });
            }

            if ((int) $auth->role_id === 3) {
                $stageQuery->orWhere(function ($q) {
                    $q->where('finance_approval', 'Approved');
                });
            }

            if ($this->isManagementApprover($auth)) {
                $stageQuery->orWhere(function ($q) {
                    $q->where('hr_approval', 'Approved')
                        ->where(function ($required) {
                            $required->where('hr_management_approval_req', 'Yes')
                                ->orWhere(function ($financeRequired) {
                                    $financeRequired->where(function ($hrNotSet) {
                                            $hrNotSet->whereNull('hr_management_approval_req')
                                                ->orWhere('hr_management_approval_req', '')
                                                ->orWhere('hr_management_approval_req', '!=', 'No');
                                        })
                                        ->where('finance_management_approval_req', 'Yes');
                                });
                        });
                });
            }

            if ((int) $auth->role_id === 28) {
                $stageQuery->orWhere(function ($q) {
                    $q->where('hr_approval', 'Approved')
                        ->where(function ($readyForPayment) {
                            $readyForPayment->where('management_approval', 'Approved')
                                ->orWhere(function ($notRequired) {
                                    $notRequired->where(function ($hrNotRequired) {
                                            $hrNotRequired->whereNull('hr_management_approval_req')
                                                ->orWhere('hr_management_approval_req', '')
                                                ->orWhere('hr_management_approval_req', 'No');
                                        })
                                        ->where(function ($financeNotRequired) {
                                            $financeNotRequired->whereNull('finance_management_approval_req')
                                                ->orWhere('finance_management_approval_req', '')
                                                ->orWhere('finance_management_approval_req', 'No');
                                        });
                                });
                        });
                });
            }
        });
    }

    private function canSeeLoanInTrack($loan, $auth)
    {
        if ($this->isLoanAdmin($auth)) {
            return $loan->status !== 'Draft';
        }
        if (!$this->hasLoanPermission('track', 'view', $auth) || $loan->status === 'Draft') {
            return false;
        }
        if ($this->isReportingApprover($loan, $auth)) {
            return true;
        }
        if ($this->isFinanceApprover($auth) && $loan->manager_approval === 'Approved') {
            return true;
        }
        if ($this->isHrApprover($auth) && $loan->finance_approval === 'Approved') {
            return true;
        }
        if ($this->isManagementApprover($auth) && $loan->hr_approval === 'Approved' && $this->managementRequired($loan)) {
            return true;
        }
        if ($this->isPaymentApprover($auth) && $loan->hr_approval === 'Approved' && (!$this->managementRequired($loan) || $loan->management_approval === 'Approved')) {
            return true;
        }

        return false;
    }

    private function nextStage($loan)
    {
        if ($loan->status === 'Draft') return null;
        if (($loan->manager_approval ?: 'Pending') === 'Pending') return 'manager';
        if ($loan->manager_approval === 'Rejected') return null;
        if (($loan->finance_approval ?: 'Pending') === 'Pending') return 'finance';
        if ($loan->finance_approval === 'Rejected') return null;
        if (($loan->hr_approval ?: 'Pending') === 'Pending') return 'hr';
        if ($loan->hr_approval === 'Rejected') return null;
        if ($this->managementRequired($loan) && (($loan->management_approval ?: 'Pending') === 'Pending')) return 'management';
        if ($loan->management_approval === 'Rejected') return null;
        if (($loan->payment_approval ?: 'Pending') === 'Pending') return 'payment';
        return null;
    }

    private function canActOnStage($stage, $loan, $auth)
    {
        if (!$stage) return false;
        if (in_array($auth->role_id, [1, 2])) return true;

        if ($stage === 'manager') return $this->isReportingApprover($loan, $auth);
        if ($stage === 'finance') return $this->isFinanceApprover($auth);
        if ($stage === 'hr') return $this->isHrApprover($auth);
        if ($stage === 'management') return $this->isManagementApprover($auth);
        if ($stage === 'payment') return $this->isPaymentApprover($auth);

        return false;
    }
}

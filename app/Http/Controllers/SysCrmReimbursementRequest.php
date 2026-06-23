<?php

namespace App\Http\Controllers;

use App\ApiBaseMethod;
use App\SmInspectingDepartment;
use App\SmItem;
use Illuminate\Http\Request;
use App\SmItemStore;
use App\SmStaff;
use App\SmModuleLink;
use App\SmRolePermission;
use App\SysBrand;
use App\SysChartofAccounts;
use App\SysCompany;
use App\SysCountries;
use App\SysCrmAmc;
use App\SysCrmDeals;
use App\SysCrmDealsCollaboration;
use App\SysCrmDealsComments;
use App\SysCrmDealTrack;
use App\SysCrmLeads;
use App\SysCrmQuoteCSItems;
use App\SysCrmQuoteItems;
use App\SysCrmReimbursement;
use App\SysCrmService;
use App\SysCrmServiceAssign;
use App\SysCrmServiceComments;
use App\SysCurrencySettings;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysHelper;
use App\SysItemOpeningStock;
use App\SysItemStock;
use App\SysPaymentTerms;
use App\SysShipping;
use App\SysStockIn;
use App\SysStockInSerialNo;
use App\SysSupplierType;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Stmt\Return_;
use Validator;

class SysCrmReimbursementRequest extends Controller
{
    public function __construct()
    {
        $this->middleware('PM');
    }

    public function index(Request $request)
    {
        try {
            if (!$this->hasReimbursementPermission('request', 'view')) {
                abort(403, 'You are not authorized to view Reimbursement Request.');
            }

            $company_id = session('logged_session_data.company_id');

            $query = SysCrmReimbursement::with(['createdby', 'deal_code.customername', 'accountsby', 'accoheadby', 'deptheadby', 'currencycode'])
                ->where('company_id', $company_id);

            if (!$this->isReimbursementAdmin()) {
                $this->applyRequestVisibility($query, Auth::user());
            }

            $ctrl_from_date = $request->from_date;
            $ctrl_to_date = $request->to_date;
            $ctrl_expense_category = $request->expense_category;
            $ctrl_invoice_no = $request->invoice_no;
            $ctrl_invoice_date = $request->invoice_date;
            $ctrl_amount = $request->amount;
            $ctrl_reimbursement_no = $request->reimbursement_no;
            $ctrl_deal_id = $request->deal_id;
            $ctrl_project_id = $request->project_id;
            $ctrl_attachments = $request->attachments;
            $ctrl_submitted_by = $request->submitted_by;
            $ctrl_status = $request->status_filter;
            $filter_by = "";

            if ($request->filter_by == "this_month") {
                $ctrl_from_date = date('d/m/Y', strtotime(date('Y-m-01')));
                $ctrl_to_date = date('d/m/Y', strtotime(date("Y-m-t")));
                $filter_by = 'this_month';
            }
            if ($request->filter_by == "today") {
                $ctrl_from_date = date('d/m/Y');
                $ctrl_to_date = date('d/m/Y');
                $filter_by = 'today';
            }
            if ($request->filter_by == "this_week") {
                $ctrl_from_date = date('d/m/Y', strtotime('-1 week sunday 00:00:00'));
                $ctrl_to_date = date('d/m/Y', strtotime('saturday 23:59:59'));
                $filter_by = 'this_week';
            }
            if ($request->filter_by == "last_week") {
                $ctrl_from_date = date('d/m/Y', strtotime('-2 week sunday 00:00:00'));
                $ctrl_to_date = date('d/m/Y', strtotime('-1 week saturday 23:59:59'));
                $filter_by = 'last_week';
            }
            if ($request->filter_by == "last_month") {
                $ctrl_from_date = date('d/m/Y', strtotime('first day of previous month'));
                $ctrl_to_date = date('d/m/Y', strtotime('last day of previous month'));
                $filter_by = 'last_month';
            }
            if ($request->filter_by == "this_quarter") {
                $q_date = SysHelper::get_quarter(date('m'));
                $ctrl_from_date = date('d/m/Y', strtotime($q_date[0]));
                $ctrl_to_date = date('d/m/Y', strtotime($q_date[1]));
                $filter_by = 'this_quarter';
            }
            if ($request->filter_by == "pre_quarter") {
                $q_date = SysHelper::get_pre_quarter(date('m'));
                $ctrl_from_date = date('d/m/Y', strtotime($q_date[0]));
                $ctrl_to_date = date('d/m/Y', strtotime($q_date[1]));
                $filter_by = 'pre_quarter';
            }
            if ($request->filter_by == "this_year") {
                $ctrl_from_date = date('d/m/Y', strtotime(date('Y-01-01')));
                $ctrl_to_date = date('d/m/Y', strtotime(date('Y-12-31')));
                $filter_by = 'this_year';
            }
            if ($request->filter_by == "last_year") {
                $ctrl_from_date = date('d/m/Y', strtotime("-1 year", strtotime(date('Y-01-01'))));
                $ctrl_to_date = date('d/m/Y', strtotime("-1 year", strtotime(date('Y-12-31'))));
                $filter_by = 'last_year';
            }

            if ($ctrl_from_date) {
                try {
                    $fromDate = Carbon::createFromFormat('d/m/Y', $ctrl_from_date)->format('Y-m-d');
                    $query->where('date', '>=', $fromDate);
                } catch (\Exception $e) {}
            }
            if ($ctrl_to_date) {
                try {
                    $toDate = Carbon::createFromFormat('d/m/Y', $ctrl_to_date)->format('Y-m-d');
                    $query->where('date', '<=', $toDate);
                } catch (\Exception $e) {}
            }
            if ($ctrl_invoice_date) {
                try {
                    $invDate = \Carbon\Carbon::parse(str_replace('/', '-', $ctrl_invoice_date))->format('Y-m-d');
                    $query->where('invoice_date', '=', $invDate);
                } catch (\Exception $e) {}
            }

            if ($ctrl_expense_category) {
                $query->where('remarks', 'like', '%' . $ctrl_expense_category . '%');
            }
            if ($ctrl_invoice_no) {
                $query->where('invoice_no', 'like', '%' . $ctrl_invoice_no . '%');
            }
            if ($ctrl_amount) {
                $query->where('amount', str_replace(',', '', $ctrl_amount));
            }
            if ($ctrl_reimbursement_no) {
                $query->where('reimbursement_no', 'like', '%' . $ctrl_reimbursement_no . '%');
            }
            if ($ctrl_deal_id) {
                $query->whereHas('deal_code', function($q) use ($ctrl_deal_id) {
                    $q->where('code', 'like', '%' . $ctrl_deal_id . '%');
                });
            }
            if ($ctrl_project_id) {
                $query->where('project_id', 'like', '%' . $ctrl_project_id . '%');
            }
            if ($ctrl_submitted_by) {
                $query->whereHas('createdby', function($q) use ($ctrl_submitted_by) {
                    $q->where('full_name', 'like', '%' . $ctrl_submitted_by . '%');
                });
            }
            if ($ctrl_status) {
                $this->applyReimbursementStatusFilter($query, $ctrl_status);
            }
            if ($ctrl_attachments == 1) {
                $query->whereNotNull('attachmant')->where('attachmant', '!=', '');
            }
            if ($ctrl_attachments == 2) {
                $query->where(function ($q) {
                    $q->whereNull('attachmant')->orWhere('attachmant', '');
                });
            }

            $data = $query->orderByRaw("CASE WHEN dept_head_status=0 THEN 1 WHEN dept_head_status=1 AND acco_head_status=0 THEN 2 WHEN dept_head_status=1 AND acco_head_status=1 AND accounts_status=0 THEN 3 ELSE 4 END ASC")->orderby('date', 'desc')->orderBy('reimbursement_no', 'asc')->get();

            $active_id = null;
            $selectedReimbursement = $data->first();
            if ($selectedReimbursement) {
                $active_id = $selectedReimbursement->id;
            }
            $staff = null;
            $submitter = null;
            if ($selectedReimbursement) {
                $staff = \App\SmStaff::where('user_id', $selectedReimbursement->employee_id ?? $selectedReimbursement->created_by)->first();
                $submitter = \App\SmStaff::where('user_id', $selectedReimbursement->created_by)->first();
            }
            
            $currencies = \App\SysCurrency::where('active_status', 1)->get();
            $employees = \App\SmStaff::where('company_id', $company_id)->where('active_status', 1)->get();
            $company = \App\SysCompany::find($company_id);
            $default_currency = $company ? $company->currency_id : null;
            
            $other_code = $company ? $company->other_code : 'M';
            
            $last = SysCrmReimbursement::orderBy('id', 'desc')->first();
            $next_id = ($last ? $last->id : 0) + 1000;
            $next_reimbursement_no = 'RE' . $other_code . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);

            $reimbursementPermissions = $this->reimbursementPermissionSet('request');
            $reimbursementTrackPermissions = $this->reimbursementPermissionSet('track');
            $permissions = SmRolePermission::where('role_id', Auth::user()->role_id)->get();

            return view('backEnd.amc.reimbursementlist', compact('data', 'currencies', 'employees', 'default_currency', 'next_reimbursement_no', 'active_id', 'selectedReimbursement', 'staff', 'submitter', 'ctrl_from_date', 'ctrl_to_date', 'ctrl_expense_category', 'ctrl_invoice_no', 'ctrl_invoice_date', 'ctrl_amount', 'ctrl_reimbursement_no', 'ctrl_deal_id', 'ctrl_project_id', 'ctrl_attachments', 'ctrl_submitted_by', 'ctrl_status', 'filter_by', 'reimbursementPermissions', 'reimbursementTrackPermissions', 'permissions'));

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
        public function check_invoice(Request $request)
    {
        $company_id = session('logged_session_data.company_id');
        
        if (empty($request->invoice_no)) {
            return response()->json(['exists' => false]);
        }
        
        $query = SysCrmReimbursement::where('invoice_no', $request->invoice_no)
            ->where('company_id', $company_id);

        if ($request->has('edit_id') && $request->edit_id) {
            $query->where('id', '!=', $request->edit_id);
        }

        $invoiceExists = (clone $query)->exists();
        $sameAmountExists = false;

        if ($request->has('amount') && $request->amount !== '') {
            $sameAmountExists = (clone $query)
                ->where('amount', str_replace(',', '', $request->amount))
                ->exists();
        }
        
        return response()->json([
            'exists' => $sameAmountExists,
            'invoice_exists' => $invoiceExists,
        ]);
    }

    public function export(Request $request)
    {
        try {
            if (!$this->hasReimbursementPermission('request', 'export')) {
                abort(403, 'You are not authorized to export Reimbursement Request.');
            }

            $company_id = session('logged_session_data.company_id');
            $query = SysCrmReimbursement::with(['createdby', 'deal_code.customername', 'accountsby', 'accoheadby', 'deptheadby', 'currencycode'])
                ->where('company_id', $company_id);

            if (!$this->isReimbursementAdmin()) {
                $this->applyRequestVisibility($query, Auth::user());
            }

            $ctrl_from_date = $request->from_date;
            $ctrl_to_date = $request->to_date;

            if ($request->filter_by == "this_month") {
                $ctrl_from_date = date('d/m/Y', strtotime(date('Y-m-01')));
                $ctrl_to_date = date('d/m/Y', strtotime(date("Y-m-t")));
            }
            if ($request->filter_by == "today") {
                $ctrl_from_date = date('d/m/Y');
                $ctrl_to_date = date('d/m/Y');
            }
            if ($request->filter_by == "this_week") {
                $ctrl_from_date = date('d/m/Y', strtotime('-1 week sunday 00:00:00'));
                $ctrl_to_date = date('d/m/Y', strtotime('saturday 23:59:59'));
            }
            if ($request->filter_by == "last_week") {
                $ctrl_from_date = date('d/m/Y', strtotime('-2 week sunday 00:00:00'));
                $ctrl_to_date = date('d/m/Y', strtotime('-1 week saturday 23:59:59'));
            }
            if ($request->filter_by == "last_month") {
                $ctrl_from_date = date('d/m/Y', strtotime('first day of previous month'));
                $ctrl_to_date = date('d/m/Y', strtotime('last day of previous month'));
            }
            if ($request->filter_by == "this_quarter") {
                $q_date = SysHelper::get_quarter(date('m'));
                $ctrl_from_date = date('d/m/Y', strtotime($q_date[0]));
                $ctrl_to_date = date('d/m/Y', strtotime($q_date[1]));
            }
            if ($request->filter_by == "pre_quarter") {
                $q_date = SysHelper::get_pre_quarter(date('m'));
                $ctrl_from_date = date('d/m/Y', strtotime($q_date[0]));
                $ctrl_to_date = date('d/m/Y', strtotime($q_date[1]));
            }
            if ($request->filter_by == "this_year") {
                $ctrl_from_date = date('d/m/Y', strtotime(date('Y-01-01')));
                $ctrl_to_date = date('d/m/Y', strtotime(date('Y-12-31')));
            }
            if ($request->filter_by == "last_year") {
                $ctrl_from_date = date('d/m/Y', strtotime("-1 year", strtotime(date('Y-01-01'))));
                $ctrl_to_date = date('d/m/Y', strtotime("-1 year", strtotime(date('Y-12-31'))));
            }

            if ($ctrl_from_date) {
                try {
                    $query->where('date', '>=', Carbon::createFromFormat('d/m/Y', $ctrl_from_date)->format('Y-m-d'));
                } catch (\Exception $e) {}
            }
            if ($ctrl_to_date) {
                try {
                    $query->where('date', '<=', Carbon::createFromFormat('d/m/Y', $ctrl_to_date)->format('Y-m-d'));
                } catch (\Exception $e) {}
            }
            if ($request->invoice_date) {
                try {
                    $query->where('invoice_date', '=', Carbon::parse(str_replace('/', '-', $request->invoice_date))->format('Y-m-d'));
                } catch (\Exception $e) {}
            }
            if ($request->expense_category) {
                $query->where('remarks', 'like', '%' . $request->expense_category . '%');
            }
            if ($request->invoice_no) {
                $query->where('invoice_no', 'like', '%' . $request->invoice_no . '%');
            }
            if ($request->amount) {
                $query->where('amount', str_replace(',', '', $request->amount));
            }
            if ($request->reimbursement_no) {
                $query->where('reimbursement_no', 'like', '%' . $request->reimbursement_no . '%');
            }
            if ($request->deal_id) {
                $deal_id = $request->deal_id;
                $query->whereHas('deal_code', function($q) use ($deal_id) {
                    $q->where('code', 'like', '%' . $deal_id . '%');
                });
            }
            if ($request->project_id) {
                $query->where('project_id', 'like', '%' . $request->project_id . '%');
            }
            if ($request->submitted_by) {
                $submitted_by = $request->submitted_by;
                $query->whereHas('createdby', function($q) use ($submitted_by) {
                    $q->where('full_name', 'like', '%' . $submitted_by . '%');
                });
            }
            if ($request->status_filter) {
                $this->applyReimbursementStatusFilter($query, $request->status_filter);
            }
            if ($request->attachments == 1) {
                $query->whereNotNull('attachmant')->where('attachmant', '!=', '');
            }
            if ($request->attachments == 2) {
                $query->where(function ($q) {
                    $q->whereNull('attachmant')->orWhere('attachmant', '');
                });
            }

            $data = $query->orderByRaw("CASE WHEN dept_head_status=0 THEN 1 WHEN dept_head_status=1 AND acco_head_status=0 THEN 2 WHEN dept_head_status=1 AND acco_head_status=1 AND accounts_status=0 THEN 3 ELSE 4 END ASC")->orderby('date', 'desc')->orderBy('reimbursement_no', 'asc')->get();

            $company = SysCompany::find($company_id);
            $companyName = $company ? ($company->trade_name ?: $company->company_name) : '';

            return Excel::create('reimbursement_request_' . date('YmdHis'), function ($excel) use ($data, $companyName) {
                $excel->sheet('Reimbursement Request', function ($sheet) use ($data, $companyName) {
                    $headers = ['Expense Date', 'Reimb. No', 'Deal ID', 'Customer Name', 'Scope of Work', 'Invoice No', 'Amount', 'Expense Category', 'Head Count & Name', 'Submitted By', 'Status'];
                    $rows = [];
                    foreach ($data as $value) {
                        $rows[] = [
                            $value->date ? date('d/m/Y', strtotime($value->date)) : '',
                            $value->reimbursement_no,
                            $value->deal_code->code ?? '',
                            $value->site_name,
                            $value->scope_of_work,
                            $value->invoice_no,
                            number_format((float) $value->amount, 2, '.', ','),
                            $value->remarks,
                            $value->head_count_name,
                            $value->createdby->full_name ?? '',
                            $this->getReimbursementStatusText($value),
                        ];
                    }

                    $lastColumn = 'K';
                    $lastRow = count($rows) + 4;

                    $sheet->mergeCells('A1:' . $lastColumn . '1');
                    $sheet->mergeCells('A2:' . $lastColumn . '2');
                    $sheet->row(1, [$companyName]);
                    $sheet->row(2, ['Reimbursement Request (' . count($rows) . ')']);
                    $sheet->fromArray([$headers], null, 'A4', false, false);
                    if (count($rows) > 0) {
                        $sheet->fromArray($rows, null, 'A5', false, false);
                    }

                    $sheet->setWidth([
                        'A' => 16,
                        'B' => 16,
                        'C' => 16,
                        'D' => 28,
                        'E' => 22,
                        'F' => 16,
                        'G' => 16,
                        'H' => 22,
                        'I' => 18,
                        'J' => 18,
                        'K' => 24,
                    ]);

                    $sheet->setHeight([
                        1 => 24,
                        2 => 20,
                        4 => 20,
                    ]);

                    $sheet->getStyle('A1:' . $lastColumn . '2')->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => [
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
                        'font' => ['size' => 14],
                    ]);

                    $sheet->getStyle('A4:' . $lastColumn . '4')->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                        'fill' => [
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => ['rgb' => '2D5496'],
                        ],
                        'alignment' => [
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    $sheet->getStyle('A4:' . $lastColumn . $lastRow)->applyFromArray([
                        'borders' => [
                            'allborders' => [
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                            'wrap' => true,
                        ],
                    ]);
                });
            })->download('xlsx');
        } catch (\Throwable $th) {
            return $th;
        }
    }

    private function getReimbursementStatusText($value)
    {
        if ((int) $value->approval_status === 0) {
            return 'Draft';
        }
        if ($value->accounts_status == 1) {
            return 'Payment Processing Approved';
        }
        if ($value->accounts_status == 2) {
            return 'Payment Processing Rejected';
        }
        if ($value->acco_head_status == 1) {
            return 'Finance Approved';
        }
        if ($value->acco_head_status == 2) {
            return 'Finance Rejected';
        }
        if ($value->dept_head_status == 1) {
            return 'Reporting Manager Approved';
        }
        if ($value->dept_head_status == 2) {
            return 'Reporting Manager Rejected';
        }
        return 'New / Pending';
    }

    private function applyReimbursementStatusFilter($query, $status)
    {
        if ($status == 'draft') {
            $query->where('approval_status', 0);
        } elseif ($status == 'accounts_approved') {
            $query->where('accounts_status', 1);
        } elseif ($status == 'accounts_rejected') {
            $query->where('accounts_status', 2);
        } elseif ($status == 'accounts_head_approved') {
            $query->where('accounts_status', 0)->where('acco_head_status', 1);
        } elseif ($status == 'accounts_head_rejected') {
            $query->where('accounts_status', 0)->where('acco_head_status', 2);
        } elseif ($status == 'dept_head_approved') {
            $query->where('accounts_status', 0)->where('acco_head_status', 0)->where('dept_head_status', 1);
        } elseif ($status == 'dept_head_rejected') {
            $query->where('accounts_status', 0)->where('acco_head_status', 0)->where('dept_head_status', 2);
        } elseif ($status == 'new_pending') {
            $query->where('approval_status', 1)->where('accounts_status', 0)->where('acco_head_status', 0)->where('dept_head_status', 0);
        }
    }

    private function isReimbursementAdmin($auth = null)
    {
        $auth = $auth ?: Auth::user();
        return $auth && in_array((int) $auth->role_id, [1, 2], true);
    }

    private function reimbursementPermissionPages($module)
    {
        if ($module === 'track') {
            return ['reimbursement_list', 'crm-reimbursement-track'];
        }

        return ['crm-reimbursement-request', 'reimbursement_list'];
    }

    private function reimbursementPermissionColumn($action)
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

    private function hasReimbursementPermission($module, $action = 'view', $auth = null)
    {
        $auth = $auth ?: Auth::user();
        if (!$auth) {
            return false;
        }
        if ($this->isReimbursementAdmin($auth)) {
            return true;
        }

        $linkIds = SmModuleLink::whereIn('page_name', $this->reimbursementPermissionPages($module))->pluck('id')->toArray();
        if (!count($linkIds)) {
            return false;
        }

        $column = $this->reimbursementPermissionColumn($action);
        return SmRolePermission::where('role_id', $auth->role_id)
            ->whereIn('module_link_id', $linkIds)
            ->where($column, 1)
            ->exists();
    }

    private function reimbursementPermissionSet($module)
    {
        return [
            'create' => $this->hasReimbursementPermission($module, 'create'),
            'view' => $this->hasReimbursementPermission($module, 'view'),
            'edit' => $this->hasReimbursementPermission($module, 'edit'),
            'delete' => $this->hasReimbursementPermission($module, 'delete'),
            'export' => $this->hasReimbursementPermission($module, 'export'),
            'attach' => $this->hasReimbursementPermission($module, 'attach'),
        ];
    }

    private function reportingEmployeeUserIds($auth)
    {
        return SmStaff::where(function ($q) use ($auth) {
            $q->whereRaw("FIND_IN_SET(?, reporting_manager)", [$auth->id])
                ->orWhereRaw("FIND_IN_SET(?, reporting_manager)", [$auth->role_id]);
        })->pluck('user_id')->filter()->toArray();
    }

    private function isReportingApprover($reimbursement, $auth)
    {
        if (!$this->hasReimbursementPermission('track', 'edit', $auth)) {
            return false;
        }

        if ((int) $auth->role_id === 8) {
            return true;
        }

        $staff = SmStaff::where('user_id', $reimbursement->employee_id ?: $reimbursement->created_by)->first();
        if (!$staff || empty($staff->reporting_manager)) {
            return false;
        }

        $managerIds = array_map('trim', explode(',', (string) $staff->reporting_manager));
        return in_array((string) $auth->id, $managerIds, true) || in_array((string) $auth->role_id, $managerIds, true);
    }

    private function isFinanceApprover($auth)
    {
        return (int) $auth->role_id === 27 && $this->hasReimbursementPermission('track', 'edit', $auth);
    }

    private function isPaymentApprover($auth)
    {
        return (int) $auth->role_id === 28 && $this->hasReimbursementPermission('track', 'edit', $auth);
    }

    private function canAccessReimbursementRequest($reimbursement, $auth = null)
    {
        $auth = $auth ?: Auth::user();
        if ($this->isReimbursementAdmin($auth)) {
            return true;
        }

        return (int) $reimbursement->created_by === (int) $auth->id
            || (int) $reimbursement->employee_id === (int) $auth->id;
    }

    private function applyRequestVisibility($query, $auth)
    {
        $query->where(function ($scope) use ($auth) {
            $scope->where('created_by', $auth->id)
                ->orWhere('employee_id', $auth->id);

            if ($this->hasReimbursementPermission('track', 'view', $auth) && $this->hasReimbursementPermission('track', 'edit', $auth)) {
                if ((int) $auth->role_id === 8) {
                    $reportingUserIds = $this->reportingEmployeeUserIds($auth);
                    $scope->orWhere(function ($q) use ($reportingUserIds) {
                        $q->where('approval_status', 1)
                            ->where(function ($reporting) use ($reportingUserIds) {
                                if (count($reportingUserIds)) {
                                    $reporting->whereIn('created_by', $reportingUserIds)
                                        ->orWhereIn('employee_id', $reportingUserIds);
                                } else {
                                    $reporting->whereRaw('1 = 1');
                                }
                            });
                    });
                }
                if ((int) $auth->role_id === 27) {
                    $scope->orWhere(function ($q) {
                        $q->where('approval_status', 1)->where('dept_head_status', 1);
                    });
                }
                if ((int) $auth->role_id === 28) {
                    $scope->orWhere(function ($q) {
                        $q->where('approval_status', 1)->where('dept_head_status', 1)->where('acco_head_status', 1);
                    });
                }
            }
        });
    }

    private function applyTrackVisibility($query, $auth)
    {
        if (!$this->hasReimbursementPermission('track', 'view', $auth)) {
            $query->whereRaw('1 = 0');
            return;
        }

        $query->where(function ($scope) use ($auth) {
            $scope->where(function ($own) use ($auth) {
                $own->where('created_by', $auth->id)->orWhere('employee_id', $auth->id);
            });

            if ($this->hasReimbursementPermission('track', 'edit', $auth)) {
                if ((int) $auth->role_id === 8) {
                    $reportingUserIds = $this->reportingEmployeeUserIds($auth);
                    $scope->orWhere(function ($q) use ($reportingUserIds) {
                        if (count($reportingUserIds)) {
                            $q->whereIn('created_by', $reportingUserIds)->orWhereIn('employee_id', $reportingUserIds);
                        } else {
                            $q->whereRaw('1 = 1');
                        }
                    });
                }
                if ((int) $auth->role_id === 27) {
                    $scope->orWhere(function ($q) {
                        $q->where('dept_head_status', 1);
                    });
                }
                if ((int) $auth->role_id === 28) {
                    $scope->orWhere(function ($q) {
                        $q->where('dept_head_status', 1)->where('acco_head_status', 1);
                    });
                }
            }
        });
    }

    private function canSeeReimbursementInTrack($reimbursement, $auth = null)
    {
        $auth = $auth ?: Auth::user();
        if ((int) $reimbursement->approval_status !== 1 || (int) $reimbursement->status === 2) {
            return false;
        }
        if ($this->isReimbursementAdmin($auth)) {
            return true;
        }
        if (!$this->hasReimbursementPermission('track', 'view', $auth)) {
            return false;
        }
        if ($this->canAccessReimbursementRequest($reimbursement, $auth)) {
            return true;
        }
        if ($this->isReportingApprover($reimbursement, $auth)) {
            return true;
        }
        if ($this->isFinanceApprover($auth) && (int) $reimbursement->dept_head_status === 1) {
            return true;
        }
        if ($this->isPaymentApprover($auth) && (int) $reimbursement->dept_head_status === 1 && (int) $reimbursement->acco_head_status === 1) {
            return true;
        }

        return false;
    }

    private function canApproveReimbursementStage($stage, $reimbursement, $auth = null)
    {
        $auth = $auth ?: Auth::user();
        if ($this->isReimbursementAdmin($auth)) {
            return true;
        }

        if ((int) $reimbursement->approval_status !== 1 || (int) $reimbursement->status === 2) {
            return false;
        }
        if ($stage === 'reporting') {
            return $this->isReportingApprover($reimbursement, $auth) && (int) $reimbursement->dept_head_status !== 1;
        }
        if ($stage === 'finance') {
            return $this->isFinanceApprover($auth)
                && (int) $reimbursement->dept_head_status === 1
                && (int) $reimbursement->acco_head_status !== 1;
        }
        if ($stage === 'payment') {
            return $this->isPaymentApprover($auth)
                && (int) $reimbursement->dept_head_status === 1
                && (int) $reimbursement->acco_head_status === 1
                && (int) $reimbursement->accounts_status !== 1;
        }

        return false;
    }

    public function store(Request $request)
    {
        try {
            if (!$this->hasReimbursementPermission('request', 'create')) {
                Toastr::error('You are not authorized to create reimbursement requests.', 'Failed');
                return redirect()->back();
            }
            if (($request->hasFile('attachmant') || $request->hasFile('attachment')) && !$this->hasReimbursementPermission('request', 'attach')) {
                Toastr::error('You are not authorized to attach files.', 'Failed');
                return redirect()->back()->withInput();
            }

            $doc_file = "";
            $files = $request->file('attachmant') ?: $request->file('attachment');
            if ($files != "") {
                $files = is_array($files) ? $files : [$files];
                for ($i = 0; $i < count($files); $i++) {
                    $file1 = $files[$i];
                    $doc_file = md5(time()) . "_reimbursement_" . $i . "." . $file1->getclientoriginalextension();
                    $file1->move('public/uploads/crm_amc_doc/', $doc_file);
                    $lpo[] = $doc_file;
                }
                $doc_file = implode("|", $lpo);
            }
            $r = new SysCrmReimbursement();
            
            $company_id = session('logged_session_data.company_id');
            $company = \App\SysCompany::find($company_id);
            $other_code = $company ? $company->other_code : 'M';
            
            $clean_amount = str_replace(',', '', $request->amount);

            if (!empty($request->invoice_no)) {
                $exists = SysCrmReimbursement::where('invoice_no', $request->invoice_no)
                    ->where('amount', $clean_amount)
                    ->where('company_id', $company_id)
                    ->exists();

                if ($exists) {
                    Toastr::error('Invoice number with this amount already exists.', 'Failed');
                    return redirect()->back()->withInput();
                }
            }

            $last = SysCrmReimbursement::orderBy('id', 'desc')->first();
            $next_id = ($last ? $last->id : 0) + 1000;
            $r->reimbursement_no = 'RE' . $other_code . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
            
            $r->date = $request->date
                ? Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d')
                : null;
                
            $r->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
            $r->site_name = $request->site_name;
            $r->scope_of_work = $request->scope_of_work;
            $r->invoice_no = $request->invoice_no;
            $r->amount = str_replace(',', '', $request->amount);
            if ($request->remarks == "Other") {
                $r->remarks = $request->remarks_other;
            } else {
                $r->remarks = $request->remarks;
            }
            $r->head_count_name = $request->head_count_name;
            $r->invoice_date = $request->invoice_date ? Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d') : null;
            $r->reimbursable_amount = str_replace(',', '', $request->reimbursable_amount);
            $r->payment_method = $request->payment_method;
            $r->project_id = $request->project_id;
            $r->vendor_name = $request->vendor_name;
            $r->currency_id = $request->currency_id;
            $r->attachmant = $doc_file;
            $r->attachment_remarks = $request->attachment_remarks;
            $r->approval_status = $request->approval_action == 'draft' ? 0 : 1;
            if ($request->has('employee_id') && $request->employee_id != '') {
                $r->employee_id = $request->employee_id;
            }
            $r->created_by = Auth::user()->id;
            $r->created_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            $r->company_id = session('logged_session_data.company_id');
            $r->status = 1;
            $r->save();
            Toastr::success('Reimbursement has been added successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
        }
    }
    public function update(Request $request)
    {
        try {
            $r = SysCrmReimbursement::find($request->edit_id);
            if (!$r) {
                Toastr::error('Reimbursement request not found.', 'Failed');
                return redirect()->back();
            }
            $ownsEditableDraft = $this->canAccessReimbursementRequest($r) && (int) $r->approval_status === 0;
            if (!$ownsEditableDraft && (!$this->hasReimbursementPermission('request', 'edit') || !$this->canAccessReimbursementRequest($r))) {
                Toastr::error('You are not authorized to edit this reimbursement request.', 'Failed');
                return redirect()->back();
            }
            if (($request->hasFile('attachmant') || $request->hasFile('attachment')) && !$this->hasReimbursementPermission('request', 'attach')) {
                Toastr::error('You are not authorized to attach files.', 'Failed');
                return redirect()->back()->withInput();
            }

            $doc_file = "";
            $files = $request->file('attachmant') ?: $request->file('attachment');
            if ($files != "") {
                $files = is_array($files) ? $files : [$files];
                for ($i = 0; $i < count($files); $i++) {
                    $file1 = $files[$i];
                    $doc_file = md5(time()) . "_reimbursement_" . $i . "." . $file1->getclientoriginalextension();
                    $file1->move('public/uploads/crm_amc_doc/', $doc_file);
                    $lpo[] = $doc_file;
                }
                $doc_file = implode("|", $lpo);
            }
            
            $clean_amount = str_replace(',', '', $request->amount);
            $company_id = session('logged_session_data.company_id');

            if (!empty($request->invoice_no)) {
                $exists = SysCrmReimbursement::where('invoice_no', $request->invoice_no)
                    ->where('amount', $clean_amount)
                    ->where('company_id', $company_id)
                    ->where('id', '!=', $request->edit_id)
                    ->exists();

                if ($exists) {
                    Toastr::error('Invoice number with this amount already exists.', 'Failed');
                    return redirect()->back()->withInput();
                }
            }
            
            $r->date = $request->date
                ? Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d')
                : null;
            $r->deal_id = SysHelper::get_dealid_from_code($request->deal_id);
            $r->site_name = $request->site_name;
            $r->scope_of_work = $request->scope_of_work;
            $r->invoice_no = $request->invoice_no;
            $r->amount = str_replace(',', '', $request->amount);
            if ($request->remarks == "Other") {
                $r->remarks = $request->remarks_other;
            } else {
                $r->remarks = $request->remarks;
            }
            $r->head_count_name = $request->head_count_name;
            $r->invoice_date = $request->invoice_date ? Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d') : null;
            $r->reimbursable_amount = str_replace(',', '', $request->reimbursable_amount);
            $r->payment_method = $request->payment_method;
            $r->project_id = $request->project_id;
            $r->vendor_name = $request->vendor_name;
            $r->currency_id = $request->currency_id;
            $r->employee_id = $request->employee_id ?: null;
            if ($doc_file != "") {
                $r->attachmant = $doc_file;
            }
            $r->attachment_remarks = $request->attachment_remarks;
            if ((int) $r->approval_status === 0) {
                $r->approval_status = $request->approval_action == 'submit' ? 1 : 0;
            }
            $r->updated_by = Auth::user()->id;
            $r->updated_at = Carbon::now('+04:00')->format('Y-m-d H:i:s');

            if ($r->dept_head_status == 2) {
                $r->dept_head_status = 0;
            }
            if ($r->acco_head_status == 2) {
                $r->acco_head_status = 0;
            }
            if ($r->accounts_status == 2) {
                $r->accounts_status = 0;
            }

            $r->save();
            Toastr::success('Reimbursement has been updated successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function account_approve(Request $request)
    {
        try {
            $r = SysCrmReimbursement::find($request->account_re_id);
            if (!$r) return redirect()->back();
            if (!$this->canApproveReimbursementStage('payment', $r)) {
                Toastr::error('You are not authorized to approve this stage.', 'Failed');
                return redirect()->back();
            }
            
            // Backend validation
            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && $r->acco_head_status != 1) {
                Toastr::error('Finance must approve first.', 'Failed');
                return redirect()->back();
            }
            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && $r->accounts_status == 1) {
                Toastr::error('Approved record cannot be edited.', 'Failed');
                return redirect()->back();
            }

            $r->accounts_status = $request->btn_status;
            $r->accounts_by = Auth::user()->id;
            $r->accounts_remarks = $request->remarks;
            
            if ($request->btn_status == 1) {
                $r->accounts_payment_voucher_no = $request->accounts_payment_voucher_no;
                $r->accounts_payment_date = $request->accounts_payment_date ? Carbon::createFromFormat('d/m/Y', $request->accounts_payment_date)->format('Y-m-d') : null;
                $r->accounts_payment_method = $request->accounts_payment_method;
                $r->accounts_bank_account_id = $request->accounts_bank_account_id;
                $r->accounts_paid_amount = $request->accounts_paid_amount;
                $r->accounts_payment_status = $request->accounts_payment_status;
                $r->accounts_payment_reference = $request->accounts_payment_reference;
                $r->accounts_datetime = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            }

            $r->save();

            if ($request->btn_status == 1) {
                Toastr::success('Approved has been added successfully', 'Success');
            } else {
                Toastr::warning('Disapproved has been added successfully', 'Warning');
            }
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function accounts_head_approve(Request $request)
    {
        try {
            $r = SysCrmReimbursement::find($request->acco_head_re_id);
            if (!$r) return redirect()->back();
            if (!$this->canApproveReimbursementStage('finance', $r)) {
                Toastr::error('You are not authorized to approve this stage.', 'Failed');
                return redirect()->back();
            }

            // Backend validation
            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && $r->dept_head_status != 1) {
                Toastr::error('Reporting Manager must approve first.', 'Failed');
                return redirect()->back();
            }
            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && $r->acco_head_status == 1) {
                Toastr::error('Approved record cannot be edited.', 'Failed');
                return redirect()->back();
            }

            $r->acco_head_status = $request->btn_status;
            $r->acco_head_by = Auth::user()->id;
            $r->acco_head_remarks = $request->remarks;
            
            if ($request->btn_status == 1) {
                $r->acco_head_approved_amount = $request->acco_head_approved_amount;
                $r->acco_head_account_id = $request->acco_head_account_id;
                $r->acco_head_payment_required = $request->acco_head_payment_required;
                $r->acco_head_datetime = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            }

            $r->save();

            if ($request->btn_status == 1) {
                Toastr::success('Approved has been added successfully', 'Success');
            } else {
                Toastr::warning('Disapproved has been added successfully', 'Warning');
            }
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }
    public function dept_head_approve(Request $request)
    {
        try {
            $r = SysCrmReimbursement::find($request->dept_head_re_id);
            if (!$r) return redirect()->back();
            if (!$this->canApproveReimbursementStage('reporting', $r)) {
                Toastr::error('You are not authorized to approve this stage.', 'Failed');
                return redirect()->back();
            }

            if (Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && $r->dept_head_status == 1) {
                Toastr::error('Approved record cannot be edited.', 'Failed');
                return redirect()->back();
            }

            $r->dept_head_status = $request->btn_status;
            $r->dept_head_by = Auth::user()->id;
            $r->dept_head_remarks = $request->remarks;
            
            if ($request->btn_status == 1) {
                $r->dept_head_date = $request->dept_head_date ? Carbon::createFromFormat('d/m/Y', $request->dept_head_date)->format('Y-m-d') : null;
                $r->dept_head_datetime = Carbon::now('+04:00')->format('Y-m-d H:i:s');
            }

            $r->save();

            if ($request->btn_status == 1) {
                Toastr::success('Approved has been added successfully', 'Success');
            } else {
                Toastr::warning('Disapproved has been added successfully', 'Warning');
            }
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Something went wrong, please try again', 'Failed');
            return redirect()->back();
        }
    }


    public function get_custname(Request $request)
    {
        try {
            $deal_id = SysHelper::get_dealid_from_code($request->deal_id);

            $customers = DB::table('sys_cust_suppl as cs')->select('cs.name')
                ->leftjoin('sys_crm_deals as d', 'd.cust_id', 'cs.id')
                ->where('d.id', $deal_id)
                ->limit(1)->orderby('cs.id', 'desc')->get();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            return json_encode(array('data' => $customers));
        } else {
            $retData = 'ERROR';
            return json_encode(array('data' => $retData));
        }
    }
    public function delete(Request $request)
    {
        try {
            $r = SysCrmReimbursement::find($request->id);
            if (!$r || !$this->hasReimbursementPermission('request', 'delete') || !$this->canAccessReimbursementRequest($r)) {
                return response()->json('UNAUTHORIZED', 403);
            }
            $r->status = 2;
            $r->save();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {
            return json_encode("SUCCESS");
        } else {
            $retData = 'ERROR';
            return json_encode('ERROR');
        }
    }
    public function restore(Request $request)
    {
        try {
            $r = SysCrmReimbursement::find($request->id);
            if (!$r || !$this->hasReimbursementPermission('request', 'delete') || !$this->canAccessReimbursementRequest($r)) {
                Toastr::error('You are not authorized to restore this reimbursement request.', 'Failed');
                return redirect()->back();
            }
            $r->status = 1;
            $r->save();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            $bug = $e->errorInfo[1];
        }
        if ($bug == 0) {

            if ($r->dept_head_status == 2) {
                $r->dept_head_status = 0;
            }
            if ($r->acco_head_status == 2) {
                $r->acco_head_status = 0;
            }
            if ($r->accounts_status == 2) {
                $r->accounts_status = 0;
            }

            $r->save();
            Toastr::success('Reimbursement has been updated successfully', 'Success');
            return redirect()->back();
        }
    }

    public function track(Request $request, $id = null)
    {
        try {
            if (!$this->hasReimbursementPermission('track', 'view')) {
                abort(403, 'You are not authorized to view Reimbursement Track.');
            }
            $query = SysCrmReimbursement::with(['createdby', 'deal_code.customername', 'accountsby', 'accoheadby', 'deptheadby', 'currencycode'])
                ->where('status', '!=', 2)
                ->where('approval_status', 1);
            $company_id = session('logged_session_data.company_id');
            if ($company_id != 1) {
                $query->where('company_id', $company_id);
            }
            if (!$this->isReimbursementAdmin()) {
                $this->applyTrackVisibility($query, Auth::user());
            }

            $ctrl_from_date = $request->from_date;
            $ctrl_to_date = $request->to_date;
            $filter_by = "";
            if ($request->filter_by == "this_month") {
                $ctrl_from_date = date('d/m/Y', strtotime(date('Y-m-01')));
                $ctrl_to_date = date('d/m/Y', strtotime(date("Y-m-t")));
                $filter_by = 'this_month';
            }
            if ($request->filter_by == "today") {
                $ctrl_from_date = date('d/m/Y');
                $ctrl_to_date = date('d/m/Y');
                $filter_by = 'today';
            }
            if ($request->filter_by == "this_week") {
                $ctrl_from_date = date('d/m/Y', strtotime('-1 week sunday 00:00:00'));
                $ctrl_to_date = date('d/m/Y', strtotime('saturday 23:59:59'));
                $filter_by = 'this_week';
            }
            if ($request->filter_by == "last_week") {
                $ctrl_from_date = date('d/m/Y', strtotime('-2 week sunday 00:00:00'));
                $ctrl_to_date = date('d/m/Y', strtotime('-1 week saturday 23:59:59'));
                $filter_by = 'last_week';
            }
            if ($request->filter_by == "last_month") {
                $ctrl_from_date = date('d/m/Y', strtotime('first day of previous month'));
                $ctrl_to_date = date('d/m/Y', strtotime('last day of previous month'));
                $filter_by = 'last_month';
            }
            if ($request->filter_by == "this_quarter") {
                $q_date = SysHelper::get_quarter(date('m'));
                $ctrl_from_date = date('d/m/Y', strtotime($q_date[0]));
                $ctrl_to_date = date('d/m/Y', strtotime($q_date[1]));
                $filter_by = 'this_quarter';
            }
            if ($request->filter_by == "pre_quarter") {
                $q_date = SysHelper::get_pre_quarter(date('m'));
                $ctrl_from_date = date('d/m/Y', strtotime($q_date[0]));
                $ctrl_to_date = date('d/m/Y', strtotime($q_date[1]));
                $filter_by = 'pre_quarter';
            }
            if ($request->filter_by == "this_year") {
                $ctrl_from_date = date('d/m/Y', strtotime(date('Y-01-01')));
                $ctrl_to_date = date('d/m/Y', strtotime(date('Y-12-31')));
                $filter_by = 'this_year';
            }
            if ($request->filter_by == "last_year") {
                $ctrl_from_date = date('d/m/Y', strtotime("-1 year", strtotime(date('Y-01-01'))));
                $ctrl_to_date = date('d/m/Y', strtotime("-1 year", strtotime(date('Y-12-31'))));
                $filter_by = 'last_year';
            }

            if ($request->has('reimbursement_no') && $request->reimbursement_no != '') {
                $query->where('reimbursement_no', 'like', '%' . $request->reimbursement_no . '%');
            }
            if ($request->has('vendor_name') && $request->vendor_name != '') {
                $query->where('vendor_name', 'like', '%' . $request->vendor_name . '%');
            }
            if ($ctrl_from_date) {
                $query->whereDate('date', '>=', \Carbon\Carbon::createFromFormat('d/m/Y', $ctrl_from_date)->format('Y-m-d'));
            }
            if ($ctrl_to_date) {
                $query->whereDate('date', '<=', \Carbon\Carbon::createFromFormat('d/m/Y', $ctrl_to_date)->format('Y-m-d'));
            }
            if ($request->has('expense_category') && $request->expense_category != '') {
                $query->where('remarks', 'like', '%' . $request->expense_category . '%');
            }
            if ($request->has('invoice_no') && $request->invoice_no != '') {
                $query->where('invoice_no', 'like', '%' . $request->invoice_no . '%');
            }
            if ($request->has('invoice_date') && $request->invoice_date != '') {
                $query->whereDate('invoice_date', '=', \Carbon\Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d'));
            }
            if ($request->has('amount') && $request->amount != '') {
                // Strip commas from input if any, though amount might be typed as numbers
                $amt = str_replace(',', '', $request->amount);
                $query->where('amount', 'like', '%' . $amt . '%');
            }
            if ($request->has('deal_id') && $request->deal_id != '') {
                $deal_id = $request->deal_id;
                $query->whereHas('deal_code', function($q) use ($deal_id) {
                    $q->where('code', 'like', '%' . $deal_id . '%');
                });
            }
            if ($request->has('project_id') && $request->project_id != '') {
                $query->where('project_id', 'like', '%' . $request->project_id . '%');
            }
            if ($request->has('submitted_by') && $request->submitted_by != '') {
                $submitted_by = $request->submitted_by;
                $query->whereHas('createdby', function($q) use ($submitted_by) {
                    $q->where('full_name', 'like', '%' . $submitted_by . '%');
                });
            }
            if ($request->has('status_filter') && $request->status_filter != '') {
                $this->applyReimbursementStatusFilter($query, $request->status_filter);
            }
            if ($request->attachments == 1) {
                $query->whereNotNull('attachmant')->where('attachmant', '!=', '');
            }
            if ($request->attachments == 2) {
                $query->where(function ($q) {
                    $q->whereNull('attachmant')->orWhere('attachmant', '');
                });
            }

            $data = $query->orderByRaw("CASE WHEN dept_head_status=0 THEN 1 WHEN dept_head_status=1 AND acco_head_status=0 THEN 2 WHEN dept_head_status=1 AND acco_head_status=1 AND accounts_status=0 THEN 3 ELSE 4 END ASC")->orderby('date', 'desc')->orderBy('reimbursement_no', 'asc')->get();

            $ctrl_reimbursement_no = $request->reimbursement_no;
            $ctrl_vendor_name = $request->vendor_name;
            $ctrl_expense_category = $request->expense_category;
            $ctrl_invoice_no = $request->invoice_no;
            $ctrl_invoice_date = $request->invoice_date;
            $ctrl_amount = $request->amount;
            $ctrl_deal_id = $request->deal_id;
            $ctrl_project_id = $request->project_id;
            $ctrl_attachments = $request->attachments;
            $ctrl_submitted_by = $request->submitted_by;
            $ctrl_status = $request->status_filter;
            
            $active_id = $id;
            $selectedReimbursement = null;
            if ($active_id == null) {
                $firstRecord = $data->first();
                if ($firstRecord) {
                    $active_id = $firstRecord->id;
                    $selectedReimbursement = $firstRecord;
                }
            } else {
                $selectedReimbursement = $data->firstWhere('id', (int) $active_id);
                if (!$selectedReimbursement) {
                    $firstRecord = $data->first();
                    $active_id = $firstRecord ? $firstRecord->id : null;
                    $selectedReimbursement = $firstRecord;
                }
            }
            
            $staff = null;
            $submitter = null;
            if ($selectedReimbursement) {
                $staff = \App\SmStaff::where('user_id', $selectedReimbursement->employee_id ?? $selectedReimbursement->created_by)->first();
                $submitter = \App\SmStaff::where('user_id', $selectedReimbursement->created_by)->first();
            }
            
            $reimbursementPermissions = $this->reimbursementPermissionSet('request');
            $reimbursementTrackPermissions = $this->reimbursementPermissionSet('track');
            $permissions = SmRolePermission::where('role_id', Auth::user()->role_id)->get();

            return view('backEnd.amc.reimbursement_track', compact('data', 'active_id', 'selectedReimbursement', 'staff', 'submitter', 'ctrl_reimbursement_no', 'ctrl_vendor_name', 'ctrl_from_date', 'ctrl_to_date', 'ctrl_expense_category', 'ctrl_invoice_no', 'ctrl_invoice_date', 'ctrl_amount', 'ctrl_deal_id', 'ctrl_project_id', 'ctrl_attachments', 'ctrl_submitted_by', 'ctrl_status', 'filter_by', 'reimbursementPermissions', 'reimbursementTrackPermissions', 'permissions'));
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function search(Request $request)
    {
        try {
            $referer = (string) $request->headers->get('referer');
            $isTrackContext = $request->input('context') == 'track' || strpos($referer, 'crm-reimbursement-track') !== false;
            if (!$this->hasReimbursementPermission($isTrackContext ? 'track' : 'request', 'view')) {
                return response()->json([]);
            }
            $company_id = session('logged_session_data.company_id');
            $query = SysCrmReimbursement::with(['deal_code.customername', 'currencycode'])
                ->where('status', '!=', 2);
            if ($company_id != 1) {
                $query->where('company_id', $company_id);
            }
            if ($isTrackContext) {
                $query->where('approval_status', 1);
                if (!$this->isReimbursementAdmin()) {
                    $this->applyTrackVisibility($query, Auth::user());
                }
            } elseif (!$this->isReimbursementAdmin()) {
                $this->applyRequestVisibility($query, Auth::user());
            }

            if ($request->has('query')) {
                $search = $request->input('query');
                $query->where(function ($q) use ($search) {
                    $q->where('reimbursement_no', 'LIKE', "%{$search}%")
                        ->orWhere('vendor_name', 'LIKE', "%{$search}%");
                });
            }

            $data = $query->orderBy('id', 'desc')->get();

            $formatted_data = $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'reimbursement_no' => $item->reimbursement_no,
                    'customer_name' => $item->deal_code->customername->name ?? '',
                    'date' => $item->date,
                    'amount' => $item->amount,
                    'currency_code' => $item->currencycode->code ?? ''
                ];
            });

            return response()->json($formatted_data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDetails($id)
    {
        try {
            $selectedReimbursement = SysCrmReimbursement::with(['deal_code.customername', 'currencycode'])->find($id);
            if (!$selectedReimbursement) {
                return response()->json(['error' => 'Not found'], 404);
            }
            $isRequestContext = request('context') == 'request' || request()->headers->get('referer') && strpos(request()->headers->get('referer'), 'crm-reimbursement-request') !== false;
            if ($isRequestContext) {
                if (!$this->hasReimbursementPermission('request', 'view') || (!$this->isReimbursementAdmin() && !$this->canAccessReimbursementRequest($selectedReimbursement) && !$this->canSeeReimbursementInTrack($selectedReimbursement))) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            } else if (!$this->canSeeReimbursementInTrack($selectedReimbursement)) {
                return response()->json(['error' => 'Not found'], 404);
            }
            $staff = \App\SmStaff::where('user_id', $selectedReimbursement->employee_id ?? $selectedReimbursement->created_by)->first();
            $submitter = \App\SmStaff::where('user_id', $selectedReimbursement->created_by)->first();
            $reimbursementPermissions = $this->reimbursementPermissionSet('request');
            $reimbursementTrackPermissions = $this->reimbursementPermissionSet('track');

            return view('backEnd.amc.reimbursement_track_detail', compact('selectedReimbursement', 'staff', 'submitter', 'reimbursementPermissions', 'reimbursementTrackPermissions'))->render();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}

<?php

namespace App\Http\Controllers;

use App\SmStaff;
use App\SysChartofAccountsTransaction;
use App\SysCompany;
use App\SysHelper;
use App\SysSalesReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SysSalesReturnReportDetailController extends Controller
{
    public function index(Request $request)
    {
        $report_group = $request->get('report_group', 'company_wise');
        $scope_company_id = $request->get('scope_company_id', '');

        $filter_by = "this_month";
        $ctrl_date = date('Y-m-01');
        $ctrl_date2 = date("Y-m-t", strtotime($ctrl_date));
        $ctrl_doc_no = "";
        $ctrl_deal_id = "";
        $ctrl_customer = "";
        $ctrl_amount = "";
        $ctrl_amount_doc_numbers = collect();
        $ctrl_sales_person = "";
        $ctrl_company = "";
        $ctrl_show_all = 0;
        $ctrl_from_day = '';
        $ctrl_to_day = '';

        $r = SysHelper::get_data_by_role();
        $company_id = $r[0];
        if (session('logged_session_data.company_id') == 1) {
            $company_id = SysCompany::pluck('id');
        }

        $customer_scope_company_ids = $company_id;
        $sales_person_scope_company_ids = $company_id;
        if ($scope_company_id != "") {
            $customer_scope_company_ids = [$scope_company_id];
            $sales_person_scope_company_ids = [$scope_company_id];
        }

        $customer_list = SysHelper::get_customer_list($customer_scope_company_ids);
        $sales_person_list = SmStaff::select(
            'id',
            'user_id',
            DB::raw("CONCAT(first_name, ' ', last_name) as full_name")
        )
            ->where('active_status', '=', '1')
            ->whereIn('role_id', [5, 33, 30, 8, 32])
            ->whereIn('company_id', $sales_person_scope_company_ids)
            ->orderBy('first_name', 'asc')
            ->get();
        $company_list = SysCompany::select('id', 'company_name')->whereIn('id', $company_id)->get();

        $query = SysSalesReturn::select(
            DB::raw('sys_sales_return.*, (SELECT max(code) FROM sys_crm_deals WHERE id=sys_sales_return.deal_id) AS code, (SELECT max(deal_profit) FROM sys_crm_deals WHERE id=sys_sales_return.deal_id) AS deal_profit, (SELECT max(deal_value) FROM sys_crm_deals WHERE id=sys_sales_return.deal_id) AS deal_value, (SELECT max(deal_currency) FROM sys_crm_deals WHERE id=sys_sales_return.deal_id) AS deal_currency'),
            DB::raw('(SELECT SUM(vatamount) FROM sys_sales_return_list WHERE sr_id = sys_sales_return.id) AS total_vatamount'),
            DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_return_list WHERE sr_id = sys_sales_return.id) AS total_taxableamount'),
            DB::raw('(
                (SELECT COALESCE(SUM(taxableamount),0) FROM sys_sales_return_list WHERE sr_id = sys_sales_return.id) +
                (SELECT COALESCE(SUM(vatamount),0) FROM sys_sales_return_list WHERE sr_id = sys_sales_return.id)
            ) AS amount')
        );

        $query->whereIn('company_id', $company_id);
        $query->where('status', '!=', 2);

        if (SysHelper::get_pagination_post($request)) {
            if ($request->from_date != "" && $request->filter_by == "") {
                $ctrl_date = Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');
            }
            if ($request->to_date != "" && $request->filter_by == "") {
                $ctrl_date2 = Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');
            }
            if ($request->filter_by == "this_month") {
                $ctrl_date = date('Y-m-01');
                $ctrl_date2 = date("Y-m-t", strtotime($ctrl_date));
                $filter_by = 'this_month';
            }
            if ($request->filter_by == "today") {
                $ctrl_date = date('Y-m-d');
                $ctrl_date2 = date('Y-m-d');
                $filter_by = 'today';
            }
            if ($request->filter_by == "this_week") {
                $ctrl_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                $ctrl_date2 = date('Y-m-d', strtotime('saturday 23:59:59'));
                $filter_by = 'this_week';
            }
            if ($request->filter_by == "last_week") {
                $ctrl_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                $ctrl_date2 = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                $filter_by = 'last_week';
            }
            if ($request->filter_by == "last_month") {
                $ctrl_date = date('Y-m-d', strtotime('first day of previous month'));
                $ctrl_date2 = date('Y-m-d', strtotime('last day of previous month'));
                $filter_by = 'last_month';
            }
            if ($request->filter_by == "this_quarter") {
                $q_date = SysHelper::get_quarter(date('m'));
                $ctrl_date = $q_date[0];
                $ctrl_date2 = $q_date[1];
                $filter_by = 'this_quarter';
            }
            if ($request->filter_by == "pre_quarter") {
                $q_date = SysHelper::get_pre_quarter(date('m'));
                $ctrl_date = $q_date[0];
                $ctrl_date2 = $q_date[1];
                $filter_by = 'pre_quarter';
            }
            if ($request->filter_by == "this_year") {
                $ctrl_date = date('Y-01-01');
                $ctrl_date2 = date('Y-12-31');
                $filter_by = 'this_year';
            }
            if ($request->filter_by == "last_year") {
                $ctrl_date = date("Y-01-01", strtotime("-1 year"));
                $ctrl_date2 = date("Y-12-31", strtotime("-1 year"));
                $filter_by = 'last_year';
            }

            if ($request->documents_number != "") {
                $query->where('doc_number', 'like', '%' . $request->documents_number . '%');
                $ctrl_doc_no = $request->documents_number;
            }
            if ($request->customer != "") {
                $query->where('customer', $request->customer);
                $ctrl_customer = $request->customer;
            }
            if ($request->deal_number != "") {
                $query->where('deal_id', 'like', '%' . SysHelper::get_dealid_from_code($request->deal_number) . '%');
                $ctrl_deal_id = $request->deal_number;
            }
            if ($request->amount != "") {
                $amt_nos = SysChartofAccountsTransaction::select('transaction_no', DB::raw('SUM(debit_amount) as total_debit_amount'))
                    ->where('transaction_type', 'salesreturn')
                    ->groupBy('transaction_no')
                    ->havingRaw('SUM(debit_amount) BETWEEN ? AND ?', [$request->amount, $request->amount])
                    ->pluck('transaction_no');
                $query->whereIn('doc_number', $amt_nos);
                $ctrl_amount_doc_numbers = $amt_nos;
                $ctrl_amount = $request->amount;
            }
            if ($ctrl_date != "" && $ctrl_date2 != "") {
                $query->whereBetween('doc_date', [$ctrl_date, $ctrl_date2]);
            }
            if ($ctrl_date != "" && $ctrl_date2 == "") {
                $query->where('doc_date', $ctrl_date);
            }
            if ($ctrl_date == "" && $ctrl_date2 != "") {
                $query->where('doc_date', $ctrl_date2);
            }
            if ($request->sales_person != "") {
                $query->where('sales_man', $request->sales_person);
                $ctrl_sales_person = $request->sales_person;
            }
            if ($request->company != "") {
                $query->where('company_id', $request->company);
                $ctrl_company = $request->company;
            }
            if ($request->show_all == "1") {
                $ctrl_show_all = 1;
            }

            if ($report_group === 'customer_wise') {
                if ($request->has('from_day')) {
                    $ctrl_from_day = (string) $request->get('from_day');
                }
                if ($request->has('to_day')) {
                    $ctrl_to_day = (string) $request->get('to_day');
                }
            }
        }

        if ($scope_company_id != "") {
            $query->where('company_id', $scope_company_id);
        }

        $effective_sales_person_company_ids = $sales_person_scope_company_ids;
        if ($ctrl_company != "") {
            $effective_sales_person_company_ids = [$ctrl_company];
        }
        if ($scope_company_id != "") {
            $effective_sales_person_company_ids = [$scope_company_id];
        }
        $sales_person_list = SmStaff::select(
            'id',
            'user_id',
            DB::raw("CONCAT(first_name, ' ', last_name) as full_name")
        )
            ->where('active_status', '=', '1')
            ->whereIn('role_id', [5, 33, 30, 8, 32])
            ->whereIn('company_id', $effective_sales_person_company_ids)
            ->orderBy('first_name', 'asc')
            ->get();

        $query->orderBy('doc_number', 'desc');

        $salesreturn = null;
        $report_rows = collect();

        if ($report_group === 'date_wise') {
            if ($ctrl_show_all == 1) {
                $salesreturn = $query->get();
            } else {
                $salesreturn = $query->paginate(150);
            }
        } else {
            $itemsAggSub = DB::table('sys_sales_return_list')
                ->select(
                    'sr_id',
                    DB::raw('SUM(taxableamount) as total_taxableamount'),
                    DB::raw('SUM(vatamount) as total_vatamount')
                )
                ->groupBy('sr_id');

            $applyGpMetrics = function ($rows, $gpData, $keyField) {
                $gpMap = [];
                $taxableMap = [];

                foreach ($gpData as $item) {
                    $key = $item->{$keyField};
                    if ($key === null || $key === '') {
                        continue;
                    }

                    $taxable = (float) $item->total_taxableamount;
                    $deal_value = (float) SysHelper::get_aed_amount_new($item->deal_currency, $item->deal_value);
                    $deal_profit = (float) SysHelper::get_aed_amount_new($item->deal_currency, $item->deal_profit);
                    $deal_percentage = $deal_value != 0 ? round((($deal_profit / $deal_value) * 100), 2) : 0;
                    $gp = ($taxable * $deal_percentage) / 100;

                    if (!isset($gpMap[$key])) {
                        $gpMap[$key] = 0;
                        $taxableMap[$key] = 0;
                    }

                    $gpMap[$key] += $gp;
                    $taxableMap[$key] += $taxable;
                }

                return $rows->map(function ($row) use ($keyField, $gpMap, $taxableMap) {
                    $key = $row->{$keyField};
                    $row->gp = isset($gpMap[$key]) ? $gpMap[$key] : 0;
                    $row->gp_percent = (isset($taxableMap[$key]) && $taxableMap[$key] != 0)
                        ? (($row->gp / $taxableMap[$key]) * 100)
                        : 0;
                    return $row;
                });
            };

            $lastReturnAgeFilterActive = false;
            $lastReturnAgeMinDate = null;
            $lastReturnAgeMaxDate = null;

            if ($report_group === 'customer_wise') {
                $fromDays = (is_numeric($ctrl_from_day) && $ctrl_from_day !== '') ? (int) $ctrl_from_day : null;
                $toDays = (is_numeric($ctrl_to_day) && $ctrl_to_day !== '') ? (int) $ctrl_to_day : null;

                if ($fromDays !== null || $toDays !== null) {
                    $lastReturnAgeFilterActive = true;
                    $today = Carbon::today();

                    if ($fromDays !== null && $toDays !== null && $fromDays > $toDays) {
                        $tmp = $fromDays;
                        $fromDays = $toDays;
                        $toDays = $tmp;
                    }

                    if ($fromDays !== null && $toDays !== null) {
                        $lastReturnAgeMinDate = $today->copy()->subDays($toDays)->format('Y-m-d');
                        $lastReturnAgeMaxDate = $today->copy()->subDays($fromDays)->format('Y-m-d');
                    } elseif ($fromDays !== null) {
                        $lastReturnAgeMaxDate = $today->copy()->subDays($fromDays)->format('Y-m-d');
                    } else {
                        $lastReturnAgeMinDate = $today->copy()->subDays($toDays)->format('Y-m-d');
                    }
                }
            }

            $ledgerBalanceAsOnDate = $ctrl_date2 != "" ? $ctrl_date2 : ($ctrl_date != "" ? $ctrl_date : date('Y-m-d'));
            $ledgerCompanyId = session('logged_session_data.company_id');
            if ($ctrl_company != "") {
                $ledgerCompanyId = $ctrl_company;
            }
            if ($scope_company_id != "") {
                $ledgerCompanyId = $scope_company_id;
            }

            $ledgerBalanceSub = DB::table('sys_chartofaccounts_transaction as cat')
                ->join('sys_chartofaccounts as coa', 'coa.id', '=', 'cat.account_id')
                ->select(
                    'cat.account_id',
                    DB::raw('SUM(CASE WHEN coa.`group` IN (1,3) THEN (COALESCE(cat.debit_amount,0) - COALESCE(cat.credit_amount,0)) ELSE (COALESCE(cat.credit_amount,0) - COALESCE(cat.debit_amount,0)) END) as total_balance')
                )
                ->where('cat.company_id', $ledgerCompanyId)
                ->where('cat.status', 1)
                ->where('cat.transaction_type', '!=', 'opbinvoice')
                ->whereDate('cat.transaction_date', '<=', $ledgerBalanceAsOnDate)
                ->groupBy('cat.account_id');

            $customerLedgerBalanceMap = (clone $ledgerBalanceSub)
                ->whereIn('cat.account_id', collect($customer_list)->pluck('id')->toArray())
                ->pluck('total_balance', 'account_id');

            $customerIds = collect($customer_list)->pluck('id')->toArray();
            $customerSalesPersonMap = DB::table('sys_chartofaccounts as ca')
                ->join('sys_cust_suppl as cs', 'cs.code', '=', 'ca.account_code')
                ->leftJoin('sys_cust_suppl_assign as csa', function ($join) {
                    $join->on('csa.cust_supp_id', '=', 'cs.id')
                        ->where('csa.type', 1);
                })
                ->leftJoin('sm_staffs as sp', 'sp.user_id', '=', 'csa.user_id')
                ->select(
                    'ca.id as customer_id',
                    DB::raw('COALESCE(GROUP_CONCAT(DISTINCT TRIM(CONCAT(COALESCE(sp.first_name, ""), " ", COALESCE(sp.last_name, ""))) ORDER BY sp.first_name SEPARATOR ", "), "") as sales_person_names')
                )
                ->whereIn('ca.id', $customerIds)
                ->whereIn('ca.company_id', $company_id)
                ->whereIn('cs.company_id', $company_id)
                ->groupBy('ca.id')
                ->pluck('sales_person_names', 'customer_id');

            $baseAggQuery = DB::table('sys_sales_return as sr')
                ->leftJoinSub($itemsAggSub, 'ia', function ($join) {
                    $join->on('ia.sr_id', '=', 'sr.id');
                })
                ->leftJoin('sys_company as c', 'c.id', '=', 'sr.company_id')
                ->leftJoin('sys_chartofaccounts as ca', 'ca.id', '=', 'sr.customer')
                ->leftJoin('sm_staffs as sp', 'sp.user_id', '=', 'sr.sales_man')
                ->whereIn('sr.company_id', $company_id)
                ->where('sr.status', '!=', 2);

            if ($ctrl_doc_no != "") {
                $baseAggQuery->where('sr.doc_number', 'like', '%' . $ctrl_doc_no . '%');
            }
            if ($ctrl_customer != "") {
                $baseAggQuery->where('sr.customer', $ctrl_customer);
            }
            if ($ctrl_deal_id != "") {
                $baseAggQuery->where('sr.deal_id', 'like', '%' . SysHelper::get_dealid_from_code($ctrl_deal_id) . '%');
            }
            if ($ctrl_amount != "") {
                $baseAggQuery->whereIn('sr.doc_number', $ctrl_amount_doc_numbers);
            }
            if (!$lastReturnAgeFilterActive) {
                if ($ctrl_date != "" && $ctrl_date2 != "") {
                    $baseAggQuery->whereBetween('sr.doc_date', [$ctrl_date, $ctrl_date2]);
                }
                if ($ctrl_date != "" && $ctrl_date2 == "") {
                    $baseAggQuery->whereDate('sr.doc_date', $ctrl_date);
                }
                if ($ctrl_date == "" && $ctrl_date2 != "") {
                    $baseAggQuery->whereDate('sr.doc_date', $ctrl_date2);
                }
            }
            if ($ctrl_sales_person != "") {
                $baseAggQuery->where('sr.sales_man', $ctrl_sales_person);
            }
            if ($ctrl_company != "") {
                $baseAggQuery->where('sr.company_id', $ctrl_company);
            }
            if ($scope_company_id != "") {
                $baseAggQuery->where('sr.company_id', $scope_company_id);
            }

            if ($report_group === 'company_wise') {
                $report_rows = (clone $baseAggQuery)
                    ->select(
                        'sr.company_id',
                        DB::raw('COALESCE(c.company_name, "N/A") as group_name'),
                        DB::raw('COUNT(sr.id) as return_count'),
                        DB::raw('SUM(COALESCE(ia.total_taxableamount,0)) as taxable'),
                        DB::raw('SUM(COALESCE(ia.total_vatamount,0)) as tax'),
                        DB::raw('SUM(COALESCE(ia.total_taxableamount,0) + COALESCE(ia.total_vatamount,0)) as amount'),
                        DB::raw('0 as gp'),
                        DB::raw('0 as gp_percent')
                    )
                    ->groupBy('sr.company_id', 'c.company_name')
                    ->orderBy('group_name')
                    ->get();

                $companyGpData = (clone $baseAggQuery)
                    ->leftJoin('sys_crm_deals as d', 'd.id', '=', 'sr.deal_id')
                    ->select(
                        'sr.company_id',
                        'ia.total_taxableamount',
                        'd.deal_currency',
                        'd.deal_value',
                        'd.deal_profit'
                    )
                    ->get();
                $report_rows = $applyGpMetrics($report_rows, $companyGpData, 'company_id');

                $source_companies = $scope_company_id != ""
                    ? $company_list->where('id', (int) $scope_company_id)->values()
                    : $company_list;
                $indexed = $report_rows->keyBy('company_id');
                $report_rows = $source_companies->map(function ($company) use ($indexed) {
                    if (isset($indexed[$company->id])) {
                        return $indexed[$company->id];
                    }
                    return (object) [
                        'company_id' => $company->id,
                        'group_name' => $company->company_name,
                        'return_count' => 0,
                        'taxable' => 0,
                        'tax' => 0,
                        'amount' => 0,
                        'gp' => 0,
                        'gp_percent' => 0,
                    ];
                })->values();
            } elseif ($report_group === 'customer_wise') {
                $report_rows = (clone $baseAggQuery)
                    ->select(
                        'sr.customer',
                        DB::raw('COALESCE(ca.account_name, "N/A") as group_name'),
                        DB::raw('COALESCE(GROUP_CONCAT(DISTINCT TRIM(CONCAT(COALESCE(sp.first_name, ""), " ", COALESCE(sp.last_name, ""))) ORDER BY sp.first_name SEPARATOR ", "), "") as sales_person_names'),
                        DB::raw('MAX(sr.doc_date) as last_return_date'),
                        DB::raw('COUNT(sr.id) as return_count'),
                        DB::raw('SUM(COALESCE(ia.total_taxableamount,0)) as taxable'),
                        DB::raw('SUM(COALESCE(ia.total_vatamount,0)) as tax'),
                        DB::raw('SUM(COALESCE(ia.total_taxableamount,0) + COALESCE(ia.total_vatamount,0)) as amount'),
                        DB::raw('0 as gp'),
                        DB::raw('0 as gp_percent')
                    )
                    ->groupBy('sr.customer', 'ca.account_name')
                    ->when($lastReturnAgeFilterActive, function ($q) use ($lastReturnAgeMinDate, $lastReturnAgeMaxDate) {
                        if ($lastReturnAgeMinDate !== null && $lastReturnAgeMaxDate !== null) {
                            return $q->havingRaw('MAX(sr.doc_date) BETWEEN ? AND ?', [$lastReturnAgeMinDate, $lastReturnAgeMaxDate]);
                        }
                        if ($lastReturnAgeMinDate !== null) {
                            return $q->havingRaw('MAX(sr.doc_date) >= ?', [$lastReturnAgeMinDate]);
                        }
                        if ($lastReturnAgeMaxDate !== null) {
                            return $q->havingRaw('MAX(sr.doc_date) <= ?', [$lastReturnAgeMaxDate]);
                        }
                        return $q;
                    })
                    ->orderBy('group_name')
                    ->get();

                $customerGpData = (clone $baseAggQuery)
                    ->leftJoin('sys_crm_deals as d', 'd.id', '=', 'sr.deal_id')
                    ->select(
                        'sr.customer',
                        'ia.total_taxableamount',
                        'd.deal_currency',
                        'd.deal_value',
                        'd.deal_profit'
                    )
                    ->get();
                $report_rows = $applyGpMetrics($report_rows, $customerGpData, 'customer');

                $indexed = $report_rows->keyBy('customer');
                $report_rows = collect($customer_list)->map(function ($customer) use ($indexed, $scope_company_id, $customerLedgerBalanceMap) {
                    if (isset($indexed[$customer->id])) {
                        return $indexed[$customer->id];
                    }
                    return (object) [
                        'customer' => $customer->id,
                        'company_id' => $scope_company_id != "" ? (int) $scope_company_id : null,
                        'group_name' => $customer->account_name,
                        'sales_person_names' => '',
                        'customer_balance' => isset($customerLedgerBalanceMap[$customer->id]) ? (float) $customerLedgerBalanceMap[$customer->id] : 0,
                        'last_return_date' => null,
                        'return_count' => 0,
                        'taxable' => 0,
                        'tax' => 0,
                        'amount' => 0,
                        'gp' => 0,
                        'gp_percent' => 0,
                    ];
                })->values();

                $report_rows = $report_rows->map(function ($row) use ($customerLedgerBalanceMap, $customerSalesPersonMap) {
                    $row->customer_balance = isset($customerLedgerBalanceMap[$row->customer]) ? (float) $customerLedgerBalanceMap[$row->customer] : 0;
                    $row->sales_person_names = isset($customerSalesPersonMap[$row->customer]) ? (string) $customerSalesPersonMap[$row->customer] : '';
                    return $row;
                });

                if ($lastReturnAgeFilterActive) {
                    $report_rows = $report_rows
                        ->filter(function ($row) {
                            return !empty($row->last_return_date);
                        })
                        ->values();
                }
            } elseif ($report_group === 'sales_person_wise') {
                $report_rows = (clone $baseAggQuery)
                    ->select(
                        'sr.sales_man',
                        DB::raw('MAX(sp.id) as staff_id'),
                        DB::raw('COALESCE(CONCAT(sp.first_name, " ", sp.last_name), "N/A") as group_name'),
                        DB::raw('COUNT(sr.id) as return_count'),
                        DB::raw('SUM(COALESCE(ia.total_taxableamount,0)) as taxable'),
                        DB::raw('SUM(COALESCE(ia.total_vatamount,0)) as tax'),
                        DB::raw('SUM(COALESCE(ia.total_taxableamount,0) + COALESCE(ia.total_vatamount,0)) as amount'),
                        DB::raw('0 as gp'),
                        DB::raw('0 as gp_percent')
                    )
                    ->groupBy('sr.sales_man', 'sp.first_name', 'sp.last_name')
                    ->orderBy('group_name')
                    ->get();

                $salesPersonGpData = (clone $baseAggQuery)
                    ->leftJoin('sys_crm_deals as d', 'd.id', '=', 'sr.deal_id')
                    ->select(
                        'sr.sales_man',
                        'ia.total_taxableamount',
                        'd.deal_currency',
                        'd.deal_value',
                        'd.deal_profit'
                    )
                    ->get();
                $report_rows = $applyGpMetrics($report_rows, $salesPersonGpData, 'sales_man');

                $sales_person_list = collect($sales_person_list)->unique('user_id')->values();
                $nameByUserId = $sales_person_list->pluck('full_name', 'user_id');
                $indexed = $report_rows->filter(function ($row) {
                    return !empty($row->sales_man);
                })->keyBy('sales_man');

                $report_rows = $report_rows->map(function ($row) use ($nameByUserId) {
                    if (!empty($row->sales_man) && isset($nameByUserId[$row->sales_man])) {
                        $row->group_name = $nameByUserId[$row->sales_man];
                    } elseif (empty(trim((string) $row->group_name))) {
                        $row->group_name = 'Unassigned';
                    }
                    return $row;
                });

                $missingSalesPersons = $sales_person_list->filter(function ($salesPerson) use ($indexed) {
                    return !isset($indexed[$salesPerson->user_id]);
                })->map(function ($salesPerson) use ($scope_company_id, $ctrl_company) {
                    return (object) [
                        'sales_man' => $salesPerson->user_id,
                        'staff_id' => $salesPerson->id ?? null,
                        'company_id' => $scope_company_id != "" ? (int) $scope_company_id : ($ctrl_company != "" ? (int) $ctrl_company : null),
                        'group_name' => $salesPerson->full_name,
                        'return_count' => 0,
                        'taxable' => 0,
                        'tax' => 0,
                        'amount' => 0,
                        'gp' => 0,
                        'gp_percent' => 0,
                    ];
                });

                $report_rows = $report_rows
                    ->concat($missingSalesPersons)
                    ->sortBy('group_name')
                    ->values();
            }
        }

        return view('backEnd/salesreturn/reports/sales_return_report_detail', compact(
            'salesreturn',
            'customer_list',
            'sales_person_list',
            'filter_by',
            'ctrl_doc_no',
            'ctrl_deal_id',
            'ctrl_customer',
            'ctrl_amount',
            'ctrl_date',
            'ctrl_date2',
            'ctrl_sales_person',
            'company_list',
            'ctrl_company',
            'ctrl_show_all',
            'report_group',
            'scope_company_id',
            'report_rows',
            'ctrl_from_day',
            'ctrl_to_day'
        ));
    }
}

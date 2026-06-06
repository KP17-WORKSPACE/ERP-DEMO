<?php

namespace App\Http\Controllers;

use App\SmItem;
use App\SmStaff;
use App\SysCompany;
use App\SysSalesInvoice;
use App\SysSalesInvoiceItems;
use App\SysSalesInvoiceAttachment;
use App\SysSalesInvoiceCFCharges;
use App\SmQuotation;
use App\SysCurrencySettings;
use App\SysPaymentTerms;
use App\SysShipping;
use App\SmGeneralSettings;
use App\SmQuotationProducts;
use App\ApiBaseMethod;
use App\SysAppTabs;
use App\SmInspectingDepartment;
use App\SysCustomer;
use App\SysSupplierType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;
//use Barryvdh\DomPDF\PDF;
use Barryvdh\DomPDF\Facade as PDF;


use App\Role;
use App\SysChartofAccounts;
use App\SysChartofAccountsTransaction;
use App\SysCountries;
use App\SysCrmDeals;
use App\SysCrmDealTrack;
use App\SysCrmDealTrackApprovalPurchease;
use App\SysCrmEndUser;
use App\SysCrmQuoteCharges;
use App\SysCrmQuoteItems;
use App\SysCurrency;
use App\SysCustomerType;
use App\SysCustSupDetailAr;
use App\SysCustSuppl;
use App\SysCustSupplAddressbook;
use App\SysDealSalesInvoiceItems;
use App\SysDealSalesInvoiceItemsCart;
use App\SysDeliveryNote;
use App\SysDeliveryNoteItems;
use App\SysHelper;
use App\SysItemStock;
use App\SysLedgerEntries;
use App\SysProformaInvoice;
use App\SysProformaInvoiceItems;
use App\SysPurchaseOrderItemsCart;
use App\SysReceiptAdjustments;
use App\SysSalesInvoiceItemsCart;
use App\SysSalesReturn;
use App\SysSaleType;
use App\SysStates;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

use function GuzzleHttp\Promise\exception_for;


class SysSalesInvoiceReportDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $report_group = $request->get('report_group', 'company_wise');
            $scope_company_id = $request->get('scope_company_id', '');

            $filter_by="this_month";
            $ctrl_date=date('Y-m-01');
            $ctrl_date2=date("Y-m-t", strtotime($ctrl_date));
            $ctrl_doc_no="";
            $ctrl_deal_id="";
            $ctrl_customer="";
            $ctrl_amount="";
            $ctrl_amount_doc_numbers = collect();
            $ctrl_sales_person="";
            $ctrl_company="";
            $ctrl_show_all = 0;
            $ctrl_from_day = '';
            $ctrl_to_day = '';

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            if(session('logged_session_data.company_id')==1){
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
            $company_list = SysCompany::select('id','company_name')->wherein('id',$company_id)->get();

            $paid_doc_numbers = array_flip(
                SysReceiptAdjustments::whereIn('company_id', $company_id)
                    ->whereNotNull('bi_doc_no')
                    ->where('bi_doc_no', '!=', '')
                    ->distinct()
                    ->pluck('bi_doc_no')
                    ->toArray()
            );

            $query = SysSalesInvoice::select(
                DB::raw('sys_sales_invoice.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_sales_invoice_att WHERE siv_id = sys_sales_invoice.id) AS attach, (SELECT max(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesinvoice" and transaction_no=sys_sales_invoice.doc_number and account_id=sys_sales_invoice.customer) AS amount, (SELECT max(code) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS code, (SELECT max(deal_profit) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_profit, (SELECT max(deal_value) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_value, (SELECT max(deal_currency) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_currency'),
                DB::raw('(SELECT SUM(vatamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_vatamount'),
                DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'),
                DB::raw('(SELECT SUM(value) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS value'),
                DB::raw('(SELECT SUM(discount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS discount')
            );

            $query->wherein('company_id',$company_id);
            $query->where('status','!=',2);

            if(SysHelper::get_pagination_post($request)){
                if ($request->from_date != "" && $request->filter_by == "") {
                    $ctrl_date=Carbon::createFromFormat('d/m/Y', $request->from_date)->format('Y-m-d');
                }
                if ($request->to_date != "" && $request->filter_by == "") {
                    $ctrl_date2=Carbon::createFromFormat('d/m/Y', $request->to_date)->format('Y-m-d');
                }
                if ($request->filter_by == "this_month") {
                    $ctrl_date=date('Y-m-01');
                    $ctrl_date2=date("Y-m-t", strtotime($ctrl_date));
                    $filter_by='this_month';
                }
                if ($request->filter_by == "today") {
                    $ctrl_date=date('Y-m-d');
                    $ctrl_date2=date('Y-m-d');
                    $filter_by='today';
                }
                if ($request->filter_by == "this_week") {
                    $ctrl_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                    $ctrl_date2 = date('Y-m-d', strtotime('saturday 23:59:59'));
                    $filter_by='this_week';
                }
                if ($request->filter_by == "last_week") {
                    $ctrl_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                    $ctrl_date2 = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                    $filter_by='last_week';
                }
                if ($request->filter_by == "last_month") {
                    $ctrl_date = date('Y-m-d', strtotime('first day of previous month'));
                    $ctrl_date2 = date('Y-m-d', strtotime('last day of previous month'));
                    $filter_by='last_month';
                }
                if ($request->filter_by == "this_quarter") {
                    $q_date = SysHelper::get_quarter(date('m'));
                    $ctrl_date = $q_date[0];
                    $ctrl_date2 = $q_date[1];
                    $filter_by='this_quarter';
                }
                if ($request->filter_by == "pre_quarter") {
                    $q_date = SysHelper::get_pre_quarter(date('m'));
                    $ctrl_date = $q_date[0];
                    $ctrl_date2 = $q_date[1];
                    $filter_by='pre_quarter';
                }
                if ($request->filter_by == "this_year") {
                    $ctrl_date = date('Y-01-01');
                    $ctrl_date2 = date('Y-12-31');
                    $filter_by='this_year';
                }
                if ($request->filter_by == "last_year") {
                    $ctrl_date = date("Y-01-01",strtotime("-1 year"));
                    $ctrl_date2 = date("Y-12-31",strtotime("-1 year"));
                    $filter_by='last_year';
                }

                if ($request->documents_number != "") {
                    $query->where('doc_number','like','%'.$request->documents_number.'%');
                    $ctrl_doc_no = $request->documents_number;
                }
                if ($request->customer != "") {
                    $query->where('customer',$request->customer);
                    $ctrl_customer = $request->customer;
                }
                if ($request->deal_number != "") {
                    $query->where('deal_id','like','%'.SysHelper::get_dealid_from_code($request->deal_number).'%');
                    $ctrl_deal_id = $request->deal_number;
                }
                if ($request->amount != "") {
                    $amt_nos = SysChartofAccountsTransaction::select('transaction_no', DB::raw('SUM(debit_amount) as total_debit_amount'))
                    ->where('transaction_type', 'salesinvoice')
                    ->groupBy('transaction_no')
                    ->havingRaw('SUM(debit_amount) BETWEEN ? AND ?', [$request->amount, $request->amount])
                    ->pluck('transaction_no');
                    $query->wherein('doc_number',$amt_nos);
                    $ctrl_amount_doc_numbers = $amt_nos;
                    $ctrl_amount = $request->amount;
                }
                if ($ctrl_date != "" && $ctrl_date2 != "") {
                    $query->whereBetween('doc_date', [$ctrl_date, $ctrl_date2]);
                }
                if ($ctrl_date != "" && $ctrl_date2 == "") {
                    $query->where('doc_date',$ctrl_date);
                }
                if ($ctrl_date == "" && $ctrl_date2 != "") {
                    $query->where('doc_date',$ctrl_date2);
                }
                if ($request->sales_person != "") {
                    $query->where('sales_man',$request->sales_person);
                    $ctrl_sales_person = $request->sales_person;
                }
                if ($request->company != "") {
                    $query->where('company_id',$request->company);
                    $ctrl_company = $request->company;
                }
                if ($request->show_all == "1") {
                    $ctrl_show_all = 1;
                }

                // Customer wise only: filter by age (days) of the customer's last invoice
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

            $query->orderby('doc_number','desc');

            $salesinvoice = null;
            $report_rows = collect();

            if ($report_group === 'date_wise') {
                if ($ctrl_show_all == 1) {
                    $salesinvoice = $query->get();
                } else {
                    $salesinvoice = $query->paginate(150);
                }
            } else {
                $itemsAggSub = DB::table('sys_sales_invoice_items')
                    ->select(
                        'si_id',
                        DB::raw('SUM(value) as value'),
                        DB::raw('SUM(discount) as discount'),
                        DB::raw('SUM(taxableamount) as total_taxableamount'),
                        DB::raw('SUM(vatamount) as total_vatamount')
                    )
                    ->groupBy('si_id');

                $applyGpMetrics = function ($rows, $gpData, $keyField) {
                    $gpMap = [];
                    $taxableMap = [];

                    foreach ($gpData as $item) {
                        $key = $item->{$keyField};
                        if ($key === null || $key === '') {
                            continue;
                        }

                        $taxable = (float) $item->total_taxableamount - (float) $item->deal_discount;
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

                $lastInvoiceAgeFilterActive = false;
                $lastInvoiceAgeMinDate = null; // YYYY-MM-DD
                $lastInvoiceAgeMaxDate = null; // YYYY-MM-DD

                if ($report_group === 'customer_wise') {
                    $fromDays = (is_numeric($ctrl_from_day) && $ctrl_from_day !== '') ? (int) $ctrl_from_day : null;
                    $toDays = (is_numeric($ctrl_to_day) && $ctrl_to_day !== '') ? (int) $ctrl_to_day : null;

                    if ($fromDays !== null || $toDays !== null) {
                        $lastInvoiceAgeFilterActive = true;
                        $today = Carbon::today();

                        // Normalize when both values are provided
                        if ($fromDays !== null && $toDays !== null && $fromDays > $toDays) {
                            $tmp = $fromDays;
                            $fromDays = $toDays;
                            $toDays = $tmp;
                        }

                        if ($fromDays !== null && $toDays !== null) {
                            // From/To meaning: age between From and To days (inclusive)
                            $lastInvoiceAgeMinDate = $today->copy()->subDays($toDays)->format('Y-m-d');
                            $lastInvoiceAgeMaxDate = $today->copy()->subDays($fromDays)->format('Y-m-d');
                        } elseif ($fromDays !== null) {
                            // Only "from": show customers whose last invoice is older than/at From days
                            $lastInvoiceAgeMaxDate = $today->copy()->subDays($fromDays)->format('Y-m-d');
                        } else {
                            // Only "to": show customers whose last invoice is not older than To days
                            $lastInvoiceAgeMinDate = $today->copy()->subDays($toDays)->format('Y-m-d');
                        }
                    }
                }

                $ledgerBalanceAsOnDate = $ctrl_date2 != "" ? $ctrl_date2 : ($ctrl_date != "" ? $ctrl_date : date('Y-m-d'));
                // Keep ledger balance company scope aligned with General Ledger report behavior.
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
                    ->whereDate('cat.transaction_date', '<=', $ledgerBalanceAsOnDate);
                $ledgerBalanceSub->groupBy('cat.account_id');
                $customerLedgerBalanceMap = (clone $ledgerBalanceSub)
                    ->whereIn('cat.account_id', collect($customer_list)->pluck('id')->toArray())
                    ->pluck('total_balance', 'account_id');
                $customerIds = collect($customer_list)->pluck('id')->toArray();
                $customerSalesPersonMap = DB::table('sys_chartofaccounts as ca')
                    ->join('sys_cust_suppl as cs', 'cs.code', '=', 'ca.account_code')
                    ->leftJoin('sys_cust_suppl_assign as csa', function ($join) {
                        $join->on('csa.cust_supp_id', '=', 'cs.id')
                            ->where('csa.type', 1); // customer assignment
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

                $baseAggQuery = DB::table('sys_sales_invoice as si')
                    ->leftJoinSub($itemsAggSub, 'ia', function ($join) {
                        $join->on('ia.si_id', '=', 'si.id');
                    })
                    ->leftJoin('sys_crm_deals as d', 'd.id', '=', 'si.deal_id')
                    ->leftJoin('sys_company as c', 'c.id', '=', 'si.company_id')
                    ->leftJoin('sys_chartofaccounts as ca', 'ca.id', '=', 'si.customer')
                    ->leftJoin('sm_staffs as sp', 'sp.user_id', '=', 'si.sales_man')
                    ->whereIn('si.company_id', $company_id)
                    ->where('si.status', '!=', 2);

                if ($ctrl_doc_no != "") {
                    $baseAggQuery->where('si.doc_number', 'like', '%' . $ctrl_doc_no . '%');
                }
                if ($ctrl_customer != "") {
                    $baseAggQuery->where('si.customer', $ctrl_customer);
                }
                if ($ctrl_deal_id != "") {
                    $baseAggQuery->where('si.deal_id', 'like', '%' . SysHelper::get_dealid_from_code($ctrl_deal_id) . '%');
                }
                if ($ctrl_amount != "") {
                    $baseAggQuery->whereIn('si.doc_number', $ctrl_amount_doc_numbers);
                }
                // When Customer Wise "Last Invoice age (days)" filter is active, we ignore the normal doc_date range
                // because the report should be filtered by the age of MAX(doc_date) per customer.
                if (!$lastInvoiceAgeFilterActive) {
                    if ($ctrl_date != "" && $ctrl_date2 != "") {
                        $baseAggQuery->whereBetween('si.doc_date', [$ctrl_date, $ctrl_date2]);
                    }
                    if ($ctrl_date != "" && $ctrl_date2 == "") {
                        $baseAggQuery->whereDate('si.doc_date', $ctrl_date);
                    }
                    if ($ctrl_date == "" && $ctrl_date2 != "") {
                        $baseAggQuery->whereDate('si.doc_date', $ctrl_date2);
                    }
                }
                if ($ctrl_sales_person != "") {
                    $baseAggQuery->where('si.sales_man', $ctrl_sales_person);
                }
                if ($ctrl_company != "") {
                    $baseAggQuery->where('si.company_id', $ctrl_company);
                }
                if ($scope_company_id != "") {
                    $baseAggQuery->where('si.company_id', $scope_company_id);
                }

                if ($report_group === 'company_wise') {
                    $report_rows = (clone $baseAggQuery)
                        ->select(
                            'si.company_id',
                            DB::raw('COALESCE(c.company_name, "N/A") as group_name'),
                            DB::raw('COUNT(si.id) as invoice_count'),
                            DB::raw('SUM(COALESCE(ia.value,0)) as value'),
                            DB::raw('SUM(COALESCE(ia.discount,0) + COALESCE(si.deal_discount,0)) as discount'),
                            DB::raw('SUM(COALESCE(ia.total_taxableamount,0) - COALESCE(si.deal_discount,0)) as taxable'),
                            DB::raw('SUM(COALESCE(ia.total_vatamount,0)) as tax'),
                            DB::raw('SUM((COALESCE(ia.total_taxableamount,0) - COALESCE(si.deal_discount,0)) + COALESCE(ia.total_vatamount,0)) as amount'),
                            DB::raw('0 as gp'),
                            DB::raw('0 as gp_percent')
                        )
                        ->groupBy('si.company_id', 'c.company_name')
                        ->orderBy('group_name')
                        ->get();

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
                            'invoice_count' => 0,
                            'value' => 0,
                            'discount' => 0,
                            'taxable' => 0,
                            'tax' => 0,
                            'amount' => 0,
                            'gp' => 0,
                            'gp_percent' => 0,
                        ];
                    })->values();

                    $companyGpData = (clone $baseAggQuery)->select(
                        'si.company_id',
                        'ia.total_taxableamount',
                        'si.deal_discount',
                        'd.deal_currency',
                        'd.deal_value',
                        'd.deal_profit'
                    )->get();
                    $report_rows = $applyGpMetrics($report_rows, $companyGpData, 'company_id');
                } elseif ($report_group === 'customer_wise') {
                    $report_rows = (clone $baseAggQuery)
                        ->select(
                            'si.customer',
                            DB::raw('COALESCE(ca.account_name, "N/A") as group_name'),
                            DB::raw('COALESCE(GROUP_CONCAT(DISTINCT TRIM(CONCAT(COALESCE(sp.first_name, ""), " ", COALESCE(sp.last_name, ""))) ORDER BY sp.first_name SEPARATOR ", "), "") as sales_person_names'),
                            DB::raw('MAX(si.doc_date) as last_invoice_date'),
                            DB::raw('COUNT(si.id) as invoice_count'),
                            DB::raw('SUM(COALESCE(ia.value,0)) as value'),
                            DB::raw('SUM(COALESCE(ia.discount,0) + COALESCE(si.deal_discount,0)) as discount'),
                            DB::raw('SUM(COALESCE(ia.total_taxableamount,0) - COALESCE(si.deal_discount,0)) as taxable'),
                            DB::raw('SUM(COALESCE(ia.total_vatamount,0)) as tax'),
                            DB::raw('SUM((COALESCE(ia.total_taxableamount,0) - COALESCE(si.deal_discount,0)) + COALESCE(ia.total_vatamount,0)) as amount'),
                            DB::raw('0 as gp'),
                            DB::raw('0 as gp_percent')
                        )
                        ->groupBy('si.customer', 'ca.account_name')
                        ->when($lastInvoiceAgeFilterActive, function ($q) use ($lastInvoiceAgeMinDate, $lastInvoiceAgeMaxDate) {
                            if ($lastInvoiceAgeMinDate !== null && $lastInvoiceAgeMaxDate !== null) {
                                return $q->havingRaw('MAX(si.doc_date) BETWEEN ? AND ?', [$lastInvoiceAgeMinDate, $lastInvoiceAgeMaxDate]);
                            }
                            if ($lastInvoiceAgeMinDate !== null) {
                                return $q->havingRaw('MAX(si.doc_date) >= ?', [$lastInvoiceAgeMinDate]);
                            }
                            if ($lastInvoiceAgeMaxDate !== null) {
                                return $q->havingRaw('MAX(si.doc_date) <= ?', [$lastInvoiceAgeMaxDate]);
                            }
                            return $q;
                        })
                        ->orderBy('group_name')
                        ->get();

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
                            'last_invoice_date' => null,
                            'invoice_count' => 0,
                            'value' => 0,
                            'discount' => 0,
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

                    // If the age filter is active, exclude customers without invoices (no last_invoice_date).
                    if ($lastInvoiceAgeFilterActive) {
                        $report_rows = $report_rows
                            ->filter(function ($row) {
                                return !empty($row->last_invoice_date);
                            })
                            ->values();
                    }

                    $customerGpData = (clone $baseAggQuery)->select(
                        'si.customer',
                        'ia.total_taxableamount',
                        'si.deal_discount',
                        'd.deal_currency',
                        'd.deal_value',
                        'd.deal_profit'
                    )->get();
                    $report_rows = $applyGpMetrics($report_rows, $customerGpData, 'customer');
                } elseif ($report_group === 'sales_person_wise') {
                    $report_rows = (clone $baseAggQuery)
                        ->select(
                            'si.sales_man',
                            DB::raw('MAX(sp.id) as staff_id'),
                            DB::raw('COALESCE(CONCAT(sp.first_name, " ", sp.last_name), "N/A") as group_name'),
                            DB::raw('COUNT(si.id) as invoice_count'),
                            DB::raw('SUM(COALESCE(ia.value,0)) as value'),
                            DB::raw('SUM(COALESCE(ia.discount,0) + COALESCE(si.deal_discount,0)) as discount'),
                            DB::raw('SUM(COALESCE(ia.total_taxableamount,0) - COALESCE(si.deal_discount,0)) as taxable'),
                            DB::raw('SUM(COALESCE(ia.total_vatamount,0)) as tax'),
                            DB::raw('SUM((COALESCE(ia.total_taxableamount,0) - COALESCE(si.deal_discount,0)) + COALESCE(ia.total_vatamount,0)) as amount'),
                            DB::raw('0 as gp'),
                            DB::raw('0 as gp_percent')
                        )
                        ->groupBy('si.sales_man', 'sp.first_name', 'sp.last_name')
                        ->orderBy('group_name')
                        ->get();

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
                            'invoice_count' => 0,
                            'value' => 0,
                            'discount' => 0,
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

                    $salesPersonGpData = (clone $baseAggQuery)->select(
                        'si.sales_man',
                        'ia.total_taxableamount',
                        'si.deal_discount',
                        'd.deal_currency',
                        'd.deal_value',
                        'd.deal_profit'
                    )->get();
                    $report_rows = $applyGpMetrics($report_rows, $salesPersonGpData, 'sales_man');
                }
            }

            return view('backEnd/salesinvoice/reports/sales_invoice_report_detail', compact(
                'salesinvoice','customer_list','paid_doc_numbers','sales_person_list','filter_by',
                'ctrl_doc_no','ctrl_deal_id','ctrl_customer','ctrl_amount','ctrl_date','ctrl_date2',
                'ctrl_sales_person','company_list','ctrl_company','ctrl_show_all','report_group','scope_company_id','report_rows',
                'ctrl_from_day','ctrl_to_day'
            ));
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }


     public function salesreportcompany(Request $request)
    {
        try {
            if (session('logged_session_data.company_id') != 1) {
                return redirect('crm-deals-sales-report'); // Redirect if company_id is not 1
            }
            $ctrl_owner = '';
            $filter_by = '';
            $ctrl_company = session('logged_session_data.company_id');
            $ctrl_company_list = [];
            $ctrl_date = date('Y-m-01');
            $ctrl_date2 = date("Y-m-t", strtotime($ctrl_date));
            $ps_amc = "";
            $ps_amc_deal_id = [];

            $company = SysCompany::select('id', 'company_name')->orderby('sort_id', 'asc')->get();
            if (Auth::user()->id == 36) {
                $company = SysCompany::select('id', 'company_name')->where('id', 5)->orderby('sort_id', 'asc')->get();
            }

            if ($_POST) {
                if ($request->company_id != 1) {
                    //$query->where('company_id', $request->company_id);
                    $ctrl_company_list = [$request->company_id];
                    $ctrl_company = $request->company_id;
                } else {
                    if ($ctrl_company == 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)) {
                        $ctrl_company_list = SysCompany::pluck('id');
                    }
                }

                if ($request->date != "" && $request->filter_by == "") {
                    $ctrl_date = SysHelper::normalizeToYmd($request->date);
                }
                if ($request->date != "" && $request->filter_by == "") {
                    $ctrl_date = SysHelper::normalizeToYmd($request->date);
                    if ($request->date2 != "") {
                        $ctrl_date2 = SysHelper::normalizeToYmd($request->date2);
                    }
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
                if ($request->ps_amc == "ps") {
                    $ps_amc = "ps";
                    $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
                        ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                        ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
                        ->wherein('sys_crm_quote_items.product_id', [26328, 35710, 36223])
                        ->where('sys_crm_deals.stage', 4)->pluck('sys_crm_deals.id');
                }
                if ($request->ps_amc == "amc") {
                    $ps_amc = "amc";
                    $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
                        ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                        ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
                        ->wherein('sys_crm_quote_items.product_id', [35657, 35716, 37892])
                        ->where('sys_crm_deals.stage', 4)->pluck('sys_crm_deals.id');
                }
                if ($request->ps_amc == "ps_amc") {
                    $ps_amc = "ps_amc";
                    $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
                        ->leftjoin('sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_invoice.deal_id', 'sys_crm_deals.id')
                        ->join('sys_crm_quote_items', 'sys_crm_quote_items.deal_id', 'sys_crm_deals.id')
                        ->wherein('sys_crm_quote_items.product_id', [26328, 35710, 36223, 35657, 35716, 37892])
                        ->where('sys_crm_deals.stage', 4)->pluck('sys_crm_deals.id');
                }
            } else {

                if ($ctrl_company == 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)) {
                    $ctrl_company_list = SysCompany::pluck('id');
                } else {
                    $ctrl_company_list = [$ctrl_company];
                }
            }

            if (count($company) > 0) {
                foreach ($company as $value) {
                    $company = $value->id;
                    $revenue = SysHelper::get_total_revenue_all_by_company($ctrl_date, $ctrl_date2, $company, $ps_amc_deal_id);
                    if ($value->combind_user_id == "") {
                        $target = SysHelper::calculateSalesTarget([0], $ctrl_date, $ctrl_date2, $filter_by, $company);
                    } else {
                        $target = SysHelper::calculateSalesTarget([0], $ctrl_date, $ctrl_date2, $filter_by, $company);
                    }
                    //return SysHelper::calculateSalesTarget([5800], '2025-01-01', '2025-02-01');

                    if ($target['rev_amount'] == 0) {
                        $tp = 0;
                    } else {
                        $tp1 = ($revenue[0] / $target['rev_amount']) * 100;
                        $tp2 = ($revenue[1] / $target['gp_amount']) * 100;
                        $tp = round(($tp1 + $tp2) / 2, 2);
                    }
                    $receivable = \App\Http\Controllers\SysCrmReportController::get_receivable_os_report_summary($company);
                    $outstanding_balance = isset($receivable[0][0]) ? $receivable[0][0] : 0;

                    $arrayVariable = [
                        'company_id' => $value->id,
                        'full_name' => $value->company_name,
                        'role_id' => 1,
                        'combind_user_id' => 0,
                        'revenue' => $revenue,
                        'forcast' => SysHelper::get_total_forcast_all_by_company($ctrl_date, $ctrl_date2, $company, $ps_amc_deal_id),
                        'revenue_actual' => SysHelper::get_total_revenue_actual_all_by_company($ctrl_date, $ctrl_date2, $company, $ps_amc_deal_id),
                        'on_process' => SysHelper::get_total_on_process_all_by_company($ctrl_date, $ctrl_date2, $company, $ps_amc_deal_id),
                        'target' => $target,
                        'dealcount' => SysHelper::get_deal_count_by_company($ctrl_date, $ctrl_date2, $company, $ps_amc_deal_id),
                        'tp' => $tp,
                        'outstanding_balance' => $outstanding_balance,
                    ];
                    $data[] = $arrayVariable;
                }
                $data = collect($data);
                //$data = $data->sortByDesc('tp');
                $rev_sum = SysHelper::get_total_revenue_all_by_company_sum($ctrl_date, $ctrl_date2, $company, $ps_amc_deal_id);
            } else {
                $data = '0';
                $rev_sum = [0.00, 0.00, 0.00];
            }

            $form_data = [
                'data' => $data,
                'ctrl_company' => $ctrl_company,
                'ctrl_date' => $ctrl_date,
                'ctrl_date2' => $ctrl_date2,
                'filter_by' => $filter_by,
                'rev_sum' => $rev_sum,
            ];

            return view('backEnd/salesinvoice/reports/SalesReportCompany', compact('data', 'ctrl_date', 'ctrl_date2', 'filter_by', 'ps_amc', 'company', 'rev_sum'));

            session()->put('sale_report_list_query', $form_data);
            return redirect('crm-deals-sales-reports');
            //return $data;

        } catch (\Throwable $th) {
            return $th;
        }
    }


    public function salesreport(Request $request,$cid=null, $m1=null, $m2=null)
    {
        try {
            
            
            $ctrl_owner='';
            $filter_by='';
            $ctrl_company=session('logged_session_data.company_id');
            $ctrl_company_list=[];
            $ctrl_date=date('Y-m-01');
            $ctrl_date2=date("Y-m-t", strtotime($ctrl_date));
            $ps_amc="";
            $ps_amc_deal_id=[];

            if($cid != null && $m1 != null && $m2 != null){                
                $ctrl_company = $cid;
                $ctrl_date=$m1;
                $ctrl_date2=$m2;
            }
            
            $company = SysCompany::select('id','company_name')->orderby('sort_id','asc')->get();
            if(Auth::user()->id == 36){
                $company = SysCompany::select('id','company_name')->where('id',5)->orderby('sort_id','asc')->get();
            }
            
            if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35){
                if($ctrl_company == 1){
                    $staff = SmStaff::select('user_id','full_name')->where('active_status',1)->orderby('full_name','asc')->get();
                }
                else{
                    $staff = SmStaff::select('user_id','full_name')
                    ->whereRaw("find_in_set($ctrl_company,company_access)")
                    //->where('main_company',$ctrl_company)
                    ->where('active_status',1)->orderby('full_name','asc')->get();
                }
            }
            else{
                $staff = SmStaff::select('user_id','full_name')->where('user_id',Auth::user()->id)->get();
            }

            $query = SmStaff::select('user_id','full_name','role_id','combind_user_id');
            
            if(isset($request->company_id)){
                if($request->company_id != 1){
                    $query->where('main_company',$request->company_id);
                    //$query->whereRaw("find_in_set($request->company_id,company_access)");    
                }
            }
            else{
                if($ctrl_company!=1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)){
                    //$query->where('main_company',$ctrl_company);
                    $query->whereRaw("find_in_set($ctrl_company,company_access)");
                    //$query->whereRaw("find_in_set($ctrl_company,company_access)");

                }
            }

            // if(Auth::user()->id == 36){ //Jacob George
            //     $query->wherein('user_id',[36,25,51,31,27]);
            // }
            // if(Auth::user()->id == 58) { //Thaiab Mohammed
            //     $query->wherein('user_id',[58,59,60,62]);
            // }
            // if(Auth::user()->id == 18) { //Prajeesh Prabhakar
            //     $query->wherein('user_id',[18,20,19]);
            // }
            // if(Auth::user()->id == 48) { //Parveen Sheik Asif
            //     $query->wherein('user_id',[48,39,71]);
            // }

            //on 27/03/2025 commented mismatch issue in total
            //$query->wherein('role_id', [1,2,5,8,32]);

            //$query->wherenotin('user_id', [51,28]);
            
            //->where('active_status',1);
            //$query->wherenotin('user_id', [48,82,21,49,71,28,55,56,35,80,75,30,18,61,3,72,37,78,4,60,73,22,51,31,23,1,59,89,76,24,102]);
            if($_POST){
                if ($request->company_id != 1) {
                    //$query->where('company_id', $request->company_id);
                    $ctrl_company_list=[$request->company_id];
                    $ctrl_company = $request->company_id;
                } else {
                    if($ctrl_company == 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)){
                        $ctrl_company_list= SysCompany::pluck('id');
                    }
                }
                if ($request->owner_id != "") {
                    $query->where('user_id', $request->owner_id);
                    $ctrl_owner=$request->owner_id;
                }
                if ($request->date != "" && $request->filter_by == "") {
                    $ctrl_date= SysHelper::normalizeToYmd($request->date);
                }
                if ($request->date != "" && $request->filter_by == "") {
                    $ctrl_date= SysHelper::normalizeToYmd($request->date);
                    if ($request->date2 != "") {
                        $ctrl_date2= SysHelper::normalizeToYmd($request->date2);
                    }
                }
                if ($request->filter_by == "this_month") {
                    $ctrl_date=date('Y-m-01');
                    $ctrl_date2=date("Y-m-t", strtotime($ctrl_date));
                    $filter_by='this_month';               
                }
                if ($request->filter_by == "today") {
                    $ctrl_date=date('Y-m-d');
                    $ctrl_date2=date('Y-m-d');
                    $filter_by='today';
                }
                if ($request->filter_by == "this_week") {
                    $ctrl_date = date('Y-m-d', strtotime('-1 week sunday 00:00:00'));
                    $ctrl_date2 = date('Y-m-d', strtotime('saturday 23:59:59'));
                    $filter_by='this_week';
                }
                if ($request->filter_by == "last_week") {
                    $ctrl_date = date('Y-m-d', strtotime('-2 week sunday 00:00:00'));
                    $ctrl_date2 = date('Y-m-d', strtotime('-1 week saturday 23:59:59'));
                    $filter_by='last_week';
                }
                if ($request->filter_by == "last_month") {
                    $ctrl_date = date('Y-m-d', strtotime('first day of previous month'));
                    $ctrl_date2 = date('Y-m-d', strtotime('last day of previous month'));
                    $filter_by='last_month';
                }
                if ($request->filter_by == "this_quarter") {
                    $q_date = SysHelper::get_quarter(date('m'));
                    $ctrl_date = $q_date[0];
                    $ctrl_date2 = $q_date[1];
                    $filter_by='this_quarter';
                }
                if ($request->filter_by == "pre_quarter") {
                    $q_date = SysHelper::get_pre_quarter(date('m'));
                    $ctrl_date = $q_date[0];
                    $ctrl_date2 = $q_date[1];
                    $filter_by='pre_quarter';
                }
                if ($request->filter_by == "this_year") {
                    $ctrl_date = date('Y-01-01');
                    $ctrl_date2 = date('Y-12-31');
                    $filter_by='this_year';
                }
                if ($request->filter_by == "last_year") {
                    $ctrl_date = date("Y-01-01",strtotime("-1 year"));
                    $ctrl_date2 = date("Y-12-31",strtotime("-1 year"));
                    $filter_by='last_year';
                }
                
                if($request->ps_amc == "ps")
                {
                    $ps_amc="ps";
                    $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
                    ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id',[26328,35710,36223])
                    ->where('sys_crm_deals.stage',4)->pluck('sys_crm_deals.id');
                }
                if($request->ps_amc == "amc")
                {
                    $ps_amc="amc";
                    $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
                    ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id',[35657,35716,37892])
                    ->where('sys_crm_deals.stage',4)->pluck('sys_crm_deals.id');
                }
                if($request->ps_amc == "ps_amc")
                {
                    $ps_amc="ps_amc";
                    $ps_amc_deal_id = DB::table('sys_crm_deals')->select('sys_crm_deals.id')
                    ->leftjoin('sys_crm_deal_track_approval_invoice','sys_crm_deal_track_approval_invoice.deal_id','sys_crm_deals.id')
                    ->join('sys_crm_quote_items','sys_crm_quote_items.deal_id','sys_crm_deals.id')
                    ->wherein('sys_crm_quote_items.product_id',[26328,35710,36223,35657,35716,37892])
                    ->where('sys_crm_deals.stage',4)->pluck('sys_crm_deals.id');
                }

                $query->where(function($q) use ($ctrl_date2) {
                    $q->whereRaw("DATE_FORMAT(date_of_resign, '%Y-%m-%d') > ?", [date('Y-m-d', strtotime($ctrl_date2))])
                      ->orWhereNull('date_of_resign');
                });

                if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2){
                        $query->where('user_id', Auth::user()->id);
                }
                $deals = $query->orderby('full_name','asc')->get();
            }
            else{

                if($ctrl_company == 1 && (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 35)){
                    $ctrl_company_list= SysCompany::pluck('id');
                } else{$ctrl_company_list= [$ctrl_company];}


                //$query->where('company_id', $ctrl_company);
                if(Auth::user()->role_id != 1 && Auth::user()->role_id != 2 && Auth::user()->role_id != 8 && Auth::user()->role_id != 35){
                    $query->where('user_id', Auth::user()->id);
                }
                if(Auth::user()->role_id == 8){
                    $teams = DB::table('users')->wherein('role_id',[5,33])->pluck('id');
                    $query->wherein('user_id', $teams);
                }
                
                $query->where(function($q) use ($ctrl_date2) {
                    $q->whereRaw("DATE_FORMAT(date_of_resign, '%Y-%m-%d') > ?", [date('Y-m-d', strtotime($ctrl_date2))])
                      ->orWhereNull('date_of_resign');
                });
                $deals = $query->orderby('full_name','asc')->get();
            }
            
            if(count($deals)>0){
                foreach ($deals as $value) {
                    $user=[$value->user_id];
                    $revenue = SysHelper::get_total_revenue_all_by_user($user,$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id);
                    //return $revenue;
                    
                    /*$target = SysCrmSalesTarget::select(db::raw('sum(target) as amount'),db::raw('max(type) as type'))->where('user_id',$value->user_id)
                    ->whereRaw("DATE_FORMAT(target_month, '%Y-%m') >= '".date('Y-m', strtotime($ctrl_date))."' and DATE_FORMAT(target_month, '%Y-%m') <= '".date('Y-m', strtotime($ctrl_date2))."'")->first();*/
                    if($value->combind_user_id==""){
                        //$target = SysCrmSalesTarget::select(db::raw('COALESCE(sum(revenue_target_monthly), 0) as rev_amount'),
                        //db::raw('COALESCE(sum(gp_target_monthly), 0) as gp_amount'))->where('user_id',$value->user_id)
                        //->whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= '".date('Y-m', strtotime($ctrl_date))."'")->orderby('target_month_from','desc')->first();
                        $target = SysHelper::calculateSalesTarget([$value->user_id], $ctrl_date, $ctrl_date2,$filter_by,$ctrl_company);
                    } else {
                        $userIds = array_map('intval', explode(',', $value->combind_user_id));
                        /*$target = SysCrmSalesTarget::select(
                            DB::raw('COALESCE(sum(revenue_target_monthly), 0) as rev_amount'),
                            DB::raw('COALESCE(sum(gp_target_monthly), 0) as gp_amount')
                        )
                        ->wherein("user_id", $userIds)
                        ->whereRaw("DATE_FORMAT(target_month_from, '%Y-%m') <= ?", [date('Y-m', strtotime($ctrl_date))]) // Using parameter binding for safety
                        ->orderBy('target_month_from', 'desc')
                        ->first();*/
                        $target = SysHelper::calculateSalesTarget($userIds, $ctrl_date, $ctrl_date2,$filter_by,$ctrl_company);
                    }

                    //return SysHelper::calculateSalesTarget([5800], '2025-01-01', '2025-02-01');

                    if($target['rev_amount']==0){
                        $tp = 0;
                    } else{
                            $tp1 = ($revenue[0] / $target['rev_amount']) * 100;
                            $tp2 = ($revenue[1] / $target['gp_amount']) * 100;
                            $tp = round(($tp1+$tp2)/2,2);
                    }
                    $receivable = \App\Http\Controllers\SysCrmReportController::get_receivable_os_report_dashboard($value->user_id, $ctrl_company);
                    $outstanding_balance = isset($receivable[0][0]) ? $receivable[0][0] : 0;

                    $arrayVariable = [
                        'user_id'  => $value->user_id,
                        'full_name' => $value->full_name,
                        'role_id' => $value->role_id,
                        'combind_user_id' => $value->combind_user_id,
                        'revenue' => $revenue,
                        'revenue_nocombind' => SysHelper::get_total_revenue_all_by_user_nocombind($user,$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id),
                        'forcast' => SysHelper::get_total_forcast_all_by_user($user,$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id),
                        'revenue_actual' => SysHelper::get_total_revenue_actual_all_by_user($user,$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id),
                        'on_process' => SysHelper::get_total_on_process_all_by_user($user,$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id),
                        'target' => $target,
                        'dealcount' => SysHelper::get_deal_count_by_user($user,$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id),
                        'tp' => $tp,
                        'outstanding_balance' => $outstanding_balance,
                    ];
                    $data[]=$arrayVariable;
                }
                $data =  collect($data)->unique('user_id')->values();
                $data = $data->sortByDesc('tp');
                $rev_sum = SysHelper::get_total_revenue_all_by_user_sum($deals->pluck('user_id'),$ctrl_date,$ctrl_date2,$ctrl_company_list,$ps_amc_deal_id);
            }
            else{
                $data='0';
                $rev_sum=[0.00,0.00,0.00];
            }
            
            $form_data = [
                'data' => $data,
                'staff' => $staff,
                'ctrl_owner' => $ctrl_owner,
                'ctrl_company' => $ctrl_company,
                'ctrl_date' => $ctrl_date,
                'ctrl_date2' => $ctrl_date2,
                'filter_by' => $filter_by,
                'rev_sum' => $rev_sum,
            ];

   
            return view('backEnd.salesinvoice.reports.SalesReportSalesPerson', compact('data','staff','ctrl_owner','ctrl_company','ctrl_date','ctrl_date2','filter_by','company','rev_sum','ps_amc'));

            session()->put('sale_report_list_query', $form_data);
            return redirect('crm-deals-sales-reports');
            //return $data;

        } catch (\Throwable $th) {
            return $th;
        }
    }







}
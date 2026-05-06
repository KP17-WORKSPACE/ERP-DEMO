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


class SysSalesInvoiceReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        try{
            $filter_by="";
            $ctrl_date="";
            $ctrl_date2="";
            $ctrl_doc_no="";
            $ctrl_deal_id="";
            $ctrl_customer="";
            $ctrl_amount="";
            $ctrl_sales_person="";
            $ctrl_company="";
            

            $r = SysHelper::get_data_by_role();
            $company_id = $r[0];
            if(session('logged_session_data.company_id')==1){
                $company_id = SysCompany::pluck('id');
            }
            $customer_list = SysHelper::get_customer_list($company_id);
            $sales_person_list = SysHelper::get_sales_persons2();

            $company_list = SysCompany::select('id','company_name')->wherein('id',$company_id)->get();
            
            $adj_list = SysReceiptAdjustments::select('bi_doc_number','bi_doc_no','bi_total','bi_paid','bi_balance','bi_amount')->wherein('company_id',$company_id)->get();
            
            $query = SysSalesInvoice::select(DB::raw('sys_sales_invoice.*, (SELECT GROUP_CONCAT(doc_file) FROM sys_sales_invoice_att WHERE siv_id = sys_sales_invoice.id) AS attach, (SELECT max(debit_amount) FROM sys_chartofaccounts_transaction WHERE transaction_type="salesinvoice" and transaction_no=sys_sales_invoice.doc_number and account_id=sys_sales_invoice.customer) AS amount, (SELECT max(code) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS code, (SELECT max(deal_profit) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_profit, (SELECT max(deal_value) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_value, (SELECT max(deal_currency) FROM sys_crm_deals WHERE id=sys_sales_invoice.deal_id) AS deal_currency'),DB::raw('(SELECT SUM(vatamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_vatamount'),DB::raw('(SELECT SUM(taxableamount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS total_taxableamount'),DB::raw('(SELECT SUM(value) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS value'),DB::raw('(SELECT SUM(discount) FROM sys_sales_invoice_items WHERE si_id = sys_sales_invoice.id) AS discount'));

            $query->wherein('company_id',$company_id);

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
                    //$amt_nos = SysChartofAccountsTransaction::where('transaction_type', 'salesinvoice')->whereBetween('debit_amount',[$request->amount, $request->amount])->pluck('transaction_no');
                    $amt_nos = SysChartofAccountsTransaction::select('transaction_no', DB::raw('SUM(debit_amount) as total_debit_amount'))
                    ->where('transaction_type', 'salesinvoice')
                    ->groupBy('transaction_no')
                    ->havingRaw('SUM(debit_amount) BETWEEN ? AND ?', [$request->amount, $request->amount])  // Filter by the sum
                    ->pluck('transaction_no');
                    $query->wherein('doc_number',$amt_nos);
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
                
            }
            else{
                
            }
            $query->orderby('doc_number','desc');
            $salesinvoice = $query->get();
            //return $salesinvoice;
            return view('backEnd/salesinvoice/sales_invoice_report', compact('salesinvoice','customer_list','adj_list','sales_person_list','filter_by','ctrl_doc_no','ctrl_deal_id','ctrl_customer','ctrl_amount','ctrl_date','ctrl_date2','ctrl_sales_person','company_list','ctrl_company'));
            
        }catch (\Exception $e) {
            return $e;
           Toastr::error('Operation Failed', 'Failed');
           return redirect()->back(); 
        }
    }

}
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
use Illuminate\Support\Facades\File;

use setasign\Fpdi\Fpdi;

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
use App\SysCurrencyRate;
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
use App\SysReceipt;
use App\SysReceiptAdjustments;
use App\SysSalesInvoiceItemsCart;
use App\SysSalesReturn;
use App\SysSalesReturnAdjestment;
use App\SysSaleType;
use App\SysStates;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use function GuzzleHttp\Promise\exception_for;

use App\SysAccountGroupSub;
use App\SysAccountGroupSub2;
use App\SysAccountGroup;


class SysDataDeleteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $siv_id = null)
    {
        try {
            if (Auth::user()->role_id != 1) {
                Toastr::error('You do not have permission to access this page', 'Failed');
                return redirect()->back();
            }

            $backupPath = storage_path('backups');
            $folders = File::directories($backupPath);
            $backup_folders = array_map(function ($path) {
                return basename($path);
            }, $folders);

            if (session('logged_session_data.company_id') == 1) {
                $company_list = SysCompany::wherenotin('id', [1])->orderby('id', 'asc')->get();
            } else {
                $company_list = SysCompany::where('id', session('logged_session_data.company_id'))->orderby('id', 'asc')->get();
            }

            $databaseName = env('DB_DATABASE') ?: DB::connection()->getDatabaseName();
            $tableRecords = [];

            if ($databaseName) {
                try {
                    $tableRows = DB::select('SHOW TABLES');
                    $tableNames = collect($tableRows)->map(function ($row) {
                        $row = (array) $row;
                        return array_values($row)[0] ?? null;
                    })->filter()->values()->toArray();

                    foreach ($tableNames as $tableName) {
                        try {
                            $count = DB::table($tableName)->count();
                        } catch (\Throwable $e) {
                            $count = null;
                        }
                        $tableRecords[] = ['name' => $tableName, 'count' => $count];
                    }
                    usort($tableRecords, function ($a, $b) {
                        return ($b['count'] ?? 0) <=> ($a['count'] ?? 0);
                    });
                } catch (\Throwable $e) {
                    $tableRecords = [];
                }
            }

            return view('backEnd/datadelete/index', compact('backup_folders', 'company_list', 'databaseName', 'tableRecords'));
        } catch (\Exception $e) {
            return $e;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    public function data_login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user); // Log the user in

            return response()->json([
                'success' => true,
                'message' => 'Login successful'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid username or password'
            ]);
        }
    }

    public function all_data(Request $request)
    {
        try {
            //return $request->all();
            DB::beginTransaction();
            // $backupPath = storage_path('backups/data-delete-backup_' . date('Y-M-d_H-i'));
            // if (!file_exists($backupPath)) {
            //     mkdir($backupPath, 0755, true);
            // }
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $r1 = $this->backup_tables();
            if ($r1 != "SUCCESS") {
                DB::rollBack();
                return $r1;
                Toastr::error('Backup Failed', 'Failed');
                return redirect()->back();
            }
            if (isset($request->options)) {
                foreach ($request->options as $options) {
                    $r = $this->delete_tables($options, $request->company_id);

                    if ($r != "SUCCESS") {
                        DB::rollBack();
                        return $r;
                        Toastr::error('Delete Failed', 'Failed');
                        return redirect()->back();
                    }
                }
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                $ids = [1, Auth::user()->id];
                if ($request->delete_company == 1) {
                    DB::table('sys_company')->wherein('id', $request->company_id)->delete();
                }
            }

            DB::commit();
            Toastr::success('Data Deleted Successfully.', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollback();
            return $th;
        }
    }

    public function deleteTableData(Request $request)
    {
        try {
            $request->validate([
                'table_names' => 'required|array|min:1',
                'table_names.*' => 'required|string',
            ]);

            $validTables = collect(DB::select('SHOW TABLES'))->map(function ($row) {
                $row = (array) $row;
                return array_values($row)[0] ?? null;
            })->filter()->values()->toArray();

            $tablesToDelete = array_intersect($validTables, $request->table_names);

            if (empty($tablesToDelete)) {
                Toastr::error('No valid tables selected for deletion.', 'Failed');
                return redirect()->back();
            }

            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            foreach ($tablesToDelete as $table) {
                DB::table($table)->delete();
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();

            Toastr::success('Selected table data deleted successfully.', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    public function delete_tables($tab, $companyIds)
    {
        try {
            if ($tab == "Supplier Register") {
                $id = DB::table('sys_cust_suppl')
                    ->where(function ($query) use ($companyIds) {
                        foreach ($companyIds as $cid) {
                            $query->orWhereRaw('FIND_IN_SET(?, company_access)', [$cid]);
                        }
                    })->pluck('id')->toArray();

                DB::table('sys_cust_suppl')->whereIn('id', $id)->delete();
                DB::table('sys_cust_suppl_addressbook')->whereIn('cust_suppl_id', $id)->delete();
                DB::table('sys_cust_suppl_addressbook_form')->whereIn('cust_suppl_id', $id)->delete();
                DB::table('sys_cust_suppl_assign')->whereIn('cust_supp_id', $id)->delete();
                DB::table('sys_cust_suppl_contact')->whereIn('cust_suppl_id', $id)->delete();
                DB::table('sys_cust_suppl_contact_form')->whereIn('cust_suppl_id', $id)->delete();
                DB::table('sys_cust_suppl_doc')->whereIn('cust_suppl_id', $id)->delete();
                DB::table('sys_cust_suppl_doc_form')->whereIn('cust_suppl_id', $id)->delete();
                DB::table('sys_cust_suppl_form')->whereIn('company_id', $companyIds)->delete();
                DB::table('sys_cust_suppl_import')->whereIn('company_id', $companyIds)->delete();
                DB::table('sys_cust_suppl_merge')->whereIn('from_id', $id)->delete();
                DB::table('sys_cust_suppl_merge')->whereIn('to_id', $id)->delete();
                DB::table('sys_cust_suppl_stl')->whereIn('cust_suppl_id', $id)->delete();
                //DB::table('sys_cust_suppl_addressbook_cart')->wherein('supplier_id', $id)->wherein('company_id',$com)->delete();

            } else if ($tab == "Purchase Order") {
                $poIds = DB::table('sys_purchase_order')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                DB::table('sys_purchase_order')->whereIn('id', $poIds)->delete();
                DB::table('sys_purchase_order_items')->whereIn('po_id', $poIds)->delete();
                DB::table('sys_purchase_order_items_cart')->whereIn('po_id', $poIds)->delete();
                DB::table('sys_purchase_order_items_srl')->whereIn('po_id', $poIds)->delete();
                DB::table('sys_purchase_order_att')->whereIn('doc_id', $poIds)->delete();
                DB::table('sys_purchase_order_attachment')->whereIn('po_id', $poIds)->delete();
            } else if ($tab == "Goods Receipt Note") {
                $grnIds = DB::table('sys_purchase_grn')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                DB::table('sys_purchase_grn')->whereIn('id', $grnIds)->delete();
                DB::table('sys_purchase_grn_items')->whereIn('grn_id', $grnIds)->delete();
                DB::table('sys_purchase_grn_items_restore')->whereIn('grn_id', $grnIds)->delete();
                DB::table('sys_purchase_grn_items_srlno')->whereIn('grn_id', $grnIds)->delete();
                DB::table('sys_purchase_grn_items_srlno_cart')->whereIn('grn_id', $grnIds)->delete();
                DB::table('sys_purchase_grn_items_srlno_restore')->whereIn('grn_id', $grnIds)->delete();
                DB::table('sys_purchase_grn_license_key')->whereIn('grn_id', $grnIds)->delete();
                DB::table('sys_purchase_grn_license_key_test')->whereIn('grn_id', $grnIds)->delete();
            } else if ($tab == "Purchase Invoice") {

                $invoiceIds = DB::table('sys_purchase_invoice')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                $docNumbers = DB::table('sys_purchase_invoice')->whereIn('company_id', $companyIds)->pluck('doc_number')->toArray();
                DB::table('sys_purchase_invoice')->whereIn('id', $invoiceIds)->delete();
                DB::table('sys_purchase_invoice_att')->whereIn('doc_id', $invoiceIds)->delete();
                DB::table('sys_purchase_invoice_attachment')->whereIn('pi_id', $invoiceIds)->delete();
                DB::table('sys_purchase_invoice_cf_charges')->whereIn('pi_id', $invoiceIds)->delete();
                DB::table('sys_purchase_invoice_items')->whereIn('pi_id', $invoiceIds)->delete();
                DB::table('sys_purchase_invoice_items_history')->whereIn('pi_id', $invoiceIds)->delete();
                DB::table('sys_purchase_return_adjestment')->whereIn('piv_no', $docNumbers)->delete();
                DB::table('sys_chartofaccounts_transaction')
                    ->whereIn('transaction_type', ['purchaseinvoice'])
                    ->whereIn('transaction_id', $invoiceIds)
                    ->delete();

                //DB::table('sys_purchase_invoice_adjustment_temp')->wherein('invoice_id', $id)->delete();
                //DB::table('sys_purchase_invoice_restore')->wherein('pi_id', $id)->delete();

            } else if ($tab == "Purchase Return") {

                $returnIds = DB::table('sys_purchase_return')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                $docNumbers = DB::table('sys_purchase_return')->whereIn('company_id', $companyIds)->pluck('doc_number')->toArray();
                DB::table('sys_purchase_return')->whereIn('id', $returnIds)->delete();
                DB::table('sys_purchase_return_adjestment')->whereIn('pri_no', $docNumbers)->delete();
                DB::table('sys_purchase_return_copy')->whereIn('doc_number', $docNumbers)->delete();
                DB::table('sys_purchase_return_items_srlno')->whereIn('prt_id', $returnIds)->delete();
                DB::table('sys_purchase_return_items_srlno_cart')->whereIn('prt_id', $returnIds)->delete();
                DB::table('sys_purchase_return_list')->whereIn('pr_id', $returnIds)->delete();
                DB::table('sys_purchase_return_list_cart')->whereIn('pr_id', $returnIds)->delete();
                DB::table('sys_purchase_return_list_history')->whereIn('pr_id', $returnIds)->delete();
                DB::table('sys_chartofaccounts_transaction')
                    ->whereIn('transaction_type', ['purchasereturn'])
                    ->whereIn('transaction_id', $returnIds)
                    ->delete();


            } else if ($tab == "Payments") {
                $paymentIds = DB::table('sys_payment')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                $docNumbers = DB::table('sys_payment')->whereIn('company_id', $companyIds)->pluck('doc_number')->toArray();
                DB::table('sys_payment')->whereIn('id', $paymentIds)->delete();
                DB::table('sys_payment_adjustments')->whereIn('bi_doc_number', $docNumbers)->delete();
                DB::table('sys_payment_cheque')->whereIn('doc_number', $docNumbers)->delete();
                DB::table('sys_chartofaccounts_transaction')
                    ->whereIn('transaction_type', ['bankpayment', 'cashpayment'])
                    ->whereIn('transaction_id', $paymentIds)
                    ->delete();
                //DB::table('sys_payment_adjustments_temp')->wherein('payment_id', $id)->delete();
                //DB::table('sys_payment_cheque_template')->wherein('company_id',$companyIds)->delete();
            } else if ($tab == "Customer Register") {
                $id = DB::table('sys_cust_suppl')
                    ->where(function ($query) use ($companyIds) {
                        foreach ($companyIds as $cid) {
                            $query->orWhereRaw('FIND_IN_SET(?, company_access)', [$cid]);
                        }
                    })
                    ->pluck('id')
                    ->toArray();
                DB::table('sys_cust_suppl')->whereIn('id', $id)->delete();
                DB::table('sys_cust_suppl_addressbook')->whereIn('cust_suppl_id', $id)->delete();
                DB::table('sys_cust_suppl_addressbook_form')->whereIn('cust_suppl_id', $id)->delete();
                DB::table('sys_cust_suppl_assign')->whereIn('cust_supp_id', $id)->delete();
                DB::table('sys_cust_suppl_contact')->whereIn('cust_suppl_id', $id)->delete();
                DB::table('sys_cust_suppl_contact_form')->whereIn('cust_suppl_id', $id)->delete();
                DB::table('sys_cust_suppl_doc')->whereIn('cust_suppl_id', $id)->delete();
                DB::table('sys_cust_suppl_doc_form')->whereIn('cust_suppl_id', $id)->delete();
                DB::table('sys_cust_suppl_form')->whereIn('company_id', $companyIds)->delete();
                DB::table('sys_cust_suppl_import')->whereIn('company_id', $companyIds)->delete();
                DB::table('sys_cust_suppl_merge')->whereIn('from_id', $id)->delete();
                DB::table('sys_cust_suppl_merge')->whereIn('to_id', $id)->delete();
                DB::table('sys_cust_suppl_stl')->whereIn('cust_suppl_id', $id)->delete();
                //DB::table('sys_cust_suppl_addressbook_cart')->wherein('supplier_id', $id)->wherein('company_id',$com)->delete();
            } else if ($tab == "Quotation") {

            } else if ($tab == "Proforma Invoice") {
                $invoiceIds = DB::table('sys_proforma_invoice')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                DB::table('sys_proforma_invoice')->whereIn('id', $invoiceIds)->delete();
                DB::table('sys_proforma_invoice_items')->whereIn('profo_id', $invoiceIds)->delete();
            } else if ($tab == "Sales Invoice") {
                $invoiceIds = DB::table('sys_sales_invoice')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                $docNumbers = DB::table('sys_sales_invoice')->whereIn('company_id', $companyIds)->pluck('doc_number')->toArray();
                DB::table('sys_sales_invoice')->whereIn('id', $invoiceIds)->delete();
                DB::table('sys_sales_invoice_att')->whereIn('siv_id', $invoiceIds)->delete();
                DB::table('sys_sales_invoice_attachment')->whereIn('si_id', $invoiceIds)->delete();
                DB::table('sys_sales_invoice_cf_charges')->whereIn('si_id', $invoiceIds)->delete();
                DB::table('sys_sales_invoice_items')->whereIn('si_id', $invoiceIds)->delete();
                DB::table('sys_sales_invoice_items_cart')->whereIn('si_id', $invoiceIds)->delete();
                DB::table('sys_sales_invoice_items_history')->whereIn('si_id', $invoiceIds)->delete();
                DB::table('sys_sales_invoice_items_srl')->whereIn('si_id', $invoiceIds)->delete();
                DB::table('sys_chartofaccounts_transaction')
                    ->whereIn('transaction_type', ['salesinvoice'])
                    ->whereIn('transaction_id', $invoiceIds)
                    ->delete();
                //DB::table('sys_sales_invoice_adjustment_temp')->wherein('invoice_id', $id)->delete();
            } else if ($tab == "Delivery Note") {
                $dnIds = DB::table('sys_delivery_note')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                DB::table('sys_delivery_note')->whereIn('id', $dnIds)->delete();
                DB::table('sys_delivery_note_items')->whereIn('dn_id', $dnIds)->delete();
                DB::table('sys_delivery_note_items_srl')->whereIn('dn_id', $dnIds)->delete();
            } else if ($tab == "Sales Return") {
                $salesReturnIds = DB::table('sys_sales_return')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                $docNumbers = DB::table('sys_sales_return')->whereIn('company_id', $companyIds)->pluck('doc_number')->toArray();
                DB::table('sys_sales_return')->whereIn('id', $salesReturnIds)->delete();
                DB::table('sys_sales_return_adjestment')->whereIn('srn_no', $docNumbers)->delete();
                DB::table('sys_sales_return_list')->whereIn('sr_id', $salesReturnIds)->delete();
                DB::table('sys_sales_return_list_cart')->whereIn('sr_id', $salesReturnIds)->delete();
                DB::table('sys_sales_return_list_history')->whereIn('sr_id', $salesReturnIds)->delete();
                DB::table('sys_sales_return_list_srl')->whereIn('sr_id', $salesReturnIds)->delete();
                DB::table('sys_chartofaccounts_transaction')
                    ->whereIn('transaction_type', ['salesreturn'])
                    ->whereIn('transaction_id', $salesReturnIds)
                    ->delete();
            } else if ($tab == "Receipts") {
                $receiptIds = DB::table('sys_receipt')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                $docNumbers = DB::table('sys_receipt')->whereIn('company_id', $companyIds)->pluck('doc_number')->toArray();
                DB::table('sys_receipt')->whereIn('id', $receiptIds)->delete();
                DB::table('sys_receipt_adjustments')->whereIn('bi_doc_number', $docNumbers)->delete();
                DB::table('sys_receipt_adjustments_temp')->whereIn('bi_doc_number', $docNumbers)->delete();
                DB::table('sys_chartofaccounts_transaction')
                    ->whereIn('transaction_type', ['bankreceipt', 'cashreceipt'])
                    ->whereIn('transaction_id', $receiptIds)
                    ->delete();
            } else if ($tab == "Customs Clearance (only in FZE)") {
                $clearanceIds = DB::table('sys_clearance')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                DB::table('sys_clearance')->whereIn('id', $clearanceIds)->delete();
                DB::table('sys_clearance_att')->whereIn('doc_id', $clearanceIds)->delete();
                DB::table('sys_clearance_items')->whereIn('clearance_id', $clearanceIds)->delete();
                DB::table('sys_clearance_items_cart')->whereIn('clearance_id', $clearanceIds)->delete();
                DB::table('sys_clearance_items_trns')->whereIn('clearance_id', $clearanceIds)->delete();
                //DB::table('sys_clearance_trns')->wherein('clearance_id', $id)->delete();
            } else if ($tab == "Products - Product") {
                DB::table('sm_items')->whereIn('company_id', $companyIds)->delete();
            } else if ($tab == "Products - Brand") {
                DB::table('sys_brand')->wherein('company_id', $companyIds)->delete();
            } else if ($tab == "Products - Category") {
                DB::table('sm_item_categories')->wherein('company_id', $companyIds)->delete();
            } else if ($tab == "Product - Sub-Category") {
                DB::table('sm_item_subcategories')->wherein('company_id', $companyIds)->delete();
            } else if ($tab == "Opening Stock") {
                DB::table('sys_item_opening_stock')->wherein('company_id', $companyIds)->delete();
                DB::table('sys_item_stock')->whereNotNull('ops_id')->wherein('company_id', $companyIds)->delete();
            } else if ($tab == "Excess Stock") {
                DB::table('sys_item_stock')->where(function ($query) {
                    $query->whereNotNull('grn_id')
                        ->orWhereNotNull('slr_id');
                })->whereIn('company_id', $companyIds)->delete();
            } else if ($tab == "Shortage Stock") {
                DB::table('sys_item_stock')->where(function ($query) {
                    $query->whereNotNull('pri_id')
                        ->orWhereNotNull('dln_id');
                })->whereIn('company_id', $companyIds)->delete();
            } else if ($tab == "Packing List") {
                $packingListIds = DB::table('sys_packing_list')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                DB::table('sys_packing_list')->whereIn('id', $packingListIds)->delete();
                DB::table('sys_packing_list_items')->whereIn('packing_list_id', $packingListIds)->delete();

                //DB::table('sys_packing_list_items_cart')->wherein('packing_list_id', $id)->delete();
            } else if ($tab == "Leads") {
                $leadIds = DB::table('sys_crm_leads')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                DB::table('sys_crm_leads')->whereIn('id', $leadIds)->delete();
                DB::table('sys_crm_leads_comments')->whereIn('lead_id', $leadIds)->delete();
            } else if ($tab == "Deals") {
                $dealIds = DB::table('sys_crm_deals')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                DB::table('sys_crm_deals')->whereIn('id', $dealIds)->delete();
                $relatedTables = [
                    'sys_crm_deal_delivery' => 'deal_id',
                    'sys_crm_deal_delivery_items' => 'deal_id',
                    'sys_crm_deal_return_sales' => 'deal_id',
                    'sys_crm_deal_track' => 'deal_id',
                    'sys_crm_deal_track_approval_accounts' => 'deal_id',
                    'sys_crm_deal_track_approval_accounts_pending' => 'deal_id',
                    'sys_crm_deal_track_approval_delivery' => 'deal_id',
                    'sys_crm_deal_track_approval_invoice' => 'deal_id',
                    'sys_crm_deal_track_approval_purchease' => 'deal_id',
                    'sys_crm_deal_track_approval_purchease_grn' => 'deal_id',
                    'sys_crm_deal_track_approval_receivables' => 'deal_id',
                    'sys_crm_deal_track_approval_sales' => 'deal_id',
                    'sys_crm_deal_track_approval_technical' => 'deal_id',
                    'sys_crm_deal_track_temp' => 'deal_id',
                    'sys_crm_deals_collaboration' => 'deal_id',
                    'sys_crm_deals_comments' => 'deal_id',
                    'sys_crm_deals_company_change' => 'deal_id',
                ];

                foreach ($relatedTables as $table => $column) {
                    DB::table($table)->whereIn($column, $dealIds)->delete();
                }

                //DB::table('sys_crm_deal_delivery_details')->wherein('delivery_id', $id)->delete();
                //DB::table('sys_crm_deal_track_approval_purchease_grn_list')->wherein('deal_id', $id)->delete();
            } else if ($tab == "All section") {

            } else if ($tab == "Reimbursement Request") {
                DB::table('sys_crm_reimbursement')->wherein('company_id', $companyIds)->delete();
            } else if ($tab == "Annual Maintenance Contract") {
                $amcIds = DB::table('sys_crm_amc')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                DB::table('sys_crm_amc')->whereIn('id', $amcIds)->delete();
                $relatedTables = [
                    'sys_crm_amc_asign' => 'amc_id',
                    'sys_crm_amc_comments' => 'amc_id',
                    'sys_crm_amc_table' => 'amc_id',
                    'sys_crm_amc_table_service_comments' => 'amc_id',
                    'sys_crm_amc_table_service_request' => 'amc_id',
                    'sys_crm_amc_table_service_scope_of_work' => 'amc_id',
                    'sys_crm_amc_updates' => 'amc_id',
                ];
                foreach ($relatedTables as $table => $column) {
                    DB::table($table)->whereIn($column, $amcIds)->delete();
                }

                //DB::table('sys_crm_amc_per_month')->wherein('amc_id', $id)->delete();
                //DB::table('sys_crm_amc_table_service_request_scope_of_work')->wherein('amc_id', $id)->delete();
            } else if ($tab == "Professional Services") {
                //sys_crm_ps_service_table;
                //sys_crm_ps_service_table_scope_of_work;
                //sys_crm_ps_table_service_comments;
            } else if ($tab == "Pre-Sales Request") {

            } else if ($tab == "Service Request List") {

            } else if ($tab == "Task") {
                $taskIds = DB::table('sys_crm_user_tasks')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                DB::table('sys_crm_user_tasks')->whereIn('id', $taskIds)->delete();

                $relatedTables = [
                    'sys_crm_user_task_comments' => 'task_id',
                    'sys_crm_user_task_items' => 'task_id',
                ];

                foreach ($relatedTables as $table => $column) {
                    DB::table($table)->whereIn($column, $taskIds)->delete();
                }
            } else if ($tab == "Todo") {
                //$data = DB::table('sys_crm_user_todos')->wherein('company_id',$companyIds)->get();
                //file_put_contents($backupPath . "/sys_crm_user_todos.json", $data->toJson(JSON_PRETTY_PRINT));
                //DB::table('sys_crm_user_todos')->wherein('company_id',$companyIds)->delete();
            } else if ($tab == "Notes") {

            } else if ($tab == "Activity Tracker") {

            } else if ($tab == "Chart of Accounts - Account") {
                $coaIds = DB::table('sys_chartofaccounts')
                    ->whereIn('company_id', $companyIds)
                    ->where('account_code', 'like', 'ACC%')
                    ->where('main_account_id', 0)
                    ->pluck('id')
                    ->toArray();
                DB::table('sys_chartofaccounts')->whereIn('id', $coaIds)->delete();
                DB::table('sys_chartofaccounts_transaction')->whereIn('transaction_id', $coaIds)->delete();
            } else if ($tab == "Chart of Accounts - Sub -Account") {
                $coaIds = DB::table('sys_chartofaccounts')
                    ->whereIn('company_id', $companyIds)
                    ->where('account_code', 'like', 'SACC%')
                    ->where('main_account_id', '!=', 0)
                    ->pluck('id')
                    ->toArray();
                DB::table('sys_chartofaccounts')->whereIn('id', $coaIds)->delete();
                DB::table('sys_chartofaccounts_transaction')->whereIn('transaction_id', $coaIds)->delete();
            } else if ($tab == "Opening Stock - Import Invoice") {
                //sys_chartofaccounts_opening_balance_invoice;
                //sys_chartofaccounts_opening_balance_invoice_import;
                DB::table('sys_chartofaccounts_transaction')->wherein('transaction_type', ['openingstock', 'openingbalance', 'opbinvoice'])->wherein('company_id', $companyIds)->delete();
            } else if ($tab == "Journal Voucher") {
                $jvIds = DB::table('sys_journalvoucher')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                $jvDocNumbers = DB::table('sys_journalvoucher')->whereIn('company_id', $companyIds)->pluck('doc_number')->toArray();
                DB::table('sys_journalvoucher')->whereIn('id', $jvIds)->delete();

                $relatedTables = [
                    'sys_journalvoucher_att' => 'doc_id',
                    'sys_payment_adjustments_jv' => 'jv_id',
                    'sys_receipt_adjustments_jv' => 'jv_id',
                ];

                foreach ($relatedTables as $table => $column) {
                    $values = in_array($table, ['sys_payment_adjustments_jv', 'sys_receipt_adjustments_jv']) ? $jvDocNumbers : $jvIds;
                    DB::table($table)->whereIn($column, $values)->delete();
                }

                DB::table('sys_chartofaccounts_transaction')
                    ->whereIn('transaction_type', ['journalpayment', 'journalvoucher'])
                    ->whereIn('company_id', $companyIds)
                    ->delete();
                //DB::table('sys_journalvoucher_list')->wherein('journal_voucher_id', $id)->delete();
                //DB::table('sys_journalvoucher_list_history')->wherein('journal_voucher_id', $id)->delete();
            } else if ($tab == "Cash Book") {

            } else if ($tab == "Bank Book") {

            } else if ($tab == "STL Report") {
                $stlIds = DB::table('sys_stl')->whereIn('company_id', $companyIds)->pluck('id')->toArray();
                DB::table('sys_stl')->whereIn('id', $stlIds)->delete();

                $relatedTables = [
                    'sys_stl_items' => 'stl_id',
                    'sys_stl_payment' => 'stl_id',
                ];

                foreach ($relatedTables as $table => $column) {
                    DB::table($table)->whereIn($column, $stlIds)->delete();
                }
            } else if ($tab == "Role") {
                //$id = DB::table('roles')->wherein('company_id', $companyIds)->get();
                DB::table('sm_role_permissions')->wherein('company_id', $companyIds)->delete();
            } else if ($tab == "User") {
                $excludedIds = [1, Auth::user()->id];
                DB::table('users')->whereNotIn('id', $excludedIds)->whereIn('company_id', $companyIds)->delete();
                DB::table('sm_staffs')->whereNotIn('user_id', $excludedIds)->whereIn('company_id', $companyIds)->delete();
            } else if ($tab == "Manage currency") {
                //sys_currency;
                //sys_currency_rate;
            } else if ($tab == "Payment Terms") {
                //sys_payment_terms
            } else if ($tab == "Shipping") {
                //sys_shipping
            } else if ($tab == "VAT Settings") {
                DB::table('sys_vat')->wherein('company_id', $companyIds)->delete();
            } else if ($tab == "Daily Quotes") {
                //sys_daily_quotes
            } else if ($tab == "Cheque Print Template") {
                DB::table('sys_payment_cheque_template')->wherein('company_id', $companyIds)->delete();
            }
            return "SUCCESS";
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function backup_tables()
    {
        try {
            $backupPath = storage_path('backups/data-delete-backup_' . date('Y-M-d_H-i'));
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            $tables = ['gitex_leads', 'gitex_leads_2023', 'gitex_leads_copy', 'gitex_leads_irshaad', 'gitex_leads_stephan', 'sm_item_categories', 'sm_item_subcategories', 'sm_items', 'sm_items_backup', 'sm_items_bk', 'sm_items_bk_up', 'sm_items_cart', 'sm_items_ids', 'sm_role_permissions', 'sm_staff_attendences', 'sm_staff_bank_details', 'sm_staff_documents', 'sm_staff_education_qualifications', 'sm_staff_job_details', 'sm_staff_professional_experiences', 'sm_staffs', 'sys_bankpayment', 'sys_bankreceipt', 'sys_bankreceipt_list', 'sys_bankreceipt_list_history', 'sys_brand', 'sys_cashpayment', 'sys_cashreceipt', 'sys_cashreceipt_list', 'sys_cashreceipt_list_history', 'sys_chartofaccounts', 'sys_chartofaccounts_import', 'sys_chartofaccounts_import_sub', 'sys_chartofaccounts_opening_balance_invoice', 'sys_chartofaccounts_opening_balance_invoice_import', 'sys_chartofaccounts_transaction', 'sys_chartofaccounts_transaction_history', 'sys_chartofaccounts_transaction_invoice_detail', 'sys_clearance', 'sys_clearance_att', 'sys_clearance_items', 'sys_clearance_items_cart', 'sys_clearance_items_trns', 'sys_company', 'sys_company_banking', 'sys_company_compliances', 'sys_company_hr_policies', 'sys_crm_amc', 'sys_crm_amc_asign', 'sys_crm_amc_comments', 'sys_crm_amc_per_month', 'sys_crm_amc_table', 'sys_crm_amc_table_service_comments', 'sys_crm_amc_table_service_request', 'sys_crm_amc_table_service_request_scope_of_work', 'sys_crm_amc_table_service_scope_of_work', 'sys_crm_amc_updates', 'sys_crm_deal_delivery', 'sys_crm_deal_delivery_details', 'sys_crm_deal_delivery_items', 'sys_crm_deal_return_sales', 'sys_crm_deal_track', 'sys_crm_deal_track_approval_accounts', 'sys_crm_deal_track_approval_accounts_pending', 'sys_crm_deal_track_approval_delivery', 'sys_crm_deal_track_approval_invoice', 'sys_crm_deal_track_approval_purchease', 'sys_crm_deal_track_approval_purchease_grn', 'sys_crm_deal_track_approval_purchease_grn_list', 'sys_crm_deal_track_approval_receivables', 'sys_crm_deal_track_approval_sales', 'sys_crm_deal_track_approval_technical', 'sys_crm_deal_track_temp'];
            $tables1 = ['sys_crm_deals', 'sys_crm_deals_collaboration', 'sys_crm_deals_comments', 'sys_crm_deals_company_change', 'sys_crm_leads', 'sys_crm_leads_comments', 'sys_crm_ps_service_table', 'sys_crm_ps_service_table_scope_of_work', 'sys_crm_ps_table_service_comments', 'sys_crm_quote_cart', 'sys_crm_quote_cart_edit', 'sys_crm_quote_charges', 'sys_crm_quote_cs_items', 'sys_crm_quote_items', 'sys_crm_sales_target', 'sys_crm_service', 'sys_crm_service_assign', 'sys_crm_service_comments', 'sys_crm_support', 'sys_crm_support_activity', 'sys_crm_support_comments', 'sys_crm_support_work', 'sys_crm_user_task_comments', 'sys_crm_user_task_items', 'sys_crm_user_tasks', 'sys_crm_user_todos', 'sys_currency', 'sys_currency_rate', 'sys_cust_suppl', 'sys_cust_suppl_addressbook', 'sys_cust_suppl_addressbook_cart', 'sys_cust_suppl_addressbook_form', 'sys_cust_suppl_assign', 'sys_cust_suppl_bk', 'sys_cust_suppl_contact', 'sys_cust_suppl_contact_form', 'sys_cust_suppl_doc', 'sys_cust_suppl_doc_form', 'sys_cust_suppl_form', 'sys_cust_suppl_import', 'sys_cust_suppl_merge', 'sys_cust_suppl_stl', 'sys_customer', 'sys_daily_quotes', 'sys_deal_purchase_order_items', 'sys_deal_purchase_order_items_cart', 'sys_deal_sales_invoice_items', 'sys_deal_sales_invoice_items_cart', 'sys_delivery_note', 'sys_delivery_note_items', 'sys_delivery_note_items_srl', 'sys_grn_items_cart', 'sys_item_opening_stock', 'sys_item_stock', 'sys_item_stock_copy', 'sys_item_stock_import', 'sys_item_stock_restore', 'sys_journalvoucher', 'sys_journalvoucher_att', 'sys_journalvoucher_cart', 'sys_journalvoucher_list', 'sys_journalvoucher_list_history', 'sys_packing_list', 'sys_packing_list_items', 'sys_packing_list_items_cart', 'sys_payment', 'sys_payment_adjustments', 'sys_payment_adjustments_jv', 'sys_payment_adjustments_temp', 'sys_payment_cheque', 'sys_payment_cheque_template', 'sys_payment_terms', 'sys_postdated_payment'];
            $tables2 = ['sys_postdated_receipt', 'sys_proforma_invoice', 'sys_proforma_invoice_items', 'sys_purchase_auto', 'sys_purchase_dln_license_key', 'sys_purchase_grn', 'sys_purchase_grn_items', 'sys_purchase_grn_items_restore', 'sys_purchase_grn_items_srlno', 'sys_purchase_grn_items_srlno_cart', 'sys_purchase_grn_items_srlno_restore', 'sys_purchase_grn_license_key', 'sys_purchase_grn_license_key_test', 'sys_purchase_grn_license_key_trn', 'sys_purchase_invoice', 'sys_purchase_invoice_adjustment_temp', 'sys_purchase_invoice_att', 'sys_purchase_invoice_attachment', 'sys_purchase_invoice_cf_charges', 'sys_purchase_invoice_items', 'sys_purchase_invoice_items_history', 'sys_purchase_invoice_restore', 'sys_purchase_order', 'sys_purchase_order_att', 'sys_purchase_order_attachment', 'sys_purchase_order_items', 'sys_purchase_order_items_cart', 'sys_purchase_order_items_srl', 'sys_purchase_return', 'sys_purchase_return_copy', 'sys_purchase_return_items_srlno', 'sys_purchase_return_items_srlno_cart', 'sys_purchase_return_list', 'sys_purchase_return_list_cart', 'sys_purchase_return_list_history', 'sys_receipt', 'sys_receipt_adjustments', 'sys_receipt_adjustments_jv', 'sys_receipt_adjustments_temp', 'sys_receipt_mode', 'sys_reserve_stock', 'sys_sales_invoice', 'sys_sales_invoice_adjustment_temp', 'sys_sales_invoice_att', 'sys_sales_invoice_attachment', 'sys_sales_invoice_cf_charges', 'sys_sales_invoice_items', 'sys_sales_invoice_items_cart', 'sys_sales_invoice_items_history', 'sys_sales_invoice_items_srl', 'sys_sales_return', 'sys_sales_return_adjestment', 'sys_sales_return_list', 'sys_sales_return_list_cart', 'sys_sales_return_list_history', 'sys_sales_return_list_srl', 'sys_shipping', 'sys_stock_in', 'sys_stock_in_items', 'sys_stock_in_items_cart', 'sys_stock_in_items_import', 'sys_stock_in_serial_no', 'sys_stock_items_cart', 'sys_stock_out', 'sys_stock_out_items', 'sys_stock_out_items_cart', 'sys_stock_out_items_import', 'sys_stock_out_serial_no', 'sys_stl', 'sys_stl_items', 'sys_stl_payment', 'sys_vat', 'users'];


            // foreach ($tables as $table) {
            //     $data = DB::table($table)->get();
            //     file_put_contents($backupPath . "/{$table}.json", $data->toJson(JSON_PRETTY_PRINT));
            // }

            foreach ($tables as $table) {
                if (!Schema::hasTable($table)) {
                    //return "Skipping missing table: {$table}\n";
                    continue;
                }
                try {
                    $data = DB::table($table)->limit(100000000)->get(); // optional limit
                    file_put_contents($backupPath . "/{$table}.json", $data->toJson(JSON_PRETTY_PRINT));
                    $return[] = "{$table}\n";
                } catch (\Throwable $e) {
                    $return[] = "{$table} failed: {$e->getMessage()}\n";
                    return $e;
                }
            }
            foreach ($tables1 as $table) {
                if (!Schema::hasTable($table)) {
                    //return "Skipping missing table: {$table}\n";
                    continue;
                }
                try {
                    $data = DB::table($table)->limit(100000000)->get(); // optional limit
                    file_put_contents($backupPath . "/{$table}.json", $data->toJson(JSON_PRETTY_PRINT));
                    $return[] = "{$table}\n";
                } catch (\Throwable $e) {
                    $return[] = "{$table} failed: {$e->getMessage()}\n";
                    return $e;
                }
            }
            foreach ($tables2 as $table) {
                if (!Schema::hasTable($table)) {
                    //return "Skipping missing table: {$table}\n";
                    continue;
                }
                try {
                    $data = DB::table($table)->limit(100000000)->get(); // optional limit
                    file_put_contents($backupPath . "/{$table}.json", $data->toJson(JSON_PRETTY_PRINT));
                    $return[] = "{$table}\n";
                } catch (\Throwable $e) {
                    $return[] = "{$table} failed: {$e->getMessage()}\n";
                    return $e;
                }
            }
            return "SUCCESS";
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function restore_tables($id)
    {
        try {
            $backupPath = storage_path('backups/' . $id);
            $files = glob($backupPath . '/*.json');

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            foreach ($files as $file) {
                $table = basename($file, '.json');

                if (!Schema::hasTable($table)) {
                    echo "Table does not exist: {$table}\n";
                    continue;
                }

                $json = file_get_contents($file);
                $data = json_decode($json, true);

                if (!empty($data)) {
                    DB::table($table)->truncate(); // optional

                    // Replace invalid datetime values with a default valid date
                    foreach ($data as &$row) {
                        foreach ($row as $key => $value) {
                            if ($value === '0000-00-00 00:00:00') {
                                $row[$key] = date('Y-m-d H:i:s'); // current timestamp
                                // Or use a fixed default: '1970-01-01 00:00:00'
                            }
                        }
                    }

                    // Insert in chunks to avoid "too many placeholders" error
                    foreach (array_chunk($data, 100) as $chunk) {
                        DB::table($table)->insert($chunk);
                    }

                    echo "Restored: {$table}\n";
                } else {
                    echo "Empty file: {$table}\n";
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            Toastr::success('Data Restored Successfully.', 'Success');
            return redirect()->back();

        } catch (\Throwable $th) {
            return $th;
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }


    public function delete_data_by_table(Request $request)
    {
        try {
            $checkboxes = ['heads', 'groups', 'subgroups', 'accounts', 'subaccounts',
                           'chartofaccounts_invoice_import', 'cash_book', 'bank_book',
                           'cheque_book', 'stl', 'leads', 'deals'];

            $selectedAny = collect($checkboxes)->contains(function ($k) use ($request) {
                return $request->has($k);
            });

            if (! $selectedAny) {
                Toastr::error('Please select at least one category.', 'Failed');
                return redirect()->back();
            }

            $backupDir = storage_path('backups/backup_' . date('Y-m-d_H-i-s'));
            if (! file_exists($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            DB::beginTransaction();
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            if ($request->has('heads')) {
                $this->exportTableToBackup('sys_account_group', $backupDir);
                DB::table('sys_account_group')->truncate();
            }

            if ($request->has('groups')) {
                $this->exportTableToBackup('sys_account_group_sub', $backupDir);
                DB::table('sys_account_group_sub')->truncate();
            }

            if ($request->has('subgroups')) {
                $this->exportTableToBackup('sys_account_group_sub2', $backupDir);
                DB::table('sys_account_group_sub2')->truncate();
            }

            if ($request->has('accounts')) {
                $this->exportTableToBackup('sys_chartofaccounts', $backupDir, function ($q) {
                    return $q->where('account_code', 'like', 'ACC%');
                }, 'sys_chartofaccounts_ACC');
                DB::table('sys_chartofaccounts')->where('account_code', 'like', 'ACC%')->delete();
            }

            if ($request->has('subaccounts')) {
                $this->exportTableToBackup('sys_chartofaccounts', $backupDir, function ($q) {
                    return $q->where('account_code', 'like', 'SACC%');
                }, 'sys_chartofaccounts_SACC');
                DB::table('sys_chartofaccounts')->where('account_code', 'like', 'SACC%')->delete();
            }

            if ($request->has('chartofaccounts_invoice_import')) {
                $this->exportTableToBackup('sys_chartofaccounts_import', $backupDir);
                $this->exportTableToBackup('sys_chartofaccounts_import_sub', $backupDir);
                DB::table('sys_chartofaccounts_import')->truncate();
                DB::table('sys_chartofaccounts_import_sub')->truncate();
            }

                // if ($request->has('cash_book')) {
                //     $this->exportTableToBackup('sys_cashpayment', $backupDir);
                //     DB::table('sys_cashpayment')->truncate();
                // }

                // if ($request->has('bank_book')) {
                //     $this->exportTableToBackup('sys_bankpayment', $backupDir);
                //     DB::table('sys_bankpayment')->truncate();
                // }

            if ($request->has('cheque_book')) {
                $this->exportTableToBackup('chequebooks', $backupDir);
                DB::table('chequebooks')->truncate();
            }

            if ($request->has('stl')) {
                $this->exportTableToBackup('sys_stl', $backupDir);
                $this->exportTableToBackup('sys_stl_items', $backupDir);
                DB::table('sys_stl_items')->truncate();
                DB::table('sys_stl')->truncate();
            }

            if ($request->has('leads')) {
                $this->exportTableToBackup('sys_crm_leads', $backupDir);
                $this->exportTableToBackup('sys_crm_leads_comments', $backupDir);
                DB::table('sys_crm_leads_comments')->truncate();
                DB::table('sys_crm_leads')->truncate();
            }

            if ($request->has('deals')) {
                $dealTables = [
                    'sys_crm_deals',
                    'sys_crm_deals_comments',
                    'sys_crm_deals_collaboration',
                    'sys_crm_deals_company_change',
                    'sys_crm_deal_track',
                    'sys_crm_deal_delivery',
                    'sys_crm_deal_delivery_items',
                    'sys_crm_deal_return_sales',
                    'sys_crm_deal_track_approval_accounts',
                    'sys_crm_deal_track_approval_accounts_pending',
                    'sys_crm_deal_track_approval_delivery',
                    'sys_crm_deal_track_approval_invoice',
                    'sys_crm_deal_track_approval_purchease',
                    'sys_crm_deal_track_approval_purchease_grn',
                    'sys_crm_deal_track_approval_receivables',
                    'sys_crm_deal_track_approval_sales',
                    'sys_crm_deal_track_approval_technical',
                    'sys_crm_deal_track_temp',
                    'sys_crm_deal_track_approval_purchease_grn_list',
                ];

                foreach ($dealTables as $table) {
                    $this->exportTableToBackup($table, $backupDir);
                    if (Schema::hasTable($table)) {
                        DB::table($table)->truncate();
                    }
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            DB::commit();

            Toastr::success('Data exported to backup and deleted successfully.', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th;
        }
    }

    protected function exportTableToBackup(string $table, string $backupDir, callable $scope = null, string $filename = null)
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $filePath = $backupDir . DIRECTORY_SEPARATOR . ($filename ?? $table) . '.json';
        $data = [];

        $query = DB::table($table)->orderBy('id');
        if ($scope) {
            $query = $scope($query);
        }

        $query->chunk(1000, function ($rows) use (&$data) {
            foreach ($rows as $row) {
                $data[] = (array) $row;
            }
        });

        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }


}